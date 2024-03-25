@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="panel">
                    <div class="panel-heading">Login Anggota</div>
                    <div class="panel-body" style="margin-bottom: 10px;">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/loginanggota') }}">
                            {{ csrf_field() }}
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="" class="form-input">
                                    <input type="email" name="email" value="{{ old('email') }}"  required autofocus="true" required />
                                    <span class="label">Email</span>
                                    <span class="underline"></span>
                                </label>
                            </div>

                            <div class="form-group">
                                <label for="" class="form-input">
                                    <input type="password" autofocus="true" required />
                                    <span class="label">Password</span>
                                    <span class="underline"></span>
                                </label>
                            </div>

                            <div class="bottom text-center" >
                                <button type="submit" class="btn btn-primary mx-auto">
                                    Login
                                </button>
                            </div>    
                        </form>
                              <div class="col-md-6 float-left" style="margin-bottom: 10px;">
                                     <p><a href="{{ url('/password/reset') }}">lupa Password?</a></p>
                             </div>
                             <div class="col-md-6 float-right" style="margin-bottom: 10px;">
                                  <p>Belum Punya Akun? <a href="{{ url('/daftar') }}">Mendaftar</a></p>
                              </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
