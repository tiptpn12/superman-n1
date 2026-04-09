@extends('template.master')
@section('title', 'SPP Khusus | Tambah SPP')
@section('header')
<link rel="stylesheet" href="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/css/fileinput.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

@endsection

@section('konten')
<?php 
$hakakses = Session::get('hak_akses');
?>
<!-- MAIN -->
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
<div class="main">
	<!-- MAIN CONTENT -->
	<div class="main-content">
		<form action="{{route('storesppk')}}" id="form_spp" method="post" target="" enctype="multipart/form-data">
		{{csrf_field()}}
		<div class="container-fluid">
			<h3 class="page-title">Buat SPP Keuangan</h3>
			<div class="row">
				<div class="col-md-12">
					<!-- FORM SPP -->
					<div class="panel">
						<div class="panel-heading">
							<h3 class="panel-title">Form SPP Keuangan</h3>
						</div>
						<div class="panel-body">
							<div class="form-group row" id="panel_jenis_form">
							    <label class="col-sm-2 col-form-label">Jenis Jalur</label>
							    <div class="col-sm-10">
							        <select class="form-control" id="jenis_jalur" name="jalur_pajak" required>
							            <option value="" disabled selected>-- Pilih Jenis Jalur --</option>
							            <option value=1>Melalui Pajak dan MIRO</option>
							            <option value=0>Tidak Melalui Pajak dan MIRO</option>
							        </select>
							    </div>
							</div>
							<div class="form-group row" id="panel_jenis_form">
								<label class="col-sm-2 col-form-label">Jenis Form</label>
								<div class="col-sm-10">
									<select class="form-control" id="jenis_form" name="jenis_form" required>
										<option value="" disabled selected>-- Pilih Jenis Form --</option>
										<option value="sppb">SPPb Saja</option>
										<option value="sppn">SPPn Saja</option>
										<option value="sppb_sppn">SPPb dan SPPn</option>
									</select>
								</div>
							</div>
							<div class="form-group row" id="panel_sumber_dana" >
									<label class="col-sm-2 col-form-label">Jenis Sumber Dana</label>
									<div class="col-sm-10">
										<select class="form-control validate_sppb validate_sppn validate_spp_all" id="sumber_dana" name="sumber_dana" required>
											<option value="" disabled selected>-- Pilih Sumber Dana --</option>
											@foreach($sumberdana as $b)
											<option value="{{$b->sumber_dana_id}}">{{$b->nama_sumber_dana}}</option>
											@endforeach
										</select>
									</div>
								</div>
						</div>
					</div>
					<!-- END FORM SPP -->
					<div class="panel" id="panel_sppb_sppn" style="display: none">
						<div class="panel-body">
								<div class="form-group row">  
									<label class="col-sm-2 col-form-label" id="label_kwitansi_spp">Kwitansi *</label>
									<div class="col-sm-10">
										<input type="text" id="kwitansi_spp" name="kwitansi" class="form-control" placeholder="Nama Pihak Kwitansi" autocomplete="off">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2 col-form-label">Referensi</label>
									<div class="col-sm-10">
										<input type="text" id="referensi_spp" name="referensi" class="form-control" placeholder="Nomor Referensi" autocomplete="off">
									</div>
								</div>
								<div id="fp_spp">
									<div id="faktur_pajak_spp_1">
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Faktur Pajak *</label>
											<div class="col-sm-8">
												<input type="text" id="faktur_pajak_spp" name="faktur_pajak_spp[1][fp]" class="form-control validate_spp_all" placeholder="Nomor Faktur Pajak 1" autocomplete="off">
											</div>
											<div class="col-sm-2" id="btn_tambah_faktur_pajak_1">
												<button type="button" class="btn btn-success btn-sm" onclick="tambah_faktur_pajak_spp(1)">+</button>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2 col-form-label">No. Kontrak *</label>
									<div class="col-sm-10">
										<input type="text" id="sp_opl_spp" name="sp_opl" class="form-control validate_spp_all" placeholder="Nomor Kontrak" autocomplete="off">
									</div>
								</div>
						</div>
					</div>
					<!-- FORM SPPB -->
					<div class="panel" id="panel_sppb" style="display: none">
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

								<!-- TAB DATA -->
								<div class="tab-pane fade in active" id="tab-informasi-sppb">
									<div class="form-group row" id="form_kwitansi_sppb">
										<label class="col-sm-2 col-form-label">Kwitansi *</label>
										<div class="col-sm-10">
											<input type="text" id="kwitansi_sppb" name="kwitansi_sppb" class="form-control validate_sppb validate_spp_all" placeholder="Nama Pihak Kwitansi SPPb" autocomplete="off" required>
										</div>
									</div>
									<div class="form-group row" id="form_referensi_sppb">
										<label class="col-sm-2 col-form-label">Referensi *</label>
										<div class="col-sm-10">
											<input type="text" id="referensi_sppb" name="referensi_sppb" class="form-control validate_sppb validate_spp_all" placeholder="Nomor Referensi SPPb" autocomplete="off" required>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-2 col-form-label">AU.53 *</label>
										<div class="col-sm-10">
											<input type="text" id="au53_sppb" name="au53_sppb" class="form-control validate_sppb validate_spp_all" placeholder=" Nomor AU. 53 SPPb" autocomplete="off" required>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-2 col-form-label">Berita Acara *</label>
										<div class="col-sm-10">
											<input type="text" id="berita_acara_sppb" name="berita_acara_sppb" class="form-control validate_sppb validate_spp_all" placeholder="Nomor Berita Acara SPPb" autocomplete="off" required>
										</div>
									</div>
									<div id="fp_sppb">
										<div id="faktur_pajak_sppb_1">
											<div class="form-group row" id="form_faktur_pajak_sppb">
												<label class="col-sm-2 col-form-label">Faktur Pajak *</label>
												<div class="col-sm-8">
													<input type="text" id="faktur_pajak_sppb" name="faktur_pajak_sppb[1][fp]" class="form-control validate_sppb validate_spp_all" placeholder="Nomor Faktur Pajak SPPb 1" autocomplete="off" required>
												</div>
												<div class="col-sm-2" id="btn_faktur_pajak_sppb_1">
													<button type="button" class="btn btn-success btn-sm" onclick="tambah_faktur_pajak_sppb(1)">+</button>									
												</div>
											</div>
											
										</div>
									</div>
									<div class="form-group row" id="form_sp_opl_sppb">
										<label class="col-sm-2 col-form-label">No. Kontrak *</label>
										<div class="col-sm-10">
											<input type="text" id="sp_opl_sppb" name="sp_opl_sppb" class="form-control validate_sppb validate_spp_all" placeholder="Nomor Kontrak SPPb" autocomplete="off" required>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-2 col-form-label">Bagian *</label>
										<div class="col-sm-10">
											<select class="form-control" id="bagian_sppb" disabled>
												<option value="" disabled>-- Pilih Jenis SPP --</option>
												<option value="2" selected>Keuangan dan Akuntansi</option>
											</select>
											<input type="hidden" value="2" name="bagian_sppb">
										</div>
									</div>
									@if($hakakses == 1)
									<div class="form-group row">
										<label class="col-sm-2 col-form-label">Tanggal *</label>
										<div class="col-sm-10">
											<input type="text" id="tanggal_sppb" name="tanggal_sppb" class="form-control date validate_sppb validate_spp_all" placeholder="Tanggal SPPb" value="{{DATE('d-m-Y')}}" autocomplete="off" required>
										</div>
									</div>
									@else
									<div class="form-group row">
										<label class="col-sm-2 col-form-label">Tanggal *</label>
										<div class="col-sm-10">
											<input type="text" id="tanggal_sppb" name="tanggal_sppb" class="form-control validate_sppb validate_spp_all" placeholder="Tanggal SPPb" value="{{DATE('d-m-Y')}}" autocomplete="off" readonly required>
										</div>
									</div>
									@endif
									<div class="form-group row">
											<label class="col-sm-2 col-form-label">Metode Pembayaran *</label>
											<div class="col-sm-10">
												<select class="form-control validate_sppb validate_spp_all" id="metode_pembayaran_sppb" name="metode_pembayaran_sppb" required>
													<option value="" disabled selected>-- Pilih Metode Pembayaran --</option>
													<option value="kas">Kas</option>
													<option value="bank">Bank</option>
													<option value="karyawan">Karyawan</option>
													<option value="skbdn">SKBDN</option>
													<option value="kas_negara">Kas Negara</option>
													
												</select>
											</div>
										</div>
										<div id="kas_sppb_input"  style="display:none;">
													<div class="form-group row">
														<label class="col-sm-2 col-form-label">Penerima *</label>
														<div id="kas_karyawan_input">
															<div class="col-sm-8" >
																<input type="text" class="form-control" id="nama_kas_sppb_input"  name="kas_nama_sppb" Placeholder="Nama Penerima">
																<span style="font-size: 10px;color:red;">Tulis "Terlampir" jika data lebih dari 1 (satu)</span>

															</div>
															<!-- <div class="col-sm-2" id="btn_karyawan_kas_sppb_input_1">
																<button type="button" class="btn btn-success btn-sm" onclick="tambah_karyawan_kas_sppb_input(1)">+</button>									
															</div> -->
														</div>
													</div>
													<div class="form-group row" id="alamat_karyawan_kas_input_1">
														<label class="col-sm-2 col-form-label">Alamat *</label>
														<div id="kas_karyawan_input">
															<div class="col-sm-8" >
																<input type="text" class="form-control" id="alamat_kas_sppb_input"  name="kas_alamat_sppb" Placeholder="Alamat">
																<span style="font-size: 10px;color:red;">Tulis "Terlampir" jika data lebih dari 1 (satu)</span>

															</div>
															<!-- <div class="col-sm-2" id="btn_karyawan_kas_sppb_input_1">
																<button type="button" class="btn btn-success btn-sm" onclick="tambah_karyawan_kas_sppb_input(1)">+</button>									
															</div> -->
														</div>
													</div>
											</div>
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
													<input type="text" id="atas_nama_bank_sppb_vendor"  name="atas_nama_bank_sppb_vendor" class="form-control" placeholder="Atas Nama Bank SPPb" >
													<span style="font-size: 10px;color:red;">Tulis "Terlampir" jika data lebih dari 1 (satuu)</span>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nama Bank *</label>
												<div class="col-sm-10">
												<input type="text" id="nama_bank_sppb_vendor"  name="nama_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" placeholder="Nama Bank" autocomplete="off">		
													<span style="font-size: 10px;color:red;">Tulis "Terlampir" jika data lebih dari 1 (satu)</span>

												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
												<div class="col-sm-10">
													<input type="text" id="rekening_bank_sppb_vendor"  name="rekening_bank_sppb_vendor" class="form-control" onclick="data_bank_sppb(1)" placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
													<span style="font-size: 10px;color:red;">Tulis "Terlampir" jika data lebih dari 1 (satu)</span>

												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Alamat *</label>
												<div class="col-sm-8">
													<input type="text" id="alamat_bank_sppb_vendor"    name="alamat_bank_sppb_vendor" class="form-control"  placeholder="Alamat Bank SPPb" autocomplete="off">
													<span style="font-size: 10px;color:red;">Tulis "Terlampir" jika data lebih dari 1 (satu)</span>
												</div>
											</div>
											
										</div>
										<div id="bank_sppb_karyawan" style="display: none">	
											<div id="bank_sppb_karyawan_input">
												<div class="form-group row" id="atas_nama_karyawan_sppb">
													<label class="col-sm-2 col-form-label">Atas Nama Rekening *</label>
													<div class="col-sm-8">
														<input type="text" id="atas_nama_bank_sppb_karyawan_input_1"  name="karyawan_sppb_input[1][nama]" class="form-control"  placeholder="Atas Nama Bank SPPb" autocomplete="off">						
													</div>
													<div class="col-sm-2" id="btn_karyawan_bank_sppb_input_1">
														<button type="button" class="btn btn-success btn-sm" id="btn_tambah_karyawan_bank_sppb_input_1" onclick="tambah_karyawan_bank_sppb_input(1)">+</button>									
													</div>	
												</div>								
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nama Bank *</label>
													<div class="col-sm-8">
													<input type="text" id="nama_bank_sppb_karyawan_input_1"   name="karyawan_sppb_input[1][bank]" class="form-control" placeholder="Nama Bank SPPb" autocomplete="off">
													</div>
													
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
													<div class="col-sm-8">
														<input type="text" id="rekening_bank_sppb_karyawan_input_1"   name="karyawan_sppb_input[1][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Alamat *</label>
													<div class="col-sm-8">
														<input type="text" id="alamat_bank_sppb_karyawan_input_1"  name="karyawan_sppb_input[1][alamat]" class="form-control"  placeholder="Alamat Bank SPPb" autocomplete="off">
													</div>
												</div>
											</div>
											<div id="bank_sppb_karyawan_master">
												<div class="form-group row" id="atas_nama_karyawan_sppb">
													<label class="col-sm-2 col-form-label">Atas Nama Rekening *</label>
													<div class="col-sm-8">
														<input type="text" id="atas_nama_bank_sppb_karyawan_1" onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][nama]" class="form-control"  placeholder="Atas Nama Bank SPPb" autocomplete="off">						
													</div>
													<div class="col-sm-2" id="btn_karyawan_bank_sppb_1">
														<button type="button" class="btn btn-success btn-sm" id="btn_tambah_karyawan_bank_sppb_1" onclick="tambah_karyawan_bank_sppb(1)">+</button>									
													</div>	
												</div>								
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nama Bank *</label>
													<div class="col-sm-8">
													<input type="text" id="nama_bank_sppb_karyawan_1" onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][bank]" class="form-control" placeholder="Nama Bank SPPb" autocomplete="off">
													</div>
													
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
													<div class="col-sm-8">
														<input type="text" id="rekening_bank_sppb_karyawan_1"  onclick="bank_karyawan_sppb(1)" name="karyawan_sppb[1][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Alamat *</label>
													<div class="col-sm-8">
														<input type="text" id="alamat_bank_sppb_karyawan_1"   name="karyawan_sppb[1][alamat]" class="form-control"  placeholder="Alamat Bank SPPb" autocomplete="off">
													</div>
												</div>
											</div>
											
										</div>
									<div class="form-group row">
										<label class="col-sm-2 col-form-label">Catatan</label>
										<div class="col-sm-10">
											<textarea class="form-control " id="catatan_sppb" name="catatan_sppb" placeholder="Catatan SPPb" rows="4"></textarea>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-2 col-form-label">Upload Dokumen Pendukung</label>
										<div class="col-sm-10">
											<div class="file-loading">
												<input type="file" id="dokumen_pendukung_sppb" name="dokumen_pendukung_sppb[]" class="file-multiple" multiple>									
											</div>
										</div>
									</div>
								</div>
								<!-- END TAB DATA -->

								<!-- TAB ISI -->
								<div class="tab-pane fade" id="tab-isi-sppb">
										<div id="isi_sppb_1" class="col-sm-12">
											<div  style="padding-bottom: 10px; margin-bottom: 20px; border-bottom: 1px solid #eaeaea;">
												<font size="4" style="margin-right: 20px">Isi 1. </font>
												<button type="button" class="btn btn-info btn-sm" onclick="tambah_isi_sppb()">+</button>
											</div>
											<div class="col-sm-5">
												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Kode KBB *</label>
													<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppb_1" >
														<select class="selectpicker slct_sppb"  data-live-search="true" data-dropup-auto="false" id="kode_kbb_sppb_1" data-width="100%" name="isi_sppb[1][kode_kbb]" data-size="7" onchange="pilih_rekening_sppb(1,'kode_kbb_sppb_')">
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
														<select class="form-control validate_sppb validate_spp_all" id="jenis_sap_sppb_1" onchange="js_sppb(1)" name="isi_sppb[1][jenis_sap]">
															<option value="" disabled selected>-- Pilih Jenis Kode SAP --</option>
															<option value="vendor">Nomor Vendor</option>
															<option value="gl">Nomor GL</option>
															<option value="customer">Nomor Customer</option>
														</select>
													</label>
													<label class="col-sm-3 col-form-label"></label>

													<div id="nomor_vendor_sppb_1" style="display:none">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppb_1" >
																<select class="selectpicker slct_sppb"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_vendor_sppb_1" data-size="7" name="isi_sppb_rekening" onchange="pilih_rekening_sppb(1,'sap_vendor_sppb_')">
																	<option value="" disabled selected>-- Pilih Kode Vendor --</option>
																	@foreach($rekening as $r)
																	<option value="{{$r->master_rekening_id}}">{{$r->master_rekening_kode_sap}} ({{$r->master_rekening_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_vendor_sppb_id_1" name="isi_sppb[1][vendor]" class="form-control" onclick="kode_rekening_sppb(1)" placeholder="Kode SAP (Nomor Vendor)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_customer_sppb_1" style="display:none">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppb_1" >
																<select class="selectpicker"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_customer_sppb_1" data-size="7" name="isi_sppb_customer" onchange="pilih_rekening_sppb(1,'sap_customer_sppb_')">
																	<option value="" disabled selected>-- Pilih Kode Customer --</option>
																	@foreach($customer as $r)
																	<option value="{{$r->master_customer_id}}">{{$r->master_customer_kode_sap}} ({{$r->master_customer_nama}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_customer_sppb_id_1" name="isi_sppb[1][customer]" class="form-control" onclick="kode_customer_sppb(1)" placeholder="Kode SAP (Nomor Vendor)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_gl_sppb_1" style="display:none">
														<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppb_1" >
																<select class="selectpicker"  data-live-search="true" data-dropup-auto="false" id="sap_gl_sppb_1" data-size="7" data-width="100%" name="isi_sppb_gl" onchange="pilih_rekening_sppb(1,'sap_gl_sppb_')">
																	<option value="" disabled selected>-- Pilih Kode GL --</option>
																	@foreach($gl as $r)
																	<option value="{{$r->master_gl_id}}" data-budget_1="{{$r->jumlah_budget}}" data-budget_1="{{$r->master_gl_id}}">{{$r->master_gl_kode}} ({{$r->master_gl_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														</div>
														<label class="col-sm-12 col-form-label mt-2"></label>
														<label class="col-sm-3 col-form-label">RKAP</label>

														<div class="col-sm-9" >
															<input type="text" class="form-control budget_gl_1 nominal" placeholder="Budget" autocomplete="off" readonly>
															<input type="hidden"  class="budget_gl_hide_1" name="budget" >
														</div>
														<label class="col-sm-12 col-form-label mt-2"></label>
														<label class="col-sm-3 col-form-label">Realisasi</label>

														<div class="col-sm-9" >
															<input type="text" class="form-control realisasi_1 nominal" placeholder="Budget" autocomplete="off" readonly>
															<input type="hidden"  class="budget_gl_hide" name="budget" >
														</div>
														<label class="col-sm-12 col-form-label mt-2"></label>
														<label class="col-sm-3 col-form-label">On Process</label>

														<div class="col-sm-9" >
															<input type="text" class="form-control onproses_1 nominal" placeholder="Budget" autocomplete="off" readonly>
															<input type="hidden"  class="budget_gl_hide" name="budget" >
														</div>
														<label class="col-sm-12 col-form-label mt-2"></label>
														<label class="col-sm-3 col-form-label">Sisa</label>

														<div class="col-sm-9" >
															<input type="text" class="form-control sisa_1 nominal" placeholder="Budget" autocomplete="off" readonly>
															<input type="hidden"  class="budget_gl_hide" name="budget" >
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_gl_sppb_id_1" name="isi_sppb[1][gl]" class="form-control" onclick="kode_gl_sppb(1)" placeholder="Kode SAP (Nomor GL)" autocomplete="off" required>
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_gl_sppb_id_1" name="isi_sppb[1][gl]" class="form-control" onclick="kode_gl_sppb(1)" placeholder="Kode SAP (Nomor GL)" autocomplete="off" required>
														</div>
													</div>
													
												</div>
												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Cost/Profit*</label>
													<label class="col-sm-9">
														<select class="form-control validate_sppb validate_spp_all" id="jenis_center_sppb_1" onchange="jc_sppb(1)" name="isi_sppb[1][jenis_center]">
															<option value="" disabled selected>-- Pilih --</option>
															<option value="cost_center">Cost Center</option>
															<option value="profit_center">Profit Center</option>
														</select>
													</label>
													<label class="col-sm-3 col-form-label"></label>
													<div class="col-sm-9" id="cost_center_sppb_1" style="display: none">
														<select class="form-control " id="select_cost_center_sppb_1" name="isi_sppb[1][cost_center]">
															<option value="" disabled selected>-- Pilih Cost Center --</option>
															@foreach($costcenter as $cost)
																<option value="{{$cost->master_cost_center_id}}">{{$cost->master_cost_center_kode}} {{$cost->master_cost_center_keterangan}}</option>
															@endforeach
														</select>
													</div>
													<div class="col-sm-9" id="profit_center_sppb_1" style="display: none">
														<select class="form-control " id="select_profit_center_sppb_1" name="isi_sppb[1][profit_center]">
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
														<select class="form-control validate_sppb validate_spp_all" id="cash_flow_sppb_1" name="isi_sppb[1][cash_flow]">
															<option value="" disabled selected>-- Pilih Cash Flow --</option>
															@foreach($cashflow as $cash)
																<option value="{{$cash->master_cash_flow_id}}">{{$cash->master_cash_flow_kode}} {{$cash->master_cash_flow_keterangan}}</option>
															@endforeach
														</select>
													</div>
												</div>
											</div>
											<div id="sub_isi_sppb_1_1">
												<div class="col-md-6">
													<div class="form-group row">
														<label class="col-sm-1 col-form-label">1. </label>
														<label class="col-sm-2 col-form-label">Uraian *</label>
														<div class="col-sm-9">
															<div style="border: 1px solid hsla(0, 0%, 0%, 0.15);">
																<div id="uraian_sppb_1_1" style="height:auto;min-height:100px">
																	<!-- <input type="hidden" name="uraian_sppb[0][0][uraian]" id="uraian_sppb_value_1_1"> -->
																	<textarea class="form-control" id="ckeditor_1_1" name="uraian_sppb[1][1][ket]"></textarea>
																</div>
															</div>
														</div>
													</div>
													<div class="form-group row">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">Nominal*</label>
														<div class="col-sm-9">
															<input type="text"  id="jumlah_sppb_1_1" name="uraian_sppb[1][1][jumlah]" class="form-control nominal validate_sppb validate_spp_all" placeholder="Nominal SPPb" autocomplete="off" required>
															<label class="col-sm-6 col-form-label cek_dana_gagal_1_1" style="display:none;color:red;">Dana melebihi sisa RKAP</label>
															<label class="col-sm-6 col-form-label cek_dana_berhasil_1_1" style="display:none;color:green;">Dana dibawah sisa RKAP</label>
														</div>
													</div>
												</div>
												<div class="col-sm-1">
													<div class="col-sm-12" style="margin-bottom: 10px">
														<button type="button" class="btn btn-success btn-sm" onclick="tambah_sub_isi_sppb(1)">+</button>
													</div>
												</div>
											</div>
										</div>
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
									<div class="form-group row" id="form_kwitansi_sppn">
										<label class="col-sm-2 col-form-label">Kwitansi *</label>
										<div class="col-sm-10">
											<input type="text" id="kwitansi_sppn" name="kwitansi_sppn" class="form-control validate_sppn validate_spp_all" placeholder="Nama Pihak Kwitansi SPPn" autocomplete="off" required>
										</div>
									</div>
									<div class="form-group row" id="form_referensi_sppn">
										<label class="col-sm-2 col-form-label">Referensi *</label>
										<div class="col-sm-10">
											<input type="text" id="referensi_sppn" name="referensi_sppn" class="form-control" placeholder="Nomor Referensi SPPn" autocomplete="off" required>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-2 col-form-label">BA/AU.58 *</label>
										<div class="col-sm-10">
											<input type="text" id="baau58_sppn" name="baau58_sppn" class="form-control validate_sppn validate_spp_all" placeholder="Nomor BA/AU. 58 SPPn" autocomplete="off" required>
										</div>
									</div>
									<div id="fp_sppn">
										<div id="faktur_pajak_sppn_1">
											<div class="form-group row" id="form_faktur_pajak_sppn">
												<label class="col-sm-2 col-form-label">Faktur Pajak *</label>
												<div class="col-sm-8">
													<input type="text" id="faktur_pajak_sppn_1" name="faktur_pajak_sppn[1][fp]" class="form-control validate_sppn" placeholder="Nomor Faktur Pajak SPPn 1" autocomplete="off" required>
												</div>
												<div class="col-sm-2" id="btn_faktur_pajak_sppn_1">
													<button type="button" class="btn btn-success btn-sm" onclick="tambah_faktur_pajak_sppn(1)">+</button>									
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row" id="form_sp_opl_sppn">
										<label class="col-sm-2 col-form-label">No. Kontrak *</label>
										<div class="col-sm-10">
											<input type="text" id="sp_opl_sppn" name="sp_opl_sppn" class="form-control validate_sppn validate_spp_all" placeholder="Nomor Kontrak SPPn" autocomplete="off" required>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-2 col-form-label">Bagian *</label>
										<div class="col-sm-10">
											<select class="form-control validate_sppn validate_spp_all" id="bagian_sppn" disabled>
												<option value="" disabled>-- Pilih Bagian --</option>
												<option value="2" selected>Keuangan dan Akuntansi</option>
											</select>
											<input type="hidden" value="2" name="bagian_sppn">
										</div>
									</div>
									@if($hakakses == 1)
									<div class="form-group row">
										<label class="col-sm-2 col-form-label">Tanggal *</label>
										<div class="col-sm-10">
											<input type="text" id="tanggal_sppn" name="tanggal_sppn" class="form-control date validate_sppn validate_spp_all" placeholder="Tanggal Sppn" value="{{DATE('d-m-Y')}}" autocomplete="off" required>
										</div>
									</div>
									@else
									<div class="form-group row">
										<label class="col-sm-2 col-form-label">Tanggal *</label>
										<div class="col-sm-10">
											<input type="text" id="tanggal_sppn" name="tanggal_sppn" class="form-control validate_sppn validate_spp_all" placeholder="Tanggal Sppn" value="{{DATE('d-m-Y')}}" autocomplete="off" readonly required>
										</div>
									</div>
									@endif
									<!-- <div class="form-group row">
											<label class="col-sm-2 col-form-label">Metode Pembayaran *</label>
											<div class="col-sm-10">
												<select class="form-control validate_sppn validate_spp_all" id="metode_pembayaran_sppn" name="metode_pembayaran_sppn" required>
													<option value="" disabled selected>-- Pilih Metode Pembayaran --</option>
													<option value="kas">Kas</option>
													<option value="bank">Bank</option>
													<option value="karyawan">Karyawan</option>
												</select>
											</div>
										</div>
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
											<input type="hidden" id="id_bank_sppn_1" name="id_bank_sppn" class="form-control" onclick="data_bank_sppn(1)" placeholder="Id Bank SPPn" autocomplete="off">
											<div class="form-group row" id="atas_nama_vendor_sppn">
												<label class="col-sm-2 col-form-label">Atas Nama *</label>
												<div class="col-sm-10">
													<input type="text" id="atas_nama_bank_sppn_vendor" name="atas_nama_bank_sppn_vendor" class="form-control" onclick="data_bank_sppn(1)" placeholder="Atas Nama Bank SPPn" autocomplete="off">
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nama Bank *</label>
												<div class="col-sm-10">
												<input type="text" id="nama_bank_sppn_vendor" name="nama_bank_sppn" class="form-control" onclick="data_bank_sppn(1)" placeholder="Nama Rekening Bank" autocomplete="off" >												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
												<div class="col-sm-10">
													<input type="text" id="rekening_bank_sppn_vendor" name="rekening_bank_sppn" class="form-control" onclick="data_bank_sppn(1)" placeholder="Nomor Rekening Bank SPPn" autocomplete="off">
												</div>
											</div>
											
											
										</div>
										<div id="bank_sppn_karyawan" style="display: none">
											<div class="form-group row" id="atas_nama_karyawan_bank_sppn">
												<label class="col-sm-2 col-form-label">Atas Nama Rekening *</label>
												<div class="col-sm-8">
													<input type="text" id="atas_nama_bank_sppn_karyawan_1"  onclick="bank_karyawan_sppn(1)" name="karyawan_sppn[1][nama]" class="form-control"  placeholder="Atas Nama Bank SPPn" autocomplete="off">													
						
												</div>
												<div class="col-sm-2" id="btn_karyawan_bank_sppn_1">
														<button type="button" class="btn btn-success btn-sm" onclick="tambah_karyawan_bank_sppn(1)">+</button>									
													</div>
											</div>
											<div class="form-group row">

												<label class="col-sm-2 col-form-label">Nama Bank *</label>
												<div class="col-sm-8">
												<input type="text" id="nama_bank_sppn_karyawan_1" name="karyawan_sppn[1][bank]" onclick="bank_karyawan_sppn(1)" class="form-control" placeholder="Nama Rekening Bank" autocomplete="off">												
												</div>
												
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nomor Rekening *</label>
												<div class="col-sm-8">
													<input type="text" id="rekening_bank_sppn_karyawan_1" onclick="bank_karyawan_sppn(1)" name="karyawan_sppn[1][no_rek]" class="form-control" placeholder="Nomor Rekening Bank SPPn" autocomplete="off">
												</div>
											</div>
											
										</div> -->
									<div id="diterima_sppn_input"  style="display:none;">
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Diterima dari *</label>
												<div id="kas_karyawan_input">
													<div class="col-sm-8" >
														<input type="text" class="form-control"  id="nama_diterima_sppn_input" name="diterima_dari" Placeholder="Nama Penerima">
															<span style="font-size: 10px;color:red;">Tulis "Terlampir" jika data lebih dari 1 (satu)</span>
													</div>
													<!-- <div class="col-sm-2" id="btn_karyawan_kas_sppb_input_1">
														<button type="button" class="btn btn-success btn-sm" onclick="tambah_karyawan_kas_sppb_input(1)">+</button>									
													</div> -->
												</div>
											</div>
											<div class="form-group row" id="alamat_karyawan_kas_input_1">
												<label class="col-sm-2 col-form-label">Alamat *</label>
												<div id="kas_karyawan_input">
													<div class="col-sm-8" >
														<input type="text" class="form-control"  id="alamat_diterima_sppn_input" name="alamat_sppn" Placeholder="Alamat">
														<span style="font-size: 10px;color:red;">Tulis "Terlampir" jika data lebih dari 1 (satu)</span>
													</div>
													<!-- <div class="col-sm-2" id="btn_karyawan_kas_sppb_input_1">
														<button type="button" class="btn btn-success btn-sm" onclick="tambah_karyawan_kas_sppb_input(1)">+</button>									
													</div> -->
												</div>
											</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-2 col-form-label">Catatan</label>
										<div class="col-sm-10">
											<textarea class="form-control" id="catatan_sppn" name="catatan_sppn" placeholder="Keterangan Tambahan" rows="4" ></textarea>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-2 col-form-label">Upload Dokumen Pendukung</label>
										<div class="col-sm-10">
											<input type="file" id="dokumen_pendukung_sppn" name="dokumen_pendukung_sppn[]" class="file-multiple" multiple>
										</div>
									</div>
								</div>
								<!-- END TAB INFORMASI -->

								<!-- TAB ISI -->
								<div class="tab-pane fade" id="tab-isi-sppn">
									<div id="isi_sppn_1" class="col-sm-12">
										<div  style="padding-bottom: 10px; margin-bottom: 20px; border-bottom: 1px solid #eaeaea;">
											<font size="4" style="margin-right: 20px">Isi 1. </font>
											<button class="btn btn-info btn-sm" onclick="tambah_isi_sppn()">+</button>
										</div>
										<div class="col-sm-5">
										<div class="form-group row">
													<label class="col-sm-3 col-form-label">Kode KBB *</label>
													<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppn_1" >
														<select class="selectpicker"  data-live-search="true" data-dropup-auto="false" id="kode_kbb_sppn_1" data-width="100%" name="isi_sppn[1][kode_kbb]" data-size="7" onchange="pilih_rekening_sppn(1,'kode_kbb_sppn_')">
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
														<select class="form-control validate_sppn validate_spp_all" id="jenis_sap_sppn_1" onchange="js_sppn(1)" name="isi_sppn[1][jenis_sap]">
															<option value="" disabled selected>-- Pilih Jenis Kode SAP --</option>
															<option value="vendor">Nomor Vendor</option>
															<option value="gl">Nomor GL</option>
															<option value="customer">Nomor Customer</option>

														</select>
													</label>
													<label class="col-sm-3 col-form-label"></label>

													<div id="nomor_vendor_sppn_1" style="display:none">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppn_1" >
																<select class="selectpicker"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_vendor_sppn_1" data-size="7" name="isi_sppn_rekening" onchange="pilih_rekening_sppn(1,'sap_vendor_sppn_')">
																	<option value="" disabled selected>-- Pilih Kode Vendor --</option>
																	@foreach($rekening as $r)
																	<option value="{{$r->master_rekening_id}}">{{$r->master_rekening_kode_sap}} ({{$r->master_rekening_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_vendor_sppn_id_1" name="isi_sppn[1][vendor]" class="form-control" onclick="kode_rekening_sppn(1)" placeholder="Kode SAP (Nomor Vendor)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_customer_sppn_1" style="display:none">
														<div class="col-sm-9">
															<div class="row-fluid" id="parent_kbb_sppn_1" >
																<select class="selectpicker"  data-live-search="true" data-dropup-auto="false" data-width="100%" id="sap_customer_sppn_1" data-size="7" name="isi_sppn_rekening" onchange="pilih_rekening_sppn(1,'sap_customer_sppn_')">
																	<option value="" disabled selected>-- Pilih Kode Customer --</option>
																	@foreach($customer as $r)
																	<option value="{{$r->master_customer_id}}">{{$r->master_customer_kode_sap}} ({{$r->master_customer_nama}})</option>
																	@endforeach
																</select>
															</div>
														
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_customer_sppn_id_1" name="isi_sppn[1][customer]" class="form-control" onclick="kode_customer_sppn(1)" placeholder="Kode SAP (Nomor Customer)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_gl_sppn_1" style="display:none">
														<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppn_1" >
																<select class="selectpicker"  data-live-search="true" data-dropup-auto="false" id="sap_gl_sppn_1" data-size="7" data-width="100%" name="isi_sppn_rekening" onchange="pilih_rekening_sppn(1,'sap_gl_sppn_')">
																	<option value="" disabled selected>-- Pilih Kode GL --</option>
																	@foreach($gl as $r)
																	<option value="{{$r->master_gl_id}}" data-budgetsppn_1="{{$r->jumlah_budget}}" data-budgetsppn_1="{{$r->master_gl_id}}">{{$r->master_gl_kode}} ({{$r->master_gl_keterangan}})</option>
																	@endforeach
																</select>
															</div>
														</div>
														<label class="col-sm-12 col-form-label mt-2"></label>
														<label class="col-sm-3 col-form-label">RKAP</label>

														<div class="col-sm-9" >
															<input type="text" class="form-control budget_glsppn_1 nominal" placeholder="Budget" autocomplete="off" readonly>
															<input type="hidden"  class="budget_glsppn_hide_1" name="budget" >
														</div>
														<label class="col-sm-12 col-form-label mt-2"></label>
														<label class="col-sm-3 col-form-label">Realisasi</label>

														<div class="col-sm-9" >
															<input type="text" class="form-control realisasisppn_1 nominal" placeholder="Budget" autocomplete="off" readonly>
															<input type="hidden"  class="budget_glsppn_hide" name="budget" >
														</div>
														<label class="col-sm-12 col-form-label mt-2"></label>
														<label class="col-sm-3 col-form-label">On Process</label>

														<div class="col-sm-9" >
															<input type="text" class="form-control onprosessppn_1 nominal" placeholder="Budget" autocomplete="off" readonly>
															<input type="hidden"  class="budget_glsppn_hide" name="budget" >
														</div>
														<label class="col-sm-12 col-form-label mt-2"></label>
														<label class="col-sm-3 col-form-label">Sisa</label>

														<div class="col-sm-9" >
															<input type="text" class="form-control sisasppn_1 nominal" placeholder="Budget" autocomplete="off" readonly>
															<input type="hidden"  class="budget_glsppn_hide" name="budget" >
														</div>
														<div class="col-sm-9">
															<input type="text" style="display:none;" id="sap_gl_sppn_id_1" name="isi_sppn[1][gl]" class="form-control" onclick="kode_gl_sppn(1)" placeholder="Kode SAP (Nomor GL)" autocomplete="off" required>
														</div>
													</div>
													
												</div>
												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Cost/Profit*</label>
													<label class="col-sm-9">
														<select class="form-control validate_sppn validate_spp_all" id="jenis_center_sppn_1" onchange="jc_sppn(1)" name="isi_sppn[1][jenis_center]">
															<option value="" disabled selected>-- Pilih --</option>
															<option value="cost_center">Cost Center</option>
															<option value="profit_center">Profit Center</option>
														</select>
													</label>
													<label class="col-sm-3 col-form-label"></label>
													<div class="col-sm-9" id="cost_center_sppn_1" style="display: none">
														<select class="form-control " id="select_cost_center_sppn_1" name="isi_sppn[1][cost_center]">
															<option value="" disabled selected>-- Pilih Cost Center --</option>
															@foreach($costcenter as $cost)
																<option value="{{$cost->master_cost_center_id}}">{{$cost->master_cost_center_kode}} {{$cost->master_cost_center_keterangan}}</option>
															@endforeach
														</select>
													</div>
													<div class="col-sm-9" id="profit_center_sppn_1" style="display: none">
														<select class="form-control " id="select_profit_center_sppn_1" name="isi_sppn[1][profit_center]">
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
														<select class="form-control validate_sppn validate_spp_all" id="cash_flow_sppn" name="isi_sppn[1][cash_flow]">
															<option value="" disabled selected>-- Pilih Cash Flow --</option>
															@foreach($cashflow as $cash)
																<option value="{{$cash->master_cash_flow_id}}">{{$cash->master_cash_flow_kode}} {{$cash->master_cash_flow_keterangan}}</option>
															@endforeach
														</select>
													</div>
												</div>
										</div>
										<div id="sub_isi_sppn_1_1">
											<div class="col-md-6">
												<div class="form-group row">
													<label class="col-sm-1 col-form-label">1. </label>
													<label class="col-sm-2 col-form-label">Uraian *</label>
													<div class="col-sm-9">
															<div style="border: 1px solid hsla(0, 0%, 0%, 0.15);">
																<div id="uraian_sppn_1_1"style="height:auto;min-height:100px" >
																<textarea class="form-control" id="ckeditors_1_1" name="uraian_sppn[1][1][ket]"></textarea>
																</div>
															</div>
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-1 col-form-label"></label>
													<label class="col-sm-2 col-form-label">Nominal*</label>
													<div class="col-sm-9">
														<input type="text" id="jumlah_sppn_1_1" name="uraian_sppn[1][1][jumlah]" class="form-control nominal validate_sppn validate_spp_all" placeholder="Nominal SPPn" autocomplete="off"  data-affixes-stay="true" data-prefix="Rp. " data-thousands="." data-decimal="," required/>
														<label class="col-sm-6 col-form-label sppncek_dana_gagal_1_1" style="display:none;color:red;">Dana melebihi sisa RKAP</label>
														<label class="col-sm-6 col-form-label sppncek_dana_berhasil_1_1" style="display:none;color:green;">Dana dibawah sisa RKAP</label>
													</div>
												</div>
											</div>
											<div class="col-sm-1">
												<div class="col-sm-12" style="margin-bottom: 10px">
													<button class="btn btn-success btn-sm" onclick="tambah_sub_isi_sppn(1, 1)">+</button>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- END TAB ISI -->

							</div>
						</div>
					</div>
					<!-- END FORM SPPN -->

					<!-- FORM SUBMIT -->
					<div class="panel" id="panel_submit" style="display: none">
						<div class="panel-body">
							<center>
							<br>
								<!-- <br>
								<button class="btn btn-primary" type="button" style="margin-bottom: 15px">Simpan Sementara</button> -->
								<button class="btn btn-success" type="button" id="simpan" style="margin-bottom: 15px">Simpan</button>
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
                                <th>No. </th>
                                <td style="display:none;">id</th>
                                <th>No KBB</th>
                                <th>No SAP</th>
                                <th>Keterangan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekening as $key => $value)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td style="display:none;"> {{$value->master_rekening_id}}</td>
                                <td>{{$value->master_rekening_kode_kbb}}</td>
                                <td>{{$value->master_rekening_kode_sap}}</td>
                                <td>{{$value->master_rekening_keterangan}}</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm"
                                        onclick="pilih_rekening_sppb('{{$value->master_rekening_id}}','{{$value->master_rekening_kode_kbb}}', '{{$value->master_rekening_kode_sap}}', '{{$value->master_rekening_keterangan}}')"
                                        title="Pilih"><i class="fa fa-check"></i></button>
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
								<th style="display:none;"> id </th>
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
							<td style="display:none;"> {{$value->master_rekening_id}}</td>
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
	<div class="modal-dialog modal-lg" style="width: 1200px;">
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
								<th style="display:none">id</th>
								<th>Nama Vendor</th>
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
								<th>{{$value->master_vendor_nama}}</th>
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
								<th style="display:none;"> id </th>
								<th>Nama Vendor</th>
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
								<td>{{$value->master_vendor_nama}}</td>
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
	var jum_nom = [];
	var sisa = [];
	$('#jenis_jalur').change(function(event) {
		$( "#jumlah_sppb_1_1").on('keyup',function(e) {
							console.log("First - #jumlah_sppb_"+'1'+"_"+'1');
							jum_nom[1] = [];
							jum_nom[1][1] = this.value.replace(/[^\d,]/g, "");
							var jum_nom_total = 0;
							for (let i = 1; i <= sub_index_sppb[1]; i++) {
								var jum_nom_value = $("#jumlah_sppb_1_"+i) ? $("#jumlah_sppb_1_"+i).val().replace(/[^\d,]/g, "") : 0;
								jum_nom_total = jum_nom_value ? parseInt(jum_nom_total) + parseInt(jum_nom_value) : jum_nom_total;
								console.log(jum_nom_value);
								console.log(jum_nom_total);
							}
							if (jum_nom_total > sisa[1]) {
								$('.cek_dana_gagal_1_1').css('display','block');
								$('.cek_dana_berhasil_1_1').css('display','none');
								// $("#simpan").prop("disabled", true);
							}else{
								$('.cek_dana_berhasil_1_1').css('display','block');
								$('.cek_dana_gagal_1_1').css('display','none');
								// $("#simpan").prop("disabled", false);

							}
						});
						$( "#jumlah_sppn_1_1").on('keyup',function(e) {
							console.log("First - #jumlah_sppb_"+'1'+"_"+'1');
							jum_nom[1] = [];
							jum_nom[1][1] = this.value.replace(/[^\d,]/g, "");
							var jum_nom_total = 0;
							for (let i = 1; i <= sub_index_sppn[1]; i++) {
								var jum_nom_value = $("#jumlah_sppn_1_"+i) ? $("#jumlah_sppn_1_"+i).val().replace(/[^\d,]/g, "") : 0;
								jum_nom_total = jum_nom_value ? parseInt(jum_nom_total) + parseInt(jum_nom_value) : jum_nom_total;
								console.log(jum_nom_value);
								console.log(jum_nom_total);
							}
							if (jum_nom_total > sisa[1]) {
								$('.sppncek_dana_gagal_1_1').css('display','block');
								$('.sppncek_dana_berhasil_1_1').css('display','none');
								// $("#simpan").prop("disabled", true);
							}else{
								$('.sppncek_dana_berhasil_1_1').css('display','block');
								// $("#simpan").prop("disabled", false);
								$('.sppncek_dana_gagal_1_1').css('display','none');
							}
						});
	});


	$(document).ready(function() {
		CKEDITOR.inline( 'ckeditor_1_1' );
		
		CKEDITOR.inline( 'ckeditors_1_1' );
		

		
		$('#jenis_form').change(function(event) {
			if ($(this).val() == 'sppb') {
				$('#panel_sppb').show();
				$('#panel_sppn').hide();
				$('#panel_sppb_sppn').hide();
				$('#form_kwitansi_sppb').show();
				$('#form_referensi_sppb').show();
				$('#form_sp_opl_sppb').show();
				$('#form_faktur_pajak_sppb').show();
				$('#form_kwitansi_sppn').show();
				$('#form_referensi_sppn').show();
				$('#form_sp_opl_sppn').show();
				$('#diterima_sppn_input').hide();
				$('#form_faktur_pajak_sppn').show();
				$("#jumlah_sppn_1_1").attr('required',false);
				document.getElementById("nama_diterima_sppn_input").className = "form-control"; 			
				document.getElementById("alamat_diterima_sppn_input").className = "form-control";
			} else if ($(this).val() == 'sppn') {
				$('#panel_sppb').hide();
				$('#panel_sppn').show();
				$('#panel_dokumen_pendukung').show();
				$('#panel_sppb_sppn').hide();
				$('#diterima_sppn_input').show();
				$('#form_kwitansi_sppb').show();
				$('#form_referensi_sppb').show();
				$('#form_sp_opl_sppb').show();
				$('#form_faktur_pajak_sppb').show();
				$('#form_kwitansi_sppn').show();
				$('#form_referensi_sppn').show();
				$('#form_sp_opl_sppn').show();
				$('#form_faktur_pajak_sppn').show();
				document.getElementById("nama_diterima_sppn_input").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("alamat_diterima_sppn_input").className = "form-control validate_sppb validate_spp_all"; 
			} else {
				$('#panel_sppb').show();
				$('#panel_sppn').show();
				$('#panel_sppb_sppn').show();
				$('#diterima_sppn_input').show();
				$('#form_kwitansi_sppb').hide();
				$('#form_referensi_sppb').hide();
				$('#form_sp_opl_sppb').hide();
				$('#form_faktur_pajak_sppb').hide();
				$('#form_kwitansi_sppn').hide();
				$('#form_referensi_sppn').hide();
				$('#form_sp_opl_sppn').hide();
				$('#form_faktur_pajak_sppn').hide();
				document.getElementById("nama_diterima_sppn_input").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("alamat_diterima_sppn_input").className = "form-control validate_sppb validate_spp_all"; 
			}
			$('#panel_submit').show();
		});
	});

	$('#jenis_form').change(function(event){
		if ($("#jenis_form").val() == 'sppb') {
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
		else if($("#jenis_form").val() == 'sppn') {
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
        if (this.value == null || this.value == "" ) {
            this.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;";
        } else{
            this.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;";
        }
    }

    
    function validateForm(e){
		var hasEmpty = false;
        if ($("#jenis_form").val() == 'sppb') {
		var inputs = document.getElementsByClassName("validate_sppb");
		var slct_sppb = $('.selectpicker');
			console.log(slct_sppb);
		
			for(var i=0; i < slct_sppb.length; i++){
				console.log(slct_sppb[i].value);
				var a= slct_sppb[i].id;

				if(slct_sppb[i].value=="" || slct_sppb[i].value==null){
					if(a.includes('sppb')){
						var p_slct_sppb = document.getElementById(slct_sppb[i].id).parentElement;
						console.log(p_slct_sppb);
						p_slct_sppb.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;width:100%;";
						hasEmpty = true;
					}
					
				}
				else{
					if(a.includes('sppb')){
						var p_slct_sppb = document.getElementById(slct_sppb[i].id).parentElement;
						console.log(p_slct_sppb);
						p_slct_sppb.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;width:100%;";
					}		
				}
			}
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
		else if($("#jenis_form").val() == 'sppn') {
		var inputs = document.getElementsByClassName("validate_sppn");
		var slct_sppn = $('.selectpicker');
			console.log(slct_sppn);
		
			for(var i=0; i < slct_sppn.length; i++){
				console.log(slct_sppn[i].value);
				var a= slct_sppn[i].id;

				if(slct_sppn[i].value=="" || slct_sppn[i].value==null){
					if(a.includes('sppn')){
						var p_slct_sppn = document.getElementById(slct_sppn[i].id).parentElement;
						console.log(p_slct_sppn);
						p_slct_sppn.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;width:100%;";
						hasEmpty = true;
					}
					
				}
				else{
					if(a.includes('sppn')){
						var p_slct_sppn = document.getElementById(slct_sppn[i].id).parentElement;
						console.log(p_slct_sppn);
						p_slct_sppn.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;width:100%;";
					}		
				}
			}
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
			var slct_sppb = $('.selectpicker');
				console.log(slct_sppb);
			
				for(var i=0; i < slct_sppb.length; i++){
					console.log(slct_sppb[i].value);
					var a= slct_sppb[i].id;

					if(slct_sppb[i].value=="" || slct_sppb[i].value==null){
							var p_slct_sppb = document.getElementById(slct_sppb[i].id).parentElement;
							console.log(p_slct_sppb);
							p_slct_sppb.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;width:100%;";
							hasEmpty = true;
						
						
					}
					else{
							var p_slct_sppb = document.getElementById(slct_sppb[i].id).parentElement;
							console.log(p_slct_sppb);
							p_slct_sppb.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;width:100%;";
						
					}
				}
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
            if(inputs[i].value=="" || inputs[i].value==null) {
				console.log(inputs[i]);
				hasEmpty = true;
			}
				
			
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
	
	function simpan_spp() {
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
	$(document).ready(function(){
		$(".file").fileinput({
	        allowedFileTypes: ["image", "pdf"],
	        browseClass: "btn btn-primary btn-block",
	        maxFileSize: 55000,
	        showCaption: true,
	        showRemove: false,
	        showUpload: false,
	        showPreview: false,
	    });

		$(".file-multiple").fileinput({
	        // uploadUrl: '#',
	        allowedFileTypes: ["image", "pdf"],
	        browseClass: "btn btn-primary btn-block",
	        showCaption: false,
	        showRemove: false,
	        showUpload: false,
			dropZoneTitle:"Drag & drop banyak file sekaligus disini..",
	        fileActionSettings: {
				showRemove: true,
				showUpload: false,
			}
	    });
		$('.nominal').mask('0.000.000.000.000.000.000', {reverse: true});
	
	$('#tanggal_sppb').change(function(event){
			var tgl = $(this).val();
			$('#tanggal_sppn').val(tgl);
		});
	$('#tanggal_sppn').change(function(event){
			var tgl = $(this).val();
			$('#tanggal_sppb').val(tgl);
		});
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
			$('#kas_sppb_input').hide();
			$('#pilih_lampirkan_sppb').show();
			document.getElementById("nama_kas_sppb_input").className = "form-control";			
			document.getElementById("alamat_kas_sppb_input").className = "form-control";			

			pilih_data_sppb.call();
		} 
		else if($(this).val() == 'karyawan'){
			$('#kas_sppb_input').hide();
			$('#pilih_lampirkan_sppb').show();
			document.getElementById("nama_kas_sppb_input").className = "form-control";			
			document.getElementById("alamat_kas_sppb_input").className = "form-control";			
			pilih_data_sppb.call();
		}
		else if ($(this).val() == 'kas'){
			$('#pilih_lampirkan_sppb').hide();
			$('#bank_sppb_karyawan').hide();
			$('#kas_sppb_input').show();
			$('#bank_sppb').hide();
			document.getElementById("nama_bank_sppb_vendor").className = "form-control"; 			
			document.getElementById("rekening_bank_sppb_vendor").className = "form-control"; 			
			document.getElementById("alamat_bank_sppb_vendor").className = "form-control";			
			document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control"; 			
			document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
			document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
			document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
			document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
			document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
			document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
			document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
			document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
			document.getElementById("nama_kas_sppb_input").className = "form-control validate_sppb validate_spp_all";
			document.getElementById("alamat_kas_sppb_input").className = "form-control validate_sppb validate_spp_all";
			

		}
		else if($(this).val() == 'skbdn'){
			$('#pilih_lampirkan_sppb').show();
			document.getElementById("nama_kas_sppb_input").className = "form-control";			
			document.getElementById("alamat_kas_sppb_input").className = "form-control";			
			$('#kas_sppb_input').hide();

			pilih_data_sppb.call();
		}
		else{
			$('#pilih_lampirkan_sppb').hide();
			$('#kas_sppb_input').show();
			$('#bank_sppb_karyawan').hide();
			$('#bank_sppb').hide();

			document.getElementById("nama_bank_sppb_vendor").className = "form-control"; 			
			document.getElementById("alamat_bank_sppb_vendor").className = "form-control";			
			document.getElementById("rekening_bank_sppb_vendor").className = "form-control"; 			
			document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control"; 			
			document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
			document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
			document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
			document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
			document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
			document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
			document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
			document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
			document.getElementById("nama_kas_sppb_input").className = "form-control validate_sppb validate_spp_all";
			document.getElementById("alamat_kas_sppb_input").className = "form-control validate_sppb validate_spp_all";
			
		}
	});

	
	

	// $('#metode_pembayaran_sppn').change(function(event) {
	// 	if ($(this).val() == 'bank') {
	// 		$('#bank_sppn').show();
	// 		$('#bank_sppn_karyawan').hide();
	// 		$('#pilih_lampirkan_sppn').hide();

	// 		document.getElementById("nama_bank_sppn_vendor").className = "form-control validate_sppn validate_spp_all"; 			
	// 		document.getElementById("rekening_bank_sppn_vendor").className = "form-control validate_sppn validate_spp_all"; 			
	// 		document.getElementById("atas_nama_bank_sppn_vendor").className = "form-control validate_sppn validate_spp_all"; 			
	// 		document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control";
	// 		document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control";
	// 		document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control";
			
	// 		$('#atas_nama_vendor_sppn').show();
	// 			$('#atas_nama_karyawan_sppn').hide();
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
	// 		$('#pilih_lampirkan_sppn').hide();

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
});

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
				document.getElementById("alamat_bank_sppb_vendor").className = "form-control";			
				document.getElementById("rekening_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control validate_sppb validate_spp_all";
				document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control validate_sppb validate_spp_all";
				document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control validate_sppb validate_spp_all";
				document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control validate_sppb validate_spp_all";
				document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
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
				document.getElementById("alamat_bank_sppb_vendor").className = "form-control  validate_sppb validate_spp_all";			
				document.getElementById("rekening_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
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
				document.getElementById("alamat_bank_sppb_vendor").className = "form-control  validate_sppb validate_spp_all";			
				document.getElementById("rekening_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
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
				document.getElementById("alamat_bank_sppb_vendor").className = "form-control";
				document.getElementById("rekening_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control"; 			
				document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control ";
				document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control validate_sppb validate_spp_all";
				document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control validate_sppb validate_spp_all";
				document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control validate_sppb validate_spp_all";
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
				document.getElementById("alamat_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all";
				document.getElementById("rekening_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control ";
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
				document.getElementById("alamat_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all";
				document.getElementById("rekening_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control validate_sppb validate_spp_all"; 			
				document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
				document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
				document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control ";
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
			document.getElementById("alamat_bank_sppb_vendor").className = "form-control";
			document.getElementById("rekening_bank_sppb_vendor").className = "form-control"; 			
			document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control"; 			
			document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
			document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
			document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control ";
			document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
			document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
			document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
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
function pilih_data_sppn() {
		var radio_check_val = "";
		for (var i = 0; i < document.getElementsByName('pilih_data_sppn').length; i++){
			if(document.getElementsByName('pilih_data_sppn')[i].checked){
				radio_check_val = document.getElementsByName('pilih_data_sppn')[i].value;
			}
		}
		if(radio_check_val == "input_data"){
			$('#bank_sppn_karyawan').show();
			$('#bank_sppn').hide();
			document.getElementById("nama_bank_sppn_vendor").className = "form-control "; 			
			document.getElementById("rekening_bank_sppn_vendor").className = "form-control "; 			
			document.getElementById("atas_nama_bank_sppn_vendor").className = "form-control "; 			
			document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control validate_sppn validate_spp_all";
			document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control validate_sppn validate_spp_all";
			document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control validate_sppn validate_spp_all";
			var inputs = document.getElementsByClassName("validate_sppn");
			if(inputs){
				for(var i=0; i<inputs.length; i++){
					inputs[i].addEventListener("change",validateInput);
					inputs[i].addEventListener("focus",validateInput);
				}
        	}
		}
		else{
			$('#bank_sppn_karyawan').hide();
			$('#bank_sppn').hide();
			document.getElementById("nama_bank_sppn_vendor").className = "form-control "; 			
			document.getElementById("rekening_bank_sppn_vendor").className = "form-control "; 			
			document.getElementById("atas_nama_bank_sppn_vendor").className = "form-control "; 			
			document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control";
			document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control";
			document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control";
		}
}

		
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
													<input type="text" id="rekening_bank_sppb_karyawan_${index}" onclick="bank_karyawan_sppb(${index})" name="karyawan_sppb[${index}][no_rek]" class="form-control"  placeholder="Nomor Rekening Bank SPPb ${index}" autocomplete="off">
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

	// function tambah_karyawan_bank_sppn(index){
	// 	index++;
	// 	$('#bank_sppn_karyawan').append(`<div id="bank_karyawan_sppn_${index}">
	// 										<div class="form-group row" id="atas_nama_karyawan_bank_sppn_${index}">
	// 											<label class="col-sm-2 col-form-label">Atas Nama Rekening ${index}*</label>
	// 											<div class="col-sm-8">
	// 												<input type="text" id="atas_nama_bank_sppn_karyawan_${index}"  onclick="bank_karyawan_sppn(${index})" name="karyawan_sppn[${index}][nama]" class="form-control"  placeholder="Atas Nama Bank ${index}" autocomplete="off">						
	// 											</div>
	// 											<div class="col-sm-2" id="btn_karyawan_bank_sppn_${index}">
	// 													<button type="button" class="btn btn-success btn-sm" onclick="tambah_karyawan_bank_sppn(${index})">+</button>
	// 													<button type="button" class="btn btn-danger btn-sm" onclick="hapus_karyawan_bank_sppn(${index})">x</button>
	// 											</div>
	// 										</div>
	// 										<div class="form-group row">
	// 											<label class="col-sm-2 col-form-label">Nama Bank ${index}*</label>
	// 											<div class="col-sm-8">
	// 												<input type="text" id="nama_bank_sppn_karyawan_${index}" onclick="bank_karyawan_sppn(${index})" name="karyawan_sppn[${index}][bank]" class="form-control" placeholder="Nama Rekening Bank ${index}" autocomplete="off">												
	// 											</div>
	// 										</div>
	// 										<div class="form-group row">
	// 											<label class="col-sm-2 col-form-label">Nomor Rekening ${index}*</label>
	// 											<div class="col-sm-8">
	// 												<input type="text" id="rekening_bank_sppn_karyawan_${index}" onclick="bank_karyawan_sppn(${index})" name="karyawan_sppn[${index}][no_rek]" class="form-control" placeholder="Nomor Rekening Bank ${index}" autocomplete="off">
	// 											</div>
	// 										</div>
											
	// 										</div>`);
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
	// function pilih_karyawan_sppn(id,nama,namabank,norek){
	// 	//window.alert(namabank);
	// 	if(namabank == ""){
	// 		$('#'+nama_bank_sppn_karyawan_id).val("");
	// 		document.getElementById(nama_bank_sppn_karyawan_id).onclick = function(){ };
	// 	}
	// 	else{
	// 		$('#'+nama_bank_sppn_karyawan_id).val(namabank);
	// 		document.getElementById(nama_bank_sppn_karyawan_id).onclick = function() { bank_karyawan_sppn(index_bank_sppn_karyawan); }
	// 	}
	// 	if(norek == ""){
	// 		$('#'+rekening_bank_sppn_karyawan_id).val("");
	// 		document.getElementById(rekening_bank_sppn_karyawan_id).onclick = function(){ };
	// 	}
	// 	else{
	// 		$('#'+rekening_bank_sppn_karyawan_id).val(norek);
	// 		document.getElementById(rekening_bank_sppn_karyawan_id).onclick = function() { bank_karyawan_sppn(index_bank_sppn_karyawan); }

	// 	}
	// 	$('#'+atas_nama_bank_sppn_karyawan_id).val(nama);
	// 	$('#modal_karyawan_sppn').modal('hide');
	// }

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
	function kode_customer_sppb(isi){
		$('#modal_customer_sppb').modal('show');
		gl_sppb_id = 'sap_customer_sppb_'+isi;
		gl_sppb_id_id = 'sap_customer_sppb_id_'+isi;
	}
	function kode_customer_sppn(isi){
		$('#modal_customer_sppn').modal('show');
		gl_sppn_id = 'sap_customer_sppn_'+isi;
		gl_sppn_id_id = 'sap_customer_sppn_id_'+isi;
	}
	function kode_gl_sppb(isi){
		$('#modal_gl_sppb').modal('show');
		gl_sppb_id = 'sap_gl_sppb_'+isi;
		gl_sppb_id_id = 'sap_gl_sppb_id_'+isi;
	}
	function kode_gl_sppn(isi){
		$('#modal_gl_sppn').modal('show');
		gl_sppn_id = 'sap_gl_sppn_'+isi;
		gl_sppn_id_id = 'sap_gl_sppn_id_'+isi;
	}
	function pilih_gl_sppb(id, kode, keterangan){
		var rek = kode+' ('+keterangan+')';
		document.getElementById(gl_sppb_id).value = rek;
		document.getElementById(gl_sppb_id).focus();
		$('#'+gl_sppb_id_id).val(id);
		$('#modal_gl_sppb').modal('hide');
	}
	function pilih_gl_sppn(id, kode, keterangan){
		var rek = kode+' ('+keterangan+')';
		document.getElementById(gl_sppn_id).value = rek;
		document.getElementById(gl_sppn_id).focus();
		$('#'+gl_sppn_id_id).val(id);
		$('#modal_gl_sppn').modal('hide');
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
	// function pilih_rekening_sppb(id, kbb, sap, keterangan){
	// 	var rek = kbb+' / '+sap+' ('+keterangan+')';
	// 	document.getElementById(rekening_sppb_id).value = rek;
	// 	document.getElementById(rekening_sppb_id).focus();
	// 	$('#'+rekening_sppb_id_id).val(id);
	// 	$('#modal_rekening_sppb').modal('hide');
	// }

	// function pilih_rekening_sppn(id, kbb, sap, keterangan){
	// 	document.getElementById(rekening_sppn_id).value = kbb+' / '+sap+' ('+keterangan+')';
	// 	document.getElementById(rekening_sppn_id).focus();
	// 	$('#'+rekening_sppn_id_id).val(id);
	// 	$('#modal_rekening_sppn').modal('hide');
	// }

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
			document.getElementById("sap_gl_sppb_"+index).className = "form-control"; 			
			document.getElementById("sap_vendor_sppb_"+index).className = "selectpicker";
			document.getElementById("sap_customer_sppb_"+index).className = "form-control";
			var inputs = document.getElementsByClassName("validate_sppb");
			if(inputs){
				for(var i=0; i<inputs.length; i++){
					inputs[i].addEventListener("change",validateInput);
					inputs[i].addEventListener("keyup",validateInput);
				}
        	}
			$('#nomor_vendor_sppb_'+index).show();
			$('#nomor_gl_sppb_'+index).hide();
			$('#nomor_customer_sppb_'+index).hide();
			
		}else if (pilihan == 'customer') {
			document.getElementById("sap_gl_sppb_"+index).className = "form-control"; 			
			document.getElementById("sap_customer_sppb_"+index).className = "selectpicker";
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
			
		} else {
			document.getElementById("sap_gl_sppb_"+index).className = "selectpicker"; 			
			document.getElementById("sap_customer_sppb_"+index).className = "form-control";
			document.getElementById("sap_vendor_sppb_"+index).className = "form-control"; 
			var inputs = document.getElementsByClassName("validate_sppb");
			if(inputs){
				for(var i=0; i<inputs.length; i++){
					inputs[i].addEventListener("change",validateInput);
					inputs[i].addEventListener("keyup",validateInput);
				}
        	}
			$('#nomor_customer_sppb_'+index).hide();
			$('#nomor_vendor_sppb_'+index).hide();
			$('#nomor_gl_sppb_'+index).show();

			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			$('#sap_gl_sppb_'+index).on('change', function () {
			//ways to retrieve selected option and text outside handler
				var budeget = $(this).find(':selected').data('budget_'+index);
				console.log(budeget);
				var gl_id = $(this).find(':selected').val();
				var reverse = budeget.toString().split('').reverse().join(''),
				ribuan = reverse.match(/\d{1,3}/g);
				ribuan = ribuan.join('.').split('').reverse().join('');
				$('.budget_gl_'+index).val('Rp. ' +ribuan);  
				$('.budget_gl_hide_'+index).val(budeget);
				$.ajax({
					type:'POST',
					url:"{{ route('realisasi') }}",
					data:{gl_id:gl_id},
					success:function(data){
							realisasi = parseInt(data.realisasisppn) +parseInt(data.realisasi);
							var reverse1 = realisasi.toString().split('').reverse().join(''),
							ribuan1 = reverse1.match(/\d{1,3}/g);
							ribuan1 = ribuan1.join('.').split('').reverse().join('');
							$('.realisasi_'+index).val('Rp. '+ribuan1); 

							onproses = parseInt(data.onproses) +parseInt(data.onprosessppn);
							var reverse2 = onproses.toString().split('').reverse().join(''),
							ribuan2 = reverse2.match(/\d{1,3}/g);
							ribuan2 = ribuan2.join('.').split('').reverse().join('');
							$('.onproses_'+index).val('Rp. '+ribuan2); 

							sisa[index] = budeget -  data.realisasi - data.onproses - data.realisasisppn - data.onprosessppn;
							var reverse3 = sisa[index].toString().split('').reverse().join(''),
							ribuan3 = reverse3.match(/\d{1,3}/g);
							ribuan3 = ribuan3.join('.').split('').reverse().join('');
							$('.sisa_'+index).val('Rp. '+ribuan3); 
					}
					});
			});
		}
	}

	function js_sppn(index){
		// console.log(index);
		var pilihan = $('#jenis_sap_sppn_'+index).val();
		if (pilihan == 'vendor') {
			document.getElementById("sap_gl_sppn_"+index).className = "form-control"; 			
			document.getElementById("sap_vendor_sppn_"+index).className = "selectpicker"; 	
			document.getElementById("sap_customer_sppn_"+index).className = "form-control";

			var inputs = document.getElementsByClassName("validate_sppn");
			if(inputs){
				for(var i=0; i<inputs.length; i++){
					inputs[i].addEventListener("change",validateInput);
					inputs[i].addEventListener("keyup",validateInput);
				}
        	}
			$('#nomor_vendor_sppn_'+index).show();
			$('#nomor_customer_sppn_'+index).hide();
			$('#nomor_gl_sppn_'+index).hide();
			
		}else if (pilihan == 'customer') {
			document.getElementById("sap_gl_sppn_"+index).className = "form-control"; 			
			document.getElementById("sap_customer_sppn_"+index).className = "selectpicker";
			document.getElementById("sap_vendor_sppn_"+index).className = "form-control"; 
			var inputs = document.getElementsByClassName("validate_sppn");
			if(inputs){
				for(var i=0; i<inputs.length; i++){
					inputs[i].addEventListener("change",validateInput);
					inputs[i].addEventListener("keyup",validateInput);
				}
        	}
			$('#nomor_customer_sppn_'+index).show();
			$('#nomor_gl_sppn_'+index).hide();
			$('#nomor_vendor_sppn_'+index).hide();
			
		}  else {
			document.getElementById("sap_gl_sppn_"+index).className = "selectpicker"; 			
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

			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
				$('#sap_gl_sppn_'+index).on('change', function () {
			//ways to retrieve selected option and text outside handler
				var budeget = $(this).find(':selected').data('budgetsppn_'+index);
				var gl_id = $(this).find(':selected').val();
				var reverse = budeget.toString().split('').reverse().join(''),
				ribuan = reverse.match(/\d{1,3}/g);
				ribuan = ribuan.join('.').split('').reverse().join('');
				$('.budget_glsppn_'+index).val('Rp. ' +ribuan); 
				console.log(ribuan); 
				$('.budget_glsppn_hide'+index).val(budeget);
				$.ajax({
					type:'POST',
					url:"{{ route('realisasi') }}",
					data:{gl_id:gl_id},
					success:function(data){
							
							realisasi = parseInt(data.realisasisppn) +parseInt(data.realisasi);
							var reverse1 = realisasi.toString().split('').reverse().join(''),
							ribuan1 = reverse1.match(/\d{1,3}/g);
							ribuan1 = ribuan1.join('.').split('').reverse().join('');
							$('.realisasisppn_'+index).val('Rp. '+ribuan1); 

							onproses = parseInt(data.onproses) +parseInt(data.onprosessppn);
							var reverse2 = onproses.toString().split('').reverse().join(''),
							ribuan2 = reverse2.match(/\d{1,3}/g);
							ribuan2 = ribuan2.join('.').split('').reverse().join('');
							$('.onprosessppn_'+index).val('Rp. '+ribuan2); 

							sisa[index] = budeget - data.realisasisppn - data.onprosessppn - data.realisasi - data.onproses;
							var reverse3 = sisa[index].toString().split('').reverse().join(''),
							ribuan3 = reverse3.match(/\d{1,3}/g);
							ribuan3 = ribuan3.join('.').split('').reverse().join('');
							$('.sisasppn_'+index).val('Rp. '+ribuan3); 
					}
					});
			});
		}
	}

	function tambah_faktur_pajak_spp(index){
		index++;
		$('#fp_spp').append(`<div id="faktur_pajak_spp_${index}">
									<div class="form-group row">
										<label class="col-sm-2 col-form-label"></label>
										<div class="col-sm-8">
											<input type="text" id="faktur_pajak_spp" name="faktur_pajak_spp[${index}][fp]" class="form-control validate_spp_all" placeholder="Faktur Pajak ${index}" autocomplete="off">
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
											<input type="text" id="faktur_pajak_spp" name="faktur_pajak_sppb[${index}][fp]" class="form-control validate_spp_all" placeholder="Nomor Faktur Pajak SPPb ${index}" autocomplete="off">
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
											<input type="text" id="faktur_pajak_spp" name="faktur_pajak_sppn[${index}][fp]" class="form-control validate_spp_all" placeholder="Nomor Faktur Pajak SPPn ${index}" autocomplete="off">
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

	function tambah_isi_sppb(){
		index_sppb++;
		// index = index_sppb-1;
		sub_index_sppb[index_sppb] = 1;
		$('#tab-isi-sppb').append(`
										<div id="isi_sppb_${index_sppb}" class="col-sm-12">
											<div  style="padding-bottom: 10px; margin-bottom: 20px; border-bottom: 1px solid #eaeaea;">
												<font size="4" style="margin-right: 20px">Isi ${index_sppb}. </font>
												<button type="button" class="btn btn-info btn-sm" onclick="tambah_isi_sppb()">+</button>
												<button type="button" class="btn btn-danger btn-sm" onclick="hapus_isi_sppb(${index_sppb},'ckeditor_${index_sppb}_1')">x</button>
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
															<input type="text" style="display:none;" id="sap_customer_sppb_id_${index_sppb}" name="isi_sppb[${index_sppb}][customer]" class="form-control" onclick="kode_customer_sppb(${index_sppb})" placeholder="Kode SAP (Nomor Vendor)" autocomplete="off" required>
														</div>
													</div>
													<div id="nomor_gl_sppb_${index_sppb}" style="display:none">
														<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppb_${index_sppb}" >
																<select class="selectpicker slct_sppb"  data-live-search="true" data-dropup-auto="false" id="sap_gl_sppb_${index_sppb}" data-size="7" data-width="100%" name="isi_sppb_rekening" onchange="pilih_rekening_sppb(${index_sppb},'sap_gl_sppb_')">
																	<option value="" disabled selected>-- Pilih Kode GL --</option>
																	@foreach($gl as $r)
																	<option value="{{$r->master_gl_id}}" data-budget_${index_sppb}="{{$r->jumlah_budget}}" data-budget_${index_sppb}="{{$r->master_gl_id}}">{{$r->master_gl_kode}} ({{$r->master_gl_keterangan}})</option>
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
											<div id="sub_isi_sppb_1_1">
												<div class="col-md-6">
													<div class="form-group row">
														<label class="col-sm-1 col-form-label">1. </label>
														<label class="col-sm-2 col-form-label">Uraian*</label>
														<div class="col-sm-9">
															<div style="border: 1px solid hsla(0, 0%, 0%, 0.15);">
																<div id="uraian_sppb_${index_sppb}_1" style="height:auto;min-height:100px">
																	<textarea class="form-control" id="ckeditor_${index_sppb}_1" name="uraian_sppb[${index_sppb}][1][ket]"></textarea>
																</div>
															</div>
														</div>
													</div>
													<div class="form-group row">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">Nominal*</label>
														<div class="col-sm-9">
															<input type="text" id="jumlah_sppb_${index_sppb}_1" name="uraian_sppb[${index_sppb}][1][jumlah]" class="form-control nominal validate_sppb validate_sppb_all" placeholder="Nominal SPPb" autocomplete="off" required>
															
														</div>
													</div>
												</div>

												<div class="col-sm-1">
													<div class="col-sm-12" style="margin-bottom: 10px">
														<button type="button" class="btn btn-success btn-sm" onclick="tambah_sub_isi_sppb(${index_sppb})">+</button>
													</div>
												</div>
											</div>
										</div>`);

        CKEDITOR.inline( 'ckeditor_'+index_sppb+'_1');
	
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
			$( "#jumlah_sppb_"+index_sppb+"_1").on('keyup',function(e) {
			console.log("Isi - #jumlah_sppb_"+index_sppb+"_1");
			jum_nom[index_sppb] = [];
			jum_nom[index_sppb][1] = this.value.replace(/[^\d,]/g, "");
			var jum_nom_total = 0;
			for (let i = 1; i <= sub_index_sppb[index_sppb]; i++) {
				var jum_nom_value = $("#jumlah_sppb_"+index_sppb+"_"+i) ? $("#jumlah_sppb_"+index_sppb+"_"+i).val().replace(/[^\d,]/g, "") : 0;
				jum_nom_total = jum_nom_value ? parseInt(jum_nom_total) + parseInt(jum_nom_value) : jum_nom_total;
				console.log(jum_nom_total);
			}
			if (jum_nom_total > sisa[index_sppb]) {
				$('.cek_dana_gagal_'+index_sppb+'_1').css('display','block');
				$('.cek_dana_berhasil_'+index_sppb+'_1').css('display','none');
				// $("#simpan").prop("disabled", true);
			}else{
				$('.cek_dana_berhasil_'+index_sppb+'_1').css('display','block');
				$('.cek_dana_gagal_'+index_sppb+'_1').css('display','none');
				// $("#simpan").prop("disabled", false);
			}
		});
		$('.selectpicker').selectpicker();
	}

	function hapus_isi_sppb(isi,instance){
		$("#isi_sppb_"+isi).remove();
		CKEDITOR.instances[instance].destroy();
	}

	function tambah_sub_isi_sppb(isi){
		sub_index_sppb[isi]++;
		$('#isi_sppb_'+isi).append(`<div id="sub_isi_sppb_${isi}_${sub_index_sppb[isi]}">
								<div class="col-sm-5"></div>
								<div class="col-md-6">
									<div class="form-group row">
										<label class="col-sm-1 col-form-label">${sub_index_sppb[isi]}. </label>
										<label class="col-sm-2 col-form-label">Uraian*</label>
										<div class="col-sm-9">
											<div style="border: 1px solid hsla(0, 0%, 0%, 0.15);">
												<div id="uraian_sppb_${isi}_${sub_index_sppb[isi]}"style="height:auto;min-height:100px">
												<textarea class="form-control" id="ckeditor_${isi}_${sub_index_sppb[isi]}" name="uraian_sppb[${isi}][${sub_index_sppb[isi]}][ket]"></textarea>
												</div>
												</div>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-1 col-form-label"></label>
										<label class="col-sm-2 col-form-label">Nominal*</label>
										<div class="col-sm-9">
											<input type="text" id="jumlah_sppb_${isi}_${sub_index_sppb[isi]}" name="uraian_sppb[${isi}][${sub_index_sppb[isi]}][jumlah]" class="form-control nominal validate_sppb validate_spp_all" placeholder="Nominal sppb" autocomplete="off" required>
										
										</div>
									</div>
								</div>
								<div class="col-sm-1">
									<div class="col-sm-12" id="hapus_sub_isi_sppb_${isi}_${sub_index_sppb[isi]}" onclick="hapus_sub_isi_sppb(${isi},${sub_index_sppb[isi]},'ckeditor_${isi}_${sub_index_sppb[isi]}')" style="margin-bottom: 10px">
										<button class="btn btn-danger btn-sm">X</button>
									</div>
									<div class="col-sm-12" id="tambah_sub_isi_sppb_${isi}_${sub_index_sppb[isi]}" onclick="tambah_sub_isi_sppb(${isi})" style="margin-bottom: 10px">
										<button class="btn btn-success btn-sm">+</button>
									</div>
								</div>
							</div>`);
						CKEDITOR.inline('ckeditor_'+isi+'_'+sub_index_sppb[isi]);

						$('.nominal').mask('0.000.000.000.000.000.000', {reverse: true}
						);
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
						})
							}
						}
						var sub = sub_index_sppb[isi];
						$( "#jumlah_sppb_"+isi+"_"+sub).on('keyup',function(e) {
							console.log("Sub Isi - #jumlah_sppb_"+isi+"_"+sub);
							jum_nom[isi][sub] = this.value.replace(/[^\d,]/g, "");
							var jum_nom_total = 0;
							for (let i = 1; i <= sub_index_sppb[isi]; i++) {
								var jum_nom_value = $("#jumlah_sppb_"+isi+"_"+i) ? $("#jumlah_sppb_"+isi+"_"+i).val().replace(/[^\d,]/g, "") : 0;
								jum_nom_total = jum_nom_value ? parseInt(jum_nom_total) + parseInt(jum_nom_value) : jum_nom_total;
								console.log(jum_nom_total);
							}
							if (jum_nom_total > sisa[isi]) {
								$('.cek_dana_gagal_'+isi+'_'+sub).css('display','block');
								$('.cek_dana_berhasil_'+isi+'_'+sub).css('display','none');
								// $("#simpan").prop("disabled", true);
							}else{
								$('.cek_dana_berhasil_'+isi+'_'+sub).css('display','block');
								$('.cek_dana_gagal_'+isi+'_'+sub).css('display','none');
								// $("#simpan").prop("disabled", false);
							}
						});
	}

	function hapus_sub_isi_sppb(isi, sub_isi,instance){
		$('#sub_isi_sppb_'+isi+'_'+sub_isi).remove();
		CKEDITOR.instances[instance].destroy();
	}

	function tambah_isi_sppn(){
		index_sppn++;
		sub_index_sppn[index_sppn] = 1;
		$('#tab-isi-sppn').append(`<div id="isi_sppn_${index_sppn}" class="col-sm-12">
					<div  style="padding-bottom: 10px; margin-bottom: 20px; border-bottom: 1px solid #eaeaea;">
						<font size="4" style="margin-right: 20px">Isi ${index_sppn}. </font>
						<button class="btn btn-info btn-sm" onclick="tambah_isi_sppn()">+</button>
						<button class="btn btn-danger btn-sm" onclick="hapus_isi_sppn(${index_sppn},'ckeditors_${index_sppn}_1')">x</button>
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
																	<option value="{{$r->master_gl_id}}" data-budgetsppn_${index_sppn}="{{$r->jumlah_budget}}" data-budgetsppn_${index_sppn}="{{$r->master_gl_id}}">{{$r->master_gl_kode}} ({{$r->master_gl_keterangan}})</option>
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
										<div id="sub_isi_sppn_1_1">
											<div class="col-md-6">
												<div class="form-group row">
													<label class="col-sm-1 col-form-label">1. </label>
													<label class="col-sm-2 col-form-label">Uraian*</label>
													<div class="col-sm-9">
														<div style="border: 1px solid hsla(0, 0%, 0%, 0.15);">
															<div id="uraian_sppn_${index_sppn}_1" style="height:auto;min-height:100px">
															<textarea class="form-control" id="ckeditors_${index_sppn}_1" name="uraian_sppn[${index_sppn}][1][ket]" ></textarea>
															</div>
														</div>
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-1 col-form-label"></label>
													<label class="col-sm-2 col-form-label">Nominal*</label>
													<div class="col-sm-9">
														<input type="text" id="jumlah_sppn_${index_sppn}_1" name="uraian_sppn[${index_sppn}][1][jumlah]" class="form-control nominal validate_sppn validate_spp_all" placeholder="Nominal SPPn" autocomplete="off" required>
													
													</div>
												</div>
											</div>
											<div class="col-sm-1">
												<div class="col-sm-12" style="margin-bottom: 10px">
													<button class="btn btn-success btn-sm" onclick="tambah_sub_isi_sppn(${index_sppn})">+</button>
												</div>
											</div>
										</div>
									</div>`);
				CKEDITOR.inline( 'ckeditors_'+index_sppn+'_1');

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
				$('.nominal').mask('0.000.000.000.000.000.000', {reverse: true});
				$( "#jumlah_sppn_"+index_sppn+"_1").on('keyup',function(e) {
				console.log("Isi - #jumlah_sppn_"+index_sppn+"_1");
				jum_nom[index_sppn] = [];
				jum_nom[index_sppn][1] = this.value.replace(/[^\d,]/g, "");
				var jum_nom_total = 0;
				for (let i = 1; i <= sub_index_sppn[index_sppn]; i++) {
					var jum_nom_value = $("#jumlah_sppn_"+index_sppn+"_"+i) ? $("#jumlah_sppn_"+index_sppn+"_"+i).val().replace(/[^\d,]/g, "") : 0;
					jum_nom_total = jum_nom_value ? parseInt(jum_nom_total) + parseInt(jum_nom_value) : jum_nom_total;
					console.log(jum_nom_total);
				}
				if (jum_nom_total > sisa[index_sppn]) {
					$('.sppncek_dana_gagal_'+index_sppn+'_1').css('display','block');
					$('.sppncek_dana_berhasil_'+index_sppn+'_1').css('display','none');
					// $("#simpan").prop("disabled", true);
				}else{
					$('.sppncek_dana_berhasil_'+index_sppn+'_1').css('display','block');
					$('.sppncek_dana_gagal_'+index_sppn+'_1').css('display','none');
					// $("#simpan").prop("disabled", false);
				}
			});
		$('.selectpicker').selectpicker();
				// );
	}

	function hapus_isi_sppn(isi,instance){
		$("#isi_sppn_"+isi).remove();
		CKEDITOR.instances[instance].destroy();
	}

	function tambah_sub_isi_sppn(isi){
		sub_index_sppn[isi]++;
		$('#isi_sppn_'+isi).append(`<div id="sub_isi_sppn_${isi}_${sub_index_sppn[isi]}">
								<div class="col-sm-5"></div>
								<div class="col-md-6">
									<div class="form-group row">
										<label class="col-sm-1 col-form-label">${sub_index_sppn[isi]}. </label>
										<label class="col-sm-2 col-form-label">Uraian*</label>
										<div class="col-sm-9">
											<div style="border: 1px solid hsla(0, 0%, 0%, 0.15);">
												<div id="uraian_sppn_${isi}_${sub_index_sppn[isi]}" style="height:auto;min-height:100px">
												<textarea class="form-control" id="ckeditors_${isi}_${sub_index_sppn[isi]}" name="uraian_sppn[${isi}][${sub_index_sppn[isi]}][ket]"></textarea>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-1 col-form-label"></label>
										<label class="col-sm-2 col-form-label">Nominal*</label>
										<div class="col-sm-9">
											<input type="text" id="jumlah_sppn_${isi}_${sub_index_sppn[isi]}" name="uraian_sppn[${isi}][${sub_index_sppn[isi]}][jumlah]" class="form-control nominal validate_sppn validate_spp_all" placeholder="Nominal SPPn" autocomplete="off" required>
										
										</div>
									</div>
								</div>
								<div class="col-sm-1">
									<div class="col-sm-12" id="hapus_sub_isi_sppn_${isi}_${sub_index_sppn[isi]}" onclick="hapus_sub_isi_sppn(${isi},${sub_index_sppn[isi]},'ckeditors_${isi}_${sub_index_sppn[isi]}')" style="margin-bottom: 10px">
										<button class="btn btn-danger btn-sm">X</button>
									</div>
									<div class="col-sm-12" id="tambah_sub_isi_sppn_${isi}_${sub_index_sppn[isi]}" onclick="tambah_sub_isi_sppn(${isi})" style="margin-bottom: 10px">
										<button class="btn btn-success btn-sm">+</button>
									</div>
								</div>
							</div>`);
						
						CKEDITOR.inline('ckeditors_'+isi+'_'+sub_index_sppn[isi]);
						
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

						$('.nominal').mask('0.000.000.000.000.000.000', {reverse: true});
						var sub = sub_index_sppn[isi];
						$( "#jumlah_sppn_"+isi+"_"+sub).on('keyup',function(e) {
							console.log("Sub Isi - #jumlah_sppn_"+isi+"_"+sub);
							jum_nom[isi][sub] = this.value.replace(/[^\d,]/g, "");
							var jum_nom_total = 0;
							for (let i = 1; i <= sub_index_sppn[isi]; i++) {
								var jum_nom_value = $("#jumlah_sppn_"+isi+"_"+i) ? $("#jumlah_sppn_"+isi+"_"+i).val().replace(/[^\d,]/g, "") : 0;
								jum_nom_total = jum_nom_value ? parseInt(jum_nom_total) + parseInt(jum_nom_value) : jum_nom_total;
								console.log(jum_nom_total);
							}
							if (jum_nom_total > sisa[isi]) {
								$('.sppncek_dana_gagal_'+isi+'_'+sub).css('display','block');
								$('.sppncek_dana_berhasil_'+isi+'_'+sub).css('display','none');
								// $("#simpan").prop("disabled", true);
							}else{
								$('.sppncek_dana_berhasil_'+isi+'_'+sub).css('display','block');
								$('.sppncek_dana_gagal_'+isi+'_'+sub).css('display','none');
								// $("#simpan").prop("disabled", false);
							}
						});
	}

	function hapus_sub_isi_sppn(isi, sub_isi, instance){
		$('#sub_isi_sppn_'+isi+'_'+sub_isi).remove();
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