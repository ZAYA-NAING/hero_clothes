@if (
    request()->routeIs('shop.checkout.onepage.index')
    && (bool) core()->getConfigData('sales.payment_methods.paypal_smart_button_v2.active')
)
    @php
        $clientId = core()->getConfigData('sales.payment_methods.paypal_smart_button_v2.client_id');

        $acceptedCurrency = core()->getConfigData('sales.payment_methods.paypal_smart_button_v2.accepted_currencies');

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
            src="https://www.paypal.com/sdk/js?client-id={{ $clientId }}"
            data-sdk-integration-source="developer-studio"
        >

        </script>
        <script
            type="text/x-template"
            id="v-paypal-smart-button-v2-template"
        >
            <div class="w-full paypal-button-container"></div>
        </script>

        <script type="module">
            app.component('v-paypal-smart-button-v2', {
                template: '#v-paypal-smart-button-v2-template',

                data() {
                    return {
                        test_data : null,
                    }
                },

                mounted() {
                    this.register();
                    this.getTestData();
                },

                methods: {
                    register() {
                        if (typeof paypal == 'undefined') {
                            this.$emitter.emit('add-flash', { type: 'error', message: '@lang('Something went wrong.')' });

                            return;
                        }

                        paypal.Buttons(this.getOptions()).render('.paypal-button-container');
                    },

                    getTestData() {
                        return this.$axios.get("{{ route('paypal.smart-button-v2.index') }}")
                                    .then((response) => {
                                        this.test_data = response;
                                        console.log( this.test_data );
                                    })
                                    .catch(error => {
                                        if (error.response.data.error === 'invalid_client') {
                                            options.authorizationFailed = true;

                                            options.alertBox('@lang('Something went wrong.')');
                                        }

                                        return error;
                                    });
                    },

                    getOptions() {
                        let options = {
                            style: {
                                layout: 'vertical',
                                shape: 'rect',
                            },

                            authorizationFailed: false,

                            enableStandardCardFields: false,

                            alertBox: (message) => {
                                this.$emitter.emit('add-flash', { type: 'error', message: message });
                            },

                            createOrder: (data, actions) => {
                                return this.$axios.get("{{ route('paypal.smart-button.create-order') }}")
                                    .then(response => response.data.result)
                                    .then(order => order.id)
                                    .catch(error => {
                                        if (error.response.data.error === 'invalid_client') {
                                            options.authorizationFailed = true;

                                            options.alertBox('@lang('Something went wrong.')');
                                        }

                                        return error;
                                    });
                            },

                            onApprove: (data, actions) => {
                                this.$axios.post("{{ route('paypal.smart-button.capture-order') }}", {
                                    _token: "{{ csrf_token() }}",
                                    orderData: data
                                })
                                .then(response => {
                                    if (response.data.success) {
                                        if (response.data.redirect_url) {
                                            window.location.href = response.data.redirect_url;
                                        } else {
                                            window.location.href = "{{ route('shop.checkout.onepage.success') }}";
                                        }
                                    }
                                })
                                .catch(error => window.location.href = "{{ route('shop.checkout.cart.index') }}");
                            },

                            onError: (error) => {
                                if (! options.authorizationFailed) {
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
