<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $ownOrg = $user->organization;
        $memberOrgs = $user->organizations()->get();

        $orgs = collect([$ownOrg])
            ->merge($memberOrgs)
            ->unique('id')
            ->filter()
            ->map(fn (Organization $org) => $this->format($org, $user));

        return response()->json(['data' => $orgs->values()]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $org = Organization::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . substr(uniqid(), -6),
            'plan' => 'starter',
        ]);

        $user->organizations()->attach($org->id, ['role' => 'company_admin']);

        $user->update(['active_organization_id' => $org->id]);

        return response()->json([
            'data' => $this->format($org, $user),
            'message' => 'Organization created',
        ], 201);
    }

    public function switch(Request $request, Organization $organization): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $hasAccess = $user->organization_id === $organization->id
            || $user->organizations()->where('organizations.id', $organization->id)->exists();

        if (!$hasAccess) {
            abort(403, 'You do not have access to this organization');
        }

        $user->update(['active_organization_id' => $organization->id]);
        $user->refresh();

        return response()->json([
            'data' => $this->format($organization, $user),
            'message' => 'Switched to ' . $organization->name,
        ]);
    }

    public function current(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $org = $user->activeOrganization() ?? $user->organization;

        if (!$org) {
            return response()->json(['data' => null]);
        }

        return response()->json(['data' => $this->format($org, $user)]);
    }

    private function format(Organization $org, User $user): array
    {
        return [
            'id' => (string) $org->id,
            'name' => $org->name,
            'slug' => $org->slug,
            'plan' => $org->plan,
            'logoUrl' => $org->logo_url,
            'subscriptionStatus' => $org->subscription_status,
            'isActive' => $user->activeOrgId() === $org->id,
            'createdAt' => $org->created_at?->toISOString(),
        ];
    }
}
