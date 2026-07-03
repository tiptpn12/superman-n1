<!doctype html>
<html lang="en" class="fullscreen-bg">

<head>
    <title>Login </title>
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
    <link rel="icon" sizes="76x76" href="{{ asset('') }}assets/img/ptpn1.png">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('') }}assets/img/ptpn1.png">
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
                                <div class="logo text-center"><img src="{{ asset('') }}assets/img/ptpn1.png"
                                        alt="Klorofil Logo" style="height: 80px;"></div>

                                <p class="lead">Login to your account</p>
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
                            <form class="form-auth-small" action="{{ route('loginpost') }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="signin-username" class="control-label sr-only">Username</label>
                                    <input type="text" class="form-control" id="signin-username" name="username"
                                        placeholder="username">

                                </div>
                                <div class="form-group">
                                    <label for="signin-password" class="control-label sr-only">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="signin-password" name="password"
                                            placeholder="password">
                                        <span class="input-group-addon" id="togglePassword" style="cursor: pointer;">
                                            <i class="fa fa-eye" id="eyeIcon"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group element-left">
                                    <div class="captcha">
                                        <span>{!! captcha_img('math') !!}</span>
                                        <button type="button" class="btn btn-danger reload" id="reload">
                                            &#x21bb;
                                        </button>
                                        <!-- <span class="text-danger" role="alert">
                                            <strong>Silahkan Verifikasi Captcha Terlebih Dahulu</strong>
                                        </span> -->
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="captcha" name="captcha"
                                        placeholder="captcha">
                                </div>
                                @if ($errors->has('required'))
                                <span class="text-danger">{{ $errors->first('required') }}</span>
                                @endif
                                @if ($errors->has('captcha'))
                                <span class="text-danger">{{ $errors->first('captcha') }}</span>
                                @endif
                                <div class="form-group clearfix">
                                    <label class="fancy-checkbox element-left">
                                        <input type="checkbox">
                                        <span>Remember me</span>
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg btn-block">LOGIN</button>
                                <div class="bottom">
                                    <span class="helper-text"><i class="fa fa-lock"></i> <a
                                            href="{{ route('forgot_password') }}">Forgot
                                            password?</a></span>
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
        <script>
            const reloadButton = document.getElementById("reload");
            reloadButton.addEventListener("click", function() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('reloadcaptcha') }}",
                    success: function(data) {
                        $(".captcha span").html(data.captcha)
                    }
                });
            });
        </script>
        <script>
            $(document).ready(function() {
        		// Ambil waktu unlock dari session
        		const unlockTime = {{ session('unlock_time', 0) }};
        		const currentTime = Math.floor(Date.now() / 1000); // waktu saat ini dalam detik

        		if (unlockTime > currentTime) {
        			const remainingTime = unlockTime - currentTime;

        			// Tampilkan countdown di elemen tertentu
        			const countdownElement = $('#countdown');
        			countdownElement.text(`Anda telah gagal login lebih dari 4 kali. Silakan coba lagi dalam ${remainingTime} detik.`);
        			countdownElement.css({
                        'color': '#721c24',
                        'background-color': '#f8d7da',
                        'border': '1px solid #f5c6cb',
                        'padding': '10px',
                        'border-radius': '5px',
                        'display': 'block',
                        'margin-top': '10px',
        				'margin-bottom': '10px'
                    });
        			// Countdown logic
        			let countdown = remainingTime;
        			const interval = setInterval(() => {
        				countdown--;
        				countdownElement.text(`Anda telah gagal login lebih dari 4 kali. Silakan coba lagi dalam ${countdown} detik.`);

        				if (countdown <= 0) {
        					clearInterval(interval);
        					countdownElement.text("Anda dapat mencoba login kembali.");
        				}
        			}, 1000);
        		}

                // Password visibility (hold to view)
                $('#togglePassword').on('mousedown touchstart', function(e) {
                    e.preventDefault();
                    $('#signin-password').attr('type', 'text');
                    $('#eyeIcon').removeClass('fa-eye').addClass('fa-eye-slash');
                }).on('mouseup mouseleave touchend', function() {
                    $('#signin-password').attr('type', 'password');
                    $('#eyeIcon').removeClass('fa-eye-slash').addClass('fa-eye');
                });
        	});
        </script>
</body>

</html>