@extends('template.master')
@section('title', 'SPP | Validasi')
@section('header')
<link rel="stylesheet" href="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/css/fileinput.min.css">
@endsection
@section('konten')

<!-- MAIN -->
<div class="main">
	<!-- MAIN CONTENT -->
	<div class="main-content">
		<div class="container-fluid">
			<h3 class="page-title">Validasi SPP</h3>
			<div class="row">
				<div class="col-md-12">
					<!-- Validasi SPP -->
					<div class="panel">
						<div class="panel-heading">
							<h3 class="panel-title">Validasi SPP</h3>
						</div>
						<input type="hidden" id="formspp" value="{{$formspp}}">
						<div class="panel-body">
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">Jenis SPP</label>
								<div class="col-sm-10">
								@if(isset($sppb['sppb_jenis']))
								<p>{{$sppb['sppb_jenis']}} </p>
								@else
								<p>{{$sppn['sppn_jenis']}} </p>
								@endif
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">Dokumen SPP</label>
								<div class="col-sm-10">
									<a name="previewvalidasi" href="{{url('user/validasi_spp/preview/'.$id_validasi)}}" target="_blank">Preview</a>
							
								</div>
							</div>
						</div>
					</div>
					@if(isset($sppb) && $sppb !== [])
						<!-- DETAIL SPPB -->
					<div class="panel" id="panel_sppb"  style="display: none">	
						<div class="panel-heading">
							SPPb
						</div>
						<div class="panel-body">
							<table class="table-bordered striped" style="width: 100%">
								<thead>
									<tr>
										<th>Nomor SPP</th>
										<th style="display:none;">Id</th>
										<th>Tanggal</th>
										<th>Dari</th>
										<th>Kepada</th>
										<th>Perihal</th>
										<th>Jumlah</th>
										<th>Lampiran</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>{{$sppb['sppb_no']}}</td>
										<td style="display:none;">{{$sppb['sppb_no']}}</td>
										<td>{{date('d-m-Y',strtotime($sppb['sppb_tanggal']))}}</td>
										<td>{{$sppb['master_bagian_nama']}}</td>
										<td>Kepala Bag. Keuangan dan Akuntansi</td>
										<td>
											@for($i=0;$i< count($sppb[0]); $i++)
												@for($a=0;$a< count($sppb[0][$i][0]); $a++)
													{!! $sppb[0][$i][0][$a]->sppb_uraian_uraian !!}
												@endfor
											@endfor
										</td>
										<td>Rp. {{number_format($sppb['sppb_total'])}}</td>
										<td>
										@if(isset($dokpensppb))
											@foreach($dokpensppb as $d)
											<a href="{{ asset('dokumen/'.$d->dokumen_pendukung_sppb_nama)}}" target="_blank">{{$d->dokumen_pendukung_sppb_nama}}</a><br></br>  
											@endforeach
										@else
										-
										@endif
										</td>
									</tr>
								</tbody>
							</table>
						
						</div>
					</div>
						@endif
						
						<!-- END DETAIL SPPB -->
					@if(isset($sppn) && $sppn !== [])
						<!-- DETAIL SPPN -->
					<div class="panel" id="panel_sppn"  style="display: none">
						<div class="panel-heading">
							SPPn
						</div>
						<div class="panel-body">
						<table class="table-bordered striped"  style="width: 100%">
								<thead>
									<tr>
										<th>Nomor SPP</th>
										<th style="display:none;">Id</th>
										<th>Tanggal</th>
										<th>Dari</th>
										<th>Kepada</th>
										<th>Perihal</th>
										<th>Jumlah</th>
										<th>Lampiran</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>{{$sppn['sppn_no']}}</td>
										<td style="display:none;">{{$sppn['sppn_no']}}</td>
										<td>{{date('d-m-Y',strtotime($sppn['sppn_tanggal']))}}</td>
										<td>{{$sppn['master_bagian_nama']}}</td>
										<td>Kepala Bag. Keuangan dan Akuntansi</td>
										<td>
											@for($i=0;$i< count($sppn[0]); $i++)
												@for($a=0;$a< count($sppn[0][$i][0]); $a++)
													{!! $sppn[0][$i][0][$a]->sppn_uraian_uraian !!}
												@endfor
											@endfor
										</td>
										<td>Rp. {{number_format($sppn['sppn_jumlah'])}}</td>
										<td>
										@if(isset($dokpensppn))
											@foreach($dokpensppn as $d)
											<a href="{{ asset('dokumen/'.$d->dokumen_pendukung_sppn_nama)}}" target="_blank">{{$d->dokumen_pendukung_sppn_nama}}</a><br></br>  
											@endforeach
										@else
										-
										@endif
										</td>
									</tr>
								</tbody>
							</table>
						
						</div>
						
					</div>
					@endif
				
					<?php 
					$hakakses = Session::get('hak_akses');
					$level = Session::get('level');
					?>
					
				</div>
			</div>
		</div>
	<!-- END MAIN CONTENT -->
</div>
<!-- END MAIN -->


{{-- Modal KIRIM --}}
<div id="modal_kirim" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <form action="" id="form-kirim" method="post" enctype="multipart/form-data">
      {{csrf_field()}}
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Kirim SPP</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Upload File SPP yang sudah di TTD Kepala Bagian :</label><br>
            <input type="file" id="spp_kabag" name="spp_kabag" class="file" accept="application/pdf, image/*" placeholder="SPP tanda tangan Kabag" autocomplete="off">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
{{-- End Modal KIRIM --}}


{{-- Modal REVISI --}}
<div id="modal_revisi" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<form action="{{ url('spp') }}" method="get">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Revisi SPP</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Masukkan Keterangan Revisi SPP :</label><br>
						<textarea class="form-control" name="" placeholder="Keterangan Revisi" required></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success">Submit</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
{{-- End Modal REVISI --}}

<!-- Javascript -->
<script type="text/javascript">
	$(document).ready(function() {
		$(".file").fileinput({
			allowedFileTypes: ["image", "pdf"],
			browseClass: "btn btn-primary btn-block",
			showCaption: true,
			showRemove: false,
			showUpload: false,
			showPreview: false,
		});
	});
	$(document).ready(function() {
		var formspp = $("#formspp").val();
		
		if(formspp==2){
			$('#panel_sppn').show();
		
		}
		else if(formspp==1){
			$('#panel_sppb').show();
			
		}
		else {
			$('#panel_sppb').show();
			$('#panel_sppn').show();
			
		}

	});
	
	function revisi(id){
    	$("#modal_revisi").modal('show');
    	$("#form-revisi").attr('action', '{{url('')}}/spp/revisi/'+ id);
  	}

	function upload_kirim(id){
		$("#modal_kirim").modal('show');
		$("#form-kirim").attr('action', '{{url('')}}/spp/upload/'+ id);
	}

	function terima(id){
		Swal.fire({
		title: 'Apakah Anda Yakin sudah menerima SPP?',
		text: "Terima Data SPP!",
		icon: 'info',
		showCancelButton: true,
		confirmButtonColor: '#41B314',
		cancelButtonColor: '#F9354C',
		confirmButtonText: 'Terima SPP'
		}).then((result) => {
		if (result.isConfirmed) {
			window.location.href = `{{url('')}}/spp/accept/`+id;
			Swal.fire(
			'Terima SPP',
			'SPP berhasil anda Terima.',
			'success'
			)
		}
		})
	}
	<?php
	$level = Session::get('level');
	?>
	function kirim(id){
		Swal.fire({
		title: 'Apakah Anda Yakin?',
		text: "Kirim Data SPP!",
		icon: 'info',
		showCancelButton: true,
		confirmButtonColor: '#41B314',
		cancelButtonColor: '#F9354C',
		confirmButtonText: 'Kirim SPP'
		}).then((result) => {
		if (result.isConfirmed) {
		window.location.href = `{{url('')}}/spp/send/`+id;
			Swal.fire(
			'Kirim SPP',
			'SPP berhasil anda Kirim.',
			'success',
			)
		$("#respon").hide();
		}
		})
	}

	$('#rekening_sppb').click(function(event) {
		$('#modal_rekening_sppb').modal('show');
	});

	$('#rekening_sppn').click(function(event) {
		$('#modal_rekening_sppn').modal('show');
	});

	function pilih_rekening_sppb(kbb, sap, keterangan){
		$('#rekening_sppb').val(kbb+' / '+sap+' ('+keterangan+')');
		$('#modal_rekening_sppb').modal('hide');
	}

	function pilih_rekening_sppn(kbb, sap, keterangan){
		$('#rekening_sppn').val(kbb+' / '+sap+' ('+keterangan+')');
		$('#modal_rekening_sppn').modal('hide');
	}

	function hapus_dokumen_tambahan(id,nama){
		Swal.fire({
		title: 'Apakah Anda Yakin?',
		text: "Menghapus Dokumen " +nama+ " !",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d33',
		cancelButtonColor: '#3085d6',
		confirmButtonText: 'Hapus!'
		}).then((result) => {
		if (result.isConfirmed) {
			window.location.href = `{{url('')}}/dokumen_tambahan/hapus/`+id;
			Swal.fire(
			'Hapus!',
			'Dokumen telah berhasil dihapus.',
			'success'
			)
		}
		})
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
@endsection
