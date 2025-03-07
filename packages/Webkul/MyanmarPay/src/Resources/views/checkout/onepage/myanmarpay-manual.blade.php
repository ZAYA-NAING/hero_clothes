@if (
    request()->routeIs('shop.checkout.onepage.index')
    && (bool) core()->getConfigData('sales.payment_methods.myanmarpay-manual.active')
)
    @php
        $clientId = core()->getConfigData('sales.payment_methods.myanmarpay-manual.client_id');

        $acceptedCurrency = core()->getConfigData('sales.payment_methods.myanmarpay-manual.accepted_currencies');

        $currentCurrency = core()->getCurrentCurrencyCode();

        $acceptedCurrenciesArray = array_map('trim', explode(',', $acceptedCurrency));

        $currencyToUse = in_array($currentCurrency, $acceptedCurrenciesArray)
            ? $currentCurrency
            : $acceptedCurrenciesArray[0];
    @endphp

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-myanmarpay-manual-template"
        >
            <div class="w-full myanmarpay-button-container"></div>
        </script>

        <script type="module">
            app.component('v-myanmarpay-manual', {
                template: '#v-myanmarpay-manual-template',

                mounted() {
                    this.register();
                },

                methods: {
                    register() {
                        if (typeof paypal == 'undefined') {
                            this.$emitter.emit('add-flash', { type: 'error', message: '@lang('Something went wrong.')' });

                            return;
                        }


                    }
                },
            });
        </script>
    @endPushOnce
@endif
