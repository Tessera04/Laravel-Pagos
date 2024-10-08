<?php

namespace App\Services;

use App\Traits\ConsumesExternalServices;
use Illuminate\Http\Request;

class StripeServices{
    use ConsumesExternalServices;

    protected $baseUri;

    protected $key;

    protected $secret;

    public function __construct()
    {
        //Ese config por medio de puntos se mete al archivo services, al apartado stripe y agarra cada elemento
        $this->baseUri = config('services.stripe.base_uri');
        $this->key = config('services.stripe.key');
        $this->secret = config('services.stripe.secret');
    }

    public function resolveAuthorization(&$queryParams, &$formParams, &$headers){
        $headers['Authorization'] = $this->resolveAccessToken();
    }

    public function decodeResponse($response){
        //tanto stripe como paypal devuelven un json por eso necesitamos este fragmento
        return json_decode($response);
    }

    public function resolveAccessToken(){
        return "Bearer {$this->secret}";
    }

    public function handlePayment(Request $request){
        $request->validate([
            'payment_method' => 'required',
        ]);

        $intent = $this->createIntent($request->value, $request->currency, $request->payment_method);

        session()->put('paymentIntentId', $intent->id);

        return redirect()->route('approval');
    }

    public function handleApproval(){
        if(session()->has('paymentIntentId')){
            $paymentIntentId = session()->get('paymentIntentId');

            $confirmation = $this->confirmPayment($paymentIntentId);

            if($confirmation->status === 'succeded'){
                $name = $confirmation->charges->data[0]->billing_details->name;
                $currency = strtoupper($confirmation->currency);
                $amount = $confirmation->amount / $this->resolveFactor($currency);

                return redirect()->route('home')->withSuccess(['payment' => "Gracias {$name}. Recibimos su pago de {$amount}{$currency}!"]);
            }
        }

        return redirect()->route('home')->withErrors('No pudimos procesar su pago, por favor reintente o elija otra plataforma');
    }

    public function createIntent($value, $currency, $paymentMethod){
        return $this->makeRequest(
            'POST',
            '/v1/payment_intents',
            [],
            [
                'amount' => round($value * $this->resolveFactor($currency)),
                'currency' => strtolower($currency),
                'payment_method' => strtolower($paymentMethod),
                'confirmation_method' => 'manual',
            ],
        );
    }

    public function confirmPayment($paymentIntentId){
        return $this->makeRequest(
            'POST',
            "/v1/payment_intents/{$paymentIntentId}/confirm",
        );
    }

    public function resolveFactor($currency){
        $zeroDecimalCurrencies = ['JPY'];

        if(in_array(strtoupper($currency), $zeroDecimalCurrencies)){
            return 1;
        }

        return 100;
    }

}

?>