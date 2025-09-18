@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="content">
    <form class="purchase-form" action="/purchase/{{ $item->id }}" method="post">
        @csrf
        <div class="purchase-container">
            <div class="item-box">
                <div class="item-image">
                    <img class="item-image__main" src="{{ asset($item->image) }}" alt="{{ $item->name }}">
                </div>
                <div class="item-detail">
                    <div class="item-name">
                        {{$item->name}}
                    </div>
                    <div class="item-value">
                        ¥ <span class="item-price__value">{{$item->price}}</span>
                    </div>
                </div>
            </div>
            <div class="method-box">
                <div class="method-label">
                    支払い方法
                </div>
                <div class="method">
                    <select class="method__input" id="method" name="method" required>
                        <option value="" hidden disabled selected>選択してください</option>
                        <option class="method__input-label" value="コンビニ払い" {{ old('method') == 'コンビニ払い' ? 'selected' : '' }}>コンビニ払い</option>
                        <option class="method__input-label" value="カード払い" {{ old('method') == 'カード払い' ? 'selected' : '' }}>カード払い</option>
                    </select>
                </div>
                <div class="form__error">
                    @error('method')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="address-box">
                <div class="address-container">
                    <div class="address-label">
                        配送先
                    </div>
                    <a href="/purchase/address/{{ $item->id }}" class="change-address">
                        変更する
                    </a>
                </div>
                <div class="address-detail">
                    <div class="postal_code">
                        〒 {{ $profile->postal_code }}
                    </div>
                    <div class="address">
                        {{ $profile->address . $profile->building_name}}
                    </div>
                    <input type="hidden" name="full_address" value="{{ $profile->postal_code . ' ' . $profile->address . $profile->building_name }}">
                </div>
            </div>
        </div>
        <div class="confirmation-container">
            <table class="confirmation-table">
                <tr class="confirmation-table__row">
                    <th class="confirmation__label">商品代金</th>
                    <td class="confirmation__value">¥<span class="item-price__value">{{$item->price}}</span></td>
                </tr>
                <tr class="confirmation-table__row">
                    <th class="confirmation__label">支払方法</th>
                    <td class="confirmation__value" id="selected-method"></td>
                </tr>
            </table>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const methodSelect = document.getElementById('method');
                    const methodDisplay = document.getElementById('selected-method');
                    methodSelect.addEventListener('change', function() {
                        const selected = methodSelect.options[methodSelect.selectedIndex].text;
                        methodDisplay.textContent = selected;
                    });
                });
            </script>
            <button class="purchase__button">購入する</button>
        </div>
    </form>
</div>
@endsection