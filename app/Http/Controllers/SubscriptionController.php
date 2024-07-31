<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $user = $request->user();

        $paymentMethod = $request->paymentMethod;

        $user->newSubscription('default', 'price_1Onp7rEb2UyIF2shJJVhIDcL') // Replace with your Stripe price ID
             ->create($paymentMethod);

        return response()->json(['status' => 'Subscription created successfully']);
    }
}
