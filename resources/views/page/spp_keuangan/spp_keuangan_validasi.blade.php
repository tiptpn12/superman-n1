@extends('template.master')
@section('title', 'SPP Khusus | Detail SPP')
@section('header')
<link rel="stylesheet" href="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/css/fileinput.min.css">
@endsection
@section('konten')

<!-- MAIN -->
<div class="main">
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="container-fluid">
            <h3 class="page-title"></h3>
            <div class="row">
                <div class="col-md-12">
                    <!-- DETAIL SPP -->
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">Validasi SPP Khusus</h3>
                        </div>
                        <input type="hidden" id="formspp" value="{{$formspp}}">
                        <div class="panel-body">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Jalur Pajak</label>
                                <div class="col-sm-10">
                                    @if($spp->spp_jalur_pajak == 0)
                                    Tidak Melalui Pajak dan MIRO
                                    @else
                                    Melalui Pajak dan MIRO
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Dokumen SPP Khusus</label>
                                <div class="col-sm-10">
                                <a href="{{route('previewvalidasisppk',['id' => $id_validasi])}}" target="_blank">Preview</a><br></br>

                                </div>
                            </div>
                        </div>
                    </div>
                    @if(isset($sppb) && $sppb !== [])
                    <!-- DETAIL SPPB -->
                    <div class="panel" id="panel_sppb" style="display: none">
                        <div class="panel-heading">
                            SPPb
                        </div>
                        <div class="panel-body">
                        <table class="table-bordered table-striped nowrap" style="width: 100%">
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

                    <!-- DETAIL SPPN -->
                    @if(isset($sppn))
                    <div class="panel" id="panel_sppn" style="display: none">
                        <div class="panel-heading">
                            SPPn
                        </div>
                        <div class="panel-body">
                        <table class="table-bordered table-striped nowrap" style="width: 100%">
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
                            <!-- <div class="form-group row">
                                <label class="col-sm-2 col-form-label">No SPPb</label>
                                <div class="col-sm-10">
                                    @if(isset($sppn['sppn_no']))
                                    <p>{{$sppn['sppn_no']}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Kwitansi</label>
                                <div class="col-sm-10">
                                    @if(isset($sppn['sppn_kwitansi']))
                                    <p>{{$sppn['sppn_kwitansi']}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Referensi</label>
                                <div class="col-sm-10">
                                    @if(isset($sppn['sppn_referensi']))
                                    <p>{{$sppn['sppn_referensi']}}</p>
                                    @endif </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">BA/AU.58</label>
                                <div class="col-sm-10">
                                    @if(isset($sppn['sppn_ba_au_53']))
                                    <p>{{$sppn['sppn_ba_au_53']}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Faktur Pajak</label>
                                <div class="col-sm-10">
                                    @if(isset($sppn[1]))
                                        @foreach($sppn[1] as $value)
                                            <p>{{$value->faktur_pajak_nomor}}</p>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">SP / OPL</label>
                                <div class="col-sm-10">
                                    @if(isset($sppn['sppn_sp_opl']))
                                    <p>{{$sppn['sppn_sp_opl']}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Bagian</label>
                                <div class="col-sm-10">
                                    @if(isset($sppn['master_bagian_nama']))
                                    <p>{{$sppn['master_bagian_nama']}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Tanggal</label>
                                <div class="col-sm-10">
                                    @if(isset($sppn['sppn_tanggal']))
                                    <p>
                                        <p>{{date('d-m-Y',strtotime($sppn['sppn_tanggal']))}}</p>
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Catatan</label>
                                <div class="col-sm-10">
                                    @if(isset($sppn['sppn_catatan']))
                                    <p>{{$sppn['sppn_catatan']}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Dokumen Pendukung</label>
                                <div class="col-sm-10">
                                    @if(isset($dokpensppn))
                                    @foreach($dokpensppn as $d)
                                    <a href="{{ asset('dokumen/'.$d->dokumen_pendukung_sppn_nama) }}"
                                        target="_blank">{{$d->dokumen_pendukung_sppn_nama}}</a><br></br>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Isi SPPn</label>
                                <br></br>
                                <div class="col-sm-12">
                                    <table class="table table-bordered striped" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th style="display:none;">id</th>
                                                <th rowspan="2">No</th>
                                                <th colspan="4">Kode Rekening</th>
                                                <th rowspan="2">Uraian</th>
                                                <th rowspan="2">Jumlah (Rp.)</th>
                                            </tr>
                                            <tr>
                                                <th> KBB </th>
                                                <th> SAP </th>
                                                <th> CC/PC </th>
                                                <th> CF </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($sppn[0]))
                                            @for($i=0;$i< count($sppn[0]) ;$i++) <tr>
                                                <th style="display:none;">{{$sppn[0][$i]['sppn_isi_id']}}</th>
                                                <th> {{$i+1}}</th>
                                                <th>{{$sppn[0][$i]['master_rekening_kode_kbb']}}</th>
                                                @if($no_vendor_sppn !== null)
                                                <th style="text-align:center">
												@foreach($no_vendor_sppn as $no)
												{{$no}} <br>
												@endforeach
												</th>
                                                @else
                                                <th style="text-align:center">{{$sppn[0][$i]['master_rekening_kode_sap']}}</th>
                                                
                                                @endif
                                                <th>{{$sppn[0][$i]['master_cost_center_kode']}}{{$sppn[0][$i]['master_profit_center_kode']}}
                                                <th >{{$sppn[0][$i]['master_cash_flow_kode']}}</th>
                                                </th>
                                                <th style="text-align:left">{!! $sppn[0][$i][0][0]->sppn_uraian_uraian
                                                    !!}</th>
                                                <th>{{number_format($sppn[0][$i][0][0]->sppn_uraian_nominal)}}</th>

                                                @for($a=1;$a< count($sppn[0][$i][0]) ;$a++) <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th style="text-align:left">{!!
                                                        $sppn[0][$i][0][$a]->sppn_uraian_uraian !!}</th>
                                                    <th>{{number_format($sppn[0][$i][0][$a]->sppn_uraian_nominal)}}</th>
                                                    </tr>
                                                    @endfor
                                                    </tr>
                                                    @endfor
                                                    @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
                @endif
                <!-- END DETAIL SPPN -->

                <!-- PANEL DOKUMEN TAMBAHAN -->
                <!-- <div class="panel" id="panel_dokumen_tambahan">
                    <div class="panel-heading">
                        <h3 class="panel-title">Dokumen Tambahan</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-striped nowrap" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>No. </th>
                                    <th>Dokumen</th>
                                    <th>User</th>
                                    <th>Waktu</th>
									<th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1 @endphp
                                @foreach($doktam as $d)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>
                                        <a href="{{ asset('dokumen/'.$d->dokumen_tambahan_nama)}}"
                                            target="_blank">{{$d->dokumen_tambahan_nama}}</a>
                                    </td>
                                    <td>{{$d->master_hak_akses_nama}}</td>
                                    <td>{{date('d-m-Y H:i:s',strtotime($d->dokumen_tambahan_waktu))}}</td>
                                    <td style="text-align:center;">
                                        <button type="button" class="btn btn-danger btn-sm" title="hapus"
                                            onclick="hapus_dokumen_tambahan('{{$d->dokumen_tambahan_id}}','{{$d->dokumen_tambahan_nama}}')">x</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> -->
                <!-- END PANEL DOKUMEN TAMBAHAN -->
                <!-- DOKUMEN BUKTI KAS -->
					<!-- <div class="panel" id="panel_dokumen_bukti_kas">
						<div class="panel-heading">
							<h3 class="panel-title">Dokumen Bukti Kas</h3>
						</div>
						<div class="panel-body">
						@if(isset($sppb_bayar->sppb_bayar_bukti))
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">Bukti Transfer</label>
								<div class="col-sm-10">
								
									<a href="{{ asset('dokumen/'.$sppb_bayar->sppb_bayar_bukti)}}" target="_blank">{{$sppb_bayar->sppb_bayar_bukti}}</a>
									
								</div>
							</div>
							@endif
							@if(isset($sppn_terima->sppn_terima_bukti))
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">Bukti Terima</label>
								<div class="col-sm-10">
								
									<a href="{{ asset('dokumen/'.$sppn_terima->sppn_terima_bukti)}}" target="_blank">{{$sppn_terima->sppn_terima_bukti}}</a>
									
								</div>
							</div>
							@endif
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">Bukti Kas</label>
								<div class="col-sm-10">
								@if(isset($spp->spp_bukti_kas_bank))
									<a href="{{ asset('dokumen/'.$spp->spp_bukti_kas_bank)}}" target="_blank">{{$spp->spp_bukti_kas_bank}}</a>
									@endif
								</div>
							</div>
						</div>
					</div> -->
					<!-- END DOKUMEN BUKTI KAS -->
                <?php 
					$hakakses = Session::get('hak_akses');
					$level = Session::get('level');
					?>
                
            </div>
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
                        <input type="file" id="spp_kabag" name="spp_kabag" class="file"
                            accept="application/pdf, image/*" placeholder="SPP tanda tangan Kabag" autocomplete="off">
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
            <form action="" method="post" id="form-revisi" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Revisi SPP</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Masukkan Keterangan Revisi SPP :</label><br>
                        <textarea class="form-control" name="revisi" placeholder="Keterangan Revisi"
                            required></textarea>
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
    $(document).ready(function () {

        var formspp = $("#formspp").val();

        if (formspp == 2) {
            $('#panel_sppn').show();

        } else if (formspp == 1) {
            $('#panel_sppb').show();

        } else {
            $('#panel_sppb').show();
            $('#panel_sppn').show();

        }


    });
    $(document).ready(function () {
        $(".file").fileinput({
            allowedFileTypes: ["image", "pdf"],
            browseClass: "btn btn-primary btn-block",
            showCaption: true,
            showRemove: false,
            showUpload: false,
            showPreview: false,
        });
    });

    function revisi(id) {
        $("#modal_revisi").modal('show');
        $("#form-revisi").attr('action', '{{url('')}}/spp_keuangan/revisi/' + id);
    }

    function upload_kirim(id) {
        $("#modal_kirim").modal('show');
        $("#form-kirim").attr('action', '{{url('')}}/spp_keuangan/upload/' + id);
    }

    function terima(id) {
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
                window.location.href = `{{url('')}}/spp_keuangan/accept/` + id;
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
    function kirim(id) {
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
                window.location.href = `{{url('')}}/spp_keuangan/send/` + id;
                Swal.fire(
                    'Kirim SPP',
                    'SPP berhasil anda Kirim.',
                    'success',
                )
                $("#respon").hide();
            }
        })
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