<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Stripe\StripeClient;
use Webkul\Customer\Models\Customer;
use Webkul\Customer\Repositories\CustomerPaymentMethodRepository;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Shop\Http\Requests\Customer\PaymentMethodRequest;

class PaymentMethodController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected CustomerPaymentMethodRepository $customerPaymentMethodRepository) {}

    /**
     * Payment route index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $s = auth()->guard('customer')->user()->customer_payment_methods;
        Log::info("[Payment route index page] $s");
        return view('shop::customers.account.payment_methods.index')->with('customer_payment_methods', auth()->guard('customer')->user()->customer_payment_methods);
    }

    /**
     * Add Payment methods route index page.
     *
     * @return \Illuminate\View\View
     */
    public function addIndex()
    {
        $s = auth()->guard('customer')->user()->customer_payment_methods;
        Log::info("[Add Payment methods route index page] $s");
        return view('shop::customers.account.payment_methods.add.index')->with('customer_payment_methods', auth()->guard('customer')->user()->customer_payment_methods);
    }

    /**
     * Show the payment method add form.
     *
     * @return \Illuminate\View\View
     */
    public function add(string $type)
    {
        Log::info("[Show the payment method ADD form] pm_type={$type}");
        $customer = auth()->guard('customer')->user();
        Log::info("[Show the payment create form] customer={$customer}");
        // $stripeCustomer = Cashier::findBillable(null);
        $stripeCustomer = $customer->createOrGetStripeCustomer();
        Log::info("[Show the payment create form] stripeCustomer={$stripeCustomer}");
        $stripeCeateSetupIntent = $customer->createSetupIntent();
        Log::info("[Show the payment create form] setupIntent={$stripeCeateSetupIntent}");
        return view('shop::customers.account.payment_methods.create', [
            'intent' => $stripeCeateSetupIntent
        ]);
    }

    /**
     * Show the payment method create form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $customer = auth()->guard('customer')->user();
        Log::info("[Show the payment create form] customer={$customer}");
        // $stripeCustomer = Cashier::findBillable(null);
        $stripeCustomer = $customer->createOrGetStripeCustomer();
        Log::info("[Show the payment create form] stripeCustomer={$stripeCustomer}");
        $stripeCeateSetupIntent = $customer->createSetupIntent();
        Log::info("[Show the payment create form] setupIntent={$stripeCeateSetupIntent}");
        return view('shop::customers.account.payment_methods.create', [
            'intent' => $stripeCeateSetupIntent
        ]);
    }

    /**
     * Create a new payment method for customer.
     *
     * @return view
     */
    public function store(Request $request)
    {
        Log::info("[Create a new payment method for customer] customer={$request}");
        // $request->validate([
        //     'card_holder_name' => ['required'],
        //     'card_no'          => ['required', 'unique:payment-methods,card_no'],
        //     'card_expiration'  => ['nullable'],
        //     'card_cvc_no'      => ['nullable'],
        // ]);

        $customer = auth()->guard('customer')->user();

        Event::dispatch('customer.payment-methods.create.before');

        $data = array_merge(request()->only([
            'card_holder_name',
            'card_no',
            'card_expiration',
            'card_cvc_no',
            'default_payment',
        ]), [
            'customer_id' => $customer->id,
        ]);

        $customerPaymentMethod = $this->customerPaymentMethodRepository->create($data);

        $stripeCustomer = Cashier::findBillable($customer->stripe_id);

        $stripeCustomer->addPaymentMethod(request()->input('payment_method'));

        Log::info("[Create a new payment method for customer into the stripe account] stripeCustomer={$stripeCustomer}");

        Event::dispatch('customer.payment-methods.create.after', $customerPaymentMethod);

        session()->flash('success', trans('shop::app.customers.account.payment-methods.index.create-success'));

        return redirect()->route('shop.customers.account.payment_methods.index');
    }

    /**
     * For editing the existing payment-methods of current logged in customer.
     *
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $customerPaymentMethod = $this->customerPaymentMethodRepository->findOneWhere([
            'id'          => $id,
            'customer_id' => auth()->guard('customer')->id(),
        ]);

        if (! $customerPaymentMethod) {
            abort(404);
        }

        return view('shop::customers.account.payment-methods.edit')->with('customer_payment_method', $customerPaymentMethod);
    }

    /**
     * Edit's the pre-made resource of customer called Payment.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(int $id, PaymentMethodRequest $request)
    {
        $customer = auth()->guard('customer')->user();

        if (! $customer->customer_payment_methods()->find($id)) {
            session()->flash('warning', trans('shop::app.customers.account.payment-methods.index.security-warning'));

            return redirect()->route('shop.customers.account.payment_methods.index');
        }

        Event::dispatch('customer.payment-methods.update.before', $id);

        $data = array_merge(request()->only([
            'company_name',
            'card_holder_name',
            'card_number',
            'card_expiration',
            'card_cvv',
        ]), [
            'customer_id' => $customer->id,
        ]);

        $customerPaymentMethod = $this->customerPaymentMethodRepository->update($data, $id);

        Event::dispatch('customer.payment-methods.update.after', $customerPaymentMethod);

        session()->flash('success', trans('shop::app.customers.account.payment-methods.index.edit-success'));

        return redirect()->route('shop.customers.account.payment_methods.index');
    }

    /**
     * To change the default customer payment method or make the default customer payment method,
     * by default when first customer payment method is created will be the default customerPaymentMethod.
     *
     * @return \Illuminate\Http\Response
     */
    public function makeDefault(int $id)
    {
        $customer = auth()->guard('customer')->user();

        $defaultPaymentMethod = $customer->customer_payment_methods()->where('default_payment', 1)->first();

        $paymentMethodToSetDefault = $customer->customer_payment_methods()->find($id);

        if ($defaultPaymentMethod && $defaultPaymentMethod->id !== $id) {
            $defaultPaymentMethod->update(['default_payment_method' => 0]);
        }

        if ($paymentMethodToSetDefault) {
            $paymentMethodToSetDefault->update(['default_payment_method' => 1]);
        } else {
            session()->flash('success', trans('shop::app.customers.account.payment-methods.index.default-delete'));
        }

        return redirect()->back();
    }

    /**
     * Delete customer payment method of the current customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $customerPaymentMethod = $this->customerPaymentMethodRepository->findOneWhere([
            'id'          => $id,
            'customer_id' => auth()->guard('customer')->user()->id,
        ]);

        if (! $customerPaymentMethod) {
            abort(404);
        }

        Event::dispatch('customer.payment-methods.delete.before', $id);

        $this->customerPaymentMethodRepository->delete($id);

        Event::dispatch('customer.payment-methods.delete.after', $id);

        session()->flash('success', trans('shop::app.customers.account.payment-methods.index.delete-success'));

        return redirect()->route('shop.customers.account.payment_methods.index');
    }
}
