@extends('template.master')
@section('title', 'SPP | Laporan')
@section('yoro','active')
@section('konten')
<?php 
$grup_id = Session::get('grup_ui');
$hakakses = Session::get('hak_akses');
$bagian = Session::get('bagian');
$level = Session::get('level');
?>
<script>
     $( function() { 
					$(".datepicker").daterangepicker({
						locale: {
							format: 'DD-MM-YYYY'
						}
		  			}); 
				});
  </script>
<!-- MAIN -->
<div class="main">
  <!-- MAIN CONTENT -->
 
  <div class="main-content">
    <div class="container-fluid">
      <h3 class="page-title">Laporan</h3>
      <div class="row">
        <div class="col-md-12">
          <!-- TABLE -->
          <div class="panel">
                
            <div class="panel-heading">
              <h3 class="panel-title">Tabel Laporan</h3>
            </div>
            <div class="panel-body">
              <div class="row">
                    <form class="col-md-3" action="" method="post" id="form_export" enctype="multipart\form-data">
                      {{csrf_field()}}
                        <div class="modal-header">
                          <h4 class="modal-title">Filter</h4>
                        </div>
                        <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" class="form-control" name="bagian" value="{{$bagian}}" placeholder="{{$bagian}}">
                          </div>
                        <div class="form-group">
                          <input type="hidden" class="form-control" name="hakakses" value="{{$hakakses}}" placeholder="{{$hakakses}}">
                        </div>
                        @if(isset($rentang_waktu_raw))
                          <div class="form-group">
                            <label>Rentang Waktu :</label><br>
                            <input type="text" class="form-control datepicker" name="rentang_waktu" value="{{$rentang_waktu_raw}}">
                          </div>
                        @else
                        <div class="form-group">
                            <label>Rentang Waktu :</label><br>
                            <input type="text" class="form-control datepicker" name="rentang_waktu">
                          </div>
                        @endif
                          <!-- <div class="form-group">
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
                          </div> -->
                          @if(isset($jenis_spp))
                          <div class="form-group" hidden>
                            <label for="">Jenis SPP</label>
                              @if($jenis_spp == "semua")
                              <select class="form-control" name="jenis_spp" id="jenis_spp">
                                  <option selected>Pilih</option>
                                  <option value="semua">Tampilkan Semua</option>
                                  <option value="spp_biasa">SPPb/SPPn</option>
                                  <option value="spp_khusus">SPP Khusus</option>
                              </select>
                              @elseif($jenis_spp == "spp_khusus")
                              <select class="form-control" name="jenis_spp" id="jenis_spp">
                                  <option selected>Pilih</option>
                                  <option value="semua">Tampilkan Semua</option>
                                  <option value="spp_biasa">SPPb/SPPn</option>
                                  <option value="spp_khusus" selected>SPP Khusus</option>
                              </select>
                              @else
                              <select class="form-control" name="jenis_spp" id="jenis_spp">
                                  <option selected>Pilih</option>
                                  <option value="semua">Tampilkan Semua</option>
                                  <option value="spp_biasa" selected>SPPb/SPPn</option>
                                  <option value="spp_khusus">SPP Khusus</option>
                              </select>
                              @endif
                          </div>
                          @else
                          <div class="form-group" hidden>
                            <label for="">Jenis SPP</label>
                              <select class="form-control" name="jenis_spp" id="jenis_spp">
                                  <option selected>Pilih</option>
                                  <option value="semua">Tampilkan Semua</option>
                                  <option value="spp_biasa">SPPb/SPPn</option>
                                  <option value="spp_khusus">SPP Khusus</option>
                              </select>
                          </div>
                          @endif
                          @if(isset($status_bayar))
                          <div class="form-group">
                            <label>Status Bayar :</label><br>
                            <select class="form-control" name="status_bayar">
                              @if($status_bayar == "0")
                              <option selected>Pilih</option>
                              <option value="semua">Tampilkan Semua</option>
                              <option value="0">Belum Dibayar</option>
                              <option value="1">Sudah Dibayar</option>
                              @elseif($status_bayar == "1")
                              <option selected>Pilih</option>
                              <option value="semua">Tampilkan Semua</option>
                              <option value="0" >Belum Dibayar</option>
                              <option value="1" >Sudah Dibayar</option>
                              @else
                              <option selected>Pilih</option>
                              <option value="semua">Tampilkan Semua</option>
                              <option value="0" >Belum Dibayar</option>
                              <option value="1">Sudah Dibayar</option>
                              @endif
                            </select>
                          </div>
                          
                          @else
                          <div class="form-group">
                            <label>Status Bayar :</label><br>
                            <select class="form-control" name="status_bayar">
                                <option selected>Pilih</option>
                              <option value="semua">Tampilkan Semua</option>
                              <option value="0">Belum Dibayar</option>
                              <option value="1">Sudah Dibayar</option>
                            </select>
                          </div>
                          @endif
                          @if(isset($jenis_report))
                                <div class="form-group">
                                  <label> Jenis Report :</label><br>
                                  <select class="form-control"  id="jns_report" name="jenis_report">
                                  @if($jenis_report == "1")
                                  <option value="1" selected>Simple</option>
                                    <option value="2"> Detail</option>
                                  @elseif($jenis_report == "2")
                                  <option value="1" >Simple</option>
                                    <option value="2" selected> Detail</option>
                                  @endif
                                  </select>
                                </div>
                            @else
                                <div class="form-group" >
                                  <label> Jenis Report :</label><br>
                                    <select class="form-control" id="jns_report" name="jenis_report">
                                      <option value="1" selected>Simple</option>
                                      <option value="2"> Detail</option>
                                    </select>
                                </div>
                            @endif
                            @if(isset($export_tipe))
                                <div class="form-group">
                                  <label> Export :</label><br>
                                  <select class="form-control" id="export" name="export_tipe">
                                    @if($export_tipe == "1")
                                    <option value="3"> Web</option>
                                    <option value="1" selected>PDF</option>
                                    <option value="2"> Excel</option>
                                    @elseif($export_tipe == "2")
                                    <option value="3"> Web</option>
                                    <option value="1" >PDF</option>
                                    <option value="2"selected> Excel</option>
                                    @else
                                    <option value="3"selected> Web</option>
                                    <option value="1" >PDF</option>
                                    <option value="2"> Excel</option>
                                    @endif
                                    
                                  </select>
                                </div>
                            @else
                                <div class="form-group">
                                  <label> Export :</label><br>
                                  <select class="form-control" id="export" name="export_tipe">
                                    <option value="3"selected> Web</option>
                                    <option value="1" >PDF</option>
                                    <option value="2"> Excel</option>
                                    <option value="4">CSV (mandiri)</option>
                                  </select>
                                </div>
                            @endif
                        </div>
                        <input type="hidden" name="c_spp" value="{{$c_spp}}">
                        <input type="hidden" name="c_sppb" value="{{$c_sppb}}">
                        <input type="hidden" name="c_sppn" value="{{$c_sppn}}">
                              <!-- <button type="button" class="btn btn-primary" onclick="export_submit(1)">PDF</button>
                              <button type="button" class="btn btn-primary" onclick="export_submit(2)">Excel</button>
                              <button type="button" class="btn btn-primary" onclick="export_submit(3)">Web</button> -->
                        <div class="modal-footer">
                          <button type="submit" onclick="export_submit()" class="btn btn-success">Sumbit</button>
                          
                        </div>
                    </form>
                    <div  >
                      <form action="{{route('export_csv')}}" method="POST">
                        @csrf
                      <button type="submit" class="btn btn-success">Export CSV</button>
                      </form>
                    </div>
                  </div>
                <!-- <button class="btn btn-primary" onclick="advanced_search()" style="margin-bottom: 15px">Advanced Search</button>
                <button class="btn btn-primary" onclick="exports()" style="margin-bottom: 15px; float: right;">Export</button> -->
                <!-- <table class="table table-bordered table-striped nowrap" style="width: 100%">
                  <thead>
                    <tr>
                      <th>No. </th>
                      <th>Tanggal</th>
                      <th>Kode Cash Flow</th>
                      <th>No. Bukti Kas</th>
                      <th>Uraian</th>
                      <th>No SPPb</th>
                      <th>No SPPn</th>
                      <th>No Rekg Kas/Bank</th>
                      <th>No Rekg KBB</th>
                      <th>No Rekg SAP</th>
                      <th>Profit/Cost Center</th>
                      <th>Jumlah SPPb</th>
                      <th>Jumlah SPPn</th>
                      <th>Status Bayar</th>
                    </tr>
                  </thead>
                  <tbody>
                  @foreach($data as $key => $value)
                    <tr>
                      <td>{{$key+1}}</td>
                      <td>{{date('d-m-Y',strtotime($value->tanggal))}}</td>
                      @if(isset($sppbisi[$key]))
                      <td>{{$sppbisi[$key]->master_cash_flow_kode}}</td>
                      @elseif(isset($sppnisi[$key]))
                      <td>{{$sppnisi[$key]->master_cash_flow_kode}}</td>
                      @else
                      <td></td>
                      @endif
                      @if(isset($sppb_bayar[$key]) && $sppb_bayar[$key] !== null)
                      <td >{{$sppb_bayar[$key]->sppb_bayar_nomor_bukti_kas}}</td>
                      @elseif(isset($sppn_terima[$key]) && $sppn_terima[$key]!== null)
                      <td >{{$sppn_terima[$key]->sppn_terima_nomor_bukti_kas}}</td>
                      @else
                      <td ></td>
                      @endif
                      @if(isset($value->sppb_uraian2))
                      <td>{{strip_tags($value->sppb_uraian2)}}</td>
                      @elseif(isset($value->sppn_uraian2))
                      <td>{{strip_tags($value->sppn_uraian2)}}</td>
                      @endif
                      @if(isset($value->sppb_no))
                      <td >{{$value->sppb_no}}</td>
                      <td ></td>
                      @elseif(isset($value->sppn_no))
                      <td ></td>
                      <td >{{$value->sppn_no}}</td>
                      @endif
                      @if(isset($sppbisi[$key]))
                      <td >{{$sppbisi[$key]->master_kode_kbb}}</td>
                      <td >{{$sppbisi[$key]->master_kode_kbb}}</td>
                        @if(isset($sppbisi[$key]->master_gl_id) && ($sppbisi[$key]->master_gl_id !== null))
                          
                          <td >$sppbisi[$key]->master_gl_kode</td>
                        
                        @else
                        <td >{{$sppbisi[$key]->master_rekening_kode_sap}}</td>
                        @endif
                      <td >{{$sppbisi[$key]->master_cost_center_kode}}</td>
                      @elseif(isset($sppnisi[$key]))
                      <td >{{$sppnisi[$key]->master_kode_kbb}}</td>
                      <td >{{$sppnisi[$key]->master_kode_kbb}}</td>
                      @if(isset($sppnisi[$key]->master_gl_id) && ($sppnisi[$key]->master_gl_id !== null))
                          
                          <td >$sppnisi[$key]->master_gl_kode</td>
                        
                        @else
                        <td >{{$sppnisi[$key]->master_rekening_kode_sap}}</td>
                        @endif
                      <td >{{$sppnisi[$key]->master_profit_center_kode}}{{$sppnisi[$key]->master_cost_center_kode}}</td>
                      @endif
                      @if(isset($value->sppb_total))
                      <td style="text-align:right;">Rp. {{number_format($value->sppb_total)}}</td>
                      <td ></td>
                        @if($value->spp_status_bayar == 1)
                          <td>Sudah Dibayar</td>
                        @else
                          <td>Belum Dibayar</td>
                        @endif
                      @elseif(isset($value->sppn_jumlah))
                      <td ></td>
                      <td style="text-align:right;">Rp. {{number_format($value->sppn_jumlah)}}</td>
                        @if($value->spp_status_terima == 1)
                          <td>Sudah Dibayar</td>
                        @else
                          <td>Belum Dibayar</td>
                        @endif
                      @endif
                    </tr>
                  @endforeach
                  </tbody>
                </table> -->
              </div>
            </div>
          </div>
          <!-- END TABLE -->
        </div>
      </div>
    </div>
  </div>
  <!-- END MAIN CONTENT -->
</div>

<script type="text/javascript">
  $(document).ready(function () {

  });
  function export_submit(){
  var id = $( "#export option:selected" ).val();
  var jensi_report = $( "#jns_report option:selected" ).val();
  if (jensi_report == '1') {
    if(id == 1){
      $("#form_export").attr('action', '{{route('laporan_pdf')}}');
      $("#form_export").attr('target', '');
      document.getElementById("form_export").submit();
    }
    else if (id == 2){
      $("#form_export").attr('action', '{{route('laporan_export')}}');
      $("#form_export").attr('target', '');
      document.getElementById("form_export").submit();
    }else if(id == 4){
      $("#form_export").attr('action', '{{route('laporan_csv')}}');
      $("#form_export").attr('target', '');
      document.getElementById("form_export").submit();
    }else{
      $("#form_export").attr('action', '{{route('laporan_web')}}');
      $("#form_export").attr('target', '_blank');
      document.getElementById("form_export").submit();
    }
  }else{
    if(id == 1){
      $("#form_export").attr('action', '{{route('laporan_pdf_detail')}}');
      $("#form_export").attr('target', '');
      document.getElementById("form_export").submit();
    }
    else if (id == 2){
      $("#form_export").attr('action', '{{route('laporan_pdf_detail')}}');
      $("#form_export").attr('target', '');
      document.getElementById("form_export").submit();
    }else if(id == 4){
      $("#form_export").attr('action', '{{route('laporan_csv')}}');
      $("#form_export").attr('target', '');
      document.getElementById("form_export").submit();
    }else{
      $("#form_export").attr('action', '{{route('laporan_web_detail')}}');
      $("#form_export").attr('target', '_blank');
      document.getElementById("form_export").submit();
    }
  }
    
  }

</script>



@endsection