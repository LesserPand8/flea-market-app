@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile-setting.css') }}">
@endsection

@section('content')
<div class="profile-setting-content">
    <div class="profile-setting__heading">
        <h2>プロフィール設定</h2>
    </div>
    <div class="profile-setting__form">
        <form class="profile-setting__form-container" action="/profile" method="post" enctype="multipart/form-data">
            @csrf
            <div class="profile-image">
                <output id="list" class="image_output">
                    @if ($profile?->profile_image)
                    <img src="{{ asset($profile->profile_image) }}" alt="プロフィール画像">
                    @else
                    <img src="{{ asset('storage/images/default-profile.png') }}" alt="デフォルト画像">
                    @endif
                </output>
                <label for="profile_image" class="custom-file-label">画像を選択する
                    <input type="file" id="profile_image" class="image" name="profile_image">
                </label>
            </div>

            <div class="form-group">
                <label class="form__label" for="name">ユーザー名</label>
                <input class="form__input" type="text" id="name" name="name" value="{{ old('name', $user->name ?? '') }}">
                <div class="form__error">
                    @error('name')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form__label" for="postal_code">郵便番号</label>
                <input class="form__input" type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $profile->postal_code ?? '') }}">
                <div class="form__error">
                    @error('postal_code')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form__label" for="address">住所</label>
                <input class="form__input" type="text" id="address" name="address" value="{{ old('address', $profile->address ?? '') }}">
                <div class="form__error">
                    @error('address')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form__label" for="building_name">建物名</label>
                <input class="form__input" type="text" id="building_name" name="building_name" value="{{ old('building_name', $profile->building_name ?? '') }}">
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