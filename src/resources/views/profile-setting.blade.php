@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="profile-setting-content">
    <div class="profile-setting__heading">
        <h2>プロフィール設定</h2>
    </div>
    <div class="profile-image">
        <output id="list" class="image_output"></output>
        <input type="file" id="profile_image" class="image" name="profile_image">
        <button class="profile-image__button">画像を変更</button>
    </div>
    <div class="profile-setting__form">
        <form class="profile-setting__form-container" action="/profile" method="post">
            @csrf
            <div class="form-group">
                <label for="name">ユーザー名</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
                <div class="form__error">
                    @error('name')
                    {{ $message }}
                    @enderror
                </div>
            </div>
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