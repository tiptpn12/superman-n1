@extends('template.master')
@section('title', 'SPP')

@section('header')
    <link rel="stylesheet" href="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/css/fileinput.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">



@endsection
@section('open')
    active
@endsection
@section('konten')
    <?php   
    $grup_id = Session::get('grup_ui');
    $hakakses = Session::get('hak_akses');
    $bagian = Session::get('bagian');
    $level = Session::get('level');
    $company = session::get('company');
    ?>

    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                          <h4 class="panel-title">Tabel SPPb/SPPn</h4>
                          <hr>
                          <div class="row">
                            <div class="col-md-2">
                                <select name="" class="form-control" id="status_spp">
                                    <option {{ Request::get('status') == 'todolist' ? 'selected' : ''}} value="todolist">To Do List</option>
                                    <option {{ Request::get('status') == 'revisi' ? 'selected' : ''}} value="revisi">Revisi</option>
                                    <option {{ Request::get('status') == 'sedang_proses' ? 'selected' : ''}} value="sedang_proses">Sedang Proses</option>
                                    <option {{ Request::get('status') == 'done' ? 'selected' : ''}} value="done">Sudah Selesai</option>
                                    {{-- <option value="">Dibatalkan</option> --}}
                                </select>
                            </div>
                            {{-- <div class="col-md-2">
                                <select name="" class="form-control" id="">
                                    <option value="">Divisi</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="" class="form-control" id="">
                                    <option value="">Kode SAP Vendor SPPB</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="button" class="btn btn-danger" value="Cari">
                            </div> --}}

                          </div>
                        </div>
                        <div class="panel-body">
                            <div class="tab-pane fade in active" id="tab-to-do-list-petugas">
                                <table class="table table-bordered table-striped nowrap" style="width: 100%">
                                <thead>
                                  <tr>
                                    <th rowspan="2">No. </th>
                                    <th rowspan="2">Tanggal PP</th>
                                    <th colspan="3">PPb</th>
                                    <th colspan="3">PPn</th>
                                    <th rowspan="2">Status</th>
                                    <!-- <th rowspan="2">Status Pembayaran</th> -->
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
                                    @foreach ($data as $d => $s)
                                    <tr>
                                        <td>{{$d+1}}</td>
                                        <td style="display:none;">{{$s->spp_id}}</td>
                                        <td><strong>{{date('d-m-Y',strtotime($s->tanggal_spp))}}</strong></td>
                                        <td ><strong>{{$s->sppb_no}}</strong></td>
                                        <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppb_uraian_uraian)),15)}}</strong></td>
                                        <td><strong>Rp.{{number_format($s->sppb_jumlah)}}</strong></td>
                                        <td><strong>{{$s->sppn_no}}</strong></td>
                                        <td><strong>{{Str::limit(html_entity_decode(strip_tags($s->sppn_uraian_uraian)),15)}}</strong></td>
                                        <td><strong>Rp.{{number_format($s->sppn_jumlah)}}</strong></td>
                                        @if($s->sppd_status == 100)
                                          <td><strong>Selesai</strong></td>
                                        @else
                                          <td><strong>{{$s->master_hak_akses_nama}}</strong></td>
                                        @endif
                                        @if (Request::get('status') == 'todolist' || Request::get('status') == null )
                                        
                                            <td>
                                                @if($s->sppd_status == 1 && $s->sppd_posisi == $hakakses)
                                                <a class="btn btn-success btn-sm" onclick="terima({{$s->spp_id}})" title="Terima" ><i class="fa fa-check"></i></a>
                                                @elseif($s->sppd_status == 2 && $s->sppd_posisi == $hakakses)
                                                <button type="button" class="btn btn-warning btn-sm" onclick="revisi({{$s->spp_id}})" title="Revisi" ><i class="fa fa-arrow-left"></i></button>
                                                @if($grup_id == 2)
                                                <button type="button" class="btn btn-success btn-sm" onclick="kirim({{$s->spp_id}})" title="Kirim" ><i class="fa fa-arrow-right"></i></button>
                                                @endif  
                                                @if($s->sppd_proses == 1 && $s->sppd_proses == 2 )
                                                <a type="button" class="btn btn-warning btn-sm" href="{{ url('sppd/edit/'.$s->spp_id) }}" title="Edit Data" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                @endif
                                                @if($grup_id == 7)
                                                    @if($s->spp_no_dokumen)
                                                    <button type="button" class="btn btn-success btn-sm" onclick="kirim({{$s->spp_id}})" title="Kirim" ><i class="fa fa-arrow-right"></i></button>
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="upload_no_doc({{$d}},{{$s->spp_id}},{{$s->spp_no_dokumen}})" style="background-color: #800000; border-color: #800000" title="Upload No Doc"><i class="fa fa-file"></i></button>
                                                    @else
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="upload_no_doc({{$d}},{{$s->spp_id}},{{$s->spp_no_dokumen}})" style="background-color: #7CFC00 ;border-color: #7CFC00" title="Upload No Doc"><i class="fa fa-file"></i></button>
                                                    @endif
                                                @endif
                                                @endif
                                                @if($s->sppd_status == 3 && $s->sppd_posisi == $hakakses)
                                                <button type="button" class="btn btn-danger btn-sm" onclick="kirim({{$s->spp_id}})" title="Kirim" ><i class="fa fa-arrow-left"></i></button>
                                                @endif
                                                {{-- <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak_to_do_list[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button> --}}
                                                <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                                                <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                                            </td>

                                          @elseif (Request::get('status') == 'done')
                                            <td> <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>    </td>
                                          
                                          @elseif(Request::get('status') == 'sedang_proses')
                                             <td>
                                                {{-- <button type="button" class="btn btn-primary btn-sm" onclick="rekam_jejak({{ json_encode( $rekam_jejak[$d] )}})" title="Rekam Jejak" ><i class="fa fa-map-o"></i></button> --}}
                                                <button type="button" class="btn btn-info btn-sm" onclick="window.open('{{ url('spp/detail/'.$s->spp_id) }}')" title="Detail" ><i class="fa fa-info"></i></button>
                                                <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung({{$s->spp_id}})" title="Upload Dokumen Tambahan" ><i class="fa fa-upload"></i></button>
                                            </td>
                                        
                                        @endif
                                      </tr>
                                    @endforeach
        
                                </tbody>
                              </table>
                            </div>
                        </div>
                      </div>
                </div>
            </div>

            </div>
            <!-- END MAIN CONTENT -->
        </div>


        {{-- Modal ADVANCED SEARCH --}}
        <div id="modal_advanced_search" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <form action="{{ route('advanced_search_sppd') }}" method="post" id="form_advanced_search"
                        enctype="multipart\form-data">
                        {{ csrf_field() }}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Advanced Search</h4>
                            <input type="hidden" id="index_advanced_search" name="index_advanced_search" value="">
                        </div>
                        <div class="modal-body">
                            @if ($hakakses == 10 || $hakakses == 11 || $hakakses == 18)
                                <div class="form-group" id="advanced_search_bagian">
                                    <label>Divisi :</label><br>
                                    <select class="form-control" name="bagian">

                                        <option value="{{ $bagian_id->master_bagian_id }}">
                                            {{ $bagian_id->master_bagian_nama }}</option>

                                    </select>
                                </div>
                            @else
                                <div class="form-group" id="advanced_search_bagian">
                                    <label>Divisi :</label><br>
                                    <select class="form-control" name="bagian">
                                        <option value="semua">Tampilkan Semua</option>

                                    </select>
                                </div>
                            @endif
                            <!-- <div class="form-group" id="advanced_search_bagian">
                <label>Vendor :</label><br>
                <select class="form-control" name="vendor">
                  <option value="semua">Tampilkan Semua</option>
               
                </select>
              </div> -->
                            <div class="form-group" id="advanced_search_rentang_waktu">
                                <label>Rentang Waktu :</label><br>
                                <input type="text" class="form-control date-range" name="rentang_waktu">
                            </div>
                            <div class="form-group" id="advanced_search_kode_sap">
                                <label>Kode SAP Vendor SPPB</label>
                                <select class="form-control selectpicker" data-live-search="true" name="kode_sap_sppb">
                                    <option value="semua" selected>-- Pilih Kode SAP Vendor SPPB --</option>

                                </select>
                            </div>
                            <div class="form-group" id="advanced_search_kode_sap">
                                <label>Kode SAP Vendor SPPN</label>
                                <select class="form-control selectpicker" data-live-search="true" name="kode_sap_sppn">
                                    <option value="semua" selected>-- Pilih Kode SAP Vendor SPPN --</option>

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
                                <li id="tab_bayar"><a href="#tab-pembayaran" role="tab"
                                        data-toggle="tab">Pembayaran</a></li>
                                <li id="tab_terima"><a href="#tab-penerimaan" role="tab"
                                        data-toggle="tab">Penerimaan</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade in" id="tab-pembayaran">

                            <form action="" id="form-bayar" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" id="form_a" name="form_a">

                                <input type="hidden" id="id_sppb_bayar" name="id_sppb_bayar">
                                <div id="bayar_sppb">
                                    <div class="modal-body">
                                        <div class="form-group" hidden>
                                            <label>Nomor Bukti Kas :</label><br>
                                            <input type="text" class="form-control"
                                                placeholder="Nomor bukti kas pengeluaran" id="nomor_bukti_kas_sppb"
                                                name="nomor_bukti_kas_sppb" maxlength="10" value="-">
                                        </div>
                                        <div class="form-group">
                                            <label>Tanggal Pembayaran :</label><br>
                                            <input type="text" class="form-control date"
                                                placeholder="Tanggal pembayaran" id="tanggal_bayar_sppb"
                                                name="tanggal_bayar_sppb" value="{{ DATE('d-m-Y') }}" required>
                                        </div>
                                        <div class="form-group" hidden>
                                            <label>Kode Rekening :</label><br>
                                            <input type="text" class="form-control" value="-" id="rekening_sppb"
                                                placeholder="Kode rekening" onclick="kode_rekening_sppb()">
                                            <input type="hidden" id="rekening_sppb_1" name="rekening_bank_sppb">
                                            <input type="hidden" id="kode_kbb_bayar_sppb" name="sppb_kode_kbb_bayar">
                                            <input type="hidden" id="kode_sap_bayar_sppb" name="sppb_kode_sap_bayar">
                                        </div>
                                        <div class="form-group">
                                            <label>Bukti Transfer : </label><br>
                                            <div id="bukti_transfer_sppb">
                                                <input type="file" name="bukti_sppb" class="file"
                                                    accept="application/pdf, image/*" placeholder="Bukti Transfer"
                                                    autocomplete="off" required>
                                            </div>

                                            <a href="#" target="_blank" id="bukti_sppb"></a>
                                            <button type="button" class="btn btn-warning btn-sm" id="remove_bukti_sppb"
                                                onclick="hapus_bukti_sppb()"><i class="fa fa-pencil"
                                                    aria-hidden="true"></i></button>

                                        </div>
                                    </div>
                                </div>
                                <div id="footer_submit_sppb" class="modal-footer">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                    <button type="button" class="btn btn-danger"
                                        onclick="clear_spp_bayar(0)">Clear</button>
                                </div>
                                <div id="footer_edit_sppb" class="modal-footer">
                                    <button type="button" class="btn btn-warning"
                                        onclick="edit_bayar_sppb(0)">Edit</button>

                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade in" id="tab-penerimaan">
                            <form action="" id="form-terima" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" id="form_b" name="form_b">

                                <div id="bayar_sppn">
                                    <div class="modal-body">
                                        <input type="hidden" id="id_sppn_terima" name="id_sppn_terima">
                                        <div class="form-group" hidden>
                                            <label>Nomor Bukti Kas :</label><br>
                                            <input type="text" class="form-control" value="-"
                                                placeholder="Nomor bukti kas penerimaan" id="nomor_bukti_kas_sppn"
                                                name="nomor_bukti_kas_sppn" maxlength="10">
                                        </div>
                                        <div class="form-group">
                                            <label>Tanggal Penerimaan :</label><br>
                                            <input type="text" class="form-control date" id="tanggal_terima_sppn"
                                                name="tanggal_terima_sppn" value="{{ DATE('d-m-Y') }}" required>
                                        </div>
                                        <div class="form-group" hidden>
                                            <label>Kode Rekening :</label><br>
                                            <input type="text" class="form-control" value="-"id="rekening_sppn"
                                                placeholder="Kode rekening" onclick="kode_rekening_sppn()">
                                            <input type="hidden" id="rekening_sppn_1" name="rekening_bank_sppn">
                                            <input type="hidden" id="sppn_kode_kbb_terima" name="sppn_kode_kbb_terima">
                                            <input type="hidden" id="sppn_kode_sap_terima" name="sppn_kode_sap_terima">
                                        </div>
                                        <div class="form-group">
                                            <label>Bukti Transfer : </label><br>
                                            <div id="bukti_transfer_sppn">
                                                <input type="file" name="bukti_sppn" class="file"
                                                    accept="application/pdf, image/*" placeholder="Bukti Transfer"
                                                    autocomplete="off" required>
                                            </div>

                                            <a href="#" target="_blank" id="bukti_sppn"></a>
                                            <button type="button" class="btn btn-warning btn-sm" id="remove_bukti_sppn"
                                                onclick="hapus_bukti_sppn()"><i class="fa fa-pencil"
                                                    aria-hidden="true"></i></button>

                                        </div>
                                    </div>
                                </div>
                                <div id="footer_submit_sppn" class="modal-footer">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                    <button type="button" class="btn btn-danger"
                                        onclick="clear_spp_bayar(1)">Clear</button>
                                </div>
                                <div id="footer_edit_sppn" class="modal-footer">
                                    <button type="button" class="btn btn-warning"
                                        onclick="edit_bayar_sppb(1)">Edit</button>

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
                                <li id="tab_bayar_cbk"><a href="#tab-pembayaran_cbk" role="tab"
                                        data-toggle="tab">SPPb</a></li>
                                <li id="tab_terima_cbk"><a href="#tab-penerimaan_cbk" role="tab"
                                        data-toggle="tab">SPPn</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade in" id="tab-pembayaran_cbk">

                            <form action="" id="form-bayar_cbk" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" id="form_a_cbk" name="form_a" value="">

                                <input type="hidden" id="id_sppb_bayar_cbk" name="id_sppb_bayar">
                                <div id="bayar_sppb_cbk">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <input type="hidden" id="cetak_bukti_kas_metode_pembayaran"
                                                name="cetak_bukti_kas_metode_pembayaran">
                                        </div>
                                        <div class="form-group">
                                            <label>Nomor Cek/Giro :</label><br>
                                            <input type="text" class="form-control" id="nomor_bukti_kas_sppb_cbk"
                                                name="nomor_bukti_kas_sppb" maxlength="10"
                                                placeholder="nomor cek/giro pembayaran" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Kode Rekening :</label><br>
                                            <input type="text" class="form-control" id="rekening_sppb_cbk"
                                                onclick="kode_rekening_sppb()" placeholder="rekening pembayaran"
                                                autocomplete="off" required>
                                            <input type="hidden" id="rekening_sppb_1_cbk" name="rekening_bank_sppb">
                                        </div>
                                        <div class="form-group">
                                            <label>Penerima :</label><br>
                                            <input type="text" class="form-control" id="penerima_cbk" name="penerima"
                                                autocomplete="off" placeholder="Penerima" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Alamat :</label><br>
                                            <input type="text" class="form-control" id="alamat_penerima_cbk"
                                                name="alamat_sppb" autocomplete="off" placeholder="Penerima" required>
                                        </div>



                                    </div>
                                </div>
                                <div id="footer_submit_sppb_cbk" class="modal-footer">
                                    <button type="submit" class="btn btn-success pisan">Submit</button>
                                    <button type="button" class="btn btn-danger pisan"
                                        onclick="clear_spp_bayar(0)">Clear</button>
                                </div>
                                <div id="footer_edit_sppb_cbk" class="modal-footer">
                                    <!-- <button type="button" class="btn btn-warning" onclick="edit_bayar_sppb(0)">Edit</button> -->
                                    <button type="button" id="cetakbuktikas" class="btn btn-success cetakbuktikas"
                                        value="">Cetak Bukti Kas</button>


                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade in" id="tab-penerimaan_cbk">
                            <form action="" id="form-terima_cbk" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" id="form_b_cbk" name="form_b" value="">
                                <div id="bayar_sppn">
                                    <div class="modal-body">
                                        <input type="hidden" id="id_sppn_terima_cbk" name="id_sppn_terima">
                                        <div class="form-group">
                                            <label>Nomor Cek/Giro :</label><br>
                                            <input type="text" class="form-control" id="nomor_bukti_kas_sppn_cbk"
                                                name="nomor_bukti_kas_sppn" maxlength="10"
                                                placeholder="nomor cek/giro penerimaan" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Kode Rekening :</label><br>
                                            <input type="text" class="form-control" autocomplete="off"
                                                id="rekening_sppn_cbk" onclick="kode_rekening_sppn()"
                                                placeholder="rekening penerimaan" required>
                                            <input type="hidden" id="rekening_sppn_1_cbk" name="rekening_bank_sppn">
                                        </div>
                                        <div class="form-group">
                                            <label>Diterima Dari :</label><br>
                                            <input type="text" class="form-control" autocomplete="off"
                                                name="diterima_dari" id="diterima_dari_cbk"placeholder=" Diterima Dari"
                                                required>

                                        </div>
                                        <div class="form-group">
                                            <label>Alamat :</label><br>
                                            <input type="text" class="form-control" autocomplete="off"
                                                id="alamat_diterima_dari_cbk" name="alamat_sppn"
                                                placeholder=" Diterima Dari" required>
                                        </div>



                                    </div>
                                </div>
                                <div id="footer_submit_sppn_cbk" class="modal-footer">
                                    <button type="submit" class="btn btn-success pisan">Submit</button>
                                    <button type="button" class="btn btn-danger pisan"
                                        onclick="clear_spp_bayar(1)">Clear</button>
                                </div>
                                <div id="footer_edit_sppn_cbk" class="modal-footer">
                                    <!-- <button type="button" class="btn btn-warning" onclick="edit_bayar_sppb(1)">Edit</button> -->
                                    <button type="button" id="cetakbuktikas" class="btn btn-success cetakbuktikas"
                                        value="">Cetak Bukti Kas</button>

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
                        {{ csrf_field() }}
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
                                    <input type="text" class="form-control" id="detil_rekening_sppb" disabled>
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

        {{-- Modal No Doc --}}
        <div id="modal_no_doc" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('nomor_dokumen') }}" id="form_no_doc" method="post"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="modal_header">
                            <input type="hidden" id="no_doc_id" name="no_doc_id">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Input No Dokumen</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Input No Doc SAP</label>
                                @if ($hakakses == 13)
                                    <input type="text" id="no_doc" name="no_doc" class="form-control" required>
                                @else
                                    <input type="text" id="no_doc" name="no_doc" class="form-control" required
                                        readonly>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            @if ($hakakses == 13)
                                <button type="submit" class="btn btn-success">Submit</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            @else
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- End modal No Doc --}}

        {{-- Modal KIRIM --}}
        <div id="modal_kirim" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <form action="" id="form-kirim" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Kirim PP</h4>
                        </div>
                        <div class="modal-body">

                            <div class="form-group" id="pilih_file_spp" onclick="pilih_file()">
                                <label class="fancy-radio">
                                    <input name="upload_file" id="file_lama" value="file_lama" type="radio"
                                        checked="checked">
                                    <span style="font-size:17px"><i></i>Gunakan File Lama <a href=""
                                            id="file_file_lama" target="_blank">(lihat)</a></span>
                                </label>
                                <label class="fancy-radio">
                                    <input name="upload_file" id="file_baru" value="file_baru" type="radio">
                                    <span style="font-size:17px"><i></i>Upload File Baru</span>
                                </label>
                            </div>

                            <div class="form-group" id="upload_file_baru" style="display:none">
                                <label>Upload File PP yang sudah di TTD Kepala Bagian :</label><br>
                                <input type="file" id="spp_kabag" name="spp_kabag" class="file"
                                    accept="application/pdf, image/*" placeholder="PP tanda tangan Kabag"
                                    autocomplete="off" required>
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
                    <form action="{{ route('storedokumentambahan') }}" id="form_dokumen_tambahan" method="post"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Upload Dokumen Tambahan PP</h4>
                        </div>
                        <input type="hidden" id="spp_id" name="spp_id">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Upload File Dokumen Tambahan :</label><br>
                                <span style="color: red">*Format Gambar/PDF</span>
                                <input type="file" class="file-multiple" name="dokumen_tambahan[]"
                                    accept="application/pdf, image/*" multiple>
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
                    <form action="{{ route('storebuktikas') }}" id="form_upload_bukti_kas" method="post"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Upload Bukti Kas/Bank</h4>
                        </div>
                        <input type="hidden" id="bukti_spp_id" name="bukti_spp_id">
                        <div class="modal-body">
                            <div class="form-group" id="upload_bukti">
                                <label>Upload File Bukti Kas/Bank :</label><br>
                                <span style="color: red">*Format Gambar/PDF</span>
                                <input type="file" class="file" name="file_bukti_kas"
                                    accept="application/pdf, image/*" required>
                            </div>
                            <div class="form-group" id="file_bukti_kas">
                                <a href="#" target="_blank" id="bukti_kas_lama"></a>
                                <button type="button" class="btn btn-warning btn-sm" id="remove_bukti_kas"
                                    onclick="edit_bukti_kas()"><i class="fa fa-pencil" aria-hidden="true"></i></button>
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
                        {{ csrf_field() }}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Revisi PP</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Keterangan Revisi PP :</label><br>
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

        {{-- Modal BATAL --}}
        <div id="modal_batal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <form action="" method="get" id="form-batal" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Batal PP</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Keterangan Batal PP :</label><br>
                                <textarea class="form-control" id="keterangan_batal" name="batal" placeholder="Batal" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="confirm_batal()">Submit</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- End Modal BATAL --}}

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
                            <table id="table_rekeningsppb" class="table  table-bordered table-striped nowrap"
                                style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No. </th>
                                        <!-- <th>No KBB</th> -->
                                        <th>No SAP</th>
                                        <th>Keterangan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

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
                            <table id="table_rekeningsppn" class="table  table-bordered table-striped nowrap"
                                style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No. </th>
                                        <th>No KBB</th>
                                        <th>No SAP</th>
                                        <th>Keterangan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

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
        <script>
           $("#status_spp").change(function(){
                status = $(this).val();
                window.location = "{{url('dashboard-v2')}}?status="+status
            })
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                if (index_cetak == 1) {
                    var url = 'spp/cetak/' + id_cetak;
                    setTimeout(() => $('<a href="' + url + '" target="_blank"></a>')[0].click(), 500);
                }
                //window.alert(index_cetak);
                if (index_adv == 1) {
                    document.getElementById("tab-sedang-proses").className = "active";
                    document.getElementById("tab-sedang-proses-petugas").className = "tab-pane fade in active";
                    document.getElementById("tab-sudah-selesai").className = "";
                    document.getElementById("tab-sudah-selesai-petugas").className = "tab-pane fade";
                    document.getElementById("tab-dibatalkan").className = "";
                    document.getElementById("tab-dibatalkan-petugas").className = "tab-pane fade";
                    document.getElementById("tab-to-do-list").className = "";
                    document.getElementById("tab-to-do-list-petugas").className = "tab-pane fade";
                    document.getElementById("tab-revisi").className = "";
                    document.getElementById("tab-revisi-petugas").className = "tab-pane fade";
                } else if (index_adv == 2) {
                    document.getElementById("tab-sudah-selesai").className = "active";
                    document.getElementById("tab-sudah-selesai-petugas").className = "tab-pane fade in active";
                    document.getElementById("tab-sedang-proses").className = "";
                    document.getElementById("tab-sedang-proses-petugas").className = "tab-pane fade";
                    document.getElementById("tab-to-do-list").className = "";
                    document.getElementById("tab-to-do-list-petugas").className = "tab-pane fade";
                    document.getElementById("tab-revisi").className = "";
                    document.getElementById("tab-revisi-petugas").className = "tab-pane fade";
                } else if (index_adv == 3) {
                    document.getElementById("tab-dibatalkan").className = "active";
                    document.getElementById("tab-dibatalkan-petugas").className = "tab-pane fade in active";
                    document.getElementById("tab-sedang-proses").className = "";
                    document.getElementById("tab-sedang-proses-petugas").className = "tab-pane fade";
                    document.getElementById("tab-sudah-selesai").className = "";
                    document.getElementById("tab-sudah-selesai-petugas").className = "tab-pane fade";
                    document.getElementById("tab-to-do-list").className = "";
                    document.getElementById("tab-to-do-list-petugas").className = "tab-pane fade";
                    document.getElementById("tab-revisi").className = "";
                    document.getElementById("tab-revisi-petugas").className = "tab-pane fade";
                } else if (index_adv == 4) {
                    document.getElementById("tab-to-do-list").className = "active";
                    document.getElementById("tab-to-do-list-petugas").className = "tab-pane fade in active";
                    document.getElementById("tab-sudah-selesai").className = "";
                    document.getElementById("tab-sudah-selesai-petugas").className = "tab-pane fade";
                    document.getElementById("tab-dibatalkan").className = "";
                    document.getElementById("tab-dibatalkan-petugas").className = "tab-pane fade";
                    document.getElementById("tab-sedang-proses").className = "";
                    document.getElementById("tab-sedang-proses-petugas").className = "tab-pane fade";
                    document.getElementById("tab-revisi").className = "";
                    document.getElementById("tab-revisi-petugas").className = "tab-pane fade";
                } else if (index_adv == 5) {
                    document.getElementById("tab-to-do-list").className = "";
                    document.getElementById("tab-to-do-list-petugas").className = "tab-pane fade";
                    document.getElementById("tab-sudah-selesai").className = "";
                    document.getElementById("tab-sudah-selesai-petugas").className = "tab-pane fade";
                    document.getElementById("tab-dibatalkan").className = "";
                    document.getElementById("tab-dibatalkan-petugas").className = "tab-pane fade";
                    document.getElementById("tab-sedang-proses").className = "";
                    document.getElementById("tab-sedang-proses-petugas").className = "tab-pane fade";
                    document.getElementById("tab-revisi").className = "active";
                    document.getElementById("tab-revisi-petugas").className = "tab-pane fade";
                } else {
                    document.getElementById("tab-to-do-list").className = "active";
                    document.getElementById("tab-to-do-list-petugas").className = "tab-pane fade in active";
                    document.getElementById("tab-sudah-selesai").className = "";
                    document.getElementById("tab-sudah-selesai-petugas").className = "tab-pane fade";
                    document.getElementById("tab-dibatalkan").className = "";
                    document.getElementById("tab-dibatalkan-petugas").className = "tab-pane fade";
                    document.getElementById("tab-sedang-proses").className = "";
                    document.getElementById("tab-sedang-proses-petugas").className = "tab-pane fade";
                    document.getElementById("tab-revisi").className = "";
                    document.getElementById("tab-revisi-petugas").className = "tab-pane fade";
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
                    dropZoneTitle: "Drag & drop banyak file sekaligus disini..",
                    fileActionSettings: {
                        showRemove: true,
                        showUpload: false
                    }
                });
            });

            function advanced_search(index, user) {
                $('#index_advanced_search').val(index);
                if (index == 1) {
                    $('#advanced_search_rentang_waktu').show();

                    if (user == 1) {
                        $('#advanced_search_posisi_terkini').hide();
                        $('#advanced_search_status_bayar1').hide();

                    } else {
                        $('#advanced_search_posisi_terkini').show();
                        $('#advanced_search_status_bayar1').show();

                    }
                    $('#advanced_search_bagian').show();

                    $('#advanced_search_status_bayar2').hide();
                    $('#modal_advanced_search').modal('show');
                } else if (index == 2) {
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

            function clear_spp_bayar(id) {
                if (id == 0) {
                    document.getElementById('form-bayar').reset();
                    $("#bukti_sppb").hide();
                    $("#remove_bukti_sppb").hide();
                    $("#bukti_transfer_sppb").show();
                } else {
                    document.getElementById('form-terima').reset();
                    $("#bukti_sppn").hide();
                    $("#remove_bukti_sppn").hide();
                    $("#bukti_transfer_sppn").show();
                }
            }

            function hapus_bukti_sppb() {
                $("#bukti_sppb").hide();
                $("#remove_bukti_sppb").hide();
                $("#bukti_transfer_sppb").show();
            }

            function hapus_bukti_sppn() {
                $("#bukti_sppn").hide();
                $("#remove_bukti_sppn").hide();
                $("#bukti_transfer_sppn").show();
            }
            $(".cetakbuktikas").click(function() {
                var str = $(".cetakbuktikas").val();
                window.open('sppd/cetak_bukti_kas/' + str);
            });

            function edit_bayar_sppb(form) {
                if (form == 0) {
                    var id = $("#id_sppb_bayar").val();
                    $("#form").val(0);
                    $("#form-bayar").attr('action', 'spp/update_bayar/' + id);
                    document.getElementById('nomor_bukti_kas_sppb').removeAttribute('readonly');
                    document.getElementById('rekening_sppb').disabled = false;
                    document.getElementById('tanggal_bayar_sppb').disabled = false;
                    $("#bukti_transfer_sppb").hide();
                    $("#bukti_sppb").show();
                    $("#remove_bukti_sppb").show();
                    $("#footer_submit_sppb").show();
                    $("#footer_edit_sppb").hide();
                } else {
                    $("#form").val(1);
                    var id = $("#id_sppn_terima").val();
                    $("#form-terima").attr('action', 'spp/update_bayar/' + id);
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

            function pembayaran(id_sppb, id_sppn, form, data_sppb, data_sppn) {
                $("#form_a").val(form);
                $("#form_b").val(form);
                if (data_sppb !== 0 && data_sppb !== null) {
                    var now = new Date(data_sppb.sppb_bayar_tanggal);
                    var tanggal = moment(now).format("DD-MM-YYYY");
                    $("#nomor_bukti_kas_sppb").val(data_sppb.sppb_bayar_nomor_bukti_kas);
                    $("#id_sppb_bayar").val(data_sppb.sppb_bayar_id);
                    $("#nomor_bukti_kas_sppb").attr('readonly', true);
                    $("#rekening_sppb").val(data_sppb.master_rekening_kode_kbb + ' / ' + data_sppb.master_rekening_kode_sap +
                        '(' + data_sppb.master_rekening_keterangan + ')');
                    $("#rekening_sppb").attr('disabled', 'disabled');
                    $("#tanggal_bayar_sppb").val(tanggal);
                    $("#tanggal_bayar_sppb").attr('disabled', 'disabled');
                    $("#rekening_sppb_1").val(data_sppb.master_rekening_id);
                    $("#bukti_transfer_sppb").hide();
                    $("#bukti_sppb").show();
                    $("#remove_bukti_sppb").hide();
                    document.getElementById("bukti_sppb").href = 'dokumen/' + data_sppb.sppb_bayar_bukti;
                    document.getElementById("bukti_sppb").innerHTML = data_sppb.sppb_bayar_bukti;
                    $("#footer_submit_sppb").hide();
                    $("#footer_edit_sppb").show();
                } else {
                    // alert();
                    var now = new Date();
                    var tanggal = moment(now).format("DD-MM-YYYY");
                    $("#nomor_bukti_kas_sppb").val('');
                    $("#id_sppb_bayar").val('');
                    $("#nomor_bukti_kas_sppb").attr('readonly', false);
                    $("#rekening_sppb").val('');
                    $("#rekening_sppb").attr('disabled', false);
                    $("#tanggal_bayar_sppb").val(tanggal);
                    $("#tanggal_bayar_sppb").attr('disabled', false);
                    $("#rekening_sppb_1").val('');
                    $("#remove_bukti_sppb").hide();
                    $("#bukti_sppb").hide();
                    $("#bukti_transfer_sppb").show();
                    $("#footer_submit_sppb").show();
                    $("#footer_edit_sppb").hide();
                }
                if (data_sppn !== 0 && data_sppn !== null) {
                    var now = new Date(data_sppn.sppn_terima_tanggal);
                    var tanggal = moment(now).format("DD-MM-YYYY");
                    $("#id_sppn_terima").val(data_sppn.sppn_terima_id);
                    $("#nomor_bukti_kas_sppn").val(data_sppn.sppn_terima_nomor_bukti_kas);
                    $("#nomor_bukti_kas_sppn").attr('readonly', 'readonly');
                    $("#rekening_sppn").val(data_sppn.master_rekening_kode_kbb + ' / ' + data_sppn.master_rekening_kode_sap +
                        '(' + data_sppn.master_rekening_keterangan + ')');
                    $("#rekening_sppn").attr('disabled', 'disabled');
                    $("#tanggal_terima_sppn").val(tanggal);
                    $("#tanggal_terima_sppn").attr('disabled', 'disabled');
                    $("#rekening_sppn_1").val(data_sppn.master_rekening_id);
                    $("#bukti_transfer_sppn").hide();
                    $("#bukti_sppn").show();
                    $("#remove_bukti_sppn").hide();
                    document.getElementById("bukti_sppn").href = 'dokumen/' + data_sppn.sppn_terima_bukti;
                    document.getElementById("bukti_sppn").innerHTML = data_sppn.sppn_terima_bukti;
                    $("#footer_submit_sppn").hide();
                    $("#footer_edit_sppn").show();
                } else {
                    var now = new Date();
                    var tanggal = moment(now).format("DD-MM-YYYY");
                    $("#id_sppn_terima").val('');
                    $("#nomor_bukti_kas_sppn").val('');
                    $("#nomor_bukti_kas_sppn").attr('readonly', false);
                    $("#rekening_sppn").val('');
                    $("#rekening_sppn").attr('disabled', false);
                    $("#tanggal_terima_sppn").val(tanggal);
                    $("#tanggal_terima_sppn").attr('disabled', false);
                    $("#rekening_sppn_1").val('');
                    $("#remove_bukti_sppn").hide();
                    $("#bukti_sppn_1").hide();
                    $("#bukti_transfer_sppn").show();
                    $("#footer_submit_sppn").show();
                    $("#footer_edit_sppn").hide();
                }
                if (form == 0) {
                    $("#tab_bayar").show();
                    $("#tab_terima").hide();
                    document.getElementById("tab_bayar").className = "active";
                    document.getElementById("tab-pembayaran").className = "tab tab-pane active";
                    document.getElementById("tab_terima").className = "";
                    document.getElementById("tab-penerimaan").className = "tab tab-pane";
                    $("#modal_pembayaran").modal('show');
                    $("#form-bayar").attr('action', 'sppd/bayar/' + id_sppb);

                } else if (form == 1) {
                    $("#tab_terima").show();
                    $("#tab_bayar").hide();
                    document.getElementById("tab_bayar").className = "";
                    document.getElementById("tab-pembayaran").className = "tab tab-pane";
                    document.getElementById("tab_terima").className = "active";
                    document.getElementById("tab-penerimaan").className = "tab tab-pane active";
                    $("#modal_pembayaran").modal('show');
                    $("#form-terima").attr('action', 'sppd/bayar/' + id_sppn);

                } else {

                    document.getElementById("tab_bayar").className = "active";
                    document.getElementById("tab-pembayaran").className = "tab tab-pane active";
                    document.getElementById("tab_terima").className = "";
                    document.getElementById("tab-penerimaan").className = "tab tab-pane";
                    $("#tab_terima").show();
                    $("#tab_bayar").show();
                    $("#modal_pembayaran").modal('show');
                    $("#form-bayar").attr('action', 'sppd/bayar/' + id_sppb);
                    $("#form-terima").attr('action', 'sppd/bayar/' + id_sppn);
                }
            }

            function cetak_bukti_kas(metode_pembayaran, id_spp, id_sppb, id_sppn, form, data_sppb, data_sppn, penerima,
                diterima) {
                $('#table_rekeningsppn').DataTable().clear().destroy();
                $('#table_rekeningsppn').DataTable({
                    processing: false,
                    serverSide: true,
                    ajax: '{{ route('mas_rek') }}',
                    order: [],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'master_rekening_kode_kbb',
                            name: 'master_rekening_kode_kbb'
                        },
                        {
                            data: 'master_rekening_kode_sap',
                            name: 'master_rekening_kode_sap'
                        },
                        {
                            data: 'master_rekening_keterangan',
                            name: 'master_rekening_keterangan'
                        },
                        {
                            data: 'master_rekening_kode_kbb',
                            "render": function(data, type, row) {
                                return `<button type="button" class="btn btn-info btn-sm" onclick="pilih_rekening_sppn('${row.master_rekening_id}','${row.master_rekening_kode_kbb}', '${row.master_rekening_kode_sap}', '${row.master_rekening_keterangan}')" title="Pilih" ><i class="fa fa-check"></i></button>`
                            },
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
                $('#table_rekeningsppb').DataTable().clear().destroy();
                $('#table_rekeningsppb').DataTable({
                    processing: false,
                    serverSide: true,
                    ajax: '{{ route('mas_rek') }}',
                    order: [],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'master_rekening_kode_sap',
                            name: 'master_rekening_kode_sap'
                        },
                        {
                            data: 'master_rekening_keterangan',
                            name: 'master_rekening_keterangan'
                        },
                        {
                            data: 'master_rekening_kode_kbb',
                            "render": function(data, type, row) {
                                return `<button type="button" class="btn btn-info btn-sm" onclick="pilih_rekening_sppb('${row.master_rekening_id}','${row.master_rekening_kode_kbb}', '${row.master_rekening_kode_sap}', '${row.master_rekening_keterangan}')" title="Pilih" ><i class="fa fa-check"></i></button>`
                            },
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
                $("#form_a_cbk").val(form);
                $("#form_b_cbk").val(form);
                $("#cetak_bukti_kas_metode_pembayaran").val(metode_pembayaran);
                $(".cetakbuktikas").prop("value", id_spp);
                // console.log(penerima);
                // console.log(diterima);
                // console.log(data_sppb);
                // console.log(data_sppn);


                // console.log(id_spp);

                // console.log($("#form").val());
                if (data_sppb !== 0) {
                    $("#nomor_bukti_kas_sppb_cbk").val(data_sppb.cek_giro);
                    $("#id_sppb_bayar_cbk").val(data_sppb.sppb_bayar_id);
                    $("#nomor_bukti_kas_sppb_cbk").attr('readonly', 'readonly');
                    $("#rekening_sppb_cbk").val(data_sppb.master_rekening_kode_sap + '(' + data_sppb
                        .master_rekening_keterangan + ')');
                    $("#rekening_sppb_cbk").attr('disabled', 'disabled');
                    $("#rekening_sppb_1_cbk").val(data_sppb.master_rekening_id);
                    $("#penerima_cbk").val(data_sppb.master_vendor_id);
                    $("#alamat_penerima_cbk").val(data_sppb.alamat_sppb);
                    $("#footer_submit_sppb_cbk").hide();
                    $("#footer_edit_sppb_cbk").show();
                    // alert('a');
                } else {

                    $("#penerima_cbk").val('');
                    $("#alamat_penerima_cbk").val('');
                    $("#nomor_bukti_kas_sppb_cbk").val('');
                    $("#id_sppb_bayar_cbk").val('');
                    $("#nomor_bukti_kas_sppb_cbk").attr('readonly', false);
                    $("#rekening_sppb_cbk").val('');
                    $("#rekening_sppb_cbk").attr('disabled', false);
                    $("#rekening_sppb_1_cbk").val('');
                    $("#footer_submit_sppb_cbk").show();
                    $("#footer_edit_sppb_cbk").hide();
                    // alert('b');

                }
                if (data_sppn !== 0) {
                    $("#diterima_dari_cbk").val(data_sppn.master_vendor_id);
                    $("#alamat_diterima_dari_cbk").val(data_sppn.alamat_sppn);
                    $("#id_sppn_terima_cbk").val(data_sppn.sppn_terima_id);
                    $("#nomor_bukti_kas_sppn_cbk").val(data_sppn.cek_giro);
                    $("#nomor_bukti_kas_sppn_cbk").attr('readonly', 'readonly');
                    $("#rekening_sppn_cbk").val(data_sppn.master_rekening_kode_kbb + ' / ' + data_sppn
                        .master_rekening_kode_sap + '(' + data_sppn.master_rekening_keterangan + ')');
                    $("#rekening_sppn_cbk").attr('disabled', 'disabled');
                    $("#rekening_sppn_1_cbk").val(data_sppn.master_rekening_id);
                    $("#footer_submit_sppn_cbk").hide();
                    $("#footer_edit_sppn_cbk").show();
                    // alert('c');

                } else {
                    if (penerima != null && diterima == null) {
                        $("#diterima_dari_cbk").val('');
                        $("#alamat_diterima_dari_cbk").val('');
                        // alert('d11');

                    } else {
                        // alert('d22');
                        $("#diterima_dari_cbk").val(diterima.karyawan_nama);
                        $("#alamat_diterima_dari_cbk").val(diterima.karyawan_alamat);

                    }
                    $("#id_sppn_terima_cbk").val('');
                    $("#nomor_bukti_kas_sppn_cbk").val('');
                    $("#nomor_bukti_kas_sppn_cbk").attr('readonly', false);
                    $("#rekening_sppn_cbk").val('');
                    $("#rekening_sppn_cbk").attr('disabled', false);
                    $("#rekening_sppn_1_cbk").val('');
                    $("#footer_submit_sppn_cbk").show();
                    $("#footer_edit_sppn_cbk").hide();
                    // alert('d');
                }

                if (form == 0) {
                    $("#tab_bayar_cbk").show();
                    $("#tab_terima_cbk").hide();
                    document.getElementById("tab_bayar_cbk").className = "active";
                    document.getElementById("tab-pembayaran_cbk").className = "tab tab-pane active";
                    document.getElementById("tab_terima_cbk").className = "";
                    document.getElementById("tab-penerimaan_cbk").className = "tab tab-pane";
                    $("#modal_cetak_bukti_kas").modal('show');
                    $(".pisan").click(function() {
                        if ($("#nomor_bukti_kas_sppb_cbk").val() == '' || $("#rekening_sppb_cbk").val() == '' || $(
                                "#penerima_cbk").val() == '' || $("#alamat_penerima_cbk").val() == '') {
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
                        } else {
                            $("#form-bayar_cbk").attr('action', 'sppd/bukti_kas/' + id_sppb);
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

                } else if (form == 1) {
                    $("#tab_bayar_cbk").hide();
                    $("#tab_terima_cbk").show();
                    document.getElementById("tab_terima_cbk").className = "active";
                    document.getElementById("tab-penerimaan_cbk").className = "tab tab-pane active";
                    document.getElementById("tab_bayar_cbk").className = "";
                    document.getElementById("tab-pembayaran_cbk").className = "tab tab-pane";
                    $("#modal_cetak_bukti_kas").modal('show');

                    $(".pisan").click(function() {
                        if ($("#nomor_bukti_kas_sppn_cbk").val() == '' || $("#rekening_sppn_cbk").val() == '' || $(
                                "#diterima_dari_cbk").val() == '' || $("#alamat_diterima_dari_cbk").val() == '') {
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
                        } else {
                            $("#form-terima_cbk").attr('action', 'sppd/bukti_kas/' + id_sppn);
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

                } else {
                    $("#tab_bayar_cbk").show();
                    $("#tab_terima_cbk").show();
                    document.getElementById("tab_bayar_cbk").className = "active";
                    document.getElementById("tab-pembayaran_cbk").className = "tab tab-pane active";
                    document.getElementById("tab_terima_cbk").className = "";
                    document.getElementById("tab-penerimaan_cbk").className = "tab tab-pane";
                    $("#modal_cetak_bukti_kas").modal('show');
                    $(".pisan").click(function() {
                        if ($("#nomor_bukti_kas_sppb_cbk").val() == '' || $("#rekening_sppb_cbk").val() == '' || $(
                                "#penerima_cbk").val() == '' || $("#alamat_penerima_cbk").val() == '') {
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
                        } else {
                            $("#form-bayar_cbk").attr('action', 'sppd/bukti_kas/' + id_sppb);
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
                    $(".pisan").click(function() {
                        $(".pisan").hide();
                        $("#form-terima_cbk").attr('action', 'sppd/bukti_kas/' + id_sppn);
                    });
                }
            }

            function kode_rekening_sppb() {
                $('#modal_rekening_sppb').modal('show');
            }

            function kode_rekening_sppn() {
                $('#modal_rekening_sppn').modal('show');
            }

            function kode_penerima_sppb() {
                $('#modal_penerima').modal('show');
            }

            function kode_diterima_dari_sppb() {
                $('#modal_diterima_dari').modal('show');
            }

            function pilih_rekening_sppb(id, kbb, sap, keterangan) {
                // alert(id);
                $("#rekening_sppb_cbk").val(sap + ' (' + keterangan + ')');
                $("#rekening_sppb_1_cbk").val(id);
                $('#modal_rekening_sppb').modal('hide');
            }

            function pilih_penerima(id, nama_bank, alamat) {
                $("#penerima_cbk").val(nama_bank + ' / ' + alamat);
                $("#penerima_1_cbk").val(id);
                $('#modal_penerima').modal('hide');
            }

            function pilih_diterima_dari(id, nama_bank, alamat) {
                $("#diterima_dari_cbk").val(nama_bank + ' / ' + alamat);
                $("#diterima_dari_1_cbk").val(id);
                $('#modal_diterima_dari').modal('hide');
            }

            function pilih_rekening_sppn(id, kbb, sap, keterangan) {

                $('#rekening_sppn_cbk').val(kbb + ' / ' + sap + ' (' + keterangan + ')');
                $('#rekening_sppn_1_cbk').val(id);
                $('#modal_rekening_sppn').modal('hide');
            }

            function upload_kirim(id, file) {
                console.log(id, file);
                $("#modal_kirim").modal('show');
                // window.alert(file);
                if (file) {
                    //window.alert('file');
                    $("#pilih_file_spp").show();
                    $("#upload_file_lama").hide();
                    $("#upload_file_baru").hide();
                    document.getElementById("spp_kabag").removeAttribute("required");
                    $("#form-kirim").attr('action', 'sppd/upload/' + id);

                    $("#file_file_lama").attr('href', 'dokumen/' + file);
                } else {
                    $("#pilih_file_spp").hide();
                    $("#upload_file_lama").hide();
                    $("#upload_file_baru").show();

                }
                $("#pisanae").click(function() {
                    var radio_check_val = "";
                    for (var i = 0; i < document.getElementsByName('upload_file').length; i++) {
                        if (document.getElementsByName('upload_file')[i].checked) {
                            radio_check_val = document.getElementsByName('upload_file')[i].value;
                        }
                    }
                    if (radio_check_val == 'file_lama') {
                        $("#modal_kirim").modal('hide');
                        $("#form-kirim").attr('action', 'sppd/upload/' + id);
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
                    } else {
                        if (document.getElementById("spp_kabag").files.length == 0) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Pilih file dulu!'
                            })
                        } else {
                            $("#modal_kirim").modal('hide');
                            $("#form-kirim").attr('action', 'sppd/upload/' + id);
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


            function pilih_file() {
                var radio_check_val = "";
                for (var i = 0; i < document.getElementsByName('upload_file').length; i++) {
                    if (document.getElementsByName('upload_file')[i].checked) {
                        radio_check_val = document.getElementsByName('upload_file')[i].value;
                    }
                }
                if (radio_check_val == 'file_lama') {
                    document.getElementById("spp_kabag").removeAttribute("required");

                    $("#upload_file_baru").hide();
                } else {
                    $("#upload_file_baru").show();
                    $("#spp_kabag").attr("required", "required");

                }

            }


            function terima(id) {
                Swal.fire({
                    title: 'Apakah Anda Yakin sudah menerima PP?',
                    text: "Terima Data PP!",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#41B314',
                    cancelButtonColor: '#F9354C',
                    confirmButtonText: 'Terima PP'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `{{ url('') }}/sppd/accept/` + id;
                        Swal.fire({
                            title: 'Terima PP',
                            text: 'PP berhasil anda Terima. Harap Menunggu...',
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

            function kirim(id) {
                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Kirim Data PP!",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#41B314',
                    cancelButtonColor: '#F9354C',
                    confirmButtonText: 'Kirim PP'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `{{ url('') }}/sppd/send/` + id;
                        Swal.fire({
                            title: 'Kirim PP',
                            text: 'PP berhasil anda Kirim.',
                            allowOutsideClick: false,
                            icon: 'success',
                            showConfirmButton: false,
                        })
                    }
                })
            }



            function selesai(id) {
                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Menyelesaikan Data PP!",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#41B314',
                    cancelButtonColor: '#F9354C',
                    confirmButtonText: 'Kirim PP'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `{{ url('') }}/sppd/selesai/` + id;
                        Swal.fire({
                            title: 'Selesaikan PP',
                            text: 'PP berhasil diselesaikan.',
                            icon: 'success',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                        })
                    }
                })
            }

            function upload_dokumen_pendukung(id) {
                $("#modal_dokumen_pendukung").modal('show');
                $("#spp_id").val(id);
            }

            function upload_no_doc(index, id, value, posisi) {
                $("#modal_no_doc").modal('show');
                var a = $("#no_doc_id").val(id);
                // console.log(index,)
                $("#no_doc").val(value);
                var c = $("#no_doc").val(value);
                var isi_no_doc = $("#no_doc").val();
                console.log(isi_no_doc);
            }

            function revisi(id, data) {
                $("#modal_revisi").modal('show');
                if (data != null) {
                    document.getElementById('keterangan_revisi').innerHTML = data;
                    $("#keterangan_revisi").attr('readonly', 'readonly');
                } else {
                    $("#keterangan_revisi").attr('required', true);

                }
                // alert(data);
                $("#form-revisi").attr('action', 'sppd/revisi/' + id);
            }

            function confirm_revisi() {
                let ket = $('#keterangan_revisi').val();
                // console.log(ket === "");
                if (ket === "") {
                    alert("Isi Keterangan!!");
                    return

                } else {
                    Swal.fire({
                        title: 'Apakah Anda Yakin?',
                        text: "Mengembalikan Data PP!",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#41B314',
                        cancelButtonColor: '#F9354C',
                        confirmButtonText: 'Kembalikan PP'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('form-revisi').submit();
                            Swal.fire({
                                title: 'Mengembalikan PP',
                                text: 'PP berhasil dikembalikan.',
                                icon: 'success',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                            })
                        }
                    })
                }
            }


            function rekam_jejak(data, asal) {
                $('#rekam_jejak_body').empty()
                var level = {{ $hakakses }};
                // var revisi = JSON.parse(${val.rekam_jejak_revisi});
                // console.log(rekam_jejak_status);

                $.each(data, function(index, val) {
                    if ((val.master_user_id != 2 || val.master_user_id == 99) && val.rekam_jejak_status == 0) {
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
                <p>Membuat PP Baru</p>
              </div>
            </div>
          </li>`);
                    } else if (val.rekam_jejak_status == 1) {
                        var date = new Date(val.rekam_jejak_waktu);
                        var tanggal = moment(date).format("D-MM-YYYY HH:mm:ss");
                        if (val.master_user_id == 2) {
                            $('#rekam_jejak_body').append(`<li class="timeline-inverted" id="timeline_setuju_${index}">
            <div class="timeline-badge warning"><i class="glyphicon glyphicon-send"></i></div>
            <div class="timeline-panel">
              <div class="timeline-heading">
                <h4 class="timeline-title" id="user_2_${index}"><strong>${val.asal}</strong></h4>
                <p id="time_2_${index}"><small class="text-muted"><i class="glyphicon glyphicon-time"></i>${tanggal}</small></p>
              </div>
              <div class="timeline-body">
                <p>Mengirim PP ke kasub_bagian</p>
              </div>
            </div>
          </li>`);
                        } else {
                            $('#rekam_jejak_body').append(`<li class="timeline-inverted" id="timeline_setuju_${index}">
            <div class="timeline-badge warning"><i class="glyphicon glyphicon-send"></i></div>
            <div class="timeline-panel">
              <div class="timeline-heading">
                <h4 class="timeline-title" id="user_2_${index}"><strong>${val.asal}</strong></h4>
                <p id="time_2_${index}"><small class="text-muted"><i class="glyphicon glyphicon-time"></i>${tanggal}</small></p>
              </div>
              <div class="timeline-body">
                <p>Mengirim PP ke ${val.tujuan}</p>
              </div>
            </div>
          </li>`);
                        }
                    } else if (val.rekam_jejak_status == 6) {
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
                <p>Menerima PP yang masuk</p>
              </div>
            </div>
          </li>`);
                    } else if (val.rekam_jejak_status == 33) {
                        var date = new Date(val.rekam_jejak_waktu);
                        var tanggal = moment(date).format("D-MM-YYYY HH:mm:ss");
                        if (level == 2) {
                            var i = index;
                            if (val.asal == "Petugas Penerima") {
                                $('#rekam_jejak_body').append(`<li id="timeline_revisi_${index}">
              <div class="timeline-badge warning"><i class="glyphicon glyphicon-pencil"></i></div>
              <div class="timeline-panel">
                <div class="timeline-heading">
                  <h4 class="timeline-title" id="user_3_${index}"><strong>${val.asal}</strong></h4>
                  <p id="time_3_${index}"><small class="text-muted"><i class="glyphicon glyphicon-time"></i>${tanggal}</small></p>
                </div>
                <div class="timeline-body">
                  <p>Mengembalikan ke Bagian</p>
                  <p>Revisi Oleh : ${val.asal}</p>
                  <h5 id="revisi_${index}"><strong style="color: red">Keterangan Revisi :</strong><br><span> ${val.rekam_jejak_revisi}</span></h5>
                </div>
              </div>
            </li>`);
                            } else {
                                $('#rekam_jejak_body').append(`<li id="timeline_revisi_${index}">
              <div class="timeline-badge warning"><i class="glyphicon glyphicon-pencil"></i></div>
              <div class="timeline-panel">
                <div class="timeline-heading">
                  <h4 class="timeline-title" id="user_3_${index}"><strong>${val.asal}</strong></h4>
                  <p id="time_3_${index}"><small class="text-muted"><i class="glyphicon glyphicon-time"></i>${tanggal}</small></p>
                </div>
                <div class="timeline-body">
                  <p>Mengembalikan PP</p>
                  <p>Revisi Oleh : ${val.asal}</p>
                  <h5 id="revisi_${index}"><strong style="color: red">Keterangan Revisi :</strong><br><span> ${val.rekam_jejak_revisi}</span></h5>
                </div>
              </div>
            </li>`);
                            }

                        } else {
                            if (val.asal == "Petugas Penerima") {
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
                            } else {
                                $('#rekam_jejak_body').append(`<li class="timeline-inverted" id="timeline_revisi_${index}">
              <div class="timeline-badge warning"><i class="glyphicon glyphicon-pencil"></i></div>
              <div class="timeline-panel">
                <div class="timeline-heading">
                  <h4 class="timeline-title" id="user_3_${index}"><strong>${val.asal}</strong></h4>
                  <p id="time_3_${index}"><small class="text-muted"><i class="glyphicon glyphicon-time"></i>${tanggal}</small></p>
                </div>
                <div class="timeline-body">
                  <p>Mengembalikan PP</p>
                  <h5 id="revisi_${index}"><strong style="color: red">Keterangan Revisi :</strong><br><span> ${val.rekam_jejak_revisi}</span></h5>
                </div>
              </div>
            </li>`);
                            }
                        }

                    } else if (val.rekam_jejak_status == 2) {
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
                    } else if (val.rekam_jejak_status == 3) {
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
                    } else if (val.rekam_jejak_status == 4) {
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
                <p>Penyelesaian PP</p>
              </div>
            </div>
          </li>`);
                    } else if (val.rekam_jejak_status == 5) {
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
                <p>Pembatalan PP</p>
              </div>
            </div>
          </li>`);
                    }
                });
                $("#modal_rekam_jejak").modal('show');


            }

            function upload_bukti_kas(id, bukti) {
                $("#bukti_spp_id").val(id);
                if (bukti == null || bukti == '') {
                    $("#upload_bukti").show();
                    $("#file_bukti_kas").hide();
                    $("#submit_bukti").show();

                } else {
                    $("#upload_bukti").hide();
                    $("#file_bukti_kas").show();
                    $("#submit_bukti").hide();
                    $("#bukti_kas_lama").attr('href', 'dokumen/' + bukti);
                    document.getElementById("bukti_kas_lama").innerHTML = bukti;

                }
                $("#modal_bukti_kas").modal('show');
            }

            function edit_bukti_kas() {
                $("#upload_bukti").show();
                $("#file_bukti_kas").hide();
                $("#submit_bukti").show();
            }

            function confirm_batal(id) {
                let batal = $('#keterangan_batal').val();
                // console.log(ket === "");
                if (batal === "") {
                    alert("Isi Keterangan!!");
                    return

                } else {
                    Swal.fire({
                        title: 'Apakah Anda Yakin?',
                        text: "Membatalkan PP!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Batalkan PP!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            //window.location.href = `{{ url('') }}/spp/batal/`+id;
                            document.getElementById('form-batal').submit();
                            Swal.fire({
                                title: 'Batal',
                                text: 'PP berhasil anda Batalkan.',
                                icon: 'success',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                            })
                        }
                    })
                }
            }

            function batal(id, data) {
                $("#modal_batal").modal('show');
                if (data != null) {
                    document.getElementById('keterangan_batal').innerHTML = data;
                    $("#keterangan_batal").attr('readonly', 'readonly');
                } else {
                    $("#keterangan_batal").attr('required', true);

                }
                // alert(data);
                $("#form-batal").attr('action', `{{ url('') }}/spp/batal/` + id);
            }

            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust()
                    .responsive.recalc();
            });
        </script>

    @endsection

    @section('footer')
        <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/piexif.min.js"></script>
        <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/sortable.min.js"></script>
        <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/fileinput.min.js"></script>
        <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/themes/fa/theme.js"></script>
        <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/locales/id.js"></script>
        <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/piexif.min.js"></script>
        <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/sortable.min.js"></script>
        <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/fileinput.min.js"></script>
        <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/themes/fa/theme.js"></script>
        <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/locales/id.js"></script>
        <script src="{{ asset('') }}assets/vendor/ckeditor/ckeditor5-build-inline/build/ckeditor.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    @endsection
