<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Laravel\Cashier\Cashier;
// use \Stripe\Stripe;
use Webkul\Customer\Repositories\CustomerRepository;

class StripeSubscriptionController extends Controller
{

    public function __construct(protected CustomerRepository $customerRepository)
    {
        // $this->middleware('auth');
    }

    public function index()
    {
        /**
         * If guest checkout is not allowed then redirect back to the cart page
         */
        if (
            ! auth()->guard('customer')->check()
        ) {
            return redirect()->route('shop.customer.session.index');
        }



        $customer = $this->customerRepository->find(auth()->guard('customer')->user()->id);
        return view('shop::checkout.stripe.index', [
            'intent' => $customer->createSetupIntent(),
        ]);
    }

    public function singleCharge(Request $request)
    {
        // return $request->all();
        $customer = $this->customerRepository->find(auth()->guard('customer')->user()->id);

        $customer->createOrGetStripeCustomer();

        $paymentMethod = $customer->addPaymentMethod(request()->input('payment_method'));

        $customer->charge(request()->input('amount'), $paymentMethod->id);

        return redirect()->route('shop::customers.account.profile.index');
    }

    public function retrievePlans()
    {
        $key = \config('services.stripe.secret');
        $stripe = new \Stripe\StripeClient($key);
        $plansraw = $stripe->plans->all();
        $plans = $plansraw->data;

        foreach ($plans as $plan) {
            $prod = $stripe->products->retrieve(
                $plan->product,
                []
            );
            $plan->product = $prod;
        }
        return $plans;
    }

    public function showSubscription()
    {
        /**
         * If guest checkout is not allowed then redirect back to the cart page
         */
        if (
            ! auth()->guard('customer')->check()
        ) {
            return redirect()->route('shop.customer.session.index');
        }

        $plans = $this->retrievePlans();
        $customer = $this->customerRepository->find(auth()->guard('customer')->user()->id);
        // $user = Auth::user();

        return view('shop::checkout.stripe.subscribe', [
            'user'   => $customer,
            'intent' => $customer->createSetupIntent(),
            'plans'  => $plans
        ]);
    }

    public function processSubscription(Request $request)
    {
        // $user = Auth::user();
        $customer = $this->customerRepository->find(auth()->guard('customer')->user()->id);
        $paymentMethod = $request->input('payment_method');

        $customer->createOrGetStripeCustomer();
        $customer->addPaymentMethod($paymentMethod);
        $plan = $request->input('plan');
        try {
            $customer->newSubscription('default', $plan)->create($paymentMethod, [
                'email' => $customer->email
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Error creating subscription. ' . $e->getMessage()]);
        }

        return redirect('dashboard');
    }
}
