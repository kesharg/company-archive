<?php

use Carbon\Carbon;
use App\Utils\AppCache;
use App\Utils\AppStatic;
use App\Utils\SessionLab;
use Illuminate\Support\Str;
use App\Services\Log\LogService;
use App\Services\File\FileService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

if(!function_exists("user")){
    function user(){

        return auth()->user();
    }
}

if(!function_exists("isActiveUser")){
    function isActiveUser(){

        return user()->is_active == 1 || user()->is_active == true;
    }
}

if(!function_exists("userId")){
    function userId(){

        if(!isLoggedIn()){

            abort(401);
        }


        return auth()->id();
    }
}

if(!function_exists("isLoggedIn")){
    function isLoggedIn(){

        return auth()->check();
    }
}

if(!function_exists("getUserType")){
    function getUserType(){

        if(!isLoggedIn()){

            abort(401);
        }

        return user()->user_type;
    }
}

if(!function_exists("getUserTypeClass")){
    function getUserTypeClass($userType){

        return match($userType){
            "admin"             => "bg-primary",
            "admin_staff"       => "bg-primary",
            "partner"           => "bg-success",
            "partner_staff"     => "bg-success",
            "distributor"       => "bg-info",
            "distributor_staff" => "bg-info",
        };
    }
}

if(!function_exists("isAdmin")){
    function isAdmin(){

        if(!isLoggedIn()){

            abort(401);
        }

        return  getUserType() == appStatic()::TYPE_ADMIN;
    }
}


if(!function_exists("isAdminStaff")){
    function isAdminStaff(){

        if(!isLoggedIn()){

            abort(401);
        }

        return getUserType() === appStatic()::TYPE_ADMIN_STAFF;
    }
}

if(!function_exists("isPartner")){
    function isPartner(){

        if(!isLoggedIn()){

            abort(401);
        }

        return getUserType() === appStatic()::TYPE_PARTNER;
    }
}

if(!function_exists("isPartnerStaff")){
    function isPartnerStaff(){

        if(!isLoggedIn()){

            abort(401);
        }

        return getUserType() === appStatic()::TYPE_PARTNER_STAFF;
    }
}

if(!function_exists("isDistributor")){
    function isDistributor(){

        if(!isLoggedIn()){

            abort(401);
        }

        return getUserType() === appStatic()::TYPE_DISTRIBUTOR;
    }
}

if(!function_exists("errorName")){
    function errorName($name){


        return ;
    }
}

if(!function_exists("showRequiredStar")){
    function showRequiredStar(){


        return "*";
    }
}

# Text Replace
if (!function_exists("textReplace")) {
    function textReplace($value, $searchKey = ".", $replaceWith = " ")
    {

        return str_replace($searchKey, $replaceWith, $value);
    }
}

if(!function_exists("getParentId")){
    function getParentId(){

        if(!isLoggedIn()){

            abort(401);
        }

        if(isAdmin() || isDistributor() || isPartner()){
            return userId();
        }

        return user()->parent_user_id;
    }
}

if(!function_exists("isDistributorStaff")){
    function isDistributorStaff(){

        if(!isLoggedIn()){

            abort(401);
        }

        return getUserType() === appStatic()::TYPE_DISTRIBUTOR_STAFF;
    }
}


if(!function_exists("maxPaginateNo")){
    function maxPaginateNo($max = 10){

        $request = request();

        return  $request->has('per_page') ? $request->per_page : $max;
    }
}


if(!function_exists("appStatic")){
    function appStatic(){


        return new \App\Utils\AppStatic();
    }
}

# Random String Number Generator

if (!function_exists('randomStringNumberGenerator')) {
    function randomStringNumberGenerator(
        $length = 6,
        $includeNumbers = true,
        $includeLetters = false,
        $includeSymbols = false
    ) {
        $chars = [
            'letters' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'numbers' => '0123456789',
            'symbols' => '!@#$%^&*()-_+=<>?'
        ];

        $password = '';
        $charSets = [];

        if ($includeLetters) {
            $charSets[] = $chars['letters'];
        }

        if ($includeNumbers) {
            $charSets[] = $chars['numbers'];
        }

        if ($includeSymbols) {
            $charSets[] = $chars['symbols'];
        }

        $charSetsCount = count($charSets);

        if ($charSetsCount === 0) {
            return 'Invalid character set configuration';
        }

        for ($i = 0; $i < $length; $i++) {
            $charSet = $charSets[$i % $charSetsCount];
            $password .= $charSet[random_int(0, strlen($charSet) - 1)];
        }

        return $password;
    }
}



if(!function_exists("getLanguageByCode")){
    function getLanguageByCode($code = "en"){
        if(isCacheExists($code)){

            return getCache($code);
        }

        $language = \App\Models\Language::query()->where("code",$code)->firstOrFail();
        if($language){
            return setCacheData($code, $language);
        }
    }
}


if(!function_exists("localize")){
    function localize($key, $appLocale = null){
        $locale = !is_null($appLocale) ? $appLocale :  app()->getLocale();
        $cacheKey = 'localization_' . $locale;

        // Get language IDs
        $currentLanguageId = getLanguageByCode($locale)->id;
        $englishLanguageId = getLanguageByCode()->id;

        if (!$currentLanguageId || !$englishLanguageId) {
            return $key; // Fallback if language IDs are not found
        }

        $localizations = Cache::rememberForever($cacheKey, function () use ($currentLanguageId) {
            return DB::table('localizations')
                ->where('language_id', $currentLanguageId)
                ->pluck('value', 'key')
                ->toArray();
        });

        if (!isset($localizations[$key])) {
            // Check and insert default English value if it doesn't exist
            $englishLocalization = DB::table('localizations')
                ->where('language_id', $englishLanguageId)
                ->where('key', $key)
                ->first();

            if (!$englishLocalization) {
                DB::table('localizations')->insert([
                    'key' => $key,
                    'language_id' => $englishLanguageId,
                    'value' => $key,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $currentLocalizationCacheKey = 'localization_' . $currentLanguageId;

            if(isCacheExists($currentLocalizationCacheKey)){
                $currentLocalization = getCache($currentLocalizationCacheKey);
            }else{
                $currentLocalization =  setCacheData($currentLocalizationCacheKey,DB::table('localizations')
                    ->where('language_id', $currentLanguageId)
                    ->where('key', $key)
                    ->first());
            }
            // Check and insert default current locale value if it doesn't exist

            if (!$currentLocalization) {
                DB::table('localizations')->insert([
                    'key' => $key,
                    'language_id' => $currentLanguageId,
                    'value' => $key,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Refresh the cache
            Cache::forget($cacheKey);
            $localizations = Cache::rememberForever($cacheKey, function () use ($currentLanguageId) {
                return DB::table('localizations')
                    ->where('language_id', $currentLanguageId)
                    ->pluck('value', 'key')
                    ->toArray();
            });
        }

        return $localizations[$key] ?? $key;
    }
}

if(!function_exists("isRouteShow")){
    function isRouteShow($routeName = null){

        //TODO:: router show or not code here
        return true;
    }
}

if(!function_exists("ddError"))
{
    function ddError($e){

        return dd(errorArray($e));
    }
}

/**
 * Beware of changes, Because it's using as API Error Response
 * */
if(!function_exists("errorArray")){
    function errorArray($e){

        return [
            "title"         => $e->getMessage(),
            "file"          => $e->getFile(),
            "line"          => $e->getLine(),
        ];
    }
}


if(!function_exists("commonLog")) {
    /**
     * @throws JsonException
     */
    function commonLog($title, array $payloads = [], $channel = "daily", $writeToLog = true){

        if(!$writeToLog) {
            return false;
        }

        \logService()->commonLog(
            $title,
            $payloads,
            $channel
        );
    }
}

if(!function_exists("logService")) {
    function logService(){

        return new LogService();
    }
}

if(!function_exists("appStatic")) {
    function appStatic(){

        return new AppStatic();
    }
}

if(!function_exists("sessionLab")) {
    function sessionLab(){

        return new SessionLab();
    }
}

if (!function_exists('isSvg')) {
    function isSvg(string $extension)
    {
        return $extension === "svg";
    }
}


# FIle Name Prefix
if (!function_exists('fileRenamePrefix')) {
    function fileRenamePrefix()
    {
        return (new FileService())::FILE_RENAME_PREFIX;
    }
}

if (!function_exists('fileRename')) {
    /**
     * @throws Exception
     */
    function fileRename() : string
    {
        return now()->format("Ymd") . fileRenamePrefix() . time() . random_int(1111, 9999);
    }
}

if (!function_exists('currentUrl')) {

    function currentUrl()
    {
        return request()->fullUrl();
    }
}

if (!function_exists("currentRoute")) {
    function currentRoute() : string
    {
        try{
            return  request()->route()?->getName() ?? "home";
        }
        catch(\Throwable $e){
            info("Current Route Name error : ".$e->getMessage());
            return $e->getMessage();
        }
    }
}

if (!function_exists("manageDateTime")) {
    function manageDateTime($dateTime = null, $formatType = 1)
    {
        $dateTime = is_null($dateTime) ? now() : $dateTime;

        return carbonParse($dateTime)->format(getDateTimeFormat($formatType));
    }
}

if (!function_exists('carbonParse')) {
    function carbonParse($dateTime = null)
    {
        $dateTime = empty($dateTime) ? now() : $dateTime;
        return Carbon::parse($dateTime);
    }
}

if (!function_exists("clientIP")) {
    function clientIP()
    {

        return request()->ip();
    }
}


if (!function_exists("appCache")) {
    function appCache()
    {

        return new AppCache();
    }
}

# Image Mimes
if (!function_exists('imageMimes')) {
    function imageMimes()
    {

        return "mimes:jpg,png,webp,bimp,svg";
    }
}

# Set Active Status
if (!function_exists("setIsActive")) {
    function setIsActive()
    {

        return request()->has("is_active") ? request()->is_active : 0;
    }
}

if (!function_exists("fileService")) {
    function fileService()
    {

        return new FileService();
    }
}

# Default Disk
if (!function_exists('setDefaultDisk')) {
    function setDefaultDisk()
    {
        return "public";
    }
}


if (!function_exists('allowedImageExtensions')) {
    function allowedImageExtensions()
    {

        return [
            "jpeg",
            "jpg",
            "png",
            "bimp",
            "svg",
            "webp",
        ];
    }
}


if (!function_exists('allowedMediaExtensions')) {
    function allowedMediaExtensions()
    {

        return [
            "mp4",
            "mp3",
            "amr",
            "wev",
        ];
    }
}


if (!function_exists("getDateTimeFormat")) {
    function getDateTimeFormat($formatType = 1)
    {

        return [
            1 => "h:i:s,v d-m-Y",
            2 => "h:i A d-m-Y",
            3 => "H:i A d-M-Y",
            4 => "h:i A d-M-Y",
            5 => "d-M-Y",
            6 => "h:i A"
        ][$formatType] ?? "";
    }
}

# Slug Maker
if (!function_exists("slugMaker")) {
    function slugMaker($value)
    {
        return Str::slug($value);
    }
}

# Flush Message
if (!function_exists("flashMessage")) {
    function flashMessage($message, $type = "success")
    {
        return session()->flash($type,$message);
    }
}

# Dashboard Prefix
if (!function_exists("dashboardPrefix")) {
    function dashboardPrefix()
    {
        if(isAdmin() || isAdminStaff()){
            return "Admin Dashboard";
        }

        if(isDistributor() || isDistributorStaff()){
            return "Distributor Dashboard";
        }

        if(isPartner() || isPartnerStaff()){
            return "Partner Dashboard";
        }
    }
}

# Dashboard Prefix
if (!function_exists("urlVersion")) {
    function urlVersion($file = null)
    {
        if(is_null($file)){
            return asset("logo/logo.png?v=".time());
        }

        if(!file_exists($file)){
            return asset("logo/logo.png?v=".time());
        }

        return asset($file."?v=".time());
    }
}


# currencySymbol
if (!function_exists("currencySymbol")) {
    function currencySymbol()
    {
        //TODO:: System setting currency symbol will implement here
        return "$";
    }
}

# currencySymbol
if (!function_exists("showExpireDateTime")) {
    function showExpireDateTime($value)
    {

        return carbonParse($value)->format("d-M Y, 23:59:59");
    }
}

# currencySymbol
if (!function_exists("cacheClear")) {
    function cacheClear()
    {
        \Artisan::call("optimize:clear");
    }
}


# Find By Id

if (!function_exists("findById")) {
    function findById(\Illuminate\Database\Eloquent\Model $model,  $id, array | string $withRelationShip = [])
    {

        // Relationship Add
        (!empty($withRelationShip) ? $model->with($withRelationShip) : true);

        if (is_array($id)) {
            return $model->find($id);
        }

        return  $model->findOrFail($id);
    }
}


#Get the Subdomain from the Route
if (!function_exists("getUserObject")) {
    function getUserObject()
    {
       return isDistributor() ? user() : user()->parentUser;
    }
}

#Get the Subdomain from the Route
if (!function_exists("getSubDomain")) {
    function getSubDomain()
    {
        $subdomain = explode('.', request()->getHost());

        return count($subdomain) > 2 ? $subdomain[0] : null;
    }
}

#Get the Subdomain from the Route
if (!function_exists("getAppUrl")) {
    function getAppUrl()
    {
        $appUrl = config("app.url");

        return explode("//", $appUrl)[1];
    }
}


#Get the Subdomain from the Route
if (!function_exists("serialNoGenerator")) {
    function serialNoGenerator($start = 1, $addLeft = true, $padString = "0", $lentgh = 8) : int
    {

        return (int) str_pad($start, $lentgh, $padString, $addLeft ? STR_PAD_LEFT : STR_PAD_RIGHT);
    }
}



#Get the Subdomain from the Route
if (!function_exists("showDateTime")) {
    function showDateTime($value, $formatType = 4)
    {

        return carbonParse($value)->format(getDateTimeFormat($formatType));
    }
}

#Get the Subdomain from the Route
if (!function_exists("getSubTotal")) {
    function getSubTotal($price, $quantity)
    {
        if($quantity == 0 || $price == 0){
            return 0;
        }

        return $price*$quantity;
    }
}

#Get the Subdomain from the Route
if (!function_exists("getDistributorId")) {
    function getDistributorId($user_id = null)
    {
        if(is_null($user_id)){
            return user()->distributor?->id ?:null;
        }

        $distributor  = (new \App\Services\Models\DistrackModel\DistrackModelService())->getDistributorByUserId($user_id);
    }
}
#Get the Subdomain from the Route
if (!function_exists("isRead")) {
    function isRead($readAt = null)
    {
        return !empty($readAt);
    }
}

#Get the Subdomain from the Route
if (!function_exists("isExpired")) {
    function isExpired($expire_date_time)
    {
        $expiresAt = carbonParse($expire_date_time);

        return Carbon::now()->greaterThanOrEqualTo($expiresAt);
    }
}


#Get the Subdomain from the Route
if (!function_exists("getGooglePlaceApi")) {
    function getGooglePlaceApi()
    {
       $googlePlaceApiKey= \appStatic()::GOOGLE_PLACE_API_KEY;
        return env("GOOGLE_PLACE_API_KEY", $googlePlaceApiKey);
    }
}

if (!function_exists("getStripeKey")) {
    function getStripeKey()
    {
       $stripeKey= \appStatic()::STRIPE_KEY;
        return env("STRIPE_KEY", $stripeKey);
    }
}

if (!function_exists("getStripeSecret")) {
    function getStripeSecret()
    {
       $STRIPE_SECRET= \appStatic()::STRIPE_SECRET;
        return env("STRIPE_SECRET", $STRIPE_SECRET);
    }
}

if (!function_exists("getStripeWebhookSecret")) {
    function getStripeWebhookSecret()
    {
       $STRIPE_WEBHOOK_SECRET= \appStatic()::STRIPE_WEBHOOK_SECRET;
        return env("STRIPE_WEBHOOK_SECRET", $STRIPE_WEBHOOK_SECRET);
    }
}

# Logged in session Destroy
if (!function_exists('loggedInSessionDestroy')) {
    function loggedInSessionDestroy()
    {

        session()->forget("user_powers");
        session()->forget("menu_permission_version");
        session()->forget("user_routes");
    }
}

# Is Route exits
if (!function_exists('isRouteExists')) {
    function isRouteExists($route = null)
    {
        // If it's Admin Allow the routes
        if (isAdmin() || isDistributor() || isPartner()) {
            return true;
        }

        // return true; // Temporary till production.
        $route = is_null($route) ? currentRoute() : $route;

        return in_array($route, userRoutesSession()); // true OR false;
    }
}


# Is Route exits
if (!function_exists('userRoutesSession')) {
    function userRoutesSession()
    {

        return session("user_routes")  ?? [];
    }
}


# Set Active Status
if (!function_exists("setActiveStatus")) {
    function setActiveStatus()
    {

        return request()->has("is_active") ? request()->is_active : 0;
    }
}


/**
 * Cache ENGINE Start
 * */

 if (!function_exists('getCache')) {
    function getCache($keyword)
    {

        return Cache::get($keyword) ?: null;
    }
}

if (!function_exists('isCacheExists')) {
    function isCacheExists($keyword)
    {
        return Cache::has($keyword);
    }
}

if (!function_exists('setCacheData')) {
    function setCacheData($keyword, $data, $time = (60*24*30))
    {

        return Cache::remember($keyword, $time, function () use ($data){
           return $data;
        });
    }
}


/**
 * Cache ENGINE END
 * */


if (!function_exists("activeStatus")) {
    function activeStatus($activeStatus = 1)
    {

        $activeClasses   = 'fas fa-check-circle';
        $inactiveClasses = 'fa fa-times';

        $spanClass = $activeStatus == 1 ? "success" : "danger";
        //       $iClass    = $activeStatus == 1 ? $activeClasses : $inactiveClasses;

        $text = $activeStatus == 1 ? "Active" : "In-Active";

        return ' <span class="badge rounded-pill bg-' . $spanClass . '"> ' . $text . ' </span>';
    }
}


if (!function_exists("activeStatusInside")) {
    function activeStatusInside()
    {
        //return \appStatic()::ACTIVE_STATUS_INSIDE;
    }
}

# Delete Action
if (!function_exists("deleteAction")) {
    function deleteAction($route, $id, $method = "DELETE")
    {
        return '<a
                data-href="' . $route . '"
                data-id="' . $id . '"
                data-method="' . $method . '"
                class="dropdown-item erase"
                href="javascript:void(0);">
                <i data-feather="trash" class="me-2"></i>Delete
            </a>';
    }
}

# User Permissions
if (!function_exists("userPermissions")) {
    function userPermissions()
    {
        return session("user_powers");
    }
}

# User userActivePlan
if (!function_exists("userActivePlan")) {
    function userActivePlan()
    {
        $user = getUserObject();

        return $user->packageUserUsages()?->orderBy('id', 'desc')->first() ?? [];
    }
}

# User All time asset
if (!function_exists("userAllTimeAsset")) {
    function userAllTimeAsset()
    {
        $user = getUserObject();

        return $user->userAsset ?? [];
    }
}



# Group Permission
if (!function_exists("getGroupPermissions")) {
    function getGroupPermissions($group_id)
    {

        $filteredArray = \Illuminate\Support\Arr::where(userPermissions(), function ($value, $key) use ($group_id) {
            return $value['group_id'] == $group_id;
        });

        return $filteredArray;
    }
}


if (!function_exists("isMenuGroupShow")){
    function isMenuGroupShow(array $routeGroups){
        $isShow = false;

        if(isAdmin()){
            return !$isShow;
        }

        foreach($routeGroups as $key=>$route){
            $isExists = isRouteExists($route);

            if($isExists){
                $isShow = true;
                break;
            }
        }

        return $isShow;
    }
}

if (!function_exists("setRoutePrefix")){
    function setRoutePrefix()
    {
        if(isAdmin() || isAdminStaff()){
            return "admin";
        }


        if(isDistributor() || isDistributorStaff()){
            return "distributor";
        }

        return "partner";
    }
}


/**
 * Product Number Convertion
 * @incomingParams $productNumber, $convertTo
 * @param $productNumber will contain the number of amount
 * @param $convertTo will contain the convertion type as K for 1000 or M for 1000000, B for 1000000000
 *
 * */
if(!function_exists("productNumberConvertion")) {
    function productNumberConvertion($productNumber = 0){

        $productNumber = (int)$productNumber;

        if($productNumber == 0){
            return 10*1000;
        }

        return $productNumber * 1000000;
    }
}

/**
 * Number Converter
 * @incomingParams $productNumber will contain the integer number
 *
 * Will convert 10000 to 10k or 1000000 to 1M
 *
 * */
if(!function_exists("numberConverter")) {
    function numberConverter($n, $precision = 1){

        if ($n < 900) {
            // 0 - 900
            $n_format = number_format($n, $precision);
            $suffix = '';
        } else if ($n < 900000) {
            // 0.9k-850k
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'K';
        } else if ($n < 900000000) {
            // 0.9m-850m
            $n_format = number_format($n / 1000000, $precision);
            $suffix = 'M';
        } else if ($n < 900000000000) {
            // 0.9b-850b
            $n_format = number_format($n / 1000000000, $precision);
            $suffix = 'B';
        } else {
            // 0.9t+
            $n_format = number_format($n / 1000000000000, $precision);
            $suffix = 'T';
        }

        // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
        // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ( $precision > 0 ) {
            $dotzero = '.' . str_repeat( '0', $precision );
            $n_format = str_replace( $dotzero, '', $n_format );
        }

        return $n_format . $suffix;
    }
}


