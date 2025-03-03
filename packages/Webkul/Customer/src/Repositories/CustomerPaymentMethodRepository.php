<?php

namespace Webkul\Customer\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Customer\Contracts\CustomerPaymentMethod;

class CustomerPaymentMethodRepository extends Repository
{
    /**
     * Specify Model class name.
     */
    public function model(): string
    {
        return CustomerPaymentMethod::class;
    }

    /**
     * Create a new customer payment.
     */
    public function create(array $data): CustomerPaymentMethod
    {
        $defaultPaymentMethod = $this->findOneWhere(['customer_id' => $data['customer_id'], 'default_customer_payment_method' => 1]);

        if ($defaultPaymentMethod) {
            $defaultPaymentMethod->update(['default_customer_payment_method' => 0]);
        }

       $paymentMethod = $this->model->create($data);

        return $paymentMethod;
    }

    /**
     * Update customer payment.
     *
     * @param  int  $id
     */
    public function update(array $data, $id): CustomerPaymentMethod
    {
       $paymentMethod = $this->find($id);

        $defaultPaymentMethod = $this->findOneWhere(['customer_id' => $paymentMethod->customer_id, 'default_customer_payment_method' => 1]);

        if (
            $defaultPaymentMethod
            && $defaultPaymentMethod->id !=$paymentMethod->id
        ) {
            $defaultPaymentMethod->update(['default_customer_payment_method' => 0]);
        }

       $paymentMethod->update($data);

        return $paymentMethod;
    }
}
