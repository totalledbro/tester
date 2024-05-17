<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PERPUSTAKAAN DIGITAL KALINGANYAR</title>
    <!-- Google Fonts Link For Icons -->
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
                <a href="{{url('/')}}" class="logo">
                    <img src="{{ asset('img/logodesa.png') }}" alt="logo">
                    <h2></h2>
                    <h3>PERPUSTAKAAN DIGITAL</h3>
                </a>
                <ul class="links">
                    <span class="close-btn material-symbols-rounded">close</span>
                    <li><a href="{{url('/')}}">Beranda</a></li>
                    <li><a href="{{url('/jelajahi')}}">Jelajahi</a></li>
                    <li><a href="#">Kategori</a></li>
                    <li><a href="#">About us</a></li>
                    <li><a href="#">Contact us</a></li>
                </ul>
                @guest
                    <button class="login-btn">LOG IN</button>
                @else
                    <div class="dropdown">
                        <div class="greeting" id="greeting"> {{ ucwords(Auth::user()->last_name) }}</div>
                        <button id="dropdown-button" class="dropbtn">
                            <span class="icon"><ion-icon name="caret-down-outline"></ion-icon></span>
                        </button>
                        <div class="dropdown-content">
                            <a href="#">Settings</a>
                            <a href="{{ route('actionlogout') }}"
                                onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                Logout
                            </a>
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
                            <input type="text" name="email" required>
                            <label>Email</label>
                        </div>
                        <div class="input-field">
                            <input type="password" name="password" required>
                            <label>Password</label>
                        </div>
                        <a href="#" class="forgot-pass-link">Forgot password?</a>
                        <button type="submit">Log In</button>
                    </form>
                    <div class="bottom-link">
                        Don't have an account?
                        <a href="#" id="signup-link">Signup</a>
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
                            <p id="passwordError1" style="color: red; display: none;">Password setidaknya harus 8 karakter.</p>
                        </div>
                        <div class="input-field">
                            <input type="password" id="password2" name="password_confirmation" required>
                            <label>Confirm password</label>
                            <p id="passwordError2" style="color: red; display: none;">Password tidak sesuai.</p>
                        </div>
                        <button type="submit" id="signup-btn">Sign Up</button>
                    </form>
                    <div class="bottom-link">
                        Already have an account?
                        <a href="#" id="login-link">Login</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="success-popup" style="display: none;">
            <h3>Registration Successful!</h3>
            <!-- Add any additional content or styling for the popup -->
        </div>

        @yield('content')
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get the current hour
        const currentHour = new Date().getHours();

        // Define the greetings based on the time of day
        let greeting;
        if (currentHour < 12) {
            greeting = 'Selamat pagi';
        } else if(currentHour < 15){
            greeting = 'Selamat siang';
        } else if (currentHour < 18) {
            greeting = 'Selamat sore';
        } else {
            greeting = 'Selamat malam';
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

    $(document).ready(function() {
        // Function to handle form submission
        $("#signup-form").submit(function(event) {
            // Prevent default form submission
            event.preventDefault();

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
                        $(".success-popup").fadeIn(); // You can customize this class or style
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
    });


</script>
@yield('scripts')
</body>
</html>
