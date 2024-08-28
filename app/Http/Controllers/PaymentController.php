<?php
namespace App\Http\Controllers;

use App\Services\PayPalServices;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function pay(Request $request){
        $rules = [
            'value' => 'required|numeric|min:5',
            'currency' => 'required|exists:currencies,iso',
            'payment_platform' => 'required|exists:payment_platforms,id',
        ];

        $request->validate($rules);

        $paymentPlatform = resolve(PayPalServices::class);

        return $paymentPlatform->handlePayment($request);
    }

    public function approval(){
        //
    }

    public function cancelled(){
        //
    }
}
