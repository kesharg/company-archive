<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\Information;
use App\Services\Models\Code\CodeService;
use App\Services\Models\User\DistributorService;
use App\Services\Notification\NotificationService;
use App\Traits\Api\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    use ApiResponseTrait;
    public function index() {

        if(isDistributor() || isDistributorStaff()){
            return view('dashboard.pages.dashboard_distributor');
        }

        $totalCode       = Code::count();
        $scannedCode     = Code::where('scanned', 1)->count();
        $multScannedCode = Code::where('scanned', '>', 1)->count();
        $recentScanned   = Information::query()->latest()->take(10)->get();

        $codes = (new CodeService())->getAll(
            true,null,
            ["informations","store","model","series"],["informations"], ["informations"],
        );

        $stats = Code::query()
                ->select('id')
                ->addSelect(['last_30' => Code::selectRaw('count(*) as total')
                                ->whereDate('created_at', '<', now()->subDays(7))])
                ->addSelect(['new_codes' => Code::selectRaw('count(*) as total')
                                ->whereDate('created_at', '>=', now()->subDays(7))])
                ->addSelect(['scanned_codes' => Code::where('scanned', 1)->selectRaw('count(*) as total')
                                ->whereDate('created_at', '<', now()->subDays(7))])
                ->addSelect(['new_scanned_codes' => Code::where('scanned', 1)->selectRaw('count(*) as total')
                                ->whereDate('created_at', '>=', now()->subDays(7))])
                ->addSelect(['multi_scanned_codes' => Code::where('scanned', '>', 1)->selectRaw('count(*) as total')
                                ->whereDate('created_at', '<', now()->subDays(7))])
                ->addSelect(['new_multi_scanned_codes' => Code::where('scanned', '>', 1)->selectRaw('count(*) as total')
                                ->whereDate('created_at', '>=', now()->subDays(7))])
                ->first();

        $last_30 = isset($stats->last_30) ? $stats->last_30 : 0;
        $scanned_codes = isset($stats->scanned_codes) ? $stats->scanned_codes : 0;
        $multi_scanned_codes = isset($stats->multi_scanned_codes) ? $stats->multi_scanned_codes : 0;

        $new_codes = isset($stats->new_codes) ? $stats->new_codes : 0;
        $new_scanned_codes = isset($stats->new_scanned_codes) ? $stats->new_scanned_codes : 0;
        $new_multi_scanned_codes = isset($stats->new_multi_scanned_codes) ? $stats->new_multi_scanned_codes : 0;

        $growthPercentage = $this->calculatePercentage($last_30, $new_codes);
        $scannedGrowthPercentage = $this->calculatePercentage($scanned_codes, $new_scanned_codes);
        $multiScannedGrowthPercentage = $this->calculatePercentage($multi_scanned_codes, $new_multi_scanned_codes);

        $data = [
            'totalCode' => $totalCode,
            'scannedCode' => $scannedCode,
            'multScannedCode' => $multScannedCode,
            'recentScanned' => $recentScanned,
            'growthPercentage' => $growthPercentage,
            'scannedGrowthPercentage' => $scannedGrowthPercentage,
            'multiScannedGrowthPercentage' => $multiScannedGrowthPercentage,
            'codes' => $codes
        ];


        return view('dashboard.pages.dashboard')->with($data);
    }

    public function notifications(Request $request)
    {
        // Get unread notifications
        $data["notifications"] = user()->notifications()->orderBy('created_at', 'desc')->get();

        return view('dashboard.admin.notifications.index')->with($data);
    }

    public function calculatePercentage($oldNumber, $newNumber){
        $decreaseValue =  $newNumber - $oldNumber;
        if($decreaseValue == 0 || $oldNumber == 0) {
            return 0;
        }
        return ($decreaseValue / $oldNumber) * 100;
    }

    public function signOut() {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }

    public function allDistributors(Request $request, DistributorService $distributorService)
    {
        $data["distributors"] = $distributorService->getAll(true);

        return view("dashboard.distributor.index")->with($data);
    }

    public function markNotification(Request $request, NotificationService $notificationService)
    {
        try {
            $notification = $notificationService->markNotificationAsReadById($request->id);

            return $this->sendResponse(
              appStatic()::SUCCESS_WITH_DATA,
              "Notification marked as read successfully."
            );
        }
        catch (\Throwable $e){
            return $this->sendResponse(
                appStatic()::INTERNAL_SERVER_ERROR,
                "Failed to update notification",
                errorArray($e)
            );
        }
    }
}
