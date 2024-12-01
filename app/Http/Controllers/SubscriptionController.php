<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Handle the subscription checkout process.
     *
     * This method initializes a new subscription for the authenticated user with the specified type
     * and Stripe price ID. It includes a 14-day trial period and allows promotion codes.
     * The user is redirected to Stripe's hosted checkout page.
     *
     * @param  \Illuminate\Http\Request  $request  The current request instance.
     * @param  string  $type  The type of the subscription, used as the name of the subscription.
     * @param  string  $priceId  The Stripe price ID for the subscription plan.
     * @return \Illuminate\Http\Response The response containing the redirect to Stripe's checkout page.
     */
    public function checkout(Request $request, $type, $priceId)
    {
        return $request->user()
            ->newSubscription($type, $priceId)
            ->trialDays(30)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('welcome'),
                'cancel_url' => route('welcome'),
                'billing_address_collection' => 'required',
            ]);
    }

    public function manage()
    {
        return redirect('https://billing.stripe.com/p/login/fZeg229Aa2T5aSAcMM');
    }
}
