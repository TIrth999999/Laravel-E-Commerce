<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Razorpay\Api\Api;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'customer_email' => 'required|email',
            'customer_phone' => 'nullable',
            'customer_address' => 'nullable',
        ]);

        $sessionId = Session::getId();
        $cartItems = Cart::where('session_id', $sessionId)->with('product.taxes')->get();

        if ($cartItems->isEmpty()) {
            return back()->withErrors(['error' => 'Cart is empty']);
        }

        $subtotal = 0;
        $totalDiscount = 0;
        $totalTax = 0;

        foreach ($cartItems as $item) {
            $product = $item->product;
            $subtotal += ($product->actual_price * $item->quantity);

            if ($product->discount_price) {
                $totalDiscount += ($product->actual_price - $product->discount_price) * $item->quantity;
            }

            $discountedPrice = $product->discount_price ?? $product->actual_price;
            foreach ($product->taxes as $tax) {
                if ($tax->type == 'flat') {
                    $totalTax += $tax->value * $item->quantity;
                } else {
                    $base = ($tax->apply_on ?? 'after_discount') === 'before_discount'
                        ? $product->actual_price
                        : $discountedPrice;
                    $totalTax += ($base * $tax->value / 100) * $item->quantity;
                }
            }
        }

        $subtotalAfterDiscount = $subtotal - $totalDiscount;
        $total = $subtotalAfterDiscount + $totalTax;

        $stripeSecret = config('services.stripe.secret_key');
        $stripeCurrency = config('services.stripe.currency', 'inr');
        if (!$stripeSecret) {
            return back()->withErrors(['error' => 'Stripe is not configured. Please set secret_key in config/services.php']);
        }

        $orderNumber = 'ORD' . time();
        try {
            $stripe = new StripeClient($stripeSecret);
            $session = $stripe->checkout->sessions->create([
                'mode' => 'payment',
                'success_url' => route('payment.stripe.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.stripe.cancel', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
                'customer_email' => $request->customer_email,
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $stripeCurrency,
                            'unit_amount' => (int) round($total * 100),
                            'product_data' => [
                                'name' => 'Order ' . $orderNumber,
                            ],
                        ],
                        'quantity' => 1,
                    ],
                ],
                'metadata' => [
                    'order_number' => $orderNumber,
                ],
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Stripe error: ' . $e->getMessage()]);
        }

        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'subtotal' => $subtotal,
            'discount' => $totalDiscount,
            'tax_amount' => $totalTax,
            'total' => $total,
            'payment_status' => 'pending',
            'payment_gateway' => 'stripe',
            'stripe_session_id' => $session->id,
        ]);

        foreach ($cartItems as $item) {
            $product = $item->product;
            $price = $product->discount_price ?? $product->actual_price;
            $discount = $product->discount_price ? ($product->actual_price - $product->discount_price) * $item->quantity : 0;
            
            $discountedPrice = $product->discount_price ?? $product->actual_price;
            $itemTax = 0;
            foreach ($product->taxes as $tax) {
                if ($tax->type == 'flat') {
                    $itemTax += $tax->value * $item->quantity;
                } else {
                    $base = ($tax->apply_on ?? 'after_discount') === 'before_discount'
                        ? $product->actual_price
                        : $discountedPrice;
                    $itemTax += ($base * $tax->value / 100) * $item->quantity;
                }
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item->quantity,
                'price' => $price,
                'discount' => $discount,
                'tax_amount' => $itemTax,
                'total' => ($price * $item->quantity) + $itemTax,
            ]);
        }
        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
        ]);

        $api = new Api(config('services.razorpay.key_id'), config('services.razorpay.key_secret'));

        try {
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ];

            $api->utility->verifyPaymentSignature($attributes);

            $order = Order::where('razorpay_order_id', $request->razorpay_order_id)->firstOrFail();
            $order->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'payment_status' => 'paid',
            ]);

            Cart::where('session_id', Session::getId())->delete();

            return view('frontend.payment-success', compact('order'));
        } catch (\Exception $e) {
            $order = Order::where('razorpay_order_id', $request->razorpay_order_id)->first();
            if ($order) {
                $order->update([
                    'payment_status' => 'failed',
                ]);
            }

            return redirect()->route('cart.index')->withErrors(['error' => 'Payment failed / could not be verified.']);
        }
    }

    public function stripeSuccess(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        $stripeSecret = config('services.stripe.secret_key');
        if (!$stripeSecret) {
            return redirect()->route('cart.index')->withErrors(['error' => 'Stripe is not configured.']);
        }

        try {
            $stripe = new StripeClient($stripeSecret);
            $session = $stripe->checkout->sessions->retrieve($request->session_id, []);

            $order = Order::where('stripe_session_id', $session->id)->first();
            if (!$order) {
                return redirect()->route('cart.index')->withErrors(['error' => 'Order not found for Stripe session.']);
            }

            if (($session->payment_status ?? null) !== 'paid') {
                $order->update(['payment_status' => 'failed']);
                return redirect()->route('cart.index')->withErrors(['error' => 'Stripe payment not completed.']);
            }

            $order->update([
                'payment_status' => 'paid',
                'stripe_payment_intent_id' => $session->payment_intent ?? null,
            ]);

            Cart::where('session_id', Session::getId())->delete();

            return view('frontend.payment-success', compact('order'));
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->withErrors(['error' => 'Stripe verification failed.']);
        }
    }

    public function stripeCancel(Request $request)
    {
        $sessionId = $request->query('session_id');
        if ($sessionId) {
            $order = Order::where('stripe_session_id', $sessionId)->first();
            if ($order && $order->payment_status === 'pending') {
                $order->update(['payment_status' => 'failed']);
            }
        }

        return redirect()->route('cart.index')->withErrors(['error' => 'Payment cancelled.']);
    }

}
