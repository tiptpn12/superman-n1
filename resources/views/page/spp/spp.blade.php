@extends('template.master')
@section('title', 'SPP')

@section('header')
<link rel="stylesheet" href="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/css/fileinput.min.css">
@endsection
@section('open')
active
@endsection
@section('konten')
<?php 
$hakakses = Session::get('hak_akses');
$bagian = Session::get('bagian');
$level = Session::get('level');
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
    <div class="container-fluid">
      <!-- <h3 class="page-title">SPP</h3> -->
      <div class="row">
        <div class="col-md-12">
          <!-- TABLE -->
          <div class="panel">
            <!-- <div class="custom-tabs-line tabs-line-bottom left-aligned">
              <ul class="nav" role="tablist">
                <li class="active"><a href="#tab-operator-bagian" role="tab" data-toggle="tab">Operator Bagian</a></li>
                <li><a href="#tab-kepala-bagian" role="tab" data-toggle="tab">Kepala Bagian</a></li>
                <li><a href="#tab-petugas-penerima" role="tab" data-toggle="tab">Petugas Penerima</a></li>
                <li><a href="#tab-petugas-pajak" role="tab" data-toggle="tab">Petugas Pajak</a></li>
                <li><a href="#tab-petugas-sap-miro" role="tab" data-toggle="tab">Petugas SAP MIRO</a></li>
                <li><a href="#tab-petugas-verifikasi" role="tab" data-toggle="tab">Petugas Verifikasi</a></li>
                <li><a href="#tab-petugas-kas-bank" role="tab" data-toggle="tab">Petugas Kas dan Bank</a></li>
                <li><a href="#tab-petugas-pembayaran" role="tab" data-toggle="tab">Petugas Pembayaran</a></li>
              </ul>
            </div>  -->
            <!-- <div class="tab-content"> -->
            @if($hakakses == 1)
              {{-- TAB ADMIN --}}
              <div class="tab-pane fade in active" id="tab-operator-bagian">
                <div class="panel-heading">
                  <h3 class="panel-title">Tabel SPPb / SPPn</h3>
                </div>
                <div class="panel-body">
                  <a class="btn btn-primary" href="{{ url('spp/tambah') }}" style="margin-bottom: 15px">Buat SPP</a>
                  <div class="custom-tabs-line tabs-line-bottom left-aligned">
                    <ul class="nav" role="tablist">
                      <li id="tab-sedang-proses"><a href="#tab-sedang-proses-petugas" role="tab" data-toggle="tab">Sedang Proses</a></li>
                      <li id="tab-sudah-selesai"><a href="#tab-sudah-selesai-petugas" role="tab" data-toggle="tab">Sudah Selesai</a></li>
                      <li id="tab-dibatalkan"><a href="#tab-dibatalkan-petugas" role="tab" data-toggle="tab">Dibatalkan</a></li>
                    </ul>
                  </div>
                  <div class="tab-content">

                    {{-- Panel Sedang Proses --}}
                    <div class="tab-pane fade in active" id="tab-sedang-proses-petugas">
                      <button class="btn btn-primary" onclick="advanced_search(1,9)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2">No. </th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th style="display:none;">Id</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data as $d => $s)
                          @if($s->spp_status_ob==2)
                          <tr>
                            <td>{{$a++}}</td>
                            <td><strong>{{$s->master_bagian_nama}}</strong></td>
                            <td style="display:none;">{{$s->spp_id}}</td>
                            <td><strong>{{date('d-m-Y',strtotime($s->tanggal))}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$s->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppb_total)}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$s->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
                            <td><strong>{{$status_revisi[$d]}} {{$posisi[$d]->master_hak_akses_keterangan}}</strong></td>

                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="window.open('{{ url('spp/cetak/'.$s->spp_id) }}').print();" title="Cetak"><i class="fa fa-print"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                              @if($s->spp_status_ob==0 || $s->spp_status_proses == 0)
                              <a type="button" class="btn btn-warning btn-sm" href="{{ url('spp/edit/'.$s->spp_id) }}" title="Edit Data" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
                              @endif
                              <button type="button" class="btn btn-danger btn-sm" onclick="batal({{$s->spp_id}})" title="Batalkan" ><i class="fa fa-ban" aria-hidden="true"></i></button>
                            </td>
                          </tr>
                          @endif
                          @endforeach
                          @foreach($data as $d => $s)
                          @if($s->spp_status_ob==0)
                          <tr>
                            <td>{{$a++}}</td>
                            <td style="display:none;">{{$s->spp_id}}</td>
                            <td><strong>{{$s->master_bagian_nama}}</strong></td>
                            <td><strong>{{date('d-m-Y',strtotime($s->tanggal))}}</strong></td>
                            <td ><strong>{{$s->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppb_total)}}</strong></td>
                            <td><strong>{{$s->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
                            <td><strong>{{$status_revisi[$d]}} {{$posisi[$d]->master_hak_akses_keterangan}}</strong></td>

                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="window.open('{{ url('spp/cetak/'.$s->spp_id) }}').print();" title="Cetak"><i class="fa fa-print"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                              @if($s->spp_status_ob==0 || $s->spp_status_proses == 0)
                              <a type="button" class="btn btn-warning btn-sm" href="{{ url('spp/edit/'.$s->spp_id) }}" title="Edit Data" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
                              @endif
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-danger btn-sm" onclick="batal({{$s->spp_id}})" title="Batalkan" ><i class="fa fa-ban" aria-hidden="true"></i></button>
                            </td>
                          </tr>
                          @endif
                          @endforeach
                          @foreach($data as $d => $s)
                          @if($s->spp_status_ob==1)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td style="display:none;">{{$s->spp_id}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td>{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            @if(isset($posisi[$d]->master_hak_akses_keterangan))
                            <td><strong>{{$posisi[$d]->master_hak_akses_keterangan}}</strong></td>
                            @else
                            <td>Selesai</td>
                            @endif
                            

                            
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="window.open('{{ url('spp/cetak/'.$s->spp_id) }}').print();" title="Cetak"><i class="fa fa-print"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                              @if($s->spp_status_ob==0 || $s->spp_status_proses == 0)
                              <a type="button" class="btn btn-warning btn-sm" href="{{ url('spp/edit/'.$s->spp_id) }}" title="Edit Data" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
                              @endif
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-danger btn-sm" onclick="batal({{$s->spp_id}})" title="Batalkan" ><i class="fa fa-ban" aria-hidden="true"></i></button>
                            </td>
                          </tr>
                          @endif
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sedang Proses --}}

                    {{-- Panel Sudah Selesai --}}
                    <div class="tab-pane fade" id="tab-sudah-selesai-petugas">
                      <button class="btn btn-primary" onclick="advanced_search(2,9)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_selesai as $d => $s)
                        <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td>{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td>{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Selesai</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm"
                                    onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail"><i
                                        class="fa fa-info"></i></button>
                                <button type="button" class="btn btn-primary btn-sm"
                                    onclick="rekam_jejak({{ json_encode( $rekam_jejak_selesai[$d] )}})" title="Rekam Jejak"><i
                                        class="fa fa-map-o"></i></button>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sudah Selesai --}}

                    {{-- Panel Dibatalkan --}}
                    <div class="tab-pane fade" id="tab-dibatalkan-petugas">
                      <button class="btn btn-primary" onclick="advanced_search(3,9)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_batal as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Dibatalkan ( {{$posisi_batal[$d]->master_hak_akses_keterangan}} )</td>
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_batal[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                            </td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Dibatalkan --}}

                  </div>
                </div>
              </div>
              {{-- END TAB ADMIN --}}
              @endif
              @if($hakakses == 2)
              {{-- TAB OPERATOR BAGIAN --}}
              <div class="tab-pane fade in active" id="tab-operator-bagian">
                <div class="panel-heading">
                  <h3 class="panel-title">Tabel SPPb / SPPn</h3>
                </div>
                <div class="panel-body">
                  <a class="btn btn-primary" href="{{ url('spp/tambah') }}" style="margin-bottom: 15px">Buat SPP</a>
                  <div class="custom-tabs-line tabs-line-bottom left-aligned">
                    <ul class="nav" role="tablist">
                    <li id="tab-sedang-proses"><a href="#tab-sedang-proses-petugas" role="tab" data-toggle="tab">Sedang Proses</a></li>
                      <li id="tab-sudah-selesai"><a href="#tab-sudah-selesai-petugas" role="tab" data-toggle="tab">Sudah Selesai</a></li>
                      <li id="tab-dibatalkan"><a href="#tab-dibatalkan-petugas" role="tab" data-toggle="tab">Dibatalkan</a></li>
                    </ul>
                  </div>
                  <div class="tab-content">

                    {{-- Panel Sedang Proses --}}
                    <div class="tab-pane fade in active" id="tab-sedang-proses-petugas">
                      <button class="btn btn-primary" onclick="advanced_search(1,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2">No. </th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th style="display:none;">Id</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data as $d => $s)
                          @if($s->spp_status_ob==2)
                          <tr>
                            <td>{{$a++}}</td>
                            <td style="display:none;">{{$s->spp_id}}</td>
                            <td><strong>{{date('d-m-Y',strtotime($s->tanggal))}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$s->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppb_total)}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$s->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
                            
                            <td><strong>{{$status_revisi[$d]}} {{$posisi[$d]->master_hak_akses_keterangan}}</strong></td>
                            
                            <td>
                            @if($s->spp_status_ob==0 || $s->spp_status_proses == 0)
                              <button type="button" class="btn btn-success btn-sm" onclick="upload_kirim({{$s->spp_id}},'{{$s->spp_kabag}}')" title="kirim" ><i class="fa fa-check"></i></button>
                            @endif
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="window.open('{{ url('spp/cetak/'.$s->spp_id) }}').print();" title="Cetak"><i class="fa fa-print"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                              @if($s->spp_status_ob==0 || $s->spp_status_proses == 0)
                              <a type="button" class="btn btn-warning btn-sm" href="{{ url('spp/edit/'.$s->spp_id) }}" title="Edit Data" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
                              @endif
                              @if($sppb_bayar[$d] == null || $sppn_terima[$d] == null)
                              <button type="button" class="btn btn-danger btn-sm" onclick="batal({{$s->spp_id}})" title="Batalkan" ><i class="fa fa-ban" aria-hidden="true"></i></button>
                              @endif
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}},{{json_encode($asal[$d])}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>

                            </td>
                          </tr>
                          @endif
                          @endforeach
                          @foreach($data as $d => $s)
                          @if($s->spp_status_ob==0)
                          <tr>
                            <td>{{$a++}}</td>
                            <td style="display:none;">{{$s->spp_id}}</td>
                            <td><strong>{{date('d-m-Y',strtotime($s->tanggal))}}</strong></td>
                            <td ><strong>{{$s->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppb_total)}}</strong></td>
                            <td><strong>{{$s->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
                            
                            <td><strong>Belum Diproses</strong></td>
                            
                            <td>
                            @if($s->spp_status_ob==0 || $s->spp_status_proses == 0)
                              <button type="button" class="btn btn-success btn-sm" onclick="upload_kirim({{$s->spp_id}})" title="kirim" ><i class="fa fa-check"></i></button>
                            @endif
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="window.open('{{ url('spp/cetak/'.$s->spp_id) }}').print();" title="Cetak"><i class="fa fa-print"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                              @if($s->spp_status_ob==0 || $s->spp_status_proses == 0)
                              <a type="button" class="btn btn-warning btn-sm" href="{{ url('spp/edit/'.$s->spp_id) }}" title="Edit Data" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
                              @endif
                              @if($sppb_bayar[$d] == null || $sppn_terima[$d] == null)
                              <button type="button" class="btn btn-danger btn-sm" onclick="batal({{$s->spp_id}})" title="Batalkan" ><i class="fa fa-ban" aria-hidden="true"></i></button>
                              @endif
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak()" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              
                            </td>
                          </tr>
                          @endif
                          @endforeach
                          @foreach($data as $d => $s)
                          @if($s->spp_status_ob==1)
                          <tr>
                            <td>{{$a++}}</td>
                            <td style="display:none;">{{$s->spp_id}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td>{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            
                            <td><strong>Sedang Diproses</strong></td>
                            
                            <td>
                            @if($s->spp_status_ob==0 || $s->spp_status_proses == 0)
                              <button type="button" class="btn btn-success btn-sm" onclick="upload_kirim({{$s->spp_id}})" title="kirim" ><i class="fa fa-check"></i></button>
                            @endif
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="window.open('{{ url('spp/cetak/'.$s->spp_id) }}').print();" title="Cetak"><i class="fa fa-print"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                              @if($s->spp_status_ob==0 || $s->spp_status_proses == 0)
                              <a type="button" class="btn btn-warning btn-sm" href="{{ url('spp/edit/'.$s->spp_id) }}" title="Edit Data" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
                              <button type="button" class="btn btn-warning btn-sm" onclick="batal({{$s->spp_id}})" title="Batalkan" ><i class="fa fa-ban" aria-hidden="true"></i></button>
                              @endif
                              @if($sppb_bayar[$d] == null || $sppn_terima[$d] == null)
                              
                              @endif
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}},{{json_encode($asal[$d])}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>

                            </td>
                          </tr>
                          @endif
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sedang Proses --}}

                    {{-- Panel Sudah Selesai --}}
                    <div class="tab-pane fade" id="tab-sudah-selesai-petugas">
                      <button class="btn btn-primary" onclick="advanced_search(2,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_selesai as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Selesai</td>
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_selesai[$d] )}},{{json_encode($asal[$d])}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                            </td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sudah Selesai --}}

                    {{-- Panel Dibatalkan --}}
                    <div class="tab-pane fade" id="tab-dibatalkan-petugas">
                      <button class="btn btn-primary" onclick="advanced_search(3,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_batal as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            @if(isset($posisi_batal[$d]))
                            <td>Dibatalkan ( {{$posisi_batal[$d]->master_hak_akses_keterangan}} )</td>
                            @else
                            <td>Dibatalkan ( )</td>

                            @endif
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_batal[$d] )}},1)" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>

                            </td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Dibatalkan --}}

                  </div>
                </div>
              </div>
              {{-- END TAB OPERATOR BAGIAN --}}
              @endif

              @if($hakakses == 3)
              {{-- TAB KEPALA BAGIAN --}}
              <div class="tab-pane fade in active" id="tab-kepala-bagian">
                <div class="panel-heading">
                  <h3 class="panel-title">Tabel SPPb / SPPn</h3>
                </div>
                <div class="panel-body">
                  <div class="custom-tabs-line tabs-line-bottom left-aligned">
                    <ul class="nav" role="tablist">
                    <li id="tab-sedang-proses"><a href="#tab-sedang-proses-petugas" role="tab" data-toggle="tab">Sedang Proses</a></li>
                      <li id="tab-sudah-selesai"><a href="#tab-sudah-selesai-petugas" role="tab" data-toggle="tab">Sudah Selesai</a></li>
                      <li id="tab-dibatalkan"><a href="#tab-dibatalkan-petugas" role="tab" data-toggle="tab">Dibatalkan</a></li>
                    </ul>
                  </div>
                  <div class="tab-content">

                    {{-- Panel Sedang Proses --}}
                    <div class="tab-pane fade in active" id="tab-sedang-proses-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(1,2)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2">No. </th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Posisi</th>
                            <th rowspan="2">Status Bayar</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp                        
                        @foreach($data as $d => $s)
                        @if($s->spp_status_ob==2)
                          <tr>
                            <td>{{$a++}}</td>                         
                            <td><strong>{{$s->master_bagian_nama}}</strong></td>
                            <td><strong>{{date('d-m-Y',strtotime($s->tanggal))}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$s->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppb_total)}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$s->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
                            
                            <td><strong>{{$status_revisi[$d]}} {{$posisi[$d]->master_hak_akses_keterangan}}</strong></td>
                            @if($s->spp_status_lunas == 1)
                            <td><strong>Sudah Dibayar</strong></td>
                            @else
                            <td><strong>Belum Dibayar</strong></td>
                            @endif
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="window.open('{{ url('spp/cetak/'.$s->spp_id) }}').print();" title="Cetak"><i class="fa fa-print"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            </td>
                          </tr>
                          @endif
                        @endforeach
                        @foreach($data as $d => $s)
                        @if($s->spp_status_ob < 3 && $s->spp_status_ob != 2)
                        
                          <tr>
                            <td><strong>{{$a++}}</strong></td>
                            <td><strong>{{$s->master_bagian_nama}}</strong></td>
                            <td><strong>{{date('d-m-Y',strtotime($s->tanggal))}}</strong></td>
                            <td><strong>{{$s->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppb_total)}}</strong></td>
                            <td><strong>{{$s->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
                            @if(isset($posisi[$d]->master_hak_akses_keterangan))
                            <td><strong>{{$posisi[$d]->master_hak_akses_keterangan}}</strong></td>
                            @else
                            <td>Selesai</td>
                            @endif
                            @if($s->spp_status_lunas == 1)
                            <td><strong>Sudah Dibayar</strong></td>
                            @else
                            <td><strong>Belum Dibayar</strong></td>
                            @endif
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="window.open('{{ url('spp/cetak/'.$s->spp_id) }}').print();" title="Cetak"><i class="fa fa-print"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            </td>
                          </tr>
                          @endif
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sedang Proses --}}

                    {{-- Panel Sudah Selesai --}}
                    <div class="tab-pane fade" id="tab-sudah-selesai-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(2,2)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Status Bayar</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_selesai as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Selesai</td>
                            @if($s->spp_status_lunas == 1)
                            <td><strong>Sudah Dibayar</strong></td>
                            @else
                            <td><strong>Belum Dibayar</strong></td>
                            @endif
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_selesai[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                            </td>
                          </tr>
                        @endforeach
                          
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sudah Selesai --}}

                    {{-- Panel Dibatalkan --}}
                    <div class="tab-pane fade" id="tab-dibatalkan-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(3,2)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_batal as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Dibatalkan ( {{$posisi_batal[$d]->master_hak_akses_keterangan}} )</td>
                            @if($s->spp_status_lunas == 1)
                            <td><strong>Sudah Dibayar</strong></td>
                            @else
                            <td><strong>Belum Dibayar</strong></td>
                            @endif
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_batal[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                            </td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Dibatalkan --}}

                  </div>
                </div>
              </div>
              {{-- END TAB KEPALA BAGIAN --}}
              @endif
              @if ($hakakses == 4)
              {{-- TAB PETUGAS PENERIMA --}}
              <div class="tab-pane fade in active" id="tab-petugas-penerima">
                <div class="panel-heading">
                  <h3 class="panel-title">Tabel SPPb / SPPn</h3>
                </div>
                <div class="panel-body">
                  <div class="custom-tabs-line tabs-line-bottom left-aligned">
                    <ul class="nav" role="tablist">
                    <li id="tab-sedang-proses"><a href="#tab-sedang-proses-petugas" role="tab" data-toggle="tab">Sedang Proses</a></li>
                      <li id="tab-sudah-selesai"><a href="#tab-sudah-selesai-petugas" role="tab" data-toggle="tab">Sudah Selesai</a></li>
                      <li id="tab-dibatalkan"><a href="#tab-dibatalkan-petugas" role="tab" data-toggle="tab">Dibatalkan</a></li>
                    </ul>
                  </div>
                  <div class="tab-content">

                    {{-- Panel Sedang Proses --}}
                    <div class="tab-pane fade in active" id="tab-sedang-proses-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(1,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2">No.asaas </th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP </th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data as $d => $s)
                        @if($s->spp_status_proses < 3  && $s->spp_status_ob < 3 && $s->spp_status_ob != 2 && $status_revisi[$d] == 'Revisi oleh')
                          <tr>
                            <td><strong>{{$a++}}</strong></td>
                            <td><strong>{{$s->master_bagian_nama}}</strong></td>
                            <td><strong>{{date('d-m-Y',strtotime($s->tanggal))}}</strong></td>
                            <td ><strong>{{$s->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppb_total)}}</strong></td>
                            <td><strong>{{$s->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
                            <td><strong>{{$posisi[$d]->master_hak_akses_keterangan}}</strong></td>
                            <td>
                              @if($s->spp_status_proses == 1)
                              <a class="btn btn-success btn-sm" onclick="terima({{$s->spp_id}})" title="Terima" ><i class="fa fa-check"></i></a>
                              @endif
                              @if($s->spp_status_proses == 2)
                              <button type="button" class="btn btn-success btn-sm" onclick="kirim({{$s->spp_id}})" title="Kirim" ><i class="fa fa-arrow-right"></i></button>
                              <button type="button" class="btn btn-warning btn-sm" onclick="revisi({{$s->spp_id}},{{json_encode($rekam_jejak[$d])}})" title="Revisi" ><i class="fa fa-arrow-left"></i></button>
                              @endif
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            </td>
                          </tr>
                          @endif
                          @endforeach
                          @foreach($data as $d => $s)
                          @if($s->spp_status_proses <= 2 && $s->spp_status_ob == 2 && $status_revisi[$d] =='Revisi oleh')
                          <tr>
                            <td><strong>{{$a++}}</strong></td>
                            <td><strong>{{$s->master_bagian_nama}}</strong></td>
                            <td><strong>{{date('d-m-Y',strtotime($s->tanggal))}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$s->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppb_total)}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$s->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
                            <td><strong>{{$status_revisi[$d]}} {{$posisi[$d]->master_hak_akses_keterangan}}</strong></td>
                            <td>
                              @if($s->spp_status_proses == 1)
                              <a class="btn btn-success btn-sm" onclick="terima({{$s->spp_id}})" title="Terima" ><i class="fa fa-check"></i></a>
                              @endif
                              @if($s->spp_status_proses == 2)
                              <button type="button" class="btn btn-warning btn-sm" onclick="revisi({{$s->spp_id}},{{json_encode($rekam_jejak[$d])}})" title="Revisi" ><i class="fa fa-arrow-left"></i></button>
                              @endif
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            </td>
                          </tr>
                          @endif
                        @endforeach
                        @foreach($data as $d => $s) 
                        @if($s->spp_status_proses >= 3 && $s->spp_status_ob == 1 )
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td>{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td>{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            @if(isset($posisi[$d]->master_hak_akses_keterangan))
                            <td>{{$posisi[$d]->master_hak_akses_keterangan}}</td>
                            @else
                            <td>Selesai</td>
                            @endif
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            </td>
                          </tr>
                        @endif
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sedang Proses --}}

                    {{-- Panel Sudah Selesai --}}
                    <div class="tab-pane fade" id="tab-sudah-selesai-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(2,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_selesai as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Selesai</td>
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_selesai[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                            </td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sudah Selesai --}}

                    {{-- Panel Dibatalkan --}}
                    <div class="tab-pane fade" id="tab-dibatalkan-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(3,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_batal as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Dibatalkan ( {{$posisi_batal[$d]->master_hak_akses_keterangan}} )</td>
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_batal[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                            </td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Dibatalkan --}}

                  </div>
                </div>
              </div>
              {{-- END TAB PETUGAS PENERIMA --}}
              @endif
              @if ($hakakses == 5)
              {{-- TAB PETUGAS PAJAK --}}
              <div class="tab-pane fade in active" id="tab-petugas-pajak">
                <div class="panel-heading">
                  <h3 class="panel-title">Tabel SPPb / SPPn</h3>
                </div>
                <div class="panel-body">
                  <div class="custom-tabs-line tabs-line-bottom left-aligned">
                    <ul class="nav" role="tablist">
                    <li id="tab-sedang-proses"><a href="#tab-sedang-proses-petugas" role="tab" data-toggle="tab">Sedang Proses</a></li>
                      <li id="tab-sudah-selesai"><a href="#tab-sudah-selesai-petugas" role="tab" data-toggle="tab">Sudah Selesai</a></li>
                      <li id="tab-dibatalkan"><a href="#tab-dibatalkan-petugas" role="tab" data-toggle="tab">Dibatalkan</a></li>
                    </ul>
                  </div>
                  <div class="tab-content">

                    {{-- Panel Sedang Proses --}}
                    <div class="tab-pane fade in active" id="tab-sedang-proses-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(1,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2">No. </th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data as $s => $d)
                        @if($d->spp_status_proses < 5 && $d->spp_status_ob < 3 && $d->spp_status_ob != 2 && $status_revisi[$s] == 'Revisi oleh')

                          <tr>
                            <td><strong>{{$a++}}</strong></td>
                            <td><strong>{{$d->master_bagian_nama}}</strong></td>
                            <td><strong>{{date('d-m-Y',strtotime($d->tanggal))}}</strong></td>
                            <td><strong>{{$d->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($d->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($d->sppb_total)}}</strong></td>
                            <td><strong>{{$d->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($d->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($d->sppn_jumlah)}}</strong></td>
                            <td><strong> {{$posisi[$s]->master_hak_akses_keterangan}}</strong></td>
                 
                            <td>
                              @if($d->spp_status_proses == 3)
                              <a class="btn btn-success btn-sm" onclick="terima({{$d->spp_id}})" title="Terima" ><i class="fa fa-check"></i></a>
                              @endif
                              @if($d->spp_status_proses == 4)
                              <button type="button" class="btn btn-success btn-sm" onclick="kirim({{$d->spp_id}})" title="Kirim" ><i class="fa fa-arrow-right"></i></button>
                              <button type="button" class="btn btn-warning btn-sm" onclick="revisi({{$d->spp_id}})" title="Revisi" ><i class="fa fa-arrow-left"></i></button>
                              @endif
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$d->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$s] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$d->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            </td>
                          </tr>
                          @endif
                          @endforeach
                          @foreach($data as $s => $d)
                          @if($d->spp_status_proses < 5 && $d->spp_status_ob == 2  && $status_revisi[$s] == 'Revisi oleh')
                          <tr>
                            <td><strong>{{$a++}}</strong></td>
                            <td><strong>{{$d->master_bagian_nama}}</strong></td>
                            <td><strong>{{date('d-m-Y',strtotime($d->tanggal))}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$d->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($d->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($d->sppb_total)}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$d->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($d->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($d->sppn_jumlah)}}</strong></td>
                            <td><strong>{{$status_revisi[$s]}} {{$posisi[$s]->master_hak_akses_keterangan}}</strong></td>
                 
                            <td>
                              @if($d->spp_status_proses == 3)
                              <a class="btn btn-success btn-sm" onclick="terima({{$d->spp_id}})" title="Terima" ><i class="fa fa-check"></i></a>
                              @endif
                              @if($d->spp_status_proses == 4)
                              <button type="button" class="btn btn-success btn-sm" onclick="kirim({{$d->spp_id}})" title="Kirim" ><i class="fa fa-arrow-right"></i></button>
                              <button type="button" class="btn btn-warning btn-sm" onclick="revisi({{$d->spp_id}})" title="Revisi" ><i class="fa fa-arrow-left"></i></button>
                              @endif
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$d->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$s] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$d->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            </td>
                          </tr>
                          @endif
                          @endforeach
                          @foreach($data as $s => $d)
                          @if($d->spp_status_proses >= 5 && $d->spp_status_ob == 1)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$d->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($d->tanggal))}}</td>
                            <td>{{$d->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($d->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($d->sppb_total)}}</td>
                            <td>{{$d->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($d->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($d->sppn_jumlah)}}</td>
                            @if(isset($posisi[$s]->master_hak_akses_keterangan))
                            <td>{{$posisi[$s]->master_hak_akses_keterangan}}</td>
                            @else
                            <td>Selesai</td>
                            @endif
                            <td>
                              
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$d->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$s] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$d->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            </td>
                          </tr>
                          @endif
                          @endforeach
                      
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sedang Proses --}}

                    {{-- Panel Sudah Selesai --}}
                    <div class="tab-pane fade" id="tab-sudah-selesai-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(2,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_selesai as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Selesai</td>
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_selesai[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                            </td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sudah Selesai --}}

                    {{-- Panel Dibatalkan --}}
                    <div class="tab-pane fade" id="tab-dibatalkan-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(3,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_batal as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Dibatalkan ( {{$posisi_batal[$d]->master_hak_akses_keterangan}} )</td>
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_batal[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                            </td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Dibatalkan --}}

                  </div>
                </div>
              </div>
              {{-- END TAB PETUGAS PAJAK --}}
              @endif
              @if ($hakakses == 6)
              {{-- TAB PETUGAS SAP MIRO --}}
              <div class="tab-pane fade in active" id="tab-petugas-sap-miro">
                <div class="panel-heading">
                  <h3 class="panel-title">Tabel SPPb / SPPn</h3>
                </div>
                <div class="panel-body">
                  <div class="custom-tabs-line tabs-line-bottom left-aligned">
                    <ul class="nav" role="tablist">
                    <li id="tab-sedang-proses"><a href="#tab-sedang-proses-petugas" role="tab" data-toggle="tab">Sedang Proses</a></li>
                      <li id="tab-sudah-selesai"><a href="#tab-sudah-selesai-petugas" role="tab" data-toggle="tab">Sudah Selesai</a></li>
                      <li id="tab-dibatalkan"><a href="#tab-dibatalkan-petugas" role="tab" data-toggle="tab">Dibatalkan</a></li>
                    </ul>
                  </div>
                  <div class="tab-content">

                    {{-- Panel Sedang Proses --}}
                    <div class="tab-pane fade in active" id="tab-sedang-proses-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(1,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2">No. </th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data as $d => $s)
                        @if($s->spp_status_proses < 7 && $s->spp_status_ob < 3 && $s->spp_status_ob != 2 && $status_revisi[$d] == 'Revisi oleh')
                          <tr>
                            <td><strong>{{$a++}}</strong></td>
                            <td><strong>{{$s->master_bagian_nama}}</strong></td>
                            <td><strong>{{date('d-m-Y',strtotime($s->tanggal))}}</strong></td>
                            <td><strong>{{$s->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppb_total)}}</strong></td>
                            <td><strong>{{$s->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
              
                            <td><strong>{{$posisi[$d]->master_hak_akses_keterangan}}</strong></td>
            
                            <td>
                              @if($s->spp_status_proses == 5)
                              <a class="btn btn-success btn-sm" onclick="terima({{$s->spp_id}})" title="Terima" ><i class="fa fa-check"></i></a>
                              @endif
                              @if($s->spp_status_proses == 6)
                              <button type="button" class="btn btn-success btn-sm" onclick="kirim({{$s->spp_id}})" title="Kirim" ><i class="fa fa-arrow-right"></i></button>
                              <button type="button" class="btn btn-warning btn-sm" onclick="revisi({{$s->spp_id}})" title="Revisi" ><i class="fa fa-arrow-left"></i></button>
                              @endif
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            </td>
                          </tr>
                          @endif
                          @endforeach
                          @foreach($data as $d => $s)
                          @if($s->spp_status_proses < 7 && $s->spp_status_ob == 2 && $status_revisi[$d] == 'Revisi oleh')
                          <tr>
                            <td><strong>{{$a++}}</strong></td>
                            <td><strong>{{$s->master_bagian_nama}}</strong></td>
                            <td><strong>{{date('d-m-Y',strtotime($s->tanggal))}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$s->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppb_total)}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$s->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
              
                            <td><strong>{{$status_revisi[$d]}} {{$posisi[$d]->master_hak_akses_keterangan}}</strong></td>
            
                            <td>
                              @if($s->spp_status_proses == 5)
                              <a class="btn btn-success btn-sm" onclick="terima({{$s->spp_id}})" title="Terima" ><i class="fa fa-check"></i></a>
                              @endif
                              @if($s->spp_status_proses == 6)
                              <button type="button" class="btn btn-success btn-sm" onclick="kirim({{$s->spp_id}})" title="Kirim" ><i class="fa fa-arrow-right"></i></button>
                              <button type="button" class="btn btn-warning btn-sm" onclick="revisi({{$s->spp_id}})" title="Revisi" ><i class="fa fa-arrow-left"></i></button>
                              @endif
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            </td>
                          </tr>
                          @endif
                          @endforeach
                          @foreach($data as $d => $s)
                          @if($s->spp_status_proses >= 7 && $s->spp_status_ob == 1)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td>{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td>{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
              
                            @if(isset($posisi[$d]->master_hak_akses_keterangan))
                            <td>{{$posisi[$d]->master_hak_akses_keterangan}}</td>
                            @else
                            <td>Selesai</td>
                            @endif
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            </td>
                          </tr>
                          @endif
                          @endforeach
                          
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sedang Proses --}}

                    {{-- Panel Sudah Selesai --}}
                    <div class="tab-pane fade" id="tab-sudah-selesai-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(2,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                        
                           <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>

                        <tbody>
                        @php $a=1 @endphp
                          <tr>
                          @foreach($data_selesai as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Selesai</td>
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_selesai[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                            </td>
                          </tr>
                          @endforeach
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sudah Selesai --}}

                    {{-- Panel Dibatalkan --}}
                    <div class="tab-pane fade" id="tab-dibatalkan-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(3,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_batal as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Dibatalkan ( {{$posisi_batal[$d]->master_hak_akses_keterangan}} )</td>
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_batal[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                            </td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Dibatalkan --}}

                  </div>
                </div>
              </div>
              {{-- END TAB PETUGAS SAP MIRO --}}
              @endif
              @if ($hakakses == 7)
              {{-- TAB PETUGAS VERIFIKASI --}}
              <div class="tab-pane fade in active" id="tab-petugas-verifikasi">
                <div class="panel-heading">
                  <h3 class="panel-title">Tabel SPPb / SPPn</h3>
                </div>
                <div class="panel-body">
                  <div class="custom-tabs-line tabs-line-bottom left-aligned">
                    <ul class="nav" role="tablist">
                    <li id="tab-sedang-proses"><a href="#tab-sedang-proses-petugas" role="tab" data-toggle="tab">Sedang Proses</a></li>
                      <li id="tab-sudah-selesai"><a href="#tab-sudah-selesai-petugas" role="tab" data-toggle="tab">Sudah Selesai</a></li>
                      <li id="tab-dibatalkan"><a href="#tab-dibatalkan-petugas" role="tab" data-toggle="tab">Dibatalkan</a></li>
                    </ul>
                  </div>
                  <div class="tab-content">

                    {{-- Panel Sedang Proses --}}
                    <div class="tab-pane fade in active" id="tab-sedang-proses-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(1,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2">No. </th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data as $d => $s)
                        @if($s->spp_status_proses < 9 && $s->spp_status_ob < 3 && $s->spp_status_ob != 2 && $status_revisi[$d] == 'Revisi oleh')
                          <tr>
                            <td><strong>{{$a++}}</strong></td>
                            <td><strong>{{$s->master_bagian_nama}}</strong></td>
                            <td><strong>{{date('d-m-Y',strtotime($s->tanggal))}}</strong></td>
                            <td><strong>{{$s->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppb_total)}}</strong></td>
                            <td><strong>{{$s->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
                            <td><strong>{{$posisi[$d]->master_hak_akses_keterangan}}</strong></td>
                            <td>
                              @if($s->spp_status_proses == 7)
                              <a class="btn btn-success btn-sm" onclick="terima({{$s->spp_id}})" title="Terima" ><i class="fa fa-check"></i></a>
                              @endif
                              @if($s->spp_status_proses == 8)
                              <button type="button" class="btn btn-success btn-sm" onclick="kirim({{$s->spp_id}})" title="Kirim" ><i class="fa fa-arrow-right"></i></button>
                              <button type="button" class="btn btn-warning btn-sm" onclick="revisi({{$s->spp_id}})" title="Revisi" ><i class="fa fa-arrow-left"></i></button>
                              @endif
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            </td>
                          </tr>
                          @endif
                          @endforeach
                          @foreach($data as $d => $s)
                          @if($s->spp_status_proses < 9 && $s->spp_status_ob == 2 &&  $status_revisi[$d] == 'Revisi oleh')
                          <tr>
                            <td><strong>{{$a++}}</strong></td>
                            <td><strong>{{$s->master_bagian_nama}}</strong></td>
                            <td><strong>{{date('d-m-Y',strtotime($s->tanggal))}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$s->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppb_total)}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$s->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
                            <td><strong>{{$status_revisi[$d]}} {{$posisi[$d]->master_hak_akses_keterangan}}</strong></td>
                            <td>
                              @if($s->spp_status_proses == 7)
                              <a class="btn btn-success btn-sm" onclick="terima({{$s->spp_id}})" title="Terima" ><i class="fa fa-check"></i></a>
                              @endif
                              @if($s->spp_status_proses == 8)
                              <button type="button" class="btn btn-success btn-sm" onclick="kirim({{$s->spp_id}})" title="Kirim" ><i class="fa fa-arrow-right"></i></button>
                              <button type="button" class="btn btn-warning btn-sm" onclick="revisi({{$s->spp_id}})" title="Revisi" ><i class="fa fa-arrow-left"></i></button>
                              @endif
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            </td>
                          </tr>
                          @endif
                          @endforeach
                          @foreach($data as $d => $s)
                          @if($s->spp_status_proses >= 9 && $s->spp_status_ob == 1)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td>{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td>{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            @if(isset($posisi[$d]->master_hak_akses_keterangan))
                            <td>{{$posisi[$d]->master_hak_akses_keterangan}}</td>
                            @else
                            <td>Selesai</td>
                            @endif
                            <td>
                              
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            </td>
                          </tr>
                          @endif
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sedang Proses --}}

                    {{-- Panel Sudah Selesai --}}
                    <div class="tab-pane fade" id="tab-sudah-selesai-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(2,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_selesai as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Selesai</td>
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_selesai[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                            </td>
                          </tr>
                        @endforeach
                          
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sudah Selesai --}}

                    {{-- Panel Dibatalkan --}}
                    <div class="tab-pane fade" id="tab-dibatalkan-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(3,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_batal as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Dibatalkan ( {{$posisi_batal[$d]->master_hak_akses_keterangan}} )</td>
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_batal[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                            </td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Dibatalkan --}}

                  </div>
                </div>
              </div>
              {{-- END TAB PETUGAS VERIFIKASI --}}
              @endif
              @if ($hakakses == 8)
              {{-- TAB PETUGAS KAS BANK --}}
              <div class="tab-pane fade in active" id="tab-petugas-kas-bank">
                <div class="panel-heading">
                  <h3 class="panel-title">Tabel SPPb / SPPn</h3>
                </div>
                <div class="panel-body">
                  <div class="custom-tabs-line tabs-line-bottom left-aligned">
                    <ul class="nav" role="tablist">
                    <li id="tab-sedang-proses"><a href="#tab-sedang-proses-petugas" role="tab" data-toggle="tab">Sedang Proses</a></li>
                      <li id="tab-sudah-selesai"><a href="#tab-sudah-selesai-petugas" role="tab" data-toggle="tab">Sudah Selesai</a></li>
                      <li id="tab-dibatalkan"><a href="#tab-dibatalkan-petugas" role="tab" data-toggle="tab">Dibatalkan</a></li>
                    </ul>
                  </div>
                  <div class="tab-content">

                    {{-- Panel Sedang Proses --}}
                    <div class="tab-pane fade in active" id="tab-sedang-proses-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(1,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2">No. </th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data as $d => $s)
                        @if($s->spp_status_proses < 11 && $s->spp_status_ob < 3 && $s->spp_status_ob != 2 && $status_revisi[$d] == 'Revisi oleh')
                          <tr>
                            <td><strong>{{$a++}}</strong></td>
                            <td><strong>{{$s->master_bagian_nama}}</strong></td>
                            <td><strong>{{date('d-m-Y',strtotime($s->tanggal))}}</strong></td>
                            <td><strong>{{$s->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppb_total)}}</strong></td>
                            <td><strong>{{$s->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
                            
                            <td><strong>{{$posisi[$d]->master_hak_akses_keterangan}}</strong></td>
                       
                            <td>
                              @if($s->spp_status_proses ==9)
                              <a class="btn btn-success btn-sm" onclick="terima({{$s->spp_id}})" title="Terima" ><i class="fa fa-check"></i></a>
                              @endif
                              @if($s->spp_status_proses ==10)
                              <button type="button" class="btn btn-success btn-sm" onclick="kirim({{$s->spp_id}})" title="Kirim" ><i class="fa fa-arrow-right"></i></button>
                              <button type="button" class="btn btn-warning btn-sm" onclick="revisi({{$s->spp_id}})" title="Revisi" ><i class="fa fa-arrow-left"></i></button>

                                  @if(isset($s->sppb_no) && empty($s->sppn_no))
                                  @if($s->nomor_byr == null)
                                    <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000"title="Bukti Kas"  onclick="cetak_bukti_kas(0,{{$s->sppb_id}},0,0,0,0,{{ json_encode($datapenerima[$d]) }},{{ json_encode($data_diterima_dari[$d]) }})"><i class="fa fa-money"></i></button>
                                    @php $a=0 @endphp
                                  @elseif($s->nomor_byr != null)
                                    <button type="button" class="btn btn-primary btn-sm" style="background-color: #6E00FF;border-color: #6E00FF"title="Bukti Kas" onclick="cetak_bukti_kas({{$s->spp_id}},{{$s->sppb_id}},0,0,{{ json_encode($sppb_cetak_bukti_kas[$d]) }},0,{{ json_encode($datapenerima[$d]) }},{{ json_encode($data_diterima_dari[$d]) }})"><i class="fa fa-money"></i></button>
                                    @php $a=1 @endphp
                                  @endif
                                @elseif(empty($s->sppb_no) && isset($s->sppn_no))
                                  @if($s->nomor_pnr == null)
                                    <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onclick="cetak_bukti_kas(0,0,{{$s->sppn_id}},1,0,0,{{ json_encode($datapenerima[$d]) }},{{ json_encode($data_diterima_dari[$d]) }})"><i class="fa fa-money"></i></button>
                                    @php $a=0 @endphp
                                  @elseif($s->nomor_pnr != null)
                                    <button type="button" class="btn btn-primary btn-sm" style="background-color: #6E00FF; border-color: #6E00FF"  title="Bukti Kas" onclick="cetak_bukti_kas({{$s->spp_id}},0,{{$s->sppn_id}},1,0,{{ json_encode($sppn_cetak_bukti_kas[$d]) }},{{ json_encode($datapenerima[$d]) }},{{ json_encode($data_diterima_dari[$d]) }})"><i class="fa fa-money"></i></button>
                                    @php $a=1 @endphp
                                  @endif
                                @elseif(isset($s->sppb_no) && isset($s->sppn_no))
                                  @if($s->nomor_byr == null && $s->nomor_pnr == null)
                                    <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onclick="cetak_bukti_kas(0,{{$s->sppb_id}},{{$s->sppn_id}},2,0,0,{{ json_encode($datapenerima[$d]) }},{{ json_encode($data_diterima_dari[$d]) }})"><i class="fa fa-money"></i></button>
                                    @php $a=0 @endphp
                                  @elseif($s->nomor_byr != null && $s->nomor_pnr == null)
                                    <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onclick="cetak_bukti_kas({{$s->spp_id}},{{$s->sppb_id}},{{$s->sppn_id}},2,{{ json_encode($sppb_cetak_bukti_kas[$d]) }},0,{{ json_encode($datapenerima[$d]) }},{{ json_encode($data_diterima_dari[$d]) }})"><i class="fa fa-money"></i></button>
                                    @php $a=0 @endphp
                                  @elseif($s->nomor_byr == null && $s->nomor_pnr != null)
                                 <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onclick="cetak_bukti_kas({{$s->spp_id}},{{$s->sppb_id}},{{$s->sppn_id}},2,0,{{ json_encode($sppn_cetak_bukti_kas[$d]) }},{{ json_encode($datapenerima[$d]) }},{{ json_encode($data_diterima_dari[$d]) }})"><i class="fa fa-money"></i></button>
                                    @php $a=0 @endphp
                                  @elseif($s->nomor_byr != null && $s->nomor_pnr != null)
                                  <button type="button" class="btn btn-primary btn-sm" style="background-color: #6E00FF; border-color: #6E00FF" title="Bukti Kas" onclick="cetak_bukti_kas({{$s->spp_id}},{{$s->sppb_id}},{{$s->sppn_id}},2,{{ json_encode($sppb_cetak_bukti_kas[$d]) }},{{ json_encode($sppn_cetak_bukti_kas[$d]) }},{{ json_encode($datapenerima[$d]) }},{{ json_encode($data_diterima_dari[$d]) }})"><i class="fa fa-money"></i></button>                              
                                      @php $a=1 @endphp
                                  @endif
                                @endif

                              @endif

                              
                            
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                              <!-- <button type="button" class="btn btn-danger btn-sm" onclick="batal({{$s->spp_id}})" title="Batalkan" ><i class="fa fa-ban" aria-hidden="true"></i></button> -->

                            </td>
                          </tr>
                        @endif
                        @endforeach
                        @foreach($data as $d => $s)
                        @if($s->spp_status_proses < 11 && $s->spp_status_ob == 2 && $status_revisi[$d] == 'Revisi oleh')
                          <tr>
                            <td><strong>{{$a++}}</strong></td>
                            <td><strong>{{$s->master_bagian_nama}}</strong></td>
                            <td><strong>{{date('d-m-Y',strtotime($s->tanggal))}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$s->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppb_total)}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$s->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
                            
                            <td><strong>{{$status_revisi[$d]}} {{$posisi[$d]->master_hak_akses_keterangan}}</strong></td>
                       
                            <td>
                              @if($s->spp_status_proses ==9)
                              <a class="btn btn-success btn-sm" onclick="terima({{$s->spp_id}})" title="Terima" ><i class="fa fa-check"></i></a>
                              @endif
                              @if($s->spp_status_proses ==10)
                              <button type="button" class="btn btn-success btn-sm" onclick="kirim({{$s->spp_id}})" title="Kirim" ><i class="fa fa-arrow-right"></i></button>
                              <button type="button" class="btn btn-warning btn-sm" onclick="revisi({{$s->spp_id}})" title="Revisi" ><i class="fa fa-arrow-left"></i></button>
                              @endif
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            </td>
                          </tr>
                          @endif
                          @endforeach
                          @foreach($data as $d => $s)
                          @if($s->spp_status_proses >= 11 && $s->spp_status_ob == 1)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td>{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td>{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            @if(isset($posisi[$d]->master_hak_akses_keterangan))
                            <td>{{$posisi[$d]->master_hak_akses_keterangan}}</td>
                            @else
                            <td>Selesai</td>
                            @endif
                            <td>
                                  @if(isset($s->sppb_no) && empty($s->sppn_no))
                                  @if($s->nomor_byr == null)
                                    <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000"title="Bukti Kas"  onclick="cetak_bukti_kas(0,{{$s->sppb_id}},0,0,0,0,{{ json_encode($datapenerima[$d]) }},{{ json_encode($data_diterima_dari[$d]) }})"><i class="fa fa-money"></i></button>
                                    @php $a=0 @endphp
                                  @elseif($s->nomor_byr != null)
                                    <button type="button" class="btn btn-primary btn-sm" style="background-color: #6E00FF;border-color: #6E00FF"title="Bukti Kas" onclick="cetak_bukti_kas({{$s->spp_id}},{{$s->sppb_id}},0,0,{{ json_encode($sppb_cetak_bukti_kas[$d]) }},0,{{ json_encode($datapenerima[$d]) }},{{ json_encode($data_diterima_dari[$d]) }})"><i class="fa fa-money"></i></button>
                                    @php $a=1 @endphp
                                  @endif
                                @elseif(empty($s->sppb_no) && isset($s->sppn_no))
                                  @if($s->nomor_pnr == null)
                                    <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onclick="cetak_bukti_kas(0,0,{{$s->sppn_id}},1,0,0,{{ json_encode($datapenerima[$d]) }},{{ json_encode($data_diterima_dari[$d]) }})"><i class="fa fa-money"></i></button>
                                    @php $a=0 @endphp
                                  @elseif($s->nomor_pnr != null)
                                    <button type="button" class="btn btn-primary btn-sm" style="background-color: #6E00FF; border-color: #6E00FF"  title="Bukti Kas" onclick="cetak_bukti_kas({{$s->spp_id}},0,{{$s->sppn_id}},1,0,{{ json_encode($sppn_cetak_bukti_kas[$d]) }},{{ json_encode($datapenerima[$d]) }},{{ json_encode($data_diterima_dari[$d]) }})"><i class="fa fa-money"></i></button>
                                    @php $a=1 @endphp
                                  @endif
                                @elseif(isset($s->sppb_no) && isset($s->sppn_no))
                                  @if($s->nomor_byr == null && $s->nomor_pnr == null)
                                    <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onclick="cetak_bukti_kas(0,{{$s->sppb_id}},{{$s->sppn_id}},2,0,0,{{ json_encode($datapenerima[$d]) }},{{ json_encode($data_diterima_dari[$d]) }})"><i class="fa fa-money"></i></button>
                                    @php $a=0 @endphp
                                  @elseif($s->nomor_byr != null && $s->nomor_pnr == null)
                                    <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onclick="cetak_bukti_kas({{$s->spp_id}},{{$s->sppb_id}},{{$s->sppn_id}},2,{{ json_encode($sppb_cetak_bukti_kas[$d]) }},0,{{ json_encode($datapenerima[$d]) }},{{ json_encode($data_diterima_dari[$d]) }})"><i class="fa fa-money"></i></button>
                                    @php $a=0 @endphp
                                  @elseif($s->nomor_byr == null && $s->nomor_pnr != null)
                                 <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onclick="cetak_bukti_kas({{$s->spp_id}},{{$s->sppb_id}},{{$s->sppn_id}},2,0,{{ json_encode($sppn_cetak_bukti_kas[$d]) }},{{ json_encode($datapenerima[$d]) }},{{ json_encode($data_diterima_dari[$d]) }})"><i class="fa fa-money"></i></button>
                                    @php $a=0 @endphp
                                  @elseif($s->nomor_byr != null && $s->nomor_pnr != null)
                                  <button type="button" class="btn btn-primary btn-sm" style="background-color: #6E00FF; border-color: #6E00FF" title="Bukti Kas" onclick="cetak_bukti_kas({{$s->spp_id}},{{$s->sppb_id}},{{$s->sppn_id}},2,{{ json_encode($sppb_cetak_bukti_kas[$d]) }},{{ json_encode($sppn_cetak_bukti_kas[$d]) }},{{ json_encode($datapenerima[$d]) }},{{ json_encode($data_diterima_dari[$d]) }})"><i class="fa fa-money"></i></button>                              
                                      @php $a=1 @endphp
                                  @endif
                                @endif
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>

                            </td>
                          </tr>
                          @endif
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sedang Proses --}}

                    {{-- Panel Sudah Selesai --}}
                    <div class="tab-pane fade" id="tab-sudah-selesai-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(2,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_selesai as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Selesai</td>
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_selesai[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                            </td>
                          </tr>
                        @endforeach
                          
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sudah Selesai --}}

                    {{-- Panel Dibatalkan --}}
                    <div class="tab-pane fade" id="tab-dibatalkan-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(3,1)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_batal as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Dibatalkan ( {{$posisi_batal[$d]->master_hak_akses_keterangan}} )</td>
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_batal[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                            </td>
                          </tr>
                        @endforeach
                          
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Dibatalkan --}}

                  </div>
                </div>
              </div>
              {{-- END TAB PETUGAS KAS BANK --}}
              @endif

              @if($hakakses == 9)
              {{-- TAB PETUGAS PEMBAYARAN --}}
              <div class="tab-pane fade in active" id="tab-petugas-pembayaran">
                <div class="panel-heading">
                  <h3 class="panel-title">Tabel SPPb / SPPn</h3>
                </div>
                <div class="panel-body">
                  <div class="custom-tabs-line tabs-line-bottom left-aligned">
                    <ul class="nav" role="tablist">
                    <li id="tab-sedang-proses"><a href="#tab-sedang-proses-petugas" role="tab" data-toggle="tab">Sedang Proses</a></li>
                      <li id="tab-sudah-selesai"><a href="#tab-sudah-selesai-petugas" role="tab" data-toggle="tab">Sudah Selesai</a></li>
                      <li id="tab-dibatalkan"><a href="#tab-dibatalkan-petugas" role="tab" data-toggle="tab">Dibatalkan</a></li>
                    </ul>
                  </div>
                  <div class="tab-content">

                    {{-- Panel Sedang Proses --}}
                    <div class="tab-pane fade in active" id="tab-sedang-proses-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(1,2)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2">No. </th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Status Pembayaran</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $n=1 @endphp
                        @foreach($data as $d => $s)
                        @if($s->spp_status_proses < 13 && $s->spp_status_ob < 3 && $s->spp_status_ob != 2 && $status_revisi[$d] == 'Revisi oleh')
                          <tr>
                            <td><strong>{{$n++}}</strong></td>
                            <td><strong>{{$s->master_bagian_nama}}</strong></td>
                            <td><strong>{{date('d-m-Y',strtotime($s->tanggal))}}</strong></td>
                            <td><strong>{{$s->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppb_total)}}</strong></td>
                            <td><strong>{{$s->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
                            <td><strong>{{$posisi[$d]->master_hak_akses_keterangan}}</strong></td>
                            @if($s->spp_status_proses == 12 && ($sppb_cetak_bukti_kas[$d] != null || $sppn_cetak_bukti_kas[$d] != null) )
                              @if(isset($s->sppb_no) && empty($s->sppn_no))
                                @if($s->spp_status_bayar == 0)
                                  <td><button type="button" class="btn btn-warning btn-sm" onclick="pembayaran({{$s->sppb_id}},0,0,0,0)">Belum Dibayar</button></td>
                                  @php $a=0 @endphp
                                @elseif($s->spp_status_bayar == 1)
                                  <td><button type="button" class="btn btn-success btn-sm" onclick="pembayaran({{$s->sppb_id}},0,0,{{ json_encode($sppb_bayar[$d]) }},0)" >Sudah Dibayar</button></td>
                                  @php $a=1 @endphp
                                @endif
                              @elseif(empty($s->sppb_no) && isset($s->sppn_no))
                                @if($s->spp_status_terima == 0)
                                  <td><button type="button" class="btn btn-warning btn-sm" onclick="pembayaran(0,{{$s->sppn_id}},1,0,0)">Belum Dibayar</button></td>
                                  @php $a=0 @endphp
                                @elseif($s->spp_status_terima == 1)
                                  <td><button type="button" class="btn btn-success btn-sm" onclick="pembayaran(0,{{$s->sppn_id}},1,0,{{ json_encode($sppn_terima[$d]) }})" >Sudah Dibayar</button></td>

                                  @php $a=1 @endphp
                                @endif
                              @elseif(isset($s->sppb_no) && isset($s->sppn_no))
                                @if($s->spp_status_bayar == 0 && $s->spp_status_terima == 0)
                                  <td><button type="button" class="btn btn-warning btn-sm" onclick="pembayaran({{$s->sppb_id}},{{$s->sppn_id}},2,{{ json_encode($sppb_bayar[$d]) }},{{ json_encode($sppn_terima[$d]) }})">Belum Dibayar</button></td>
                                  @php $a=0 @endphp
                                @elseif($s->spp_status_bayar == 1 && $s->spp_status_terima == 0)
                                  <td><button type="button" class="btn btn-warning btn-sm" onclick="pembayaran({{$s->sppb_id}},{{$s->sppn_id}},2,{{ json_encode($sppb_bayar[$d]) }},{{ json_encode($sppn_terima[$d]) }})">Belum Dibayar</button></td>
                                  @php $a=0 @endphp
                                @elseif($s->spp_status_bayar == 0 && $s->spp_status_terima == 1)
                                  <td><button type="button" class="btn btn-warning btn-sm" onclick="pembayaran({{$s->sppb_id}},{{$s->sppn_id}},2,{{ json_encode($sppb_bayar[$d]) }},{{ json_encode($sppn_terima[$d]) }})">Belum Dibayar</button></td>
                                  @php $a=0 @endphp
                                @elseif($s->spp_status_bayar == 1 && $s->spp_status_terima == 1)
                                  <td><button type="button" class="btn btn-success btn-sm" onclick="pembayaran({{$s->sppb_id}},{{$s->sppn_id}},2,{{ json_encode($sppb_bayar[$d]) }},{{ json_encode($sppn_terima[$d]) }})" >Sudah Dibayar</button></td>                              
                                  @php $a=1 @endphp
                                @endif
                              @endif

                            @elseif($s->spp_status_proses == 12 && ($sppb_cetak_bukti_kas[$d] == null || $sppn_cetak_bukti_kas[$d] == null))
                              <td><button type="button" class="btn btn-danger btn-sm" disabled>Data Bukti Kas Kosong</button></td>
                               @php $a=0 @endphp
                              @elseif($s->spp_status_proses == 11)
                              <td><button type="button" class="btn btn-default btn-sm" disabled>Belum Dibayar</button></td>

                            @endif
                            <td>
                            @if($s->spp_status_proses == 11)
                              <a class="btn btn-success btn-sm" onclick="terima({{$s->spp_id}})" title="Terima" ><i class="fa fa-check"></i></a>                            
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                          @elseif($s->spp_status_proses == 12)
                              @if($a==1)
                              @if($s->spp_bukti_kas_bank !== null)
                              <button type="button" class="btn btn-success btn-sm" onclick="selesai({{$s->spp_id}})" title="Selesai"  ><i class="fa fa-arrow-right"></i></button>
                              @else
                              <button type="button" class="btn btn-success btn-sm" onclick="selesai({{$s->spp_id}})" title="Selesai" disabled ><i class="fa fa-arrow-right"></i></button>
                              
                              @endif
                              <button type="button" class="btn btn-warning btn-sm" onclick="upload_bukti_kas({{$s->spp_id}},'{{$s->spp_bukti_kas_bank}}')" ><i class="fa fa-upload"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="window.open('{{ url('spp/cetak_bukti_kas/'.$s->spp_id) }}').print();" title="Bukti Kas"><i class="fa fa-money"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                              @else
                              <button type="button" class="btn btn-warning btn-sm" onclick="revisi({{$s->spp_id}})" title="Revisi" ><i class="fa fa-arrow-left"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="window.open('{{ url('spp/cetak_bukti_kas/'.$s->spp_id) }}').print();" title="Bukti Kas"><i class="fa fa-money"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            <!--   <button type="button" class="btn btn-danger btn-sm" onclick="batal({{$s->spp_id}})" title="Batalkan" ><i class="fa fa-ban" aria-hidden="true"></i></button> -->

                              @endif
                              
                            @endif
                              
                              
                            </td>
                          </tr>
                          @endif
                        @endforeach
                        @foreach($data as $d => $s)
                        @if($s->spp_status_proses < 13 && $s->spp_status_ob == 2 && $status_revisi[$d] == 'Revisi oleh')
                          <tr>
                            <td><strong>{{$n++}}</strong></td>
                            <td><strong>{{$s->master_bagian_nama}}</strong></td>
                            <td><strong>{{date('d-m-Y',strtotime($s->tanggal))}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$s->sppb_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppb_total)}}</strong></td>
                            <td style="background-color: red; color: white;"><strong>{{$s->sppn_no}}</strong></td>
                            <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</strong></td>
                            <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
                            <td><strong>{{$posisi[$d]->master_hak_akses_keterangan}}</strong></td>
                            @if($s->spp_status_proses == 12)
                              @if(isset($s->sppb_no) && empty($s->sppn_no))
                                @if($s->spp_status_bayar == 0)
                                  <td><button type="button" class="btn btn-warning btn-sm" onclick="pembayaran({{$s->sppb_id}},0,0,0,0)">Belum Dibayar</button></td>
                                  @php $a=0 @endphp
                                @elseif($s->spp_status_bayar == 1)
                                  <td><button type="button" class="btn btn-success btn-sm" onclick="pembayaran({{$s->sppb_id}}0,0,{{ json_encode($sppb_bayar[$d]) }},0)">Sudah Dibayar</button></td>
                                  @php $a=1 @endphp
                                @endif
                              @elseif(isset($s->sppn_no) && empty($s->sppb_no))
                              
                                @if($s->spp_status_terima == 0)
                                  <td><button type="button" class="btn btn-warning btn-sm" onclick="pembayaran({{$s->sppb_id}},{{$s->sppn_id}},2,{{ json_encode($sppb_bayar[$d]) }},{{ json_encode($sppn_terima[$d]) }})">Belum Dibayar</button></td>
                                  @php $a=0 @endphp
                                @elseif($s->spp_status_terima == 1)
                                  <td><button type="button" class="btn btn-success btn-sm" onclick="pembayaran({{$s->sppb_id}},{{$s->sppn_id}},2,{{ json_encode($sppb_bayar[$d]) }},{{ json_encode($sppn_terima[$d]) }})">Sudah Dibayar</button></td>
                                  @php $a=1 @endphp
                                @endif
                              @elseif(isset($s->sppb_no) && isset($s->sppn_no))
                                @if($s->spp_status_bayar == 0 && $s->spp_status_terima == 0)
                                  <td><button type="button" class="btn btn-warning btn-sm" onclick="pembayaran({{$s->sppb_id}},{{$s->sppn_id}},2,{{ json_encode($sppb_bayar[$d]) }},{{ json_encode($sppn_terima[$d]) }})">Belum Dibayar</button></td>
                                  @php $a=0 @endphp
                                @elseif($s->spp_status_bayar == 1 && $s->spp_status_terima == 0)
                                  <td><button type="button" class="btn btn-warning btn-sm" onclick="pembayaran({{$s->sppb_id}},{{$s->sppn_id}},2,{{ json_encode($sppb_bayar[$d]) }},{{ json_encode($sppn_terima[$d]) }})">Belum Dibayar</button></td>
                                  @php $a=0 @endphp
                                @elseif($s->spp_status_bayar == 0 && $s->spp_status_terima == 1)
                                  <td><button type="button" class="btn btn-warning btn-sm" onclick="pembayaran({{$s->sppb_id}},{{$s->sppn_id}},2,{{ json_encode($sppb_bayar[$d]) }},{{ json_encode($sppn_terima[$d]) }})">Belum Dibayar</button></td>
                                  @php $a=0 @endphp
                                @elseif($s->spp_status_bayar == 1 && $s->spp_status_terima == 1)
                                  <td><button type="button" class="btn btn-success btn-sm" onclick="pembayaran({{$s->sppb_id}},{{$s->sppn_id}},2,{{ json_encode($sppb_bayar[$d]) }},{{ json_encode($sppn_terima[$d]) }})">Sudah Dibayar</button></td>                              
                                  @php $a=1 @endphp
                                @endif
                              @endif
                            @elseif($s->spp_status_proses <= 11)
                              <td><button type="button" class="btn btn-default btn-sm" disabled>Belum Dibayar</button></td>
                      
                            @endif
                            <td>
                            @if($s->spp_status_proses == 11)
                              <a class="btn btn-success btn-sm" onclick="terima({{$s->spp_id}})" title="Terima" ><i class="fa fa-check"></i></a>                            
                            @elseif($s->spp_status_proses == 12)
                              @if($a==1)
                              @if($s->spp_bukti_kas_bank !== null)
                              <button type="button" class="btn btn-success btn-sm" onclick="selesai({{$s->spp_id}})" title="Selesai" ><i class="fa fa-arrow-right"></i></button>
                              @else
                              <button type="button" class="btn btn-success btn-sm" onclick="selesai({{$s->spp_id}})" title="Selesai" ><i class="fa fa-arrow-right" disabled></i></button>
                              @endif
                              <button type="button" class="btn btn-warning btn-sm" onclick="upload_bukti_kas({{$s->spp_id}},'{{$s->spp_bukti_kas_bank}}')" ><i class="fa fa-upload"></i></button>
                              @else
                              <button type="button" class="btn btn-warning btn-sm" onclick="revisi({{$s->spp_id}})" title="Revisi" ><i class="fa fa-arrow-left"></i></button>
                              <button type="button" class="btn btn-danger btn-sm" onclick="batal({{$s->spp_id}})" title="Batalkan" ><i class="fa fa-ban" aria-hidden="true"></i></button>

                              @endif
                            @endif
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="window.open('{{ url('spp/cetak_bukti_kas/'.$s->spp_id) }}').print();" title="Bukti Kas"><i class="fa fa-money"></i></button>
                              <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                            </td>
                          </tr>
                          @endif
                        @endforeach
                        
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sedang Proses --}}

                    {{-- Panel Sudah Selesai --}}
                    <div class="tab-pane fade" id="tab-sudah-selesai-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(2,2)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_selesai as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Selesai</td>
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <!-- <button type="button" class="btn btn-warning btn-sm" onclick="upload_bukti_kas({{$s->spp_id}},'{{$s->spp_bukti_kas_bank}}')" ><i class="fa fa-upload"></i></button> -->
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_selesai[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                            
                            </td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Sudah Selesai --}}

                    {{-- Panel Dibatalkan --}}
                    <div class="tab-pane fade" id="tab-dibatalkan-petugas">
                    <button class="btn btn-primary" onclick="advanced_search(3,2)" style="margin-bottom: 15px">Advanced Search</button>
                      <table class="table table-bordered table-striped nowrap" style="width: 100%">
                        <thead>
                          <tr>
                            <th rowspan="2"><center>No. </center></th>
                            <th rowspan="2">Bagian</th>
                            <th rowspan="2">Tanggal SPP</th>
                            <th colspan="3">SPPb</th>
                            <th colspan="3">SPPn</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Action</th>
                          </tr>
                          <tr>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>No</th>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @php $a=1 @endphp
                        @foreach($data_batal as $d => $s)
                          <tr>
                            <td>{{$a++}}</td>
                            <td>{{$s->master_bagian_nama}}</td>
                            <td>{{date('d-m-Y',strtotime($s->tanggal))}}</td>
                            <td >{{$s->sppb_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppb_total)}}</td>
                            <td >{{$s->sppn_no}}</td>
                            <td>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),15)}}</td>
                            <td>Rp.{{number_format($s->sppn_jumlah)}}</td>
                            <td>Dibatalkan ( {{$posisi_batal[$d]->master_hak_akses_keterangan}} )</td>
                            <td>
                              <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                              <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_batal[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button>
                            </td>
                          </tr>
                        @endforeach
                          
                        </tbody>
                      </table>
                    </div>
                    {{-- End Panel Dibatalkan --}}

                  </div>
                </div>
              </div>
              {{-- END TAB PETUGAS PEMBAYARAN --}}
              @endif
            <!-- </div> -->
            <!-- END TAB CONTENT -->
          </div>
          <!-- END TABLE -->

        </div>
      </div>
    </div>
  </div>
  <!-- END MAIN CONTENT -->
</div>
<!-- END MAIN -->

<!-- Modal -->

{{-- Modal ADVANCED SEARCH --}}
<div id="modal_advanced_search" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
    <form action="{{route('advanced_search_spp')}}" method="post" id="form_advanced_search" enctype="multipart\form-data">
      {{csrf_field()}}
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Advanced Search</h4>
          <input type="hidden" id="index_advanced_search" name="index_advanced_search" value="">
        </div>
        <div class="modal-body">
        @if($level == 1)
          <div class="form-group" id="advanced_search_bagian">
            <label>Bagian :</label><br>
            <select class="form-control" name="bagian">
      
              <option value="{{$bagian_id->master_bagian_id}}">{{$bagian_id->master_bagian_nama}}</option>
         
            </select>
          </div>
        @else
        <div class="form-group" id="advanced_search_bagian">
            <label>Bagian :</label><br>
            <select class="form-control" name="bagian">
              <option value="semua">Tampilkan Semua</option>
              @foreach($b as $key => $value)
              <option value="{{$value->master_bagian_id}}">{{$value->master_bagian_nama}}</option>
              @endforeach
            </select>
          </div>
        @endif
        <div class="form-group" id="advanced_search_bagian">
            <label>Vendor :</label><br>
            <select class="form-control" name="vendor">
              <option value="semua">Tampilkan Semua</option>

            </select>
          </div>
          <div class="form-group" id="advanced_search_rentang_waktu">
            <label>Rentang Waktu :</label><br>
            <input type="text" class="form-control date-range" name="rentang_waktu">
          </div>

          <div class="form-group" id="advanced_search_posisi_terkini">
            <label>Posisi Terkini :</label><br>
            <select class="form-control" name="posisi_terkini">
              <option value="semua">Tampilkan Semua</option>
              <option value="1">Operator</option>
              <option value="2">Petugas Penerimaan</option>
              <option value="3">Petugas Pajak</option>
              <option value="4">Petugas SAP Miro</option>
              <option value="5">Petugas Verifikasi</option>
              <option value="6">Petugas Kas dan Bank</option>
              <option value="7">Petugas Pembayaran</option>
            </select>
          </div>
          <div class="form-group" id="advanced_search_status_bayar1">
            <label>Status Bayar :</label><br>
            <select class="form-control" name="status_bayar">
            <option value="semua">Tampilkan Semua</option>
              <option value="0" >Belum Dibayar</option>
              <option value="1">Sudah Dibayar</option>
            </select>
          </div>
          <div class="form-group" id="advanced_search_status_bayar2">
            <label>Status Bayar :</label><br>
            <select class="form-control" name="status_bayar">
            <option value="semua">Tampilkan Semua</option>
              <option value="0" >Belum Dibayar</option>
              <option value="1">Sudah Dibayar</option>
              <option value="2">Dibatalkan</option>
            </select>
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
{{-- End Modal ADVANCED SEARCH --}}

{{-- Modal PEMBAYARAN --}}
<div id="modal_pembayaran" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
    <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
              <div class="custom-tabs-line tabs-line-bottom left-aligned">
                  <ul class="nav" role="tablist">
                      <li id="tab_bayar"><a href="#tab-pembayaran" role="tab" data-toggle="tab">Pembayaran</a></li>
                      <li id="tab_terima"><a href="#tab-penerimaan" role="tab" data-toggle="tab">Penerimaan</a></li>
                  </ul>
            </div>
          </div>
    
    <div class="tab-content">
      <div class="tab-pane fade in" id="tab-pembayaran">
      
        <form action="" id="form-bayar" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        <input type="hidden" id="form_a" name="form_a">

        <input type="hidden" id="id_sppb_bayar" name="id_sppb_bayar">
        <div id="bayar_sppb">
          <div class="modal-body">
            <div class="form-group" hidden>
              <label>Nomor Bukti Kas :</label><br>
              <input type="text" class="form-control" placeholder="Nomor bukti kas pengeluaran" id="nomor_bukti_kas_sppb" name="nomor_bukti_kas_sppb" maxlength="10" value="-">
            </div>
            <div class="form-group">
              <label>Tanggal Pembayaran :</label><br>
              <input type="text" class="form-control date" placeholder="Tanggal pembayaran" id="tanggal_bayar_sppb" name="tanggal_bayar_sppb" value="{{DATE('d-m-Y')}}" required>
            </div>
            <div class="form-group" hidden>
              <label>Kode Rekening :</label><br>
              <input type="text" class="form-control" value="-" id="rekening_sppb" placeholder="Kode rekening" onclick="kode_rekening_sppb()" >
              <input type="hidden" id="rekening_sppb_1" name="rekening_bank_sppb" >
              <input type="hidden" id="kode_kbb_bayar_sppb" name="sppb_kode_kbb_bayar"> 
              <input type="hidden" id="kode_sap_bayar_sppb" name="sppb_kode_sap_bayar"> 
            </div>
            <div class="form-group">
              <label>Bukti Transfer : </label><br>
              <div id="bukti_transfer_sppb">
              <input type="file"  name="bukti_sppb" class="file" accept="application/pdf, image/*" placeholder="Bukti Transfer" autocomplete="off" required>
              </div>
              
              <a href="#" target="_blank" id="bukti_sppb"></a>
              <button type="button" class="btn btn-warning btn-sm" id="remove_bukti_sppb" onclick="hapus_bukti_sppb()" ><i class="fa fa-pencil" aria-hidden="true"></i></button>
             
            </div>
          </div>
        </div>
        <div id="footer_submit_sppb" class="modal-footer">
          <button type="submit" class="btn btn-success">Submit</button>
          <button type="button" class="btn btn-danger" onclick="clear_spp_bayar(0)">Clear</button>
        </div>
        <div id="footer_edit_sppb" class="modal-footer">
          <button type="button" class="btn btn-warning" onclick="edit_bayar_sppb(0)">Edit</button>
          
        </div>
        </form>
      </div>
      <div class="tab-pane fade in" id="tab-penerimaan">
        <form action="" id="form-terima" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        <input type="hidden" id="form_b" name="form_b">

        <div id="bayar_sppn">
          <div class="modal-body">
          <input type="hidden" id="id_sppn_terima" name="id_sppn_terima">
            <div class="form-group" hidden>
              <label>Nomor Bukti Kas :</label><br>
              <input type="text" class="form-control" value="-" placeholder="Nomor bukti kas penerimaan" id="nomor_bukti_kas_sppn" name="nomor_bukti_kas_sppn" maxlength="10" >
            </div>
            <div class="form-group">
              <label>Tanggal Penerimaan :</label><br>
              <input type="text" class="form-control date" id="tanggal_terima_sppn" name="tanggal_terima_sppn" value="{{DATE('d-m-Y')}}" required>
            </div>
            <div class="form-group" hidden>
              <label>Kode Rekening :</label><br>
              <input type="text" class="form-control" value="-"id="rekening_sppn" placeholder="Kode rekening" onclick="kode_rekening_sppn()" >
              <input type="hidden" id="rekening_sppn_1" name="rekening_bank_sppn"> 
              <input type="hidden" id="sppn_kode_kbb_terima" name="sppn_kode_kbb_terima"> 
              <input type="hidden" id="sppn_kode_sap_terima" name="sppn_kode_sap_terima">
            </div>
            <div class="form-group">
              <label>Bukti Transfer : </label><br>
              <div id="bukti_transfer_sppn">
                <input type="file" name="bukti_sppn" class="file" accept="application/pdf, image/*" placeholder="Bukti Transfer" autocomplete="off" required>
              </div>
              
              <a href="#" target="_blank" id="bukti_sppn"></a>
              <button type="button" class="btn btn-warning btn-sm" id="remove_bukti_sppn" onclick="hapus_bukti_sppn()" ><i class="fa fa-pencil" aria-hidden="true"></i></button>
             
            </div>
          </div>
        </div>
        <div id="footer_submit_sppn" class="modal-footer">
          <button type="submit" class="btn btn-success">Submit</button>
          <button type="button" class="btn btn-danger" onclick="clear_spp_bayar(1)">Clear</button>
        </div>
        <div id="footer_edit_sppn" class="modal-footer">
          <button type="button" class="btn btn-warning" onclick="edit_bayar_sppb(1)">Edit</button>
          
        </div>
      </form>
      </div>
    </div>
  </div>
</div>
</div>
{{-- End Modal PEMBAYARAN --}}
{{-- Modal cetak bukti kas --}}
<div id="modal_cetak_bukti_kas" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
    <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3>Cetak Bukti Kas</h3>
              <div class="custom-tabs-line tabs-line-bottom left-aligned">
                  <ul class="nav" role="tablist">
                      <li id="tab_bayar_cbk"><a href="#tab-pembayaran_cbk" role="tab" data-toggle="tab">SPPb</a></li>
                      <li id="tab_terima_cbk"><a href="#tab-penerimaan_cbk" role="tab" data-toggle="tab">SPPn</a></li>
                  </ul>
            </div>
          </div>
    
    <div class="tab-content">
      <div class="tab-pane fade in" id="tab-pembayaran_cbk">
      
        <form action="" id="form-bayar_cbk" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        <input type="hidden" id="form_a_cbk" name="form_a" value="">

        <input type="hidden" id="id_sppb_bayar_cbk" name="id_sppb_bayar">
        <div id="bayar_sppb_cbk">
          <div class="modal-body">
            <div class="form-group">
              <label>Nomor Cek/Giro :</label><br>
              <input type="text" class="form-control" id="nomor_bukti_kas_sppb_cbk" name="nomor_bukti_kas_sppb" maxlength="10" placeholder="nomor cek/giro pembayaran" required>
            </div>
            <div class="form-group">
              <label>Kode Rekening :</label><br>
              <input type="text" class="form-control" id="rekening_sppb_cbk" onclick="kode_rekening_sppb()" placeholder="rekening pembayaran" autocomplete="off" required>
              <input type="hidden" id="rekening_sppb_1_cbk" name="rekening_bank_sppb"> 
            </div>
            <div class="form-group">
              <label>Penerima :</label><br>
              <input type="text" class="form-control" id="penerima_cbk" name="penerima" autocomplete="off" placeholder="Penerima" required>
            </div>
            <div class="form-group">
              <label>Alamat :</label><br>
              <input type="text" class="form-control" id="alamat_penerima_cbk" name="alamat_sppb" autocomplete="off" placeholder="Penerima" required>
            </div>
          
            
            
          </div>
        </div>
        <div id="footer_submit_sppb_cbk" class="modal-footer">
          <button type="submit" class="btn btn-success pisan">Submit</button>
          <button type="button" class="btn btn-danger pisan" onclick="clear_spp_bayar(0)">Clear</button>
        </div>
        <div id="footer_edit_sppb_cbk" class="modal-footer">
          <!-- <button type="button" class="btn btn-warning" onclick="edit_bayar_sppb(0)">Edit</button> -->
          <button type="button" id="cetakbuktikas" class="btn btn-success cetakbuktikas" value="">Cetak Bukti Kas</button>

          
        </div>
        </form>
      </div>
      <div class="tab-pane fade in" id="tab-penerimaan_cbk">
        <form action="" id="form-terima_cbk" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        <input type="hidden" id="form_b_cbk" name="form_b" value="">
        <div id="bayar_sppn">
          <div class="modal-body">
          <input type="hidden" id="id_sppn_terima_cbk" name="id_sppn_terima">
            <div class="form-group">
              <label>Nomor Cek/Giro :</label><br>
              <input type="text" class="form-control" id="nomor_bukti_kas_sppn_cbk" name="nomor_bukti_kas_sppn" maxlength="10" placeholder="nomor cek/giro penerimaan" required>
            </div>
            <div class="form-group">
              <label>Kode Rekening :</label><br>
              <input type="text" class="form-control" autocomplete="off" id="rekening_sppn_cbk" onclick="kode_rekening_sppn()" placeholder="rekening penerimaan" required>
              <input type="hidden" id="rekening_sppn_1_cbk" name="rekening_bank_sppn"> 
            </div>
            <div class="form-group">
              <label>Diterima Dari :</label><br>
              <input type="text" class="form-control" autocomplete="off" name="diterima_dari" id="diterima_dari_cbk"placeholder=" Diterima Dari" required>

            </div>
            <div class="form-group">
              <label>Alamat :</label><br>
              <input type="text" class="form-control" autocomplete="off" id="alamat_diterima_dari_cbk" name="alamat_sppn" placeholder=" Diterima Dari" required> 
            </div>
         
           
            
          </div>
        </div>
        <div id="footer_submit_sppn_cbk" class="modal-footer">
          <button type="submit" class="btn btn-success pisan">Submit</button>
          <button type="button" class="btn btn-danger pisan" onclick="clear_spp_bayar(1)">Clear</button>
        </div>
        <div id="footer_edit_sppn_cbk" class="modal-footer">
          <!-- <button type="button" class="btn btn-warning" onclick="edit_bayar_sppb(1)">Edit</button> -->
          <button type="button" id="cetakbuktikas" class="btn btn-success cetakbuktikas"  value="">Cetak Bukti Kas</button>
          
        </div>
      </form>
      </div>
    </div>
  </div>
  </div>
</div>
{{-- End Modal cetak bukti kas --}}
{{-- Modal Detail PEMBAYARAN --}}
<div id="modal_detil_pembayaran" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <form action="" id="form-detil-bayar" method="post" enctype="multipart/form-data">
      {{csrf_field()}}
      <input type="hidden" id="form" name="form">
      <div id="detil_bayar_sppb" style="display:none">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Pembayaran</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nomor Bukti Kas :</label><br>
            <input type="text" class="form-control" id="detil_nomor_bukti_kas_sppb" disabled>
          </div>
          <div class="form-group">
            <label>Kode Rekening :</label><br>
            <input type="text" class="form-control" id ="detil_rekening_sppb" disabled>
          </div>
          <div class="form-group">
            <label>Bukti Transfer : </label><br>
            <a href="#" target="_blank" id="detil_bukti_sppb"></a>
            <!-- <input type="file" id="bukti_transfer" name="bukti_sppb" class="file" accept="application/pdf, image/*" placeholder="Bukti Transfer" autocomplete="off"> -->
          </div>
        </div>
      </div>
      <div id="detil_bayar_sppn" style="display:none">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Penerimaan</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nomor Bukti Kas :</label><br>
            <input type="text" class="form-control" id="detil_nomor_bukti_kas_sppn" disabled>
          </div>
          <div class="form-group">
            <label>Kode Rekening :</label><br>
            <input type="text" class="form-control" id="detil_rekening_sppn" disabled>
          </div>
          <div class="form-group">
            <label>Upload Bukti Transfer : </label><br>
            <a href="#" target="_blank" id="detil_bukti_sppn"></a>          
            </div>
        </div>
      </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
{{-- End Modal Detail PEMBAYARAN --}}

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
          
          <div class="form-group" id="pilih_file_spp" onclick="pilih_file()">
            <label class="fancy-radio">
              <input  name="upload_file" id="file_lama" value="file_lama" type="radio" checked="checked" > 
              <span style="font-size:17px"><i ></i>Gunakan File Lama <a href="" id="file_file_lama" target="_blank">(lihat)</a></span>
            </label>
            <label class="fancy-radio">
              <input  name="upload_file" id="file_baru" value="file_baru" type="radio"> 
              <span style="font-size:17px"><i ></i>Upload File Baru</span>
            </label>
          </div>
          
          <div class="form-group" id="upload_file_baru" style="display:none">
            <label>Upload File SPP yang sudah di TTD Kepala Bagian :</label><br>
            <input type="file" id="spp_kabag" name="spp_kabag" class="file" accept="application/pdf, image/*" placeholder="SPP tanda tangan Kabag" autocomplete="off" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" id="pisanae" class="btn btn-success">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
{{-- End Modal KIRIM --}}

{{-- Modal Upload Dokumen Tambahan --}}
<div id="modal_dokumen_pendukung" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{route('storedokumentambahan')}}" id="form_dokumen_tambahan" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Upload Dokumen Tambahan SPP</h4>
        </div>
        <input type="hidden" id="spp_id" name="spp_id">
        <div class="modal-body">
          <div class="form-group">
            <label>Upload File Dokumen Tambahan :</label><br>
            <span style="color: red">*Format Gambar/PDF</span>
            <input type="file" class="file-multiple" name="dokumen_tambahan[]" accept="application/pdf, image/*" multiple>
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
{{-- End Modal Upload Dokumen Tambahan --}}

{{-- Modal Upload Bukti Kas/Bank --}}
<div id="modal_bukti_kas" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{route('storebuktikas')}}" id="form_upload_bukti_kas" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Upload Bukti Kas/Bank</h4>
        </div>
        <input type="hidden" id="bukti_spp_id" name="bukti_spp_id">
        <div class="modal-body">
          <div class="form-group" id="upload_bukti">
            <label>Upload File Bukti Kas/Bank :</label><br>
            <span style="color: red">*Format Gambar/PDF</span>
            <input type="file" class="file" name="file_bukti_kas" accept="application/pdf, image/*" required>
          </div>
          <div class="form-group" id="file_bukti_kas">
              <a href="#" target="_blank" id="bukti_kas_lama"></a>
              <button type="button" class="btn btn-warning btn-sm" id="remove_bukti_kas" onclick="edit_bukti_kas()" ><i class="fa fa-pencil" aria-hidden="true"></i></button>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" id="submit_bukti" class="btn btn-success">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
{{-- End Modal Upload Bukti Kas/Bank --}}

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
            <label>Keterangan Revisi SPP :</label><br>
            <textarea class="form-control" id="keterangan_revisi" name="revisi" placeholder="Keterangan Revisi" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" onclick="confirm_revisi()">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
{{-- End Modal REVISI --}}

{{-- Modal Rekam Jejak --}}
<div id="modal_rekam_jejak" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modal Rekam Jejak</h4>
      </div>
      <div class="modal-body">
    
        <ul class="timeline" id="rekam_jejak_body">
        
          
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
{{-- End Modal Rekam Jejak --}}
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
{{-- Modal Penerima --}}
<div id="modal_penerima" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:800px">
    <!-- Modal content-->
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Pilih Penerima SPPb</h4>
        </div>
        <div class="modal-body">
          <table class="table table-bordered table-striped nowrap" style="width: 100%">
            <thead>
              <tr>
                <th style="display:none">id</th>
                <th>No. </th>
                <th>Nama Bank</th>
                <th>Alamat</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($vendor as $key => $value)
              <tr>
                <td style="display:none">{{$value->master_vendor_id}}</td>
                <td>{{$key+1}}</td>
                <td>{{$value->master_vendor_nama_bank}}</td>
                <td>(Alamat Kosong)</td>
                <td>
                  <button type="button" class="btn btn-info btn-sm" onclick="pilih_penerima('{{$value->master_vendor_id}}','{{$value->master_vendor_nama_bank}}',0)" title="Pilih" ><i class="fa fa-check"></i></button>
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
{{-- End Modal Penerima --}}

{{-- Modal Diterima dari --}}
<div id="modal_diterima_dari" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:800px">
    <!-- Modal content-->
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Pilih Penerima SPPb</h4>
        </div>
        <div class="modal-body">
          <table class="table table-bordered table-striped nowrap" style="width: 100%">
            <thead>
              <tr>
                <th style="display:none">id</th>
                <th>No. </th>
                <th>Nama Bank</th>
                <th>Alamat</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($vendor as $key => $value)
              <tr>
                <td style="display:none">{{$value->master_vendor_id}}</td>
                <td>{{$key+1}}</td>
                <td>{{$value->master_vendor_nama_bank}}</td>
                <td>(Alamat Kosong)</td>
                <td>
                  <button type="button" class="btn btn-info btn-sm" onclick="pilih_diterima_dari('{{$value->master_vendor_id}}','{{$value->master_vendor_nama_bank}}',0)" title="Pilih" ><i class="fa fa-check"></i></button>
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
{{-- End Modal Diterima dari --}}
<!-- End Modal -->

<script type="text/javascript">
var index_adv = {{$index}};

var index_cetak = {{$index_cetak}};
var id_cetak = {{$id_cetak}};
  
  
  
  $(document).ready(function(){
    if(index_cetak == 1){
      var url = 'spp/cetak/'+id_cetak;
      setTimeout(() => $('<a href="'+url+'" target="_blank"></a>')[0].click(), 500);
    }
    //window.alert(index_cetak);
    if (index_adv == 1){
      document.getElementById("tab-sedang-proses").className = "active";
      document.getElementById("tab-sedang-proses-petugas").className = "tab-pane fade in active";
      document.getElementById("tab-sudah-selesai").className = "";
      document.getElementById("tab-sudah-selesai-petugas").className = "tab-pane fade";
      document.getElementById("tab-dibatalkan").className = "";
      document.getElementById("tab-dibatalkan-petugas").className = "tab-pane fade";     
    }
    else if (index_adv == 2){
      document.getElementById("tab-sudah-selesai").className = "active";
      document.getElementById("tab-sudah-selesai-petugas").className = "tab-pane fade in active";
      document.getElementById("tab-sedang-proses").className = "";
      document.getElementById("tab-sedang-proses-petugas").className = "tab-pane fade";     
    }
    else if(index_adv == 3){
      document.getElementById("tab-dibatalkan").className = "active";
      document.getElementById("tab-dibatalkan-petugas").className = "tab-pane fade in active";
      document.getElementById("tab-sedang-proses").className = "";
      document.getElementById("tab-sedang-proses-petugas").className = "tab-pane fade";
      document.getElementById("tab-sudah-selesai").className = "";
      document.getElementById("tab-sudah-selesai-petugas").className = "tab-pane fade";
    }
    else{
      document.getElementById("tab-sedang-proses").className = "active";
      document.getElementById("tab-sedang-proses-petugas").className = "tab-pane fade in active";
      document.getElementById("tab-sudah-selesai").className = "";
      document.getElementById("tab-sudah-selesai-petugas").className = "tab-pane fade";
      document.getElementById("tab-dibatalkan").className = "";
      document.getElementById("tab-dibatalkan-petugas").className = "tab-pane fade";     
    }
  });

  $(document).ready(function() {
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
      // uploadUrl: ['#'],
      allowedFileTypes: ["image", "pdf"],
      browseClass: "btn btn-primary btn-block",
      maxFileSize: 55000,
      showCaption: false,
      showRemove: false,
      showUpload: false,
      dropZoneTitle:"Drag & drop banyak file sekaligus disini..",
      fileActionSettings: {
        showRemove: true,
        showUpload: false
      }
    });
  });

  function advanced_search(index,user){
    $('#index_advanced_search').val(index);
    if (index == 1) {
      $('#advanced_search_rentang_waktu').show();
      
      if(user == 1){
        $('#advanced_search_posisi_terkini').hide();
        $('#advanced_search_status_bayar1').hide();

      }
      else{
        $('#advanced_search_posisi_terkini').show();
        $('#advanced_search_status_bayar1').show();

      }
      $('#advanced_search_bagian').show();

      $('#advanced_search_status_bayar2').hide();
      $('#modal_advanced_search').modal('show');
    } else if(index == 2){
      $('#advanced_search_bagian').show();
      $('#advanced_search_rentang_waktu').show();
      $('#advanced_search_posisi_terkini').hide();
      $('#advanced_search_status_bayar1').hide();
      $('#advanced_search_status_bayar2').hide();
      $('#modal_advanced_search').modal('show');
    } else {
      $('#advanced_search_bagian').show();
      $('#advanced_search_rentang_waktu').show();
      $('#advanced_search_posisi_terkini').hide();
      $('#advanced_search_status_bayar1').hide();
      $('#advanced_search_status_bayar2').hide();
      $('#modal_advanced_search').modal('show');
    }
  }

  function clear_spp_bayar(id){
    if(id==0){
      document.getElementById('form-bayar').reset();
      $("#bukti_sppb").hide();
      $("#remove_bukti_sppb").hide();
      $("#bukti_transfer_sppb").show();
    }
    else{
      document.getElementById('form-terima').reset();
      $("#bukti_sppn").hide();
      $("#remove_bukti_sppn").hide();
      $("#bukti_transfer_sppn").show();
    }
  }

  function hapus_bukti_sppb(){
    $("#bukti_sppb").hide();
    $("#remove_bukti_sppb").hide();
    $("#bukti_transfer_sppb").show();
  }

  function hapus_bukti_sppn(){
    $("#bukti_sppn").hide();
    $("#remove_bukti_sppn").hide();
    $("#bukti_transfer_sppn").show();
  }
  $( ".cetakbuktikas" ).click(function() {
    var str = $(".cetakbuktikas").val();
     window.open('spp/cetak_bukti_kas/'+ str);
  });
  function edit_bayar_sppb(form){
    if(form == 0){
      var id = $("#id_sppb_bayar").val();
      $("#form").val(0);
      $("#form-bayar").attr('action', 'spp/update_bayar/'+ id);
      document.getElementById('nomor_bukti_kas_sppb').removeAttribute('readonly');
      document.getElementById('rekening_sppb').disabled = false;
      document.getElementById('tanggal_bayar_sppb').disabled = false;
      $("#bukti_transfer_sppb").hide();
      $("#bukti_sppb").show();
      $("#remove_bukti_sppb").show();
      $("#footer_submit_sppb").show();
      $("#footer_edit_sppb").hide();
    }
    else{
      $("#form").val(1);
      var id = $("#id_sppn_terima").val();
      $("#form-terima").attr('action', 'spp/update_bayar/'+ id);
      document.getElementById('nomor_bukti_kas_sppn').removeAttribute('readonly');
      document.getElementById('rekening_sppn').disabled = false;
      document.getElementById('tanggal_terima_sppn').disabled = false;
      $("#bukti_transfer_sppn").hide();
      $("#bukti_sppn").show();
      $("#remove_bukti_sppn").show();
      $("#footer_submit_sppn").show();
      $("#footer_edit_sppn").hide();
    }
    
  }

  function pembayaran(id_sppb,id_sppn,form,data_sppb,data_sppn){
    $("#form_a").val(form);
    $("#form_b").val(form);
    if(data_sppb !== 0 && data_sppb !== null){
        var now = new Date(data_sppb.sppb_bayar_tanggal);
        var tanggal = moment(now).format("DD-MM-YYYY"); 
        $("#nomor_bukti_kas_sppb").val(data_sppb.sppb_bayar_nomor_bukti_kas);
        $("#id_sppb_bayar").val(data_sppb.sppb_bayar_id);
        $("#nomor_bukti_kas_sppb").attr('readonly',true);
        $("#rekening_sppb").val(data_sppb.master_rekening_kode_kbb+' / '+data_sppb.master_rekening_kode_sap+'('+data_sppb.master_rekening_keterangan+')');
        $("#rekening_sppb").attr('disabled','disabled');
        $("#tanggal_bayar_sppb").val(tanggal);
        $("#tanggal_bayar_sppb").attr('disabled','disabled');
        $("#rekening_sppb_1").val(data_sppb.master_rekening_id);
        $("#bukti_transfer_sppb").hide();
        $("#bukti_sppb").show();
        $("#remove_bukti_sppb").hide();
        document.getElementById("bukti_sppb").href = 'dokumen/' + data_sppb.sppb_bayar_bukti;
        document.getElementById("bukti_sppb").innerHTML = data_sppb.sppb_bayar_bukti;
        $("#footer_submit_sppb").hide();
        $("#footer_edit_sppb").show();
      }
      else{
        alert();
        var now = new Date();
        var tanggal = moment(now).format("DD-MM-YYYY");
        $("#nomor_bukti_kas_sppb").val('');
        $("#id_sppb_bayar").val('');
        $("#nomor_bukti_kas_sppb").attr('readonly',false);
        $("#rekening_sppb").val('');
        $("#rekening_sppb").attr('disabled',false);
        $("#tanggal_bayar_sppb").val(tanggal);
        $("#tanggal_bayar_sppb").attr('disabled',false);
        $("#rekening_sppb_1").val('');
        $("#remove_bukti_sppb").hide();
        $("#bukti_sppb").hide();
        $("#bukti_transfer_sppb").show();
        $("#footer_submit_sppb").show();
        $("#footer_edit_sppb").hide();
      }
      if(data_sppn !== 0 && data_sppn !== null){
        var now = new Date(data_sppn.sppn_terima_tanggal);
        var tanggal = moment(now).format("DD-MM-YYYY");
        $("#id_sppn_terima").val(data_sppn.sppn_terima_id);
        $("#nomor_bukti_kas_sppn").val(data_sppn.sppn_terima_nomor_bukti_kas);
        $("#nomor_bukti_kas_sppn").attr('readonly','readonly');
        $("#rekening_sppn").val(data_sppn.master_rekening_kode_kbb+' / '+data_sppn.master_rekening_kode_sap+'('+data_sppn.master_rekening_keterangan+')');
        $("#rekening_sppn").attr('disabled','disabled');
        $("#tanggal_terima_sppn").val(tanggal);
        $("#tanggal_terima_sppn").attr('disabled','disabled');
        $("#rekening_sppn_1").val(data_sppn.master_rekening_id);
        $("#bukti_transfer_sppn").hide();
        $("#bukti_sppn").show();
        $("#remove_bukti_sppn").hide();
        document.getElementById("bukti_sppn").href = 'dokumen/' + data_sppn.sppn_terima_bukti;
        document.getElementById("bukti_sppn").innerHTML = data_sppn.sppn_terima_bukti;
        $("#footer_submit_sppn").hide();
        $("#footer_edit_sppn").show();
      }
      else{
        var now = new Date();
        var tanggal = moment(now).format("DD-MM-YYYY");
        $("#id_sppn_terima").val('');
        $("#nomor_bukti_kas_sppn").val('');
        $("#nomor_bukti_kas_sppn").attr('readonly',false);
        $("#rekening_sppn").val('');
        $("#rekening_sppn").attr('disabled',false);
        $("#tanggal_terima_sppn").val(tanggal);
        $("#tanggal_terima_sppn").attr('disabled',false);
        $("#rekening_sppn_1").val('');
        $("#remove_bukti_sppn").hide();
        $("#bukti_sppn_1").hide();
        $("#bukti_transfer_sppn").show();
        $("#footer_submit_sppn").show();
        $("#footer_edit_sppn").hide();
      }
    if(form == 0){
      $("#tab_bayar").show();
      $("#tab_terima").hide();
      document.getElementById("tab_bayar").className = "active";
      document.getElementById("tab-pembayaran").className = "tab tab-pane active";
      document.getElementById("tab_terima").className = "";
      document.getElementById("tab-penerimaan").className = "tab tab-pane";
      $("#modal_pembayaran").modal('show');
      $("#form-bayar").attr('action', 'spp/bayar/'+ id_sppb);
      
    }
    else if (form == 1){
      $("#tab_terima").show();
      $("#tab_bayar").hide();
      document.getElementById("tab_bayar").className = "";
      document.getElementById("tab-pembayaran").className = "tab tab-pane";
      document.getElementById("tab_terima").className = "active";
      document.getElementById("tab-penerimaan").className = "tab tab-pane active";
      $("#modal_pembayaran").modal('show');
      $("#form-terima").attr('action', 'spp/bayar/'+ id_sppn);
      
    }
    else{
      
      document.getElementById("tab_bayar").className = "active";
      document.getElementById("tab-pembayaran").className = "tab tab-pane active";
      document.getElementById("tab_terima").className = "";
      document.getElementById("tab-penerimaan").className = "tab tab-pane";
      $("#tab_terima").show();
      $("#tab_bayar").show();
      $("#modal_pembayaran").modal('show');
      $("#form-bayar").attr('action', 'spp/bayar/'+ id_sppb);
      $("#form-terima").attr('action', 'spp/bayar/'+ id_sppn);
    }
  }

function cetak_bukti_kas(id_spp,id_sppb,id_sppn,form,data_sppb,data_sppn,penerima,diterima){
    $("#form_a_cbk").val(form);
    $("#form_b_cbk").val(form);
    $(".cetakbuktikas").prop("value", id_spp);
    console.log(penerima);
    console.log(diterima);
    console.log(data_sppb);
    console.log(data_sppn);
    

    // console.log(id_spp);

    // console.log($("#form").val());
      if(data_sppb !== 0){
          $("#nomor_bukti_kas_sppb_cbk").val(data_sppb.cek_giro);
          $("#id_sppb_bayar_cbk").val(data_sppb.sppb_bayar_id);
          $("#nomor_bukti_kas_sppb_cbk").attr('readonly','readonly');
          $("#rekening_sppb_cbk").val(data_sppb.master_rekening_kode_kbb+' / '+data_sppb.master_rekening_kode_sap+'('+data_sppb.master_rekening_keterangan+')');
          $("#rekening_sppb_cbk").attr('disabled','disabled');
          $("#rekening_sppb_1_cbk").val(data_sppb.master_rekening_id);
          $("#penerima_cbk").val(data_sppb.master_vendor_id);
          $("#alamat_penerima_cbk").val(data_sppb.alamat_sppb);
          $("#footer_submit_sppb_cbk").hide();
          $("#footer_edit_sppb_cbk").show();
          // alert('a');
      }else{
         
        $("#penerima_cbk").val(penerima.karyawan_nama);
        $("#alamat_penerima_cbk").val(penerima.karyawan_alamat);
        $("#nomor_bukti_kas_sppb_cbk").val('');
        $("#id_sppb_bayar_cbk").val('');
        $("#nomor_bukti_kas_sppb_cbk").attr('readonly',false);
        $("#rekening_sppb_cbk").val('');
        $("#rekening_sppb_cbk").attr('disabled',false);
        $("#rekening_sppb_1_cbk").val('');
        $("#footer_submit_sppb_cbk").show();
        $("#footer_edit_sppb_cbk").hide();
          // alert('b');

      }
      if(data_sppn !== 0){
          $("#diterima_dari_cbk").val(data_sppn.master_vendor_id);
          $("#alamat_diterima_dari_cbk").val(data_sppn.alamat_sppn);
          $("#id_sppn_terima_cbk").val(data_sppn.sppn_terima_id);
          $("#nomor_bukti_kas_sppn_cbk").val(data_sppn.cek_giro);
          $("#nomor_bukti_kas_sppn_cbk").attr('readonly','readonly');
          $("#rekening_sppn_cbk").val(data_sppn.master_rekening_kode_kbb+' / '+data_sppn.master_rekening_kode_sap+'('+data_sppn.master_rekening_keterangan+')');
          $("#rekening_sppn_cbk").attr('disabled','disabled');
          $("#rekening_sppn_1_cbk").val(data_sppn.master_rekening_id);
          $("#footer_submit_sppn_cbk").hide();
          $("#footer_edit_sppn_cbk").show();
          // alert('c');
        
      }
      else{
         if(penerima != null && diterima == null){
            $("#diterima_dari_cbk").val('');
            $("#alamat_diterima_dari_cbk").val('');
            // alert('d11');

          }else{
            // alert('d22');
            $("#diterima_dari_cbk").val(diterima.karyawan_nama);
            $("#alamat_diterima_dari_cbk").val(diterima.karyawan_alamat);

          }
        $("#id_sppn_terima_cbk").val('');
        $("#nomor_bukti_kas_sppn_cbk").val('');
        $("#nomor_bukti_kas_sppn_cbk").attr('readonly',false);
        $("#rekening_sppn_cbk").val('');
        $("#rekening_sppn_cbk").attr('disabled',false);
        $("#rekening_sppn_1_cbk").val('');
        $("#footer_submit_sppn_cbk").show();
        $("#footer_edit_sppn_cbk").hide();
          // alert('d');
      }

    if(form == 0){
      $("#tab_bayar_cbk").show();
      $("#tab_terima_cbk").hide();
      document.getElementById("tab_bayar_cbk").className = "active";
      document.getElementById("tab-pembayaran_cbk").className = "tab tab-pane active";
      document.getElementById("tab_terima_cbk").className = "";
      document.getElementById("tab-penerimaan_cbk").className = "tab tab-pane";
      $("#modal_cetak_bukti_kas").modal('show');
      $( ".pisan" ).click( function() {
            if($("#nomor_bukti_kas_sppb_cbk").val() == '' || $("#rekening_sppb_cbk").val() == '' || $("#penerima_cbk").val() == '' || $("#alamat_penerima_cbk").val() == ''){
              window.onload = function() { 
                  document.getElementById("form-bayar_cbk").onsubmit = function() { 
                      loadXMLDoc('file.xml');
                      return false;
                  };
              };
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Pilih file dulu!'
                })
            }else{
              $("#form-bayar_cbk").attr('action', 'spp/bukti_kas/'+ id_sppb);
              let timerInterval
              Swal.fire({
                title: 'Loading !',
                allowOutsideClick: false,
                timer: 100000,
                timerProgressBar: true,
                didOpen: () => {
                  Swal.showLoading()
                  const b = Swal.getHtmlContainer().querySelector('b')
                  timerInterval = setInterval(() => {
                    b.textContent = Swal.getTimerLeft()
                  }, 100)
                },
                willClose: () => {
                  clearInterval(timerInterval)
                }
              }).then((result) => {
                /* Read more about handling dismissals below */
                if (result.dismiss === Swal.DismissReason.timer) {
                  console.log('I was closed by the timer')
                }
              })
            }
      });
      
    }
    else if (form == 1){
      $("#tab_bayar_cbk").hide();
      $("#tab_terima_cbk").show();
      document.getElementById("tab_terima_cbk").className = "active";
      document.getElementById("tab-penerimaan_cbk").className = "tab tab-pane active";
      document.getElementById("tab_bayar_cbk").className = "";
      document.getElementById("tab-pembayaran_cbk").className = "tab tab-pane";
      $("#modal_cetak_bukti_kas").modal('show');
      
      $( ".pisan" ).click( function() {
            if($("#nomor_bukti_kas_sppn_cbk").val() == '' || $("#rekening_sppn_cbk").val() == '' || $("#diterima_dari_cbk").val() == '' || $("#alamat_diterima_dari_cbk").val() == ''){
              window.onload = function() { 
                  document.getElementById("form-bayar_cbk").onsubmit = function() { 
                      loadXMLDoc('file.xml');
                      return false;
                  };
              };
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Pilih file dulu!'
                })
            }else{
              $("#form-terima_cbk").attr('action', 'spp/bukti_kas/'+ id_sppn);
              let timerInterval
              Swal.fire({
                title: 'Loading !',
                allowOutsideClick: false,
                timer: 100000,
                timerProgressBar: true,
                didOpen: () => {
                  Swal.showLoading()
                  const b = Swal.getHtmlContainer().querySelector('b')
                  timerInterval = setInterval(() => {
                    b.textContent = Swal.getTimerLeft()
                  }, 100)
                },
                willClose: () => {
                  clearInterval(timerInterval)
                }
              }).then((result) => {
                /* Read more about handling dismissals below */
                if (result.dismiss === Swal.DismissReason.timer) {
                  console.log('I was closed by the timer')
                }
              })
            }
      });
      
    }
    else{
      $("#tab_bayar_cbk").show();
      $("#tab_terima_cbk").show();
      document.getElementById("tab_bayar_cbk").className = "active";
      document.getElementById("tab-pembayaran_cbk").className = "tab tab-pane active";
      document.getElementById("tab_terima_cbk").className = "";
      document.getElementById("tab-penerimaan_cbk").className = "tab tab-pane";
      $("#modal_cetak_bukti_kas").modal('show');
      $( ".pisan" ).click( function() {
            if($("#nomor_bukti_kas_sppb_cbk").val() == '' || $("#rekening_sppb_cbk").val() == '' || $("#penerima_cbk").val() == '' || $("#alamat_penerima_cbk").val() == ''){
              window.onload = function() { 
                  document.getElementById("form-bayar_cbk").onsubmit = function() { 
                      loadXMLDoc('file.xml');
                      return false;
                  };
              };
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Pilih file dulu!'
                })
            }else{
              $("#form-bayar_cbk").attr('action', 'spp/bukti_kas/'+ id_sppb);
              let timerInterval
              Swal.fire({
                title: 'Loading !',
                allowOutsideClick: false,
                timer: 100000,
                timerProgressBar: true,
                didOpen: () => {
                  Swal.showLoading()
                  const b = Swal.getHtmlContainer().querySelector('b')
                  timerInterval = setInterval(() => {
                    b.textContent = Swal.getTimerLeft()
                  }, 100)
                },
                willClose: () => {
                  clearInterval(timerInterval)
                }
              }).then((result) => {
                /* Read more about handling dismissals below */
                if (result.dismiss === Swal.DismissReason.timer) {
                  console.log('I was closed by the timer')
                }
              })
            }
      });
      $( ".pisan" ).click( function() {
          $(".pisan").hide();
          $("#form-terima_cbk").attr('action', 'spp/bukti_kas/'+ id_sppn);
      });
    }
  }

  function kode_rekening_sppb(){
		$('#modal_rekening_sppb').modal('show');
	}
	function kode_rekening_sppn(){
		$('#modal_rekening_sppn').modal('show');
	}
  function kode_penerima_sppb(){
    $('#modal_penerima').modal('show');
  }
  function kode_diterima_dari_sppb(){
    $('#modal_diterima_dari').modal('show');
  }

  function pilih_rekening_sppb(id,kbb, sap, keterangan){
    $("#rekening_sppb_cbk").val(kbb+' / '+sap+' ('+keterangan+')');
    $("#rekening_sppb_1_cbk").val(id);
    $('#modal_rekening_sppb').modal('hide');
  }
  function pilih_penerima(id,nama_bank, alamat){
    $("#penerima_cbk").val(nama_bank+' / '+alamat);
    $("#penerima_1_cbk").val(id);
    $('#modal_penerima').modal('hide');
  }
  function pilih_diterima_dari(id,nama_bank, alamat){
    $("#diterima_dari_cbk").val(nama_bank+' / '+alamat);
    $("#diterima_dari_1_cbk").val(id);
    $('#modal_diterima_dari').modal('hide');
  }

  function pilih_rekening_sppn(id,kbb, sap, keterangan){
    $('#rekening_sppn_cbk').val(kbb+' / '+sap+' ('+keterangan+')');
    $('#rekening_sppn_1_cbk').val(id);
    $('#modal_rekening_sppn').modal('hide');
  }

  function upload_kirim(id,file){
    console.log(id,file);
    $("#modal_kirim").modal('show');
    // window.alert(file);
    if(file){
      //window.alert('file');
      $("#pilih_file_spp").show();
      $("#upload_file_lama").hide();
      $("#upload_file_baru").hide();
      document.getElementById("spp_kabag").removeAttribute("required");
        $("#form-kirim").attr('action', 'spp/upload/'+ id);

      $("#file_file_lama").attr('href','dokumen/'+file);
    }
    else{
      $("#pilih_file_spp").hide();
      $("#upload_file_lama").hide();
      $("#upload_file_baru").show();

    }
    $( "#pisanae" ).click( function() {
      var radio_check_val = "";
      for (var i = 0; i < document.getElementsByName('upload_file').length; i++){
        if(document.getElementsByName('upload_file')[i].checked){
          radio_check_val = document.getElementsByName('upload_file')[i].value;
        }
      }
      if(radio_check_val == 'file_lama'){
        $("#modal_kirim").modal('hide');
        $("#form-kirim").attr('action', 'spp/upload/'+ id);
        let timerInterval
        Swal.fire({
          title: 'Loading !',
          allowOutsideClick: false,
          timer: 100000,
          timerProgressBar: true,
          didOpen: () => {
            Swal.showLoading()
            const b = Swal.getHtmlContainer().querySelector('b')
            timerInterval = setInterval(() => {
              b.textContent = Swal.getTimerLeft()
            }, 100)
          },
          willClose: () => {
            clearInterval(timerInterval)
          }
        }).then((result) => {
          /* Read more about handling dismissals below */
          if (result.dismiss === Swal.DismissReason.timer) {
            console.log('I was closed by the timer')
          }
        })
      }
      else{
       if( document.getElementById("spp_kabag").files.length == 0 ){
          Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Pilih file dulu!'
        })
      }else{
        $("#modal_kirim").modal('hide');
        $("#form-kirim").attr('action', 'spp/upload/'+ id);
        let timerInterval
        Swal.fire({
          title: 'Loading !',
          allowOutsideClick: false,
          timer: 100000,
          timerProgressBar: true,
          didOpen: () => {
            Swal.showLoading()
            const b = Swal.getHtmlContainer().querySelector('b')
            timerInterval = setInterval(() => {
              b.textContent = Swal.getTimerLeft()
            }, 100)
          },
          willClose: () => {
            clearInterval(timerInterval)
          }
        }).then((result) => {
          /* Read more about handling dismissals below */
          if (result.dismiss === Swal.DismissReason.timer) {
            console.log('I was closed by the timer')
          }
        })
      }
      }
      
      
    });
  }

 
  function pilih_file(){
    var radio_check_val = "";
    for (var i = 0; i < document.getElementsByName('upload_file').length; i++){
      if(document.getElementsByName('upload_file')[i].checked){
        radio_check_val = document.getElementsByName('upload_file')[i].value;
      }
    }
      if(radio_check_val == 'file_lama'){
        document.getElementById("spp_kabag").removeAttribute("required");
        
        $("#upload_file_baru").hide();
      }
      else{
        $("#upload_file_baru").show();
        $("#spp_kabag").attr("required","required");

      }
    
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
        Swal.fire({
          title: 'Terima SPP',
          text: 'SPP berhasil anda Terima. Harap Menunggu...',
          icon: 'success',
          allowOutsideClick: false,
          showConfirmButton: false,
        }) 
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
        Swal.fire({
          title: 'Kirim SPP',
          text: 'SPP berhasil anda Kirim.',
          allowOutsideClick: false,
          icon: 'success',
          showConfirmButton: false,
          })
      }
    })
  }


	
  function selesai(id){
    Swal.fire({
      title: 'Apakah Anda Yakin?',
      text: "Menyelesaikan Data SPP!",
      icon: 'info',
      showCancelButton: true,
      confirmButtonColor: '#41B314',
      cancelButtonColor: '#F9354C',
      confirmButtonText: 'Kirim SPP'
    }).then((result) => {
      if (result.isConfirmed) {
      window.location.href = `{{url('')}}/spp/selesai/`+id;
      Swal.fire({
          title: 'Selesaikan SPP',
          text: 'SPP berhasil diselesaikan.',
          icon: 'success',
          allowOutsideClick: false,
          showConfirmButton: false,
          })
      }
    })
  }

  function upload_dokumen_pendukung(id){
    $("#modal_dokumen_pendukung").modal('show');
    $("#spp_id").val(id);
  }

  function revisi(id,data){
    $("#modal_revisi").modal('show');
    if(data){
      if(data[data.length-2].rekam_jejak_revisi != null){
      document.getElementById('keterangan_revisi').innerHTML = data[data.length-2].rekam_jejak_revisi;
      $("#keterangan_revisi").attr('readonly','readonly');
      }else{
      $("#keterangan_revisi").attr('required', true);
        
      }
    }
    $("#form-revisi").attr('action', 'spp/revisi/'+ id);
  }

  function confirm_revisi(){
    Swal.fire({
      title: 'Apakah Anda Yakin?',
      text: "Mengembalikan Data SPP!",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#41B314',
      cancelButtonColor: '#F9354C',
      confirmButtonText: 'Kembalikan SPP'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('form-revisi').submit();
        Swal.fire({
          title: 'Mengembalikan SPP',
          text: 'SPP berhasil dikembalikan.',
          icon: 'success',
          allowOutsideClick: false,
          showConfirmButton: false,
          })
      }
    })
  }

  function rekam_jejak(data,asal){
    $('#rekam_jejak_body').empty()
    var level = {{$hakakses}};
    

    $.each(data,function(index,val){
      if((val.master_user_id == 1 || val.master_user_id == 99) && val.rekam_jejak_status == 0){
        var date = new Date(val.rekam_jejak_waktu);
        var tanggal = moment(date).format("D-MM-YYYY HH:mm:ss");
        $('#rekam_jejak_body').append(`<li id="timeline_buat_${index}">
            <div class="timeline-badge info"><i class="glyphicon glyphicon-plus"></i></div>
            <div class="timeline-panel">
              <div class="timeline-heading">
                <h4 class="timeline-title" id="user_1_${index}"><strong>${val.asal}</strong></h4>
              <p id="time_1_${index}"><small class="text-muted"><i class="glyphicon glyphicon-time"></i> ${tanggal}</small></p>
              </div>
              <div class="timeline-body">
                <p>Membuat SPP Baru</p>
              </div>
            </div>
          </li>`);
      }
      else if(val.rekam_jejak_status == 1){
        var date = new Date(val.rekam_jejak_waktu);
        var tanggal = moment(date).format("D-MM-YYYY HH:mm:ss");
        $('#rekam_jejak_body').append(`<li class="timeline-inverted" id="timeline_setuju_${index}">
            <div class="timeline-badge warning"><i class="glyphicon glyphicon-send"></i></div>
            <div class="timeline-panel">
              <div class="timeline-heading">
                <h4 class="timeline-title" id="user_2_${index}"><strong>${val.asal}</strong></h4>
                <p id="time_2_${index}"><small class="text-muted"><i class="glyphicon glyphicon-time"></i>${tanggal}</small></p>
              </div>
              <div class="timeline-body">
                <p>Mengirim SPP ke ${val.tujuan}</p>
              </div>
            </div>
          </li>`);
      }
      else if(val.rekam_jejak_status == 6){
        var date = new Date(val.rekam_jejak_waktu);
        var tanggal = moment(date).format("D-MM-YYYY HH:mm:ss");
        $('#rekam_jejak_body').append(`<li class="timeline" id="timeline_terima_${index}">
            <div class="timeline-badge success"><i class="glyphicon glyphicon-ok"></i></div>
            <div class="timeline-panel">
              <div class="timeline-heading">
                <h4 class="timeline-title" id="user_2_${index}"><strong>${val.tujuan}</strong></h4>
                <p id="time_2_${index}"><small class="text-muted"><i class="glyphicon glyphicon-time"></i>${tanggal}</small></p>
              </div>
              <div class="timeline-body">
                <p>Menerima SPP dari ${val.asal}</p>
              </div>
            </div>
          </li>`);
      }
      
      else if(val.master_user_id !=1 && val.rekam_jejak_status == 0){
        var date = new Date(val.rekam_jejak_waktu);
        var tanggal = moment(date).format("D-MM-YYYY HH:mm:ss");
        if(level == 2){
          var i = index;
          if(val.asal == "Petugas Penerima"){
              $('#rekam_jejak_body').append(`<li id="timeline_revisi_${index}">
              <div class="timeline-badge warning"><i class="glyphicon glyphicon-pencil"></i></div>
              <div class="timeline-panel">
                <div class="timeline-heading">
                  <h4 class="timeline-title" id="user_3_${index}"><strong>${val.asal}</strong></h4>
                  <p id="time_3_${index}"><small class="text-muted"><i class="glyphicon glyphicon-time"></i>${tanggal}</small></p>
                </div>
                <div class="timeline-body">
                  <p>Mengembalikan ke Bagian</p>
                  <p>Revisi Oleh : ${asal[i]}</p>
                  <h5 id="revisi_${index}"><strong style="color: red">Keterangan Revisi :</strong><br><span> ${val.rekam_jejak_revisi}</span></h5>
                </div>
              </div>
            </li>`);
          }else{
            $('#rekam_jejak_body').append(`<li id="timeline_revisi_${index}">
              <div class="timeline-badge warning"><i class="glyphicon glyphicon-pencil"></i></div>
              <div class="timeline-panel">
                <div class="timeline-heading">
                  <h4 class="timeline-title" id="user_3_${index}"><strong>${val.asal}</strong></h4>
                  <p id="time_3_${index}"><small class="text-muted"><i class="glyphicon glyphicon-time"></i>${tanggal}</small></p>
                </div>
                <div class="timeline-body">
                  <p>Mengembalikan ke Petugas Penerima</p>
                  <p>Revisi Oleh : ${asal[i]}</p>
                  <h5 id="revisi_${index}"><strong style="color: red">Keterangan Revisi :</strong><br><span> ${val.rekam_jejak_revisi}</span></h5>
                </div>
              </div>
            </li>`);
          }
          
        }else{
          if(val.asal == "Petugas Penerima"){

          $('#rekam_jejak_body').append(`<li class="timeline-inverted" id="timeline_revisi_${index}">
            <div class="timeline-badge warning"><i class="glyphicon glyphicon-pencil"></i></div>
            <div class="timeline-panel">
              <div class="timeline-heading">
                <h4 class="timeline-title" id="user_3_${index}"><strong>${val.asal}</strong></h4>
                <p id="time_3_${index}"><small class="text-muted"><i class="glyphicon glyphicon-time"></i>${tanggal}</small></p>
              </div>
              <div class="timeline-body">
                <p>Mengembalikan ke Bagian</p>
                <h5 id="revisi_${index}"><strong style="color: red">Keterangan Revisi :</strong><br><span> ${val.rekam_jejak_revisi}</span></h5>
              </div>
            </div>
          </li>`);
        }
          else{
              $('#rekam_jejak_body').append(`<li class="timeline-inverted" id="timeline_revisi_${index}">
              <div class="timeline-badge warning"><i class="glyphicon glyphicon-pencil"></i></div>
              <div class="timeline-panel">
                <div class="timeline-heading">
                  <h4 class="timeline-title" id="user_3_${index}"><strong>${val.asal}</strong></h4>
                  <p id="time_3_${index}"><small class="text-muted"><i class="glyphicon glyphicon-time"></i>${tanggal}</small></p>
                </div>
                <div class="timeline-body">
                  <p>Mengembalikan ke Petugas Penerima</p>
                  <h5 id="revisi_${index}"><strong style="color: red">Keterangan Revisi :</strong><br><span> ${val.rekam_jejak_revisi}</span></h5>
                </div>
              </div>
            </li>`);
          }
        }
        
      }
      else if(val.rekam_jejak_status == 2){
        var date = new Date(val.rekam_jejak_waktu);
        var tanggal = moment(date).format("D-MM-YYYY HH:mm:ss");
        $('#rekam_jejak_body').append(`<li class="timeline-inverted" id="timeline_bayar_${index}">
            <div class="timeline-badge danger"><i class="glyphicon glyphicon-usd"></i></div>
            <div class="timeline-panel">
              <div class="timeline-heading">
                <h4 class="timeline-title" id="user_4_${index}"><strong>${val.asal}</strong></h4>
                <p id="time_4_${index}"><small class="text-muted"><i class="glyphicon glyphicon-time"></i>${tanggal}</small></p>
              </div>
              <div class="timeline-body">
                <p>Melakukan pembayaran SPPb</p>
              </div>
            </div>
          </li>`);
      }
      else if(val.rekam_jejak_status == 3){
        var date = new Date(val.rekam_jejak_waktu);
        var tanggal = moment(date).format("D-MM-YYYY HH:mm:ss");
        $('#rekam_jejak_body').append(`<li class="timeline-inverted" id="timeline_terima_${index}">
            <div class="timeline-badge danger"><i class="glyphicon glyphicon-credit-card"></i></div>
            <div class="timeline-panel">
              <div class="timeline-heading">
                <h4 class="timeline-title" id="user_4_${index}"><strong>${val.asal}</strong></h4>
                <p id="time_4_${index}"><small class="text-muted"><i class="glyphicon glyphicon-time"></i>${tanggal}</small></p>
              </div>
              <div class="timeline-body">
                <p>Penerimaan SPPn</p>
              </div>
            </div>
          </li>`);
      }
      else if(val.rekam_jejak_status == 4){
        var date = new Date(val.rekam_jejak_waktu);
        var tanggal = moment(date).format("D-MM-YYYY HH:mm:ss");
        $('#rekam_jejak_body').append(`<li class="timeline" id="timeline_selesai_${index}">
            <div class="timeline-badge info"><i class="glyphicon glyphicon-check"></i></div>
            <div class="timeline-panel">
              <div class="timeline-heading">
                <h4 class="timeline-title" id="user_5_${index}"><strong>${val.asal}</strong></h4>
                <p id="time_5_${index}"><small class="text-muted"><i class="glyphicon glyphicon-time"></i>${tanggal}</small></p>
              </div>
              <div class="timeline-body">
                <p>Penyelesaian SPP</p>
              </div>
            </div>
          </li>`);
      }
      else if(val.rekam_jejak_status == 5){
        var date = new Date(val.rekam_jejak_waktu);
        var tanggal = moment(date).format("D-MM-YYYY HH:mm:ss");
        $('#rekam_jejak_body').append(`<li class="timeline" id="timeline_selesai_${index}">
            <div class="timeline-badge danger"><i class="glyphicon glyphicon-remove"></i></div>
            <div class="timeline-panel">
              <div class="timeline-heading">
                <h4 class="timeline-title" id="user_5_${index}"><strong>${val.asal}</strong></h4>
                <p id="time_5_${index}"><small class="text-muted"><i class="glyphicon glyphicon-time"></i>${tanggal}</small></p>
              </div>
              <div class="timeline-body">
                <p>Pembatalan SPP</p>
              </div>
            </div>
          </li>`);
      }
    });
    $("#modal_rekam_jejak").modal('show');


  }

  function upload_bukti_kas(id,bukti){
    $("#bukti_spp_id").val(id);
    if(bukti == null || bukti == ''){
      $("#upload_bukti").show();
      $("#file_bukti_kas").hide();
      $("#submit_bukti").show();

    }
    else{
      $("#upload_bukti").hide();
      $("#file_bukti_kas").show();
      $("#submit_bukti").hide();
      $("#bukti_kas_lama").attr('href','dokumen/'+bukti);
      document.getElementById("bukti_kas_lama").innerHTML = bukti;

    }
    $("#modal_bukti_kas").modal('show');
  }

  function edit_bukti_kas(){
    $("#upload_bukti").show();
    $("#file_bukti_kas").hide();
    $("#submit_bukti").show();
  }

  function batal(id){
    Swal.fire({
      title: 'Apakah Anda Yakin?',
      text: "Membatalkan SPP!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Batalkan SPP!'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = `{{url('')}}/spp/batal/`+id;
        Swal.fire({
          title: 'Batal',
          text: 'SPP berhasil anda Batalkan.',
          icon: 'success',
          allowOutsideClick: false,
          showConfirmButton: false,
          })
      }
    })
  }

  $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
    $($.fn.dataTable.tables(true)).DataTable()
    .columns.adjust()
    .responsive.recalc();
  });
</script>

@endsection

@section('footer')
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/piexif.min.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/sortable.min.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/fileinput.min.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/themes/fa/theme.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/locales/id.js"></script>
@endsection