@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="content-list">
        <div class="recommendation">

        </div>
        <div class="mylist">

        </div>
    </div>
    <div class="item-contents">
        @foreach ($items as $item)
        <div class="item-content">
            <a href="/item/{{$item->id}}" class="item-link"></a>
            <img src="{{ asset($item->image) }}" alt="商品画像" class="img-content" />
            <div class="detail-content">
                <p>{{$item->name}}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection