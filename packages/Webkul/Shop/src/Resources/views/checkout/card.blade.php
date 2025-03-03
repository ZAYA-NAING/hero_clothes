<!-- Card Vue Component -->
<v-card :cart="cart" :stripePaymentMethods="stripePaymentMethods" @card-added="getStripePaymentMethods"></v-card>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-card-template"
    >
        <div class="flex justify-between text-right">
            <p class="text-base max-md:font-normal max-sm:text-sm">
                @{{ cart.card_code ? "@lang('shop::app.checkout.card.added')" : "@lang('shop::app.checkout.card.discount')" }}
            </p>

            {!! view_render_event('bagisto.shop.checkout.cart.card.before') !!}

            <p class="text-base font-medium max-sm:text-sm">
                <!-- Add Card Form -->
                <x-shop::form
                    v-slot="{ meta, errors, handleSubmit }"
                    as="div"
                >
                    <!-- Add card form -->
                    <form @submit="handleSubmit($event, addCard)">
                        {!! view_render_event('bagisto.shop.checkout.cart.card.card_form_controls.before') !!}

                        <!-- Apply card modal -->
                        <x-shop::modal ref="cardModel">
                            <!-- Modal Toggler -->
                            <x-slot:toggle>
                                <span
                                    class="cursor-pointer text-base text-blue-700 max-sm:text-sm"
                                    role="button"
                                    tabindex="0"
                                >
                                    @lang('shop::app.checkout.card.add')
                                </span>
                            </x-slot>

                            <!-- Modal Header -->
                            <x-slot:header class="max-md:p-5">
                                <h2 class="text-2xl font-medium max-md:text-base">
                                    @lang('shop::app.checkout.card.add')
                                </h2>
                            </x-slot>

                            <!-- Modal Content -->
                            <x-slot:content class="!px-4">

                                <!-- Card Holder Name -->
                                <x-shop::form.control-group>
                                    <!-- Label of Card Holder Name -->
                                    <x-shop::form.control-group.label class="required text-left">
                                        @lang('shop::app.customers.account.payment-methods.create.card-holder-name')
                                    </x-shop::form.control-group.label>
                                    <!-- Input of Card Holder Name -->
                                    <x-shop::form.control-group.control
                                        id="card-holder-name"
                                        type="text"
                                        name="card_holder_name"
                                        rules="required"
                                        :value="old('card_holder_name')"
                                        :label="trans('shop::app.customers.account.payment-methods.create.card-holder-name')"
                                        :placeholder="trans('shop::app.customers.account.payment-methods.create.card-holder-name')"
                                    />
                                     <!-- Error of Card Holder Name -->
                                    <x-shop::form.control-group.error control-name="card_holder_name" class="flex" />
                                </x-shop::form.control-group>

                                <!-- Card Element (Card Number, Expiration, CVV) -->
                                <label for="card-elements" class="mb-2 block text-base max-sm:text-sm max-sm:mb-1 required text-left">Credit or debit card</label>
                                <div id="card-elements" class="mb-1.5 w-full rounded-lg border px-5 py-3 text-base font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 max-sm:px-4 max-md:py-2 max-sm:text-sm"></div>
                                <!-- Used to display form errors. -->
                                <div id="card-errors" role="alert" class="text-red-500 text-xs italic"></div>
                                <div class="stripe-errors"></div>

                                <!-- Set default for Card Checkbox -->
                                <x-shop::form.control-group class="!mb-0 mt-5 flex items-center gap-2.5">
                                    <x-shop::form.control-group.control
                                        type="checkbox"
                                        name="set_default_card"
                                        id="set_default_card"
                                        for="set_default_card"
                                        value="1"
                                        ::checked="!! true"
                                    />

                                    <label
                                        class="cursor-pointer select-none text-base text-zinc-500 max-md:text-sm max-sm:text-xs ltr:pl-0 rtl:pr-0"
                                        for="set_default_card"
                                    >
                                        Set default card
                                    </label>
                                </x-shop::form.control-group>
                            </x-slot>

                            <!-- Modal Footer -->
                            <x-slot:footer>
                                <!-- Card Form Action Container -->
                                <div class="flex flex-wrap items-center gap-4 max-md:justify-between">
                                    <x-shop::button
                                        class="primary-button max-w-none flex-auto rounded-2xl px-11 py-3 max-md:max-w-[153px] max-md:rounded-lg max-md:py-2"
                                        :title="trans('shop::app.checkout.card.button-title')"
                                        ::loading="isStoring"
                                        ::disabled="isStoring"
                                    />
                                </div>
                            </x-slot>
                        </x-shop::modal>

                        {!! view_render_event('bagisto.shop.checkout.cart.card.card_form_controls.after') !!}
                    </form>
                </x-shop::form>

                <!-- Applied Coupon Information Container -->
                <div
                    class="font-small flex items-center justify-between text-xs"
                    v-if="cart.card_code"
                >
                    <p
                        class="text-base font-medium text-navyBlue max-sm:text-sm"
                        title="@lang('shop::app.checkout.card.added')"
                    >
                        "@{{ cart.card_code }}"
                    </p>

                    <span
                        class="icon-cancel cursor-pointer text-2xl max-sm:text-base"
                        title="@lang('shop::app.checkout.card.remove')"
                        @click="destroyCoupon"
                    >
                    </span>
                </div>
            </p>

            {!! view_render_event('bagisto.shop.checkout.cart.card.after') !!}
        </div>
    </script>

    <script type="module">
        app.component('v-card', {
            template: '#v-card-template',

            props: ['cart', 'stripePaymentMethods'],

            data() {
                return {
                    isStoring: false,

                    stripe: Stripe("{{ env('STRIPE_KEY') }}"),

                    cardElements: null,

                    billingAddress: null,

                    useDefault: true,
                }
            },

            computed: {
                stripeElements() {
                    return this.stripe.elements();
                }
            },

            mounted() {
                this.register();
            },

            methods: {
                register() {
                    if (typeof this.stripe == 'undefined') {
                        this.$emitter.emit('add-flash', {
                            type: 'error',
                            message: '@lang('Something went wrong.')'
                        });

                        return;
                    }

                    this.createStripeCardElement();
                },

                createStripeCardElement() {
                    let style = {
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
                    this.cardElements = this.stripeElements.create('card', {
                        hidePostalCode: true,
                        style: style
                    });
                    this.cardElements.mount('#card-elements');

                    console.log(this.cardElements);
                },

                addCard(params, {
                    resetForm
                }) {
                    this.isStoring = true;

                    const cardHolderName = document.getElementById('card-holder-name');
                    const address2 = this.cart.billing_addresss?.address?.length === 1 ? this.cart.billing_addresss
                        .address[0] : null;
                    const address1 = this.cart.billing_addresss?.address?.length > 0 ? this.cart.billing_addresss
                        .address[this.cart.billing_addresss?.address.length - 1] : address2;
                    this.billingAddress = this._getBillingAddressFromCartAddress(this.cart, {
                        name: cardHolderName.value,
                        address1: address1,
                        address2: address2
                    });

                    console.log(params);

                    this.stripe.createPaymentMethod(
                        'card', this.cardElements, this.billingAddress
                    ).then((stripePaymentMethodRes) => {
                        if (stripePaymentMethodRes?.error) {
                            console.log('Error: Payment method from stripe server', stripePaymentMethodRes?.error);
                            /**  If a payment method error occurs,
                             * 1. stop the process of loading indicator.
                             * 2. display "stripePaymentMethodRes?.error" to the user...
                             */
                            this.isStoring = false;

                            this.$refs.cardModel.toggle();

                            // if ([400, 422].includes(error.response.request.status)) {
                            //     this.$emitter.emit('add-flash', {
                            //         type: 'warning',
                            //         message: error.response.data.message
                            //     });

                            //     resetForm();

                            //     return;
                            // }

                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: stripePaymentMethodRes?.error
                            });


                        } else {
                            // The card has been verified successfully...
                            console.log('Success: Payment method from stripe server', stripePaymentMethodRes?.paymentMethod);


                            this.$axios.post("{{ route('stripe.smart-button.create-payment-method') }}", {
                                    'stripe_payment_method': stripePaymentMethodRes.paymentMethod
                                })
                                .then((response) => {
                                    console.log('Response: Payment method from our server', response);
                                    /** After the card has been added,
                                     * 1. stop the process of loading indicator.
                                     * 2. send the message [card-added] to the parent component.
                                     * 3. send the message [add-flash] and {type: type, message: message} to the parent component.
                                     * 4. close the card model
                                     * 5. reset form of the card model
                                     */
                                    this.isStoring = false;

                                    this.$emit('card-added');

                                    this.$emitter.emit('add-flash', {
                                        type: 'success',
                                        message: 'Your card has been registered successfully'
                                    });

                                    this.$refs.cardModel.toggle();

                                    resetForm();
                                });
                        }
                    })


                },

                originalAddCard(params, {
                    resetForm
                }) {
                    this.isStoring = true;

                    this.$axios.post("{{ route('shop.api.checkout.cart.coupon.apply') }}", params)
                        .then((response) => {
                            this.isStoring = false;

                            this.$emit('card-added');

                            this.$emitter.emit('add-flash', {
                                type: 'success',
                                message: response.data.message
                            });

                            this.$refs.cardModel.toggle();

                            resetForm();
                        })
                        .catch((error) => {
                            this.isStoring = false;

                            this.$refs.cardModel.toggle();

                            if ([400, 422].includes(error.response.request.status)) {
                                this.$emitter.emit('add-flash', {
                                    type: 'warning',
                                    message: error.response.data.message
                                });

                                resetForm();

                                return;
                            }

                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: error.response.data.message
                            });
                        });
                },

                destroyCoupon() {
                    this.$axios.delete("{{ route('shop.api.checkout.cart.coupon.remove') }}", {
                            '_token': "{{ csrf_token() }}"
                        })
                        .then((response) => {
                            this.$emit('card-removed');

                            this.$emitter.emit('add-flash', {
                                type: 'success',
                                message: response.data.message
                            });
                        })
                        .catch(error => console.log(error));
                },

                _getBillingAddressFromCartAddress(cart, options) {
                    const address2 = this.cart.billing_address?.address?.length === 1 ? this.cart.billing_address
                        .address[0] : null;
                    const address1 = this.cart.billing_address?.address?.length > 0 ? this.cart.billing_address
                        .address[this.cart.billing_address?.address?.length - 1] : address2;
                    console.log(address2);
                    if (cart?.billing_address) {
                        const billingAddress = {
                            billing_details: {
                                address: {
                                    city: this.cart.billing_address.city,
                                    country: this.cart.billing_address.country,
                                    line1: options.address1,
                                    line2: options.address2,
                                    postal_code: this.cart.billing_address.postcode,
                                    state: this.cart.billing_address.state,
                                },
                                email: this.cart.billing_address.email,
                                name: options.name,
                                phone: this.cart.billing_address.phone
                            },
                        };
                        return billingAddress;
                    } else {
                        const billingAddress = {
                            billing_details: {
                                address: {
                                    city: null,
                                    country: null,
                                    line1: null,
                                    line2: null,
                                    postal_code: null,
                                    state: null,
                                },
                                email: null,
                                name: options.name,
                                phone: null,
                            },
                        }
                        return billingAddress;
                    }
                }
            }
        });
    </script>
@endPushOnce
