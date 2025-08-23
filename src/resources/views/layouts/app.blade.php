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
                    <img src="{{ asset('img/logo.svg') }}" alt="coachtech">
                </a>
                <nav>
                    <ul class="header-nav">
                        @if (!Auth::check())
                        <li class="header-nav__item">
                            <!-- <form class="form" action="/login" method="get">
                                @csrf
                                <button class="header-nav__button">ログイン</button>
                            </form> -->
                            <a class="header-nav__button" href="/login">ログイン</a>
                        </li>
                        @endif
                        @if (Auth::check())
                        <li class="header-nav__item">
                            <form class="form" action="/logout" method="post">
                                @csrf
                                <button class="header-nav__button">ログアウト</button>
                            </form>
                        </li>
                        @endif
                        <li class="header-nav__item">
                            <a class="header-nav__link" href="/mypage">マイページ</a>
                        </li>
                        <li class="header-nav__sell-item">
                            <form class="form" action="/sell" method="get">
                                @csrf
                                <button class="sell__button">出品</button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>