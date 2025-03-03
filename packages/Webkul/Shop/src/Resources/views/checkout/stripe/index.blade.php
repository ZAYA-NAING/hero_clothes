<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="@lang('shop::app.checkout.onepage.index.checkout')"/>

    <meta name="keywords" content="@lang('shop::app.checkout.onepage.index.checkout')"/>
@endPush

@push('style')
<style>
    .StripeElement {
        background-color: white;
        padding: 8px 12px;
        border-radius: 4px;
        border: 1px solid transparent;
        box-shadow: 0 1px 3px 0 #e6ebf1;
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }
    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }
    .StripeElement--invalid {
        border-color: #fa755a;
    }
    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }
</style>
@endPush

<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <!-- Page Title -->
    <x-slot:title>
        {{ 'Stripe checkout' }}
    </x-slot>


    {!! view_render_event('bagisto.shop.checkout.onepage.header.before') !!}

    <!-- Page Header -->
    <div class="flex-wrap">
        <div class="flex w-full justify-between border border-b border-l-0 border-r-0 border-t-0 px-[60px] py-4 max-lg:px-8 max-sm:px-4">
            <div class="flex items-center gap-x-14 max-[1180px]:gap-x-9">
                <a
                    href="{{ route('shop.home.index') }}"
                    class="flex min-h-[30px]"
                    aria-label="@lang('shop::checkout.onepage.index.bagisto')"
                >
                    <img
                        src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                        alt="{{ config('app.name') }}"
                        width="131"
                        height="29"
                    >
                </a>
            </div>

            @guest('customer')
                @include('shop::checkout.login')
            @endguest
        </div>
    </div>

    {!! view_render_event('bagisto.shop.checkout.onepage.header.after') !!}

    <!-- Page Content -->
    <div class="container px-[60px] max-lg:px-8 max-sm:px-4">
        <div class="flex items-center">
            <form action="{{route('shop.checkout.stripe.single.charge')}}" method="POST" id="subscribe-form">
                {{-- <div class="form-group">
                    <div class="row">
                        @foreach($plans as $plan)
                        <div class="col-md-4">
                            <div class="subscription-option">
                                <input type="radio" id="plan-silver" name="plan" value='{{$plan->id}}'>
                                <label for="plan-silver">
                                    <span class="plan-price">{{$plan->currency}}{{$plan->amount/100}}<small> /{{$plan->interval}}</small></span>
                                    <span class="plan-name">{{$plan->product->name}}</span>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div> --}}
                <label for="amount form-control">Amount</label> <br>
                <input id="amount" type="number"  class="form-control"><hr> <br><br>

                <label for="card-holder-name form-control">Card Holder Name</label> <br>
                <input id="card-holder-name" type="text"><hr> <br><br>
                @csrf
                <div class="form-row">
                    <label for="card-element">Credit or debit card</label>
                    <div id="card-element" class="form-control">
                    </div>
                    <!-- Used to display form errors. -->
                    <div id="card-errors" role="alert"></div>
                </div>
                <div class="stripe-errors"></div>
                @if (count($errors) > 0)
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                    @endforeach
                </div>
                @endif
                <div class="form-group text-center">
                    <button  id="card-button" data-secret="{{ $intent->client_secret }}" class="btn btn-lg btn-success btn-block">SUBMIT</button>
                </div>
            </form>
        </div>
        <!-- Stripe Vue Component -->
        <v-stripe></v-stripe>
    </div>

    @pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-stripe-template"
    >
        <template>
            @include('shop::checkout.stripe.deposit')
        </template>
    </script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        var stripe = Stripe('{{ env('STRIPE_KEY') }}');
        var elements = stripe.elements();
        console.log(stripe);
        console.log(elements);
        var style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };
        var card = elements.create('card', {hidePostalCode: true,
            style: style});
        card.mount('#card-element');
        card.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
        const cardHolderName = document.getElementById('card-holder-name');
        const cardButton = document.getElementById('card-button');
        const clientSecret = cardButton.dataset.secret;
        cardButton.addEventListener('click', async (e) => {
            e.preventDefault();
            console.log("attempting");
            const { setupIntent, error } = await stripe.confirmCardSetup(
                clientSecret, {
                    payment_method: {
                        card: card,
                        billing_details: { name: cardHolderName.value }
                    }
                }
                );
                console.log(setupIntent);
            if (error) {
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = error.message;
            } else {
                paymentMethodHandler(setupIntent.payment_method);
            }
        });
        function paymentMethodHandler(payment_method) {
            var form = document.getElementById('subscribe-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'payment_method');
            hiddenInput.setAttribute('value', payment_method);
            form.appendChild(hiddenInput);
            form.submit();
        }
    </script>

    @endPushOnce

</x-shop::layouts>
