<?php
use App\Http\Controllers\Partner\Dashboard\PartnerDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\User\UserProfileController;
use App\Http\Controllers\Admin\User\UserPasswordResetController;
use App\Http\Controllers\Admin\User\UserSettingController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\Distributor\DistributorDashboardController;

Route::middleware(["auth", "isPartner","IsActive","verified","superPowerMiddleware"])->group(function () {
    Route::get("/", [PartnerDashboardController::class, "index"])->name("dashboard");
    Route::get("my-distributors", [PartnerDashboardController::class, "myDistributors"])->name("myDistributors");

    Route::prefix("notifications")->name("notifications.")->group(function(){
        Route::get('/', [DistributorDashboardController::class, 'notifications'])->name('index');
        Route::post('mark-as-read', [DistributorDashboardController::class, 'markNotification'])->name('markAsRead');
    });

    Route::prefix("user-role-management")->group(function () {
        Route::resource("roles", RoleController::class);
        Route::resource("users", UserController::class);
    });

    Route::get('profile', [UserProfileController::class, 'userProfile'])->name('user.profile');
    Route::post('profile', [UserProfileController::class, 'userProfileUpdate'])->name('user.profileUpdate');

    Route::get('user/reset-password', [UserPasswordResetController::class, 'userPasswordReset'])->name('userPasswordReset');
    Route::post('user/reset-password', [UserPasswordResetController::class, 'updatePassword'])->name('updatePassword.update');
    Route::resource("user-settings",UserSettingController::class);

    Route::prefix("stores/all-stores")->name("stores.")->group(function(){
        Route::get("view",[\App\Http\Controllers\Admin\StoreController::class,"allStoreGoogleMapViews"])->name("storeGoogleMapView");
    });
});

