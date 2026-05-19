<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::post('/billing/webhook', [BillingController::class, 'webhook']);

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    Route::apiResource('workflows', WorkflowController::class);
    Route::patch('/workflows/{workflow}/toggle', [WorkflowController::class, 'toggle']);

    Route::apiResource('automations', AutomationController::class);
    Route::patch('/automations/{automation}/toggle', [AutomationController::class, 'toggle']);

    Route::get('/ai/logs', [AiController::class, 'logs']);
    Route::post('/ai/logs', [AiController::class, 'store']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead']);
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead']);

    Route::get('/analytics/overview', [AnalyticsController::class, 'overview']);
    Route::get('/analytics/workflows', [AnalyticsController::class, 'workflows']);
    Route::get('/analytics/activity', [AnalyticsController::class, 'activity']);
    Route::get('/analytics/predictions', [AnalyticsController::class, 'predictions']);

    Route::get('/recruitment/stats', [CandidateController::class, 'stats']);
    Route::apiResource('candidates', CandidateController::class);
    Route::patch('/candidates/{candidate}/analysis', [CandidateController::class, 'saveAnalysis']);

    Route::get('/articles/categories', [ArticleController::class, 'categories']);
    Route::apiResource('articles', ArticleController::class);

    Route::get('/logistics/stats', [LogisticsController::class, 'stats']);
    Route::apiResource('shipments', LogisticsController::class);
    Route::patch('/shipments/{shipment}/optimization', [LogisticsController::class, 'saveOptimization']);

    Route::get('/organizations', [OrganizationController::class, 'index']);
    Route::post('/organizations', [OrganizationController::class, 'store']);
    Route::get('/organizations/current', [OrganizationController::class, 'current']);
    Route::patch('/organizations/{organization}/switch', [OrganizationController::class, 'switch']);

    Route::get('/billing/plans', [BillingController::class, 'plans']);
    Route::post('/billing/checkout', [BillingController::class, 'checkout']);
    Route::post('/billing/portal', [BillingController::class, 'portal']);
});
