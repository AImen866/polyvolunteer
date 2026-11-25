<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Афиша волонтёров') — Московский Политех</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="site">
    <div class="container">
        <a class="logo" href="{{ route('home') }}">Волонтёры <span>Политеха</span></a>
        <nav class="main">
            <a href="{{ route('events.index') }}" class="{{ request()->routeIs('events.*') ? 'active' : '' }}">Афиша</a>
            <a href="{{ route('articles.index') }}" class="{{ request()->routeIs('articles.*') ? 'active' : '' }}">Статьи</a>
            <a href="{{ route('calculator') }}" class="{{ request()->routeIs('calculator') ? 'active' : '' }}">Калькулятор</a>
            @auth
                @if(auth()->user()->hasRole('organizer', 'admin'))
                    <a href="{{ route('organizer.events') }}" class="{{ request()->routeIs('organizer.*') ? 'active' : '' }}">Мои мероприятия</a>
                @endif
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">Администрирование</a>
                @endif
            @endauth
        </nav>
        <div class="userbox">
            @auth
                <a href="{{ route('profile.show') }}">{{ auth()->user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn link">Выйти</button></form>
            @else
                <a href="{{ route('login') }}">Войти</a>
                <a class="btn small" href="{{ route('register') }}">Регистрация</a>
            @endauth
        </div>
    </div>
</header>

<main class="container">
    @if(session('status'))<div class="alert ok">{{ session('status') }}</div>@endif
    @if(session('error'))<div class="alert bad">{{ session('error') }}</div>@endif
</main>

@yield('content')

<footer class="site">
    <div class="container">Афиша волонтёров Московского политехнического университета. Учебный проект по дисциплине «Серверная веб-разработка», Laravel {{ app()->version() }}.</div>
</footer>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
