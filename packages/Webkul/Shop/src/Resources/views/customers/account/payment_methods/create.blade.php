<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.account.payment-methods.create.add-payment-method')
    </x-slot>

    <!-- Breadcrumbs -->
    @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        @section('breadcrumbs')
            <x-shop::breadcrumbs name="payment-methods.create" />
        @endSection
    @endif

    <div class="max-md:hidden">
        <x-shop::layouts.account.navigation />
    </div>

    <div class="mx-4 flex-auto max-md:mx-6 max-sm:mx-4">
        <div class="mb-8 flex items-center max-md:mb-5">
            <!-- Back Button -->
            <a
                class="grid md:hidden"
                href="{{ route('shop.customers.account.payment_methods.index') }}"
            >
                <span class="icon-arrow-left rtl:icon-arrow-right text-2xl"></span>
            </a>

            <h2 class="text-2xl font-medium max-md:text-xl max-sm:text-base ltr:ml-2.5 md:ltr:ml-0 rtl:mr-2.5 md:rtl:mr-0">
                @lang('shop::app.customers.account.payment-methods.create.add-payment-method')
            </h2>
        </div>

        <v-create-customer-payment>
            <!--Payment Shimmer-->
            <x-shop::shimmer.form.control-group :count="10" />
        </v-create-customer-payment>
    </div>

    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>
        <script
            type="text/x-template"
            id="v-create-customer-payment-template"
        >
            <div>
                <!-- :action="route('shop.customers.account.payment_methods.store')" -->
                <x-shop::form id="payment-method-form" :action="route('shop.customers.account.payment_methods.store')">
                    {!! view_render_event('bagisto.shop.customers.account.payment_methods.create_form_controls.before') !!}

                     <!-- Card Holder Name -->
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('shop::app.customers.account.payment-methods.create.card-holder-name')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            id="card-holder-name"
                            type="text"
                            name="card_holder_name"
                            rules="required"
                            :value="old('card_holder_name')"
                            :label="trans('shop::app.customers.account.payment-methods.create.card-holder-name')"
                            :placeholder="trans('shop::app.customers.account.payment-methods.create.card-holder-name')"
                        />

                        <x-shop::form.control-group.error control-name="card_holder_name" />
                    </x-shop::form.control-group>
                    {!! view_render_event('bagisto.shop.customers.account.payment_methods.create_form_controls.card_holder_name.after') !!}

                    <!-- Card Element (Card Number, Expiration, CVV) -->
                    <label for="card-el" class="mb-2 block text-base max-sm:text-sm max-sm:mb-1">Credit or debit card</label>
                    <div id="card-el" class="mb-1.5 w-full rounded-lg border px-5 py-3 text-base font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 max-sm:px-4 max-md:py-2 max-sm:text-sm"></div>

                    <!-- Used to display form errors. -->
                    <div id="card-errors" role="alert" class="text-red-500 text-xs italic"></div>
                    <div class="stripe-errors"></div>

                    <button id="custom-button" type="submit"
                        class="primary-button w-max rounded-2xl bg-navyBlue px-11 py-3 max-md:mb-4 max-md:w-full max-md:max-w-full max-md:rounded-lg max-sm:py-1.5"
                        @click="createPaymentMethod($event)">
                            Create Payment Method
                    </button>
                </x-shop::form>
                {!! view_render_event('bagisto.shop.customers.account.payment_methods.create.after') !!}
            </div>
        </script>

        <script type="module">
            app.component('v-create-customer-payment', {
                template: '#v-create-customer-payment-template',

                data() {
                    return {
                        stripe: null,

                        paymentMethod: null,

                        setupIntent: null,

                        cardElements: null,

                        cardEl: null,

                    }
                },

                mounted() {
                    this.register();
                },

                computed: {
                    stripeElements() {
                        return this.stripe.elements();
                    },
                },

                methods: {
                    register() {
                        this.stripe = Stripe('{{ env('STRIPE_KEY') }}');
                        if (typeof this.stripe == 'undefined') {
                            this.$emitter.emit('add-flash', { type: 'error', message: '@lang('Something went wrong.')' });
                            return;
                        }
                        // Style Object documentation here: https://stripe.com/docs/js/appendix/style
                        const style = {
                            base: {
                                color: 'black',
                                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                                fontSmoothing: 'antialiased',
                                fontSize: '14px',
                                '::placeholder': {
                                    color: '#aab7c4',
                                },
                            },
                            invalid: {
                                color: '#fa755a',
                                iconColor: '#fa755a',
                            },
                        };
                        // Create Card element
                        this.cardEl = this._createElement(
                            'card',
                            {
                                hidePostalCode: true,
                                style: style
                            }
                        );
                        // Mount to the Card element
                        this.cardEl.mount('#card-el');
                    },

                    createPaymentMethod(e) {
                        e.preventDefault();
                        const cardHolderName = document.getElementById('card-holder-name');
                        // Register payment method to the stripe account
                        this.stripe.createPaymentMethod(
                            'card', this.cardEl, {
                            billing_details: { name: cardHolderName.value }
                         }
                        ).then((res) => {
                            console.log(res);
                            const {paymentMethod, error} = res;
                            if (error) {
                                document.getElementById('card-errors').innerHTML = error.message;
                            } else {
                                console.log(paymentMethod);
                                console.log(paymentMethod.id);
                                this.paymentMethod = paymentMethod;
                                this._paymentMethodHandler(paymentMethod);
                            }
                        });
                    },

                    _createElement(type, options) {
                        return this.stripeElements.create(type, options);
                    },

                    _paymentMethodHandler(payment_method) {
                        let form = document.getElementById('payment-method-form');
                        // let cardNoHiddenInput = document.createElement('input');
                        // cardNoHiddenInput.setAttribute('type', 'hidden');
                        // cardNoHiddenInput.setAttribute('name', 'card_no');
                        // cardNoHiddenInput.setAttribute('value', payment_method.card.last4);
                        // form.appendChild(cardNoHiddenInput);
                        // let cvcNoHiddenInput = document.createElement('input');
                        // cvcNoHiddenInput.setAttribute('type', 'hidden');
                        // cvcNoHiddenInput.setAttribute('name', 'card_cvc_no');
                        // cvcNoHiddenInput.setAttribute('value', this.cardElements[1].value);
                        // form.appendChild(cvcNoHiddenInput);
                        // let expirationDateHiddenInput = document.createElement('input');
                        // expirationDateHiddenInput.setAttribute('type', 'hidden');
                        // expirationDateHiddenInput.setAttribute('name', 'card_expiration');
                        // expirationDateHiddenInput.setAttribute('value', this.cardElements[2].value);
                        // form.appendChild(expirationDateHiddenInput);
                        let hiddenInput = document.createElement('input');
                        hiddenInput.setAttribute('type', 'hidden');
                        hiddenInput.setAttribute('name', 'payment_method');
                        hiddenInput.setAttribute('value', payment_method.id);
                        form.appendChild(hiddenInput);
                        form.submit();
                    },

                }
            });
        </script>

    @endpush

</x-shop::layouts.account>
