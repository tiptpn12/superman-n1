<!doctype html>
<html lang="en" class="fullscreen-bg">

<head>
    <title>Reset Password</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- VENDOR CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('') }}assets/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('') }}assets/vendor/linearicons/style.css">
    <!-- MAIN CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/css/main.css">
    <!-- FOR DEMO PURPOSES ONLY. You should remove this in your project -->
    <link rel="stylesheet" href="{{ asset('') }}assets/css/demo.css">
    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    <!-- ICONS -->
    <link rel="icon" sizes="76x76" href="{{ asset('') }}assets/img/ptpn3.png">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('') }}assets/img/ptpn3.png">
    <script src="{{ asset('') }}assets/vendor/jquery/jquery.min.js"></script>
    <style>
        .auth-box {
            height: 80%;
        }
    </style>
</head>

<body>
    <!-- WRAPPER -->
    <div id="wrapper">
        <div class="vertical-align-wrap">
            <div class="vertical-align-middle">
                <div class="auth-box ">
                    <div class="left">
                        <div class="content">
                            <div class="header">
                                <div class="logo text-center"><img src="{{ asset('') }}assets/img/ptpn3.png"
                                        alt="Klorofil Logo" style="height: 80px;"></div>

                                <p class="lead">Reset your password</p>
                            </div>
                            @if (\Session::has('alert'))
                            <div class="alert alert-danger">
                                <div>{{ Session::get('alert') }}</div>
                            </div>
                            @endif
                            @if (\Session::has('alert-success'))
                            <div class="alert alert-success">
                                <div>{{ Session::get('alert-success') }}</div>
                            </div>
                            @endif
                            <form class="form-auth-small" action="{{ route('reset_password_post') }}" method="post"
                                enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="email" value="{{ $email }}">
                                <input type="hidden" name="token" value="{{ $token }}">

                                <div class="form-group">
                                    <label for="password" class="control-label sr-only">New Password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Enter new password">
                                </div>
                                @if ($errors->has('password'))
                                <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif

                                <div class="form-group">
                                    <label for="password_confirmation" class="control-label sr-only">Confirm
                                        Password</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Confirm your new password">
                                </div>
                                @if ($errors->has('password_confirmation'))
                                <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                                @endif

                                <button type="submit" class="btn btn-primary btn-lg btn-block">Reset Password</button>
                                <div class="bottom">
                                    <span class="helper-text"><i class="fa fa-arrow-left"></i> <a
                                            href="{{ route('login') }}">Back to Login</a></span>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="right" style="background:none">
                        <div class="overlay"></div>
                        <div class="content text">
                            <div class="row text-center">
                                <img src="{{ asset('') }}assets/img/ptpn1.png" alt="" height="60px"
                                    style="margin: 15px;">
                                <img src="{{ asset('') }}assets/img/ptpn2.png" alt="" height="40px"
                                    style="margin: 15px;">
                                <img src="{{ asset('') }}assets/img/ptpn3.png" alt="" height="40px"
                                    style="margin: 15px;">
                                <img src="{{ asset('') }}assets/img/ptpn4.png" alt="" height="50px"
                                    style="margin: 15px;">
                                <img src="{{ asset('') }}assets/img/ptpn5.png" alt="" height="40px"
                                    style="margin: 15px;">
                            </div>
                            <div class="row text-center">
                                <img src="{{ asset('') }}assets/img/ptpn6.png" alt="" height="40px"
                                    style="margin: 5px;">
                                <img src="{{ asset('') }}assets/img/ptpn7.png" alt="" height="30px"
                                    style="margin: 5px;">
                                <img src="{{ asset('') }}assets/img/ptpn8.png" alt="" height="50px"
                                    style="margin: 5px;">
                                <img src="{{ asset('') }}assets/img/ptpn9.png" alt="" height="20px"
                                    style="margin: 5px;">
                                <img src="{{ asset('') }}assets/img/ptpn10.png" alt="" height="60px"
                                    style="margin: 5px;">
                            </div>
                            <div class="row text-center">
                                <img src="{{ asset('') }}assets/img/ptpn11.png" alt="" height="40px"
                                    style="margin: 15px;">
                                <img src="{{ asset('') }}assets/img/ptpn12.png" alt="" height="40px"
                                    style="margin: 15px;">
                                <img src="{{ asset('') }}assets/img/ptpn13.png" alt="" height="40px"
                                    style="margin: 15px;">
                                <img src="{{ asset('') }}assets/img/ptpn14.png" alt="" height="40px"
                                    style="margin: 15px;">
                            </div>
                            <h2>Selamat Datang!</h2>
                            <h3>di Aplikasi Pembayaran dan Penerimaan<br><br> PTPN Group</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END WRAPPER -->
    </div>
</body>

</html>
