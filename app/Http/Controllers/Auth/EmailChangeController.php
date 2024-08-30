<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class EmailChangeController extends Controller
{
    public function verify($token)
    {
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

            return Redirect::route('dashboard')->with('status', 'email-updated');
        } else {
            abort(404);
        }
    }
}
