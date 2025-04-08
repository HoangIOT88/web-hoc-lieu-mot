<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="refresh" content="2;url={{ route('courses.index') }}">

        <title>{{ config('app.name', 'Quản Lý Bài Giảng') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

        <!-- Styles -->
        <style>
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
                background-color: #f8fafc;
            }
            body {
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                font-family: 'Figtree', sans-serif;
            }
            .container {
                text-align: center;
                padding: 2rem;
            }
            h1 {
                font-size: 2.5rem;
                color: #1a202c;
                margin-bottom: 1rem;
            }
            p {
                font-size: 1.25rem;
                color: #4a5568;
                margin-bottom: 2rem;
            }
            .spinner {
                border: 4px solid rgba(0, 0, 0, 0.1);
                width: 36px;
                height: 36px;
                border-radius: 50%;
                border-left-color: #3490dc;
                animation: spin 1s linear infinite;
                margin: 0 auto;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>{{ __('Hệ thống quản lý bài giảng') }}</h1>
            <p>{{ __('Đang chuyển hướng đến trang chủ...') }}</p>
            <div class="spinner"></div>
        </div>

        <!-- Bootstrap JS Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>
