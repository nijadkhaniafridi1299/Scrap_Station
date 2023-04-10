@extends('dashboard')
@section('content')
<main class="login-form">
    <div class="cotainer">
        <img src="{{ asset("/assets/img/wave.png")}}" alt="" style="width:50%; height:100%;">
        <div class="row d-flex justify-content-center align-items-center" style="margin-left:30%; margin-top:-40%;">
            <div class="col-md-4">
                <div class="card" style="width:130%;">
                    <h3 class="card-header text-center">Welcome to Scrap-Station</h3>
                    <div class="card-body p-5 text-center">
                        <form method="POST" action="{{ route('login.custom') }}">
                            @csrf
                            <div class="form-group mb-3 ">
                                <input type="text" placeholder="Email" id="email" class="form-control" name="email" required
                                    autofocus>
                                @if ($errors->has('email'))
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <input type="password" placeholder="Password" id="password" class="form-control" name="password" required>
                                @if ($errors->has('password'))
                                <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                                <input type="hidden" id="fcmtoken" name="fcmtoken" class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <div class="checkbox">
                                    {{-- <label>
                                        <input type="checkbox" name="remember"> Remember Me
                                    </label> --}}
                                </div>
                            </div>
                            <div class="d-grid mx-auto">
                                <button type="submit" class="btn btn-dark btn-block">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

<script src="{{ asset('/assets/js/jquery-3.6.3.js') }}"></script>

<!-- ======= Header ======= -->
@include('admin.scrapstation_firebase')
<!-- End Header -->