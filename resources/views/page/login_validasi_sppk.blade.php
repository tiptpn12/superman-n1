<!doctype html>
<html lang="en" class="fullscreen-bg">

<head>
	<title>Login | Superman</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<!-- VENDOR CSS -->
	<link rel="stylesheet" href="{{asset('')}}assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="{{asset('')}}assets/vendor/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="{{asset('')}}assets/vendor/linearicons/style.css">
	<!-- MAIN CSS -->
	<link rel="stylesheet" href="{{asset('')}}assets/css/main.css">
	<!-- FOR DEMO PURPOSES ONLY. You should remove this in your project -->
	<link rel="stylesheet" href="{{asset('')}}assets/css/demo.css">
	<!-- GOOGLE FONTS -->
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
	<!-- ICONS -->
	<link rel="icon" sizes="76x76" href="{{asset('')}}assets/img/logo-ptpn.png">
	<link rel="icon" type="image/png" sizes="96x96" href="{{asset('')}}assets/img/logo-ptpn.png">
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
								<!-- <div class="logo text-center"><img src="{{asset('')}}assets/img/logo-ptpn.png" alt="Klorofil Logo"></div> -->
								<div class="logo text-center"><img src="{{asset('')}}assets/img/logo-ptpn-spp.png" alt="Klorofil Logo" style="height:60px;"></div>
								<p class="lead">Login to your account</p>
							</div>
							@if(\Session::has('alert'))
                				<div class="alert alert-danger">
                    			<div>{{Session::get('alert')}}</div>
                				</div>
            				@endif
            				@if(\Session::has('alert-success'))
                				<div class="alert alert-success">
                    			<div>{{Session::get('alert-success')}}</div>
                				</div>
            				@endif
							<form class="form-auth-small" action="{{route('loginvalidasisppkpost',['id' => $id])}}" method="post" enctype="multipart/form-data">
							{{ csrf_field() }}
								<div class="form-group">
									<label for="signin-username" class="control-label sr-only">Username</label>
									<input type="text" class="form-control" id="signin-username"  name="username" placeholder="username">
								</div>
								<div class="form-group">
									<label for="signin-password" class="control-label sr-only">Password</label>
									<input type="password" class="form-control" id="signin-password" name="password" placeholder="password">
								</div>
								<div class="form-group clearfix">
									<label class="fancy-checkbox element-left">
										<input type="checkbox">
										<span>Remember me</span>
									</label>
								</div>
								<button type="submit" class="btn btn-primary btn-lg btn-block">LOGIN</button>
								<div class="bottom">
									<span class="helper-text"><i class="fa fa-lock"></i> <a href="#">Forgot password?</a></span>
								</div>
							</form>
						</div>
					</div>
					<div class="right">
						<div class="overlay"></div>
						<div class="content text">
							<h2>Selamat Datang!</h2>
							<h1>di Aplikasi Monitoring SPP<br> PT Perkebunan Nusantara XII</h1>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
	<!-- END WRAPPER -->
</body>

</html>
