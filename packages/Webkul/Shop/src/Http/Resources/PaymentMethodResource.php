<?php

namespace Webkul\Shop\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'payment_type'      => $this->payment_type,
            'parent_payment_id' => $this->parent_payment_id,
            'customer_id'       => $this->customer_id,
            'cart_id'           => $this->cart_id,
            'order_id'          => $this->order_id,
            'card_holder_name'  => $this->card_holder_name,
            'card_no'           => $this->card_no,
            'card_expiration'   => $this->card_expiration,
            'card_cvc'          => $this->card_cvv,
            'company_name'      => $this->company_name,
            'default_payment'   => $this->default_payment,
        ];
    }
}
