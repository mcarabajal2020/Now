<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: system-ui, sans-serif;
            background-image:
                linear-gradient(
                    135deg,
                    rgba(15, 23, 42, 0.85),
                    rgba(30, 41, 59, 0.70),
                    rgba(15, 23, 42, 0.90)
                ),
                url('{{ asset('images/background.jpg') }}');

            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            text-align: center;
            color: white;
            max-width: 700px;
            padding: 2rem;
        }

        .logo {
            width: 120px;
            margin-bottom: 1.5rem;
        }

        h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        p {
            font-size: 1.1rem;
            color: rgba(255,255,255,.85);
            margin-bottom: 2rem;
            line-height: 1.7;
        }

        .btn {
            display: inline-block;
            padding: 14px 32px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            background: white;
            color: #111827;
            transition: all .3s ease;
            box-shadow: 0 10px 25px rgba(0,0,0,.2);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(0,0,0,.3);
        }

        .card {
            backdrop-filter: blur(8px);
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 20px;
            padding: 3rem;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="card">

            {{-- Opcional: Logo --}}
            {{-- <img src="{{ asset('images/logo.png') }}" class="logo" alt="Logo"> --}}

            <h1>{{ config('app.name') }}</h1>

            <p>
                Sistema de gestión desarrollado con Laravel y Filament.
                Acceda al panel administrativo para gestionar la información
                de forma rápida y segura.
            </p>

            <a href="{{ url('/admin') }}" class="btn">
                Ingresar al Sistema
            </a>

        </div>
    </div>

</body>
</html>