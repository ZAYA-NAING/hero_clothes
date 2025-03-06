@if (request()->routeIs('shop.checkout.onepage.index') &&
        (bool) core()->getConfigData('sales.payment_methods.stripe_checkout_session.active'))
    @php
        $clientId = core()->getConfigData('sales.payment_methods.stripe_checkout_session.client_id');

        $publishKey = core()->getConfigData('sales.payment_methods.stripe_checkout_session.publish_key');

        $acceptedCurrency = core()->getConfigData('sales.payment_methods.stripe_smart_button.accepted_currencies');

        $currentCurrency = core()->getCurrentCurrencyCode();

        $acceptedCurrenciesArray = array_map('trim', explode(',', $acceptedCurrency));

        $currencyToUse = in_array($currentCurrency, $acceptedCurrenciesArray)
            ? $currentCurrency
            : $acceptedCurrenciesArray[0];
    @endphp

    @pushOnce('scripts')
        <script src="https://js.stripe.com/v3/"></script>

        <script
            type="text/x-template"
            id="v-stripe-checkout-session-template"
        >
            <div>
                <button class="primary-button rounded-2xl px-11 py-3 max-md:rounded-lg max-sm:w-full max-sm:max-w-full max-sm:py-1.5">Checkout</button>
            </div>
        </script>

        <script type="module">
            app.component('v-stripe-checkout-session', {
                template: '#v-stripe-checkout-session-template',

                data() {
                    return {
                        stripe: Stripe("{{ $publishKey }}"),
                    }
                },

                mounted() {
                    console.log(this.stripe);
                },

                methods: {

                }
            });
        </script>
    @endPushOnce
@endif
