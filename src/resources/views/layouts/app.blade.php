<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <a class="header__logo" href="/">
                    <img src="{{ asset('storage/images/logo.svg') }}" alt="coachtech">
                </a>
                <form class="search" action="/search" method="GET">
                    <input class="search__input" type="text" name="keyword" placeholder="なにをお探しですか？">
                </form>
                <div class="header-nav">
                    @if (!Auth::check())
                    <div class="header-nav__item">
                        <!-- <form class="form" action="/login" method="get">
                                @csrf
                                <button class="header-nav__button">ログイン</button>
                            </form> -->
                        <a class="header-nav__button" href="/login">ログイン</a>
                    </div>
                    @endif
                    @if (Auth::check())
                    <div class="header-nav__item">
                        <form class="form" action="/logout" method="post">
                            @csrf
                            <button class="header-nav__button">ログアウト</button>
                        </form>
                    </div>
                    @endif
                    <div class="header-nav__item">
                        <a class="header-nav__link" href="/mypage">マイページ</a>
                    </div>
                    <div class="header-nav__sell-item">
                        <form class="form" action="/sell" method="get">
                            @csrf
                            <button class="sell__button">出品</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>