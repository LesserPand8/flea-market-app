@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="tab-menu">
        <a class="tab-recommend" href="#">おすすめ</a>
        <a class="tab-mylist" href="#">マイリスト</a>
    </div>
    <div class="item-contents">
        @foreach ($items as $item)
        <div class="item-card">
            <a href="/item/{{$item->id}}" class="item-link">
                <img src="{{ asset($item->image) }}" alt="商品画像" class="img-content" />
                <div class="item-name">{{$item->name}}</div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection