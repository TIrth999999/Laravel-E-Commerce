<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $sessionId = Session::getId();
        $cartItems = Cart::where('session_id', $sessionId)->with('product.taxes')->get();
        
        $subtotal = 0; // original subtotal (before discount)
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
        
        return view('frontend.cart', compact('cartItems', 'subtotal', 'totalDiscount', 'totalTax', 'total', 'subtotalAfterDiscount'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $sessionId = Session::getId();
        $cartItem = Cart::where('session_id', $sessionId)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            Cart::create([
                'session_id' => $sessionId,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return back()->with('added_to_cart', true);
    }

    public function update(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:cart,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $sessionId = Session::getId();
        $cartItem = Cart::where('id', $request->cart_id)
            ->where('session_id', $sessionId)
            ->firstOrFail();

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return redirect()->route('cart.index')->with('success', 'Cart updated');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:cart,id',
        ]);

        $sessionId = Session::getId();
        Cart::where('id', $request->cart_id)
            ->where('session_id', $sessionId)
            ->delete();

        return redirect()->route('cart.index')->with('success', 'Item removed from cart');
    }
}
