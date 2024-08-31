<?php
namespace App\Http\Controllers;

use App\Resolvers\PaymentPlatformResolver;
use App\Services\PayPalServices;
use App\Services\StripeServices;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentPlatformResolver;

    public function __construct(PaymentPlatformResolver $paymentPlatformResolver)
    {
        $this->middleware('auth');

        $this->paymentPlatformResolver = $paymentPlatformResolver;
    }

    public function pay(Request $request){
        $rules = [
            'value' => 'required|numeric|min:5',
            'currency' => 'required|exists:currencies,iso',
            'payment_platform' => 'required|exists:payment_platforms,id',
        ];

        dd($request->all());

        $request->validate($rules);

        $paymentPlatform = $this->paymentPlatformResolver->resolveService($request->payment_platform);

        session()->put('paymentPlatformId', $request->payment_platform);

        return $paymentPlatform->handlePayment($request);
    }

    public function approval(){

        if(session()->has('paymentPlatformId')){
            $paymentPlatform = $this->paymentPlatformResolver->resolveService(session()->get('paymentPlatformId'));
            
            return $paymentPlatform->handleApproval();
        }

        return redirect()->route('home')->withErrors('Tenemos problemas para iniciar su plataforma de pago, por favor elija otra');
        
    }

    public function cancelled(){
        return redirect()->route('home')->withErrors('Has cancelado el Pago');
    }
}
