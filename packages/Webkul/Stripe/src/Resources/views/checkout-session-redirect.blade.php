<?php

    $stripeCheckoutSession = app('Webkul\Stripe\Payment\CheckoutSession');

    $checkoutSessionUrl = $session->success_url;
?>

<body>

    <h1>Checkout Session Redirect Blade Page</h1>

    <button id="checkout-session" class="primary-button rounded-2xl px-11 py-3 max-md:rounded-lg max-sm:w-full max-sm:max-w-full max-sm:py-1.5">Checkout</button>


    <script type="text/javascript">
        console.log('Start -> Checkout session blade script is running ...');

        document.getElementById('checkout-session').addEventListener('click', () => {
            this.$axios.post("{{ route('stripe.checkout-session.success') }}", {
                    payment_method: this.paymentMethod
                })
                .then((response) => {
                    console.log(response);
                });
        });

        console.log('End -> Checkout session blade script is running ...');
    </script>
</body>
