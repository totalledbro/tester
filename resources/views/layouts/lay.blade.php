<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PERPUSTAKAAN DIGITAL KALINGANYAR</title>
    <link rel="icon" href="{{ asset('img/logodesa.png') }}" type="image/png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/5.0.1/css/ionicons.min.css">
    <script src="{{ asset('js/script.js') }}" defer></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
<div class="container">
    <div class="main active">
    <header>
        <nav class="navbar">
            <span class="hamburger-btn material-symbols-rounded">menu</span>
            <a href="{{ url('/') }}" class="logo">
                <img src="{{ asset('img/logodesa.png') }}" alt="logo">
                <h2></h2>
                <h3>PERPUSTAKAAN DIGITAL KALINGANYAR</h3>
            </a>
            <ul class="links">
                <span class="close-btn material-symbols-rounded">close</span>
                <li><a href="{{ url('/') }}">Beranda</a></li>
                <li><a href="{{ url('/jelajahi') }}">Jelajahi</a></li>
                <li><a href="{{ url('/kategori') }}">Kategori</a></li>
                <li><a href="{{ url('/tentang-kami') }}">Tentang Kami</a></li>
                <li>
                    @auth
                    <a href="{{ url('/pinjaman') }}">Pinjamanku</a>
                    @endauth
                </li>
                <li class="mobile-auth">
                    @guest
                    <button class="login-btn">LOGIN/DAFTAR</button>
                    @else
                    <div class="dropdown">
                        <div class="greeting">{{ ucwords(Auth::user()->last_name) }}</div>
                        <button id="dropdown-buttonmob" class="dropbtn">
                            <span class="icon"><ion-icon name="caret-down-outline"></ion-icon></span>
                        </button>
                        <div class="dropdown-content">
                            <a href="#" class="ubah-password-link" style="padding: 3px 4px; margin:10px; font-size: 12px;">Ubah password</a>
                            <a href="{{ route('actionlogout') }}" style="padding: 3px 4px; margin:10px; font-size: 12px;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        </div>
                    </div>
                    <form id="logout-form" action="{{ route('actionlogout') }}" method="GET" style="display: none;">
                        @csrf
                    </form>
                    @endguest
                </li>
            </ul>
            <div class="desktop-auth">
                @guest
                <button class="login-btn">LOGIN/DAFTAR</button>
                @else
                    <div class="dropdown">
                        <div class="greeting">{{ ucwords(Auth::user()->last_name) }}</div>
                        <button id="dropdown-buttondes"  class="dropbtn">
                            <span class="icon"><ion-icon name="caret-down-outline"></ion-icon></span>
                        </button>
                        <div class="dropdown-content">
                            <a href="#" class="ubah-password-link">Ubah password</a>
                            <a href="{{ route('actionlogout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        </div>
                    </div>
                    <form id="logout-form" action="{{ route('actionlogout') }}" method="GET" style="display: none;">
                        @csrf
                    </form>
                @endguest
            </div>
            <span class="hamburger-btn material-symbols-rounded" style="opacity: 0;">menu</span>
        </nav>
    </header>

        <div class="blur-bg-overlay"></div>
        <div class="form-popup">
            <span class="close-btn material-symbols-rounded">close</span>
            <div class="form-box login">
                <div class="form-content">
                    <h2>LOGIN</h2>
                    <form id="login-form" method="POST" action="{{ route('actionlogin') }}">
                        @csrf
                        <div class="input-field">
                            <input type="text" name="email" autocomplete="email" required>
                            <label>Email</label>
                        </div>
                        <div class="input-field">
                            <input type="password" name="password" id="login-password" required>
                            <label>Password</label>
                            <span class="toggle-password">
                                <ion-icon name="eye-off-outline" id="toggle-login-password"></ion-icon>
                            </span>
                        </div>
                        <a href="#" class="forgot-pass-link" id="forgot-pass-link">Lupa password?</a>
                        <button type="submit">Login</button>
                    </form>
                    <div class="bottom-link">
                        Belum punya akun? <a href="#" id="signup-link">Daftar</a>
                    </div>
                </div>
            </div>
            <div class="form-box signup">
                <div class="form-content">
                    <h2>DAFTAR</h2>
                    <form id="signup-form" method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="input-field">
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                            <label>Nama Depan</label>
                        </div>
                        <div class="input-field">
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required>
                            <label>Nama Belakang</label>
                        </div>
                        <div class="input-field">
                            <input type="email" name="email" value="{{ old('email') }}" required>
                            <label>Email</label>
                        </div>
                        <div class="input-field">
                            <input type="password" id="password1" name="password" required>
                            <label>Password</label>
                            <span class="toggle-password">
                                <ion-icon name="eye-off-outline" id="toggle-password1"></ion-icon>
                            </span>
                            <p id="passwordError1" style="color: red; display: none;">Password setidaknya harus 8 karakter.</p>
                        </div>
                        <div class="input-field">
                            <input type="password" id="password2" name="password_confirmation" required>
                            <label>Konfirmasi Password</label>
                            <span class="toggle-password">
                                <ion-icon name="eye-off-outline" id="toggle-password2"></ion-icon>
                            </span>
                            <p id="passwordError2" style="color: red; display: none;">Password tidak sesuai.</p>
                        </div>
                        <button type="submit" id="signup-btn">Daftar</button>
                    </form>
                    <div class="bottom-link">
                        Sudah punya akun? <a href="#" id="login-link">Login</a>
                    </div>
                </div>
            </div>
            <div class="form-box forgot-password">
            <div class="form-content">
                <h2>Reset Password</h2>
                <form id="forgot-password-form" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="input-field">
                        <input type="email" name="email" autocomplete="email" required>
                        <label>Email</label>
                    </div>
                    <button type="submit">Kirim Link Reset Password</button>
                </form>
                <div class="bottom-link">
                    Sudah ingat akun? <a href="#" id="login-link">Login</a>
                </div>
            </div>
        </div>
        </div>
        <div class="success-popup" style="display: none;">
            <h3>Pendaftaran Berhasil</h3>
            <!-- Add any additional content or styling for the popup -->
        </div>
        <div id="ubah-password-modal" class="modal">
            <div class="modal-content">
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
                        <p id="newPasswordError1" style="color: red; display: none;">Password setidaknya harus 8 karakter.</p>
                    </div>
                    <div class="input-field">
                        <input type="password" name="new_password_confirmation" id="new-password-confirmation" required>
                        <label>Konfirmasi Password Baru</label>
                        <span class="toggle-password">
                            <ion-icon name="eye-off-outline" id="toggle-new-password-confirmation"></ion-icon>
                        </span>
                        <p id="newPasswordError2" style="color: red; display: none;">Password tidak sesuai.</p>
                    </div>
                    <button type="submit">Ubah Password</button>
                </form>
            </div>
        </div>

        @yield('content')
    </div>
</div>
<!-- Modal for email verification -->
<div id="emailVerifyModal" class="modal">
    <div class="modal-content">
        <div class="modal-icon">
            <ion-icon name="mail-outline"></ion-icon>
        </div>
        <p>Email Verifikasi Telah Dikirim!</p>
        <p>Silahkan Periksa Kotak Masuk Dan Folder Spam Email Anda.</p>
    </div>
</div>

<div id="emailErrorModal" class="modal">
    <div class="modal-content">
        <div class="modal-icon">
            <ion-icon name="alert-circle-outline"></ion-icon>
        </div>
        <p>Email Telah Digunakan!</p>
    </div>
</div>

<!-- Modal for email not found -->
<div id="emailNotFoundModal" class="modal">
    <div class="modal-content">
        <div class="modal-icon">
            <ion-icon name="alert-circle-outline"></ion-icon>
        </div>
        <p>Email Tidak Ditemukan!</p>
    </div>
</div>

<!-- Modal for login error -->
<div id="loginErrorModal" class="modal">
    <div class="modal-content">
        <div class="modal-icon">
            <ion-icon name="alert-circle-outline"></ion-icon>
        </div>
        <p>Email Atau Password Salah!</p>
        <p>Mohon Coba Lagi</p>
    </div>
</div>

<!-- Modal for password change success -->
<div id="passwordSuccessModal" class="modal">
    <div class="modal-content">
        <div class="modal-icon">
            <ion-icon name="checkmark-circle-outline"></ion-icon>
        </div>
        <p>Password Berhasil Diubah!</p>
        <p>Silahkan login kembali.</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Get the current hour
    const currentHour = new Date().getHours();

    // Define the greetings based on the time of day
    let greeting;
    if (currentHour < 12) {
        greeting = 'Selamat Pagi';
    } else if (currentHour < 15) {
        greeting = 'Selamat Siang';
    } else if (currentHour < 18) {
        greeting = 'Selamat Sore';
    } else {
        greeting = 'Selamat Malam';
    }

    // Update all greeting elements in the DOM
    const greetingElements = document.querySelectorAll('.greeting');
    greetingElements.forEach(element => {
        const userLastName = element.innerText.trim();
        element.innerText = `${greeting}, ${userLastName}`;
    });
});


    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password1');
        const confirmPasswordInput = document.getElementById('password2');
        const passwordError1 = document.getElementById('passwordError1');
        const passwordError2 = document.getElementById('passwordError2');

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

        passwordInput.addEventListener('keyup', validatePassword1);
        confirmPasswordInput.addEventListener('keyup', validatePassword2);
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Toggle password visibility
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            icon.addEventListener('click', function () {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                icon.setAttribute('name', type === 'password' ? 'eye-off-outline' : 'eye-outline');
            });
        }

        togglePasswordVisibility('login-password', 'toggle-login-password');
        togglePasswordVisibility('password1', 'toggle-password1');
        togglePasswordVisibility('password2', 'toggle-password2');
        togglePasswordVisibility('current-password', 'toggle-current-password');
        togglePasswordVisibility('new-password', 'toggle-new-password');
        togglePasswordVisibility('new-password-confirmation', 'toggle-new-password-confirmation');
    });

    $(document).ready(function() {
    // Handle login form submission
    $("#login-form").submit(function(event) {
        event.preventDefault(); // Prevent default form submission

        // Serialize form data
        var formData = $(this).serialize();

        // Send form data to the server using Ajax
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            data: formData,
            success: function(response) {
                if (response.success) {
                    // If login is successful, redirect to the dashboard
                    window.location.href = response.redirect_url;
                } else {
                    if (response.message === 'email_not_verified') {
                        // Show the email verification modal
                        $("#emailVerifyModal").fadeIn();
                    } else {
                        // If there's another error, display the error modal
                        $("#loginErrorModal").fadeIn();
                        $(".form-popup").addClass("blur-and-disable");
                    }
                }
            },
            error: function(xhr, status, error) {
                // Handle error, if any
                console.log(error);
            }
        });
    });

    // Handle error modal close when clicking outside of modal content
    $(window).click(function(event) {
        var loginErrorModal = $("#loginErrorModal");
        if (event.target == loginErrorModal[0]) {
            loginErrorModal.fadeOut();
            $(".form-popup").removeClass("blur-and-disable");
        }
        var emailVerifyModal = $("#emailVerifyModal");
        if (event.target == emailVerifyModal[0]) {
            emailVerifyModal.fadeOut();
        }
    });
});


    $(document).ready(function() {
        // Function to handle form submission

    // Handle registration form submission
    $("#signup-form").submit(function(event) {
        event.preventDefault(); // Prevent default form submission

        // Serialize form data
        var formData = $(this).serialize();

        // Send form data to the server using Ajax
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            data: formData,
            success: function(response) {
                if (response.success) {
                    // If registration is successful, display the email verification modal
                    $("#emailVerifyModal").fadeIn();
                } else {
                    if (response.message === 'email_taken') {
                        // Show the email taken error modal
                        $("#emailErrorModal").fadeIn();
                    } else {
                        console.log(response.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                if (xhr.status === 422) {
                    // Show the email taken error modal if the error message matches
                    $("#emailErrorModal").fadeIn();
                } else {
                    console.log('Error:', error);
                }
            }
        });
    });

    // Close modals when clicking outside of modal content
    $(window).click(function(event) {
        if (event.target == $("#emailErrorModal")[0]) {
            $("#emailErrorModal").fadeOut();
        }
        if (event.target == $("#emailVerifyModal")[0]) {
            $("#emailVerifyModal").fadeOut();
        }
    });

        // Dropdown button functionality
        $("#dropdown-buttonmob, #dropdown-buttondes").click(function(event) {
            event.stopPropagation();
            $(this).siblings(".dropdown-content").toggleClass("show");
        });

        // Close the dropdown menu if the user clicks outside of it
        $(window).click(function() {
            if ($(".dropdown-content").hasClass("show")) {
                $(".dropdown-content").removeClass("show");
            }
        });

        // Show the "Ubah Password" modal
        $(".ubah-password-link").click(function() {
            $("#ubah-password-modal").fadeIn();
        });

        // Close the "Ubah Password" modal when clicking outside of the modal content
        $(window).click(function(event) {
            var modal = $("#ubah-password-modal");
            if (event.target == modal[0]) {
                modal.fadeOut();
            }
        });

        // Handle "Ubah Password" form submission
        $("#ubah-password-form").submit(function(event) {
            event.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                type: "POST",
                url: $(this).attr("action"),
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert("Password berhasil diubah!");
                        $("#ubah-password-modal").fadeOut();
                    } else {
                        console.log(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        });
    });


    // Handle "Ubah Password" form submission
    $("#ubah-password-form").submit(function(event) {
        event.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            data: formData,
            success: function(response) {
                if (response.success) {
                    $("#passwordSuccessModal").fadeIn();
                    $(".form-popup").addClass("blur-and-disable");
                    
                    setTimeout(function() {
                        document.getElementById('logout-form').submit();
                    }, 3000);
                } else {
                    console.log(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    });

    // Handle success modal close when clicking outside of modal content
    $(window).click(function(event) {
        var modal = $("#passwordSuccessModal");
        if (event.target == modal[0]) {
            modal.fadeOut();
            $(".form-popup").removeClass("blur-and-disable");
        }
    });
    $(document).ready(function() {
    $("#forgot-password-form").submit(function(event) {
        event.preventDefault(); // Prevent default form submission

        // Serialize form data
        var formData = $(this).serialize();

        // Send form data to the server using Ajax
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show the check email modal
                    $("#emailVerifyModal").fadeIn();
                } else {
                    // Show the email not found modal
                    $("#emailNotFoundModal").fadeIn();
                }
                setTimeout(function() {
                    location.reload();
                }, 10000);
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        });

        // Close modals when clicking outside of modal content
        $(window).click(function(event) {
            if (event.target == $("#emailVerifyModal")[0]) {
                $("#emailVerifyModal").fadeOut();
            }
            if (event.target == $("#emailNotFoundModal")[0]) {
                $("#emailNotFoundModal").fadeOut();
            }
        });
    });

</script>
@yield('scripts')
</body>
</html>