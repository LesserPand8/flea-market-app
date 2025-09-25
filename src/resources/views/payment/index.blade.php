<h2>決済ボタン表示</h2>
<form action="{{ route('payment.store') }}" method="post">
    @csrf
    <script
        src="https://checkout.stripe.com/checkout.js"
        class="stripe-button"
        data-key="{{ config('services.stripe.public_key') }}"
        data-amount="1000"
        data-name="決済フォーム"
        data-label="決済する"
        data-description="単発決済: 1,000円"
        data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
        data-locale="auto"
        data-currency="JPY"></script>
</form>