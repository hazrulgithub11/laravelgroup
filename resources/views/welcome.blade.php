<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Home Services</title>
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Nunito', sans-serif;
                margin: 0;
                padding: 70px 0 0 0;
                min-height: 100vh;
                background: #ffffff;
            }

            /* Navigation Styles */
            .nav-container {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                background: white;
                padding: 1rem 2rem;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                display: flex;
                justify-content: space-between;
                align-items: center;
                z-index: 1000;
            }

            .nav-logo {
                font-size: 1.5rem;
                font-weight: bold;
                margin-bottom: 3rem;
                color: #1E856D;
                text-decoration: none;
            }

            .nav-links {
                display: flex;
                gap: 1rem;
                align-items: center;
            }

            .nav-button {
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .nav-button.outline {
                border: 2px solid #1E856D;
                color: #1E856D;
                font-weight: bold;
            }

            .nav-button.solid {
                background: #1E856D;
                color: white;
                border: 2px solid #1E856D;
            }

            /* Hero Section */
            .hero {
                text-align: center;
                padding: 4rem 2rem;
                background: #f9f9f9;
            }

            .hero-title {
                font-size: 3rem;
                color: #1E856D;
                margin-bottom: 1rem;
            }

            .hero-subtitle {
                font-size: 1.2rem;
                color: #2E6B5E;
                margin-bottom: 2rem;
            }

            /* Search Bar */
            .search-container {
                max-width: 600px;
                margin: 0 auto 3rem auto;
                position: relative;
            }

            .search-input {
                width: 100%;
                padding: 1rem;
                border: 2px solid #1E856D;
                border-radius: 0.5rem;
                font-size: 1rem;
                outline: none;
            }

            .search-button {
                position: absolute;
                right: 0;
                top: 0;
                bottom: 0;
                padding: 0 1.5rem;
                background: #1E856D;
                border: none;
                border-radius: 0 0.5rem 0.5rem 0;
                color: white;
                cursor: pointer;
            }

            /* Services Icons Section */
            .services-icons {
                display: flex;
                justify-content: center;
                gap: 3rem;
                margin: 2rem 0;
                padding: 1rem;
            }

            .service-icon-wrapper {
                display: flex;
                flex-direction: column;
                align-items: center;
                cursor: pointer;
                padding: 1rem;
                border-radius: 0.5rem;
                transition: all 0.3s ease;
            }

            .service-icon-wrapper:hover,
            .service-icon-wrapper.active {
                background: rgba(30, 133, 109, 0.1);
            }

            .service-icon-wrapper.active {
                border-bottom: 3px solid #1E856D;
            }

            .icon-circle {
                width: 60px;
                height: 60px;
                background: white;
                border: 2px solid #1E856D;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2rem;
                margin-bottom: 0.5rem;
            }

            .icon-label {
                color: #1E856D;
                font-weight: 600;
            }

            /* Service Detail Section */
            .service-detail {
                position: relative;
                min-height: 400px;
                background-size: cover;
                background-position: center;
                border-radius: 1rem;
                margin: 2rem auto;
                max-width: 1000px;
                overflow: hidden;
            }

            .service-detail-content {
                position: relative;
                background: rgba(255, 255, 255, 0.95);
                padding: 2rem;
                margin: 2rem;
                border-radius: 1rem;
                max-width: 500px;
            }

            .service-detail-title {
                font-size: 2rem;
                color: #1E856D;
                margin-bottom: 1rem;
            }

            .service-detail-description {
                color: #2E6B5E;
                line-height: 1.6;
                margin-bottom: 1.5rem;
            }

            .service-detail-button {
                background: #1E856D;
                color: white;
                padding: 0.8rem 1.5rem;
                border-radius: 0.5rem;
                text-decoration: none;
                display: inline-block;
                transition: background 0.3s ease;
            }

            .service-detail-button:hover {
                background: #166F5A;
            }

            .hidden {
                display: none;
            }

            @media (max-width: 768px) {
                .services-icons {
                    flex-wrap: wrap;
                }

                .hero-title {
                    font-size: 2rem;
                }

                .nav-container {
                    padding: 1rem;
                }

                .nav-links {
                    gap: 0.5rem;
                }

                .nav-button {
                    padding: 0.4rem 0.8rem;
                    font-size: 0.9rem;
                }
            }
        </style>
    </head>
    <body>
        <!-- Navigation -->
        <nav class="nav-container">
            <a href="/" class="nav-logo">HomeServices</a>
            <div class="nav-links">
                <a href="{{ route('login') }}" class="nav-button outline">Log in</a>
                <a href="{{ route('register') }}" class="nav-button outline">Sign up</a>
                <a href="{{ route('provider.register') }}" class="nav-button solid">Become a Tasker</a>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero">
            <h1 class="hero-title">Book trusted help</h1>
            <p class="hero-subtitle">for home services</p>
            
            <!-- Service Icons -->
            <div class="services-icons">
                <div class="service-icon-wrapper" onclick="showService('laundry')">
                    <div class="icon-circle">🧺</div>
                    <span class="icon-label">Laundry</span>
                </div>
                <div class="service-icon-wrapper" onclick="showService('gardening')">
                    <div class="icon-circle">🌿</div>
                    <span class="icon-label">Gardening</span>
                </div>
                <div class="service-icon-wrapper" onclick="showService('cleaning')">
                    <div class="icon-circle">🧹</div>
                    <span class="icon-label">Cleaning</span>
                </div>
            </div>

            <!-- Service Details -->
            <div id="laundry-detail" class="service-detail hidden" style="background-image: url('/images/washing.jpg')">
                <div class="service-detail-content">
                    <h2 class="service-detail-title">Laundry Service</h2>
                    <p class="service-detail-description">
                        Let professionals handle your laundry with care! Our taskers provide washing, drying, ironing, and folding services to keep your clothes fresh and clean.
                    </p>
                   
                </div>
            </div>

            <div id="gardening-detail" class="service-detail hidden" style="background-image: url('/images/gardening.jpg')">
                <div class="service-detail-content">
                    <h2 class="service-detail-title">Gardener Service</h2>
                    <p class="service-detail-description">
                        Keep your garden beautiful and well-maintained with expert gardening services. From lawn mowing to plant care, our taskers ensure a green and healthy outdoor space.
                    </p>
                    
                </div>
            </div>

            <div id="cleaning-detail" class="service-detail hidden" style="background-image: url('/images/cleaning.jpg')">
                <div class="service-detail-content">
                    <h2 class="service-detail-title">Cleaning Service</h2>
                    <p class="service-detail-description">
                        Enjoy a spotless home or workspace with our reliable cleaning services. Whether it's deep cleaning, dusting, or organizing, our taskers leave your space fresh and tidy.
                    </p>
                    
                </div>
            </div>
        </section>

        <script>
            function showService(service) {
                // Hide all service details
                document.querySelectorAll('.service-detail').forEach(el => {
                    el.classList.add('hidden');
                });
                
                // Remove active class from all icons
                document.querySelectorAll('.service-icon-wrapper').forEach(el => {
                    el.classList.remove('active');
                });
                
                // Show selected service detail
                document.getElementById(service + '-detail').classList.remove('hidden');
                
                // Add active class to clicked icon
                event.currentTarget.classList.add('active');
            }
        </script>
    </body>
</html>
