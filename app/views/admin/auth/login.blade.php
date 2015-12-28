@extends('admin.auth._auth-actions')

@section('js-init')
<script type="text/javascript" src="{{ asset('assets/admin/dist/pages/login.js') }}"></script>
@endsection

@section('content')
    <!-- BEGIN LOGIN FORM -->
    <form class="login-form active" action="{{ asset('admin/auth/login') }}" method="post" accept-charset="utf-8">
        {!! csrf_field() !!}
        <h3 class="form-title font-green">Sign In</h3>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Username</label>
            <input class="form-control form-control-solid placeholder-no-fix" type="text" required autocomplete="off" placeholder="Email" name="username" />
        </div>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Password</label>
            <input class="form-control form-control-solid placeholder-no-fix" type="password" required autocomplete="off" placeholder="Password" name="password" />
        </div>
        <div class="form-actions">
            <button type="submit" class="btn green uppercase">Login</button>
            <a href="javascript:;" id="forget-password" class="forget-password">Forgot Password?</a>
        </div>
        <div class="create-account">
            <p>
                <a href="javascript:;" id="register-btn" class="uppercase">Register</a>
            </p>
        </div>
    </form>
    <!-- END LOGIN FORM -->
@endsection