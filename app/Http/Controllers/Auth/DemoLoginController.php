<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class DemoLoginController extends Controller
{
    public function login(Request $request)
    {
        // Live environment
        if (env('APP_ENV') == 'live') {
            return redirect('https://demo.parthub.online/demo-login'); // Redirect to the demo subdomain if sent from live environment
        }
        // Demo environment
        else {
            $demoUser = User::find(1);
            Auth::login($demoUser);
            return redirect('/')->with('loggedIn', true); // With welcome message
        }
    }
}
