<?php

namespace Webkul\Shop\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use LVR\CreditCard\CardCvc;
use LVR\CreditCard\CardExpirationDate;
use LVR\CreditCard\CardNumber;
use Webkul\Core\Rules\PhoneNumber;
use Webkul\Customer\Rules\VatIdRule;

class PaymentMethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_name'     => ['nullable'],
            'card_holder_name' => ['required'],
            'card_number'      => ['required', new CardNumber, 'unique:payments,card_number'],
            // 'card_expiration'  => ['required', new CardExpirationDate('y/m')],
            'card_cvv'         => ['required', new CardCvc($this->get('card_number'))],
        ];
    }

    /**
     * Attributes.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'payment.*' => 'payment',
        ];
    }
}
