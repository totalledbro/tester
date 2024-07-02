<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin</title>
    <link rel="icon" href="{{ asset('img/logodesa.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/5.0.1/css/ionicons.min.css">
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
                <li class="nav-item"><a href="#" id="ubah-password-link"><span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span><span class="title">Password</span></a></li>
                <li class="nav-item"><a href="{{ route('actionlogout') }}"><span class="icon"><ion-icon name="log-out-outline"></ion-icon></span><span class="title">Sign Out</span></a></li>
            </ul>
        </div>
        <div class="main">
            <div class="topbar">
                <div class="admin-title">Admin Area</div>
            </div>
            @yield('content')
        </div>
        <div id="ubah-password-modals" class="modals" style="display: none;">
                <div class="modals-content">
                    <h2>Ubah Password</h2>
                    <form id="ubah-password-form" method="POST" action="{{ route('changePassword') }}">
                        @csrf
                        <div class="input-field">
                            <input type="password" name="current_password" id="current-password" required>
                            <label>Password Lama</label>
                            <span class="toggle-password">
                                <ion-icon name="eye-off-outline" id="toggle-current-password"></ion-icon>
                            </span>
                        </div>
                        <div class="input-field">
                            <input type="password" name="new_password" id="new-password" required>
                            <label>Password Baru</label>
                            <span class="toggle-password">
                                <ion-icon name="eye-off-outline" id="toggle-new-password"></ion-icon>
                            </span>
                            <p id="PasswordError1" style="color: red; display: none;">Password setidaknya harus 8 karakter.</p>
                        </div>
                        <div class="input-field">
                            <input type="password" name="new_password_confirmation" id="new-password-confirmation" required>
                            <label>Konfirmasi Password Baru</label>
                            <span class="toggle-password">
                                <ion-icon name="eye-off-outline" id="toggle-new-password-confirmation"></ion-icon>
                            </span>
                            <p id="PasswordError2" style="color: red; display: none;">Password tidak sesuai.</p>
                        </div>
                        <button type="submit">Ubah Password</button>
                        <button type="button" onclick="closeForms()">Cancel</button>
                    </form>
                </div>
            </div>
            <div class="overlays" id="overlays"></div>
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

        const currentPage = window.location.pathname;
        list.forEach(item => {
            const link = item.querySelector('a').getAttribute('href');
            const fullPath = new URL(link, window.location.origin).pathname;
            if (currentPage === fullPath) {
                item.classList.add('hovered');
            }
        });

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

        const userLastName = '{{ ucwords(Auth::user()->last_name) }}';
        greeting.innerText = `${greetingText}, ${userLastName}`;
    });

    document.addEventListener('DOMContentLoaded', function () {
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input && icon) {
                icon.addEventListener('click', function () {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    icon.setAttribute('name', type === 'password' ? 'eye-off-outline' : 'eye-outline');
                });
            }
        }

        togglePasswordVisibility('current-password', 'toggle-current-password');
        togglePasswordVisibility('new-password', 'toggle-new-password');
        togglePasswordVisibility('new-password-confirmation', 'toggle-new-password-confirmation');
    });
    
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('new-password');
        const confirmPasswordInput = document.getElementById('new-password-confirmation');
        const passwordError1 = document.getElementById('PasswordError1');
        const passwordError2 = document.getElementById('PasswordError2');

        function validatePassword1() {
            if (passwordInput.value.length < 8) {
                passwordError1.style.display = 'block';
                passwordInput.setCustomValidity('Password must be at least 8 characters long.');
            } else {
                passwordError1.style.display = 'none';
                passwordInput.setCustomValidity('');
            }
        }

        function validatePassword2() {
            if (passwordInput.value !== confirmPasswordInput.value) {
                passwordError2.style.display = 'block';
                confirmPasswordInput.setCustomValidity('Passwords do not match.');
            } else {
                passwordError2.style.display = 'none';
                confirmPasswordInput.setCustomValidity('');
            }
        }

        passwordInput.addEventListener('input', validatePassword1);
        confirmPasswordInput.addEventListener('input', validatePassword2);
    });

    document.getElementById('ubah-password-link').addEventListener('click', function (event) {
        event.preventDefault();
        document.getElementById('ubah-password-modals').style.display = 'block';
        document.getElementById('overlays').style.display = 'block'; // Show overlay
    });

    function closeForms() {
        document.getElementById('ubah-password-modals').style.display = 'none';
        document.getElementById('overlays').style.display = 'none'; // Hide overlay
    }
    </script>
    @yield('scripts')
</body>
</html>
