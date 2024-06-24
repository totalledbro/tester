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
                    <h3>PERPUSTAKAAN DIGITAL</h3>
                </a>
                <ul class="links">
                    <span class="close-btn material-symbols-rounded">close</span>
                    <li><a href="{{ url('/') }}">Beranda</a></li>
                    <li><a href="{{ url('/jelajahi') }}">Jelajahi</a></li>
                    <li><a href="{{ url('/kategori') }}">Kategori</a></li>
                    <li><a href="#">About us</a></li>
                    <li>
                        @auth
                        <a href="{{ url('/pinjaman') }}">Pinjamanku</a>
                        @endauth
                    </li>
                </ul>
                @guest
                <button class="login-btn">LOGIN</button>
                @else
                <div class="dropdown">
                    <div class="greeting" id="greeting">{{ ucwords(Auth::user()->last_name) }}</div>
                    <button id="dropdown-button" class="dropbtn">
                        <span class="icon"><ion-icon name="caret-down-outline"></ion-icon></span>
                    </button>
                    <div class="dropdown-content">
                        <a href="#" id="ubah-password-link">Ubah password</a>
                        <a href="{{ route('actionlogout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    </div>
                </div>
                <form id="logout-form" action="{{ route('actionlogout') }}" method="GET" style="display: none;">
                    @csrf
                </form>
                @endguest
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
                        <a href="#" class="forgot-pass-link">Forgot password?</a>
                        <button type="submit">Login</button>
                    </form>
                    <div class="bottom-link">
                        Don't have an account? <a href="#" id="signup-link">Signup</a>
                    </div>
                </div>
            </div>
            <div class="form-box signup">
                <div class="form-content">
                    <h2>SIGNUP</h2>
                    <form id="signup-form" method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="input-field">
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                            <label>Enter your first name</label>
                        </div>
                        <div class="input-field">
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required>
                            <label>Enter your last name</label>
                        </div>
                        <div class="input-field">
                            <input type="email" name="email" value="{{ old('email') }}" required>
                            <label>Enter your email</label>
                        </div>
                        <div class="input-field">
                            <input type="password" id="password1" name="password" required>
                            <label>Create password</label>
                            <span class="toggle-password">
                                <ion-icon name="eye-off-outline" id="toggle-password1"></ion-icon>
                            </span>
                            <p id="passwordError1" style="color: red; display: none;">Password setidaknya harus 8 karakter.</p>
                        </div>
                        <div class="input-field">
                            <input type="password" id="password2" name="password_confirmation" required>
                            <label>Confirm password</label>
                            <span class="toggle-password">
                                <ion-icon name="eye-off-outline" id="toggle-password2"></ion-icon>
                            </span>
                            <p id="passwordError2" style="color: red; display: none;">Password tidak sesuai.</p>
                        </div>
                        <button type="submit" id="signup-btn">Sign Up</button>
                    </form>
                    <div class="bottom-link">
                        Already have an account? <a href="#" id="login-link">Login</a>
                    </div>
                </div>
            </div>
        </div>

        <div id="ubah-password-modal" class="modal">
            <div class="modal-content">
                <h2>Ubah Password</h2>
                <form id="ubah-password-form" method="POST" action="{{ route('changePassword') }}">
                    @csrf
                    <div class="input-field">
                        <input type="password" name="current_password" id="current-password" required>
                        <label>Current Password</label>
                        <span class="toggle-password">
                            <ion-icon name="eye-off-outline" id="toggle-current-password"></ion-icon>
                        </span>
                    </div>
                    <div class="input-field">
                        <input type="password" name="new_password" id="new-password" required>
                        <label>New Password</label>
                        <span class="toggle-password">
                            <ion-icon name="eye-off-outline" id="toggle-new-password"></ion-icon>
                        </span>
                        <p id="newPasswordError1" style="color: red; display: none;">Password setidaknya harus 8 karakter.</p>
                    </div>
                    <div class="input-field">
                        <input type="password" name="new_password_confirmation" id="new-password-confirmation" required>
                        <label>Confirm New Password</label>
                        <span class="toggle-password">
                            <ion-icon name="eye-off-outline" id="toggle-new-password-confirmation"></ion-icon>
                        </span>
                        <p id="newPasswordError2" style="color: red; display: none;">Password tidak sesuai.</p>
                    </div>
                    <button type="submit">Change Password</button>
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
        <p>Check your email to verify your account!</p>
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
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const signupForm = document.getElementById("signup-form");
    const emailVerifyModal = document.getElementById("emailVerifyModal");
    const loginForm = document.getElementById("login-form");

    signupForm.addEventListener("submit", async function(event) {
        event.preventDefault();
        const formData = new FormData(signupForm);
        const url = signupForm.action;
        console.log("submited");
        try {
            const response = await fetch(url, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                // If signup is successful, show the email verification modal
                signupForm.reset();
                emailVerifyModal.style.display = "block";
                setTimeout(() => {
                    emailVerifyModal.style.display = "none";
                }, 5000); // Hide the modal after 5 seconds
            } else {
                // Handle signup errors (e.g., validation errors)
                const errorData = await response.json();
                if (errorData.errors) {
                    for (const [field, messages] of Object.entries(errorData.errors)) {
                        console.error(`${field}: ${messages.join(', ')}`);
                    }
                }
            }
        } catch (error) {
            console.error("Signup request failed:", error);
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
    const loginForm = document.getElementById("login-form");
    const loginErrorModal = document.getElementById("loginErrorModal");
    const emailVerifyModal = document.getElementById("emailVerifyModal");

    loginForm.addEventListener("submit", async function(event) {
        event.preventDefault();
        const formData = new FormData(loginForm);
        const url = loginForm.action;

        try {
            const response = await fetch(url, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            console.log("Response data:", data);  // Make sure this line is in place

            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                if (data.email_verified_at === null) {
                    console.log("Email not verified.");
                    emailVerifyModal.style.display = "block";
                    setTimeout(() => {
                        emailVerifyModal.style.display = "none";
                    }, 5000); // Hide the modal after 5 seconds
                } else {
                    console.log("Invalid credentials.");
                    loginErrorModal.style.display = "block";
                    setTimeout(() => {
                        loginErrorModal.style.display = "none";
                    }, 5000); // Hide the modal after 5 seconds
                }
            }
        } catch (error) {
            console.error("Login request failed:", error);
        }
    });
});






    // Function to toggle password visibility
    function togglePasswordVisibility(toggleIcon, passwordField) {
        if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleIcon.name = "eye-outline";
        } else {
            passwordField.type = "password";
            toggleIcon.name = "eye-off-outline";
        }
    }

    // Attach event listeners to password toggle icons
    document.getElementById("toggle-login-password").addEventListener("click", function() {
        togglePasswordVisibility(this, document.getElementById("login-password"));
    });

    document.getElementById("toggle-password1").addEventListener("click", function() {
        togglePasswordVisibility(this, document.getElementById("password1"));
    });

    document.getElementById("toggle-password2").addEventListener("click", function() {
        togglePasswordVisibility(this, document.getElementById("password2"));
    });

    document.getElementById("toggle-current-password").addEventListener("click", function() {
        togglePasswordVisibility(this, document.getElementById("current-password"));
    });

    document.getElementById("toggle-new-password").addEventListener("click", function() {
        togglePasswordVisibility(this, document.getElementById("new-password"));
    });

    document.getElementById("toggle-new-password-confirmation").addEventListener("click", function() {
        togglePasswordVisibility(this, document.getElementById("new-password-confirmation"));
    });
});

document.addEventListener('DOMContentLoaded', function () {
    // Get the current hour
    const currentHour = new Date().getHours();

    // Define the greetings based on the time of day
    let greeting;
    if (currentHour < 12) {
        greeting = 'Selamat Pagi';
    } else if(currentHour < 15){
        greeting = 'Selamat Siang';
    } else if (currentHour < 18) {
        greeting = 'Selamat Sore';
    } else {
        greeting = 'Selamat Malam';
    }

    // Update the greeting in the DOM
    const userLastName = document.querySelector('.greeting').innerText.trim();
    document.getElementById('greeting').innerText = `${greeting}, ${userLastName}`;
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

document.addEventListener('DOMContentLoaded', function () {
    // Login form submission handling
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
                // If login is successful, redirect to the dashboard
                if (response.success) {
                    window.location.href = response.redirect_url;
                } else {
                    // If there's an error, display the error modal and blur login modal
                    $("#loginErrorModal").fadeIn();
                    $(".form-popup").addClass("blur-and-disable");
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
        var modal = $("#loginErrorModal");
        if (event.target == modal[0]) {
            modal.fadeOut();
            $(".form-popup").removeClass("blur-and-disable");
        }
    });
});

$(document).ready(function() {
    // Function to handle form submission
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
                // If registration is successful, display the popup
                if (response.success) {
                    $(".form-popup").hide(); // Hide the form popup
                    $("#emailVerifyModal").fadeIn(); // Show the success popup
                } else {
                    // If there's an error, log it to the console
                    console.log(response.message);
                }
            },
            error: function(xhr, status, error) {
                // Handle error, if any
                console.log(error);
            }
        });
    });

    // Dropdown button functionality
    $("#dropdown-button").click(function(event) {
        event.stopPropagation();
        $(".dropdown-content").toggleClass("show");
    });

    // Close the dropdown menu if the user clicks outside of it
    $(window).click(function() {
        if ($(".dropdown-content").hasClass("show")) {
            $(".dropdown-content").removeClass("show");
        }
    });

    // Show the "Ubah Password" modal
    $("#ubah-password-link").click(function() {
        $("#ubah-password-modal").fadeIn();
    });

    // Hide the "Ubah Password" modal
    $("#close-ubah-password-modal").click(function() {
        $("#ubah-password-modal").fadeOut();
    });

    // Form submission for password update
    $("#ubah-password-form").submit(function(event) {
        event.preventDefault(); // Prevent default form submission

        // Serialize form data
        var formData = $(this).serialize();

        // Send form data to the server using Ajax
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            data: formData,
            success: function(response) {
                // If password update is successful, display success message
                if (response.success) {
                    alert("Password updated successfully!");
                    $("#ubah-password-modal").fadeOut(); // Hide the modal
                } else {
                    // If there's an error, log it to the console
                    console.log(response.message);
                }
            },
            error: function(xhr, status, error) {
                // Handle error, if any
                console.log(error);
            }
        });
    });

    // Toggle password visibility for password update form
    $(".toggle-password").click(function() {
        var input = $(this).siblings("input");
        var type = input.attr("type") === "password" ? "text" : "password";
        input.attr("type", type);
        $(this).toggleClass("eye-outline eye-off-outline");
    });
});

</script>
@yield('scripts')
</body>
</html>
