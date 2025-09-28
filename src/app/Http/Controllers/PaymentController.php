<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;

class PaymentController extends Controller
{
    /**
     * 決済画面表示
     */
    public function index(Request $request)
    {
        $item = null;
        if (session('purchase_item_id')) {
            $item = \App\Models\Item::find(session('purchase_item_id'));
        }
        if (!$item) {
            return view('payment.index', ['checkoutUrl' => null]);
        }

        // Stripe Checkoutセッション作成
        \Stripe\Stripe::setApiKey(config('services.stripe.secret_key'));
        $user_id = session('purchase_user_id');
        $full_address = session('purchase_full_address');
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/'),
            'cancel_url' => url('/payment?cancel=1'),
            'metadata' => [
                'item_id' => $item->id,
                'user_id' => $user_id,
                'full_address' => $full_address,
            ],
        ]);
        return view('payment.index', ['checkoutUrl' => $session->url]);
    }
}
