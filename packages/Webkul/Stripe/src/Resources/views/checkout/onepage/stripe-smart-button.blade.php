@if (request()->routeIs('shop.checkout.onepage.index') &&
        (bool) core()->getConfigData('sales.payment_methods.stripe_smart_button.active'))
    @php
        $clientId = core()->getConfigData('sales.payment_methods.stripe_smart_button.client_id');

        $publishKey = core()->getConfigData('sales.payment_methods.stripe_smart_button.publish_key');

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
            id="v-stripe-smart-button-template"
        >
            <div>
                <template v-if="hasPaymentMethod">
                    <button @click="onPayWithCard" class="primary-button rounded-2xl px-11 py-3 max-md:rounded-lg max-sm:w-full max-sm:max-w-full max-sm:py-1.5">Pay</button>
                </template>
            </div>
        </script>

        <script type="module">
            app.component('v-stripe-smart-button', {
                template: '#v-stripe-smart-button-template',

                props: ['stripePaymentMethod'],

                data() {
                    return {
                        paymentMethod: null,

                        hasPaymentMethod: false,

                        stripe: Stripe("{{ $publishKey }}"),
                    }
                },

                computed: {},

                mounted() {
                    this.paymentMethod = this.stripePaymentMethod;

                    this.hasPaymentMethod = typeof this.stripePaymentMethod != 'undefined';
                },

                methods: {
                    onPayWithCard() {
                        this.$axios.post("{{ route('stripe.smart-button.pay') }}", {
                                payment_method: this.paymentMethod
                            })
                            .then((response) => {
                                console.log(response);
                                if ([400, 422, 500].includes(response.status) || response.data.redirect_url) {
                                    window.location.href = response.data.redirect_url;
                                } else {
                                    this.onConfirmCardPayment(response.data.client_secret, this.paymentMethod.id);
                                }
                            });
                    },

                    onConfirmCardPayment(clientSecret, paymentMethodId) {
                        this.stripe.confirmCardPayment(clientSecret, {
                            payment_method: paymentMethodId,
                        }).then((result) => {
                            console.log(result);

                            if (result.error) {
                                console.log(result.error.message);
                                return;
                            }

                            this.$axios.post("{{ route('stripe.smart-button.handle-payment-intent') }}", {
                                    payment_intent: result.paymentIntent
                                })
                                .then((response) => {
                                    console.log(response);
                                    if (result.data.error)  {
                                        this.$emitter.emit('add-flash', { type: 'error', message: response.data.error });
                                        window.location.href = "{{ route('shop.checkout.onepage.index') }}";
                                    }

                                    if (response.data.success) {
                                        window.location.href = "{{ route('shop.checkout.onepage.success') }}";
                                    }
                        });
                    }),
                }
            });
        </script>
    @endPushOnce
@endif
