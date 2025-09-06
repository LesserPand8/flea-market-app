@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address-changing.css') }}">
@endsection

@section('content')
<div class="address-changing-content">
    <div class="address-changing__heading">
        <h2>住所の変更</h2>
    </div>
    <div class="address-changing__form">
        <form class="address-changing__form-container" action="/profile" method="post">
            @csrf
            <div class="form-group">
                <label for="postal_code">郵便番号</label>
                <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
                <div class="form__error">
                    @error('postal_code')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="address">住所</label>
                <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}">
                <div class="form__error">
                    @error('address')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="building_name">建物名</label>
                <input type="text" id="building_name" name="building_name" value="{{ old('building_name', $user->building_name) }}">
                <div class="form__error">
                    @error('building_name')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <button type="submit" class="btn">更新する</button>
        </form>
    </div>
</div>
@endsection