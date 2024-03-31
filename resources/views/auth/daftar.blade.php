@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="paneldaftar">
                    <div class="panel-heading">Registrasi Anggota</div>
                    <div class="panel-body" style="margin-bottom: 10px;">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('anggota.store') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('namadepan') ? ' has-error' : '' }}">
                                <label for="" class="form-input">
                                    <input type="text" name="namadepan" value="{{ old('fnamadepan') }}"  required autofocus="true" />
                                    <span class="label">Nama Depan</span>
                                    <span class="underline"></span>
                                </label>
                            </div>

                            <div class="form-group{{ $errors->has('namablkg') ? ' has-error' : '' }}">
                                <label for="" class="form-input">
                                    <input type="text" name="namablkg" value="{{ old('namablkg') }}"  required autofocus="true"  />
                                    <span class="label">Nama Belakang</span>
                                    <span class="underline"></span>
                                </label>
                            </div>

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="" class="form-input">
                                    <input type="email" name="email" value="{{ old('email') }}"  required autofocus="true" />
                                    <span class="label">Email</span>
                                    <span class="underline"></span>
                                </label>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="" class="form-input">
                                    <input type="password" id="password1" name="password" required  />
                                    <span class="label">Password</span>
                                    <span class="underline"></span>
                                </label>
                                <p id="passwordError1" style="color: red; display: none;">Password setidaknya harus 8 karakter.</p>
                            </div>

                            <div class="form-group">
                                <label for="" class="form-input">
                                    <input type="password" id="password2"name="password_confirmation" required  />
                                    <span class="label">Konfirmasi Password</span>
                                    <span class="underline"></span>
                                </label>
                                <p id="passwordError2" style="color: red; display: none;">Password tidak sesuai.</p>
                            </div>

                            <div class="bottom text-center" >
                                <button type="submit" class="btn btn-primary mx-auto">
                                    Daftar
                                </button>
                            </div>    
                        </form>
                        <div class="col-md-6 float-right" style="margin-bottom: 10px;">
                            <p><a href="{{ url('/login') }}">Sudah Punya Akun? Login</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password1');
            const confirmPasswordInput = document.getElementById('password2');
            const passwordError1 = document.getElementById('passwordError1');
            const passwordError2 = document.getElementById('passwordError2');

            function validatePassword1() {
                if (passwordInput.value.length < 8) {
                    passwordError1.style.display = 'block';
                    passwordInput.setCustomValidity("Password must be at least 8 characters long.");
                } else {
                    passwordError1.style.display = 'none';
                    passwordInput.setCustomValidity('');
                }
            }

            function validatePassword2() {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    passwordError2.style.display = 'block';
                    confirmPasswordInput.setCustomValidity("Passwords don't match");
                } else {
                    passwordError2.style.display = 'none';
                    confirmPasswordInput.setCustomValidity('');
                }
            }

            passwordInput.addEventListener('keyup', validatePassword1);
            confirmPasswordInput.addEventListener('keyup', validatePassword2);
        });
    </script>

@endsection
@error('email')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror
