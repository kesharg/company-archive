<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomAuthController extends Controller
{

    public function login(Request $request)
    {
        if ($request->isMethod('GET')) {
            return view('dashboard.pages.login');
        }

        if ($request->isMethod('POST')) {
            $credentials = $request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);

            $credentials["is_active"] = 1;

            #$credentials = $request->only('username', 'password');
            if (Auth::attempt($credentials)) {

                // Distributor
                if (isDistributor() || isDistributorStaff()) {
                    return to_route("distributor.dashboard");
                }

                // Partner
                if (isPartner() || isPartnerStaff()) {
                    return to_route("partner.dashboard");
                }

                return redirect()->route('admin.dashboard')->with('success', "Wel-come back admin");
            }

            return redirect()->route('login')->with('error_message', 'Login details are not valid');
        }
    }

    public function showLinkRequestForm(Request $request)
    {
        $token = $request->route()->parameter('token');

        return view('dashboard.pages.password-reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}
