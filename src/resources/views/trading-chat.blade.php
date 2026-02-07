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
            <div class="trade-form">
                <button type="button" class="trade__button" id="openEvaluationModal">取引を完了する</button>
            </div>
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
            @foreach($messages as $message)
            <div class="chat-container @if($message->user_id === $currentUser->id) chat-container--self @else chat-container--other @endif">
                <div class="chat-user @if($message->user_id === $currentUser->id) chat-user--self @else chat-user--other @endif"">
                    <div class=" chat-user__image-box">
                    <img class="chat-user__image" src="{{ asset($message->user->profile?->profile_image ?? 'storage/images/default-profile.png') }}" alt="プロフィール画像">
                </div>
                <div class="chat-user__name">{{ $message->user->name }}</div>
            </div>
            <div class="chat-messages">
                @if($message->image)
                <img src="{{ asset($message->image) }}" alt="メッセージ画像" class="chat-message__image">
                @endif
                <div class="chat-message">{{ $message->message }}</div>
                @if($message->user_id === $currentUser->id)
                <div class="chat-message__actions">
                    <a href="/trade/chat/message/{{ $message->id }}/edit" class="chat-message__edit">編集</a>
                    <form action="/trade/chat/message/{{ $message->id }}/delete" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="chat-message__delete" onclick="return confirm('削除してもよろしいですか？')">削除</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @endforeach
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
        <input type="text" class="chat-input__text" id="message_text" name="message_text" placeholder="  取引メッセージを記入してください" value="{{ old('message_text') }}">
        <label for="chat-input__image" class="chat-input__image-label">画像を追加
            <input type="file" id="chat-input__image" class="chat-input__image" name="chat_image" accept=".png,.jpeg,.jpg">
        </label>
        <button type="submit" class="chat-input__button">
            <img src="{{ asset('storage/images/Submit Button.jpg') }}" alt="送信" class="chat-input__button-image">
        </button>
    </form>
</div>
</div>

@if($isPurchaser)
<div class="evaluation-modal" id="evaluationModal">
    <div class="evaluation-modal__overlay" id="evaluationModalOverlay"></div>
    <div class="evaluation-modal__content">
        <div class="evaluation-modal__header">取引が完了しました。</div>
        <div class="evaluation-modal__subtitle">今回の取引相手はどうでしたか？</div>

        @if ($errors->has('evaluation_score'))
        <div class="evaluation-modal__error">{{ $errors->first('evaluation_score') }}</div>
        @endif

        <form id="evaluationForm" action="/trade/finish/{{ $item->id }}" method="POST" class="evaluation-modal__form">
            @csrf
            <div class="evaluation-stars" id="starRating">
                <input type="radio" name="evaluation_score" value="1" id="star1" class="star-input">
                <label for="star1" class="star-label">★</label>
                <input type="radio" name="evaluation_score" value="2" id="star2" class="star-input">
                <label for="star2" class="star-label">★</label>
                <input type="radio" name="evaluation_score" value="3" id="star3" class="star-input">
                <label for="star3" class="star-label">★</label>
                <input type="radio" name="evaluation_score" value="4" id="star4" class="star-input">
                <label for="star4" class="star-label">★</label>
                <input type="radio" name="evaluation_score" value="5" id="star5" class="star-input">
                <label for="star5" class="star-label">★</label>
            </div>

            <div class="evaluation-modal__actions">
                <button type="submit" class="evaluation-modal__submit">送信する</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    const storageKey = 'chat_message_{{ $item->id }}';
    const messageInput = document.getElementById('message_text');

    // ページ読み込み時にローカルストレージから値を復元
    function restoreMessage() {
        const savedMessage = localStorage.getItem(storageKey);
        if (savedMessage && !messageInput.value) {
            messageInput.value = savedMessage;
        }
    }

    // 入力値をローカルストレージに保存
    function saveMessage() {
        localStorage.setItem(storageKey, messageInput.value);
    }

    // フォーム送信時に保存済みメッセージを削除
    messageInput.closest('form').addEventListener('submit', function() {
        // 送信成功時にのみ削除されるようにするため、成功時の処理は後述
        setTimeout(function() {
            if (messageInput.value) {
                // 送信後の画面遷移時に削除
                localStorage.removeItem(storageKey);
            }
        }, 1000);
    });

    // ページ読み込み時に復元
    document.addEventListener('DOMContentLoaded', restoreMessage);

    // リアルタイム保存
    messageInput.addEventListener('input', saveMessage);

    // ページを離れる前に保存
    window.addEventListener('beforeunload', saveMessage);

    // 送信成功時にメッセージをクリア（successメッセージがある場合）
    if (document.querySelector('[class*="success"]')) {
        localStorage.removeItem(storageKey);
    }

    // 評価モーダル制御
    const evaluationModal = document.getElementById('evaluationModal');
    const openEvaluationModal = document.getElementById('openEvaluationModal');
    const closeEvaluationModal = document.getElementById('closeEvaluationModal');
    const evaluationModalOverlay = document.getElementById('evaluationModalOverlay');

    if (openEvaluationModal) {
        openEvaluationModal.addEventListener('click', function(e) {
            e.preventDefault();
            evaluationModal.classList.add('is-open');
        });
    }

    if (closeEvaluationModal) {
        closeEvaluationModal.addEventListener('click', function() {
            evaluationModal.classList.remove('is-open');
        });
    }

    if (evaluationModalOverlay) {
        evaluationModalOverlay.addEventListener('click', function() {
            evaluationModal.classList.remove('is-open');
        });
    }

    // 星評価の制御
    const starLabels = document.querySelectorAll('.star-label');
    const starRating = document.getElementById('starRating');
    let selectedValue = 0;

    function getInputFromLabel(label) {
        const targetId = label.getAttribute('for');
        return targetId ? document.getElementById(targetId) : null;
    }

    function applyStarColors(value) {
        starLabels.forEach((label) => {
            const input = getInputFromLabel(label);
            const inputValue = input ? parseInt(input.value) : 0;
            if (value > 0 && inputValue <= value) {
                label.classList.add('hovered');
            } else {
                label.classList.remove('hovered');
            }
        });
    }

    // 初期選択値
    const initiallyChecked = document.querySelector('.star-input:checked');
    selectedValue = initiallyChecked ? parseInt(initiallyChecked.value) : 0;
    applyStarColors(selectedValue);

    // クリックで確定
    starLabels.forEach((label) => {
        label.addEventListener('click', function() {
            const input = getInputFromLabel(this);
            if (!input) return;
            input.checked = true;
            selectedValue = parseInt(input.value);
            applyStarColors(selectedValue);
        });

        // ホバーで一時表示
        label.addEventListener('mouseenter', function() {
            const input = getInputFromLabel(this);
            if (!input) return;
            applyStarColors(parseInt(input.value));
        });
    });

    if (starRating) {
        starRating.addEventListener('mouseleave', function() {
            applyStarColors(selectedValue);
        });
    }
</script>
@endsection