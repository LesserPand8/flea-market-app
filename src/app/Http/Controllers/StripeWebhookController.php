<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Purchase;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            // メタデータからitem_id, user_id, full_addressを取得
            $item_id = $session->metadata->item_id ?? null;
            $user_id = $session->metadata->user_id ?? null;
            $full_address = $session->metadata->full_address ?? null;
            if ($item_id && $user_id && $full_address) {
                // すでに購入済みでないかチェック
                $exists = Purchase::where('item_id', $item_id)->where('user_id', $user_id)->exists();
                if (!$exists) {
                    Purchase::create([
                        'item_id' => $item_id,
                        'user_id' => $user_id,
                        'method' => 'カード払い',
                        'full_address' => $full_address,
                    ]);
                }
            } else {
                Log::warning('Stripe webhook: metadata missing', ['session' => $session]);
            }
        }
        return response('Webhook handled', 200);
    }
}
