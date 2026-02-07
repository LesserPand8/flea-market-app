@component('mail::message')
# 取引が完了しました

{{ $seller->name }}様

いつもご利用いただきありがとうございます。

購入者の {{ $purchaser->name }}様が取引を完了し、評価を送信いたしました。
取引チャット画面にて購入者の {{ $purchaser->name }}様の評価をお願い致します。

## 取引内容

**商品名：** {{ $item->name }}

**商品価格：** ¥{{ $item->price }}

---

この度はご利用いただき、ありがとうございました。

@endcomponent