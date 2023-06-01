<?php

use Illuminate\Support\Facades\Route;


Route::prefix('v1')->name('api.v1.')->group(function() {
    // Auth
    Route::prefix('auth')->name('auth.')
    ->controller(\App\Http\Controllers\Auth\AuthenticatedJwtController::class)->group(function () {
        Route::post('login', 'store')->name('login');
        Route::middleware('auth')->group(function () {
            Route::post('refresh', 'refresh')->name('refresh');
            Route::post('verify-token', 'verifyToken')->name('verifyToken');
            Route::post('logout', 'destroy')->name('logout');
        });
    });

    // Tool
    Route::prefix('auth')->name('auth.')->middleware(['tool'])->group(function () {
        Route::post('tool/his',[\App\Http\Controllers\Auth\AuthenticatedToolController::class, 'getHisByLicense'])->name('tool.his');
        Route::post('tool/login',[\App\Http\Controllers\Auth\AuthenticatedToolController::class, 'store'])->name('tool.login');
        Route::post('tool/register',[\App\Http\Controllers\Auth\AuthenticatedToolController::class, 'register'])->name('tool.register');
        Route::post('tool/refresh',[\App\Http\Controllers\Auth\AuthenticatedToolController::class, 'refresh'])->middleware('auth')->name('tool.refresh');
        Route::post('tool/logout',[\App\Http\Controllers\Auth\AuthenticatedToolController::class, 'destroy'])->middleware('auth')->name('tool.logout');

    });
    // Resources
    Route::middleware('auth')->group(function () {
        Route::get('users/types', [\App\Http\Controllers\Api\V1\UserController::class, 'getUserType'])->name('users.types');
        Route::get('users/export', [\App\Http\Controllers\Api\V1\UserController::class , 'export'])->name('user.export');
        Route::apiResource('users', \App\Http\Controllers\Api\V1\UserController::class);
        Route::apiResource('roles', \App\Http\Controllers\Api\V1\RoleController::class);
        Route::post('permissions/refresh', [\App\Http\Controllers\Api\V1\PermissionController::class, 'refresh'])->name('permissions.refresh');
        Route::apiResource('permissions', \App\Http\Controllers\Api\V1\PermissionController::class)->only('index');

        // Route::get('plans/{plan}/features', [\App\Http\Controllers\Api\V1\PlanController::class, 'getFeatures'])->name('plans.features');
        Route::get('plans/{plan}/subscriptions', [\App\Http\Controllers\Api\V1\PlanController::class, 'getSubscriptions'])->name('plans.subscriptions');
        Route::apiResource('plans', \App\Http\Controllers\Api\V1\PlanController::class);
        // Route::apiResource('planfeatures', \App\Http\Controllers\Api\V1\PlanFeatureController::class);
        Route::get('plansubscriptions/getDashboardsTags', [\App\Http\Controllers\Api\V1\PlanSubscriptionController::class,'getDashboardsTags'])->name('plansubscriptions.getDashboardsTags');
        Route::get('plansubscriptions/export', [\App\Http\Controllers\Api\V1\PlanSubscriptionController::class , 'export'])->name('plansubscriptions.export');
        Route::get('plansubscriptions/exportTrial', [\App\Http\Controllers\Api\V1\PlanSubscriptionController::class , 'exportTrial'])->name('plansubscriptions.exportTrial');
        Route::get('plansubscriptions/exportExpire', [\App\Http\Controllers\Api\V1\PlanSubscriptionController::class , 'exportExpire'])->name('plansubscriptions.exportExpire');
        Route::apiResource('plansubscriptions', \App\Http\Controllers\Api\V1\PlanSubscriptionController::class)->only('index');
        Route::get('products/{product}/plans', [\App\Http\Controllers\Api\V1\ProductController::class, 'getPlans'])->name('products.plans');
        Route::get('products/{product}/subscriptions', [\App\Http\Controllers\Api\V1\ProductController::class, 'getSubscriptions'])->name('products.subscriptions');
        Route::apiResource('products', \App\Http\Controllers\Api\V1\ProductController::class);
        Route::get('invoices/getDashboardChart', [\App\Http\Controllers\Api\V1\InvoiceController::class ,'getDashboardChart'])->name('invoices.getDashboardChart');
        Route::get('invoices/getDashboardRevenue', [\App\Http\Controllers\Api\V1\InvoiceController::class ,'getDashboardRevenue'])->name('invoices.getDashboardRevenue');
        Route::apiResource('invoices', \App\Http\Controllers\Api\V1\InvoiceController::class);
        Route::apiResource('teams', \App\Http\Controllers\Api\V1\TeamController::class);
        Route::apiResource('branches', \App\Http\Controllers\Api\V1\BranchController::class);
        Route::apiResource('kpis', \App\Http\Controllers\Api\V1\KpiController::class);
        Route::get('activities/list', [\App\Http\Controllers\Api\V1\ActivityController::class, 'list'])->name('activities.list');
        Route::apiResource('activities', \App\Http\Controllers\Api\V1\ActivityController::class)->only(['index', 'show']);
        Route::get('banks/list-bank', [\App\Http\Controllers\Api\V1\BankController::class, 'listBank'])->name('banks.listBank');
        Route::apiResource('banks', \App\Http\Controllers\Api\V1\BankController::class);
    });
 });
