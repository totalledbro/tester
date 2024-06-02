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
            <div class="topbar">
                <div class="toggle"><ion-icon name="menu-outline"></ion-icon></div>
                <span class="admin-icon"><ion-icon name="person-circle-outline"></ion-icon></span>
                <span class="greeting" id="greeting"></span>
            </div>
            <ul>
                <li class="nav-item"><a href="{{ url('/dashboard') }}"><span class="icon"><ion-icon name="home-outline"></ion-icon></span><span class="title">Dashboard</span></a></li>
                <li class="nav-item"><a href="{{ url('/buku') }}"><span class="icon"><ion-icon name="book-outline"></ion-icon></span><span class="title">Buku</span></a></li>
                <li class="nav-item"><a href="{{ url('/datakategori') }}"><span class="icon"><ion-icon name="bookmarks-outline"></ion-icon></span><span class="title">Kategori</span></a></li>
                <li class="nav-item"><a href="{{ url('/datapinjam') }}"><span class="icon"><ion-icon name="time-outline"></ion-icon></span><span class="title">Data Pinjam</span></a></li>
                <li class="nav-item"><a href="#"><span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span><span class="title">Password</span></a></li>
                <li class="nav-item"><a href="{{ route('actionlogout') }}"><span class="icon"><ion-icon name="log-out-outline"></ion-icon></span><span class="title">Sign Out</span></a></li>
            </ul>
        </div>
        <div class="main">
            <div class="topbar">
                <div class="admin-title">Admin Area</div>
            </div>
            @yield('content')
        </div>
    </div>
    <script src="assets/js/main.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const list = document.querySelectorAll(".navigation .nav-item");
        const toggle = document.querySelector(".toggle");
        const navigation = document.querySelector(".navigation");
        const main = document.querySelector(".main");
        const adminIcon = document.querySelector(".admin-icon");
        const greeting = document.getElementById('greeting');

        // Function to add 'hovered' class to active link
        function activeLink() {
            list.forEach(item => item.classList.remove("hovered"));
            this.classList.add("hovered");
        }

        list.forEach(item => item.addEventListener("click", activeLink));

        if (localStorage.getItem('navState') === 'active') {
            navigation.classList.add('expanded');
            main.classList.add('expanded');
            adminIcon.style.display = 'inline-block';
            greeting.style.display = 'inline-block';
        } else {
            navigation.classList.remove('expanded');
            main.classList.remove('expanded');
            adminIcon.style.display = 'none';
            greeting.style.display = 'none';
        }

        toggle.onclick = function () {
            navigation.classList.toggle("expanded");
            main.classList.toggle("expanded");

            if (navigation.classList.contains('expanded')) {
                localStorage.setItem('navState', 'active');
                adminIcon.style.display = 'inline-block';
                greeting.style.display = 'inline-block';
            } else {
                localStorage.removeItem('navState');
                adminIcon.style.display = 'none';
                greeting.style.display = 'none';
            }
        };

        // Highlight the current page
        const currentPage = window.location.pathname;
        list.forEach(item => {
            const link = item.querySelector('a').getAttribute('href');
            const fullPath = new URL(link, window.location.origin).pathname;
            if (currentPage === fullPath) {
                item.classList.add('hovered');
            }
        });

        // Set greeting text
        const currentHour = new Date().getHours();
        let greetingText;
        if (currentHour < 12) {
            greetingText = 'Selamat Pagi';
        } else if (currentHour < 15) {
            greetingText = 'Selamat Siang';
        } else if (currentHour < 18) {
            greetingText = 'Selamat Sore';
        } else {
            greetingText = 'Selamat Malam';
        }

        // Assuming the user's last name is available in a JavaScript variable
        const userLastName = '{{ ucwords(Auth::user()->last_name) }}';
        greeting.innerText = `${greetingText}, ${userLastName}`;
    });
    </script>
    @yield('scripts')
</body>
</html>
