@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Make a Payment') }}</div>

                <div class="card-body">
                    <form id="paymentForm" action="#" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-auto">
                                <label for="amount">Que cantidad desea pagar?:</label>
                                <input type="number"
                                       min="5"
                                       required
                                       step="0.01"
                                       class="form-control"
                                       name="value"
                                       value="{{ mt_rand(500, 100000) / 100 }}"
                                >

                                <small class="form-text text-muted">*Puede usar valores con dos decimales con un punto</small>
                            </div>

                            <div class="col-auto">
                                <label for="amount">En que moneda quiere hacer el pago?:</label>
                                <select class="custom-select" name="currency" id="" required>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->iso }}">{{ $currency->iso }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" id="payButton" class="btn btn-primary btn-lg w-full">Pagar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


