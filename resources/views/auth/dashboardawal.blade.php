@extends('layouts.lay')

@section('content')
    <style>

    </style>

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
            @guest
                <button class="login-btn">LOG IN</button>
            @else
                <div class="dropdown">
                    <div class="greeting" id="greeting"> {{ ucwords(Auth::user()->last_name) }}</div>
                    <button onclick="dropdown()" class="dropbtn">
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
                <form id="login-form" method="POST" action="{{ route('actionlogin') }}" >
                @csrf
                    <div class="input-field" >
                        <input type="text" name="email" required>
                        <label>Email</label>
                    </div>
                    <div class="input-field" >
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
            <div class="form-content" role="form" method="POST" action="{{ route('register') }}">
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
@endsection
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
            } else {
                passwordError1.style.display = 'none';
                passwordInput.setCustomValidity('');
            }
        }

        function validatePassword2() {
            if (passwordInput.value !== confirmPasswordInput.value) {
                passwordError2.style.display = 'block';
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

        // Function to handle form submission for login

    });

    function dropdown() {
        var dropdownContent = document.querySelector(".dropdown-content");
        dropdownContent.classList.toggle("show");
    }

    // Close the dropdown menu if the user clicks outside of it
    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
</script>
