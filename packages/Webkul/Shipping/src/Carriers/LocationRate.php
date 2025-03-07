<?php

namespace Webkul\Shipping\Carriers;

use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Models\CartShippingRate;
use Illuminate\Support\Str;

class LocationRate extends AbstractShipping
{
    /**
     * Shipping method carrier code.
     *
     * @var string
     */
    protected $code = 'locationrate';

    /**
     * Shipping method code.
     *
     * @var string
     */
    protected $method = 'locationrate_locationrate';

     /**
     * Shipping type.
     *
     * @var string
     */
    protected $type = 'D2D (Door to Door)';

    /**
     * Calculate rate for myanmarsaterate.
     *
     * @return \Webkul\Checkout\Models\CartShippingRate|false
     */
    public function calculate()
    {
        if (! $this->isAvailable()) {
            return false;
        }

        if (! $this->isCountryAvailable()) {
            return false;
        }

        return $this->getRate();
    }

    /**
     * Checks if shipping method is available.
     *
     * @return array
     */
    public function isCountryAvailable()
    {
        $cart = Cart::getCart();

        $country = $cart->shipping_address->country;
        $city = $cart->shipping_address->city;

        $isMyanmar = Str::contains($country, ['mm', 'MM', 'Myanmar (Burma)', 'Myanmar']);
        $isYangonOrMandalay = Str::contains($city, ['Yangon', 'Mandalay']);

        if ($isMyanmar) {
            if ($isYangonOrMandalay) {
                $this->type = 'D2D (Door to Door)';
                return true;
            } else {
                $this->type = 'D2B (Delivery to Bus Terminal)';
                return true;
            }
        } else {
            $this->type =  $this->getConfigData('type');
            return false;
        }
    }

     /**
     * Get rate.
     */
    public function getRate(): CartShippingRate
    {
        $cart = Cart::getCart();

        $cartShippingRate = new CartShippingRate;

        $cartShippingRate->carrier = $this->getCode();
        $cartShippingRate->carrier_title = $this->getConfigData('title');
        $cartShippingRate->method = $this->getMethod();
        $cartShippingRate->method_title = $this->getConfigData('title');
        $cartShippingRate->method_description = $this->getConfigData('description');
        $cartShippingRate->price = 0;
        $cartShippingRate->base_price = $this->type == 'D2D (Door to Door)' ? core()->convertToBasePrice(3000) :  core()->convertToBasePrice(2500);

        if ($this->type == 'D2D (Door to Door)') {
            foreach ($cart->items as $item) {
                if ($item->getTypeInstance()->isStockable()) {
                    $cartShippingRate->price += core()->convertPrice($cartShippingRate->base_price);
                    $cartShippingRate->base_price = $cartShippingRate->base_price;
                }
            }
        } elseif($this->type == 'D2B (Delivery to Bus Terminal)') {
            foreach ($cart->items as $item) {
                if ($item->getTypeInstance()->isStockable()) {
                    $cartShippingRate->price += core()->convertPrice($cartShippingRate->base_price);
                    $cartShippingRate->base_price = $cartShippingRate->base_price;
                }
            }
        } else {
            $cartShippingRate->price = core()->convertPrice($this->getConfigData('default_rate'));
            $cartShippingRate->base_price = $this->getConfigData('default_rate');
        }

        return $cartShippingRate;
    }


    /**
     * Get rate.
     */
    // public function getRate(): CartShippingRate
    // {
    //     $cart = Cart::getCart();

    //     $cartShippingRate = new CartShippingRate;

    //     $cartShippingRate->carrier = $this->getCode();
    //     $cartShippingRate->carrier_title = $this->getConfigData('title');
    //     $cartShippingRate->method = $this->getMethod();
    //     $cartShippingRate->method_title = $this->getConfigData('title');
    //     $cartShippingRate->method_description = $this->getConfigData('description');
    //     $cartShippingRate->price = $this->setPriceByCountry($cart->shipping_address->country, $cart->shipping_address->city);
    //     $cartShippingRate->base_price = $this->getConfigData('default_rate');

    //     return $cartShippingRate;
    // }

    private function setPriceByCountry(string $country, string $city): int|array  {
        $isMyanmar = Str::contains($country, ['mm', 'MM', 'Myanmar (Burma)', 'Myanmar']);
        $isYangonOrMandalay = Str::contains($city, ['Yangon', 'Mandalay']);

        if ($isMyanmar) {
            if ($isYangonOrMandalay) {
                $price = core()->convertPrice(0.8);
                $basePrice = $this->getConfigData('default_rate');
                dd(['price' => $price, 'base_price' => $basePrice]);
            } else {
                return 0.6;
            }
        } else {
            return 0;
        }
    }
}
