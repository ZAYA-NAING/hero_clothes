<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.account.payment-methods.index.add-payment-method')
    </x-slot>

    <!-- Breadcrumbs -->
    @if (core()->getConfigData('general.general.breadcrumbs.shop'))
        @section('breadcrumbs')
            <x-shop::breadcrumbs name="payment-methods" />
        @endSection
    @endif

    <div class="max-md:hidden">
        <x-shop::layouts.account.navigation />
    </div>

    {!! view_render_event('bagisto.shop.customers.account.payment_methods.add.before') !!}

    <div class="mx-4 flex-auto">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <!-- Back Button -->
                <a class="grid md:hidden" href="{{ route('shop.customers.account.index') }}">
                    <span class="icon-arrow-left rtl:icon-arrow-right text-2xl"></span>
                </a>

                <h2
                    class="text-2xl font-medium max-md:text-xl max-sm:text-base ltr:ml-2.5 md:ltr:ml-0 rtl:mr-2.5 md:rtl:mr-0">
                    @lang('shop::app.customers.account.payment-methods.index.title')
                </h2>
            </div>
        </div>
        <!-- Page Content -->
        <div class="container px-[60px] max-lg:px-8 max-sm:px-4">
            <v-payment-methods :methods="">
                <x-shop::shimmer.customers.account.payment-methods />
            </v-payment-methods>
        </div>
    </div>

    {!! view_render_event('bagisto.shop.customers.account.payment_methods.add.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-payment-methods-template"
        >
            <div class="mb-7 max-md:last:!mb-0">
                <template v-if="! paymentMethods">
                    <!-- Payment Method shimmer Effect -->
                    <x-shop::shimmer.customers.account.payment-methods />
                    <!-- Payment Method Empty Page -->
                    <div class="m-auto grid w-full place-content-center items-center justify-items-center py-32 text-center">
                        <img class="max-md:h-[100px] max-md:w-[100px]" src="{{ bagisto_asset('images/no-address.png') }}"
                            alt="Empty Address" title="">

                        <p class="text-xl max-md:text-sm" role="heading">
                            @lang('shop::app.customers.account.payment-methods.index.empty-payment')
                        </p>
                    </div>
                </template>

                <template v-else>
                    {!! view_render_event('bagisto.shop.customers.account.payment_methods.add.accordion.before') !!}

                    <!-- Accordion Blade Component -->
                    <x-shop::accordion class="overflow-hidden !border-b-0 max-md:rounded-lg max-md:!border-none max-md:!bg-gray-100">
                        <!-- Accordion Blade Component Header
                            This component header is used `display: none` of css  -->
                        <x-slot:header class="hidden px-0 py-4 max-md:p-3 max-md:text-sm max-md:font-medium max-sm:p-2">
                            <div class="flex items-center justify-between">
                                <h2 class="text-2xl font-medium max-md:text-base">
                                    All
                                </h2>
                            </div>
                        </x-slot>

                        <!-- Accordion Blade Component Content -->
                        <x-slot:content class="mt-8 !p-0 max-md:mt-0 max-md:rounded-t-none max-md:border max-md:border-t-0 max-md:!p-4">
                            <div class="flex flex-wrap gap-7 max-md:gap-4 max-sm:gap-2.5">
                                <div
                                    class="relative cursor-pointer max-md:max-w-full max-md:flex-auto"
                                    v-for="(payment, index) in paymentMethods"
                                >
                                    {!! view_render_event('bagisto.shop.customers.account.payment_methods.add.accordion.method.before') !!}

                                    <input
                                        type="radio"
                                        :name="paymentMethods"
                                        :value="payment.method"
                                        :id="payment.method"
                                        class="peer hidden"
                                        @change="store(payment)"
                                    >

                                    <label
                                        :for="payment.method"
                                        class="icon-radio-unselect peer-checked:icon-radio-select absolute top-5 cursor-pointer text-2xl text-navyBlue ltr:right-5 rtl:left-5"
                                    >
                                    </label>

                                    <label
                                        :for="payment.method"
                                        class="block w-[190px] cursor-pointer rounded-xl border border-zinc-200 p-5 max-md:flex max-md:w-full max-md:gap-5 max-md:rounded-lg max-sm:gap-4 max-sm:px-4 max-sm:py-2.5"
                                    >
                                        {!! view_render_event('bagisto.shop.customers.account.payment_methods.add.accordion.image.before') !!}

                                        <img
                                            class="max-h-11 max-w-14"
                                            :src="payment.image"
                                            width="55"
                                            height="55"
                                            :alt="payment.method_title"
                                            :title="payment.method_title"
                                        />

                                        {!! view_render_event('bagisto.shop.customers.account.payment_methods.add.accordion.image.after') !!}

                                        <div>
                                            {!! view_render_event('bagisto.shop.customers.account.payment_methods.add.accordion.title.before') !!}

                                            <p class="mt-1.5 text-sm font-semibold max-md:mt-1 max-sm:mt-0">
                                                @{{ 'Paypal' }}
                                            </p>

                                            {!! view_render_event('bagisto.shop.checkout.onepage.payment-method.accordion.title.after') !!}

                                        </div>
                                    </label>
                                    {!! view_render_event('bagisto.shop.customers.account.payment_methods.add.accordion.after') !!}

                                    <!-- Todo implement the additionalDetails -->
                                    {{-- \Webkul\Payment\Payment::getAdditionalDetails($payment['method'] --}}
                                </div>
                            </div>
                        </x-slot>
                    </x-shop::accordion>
                    {!! view_render_event('bagisto.shop.customers.account.payment_methods.add.accordion.after') !!}
                </template>
            </div>

            <div class="mb-7 max-md:last:!mb-0">
                <template v-if="! paymentMethodTypes && !paymentMethodTypes.length">
                    <!-- Fixed: Payment Method Types shimmer Effect -->
                    <x-shop::shimmer.customers.account.payment-methods />
                </template>

                <template v-else>
                    {!! view_render_event('bagisto.shop.customers.account.payment_methods.add.accordion.before') !!}

                    <!-- Accordion Blade Component -->
                    <x-shop::accordion class="overflow-hidden !border-b-0 max-md:rounded-lg max-md:!border-none max-md:!bg-gray-100">

                        <!-- Accordion Blade Component Header
                            This component header is used `display: none` of css  -->
                        <x-slot:header class="hidden px-0 py-4 max-md:p-3 max-md:text-sm max-md:font-medium max-sm:p-2">
                            <div class="flex items-center justify-between">
                                <h2 class="text-2xl font-medium max-md:text-base">
                                All
                                </h2>
                            </div>
                        </x-slot>

                        <!-- Accordion Blade Component Content -->
                        <x-slot:content class="mt-8 !p-0 max-md:mt-0 max-md:rounded-t-none max-md:border max-md:border-t-0 max-md:!p-4">
                            <div class="flex flex-wrap gap-7 max-md:gap-4 max-sm:gap-2.5">
                                <div
                                    class="relative cursor-pointer max-md:max-w-full max-md:flex-auto"
                                    v-for="(type, index) in paymentMethodTypes"
                                >
                                    {!! view_render_event('bagisto.shop.customers.account.payment_methods.add.accordion.method.before') !!}

                                    <input
                                        type="radio"
                                        :name="paymentMethodTypes"
                                        :value="type"
                                        :id="type"
                                        class="peer hidden"
                                        @change="navigate(type)"
                                    >

                                    <label
                                        :for="type"
                                        class="icon-radio-unselect peer-checked:icon-radio-select absolute top-5 cursor-pointer text-2xl text-navyBlue ltr:right-5 rtl:left-5"
                                    >
                                    </label>

                                    <label
                                        :for="type"
                                        class="block w-[190px] cursor-pointer rounded-xl border border-zinc-200 p-5 max-md:flex max-md:w-full max-md:gap-5 max-md:rounded-lg max-sm:gap-4 max-sm:px-4 max-sm:py-2.5"
                                    >
                                        {!! view_render_event('bagisto.shop.customers.account.payment_methods.add.accordion.image.before') !!}

                                        <div>
                                            {!! view_render_event('bagisto.shop.customers.account.payment_methods.add.accordion.title.before') !!}

                                            <p class="mt-1.5 text-sm font-semibold max-md:mt-1 max-sm:mt-0">
                                                @{{ type }}
                                            </p>

                                            {!! view_render_event('bagisto.shop.checkout.onepage.payment-method.accordion.title.after') !!}

                                        </div>
                                    </label>
                                    {!! view_render_event('bagisto.shop.customers.account.payment_methods.add.accordion.after') !!}
                                </div>
                            </div>
                        </x-slot>
                    </x-shop::accordion>
                    {!! view_render_event('bagisto.shop.customers.account.payment_methods.add.accordion.after') !!}
                </template>
            </div>
        </script>

        <script type="module">
            app.component('v-payment-methods', {
                template: '#v-payment-methods-template',

                props: {
                    methods: {
                        type: Object,
                        required: true,
                        default: () => null,
                    },
                },

                emits: ['processing', 'processed'],



                data() {
                    return {
                        paymentMethods: null,

                        hasPaymentMethodTypes: false,

                        paymentMethodTypes: [],
                    }
                },

                computed: {

                },

                mounted() {
                    this.fetchPaymentMethods();
                    // console.log(this.paymentMethodCount);
                },

                methods: {
                    fetchPaymentMethods() {
                        this.$axios.get("{{ route('shop.api.customers.account.payment_methods.add.supported_payment_methods') }}")
                            .then(res => {
                                console.log(res);
                                this.paymentMethods = res?.data;
                        });
                    },

                    store(selectedMethod) {
                        console.log(selectedMethod)
                        this.paymentMethodTypes = selectedMethod.types;
                        console.log(this.paymentMethodTypes);
                    },

                    navigate(selectedType) {
                        console.log(selectedType);
                    }
                },
            });
        </script>
    @endPushOnce
</x-shop::layouts.account>
