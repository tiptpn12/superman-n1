@extends('template.master')
@section('title', 'SPP Khusus | Edit SPP')

@section('header')
<link rel="stylesheet" href="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/css/fileinput.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

@endsection

@section('konten')
<?php 
$hakakses = Session::get('hak_akses');
?>
<style>
  .preloader 
{
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url('{{asset('')}}assets/Ajux_loader.gif') 50% 50% no-repeat rgb(249,249,249);
  background-size: 200px 200px;
}
</style>
<script>
  $(window).load(function() 
  {          
        $("#preloaders").fadeOut(1000);
    });
</script>
<div id="preloaders" class="preloader"></div>
<!-- MAIN -->
<div class="main">
	<!-- MAIN CONTENT -->
	<div class="main-content">
	
		<form action="{{route('updatesppk',['id' => $spp_id])}}" id="form_spp" method="post" target="" enctype="multipart/form-data">
	
		{{csrf_field()}}
			<div class="container-fluid">
				<h3 class="page-title">Edit SPP Keuangan</h3>
				<div class="row">
					<div class="col-md-12">
						<!-- FORM SPP -->
						<div class="panel">
							<div class="panel-heading">
								<h3 class="panel-title">Form SPP</h3>
							</div>
							<input type="hidden" id="formspp" value="{{$formspp}}">
							<div class="panel-body">
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">Jalur</label>
								<div class="col-sm-10">
								@if ($spp->spp_jalur_pajak == 0)
								Tidak Melalui Pajak dan MIRO
								@else
								Melalui Pajak dan MIRO
								@endif
								</div>
							</div>
								
								<div class="form-group row" id="panel_jenis_form" style="display: none">
									<label class="col-sm-2 col-form-label">Jenis Form</label>
									<div class="col-sm-10">
										<select class="form-control" id="jenis_form" name="jenis_form">
											<option value="" disabled selected>-- Pilih Jenis Form --</option>
											<option value="sppb">SPPb Saja</option>
											<option value="sppn">SPPn Saja</option>
											<option value="sppb_sppn">SPPb dan SPPn</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<!-- END FORM SPP -->
						<div class="panel" id="panel_sppb_sppn" style="display:none">
							<div class="panel-body">
								<div class="form-group row" >  
									<label class="col-sm-2 col-form-label" id="label_kwitansi_spp">Kwitansi *</label>
									<div class="col-sm-10">
										@if(isset($sppb['sppb_kwitansi']))
										<input type="text" id="kwitansi_spp" name="kwitansi" class="form-control" placeholder="Kwitansi" autocomplete="off" value="{{$sppb['sppb_kwitansi']}}">
										@else
										<input type="text" id="kwitansi_spp" name="kwitansi" class="form-control" placeholder="Kwitansi" autocomplete="off" value="">

										@endif
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2 col-form-label">Referensi</label>
									<div class="col-sm-10">
									@if(isset($sppb['sppb_referensi']))
										<input type="text" id="referensi_spp" name="referensi" class="form-control" placeholder="Referensi" autocomplete="off" value="{{$sppb['sppb_referensi']}}">
									@else
										<input type="text" id="referensi_spp" name="referensi" class="form-control" placeholder="Referensi" autocomplete="off">
									@endif
									</div>
								</div>
								

								<div id="fp_spp">
											@if(isset($sppb[1][0]))
											<input type="hidden" id="jumlah_faktur_pajak_spp" value="{{count($sppb[1])}}">
													<div id="faktur_pajak_spp_1">
														<div class="form-group row" id="form_faktur_pajak_spp">
															<label class="col-sm-2 col-form-label">Faktur Pajak *</label>
															<div class="col-sm-8">
																<input type="text" id="faktur_pajak_spp" name="faktur_pajak_spp[1][fp]" class="form-control validate_spp_all" placeholder="Faktur Pajak SPP 1" autocomplete="off" value="{{$sppb[1][0]->faktur_pajak_nomor}}">
															</div>
															<div class="col-sm-2" id="btn_faktur_pajak_spp_1">
																<button type="button" class="btn btn-success btn-sm" onclick="tambah_faktur_pajak_spp(1)">+</button>									
															</div>
														</div>
													</div>
												@for($i=1; $i< count($sppb[1]); $i++)
													<div id="faktur_pajak_spp_{{$i+1}}">
														<div class="form-group row" id="form_faktur_pajak_spp">
															<label class="col-sm-2 col-form-label"></label>
															<div class="col-sm-8">
																<input type="text" id="faktur_pajak_spp" name="faktur_pajak_spp[{{$i+1}}][fp]" class="form-control validate_spp_all" placeholder="Faktur Pajak SPP {{$i+1}}" autocomplete="off" value="{{$sppb[1][$i]->faktur_pajak_nomor}}">
															</div>
															<div class="col-sm-2" id="btn_faktur_pajak_spp_{{$i+1}}">
																<button type="button" class="btn btn-success btn-sm" onclick="tambah_faktur_pajak_spp({{$i+1}})">+</button>
																<button type="button" class="btn btn-danger btn-sm" onclick="hapus_faktur_pajak_spp({{$i+1}})">+</button>									
															</div>
														</div>
													</div>
												@endfor
											@endif
										</div>
								<div class="form-group row">
									<label class="col-sm-2 col-form-label">No. Kontrak *</label>
									<div class="col-sm-10">
									@if(isset($sppb['sppb_sp_opl']))
									<input type="text" id="sp_opl_spp" name="sp_opl" class="form-control validate_spp_all" placeholder="Nomor Kontrak" autocomplete="off" value="{{$sppb['sppb_sp_opl']}}">
									@else
									<input type="text" id="sp_opl_spp" name="sp_opl" class="form-control validate_spp_all" placeholder="Nomor Kontrak" autocomplete="off">
									@endif
									</div>
								</div>
							</div>
						</div>
						<!-- FORM SPPB -->
						<div class="panel" id="panel_sppb" style="display: none" >
							<div class="panel-heading">
								Form SPPb
							</div>
							<div class="panel-body">

								<div class="custom-tabs-line tabs-line-bottom left-aligned">
									<ul class="nav" role="tablist">
										<li class="active"><a href="#tab-informasi-sppb" role="tab" data-toggle="tab">Informasi</a></li>
										<li><a href="#tab-isi-sppb" role="tab" data-toggle="tab">Isi</a></li>
									</ul>
								</div>
								<div class="tab-content">

									<!-- TAB INFORMASI -->
									<div class="tab-pane fade in active" id="tab-informasi-sppb">
									<div class="form-group row">
											<label class="col-sm-2 col-form-label">Tanggal*</label>
											<div class="col-sm-10">
											@if(isset($sppb['sppb_tanggal']))
												<input type="text" id="tanggal_sppb" name="tanggal_sppb" class="form-control validate_sppb validate_spp_all date " placeholder="Tanggal SPPb" value="{{date('d-m-Y',strtotime($sppb['sppb_tanggal']))}}" autocomplete="off" required>
											@endif
											</div>
										</div>
										<div class="form-group row">
										<label class="col-md-2" >Nomor SPP *</label>
										<div class="col-md-10" style="display:inline-flex">
											@if(isset($sppb['master_bagian_kode']))
												<input style="width:50px" type="text" id="kode_bagian_sppb" name="kode_bagian_sppb" class="form-control validate_sppb validate_spp_all" placeholder="Kode Bagian" value="{{$sppb['master_bagian_kode']}}" autocomplete="off" readonly required>/
											@endif
											@if(isset($sppb['sppb_urutan']))
												<input style="width:50px" type="text" id="urutan_sppb" name="urutan_sppb" class="form-control validate_sppb validate_spp_all" placeholder="Nomor Urut SPPb" value="{{$sppb['sppb_urutan']}}" autocomplete="off" readonly required>/
											@endif
											@if(isset($sppb['sppb_bulan']))
												<input style="width:50px" type="text" id="bulan_sppb" name="bulan_sppb" class="form-control validate_sppb validate_spp_all" placeholder="Bulan SPPb" value="{{$sppb['sppb_bulan']}}" autocomplete="off"  readonly required>/
											@endif
											@if(isset($sppb['sppb_tahun']))
												<input style="width:70px" type="text" id="tahun_sppb" name="tahun_sppb" class="form-control validate_sppb validate_spp_all" placeholder="Tahun SPPb" value="{{$sppb['sppb_tahun']}}" autocomplete="off" readonly required>
											@endif
											</div>
										</div>
										<div class="form-group row" id="form_kwitansi_sppb">
											<label class="col-sm-2 col-form-label">Kwitansi*</label>
											<div class="col-sm-10">
											@if(isset($sppb['sppb_kwitansi']))
												<input type="text" id="kwitansi_sppb" name="kwitansi_sppb" class="form-control validate_sppb validate_spp_all" placeholder="Kwitansi SPPb" value="{{$sppb['sppb_kwitansi']}}" autocomplete="off">
											@else
												<input type="text" id="kwitansi_sppb" name="kwitansi_sppb" class="form-control validate_sppb validate_spp_all" placeholder="Kwitansi SPPb"  autocomplete="off">
											@endif
											</div>
										</div>
										<div class="form-group row" id="form_referensi_sppb">
											<label class="col-sm-2 col-form-label">Referensi*</label>
											<div class="col-sm-10">
											@if(isset($sppb['sppb_referensi']))
												<input type="text" id="referensi_sppb" name="referensi_sppb" class="form-control" placeholder="Referensi SPPb" value="{{$sppb['sppb_referensi']}}" autocomplete="off">
											@else
												<input type="text" id="referensi_sppb" name="referensi_sppb" class="form-control" placeholder="Referensi SPPb" autocomplete="off">
											@endif
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">AU.53*</label>
											<div class="col-sm-10">
											@if(isset($sppb['sppb_au_53']))
												<input type="text" id="au53_sppb" name="au53_sppb" class="form-control validate_sppb validate_spp_all" placeholder="AU. 53 SPPb" value="{{$sppb['sppb_au_53']}}" autocomplete="off">
											@else
												<input type="text" id="au53_sppb" name="au53_sppb" class="form-control validate_sppb validate_spp_all" placeholder="AU. 53 SPPb"  autocomplete="off">
											@endif
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Berita Acara*</label>
											<div class="col-sm-10">
											@if(isset($sppb['sppb_berita_acara']))
												<input type="text" id="berita_acara_sppb" name="berita_acara_sppb" class="form-control validate_sppb validate_spp_all" placeholder="Berita Acara SPPb" value="{{$sppb['sppb_berita_acara']}}" autocomplete="off">
											@else
												<input type="text" id="berita_acara_sppb" name="berita_acara_sppb" class="form-control validate_sppb validate_spp_all" placeholder="Berita Acara SPPb" autocomplete="off">
											@endif
											</div>
										</div>

										<div id="fp_sppb">
											@if(isset($sppb[1][0]))
											<input type="hidden" id="jumlah_faktur_pajak_sppb" value="{{count($sppb[1])}}">

													<div id="faktur_pajak_sppb_1">
														<div class="form-group row" id="form_faktur_pajak_sppb">
															<label class="col-sm-2 col-form-label">Faktur Pajak *</label>
															<div class="col-sm-8">
																<input type="text" id="faktur_pajak_sppb" name="faktur_pajak_sppb[1][fp]" class="form-control validate_sppb validate_spp_all" placeholder="Faktur Pajak SPPb 1" autocomplete="off" value="{{$sppb[1][0]->faktur_pajak_nomor}}">
															</div>
															<div class="col-sm-2" id="btn_faktur_pajak_sppb_1">
																<button type="button" class="btn btn-success btn-sm" onclick="tambah_faktur_pajak_sppb(1)">+</button>									
															</div>
														</div>
													</div>
												@for($i=1; $i< count($sppb[1]); $i++)
													<div id="faktur_pajak_sppb_{{$i+1}}">
														<div class="form-group row" id="form_faktur_pajak_sppb">
															<label class="col-sm-2 col-form-label"></label>
															<div class="col-sm-8">
																<input type="text" id="faktur_pajak_sppb" name="faktur_pajak_sppb[{{$i+1}}][fp]" class="form-control validate_sppb validate_spp_all" placeholder="Faktur Pajak SPPb 1" autocomplete="off" value="{{$sppb[1][$i]->faktur_pajak_nomor}}">
															</div>
															<div class="col-sm-2" id="btn_faktur_pajak_sppb_{{$i+1}}">
																<button type="button" class="btn btn-success btn-sm" onclick="tambah_faktur_pajak_sppb({{$i+1}})">+</button>
																<button type="button" class="btn btn-danger btn-sm" onclick="hapus_faktur_pajak_sppb({{$i+1}})">x</button>									

															</div>
														</div>
													</div>
												@endfor
											@endif
										</div>
										<div class="form-group row" id="form_sp_opl_sppb">
											<label class="col-sm-2 col-form-label">No. Kontrak*</label>
											<div class="col-sm-10">
											@if(isset($sppb['sppb_sp_opl']))
												<input type="text" id="sp_opl_sppb" name="sp_opl_sppb" class="form-control validate_sppb validate_spp_all" placeholder="Nomor Kontrak SPPb" value="{{$sppb['sppb_sp_opl']}}" autocomplete="off">
											@else
												<input type="text" id="sp_opl_sppb" name="sp_opl_sppb" class="form-control validate_sppb validate_spp_all" placeholder="Nomor Kontrak SPPb" autocomplete="off">
											@endif
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Bagian*</label>
											<div class="col-sm-10">
											@if(isset($sppb['master_bagian_id']))
												<select class="form-control validate_sppb validate_spp_all" id="bagian_sppb" name="bagian_sppb" readonly>
													<option value="" disabled>-- Pilih Bagian --</option>
													<option value="{{$sppb['master_bagian_id']}}" selected>{{$sppb['master_bagian_nama']}}</option>
												</select>
											@endif
											</div>
										</div>
										@if(isset($sppb['sppb_metode_pembayaran']))
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Metode Pembayaran*</label>
											<div class="col-sm-10">
												<select class="form-control validate_sppb validate_spp_all" id="metode_pembayaran_sppb" name="metode_pembayaran_sppb"  value="{{$sppb['sppb_metode_pembayaran']}}" required>
													@if($sppb['sppb_metode_pembayaran'] == 'kas')
													<option value="kas" selected>Kas</option>
													@else
													<option value="kas" >Kas</option>
													@endif
													@if($sppb['sppb_metode_pembayaran'] == 'karyawan')
													<option value="karyawan" selected>Karyawan</option>
													@else
													<option value="karyawan">Karyawan</option>
													@endif
													@if($sppb['sppb_metode_pembayaran'] == 'bank')
													<option value="bank" selected>Bank</option>
													@else
													<option value="bank">Bank</option>
													@endif
													@if($sppb['sppb_metode_pembayaran'] == 'kas_negara')
													<option value="kas_negara" selected>Kas Negara</option>
													@else
													<option value="kas_negara">Kas Negara</option>
													@endif
													@if($sppb['sppb_metode_pembayaran'] == 'skbdn')
													<option value="skbdn" selected>SKBDN</option>
													@else
													<option value="skbdn">SKBDN</option>
													@endif
												</select>
											</div>
										</div>
										@if($karyawan_sppb !== null)
										<input type="hidden" value="{{count($karyawan_sppb)}}" id="jumlah_karyawan_sppb">
										@else
										<input type="hidden" value="0" id="jumlah_karyawan_sppb">
										@endif
										@if($sppb['sppb_metode_pembayaran'] == "karyawan")
										<!-- KARYAWAN -->
											<div class="form-group row" style="display:block;" id="pilih_lampirkan_sppb" onclick="pilih_data_sppb()">
													<label class="col-sm-2 col-form-label"></label>
													<div class="col-sm-10">
													@if($sppb['sppb_data_metpen'] == "input_data")
															<div class="col-sm-2">
																<label class="fancy-radio">
																<input  name="pilih_data_sppb" id="input_data_sppb" value="input_data" type="radio" checked="checked"> 
																<span style="font-size:17px"><i ></i>Data diinputkan manual </span>
																</label>
															</div>
															<div class="col-sm-2">
																<label class="fancy-radio">
																<input  name="pilih_data_sppb" id="master_data_sppb" value="master_data" type="radio" > 
																<span style="font-size:17px"><i ></i>Data dari master </span>
																</label>
															</div>
															<div class="col-sm-2">
																<label class="fancy-radio">
																<input  name="pilih_data_sppb" id="lampirkan_data_sppb" value="lampirkan_data" type="radio"> 
																<span style="font-size:17px"><i ></i>Data dilampirkan</span>
																</label>
															</div>
															@elseif($sppb['sppb_data_metpen'] == "lampirkan_data")
															<div class="col-sm-2">
																<label class="fancy-radio">
																<input  name="pilih_data_sppb" id="input_data_sppb" value="input_data" type="radio" > 
																<span style="font-size:17px"><i ></i>Data diinputkan manual </span>
																</label>
															</div>
															<div class="col-sm-2">
																<label class="fancy-radio">
																<input  name="pilih_data_sppb" id="master_data_sppb" value="master_data" type="radio" > 
																<span style="font-size:17px"><i ></i>Data dari master </span>
																</label>
															</div>
															<div class="col-sm-2">
																<label class="fancy-radio">
																<input  name="pilih_data_sppb" id="lampirkan_data_sppb" value="lampirkan_data" type="radio" checked="checked" > 
																<span style="font-size:17px"><i ></i>Data dilampirkan</span>
																</label>
															</div>
															@elseif($sppb['sppb_data_metpen'] == "master_data")
															<div class="col-sm-2">
																<label class="fancy-radio">
																<input  name="pilih_data_sppb" id="input_data_sppb" value="input_data" type="radio" > 
																<span style="font-size:17px"><i ></i>Data diinputkan manual </span>
																</label>
															</div>
															<div class="col-sm-2">
																<label class="fancy-radio">
																<input  name="pilih_data_sppb" id="master_data_sppb" value="master_data" type="radio"  checked="checked"> 
																<span style="font-size:17px"><i ></i>Data dari master </span>
																</label>
															</div>
															<div class="col-sm-2">
																<label class="fancy-radio">
																<input  name="pilih_data_sppb" id="lampirkan_data_sppb" value="lampirkan_data" type="radio"> 
																<span style="font-size:17px"><i ></i>Data dilampirkan</span>
																</label>
															</div>
															@endif
													</div>
												</div>
											<div id="bank_sppb" style="display:none">
												<input type="hidden" id="id_bank_sppb_1" name="id_bank_sppb" class="form-control" onclick="data_bank_sppb(1)" placeholder="Nama Rekening Bank" autocomplete="off" required>												
												<div class="form-group row" id="atas_nama_vendor_sppb">
													<label class="col-sm-2 col-form-label">Atas Nama *</label>
													<div class="col-sm-10">
														<input type="text" id="atas_nama_bank_sppb_vendor" name="atas_nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" placeholder="Atas Nama Bank SPPb" autocomplete="off">
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nama Bank *</label>
													<div class="col-sm-10">
													<input type="text" id="nama_bank_sppb_vendor" name="nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" placeholder="Nama Bank" autocomplete="off">												
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
													<div class="col-sm-10">
														<input type="text" id="rekening_bank_sppb_vendor" name="rekening_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
													</div>
												</div>
												
											</div>
											@if($sppb['sppb_data_metpen'] == "input_data")
											<!-- KARYAWAN INPUT MANUAL -->
											<div id="bank_sppb_karyawan" style="display:none">
												<div id="bank_sppb_karyawan_input" >
													@foreach($karyawan_sppb as $v => $k)	
													<div id="bank_karyawan_sppb_{{$v+1}}">
														<div class="form-group row" id="atas_nama_karyawan_sppb">
															<label class="col-sm-2 col-form-label">Atas Nama Rekening {{$v+1}}*</label>
															<div class="col-sm-8">
																<input type="text" id="atas_nama_bank_sppb_karyawan_input_{{$v+1}}" name="karyawan_sppb_input[{{$v+1}}][nama]" class="form-control"  placeholder="Atas Nama Bank SPPb" value="{{$k->karyawan_nama}}" autocomplete="off">						
								
															</div>
															<div class="col-sm-2" id="btn_karyawan_bank_sppb_input_{{$v+1}}">
																<button type="button" class="btn btn-success btn-sm" id="btn_tambah_karyawan_sppb_input_{{$v+1}}" onclick="tambah_karyawan_bank_sppb_input_({{$v+1}})">+</button>									
																<button type="button" class="btn btn-danger btn-sm" id="btn_hapus_karyawan_sppb_input_{{$v+1}}" onclick="hapus_karyawan_bank_sppb({{$v+1}})">x</button>									
															</div>
														</div>
														<div class="form-group row">
															<label class="col-sm-2 col-form-label">Nama Bank {{$v+1}}*</label>
															<div class="col-sm-8">
																<input type="text" id="nama_bank_sppb_karyawan_input_{{$v+1}}" name="karyawan_sppb_input[{{$v+1}}][bank]" class="form-control" placeholder="Nama Bank SPPb {{$v+1}}" value="{{$k->karyawan_nama_bank}}" autocomplete="off">
															</div>
														</div>
														<div class="form-group row">
															<label class="col-sm-2 col-form-label">Nomor Rekening {{$v+1}}*</label>
															<div class="col-sm-8">
																<input type="text" id="rekening_bank_sppb_karyawan_input_{{$v+1}}" name="karyawan_sppb_input[{{$v+1}}][no_rek]"  class="form-control"  placeholder="Nomor Rekening Bank SPPb {{$v+1}}"  value="{{$k->karyawan_no_rek}}" autocomplete="off">
															</div>
														</div>
													</div>
												@endforeach
												</div>
												<div id="bank_sppb_karyawan_master"  style="display: none">
															<div class="form-group row" id="atas_nama_karyawan_sppb">
																<label class="col-sm-2 col-form-label">Atas Nama Rekening 1*</label>
																<div class="col-sm-8">
																	<input type="text" id="atas_nama_bank_sppb_karyawan_1"  onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][nama]" class="form-control"  placeholder="Atas Nama Bank SPPb" autocomplete="off">						
																</div>
																<div class="col-sm-2" id="btn_karyawan_bank_sppb_1">
																	<button type="button" class="btn btn-success btn-sm" id="btn_tambah_karyawan_bank_sppb_1" onclick="tambah_karyawan_bank_sppb(1)">+</button>									
																</div>	
															</div>								
															<div class="form-group row">
																<label class="col-sm-2 col-form-label">Nama Bank 1*</label>
																<div class="col-sm-8">
																<input type="text" id="nama_bank_sppb_karyawan_1" onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][bank]" class="form-control" placeholder="Nama Bank SPPb" autocomplete="off">
																</div>
																
															</div>
															<div class="form-group row">
																<label class="col-sm-2 col-form-label">Nomor Rekening 1*</label>
																<div class="col-sm-8">
																	<input type="text" id="rekening_bank_sppb_karyawan_1"  onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
																</div>
															</div>
														</div>
											</div>
											@elseif($sppb['sppb_data_metpen'] == "master_data")
											<div id="bank_sppb_karyawan" style="display:block">
														<input type="hidden" value="{{count($karyawan_sppb)}}" id="count_karyawan_sppb">
														<div id="bank_sppb_karyawan_master">
															@foreach($karyawan_sppb as $v => $k)
																<div id="bank_karyawan_sppb_{{$v+1}}" style="display:block">
																<div class="form-group row" id="atas_nama_karyawan_sppb">
																	<label class="col-sm-2 col-form-label">Atas Nama Rekening {{$v+1}}*</label>
																	<div class="col-sm-8">
																		<input type="text" id="atas_nama_bank_sppb_karyawan_{{$v+1}}"  onclick="bank_karyawan_sppb({{$v+1}})" name="karyawan_sppb[{{$v+1}}][nama]" value="{{$k->karyawan_nama}}" class="form-control"  placeholder="Atas Nama Bank SPPb" autocomplete="off">						
								
																	</div>
																	<div class="col-sm-2" id="btn_karyawan_bank_sppb_{{$v+1}}">
																			<button type="button" class="btn btn-success btn-sm" id="btn_tambah_karyawan_bank_sppb_{{$v+1}}" onclick="tambah_karyawan_bank_sppb({{$v+1}})">+</button>									
																			@if($v !== 0)
																			<button type="button" class="btn btn-danger btn-sm" id="btn_hapus_karyawan_bank_sppb_{{$v+1}}" onclick="hapus_karyawan_bank_sppb({{$v+1}})">x</button>									
																			@endif
																	</div>
																</div>									
																<div class="form-group row">
																	<label class="col-sm-2 col-form-label">Nama Bank {{$v+1}}*</label>
																	<div class="col-sm-8">
																	<input type="text" id="nama_bank_sppb_karyawan_{{$v+1}}" onclick="bank_karyawan_sppb({{$v+1}})" name="karyawan_sppb[{{$v+1}}][bank]" class="form-control" placeholder="Nama Bank SPPb" value="{{$k->karyawan_nama_bank}}" autocomplete="off">
																	</div>
																</div>
																<div class="form-group row">
																	<label class="col-sm-2 col-form-label">Nomor Rekening {{$v+1}}*</label>
																	<div class="col-sm-8">
																		<input type="text" id="rekening_bank_sppb_karyawan_{{$v+1}}" onclick="bank_karyawan_sppb({{$v+1}})" name="karyawan_sppb[{{$v+1}}][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank SPPb" value="{{$k->karyawan_no_rek}}" autocomplete="off">
																	</div>
																</div>
															</div>
															@endforeach
														</div>
														<div id="bank_sppb_karyawan_input"  style="display: none">
															<div class="form-group row" id="atas_nama_karyawan_sppb">
																<label class="col-sm-2 col-form-label">Atas Nama Rekening 1*</label>
																<div class="col-sm-8">
																	<input type="text" id="atas_nama_bank_sppb_karyawan_input_1" name="karyawan_sppb_input[1][nama]" class="form-control"  placeholder="Atas Nama Bank SPPb" autocomplete="off">						
																</div>
																<div class="col-sm-2" id="btn_karyawan_bank_sppb_input_1">
																	<button type="button" class="btn btn-success btn-sm" id="btn_tambah_karyawan_bank_sppb_input_1" onclick="tambah_karyawan_bank_sppb_input(1)">+</button>									
																</div>	
															</div>								
															<div class="form-group row">
																<label class="col-sm-2 col-form-label">Nama Bank 1*</label>
																<div class="col-sm-8">
																<input type="text" id="nama_bank_sppb_karyawan_input_1"  name="karyawan_sppb_input[1][bank]" class="form-control" placeholder="Nama Bank SPPb" autocomplete="off">
																</div>
																
															</div>
															<div class="form-group row">
																<label class="col-sm-2 col-form-label">Nomor Rekening 1*</label>
																<div class="col-sm-8">
																	<input type="text" id="rekening_bank_sppb_karyawan_input_1"   name="karyawan_sppb_input[1][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
																</div>
															</div>
														</div>
													</div>
											@else
											<div id="bank_sppb_karyawan" style="display: none">	
															<div id="bank_sppb_karyawan_input">
																<div class="form-group row" id="atas_nama_karyawan_sppb">
																	<label class="col-sm-2 col-form-label">Atas Nama Rekening 1*</label>
																	<div class="col-sm-8">
																		<input type="text" id="atas_nama_bank_sppb_karyawan_input_1" name="karyawan_sppb_input[1][nama]" class="form-control"  placeholder="Atas Nama Bank SPPb" autocomplete="off">						
																	</div>
																	<div class="col-sm-2" id="btn_karyawan_bank_sppb_input_1">
																		<button type="button" class="btn btn-success btn-sm" id="btn_tambah_karyawan_bank_sppb_input_1" onclick="tambah_karyawan_bank_sppb_input(1)">+</button>									
																	</div>	
																</div>								
																<div class="form-group row">
																	<label class="col-sm-2 col-form-label">Nama Bank 1*</label>
																	<div class="col-sm-8">
																	<input type="text" id="nama_bank_sppb_karyawan_input_1"  name="karyawan_sppb_input[1][bank]" class="form-control" placeholder="Nama Bank SPPb" autocomplete="off">
																	</div>
																	
																</div>
																<div class="form-group row">
																	<label class="col-sm-2 col-form-label">Nomor Rekening 1*</label>
																	<div class="col-sm-8">
																		<input type="text" id="rekening_bank_sppb_karyawan_input_1"   name="karyawan_sppb_input[1][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
																	</div>
																</div>
															</div>
															<div id="bank_sppb_karyawan_master">
																<div class="form-group row" id="atas_nama_karyawan_sppb">
																	<label class="col-sm-2 col-form-label">Atas Nama Rekening 1*</label>
																	<div class="col-sm-8">
																		<input type="text" id="atas_nama_bank_sppb_karyawan_1"  onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][nama]" class="form-control"  placeholder="Atas Nama Bank SPPb" autocomplete="off">						
																	</div>
																	<div class="col-sm-2" id="btn_karyawan_bank_sppb_1">
																		<button type="button" class="btn btn-success btn-sm" id="btn_tambah_karyawan_bank_sppb_1" onclick="tambah_karyawan_bank_sppb(1)">+</button>									
																	</div>	
																</div>								
																<div class="form-group row">
																	<label class="col-sm-2 col-form-label">Nama Bank 1*</label>
																	<div class="col-sm-8">
																	<input type="text" id="nama_bank_sppb_karyawan_1" onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][bank]" class="form-control" placeholder="Nama Bank SPPb" autocomplete="off">
																	</div>
																</div>
																<div class="form-group row">
																	<label class="col-sm-2 col-form-label">Nomor Rekening 1*</label>
																	<div class="col-sm-8">
																		<input type="text" id="rekening_bank_sppb_karyawan_1"  onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
																	</div>
																</div>
															</div>
															
														</div>
											@endif
										@elseif($sppb['sppb_metode_pembayaran'] == "bank")
											<!-- BANK -->
											<div class="form-group row" style="display:block;" id="pilih_lampirkan_sppb" onclick="pilih_data_sppb()">
													<label class="col-sm-2 col-form-label"></label>
													<div class="col-sm-10">
													@if($sppb['sppb_data_metpen'] == "input_data")
														<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="input_data_sppb" value="input_data" type="radio" checked="checked" onclick="pilih_data_sppb()"> 
															<span style="font-size:17px"><i ></i>Data diinputkan manual </span>
															</label>
														</div>
														<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="master_data_sppb" value="master_data" type="radio" onclick="pilih_data_sppb()"> 
															<span style="font-size:17px"><i ></i>Data dari master </span>
															</label>
														</div>
														<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="lampirkan_data_sppb" value="lampirkan_data" type="radio" onclick="pilih_data_sppb()"> 
															<span style="font-size:17px"><i ></i>Data dilampirkan</span>
															</label>
														</div>
													@elseif($sppb['sppb_data_metpen'] == "master_data")
														<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="input_data_sppb" value="input_data" type="radio" onclick="pilih_data_sppb()" > 
															<span style="font-size:17px"><i ></i>Data diinputkan manual </span>
															</label>
														</div>
														<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="master_data_sppb" value="master_data" type="radio" checked="checked" onclick="pilih_data_sppb()"> 
															<span style="font-size:17px"><i ></i>Data dari master </span>
															</label>
														</div>
														<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="lampirkan_data_sppb" value="lampirkan_data" type="radio" onclick="pilih_data_sppb()"> 
															<span style="font-size:17px"><i ></i>Data dilampirkan</span>
															</label>
														</div>
													@else
													<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="input_data_sppb" value="input_data" type="radio" onclick="pilih_data_sppb()"> 
															<span style="font-size:17px"><i ></i>Data diinputkan manual </span>
															</label>
														</div>
														<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="master_data_sppb" value="master_data" type="radio" onclick="pilih_data_sppb()"> 
															<span style="font-size:17px"><i ></i>Data dari master </span>
															</label>
														</div>
														<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="lampirkan_data_sppb" value="lampirkan_data" type="radio" checked="checked" onclick="pilih_data_sppb()"> 
															<span style="font-size:17px"><i ></i>Data dilampirkan</span>
															</label>
														</div>
													@endif
													</div>
												</div>
											@if($sppb['sppb_data_metpen'] == 'master_data')
											<!-- BANK MASTER -->
											<div id="bank_sppb" style="display:block">
													<input type="hidden" id="id_bank_sppb_1" name="id_bank_sppb" class="form-control" onclick="data_bank_sppb(1)" value="{{$sppb['master_vendor_id']}}" placeholder="Id Bank sppb" autocomplete="off">
												<div class="form-group row" id="atas_nama_vendor_sppb">
													<label class="col-sm-2 col-form-label">Atas Nama Rekening *</label>
													<div class="col-sm-10">
														<input type="text" id="atas_nama_bank_sppb_vendor" name="atas_nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" value="{{$sppb['master_vendor_atas_nama']}}" placeholder="Atas Nama Bank sppb" autocomplete="off">
													
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nama Bank * </label>
													<div class="col-sm-10">
													
													<input type="text" id="nama_bank_sppb_vendor" name="nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" value="{{$sppb['master_vendor_nama_bank']}}" placeholder="Nama Bank sppb" autocomplete="off">
													
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
													<div class="col-sm-10">
													
														<input type="text" id="rekening_bank_sppb_vendor" name="rekening_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" value="{{$sppb['master_vendor_rekening']}}" placeholder="Nomor Rekening Bank sppb" autocomplete="off">
													
													</div>
												</div>
											</div>
											@elseif($sppb['sppb_data_metpen'] == 'input_data')
											<!-- BANK INPUT -->
											<div id="bank_sppb" style="display:block">
													<input type="hidden" id="id_bank_sppb_1" name="id_bank_sppb" class="form-control" onclick="data_bank_sppb(1)" value="" placeholder="Id Bank sppb" autocomplete="off">
												<div class="form-group row" id="atas_nama_vendor_sppb">
													<label class="col-sm-2 col-form-label">Atas Nama Rekening *</label>
													<div class="col-sm-10">
														<input type="text" id="atas_nama_bank_sppb_vendor" name="atas_nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" value="{{$karyawan_sppb[0]->karyawan_nama}}" placeholder="Atas Nama Bank sppb" autocomplete="off">
													
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nama Bank * </label>
													<div class="col-sm-10">
													
													<input type="text" id="nama_bank_sppb_vendor" name="nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" value="{{$karyawan_sppb[0]->karyawan_nama_bank}}" placeholder="Nama Bank sppb" autocomplete="off">
													
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
													<div class="col-sm-10">
													
														<input type="text" id="rekening_bank_sppb_vendor" name="rekening_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" value="{{$karyawan_sppb[0]->karyawan_no_rek}}" placeholder="Nomor Rekening Bank sppb" autocomplete="off">
													
													</div>
												</div>
											</div>
											@else
											<!-- BANK LAMPIRKAN -->
											<div id="bank_sppb" style="display: none">
												<input type="hidden" id="id_bank_sppb_1" name="id_bank_sppb" class="form-control" onclick="data_bank_sppb(1)" placeholder="Id Bank SPPb" autocomplete="off">
												<div class="form-group row" id="atas_nama_vendor_sppb">
													<label class="col-sm-2 col-form-label">Atas Nama Rekening *</label>
													<div class="col-sm-10">
														<input type="text" id="atas_nama_bank_sppb_vendor" name="atas_nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" placeholder="Atas Nama Bank SPPb" autocomplete="off">
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nama Bank *</label>
													<div class="col-sm-10">
													<input type="text" id="nama_bank_sppb_vendor" name="nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" placeholder="Nama Bank SPPb" autocomplete="off">
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
													<div class="col-sm-10">
														<input type="text" id="rekening_bank_sppb_vendor" name="rekening_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
													</div>
												</div>
											</div>
											@endif
											<div id="bank_sppb_karyawan" style="display: none">	
												<div id="bank_sppb_karyawan_input">
													<div class="form-group row" id="atas_nama_karyawan_sppb">
														<label class="col-sm-2 col-form-label">Atas Nama Rekening 1*</label>
														<div class="col-sm-8">
															<input type="text" id="atas_nama_bank_sppb_karyawan_input_1" name="karyawan_sppb_input[1][nama]" class="form-control"  placeholder="Atas Nama Bank SPPb" autocomplete="off">						
														</div>
														<div class="col-sm-2" id="btn_karyawan_bank_sppb_input_1">
															<button type="button" class="btn btn-success btn-sm" id="btn_tambah_karyawan_bank_sppb_input_1" onclick="tambah_karyawan_bank_sppb_input(1)">+</button>									
														</div>	
													</div>								
													<div class="form-group row">
														<label class="col-sm-2 col-form-label">Nama Bank 1*</label>
														<div class="col-sm-8">
														<input type="text" id="nama_bank_sppb_karyawan_input_1"  name="karyawan_sppb_input[1][bank]" class="form-control" placeholder="Nama Bank SPPb" autocomplete="off">
														</div>
														
													</div>
													<div class="form-group row">
														<label class="col-sm-2 col-form-label">Nomor Rekening 1*</label>
														<div class="col-sm-8">
															<input type="text" id="rekening_bank_sppb_karyawan_input_1"   name="karyawan_sppb_input[1][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
														</div>
													</div>
												</div>
												<div id="bank_sppb_karyawan_master">
													<div class="form-group row" id="atas_nama_karyawan_sppb">
														<label class="col-sm-2 col-form-label">Atas Nama Rekening 1*</label>
														<div class="col-sm-8">
															<input type="text" id="atas_nama_bank_sppb_karyawan_1"  onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][nama]" class="form-control"  placeholder="Atas Nama Bank SPPb" autocomplete="off">						
														</div>
														<div class="col-sm-2" id="btn_karyawan_bank_sppb_1">
															<button type="button" class="btn btn-success btn-sm" id="btn_tambah_karyawan_bank_sppb_1" onclick="tambah_karyawan_bank_sppb(1)">+</button>									
														</div>	
													</div>								
													<div class="form-group row">
														<label class="col-sm-2 col-form-label">Nama Bank 1*</label>
														<div class="col-sm-8">
														<input type="text" id="nama_bank_sppb_karyawan_1" onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][bank]" class="form-control" placeholder="Nama Bank SPPb" autocomplete="off">
														</div>
														
													</div>
													<div class="form-group row">
														<label class="col-sm-2 col-form-label">Nomor Rekening 1*</label>
														<div class="col-sm-8">
															<input type="text" id="rekening_bank_sppb_karyawan_1"  onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
														</div>
													</div>
												</div>
												
											</div>
											
										@elseif($sppb['sppb_metode_pembayaran'] == 'skbdn')
											<!-- SKBDN -->
											<div class="form-group row" style="display:block;" id="pilih_lampirkan_sppb" onclick="pilih_data_sppb()">
													<label class="col-sm-2 col-form-label"></label>
													<div class="col-sm-10">
													@if($sppb['sppb_data_metpen'] == "input_data")
														<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="input_data_sppb" value="input_data" type="radio" checked="checked" onclick="pilih_data_sppb()"> 
															<span style="font-size:17px"><i ></i>Data diinputkan manual </span>
															</label>
														</div>
														<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="master_data_sppb" value="master_data" type="radio" onclick="pilih_data_sppb()"> 
															<span style="font-size:17px"><i ></i>Data dari master </span>
															</label>
														</div>
														<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="lampirkan_data_sppb" value="lampirkan_data" type="radio" onclick="pilih_data_sppb()"> 
															<span style="font-size:17px"><i ></i>Data dilampirkan</span>
															</label>
														</div>
													@elseif($sppb['sppb_data_metpen'] == "master_data")
														<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="input_data_sppb" value="input_data" type="radio" onclick="pilih_data_sppb()" > 
															<span style="font-size:17px"><i ></i>Data diinputkan manual </span>
															</label>
														</div>
														<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="master_data_sppb" value="master_data" type="radio" checked="checked" onclick="pilih_data_sppb()"> 
															<span style="font-size:17px"><i ></i>Data dari master </span>
															</label>
														</div>
														<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="lampirkan_data_sppb" value="lampirkan_data" type="radio" onclick="pilih_data_sppb()"> 
															<span style="font-size:17px"><i ></i>Data dilampirkan</span>
															</label>
														</div>
													@else
													<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="input_data_sppb" value="input_data" type="radio" onclick="pilih_data_sppb()"> 
															<span style="font-size:17px"><i ></i>Data diinputkan manual </span>
															</label>
														</div>
														<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="master_data_sppb" value="master_data" type="radio" onclick="pilih_data_sppb()"> 
															<span style="font-size:17px"><i ></i>Data dari master </span>
															</label>
														</div>
														<div class="col-sm-2">
															<label class="fancy-radio">
															<input  name="pilih_data_sppb" id="lampirkan_data_sppb" value="lampirkan_data" type="radio" checked="checked" onclick="pilih_data_sppb()"> 
															<span style="font-size:17px"><i ></i>Data dilampirkan</span>
															</label>
														</div>
													@endif
													</div>
												</div>
											@if($sppb['sppb_data_metpen'] == 'master_data')
											<!-- SKBDN MASTER -->
											<div id="bank_sppb" style="display:block">
													<input type="hidden" id="id_bank_sppb_1" name="id_bank_sppb" class="form-control" onclick="data_bank_sppb(1)" value="{{$sppb['master_vendor_id']}}" placeholder="Id Bank sppb" autocomplete="off">
												<div class="form-group row" id="atas_nama_vendor_sppb">
													<label class="col-sm-2 col-form-label">Atas Nama Rekening *</label>
													<div class="col-sm-10">
														<input type="text" id="atas_nama_bank_sppb_vendor" name="atas_nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" value="{{$sppb['master_vendor_atas_nama']}}" placeholder="Atas Nama Bank sppb" autocomplete="off">
													
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nama Bank * </label>
													<div class="col-sm-10">
													
													<input type="text" id="nama_bank_sppb_vendor" name="nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" value="{{$sppb['master_vendor_nama_bank']}}" placeholder="Nama Bank sppb" autocomplete="off">
													
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
													<div class="col-sm-10">
													
														<input type="text" id="rekening_bank_sppb_vendor" name="rekening_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" value="{{$sppb['master_vendor_rekening']}}" placeholder="Nomor Rekening Bank sppb" autocomplete="off">
													
													</div>
												</div>
											</div>
											@elseif($sppb['sppb_data_metpen'] == 'input_data')
											<!-- SKBDN INPUT -->
											<div id="bank_sppb" style="display:block">
													<input type="hidden" id="id_bank_sppb_1" name="id_bank_sppb" class="form-control" onclick="data_bank_sppb(1)" value="" placeholder="Id Bank sppb" autocomplete="off">
												<div class="form-group row" id="atas_nama_vendor_sppb">
													<label class="col-sm-2 col-form-label">Atas Nama Rekening *</label>
													<div class="col-sm-10">
														<input type="text" id="atas_nama_bank_sppb_vendor" name="atas_nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" value="{{$karyawan_sppb[0]->karyawan_nama}}" placeholder="Atas Nama Bank sppb" autocomplete="off">
													
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nama Bank * </label>
													<div class="col-sm-10">
													
													<input type="text" id="nama_bank_sppb_vendor" name="nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" value="{{$karyawan_sppb[0]->karyawan_nama_bank}}" placeholder="Nama Bank sppb" autocomplete="off">
													
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
													<div class="col-sm-10">
													
														<input type="text" id="rekening_bank_sppb_vendor" name="rekening_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" value="{{$karyawan_sppb[0]->karyawan_no_rek}}" placeholder="Nomor Rekening Bank sppb" autocomplete="off">
													
													</div>
												</div>
											</div>
											@else
											<!-- SKBDN LAMPIRKAN -->
											<div id="bank_sppb" style="display: none">
												<input type="hidden" id="id_bank_sppb_1" name="id_bank_sppb" class="form-control" onclick="data_bank_sppb(1)" placeholder="Id Bank SPPb" autocomplete="off">
												<div class="form-group row" id="atas_nama_vendor_sppb">
													<label class="col-sm-2 col-form-label">Atas Nama Rekening *</label>
													<div class="col-sm-10">
														<input type="text" id="atas_nama_bank_sppb_vendor" name="atas_nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" placeholder="Atas Nama Bank SPPb" autocomplete="off">
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nama Bank *</label>
													<div class="col-sm-10">
													<input type="text" id="nama_bank_sppb_vendor" name="nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" placeholder="Nama Bank SPPb" autocomplete="off">
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
													<div class="col-sm-10">
														<input type="text" id="rekening_bank_sppb_vendor" name="rekening_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
													</div>
												</div>
											</div>
											@endif
											<div id="bank_sppb_karyawan" style="display: none">	
												<div id="bank_sppb_karyawan_input">
													<div class="form-group row" id="atas_nama_karyawan_sppb">
														<label class="col-sm-2 col-form-label">Atas Nama Rekening 1*</label>
														<div class="col-sm-8">
															<input type="text" id="atas_nama_bank_sppb_karyawan_input_1" name="karyawan_sppb_input[1][nama]" class="form-control"  placeholder="Atas Nama Bank SPPb" autocomplete="off">						
														</div>
														<div class="col-sm-2" id="btn_karyawan_bank_sppb_input_1">
															<button type="button" class="btn btn-success btn-sm" id="btn_tambah_karyawan_bank_sppb_input_1" onclick="tambah_karyawan_bank_sppb_input(1)">+</button>									
														</div>	
													</div>								
													<div class="form-group row">
														<label class="col-sm-2 col-form-label">Nama Bank 1*</label>
														<div class="col-sm-8">
														<input type="text" id="nama_bank_sppb_karyawan_input_1"  name="karyawan_sppb_input[1][bank]" class="form-control" placeholder="Nama Bank SPPb" autocomplete="off">
														</div>
														
													</div>
													<div class="form-group row">
														<label class="col-sm-2 col-form-label">Nomor Rekening 1*</label>
														<div class="col-sm-8">
															<input type="text" id="rekening_bank_sppb_karyawan_input_1"   name="karyawan_sppb_input[1][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
														</div>
													</div>
												</div>
												<div id="bank_sppb_karyawan_master">
													<div class="form-group row" id="atas_nama_karyawan_sppb">
														<label class="col-sm-2 col-form-label">Atas Nama Rekening 1*</label>
														<div class="col-sm-8">
															<input type="text" id="atas_nama_bank_sppb_karyawan_1"  onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][nama]" class="form-control"  placeholder="Atas Nama Bank SPPb" autocomplete="off">						
														</div>
														<div class="col-sm-2" id="btn_karyawan_bank_sppb_1">
															<button type="button" class="btn btn-success btn-sm" id="btn_tambah_karyawan_bank_sppb_1" onclick="tambah_karyawan_bank_sppb(1)">+</button>									
														</div>	
													</div>								
													<div class="form-group row">
														<label class="col-sm-2 col-form-label">Nama Bank 1*</label>
														<div class="col-sm-8">
														<input type="text" id="nama_bank_sppb_karyawan_1" onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][bank]" class="form-control" placeholder="Nama Bank SPPb" autocomplete="off">
														</div>
														
													</div>
													<div class="form-group row">
														<label class="col-sm-2 col-form-label">Nomor Rekening 1*</label>
														<div class="col-sm-8">
															<input type="text" id="rekening_bank_sppb_karyawan_1"  onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
														</div>
													</div>
												</div>
											</div>
										@else
										<div class="form-group row" style="display:none;" id="pilih_lampirkan_sppb" onclick="pilih_data_sppb()">
											<label class="col-sm-2 col-form-label"></label>
											<div class="col-sm-10">
												<div class="col-sm-2">
													<label class="fancy-radio">
													<input  name="pilih_data_sppb" id="input_data_sppb" value="input_data" type="radio" > 
													<span style="font-size:17px"><i ></i>Data diinputkan manual </span>
													</label>
												</div>
												<div class="col-sm-2">
													<label class="fancy-radio">
													<input  name="pilih_data_sppb" id="master_data_sppb" value="master_data" type="radio" > 
													<span style="font-size:17px"><i ></i>Data dari master </span>
													</label>
												</div>
												<div class="col-sm-2">
													<label class="fancy-radio">
													<input  name="pilih_data_sppb" id="lampirkan_data_sppb" value="lampirkan_data" type="radio" checked="checked" > 
													<span style="font-size:17px"><i ></i>Data dilampirkan</span>
													</label>
												</div>
											</div>
										</div>
										<div id="bank_sppb" style="display: none">
											<input type="hidden" id="id_bank_sppb_1" name="id_bank_sppb" class="form-control" onclick="data_bank_sppb(1)" placeholder="Nama Rekening Bank" autocomplete="off" required>												
											<div class="form-group row" id="atas_nama_vendor_sppb">
												<label class="col-sm-2 col-form-label">Atas Nama *</label>
												<div class="col-sm-10">
													<input type="text" id="atas_nama_bank_sppb_vendor" name="atas_nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" placeholder="Atas Nama Bank SPPb" autocomplete="off">
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nama Bank *</label>
												<div class="col-sm-10">
												<input type="text" id="nama_bank_sppb_vendor" name="nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" placeholder="Nama Bank" autocomplete="off">												
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
												<div class="col-sm-10">
													<input type="text" id="rekening_bank_sppb_vendor" name="rekening_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
												</div>
											</div>
											
											
										</div>
										<div id="bank_sppb_karyawan" style="display: none">	
											<div id="bank_sppb_karyawan_input">
												<div class="form-group row" id="atas_nama_karyawan_sppb">
													<label class="col-sm-2 col-form-label">Atas Nama Rekening 1*</label>
													<div class="col-sm-8">
														<input type="text" id="atas_nama_bank_sppb_karyawan_input_1" name="karyawan_sppb_input[1][nama]" class="form-control"  placeholder="Atas Nama Bank SPPb" autocomplete="off">						
													</div>
													<div class="col-sm-2" id="btn_karyawan_bank_sppb_input_1">
														<button type="button" class="btn btn-success btn-sm" id="btn_tambah_karyawan_bank_sppb_input_1" onclick="tambah_karyawan_bank_sppb_input(1)">+</button>									
													</div>	
												</div>								
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nama Bank 1*</label>
													<div class="col-sm-8">
													<input type="text" id="nama_bank_sppb_karyawan_input_1"  name="karyawan_sppb_input[1][bank]" class="form-control" placeholder="Nama Bank SPPb" autocomplete="off">
													</div>
													
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nomor Rekening 1*</label>
													<div class="col-sm-8">
														<input type="text" id="rekening_bank_sppb_karyawan_input_1"   name="karyawan_sppb_input[1][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
													</div>
												</div>
											</div>
											<div id="bank_sppb_karyawan_master">
												<div class="form-group row" id="atas_nama_karyawan_sppb">
													<label class="col-sm-2 col-form-label">Atas Nama Rekening 1*</label>
													<div class="col-sm-8">
														<input type="text" id="atas_nama_bank_sppb_karyawan_1"  onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][nama]" class="form-control"  placeholder="Atas Nama Bank SPPb" autocomplete="off">						
													</div>
													<div class="col-sm-2" id="btn_karyawan_bank_sppb_1">
														<button type="button" class="btn btn-success btn-sm" id="btn_tambah_karyawan_bank_sppb_1" onclick="tambah_karyawan_bank_sppb(1)">+</button>									
													</div>	
												</div>								
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nama Bank 1*</label>
													<div class="col-sm-8">
													<input type="text" id="nama_bank_sppb_karyawan_1" onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][bank]" class="form-control" placeholder="Nama Bank SPPb" autocomplete="off">
													</div>
													
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nomor Rekening 1*</label>
													<div class="col-sm-8">
														<input type="text" id="rekening_bank_sppb_karyawan_1"  onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
													</div>
												</div>
											</div>
											
										</div>
										@endif
										@endif
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Catatan</label>
											<div class="col-sm-10">
											@if(isset($sppb['sppb_catatan']))
												<textarea class="form-control" id="catatan_sppb" name="catatan_sppb"  placeholder="Catatan SPPb" rows="4">{{$sppb['sppb_catatan']}}</textarea>
											@else
												<textarea class="form-control" id="catatan_sppb" name="catatan_sppb"  placeholder="Catatan SPPb" rows="4"></textarea>
											@endif
											</div>
										</div>
										
											
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Upload Dokumen Pendukung</label>
											<div class="col-sm-10">
											@if(isset($dokpensppb))
								              @foreach($dokpensppb as $d => $value)
									            <div id="dokumen_pendukung_sppb_{{$d}}">
												<a class="btn btn-default" href="{{ asset('dokumen/'.$value->dokumen_pendukung_sppb_nama)}}" target="_blank" >{{$value->dokumen_pendukung_sppb_nama}}</a>												
												<button type="button" class="btn btn-danger btn-sm" id="hapus_dokpen_sppb" onclick="hapus_dokumen_pendukung_sppb({{$d}})" >X</button>
												<input type="hidden" name="dokpenlama_sppb[{{$d}}]" value="{{$value->dokumen_pendukung_sppb_id}}">
												<br></br>
												</div>
											  @endforeach
											 
								            @endif
												{{-- <input type="file" id="dokumen_pendukung_sppb" name="dokumen_pendukung_sppb[]" class="file" data-preview-file-type="text" data-show-upload="true" data-show-caption="true" placeholder="Upload Dokumen Pendukung Lain" multiple> --}}
												<div class="file-loading">
												    <input type="file" id="dokumen_pendukung_sppb" name="dokumen_pendukung_sppb[]" class="file-multiple" accept="application/pdf, image/*" multiple>
												</div>
											</div>
										</div>
									</div>
									<!-- END TAB INFORMASI -->

									<!-- TAB ISI -->
									<div class="tab-pane fade" id="tab-isi-sppb">
									@if(isset($sppb[0]))
										<input type="hidden" id="jumlah_isi_sppb" value="{{count($sppb[0])}}">
										@for( $i=0; $i< count($sppb[0]) ; $i++)
										<div id="isi_sppb_{{$i}}" class="col-sm-12">
											<div  style="padding-bottom: 10px; margin-bottom: 20px; border-bottom: 1px solid #eaeaea;">
												<font size="4" style="margin-right: 20px">Isi {{$i+1}}. </font>
												<button type="button" class="btn btn-info btn-sm" id="tambah_isi_sppb_{{$i}}" onclick="tambah_isi_sppb({{$i}})">+</button>
												<button type="button" class="btn btn-danger btn-sm" id="hapus_isi_sppb_{{$i}}" onclick="hapus_isi_sppb({{$i}},'ckeditor_{{$i}}_',{{count($sppb[0][$i][0])}})">x</button>
											</div>
											<div class="col-sm-5">
											<div class="form-group row">
													<label class="col-sm-3 col-form-label">Kode KBB *</label>
													<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppb_{{$i+1}}" >
														<select class="selectpicker slct_sppb"  data-live-search="true" data-dropup-auto="false" id="kode_kbb_sppb_{{$i}}" data-width="100%" name="isi_sppb[{{$i}}][kode_kbb]" data-size="7" onchange="pilih_rekening_sppb({{$i+1}},'kode_kbb_sppb_')">
															<option value="{{$sppb[0][$i]['master_kode_kbb']}}"selected>{{$sppb[0][$i]['master_kode_kbb']}}</option>
															@foreach($rekening as $r)
															<option value="{{$r->master_rekening_kode_kbb}}">{{$r->master_rekening_kode_kbb}}</option>
															@endforeach
														</select>
														</div>
														
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Kode SAP *</label>
													<label class="col-sm-9">
														<select class="form-control validate_sppb validate_spp_all" id="jenis_sap_sppb_{{$i}}" onchange="js_sppb({{$i}})" name="isi_sppb[{{$i}}][jenis_sap]">
															@if($sppb[0][$i]['master_gl_id'] != null)
															<option value="vendor">Nomor Vendor</option>
															<option value="gl" selected>Nomor GL</option>
															<option value="customer">Nomor Customer</option>
															@elseif($sppb[0][$i]['master_customer_id'] != null)
															<option value="vendor">Nomor Vendor</option>
															<option value="gl" >Nomor GL</option>
															<option value="customer" selected>Nomor Customer</option>
															@else
															<option value="vendor" selected>Nomor Vendor</option>
															<option value="gl">Nomor GL</option>
															<option value="customer">Nomor Customer</option>
															@endif
														</select>
													</label>
													<label class="col-sm-3 col-form-label"></label>
													@if($sppb[0][$i]['master_gl_id'] != null)
													<div id="nomor_vendor_sppb_{{$i}}" style="display:none">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppb{{$i}}" >
																<select class="selectpicker slct_sppb"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_vendor_sppb_{{$i}}" data-size="7" name="isi_sppb_rekening" onchange="pilih_rekening_sppb({{$i}},'sap_vendor_sppb_')">
																	<option value="" disabled selected>-- Pilih Kode Vendor --</option>
																	@foreach($rekening as $r)
																	<option value="{{$r->master_rekening_id}}">{{$r->master_rekening_kode_sap}} ({{$r->master_rekening_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_vendor_sppb_id_{{$i}}" name="isi_sppb[{{$i}}][vendor]" class="form-control" onclick="kode_rekening_sppb({{$i}})" placeholder="Kode SAP (Nomor Vendor)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_customer_sppb_{{$i}}" style="display:none">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppb_{{$i}}" >
																<select class="selectpicker"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_customer_sppb_{{$i}}" data-size="7" name="isi_sppb_customer" onchange="pilih_rekening_sppb({{$i}},'sap_customer_sppb_')">
																	<option value="" disabled selected>-- Pilih Kode Customer --</option>
																	@foreach($customer as $r)
																	<option value="{{$r->master_customer_id}}">{{$r->master_customer_kode_sap}} ({{$r->master_customer_nama}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_customer_sppb_id_{{$i}}" name="isi_sppb[{{$i}}][customer]" class="form-control" onclick="kode_customer_sppb({{$i}})" placeholder="Kode SAP (Nomor Customer)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_gl_sppb_{{$i}}" style="display:block">
														<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppb{{$i}}" >
																<select class="selectpicker slct_sppb"  data-live-search="true" data-dropup-auto="false" id="sap_gl_sppb_{{$i}}" data-size="7" data-width="100%" name="isi_sppb_rekening" onchange="pilih_rekening_sppb({{$i}},'sap_gl_sppb_')">
																	<option value="{{$sppb[0][$i]['master_gl_id']}}" selected>{{$sppb[0][$i]['master_gl_kode']}}</option>
																	@foreach($gl as $r)
																	<option value="{{$r->master_gl_id}}">{{$r->master_gl_kode}} ({{$r->master_gl_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_gl_sppb_id_{{$i}}" name="isi_sppb[{{$i}}][gl]" class="form-control" onclick="kode_gl_sppb({{$i}})" value="{{$sppb[0][$i]['master_gl_id']}}"placeholder="Kode SAP (Nomor GL)" autocomplete="off" required>
														</div>
													</div>
													
												</div>
												@elseif($sppb[0][$i]['master_customer_id'] != null)
													<div id="nomor_vendor_sppb_{{$i}}" style="display:none">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppb_{{$i}}" >
																<select class="selectpicker slct_sppb"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_vendor_sppb_{{$i}}" data-size="7" name="isi_sppb_rekening" onchange="pilih_rekening_sppb({{$i}},'sap_vendor_sppb_')">
																	<option value="" disabled selected>-- Pilih Kode Vendor --</option>
																	@foreach($rekening as $r)
																	<option value="{{$r->master_rekening_id}}">{{$r->master_rekening_kode_sap}} ({{$r->master_rekening_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_vendor_sppb_id_{{$i}}" name="isi_sppb[{{$i}}][vendor]" value="{{$sppb[0][$i]['master_rekening_id']}}" class="form-control" onclick="kode_rekening_sppb(1)" placeholder="Kode SAP (Nomor Vendor)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_customer_sppb_{{$i}}" style="display:block">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppb_{{$i}}" >
																<select class="selectpicker"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_customer_sppb_{{$i}}" data-size="7" name="isi_sppb_customer" onchange="pilih_rekening_sppb({{$i}},'sap_customer_sppb_')">
																		<option value="{{$sppb[0][$i]['master_customer_id']}}" selected>{{$sppb[0][$i]['master_customer_kode_sap']}}  ({{$sppb[0][$i]['master_customer_nama']}})</option>
																	@foreach($customer as $r)
																	<option value="{{$r->master_customer_id}}">{{$r->master_customer_kode_sap}} ({{$r->master_customer_nama}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_customer_sppb_id_{{$i}}" name="isi_sppb[{{$i}}][customer]" class="form-control" value="{{$sppb[0][$i]['master_customer_id']}}" onclick="kode_customer_sppb({{$i}})" placeholder="Kode SAP (Nomor Customer)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_gl_sppb_{{$i}}" style="display:none">
														<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppb{{$i}}" >
																<select class="selectpicker slct_sppb"  data-live-search="true" data-dropup-auto="false" id="sap_gl_sppb_{{$i}}" data-size="7" data-width="100%" name="isi_sppb_rekening" onchange="pilih_rekening_sppb({{$i}},'sap_gl_sppb_')">
																	<option value="" disabled selected>-- Pilih Kode GL --</option>
																	@foreach($gl as $r)
																	<option value="{{$r->master_gl_id}}">{{$r->master_gl_kode}} ({{$r->master_gl_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_gl_sppb_id_{{$i}}" name="isi_sppb[{{$i}}][gl]" class="form-control" onclick="kode_gl_sppb({{$i}})" placeholder="Kode SAP (Nomor GL)" autocomplete="off" required>
														</div>
													</div>
												</div>
												@else
													<div id="nomor_vendor_sppb_{{$i}}" style="display:block">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppb_{{$i}}" >
																<select class="selectpicker slct_sppb"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_vendor_sppb_{{$i}}" data-size="7" name="isi_sppb_rekening" onchange="pilih_rekening_sppb({{$i}},'sap_vendor_sppb_')">
																	<option value="{{$sppb[0][$i]['master_rekening_id']}}" selected>{{$sppb[0][$i]['master_rekening_kode_sap']}}</option>
																	@foreach($rekening as $r)
																	<option value="{{$r->master_rekening_id}}">{{$r->master_rekening_kode_sap}} ({{$r->master_rekening_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_vendor_sppb_id_{{$i}}" name="isi_sppb[{{$i}}][vendor]" value="{{$sppb[0][$i]['master_rekening_id']}}" class="form-control" onclick="kode_rekening_sppb(1)" placeholder="Kode SAP (Nomor Vendor)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_customer_sppb_{{$i}}" style="display:none">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppb_{{$i}}" >
																<select class="selectpicker"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_customer_sppb_{{$i}}" data-size="7" name="isi_sppb_customer" onchange="pilih_rekening_sppb({{$i}},'sap_customer_sppb_')">
																	<option value="" disabled selected>-- Pilih Kode Customer --</option>
																	@foreach($customer as $r)
																	<option value="{{$r->master_customer_id}}">{{$r->master_customer_kode_sap}} ({{$r->master_customer_nama}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_customer_sppb_id_{{$i}}" name="isi_sppb[{{$i}}][customer]" class="form-control" onclick="kode_customer_sppb({{$i}})" placeholder="Kode SAP (Nomor Customer)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_gl_sppb_{{$i}}" style="display:none">
														<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppb{{$i}}" >
																<select class="selectpicker slct_sppb"  data-live-search="true" data-dropup-auto="false" id="sap_gl_sppb_{{$i}}" data-size="7" data-width="100%" name="isi_sppb_rekening" onchange="pilih_rekening_sppb({{$i}},'sap_gl_sppb_')">
																	<option value="" disabled selected>-- Pilih Kode GL --</option>
																	@foreach($gl as $r)
																	<option value="{{$r->master_gl_id}}">{{$r->master_gl_kode}} ({{$r->master_gl_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_gl_sppb_id_{{$i}}" name="isi_sppb[{{$i}}][gl]" class="form-control" onclick="kode_gl_sppb({{$i}})" placeholder="Kode SAP (Nomor GL)" autocomplete="off" required>
														</div>
													</div>
												</div>
												@endif
												@if($sppb[0][$i]['master_cost_center_id'] !== null )
													<div class="form-group row">
													<label class="col-sm-3 col-form-label">Cost/Profit*</label>
													<label class="col-sm-9">
														<select class="form-control validate_sppb validate_spp_all" id="jenis_center_sppb_{{$i}}" onchange="jc_sppb({{$i}})" value="cost_center" name="isi_sppb[{{$i}}][jenis_center]">
															
															<option value="cost_center" selected>Cost Center</option>
															<option value="profit_center">Profit Center</option>
														</select>
													</label>
													<label class="col-sm-3 col-form-label"></label>
													<div class="col-sm-9" id="cost_center_sppb_{{$i}}" >
														<select class="form-control" id="select_cost_center_sppb_{{$i}}" name="isi_sppb[{{$i}}][cost_center]">
															<option value="{{$sppb[0][$i]['master_cost_center_id']}}" selected>{{$sppb[0][$i]['master_cost_center_kode']}}  {{$sppb[0][$i]['master_cost_center_keterangan']}}</option>
															@foreach($costcenter as $cost)
																<option value="{{$cost->master_cost_center_id}}">{{$cost->master_cost_center_kode}} {{$cost->master_cost_center_keterangan}}</option>
															@endforeach
														</select>
													</div>
													<div class="col-sm-9" id="profit_center_sppb_{{$i}}" style="display:none">
														<select class="form-control" id="select_profit_center_sppb_{{$i}}" name="isi_sppb[{{$i}}][profit_center]">
														<option value="" disabled selected>-- Pilih Profit Center --</option>
															@foreach($profitcenter as $profit)
																<option value="{{$profit->master_profit_center_id}}">{{$profit->master_profit_center_kode}} ({{$profit->master_profit_unit}})</option>
															@endforeach
														</select>
													</div>
												</div>
												@else
												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Cost/Profit*</label>
													<label class="col-sm-9">
														<select class="form-control validate_sppb validate_spp_all" id="jenis_center_sppb_{{$i}}" onchange="jc_sppb({{$i}})" value="profit_center" name="isi_sppb[{{$i}}][jenis_center]">
															
															<option value="cost_center" >Cost Center</option>
															<option value="profit_center" selected>Profit Center</option>
														</select>
													</label>
													<label class="col-sm-3 col-form-label"></label>
													<div class="col-sm-9" id="cost_center_sppb_{{$i}}" style="display:none">
														<select class="form-control" id="select_cost_center_sppb_{{$i}}" name="isi_sppb[{{$i}}][cost_center]">
														<option value="" disabled selected>-- Pilih Cost Center --</option>
															@foreach($costcenter as $cost)
																<option value="{{$cost->master_cost_center_id}}">{{$cost->master_cost_center_kode}} {{$cost->master_cost_center_keterangan}}</option>
															@endforeach
														</select>
													</div>
													<div class="col-sm-9" id="profit_center_sppb_{{$i}}" >
														<select class="form-control" id="select_profit_center_sppb_{{$i}}" name="isi_sppb[{{$i}}][profit_center]">
														<option value="{{$sppb[0][$i]['master_profit_center_id']}}" selected>{{$sppb[0][$i]['master_profit_center_kode']}}  ({{$sppb[0][$i]['master_profit_unit']}})</option>
															@foreach($profitcenter as $profit)
																<option value="{{$profit->master_profit_center_id}}">{{$profit->master_profit_center_kode}} ({{$profit->master_profit_unit}})</option>
															@endforeach
														</select>
													</div>
												</div>
											@endif
												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Cash Flow*</label>
													<div class="col-sm-9">
														<select class="form-control validate_sppb validate_spp_all" id="cash_flow_sppb_1" name="isi_sppb[{{$i}}][cash_flow]" >
														<option value="" disabled selected>-- Pilih Cash Flow --</option>
															<option value="{{$sppb[0][$i]['master_cash_flow_id']}}"  selected>{{$sppb[0][$i]['master_cash_flow_kode']}}  {{$sppb[0][$i]['master_cash_flow_keterangan']}}</option>
															
															@foreach($cashflow as $cash)
																<option value="{{$cash->master_cash_flow_id}}">{{$cash->master_cash_flow_kode}} {{$cash->master_cash_flow_keterangan}}</option>
															@endforeach
														</select>
													</div>
												</div>
											</div>
											<input type="hidden" id="jumlah_uraian_sppb" value="{{count($sppb[0][$i][0])}}">
											@for($a=0; $a< count($sppb[0][$i][0]) ; $a++)
											@if($a>=1)
												<div class="col-sm-5"></div>
											@endif
											<div id="sub_isi_sppb_{{$i}}_{{$a}}">
												<div class="col-md-6">
													<div class="form-group row">
														<label class="col-sm-1 col-form-label">{{$a+1}} </label>
														<label class="col-sm-2 col-form-label">Uraian*</label>
														<div class="col-sm-9">
															<div style="border: 1px solid hsla(0, 0%, 0%, 0.15);">
																<div id="uraian_sppb_{{$i}}_{{$a}}" style="height:auto;min-height:100px">
																	<!-- <input type="hidden" name="uraian_sppb[0][0][uraian]" id="uraian_sppb_value_1_1"> -->
																	<textarea class="form-control" id="ckeditor_{{$i}}_{{$a}}" name="uraian_sppb[{{$i}}][{{$a}}][ket]" >{{$sppb[0][$i][0][$a]->sppb_uraian_uraian}}</textarea>
																</div>
															</div>
														</div>
													</div>
													<div class="form-group row">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">Nominal*</label>
														<div class="col-sm-9">
															<input type="text"  id="jumlah_sppb_{{$i}}_{{$a}}" name="uraian_sppb[{{$i}}][{{$a}}][jumlah]" value="{{$sppb[0][$i][0][$a]->sppb_uraian_nominal}}" class="form-control nominal validate_sppb validate_spp_all" placeholder="Nominal SPPb" autocomplete="off" required>
														</div>
													</div>
												</div>
												<div class="col-sm-1">
													<div class="col-sm-12" style="margin-bottom: 10px">
														<button type="button" class="btn btn-danger btn-sm" id="hapus_sub_isi_sppb_{{$i}}_{{$a}}" onclick="hapus_sub_isi_sppb({{$i}},{{$a}},'ckeditor_{{$i}}_{{$a}}')" >X</button>
													</div>
													<div class="col-sm-12" style="margin-bottom: 10px">
														<button type="button" class="btn btn-success btn-sm" id="tambah_sub_isi_sppb_{{$i}}_{{$a}}" onclick="tambah_sub_isi_sppb({{$i}},{{$a}})">+</button>
													</div>
												</div>
											</div>
											@endfor
										</div>
										@endfor
										@endif
									</div>
									<!-- END TAB ISI -->
								
								</div>
							</div>
						</div>
						<!-- END FORM SPPB -->

						<!-- FORM SPPN -->
						<div class="panel" id="panel_sppn" style="display: none">
							<div class="panel-heading">
								Form SPPn
							</div>
							<div class="panel-body">

								<div class="custom-tabs-line tabs-line-bottom left-aligned">
									<ul class="nav" role="tablist">
										<li class="active"><a href="#tab-informasi-sppn" role="tab" data-toggle="tab">Informasi</a></li>
										<li><a href="#tab-isi-sppn" role="tab" data-toggle="tab">Isi</a></li>
									</ul>
								</div>
								<div class="tab-content">

									<!-- TAB INFORMASI -->
									<div class="tab-pane fade in active" id="tab-informasi-sppn">
									<div class="form-group row">
											<label class="col-sm-2 col-form-label">Tanggal*</label>
											<div class="col-sm-10">
											@if(isset($sppn['sppn_tanggal']))
												<input type="text" id="tanggal_sppn" name="tanggal_sppn" class="form-control validate_sppn validate_spp_all date" placeholder="Tanggal sppn" value="{{date('d-m-Y',strtotime($sppn['sppn_tanggal']))}}" autocomplete="off" required>
											@endif
											</div>
										</div>
										<div class="form-group row">
										<label class="col-md-2">Nomor SPP *</label>
										<div class="col-md-10" style="display:inline-flex">
											@if(isset($sppn['master_bagian_kode']))
												<input style="width:50px" type="text" id="kode_bagian_sppn" name="kode_bagian_sppn" class="form-control validate_sppn validate_spp_all" placeholder="Kode Bagian" value="{{$sppn['master_bagian_kode']}}" autocomplete="off" readonly required>/
											@endif
											@if(isset($sppn['sppn_urutan']))
												<input style="width:50px" type="text" id="urutan_sppn" name="urutan_sppn" class="form-control validate_sppn validate_spp_all" placeholder="Nomor Urut sppn" value="{{$sppn['sppn_urutan']}}" autocomplete="off" readonly required>/
											@endif
											@if(isset($sppn['sppn_bulan']))
												<input style="width:50px" type="text" id="bulan_sppn" name="bulan_sppn" class="form-control validate_sppn validate_spp_all" placeholder="Bulan sppn" value="{{$sppn['sppn_bulan']}}" autocomplete="off"  readonly required>/
											@endif
												@if(isset($sppn['sppn_tahun']))
												<input style="width:70px" type="text" id="tahun_sppn" name="tahun_sppn" class="form-control validate_sppn validate_spp_all" placeholder="Tahun sppn" value="{{$sppn['sppn_tahun']}}" autocomplete="off" readonly required>
											@endif
											</div>
										</div>
										<div class="form-group row" id="form_kwitansi_sppn">

											<label class="col-sm-2 col-form-label">Kwitansi*</label>
											<div class="col-sm-10">
											@if(isset($sppn['sppn_kwitansi']))
												<input type="text" id="kwitansi_sppn" name="kwitansi_sppn" class="form-control validate_sppn validate_spp_all" placeholder="Kwitansi SPPn" value="{{$sppn['sppn_kwitansi']}}" autocomplete="off">
											@else
												<input type="text" id="kwitansi_sppn" name="kwitansi_sppn" class="form-control validate_sppn validate_spp_all" placeholder="Kwitansi SPPn"  autocomplete="off">

											@endif
											</div>
										</div>
										<div class="form-group row" id="form_referensi_sppn">
											<label class="col-sm-2 col-form-label">Referensi*</label>
											<div class="col-sm-10">
											@if(isset($sppn['sppn_referensi']))
												<input type="text" id="referensi_sppn" name="referensi_sppn" class="form-control" placeholder="Referensi SPPn" value="{{$sppn['sppn_referensi']}}" autocomplete="off">
											@else
											<input type="text" id="referensi_sppn" name="referensi_sppn" class="form-control" placeholder="Referensi SPPn" autocomplete="off">

											@endif
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">BA/AU.58*</label>
											<div class="col-sm-10">
											@if(isset($sppn['sppn_ba_au_53']))
												<input type="text" id="au58_sppn" name="au58_sppn" class="form-control validate_sppn validate_spp_all" placeholder="BA/AU. 58 SPPn" value="{{$sppn['sppn_ba_au_53']}}" autocomplete="off">
											@else
												<input type="text" id="au58_sppn" name="au58_sppn" class="form-control validate_sppn validate_spp_all" placeholder="BA/AU. 58 SPPn" autocomplete="off">

											@endif
											</div>
										</div>

										<div id="fp_sppn">
											@if(isset($sppn[1][0]))
											<input type="hidden" id="jumlah_faktur_pajak_sppn" value="{{count($sppn[1])}}">

													<div id="faktur_pajak_sppn_1">
														<div class="form-group row" id="form_faktur_pajak_sppn">
															<label class="col-sm-2 col-form-label">Faktur Pajak *</label>
															<div class="col-sm-8">
																<input type="text" id="faktur_pajak_sppn" name="faktur_pajak_sppn[1][fp]" class="form-control validate_sppn validate_spp_all" placeholder="Faktur Pajak sppn 1" autocomplete="off" value="{{$sppn[1][0]->faktur_pajak_nomor}}">
															</div>
															<div class="col-sm-2" id="btn_faktur_pajak_sppn_1">
																<button type="button" class="btn btn-success btn-sm" onclick="tambah_faktur_pajak_sppn(1)">+</button>									
															</div>
														</div>
													</div>
												@for($i=1; $i< count($sppn[1]); $i++)
													<div id="faktur_pajak_sppn_{{$i+1}}">
														<div class="form-group row" id="form_faktur_pajak_sppn">
															<label class="col-sm-2 col-form-label"></label>
															<div class="col-sm-8">
																<input type="text" id="faktur_pajak_sppn" name="faktur_pajak_sppn[{{$i+1}}][fp]" class="form-control validate_sppn validate_spp_all" placeholder="Faktur Pajak sppn 1" autocomplete="off" value="{{$sppn[1][$i]->faktur_pajak_nomor}}">
															</div>
															<div class="col-sm-2" id="btn_faktur_pajak_sppn_{{$i+1}}">
																<button type="button" class="btn btn-success btn-sm" onclick="tambah_faktur_pajak_sppn({{$i+1}})">+</button>									
																<button type="button" class="btn btn-danger btn-sm" onclick="hapus_faktur_pajak_sppn({{$i+1}})">x</button>									
															
															</div>
														</div>
													</div>
												@endfor
											@endif
										</div>
										<div class="form-group row" id="form_sp_opl_sppn">
											<label class="col-sm-2 col-form-label">No. Kontrak *</label>
											<div class="col-sm-10">
											@if(isset($sppn['sppn_sp_opl']))
												<input type="text" id="sp_opl_sppn" name="sp_opl_sppn" class="form-control validate_sppn validate_spp_all" placeholder="Nomor Kontrak SPPn" value="{{$sppn['sppn_sp_opl']}}" autocomplete="off">
											@else
												<input type="text" id="sp_opl_sppn" name="sp_opl_sppn" class="form-control validate_sppn validate_spp_all" placeholder="Nomor Kontrak SPPn" autocomplete="off">

											@endif
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Bagian*</label>
											<div class="col-sm-10">
											@if(isset($sppn['master_bagian_id']))
												<select class="form-control validate_sppn validate_spp_all" id="bagian_sppn" name="bagian_sppn" readonly>
													<option value="" disabled>-- Pilih Bagian --</option>
													<option value="{{$sppn['master_bagian_id']}}" selected>{{$sppn['master_bagian_nama']}}</option>
												</select>
											@endif
											</div>
										</div>
										
										<!-- @if(isset($sppn['sppn_metode_pembayaran']))
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Metode Pembayaran *</label>
												<div class="col-sm-10">
													<select class="form-control validate_sppn validate_spp_all" id="metode_pembayaran_sppn" name="metode_pembayaran_sppn"  value="{{$sppn['sppn_metode_pembayaran']}}" required>
														<option value="{{$sppn['sppn_metode_pembayaran']}}" selected>{{$sppn['sppn_metode_pembayaran']}}</option>
														<option value="kas">Kas</option>
														<option value="bank">Bank</option>
														<option value="karyawan">Karyawan</option>
													</select>
												
												</div>
											</div>
											@if($karyawan_sppn !== null)
										<input type="hidden" value="{{count($karyawan_sppn)}}" id="jumlah_karyawan_sppn">
										@else
										<input type="hidden" value="0" id="jumlah_karyawan_sppn">
										@endif
											@if($sppn['sppn_metode_pembayaran'] == "karyawan")
											@if($karyawan_sppn[0]->karyawan_nama !== "TERLAMPIR")
										<div class="form-group row" style="display:block;" id="pilih_lampirkan_sppn" onclick="pilih_data_sppn()">
											<label class="col-sm-2 col-form-label"></label>
											<div class="col-sm-10">
												<div class="col-sm-2">
													<label class="fancy-radio">
													<input  name="pilih_data_sppn" id="input_data_sppn" value="input_data" type="radio" checked="checked" > 
													<span style="font-size:17px"><i ></i>Data diinputkan </span>
													</label>
												</div>
												<div class="col-sm-2">
													<label class="fancy-radio">
													<input  name="pilih_data_sppn" id="lampirkan_data_sppn" value="lampirkan_data" type="radio"  > 
													<span style="font-size:17px"><i ></i>Data dilampirkan</span>
													</label>
												</div>
											</div>
										</div>
										@else
										<div class="form-group row" style="display:block;" id="pilih_lampirkan_sppn" onclick="pilih_data_sppn()">
											<label class="col-sm-2 col-form-label"></label>
											<div class="col-sm-10">
												<div class="col-sm-2">
													<label class="fancy-radio">
													<input  name="pilih_data_sppn" id="input_data_sppn" value="input_data" type="radio" > 
													<span style="font-size:17px"><i ></i>Data diinputkan </span>
													</label>
												</div>
												<div class="col-sm-2">
													<label class="fancy-radio">
													<input  name="pilih_data_sppn" id="lampirkan_data_sppn" value="lampirkan_data" type="radio" checked="checked" > 
													<span style="font-size:17px"><i ></i>Data dilampirkan</span>
													</label>
												</div>
											</div>
										</div>
										@endif
											<div id="bank_sppn" style="display: none">
												<input type="hidden" id="id_bank_sppn_1" name="id_bank_sppn" class="form-control" onclick="data_bank_sppn(1)" placeholder="Nama Rekening Bank" autocomplete="off" required>												
												<div class="form-group row" id="atas_nama_vendor_sppn">
													<label class="col-sm-2 col-form-label">Atas Nama *</label>
													<div class="col-sm-10">
														<input type="text" id="atas_nama_bank_sppn_vendor" name="atas_nama_bank_sppn_vendor" class="form-control" onclick="data_bank_sppn(1)" placeholder="Atas Nama Bank sppn" autocomplete="off">
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nama Bank *</label>
													<div class="col-sm-10">
													<input type="text" id="nama_bank_sppn_vendor" name="nama_bank_sppn" class="form-control" onclick="data_bank_sppn(1)" placeholder="Nama Bank" autocomplete="off">												
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
													<div class="col-sm-10">
														<input type="text" id="rekening_bank_sppn_vendor" name="rekening_bank_sppn" class="form-control" onclick="data_bank_sppn(1)" placeholder="Nomor Rekening Bank sppn" autocomplete="off">
													</div>
												</div>
												
											</div>
											<div id="bank_sppn_karyawan" style="display: block">
												@foreach($karyawan_sppn as $v => $k)	
												<div id="bank_karyawan_sppn_{{$v+1}}">
												<div class="form-group row" id="atas_nama_karyawan_sppn">
													<label class="col-sm-2 col-form-label">Atas Nama Rekening {{$v+1}}*</label>
													<div class="col-sm-8">
														<input type="text" id="atas_nama_bank_sppn_karyawan_{{$v+1}}"  onclick="bank_karyawan_sppn({{$v+1}})" name="karyawan_sppn[{{$v+1}}][nama]" class="form-control" value="{{$k->karyawan_nama}}" placeholder="Atas Nama Bank SPPn" autocomplete="off">													
							
													</div>
													<div class="col-sm-2" id="btn_karyawan_bank_sppn_{{$v+1}}">
															<button type="button" class="btn btn-success btn-sm" onclick="tambah_karyawan_bank_sppn({{$v+1}})">+</button>									
															<button type="button" class="btn btn-danger btn-sm" id="btn_hapus_karyawan_sppn_{{$v+1}}" onclick="hapus_karyawan_bank_sppn({{$v+1}})">x</button>									
														
														</div>
												</div>			
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nama Bank {{$v+1}}*</label>
													<div class="col-sm-8">
													<input type="text" id="nama_bank_sppn_karyawan_{{$v+1}}" onclick="bank_karyawan_sppn({{$v+1}})" name="karyawan_sppn[{{$v+1}}][bank]" class="form-control" placeholder="Nama Bank sppn {{$v+1}}" value="{{$k->karyawan_nama_bank}}" autocomplete="off">
													</div>
													
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nomor Rekening {{$v+1}}*</label>
													<div class="col-sm-8">
														<input type="text" id="rekening_bank_sppn_karyawan_{{$v+1}}" onclick="bank_karyawan_sppn({{$v+1}})" name="karyawan_sppn[{{$v+1}}][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank sppn {{$v+1}}"  value="{{$k->karyawan_no_rek}}" autocomplete="off">
													</div>
												</div>
												
												</div>
												@endforeach
											</div>
											@elseif($sppn['sppn_metode_pembayaran'] == "bank")
											<div class="form-group row" style="display:none;" id="pilih_lampirkan_sppn" onclick="pilih_data_sppn()">
											<label class="col-sm-2 col-form-label"></label>
											<div class="col-sm-10">
												<div class="col-sm-2">
													<label class="fancy-radio">
													<input  name="pilih_data_sppn" id="input_data_sppn" value="input_data" type="radio" > 
													<span style="font-size:17px"><i ></i>Data diinputkan </span>
													</label>
												</div>
												<div class="col-sm-2">
													<label class="fancy-radio">
													<input  name="pilih_data_sppn" id="lampirkan_data_sppn" value="lampirkan_data" type="radio" checked="checked" > 
													<span style="font-size:17px"><i ></i>Data dilampirkan</span>
													</label>
												</div>
											</div>
										</div>
											<div id="bank_sppn" style="display: block">
													<input type="hidden" id="id_bank_sppn_1" name="id_bank_sppn" class="form-control" onclick="data_bank_sppn(1)" value="{{$sppn['master_vendor_id']}}" placeholder="Id Bank sppn" autocomplete="off">
												<div class="form-group row" id="atas_nama_vendor_sppn">
													<label class="col-sm-2 col-form-label">Atas Nama Rekening *</label>
													<div class="col-sm-10">
														<input type="text" id="atas_nama_bank_sppn_vendor" name="atas_nama_bank_sppn" class="form-control" onclick="data_bank_sppn(1)" value="{{$sppn['master_vendor_atas_nama']}}" placeholder="Atas Nama Bank sppn" autocomplete="off">
													
													</div>
												</div>
													<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nama Bank * </label>
													<div class="col-sm-10">
													
													<input type="text" id="nama_bank_sppn_vendor" name="nama_bank_sppn" class="form-control" onclick="data_bank_sppn(1)" value="{{$sppn['master_vendor_nama_bank']}}" placeholder="Nama Bank sppn" autocomplete="off">
													
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
													<div class="col-sm-10">
													
														<input type="text" id="rekening_bank_sppn_vendor" name="rekening_bank_sppn" class="form-control" onclick="data_bank_sppn(1)" value="{{$sppn['master_vendor_rekening']}}" placeholder="Nomor Rekening Bank sppn" autocomplete="off">
													
													</div>
												</div>
												
											</div>
											<div id="bank_sppn_karyawan" style="display: none">	
											<div class="form-group row" id="atas_nama_karyawan_sppn">
												<label class="col-sm-2 col-form-label">Atas Nama Rekening 1*</label>
												<div class="col-sm-8">
													<input type="text" id="atas_nama_bank_sppn_karyawan_1"  onclick="bank_karyawan_sppn(1)" name="karyawan_sppn[1][nama]" class="form-control"  placeholder="Atas Nama Bank SPPn" autocomplete="off">													
							
												</div>	
												<div class="col-sm-2" id="btn_karyawan_bank_sppn_1">
														<button type="button" class="btn btn-success btn-sm" onclick="tambah_karyawan_bank_sppn(1)">+</button>									
													</div>
											</div>								
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nama Bank 1*</label>
												<div class="col-sm-8">
												<input type="text" id="nama_bank_sppn_karyawan_1" onclick="bank_karyawan_sppn(1)" name="karyawan_sppn[1][bank]" class="form-control" placeholder="Nama Bank sppn" autocomplete="off">
												</div>
												
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nomor Rekening 1*</label>
												<div class="col-sm-8">
													<input type="text" id="rekening_bank_sppn_karyawan_1" onclick="bank_karyawan_sppn(1)" name="karyawan_sppn[1][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank sppn" autocomplete="off">
												</div>
											</div>
											
										</div>
											@else
											<div class="form-group row" style="display:none;" id="pilih_lampirkan_sppn" onclick="pilih_data_sppn()">
											<label class="col-sm-2 col-form-label"></label>
											<div class="col-sm-10">
												<div class="col-sm-2">
													<label class="fancy-radio">
													<input  name="pilih_data_sppn" id="input_data_sppn" value="input_data" type="radio" > 
													<span style="font-size:17px"><i ></i>Data diinputkan </span>
													</label>
												</div>
												<div class="col-sm-2">
													<label class="fancy-radio">
													<input  name="pilih_data_sppn" id="lampirkan_data_sppn" value="lampirkan_data" type="radio" checked="checked" > 
													<span style="font-size:17px"><i ></i>Data dilampirkan</span>
													</label>
												</div>
											</div>
										</div>
											<div id="bank_sppn" style="display: none">
											<input type="hidden" id="id_bank_sppn_1" name="id_bank_sppn" class="form-control" onclick="data_bank_sppn(1)" placeholder="Nama Rekening Bank" autocomplete="off" required>												
											<div class="form-group row" id="atas_nama_vendor_sppn">
												<label class="col-sm-2 col-form-label">Atas Nama *</label>
												<div class="col-sm-10">
													<input type="text" id="atas_nama_bank_sppn_vendor" name="atas_nama_bank_sppn_vendor" class="form-control" onclick="data_bank_sppn(1)" placeholder="Atas Nama Bank sppn" autocomplete="off">
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nama Bank *</label>
												<div class="col-sm-10">
												<input type="text" id="nama_bank_sppn_vendor" name="nama_bank_sppn" class="form-control" onclick="data_bank_sppn(1)" placeholder="Nama Bank" autocomplete="off">												
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
												<div class="col-sm-10">
													<input type="text" id="rekening_bank_sppn_vendor" name="rekening_bank_sppn" class="form-control" onclick="data_bank_sppn(1)" placeholder="Nomor Rekening Bank sppn" autocomplete="off">
												</div>
											</div>
											
											
										</div>
										<div id="bank_sppn_karyawan" style="display: none">	
											<div class="form-group row" id="atas_nama_karyawan_sppn">
												<label class="col-sm-2 col-form-label">Atas Nama Rekening 1*</label>
												<div class="col-sm-8">
													<input type="text" id="atas_nama_bank_sppn_karyawan_1"  onclick="bank_karyawan_sppn(1)" name="karyawan_sppn[1][nama]" class="form-control"  placeholder="Atas Nama Bank SPPn" autocomplete="off">													
							
												</div>	
												<div class="col-sm-2" id="btn_karyawan_bank_sppn_1">
														<button type="button" class="btn btn-success btn-sm" onclick="tambah_karyawan_bank_sppn(1)">+</button>									
													</div>
											</div>								
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nama Bank 1*</label>
												<div class="col-sm-8">
												<input type="text" id="nama_bank_sppn_karyawan_1" onclick="bank_karyawan_sppn(1)" name="karyawan_sppn[1][bank]" class="form-control" placeholder="Nama Bank sppn" autocomplete="off">
												</div>
												
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nomor Rekening 1*</label>
												<div class="col-sm-8">
													<input type="text" id="rekening_bank_sppn_karyawan_1" onclick="bank_karyawan_sppn(1)" name="karyawan_sppn[1][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank sppn" autocomplete="off">
												</div>
											</div>
											
										</div>
											@endif
										@else
										<div id="bank_sppn" style="display: none">
											<input type="hidden" id="id_bank_sppn_1" name="id_bank_sppn" class="form-control" onclick="data_bank_sppn(1)" placeholder="Nama Rekening Bank" autocomplete="off" required>												
											<div class="form-group row" id="atas_nama_vendor_sppn">
												<label class="col-sm-2 col-form-label">Atas Nama *</label>
												<div class="col-sm-10">
													<input type="text" id="atas_nama_bank_sppn_vendor" name="atas_nama_bank_sppn_vendor" class="form-control" onclick="data_bank_sppn(1)" placeholder="Atas Nama Bank sppn" autocomplete="off">
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nama Bank *</label>
												<div class="col-sm-10">
												<input type="text" id="nama_bank_sppn_vendor" name="nama_bank_sppn" class="form-control" onclick="data_bank_sppn(1)" placeholder="Nama Bank" autocomplete="off">												
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
												<div class="col-sm-10">
													<input type="text" id="rekening_bank_sppn_vendor" name="rekening_bank_sppn" class="form-control" onclick="data_bank_sppn(1)" placeholder="Nomor Rekening Bank sppn" autocomplete="off">
												</div>
											</div>
											
											
										</div>
										<div id="bank_sppn_karyawan" style="display: none">
											<div class="form-group row" id="atas_nama_karyawan_sppn">
												<label class="col-sm-2 col-form-label">Atas Nama Rekening 1*</label>
												<div class="col-sm-8">
													<input type="text" id="atas_nama_bank_sppn_karyawan_1"  onclick="bank_karyawan_sppn(1)" name="karyawan_sppn[1][nama]" class="form-control"  placeholder="Atas Nama Bank SPPn" autocomplete="off">													
						
												</div>
												<div class="col-sm-2" id="btn_karyawan_bank_sppn_1">
														<button type="button" class="btn btn-success btn-sm" onclick="tambah_karyawan_bank_sppn(1)">+</button>									
													</div>
											</div>									
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nama Bank 1*</label>
												<div class="col-sm-8">
												<input type="text" id="nama_bank_sppn_karyawan_1" name="karyawan_sppn[1][bank]" onclick="bank_karyawan_sppn(1)" class="form-control" placeholder="Nama Bank sppn" autocomplete="off">
												</div>
												
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nomor Rekening 1*</label>
												<div class="col-sm-8">
													<input type="text" id="rekening_bank_sppn_karyawan_1" name="karyawan_sppn[1][no_rek]" onclick="bank_karyawan_sppn(1)" class="form-control"  placeholder="Nomor Rekening Bank sppn" autocomplete="off">
												</div>
											</div>
											
										</div>
										@endif -->
										@if(isset($sppn['sppn_catatan']))
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Catatan</label>
											<div class="col-sm-10">
												<textarea class="form-control" id="catatan_sppn" name="catatan_sppn" placeholder="Catatan" rows="4">{{$sppn['sppn_catatan']}}</textarea>
											</div>
										</div>
										@else
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Catatan</label>
											<div class="col-sm-10">
												<textarea class="form-control" id="catatan_sppn" name="catatan_sppn" placeholder="Catatan" rows="4"></textarea>
											</div>
										</div>
										@endif
										
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Upload Dokumen Pendukung</label>
											<div class="col-sm-10">
											@if(isset($dokpensppn))
								              @foreach($dokpensppn as $d => $value)
									            <div id="dokumen_pendukung_sppn_{{$d}}">
												<a class="btn btn-default" href="{{ asset('dokumen/'.$value->dokumen_pendukung_sppn_nama)}}" target="_blank" >{{$value->dokumen_pendukung_sppn_nama}}</a>												
												<button type="button" class="btn btn-danger btn-sm" id="hapus_dokpen_sppn" onclick="hapus_dokumen_pendukung_sppn({{$d}})" >X</button>
												<input type="hidden" name="dokpenlama_sppn[{{$d}}]" value="{{$value->dokumen_pendukung_sppn_id}}">
												<br></br>
												</div>
											  @endforeach
											 
								            @endif
												{{-- <input type="file" id="dokumen_pendukung_sppn" name="dokumen_pendukung_sppn[]" class="file" data-preview-file-type="text" data-show-upload="true" data-show-caption="true" placeholder="Upload Dokumen Pendukung Lain" multiple> --}}
												<div class="file-loading">
												    <input type="file" id="dokumen_pendukung_sppn" name="dokumen_pendukung_sppn[]" class="file-multiple" accept="application/pdf, image/*" multiple>
												</div>
											</div>
										</div>
									</div>
									<!-- END TAB INFORMASI -->

									<!-- TAB ISI -->
									<div class="tab-pane fade" id="tab-isi-sppn">
									@if(isset($sppn[0]))
								
										<input type="hidden" id="jumlah_isi_sppn" value="{{count($sppn[0])}}">
										@for( $i=0; $i< count($sppn[0]) ; $i++)
										<div id="isi_sppn_{{$i}}" class="col-sm-12">
											<div  style="padding-bottom: 10px; margin-bottom: 20px; border-bottom: 1px solid #eaeaea;">
												<font size="4" style="margin-right: 20px">Isi {{$i+1}}. </font>
												<button type="button" class="btn btn-info btn-sm" id="tambah_isi_sppn_{{$i}}" onclick="tambah_isi_sppn({{$i}})">+</button>
												<button type="button" class="btn btn-danger btn-sm" id="hapus_isi_sppn_{{$i}}" onclick="hapus_isi_sppn({{$i}},'ckeditors_{{$i}}_',{{count($sppn[0][$i][0])}})">x</button>
											</div>
											<div class="col-sm-5">
											<div class="form-group row">
													<label class="col-sm-3 col-form-label">Kode KBB *</label>
													<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppn_{{$i+1}}" >
														<select class="selectpicker slct_sppn"  data-live-search="true" data-dropup-auto="false" id="kode_kbb_sppn_{{$i}}" data-width="100%" name="isi_sppn[{{$i}}][kode_kbb]" data-size="7" onchange="pilih_rekening_sppn({{$i+1}},'kode_kbb_sppn_')">
															<option value="{{$sppn[0][$i]['master_kode_kbb']}}"selected>{{$sppn[0][$i]['master_kode_kbb']}}</option>
															@foreach($rekening as $r)
															<option value="{{$r->master_rekening_kode_kbb}}">{{$r->master_rekening_kode_kbb}}</option>
															@endforeach
														</select>
														</div>
														
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Kode SAP *</label>
													<label class="col-sm-9">
														<select class="form-control validate_sppn validate_spp_all" id="jenis_sap_sppn_{{$i}}" onchange="js_sppn({{$i}})" name="isi_sppn[{{$i}}][jenis_sap]">
															@if($sppn[0][$i]['master_gl_id'] != null)
															<option value="vendor">Nomor Vendor</option>
															<option value="gl" selected>Nomor GL</option>
															<option value="customer">Nomor Customer</option>
															@elseif($sppn[0][$i]['master_customer_id'] != null)
															<option value="vendor" >Nomor Vendor</option>
															<option value="gl">Nomor GL</option>
															<option value="customer"selected >Nomor Customer</option>
															@else
															<option value="vendor" selected>Nomor Vendor</option>
															<option value="gl">Nomor GL</option>
															<option value="customer">Nomor Customer</option>
															@endif
														</select>
													</label>
													<label class="col-sm-3 col-form-label"></label>
													@if($sppn[0][$i]['master_gl_id'] != null)
													<div id="nomor_vendor_sppn_{{$i}}" style="display:none">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppn{{$i}}" >
																<select class="selectpicker slct_sppn"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_vendor_sppn_{{$i}}" data-size="7" name="isi_sppn_rekening" onchange="pilih_rekening_sppn({{$i}},'sap_vendor_sppn_')">
																	<option value="" disabled selected>-- Pilih Kode Vendor --</option>
																	@foreach($rekening as $r)
																	<option value="{{$r->master_rekening_id}}">{{$r->master_rekening_kode_sap}} ({{$r->master_rekening_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_vendor_sppn_id_{{$i}}" name="isi_sppn[{{$i}}][vendor]" class="form-control" onclick="kode_rekening_sppn({{$i}})" placeholder="Kode SAP (Nomor Vendor)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_customer_sppn_{{$i}}" style="display:none">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppn_{{$i}}" >
																<select class="selectpicker"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_customer_sppn_{{$i}}" data-size="7" name="isi_sppn_rekening" onchange="pilih_rekening_sppn({{$i}},'sap_customer_sppn_')">
																	<option value="" disabled selected>-- Pilih Kode Customer --</option>
																	@foreach($customer as $r)
																	<option value="{{$r->master_customer_id}}">{{$r->master_customer_kode_sap}} ({{$r->master_customer_nama}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_customer_sppn_id_{{$i}}" name="isi_sppn[{{$i}}][customer]" class="form-control" onclick="kode_customer_sppn({{$i}})" placeholder="Kode SAP (Nomor Customer)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_gl_sppn_{{$i}}" style="display:block">
														<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppn{{$i}}" >
																<select class="selectpicker slct_sppn"  data-live-search="true" data-dropup-auto="false" id="sap_gl_sppn_{{$i}}" data-size="7" data-width="100%" name="isi_sppn_rekening" onchange="pilih_rekening_sppn({{$i}},'sap_gl_sppn_')">
																	<option value="{{$sppn[0][$i]['master_gl_id']}}" selected>{{$sppn[0][$i]['master_gl_kode']}}</option>
																	@foreach($gl as $r)
																	<option value="{{$r->master_gl_id}}">{{$r->master_gl_kode}} ({{$r->master_gl_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_gl_sppn_id_{{$i}}" name="isi_sppn[{{$i}}][gl]" class="form-control" onclick="kode_gl_sppn({{$i}})" value="{{$sppn[0][$i]['master_gl_id']}}"placeholder="Kode SAP (Nomor GL)" autocomplete="off" required>
														</div>
													</div>
													
												</div>
												@elseif($sppn[0][$i]['master_customer_id'] != null)
												<div id="nomor_vendor_sppn_{{$i}}" style="display:none">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppn_{{$i}}" >
																<select class="selectpicker slct_sppn"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_vendor_sppn_{{$i}}" data-size="7" name="isi_sppn_rekening" onchange="pilih_rekening_sppn({{$i}},'sap_vendor_sppn_')">
																<option value="" disabled selected>-- Pilih Kode Vendor --</option>
																	@foreach($rekening as $r)
																	<option value="{{$r->master_rekening_id}}">{{$r->master_rekening_kode_sap}} ({{$r->master_rekening_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_vendor_sppn_id_{{$i}}" name="isi_sppn[{{$i}}][vendor]" value="{{$sppn[0][$i]['master_rekening_id']}}" class="form-control" onclick="kode_rekening_sppn({{$i}})" placeholder="Kode SAP (Nomor Vendor)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_customer_sppn_{{$i}}" style="display:block">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppn_{{$i}}" >
																<select class="selectpicker"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_customer_sppn_{{$i}}" data-size="7" name="isi_sppn_rekening" onchange="pilih_rekening_sppn({{$i}},'sap_customer_sppn_')">
																<option value="{{$sppn[0][$i]['master_customer_id']}}" selected>{{$sppn[0][$i]['master_customer_kode_sap']}} ({{$sppn[0][$i]['master_customer_nama']}})</option>
																	@foreach($customer as $r)
																	<option value="{{$r->master_customer_id}}">{{$r->master_customer_kode_sap}} ({{$r->master_customer_nama}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_customer_sppn_id_{{$i}}" name="isi_sppn[{{$i}}][customer]" class="form-control" value="{{$sppn[0][$i]['master_customer_id']}}" onclick="kode_customer_sppn({{$i}})" placeholder="Kode SAP (Nomor Customer)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_gl_sppn_{{$i}}" style="display:none">
														<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppn{{$i}}" >
																<select class="selectpicker slct_sppn"  data-live-search="true" data-dropup-auto="false" id="sap_gl_sppn_{{$i}}" data-size="7" data-width="100%" name="isi_sppn_rekening" onchange="pilih_rekening_sppn({{$i}},'sap_gl_sppn_')">
																	<option value="" disabled selected>-- Pilih Kode GL --</option>
																	@foreach($gl as $r)
																	<option value="{{$r->master_gl_id}}">{{$r->master_gl_kode}} ({{$r->master_gl_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_gl_sppn_id_{{$i}}" name="isi_sppn[{{$i}}][gl]" class="form-control" onclick="kode_gl_sppn({{$i}})" placeholder="Kode SAP (Nomor GL)" autocomplete="off" required>
														</div>
													</div>
													
												</div>
												@else
												
												<div id="nomor_vendor_sppn_{{$i}}" style="display:block">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppn_{{$i}}" >
																<select class="selectpicker slct_sppn"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_vendor_sppn_{{$i}}" data-size="7" name="isi_sppn_rekening" onchange="pilih_rekening_sppn({{$i}},'sap_vendor_sppn_')">
																	<option value="{{$sppn[0][$i]['master_rekening_id']}}" selected>{{$sppn[0][$i]['master_rekening_kode_sap']}}</option>
																	@foreach($rekening as $r)
																	<option value="{{$r->master_rekening_id}}">{{$r->master_rekening_kode_sap}} ({{$r->master_rekening_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_vendor_sppn_id_{{$i}}" name="isi_sppn[{{$i}}][vendor]" value="{{$sppn[0][$i]['master_rekening_id']}}" class="form-control" onclick="kode_rekening_sppn(1)" placeholder="Kode SAP (Nomor Vendor)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_customer_sppn_{{$i}}" style="display:none">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppn_{{$i}}" >
																<select class="selectpicker"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_customer_sppn_{{$i}}" data-size="7" name="isi_sppn_rekening" onchange="pilih_rekening_sppn({{$i}},'sap_customer_sppn_')">
																	<option value="" disabled selected>-- Pilih Kode Customer --</option>
																	@foreach($customer as $r)
																	<option value="{{$r->master_customer_id}}">{{$r->master_customer_kode_sap}} ({{$r->master_customer_nama}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_customer_sppn_id_{{$i}}" name="isi_sppn[{{$i}}][customer]" class="form-control" onclick="kode_customer_sppn({{$i}})" placeholder="Kode SAP (Nomor Customer)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_gl_sppn_{{$i}}" style="display:none">
														<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppn{{$i}}" >
																<select class="selectpicker slct_sppn"  data-live-search="true" data-dropup-auto="false" id="sap_gl_sppn_{{$i}}" data-size="7" data-width="100%" name="isi_sppn_rekening" onchange="pilih_rekening_sppn({{$i}},'sap_gl_sppn_')">
																	<option value="" disabled selected>-- Pilih Kode GL --</option>
																	@foreach($gl as $r)
																	<option value="{{$r->master_gl_id}}">{{$r->master_gl_kode}} ({{$r->master_gl_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_gl_sppn_id_{{$i}}" name="isi_sppn[{{$i}}][gl]" class="form-control" onclick="kode_gl_sppn({{$i}})" placeholder="Kode SAP (Nomor GL)" autocomplete="off" required>
														</div>
													</div>
													
												</div>
												@endif
												
													@if($sppn[0][$i]['master_cost_center_id'] !== null )
													<div class="form-group row">
													<label class="col-sm-3 col-form-label">Cost/Profit*</label>
													<label class="col-sm-9">
														<select class="form-control validate_sppn validate_spp_all" id="jenis_center_sppn_{{$i}}" onchange="jc_sppn({{$i}})" value="cost_center" name="isi_sppn[{{$i}}][jenis_center]">
															
															<option value="cost_center" selected>Cost Center</option>
															<option value="profit_center">Profit Center</option>
														</select>
													</label>
													<label class="col-sm-3 col-form-label"></label>
													<div class="col-sm-9" id="cost_center_sppn_{{$i}}" >
														<select class="form-control" id="select_cost_center_sppn_{{$i}}" name="isi_sppn[{{$i}}][cost_center]">
															<option value="{{$sppn[0][$i]['master_cost_center_id']}}" selected>{{$sppn[0][$i]['master_cost_center_kode']}}  {{$sppn[0][$i]['master_cost_center_keterangan']}}</option>
															@foreach($costcenter as $cost)
																<option value="{{$cost->master_cost_center_id}}">{{$cost->master_cost_center_kode}} {{$cost->master_cost_center_keterangan}}</option>
															@endforeach
														</select>
													</div>
													<div class="col-sm-9" id="profit_center_sppn_{{$i}}" style="display:none">
														<select class="form-control" id="select_profit_center_sppn_{{$i}}" name="isi_sppn[{{$i}}][profit_center]">
														<option value="" disabled selected>-- Pilih Profit Center --</option>
															@foreach($profitcenter as $profit)
																<option value="{{$profit->master_profit_center_id}}">{{$profit->master_profit_center_kode}} ({{$profit->master_profit_unit}})</option>
															@endforeach
														</select>
													</div>
												</div>
												@else
												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Cost/Profit*</label>
													<label class="col-sm-9">
														<select class="form-control validate_sppn validate_spp_all" id="jenis_center_sppn_{{$i}}" onchange="jc_sppn({{$i}})" value="cost_center" name="isi_sppn[{{$i}}][jenis_center]">
															
															<option value="cost_center">Cost Center</option>
															<option value="profit_center" selected>Profit Center</option>
														</select>
													</label>
													<label class="col-sm-3 col-form-label" ></label>
													<div class="col-sm-9" id="cost_center_sppn_{{$i}}" style="display:none">
														<select class="form-control" id="select_cost_center_sppn_{{$i}}" name="isi_sppn[{{$i}}][cost_center]">
															<option value="" disabled selected>-- Pilih Cost Center --</option>
															@foreach($costcenter as $cost)
																<option value="{{$cost->master_cost_center_id}}">{{$cost->master_cost_center_kode}} {{$cost->master_cost_center_keterangan}}</option>
															@endforeach
														</select>
													</div>
													<div class="col-sm-9" id="profit_center_sppn_{{$i}}" >
														<select class="form-control" id="select_profit_center_sppn_{{$i}}" name="isi_sppn[{{$i}}][profit_center]">
														<option value="{{$sppn[0][$i]['master_profit_center_id']}}" selected>{{$sppn[0][$i]['master_profit_center_kode']}}  ({{$sppn[0][$i]['master_profit_unit']}})</option>
															@foreach($profitcenter as $profit)
																<option value="{{$profit->master_profit_center_id}}">{{$profit->master_profit_center_kode}} ({{$profit->master_profit_unit}})</option>
															@endforeach
														</select>
													</div>
												</div>		
											@endif
											<div class="form-group row">
													<label class="col-sm-3 col-form-label">Cash Flow*</label>
													<div class="col-sm-9">
														<select class="form-control validate_sppn validate_spp_all" id="cash_flow_sppn" name="isi_sppn[{{$i}}][cash_flow]">
															<option value="" disabled selected>-- Pilih Cash Flow --</option>
															<option value="{{$sppn[0][$i]['master_cash_flow_id']}}"  selected>{{$sppn[0][$i]['master_cash_flow_kode']}}  {{$sppn[0][$i]['master_cash_flow_keterangan']}}</option>
															@foreach($cashflow as $cash)
																<option value="{{$cash->master_cash_flow_id}}">{{$cash->master_cash_flow_kode}} {{$cash->master_cash_flow_keterangan}}</option>
															@endforeach
														</select>
													</div>
												</div>
											</div>
											<input type="hidden" id="jumlah_uraian_sppn" value="{{count($sppn[0][$i][0])}}">
											@for($a=0; $a < count($sppn[0][$i][0]) ; $a++)
											@if($a>=1)
												<div class="col-sm-5"></div>
											@endif
											<div id="sub_isi_sppn_{{$i}}_{{$a}}">
												<div class="col-md-6">
													<div class="form-group row">
														<label class="col-sm-1 col-form-label">{{$a+1}} </label>
														<label class="col-sm-2 col-form-label">Uraian*</label>
														<div class="col-sm-9">
															<div style="border: 1px solid hsla(0, 0%, 0%, 0.15);">
																<div id="uraian_sppn_{{$i}}_{{$a}}" style="height:auto;min-height:100px">
																	<!-- <input type="hidden" name="uraian_sppn[0][0][uraian]" id="uraian_sppn_value_1_1"> -->
																	<textarea class="form-control" id="ckeditors_{{$i}}_{{$a}}" name="uraian_sppn[{{$i}}][{{$a}}][ket]" >{{$sppn[0][$i][0][$a]->sppn_uraian_uraian}}</textarea>
																</div>
															</div>
														</div>
													</div>
													<div class="form-group row">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">Nominal*</label>
														<div class="col-sm-9">
															<input type="text"  id="jumlah_sppn_{{$i}}_{{$a}}" name="uraian_sppn[{{$i}}][{{$a}}][jumlah]" value="{{$sppn[0][$i][0][$a]->sppn_uraian_nominal}}" class="form-control nominal validate_sppn validate_spp_all" placeholder="Nominal sppn" autocomplete="off" required>
														</div>
													</div>
												</div>
												<div class="col-sm-1">
													<div class="col-sm-12" style="margin-bottom: 10px">
														<button type="button" class="btn btn-danger btn-sm" id="hapus_sub_isi_sppn_{{$i}}_{{$a}}" onclick="hapus_sub_isi_sppn({{$i}},{{$a}},'ckeditors_{{$i}}_{{$a}}')" >X</button>
													</div>
													<div class="col-sm-12" style="margin-bottom: 10px">
														<button type="button" class="btn btn-success btn-sm" id="tambah_sub_isi_sppn_{{$i}}_{{$a}}" onclick="tambah_sub_isi_sppn({{$i}},{{$a}})">+</button>
													</div>
												</div>
											</div>
											@endfor
											
										</div>
										
										@endfor
										@endif
									</div>
									
									<!-- END TAB ISI -->
								</div>
							</div>
						</div>
						<!-- END FORM SPPN -->

						<!-- FORM SUBMIT -->
						<div class="panel" id="panel_submit">
							<div class="panel-body">
								<center>
									<br>
									<button class="btn btn-success" type="button"  id="simpan"  style="margin-bottom: 15px" >Simpan</button>
								</center>
							</div>
						</div>
						<input type="hidden" id="status_btn" name="status_btn">

						<!-- END FORM SUBMIT -->
					</div>
				</div>
			</div>
		</form>
	</div>
	<!-- END MAIN CONTENT -->
</div>
<!-- END MAIN -->
{{--Modal Simpan--}}
<div id="modal_simpan_spp" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- dialog body -->
            <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3 class="modal-title" style="margin: 0 auto; font-style:bold;">Apakah anda ingin menyimpan dan mencetak SPP?</h3>
            </div>
            <!-- dialog buttons -->
            <div class="modal-footer">
			<button type="button" class="btn btn-primary">Simpan Saja</button>
			<button type="button" class="btn btn-success">Simpan dan Cetak</button>
			</div>
        </div>
    </div>
</div>
{{-- Modal Rekening SPPb --}}
<div id="modal_rekening_sppb" class="modal fade" role="dialog">
	<div class="modal-dialog" style="width:800px">
		<!-- Modal content-->
		<div class="modal-content">
			<form>
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Pilih Rekening SPPb</h4>
				</div>
				<div class="modal-body">
					<table class="table table-bordered table-striped nowrap" style="width: 100%">
						<thead>
							<tr>
								<th style="display:none">id</th>
								<th>No. </th>
								<th>No KBB</th>
								<th>No SAP</th>
								<th>Keterangan</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($rekening as $key => $value)
							<tr>
								<td style="display:none">{{$value->master_rekening_id}}</td>
								<td>{{$key+1}}</td>
								<td>{{$value->master_rekening_kode_kbb}}</td>
								<td>{{$value->master_rekening_kode_sap}}</td>
								<td>{{$value->master_rekening_keterangan}}</td>
								<td>
									<button type="button" class="btn btn-info btn-sm" onclick="pilih_rekening_sppb('{{$value->master_rekening_id}}','{{$value->master_rekening_kode_kbb}}', '{{$value->master_rekening_kode_sap}}', '{{$value->master_rekening_keterangan}}')" title="Pilih" ><i class="fa fa-check"></i></button>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
{{-- End Modal Rekening SPPb --}}

{{-- Modal Rekening SPPn --}}
<div id="modal_rekening_sppn" class="modal fade" role="dialog">
	<div class="modal-dialog" style="width:800px">
		<!-- Modal content-->
		<div class="modal-content">
			<form>
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Pilih Rekening SPPn</h4>
				</div>
				<div class="modal-body">
					<table class="table table-bordered table-striped nowrap" style="width: 100%">
						<thead>
							<tr>
								<th>No. </th>
								<th style="display:none;">Id </th>
								<th>No KBB</th>
								<th>No SAP</th>
								<th>Keterangan</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@foreach ($rekening as $key => $value)
							<tr>
								<td>{{$key+1}}</td>
								<td style="display:none">{{$value->master_rekening_id}}</td>
								<td>{{$value->master_rekening_kode_kbb}}</td>
								<td>{{$value->master_rekening_kode_sap}}</td>
								<td>{{$value->master_rekening_keterangan}}</td>
								<td>
									<button type="button" class="btn btn-info btn-sm" onclick="pilih_rekening_sppn('{{$value->master_rekening_id}}','{{$value->master_rekening_kode_kbb}}', '{{$value->master_rekening_kode_sap}}', '{{$value->master_rekening_keterangan}}')" title="Pilih" ><i class="fa fa-check"></i></button>
								</td>
							</tr>
							@endforeach
							
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
{{-- End Modal Rekening SPPn --}}
{{-- Modal Bank SPPb --}}
<div id="modal_bank_sppb" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<form>
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Pilih Bank SPPb</h4>
				</div>
				<div class="modal-body">
					<table class="table table-bordered table-striped nowrap" style="width: 100%">
						<thead>
							<tr>
								<th>No. </th>
								<th style="display:none;">Id</th>
								<th>Nama Bank</th>
								<th>No Rekening</th>
								<th>Atas Nama</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@foreach ($vendor as $key => $value)
							<tr>
								<td>{{$key+1}}</td>
								<td style="display:none;">{{$value->master_vendor_id}}</td>
								<td>{{$value->master_vendor_nama_bank}}</td>
								<td>{{$value->master_vendor_rekening}}</td>
								<td>{{$value->master_vendor_atas_nama}}</td>
								<td style="text-align:center">
									<button type="button" class="btn btn-info btn-sm" onclick="pilih_bank_sppb('{{$value->master_vendor_id}}','{{$value->master_vendor_nama_bank}}', '{{$value->master_vendor_rekening}}', '{{$value->master_vendor_atas_nama}}')" title="Pilih" ><i class="fa fa-check"></i></button>
								</td>
							</tr>
							@endforeach
							
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
{{-- End Modal Bank SPPb --}}
{{-- Modal Bank SPPn --}}
<div id="modal_bank_sppn" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg" style="width: 1200px;">
		<!-- Modal content-->
		<div class="modal-content">
			<form>
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Pilih Bank SPPn</h4>
				</div>
				<div class="modal-body">
					<table class="table table-bordered table-striped nowrap" style="width: 100%">
						<thead>
							<tr>
								<th>No. </th>
								<th style="display:none;">id</th>
								<th>Nama Bank</th>
								<th>No Rekening</th>
								<th>Atas Nama</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@foreach ($vendor as $key => $value)
							<tr>
								<td>{{$key+1}}</td>
								<td style="display:none;">{{$value->master_vendor_id}}</td>
								<td>{{$value->master_vendor_nama_bank}}</td>
								<td>{{$value->master_vendor_rekening}}</td>
								<td>{{$value->master_vendor_atas_nama}}</td>
								<td style="text-align:center">
									<button type="button" class="btn btn-info btn-sm" onclick="pilih_bank_sppn('{{$value->master_vendor_id}}','{{$value->master_vendor_nama_bank}}', '{{$value->master_vendor_rekening}}', '{{$value->master_vendor_atas_nama}}')" title="Pilih" ><i class="fa fa-check"></i></button>
								</td>
							</tr>
							@endforeach
							
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
{{-- End Modal Bank SPPn --}}
{{-- Modal Karyawan SPPb --}}
<div id="modal_karyawan_sppb" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg" style="width: 1200px;">
		<!-- Modal content-->
		<div class="modal-content">
			<form>
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Pilih Karyawan SPPb</h4>
				</div>
				<div class="modal-body">
					<table class="table table-bordered table-striped nowrap" style="width: 100%">
						<thead>
							<tr>
								<th>No. </th>
								<th style="display:none;">Id</th>
								<th>Nama Karyawan</th>
								<th>Nama Bank</th>
								<th>No Rekening</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1;?>
						@foreach ($karyawan as $key => $value)
							<tr>
								<td>{{$i++}}</td>
								<td style="display:none;">{{$value->karyawan_id}}</td>
								<td>{{$value->karyawan_nama}}</td>
								<td>{{$value->karyawan_nama_bank}}</td>
								<td>{{$value->karyawan_no_rekening}}</td>
								<td style="text-align:center">
									<button type="button" class="btn btn-info btn-sm" onclick="pilih_karyawan_sppb('{{$value->karyawan_id}}','{{$value->karyawan_nama}}', '{{$value->karyawan_nama_bank}}', '{{$value->karyawan_no_rekening}}')" title="Pilih" ><i class="fa fa-check"></i></button>
								</td>
							</tr>
							@endforeach
							
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
{{-- End Modal Bank SPPb --}}
{{-- Modal Karyawan SPPn --}}
<div id="modal_karyawan_sppn" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg" style="width: 1200px;">
		<!-- Modal content-->
		<div class="modal-content">
			<form>
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Pilih Karyawan SPPn</h4>
				</div>
				<div class="modal-body">
					<table class="table table-bordered table-striped nowrap" style="width: 100%">
						<thead>
							<tr>
								<th>No. </th>
								<th style="display:none;">Id</th>
								<th>Nama Karyawan</th>
								<th>Nama Bank</th>
								<th>No Rekening</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1;?>
						@foreach ($karyawan as $key => $value)
							<tr>
								<td>{{$i++}}</td>
								<td style="display:none;">{{$value->karyawan_id}}</td>
								<td>{{$value->karyawan_nama}}</td>
								<td>{{$value->karyawan_nama_bank}}</td>
								<td>{{$value->karyawan_no_rekening}}</td>
								<td style="text-align:center">
									<button type="button" class="btn btn-info btn-sm" onclick="pilih_karyawan_sppn('{{$value->karyawan_id}}','{{$value->karyawan_nama}}', '{{$value->karyawan_nama_bank}}', '{{$value->karyawan_no_rekening}}')" title="Pilih" ><i class="fa fa-check"></i></button>
								</td>
							</tr>
							@endforeach
							
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
{{-- End Modal Bank SPPn --}}
<!-- Javascript -->
<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script type="text/javascript">

	var index_sppb = 1;
	var sub_index_sppb = [];
	sub_index_sppb[index_sppb] = index_sppb;
	var index_sppn = 1;
	var sub_index_sppn = [];
	sub_index_sppn[index_sppn] = index_sppn;
	var rekening_sppb_id = '';
	var rekening_sppb_id_id = '';
	var rekening_sppn_id = '';
	var rekening_sppn_id_id='';
	var bank_sppb_id = '';
	var rekening_bank_sppb_id = '';
	var atas_nama_bank_sppb_id = '';
	var bank_sppn_id = '';
	var rekening_bank_sppn_id = '';
	var atas_nama_bank_sppn_id = '';
	var id_bank_sppb_id ='';
	var id_bank_sppn_id = '';
	var uraian_sppb = [];
	var uraian_sppn = [];
	var index_bank_sppb_karyawan = '';
	var nama_bank_sppb_karyawan_id = '';
	var atas_nama_bank_sppb_karyawan_id = '';
	var rekening_bank_sppb_karyawan_id = '';
	var index_bank_sppn_karyawan = '';
	var nama_bank_sppn_karyawan_id = '';
	var atas_nama_bank_sppn_karyawan_id = '';
	var rekening_bank_sppn_karyawan_id = '';

	function hapus_dokumen_pendukung_sppb(isi){
		$("#dokumen_pendukung_sppb_"+isi).remove();
	}
	function hapus_dokumen_pendukung_sppn(isi){
		$("#dokumen_pendukung_sppn_"+isi).remove();
	}

	$(document).ready(function () {
		var isi_sppb = $("#jumlah_isi_sppb").val();
		for(var a=0; a<isi_sppb; a++){
			var uraian_sppb = $("#jumlah_uraian_sppb_"+a).val();
			for(var b=0; b<uraian_sppb; b++){
				CKEDITOR.inline( 'ckeditor_'+a+'_'+b );
			}
		}
		var isi_sppn = $("#jumlah_isi_sppn").val();
		for(var a=0; a<isi_sppn; a++){
			var pilihan = $('#jenis_center_sppn_'+a).val();
			if (pilihan == 'cost_center') {
				document.getElementById("select_cost_center_sppn_"+a).className = "form-control validate_sppn validate_spp_all"; 			
				document.getElementById("select_profit_center_sppn_"+a).className = "form-control"; 			
				var inputs = document.getElementsByClassName("validate_sppn");
				if(inputs){
					for(var i=0; i<inputs.length; i++){
						inputs[i].addEventListener("change",validateInput);
						inputs[i].addEventListener("keyup",validateInput);
					}
				}
			} else {
				document.getElementById("select_profit_center_sppn_"+a).className = "form-control validate_sppn validate_spp_all"; 			
				document.getElementById("select_cost_center_sppn_"+a).className = "form-control"; 		
				var inputs = document.getElementsByClassName("validate_sppn");
				if(inputs){
					for(var i=0; i<inputs.length; i++){
						inputs[i].addEventListener("change",validateInput);
						inputs[i].addEventListener("keyup",validateInput);
					}
				}
			}
			var uraian_sppn = $("#jumlah_uraian_sppn_"+a).val();
			for(var b=0; b<uraian_sppn; b++){
				CKEDITOR.inline( 'ckeditors_'+a+'_'+b );
			}
		}
		
    });

	$(document).ready(function() {

		var formspp = $("#formspp").val();
		var fp_spp = $("#jumlah_faktur_pajak_spp").val();
		var fp_sppb = $("#jumlah_faktur_pajak_sppb").val();
		var fp_sppn = $("#jumlah_faktur_pajak_sppn").val();
		var karyawan_sppb = $("#jumlah_karyawan_sppb").val();
		var karyawan_sppn = $("#jumlah_karyawan_sppn").val();
		var data_metpen_sppb = "";
		for (var i = 0; i < document.getElementsByName('pilih_data_sppb').length; i++){
			if(document.getElementsByName('pilih_data_sppb')[i].checked){
				data_metpen_sppb = document.getElementsByName('pilih_data_sppb')[i].value;
			}
		}
		if(formspp==2){
			$('#panel_sppn').show();
			$('#panel_sppb_sppn').hide();
			$('#form_kwitansi_sppn').show();
			$('#form_referensi_sppn').show();
			$('#form_faktur_pajak_sppn').show();
			$('#form_sp_opl_sppn').show();
			$('#form_kwitansi_sppb').show();
			$('#form_referensi_sppb').show();
			$('#form_faktur_pajak_sppb').show();
			$('#btn_hapus_karyawan_sppn_1').remove();

			$('#fp_sppb').show();
			$('#fp_sppn').show();
			$('#form_sp_opl_sppb').show();
				if(fp_sppn > 1){
					for(var i=1;i<fp_sppn;i++){
						$('#btn_faktur_pajak_sppn_'+i).hide();
					}
				}
				if(karyawan_sppn > 1){
					for(var i=1;i<karyawan_sppn;i++){
						
							$('#btn_karyawan_bank_sppn_'+i).hide();
						
					}
				}
		}
			
		
		else if(formspp==1){
			$('#panel_sppb').show();
			$('#panel_sppb_sppn').hide();
			$('#form_kwitansi_sppb').show();
			$('#form_referensi_sppb').show();
			$('#form_faktur_pajak_sppb').show();
			$('#form_sp_opl_sppb').show();
			$('#form_kwitansi_sppn').show();
			$('#form_referensi_sppn').show();
			$('#form_faktur_pajak_sppn').show();
			$('#form_sp_opl_sppn').show();
			$('#btn_hapus_karyawan_sppb_1').remove();

			$('#fp_sppb').show();
			$('#fp_sppn').show();
				if(fp_sppb > 1){
					for(var i=1;i<fp_sppb;i++){
						$('#btn_faktur_pajak_sppb_'+i).hide();
					}
				}
				if(karyawan_sppb > 1){
					for(var i=1;i<karyawan_sppb;i++){
						
							if(data_metpen_sppb == 'input_data'){
								$('#btn_karyawan_bank_sppb_input_'+i).hide();

							}else{
								$('#btn_karyawan_bank_sppb_'+i).hide();
							}
					}
				}
		}
		else {
			$('#panel_sppb').show();
			$('#panel_sppn').show();
			$('#panel_sppb_sppn').show();
			$('#form_kwitansi_sppb').hide();
			$('#form_referensi_sppb').hide();
			$('#form_faktur_pajak_sppb').hide();
			$('#form_sp_opl_sppb').hide();
			$('#form_kwitansi_sppn').hide();
			$('#form_referensi_sppn').hide();
			$('#form_faktur_pajak_sppn').hide();
			$('#form_sp_opl_sppn').hide();
			$('#btn_hapus_karyawan_sppb_1').remove();
			$('#btn_hapus_karyawan_sppn_1').remove();

			$('#fp_sppb').hide();
			$('#fp_sppn').hide();
				if(fp_spp > 1){
					for(var i=1;i<fp_spp;i++){
						$('#btn_faktur_pajak_spp_'+i).hide();
					}
				}
				if(karyawan_sppb > 1){
					for(var i=1;i<karyawan_sppb;i++){
					
							if(data_metpen_sppb == 'input_data'){
								$('#btn_karyawan_bank_sppb_input_'+i).hide();

							}else{
								$('#btn_karyawan_bank_sppb_'+i).hide();
							}
							

					
					}
				}
				if(karyawan_sppn > 1){
					for(var i=1;i<karyawan_sppn;i++){
						
							$('#btn_karyawan_bank_sppn_'+i).hide();
						
					}
				}
		}
		// var dokpensppb = [ @json($dokpensppb) ];
		// console.log(dokpensppb);
		
		$(".file").fileinput({
	        allowedFileTypes: ["image", "pdf"],
	        browseClass: "btn btn-primary btn-block",
	        maxFileSize: 3072,
	        showCaption: true,
	        showRemove: false,
	        showUpload: false,
	        showPreview: false,
			// initialPreview: [
			// 	dokpensppb
			// ],
			// initialPreviewAsData: true,
  			// initialPreviewConfig: [
    		// 	{type: ["image","pdf"], size: "100%", width: "100%", key: 1},
  			// ]
	    });
		
		
		$('.file-multiple').fileinput({
	        // uploadUrl: '#',
	        allowedFileTypes: ["image", "pdf"],
	        browseClass: "btn btn-primary btn-block",
	        showCaption: false,
	        showRemove: false,
	        showUpload: false,
		
			dropZoneTitle:"Drag & drop banyak file sekaligus disini..",
	        fileActionSettings: {
				showRemove: true,
				showUpload: false
			}
			
	    });


			
		$('.nominal').mask('0.000.000.000.000.000.000.000', {reverse: true});
		
		var isi_sppb = $("#jumlah_isi_sppb").val();
		if(isi_sppb == 1){
			$('#tambah_isi_sppb_0').show();
			$('#hapus_isi_sppb_0').hide();
			$('#tambah_sub_isi_sppb_0_0').show();
			$('#hapus_sub_isi_sppb_0_0').hide();
			CKEDITOR.inline('ckeditor_0_0');	
			var uraian_sppb = $("#jumlah_uraian_sppb").val();
			if(uraian_sppb>1){
				for (j=1;j<=uraian_sppb;j++){
				$('#tambah_sub_isi_sppb_0_0').hide();
				$('#tambah_sub_isi_sppb_0_'+j).show();
				$('#hapus_sub_isi_sppb_0_'+j).show();
				CKEDITOR.inline('ckeditor_0_'+j);
				}
			}
		}
		else{
			for(i=0;i<=isi_sppb;i++){
			a=i+1;
			$('#tambah_isi_sppb_0').hide();
			$('#tambah_isi_sppb_'+a).show();
			$('#hapus_isi_sppb_0').hide();
			$('#tambah_sub_isi_sppb_'+i+'_0').show();
			$('#hapus_sub_isi_sppb_'+i+'_0').hide();
			CKEDITOR.inline('ckeditor_'+i+'_0');	
			var uraian_sppb = $("#jumlah_uraian_sppb").val();
			if(uraian_sppb>1){
				for (j=1;j<=uraian_sppb;j++){
				$('#tambah_sub_isi_sppb_'+i+'_'+j).show();
				$('#hapus_sub_isi_sppb_'+i+'_'+j).show();
				CKEDITOR.inline('ckeditor_'+i+'_'+j);
				}
			}

		}
		}
		
		var isi_sppn = $("#jumlah_isi_sppn").val();
		if(isi_sppn == 1){
			$('#tambah_isi_sppn_0').show();
			$('#hapus_isi_sppn_0').hide();
			$('#tambah_sub_isi_sppn_0_0').show();
			$('#hapus_sub_isi_sppn_0_0').hide();
			CKEDITOR.inline('ckeditors_0_0');	
			var uraian_sppn = $("#jumlah_uraian_sppn").val();
			if(uraian_sppn>1){
				for (j=1;j<=uraian_sppn;j++){
				$('#tambah_sub_isi_sppn_0_0').hide();
				$('#tambah_sub_isi_sppn_0_'+j).show();
				$('#hapus_sub_isi_sppn_0_'+j).show();
				CKEDITOR.inline('ckeditors_0_'+j);
				}
			}
		}
		else{
			for(i=0;i<=isi_sppn;i++){
			a=i+1;
			$('#tambah_isi_sppn_0').hide();
			$('#tambah_isi_sppn_'+a).show();
			$('#hapus_isi_sppn_0').hide();
			$('#tambah_sub_isi_sppn_'+i+'_0').show();
			$('#hapus_sub_isi_sppn_'+i+'_0').hide();
			CKEDITOR.inline('ckeditors_'+i+'_0');	
			var uraian_sppn = $("#jumlah_uraian_sppn").val();
			if(uraian_sppn>1){
				for (j=1;j<=uraian_sppn;j++){
				$('#tambah_sub_isi_sppn_'+i+'_'+j).show();
				$('#hapus_sub_isi_sppn_'+i+'_'+j).show();
				CKEDITOR.inline('ckeditors_'+i+'_'+j);
				}
			}

		}
		}
		
		$('#tanggal_sppb').change(function(event){
			var datee = document.getElementById('tanggal_sppb').value;
			var newdate = datee.split("-").reverse().join("-");
			var tanggal = new Date(newdate);
			var bulan = tanggal.getMonth()+1;
			var tahun = tanggal.getFullYear();

			var konversiRomawi = function(nomor){
				var desimal = [10,9,5,4,1];
				var romawi = ['X','IX','V','IV','I'];
				var hasil = '';
				for(var index=0; index < desimal.length; index++){
					while(desimal[index] <= nomor){
						hasil += romawi[index];
						nomor -= desimal[index];
					}
				}
				return hasil;
			}
			if($('#formspp').val() == 1){
			var bulanromawi = konversiRomawi(bulan);
			$('#bulan_sppb').val(bulanromawi);
			$('#tahun_sppb').val(tahun);
			}
			else{
				var bulanromawi = konversiRomawi(bulan);
				$('#bulan_sppb').val(bulanromawi);
				$('#tahun_sppb').val(tahun);
				$('#tanggal_sppn').val(datee);
				$('#bulan_sppn').val(bulanromawi);
				$('#tahun_sppn').val(tahun);
			}
		});

		$('#tanggal_sppn').change(function(event){
			var datee = document.getElementById('tanggal_sppn').value;
			var newdate = datee.split("-").reverse().join("-");
			var tanggal = new Date(newdate);
			var bulan = tanggal.getMonth()+1;
			var tahun = tanggal.getFullYear();

			var konversiRomawi = function(nomor){
				var desimal = [10,9,5,4,1];
				var romawi = ['X','IX','V','IV','I'];
				var hasil = '';
				for(var index=0; index < desimal.length; index++){
					while(desimal[index] <= nomor){
						hasil += romawi[index];
						nomor -= desimal[index];
					}
				}
				return hasil;
			}
			if($('#formspp').val() == 2){
			var bulanromawi = konversiRomawi(bulan);
			$('#bulan_sppn').val(bulanromawi);
			$('#tahun_sppn').val(tahun);
			}
			else{
				var bulanromawi = konversiRomawi(bulan);
				$('#bulan_sppb').val(bulanromawi);
				$('#tahun_sppb').val(tahun);
				$('#tanggal_sppb').val(datee);
				$('#bulan_sppn').val(bulanromawi);
				$('#tahun_sppn').val(tahun);
			}
		});

		
		$('#jenis_spp').change(function(event) {
			if ($(this).val() == 'karyawan') {
				$('#panel_jenis_form').show();
				$('#panel_dokumen_pendukung').hide();
				$("#kontrak_perjanjian_sppb").attr('required', false);
				$("#invoice_sppb").attr('required',false );
				$("#efaktur_sppb").attr('required', false);
				$("#berita_acara_sppb").attr('required', false);
				$("#dokumen_pendukung_sppb").attr('required', false);
				$("#dokumen_pendukung_sppn").attr('required', false);
				$("#kwitansi_sppb").attr('required', false);
				$("#referensi_sppb").attr('required', false);
				$("#au53_sppb").attr('required', false);
				$("#berita_acara_sppb").attr('required', false);
				$("#catatan_sppb").attr('required', false);

				
			} else if ($(this).val() == 'vendor') {
				$('#panel_jenis_form').show();
				$('#panel_dokumen_pendukung').show();
				$("#kontrak_perjanjian_sppb").attr('required', true);
				$("#invoice_sppb").attr('required', true);
				$("#efaktur_sppb").attr('required', true);
				$("#berita_acara_sppb").attr('required', true);
				$("#dokumen_pendukung_sppb").attr('required', true);
				$("#dokumen_pendukung_sppn").attr('required', true);
				$("#kwitansi_sppb").attr('required', true);
				$("#referensi_sppb").attr('required', false);
				$("#au53_sppb").attr('required', false);
				$("#berita_acara_sppb").attr('required', true);
				$("#catatan_sppb").attr('required', false);
			}
		});

		$('#jenis_form').change(function(event) {
			if ($(this).val() == 'sppb') {
				$('#panel_sppb').show();
				$('#panel_sppn').hide();
				// $("#metode_pembayaran_sppn").attr('required',false);
				$("#bank_sppn_1").attr('required',false);
				$("#dokumen_pendukung_sppn").attr('required',false);
				$("#rekening_sppn_1").attr('required',false);
				$("#jumlah_sppn_1_1").attr('required',false);
			} else if ($(this).val() == 'sppn') {
				$('#panel_sppb').hide();
				$('#panel_sppn').show();
				$('#panel_dokumen_pendukung').show();
			} else {
				$('#panel_sppb').show();
				$('#panel_sppn').show();
			}
			$('#panel_submit').show();
		});

	});

	$(document).ready(function(){
		if ($("#formspp").val() == 1) {
		var inputs = document.getElementsByClassName("validate_sppb");
		for(var i in CKEDITOR.instances){
			if (i.substring(0,9) == "ckeditor_"){
			CKEDITOR.instances[i].on('change',function(){
				var urai = document.getElementById(this.name).parentElement;
				if(this.getData().replace(/<[^>]+>/g, '') == ""){
					urai.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px";
				}
				else{
					urai.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px";
				}
			})
			}
		}
		}
		else if($("#formspp").val() == 2) {
		var inputs = document.getElementsByClassName("validate_sppn");
		for(var i in CKEDITOR.instances){

			if (i.substring(0,9) == "ckeditors"){
			CKEDITOR.instances[i].on('change',function(){
				var urai = document.getElementById(this.name).parentElement;
				if(this.getData().replace(/<[^>]+>/g, '') == ""){
					urai.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px";
				}
				else{
					urai.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px";
				}
			})
			}
		}
		}
		else{
		var inputs = document.getElementsByClassName("validate_spp_all");
		for(var i in CKEDITOR.instances){
			CKEDITOR.instances[i].on('change',function(){
				var urai = document.getElementById(this.name).parentElement;
				if(this.getData().replace(/<[^>]+>/g, '') == ""){
					urai.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px";
				}
				else{
					urai.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px";
				}
			})
			}
		}

		
        if(inputs){
            for(var i=0; i<inputs.length; i++){
                inputs[i].addEventListener("change",validateInput);
				inputs[i].addEventListener("keyup",validateInput);
                inputs[i].addEventListener("focus",validateInput);

            }
        }
        var btn_simpan = document.getElementById("simpan");
		
			if(btn_simpan){
				btn_simpan.addEventListener("click",validateForm);
			}
		
    });

	function validateInput(){
        if(this.name == "urutan_sppb" || this.name == "urutan_sppn" || this.name == "kode_bagian_sppn" || this.name == "kode_bagian_sppb" || this.name == "bulan_sppn" || this.name == "bulan_sppb"){
			if (this.value == null || this.value == "" ) {
            	this.style.cssText = "width:50px;border-width:2px;border-color:red;border-style:solid;border-radius:1px;";
        	} else{
            	this.style.cssText = "width:50px;border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;";
        	}
		}
		else if(this.name == "tahun_sppb" || this.name == "tahun_sppn"){
			if (this.value == null || this.value == "" ) {
            	this.style.cssText = "width:70px;border-width:2px;border-color:red;border-style:solid;border-radius:1px;";
        	} else{
            	this.style.cssText = "width:70px;border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;";
        	}
		}
		else{
			if (this.value == null || this.value == "" ) {
            	this.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;";
        	} else{
            	this.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;";
        	}
		}
    }

	function validatefile(){
			var a = document.getElementById(this.name).parentElement.parentElement.parentElement;
			if(this.files[0]){
				a.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;";
			}
			else{
				a.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;";
			}00
	}

	function validateForm(e){
		var hasEmpty = false;
		
        if ($("#formspp").val() == 1) {
			var inputs = document.getElementsByClassName("validate_sppb");
				for(var i in CKEDITOR.instances){
					if (i.substring(0,9) == "ckeditor_"){
						var urai = document.getElementById(CKEDITOR.instances[i].name).parentElement;
						if(CKEDITOR.instances[i].getData().replace(/<[^>]+>/g, '') == ""){
							urai.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px";
							hasEmpty = true;
						}
						else{
							urai.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px";
						}
					}
				}
			}
			
		else if($("#formspp").val() == 2) {
			var inputs = document.getElementsByClassName("validate_sppn");
			for(var i in CKEDITOR.instances){
				if (i.substring(0,9) == "ckeditors"){
					var urai = document.getElementById(CKEDITOR.instances[i].name).parentElement;
					if(CKEDITOR.instances[i].getData().replace(/<[^>]+>/g, '') == ""){
						urai.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px";
						hasEmpty = true;
					}
					else{
						urai.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px";
					}
				}
			}	
		}
		else{
			var inputs = document.getElementsByClassName("validate_spp_all");
			for(var i in CKEDITOR.instances){
				var urai = document.getElementById(CKEDITOR.instances[i].name).parentElement;
				if(CKEDITOR.instances[i].getData().replace(/<[^>]+>/g, '') == ""){
					urai.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px";
					hasEmpty = true;
				}
				else{
					urai.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px";
				}
			}	
		}
		
        for(var i=0; i<inputs.length; i++){
            validateInput.call(inputs[i]);
            if(inputs[i].value=="" || inputs[i].value==null) 
				hasEmpty = true;	
        }

        if(hasEmpty == true){
            e.preventDefault();
			Swal.fire("Form belum terisi dengan lengkap!","","warning");
        }
		else{
			e.preventDefault();
			simpan_spp.call();
		}
    }
	$(document).ready(function(){
		var formspp = $("#formspp").val();
		if(formspp != 2){
			console.log('tidak dua');
			var radio_check_val = "";
			for (var i = 0; i < document.getElementsByName('pilih_data_sppb').length; i++){
				if(document.getElementsByName('pilih_data_sppb')[i].checked){
					radio_check_val = document.getElementsByName('pilih_data_sppb')[i].value;
				}
			}

			if(radio_check_val == "input_data"){
				if($('#metode_pembayaran_sppb').val() == 'karyawan'){
					$('#bank_sppb').hide();
					$('#bank_sppb_karyawan').show();
					$('#bank_sppb_karyawan_input').show();
					$('#bank_sppb_karyawan_master').hide();
					document.getElementById("nama_bank_sppb_vendor").className = "form-control"; 			
					document.getElementById("rekening_bank_sppb_vendor").className = "form-control"; 			
					document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control"; 			
					document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control validate_sppb validate_spp_all";
					document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control validate_sppb validate_spp_all";
					document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control validate_sppb validate_spp_all";
					document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
					document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
					document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
					var inputs = document.getElementsByClassName("validate_sppb");
					if(inputs){
						for(var i=0; i<inputs.length; i++){
							inputs[i].addEventListener("change",validateInput);
							inputs[i].addEventListener("focus",validateInput);
						}
					}
				}
				else if($('#metode_pembayaran_sppb').val() == 'bank'){
					$('#bank_sppb').show();
					$('#bank_sppb_karyawan').hide();
					document.getElementById("nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
					document.getElementById("rekening_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
					document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
					document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
					document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
					document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
					document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
					document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
					document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
					document.getElementById("nama_bank_sppb_vendor").onclick = function(){ }; 			
						document.getElementById("rekening_bank_sppb_vendor").onclick = function(){ }; 	
						document.getElementById("atas_nama_bank_sppb_vendor").onclick = function(){ };
					var inputs = document.getElementsByClassName("validate_sppb");
					if(inputs){
						for(var i=0; i<inputs.length; i++){
							inputs[i].addEventListener("change",validateInput);
							inputs[i].addEventListener("focus",validateInput);
						}
					}
				}
				else if($('#metode_pembayaran_sppb').val() == 'skbdn'){
					$('#bank_sppb').show();
					$('#bank_sppb_karyawan').hide();
					document.getElementById("nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
					document.getElementById("rekening_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
					document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
					document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
					document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
					document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
					document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
					document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
					document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
					document.getElementById("nama_bank_sppb_vendor").onclick = function(){ }; 			
						document.getElementById("rekening_bank_sppb_vendor").onclick = function(){ }; 	
						document.getElementById("atas_nama_bank_sppb_vendor").onclick = function(){ };
					var inputs = document.getElementsByClassName("validate_sppb");
					if(inputs){
						for(var i=0; i<inputs.length; i++){
							inputs[i].addEventListener("change",validateInput);
							inputs[i].addEventListener("focus",validateInput);
						}
					}
				}
				
			}
			else if(radio_check_val == "master_data"){
				if($('#metode_pembayaran_sppb').val() == 'karyawan'){
					$('#bank_sppb').hide();
					$('#bank_sppb_karyawan').show();
					$('#bank_sppb_karyawan_input').hide();
					$('#bank_sppb_karyawan_master').show();
					document.getElementById("nama_bank_sppb_vendor").className = "form-control"; 			
					document.getElementById("rekening_bank_sppb_vendor").className = "form-control"; 			
					document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control"; 			
					document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control ";
					document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
					document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
					document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control validate_sppb validate_spp_all";
					document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control validate_sppb validate_spp_all";
					document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control validate_sppb validate_spp_all";
					var inputs = document.getElementsByClassName("validate_sppb");
					if(inputs){
						for(var i=0; i<inputs.length; i++){
							inputs[i].addEventListener("change",validateInput);
							inputs[i].addEventListener("focus",validateInput);
						}
					}
				}
				else if($('#metode_pembayaran_sppb').val() == 'bank'){
					$('#bank_sppb').show();
					$('#bank_sppb_karyawan').hide();
					document.getElementById("nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
					document.getElementById("rekening_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
					document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
					document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
					document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
					document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
					document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
					document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
					document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
					document.getElementById("nama_bank_sppb_vendor").onclick = function(){data_bank_sppb() }; 			
						document.getElementById("rekening_bank_sppb_vendor").onclick = function(){ data_bank_sppb()}; 	
						document.getElementById("atas_nama_bank_sppb_vendor").onclick = function(){data_bank_sppb() };
					var inputs = document.getElementsByClassName("validate_sppb");
					if(inputs){
						for(var i=0; i<inputs.length; i++){
							inputs[i].addEventListener("change",validateInput);
							inputs[i].addEventListener("focus",validateInput);
						}
					}
				}
				else if($('#metode_pembayaran_sppb').val() == 'skbdn'){
					$('#bank_sppb').show();
					$('#bank_sppb_karyawan').hide();
					document.getElementById("nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
					document.getElementById("rekening_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
					document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
					document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
					document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
					document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
					document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
					document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
					document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
					document.getElementById("nama_bank_sppb_vendor").onclick = function(){ data_bank_sppb()}; 			
						document.getElementById("rekening_bank_sppb_vendor").onclick = function(){ data_bank_sppb()}; 	
						document.getElementById("atas_nama_bank_sppb_vendor").onclick = function(){ data_bank_sppb()};
					var inputs = document.getElementsByClassName("validate_sppb");
					if(inputs){
						for(var i=0; i<inputs.length; i++){
							inputs[i].addEventListener("change",validateInput);
							inputs[i].addEventListener("focus",validateInput);
						}
					}
				}
			}
			else{
				$('#bank_sppb').hide();
				$('#bank_sppb_karyawan').hide();
				$('#bank_sppb_karyawan_master').hide();
				$('#bank_sppb_karyawan_input').hide();

				document.getElementById("nama_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("rekening_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
					
				var inputs = document.getElementsByClassName("validate_sppb");
				if(inputs){
					for(var i=0; i<inputs.length; i++){
						inputs[i].addEventListener("change",validateInput);
						inputs[i].addEventListener("focus",validateInput);
					}
				}
			}	
		}
		
	
		// if ($('#metode_pembayaran_sppn').val() == 'bank') {
			
		// 	$('#bank_sppn').show();
		// 	$('#bank_sppn_karyawan').hide();
		// 	document.getElementById("nama_bank_sppn_vendor").className = "form-control validate_sppn validate_spp_all"; 			
		// 	document.getElementById("rekening_bank_sppn_vendor").className = "form-control validate_sppn validate_spp_all"; 			
		// 	document.getElementById("atas_nama_bank_sppn_vendor").className = "form-control validate_sppn validate_spp_all"; 			
		// 	document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control";
		// 	document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control";
		// 	document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control";
			
		// 	$('#atas_nama_vendor_sppn').show();
		// 	var inputs = document.getElementsByClassName("validate_sppn");
		// 	if(inputs){
		// 		for(var i=0; i<inputs.length; i++){
		// 			inputs[i].addEventListener("change",validateInput);
		// 			inputs[i].addEventListener("focus",validateInput);
		// 		}
        // 	}
		// }
		// 	else if ($('#metode_pembayaran_sppn').val() == 'karyawan') {
		// 	$('#bank_sppn').hide();
			
		// 	$('#pilih_lampirkan_sppn').show();
		// 	document.getElementById("nama_bank_sppn_vendor").className = "form-control"; 			
		// 	document.getElementById("rekening_bank_sppn_vendor").className = "form-control"; 			
		// 	document.getElementById("atas_nama_bank_sppn_vendor").className = "form-control"; 			
			
		// 	var inputs = document.getElementsByClassName("validate_sppn");
		// 	if(inputs){
		// 		for(var i=0; i<inputs.length; i++){
		// 			inputs[i].addEventListener("change",validateInput);
		// 			inputs[i].addEventListener("focus",validateInput);
		// 		}
        // 	}
		// 	var radio_check_val = "";
		// 	for (var i = 0; i < document.getElementsByName('pilih_data_sppn').length; i++){
		// 		if(document.getElementsByName('pilih_data_sppn')[i].checked){
		// 			radio_check_val = document.getElementsByName('pilih_data_sppn')[i].value;
		// 		}
		// 	}
		// 	if(radio_check_val == "input_data"){
		// 		$('#bank_sppn_karyawan').show();
		// 		document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control validate_sppn validate_spp_all";
		// 		document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control validate_sppn validate_spp_all";
		// 		document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control validate_sppn validate_spp_all";
			
		// 	}
		// 	else{
		// 		$('#bank_sppn_karyawan').hide();
		// 		document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control";
		// 		document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control";
		// 		document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control";
			
		// 	}
		// }else {
		// 	$('#bank_sppn').hide();
		// 	$('#bank_sppn_karyawan').hide();
		// 	document.getElementById("nama_bank_sppn_vendor").className = "form-control"; 			
		// 	document.getElementById("rekening_bank_sppn_vendor").className = "form-control "; 			
		// 	document.getElementById("atas_nama_bank_sppn_vendor").className = "form-control "; 			
		// 	document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control";
		// 	document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control";
		// 	document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control";
		// }
	});


// 	function pilih_data_sppn() {
// 		var radio_check_val = "";
// 		for (var i = 0; i < document.getElementsByName('pilih_data_sppn').length; i++){
// 			if(document.getElementsByName('pilih_data_sppn')[i].checked){
// 				radio_check_val = document.getElementsByName('pilih_data_sppn')[i].value;
// 			}
// 		}
// 		if(radio_check_val == "input_data"){
// 			$('#bank_sppn_karyawan').show();
// 			$('#bank_sppn').hide();
// 			document.getElementById("nama_bank_sppn_vendor").className = "form-control "; 			
// 			document.getElementById("rekening_bank_sppn_vendor").className = "form-control "; 			
// 			document.getElementById("atas_nama_bank_sppn_vendor").className = "form-control "; 			
// 			document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control validate_sppn validate_spp_all";
// 			document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control validate_sppn validate_spp_all";
// 			document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control validate_sppn validate_spp_all";
// 			var inputs = document.getElementsByClassName("validate_sppn");
// 			if(inputs){
// 				for(var i=0; i<inputs.length; i++){
// 					inputs[i].addEventListener("change",validateInput);
// 					inputs[i].addEventListener("focus",validateInput);
// 				}
//         	}
// 		}
// 		else{
// 			$('#bank_sppn_karyawan').hide();
// 			$('#bank_sppn').hide();
// 			document.getElementById("nama_bank_sppn_vendor").className = "form-control "; 			
// 			document.getElementById("rekening_bank_sppn_vendor").className = "form-control "; 			
// 			document.getElementById("atas_nama_bank_sppn_vendor").className = "form-control "; 			
// 			document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control";
// 			document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control";
// 			document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control";
// 		}
// }

		
function pilih_data_sppb() {
		var radio_check_val = "";
		for (var i = 0; i < document.getElementsByName('pilih_data_sppb').length; i++){
			if(document.getElementsByName('pilih_data_sppb')[i].checked){
				radio_check_val = document.getElementsByName('pilih_data_sppb')[i].value;
			}
		}

		if(radio_check_val == "input_data"){
			if($('#metode_pembayaran_sppb').val() == 'karyawan'){
				$('#bank_sppb').hide();
				$('#bank_sppb_karyawan').show();
				$('#bank_sppb_karyawan_input').show();
				$('#bank_sppb_karyawan_master').hide();
				document.getElementById("nama_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("rekening_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control validate_sppb validate_spp_all";
				document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control validate_sppb validate_spp_all";
				document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control validate_sppb validate_spp_all";
				document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
				var inputs = document.getElementsByClassName("validate_sppb");
				if(inputs){
					for(var i=0; i<inputs.length; i++){
						inputs[i].addEventListener("change",validateInput);
						inputs[i].addEventListener("focus",validateInput);
					}
				}
			}
			else if($('#metode_pembayaran_sppb').val() == 'bank'){
				$('#bank_sppb').show();
				$('#bank_sppb_karyawan').hide();
				document.getElementById("nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("rekening_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_vendor").onclick = function(){ }; 			
					document.getElementById("rekening_bank_sppb_vendor").onclick = function(){ }; 	
					document.getElementById("atas_nama_bank_sppb_vendor").onclick = function(){ };
				var inputs = document.getElementsByClassName("validate_sppb");
				if(inputs){
					for(var i=0; i<inputs.length; i++){
						inputs[i].addEventListener("change",validateInput);
						inputs[i].addEventListener("focus",validateInput);
					}
				}
			}
			else if($('#metode_pembayaran_sppb').val() == 'skbdn'){
				$('#bank_sppb').show();
				$('#bank_sppb_karyawan').hide();
				document.getElementById("nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("rekening_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_vendor").onclick = function(){ }; 			
					document.getElementById("rekening_bank_sppb_vendor").onclick = function(){ }; 	
					document.getElementById("atas_nama_bank_sppb_vendor").onclick = function(){ };
				var inputs = document.getElementsByClassName("validate_sppb");
				if(inputs){
					for(var i=0; i<inputs.length; i++){
						inputs[i].addEventListener("change",validateInput);
						inputs[i].addEventListener("focus",validateInput);
					}
				}
			}
			
		}
		else if(radio_check_val == "master_data"){
			if($('#metode_pembayaran_sppb').val() == 'karyawan'){
				$('#bank_sppb').hide();
				$('#bank_sppb_karyawan').show();
				$('#bank_sppb_karyawan_input').hide();
				$('#bank_sppb_karyawan_master').show();
				document.getElementById("nama_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("rekening_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control ";
				document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control validate_sppb validate_spp_all";
				document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control validate_sppb validate_spp_all";
				document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control validate_sppb validate_spp_all";
				var inputs = document.getElementsByClassName("validate_sppb");
				if(inputs){
					for(var i=0; i<inputs.length; i++){
						inputs[i].addEventListener("change",validateInput);
						inputs[i].addEventListener("focus",validateInput);
					}
				}
			}
			else if($('#metode_pembayaran_sppb').val() == 'bank'){
				$('#bank_sppb').show();
				$('#bank_sppb_karyawan').hide();
				document.getElementById("nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("rekening_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_vendor").onclick = function(){data_bank_sppb() }; 			
					document.getElementById("rekening_bank_sppb_vendor").onclick = function(){ data_bank_sppb()}; 	
					document.getElementById("atas_nama_bank_sppb_vendor").onclick = function(){data_bank_sppb() };
				var inputs = document.getElementsByClassName("validate_sppb");
				if(inputs){
					for(var i=0; i<inputs.length; i++){
						inputs[i].addEventListener("change",validateInput);
						inputs[i].addEventListener("focus",validateInput);
					}
				}
			}
			else if($('#metode_pembayaran_sppb').val() == 'skbdn'){
				$('#bank_sppb').show();
				$('#bank_sppb_karyawan').hide();
				document.getElementById("nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("rekening_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_vendor").onclick = function(){ data_bank_sppb()}; 			
					document.getElementById("rekening_bank_sppb_vendor").onclick = function(){ data_bank_sppb()}; 	
					document.getElementById("atas_nama_bank_sppb_vendor").onclick = function(){ data_bank_sppb()};
				var inputs = document.getElementsByClassName("validate_sppb");
				if(inputs){
					for(var i=0; i<inputs.length; i++){
						inputs[i].addEventListener("change",validateInput);
						inputs[i].addEventListener("focus",validateInput);
					}
				}
			}
		}
		else{
			$('#bank_sppb').hide();
			$('#bank_sppb_karyawan').hide();
			$('#bank_sppb_karyawan_master').hide();
			$('#bank_sppb_karyawan_input').hide();

			document.getElementById("nama_bank_sppb_vendor").className = "form-control"; 			
			document.getElementById("rekening_bank_sppb_vendor").className = "form-control"; 			
			document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control"; 			
			document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
			document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
			document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
			document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
			document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
			document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
				
			var inputs = document.getElementsByClassName("validate_sppb");
			if(inputs){
				for(var i=0; i<inputs.length; i++){
					inputs[i].addEventListener("change",validateInput);
					inputs[i].addEventListener("focus",validateInput);
				}
        	}
		}
	}

	function simpan_spp(){
				Swal.fire({
					title: 'Apakah anda ingin menyimpan dan mencetak SPP?',
					showDenyButton: true,
					showCancelButton: false,
					confirmButtonText: `Simpan dan Cetak`,
					denyButtonText: `Simpan Saja`,
					confirmButtonColor: '#008000',
					denyButtonColor: '#1E90FF',
				}).then((result) => {
					if (result.isConfirmed) {
						$('#form_spp').attr("target","");
						$('#status_btn').val(1);
						document.getElementById("form_spp").submit();
					} else if (result.isDenied) {
						$('#status_btn').val(0);
						document.getElementById("form_spp").submit();
					}
				})
		}


		$('#kwitansi_spp').change(function(event){
			var kwitansi = $(this).val();
			$('#kwitansi_sppb').val(kwitansi);
			$('#kwitansi_sppn').val(kwitansi);
		});
	$('#referensi_spp').change(function(event){
			var referensi = $(this).val();
			$('#referensi_sppb').val(referensi);
			$('#referensi_sppn').val(referensi);
		});
	$('#faktur_pajak_spp').change(function(event){
			var faktur_pajak = $(this).val();
			$('#faktur_pajak_sppb').val(faktur_pajak);
			$('#faktur_pajak_sppn').val(faktur_pajak);
		});
	$('#sp_opl_spp').change(function(event){
			var sp_opl = $(this).val();
			$('#sp_opl_sppb').val(sp_opl);
			$('#sp_opl_sppn').val(sp_opl);
		});	
	
		$('#metode_pembayaran_sppb').change(function(event) {
			if ($(this).val() == 'bank') {
				$('#pilih_lampirkan_sppb').show();
				pilih_data_sppb.call();
			} 
			else if($(this).val() == 'karyawan'){
				$('#pilih_lampirkan_sppb').show();
				pilih_data_sppb.call();
			}
			else if ($(this).val() == 'kas'){
				$('#pilih_lampirkan_sppb').hide();
				$('#bank_sppb_karyawan').hide();
				$('#bank_sppb').hide();
				document.getElementById("nama_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("rekening_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
				

			}
			else if($(this).val() == 'skbdn'){
				$('#pilih_lampirkan_sppb').show();
				pilih_data_sppb.call();
			}
			else{
				$('#pilih_lampirkan_sppb').hide();
				$('#bank_sppb_karyawan').hide();
				$('#bank_sppb').hide();

				document.getElementById("nama_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("rekening_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
				
			}
		});

	// $('#metode_pembayaran_sppn').change(function(event) {
	// 	if ($(this).val() == 'bank') {
	// 		$('#bank_sppn').show();
	// 		$('#bank_sppn_karyawan').hide();
	// 		document.getElementById("nama_bank_sppn_vendor").className = "form-control validate_sppn validate_spp_all"; 			
	// 		document.getElementById("rekening_bank_sppn_vendor").className = "form-control validate_sppn validate_spp_all"; 			
	// 		document.getElementById("atas_nama_bank_sppn_vendor").className = "form-control validate_sppn validate_spp_all"; 			
	// 		document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control";
	// 		document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control";
	// 		document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control";
			
	// 		$('#atas_nama_vendor_sppn').show();
	// 		var inputs = document.getElementsByClassName("validate_sppn");
	// 		if(inputs){
	// 			for(var i=0; i<inputs.length; i++){
	// 				inputs[i].addEventListener("change",validateInput);
	// 				inputs[i].addEventListener("focus",validateInput);
	// 			}
    //     	}
	// 	} else if ($(this).val() == 'kas'){
	// 		$('#bank_sppn').hide();
	// 		$('#bank_sppn_karyawan').hide();
	// 		document.getElementById("nama_bank_sppn_vendor").className = "form-control"; 			
	// 		document.getElementById("rekening_bank_sppn_vendor").className = "form-control "; 			
	// 		document.getElementById("atas_nama_bank_sppn_vendor").className = "form-control "; 			
	// 		document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control";
	// 		document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control";
	// 		document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control";

	// 	}
	// 	else {
	// 		$('#pilih_lampirkan_sppn').show();

	// 	}
	// });

	
	
function tambah_karyawan_kas_sppb(index){
		index++;
		$('#kas_sppb').append(`<div class="form-group row" id="atas_nama_karyawan_kas_${index}">
													<label class="col-sm-2 col-form-label"></label>
													<div class="col-sm-8">
														<select class="form-control" id="atas_nama_bank_sppb_kas_${index}" name="atas_nama_bank_sppb_kas[${index}]">
															<option value="" disabled selected>--Pilih Karyawan--</option>
															@foreach($karyawan as $k)
															<option value="{{$k->karyawan_nama}}">{{$k->karyawan_nama}}</option>
															@endforeach	
														</select>							
													</div>
													<div class="col-sm-2" id="btn_karyawan_kas_sppb_${index}">
														<button type="button" class="btn btn-success btn-sm" onclick="tambah_karyawan_kas_sppb(${index})">+</button>									
														<button type="button" class="btn btn-danger btn-sm" onclick="hapus_karyawan_kas_sppb(${index})">x</button>
													
													</div>
												</div>`);
		var a = index-1;
		$('#btn_karyawan_kas_sppb_'+a).hide();

	}
	function hapus_karyawan_kas_sppb(index){
		var a = index-1;
		$('#atas_nama_karyawan_kas_'+index).remove();
		$('#btn_karyawan_kas_sppb_'+a).show();

	}

	// function tambah_karyawan_kas_sppn(index){
	// 	index++;
	// 	$('#kas_sppn').append(`<div class="form-group row" id="atas_nama_karyawan_kas_${index}">
	// 												<label class="col-sm-2 col-form-label"></label>
	// 												<div class="col-sm-8">
	// 													<select class="form-control" id="atas_nama_bank_sppn_kas" name="atas_nama_bank_sppn_kas[${index}]">
	// 														<option value="" disabled selected>--Pilih Karyawan--</option>
	// 														@foreach($karyawan as $k)
	// 														<option value="{{$k->karyawan_nama}}">{{$k->karyawan_nama}}</option>
	// 														@endforeach	
	// 													</select>							
	// 												</div>
	// 												<div class="col-sm-2" id="btn_karyawan_kas_sppn_${index}">
	// 													<button type="button" class="btn btn-success btn-sm" onclick="tambah_karyawan_kas_sppn(${index})">+</button>									
	// 													<button type="button" class="btn btn-danger btn-sm" onclick="hapus_karyawan_kas_sppn(${index})">x</button>
													
	// 												</div>
	// 											</div>`);
	// 	var a = index-1;
	// 	$('#btn_karyawan_kas_sppn_'+a).hide();

	// }

	// function hapus_karyawan_kas_sppn(index){
	// 	var a = index-1;
	// 	$('#atas_nama_karyawan_kas_'+index).remove();
	// 	$('#btn_karyawan_kas_sppn_'+a).show();

	// }

	function tambah_karyawan_bank_sppb(index){
		index++;
		$('#bank_sppb_karyawan_master').append(`<div id="bank_karyawan_sppb_${index}"> 
												<div class="form-group row" id="atas_nama_karyawan_bank_sppb_${index}">
													<label class="col-sm-2 col-form-label">Atas Nama Rekening ${index}*</label>
													<div class="col-sm-8">
														<input type="text" id="atas_nama_bank_sppb_karyawan_${index}"  onclick="bank_karyawan_sppb(${index})" name="karyawan_sppb[${index}][nama]" class="form-control"  placeholder="Atas Nama Bank SPPb" autocomplete="off">						
							
													</div>
													<div class="col-sm-2" id="btn_karyawan_bank_sppb_${index}">
														<button type="button" class="btn btn-success btn-sm" onclick="tambah_karyawan_bank_sppb(${index})">+</button>									
														<button type="button" class="btn btn-danger btn-sm" onclick="hapus_karyawan_bank_sppb(${index})">x</button>
													
													</div>
												</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nama Bank ${index}*</label>
												<div class="col-sm-8">
												<input type="text" id="nama_bank_sppb_karyawan_${index}" name="karyawan_sppb[${index}][bank]" onclick="bank_karyawan_sppb(${index})" class="form-control" placeholder="Nama Bank SPPb ${index}" autocomplete="off">
												</div>
												
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nomor Rekening ${index}*</label>
												<div class="col-sm-8">
													<input type="text" id="rekening_bank_sppb_karyawan_${index}" name="karyawan_sppb[${index}][no_rek]" onclick="bank_karyawan_sppb(${index})" class="form-control"  placeholder="Nomor Rekening Bank SPPb ${index}" autocomplete="off">
												</div>
											</div>
											
												</div>
												</div>`);
		var a = index-1;
		$('#btn_karyawan_bank_sppb_'+a).hide();

	}
	function hapus_karyawan_bank_sppb(index){
		var a = index-1;
		$('#bank_karyawan_sppb_'+index).remove();
		$('#btn_karyawan_bank_sppb_'+a).show();

	}
	function tambah_karyawan_bank_sppb_input(index){
		index++;
		jumlah_bank_karyawan = jumlah_bank_karyawan + 1;

		$('#bank_sppb_karyawan_input').append(`<div id="bank_karyawan_sppb_input_${index}"> 
											<div class="form-group row" id="atas_nama_karyawan_bank_sppb_${index}">
													<label class="col-sm-2 col-form-label">Atas Nama Rekening ${index}*</label>
													<div class="col-sm-8">
														<input type="text" id="atas_nama_bank_sppb_karyawan_input_${index}"   name="karyawan_sppb_input[${index}][nama]" class="form-control"  placeholder="Atas Nama Bank SPPb" autocomplete="off">						
																					
													</div>
													<div class="col-sm-2" id="btn_karyawan_bank_sppb_input_${index}">
														<button type="button" class="btn btn-success btn-sm" id="btn_tambah_karyawan_bank_sppb_input_${index}"onclick="tambah_karyawan_bank_sppb_input(${index})">+</button>									
														<button type="button" class="btn btn-danger btn-sm" id="btn_hapus_karyawan_bank_sppb_input_${index}"onclick="hapus_karyawan_bank_sppb_input(${index})">x</button>
													
													</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nama Bank ${index}*</label>
												<div class="col-sm-8">
												<input type="text" id="nama_bank_sppb_karyawan_input_${index}" name="karyawan_sppb_input[${index}][bank]" class="form-control" placeholder="Nama Bank SPPb ${index}"  autocomplete="off">
												</div>
												
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nomor Rekening ${index}*</label>
												<div class="col-sm-8">
													<input type="text" id="rekening_bank_sppb_karyawan_input_${index}" name="karyawan_sppb_input[${index}][no_rek]"   class="form-control"  placeholder="Nomor Rekening Bank SPPb ${index}" autocomplete="off">
												</div>
											</div>
											
												</div>
												</div>`);
		var a = index-1;
		$('#btn_karyawan_bank_sppb_input_'+a).hide();

	}
	function hapus_karyawan_bank_sppb_input(index){
		jumlah_bank_karyawan = jumlah_bank_karyawan - 1;
		var a = index-1;
		$('#bank_karyawan_sppb_input_'+index).remove();
		$('#btn_karyawan_bank_sppb_input_'+a).show();
	}
	// function tambah_karyawan_bank_sppn(index){
	// 	index++;
	// 	$('#bank_sppn_karyawan').append(`<div id="bank_karyawan_sppn_${index}">
	// 										<div class="form-group row" id="atas_nama_karyawan_bank_sppn_${index}">
	// 												<label class="col-sm-2 col-form-label">Atas Nama Rekening ${index}*</label>
	// 												<div class="col-sm-8">
	// 													<input type="text" id="atas_nama_bank_sppn_karyawan_${index}"  onclick="bank_karyawan_sppn(${index})" name="karyawan_sppn[${index}][nama]" class="form-control"  placeholder="Atas Nama Bank ${index}" autocomplete="off">						
						
	// 												</div>
	// 												<div class="col-sm-2" id="btn_karyawan_bank_sppn_${index}">
	// 													<button type="button" class="btn btn-success btn-sm" onclick="tambah_karyawan_bank_sppn(${index})">+</button>
	// 													<button type="button" class="btn btn-danger btn-sm" onclick="hapus_karyawan_bank_sppn(${index})">x</button>
									
	// 												</div>
	// 										</div>
	// 										<div class="form-group row">
	// 											<label class="col-sm-2 col-form-label">Nama Bank ${index}*</label>
	// 											<div class="col-sm-8">
	// 											<input type="text" id="nama_bank_sppn_karyawan_${index}" name="karyawan_sppn[${index}][bank]" onclick="bank_karyawan_sppn(${index})" class="form-control" placeholder="Nama Rekening Bank ${index}" autocomplete="off">												
	// 											</div>
												
	// 										</div>
	// 										<div class="form-group row">
	// 											<label class="col-sm-2 col-form-label">Nomor Rekening ${index}*</label>
	// 											<div class="col-sm-8">
	// 												<input type="text" id="rekening_bank_sppn_karyawan_${index}" name="karyawan_sppn[${index}][no_rek]" onclick="bank_karyawan_sppn(${index})" class="form-control" placeholder="Nomor Rekening Bank ${index}" autocomplete="off">
	// 											</div>
	// 										</div>
											
	// 											</div>`);
	// 	var a = index-1;
	// 	$('#btn_karyawan_bank_sppn_'+a).hide();

	// }
	// function hapus_karyawan_bank_sppn(index){
	// 	var a = index-1;
	// 	$('#bank_karyawan_sppn_'+index).remove();
	// 	$('#btn_karyawan_bank_sppn_'+a).show();

	// }

	function bank_karyawan_sppb(isi){
		$('#modal_karyawan_sppb').modal('show');
		index_bank_sppb_karyawan = isi;
		nama_bank_sppb_karyawan_id = 'nama_bank_sppb_karyawan_'+isi;
		rekening_bank_sppb_karyawan_id = 'rekening_bank_sppb_karyawan_'+isi;
		atas_nama_bank_sppb_karyawan_id = 'atas_nama_bank_sppb_karyawan_'+isi;
	}
	function pilih_karyawan_sppb(id,nama,namabank,norek){
		//window.alert(namabank);
		if(namabank == ""){
			$('#'+nama_bank_sppb_karyawan_id).val("");
			document.getElementById(nama_bank_sppb_karyawan_id).onclick = function(){ };
		}
		else{
			$('#'+nama_bank_sppb_karyawan_id).val(namabank);
			document.getElementById(nama_bank_sppb_karyawan_id).onclick = function() { bank_karyawan_sppb(index_bank_sppb_karyawan); }
		}
		if(norek == ""){
			$('#'+rekening_bank_sppb_karyawan_id).val("");
			document.getElementById(rekening_bank_sppb_karyawan_id).onclick = function(){ };
		}
		else{
			$('#'+rekening_bank_sppb_karyawan_id).val(norek);
			document.getElementById(rekening_bank_sppb_karyawan_id).onclick = function() { bank_karyawan_sppb(index_bank_sppb_karyawan); }

		}
		$('#'+atas_nama_bank_sppb_karyawan_id).val(nama);
		$('#modal_karyawan_sppb').modal('hide');
	}

	// function bank_karyawan_sppn(isi){
	// 	$('#modal_karyawan_sppn').modal('show');
	// 	index_bank_sppn_karyawan = isi;
	// 	nama_bank_sppn_karyawan_id = 'nama_bank_sppn_karyawan_'+isi;
	// 	rekening_bank_sppn_karyawan_id = 'rekening_bank_sppn_karyawan_'+isi;
	// 	atas_nama_bank_sppn_karyawan_id = 'atas_nama_bank_sppn_karyawan_'+isi;
	// }
	function pilih_karyawan_sppn(id,nama,namabank,norek){
		//window.alert(namabank);
		if(namabank == ""){
			$('#'+nama_bank_sppn_karyawan_id).val("");
			document.getElementById(nama_bank_sppn_karyawan_id).onclick = function(){ };
		}
		else{
			$('#'+nama_bank_sppn_karyawan_id).val(namabank);
			document.getElementById(nama_bank_sppn_karyawan_id).onclick = function() { bank_karyawan_sppn(index_bank_sppn_karyawan); }
		}
		if(norek == ""){
			$('#'+rekening_bank_sppn_karyawan_id).val("");
			document.getElementById(rekening_bank_sppn_karyawan_id).onclick = function(){ };
		}
		else{
			$('#'+rekening_bank_sppn_karyawan_id).val(norek);
			document.getElementById(rekening_bank_sppn_karyawan_id).onclick = function() { bank_karyawan_sppn(index_bank_sppn_karyawan); }

		}
		$('#'+atas_nama_bank_sppn_karyawan_id).val(nama);
		$('#modal_karyawan_sppn').modal('hide');
	}


	function data_bank_sppb(isi){
		$('#modal_bank_sppb').modal('show');
		bank_sppb_id = 'nama_bank_sppb_vendor';
		rekening_bank_sppb_id = 'rekening_bank_sppb_vendor';
		atas_nama_bank_sppb_id = 'atas_nama_bank_sppb_vendor';
		id_bank_sppb_id = 'id_bank_sppb_1';
	}

	function pilih_bank_sppb(id,nama,norekening, atasnama){
		$('#'+id_bank_sppb_id).val(id);
		$('#'+bank_sppb_id).val(nama);
		document.getElementById(bank_sppb_id).focus(); 		
		$('#'+rekening_bank_sppb_id).val(norekening);
		document.getElementById(rekening_bank_sppb_id).focus(); 		
		$('#'+atas_nama_bank_sppb_id).val(atasnama);
		document.getElementById(atas_nama_bank_sppb_id).focus(); 		
		$('#modal_bank_sppb').modal('hide');
	}

	// function data_bank_sppn(isi){
	// 	$('#modal_bank_sppn').modal('show');
	// 	bank_sppn_id = 'nama_bank_sppn_vendor';
	// 	rekening_bank_sppn_id = 'rekening_bank_sppn_vendor';
	// 	atas_nama_bank_sppn_id = 'atas_nama_bank_sppn_vendor';
	// 	id_bank_sppn_id = 'id_bank_sppn_1';
	// }
	// function pilih_bank_sppn(id,nama,norekening, atasnama){
	// 	$('#'+id_bank_sppn_id).val(id);
	// 	$('#'+bank_sppn_id).val(nama);
	// 	document.getElementById(bank_sppn_id).focus(); 			
	// 	$('#'+rekening_bank_sppn_id).val(norekening);
	// 	document.getElementById(rekening_bank_sppn_id).focus(); 		
	// 	$('#'+atas_nama_bank_sppn_id).val(atasnama);
	// 	document.getElementById(atas_nama_bank_sppn_id).focus(); 		
	// 	$('#modal_bank_sppn').modal('hide');
	// }
	function kode_rekening_sppb(isi){
		$('#modal_rekening_sppb').modal('show');
		rekening_sppb_id = 'sap_vendor_sppb_'+isi;
		rekening_sppb_id_id = 'sap_vendor_sppb_id_'+isi;
	}

	function kode_rekening_sppn(isi){
		$('#modal_rekening_sppn').modal('show');
		rekening_sppn_id = 'sap_vendor_sppn_'+isi;
		rekening_sppn_id_id = 'sap_vendor_sppn_id_'+isi;

	}

	function pilih_rekening_sppn(index,id){
						var kbb = document.getElementById(id+index).parentElement;
						// console.log(kbb);
						if($('#'+id+index).val() == ""){
							kbb.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;width:100%;";
						}
						else{
							kbb.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;width:100%;";
						}
						var a = document.getElementById(id+'id_'+index);
						if(a !== null){
							document.getElementById(id+'id_'+index).value = $('#'+id+index).val();

						}
	}
	function pilih_rekening_sppb(index,id){
						var kbb = document.getElementById(id+index).parentElement;
						// console.log(kbb);
						if($('#'+id+index).val() == ""){
							kbb.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;width:100%;";
						}
						else{
							kbb.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;width:100%;";
						}
						var a = document.getElementById(id+'id_'+index);
						if(a !== null){
							document.getElementById(id+'id_'+index).value = $('#'+id+index).val();

						}
						//console.log($('#'+id+'id_'+index).val());

	}

	
	
	function jc_sppn(index){
		console.log(index);
		var pilihan = $('#jenis_center_sppn_'+index).val();
		if (pilihan == 'cost_center') {
			document.getElementById("select_cost_center_sppn_"+index).className = "form-control validate_sppn validate_spp_all"; 			
			document.getElementById("select_profit_center_sppn_"+index).className = "form-control"; 			
			var inputs = document.getElementsByClassName("validate_sppn");
			if(inputs){
				for(var i=0; i<inputs.length; i++){
					inputs[i].addEventListener("change",validateInput);
					inputs[i].addEventListener("keyup",validateInput);
				}
        	}
			$('#cost_center_sppn_'+index).show();
			$('#profit_center_sppn_'+index).hide();
			
		} else {
			document.getElementById("select_profit_center_sppn_"+index).className = "form-control validate_sppn validate_spp_all"; 			
			document.getElementById("select_cost_center_sppn_"+index).className = "form-control"; 		
			var inputs = document.getElementsByClassName("validate_sppn");
			if(inputs){
				for(var i=0; i<inputs.length; i++){
					inputs[i].addEventListener("change",validateInput);
					inputs[i].addEventListener("keyup",validateInput);
				}
        	}
			$('#cost_center_sppn_'+index).hide();
			$('#profit_center_sppn_'+index).show();
		}
	}
	function jc_sppb(index){
		console.log(index);
		var pilihan = $('#jenis_center_sppb_'+index).val();
		if (pilihan == 'cost_center') {
			document.getElementById("select_cost_center_sppb_"+index).className = "form-control validate_sppb validate_spp_all"; 			
			document.getElementById("select_profit_center_sppb_"+index).className = "form-control"; 			
			var inputs = document.getElementsByClassName("validate_sppb");
			if(inputs){
				for(var i=0; i<inputs.length; i++){
					inputs[i].addEventListener("change",validateInput);
					inputs[i].addEventListener("keyup",validateInput);
				}
        	}
			$('#cost_center_sppb_'+index).show();
			$('#profit_center_sppb_'+index).hide();
			
		} else {
			document.getElementById("select_profit_center_sppb_"+index).className = "form-control validate_sppb validate_spp_all"; 			
			document.getElementById("select_cost_center_sppb_"+index).className = "form-control"; 		
			var inputs = document.getElementsByClassName("validate_sppb");
			if(inputs){
				for(var i=0; i<inputs.length; i++){
					inputs[i].addEventListener("change",validateInput);
					inputs[i].addEventListener("keyup",validateInput);
				}
        	}
			$('#cost_center_sppb_'+index).hide();
			$('#profit_center_sppb_'+index).show();
		}
	}
	function js_sppb(index){
		// console.log(index);
		var pilihan = $('#jenis_sap_sppb_'+index).val();
		if (pilihan == 'vendor') {
			document.getElementById("sap_vendor_sppb_"+index).className = "form-control validate_sppb validate_spp_all"; 			
			document.getElementById("sap_customer_sppb_"+index).className = "form-control";
			document.getElementById("sap_gl_sppb_"+index).className = "form-control"; 			
			var inputs = document.getElementsByClassName("validate_sppb");
			if(inputs){
				for(var i=0; i<inputs.length; i++){
					inputs[i].addEventListener("change",validateInput);
					inputs[i].addEventListener("keyup",validateInput);
				}
        	}
			$('#nomor_vendor_sppb_'+index).show();
			$('#nomor_customer_sppb_'+index).hide();
			$('#nomor_gl_sppb_'+index).hide();
			
		}else if (pilihan == 'customer') {
			document.getElementById("sap_gl_sppb_"+index).className = "form-control"; 			
			document.getElementById("sap_customer_sppb_"+index).className = "form-control validate_sppb validate_spp_all";
			document.getElementById("sap_vendor_sppb_"+index).className = "form-control"; 
			var inputs = document.getElementsByClassName("validate_sppb");
			if(inputs){
				for(var i=0; i<inputs.length; i++){
					inputs[i].addEventListener("change",validateInput);
					inputs[i].addEventListener("keyup",validateInput);
				}
        	}
			$('#nomor_customer_sppb_'+index).show();
			$('#nomor_gl_sppb_'+index).hide();
			$('#nomor_vendor_sppb_'+index).hide();
			
		}  else {
			document.getElementById("sap_gl_sppb_"+index).className = "form-control validate_sppb validate_spp_all"; 			
			document.getElementById("sap_customer_sppb_"+index).className = "form-control";
			document.getElementById("sap_vendor_sppb_"+index).className = "form-control"; 		
			var inputs = document.getElementsByClassName("validate_sppb");
			if(inputs){
				for(var i=0; i<inputs.length; i++){
					inputs[i].addEventListener("change",validateInput);
					inputs[i].addEventListener("keyup",validateInput);
				}
        	}
			$('#nomor_vendor_sppb_'+index).hide();
			$('#nomor_customer_sppb_'+index).hide();
			$('#nomor_gl_sppb_'+index).show();
		}
	}
	function js_sppn(index){
		// console.log(index);
		var pilihan = $('#jenis_sap_sppn_'+index).val();
		if (pilihan == 'vendor') {
			document.getElementById("sap_vendor_sppn_"+index).className = "form-control validate_sppn validate_spp_all"; 			
			document.getElementById("sap_gl_sppn_"+index).className = "form-control"; 			
			document.getElementById("sap_customer_sppn_"+index).className = "form-control"; 			
			var inputs = document.getElementsByClassName("validate_sppn");
			if(inputs){
				for(var i=0; i<inputs.length; i++){
					inputs[i].addEventListener("change",validateInput);
					inputs[i].addEventListener("keyup",validateInput);
				}
        	}
			$('#nomor_vendor_sppn_'+index).show();
			$('#nomor_gl_sppn_'+index).hide();
			$('#nomor_customer_sppn_'+index).hide();
			
		}else if (pilihan == 'customer') {
			document.getElementById("sap_vendor_sppn_"+index).className = "form-control"; 			
			document.getElementById("sap_customer_sppn_"+index).className = "form-control validate_sppn validate_spp_all"; 			
			document.getElementById("sap_gl_sppn_"+index).className = "form-control"; 			
			var inputs = document.getElementsByClassName("validate_sppn");
			if(inputs){
				for(var i=0; i<inputs.length; i++){
					inputs[i].addEventListener("change",validateInput);
					inputs[i].addEventListener("keyup",validateInput);
				}
        	}
			$('#nomor_vendor_sppn_'+index).hide();
			$('#nomor_customer_sppn_'+index).show();
			$('#nomor_gl_sppn_'+index).hide();
			
		} else {
			document.getElementById("sap_gl_sppn_"+index).className = "form-control validate_sppn validate_spp_all"; 			
			document.getElementById("sap_customer_sppn_"+index).className = "form-control"; 			
			document.getElementById("sap_vendor_sppn_"+index).className = "form-control"; 		
			var inputs = document.getElementsByClassName("validate_sppn");
			if(inputs){
				for(var i=0; i<inputs.length; i++){
					inputs[i].addEventListener("change",validateInput);
					inputs[i].addEventListener("keyup",validateInput);
				}
        	}
			$('#nomor_vendor_sppn_'+index).hide();
			$('#nomor_customer_sppn_'+index).hide();
			$('#nomor_gl_sppn_'+index).show();
		}
	}
	function tambah_faktur_pajak_spp(index){
		index++;
		$('#fp_spp').append(`<div id="faktur_pajak_spp_${index}">
									<div class="form-group row">
										<label class="col-sm-2 col-form-label"></label>
										<div class="col-sm-8">
											<input type="text" id="faktur_pajak_spp" name="faktur_pajak[${index}][fp]" class="form-control validate_spp_all" placeholder="Faktur Pajak ${index}" autocomplete="off">
										</div>
										<div class="col-sm-2" id="btn_tambah_faktur_pajak_${index}">
											<button type="button" class="btn btn-success btn-sm" onclick="tambah_faktur_pajak_spp(${index})">+</button>
											<button type="button" class="btn btn-danger btn-sm" onclick="hapus_faktur_pajak_spp(${index})">x</button>
									
										</div>
									</div>
								</div>`);
		var a = index-1;
		$('#btn_tambah_faktur_pajak_'+a).hide();
		$('#btn_hapus_faktur_pajak_'+a).hide();

	}

	function hapus_faktur_pajak_spp(index){
		var a = index-1;
		$('#faktur_pajak_spp_'+index).remove();
		$('#btn_tambah_faktur_pajak_'+a).show();
		$('#btn_hapus_faktur_pajak_'+a).show();

	}
	
	function tambah_faktur_pajak_sppb(index){
		index++;
		$('#fp_sppb').append(`<div id="faktur_pajak_sppb_${index}">
									<div class="form-group row">
										<label class="col-sm-2 col-form-label"></label>
										<div class="col-sm-8">
											<input type="text" id="faktur_pajak_spp" name="faktur_pajak_sppb[${index}][fp]" class="form-control validate_spp_all" placeholder="Faktur Pajak SPPb ${index}" autocomplete="off">
										</div>
										<div class="col-sm-2" id="btn_faktur_pajak_sppb_${index}">
											<button type="button" class="btn btn-success btn-sm" onclick="tambah_faktur_pajak_sppb(${index})">+</button>
											<button type="button" class="btn btn-danger btn-sm" onclick="hapus_faktur_pajak_sppb(${index})">x</button>
									
										</div>
									</div>
								</div>`);
		var a = index-1;
		$('#btn_faktur_pajak_sppb_'+a).hide();

	}

	function hapus_faktur_pajak_sppb(index){
		var a = index-1;
		$('#faktur_pajak_sppb_'+index).remove();
		$('#btn_faktur_pajak_sppb_'+a).show();
	}
	function tambah_faktur_pajak_sppn(index){
		index++;
		$('#fp_sppn').append(`<div id="faktur_pajak_sppn_${index}">
									<div class="form-group row">
										<label class="col-sm-2 col-form-label"></label>
										<div class="col-sm-8">
											<input type="text" id="faktur_pajak_spp" name="faktur_pajak_sppn[${index}][fp]" class="form-control validate_spp_all" placeholder="Faktur Pajak SPPn ${index}" autocomplete="off">
										</div>
										<div class="col-sm-2" id="btn_faktur_pajak_sppn_${index}">
											<button type="button" class="btn btn-success btn-sm" onclick="tambah_faktur_pajak_sppn(${index})">+</button>
											<button type="button" class="btn btn-danger btn-sm" onclick="hapus_faktur_pajak_sppn(${index})">x</button>
									
										</div>
									</div>
								</div>`);
		var a = index-1;
		$('#btn_faktur_pajak_sppn_'+a).hide();

	}

	function hapus_faktur_pajak_sppn(index){
		var a = index-1;
		$('#faktur_pajak_sppn_'+index).remove();
		$('#btn_faktur_pajak_sppn_'+a).show();
	}
	function tambah_isi_sppb(index_sppb){
		index_sppb++;
		// index = index_sppb-1;
		sub_index_sppb[index_sppb] = 1;
		$('#tab-isi-sppb').append(`<div id="isi_sppb_${index_sppb}" class="col-sm-12">
					<div  style="padding-bottom: 10px; margin-bottom: 20px; border-bottom: 1px solid #eaeaea;">
						<font size="4" style="margin-right: 20px">Isi ${index_sppb+1}. </font>
						<button type="button" class="btn btn-info btn-sm" id="tambah_isi_sppb_${index_sppb}" onclick="tambah_isi_sppb(${index_sppb})">+</button>
						<button type="button" class="btn btn-danger btn-sm" onclick="hapus_isi_sppb(${index_sppb},'ckeditor_${index_sppb}_',1)">x</button>
					</div>
					<div class="col-sm-5">
					<div class="form-group row">
													<label class="col-sm-3 col-form-label">Kode KBB *</label>
													<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppb_${index_sppb}" >
														<select class="selectpicker slct_sppb"  data-live-search="true" data-dropup-auto="false" id="kode_kbb_sppb_${index_sppb}" data-width="100%" name="isi_sppb[${index_sppb}][kode_kbb]" data-size="7" onchange="pilih_rekening_sppb(${index_sppb},'kode_kbb_sppb_')">
															<option value="" disabled selected>-- Pilih Kode Kbb --</option>
															@foreach($rekening as $r)
															<option value="{{$r->master_rekening_kode_kbb}}">{{$r->master_rekening_kode_kbb}}</option>
															@endforeach
														</select>
														</div>
														
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Kode SAP *</label>
													<label class="col-sm-9">
														<select class="form-control validate_sppb validate_spp_all" id="jenis_sap_sppb_${index_sppb}" onchange="js_sppb(${index_sppb})" name="isi_sppb[${index_sppb}][jenis_sap]">
															<option value="" disabled selected>-- Pilih Jenis Kode SAP --</option>
															<option value="vendor">Nomor Vendor</option>
															<option value="gl">Nomor GL</option>
															<option value="customer">Nomor Customer</option>
														</select>
													</label>
													<label class="col-sm-3 col-form-label"></label>

													<div id="nomor_vendor_sppb_${index_sppb}" style="display:none">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppb_${index_sppb}" >
																<select class="selectpicker slct_sppb"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_vendor_sppb_${index_sppb}" data-size="7" name="isi_sppb_rekening" onchange="pilih_rekening_sppb(${index_sppb},'sap_vendor_sppb_')">
																	<option value="" disabled selected>-- Pilih Kode Vendor --</option>
																	@foreach($rekening as $r)
																	<option value="{{$r->master_rekening_id}}">{{$r->master_rekening_kode_sap}} ({{$r->master_rekening_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_vendor_sppb_id_${index_sppb}" name="isi_sppb[${index_sppb}][vendor]" class="form-control" onclick="kode_rekening_sppb(${index_sppb})" placeholder="Kode SAP (Nomor Vendor)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_customer_sppb_${index_sppb}" style="display:none">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppb_${index_sppb}" >
																<select class="selectpicker"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_customer_sppb_${index_sppb}" data-size="7" name="isi_sppb_customer" onchange="pilih_rekening_sppb(${index_sppb},'sap_customer_sppb_')">
																	<option value="" disabled selected>-- Pilih Kode Customer --</option>
																	@foreach($customer as $r)
																	<option value="{{$r->master_customer_id}}">{{$r->master_customer_kode_sap}} ({{$r->master_customer_nama}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_customer_sppb_id_${index_sppb}" name="isi_sppb[${index_sppb}][customer]" class="form-control" onclick="kode_customer_sppb(${index_sppb})" placeholder="Kode SAP (Nomor Customer)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_gl_sppb_${index_sppb}" style="display:none">
														<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppb_${index_sppb}" >
																<select class="selectpicker slct_sppb"  data-live-search="true" data-dropup-auto="false" id="sap_gl_sppb_${index_sppb}" data-size="7" data-width="100%" name="isi_sppb_rekening" onchange="pilih_rekening_sppb(${index_sppb},'sap_gl_sppb_')">
																	<option value="" disabled selected>-- Pilih Kode GL --</option>
																	@foreach($gl as $r)
																	<option value="{{$r->master_gl_id}}">{{$r->master_gl_kode}} ({{$r->master_gl_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_gl_sppb_id_${index_sppb}" name="isi_sppb[${index_sppb}][gl]" class="form-control" onclick="kode_gl_sppb(${index_sppb})" placeholder="Kode SAP (Nomor GL)" autocomplete="off" required>
														</div>
													</div>
													
												</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Cost/Profit*</label>
							<label class="col-sm-9">
								<select class="form-control validate_sppb validate_spp_all" id="jenis_center_sppb_${index_sppb}" onchange="jc_sppb(${index_sppb})" name="isi_sppb[${index_sppb}][jenis_center]">
									<option value="" disabled selected>-- Pilih --</option>
									<option value="cost_center">Cost Center</option>
									<option value="profit_center">Profit Center</option>
								</select>
							</label>
							<label class="col-sm-3 col-form-label"></label>
							<div class="col-sm-9" id="cost_center_sppb_${index_sppb}" style="display: none">
							<select class="form-control" id="select_cost_center_sppb_${index_sppb}" name="isi_sppb[${index_sppb}][cost_center]">
														<option value="" disabled selected>-- Pilih Cost Center --</option>
														@foreach($costcenter as $cost)
														<option value="{{$cost->master_cost_center_id}}">{{$cost->master_cost_center_kode}} {{$cost->master_cost_center_keterangan}}</option>
														@endforeach
													</select>
							</div>
							<div class="col-sm-9" id="profit_center_sppb_${index_sppb}" style="display: none">
							<select class="form-control" id="select_profit_center_sppb_${index_sppb}" name="isi_sppb[${index_sppb}][profit_center]">
															<option value="" disabled selected>-- Pilih Profit Center --</option>
															@foreach($profitcenter as $profit)
																<option value="{{$profit->master_profit_center_id}}">{{$profit->master_profit_center_kode}} ({{$profit->master_profit_unit}})</option>
															@endforeach
														</select>
							</div>
						</div>
						<div class="form-group row">
													<label class="col-sm-3 col-form-label">Cash Flow*</label>
													<div class="col-sm-9">
														<select class="form-control validate_sppb validate_spp_all" id="cash_flow_sppb_${index_sppb}" name="isi_sppb[${index_sppb}][cash_flow]">
															<option value="" disabled selected>-- Pilih Cash Flow --</option>
															@foreach($cashflow as $cash)
																<option value="{{$cash->master_cash_flow_id}}">{{$cash->master_cash_flow_kode}} {{$cash->master_cash_flow_keterangan}}</option>
															@endforeach
														</select>
													</div>
												</div>
					</div>
					<div id="sub_isi_sppb_${index_sppb}_0">
						<div class="col-md-6">
							<div class="form-group row">
								<label class="col-sm-1 col-form-label">1. </label>
								<label class="col-sm-2 col-form-label">Uraian*</label>
								<div class="col-sm-9">
									<div style="border: 1px solid hsla(0, 0%, 0%, 0.15);">
										<div id="uraian_sppb_${index_sppb}_0" style="height:auto;min-height:100px" >
											<textarea class="form-control" id="ckeditor_${index_sppb}_0" name="uraian_sppb[${index_sppb}][0][ket]"></textarea>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-1 col-form-label"></label>
								<label class="col-sm-2 col-form-label">Nominal*</label>
								<div class="col-sm-9">
									<input type="text" id="jumlah_sppb_${index_sppb}_0" name="uraian_sppb[${index_sppb}][0][jumlah]" class="form-control nominal validate_sppb validate_spp_all" placeholder="Nominal SPPb" autocomplete="off" required>
							
								</div>
							</div>
						</div>

						<div class="col-sm-1">
							<div class="col-sm-12" style="margin-bottom: 10px">
								<button type="button" class="btn btn-success btn-sm" id="tambah_sub_isi_sppb_${index_sppb}_0" onclick="tambah_sub_isi_sppb(${index_sppb},0)">+</button>
							</div>
						</div>
					</div>
				</div>`);

		// InlineEditor.create(document.querySelector(`#uraian_sppb_${index_sppb}_1`),{
	    // 	placeholder: 'Uraian SPPB' 
	    // }).then( editor => {
		// 	window.editor = editor;
		// }).catch( error => {
		// 	console.error( 'There was a problem initializing the editor.', error );
		// });
		for(a=0;a<index_sppb;a++){
			$("#tambah_isi_sppb_"+a).hide();
		}
        CKEDITOR.inline( 'ckeditor_'+index_sppb+'_0');
		$('.selectpicker').selectpicker();
		
		$('.nominal').mask('000.000.000.000.000.000.000', {reverse: true});
		var inputs = document.getElementsByClassName("validate_sppb");
				if(inputs){
					for(var i=0; i<inputs.length; i++){
						inputs[i].addEventListener("change",validateInput);
						inputs[i].addEventListener("keyup",validateInput);
						inputs[i].addEventListener("focus",validateInput);
					}
				}
				for(var i in CKEDITOR.instances){
					if (i.substring(0,9) == "ckeditor_"){
						CKEDITOR.instances[i].on('change',function(){
						var urai = document.getElementById(this.name).parentElement;
						if(this.getData().replace(/<[^>]+>/g, '') == ""){
							urai.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px";
						}
						else{
							urai.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px";
						}
					});
				}
			}
	}

	function hapus_isi_sppb(isi,instance,count_instance){
		a = isi-1;
		$("#isi_sppb_"+isi).remove();
		$("#tambah_isi_sppb_"+a).show();
		for(var i=0; i<count_instance; i++){
			CKEDITOR.instances[instance+i].destroy();
		}
	}

	function tambah_sub_isi_sppb(isi,uraian){
		
		uraian++;
		$('#isi_sppb_'+isi).append(`<div id="sub_isi_sppb_${isi}_${uraian}">
								<div class="col-sm-5"></div>
								<div class="col-md-6">
									<div class="form-group row">
										<label class="col-sm-1 col-form-label">${uraian+1}. </label>
										<label class="col-sm-2 col-form-label">Uraian*</label>
										<div class="col-sm-9">
											<div style="border: 1px solid hsla(0, 0%, 0%, 0.15);">
												<div id="uraian_sppb_${isi}_${uraian}" style="height:auto;min-height:100px">
													<textarea class="form-control" id="ckeditor_${isi}_${uraian}" name="uraian_sppb[${isi}][${uraian}][ket]"></textarea>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-1 col-form-label"></label>
										<label class="col-sm-2 col-form-label">Nominal*</label>
										<div class="col-sm-9">
											<input type="text" id="jumlah_sppb_${isi}_${uraian}" name="uraian_sppb[${isi}][${uraian}][jumlah]" class="form-control nominal validate_sppb validate_spp_all" placeholder="Nominal SPPb" autocomplete="off" required>
	
											</div>
									</div>
								</div>
								<div class="col-sm-1">
									<div class="col-sm-12" id="hapus_sub_isi_sppb_${isi}_${uraian}" onclick="hapus_sub_isi_sppb(${isi},${uraian},'ckeditor_${isi}_${uraian}')" style="margin-bottom: 10px">
										<button type="button" class="btn btn-danger btn-sm">X</button>
									</div>
										<div class="col-sm-12" id="tambah_sub_isi_sppb_${isi}_${uraian}" onclick="tambah_sub_isi_sppb(${isi},${uraian})" style="margin-bottom: 10px">
											<button type="button" class="btn btn-success btn-sm">+</button>
										</div>
									</div>
								</div>
							</div>`);

		for(a=0;a<uraian;a++){
			$("#tambah_sub_isi_sppb_"+isi+'_'+a).hide();
		}
		
        CKEDITOR.inline('ckeditor_'+isi+'_'+uraian);
		$('.nominal').mask('000.000.000.000.000.000.000', {reverse: true});
		var inputs = document.getElementsByClassName("validate_sppb");
						if(inputs){
							for(var i=0; i<inputs.length; i++){
								inputs[i].addEventListener("change",validateInput);
								inputs[i].addEventListener("keyup",validateInput);
								inputs[i].addEventListener("focus",validateInput);
							}
						}
						for(var i in CKEDITOR.instances){
							if (i.substring(0,9) == "ckeditor_"){
								CKEDITOR.instances[i].on('change',function(){
									var urai = document.getElementById(this.name).parentElement;
									if(this.getData().replace(/<[^>]+>/g, '') == ""){
										urai.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px";
									}
									else{
										urai.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px";
									}
								});
							}
						}
		
	}

	function hapus_sub_isi_sppb(isi, sub_isi, instance){
		a=sub_isi-1;
		$('#sub_isi_sppb_'+isi+'_'+sub_isi).remove();
		$('#tambah_sub_isi_sppb_'+isi+'_'+a).show();
		CKEDITOR.instances[instance].destroy();
	}

	function tambah_isi_sppn(index_sppn){
		index_sppn++;
		sub_index_sppn[index_sppn] = 1;
		$('#tab-isi-sppn').append(`<div id="isi_sppn_${index_sppn}" class="col-sm-12">
					<div  style="padding-bottom: 10px; margin-bottom: 20px; border-bottom: 1px solid #eaeaea;">
						<font size="4" style="margin-right: 20px">Isi ${index_sppn+1}. </font>
						<button type="button" class="btn btn-info btn-sm" id="tambah_isi_sppn_${index_sppn}" onclick="tambah_isi_sppn(${index_sppn})">+</button>
						<button type="button" class="btn btn-danger btn-sm" onclick="hapus_isi_sppn(${index_sppn},'ckeditors_${index_sppn}_',1)">x</button>
					</div>
					<div class="col-sm-5">
					<div class="form-group row">
													<label class="col-sm-3 col-form-label">Kode KBB *</label>
													<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppn_${index_sppn}" >
														<select class="selectpicker"  data-live-search="true" data-dropup-auto="false" id="kode_kbb_sppn_${index_sppn}" data-width="100%" name="isi_sppn[${index_sppn}][kode_kbb]" data-size="7" onchange="pilih_rekening_sppn(${index_sppn},'kode_kbb_sppn_')">
															<option value="" disabled selected>-- Pilih Kode Kbb --</option>
															@foreach($rekening as $r)
															<option value="{{$r->master_rekening_kode_kbb}}">{{$r->master_rekening_kode_kbb}}</option>
															@endforeach
														</select>
														</div>
														
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Kode SAP *</label>
													<label class="col-sm-9">
														<select class="form-control validate_sppn validate_spp_all" id="jenis_sap_sppn_${index_sppn}" onchange="js_sppn(${index_sppn})" name="isi_sppn[${index_sppn}][jenis_sap]">
															<option value="" disabled selected>-- Pilih Jenis Kode SAP --</option>
															<option value="vendor">Nomor Vendor</option>
															<option value="gl">Nomor GL</option>
															<option value="customer">Nomor Customer</option>
														</select>
													</label>
													<label class="col-sm-3 col-form-label"></label>

													<div id="nomor_vendor_sppn_${index_sppn}" style="display:none">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppn_${index_sppn}" >
																<select class="selectpicker"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_vendor_sppn_${index_sppn}" data-size="7" name="isi_sppn_rekening" onchange="pilih_rekening_sppn(${index_sppn},'sap_vendor_sppn_')">
																	<option value="" disabled selected>-- Pilih Kode Vendor --</option>
																	@foreach($rekening as $r)
																	<option value="{{$r->master_rekening_id}}">{{$r->master_rekening_kode_sap}} ({{$r->master_rekening_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_vendor_sppn_id_${index_sppn}" name="isi_sppn[${index_sppn}][vendor]" class="form-control" onclick="kode_rekening_sppn(${index_sppn})" placeholder="Kode SAP (Nomor Vendor)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_customer_sppn_${index_sppn}" style="display:none">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppn_${index_sppn}" >
																<select class="selectpicker"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_customer_sppn_${index_sppn}" data-size="7" name="isi_sppn_rekening" onchange="pilih_rekening_sppn(${index_sppn},'sap_customer_sppn_')">
																	<option value="" disabled selected>-- Pilih Kode Customer --</option>
																	@foreach($customer as $r)
																	<option value="{{$r->master_customer_id}}">{{$r->master_customer_kode_sap}} ({{$r->master_customer_nama}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_customer_sppn_id_${index_sppn}" name="isi_sppn[${index_sppn}][customer]" class="form-control" onclick="kode_customer_sppn(${index_sppn})" placeholder="Kode SAP (Nomor Customer)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_gl_sppn_${index_sppn}" style="display:none">
														<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppn_${index_sppn}" >
																<select class="selectpicker "  data-live-search="true" data-dropup-auto="false" id="sap_gl_sppn_${index_sppn}" data-size="7" data-width="100%" name="isi_sppn_rekening" onchange="pilih_rekening_sppn(${index_sppn},'sap_gl_sppn_')">
																	<option value="" disabled selected>-- Pilih Kode GL --</option>
																	@foreach($gl as $r)
																	<option value="{{$r->master_gl_id}}">{{$r->master_gl_kode}} ({{$r->master_gl_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_gl_sppn_id_${index_sppn}" name="isi_sppn[${index_sppn}][gl]" class="form-control" onclick="kode_gl_sppn(${index_sppn})" placeholder="Kode SAP (Nomor GL)" autocomplete="off" required>
														</div>
													</div>
													
												</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Cost/Profit*</label>
							<label class="col-sm-9">
								<select class="form-control validate_sppn validate_spp_all" id="jenis_center_sppn_${index_sppn}" onchange="jc_sppn(${index_sppn})" name="isi_sppn[${index_sppn}][jenis_center]">
									<option value="" disabled selected>-- Pilih --</option>
									<option value="cost_center">Cost Center</option>
									<option value="profit_center">Profit Center</option>
								</select>
							</label>
							<label class="col-sm-3 col-form-label"></label>
							<div class="col-sm-9" id="cost_center_sppn_${index_sppn}" style="display: none">
							<select class="form-control" id="select_cost_center_sppn_${index_sppn}" name="isi_sppn[${index_sppn}][cost_center]">
														<option value="" disabled selected>-- Pilih Cost Center --</option>
														@foreach($costcenter as $cost)
														<option value="{{$cost->master_cost_center_id}}">{{$cost->master_cost_center_kode}} {{$cost->master_cost_center_keterangan}}</option>
														@endforeach
													</select>
							</div>
							<div class="col-sm-9" id="profit_center_sppn_${index_sppn}" style="display: none">
							<select class="form-control" id="select_profit_center_sppn_${index_sppn}" name="isi_sppn[${index_sppn}][profit_center]">
															<option value="" disabled selected>-- Pilih Profit Center --</option>
															@foreach($profitcenter as $profit)
																<option value="{{$profit->master_profit_center_id}}">{{$profit->master_profit_center_kode}} ({{$profit->master_profit_unit}})</option>
															@endforeach
														</select>
							</div>
						</div>
						<div class="form-group row">
													<label class="col-sm-3 col-form-label">Cash Flow*</label>
													<div class="col-sm-9">
														<select class="form-control validate_sppn validate_spp_all" id="cash_flow_sppn_${index_sppn}" name="isi_sppn[${index_sppn}][cash_flow]">
															<option value="" disabled selected>-- Pilih Cash Flow --</option>
															@foreach($cashflow as $cash)
																<option value="{{$cash->master_cash_flow_id}}">{{$cash->master_cash_flow_kode}} {{$cash->master_cash_flow_keterangan}}</option>
															@endforeach
														</select>
													</div>
												</div>
					</div>
					<div id="sub_isi_sppn_${index_sppn}_0">
						<div class="col-md-6">
						<div class="form-group row">
								<label class="col-sm-1 col-form-label">1. </label>
								<label class="col-sm-2 col-form-label">Uraian*</label>
								<div class="col-sm-9">
									<div style="border: 1px solid hsla(0, 0%, 0%, 0.15);">
										<div id="uraian_sppn_${index_sppn}_0" style="height:auto;min-height:100px">
											<textarea class="form-control" id="ckeditors_${index_sppn}_0" name="uraian_sppn[${index_sppn}][0][ket]"></textarea>
											</div>
										</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-1 col-form-label"></label>
								<label class="col-sm-2 col-form-label">Nominal*</label>
								<div class="col-sm-9">
									<input type="text" id="jumlah_sppn_${index_sppn}_0" name="uraian_sppn[${index_sppn}][0][jumlah]" class="form-control nominal validate_sppn validate_spp_all" placeholder="Nominal SPPn" autocomplete="off" required>
						
								</div>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="col-sm-12" style="margin-bottom: 10px">
								<button type="button" class="btn btn-success btn-sm" id="tambah_sub_isi_sppn_${index_sppn}_0" onclick="tambah_sub_isi_sppn(${index_sppn},0)">+</button>
							</div>
						</div>
					</div>
				</div>`);

		// InlineEditor.create(document.querySelector(`#uraian_sppn_${index_sppn}_1`),{
	    // 	placeholder: 'Uraian SPPN'
	    // }).then( editor => {
		// 	window.editor = editor;
		// }).catch( error => {
		// 	console.error( 'There was a problem initializing the editor.', error );
		// });
		for(a=0;a<index_sppn;a++){
			$("#tambah_isi_sppn_"+a).hide();
		}
		CKEDITOR.inline( 'ckeditors_'+index_sppn+'_0');
		$('.selectpicker').selectpicker();
		
		$('.nominal').mask('000.000.000.000.000.000.000', {reverse: true});
		var inputs = document.getElementsByClassName("validate_sppn");
				if(inputs){
					for(var i=0; i<inputs.length; i++){
						inputs[i].addEventListener("change",validateInput);
						inputs[i].addEventListener("keyup",validateInput);
						inputs[i].addEventListener("focus",validateInput);

					}
				}

				for(var i in CKEDITOR.instances){
					if (i.substring(0,9) == "ckeditors"){
						CKEDITOR.instances[i].on('change',function(){
							var urai = document.getElementById(this.name).parentElement;
							if(this.getData().replace(/<[^>]+>/g, '') == ""){
								urai.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px";
							}
							else{
								urai.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px";
							}
						})
					}
				}
	}

	function hapus_isi_sppn(isi,instance,count_instance){
		a = isi-1;
		$("#isi_sppn_"+isi).remove();
		$("#tambah_isi_sppn_"+a).show();
		for(var i=0; i<count_instance; i++){
			CKEDITOR.instances[instance+i].destroy();
		}
	}

	function tambah_sub_isi_sppn(isi,uraian){
		uraian++;
		$('#isi_sppn_'+isi).append(`<div id="sub_isi_sppn_${isi}_${uraian}">
								<div class="col-sm-5"></div>
								<div class="col-md-6">
									<div class="form-group row">
										<label class="col-sm-1 col-form-label">${uraian+1}. </label>
										<label class="col-sm-2 col-form-label">Uraian*</label>
										<div class="col-sm-9">
											<div style="border: 1px solid hsla(0, 0%, 0%, 0.15);">
												<div id="uraian_sppn_${isi}_${uraian}" style="height:auto;min-height:100px">
													<textarea class="form-control" id="ckeditors_${isi}_${uraian}" name="uraian_sppn[${isi}][${uraian}][ket]"></textarea>
													</div>
													</div>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-1 col-form-label"></label>
										<label class="col-sm-2 col-form-label">Nominal*</label>
										<div class="col-sm-9">
											<input type="text" id="jumlah_sppn_${isi}_${uraian}" name="uraian_sppn[${isi}][${uraian}][jumlah]" class="form-control nominal validate_sppn validate_spp_all" placeholder="Nominal SPPn" autocomplete="off" required>

											</div>
									</div>
								</div>
								<div class="col-sm-1">
									<div class="col-sm-12" id="hapus_sub_isi_sppn_${isi}_${uraian}" onclick="hapus_sub_isi_sppn(${isi},${uraian},'ckeditors_${isi}_${uraian}')" style="margin-bottom: 10px">
										<button type="button" class="btn btn-danger btn-sm">X</button>
									</div>
									<div class="col-sm-12" id="tambah_sub_isi_sppn_${isi}_${uraian}" onclick="tambah_sub_isi_sppn(${isi},${uraian})" style="margin-bottom: 10px">
										<button type="button" class="btn btn-success btn-sm">+</button>
									</div>
								</div>
							</div>`);

		// InlineEditor.create(document.querySelector(`#uraian_sppn_${isi}_${uraian}`),{
	    // 	placeholder: 'Uraian SPPN'
	    // }).then( editor => {
		// 	window.editor = editor;
		// }).catch( error => {
		// 	console.error( 'There was a problem initializing the editor.', error );
		// });
		for(a=0;a<uraian;a++){
			$("#tambah_sub_isi_sppn_"+isi+'_'+a).hide();
		}
		CKEDITOR.inline('ckeditors_'+isi+'_'+uraian);
		$('.nominal').mask('000.000.000.000.000.000.000', {reverse: true});
		var inputs = document.getElementsByClassName("validate_sppn");
						if(inputs){
							for(var i=0; i<inputs.length; i++){
								inputs[i].addEventListener("change",validateInput);
								inputs[i].addEventListener("keyup",validateInput);
							}
						}

					for(var i in CKEDITOR.instances){
						if (i.substring(0,9) == "ckeditors"){
							CKEDITOR.instances[i].on('change',function(){
								var urai = document.getElementById(this.name).parentElement;
								if(this.getData().replace(/<[^>]+>/g, '') == ""){
									urai.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px";
								}
								else{
									urai.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px";
								}
							});
						}
					}
	}

	function hapus_sub_isi_sppn(isi, sub_isi, instance){
		a=sub_isi-1;
		$('#sub_isi_sppn_'+isi+'_'+sub_isi).remove();
		$('#tambah_sub_isi_sppn_'+isi+'_'+a).show();
		CKEDITOR.instances[instance].destroy();
	}

</script>
<!-- End Javascript -->

@endsection

@section('footer')
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/piexif.min.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/sortable.min.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/fileinput.min.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/themes/fa/theme.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/locales/id.js"></script>
<script src="{{asset('')}}assets/vendor/ckeditor/ckeditor5-build-inline/build/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

@endsection