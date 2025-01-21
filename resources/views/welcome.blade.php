<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laundry System</title>
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Nunito', sans-serif;
                margin: 0;
                padding: 0;
                min-height: 100vh;
                background: linear-gradient(135deg, #E91E63 0%, #9C27B0 100%);
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .container {
                text-align: center;
                color: white;
                padding: 2rem;
            }

            .logo {
                font-size: 2.5rem;
                font-weight: bold;
                margin-bottom: 2rem;
            }

            .login-options {
                display: flex;
                gap: 2rem;
                justify-content: center;
            }

            .login-card {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                padding: 2rem;
                border-radius: 1rem;
                width: 250px;
                transition: transform 0.3s ease;
            }

            .login-card:hover {
                transform: translateY(-5px);
            }

            .login-title {
                font-size: 1.5rem;
                margin-bottom: 1rem;
                color: #00FFD1;
            }

            .login-description {
                margin-bottom: 1.5rem;
                color: rgba(255, 255, 255, 0.8);
            }

            .login-button {
                background: #00FFD1;
                color: #1E1E2D;
                padding: 0.8rem 1.5rem;
                border-radius: 0.5rem;
                text-decoration: none;
                font-weight: bold;
                transition: background 0.3s ease;
            }

            .login-button:hover {
                background: #00E6BE;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">
                LS
                <div style="font-size: 1rem; margin-top: 0.5rem;">Laundry System</div>
            </div>

            <div class="login-options">
                <div class="login-card " style="height: 10rem">
                    <div class="login-title">Customer Login</div>
                    <div class="login-description">
                        Access your account to manage your laundry orders and track their status.
                    </div>
                    <a href="{{ route('login') }}" class="login-button">Login as Customer</a>
                </div>

                <div class="login-card" style="height: 10rem">
                    <div class="login-title" >Service Provider</div>
                    <div class="login-description">
                        Manage your laundry business, accept orders, and track deliveries.
                    </div>
                    <a href="{{ route('provider.login') }}" class="login-button">Login as Provider</a>
                </div>
            </div>
        </div>
    </body>
</html>
