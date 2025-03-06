<?php

namespace Webkul\Stripe\Http\Controllers;

use Webkul\Checkout\Facades\Cart;
use Webkul\Stripe\Helpers\Ipn;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;
use Laravel\Cashier\Cashier;
use Webkul\Stripe\Payment\CheckoutSession;

class CheckoutSessionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected Ipn $ipnHelper,
        protected CheckoutSession $checkoutSession
    ) {}

    /**
     * Redirects to the stripe.
     *
     * @return \Illuminate\View\View
     */
    public function redirect()
    {
        $cart = Cart::getCart();

        $data = (new OrderResource($cart))->jsonSerialize();

        $order = $this->orderRepository->create($data);

        $session = $this->checkoutSession->createFromCart($cart, $order);

        session()->flash('order_id', $order->id);

        return view('stripe::checkout-session-redirect', [
            'session' => $session
        ]);
    }


    /**
     * Create order for payment.
     *
     * @return \Illuminate\Http\Response
     */
    public function createOrder() {
        $cart = Cart::getCart();

        $data = (new OrderResource($cart))->jsonSerialize();

        $order = $this->orderRepository->create($data);

        $createCheckoutSession = $this->checkoutSession->createFromCart($cart, $order);

        session()->flash('order_id', $order->id);

        return redirect($createCheckoutSession['success_url']);
    }

    /**
     * Cancel payment from stripe.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel()
    {
        session()->flash('error', trans('shop::app.checkout.cart.stripe-payment-cancelled'));

        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Success payment.
     *
     * @return \Illuminate\Http\Response
     */
    public function success()
    {
        $sessionId = request()->get('session_id');

        if ($sessionId === null) {
            return;
        }

        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            return;
        }

        Cart::deActivateCart();

        $order = $this->orderRepository->find(session('order_id'));

        if ($order->status === 'pending') {
            $order = $this->orderRepository->updateOrderStatus($order, 'completed');
        }

        return redirect()->route('shop.checkout.onepage.success');
    }

    /**
     * Handling the event of stripe for client.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function webhook()
    {
        // This is your Stripe CLI webhook secret for testing your endpoint locally.
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json($e, 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json($e, 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;

                $order = $this->orderRepository->find(session('order_id'));

                if ($order && $order->status === 'pending') {
                    $this->orderRepository->updateOrderStatus($order, 'completed');
                }

            default:
                echo 'Received unknown event type ' . $event->type;
        }

        return response()->json('', 200);
    }

    /**
     * Paypal IPN listener.
     *
     * @return \Illuminate\Http\Response
     */
    public function ipn()
    {
        $this->ipnHelper->processIpn(request()->all());
    }
}
