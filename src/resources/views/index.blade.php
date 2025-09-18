@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="tab-menu">
        <a class="tab-recommend {{ ($tab ?? 'recommend') === 'recommend' ? 'active' : '' }}" href="/">おすすめ</a>
        <a class="tab-mylist {{ ($tab ?? '') === 'mylist' ? 'active' : '' }}" href="/?tab=mylist">マイリスト</a>
    </div>
    <div class="item-contents">
        @foreach ($items as $item)
        <div class="item-card">
            <a href="/item/{{$item->id}}" class="item-link">
                <img src="{{ asset($item->image) }}" alt="商品画像" class="img-content" />
                <div class="item-name">
                    {{$item->name}}
                    @if($item->purchases && $item->purchases->count() > 0)
                    <span class="sold-label">Sold</span>
                    @endif
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection