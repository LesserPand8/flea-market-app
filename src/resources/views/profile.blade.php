@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="profile-box">
        <div class="profile-box__image">
            <img class="img-content__profile" src="{{ asset($profile?->profile_image ?? 'storage/images/default-profile.png') }}" alt="プロフィール画像">
        </div>
        <div class="profile-info">
            <div class="profile-name">{{ $user->name }}</div>
        </div>
        <div class="profile-edit">
            <a class="profile-setting" href="/mypage/profile">プロフィールを編集</a>
        </div>
    </div>
    <div class="tab-menu">
        <a class="tab-sale {{ $page === 'sell' ? 'active' : '' }}" href="/mypage?page=sell">出品した商品</a>
        <a class="tab-purchase {{ $page === 'buy' ? 'active' : '' }}" href="/mypage?page=buy">購入した商品</a>
        <a class="tab-purchase {{ $page === 'trade' ? 'active' : '' }}" href="/mypage?page=trade">
            取引中の商品
            @if (!empty($newMessageCount) && $newMessageCount > 0)
            <span class="tab-badge">{{ $newMessageCount }}</span>
            @endif
        </a>
    </div>
    <div class="item-contents">
        @foreach ($items as $item)
        <div class="item-card">
            @if ($page === 'trade')
            <a href="/trade/chat/{{ $item->id }}">
                @if (!empty($itemNewMessageCounts[$item->id]) && $itemNewMessageCounts[$item->id] > 0)
                <span class="item-badge">{{ $itemNewMessageCounts[$item->id] }}</span>
                @endif
                <img src="{{ asset($item->image) }}" alt="商品画像" class="img-content" />
                <div class="item-name">
                    {{$item->name}}
                </div>
            </a>
            @else
            <img src="{{ asset($item->image) }}" alt="商品画像" class="img-content" />
            <div class="item-name">
                {{$item->name}}
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection