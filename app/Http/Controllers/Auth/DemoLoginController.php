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
        $demoUser = User::find(1);

        // Log in the user
        Auth::login($demoUser);

        // Need this to show the welcome message properly
        Session::put('loggedIn', true);

        return redirect('/'); // Redirect to the desired page after successful login
    }
}
