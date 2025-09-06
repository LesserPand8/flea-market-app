@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sale.css') }}">
@endsection

<style>
    .sale-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 50px;
    }

    .sale__heading {
        font-family: Inter;
        font-weight: 700;
        font-size: 30px;
        line-height: 100%;
        text-align: center;
        margin-bottom: 30px;
    }

    .form-group__image {
        margin: 20px 0;
    }

    .form-group__label {
        font-family: Inter;
        font-weight: 700;
        font-size: 20px;
        margin-bottom: 10px;
        color: #000000;
    }

    .label {
        border-bottom: 1.5px solid #5F5F5F;
        color: #5F5F5F;
        font-family: Inter;
        font-weight: 700;
        font-size: 25px;
        margin: 20px 0;
    }

    .form-group__input-image {
        border: 1px solid #5F5F5F;
        border-width: 1px;
        border-radius: 4px;
        border-style: dashed;
        width: 100%;
    }

    .form-group__input-condition {
        width: 100%;
        height: 30px;
        border: 1px solid #5F5F5F;
        color: #5F5F5F;
    }

    .condition-label {
        width: 100%;
        height: 30px;
        border: 1px solid #5F5F5F;
        background: #636769;
        color: #EAEAEA;
    }
</style>

@section('content')
<div class="sale-content">
    <h2 class="sale__heading">
        商品の出品
    </h2>
    <form class="sale__form-container" action="/sale" method="post">
        @csrf
        <div class="form-group__image">
            <div class="form-group__label">商品画像</div>
            <input class="form-group__input-image" type="file" id="image" name="image">
            <div class="form__error">
                @error('image')
                {{ $message }}
                @enderror
            </div>
        </div>
        <div class="form-group__item-details">
            <div class="label">商品の詳細</div>
            <div class="form-group__label">カテゴリー</div>
            <div class="category-checkbox-group">
                @foreach ($categories as $category)
                <label class="category-checkbox-label">
                    <input type="checkbox" name="category[]" value="{{ $category->id }}">
                    <span class="category-checkbox-btn">{{ $category->category }}</span>
                </label>
                @endforeach
            </div>
            <div class="form__error">
                @error('category')
                {{ $message }}
                @enderror
            </div>
            <div class="form-group__label">商品の状態</div>
            <select class="form-group__input-condition" id="condition" name="condition">
                <option class="condition-label" value="良好">良好</option>
                <option class="condition-label" value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                <option class="condition-label" value="やや傷や汚れあり">やや傷や汚れあり</option>
                <option class="condition-label" value="状態が悪い">状態が悪い</option>
            </select>
            <div class="form__error">
                @error('condition')
                {{ $message }}
                @enderror
            </div>
        </div>
        <div class="form-group__item-description">
            <div class="label">商品名と説明</div>
            <div class="form-group__label">商品名</div>
            <input class="form-group__input-name" type="text" id="name" name="name" value="">
            <div class="form__error">
                @error('name')
                {{ $message }}
                @enderror
            </div>
            <div class="form-group__label">ブランド名</div>
            <input class="form-group__input-brand" type="text" id="brand" name="brand" value="">
            <div class="form__error">
                @error('brand')
                {{ $message }}
                @enderror
            </div>
            <div class="form-group__label">商品説明</div>
            <textarea class="form-group__input-description" id="description" name="description"></textarea>
            <div class="form__error">
                @error('description')
                {{ $message }}
                @enderror
            </div>
            <div class="form-group__label">販売価格</div>
            <input class="form-group__input-price" type="text" id="price" name="price" value="">
            <div class="form__error">
                @error('price')
                {{ $message }}
                @enderror
            </div>
        </div>
        <button type="submit" class="btn">出品する</button>
    </form>
</div>
@endsection