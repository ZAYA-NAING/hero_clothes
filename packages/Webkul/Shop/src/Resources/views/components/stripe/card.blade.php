<v-card {{ $attributes }}>
    {{ $slot }}
</v-card>

@pushOnce('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script
        type="text/x-template"
        id="v-card-template"
    >
        <slot></slot>
        <!-- Card Holder Name
        <label for="card-holder-name" class="mb-2 block text-base max-sm:text-sm max-sm:mb-1">Card Holder Name</label>
        <input
            type="text"
            name="card_holder_name"
            id="card-holder-name"
            class="mb-1.5 w-full rounded-lg border px-5 py-3 text-base font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 max-sm:px-4 max-md:py-2 max-sm:text-sm"
        > -->
        <!-- Card Element (Card Number, Expiration, CVV) -->
        <label for="card-element" class="mb-2 block text-base max-sm:text-sm max-sm:mb-1">Credit or debit card</label>
        <div id="card-element" class="mb-1.5 w-full rounded-lg border px-5 py-3 text-base font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 max-sm:px-4 max-md:py-2 max-sm:text-sm"></div>

        <!-- Card Number
        <label for="card-number" class="mb-2 block text-base max-sm:text-sm max-sm:mb-1">Card Number</label>
        <div id="card-number" class="mb-1.5 w-full rounded-lg border px-5 py-3 text-base font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 max-sm:px-4 max-md:py-2 max-sm:text-sm"></div> -->
        <!-- Card Expiration
        <label for="card-expiry" class="mb-2 block text-base max-sm:text-sm max-sm:mb-1">Card Expiry</label>
        <div id="card-expiry" class="mb-1.5 w-full rounded-lg border px-5 py-3 text-base font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 max-sm:px-4 max-md:py-2 max-sm:text-sm"></div>  -->
        <!-- Card CVC
        <label for="card-cvc" class="mb-2 block text-base max-sm:text-sm max-sm:mb-1">Card CVC</label>
        <div id="card-cvc"class="mb-1.5 w-full rounded-lg border px-5 py-3 text-base font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 max-sm:px-4 max-md:py-2 max-sm:text-sm"></div> -->

        <!-- Used to display form errors. -->
        <div id="card-errors" role="alert" class="text-red-500 text-xs italic"></div>
        <div class="stripe-errors"></div>

        <button id="custom-button" class="primary-button w-max rounded-2xl bg-navyBlue px-11 py-3 max-md:mb-4 max-md:w-full max-md:max-w-full max-md:rounded-lg max-sm:py-1.5" @click="createPayment($event)">
            Create Payment
        </button>

    </script>

    <script type="module">
        app.component('v-card', {
            template: '#v-card-template',

            data: function() {
                return {
                    stripe: null,
                    token: null,
                    cardEl: null,
                    cardNumber: null,
                    cardExpiry: null,
                    cardCvc: null,
                };
            },

            computed: {
                stripeElements() {
                    console.log(this.stripe.elements());
                    return this.stripe.elements();
                },
            },

            mounted: function() {
                this.stripe = Stripe('{{ env('STRIPE_PUBLISHABLE_KEY') }}');
                console.log(this.stripe)
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
                this.cardEl = this.stripeElements.create('card', {
                    hidePostalCode: true,
                    style: style
                });
                this.cardEl.mount('#card-element');
            },
            methods: {
                createPayment: async function(e) {
                    e.preventDefault();
                    const {
                        paymentMethod,
                        error
                    } = await stripe.createPaymentMethod(
                        'card', cardElement, {
                            billing_details: {
                                name: 'ZAYA'
                            }
                        }
                    );
                    if (error) {
                        // handle error here
                        document.getElementById('card-errors').innerHTML = error.message;
                        return;
                    }
                    console.log(paymentMethod);
                    // handle the token
                    // send it to your server
                },
                createToken: async function(e) {
                    e.preventDefault();
                    // const cardNo = document.querySelectorAll("input[name='cardnumber']");
                    // const cardNo = document.getElementsByClassName("Input")[0];
                    // console.log(cardNo);
                    console.log(this.cardEl);
                    const {
                        token,
                        error
                    } = await this.stripe.createToken(this.cardEl);
                    if (error) {
                        // handle error here
                        document.getElementById('card-errors').innerHTML = error.message;
                        return;
                    }
                    console.log(token);
                    // handle the token
                    // send it to your server
                },
            }
        });
    </script>
@endPushOnce

{{-- <v-card {{ $attributes }}>
    {{ $slot }}
</v-card>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-card-template"
    >
        <label>Card Number</label>
        <div id="card-number"></div>

        <label>Card Expiry</label>
        <div id="card-expiry"></div>

        <label>Card CVC</label>
        <div id="card-cvc"></div>

        <div id="card-error"></div>
        <button id="custom-button" @click="createToken">Generate Token</button>

        <slot></slot>
    </script>

    <script type="module">
        app.component('v-card', {
            template: '#v-card-template',

            data: function() {
                return {
                    stripe: null,
                    token: null,
                    cardNumber: null,
                    cardExpiry: null,
                    cardCvc: null,
                };
            },

            computed: {
                stripeElements () {
                    this.stripe = Stripe('{{env('STRIPE_PUBLISHABLE_KEY')}}');
                    console.log(this.stripe.elements());
                    return this.stripe.elements();
                },
            },

            mounted: function() {
                // console.log(this.$stripe);
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
                this.cardNumber = this.stripeElements.create('cardNumber', { style });
                this.cardNumber.mount('#card-number');
                this.cardExpiry = this.stripeElements.create('cardExpiry', { style });
                this.cardExpiry.mount('#card-expiry');
                this.cardCvc = this.stripeElements.create('cardCvc', { style });
                this.cardCvc.mount('#card-cvc');
            },
            methods: {
                createToken: async function() {
                const { token, error } = await this.$stripe.createToken(this.cardNumber);
                if (error) {
                    // handle error here
                    document.getElementById('card-error').innerHTML = error.message;
                    return;
                }
                    console.log(token);
                    // handle the token
                    // send it to your server
                },
            }
        });
    </script>
@endPushOnce --}}
