@if(isset($checkoutUrl) && $checkoutUrl)
<script>
    window.onload = function() {
        window.location.href = "{{ $checkoutUrl }}";
    };
</script>
@elseif(isset($checkoutUrl) && !$checkoutUrl)
<p>商品情報が見つかりません。</p>
@endif