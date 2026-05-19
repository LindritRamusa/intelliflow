<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('plan');
            $table->string('stripe_subscription_id')->nullable()->after('stripe_customer_id');
            $table->enum('subscription_status', ['active', 'trialing', 'past_due', 'canceled', 'incomplete', 'none'])->default('none')->after('stripe_subscription_id');
            $table->timestamp('trial_ends_at')->nullable()->after('subscription_status');
            $table->timestamp('subscription_ends_at')->nullable()->after('trial_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['stripe_customer_id', 'stripe_subscription_id', 'subscription_status', 'trial_ends_at', 'subscription_ends_at']);
        });
    }
};
