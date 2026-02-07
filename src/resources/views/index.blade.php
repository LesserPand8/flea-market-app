@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="tab-menu">
        <a class="tab-recommend {{ ($tab ?? 'recommend') === 'recommend' ? 'active' : '' }}" href="/?keyword={{ urlencode($keyword ?? request('keyword', '')) }}">おすすめ</a>
        <a class="tab-mylist {{ ($tab ?? '') === 'mylist' ? 'active' : '' }}" href="/?tab=mylist&keyword={{ urlencode($keyword ?? request('keyword', '')) }}">マイリスト</a>
    </div>
    <div class="item-contents">
        @foreach ($items as $item)
        <div class="item-card">
            @if(($item->purchases && $item->purchases->count() > 0) || ($item->trades && $item->trades->count() > 0))
            <div class="item-link disabled">
                <div class="sold-label">Sold</div>
                <img src="{{ asset($item->image) }}" alt="商品画像" class="img-content" />
                <div class="item-name">
                    {{$item->name}}
                </div>
            </div>
            @else
            <a href="/item/{{$item->id}}" class="item-link">
                <img src="{{ asset($item->image) }}" alt="商品画像" class="img-content" />
                <div class="item-name">
                    {{$item->name}}
                </div>
            </a>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection