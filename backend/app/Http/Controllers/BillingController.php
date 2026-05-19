<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class BillingController extends Controller
{
    private function stripe(): StripeClient
    {
        return new StripeClient(config('services.stripe.secret'));
    }

    private const PLANS = [
        'starter' => [
            'name' => 'Starter',
            'price' => 0,
            'priceId' => null,
            'features' => ['Up to 5 workflows', '10 automations', 'AI chat assistant', 'Basic analytics', '1 organization'],
            'limits' => ['workflows' => 5, 'automations' => 10, 'members' => 3],
        ],
        'professional' => [
            'name' => 'Professional',
            'price' => 49,
            'priceId' => 'price_1TYrBtJkIMVEYY0aqNyIGZmx',
            'features' => ['Unlimited workflows', '100 automations', 'Recruitment AI', 'Knowledge Base CMS', 'Logistics optimizer', 'Predictive analytics', 'Up to 3 organizations', '10 team members'],
            'limits' => ['workflows' => -1, 'automations' => 100, 'members' => 10],
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'price' => 149,
            'priceId' => 'price_1TYrCIJkIMVEYY0akozhAf8u',
            'features' => ['Everything in Professional', 'Unlimited automations', 'Unlimited organizations', 'Unlimited members', 'Priority support', 'Custom integrations', 'SLA guarantee', 'Dedicated account manager'],
            'limits' => ['workflows' => -1, 'automations' => -1, 'members' => -1],
        ],
    ];

    public function plans(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $org = Organization::find($user->activeOrgId());

        $plans = collect(self::PLANS)->map(function (array $plan, string $key) use ($org) {
            return [
                ...$plan,
                'key' => $key,
                'isCurrent' => ($org?->plan ?? 'starter') === $key,
            ];
        })->values();

        return response()->json([
            'data' => [
                'plans' => $plans,
                'currentPlan' => $org?->plan ?? 'starter',
                'subscriptionStatus' => $org?->subscription_status ?? 'none',
                'trialEndsAt' => $org?->trial_ends_at?->toISOString(),
                'subscriptionEndsAt' => $org?->subscription_ends_at?->toISOString(),
            ],
        ]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan' => 'required|in:professional,enterprise',
        ]);

        /** @var User $user */
        $user = $request->user();
        $org = Organization::find($user->activeOrgId());

        if (!$org) {
            return response()->json(['error' => 'Organization not found'], 404);
        }

        if (!config('services.stripe.secret')) {
            return response()->json([
                'data' => [
                    'url' => null,
                    'message' => 'Stripe is not configured. Add STRIPE_SECRET_KEY to backend/.env to enable billing.',
                ],
            ]);
        }

        $stripe = $this->stripe();

        if (!$org->stripe_customer_id) {
            $customer = $stripe->customers->create([
                'email' => $user->email,
                'name' => $org->name,
                'metadata' => ['organization_id' => $org->id],
            ]);
            $org->update(['stripe_customer_id' => $customer->id]);
        }

        $planData = self::PLANS[$validated['plan']];

        $session = $stripe->checkout->sessions->create([
            'customer' => $org->stripe_customer_id,
            'mode' => 'subscription',
            'line_items' => [[
                'price' => $planData['priceId'],
                'quantity' => 1,
            ]],
            'success_url' => config('app.frontend_url', 'http://localhost:3000') . '/dashboard/settings?billing=success',
            'cancel_url' => config('app.frontend_url', 'http://localhost:3000') . '/dashboard/settings?billing=cancelled',
            'metadata' => [
                'organization_id' => $org->id,
                'plan' => $validated['plan'],
            ],
        ]);

        return response()->json(['data' => ['url' => $session->url]]);
    }

    public function portal(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $org = Organization::find($user->activeOrgId());

        if (!$org?->stripe_customer_id) {
            return response()->json(['error' => 'No billing account found. Subscribe to a plan first.'], 400);
        }

        if (!config('services.stripe.secret')) {
            return response()->json([
                'data' => ['url' => null, 'message' => 'Stripe is not configured.'],
            ]);
        }

        $stripe = $this->stripe();

        $session = $stripe->billingPortal->sessions->create([
            'customer' => $org->stripe_customer_id,
            'return_url' => config('app.frontend_url', 'http://localhost:3000') . '/dashboard/settings',
        ]);

        return response()->json(['data' => ['url' => $session->url]]);
    }

    public function webhook(Request $request): \Illuminate\Http\Response
    {
        $webhookSecret = config('services.stripe.webhook_secret');

        if (!$webhookSecret) {
            return response('Webhook secret not configured', 400);
        }

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                $webhookSecret
            );
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($event->data->object),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event->data->object),
            default => null,
        };

        return response('OK', 200);
    }

    private function handleCheckoutCompleted(object $session): void
    {
        $orgId = $session->metadata->organization_id ?? null;
        $plan = $session->metadata->plan ?? null;

        if (!$orgId || !$plan) return;

        Organization::where('id', $orgId)->update([
            'plan' => $plan,
            'stripe_subscription_id' => $session->subscription,
            'subscription_status' => 'active',
        ]);
    }

    private function handleSubscriptionUpdated(object $subscription): void
    {
        Organization::where('stripe_subscription_id', $subscription->id)->update([
            'subscription_status' => $subscription->status,
        ]);
    }

    private function handleSubscriptionDeleted(object $subscription): void
    {
        Organization::where('stripe_subscription_id', $subscription->id)->update([
            'plan' => 'starter',
            'subscription_status' => 'canceled',
            'stripe_subscription_id' => null,
        ]);
    }
}
