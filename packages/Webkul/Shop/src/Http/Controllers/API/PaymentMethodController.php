<?php

namespace Webkul\Shop\Http\Controllers\API;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Webkul\Customer\Repositories\CustomerPaymentMethodRepository;
use Webkul\Payment\Facades\Payment;
use Webkul\Shop\Http\Requests\Customer\PaymentMethodRequest;
use Webkul\Shop\Http\Resources\PaymentMethodResource;

class PaymentMethodController extends APIController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected CustomerPaymentMethodRepository $customerPaymentMethodRepository) {}

    /**
     * Customer payment-methods.
     */
    public function index(): JsonResource
    {
        $customer = auth()->guard('customer')->user();

        return PaymentMethodResource::collection($customer->customer_payment_methods);
    }

    /**
     * Create a new address for customer.
     */
    public function store(PaymentMethodRequest $request): JsonResource
    {
        $customer = auth()->guard('customer')->user();

        Event::dispatch('customer.payment-methods.create.before');

        $data = array_merge($request->only([
            'company_name',
            'card_holder_name',
            'card_no',
            'card_expiration',
            'card_cvc',
            'default_customer_payment_method',
        ]), [
            'customer_id' => $customer->id,
        ]);

        $customerPaymentMethod = $this->customerPaymentMethodRepository->create($data);

        Event::dispatch('customer.payment-methods.create.after', $customerPaymentMethod);

        return new JsonResource([
            'data'    => new PaymentMethodResource($customerPaymentMethod),
            'message' => trans('shop::app.customers.account.payment-methods.index.create-success'),
        ]);
    }

    /**
     * Update address for customer.
     */
    public function update(PaymentMethodRequest $request): JsonResource
    {
        $customer = auth()->guard('customer')->user();

        Event::dispatch('customer.payment-methods.update.before');

        $customerPaymentMethod = $this->customerPaymentMethodRepository->update(array_merge($request->only([
            'company_name',
            'card_holder_name',
            'card_no',
            'card_expiration',
            'card_cvc',
            'default_payment',
        ]), [
            'customer_id' => $customer->id,
        ]), request('id'));

        Event::dispatch('customer.payment-methods.update.after', $customerPaymentMethod);

        return new JsonResource([
            'data'    => new PaymentMethodResource($customerPaymentMethod),
            'message' => trans('shop::app.customers.account.payment-methods.index.update-success'),
        ]);
    }

    /**
     * Get supported payment methods.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSupportedPaymentMethods()
    {
        return response()->json(Payment::getPaymentMethods());
    }
}
