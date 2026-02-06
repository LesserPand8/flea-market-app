@extends('auth.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/trading-chat.css') }}">
@endsection

@section('content')
<div class="content">
    <aside class="sidebar">
        <div class="other-trade">
            その他の取引
        </div>
        <div class="other-trade-list">
            @foreach($otherTradeItems as $tradeItem)
            <a href="/trade/chat/{{ $tradeItem->id }}" class="trade-item">
                <div class="trade-item__name">{{ $tradeItem->name }}</div>
            </a>
            @endforeach
        </div>
    </aside>
    <div class="content-container">
        <div class="title">
            <div class="profile-box__image">
                <img class="img-content__profile" src="{{ asset($profile?->profile_image ?? 'storage/images/default-profile.png') }}" alt="プロフィール画像">
            </div>
            <div class="profile-info">
                <h2 class="profile-name"> 「{{ $otherUser->name }}」さんとの取引画面</h2>
            </div>
            @if($isPurchaser)
            <form class="trade-form" action="/trade/finish/{{ $item->id }}" method="POST">
                @csrf
                <button class="trade__button">取引を完了する</button>
            </form>
            @endif
        </div>
        <div class="item-info">
            <div class="item-image">
                <img src="{{ asset($item->image) }}" alt="商品画像" class="item-image__img" />
                <div class="item-info__text">
                    <div class="item-name">
                        {{$item->name}}
                    </div>
                    <div class="item-price">
                        ¥ {{$item->price}}
                    </div>
                </div>
            </div>
        </div>
        <div class="chat">

        </div>
        @if ($errors->any())
        <div class="chat-input__error">
            @foreach ($errors->all() as $error)
            <div class="error-message">{{ $error }}</div>
            @endforeach
        </div>
        @endif
        <form action="/trade/chat/message" method="POST" class="chat-input" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <input type="text" class="chat-input__text" name="message_text" placeholder="  取引メッセージを記入してください" value="{{ old('message_text') }}">
            <label for="chat-input__image" class="chat-input__image-label">画像を追加
                <input type="file" id="chat-input__image" class="chat-input__image" name="chat_image" accept=".png,.jpeg,.jpg">
            </label>
            <button type="submit" class="chat-input__button">
                <img src="{{ asset('storage/images/Submit Button.jpg') }}" alt="送信" class="chat-input__button-image">
            </button>
        </form>
    </div>
</div>
@endsection