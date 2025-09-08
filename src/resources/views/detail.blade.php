@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

<style>

</style>

@section('content')
<div class="content">
    <div class="image-container">
        <img class="item-image" src="{{ asset($item->image) }}" alt="{{ $item->name }}">
    </div>
    <div class="detail-container">
        <div class="item-name__box">
            <div class="item-name">
                {{$item->name}}
            </div>
            <div class="item-brand">
                {{$item->brand}}
            </div>
            <div class="item-price">
                ¥<span class="item-price__value">{{$item->price}}</span>（税込）
            </div>
            <div class="detail-icon">
                <div class="detail-icon__goods">
                    <form class="detail-icon__goods-form" action="/goods/{{ $item->id }}" method="POST">
                        @csrf
                        <button type="submit" class="detail-icon__goods-icon">
                            ☆
                        </button>
                    </form>
                    <p class="detail-icon__goods-count">{{ $item->goods->count() }}</p>
                </div>
                <div class="detail-icon__comments">
                    <span class="detail-icon__comments-icon">💬</span>
                    <p class="detail-icon__comments-count">0</p>
                </div>
            </div>
        </div>
        <form class="purchase-form" action="/purchase/{{ $item->id }}" method="get">
            @csrf
            <button class="purchase__button">購入手続きへ</button>
        </form>
        <div class="item-description__box">
            <div class="item-description__label">商品説明</div>
            <div class="item-description">{{ $item->description }}</div>
        </div>
        <div class="item-info__box">
            <div class="item-info__label">商品の情報</div>
            <div class="item-info__category"><span class="item-info__tab">カテゴリー</span>
                @foreach ($item->categories as $category)
                <span class="item-info__category-tab">{{ $category->category }}</span>
                @endforeach
            </div>
            <div class="item-info__condition"><span class="item-info__tab item-info__condition-tab">状態</span>{{$item->condition}}</div>
        </div>
        <form class="comments-form" action="/comment" method="post">
            @csrf
            <div class="comments__label">コメント<span class="comments__label-count">({{ $item->comments->count() }})</span></div>
            <div class="comments-box">
                @if ($item->comments && $item->comments->count())
                @foreach ($item->comments as $comment)
                <div class="comment-profile">
                    <div class="comment__user-image">
                        @if ($comment->user && $comment->user->profile && $comment->user->profile->image)
                        <img src="{{ asset($comment->user->profile->image) }}" alt="プロフィール画像" class="profile-image">
                        @else
                        <img src="{{ asset('storage/images/default-profile.png') }}" alt="デフォルト画像" class="profile-image">
                        @endif
                    </div>
                    <div class="comment__user">
                        {{ $comment->user ? $comment->user->name : '退会ユーザー' }}
                    </div>
                </div>
                <div class="comment__text">{{ $comment->comment }}</div>
                @endforeach
                @else
                <div class="no-comments">コメントはまだありません</div>
                @endif
            </div>
            <div class="comments-text__label">商品へのコメント</div>
            <textarea class="comments__input" name="comment" rows="4" placeholder="コメントを入力してください"></textarea>
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <button class="comments__button">コメントを送信する</button>
        </form>
    </div>
</div>
@endsection