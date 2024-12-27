<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\CodesController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LotteryController;
use App\Http\Controllers\Admin\Partner\PartnerController;
use App\Http\Controllers\Admin\User\UserProfileController;
use App\Http\Controllers\Admin\Distributor\DistributorController;
use App\Http\Controllers\DistrackModel\DistrackModelController;
use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\Admin\AdminSeriesController;
use App\Http\Controllers\Admin\Feature\FeatureController;
use App\Http\Controllers\Admin\User\UserPasswordResetController;
use App\Http\Controllers\Admin\User\UserSettingController;
use App\Http\Controllers\Web\Blog\BlogController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ContactMessage\ContactMessageController;
use App\Http\Controllers\Admin\Localization\LocalizationController;
use App\Http\Controllers\Admin\LanguageController;

Route::middleware(["auth","isSuperAdmin"])->group(function(){
// Route::middleware(["auth","isSuperAdmin","verified"])->group(function(){

//Route::group([
//    'middleware' => ['auth']
//],function (){

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix("notifications")->name("notifications.")->group(function(){
        Route::get('/', [DashboardController::class, 'notifications'])->name('index');
        Route::post('mark-as-read', [DashboardController::class, 'markNotification'])->name('markAsRead');
    });

    Route::prefix("user-role-management")->group(function () {
        Route::resource("roles", RoleController::class);
        Route::resource("users", UserController::class);
    });

    Route::resource("partners",PartnerController::class);
    Route::resource("distributors",DistributorController::class);
    Route::resource("features",FeatureController::class);
    Route::resource("blogs",BlogController::class);
    Route::resource("contact-messages",ContactMessageController::class);
    Route::get("contact-messages/status/{id}",[ContactMessageController::class, 'changeStatus'])->name('contact-messages.change.status');


    Route::resource("models",DistrackModelController::class);

    Route::resource("users",UserController::class);
    Route::resource("user-settings",UserSettingController::class);

    /*Route::prefix("distributors")->name("distributors.")->group(function(){
        Route::get("/",[DashboardController::class,"allDistributors"])->name("index");
    });*/
    Route::get('profile', [UserProfileController::class, 'userProfile'])->name('user.profile');
    Route::post('profile', [UserProfileController::class, 'userProfileUpdate'])->name('user.profileUpdate');

    Route::get('user/reset-password', [UserPasswordResetController::class, 'userPasswordReset'])->name('userPasswordReset');
    Route::post('user/reset-password', [UserPasswordResetController::class, 'updatePassword'])->name('updatePassword.update');
    //Route::post('profile', [UserProfileController::class, 'userProfileUpdate'])->name('user.profileUpdate');


    // Route::controller(BrandController::class)->group(function() {
    //     Route::get('/brands/lists', 'lists')->name('brand.lists');
    //     Route::match(['get', 'post'], '/brands/create', 'create')->name('brand.generate');
    //     Route::match(['get', 'post'], '/brands/update/{id}', 'update')->name('brand.update');
    //     Route::get('/view-brand/{brandId}', 'show')->name('brand.show');
    // });

    Route::controller(PackageController::class)->group(function() {
        Route::get('/packages/lists', 'lists')->name('package.lists');
        Route::match(['get', 'post'], '/packages/create', 'create')->name('package.generate');
        Route::match(['get', 'post'], '/packages/update/{id}', 'update')->name('package.update');
        Route::get('/view-package/{packageId}', 'show')->name('package.show');
    });

    Route::resource("series",AdminSeriesController::class);
    Route::resource("stores",StoreController::class);
    Route::prefix("stores/all-stores")->name("stores.")->group(function(){
        Route::get("view",[StoreController::class,"allStoreGoogleMapViews"])->name("storeGoogleMapView");
    });

//    Route::controller(StoreController::class)->group(function() {
//        Route::get('/stores/lists', 'lists')->name('store.lists');
//        Route::match(['get', 'post'], '/stores/create', 'create')->name('store.generate');
//        Route::match(['get', 'post'], '/stores/update/{id}', 'update')->name('store.update');
//        Route::get('/view-store/{storeId}', 'show')->name('store.show');
//    });

    Route::controller(CodesController::class)->group(function() {
        Route::match(['get', 'post'], '/codes/lists', 'lists')->name('code.lists');
        Route::match(['get', 'post'], '/codes/scanned', 'scanned_lists')->name('code.scanned.lists');
        Route::match(['get', 'post'], '/codes/import', 'generate')->name('code.generate');
        Route::match(['get', 'post'], '/codes/each', 'generateEach')->name('code.generate.each');
        Route::get('/view-code/{codeId}', 'show')->name('code.show');
        Route::get('/download-zip/{brandId?}', 'zipDownload')->name('code.zip.download');
        Route::get('/download/{filename}', 'fileDownload')->name('file.download');
        Route::get('/export/{modelId?}', 'exportToExcel')->name('code.export');
    });

    Route::resource("languages",LanguageController::class);

    Route::prefix("localizations")->name("localizations.")->group(function(){
        Route::get('localizations', [LocalizationController::class, 'index'])->name('index');
        Route::get('localizations/{localization}/edit', [LocalizationController::class, 'edit'])->name('edit');
        Route::post('localizations/{language_id}', [LocalizationController::class, 'update'])->name('update');
    });

   /* Route::controller(LotteryController::class)->group(function() {
        Route::get('/lotteries/lists', 'lists')->name('lottery.lists');
        Route::match(['get', 'post'], '/lotteries/create', 'create')->name('lottery.create');
        Route::match(['get', 'post'], '/lotteries/update/{id}', 'update')->name('lottery.update');
        Route::get('/change-status/{id}', 'changeStatus')->name('lottery.change.status');
        Route::delete('/delete/{id}', 'delete')->name('lottery.delete');
        Route::get('/lottery/applicants/{id}', 'applicants')->name('lottery.applicants');
        Route::post('/lottery/applicants/winner/{id}', 'selectWinner')->name('lottery.applicant.winner');
        Route::post('/lottery/applicants/random-winner/{lotteryId}', 'randomWinner')->name('lottery.applicant.random.winner');
    });*/
});
