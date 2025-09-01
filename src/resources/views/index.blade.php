@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

<style>
    .tab-menu {
        display: flex;
        border-bottom: 1px solid #ccc;
        margin-bottom: 40px;
        margin-top: 40px;
    }

    .tab-recommend {
        padding: 8px 24px;
        color: #5f5f5f;
        text-decoration: none;
        font-family: Inter;
        font-weight: 700;
        font-style: Bold;
        font-size: 20px;
        line-height: 100%;
        letter-spacing: 0%;
        text-align: right;
        vertical-align: middle;
        margin-left: 100px;
    }

    .tab-mylist {
        padding: 8px 24px;
        color: #5f5f5f;
        text-decoration: none;
        font-family: Inter;
        font-weight: 700;
        font-style: Bold;
        font-size: 20px;
        line-height: 100%;
        letter-spacing: 0%;
        text-align: right;
        vertical-align: middle;
        margin-left: 30px;
    }

    .item-contents {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 32px 16px;
        margin: 10px 40px 80px;
    }

    .item-card {
        box-shadow: 0px 4px 4px 0px #00000040;
        margin: 10px 20px;
    }

    .item-link {
        text-decoration: none;
        color: #000000;
    }

    .img-content {
        width: 100%;
        object-fit: cover;
        background: #ccc;
        margin-bottom: 8px;
    }

    .item-name {
        margin-top: 4px;
        font-family: Inter;
        font-weight: 400;
        font-style: Regular;
        font-size: 20px;
        line-height: 100%;
        letter-spacing: 0%;
        vertical-align: middle;
        margin-bottom: 8px;
        margin-left: 10px;
    }
</style>

@section('content')
<div class="content">
    <div class="tab-menu">
        <a class="tab-recommend" href="/">おすすめ</a>
        <a class="tab-mylist" href="/mylist">マイリスト</a>
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