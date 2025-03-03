<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="@lang('shop::app.checkout.onepage.index.checkout')" />

    <meta name="keywords" content="@lang('shop::app.checkout.onepage.index.checkout')" />
@endPush

<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.checkout.onepage.index.checkout')
    </x-slot>


    {!! view_render_event('bagisto.shop.checkout.onepage.header.before') !!}

    <!-- Page Header -->
    <div class="flex-wrap">
        <div
            class="flex w-full justify-between border border-b border-l-0 border-r-0 border-t-0 px-[60px] py-4 max-lg:px-8 max-sm:px-4">
            <div class="flex items-center gap-x-14 max-[1180px]:gap-x-9">
                <a href="{{ route('shop.home.index') }}" class="flex min-h-[30px]" aria-label="@lang('shop::checkout.onepage.index.bagisto')">
                    <img src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                        alt="{{ config('app.name') }}" width="131" height="29">
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

        {!! view_render_event('bagisto.shop.checkout.onepage.breadcrumbs.before') !!}

        <!-- Breadcrumbs -->
        @if (core()->getConfigData('general.general.breadcrumbs.shop'))
            <x-shop::breadcrumbs name="checkout" />
        @endif

        {!! view_render_event('bagisto.shop.checkout.onepage.breadcrumbs.after') !!}

        <!-- Checkout Vue Component -->
        <v-checkout>
            <!-- Shimmer Effect -->
            <x-shop::shimmer.checkout.onepage />
        </v-checkout>
    </div>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-checkout-template">
            <template v-if="! cart">
                <!-- Shimmer Effect -->
                <x-shop::shimmer.checkout.onepage />
            </template>

            <template v-else>
                <div class="grid grid-cols-[1fr_auto] gap-8 max-lg:grid-cols-[1fr] max-md:gap-5">
                    <!-- Included Checkout Summary Blade File For Mobile view -->
                    <div class="hidden max-md:block">
                        @include('shop::checkout.onepage.summary')
                    </div>

                    <div
                        class="overflow-y-auto max-md:grid max-md:gap-4"
                        id="steps-container"
                    >
                        <!-- Included Addresses Blade File -->
                        <template v-if="['address', 'shipping', 'payment', 'review'].includes(currentStep)">
                            @include('shop::checkout.onepage.address')
                        </template>

                        <!-- Included Shipping Methods Blade File -->
                        <template v-if="cart.have_stockable_items && ['shipping', 'payment', 'review'].includes(currentStep)">
                            @include('shop::checkout.onepage.shipping')
                        </template>

                        <!-- Included Payment Methods Blade File -->
                        <template v-if="['payment', 'review'].includes(currentStep)">
                            @include('shop::checkout.onepage.payment')
                        </template>
                    </div>

                    <!-- Included Checkout Summary Blade File For Desktop view -->
                    <div class="sticky top-8 block h-max w-[442px] max-w-full max-lg:w-auto max-lg:max-w-[442px] ltr:pl-8 max-lg:ltr:pl-0 rtl:pr-8 max-lg:rtl:pr-0">
                        <div class="block max-md:hidden">
                            @include('shop::checkout.onepage.summary')
                        </div>

                        <div
                            :class="[isStripeSmartButton ? '' : 'flex justify-end']"
                            v-if="canPlaceOrder"
                        >
                            <template v-if="cart.payment_method == 'paypal_smart_button_v2'">
                                {!! view_render_event('bagisto.shop.checkout.onepage.summary.paypal_smart_button_v2.before') !!}

                                <!-- Paypal Smart Button Vue Component -->
                                <v-paypal-smart-button-v2></v-paypal-smart-button-v2>

                                {!! view_render_event('bagisto.shop.checkout.onepage.summary.paypal_smart_button_v2.after') !!}
                            </template>
                            <!-- Stripe Smart Button -->
                            <template v-else-if="cart.payment_method == 'stripe_smart_button' && ['payment', 'review'].includes(currentStep)">
                                 <!-- Header -->
                                 <h1 class="text-2xl font-medium max-md:py-4 max-md:text-base">
                                    Add Cards
                                </h1>
                                <div class="flex justify-between items-center">
                                    <!-- Card -->
                                    {!! view_render_event('bagisto.shop.checkout.onepage.add-payment.before') !!}
                                    <!-- TODO: Have not been registered [card] payment method  -->
                                    <span v-if="stripePaymentMethods.message">
                                        Add credit or debit card
                                    </span>
                                    <!-- Stripe Payment Method Switcher -->
                                    <x-shop::dropdown v-if="!stripePaymentMethods.message">
                                        <x-slot:toggle>
                                            <!-- Dropdown Toggler -->
                                            <div
                                                class="flex cursor-pointer items-center gap-2.5 py-3"
                                                role="button"
                                                tabindex="0"
                                            >
                                                <!-- TODO: Have not been registered [card] payment method  -->
                                                <img
                                                    class="max-h-11 max-w-14"
                                                    src="{{ bagisto_asset('images/money-transfer.png') }}"
                                                    width="25"
                                                    height="25"
                                                    :alt="stripePaymentMethod.card"
                                                    :title="stripePaymentMethod.card"
                                                />
                                                <span>
                                                   @{{ stripePaymentMethod?.card?.last4 }}
                                                </span>

                                                <span
                                                    class="text-2xl"
                                                    :class="{'icon-arrow-up': stripePaymentMethodToggler, 'icon-arrow-down': ! stripePaymentMethodToggler}"
                                                    role="presentation"
                                                ></span>
                                            </div>
                                        </x-slot>

                                        <!-- Dropdown Content -->
                                        <x-slot:content class="journal-scroll max-h-[500px] overflow-auto !p-0">
                                            <v-stripe-payment-method-switcher :stripePaymentMethods="stripePaymentMethods" @update-selected-stripe-payment-method="updateSelectedStripePaymentMethod"></v-stripe-payment-method-switcher>
                                        </x-slot>
                                    </x-shop::dropdown>
                                     <!-- Add Card -->
                                    {!! view_render_event('bagisto.shop.checkout.onepage.card.before') !!}

                                    @include('shop::checkout.card')

                                    {!! view_render_event('bagisto.shop.checkout.onepage.card.after') !!}
                                </div>
                                {!! view_render_event('bagisto.shop.checkout.onepage.add-payment.after') !!}

                                <!-- Stripe Smart Button Vue Component -->
                                <v-stripe-smart-button :stripePaymentMethod="stripePaymentMethod"></v-stripe-smart-button>

                            </template>

                            <template v-else>
                                <x-shop::button
                                    type="button"
                                    class="primary-button w-max rounded-2xl bg-navyBlue px-11 py-3 max-md:mb-4 max-md:w-full max-md:max-w-full max-md:rounded-lg max-sm:py-1.5"
                                    :title="trans('shop::app.checkout.onepage.summary.place-order')"
                                    ::disabled="isPlacingOrder"
                                    ::loading="isPlacingOrder"
                                    @click="placeOrder"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </script>

        <script type="text/x-template" id="v-stripe-payment-method-switcher-template">
            <div class="my-2.5 mx-2.5 grid gap-1 max-md:my-0">
                <template v-if="paymentMethods">
                    <div class="flex justify-between cursor-pointer items-center gap-2.5 px-2 py-2 text-base hover:bg-gray-100"
                    :class="{'bg-gray-100': paymentMethod.id == method.id}"
                    v-for="paymentMethod in paymentMethods">
                        <img
                            class="max-h-11 max-w-14"
                            :src="brandImageUrls[paymentMethod.card.brand]"
                            width="25"
                            height="25"
                            :alt="paymentMethod.card.brand"
                            :title="paymentMethod.card.brand"
                        />
                        <span class="flex cursor-pointer items-center gap-2.5 px-2 py-2 text-base hover:bg-gray-100" @click="change(paymentMethod)">
                            @{{ paymentMethod.card.last4 }}
                        </span>
                    </div>
                </template>

            </div>
        </script>

        <script type="module">
            app.component('v-checkout', {
                template: '#v-checkout-template',

                data() {
                    return {
                        cart: null,

                        displayTax: {
                            prices: "{{ core()->getConfigData('sales.taxes.shopping_cart.display_prices') }}",

                            subtotal: "{{ core()->getConfigData('sales.taxes.shopping_cart.display_subtotal') }}",

                            shipping: "{{ core()->getConfigData('sales.taxes.shopping_cart.display_shipping_amount') }}",
                        },

                        isPlacingOrder: false,

                        currentStep: 'address',

                        shippingMethods: null,

                        paymentMethods: null,

                        isStripeSmartButton: false,

                        canPlaceOrder: false,

                        stripePaymentMethods: null,

                        stripePaymentMethod: null,

                        stripePaymentMethodToggler: '',
                    }
                },

                mounted() {
                    this.getCart();

                    this.getStripePaymentMethods();
                },

                methods: {
                    getCart() {
                        this.$axios.get("{{ route('shop.checkout.onepage.summary') }}")
                            .then(response => {
                                this.cart = response.data.data;

                                this.scrollToCurrentStep();
                            })
                            .catch(error => {});
                    },

                    stepForward(step) {
                        this.currentStep = step;

                        if (step == 'review') {
                            this.canPlaceOrder = true;

                            return;
                        }

                        this.canPlaceOrder = false;

                        if (this.currentStep == 'shipping') {
                            this.shippingMethods = null;
                        } else if (this.currentStep == 'payment') {
                            this.paymentMethods = null;
                        }
                    },

                    stepProcessed(data) {
                        if (this.currentStep == 'shipping') {
                            this.shippingMethods = data;
                        } else if (this.currentStep == 'payment') {
                            this.paymentMethods = data;
                            this.isStripeSmartButton = this.cart.payment_method == 'stripe_smart_button';
                        }

                        this.getCart();
                    },

                    scrollToCurrentStep() {
                        let container = document.getElementById('steps-container');

                        if (!container) {
                            return;
                        }

                        container.scrollIntoView({
                            behavior: 'smooth',
                            block: 'end'
                        });
                    },

                    placeOrder() {
                        this.isPlacingOrder = true;

                        this.$axios.post('{{ route('shop.checkout.onepage.orders.store') }}')
                            .then(response => {
                                if (response.data.data.redirect) {
                                    window.location.href = response.data.data.redirect_url;
                                } else {
                                    window.location.href = '{{ route('shop.checkout.onepage.success') }}';
                                }

                                this.isPlacingOrder = false;
                            })
                            .catch(error => {
                                this.isPlacingOrder = false

                                this.$emitter.emit('add-flash', {
                                    type: 'error',
                                    message: error.response.data.message
                                });
                            });
                    },

                    getStripePaymentMethods() {
                        this.$axios.get("{{ route('stripe.smart-button.get-payment-methods') }}")
                            .then(response => {
                                this.stripePaymentMethods = response.data;

                                this.stripePaymentMethod = this.stripePaymentMethods[0] ?? response.data;

                                console.log(response);
                            })
                            .catch(error => {
                                console.log(error);
                            });
                    },

                    updateSelectedStripePaymentMethod(payload) {
                        this.stripePaymentMethod = payload;
                    }
                },
            });

        app.component('v-stripe-payment-method-switcher', {
            template: '#v-stripe-payment-method-switcher-template',

            props: ['stripePaymentMethods'],

            data() {
                return {
                    method: null,

                    paymentMethods: null,

                    brandImageUrls: {
                        visa: "{{ bagisto_asset('images/visa.svg')}}",
                        mastercard: "{{ bagisto_asset('images/mastercard.svg')}}",
                        amex: "{{ bagisto_asset('images/money-transfer.png')}}",
                        discover:  "{{ bagisto_asset('images/money-transfer.png')}}",
                        jcb:  "{{ bagisto_asset('images/money-transfer.png')}}",
                        unknown:  "{{ bagisto_asset('images/money-transfer.png')}}",
                    }
                };
            },

            mounted() {
                this.method = !this.stripePaymentMethods[0] ? null : this.stripePaymentMethods[0];

                this.paymentMethods = this.stripePaymentMethods;

                console.log(this.brandImageUrls);
            },

            methods: {
                change(method) {
                    this.method = method;

                    this.$emit("update-selected-stripe-payment-method", method);
                }
            }
        });
        </script>
    @endPushOnce
</x-shop::layouts>
