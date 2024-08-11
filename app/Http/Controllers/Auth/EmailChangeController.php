<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class EmailChangeController extends Controller
{
    public function verify($token)
    {
        // Option 2: Using the `email_changes` table
        $emailChange = DB::table('email_changes')
            ->where('verification_token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if ($emailChange) {
            $user = User::find($emailChange->user_id);
            $user->email = $emailChange->new_email;
            $user->email_verified_at = now();
            $user->save();

            // Clean up the email_changes table
            DB::table('email_changes')->where('id', $emailChange->id)->delete();
        }
        else {
            abort(404);
        }

        return Redirect::route('dashboard')->with('status', 'email-verified');
    }

}