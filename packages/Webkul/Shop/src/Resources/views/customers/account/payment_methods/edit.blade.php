<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.account.payment-methods.edit.edit')
        @lang('shop::app.customers.account.payment-methods.edit.title')
    </x-slot>

    <!-- Breadcrumbs -->
    @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        @section('breadcrumbs')
            <x-shop::breadcrumbs
                name="payment-methods.edit"
                :entity="$paymentMethodMethod"
            />
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

            <h2 class="text-2xl font-medium max-sm:text-base ltr:ml-2.5 md:ltr:ml-0 rtl:mr-2.5 md:rtl:mr-0">
                @lang('shop::app.customers.account.payment-methods.edit.edit')
                @lang('shop::app.customers.account.payment-methods.edit.title')
            </h2>
        </div>

        {!! view_render_event('bagisto.shop.customers.account.payment_methods.edit.before', ['paymentMethod' => $paymentMethodMethod]) !!}

        <!-- Customer Address edit Component-->
        <v-edit-customer-payment>
            <!-- Address Shimmer -->
            <x-shop::shimmer.form.control-group :count="10" />
        </v-edit-customer-payment>

        {!! view_render_event('bagisto.shop.customers.account.payment_methods.edit.after', ['paymentMethod' => $paymentMethodMethod]) !!}
    </div>

    @push('scripts')
        <script
            type="text/x-template"
            id="v-edit-customer-payment-template"
        >
            <!-- Edit Address Form -->
            <x-shop::form
                method="PUT"
                :action="route('shop.customers.account.payment_methods.update',  $paymentMethodMethod->id)"
            >
                {!! view_render_event('bagisto.shop.customers.account.payment_methods.edit_form_controls.before', ['payment' => $paymentMethod]) !!}

                <!-- Company Name -->
                <x-shop::form.control-group>
                    <x-shop::form.control-group.label>
                        @lang('shop::app.customers.account.payment-methods.edit.company-name')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control
                        type="text"
                        name="company_name"
                        :value="old('company_name') ?? $paymentMethod->company_name"
                        :label="trans('shop::app.customers.account.payment-methods.edit.company-name')"
                        :placeholder="trans('shop::app.customers.account.payment-methods.edit.company-name')"
                    />

                    <x-shop::form.control-group.error control-name="company_name" />
                </x-shop::form.control-group>

                {!! view_render_event('bagisto.shop.customers.account.payment_methods.edit_form_controls.company_name.after', ['payment' => $paymentMethod]) !!}

                <!-- Card Holder Name -->
                <x-shop::form.control-group>
                    <x-shop::form.control-group.label class="required">
                        @lang('shop::app.customers.account.payment-methods.edit.card-holder-name')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control
                        type="text"
                        name="card_holder_name"
                        rules="required"
                        :value="old('card_holder_name') ?? $paymentMethod->card_holder_name"
                        :label="trans('shop::app.customers.account.payment-methods.edit.card-holder-name')"
                        :placeholder="trans('shop::app.customers.account.payment-methods.edit.card-holder-name')"
                    />

                    <x-shop::form.control-group.error control-name="card_holder_name" />
                </x-shop::form.control-group>

                {!! view_render_event('bagisto.shop.customers.account.payment_methods.edit_form_controls.card_holder_name.after', ['payment' => $paymentMethod]) !!}

                <!-- Card Number -->
                <x-shop::form.control-group>
                    <x-shop::form.control-group.label class="required">
                        @lang('shop::app.customers.account.payment-methods.edit.card-number')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control
                        type="text"
                        name="card_no"
                        rules="required"
                        :value="old('card_no') ?? $paymentMethod->card_no"
                        :label="trans('shop::app.customers.account.payment-methods.edit.card-number')"
                        :placeholder="trans('shop::app.customers.account.payment-methods.edit.card-number')"
                    />

                    <x-shop::form.control-group.error control-name="card_no" />
                </x-shop::form.control-group>

                {!! view_render_event('bagisto.shop.customers.account.payment_methods.edit_form_controls.card_no.after', ['payment' => $paymentMethod]) !!}

                <!-- Card Expiration  -->
                <x-shop::form.control-group>
                    <x-shop::form.control-group.label class="required">
                        @lang('shop::app.customers.account.payment-methods.create.card-expiration')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control
                        type="yearmonth"
                        name="card_expiration"
                        rules="required"
                        :value="old('card_expiration')"
                        :label="trans('shop::app.customers.account.payment-methods.create.card-expiration')"
                        :placeholder="trans('shop::app.customers.account.payment-methods.create.card-expiration')"
                    />

                    <x-shop::form.control-group.error control-name="card_expiration" />
                </x-shop::form.control-group>

                {!! view_render_event('bagisto.shop.customers.account.payment_methods.create_form_controls.card_expiration.after', ['payment' => $paymentMethod]) !!}

                <!-- Card CVV  -->
                <x-shop::form.control-group>
                    <x-shop::form.control-group.label class="required">
                        @lang('shop::app.customers.account.payment-methods.create.card-cvc')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control
                        type="password"
                        name="card_cvc"
                        rules="required|max:3"
                        :value="old('card_cvc')"
                        :label="trans('shop::app.customers.account.payment-methods.create.card-cvv')"
                        :placeholder="trans('shop::app.customers.account.payment-methods.create.card-cvv')"
                    />

                    <x-shop::form.control-group.error control-name="card_cvc" />
                </x-shop::form.control-group>

                {!! view_render_event('bagisto.shop.customers.account.payment_methods.create_form_controls.card_cvc.after', ['payment' => $paymentMethod]) !!}

                <button
                    type="submit"
                    class="primary-button m-0 block rounded-2xl px-11 py-3 text-center text-base max-md:w-full max-md:max-w-full max-md:rounded-lg max-md:py-1.5"
                >
                    @lang('shop::app.customers.account.payment-methods.edit.update-btn')
                </button>

                {!! view_render_event('bagisto.shop.customers.account.payment_methods.edit_form_controls.after', ['payment' => $paymentMethod]) !!}

            </x-shop::form>
        </script>

        <script type="module">
            app.component('v-edit-customer-payment', {
                template: '#v-edit-customer-payment-template',

                data() {
                    return {

                    };
                },

                methods: {

                },
            });
        </script>
    @endpush

</x-shop::layouts.account>
