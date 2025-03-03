@if (request()->routeIs('shop.checkout.onepage.index') &&
        (bool) core()->getConfigData('sales.payment_methods.paypal_adv_smart_button.active'))
    @php
        $clientId = core()->getConfigData('sales.payment_methods.paypal_adv_smart_button.client_id');

        $acceptedCurrency = core()->getConfigData('sales.payment_methods.paypal_adv_smart_button.accepted_currencies');

        $currentCurrency = core()->getCurrentCurrencyCode();

        $acceptedCurrenciesArray = array_map('trim', explode(',', $acceptedCurrency));

        $currencyToUse = in_array($currentCurrency, $acceptedCurrenciesArray)
            ? $currentCurrency
            : $acceptedCurrenciesArray[0];
    @endphp

    @pushOnce('scripts')
        {{-- <script
            src="https://www.paypal.com/sdk/js?client-id={{ $clientId }}&currency={{ $currencyToUse }}"
            data-partner-attribution-id="Bagisto_Cart"
        >

        </script> --}}

        <script
            src="https://www.paypal.com/sdk/js?client-id=AZpKH9atea0ib-NVm5ixh8RXdhHKVsW6r5pa4eHNOJ1P8OcWLolKm3l6i2pGjSkGdM1fSmfThLvktfBb"
            data-sdk-integration-source="developer-studio"
        >

        </script>

        {{-- <script src="https://www.paypal.com/sdk/js?client-id={{ $clientId }}"></script> --}}
        <script
            type="text/x-template"
            id="v-paypal-adv-smart-button-template"
        >
            <div class="w-full paypal-adv-button-container"></div>
        </script>

        <script type="module">
            app.component('v-paypal-adv-smart-button', {
                template: '#v-paypal-adv-smart-button-template',
                mounted() {
                    this.register();
                },
                methods: {
                    register() {
                        if (typeof paypal == 'undefined') {
                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: '@lang('Something went wrong.')'
                            });

                            return;
                        }

                        paypal.Buttons(this.getOptions()).render('.paypal-adv-button-container');
                    },

                    getOptions() {
                        let options = {
                            style: {
                                layout: 'vertical',
                                shape: 'rect',
                            },
                            message: {
                                amount: 100,
                            },

                            authorizationFailed: false,

                            enableStandardCardFields: false,

                            alertBox: (message) => {
                                this.$emitter.emit('add-flash', {
                                    type: 'error',
                                    message: message
                                });
                            },

                            createOrder: (data, actions) => {
                                return this.$axios.post("{{ route('paypal.adv-smart-button.create-order') }}")
                                    .then(response => response.data)
                                    .then((order) => {
                                        console.log(order);
                                        return order.id
                                    })
                                    .catch(error => {
                                        if (error.response.data.error === 'invalid_client') {
                                            options.authorizationFailed = true;

                                            options.alertBox('@lang('Something went wrong.')');
                                        }

                                        return error;
                                    });
                            },

                            onApprove: (data, actions) => {
                                this.$axios.post("{{ route('paypal.adv-smart-button.capture-order') }}", {
                                        _token: "{{ csrf_token() }}",
                                        orderData: data
                                    })
                                    .then(response => {
                                        if (response.success) {
                                            if (response.redirect_url) {
                                                window.location.href = response.redirect_url;
                                            } else {
                                                window.location.href =
                                                    "{{ route('shop.checkout.onepage.success') }}";
                                            }
                                        }
                                    })
                                    .catch(error => window.location.href =
                                        "{{ route('shop.checkout.cart.index') }}");
                            },

                            onError: (error) => {
                                if (!options.authorizationFailed) {
                                    options.alertBox('@lang('Something went wrong.')');
                                }
                            },
                        };

                        return options;
                    },
                },
            });
        </script>
    @endPushOnce
@endif
