<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PERPUSTAKAAN DIGITAL KALINGANYAR</title>
    <!-- Google Fonts Link For Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/script.js') }}" defer></script>
</head>
<body>
@yield('content')
</body>
    <header>
            <nav class="navbar">
                <span class="hamburger-btn material-symbols-rounded">menu</span>
                <a href="#" class="logo">
                    <img src="images/logo.jpg" alt="logo">
                    <h2></h2>
                    <h3>PERPUSTAKAAN DIGITAL</h3>
                </a>
                <ul class="links">
                    <span class="close-btn material-symbols-rounded">close</span>
                    <li><a href="#">Beranda</a></li>
                    <li><a href="#">Jelajahi</a></li>
                    <li><a href="#">Kategori</a></li>
                    <li><a href="#">About us</a></li>
                    <li><a href="#">Contact us</a></li>
                </ul>
                <button class="login-btn">LOG IN</button>
            </nav>
        </header>
<script src="https://code.jquery.com/jquery-3.6.0.min.js">
    @yield('script')
</script>
</html>