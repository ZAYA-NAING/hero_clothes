<!-- Checkout Deposit Vue JS Component -->
<v-checkout-deposit>
    <div class="flex items-center">
        <span class="cursor-pointer text-base font-medium text-blue-700">
            @lang('shop::app.checkout.deposit.title')
        </span>
    </div>
</v-checkout-deposit>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-checkout-deposit-template"
    >
        <div>
            <div class="flex items-center">
                <span
                    class="cursor-pointer text-base font-medium text-blue-700"
                    role="button"
                    @click="$refs.depositModel.open()"
                >
                    @lang('shop::app.checkout.deposit.title')
                </span>
            </div>

            <!-- Deposit Form -->
            <x-shop::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                {!! view_render_event('bagisto.shop.checkout.deposit.before') !!}

                <!-- Deposit form -->
                <form @submit="handleSubmit($event, deposit)">
                    {!! view_render_event('bagisto.shop.checkout.deposit.form_controls.before') !!}

                    <!-- Deposit modal -->
                    <x-shop::modal ref="depositModel">
                        <!-- Modal Header -->
                        <x-slot:header>
                            <h2 class="text-2xl font-medium max-md:text-base">
                                @lang('shop::app.checkout.deposit.title')
                            </h2>
                        </x-slot>

                        <!-- Modal Content -->
                        <x-slot:content>
                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label class="required">
                                    @lang('shop::app.checkout.deposit.amount')
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control
                                    type="number"
                                    class="px-6 py-4"
                                    name="amount"
                                    rules="required|number"
                                    :label="trans('shop::app.checkout.deposit.amount')"
                                    placeholder="email@example.com"
                                    :aria-label="trans('shop::app.checkout.deposit.amount')"
                                    aria-required="true"
                                />

                                <x-shop::form.control-group.error control-name="amount" />
                            </x-shop::form.control-group>

                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.label class="required">
                                    @lang('shop::app.checkout.deposit.card-holder-name')
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control
                                    type="text"
                                    class="px-6 py-4"
                                    id="card-holder-name"
                                    name="card-holder-name"
                                    rules="required"
                                    :label="trans('shop::app.checkout.deposit.card-holder-name')"
                                    :placeholder="trans('shop::app.checkout.deposit.card-holder-name')"
                                    :aria-label="trans('shop::app.checkout.deposit.card-holder-name')"
                                    aria-required="true"
                                />

                                <x-shop::form.control-group.error control-name="card-holder-name" />
                            </x-shop::form.control-group>
                        </x-slot>

                        <!-- Modal Footer -->
                        <x-slot:footer>
                            <div class="flex flex-wrap items-center gap-4">
                                <x-shop::button
                                    class="primary-button max-w-none flex-auto rounded-2xl px-11 py-3 max-md:rounded-lg max-md:py-1.5"
                                    :title="trans('shop::app.checkout.deposit.title')"
                                    ::loading="isStoring"
                                    ::disabled="isStoring"
                                />
                            </div>
                        </x-slot>
                    </x-shop::modal>

                    {!! view_render_event('bagisto.shop.checkout.deposit.form_controls.after') !!}
                </form>
            </x-shop::form>

            {!! view_render_event('bagisto.shop.checkout.deposit.after') !!}
        </div>
    </script>

    <script type="module">
        app.component('v-checkout-deposit', {
            template: '#v-checkout-deposit-template',

            data() {
                return {
                    isStoring: false,
                }
            },

            methods: {
                deposit(params, { resetForm }) {
                    this.isStoring = true;

                    this.$axios.post("{{ route('shop.api.customers.session.create') }}", params)
                        .then((response) => {
                            this.isStoring = false;

                            window.location.reload();
                        })
                        .catch((error) => {
                            this.isStoring = false;

                            if (error.response.status == 422) {
                                setErrors(error.response.data.errors);

                                return;
                            }

                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                        });
                },
            }
        })
    </script>
@endPushOnce
