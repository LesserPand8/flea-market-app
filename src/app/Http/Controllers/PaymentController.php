<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * 決済画面表示
     */
    public function index()
    {
        return view('payment.index');
    }

    /**
     * 決済処理実行
     */
    public function store(Request $request)
    {
        try {
            // APIキーをセットする
            Stripe::setApiKey(config('services.stripe.secret_key'));
            // 顧客を登録
            $customer = Customer::create(array(
                'email' => $request->stripeEmail,
                'source' => $request->stripeToken
            ));
            // 顧客に紐づく決済を登録
            $charge = Charge::create(array(
                'customer' => $customer->id,
                'amount' => 1000, // ここの金額はrequestで受け取るなりstore内で生成するなり
                'currency' => 'jpy'
            ));

            // DBに登録が必要な場合は$customerや$chargeの情報を使用する

            return redirect(route('payment.index', ['message' => '決済が完了しました！']));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect(route('payment.index', ['message' => '決済に失敗しました...']));
        }
    }
}
