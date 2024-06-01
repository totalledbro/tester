<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="icon" href="{{ asset('img/logodesa.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/5.0.1/css/ionicons.min.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap");
    </style>
</head>
<body>
    <div class="container">
        <div class="navigation">
            <ul>
                <li><a href="#"><span class="icon"><ion-icon name="desktop-outline"></ion-icon></span><span class="titletop">Admin Area</span></a></li>
                <li><a href="#"><span class="icon"><ion-icon name="home-outline"></ion-icon></span><span class="title">Dashboard</span></a></li>
                <li><a href="{{ url('/buku') }}"><span class="icon"><ion-icon name="book-outline"></ion-icon></span><span class="title">Buku</span></a></li>
                <li><a href="{{ url('/datakategori') }}"><span class="icon"><ion-icon name="bookmarks-outline"></ion-icon></span><span class="title">Kategori</span></a></li>
                <li><a href="{{ url('/datapinjam') }}"><span class="icon"><ion-icon name="time-outline"></ion-icon></span><span class="title">Data Pinjam</span></a></li>
                <li><a href="#"><span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span><span class="title">Password</span></a></li>
                <li><a href="{{ route('actionlogout') }}"><span class="icon"><ion-icon name="log-out-outline"></ion-icon></span><span class="title">Sign Out</span></a></li>
            </ul>
        </div>
        <div class="main">
            <div class="topbar">
                <div class="toggle"><ion-icon name="menu-outline"></ion-icon></div>
                <div class="search"><label><input type="text" placeholder="Search here"><ion-icon name="search-outline"></ion-icon></label></div>
            </div>
            @yield('content')
        </div>
    </div>
    <script src="assets/js/main.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const list = document.querySelectorAll(".navigation li");
        const toggle = document.querySelector(".toggle");
        const navigation = document.querySelector(".navigation");
        const main = document.querySelector(".main");

        function activeLink() {
            list.forEach(item => item.classList.remove("hovered"));
            this.classList.add("hovered");
        }

        list.forEach(item => item.addEventListener("mouseover", activeLink));

        // Restore navigation state from localStorage
        if (localStorage.getItem('navState') === 'active') {
            navigation.classList.remove('collapsed');
            main.classList.remove('collapsed');
        } else {
            navigation.classList.add('collapsed');
            main.classList.add('collapsed');
        }

        toggle.onclick = function () {
            navigation.classList.toggle("collapsed");
            main.classList.toggle("collapsed");

            // Save state to localStorage
            if (navigation.classList.contains('collapsed')) {
                localStorage.removeItem('navState');
            } else {
                localStorage.setItem('navState', 'active');
            }
        };
    });
    </script>
</body>
</html>
