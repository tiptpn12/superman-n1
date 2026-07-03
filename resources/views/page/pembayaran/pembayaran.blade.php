@extends('template.master')
@section('title', 'Pembayaran')

@section('header')
<link rel="stylesheet" href="{{asset('assets/vendor/kartik-v/bootstrap-fileinput/css/fileinput.min.css')}}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">

<style>
  .d-flex{
    display: flex;
  }
  .justify-content-between{
    justify-content: space-between;
  }
</style>

@endsection
@section('bukak')
active
@endsection
@section('konten')
<?php
$hakakses = Session::get('hak_akses');
$bagian = Session::get('bagian');
$level = Session::get('level');
?>

<!-- MAIN -->
<?php ob_start(); ?>
<script type="text/javascript"> 
</script>
<div class="main">
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel">
                        <div class="tab-pane fade in active" id="tab-petugas-pembayaran">
                            <div class="panel-heading">
                                <h3 class="panel-title">Ajukan Pembayaran</h3>
                            </div>
                            <div class="panel-body">
                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                      <li id="tab-sudah-upload"><a href="#tab-sudah-upload-dokumen" role="tab" data-toggle="tab">Sudah Upload Bukti Kas</a></li>
                                      <li id="tab-belum-upload"><a href="#tab-belum-upload-dokumen" role="tab" data-toggle="tab">Belum Upload Bukti Kas</a></li>
                                    </ul>
                                </div>
                                <div class="tab-content">

                                    <!-- Tab Sudah Upload -->
                                    <div class="tab-pane fade in active" id="tab-sudah-upload-dokumen">
                                        <div class="d-flex justify-content-between">
                                            <div  class="">
                                                <a href="{{route('export_excel')}}?status=1" class="btn btn-success" style="margin-bottom: 15px">Export Excel Semua</a>
                                                <button id="button-export-terpilih" disabled type="button" class="btn btn-success" onclick="exportTerpilih()" style="margin-bottom: 15px">Export Excel Terpilih</button>
                                                <a href="{{route('export_pdf')}}?status=1" class="btn btn-danger" style="margin-bottom: 15px">Export Web Semua</a>
                                                <button id="button-export-terpilih_pdf" disabled type="button" class="btn btn-danger" onclick="exportTerpilih_pdf()" style="margin-bottom: 15px">Export Web Terpilih</button> 
                                            </div>
                                            <div class="">
                                                <button class="btn btn-danger" onclick="window.location.href='{{route('indexpembayaran')}}'" style="margin-bottom: 15px">Reset</button>
                                                <button class="btn btn-primary" onclick="advanced_search()" style="margin-bottom: 15px">Advanced Search</button>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                          <table id="" class="table table-bordered table-striped nowrap" style="width: 100%">
                                              <thead>
                                                  <tr>
                                                      <th rowspan="2"><input type="checkbox" id="select_all"></th>
                                                      <th rowspan="2">Bagian</th>
                                                      <th rowspan="2">Tanggal SPP</th>
                                                      <th colspan="3">SPPb</th>
                                                      <th colspan="3">SPPn</th>
                                                      <th rowspan="2">Status</th>
                                                      <th rowspan="2">Status Pembayaran</th>
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
                                                  <!-- DataTables AJAX load -->
                                              </tbody>
                                          </table>
                                        </div>
                                    </div>
                                    <!-- End Tab Sudah Upload -->

                                    <!-- Tab Belum Upload -->
                                    <div class="tab-pane fade" id="tab-belum-upload-dokumen">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <a href="{{route('export_excel')}}?status=0" class="btn btn-success" style="margin-bottom: 15px">Export Excel Semua</a>
                                                <button id="button-export-terpilih-belum" disabled type="button" class="btn btn-success" onclick="exportTerpilihBelum()" style="margin-bottom: 15px">Export Excel Terpilih</button>
                                                <a href="{{route('export_pdf')}}?status=0" class="btn btn-danger" style="margin-bottom: 15px">Export Web Semua</a>
                                                <button id="button-export-terpilih_pdf_belum" disabled type="button" class="btn btn-danger" onclick="exportTerpilih_pdf_belum()" style="margin-bottom: 15px">Export Web Terpilih</button> 
                                            </div>
                                            <div>
                                                <button class="btn btn-danger" onclick="window.location.href='{{route('indexpembayaran')}}'" style="margin-bottom: 15px">Reset</button>
                                                <button class="btn btn-primary" onclick="advanced_search()" style="margin-bottom: 15px">Advanced Search</button>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                          <table id="table-belum" class="table table-bordered table-striped nowrap" style="width: 100%">
                                              <thead>
                                                  <tr>
                                                      <th rowspan="2"><input type="checkbox" id="select_all_belum"></th>
                                                      <th rowspan="2">Bagian</th>
                                                      <th rowspan="2">Tanggal SPP</th>
                                                      <th colspan="3">SPPb</th>
                                                      <th colspan="3">SPPn</th>
                                                      <th rowspan="2">Status</th>
                                                      <th rowspan="2">Status Pembayaran</th>
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
                                                  <!-- DataTables AJAX load -->
                                              </tbody>
                                          </table>
                                        </div>
                                    </div>
                                    <!-- End Tab Belum Upload -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ADVANCED SEARCH -->
<div id="modal_advanced_search" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ route('indexpembayaran') }}" method="get">
      {{csrf_field()}}
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Advanced Search</h4>
          <input type="hidden" id="index_advanced_search" name="index_advanced_search" value="">
        </div>
        <div class="modal-body">
        
        <div class="form-group" id="advanced_search_bagian">
            <label>Bagian :</label><br>
            <select class="form-control" name="bagian">
              <option value="semua">Tampilkan Semua</option>
              @foreach($b as $key => $value)
              <option value="{{$value->master_bagian_id}}">{{$value->master_bagian_nama}}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group" id="advanced_search_bagian">
            <label>Vendor :</label><br>
            <select class="form-control selectpicker" name="vendor" data-live-search="true">
              <option value="semua">Tampilkan Semua</option>
              @foreach($vendor as $key => $value)
              <option value="{{$value->master_vendor_id}}">{{$value->master_vendor_atas_nama}}</option>
              @endforeach
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
              @foreach ($m_flows as $key => $f )
                <option value="{{$f->master_hak_akses_id}}"> {{$f->master_hak_akses_keterangan}}</option>
              @endforeach
              <option value="100">Selesai </option>
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
          {{-- <div class="form-group" id="advanced_search_status_bayar2">
            <label>Status Bayar :</label><br>
            <select class="form-control" name="status_bayar">
              <option value="semua">Tampilkan Semua</option>
              <option value="0" >Belum Dibayar</option>
              <option value="1">Sudah Dibayar</option>
              <option value="2">Dibatalkan</option>
            </select>
          </div> --}}
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- END MODAL ADVANCED SEARCH -->

<!-- MODAL CETAK -->
<div id="modal_cetak" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      {{csrf_field()}}
        <div class="modal-header">
        <form class="col-md-3" action="" method="post" id="form_export" enctype="multipart\form-data">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Cetak</h4>
          <input type="hidden" id="index_advanced_search" name="index_advanced_search" value="">
        </div>
        <div class="modal-body">
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
                </select>
              </div>
        @endif
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" onclick="export_submit()">Cetak</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<form action="{{route('export_excel_terpilih')}}?status=1" method="post" id="form-export-terpilih" class="hidden">
  @csrf
    <input id="inputID" type="hidden" name="ids[]" multiple>
    <button class="hidden" style="display: none;" type="submit">S</button>
  </form>
  <form action="{{route('export_excel_terpilih')}}?status=0" method="post" id="form-export-terpilih-belum" class="hidden">
    @csrf
      <input id="inputIDBelum" type="hidden" name="ids[]" multiple>
      <button class="hidden" style="display: none;" type="submit">S</button>
    </form>
  <form action="{{route('export_pdf_terpilih')}}" method="post" id="form-export-terpilih_pdf" class="hidden">
  @csrf
    <input id="inputID_pdf" type="hidden" name="ids[]" multiple>
    <button class="hidden" style="display: none;" type="submit">S</button>
  </form>
  <form action="{{route('export_pdf_terpilih')}}" method="post" id="form-export-terpilih_pdf_belum" class="hidden">
    @csrf
      <input id="inputID_pdf_belum" type="hidden" name="ids[]" multiple>
      <button class="hidden" style="display: none;" type="submit">S</button>
    </form>
  
<script>
  $(function() {
    $("#from_datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
    $("#to_datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
    $("#from_datepicker").attr("autocomplete", "off");
    $("#to_datepicker").attr("autocomplete", "off");
  });

  $("#select_all").on('click', function() {
    var isChecked = $(this).prop('checked');
    $(".cb-child").prop('checked', isChecked);
    $("#button-export-terpilih").prop('disabled', !isChecked);
    $("#button-export-terpilih_pdf").prop('disabled', !isChecked);
  });

  $("#select_all_belum").on('click', function() {
    var isChecked = $(this).prop('checked');
    $(".checkboxcb-child_belum").prop('checked', isChecked);
    $("#button-export-terpilih-belum").prop('disabled', !isChecked);
    $("#button-export-terpilih_pdf_belum").prop('disabled', !isChecked);
  });

  // Checkbox anak Sudah Upload
  $("#table tbody").on('click', '.cb-child', function() {
      if (!$(this).prop('checked')) {
          $("#select_all").prop('checked', false);
      }
      let semua_checkbox = $("#table tbody .cb-child:checked");
      let ada_tercentang = (semua_checkbox.length > 0);
      $("#button-export-terpilih").prop('disabled', !ada_tercentang);
      $("#button-export-terpilih_pdf").prop('disabled', !ada_tercentang);
  });

  // Checkbox anak Belum Upload
  $("#table-belum tbody").on('click', '.checkboxcb-child_belum', function() {
      if (!$(this).prop('checked')) {
          $("#select_all_belum").prop('checked', false);
      }
      let semua_checkbox = $("#table-belum tbody .checkboxcb-child_belum:checked");
      let ada_tercentang = (semua_checkbox.length > 0);
      $("#button-export-terpilih-belum").prop('disabled', !ada_tercentang);
      $("#button-export-terpilih_pdf_belum").prop('disabled', !ada_tercentang);
  });

  $(document).ready(function() {
    //Sudah upload bukti kas
    if ( $.fn.DataTable.isDataTable('#table') ) {
        $('#table').DataTable().clear().destroy();
    }
    $('#table').DataTable({
      processing: true,
      serverSide: true,
      pageLength: 10,
      ajax: {
          url: '{{ route("pembayaran.data.sudahupload") }}',
          data: function (d) {
              d.rentang_waktu = $('#rentang_waktu').val();
              d.bagian = $('#bagian').val();
              d.vendor = $('#vendor').val();
              d.posisi_terkini = $('#posisi_terkini').val();
              d.status_bayar = $('#status_bayar').val();
          }
      },
      columns: [
            {
                data: null, 
                name: 'checkbox',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<input type="checkbox" class="cb-child" value="'+row.spp_id+'">';
                }
            },
            { data: 'master_bagian_nama', name: 'master_bagian.master_bagian_nama' },
            { data: 'tanggal', name: 'spp.spp_tanggal' },
            { data: 'sppb_no', name: 'sppb.sppb_no' },
            {
                data: 'sppb_uraian', 
                name: 'sppb_uraian',
                render: function(data, type, row) {
                    return data ? data.substring(0, 100) + '...' : '-'; 
                },
                 searchable: true
            },
            { 
                data: 'sppb_total', 
                name: 'sppb.sppb_total',
                render: function(data, type, row) {
                    return data ? 'Rp ' + parseInt(data).toLocaleString('id-ID') : '-';
                }
            },
            { data: 'sppn_no', name: 'sppn.sppn_no' },
            { 
                data: 'sppn_uraian', 
                name: 'sppn_uraian',
                render: function(data, type, row) {
                    return data ? data.substring(0, 100) + '...' : '-';
                },
                 searchable: true
            },
            { 
                data: 'sppn_jumlah', 
                name: 'sppn.sppn_jumlah',
                render: function(data, type, row) {
                    return data ? 'Rp ' + parseInt(data).toLocaleString('id-ID') : '-';
                }
            },
            { data: 'posisi_dinamis', name: 'posisi_dinamis', orderable: false, searchable: false },
            { data: 'status_pembayaran', name: 'status_pembayaran', orderable: false }
        ],
        search: {
            regex: true // opsional, untuk pencarian regex
        },
        order: [[2, 'desc']], 
        language: {
            processing: "Memuat data...",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    //Belum upload bukti kas a
    if ( $.fn.DataTable.isDataTable('#table-belum') ) {
        $('#table-belum').DataTable().clear().destroy();
    }
    $('#table-belum').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            url: '{{ route("pembayaran.data.belumupload") }}',
            data: function (d) {
                d.rentang_waktu = $('#rentang_waktu').val();
                d.bagian = $('#bagian').val();
                d.vendor = $('#vendor').val();
                d.posisi_terkini = $('#posisi_terkini').val();
                d.status_bayar = $('#status_bayar').val();
            }
        },
        columns: [
            {
                data: null, // Tidak perlu data dari server
                name: 'checkbox',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<input type="checkbox" class="checkboxcb-child_belum" value="'+row.spp_id+'">';
                }
            },
            { data: 'master_bagian_nama', name: 'master_bagian.master_bagian_nama' },
            { data: 'tanggal', name: 'spp.spp_tanggal' },
            { data: 'sppb_no', name: 'sppb.sppb_no' },
            { 
                data: 'sppb_uraian', 
                name: 'sppb_uraian',
                render: function(data, type, row) {
                    return data ? data.substring(0, 50) + '...' : '-';
                },
                 searchable: true
            },
            { 
                data: 'sppb_total', 
                name: 'sppb.sppb_total',
                render: function(data, type, row) {
                    return data ? 'Rp ' + parseInt(data).toLocaleString('id-ID') : '-';
                }
            },
            { data: 'sppn_no', name: 'sppn.sppn_no' },
            { 
                data: 'sppn_uraian', 
                name: 'sppn_uraian',
                render: function(data, type, row) {
                    return data ? data.substring(0, 50) + '...' : '-';
                },
                 searchable: true
            },
            { 
                data: 'sppn_jumlah', 
                name: 'sppn.sppn_jumlah',
                render: function(data, type, row) {
                    return data ? 'Rp ' + parseInt(data).toLocaleString('id-ID') : '-';
                }
            },
            { data: 'posisi_dinamis', name: 'posisi_dinamis', orderable: false, searchable: false },
            { data: 'status_pembayaran', name: 'status_pembayaran', orderable: false }
        ],
        order: [[2, 'desc']], // Order by tanggal descending
        language: {
            processing: "Memuat data...",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });
  });

  function exportTerpilih() {
    let checkbox_terpilih = $("#table tbody .cb-child:checked")
    let semua_id = []
    $.each(checkbox_terpilih,function(index,elm){
      semua_id.push(elm.value)
    })
    // let ids = semua_id.join(',')
    $("#button-export-terpilih").prop('disabled',true)
    $("#inputID").val(semua_id)
    $("#form-export-terpilih").submit()
    console.log(semua_id);
    // $.ajax({
    //   url:"{{url('')}}/karyawan/export_terpilih",
    //   method:'POST',
    //   data:{ids:semua_id},
    //   success:function(res){
    //     console.log(res)
    //     $("#button-export-terpilih").prop('disabled',false)
    //   }
    // })
  }
  
  function exportTerpilih_pdf() {
    let checkbox_terpilih = $("#table tbody .cb-child:checked")
    let semua_id = []
    $.each(checkbox_terpilih,function(index,elm){
      semua_id.push(elm.value)
    })
    // let ids = semua_id.join(',')
    $("#button-export-terpilih_pdf").prop('disabled',true)
    $("#inputID_pdf").val(semua_id)
    $("#form-export-terpilih_pdf").submit()
    console.log(semua_id);
    // $.ajax({
    //   url:"{{url('')}}/karyawan/export_terpilih",
    //   method:'POST',
    //   data:{ids:semua_id},
    //   success:function(res){
    //     console.log(res)
    //     $("#button-export-terpilih").prop('disabled',false)
    //   }
    // })
  }
  
  function exportTerpilihBelum() {
    let checkbox_terpilih = $("#table-belum tbody .checkboxcb-child_belum:checked")
    let semua_id = []
    $.each(checkbox_terpilih,function(index,elm){
      semua_id.push(elm.value)
    })
    // let ids = semua_id.join(',')
    $("#button-export-terpilih-belum").prop('disabled',true)
    $("#inputIDBelum").val(semua_id)
    $("#form-export-terpilih-belum").submit()
    console.log(semua_id);
  }

  function exportTerpilih_pdf_belum() {
    let checkbox_terpilih = $("#table-belum tbody .checkboxcb-child_belum:checked")
    let semua_id = []
    $.each(checkbox_terpilih,function(index,elm){
      semua_id.push(elm.value)
    })
    // let ids = semua_id.join(',')
    $("#button-export-terpilih_pdf_belum").prop('disabled',true)
    $("#inputID_pdf_belum").val(semua_id)
    $("#form-export-terpilih_pdf_belum").submit()
    console.log(semua_id);
    // $.ajax({
    //   url:"{{url('')}}/karyawan/export_terpilih",
    //   method:'POST',
    //   data:{ids:semua_id},
    //   success:function(res){
    //     console.log(res)
    //     $("#button-export-terpilih").prop('disabled',false)
    //   }
    // })
  }

  function advanced_search() {
    $("#modal_advanced_search").modal('show')
  }
  
</script>




@endsection

@section('footer')
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/piexif.min.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/sortable.min.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/fileinput.min.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/themes/fa/theme.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/locales/id.js"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
@endsection
