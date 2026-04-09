@extends('template.master')
@section('title', 'SPP | Rekam Jejak')
@section('header')
<link rel="stylesheet" href="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/css/fileinput.min.css">
@endsection
@section('konten')
<?php
$hakakses = Session::get('hak_akses');
?>

<div class="main">
	<div class="main-content">
		<div class="container-fluid">
			<h3 class="page-title">Rekam Jejak</h3>
			<div class="row">
				<div class="col-md-12">
                <div class="panel">
                    <ul class="timeline" id="rekam_jejak_body" style="margin: 20px">
						@foreach($rekam_jejak as $key => $data)
							@if(($data->master_user_id != 2 || $data->master_user_id == 99) && $data->rekam_jejak_status == 0)
								<li>
									<div class="timeline-badge info"><i class="glyphicon glyphicon-plus"></i></div>
									<div class="timeline-panel">
									<div class="timeline-heading">
										<h4 class="timeline-title"><strong>{{$data->asal}}</strong></h4>
									<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i> {{$data->rekam_jejak_waktu}}</small></p>
									</div>
									<div class="timeline-body">
										<p>{{ $data->user_name_asal }} Membuat PP Baru</p>
									</div>
									</div>
								</li>
							@elseif($data->rekam_jejak_status == 1 )
								@if($data->master_user_id == 2)
								<li class="timeline-inverted">
									<div class="timeline-badge info"><i class="glyphicon glyphicon-send"></i></div>
									<div class="timeline-panel">
									<div class="timeline-heading">
										<h4 class="timeline-title"><strong>{{$data->asal}}</strong></h4>
									<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i> {{$data->rekam_jejak_waktu}}</small></p>
									</div>
									<div class="timeline-body">
										<p>{{ $data->user_name_asal }} Mengirim PP ke kasub_bagian</p>
									</div>
									</div>
								</li>
								@else
								<li class="timeline-inverted">
								<div class="timeline-badge info"><i class="glyphicon glyphicon-send"></i></div>
									<div class="timeline-panel">
									<div class="timeline-heading">
										<h4 class="timeline-title"><strong>{{$data->asal}}</strong></h4>
									<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i> {{$data->rekam_jejak_waktu}}</small></p>
									</div>
									<div class="timeline-body">
										<p> {{ $data->user_name_asal }} Mengirim PP ke {{$data->tujuan}}</p>
									</div>
									</div>
								</li>
								@endif
							@elseif($data->rekam_jejak_status == 6)
								<li class="timeline">
									<div class="timeline-badge success"><i class="glyphicon glyphicon-ok"></i></div>
									<div class="timeline-panel">
									<div class="timeline-heading">
										<h4 class="timeline-title"><strong>{{$data->tujuan}}</strong></h4>
										<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i>{{$data->rekam_jejak_waktu}}</small></p>
									</div>
									<div class="timeline-body">
										<p> {{ $data->user_name_asal }} Menerima PP yang masuk</p>
									</div>
									</div>
							@elseif( $data->rekam_jejak_status == 33)
								@if($hakakses == 2)
									@if( $data->asal == "Petugas Penerima"){
										<li>
										<div class="timeline-badge warning"><i class="glyphicon glyphicon-pencil"></i></div>
										<div class="timeline-panel">
											<div class="timeline-heading">
												<h4 class="timeline-title"><strong>{{$data->asal}}</strong></h4>
												<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i>{{$data->rekam_jejak_waktu}}</small></p>
											</div>
											<div class="timeline-body">
												<p>{{ $data->user_name_asal }} Mengembalikan ke Bagian</p>
												<p>Revisi Oleh : {{$data->asal}}</p>
												<h5><strong style="color: red">Keterangan Revisi :</strong><br><span>{{$data->rekam_jejak_revisi}}</span></h5>
											</div>
										</div>
										</li>
									@else
										<li>
										<div class="timeline-badge warning"><i class="glyphicon glyphicon-pencil"></i></div>
										<div class="timeline-panel">
											<div class="timeline-heading">
												<h4 class="timeline-title"><strong>{{$data->asal}}</strong></h4>
												<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i>{{$data->rekam_jejak_waktu}}</small></p>
											</div>
											<div class="timeline-body">
												<p>{{ $data->user_name_asal }} Mengembalikan PP</p>
												<p>Revisi Oleh : {{$data->asal}}</p>
												<h5><strong style="color: red">Keterangan Revisi :</strong><br><span>{{$data->rekam_jejak_revisi}}</span></h5>
											</div>
										</div>
										</li>
									@endif

								@else
									@if($data->asal == "Petugas Penerima")
										<li class="timeline-inverted">
											<div class="timeline-badge warning"><i class="glyphicon glyphicon-pencil"></i></div>
											<div class="timeline-panel">
											<div class="timeline-heading">
												<h4 class="timeline-title"><strong>{{$data->asal}}</strong></h4>
												<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i>{{$data->rekam_jejak_waktu}}</small></p>
											</div>
											<div class="timeline-body">
												<p>{{ $data->user_name_asal }} Mengembalikan ke Bagian</p>
												<h5><strong style="color: red">Keterangan Revisi :</strong><br><span>{{$data->rekam_jejak_revisi}}</span></h5>
											</div>
											</div>
										</li>
									@else
										<li class="timeline-inverted">
											<div class="timeline-badge warning"><i class="glyphicon glyphicon-pencil"></i></div>
											<div class="timeline-panel">
												<div class="timeline-heading">
												<h4 class="timeline-title"><strong>{{$data->asal}}</strong></h4>
												<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i>{{$data->rekam_jejak_waktu}}</small></p>
												</div>
												<div class="timeline-body">
												<p>{{ $data->user_name_asal }} Mengembalikan PP</p>
												<h5><strong style="color: red">Keterangan Revisi :</strong><br><span>{{$data->rekam_jejak_revisi}}</span></h5>
												</div>
											</div>
										</li>
									@endif
								@endif
							@elseif($data->rekam_jejak_status == 2)
								<li class="timeline-inverted">
									<div class="timeline-badge danger"><i class="glyphicon glyphicon-usd"></i></div>
									<div class="timeline-panel">
									<div class="timeline-heading">
										<h4 class="timeline-title"><strong>{{$data->asal}}</strong></h4>
										<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i>{{$data->rekam_jejak_waktu}}</small></p>
									</div>
									<div class="timeline-body">
										<p>{{ $data->user_name_asal }} Melakukan pembayaran SPPb</p>
									</div>
									</div>
								</li>
							@elseif($data->rekam_jejak_status == 3)
								<li class="timeline-inverted">
									<div class="timeline-badge danger"><i class="glyphicon glyphicon-credit-card"></i></div>
									<div class="timeline-panel">
									<div class="timeline-heading">
										<h4 class="timeline-title"><strong>{{$data->asal}}}</strong></h4>
										<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i>{{$data->rekam_jejak_waktu}}</small></p>
									</div>
									<div class="timeline-body">
										<p>{{ $data->user_name_asal }} Penerimaan SPPn</p>
									</div>
									</div>
								</li>
							@elseif($data->rekam_jejak_status == 4)
								<li class="timeline">
									<div class="timeline-badge info"><i class="glyphicon glyphicon-check"></i></div>
									<div class="timeline-panel">
									<div class="timeline-heading">
										<h4 class="timeline-title"><strong>{{$data->asal}}</strong></h4>
										<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i>{{$data->rekam_jejak_waktu}}</small></p>
									</div>
									<div class="timeline-body">
										<p>{{ $data->user_name_asal }} Penyelesaian PP</p>
									</div>
									</div>
								</li>
							@elseif($data->rekam_jejak_status == 5)
								<li class="timeline">
									<div class="timeline-badge danger"><i class="glyphicon glyphicon-remove"></i></div>
									<div class="timeline-panel">
									<div class="timeline-heading">
										<h4 class="timeline-title"><strong>{{$data->asal}}</strong></h4>
										<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i>{{$data->rekam_jejak_waktu}}</small></p>
									</div>
									<div class="timeline-body">
										<p>{{ $data->user_name_asal }} Pembatalan PP</p>
									</div>
									</div>
								</li>
							@endif
						@endforeach
					</ul>
                </div>
				</div>
			</div>
		</div>
    </div>
</div>


<!-- Javascript -->
<script type="text/javascript">
</script>
<!-- End Javascript -->
            
@endsection
@section('footer')
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/piexif.min.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/sortable.min.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/fileinput.min.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/themes/fa/theme.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/locales/id.js"></script>
@endsection
