@extends('auth.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/edit-message.css') }}">
@endsection

@section('content')
<div class="edit-message">
    <div class="edit-message__container">
        <h2 class="edit-message__title">メッセージを編集</h2>

        <div class="item-info">
            <div class="item-info__label">商品情報</div>
            <div class="item-info__content">
                <img src="{{ asset($item->image) }}" alt="商品画像" class="item-info__image">
                <div class="item-info__details">
                    <div class="item-name">{{ $item->name }}</div>
                    <div class="item-price">¥{{ $item->price }}</div>
                </div>
            </div>
        </div>

        @if ($errors->any())
        <div class="error-messages">
            @foreach ($errors->all() as $error)
            <div class="error-message">{{ $error }}</div>
            @endforeach
        </div>
        @endif

        <form action="/trade/chat/message/{{ $message->id }}/edit" method="POST" class="edit-message__form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">

            <div class="form-group">
                <label for="message_text" class="form-label">本文 <span class="required">*</span></label>
                <textarea
                    id="message_text"
                    name="message_text"
                    class="form-textarea"
                    rows="5"
                    placeholder="取引メッセージを記入してください">{{ old('message_text', $message->message) }}</textarea>
                <div class="form-hint">400文字以内</div>
            </div>

            @if($message->image)
            <div class="current-image">
                <div class="current-image__label">現在の画像</div>
                <img src="{{ asset($message->image) }}" alt="メッセージ画像" class="current-image__img">
            </div>
            @endif

            <div class="form-group">
                <label for="chat_image" class="form-label">画像</label>
                <label for="chat_image" class="form-label__image">画像を選択
                    <input
                        type="file"
                        id="chat_image"
                        name="chat_image"
                        class="form-file"
                        accept=".png,.jpeg,.jpg">
                </label>
                <div class="form-hint">.png または .jpeg 形式</div>
            </div>

            <div class="form-actions">
                <a href="/trade/chat/{{ $item->id }}" class="btn-cancel">キャンセル</a>
                <button type="submit" class="btn-submit">更新する</button>
            </div>
        </form>
    </div>
</div>
@endsection