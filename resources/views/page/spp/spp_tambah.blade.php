@extends('template.master')
@section('title', 'SPP | Tambah SPP')

@section('header')
    <link rel="stylesheet" href="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/css/fileinput.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/select2/select2.min.css') }}" />
@endsection
@section('konten')
    <style>
        .preloader {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url('{{ asset('') }}assets/Ajux_loader.gif') 50% 50% no-repeat rgb(249, 249, 249);
            background-size: 200px 200px;
        }
    </style>
    @if ($error_code == 5)
        <script>
            $(window).load(function() {
                $("#preloaders").fadeOut(1000);
                Swal.fire("Terdapat Error Saat Proses Penyimpanan!", "", "warning");
            });
        </script>
    @else
        <script>
            $(window).load(function() {
                $("#preloaders").fadeOut(1000);
            });
        </script>
    @endif
    @if ($error_code != 5)
        <div id="preloaders" class="preloader"></div>
    @endif
    <?php
    $bagianid = Session::get('bagian');
    $hakakses = Session::get('hak_akses');
    $company = Session::get('company');
    ?>
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <form action="{{ route('storespp') }}" target="" id="form_spp" method="post"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="container-fluid">
                    <h3 class="page-title">Buat SPP</h3>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- FORM SPP -->
                            <div class="panel">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Form SPP</h3>
                                </div>
                                <div class="panel-body">
                                    {{-- <h2>{{ $company }}</h2> --}}
                                    <input type="hidden" value="{{ $company }}" name="company">
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Jenis Alur</label>
                                        <div class="col-sm-10">
                                            <select class="form-control" name="flow_id">
                                                <option value="" disabled selected>-- Pilih Jenis Alur --</option>
                                                @foreach ($flow as $b)
                                                    <option value="{{ $b->flow_id }}">{{ $b->flow_nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Jenis SPP</label>
                                        <div class="col-sm-10">
                                            <select class="form-control" id="jenis_spp" name="jenis">
                                                <option value="" disabled selected>-- Pilih Jenis SPP --</option>
                                                <option value="karyawan">Karyawan</option>
                                                <option value="vendor">Vendor</option>
                                            </select>
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
                                    <div class="form-group row" id="panel_sumber_dana" style="display: none">
                                        <label class="col-sm-2 col-form-label">Jenis Sumber Dana*</label>
                                        <div class="col-sm-10">
                                            <select class="form-control validate_sppb validate_sppn validate_spp_all"
                                                id="sumber_dana" name="sumber_dana" required>
                                                <option value="" disabled selected>-- Pilih Sumber Dana --</option>
                                                @foreach ($sumberdana as $b)
                                                    <option value="{{ $b->sumber_dana_id }}">{{ $b->nama_sumber_dana }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END FORM SPP -->
                            <div class="panel" id="panel_sppb_sppn" style="display:none">
                                <div class="panel-body">
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" id="label_kwitansi_spp">Kwitansi dan/atau
                                            Invoice *</label>
                                        <div class="col-sm-10">
                                            <input type="text" id="kwitansi_spp" name="kwitansi" class="form-control"
                                                placeholder="Nama Pihak Kwitansi" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Referensi</label>
                                        <div class="col-sm-10">
                                            <input type="text" maxlength="50" id="referensi_spp" name="referensi" class="form-control"
                                                placeholder="Nomor Referensi" autocomplete="off">
                                        </div>
                                    </div>
                                    <div id="fp_spp">
                                        <div id="faktur_pajak_spp_1">
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Faktur Pajak</label>
                                                <div class="col-sm-4">
                                                    <input maxlength="17" type="text" id="faktur_pajak_spp"
                                                        name="faktur_pajak_spp[1][fp]" class="form-control "
                                                        placeholder="Nomor Faktur Pajak 1" autocomplete="off">
                                                </div>
                                                <label class="col-sm-1">Tanggal Faktur Pajak</label>
                                                <div class="col-sm-3">
                                                    <input type="date" class="form-control"
                                                        id="tanggal_faktur_pajak_spp"
                                                        name="tanggal_faktur_pajak_spp[1][tanggal]">
                                                </div>
                                                <div class="col-sm-2" id="btn_tambah_faktur_pajak_1">
                                                    <button type="button" class="btn btn-success btn-sm"
                                                        onclick="tambah_faktur_pajak_spp(1)">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">No. Kontrak/dokumen pendukung lain *</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="sp_opl_spp" name="sp_opl"
                                            class="form-control validate_spp_all" placeholder="Nomor Kontrak"
                                            autocomplete="off">
                                    </div>
                                </div> --}}
                                </div>
                            </div>
                            <!-- FORM SPPB -->
                            <div class="panel" id="panel_sppb" style="display:none ">
                                <div class="panel-heading">
                                    Form PPb
                                </div>
                                <div class="panel-body">
                                    <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                        <ul class="nav" role="tablist">
                                            <li class="active"><a href="#tab-informasi-sppb" role="tab"
                                                    data-toggle="tab">Informasi</a></li>
                                            <li><a href="#tab-isi-sppb" role="tab" data-toggle="tab">Isi</a></li>
                                        </ul>
                                    </div>
                                    <div class="tab-content">

                                        <!-- TAB INFORMASI -->
                                        <div class="tab-pane fade in active" id="tab-informasi-sppb">
                                            <div class="form-group row">
                                                <div class="col-sm-10">
                                                    <input type="hidden" id="hakakses" name="hakakses"
                                                        class="form-control" placeholder="{{ $bagianid }}"
                                                        value="{{ $bagianid }}" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row" id="form_kwitansi_sppb">
                                                <label class="col-sm-2 col-form-label" id="label_kwitansi_sppb">Kwitansi
                                                    dan/atau Invoice *</label>
                                                <div class="col-sm-10">
                                                    <input type="text" id="kwitansi_sppb" name="kwitansi_sppb"
                                                        class="form-control" placeholder="Nama Pihak Kwitansi SPPb"
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row" id="form_referensi_sppb">
                                                <label class="col-sm-2 col-form-label">Referensi</label>
                                                <div class="col-sm-10">
                                                    <input type="text" maxlength="50" id="referensi_sppb" name="referensi_sppb"
                                                        class="form-control" placeholder="Nomor Referensi SPPb"
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            {{-- <div class="form-group row">
                                            <label class="col-sm-2 col-form-label" id="label_au53_sppb">AU.53</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="au53_sppb" name="au53_sppb" class="form-control"
                                                    placeholder="Nomor AU. 53 SPPb" autocomplete="off">
                                            </div>
                                        </div> --}}
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label"
                                                    id="label_berita_acara_opsional">BA/dokumen pendukung lain *</label>
                                                <div class="col-sm-10">
                                                    <input type="text" id="berita_acara_sppb" name="berita_acara_sppb"
                                                        class="form-control validate_sppb validate_spp_all"
                                                        placeholder="Nomor Berita Acara SPPb" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label"
                                                    id="label_berita_acara_opsional">Nomor SP/OPL/SPK/Perjanjian *</label>
                                                <div class="col-sm-10">
                                                    <input type="text" id="sp_opl_sppb" name="sp_opl_sppb"
                                                        class="form-control validate_sppb validate_spp_all"
                                                        placeholder="Nomor SP/OPL/SPK/Perjanjian" autocomplete="off">
                                                </div>
                                            </div>
                                            <div id="fp_sppb">
                                                <div id="faktur_pajak_sppb_1">
                                                    <div class="form-group row" id="form_faktur_pajak_sppb">
                                                        <label class="col-sm-2 col-form-label">Faktur Pajak </label>
                                                        <div class="col-sm-4">
                                                            <input type="text" id="faktur_pajak_sppb" maxlength="17"
                                                                name="faktur_pajak_sppb[1][fp]" class="form-control"
                                                                placeholder="Nomor Faktur Pajak SPPb 1" autocomplete="off"
                                                                required>
                                                        </div>
                                                        <label class="col-sm-1">Tanggal Faktur Pajak</label>
                                                        <div class="col-sm-3">
                                                            <input type="date" class="form-control"
                                                                id="tanggal_faktur_pajak_sppb"
                                                                name="tanggal_faktur_pajak_sppb[1][tanggal]">
                                                        </div>
                                                        <div class="col-sm-2" id="btn_faktur_pajak_sppb_1">
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                onclick="tambah_faktur_pajak_sppb(1)">+</button>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            {{-- <div class="form-group row" id="form_sp_opl_sppb">
                                            <label class="col-sm-2 col-form-label">No. Kontrak/dokumen pendukung lain
                                                *</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="sp_opl_sppb" name="sp_opl_sppb"
                                                    class="form-control validate_sppb validate_spp_all"
                                                    placeholder="Nomor Kontrak SPPb" autocomplete="off">
                                            </div>
                                        </div> --}}
                                            @if ($hakakses == 1)
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Bagian *</label>
                                                    <div class="col-sm-10">
                                                        <select class="form-control validate_sppb validate_spp_all"
                                                            id="bagian_sppb" name="bagian_sppb">
                                                            <option value="" disabled selected>-- Pilih Bagian --
                                                            </option>
                                                            @foreach ($bagianall as $b)
                                                                <option value="{{ $b->master_bagian_id }}">
                                                                    {{ $b->master_bagian_nama }}</optlion>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Tanggal *</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" id="tanggal_sppb" name="tanggal_sppb"
                                                            class="form-control date validate_sppb validate_spp_all"
                                                            placeholder="Tanggal SPPb" value="{{ DATE('d-m-Y') }}"
                                                            autocomplete="off" required>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Bagian *</label>
                                                    <div class="col-sm-10">
                                                        <select class="form-control validate_sppb validate_spp_all"
                                                            id="bagian_sppb" name="bagian_sppb" readonly>
                                                            <option value="" disabled>-- Pilih Bagian --</option>
                                                            <option value="{{ $bagian->master_bagian_id }}" selected>
                                                                {{ $bagian->master_bagian_nama }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Tanggal *</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" id="tanggal_sppb" name="tanggal_sppb"
                                                            class="form-control validate_sppb validate_spp_all"
                                                            placeholder="Tanggal SPPb" value="{{ DATE('d-m-Y') }}"
                                                            autocomplete="off" readonly required>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Metode Pembayaran*</label>
                                                <div class="col-sm-10">
                                                    <select class="form-control validate_sppb validate_spp_all"
                                                        id="metode_pembayaran_sppb" name="metode_pembayaran_sppb"
                                                        required>
                                                        <option value="" disabled selected>-- Pilih Transfer --
                                                        </option>
                                                        <option value="bank">Transfer</option>
                                                        <option value="tidak_transfer">Tidak Transfer</option>
                                                        {{-- <option value="kas">Kas</option>
                                                    <option value="kas_negara">Kas Negara</option>
                                                    <option value="skbdn">SKBDN</option> --}}
                                                    </select>
                                                </div>
                                            </div>
                                            <div id="kas_negara_sppb_input" style="display:none;">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Penerima *</label>
                                                    <div id="kas_karyawan_input">
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control"
                                                                id="nama_kas_negara_sppb_input"
                                                                name="nama_kas_negara_sppb_input"
                                                                Placeholder="Nama Penerima">
                                                            <span style="font-size: 10px;color:red;">Tulis "Terlampir" jika
                                                                data lebih dari 1 (satu)</span>
                                                        </div>
                                                        {{-- <div class="col-sm-2" id="btn_karyawan_kas_sppb_input_1">
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            onclick="tambah_karyawan_kas_sppb_input(1)">+</button>
                                                    </div> --}}
                                                    </div>
                                                </div>
                                                <div class="form-group row" id="alamat_karyawan_kas_input_1">
                                                    <label class="col-sm-2 col-form-label">Alamat *</label>
                                                    <div id="kas_karyawan_input">
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control"
                                                                id="alamat_kas_negara_sppb_input"
                                                                name="alamat_kas_negara_sppb_input" Placeholder="Alamat">
                                                            <span style="font-size: 10px;color:red;">Tulis "Terlampir" jika
                                                                data lebih dari 1 (satu)</span>
                                                        </div>
                                                        {{-- <div class="col-sm-2" id="btn_karyawan_kas_sppb_input_1">
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            onclick="tambah_karyawan_kas_sppb_input(1)">+</button>
                                                    </div> --}}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row" style="display:none;" id="pilih_lampirkan_sppb"
                                                onclick="pilih_data_sppb_karyawan()">
                                                <label class="col-sm-2 col-form-label"></label>
                                                <div class="col-sm-10">
                                                    <div class="col-sm-2" id="karyawan_input_manual">
                                                        <label class="fancy-radio">
                                                            <input name="pilih_data_sppb" id="input_data_sppb"
                                                                value="input_data" type="radio" checked>
                                                            <span style="font-size:17px"><i></i>Data diinputkan manual
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-2" id="karyawan_dari_master">
                                                        <!-- <label class="fancy-radio">
                                                            <input name="pilih_data_sppb" id="master_data_sppb"
                                                                value="master_data" type="radio" onclick="show1();">
                                                            <span style="font-size:17px"><i></i>Data dari master </span>
                                                        </label> -->
                                                    </div>
                                                    {{-- <div class="col-sm-2" id="karyawan_data_dilampirkan">
                                                    <label class="fancy-radio">
                                                        <input name="pilih_data_sppb" id="lampirkan_data_sppb"
                                                            value="lampirkan_data" type="radio" checked="checked"
                                                            onclick="show1();">
                                                        <span style="font-size:17px"><i></i>Data dilampirkan</span>
                                                    </label>
                                                </div> --}}
                                                    <div class="col-sm-2" id="karyawan_tidak_transfer">
                                                        <label class="fancy-radio">
                                                            <input name="pilih_data_sppb" id="lampirkan_data_sppb"
                                                                value="alasan_tidak_transfer" type="radio"
                                                                onclick="show2();">
                                                            <span style="font-size:17px"><i></i>Catatan Tidak
                                                                Transfer</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row" style="display:none;"
                                                id="pilih_lampirkan_sppb_vendor" onclick="pilih_data_sppb_vendor()">
                                                <label class="col-sm-2 col-form-label"></label>
                                                <div class="col-sm-10">
                                                    <div class="col-sm-2" id="vendor_input_manual">
                                                        <label class="fancy-radio">
                                                            <input name="pilih_data_sppb_vendor" id="input_data_sppb"
                                                                value="input_data" type="radio" checked>
                                                            <span style="font-size:17px"><i></i>Data diinputkan manual
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-2" id="vendor_dari_master">
                                                        <label class="fancy-radio">
                                                            <input name="pilih_data_sppb_vendor" id="master_data_sppb"
                                                                value="master_data" type="radio" onclick="show1()">
                                                            <span style="font-size:17px"><i></i>Data dari master </span>
                                                        </label>
                                                    </div>
                                                    {{-- <div class="col-sm-2" id="vendor_data_dilampirkan">
                                                    <label class="fancy-radio">
                                                        <input name="pilih_data_sppb_vendor" id="lampirkan_data_sppb"
                                                            value="lampirkan_data" type="radio" checked="checked"
                                                            onclick="show1()">
                                                        <span style="font-size:17px"><i></i>Data dilampirkan</span>
                                                    </label>
                                                </div> --}}
                                                    <div class="col-sm-2" id="vendor_tidak_transfer">
                                                        <label class="fancy-radio">
                                                            <input name="pilih_data_sppb_vendor" id="lampirkan_data_sppb"
                                                                value="alasan_tidak_transfer" type="radio"
                                                                onclick="show2()">
                                                            <span style="font-size:17px"><i></i>Alasan Tidak
                                                                Transfer</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="alasan_tidak_transfer" id="catatan_tidak_transfer"
                                                style="display:none ;">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Catatan Tidak Transfer</label>
                                                    <div id="alasan_tidak_transfer">
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control" id="alasan_tidak_transfer" name="karyawan_tidak_transfer"
                                                                placeholder="Alasan tidak transfer"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="kas_sppb" style="display:none">
                                                <div id="kas_sppb_input">
                                                    <div class="form-group row" id="atas_nama_karyawan_kas_input_1">
                                                        <label class="col-sm-2 col-form-label">Penerima *</label>
                                                        <div id="kas_karyawan_input">
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control"
                                                                    id="karyawan_kas_sppb_input"
                                                                    name="penerima_kas_sppb_karyawan"
                                                                    Placeholder="Nama Karyawan ">
                                                            </div>
                                                            {{-- <div class="col-sm-2" id="btn_karyawan_kas_sppb_input_1">
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                onclick="tambah_karyawan_kas_sppb_input(1)">+</button>
                                                        </div> --}}
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" id="alamat_karyawan_kas_input_1">
                                                        <label class="col-sm-2 col-form-label">Alamat *</label>
                                                        <div id="kas_karyawan_input">
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control"
                                                                    id="karyawan_alamat_sppb_input"
                                                                    name="alamat_kas_sppb_karyawan"
                                                                    Placeholder="Alamat Karyawan ">
                                                            </div>
                                                            {{-- <div class="col-sm-2" id="btn_karyawan_kas_sppb_input_1">
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                onclick="tambah_karyawan_kas_sppb_input(1)">+</button>
                                                        </div> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="kas_sppb_master">
                                                    <div class="form-group row" id="atas_nama_karyawan_kas_1">
                                                        <label class="col-sm-2 col-form-label">Penerima *</label>
                                                        <div id="kas_karyawan_master">
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control"
                                                                    id="karyawan_kas_sppb_input"
                                                                    name="penerima_kas_sppb_karyawan_master"
                                                                    Placeholder="Nama Karyawan ">
                                                            </div>
                                                            {{-- <div class="col-sm-2" id="btn_karyawan_kas_sppb_1">
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                onclick="tambah_karyawan_kas_sppb(1)">+</button>
                                                        </div> --}}
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" id="alamat_karyawan_kas_input_1">
                                                        <label class="col-sm-2 col-form-label">Alamat *</label>
                                                        <div id="kas_karyawan_input">
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control"
                                                                    id="karyawan_alamat_sppb_input_1"
                                                                    name="alamat_kas_sppb_karyawan_master"
                                                                    Placeholder="Alamat Karyawan">
                                                            </div>
                                                            {{-- <div class="col-sm-2" id="btn_karyawan_kas_sppb_input_1">
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                onclick="tambah_karyawan_kas_sppb_input(1)">+</button>
                                                        </div> --}}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div id="bank_sppb" style="display: none">
                                                <input type="hidden" id="id_bank_sppb_1" name="id_bank_sppb"
                                                    class="form-control" onclick="data_bank_sppb(1)"
                                                    placeholder="Id Bank SPPb" autocomplete="off">
                                                <div class="form-group row" id="atas_nama_vendor_sppb">
                                                    <label class="col-sm-2 col-form-label">Atas Nama Rekening *</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" id="atas_nama_bank_sppb_vendor"
                                                            name="atas_nama_bank_sppb_vendor" class="form-control"
                                                            onclick="data_bank_sppb(1)" placeholder="Atas Nama Bank SPPb"
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Nama Bank *</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" id="nama_bank_sppb_vendor"
                                                            name="nama_bank_sppb_vendor" class="form-control"
                                                            onclick="data_bank_sppb(1)" placeholder="Nama Bank SPPb"
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Nomor Rekening *</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" id="rekening_bank_sppb_vendor"
                                                            name="rekening_bank_sppb_vendor" class="form-control"
                                                            onclick="data_bank_sppb(1)"
                                                            placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Alamat *</label>
                                                    <div id="kas_karyawan_input">
                                                        <div class="col-sm-10">
                                                            <input type="text" class="form-control"
                                                                id="alamat_bank_sppb_vendor"
                                                                name="karyawan_alamat_sppb_input"
                                                                Placeholder="Alamat Karyawan " required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="bank_sppb_karyawan" style="display: none">
                                                <div id="bank_sppb_karyawan_input">
                                                    <div class="form-group row" id="atas_nama_karyawan_sppb">
                                                        <label class="col-sm-2 col-form-label">Atas Nama Rekening *</label>
                                                        <div class="col-sm-8">
                                                            <input type="text"
                                                                id="atas_nama_bank_sppb_karyawan_input_1"
                                                                name="karyawan_sppb_input[1][nama]" class="form-control"
                                                                placeholder="Atas Nama Bank SPPb" autocomplete="off">
                                                        </div>
                                                        <div class="col-sm-2" id="btn_karyawan_bank_sppb_input_1">
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                id="btn_tambah_karyawan_bank_sppb_input_1"
                                                                onclick="tambah_karyawan_bank_sppb_input(1)">+</button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label">Nama Bank *</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="nama_bank_sppb_karyawan_input_1"
                                                                name="karyawan_sppb_input[1][bank]" class="form-control"
                                                                placeholder="Nama Bank SPPb" autocomplete="off">
                                                        </div>

                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label">Nomor Rekening *</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="rekening_bank_sppb_karyawan_input_1"
                                                                name="karyawan_sppb_input[1][no_rek]" class="form-control"
                                                                placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label">Alamat *</label>
                                                        <div id="kas_karyawan_input">
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control"
                                                                    id="alamat_bank_sppb_karyawan_input_1"
                                                                    name="karyawan_alamat_sppb_input"
                                                                    Placeholder="Alamat Karyawan " required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="bank_sppb_karyawan_master">
                                                    <div class="form-group row" id="atas_nama_karyawan_sppb">
                                                        <label class="col-sm-2 col-form-label">Atas Nama Rekening *</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="atas_nama_bank_sppb_karyawan_1"
                                                                onclick="bank_karyawan_sppb(1)"
                                                                name="karyawan_sppb[1][nama]" class="form-control"
                                                                placeholder="Atas Nama Bank SPPb" autocomplete="off">
                                                        </div>
                                                        <div class="col-sm-2" id="btn_karyawan_bank_sppb_1">
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                id="btn_tambah_karyawan_bank_sppb_1"
                                                                onclick="tambah_karyawan_bank_sppb(1)">+</button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label">Nama Bank *</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="nama_bank_sppb_karyawan_1"
                                                                onclick="bank_karyawan_sppb(1)"
                                                                name="karyawan_sppb[1][bank]" class="form-control"
                                                                placeholder="Nama Bank SPPb" autocomplete="off">
                                                        </div>

                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label">Nomor Rekening *</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="rekening_bank_sppb_karyawan_1"
                                                                onclick="bank_karyawan_sppb(1)"
                                                                name="karyawan_sppb[1][no_rek]" class="form-control"
                                                                placeholder="Nomor Rekening Bank SPPb" autocomplete="off">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label">Alamat *</label>
                                                        <div id="kas_karyawan_input">
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control"
                                                                    id="alamat_bank_sppb_karyawan_1"
                                                                    name="karyawan_alamat_sppb_master"
                                                                    Placeholder="Alamat Karyawan " required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Catatan</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" id="catatan_sppb" name="catatan_sppb" placeholder="Catatan SPPb" rows="4"></textarea>
                                                </div>
                                            </div>
                                            <div id="panel_dokumen_pendukung" style="display: none">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Kontrak Perjanjian / Dokumen
                                                        Sejenis</label>
                                                    <div class="col-sm-10">
                                                        <input type="file" id="kontrak_perjanjian_sppb"
                                                            name="kontrak_perjanjian_sppb" class="file"
                                                            accept="application/pdf, image/*"
                                                            placeholder="Upload Kontrak Perjanjian / Dokumen Sejenis"
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Invoice / Nota
                                                        Pembayaran</label>
                                                    <div class="col-sm-10">
                                                        <input type="file" id="invoice_sppb" name="invoice_sppb"
                                                            class="file" accept="application/pdf, image/*"
                                                            placeholder="Upload Invoice / Nota Pembayaran"
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">E-Faktur </label>
                                                    <div class="col-sm-10">
                                                        <input type="file" id="efaktur_sppb" name="efaktur_sppb"
                                                            class="file" accept="application/pdf, image/*"
                                                            placeholder="Upload E-Faktur" autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">BA/dokumen pendukung
                                                        lain</label>
                                                    <div class="col-sm-10">
                                                        <input type="file" id="berita_acara_file_sppb"
                                                            name="berita_acara_file_sppb" class="file"
                                                            accept="application/pdf, image/*"
                                                            placeholder="Upload Berita Acara" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label"
                                                    id="label_dokumen_pendukung_sppb">Upload
                                                    Dokumen Pendukung</label>
                                                <div class="col-sm-10">
                                                    {{-- <input type="file" id="dokumen_pendukung_sppb"
                                                    name="dokumen_pendukung_sppb[]" class="file"
                                                    data-preview-file-type="text" data-show-upload="true"
                                                    data-show-caption="true" placeholder="Upload Dokumen Pendukung Lain"
                                                    multiple> --}}
                                                    <div class="file-loading">
                                                        <input type="file" id="dokumen_pendukung_sppb"
                                                            name="dokumen_pendukung_sppb[]" class="file-multiple"
                                                            accept="application/pdf, image/*" multiple>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- END TAB INFORMASI -->

                                        <!-- TAB ISI -->
                                        <div class="tab-pane fade" id="tab-isi-sppb">
                                            <div id="isi_sppb_1" class="col-sm-12">
                                                <div
                                                    style="padding-bottom: 10px; margin-bottom: 20px; border-bottom: 1px solid #eaeaea;">
                                                    <font size="4" style="margin-right: 20px">Isi 1. </font>
                                                    <button type="button" class="btn btn-info btn-sm"
                                                        onclick="tambah_isi_sppb()">+</button>
                                                </div>

                                                <div class="col-sm-5">
                                                    {{-- <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">Kode KBB *</label>
                                                    <div class="col-sm-9">
                                                        <div class="row-fluid" id="parent_kbb_sppb_1">
                                                            <select class="selectpicker slct_sppb"
                                                                data-live-search="true" data-dropup-auto="false"
                                                                id="kode_kbb_sppb_1" data-width="100%"
                                                                name="isi_sppb[1][kode_kbb]" data-size="7"
                                                                onchange="pilih_rekening_sppb(1,'kode_kbb_sppb_')">
                                                                <option disabled selected>---Pilih KBB---</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Kode SAP *</label>
                                                        <label class="col-sm-9">
                                                            <select class="form-control validate_sppb validate_spp_all"
                                                                id="jenis_sap_sppb_1" onchange="js_sppb(1)"
                                                                name="isi_sppb[1][jenis_sap]">
                                                                <option value="" disabled selected>-- Pilih Jenis
                                                                    Kode SAP --</option>
                                                                <option value="vendor">Nomor Vendor</option>
                                                                <option value="gl">Nomor GL</option>
                                                                <option value="customer">Nomor Customer</option>
                                                                {{-- membuka kode customer untuk user bagian 124, 126 dan 127 --}}
                                                                {{-- @if (in_array($bagianid, [124, 126, 127]))
                                                                    <option value="customer">Nomor Customer</option>
                                                                @endif --}}
                                                            </select>
                                                        </label>
                                                        <label class="col-sm-3 col-form-label"></label>

                                                        <div id="nomor_vendor_sppb_1" style="display:none">
                                                            <div class="col-sm-9">
                                                                <div class="row-fluid" id="parent_kbb_sppb_1">
                                                                    <select
                                                                        class="select2-vendor sap_vendor_sppb slct_sppb"
                                                                        id="sap_vendor_sppb_1" name="isi_sppb_vendor"
                                                                        onchange="pilih_rekening_sppb(1, 'sap_vendor_sppb_')">
                                                                        <option disabled selected>---Pilih SAP---</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="text" style="display:none;"
                                                                    id="sap_vendor_sppb_id_1" name="isi_sppb[1][vendor]"
                                                                    class="form-control" onclick="kode_rekening_sppb(1)"
                                                                    placeholder="Kode SAP (Nomor Vendor)"
                                                                    autocomplete="off" required>
                                                            </div>
                                                        </div>

                                                        <div id="nomor_gl_sppb_1" style="display:none">
                                                            <div class="col-sm-9">
                                                                <div class="row-fluid" id="parent_kbb_sppb_1">
                                                                    <select class="select2-gl" id="sap_gl_sppb_1"
                                                                        name="isi_sppb_gl"
                                                                        onchange="pilih_rekening_sppb(1, 'sap_gl_sppb_')">
                                                                        <option value="" disabled selected>-- Pilih
                                                                            Kode GL --</option>
                                                                        {{-- @foreach ($gl as $r)
                                                                    <option value="{{ $r->master_gl_id }}"
                                                                        data-budget_1="{{ $r->jumlah_budget }}"
                                                                        data-budget_1="{{ $r->master_gl_id }}">
                                                                        {{ $r->master_gl_kode }}
                                                                        ({{ $r->master_gl_keterangan }})
                                                                    </option>
                                                                    @endforeach --}}
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <label class="col-sm-12 col-form-label mt-2"></label>
                                                            <label class="col-sm-3 col-form-label">RKAP</label>
                                                            <div class="col-sm-9">
                                                                <input type="text"
                                                                    class="form-control budget_gl_1 nominal"
                                                                    placeholder="Budget" autocomplete="off" readonly>
                                                                <input type="hidden" class="budget_gl_hide_1"
                                                                    name="budget">
                                                            </div>
                                                            <label class="col-sm-12 col-form-label mt-2"></label>
                                                            <label class="col-sm-3 col-form-label">Realisasi</label>
                                                            <div class="col-sm-9">
                                                                <input type="text"
                                                                    class="form-control realisasi_1 nominal"
                                                                    placeholder="Realisasi" autocomplete="off" readonly>
                                                                <input type="hidden" class="budget_gl_hide"
                                                                    name="budget">
                                                            </div>
                                                            <label class="col-sm-12 col-form-label mt-2"></label>
                                                            <label class="col-sm-3 col-form-label">On Process</label>
                                                            <div class="col-sm-9">
                                                                <input type="text"
                                                                    class="form-control onproses_1 nominal"
                                                                    placeholder="Onproses" autocomplete="off" readonly>
                                                                <input type="hidden" class="budget_gl_hide"
                                                                    name="budget">
                                                            </div>
                                                            <label class="col-sm-12 col-form-label mt-2"></label>
                                                            <label class="col-sm-3 col-form-label">Sisa</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control sisa_1 nominal"
                                                                    placeholder="Sisa" autocomplete="off" readonly>
                                                                <input type="hidden" class="budget_gl_hide"
                                                                    name="budget">
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="hidden" style="display:none;"
                                                                    id="sap_gl_sppb_id_1" name="isi_sppb[1][gl]"
                                                                    class="form-control" onclick="kode_gl_sppb(1)"
                                                                    placeholder="Kode SAP (Nomor GL)" autocomplete="off"
                                                                    required>
                                                            </div>
                                                        </div>


                                                        {{-- @if (in_array($bagianid, [124, 126, 127])) --}}
                                                        <div id="nomor_customer_sppb_1" style="display:none">
                                                            <div class="col-sm-9">
                                                                <div class="row-fluid" id="parent_kbb_sppb_1">
                                                                    <select class="select2-customer slct_sppb"
                                                                        id="sap_customer_sppb_1" name="isi_sppb_customer"
                                                                        onchange="pilih_rekening_sppb(1, 'sap_customer_sppb_')">
                                                                        <option disabled selected>
                                                                            ---Pilih Kode Customer---
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="text" style="display:none;"
                                                                    id="sap_customer_sppb_id_1"
                                                                    name="isi_sppb[1][customer]" class="form-control"
                                                                    onclick="kode_customer_sppb(1)"
                                                                    placeholder="Kode SAP (Nomor Customer)"
                                                                    autocomplete="off" required>
                                                            </div>
                                                        </div>
                                                        {{-- @endif --}}

                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Cost/Profit*</label>
                                                        <label class="col-sm-9">
                                                            <select class="form-control validate_sppb validate_spp_all"
                                                                id="jenis_center_sppb_1" onchange="jc_sppb(1)"
                                                                name="isi_sppb[1][jenis_center]">
                                                                <option value="" disabled selected>-- Pilih --
                                                                </option>
                                                                <option value="cost_center">Cost Center</option>
                                                                <option value="profit_center">Profit Center</option>
                                                            </select>
                                                        </label>
                                                        <label class="col-sm-3 col-form-label"></label>
                                                        <div class="col-sm-9" id="cost_center_sppb_1"
                                                            style="display: none">
                                                            <select class="select2-costcenter"
                                                                id="select_cost_center_sppb_1"
                                                                name="isi_sppb[1][cost_center]">
                                                                {{-- <option value="" disabled selected>-- Pilih Cost
                                                                Center --</option> --}}
                                                            </select>
                                                        </div>

                                                        <div class="col-sm-9" id="profit_center_sppb_1"
                                                            style="display: none">
                                                            <select class="select2-profitcenter"
                                                                id="select_profit_center_sppb_1"
                                                                name="isi_sppb[1][profit_center]">
                                                                {{-- @foreach ($profitcenter as $profit)
                                                            <option value="{{ $profit->master_profit_center_id }}">
                                                                {{ $profit->master_profit_center_kode }}
                                                                ({{ $profit->master_profit_unit }})
                                                            </option>
                                                            @endforeach --}}
                                                            </select>
                                                        </div>

                                                        {{-- @if ($bagian_karyawan->pemisah_keb_bag == 1)
                                                    <label class="col-sm-9">
                                                        <input type="text" class="form-control" value="Cost Center"
                                                            readonly>
                                                        <input type="hidden" name="isi_sppb[1][jenis_center]"
                                                            value="cost_center" readonly>
                                                    </label>
                                                    <label class="col-sm-3 col-form-label"></label>
                                                    <div class="col-sm-9">
                                                        @foreach ($cost_center_id as $ccid)
                                                        <input type="text" class="form-control"
                                                            value="{{ $ccid->master_cost_center_kode }} {{ $ccid->master_cost_center_keterangan }}"
                                                            readonly>
                                                        <input type="hidden" name="isi_sppb[1][cost_center]"
                                                            value="{{ $ccid->master_cost_center_id }}" readonly>
                                                        @endforeach
                                                    </div>
                                                    @elseif($bagian_karyawan->pemisah_keb_bag == 2)
                                                    <label class="col-sm-9">
                                                        <input type="text" class="form-control" value="Profit Center"
                                                            readonly>
                                                        <input type="hidden" name="isi_sppb[1][jenis_center]"
                                                            value="profit_center" readonly>
                                                    </label>
                                                    <label class="col-sm-3 col-form-label"></label>
                                                    <div class="col-sm-9">
                                                        @foreach ($cost_center_id as $ccid)
                                                        <input type="text" class="form-control"
                                                            value="{{ $ccid->master_profit_center_kode }} {{ $ccid->master_profit_unit }}"
                                                            readonly>
                                                        <input type="hidden" name="isi_sppb[1][profit_center]"
                                                            value="{{ $ccid->master_profit_center_id }}" readonly>
                                                        @endforeach
                                                    </div>
                                                    @endif --}}
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Cash Flow*</label>
                                                        <div class="col-sm-9">
                                                            <select
                                                                class="form-control validate_sppb validate_spp_all select2-cashflow"
                                                                id="cash_flow_sppb_1" name="isi_sppb[1][cash_flow]">
                                                                <option value="" disabled selected>-- Pilih Cash Flow
                                                                    --</option>
                                                                @foreach ($cashflow as $cash)
                                                                    <option value="{{ $cash->master_cash_flow_id }}">
                                                                        {{ $cash->master_cash_flow_key }}
                                                                        {{ $cash->master_cash_flow_keterangan }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="sub_isi_sppb_1_1">
                                                    <div class="col-md-6 isi_karyawan" id="isi_karyawan_1_1"
                                                        style="display:none ;">
                                                        <div class="form-group row">
                                                            <input type="hidden" value="karyawan" id="jenis_karyawan">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group row">
                                                            <label class="col-sm-1 col-form-label">1. </label>
                                                            <label class="col-sm-2 col-form-label">Uraian *</label>
                                                            <div class="col-sm-9">
                                                                <div style="border: 1px solid hsla(0, 0%, 0%, 0.15);">
                                                                    <div id="uraian_sppb_1_1"
                                                                        style="height:auto;min-height:100px">
                                                                        {{-- <input type="hidden"
                                                                        name="uraian_sppb[0][0][uraian]"
                                                                        id="uraian_sppb_value_1_1"> --}}
                                                                        <textarea class="form-control" id="ckeditor_1_1" contenteditable="true" name="uraian_sppb[1][1][ket]"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- PAJAK WAPU WABA PPH MANUAL SPPB -->
                                                        <div class="form-group row formInputPajakSppb">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">Pajak*</label>
                                                            <div class="col-sm-9">
                                                                <select name="uraian_sppb[1][1][type_pajak_sppb]"
                                                                    id="pilih_pajak_sppb_1_1"
                                                                    class="form-control validasi_sppb"
                                                                    onchange="jenis_pajak_sppb(1,1);">
                                                                    <option value="" selected disabled>-- Pilih Pajak
                                                                        --</option>
                                                                    <option value="wapu_sppb_1_1">WAPU</option>
                                                                    <option value="waba_sppb_1_1">WABA</option>
                                                                    <option value="pph_sppb_1_1">PPh</option>
                                                                    <option value="tanpa_pajak_sppb_1_1">Tanpa Pajak
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="wapu_pph_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh</label>
                                                            <div class="col-sm-9">
                                                                <select name="uraian_sppb[1][1][pilih_wapu_sppb]"
                                                                    id="pilih_wapu_sppb_1_1" class="form-control">
                                                                    <option value="" disabled selected>-- Pilih --
                                                                    </option>
                                                                    <option value="wapu_normal_sppb_1_1">Normal</option>
                                                                    <option value="wapu_pph21_a_sppb_1_1">PPh 21 (2,5%)
                                                                    </option>
                                                                    <option value="wapu_pph21_b_sppb_1_1">PPh 21 (7,5%)
                                                                    </option>
                                                                    <option value="wapu_pph21_c_sppb_1_1">PPh 21 (12,5%)
                                                                    </option>
                                                                    <option value="wapu_pph22_a_sppb_1_1">PPh 22</option>
                                                                    <option value="wapu_pph23_a_sppb_1_1">PPh 23 (2%)
                                                                    </option>
                                                                    <option value="wapu_pph23_b_sppb_1_1">PPh 23 (15%)
                                                                    </option>
                                                                    <option value="wapu_pph23_c_sppb_1_1">PPh 23 (0%)
                                                                    </option>
                                                                    <option value="wapu_pph26_a_sppb_1_1">PPh 26 (0%)
                                                                    </option>
                                                                    <option value="wapu_pph26_b_sppb_1_1">PPh 26 (10%)
                                                                    </option>
                                                                    <option value="wapu_pph26_c_sppb_1_1">PPh 26 (20%)
                                                                    </option>
                                                                    <option value="wapu_pasal4ayat2_sppb_1_1">Pasal 4 Ayat
                                                                        2</option>
                                                                    <option value="wapu_nilai_lain_sppb_1_1">DPP Nilai Lain
                                                                    </option>
                                                                    <option value="wapu_manual_sppb_1_1">PPh Manual
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row" id="waba_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh</label>
                                                            <div class="col-sm-9">
                                                                <select name="uraian_sppb[1][1][pilih_waba_sppb]"
                                                                    id="pilih_waba_sppb_1_1" class="form-control">
                                                                    <option value="" disabled selected>-- Pilih --
                                                                    </option>
                                                                    <option value="waba_normal_sppb_1_1">Normal</option>
                                                                    <option value="waba_pph21_a_sppb_1_1">PPh 21 (2,5%)
                                                                    </option>
                                                                    <option value="waba_pph21_b_sppb_1_1">PPh 21 (7,5%)
                                                                    </option>
                                                                    <option value="waba_pph21_c_sppb_1_1">PPh 21 (12,5%)
                                                                    </option>
                                                                    <option value="waba_pph22_a_sppb_1_1">PPh 22</option>
                                                                    <option value="waba_pph23_a_sppb_1_1">PPh 23 (2%)
                                                                    </option>
                                                                    <option value="waba_pph23_b_sppb_1_1">PPh 23 (15%)
                                                                    </option>
                                                                    <option value="waba_pph23_c_sppb_1_1">PPh 23 (0%)
                                                                    </option>
                                                                    <option value="waba_pph26_a_sppb_1_1">PPh 26 (0%)
                                                                    </option>
                                                                    <option value="waba_pph26_b_sppb_1_1">PPh 26 (10%)
                                                                    </option>
                                                                    <option value="waba_pph26_c_sppb_1_1">PPh 26 (20%)
                                                                    </option>
                                                                    <option value="waba_pasal4ayat2_sppb_1_1">Pasal 4 Ayat
                                                                        2</option>
                                                                    <option value="waba_nilai_lain_sppb_1_1">DPP Nilai
                                                                        Lain
                                                                    </option>
                                                                    <option value="waba_manual_sppb_1_1">PPh Manual
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row" id="pph_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh</label>
                                                            <div class="col-sm-9">
                                                                <select name="uraian_sppb[1][1][pilih_pph_sppb]"
                                                                    id="pilih_pph_sppb_1_1" class="form-control">
                                                                    <option value="" disabled selected>-- Pilih --
                                                                    </option>
                                                                    <option value="pph21_a_sppb_1_1">PPh 21 (2,5%)
                                                                    </option>
                                                                    <option value="pph21_b_sppb_1_1">PPh 21 (7,5%)
                                                                    </option>
                                                                    <option value="pph21_c_sppb_1_1">PPh 21 (12,5%)
                                                                    </option>
                                                                    <option value="pph22_a_sppb_1_1">PPh 22</option>
                                                                    <option value="pph23_a_sppb_1_1">PPh 23 (2%)</option>
                                                                    <option value="pph23_b_sppb_1_1">PPh 23 (15%)</option>
                                                                    <option value="pph23_c_sppb_1_1">PPh 23 (0%)</option>
                                                                    <option value="pph26_a_sppb_1_1">PPh 26 (0%)</option>
                                                                    <option value="pph26_b_sppb_1_1">PPh 26 (10%)</option>
                                                                    <option value="pph26_c_sppb_1_1">PPh 26 (20%)</option>
                                                                    <option value="pphpasal4_ayat2_sppb_1_1">Pasal 4 Ayat
                                                                        2
                                                                    </option>
                                                                    <option value="pph_manual_sppb_1_1">Manual</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <!-- END PAJAK WAPU WABA PPH MANUAL SPPB -->
                                                        <!-- DPP LAIN DAN MANUAL PAJAK  -->
                                                        <div class="form-group row" id="dpp_nilai_lain_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">DPP Nilai Lain*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="1.1%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="waba_nilai_lain"
                                                                    id="44" value="0.011">
                                                            </div>
                                                        </div>

                                                        <!-- END DPP LAIN DAN MANUAL PAJAK  -->

                                                        <!-- GRUP INFO PERSEN PPh-->
                                                        <div class="form-group row" id="pph_normal_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="11%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_normal_sppb_1_1"
                                                                    id="11" value="0.11">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row" id="pph21_a_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 21 2,5%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="2,5%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph21_a_sppb_1_1"
                                                                    id="pph21a" value="0.025">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="pph21_b_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 21 7,5%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="7,5%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph21_b_sppb_1_1"
                                                                    id="pph21b" value="0.075">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="pph21_c_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 21 12,5%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="12,5%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph21_c_sppb_1_1"
                                                                    id="pph21c" value="0.125">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row" id="pph22_a_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 22*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="1,5%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph22_a_sppb_1_1"
                                                                    id="pph22" value="0.015">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row" id="pph23_a_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 23 2%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="2%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph23_a_sppb_1_1"
                                                                    id="pph23a" value="0.02">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="pph23_b_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 23 15%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="15%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph23_b_sppb_1_1"
                                                                    id="pph23b" value="0.15">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="pph23_c_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 23 0%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="0%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph23_c_sppb_1_1"
                                                                    id="pph23c" value="0">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row" id="pph26_a_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 26 0%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="0%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph26_sppb_1_1"
                                                                    id="pph26a" value="0">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="pph26_b_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 26 10%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="10%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph26_sppb_1_1"
                                                                    id="pph26b" value="0.1">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="pph26_c_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 26 20%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="20%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph26_sppb_1_1"
                                                                    id="pph26c" value="0.2">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row" id="pasal4_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">Pasal 4 Ayat 2*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="10%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pasal4_sppb_1_1"
                                                                    id="pasal4" value="0.10">
                                                            </div>
                                                        </div>




                                                        <!-- END GRUP INFO PERSEN PPh-->
                                                        <!-- GRUP NOMINAL SPPB -->
                                                        <div class="form-group row" id="manual_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">Manual PPh*</label>
                                                            <div class="col-sm-2">
                                                                <input type="Number" name="uraian_sppb[1][1][manual]"
                                                                    id="nominal_manual_sppb_1_1" class="form-control">
                                                            </div>
                                                            <span>
                                                                <h3>%</h3>
                                                            </span>
                                                        </div>
                                                        <div class="form-group row" id="dpp_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">DPP</label>
                                                            <div class="col-sm-9">
                                                                <div class="group">
                                                                    <input type="text" id="nominal_sppb_1_1"
                                                                        name="uraian_sppb[1][1][jumlah]"
                                                                        class="form-control nominal validate_sppb validate_spp_all"
                                                                        placeholder="Nominal PPb" autocomplete="off"
                                                                        required>
                                                                    <input type="hidden" id="tidak_nominal_sppb_1_1"
                                                                        placeholder="Nominal tanpa titik PPb"
                                                                        autocomplete="off">
                                                                    <label
                                                                        class="col-sm-6 col-form-label sppbcek_dana_gagal_1_1"
                                                                        style="display:none;color:red;">Dana melebihi sisa
                                                                        RKAP</label>
                                                                    <label
                                                                        class="col-sm-6 col-form-label sppbcek_dana_berhasil_1_1"
                                                                        style="display:none;color:green;">Dana dibawah
                                                                        sisa RKAP</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="nominal_pph_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh</label>
                                                            <div class="col-sm-9">

                                                                <input type="text" id="uraian_pph_sppb_1_1"
                                                                    name="uraian_sppb[1][1][pph]" class="form-control"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="nominal_potongan_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">Nominal
                                                                Potongan</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" id="potongan_sppb_1_1"
                                                                    name="uraian_sppb[1][1][potongan]"
                                                                    class="form-control" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="nominal_ppn_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPn</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" id="ppn_sppb_1_1"
                                                                    name="uraian_sppb[1][1][pajak]" class="form-control"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="dpp_ppn_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">DPP + PPN</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" id="dppppn_sppb_1_1"
                                                                    name="uraian_sppb[1][1][dpp_ppn]"
                                                                    class="form-control" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="nominal_akhir_sppb_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">Nominal Akhir</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" id="akhir_sppb_1_1"
                                                                    name="uraian_sppb[1][1][total_pajak]"
                                                                    class="form-control" readonly>
                                                            </div>
                                                        </div>
                                                        <!-- END GRUP NOMINAL SPPB -->
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <div class="col-sm-12" style="margin-bottom: 10px">
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                onclick="tambah_sub_isi_sppb(1,1)">+</button>
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
                                    Form PPn
                                </div>
                                <div class="panel-body">

                                    <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                        <ul class="nav" role="tablist">
                                            <li class="active"><a href="#tab-informasi-sppn" role="tab"
                                                    data-toggle="tab">Informasi</a></li>
                                            <li><a href="#tab-isi-sppn" role="tab" data-toggle="tab">Isi</a></li>
                                        </ul>
                                    </div>
                                    <div class="tab-content">

                                        <!-- TAB INFORMASI -->
                                        <div class="form-group row">
                                            <div class="col-sm-10">
                                                <input type="hidden" id="hakakses" name="hakakses"
                                                    class="form-control" placeholder="{{ $bagianid }}"
                                                    value="{{ $bagianid }}" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="tab-pane fade in active" id="tab-informasi-sppn">
                                            <div class="form-group row" id="form_kwitansi_sppn">
                                                <label class="col-sm-2 col-form-label" id="label_kwitansi_sppn">Kwitansi
                                                    dan/atau Invoice *</label>
                                                <div class="col-sm-10">
                                                    <input type="text" id="kwitansi_sppn" name="kwitansi_sppn"
                                                        class="form-control validate_sppn validate_spp_all"
                                                        placeholder="Nama Pihak Kwitansi SPPn" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row" id="form_referensi_sppn">
                                                <label class="col-sm-2 col-form-label">Referensi</label>
                                                <div class="col-sm-10">
                                                    <input type="text" maxlength="50" id="referensi_sppn" name="referensi_sppn"
                                                        class="form-control" placeholder="Nomor Referensi SPPn"
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label" id="label_au58_sppn">BA/AU.58
                                                    *</label>
                                                <div class="col-sm-10">
                                                    <input type="text" id="au58_sppn" name="au58_sppn"
                                                        class="form-control" placeholder="Nomor BA/AU. 58 SPPn"
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div id="fp_sppn">
                                                <div id="faktur_pajak_sppn_1">
                                                    <div class="form-group row" id="form_faktur_pajak_sppn">
                                                        <label class="col-sm-2 col-form-label">Faktur Pajak </label>
                                                        <div class="col-sm-4">
                                                            <input type="text" id="faktur_pajak_sppn_1"
                                                                maxlength="17" name="faktur_pajak_sppn[1][fp]"
                                                                class="form-control"
                                                                placeholder="Nomor Faktur Pajak SPPn 1"
                                                                autocomplete="off" required>
                                                        </div>
                                                        <label class="col-sm-1">Tanggal Faktur Pajak</label>
                                                        <div class="col-sm-3">
                                                            <input type="date" class="form-control"
                                                                id="tanggal_faktur_pajak_sppn"
                                                                name="tanggal_faktur_pajak_sppn[1][tanggal]">
                                                        </div>
                                                        <div class="col-sm-2" id="btn_faktur_pajak_sppn_1">
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                onclick="tambah_faktur_pajak_sppn(1)">+</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row" id="form_sp_opl_sppn">
                                                <label class="col-sm-2 col-form-label">No. Kontrak/dokumen pendukung lain
                                                    *</label>
                                                <div class="col-sm-10">
                                                    <input type="text" id="sp_opl_sppn" name="sp_opl_sppn"
                                                        class="form-control validate_sppn validate_spp_all"
                                                        placeholder="Nomor Kontrak SPPn" autocomplete="off">
                                                </div>
                                            </div>

                                            @if ($hakakses == 1)
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Bagian *</label>
                                                    <div class="col-sm-10">
                                                        <select class="form-control validate_sppn validate_spp_all"
                                                            id="bagian_sppn" name="bagian_sppn">
                                                            <option value="" disabled selected>-- Pilih Bagian --
                                                            </option>
                                                            @foreach ($bagianall as $b)
                                                                <option value="{{ $b->master_bagian_id }}">
                                                                    {{ $b->master_bagian_nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Tanggal *</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" id="tanggal_sppn" name="tanggal_sppn"
                                                            class="form-control date validate_sppn validate_spp_all"
                                                            placeholder="Tanggal SPPn" value="{{ DATE('d-m-Y') }}"
                                                            autocomplete="off" required>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Bagian *</label>
                                                    <div class="col-sm-10">
                                                        <select class="form-control validate_sppn validate_spp_all"
                                                            id="bagian_sppn" name="bagian_sppn" readonly>
                                                            <option value="" disabled>-- Pilih Bagian --</option>
                                                            <option value="{{ $bagian->master_bagian_id }}" selected>
                                                                {{ $bagian->master_bagian_nama }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Tanggal *</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" id="tanggal_sppn" name="tanggal_sppn"
                                                            class="form-control validate_sppn validate_spp_all"
                                                            placeholder="Tanggal SPPn" value="{{ DATE('d-m-Y') }}"
                                                            autocomplete="off" readonly required>
                                                    </div>
                                                </div>
                                            @endif
                                            {{-- <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Metode Pembayaran *</label>
                                            <div class="col-sm-10">
                                                <select class="form-control validate_sppn validate_spp_all"
                                                    id="metode_pembayaran_sppn" name="metode_pembayaran_sppn" required>
                                                    <option value="" disabled selected>-- Pilih Metode
                                                        Pembayaran --
                                                    </option>
                                                    <option value="kas">Kas</option>
                                                    <option value="bank">Bank</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row" style="display:none;" id="pilih_lampirkan_sppn"
                                            onclick="pilih_data_sppn()">
                                            <label class="col-sm-2 col-form-label"></label>
                                            <div class="col-sm-10">
                                                <div class="col-sm-2">
                                                    <label class="fancy-radio">
                                                        <input name="pilih_data_sppn" id="input_data_sppn"
                                                            value="input_data" type="radio">
                                                        <span style="font-size:17px"><i></i>Data diinputkan </span>
                                                    </label>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label class="fancy-radio">
                                                        <input name="pilih_data_sppn" id="lampirkan_data_sppn"
                                                            value="lampirkan_data" type="radio" checked="checked">
                                                        <span style="font-size:17px"><i></i>Data dilampirkan</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="kas_sppn" style="display:none">
                                            <div class="form-group row" id="atas_nama_karyawan_kas">
                                                <!-- <label class="col-sm-2 col-form-label">Karyawan *</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" id="atas_nama_bank_sppn_kas_1"
                                                        name="atas_nama_bank_sppn_kas[1]">
                                                        <option value="" disabled selected>--Pilih Karyawan--
                                                        </option>
                                                        @foreach ($karyawan as $k)
                                                        <option value="{{ $k->karyawan_nama }}">
                                                            {{ $k->karyawan_nama }}</option>
                                                        @endforeach
                                                    </select> -->
                                                </div>
                                                <div class="col-sm-2" id="btn_karyawan_kas_sppn_1">
                                                    <button type="button" class="btn btn-success btn-sm"
                                                        onclick="tambah_karyawan_kas_sppn(1)">+</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="bank_sppn" style="display: none">
                                            <input type="hidden" id="id_bank_sppn_1" name="id_bank_sppn"
                                                class="form-control" onclick="data_bank_sppn(1)"
                                                placeholder="Id Bank SPPn" autocomplete="off">
                                            <div class="form-group row" id="atas_nama_vendor_sppn">
                                                <label class="col-sm-2 col-form-label">Atas Nama Rekening *</label>
                                                <div class="col-sm-10">
                                                    <input type="text" id="atas_nama_bank_sppn_vendor"
                                                        name="atas_nama_bank_sppn_vendor" class="form-control"
                                                        onclick="data_bank_sppn(1)" placeholder="Atas Nama Bank SPPn"
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Nama Bank *</label>
                                                <div class="col-sm-10">
                                                    <input type="text" id="nama_bank_sppn_vendor"
                                                        name="nama_bank_sppn_vendor" class="form-control"
                                                        onclick="data_bank_sppn(1)" placeholder="Nama Rekening Bank"
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Nomor Rekening *</label>
                                                <div class="col-sm-10">
                                                    <input type="text" id="rekening_bank_sppn_vendor"
                                                        name="rekening_bank_sppn_vendor" class="form-control"
                                                        onclick="data_bank_sppn(1)"
                                                        placeholder="Nomor Rekening Bank SPPn" autocomplete="off">
                                                </div>
                                            </div>


                                        </div>
                                        <div id="bank_sppn_karyawan" style="display: none">
                                            <div class="form-group row" id="atas_nama_karyawan_bank_sppn">
                                                <label class="col-sm-2 col-form-label">Atas Nama Rekening *</label>
                                                <div class="col-sm-8">
                                                    <input type="text" id="atas_nama_bank_sppn_karyawan_1"
                                                        onclick="bank_karyawan_sppn(1)" name="karyawan_sppn[1][nama]"
                                                        class="form-control" placeholder="Atas Nama Bank SPPn"
                                                        autocomplete="off">
                                                </div>
                                                <div class="col-sm-2" id="btn_karyawan_bank_sppn_1">
                                                    <button type="button" class="btn btn-success btn-sm"
                                                        onclick="tambah_karyawan_bank_sppn(1)">+</button>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Nama Bank *</label>
                                                <div class="col-sm-8">
                                                    <input type="text" id="nama_bank_sppn_karyawan_1"
                                                        name="karyawan_sppn[1][bank]" class="form-control"
                                                        onclick="bank_karyawan_sppn(1)" placeholder="Nama Bank SPPn"
                                                        autocomplete="off">
                                                </div>

                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Nomor Rekening *</label>
                                                <div class="col-sm-8">
                                                    <input type="text" id="rekening_bank_sppn_karyawan_1"
                                                        name="karyawan_sppn[1][no_rek]" onclick="bank_karyawan_sppn(1)"
                                                        class="form-control" placeholder="Nomor Rekening Bank SPPn"
                                                        autocomplete="off">
                                                </div>
                                            </div>

                                        </div> --}}
                                            <div id="diterima_sppn_input" style="display:none;">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Diterima dari *</label>
                                                    <div id="kas_karyawan_input">
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control"
                                                                id="nama_diterima_sppn_input" name="diterima_dari"
                                                                Placeholder="Nama Penerima">
                                                            <span style="font-size: 10px;color:red;">Tulis "Terlampir"
                                                                jika data lebih dari 1 (satu)</span>
                                                        </div>
                                                        {{-- <div class="col-sm-2" id="btn_karyawan_kas_sppb_input_1">
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            onclick="tambah_karyawan_kas_sppb_input(1)">+</button>
                                                    </div> --}}
                                                    </div>
                                                </div>
                                                <div class="form-group row" id="alamat_karyawan_kas_input_1">
                                                    <label class="col-sm-2 col-form-label">Alamat *</label>
                                                    <div id="kas_karyawan_input">
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control"
                                                                id="alamat_diterima_sppn_input" name="alamat_sppn"
                                                                Placeholder="Alamat">
                                                            <span style="font-size: 10px;color:red;">Tulis "Terlampir"
                                                                jika data lebih dari 1 (satu)</span>
                                                        </div>
                                                        {{-- <div class="col-sm-2" id="btn_karyawan_kas_sppb_input_1">
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            onclick="tambah_karyawan_kas_sppb_input(1)">+</button>
                                                    </div> --}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Catatan</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" id="catatan_sppn" name="catatan_sppn" placeholder="Catatan" rows="4"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label"
                                                    id="label_dokumen_pendukung_sppn">Upload Dokumen Pendukung</label>
                                                <div class="col-sm-10">
                                                    <div class="file-loading">
                                                        <input type="file" id="dokumen_pendukung_sppn"
                                                            name="dokumen_pendukung_sppn[]" class="file-multiple"
                                                            accept="application/pdf, image/*" multiple>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- END TAB INFORMASI -->

                                        <!-- TAB ISI -->
                                        <div class="tab-pane fade" id="tab-isi-sppn">
                                            <div id="isi_sppn_1" class="col-sm-12">
                                                <div
                                                    style="padding-bottom: 10px; margin-bottom: 20px; border-bottom: 1px solid #eaeaea;">
                                                    <font size="4" style="margin-right: 20px">Isi 1. </font>
                                                    <button type="button" class="btn btn-info btn-sm"
                                                        onclick="tambah_isi_sppn()">+</button>
                                                </div>
                                                <div class="col-sm-5">
                                                    {{-- <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">Kode KBB *</label>
                                                    <div class="col-sm-9">
                                                        <div class="row-fluid" id="parent_kbb_sppn_1">
                                                            <select class="selectpicker" data-live-search="true"
                                                                data-dropup-auto="false" id="kode_kbb_sppn_1"
                                                                data-width="100%" name="isi_sppn[1][kode_kbb]"
                                                                data-size="7"
                                                                onchange="pilih_rekening_sppn(1,'sap_vendor_sppn_')">
                                                                <option value="" disabled selected>---Pilih KBB---
                                                                </option>
                                                                select>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Kode SAP *</label>
                                                        <label class="col-sm-9">
                                                            <select class="form-control validate_sppn validate_spp_all"
                                                                id="jenis_sap_sppn_1" onchange="js_sppn(1)"
                                                                name="isi_sppn[1][jenis_sap]">
                                                                <option value="" disabled selected>-- Pilih Jenis
                                                                    Kode SAP --</option>
                                                                <option value="vendor">Nomor Vendor</option>
                                                                <option value="gl">Nomor GL</option>
                                                                <option value="customer">Nomor Customer</option>
                                                                {{-- @if (in_array($bagianid, [124, 126, 127]))
                                                                    <option value="customer">Nomor Customer</option>
                                                                @endif --}}
                                                            </select>
                                                        </label>
                                                        <label class="col-sm-3 col-form-label"></label>

                                                        <div id="nomor_vendor_sppn_1" style="display:none">
                                                            <div class="col-sm-9">
                                                                <div class="row-fluid" id="parent_kbb_sppn_1">
                                                                    <select class="select2-vendor sap-vendor-sppn"
                                                                        id="sap_vendor_sppn_1" name="isi_sppn_rekening"
                                                                        onchange="pilih_rekening_sppn(1,'sap_vendor_sppn_')">
                                                                        <option disabled selected>---Pilih SAP---</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="text" style="display:none;"
                                                                    id="sap_vendor_sppn_id_1" name="isi_sppn[1][vendor]"
                                                                    class="form-control" onclick="kode_rekening_sppn(1)"
                                                                    placeholder="Kode SAP (Nomor Vendor)"
                                                                    autocomplete="off" required>
                                                            </div>
                                                        </div>

                                                        <div id="nomor_gl_sppn_1" style="display:none">
                                                            <div class="col-sm-9">
                                                                <div class="row-fluid" id="parent_kbb_sppn_1">
                                                                    <select class="select2-gl" id="sap_gl_sppn_1"
                                                                        data-size="7" data-width="100%"
                                                                        name="isi_sppn_gl"
                                                                        onchange="pilih_rekening_sppn(1,'sap_gl_sppn_')">
                                                                        <option value="" disabled selected>-- Pilih
                                                                            Kode GL --</option>
                                                                        {{-- @foreach ($gl as $r)
                                                                    <option value="{{ $r->master_gl_id }}"
                                                                        data-budgetsppn_1="{{ $r->jumlah_budget }}"
                                                                        data-budgetsppn_1="{{ $r->master_gl_id }}">
                                                                        {{ $r->master_gl_kode }}
                                                                        ({{ $r->master_gl_keterangan }})
                                                                    </option>
                                                                    @endforeach --}}
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <label class="col-sm-12 col-form-label mt-2"></label>
                                                            <label class="col-sm-3 col-form-label">RKAP</label>
                                                            <div class="col-sm-9">
                                                                <input type="text"
                                                                    class="form-control budget_glsppn_1 nominal"
                                                                    placeholder="Budget" autocomplete="off" readonly>
                                                                <input type="hidden" class="budget_glsppn_hide_1"
                                                                    name="budget">
                                                            </div>
                                                            <label class="col-sm-12 col-form-label mt-2"></label>
                                                            <label class="col-sm-3 col-form-label">Realisasi</label>
                                                            <div class="col-sm-9">
                                                                <input type="text"
                                                                    class="form-control realisasisppn_1 nominal"
                                                                    placeholder="Realisasi" autocomplete="off" readonly>
                                                                <input type="hidden" class="budget_glsppn_hide"
                                                                    name="budget">
                                                            </div>
                                                            <label class="col-sm-12 col-form-label mt-2"></label>
                                                            <label class="col-sm-3 col-form-label">On Process</label>
                                                            <div class="col-sm-9">
                                                                <input type="text"
                                                                    class="form-control onprosessppn_1 nominal"
                                                                    placeholder="Onproses" autocomplete="off" readonly>
                                                                <input type="hidden" class="budget_glsppn_hide"
                                                                    name="budget">
                                                            </div>
                                                            <label class="col-sm-12 col-form-label mt-2"></label>
                                                            <label class="col-sm-3 col-form-label">Sisa</label>
                                                            <div class="col-sm-9">
                                                                <input type="text"
                                                                    class="form-control sisasppn_1 nominal"
                                                                    placeholder="Sisa" autocomplete="off" readonly>
                                                                <input type="hidden" class="budget_glsppn_hide"
                                                                    name="budget">
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="text" style="display:none;"
                                                                    id="sap_gl_sppn_id_1" name="isi_sppn[1][gl]"
                                                                    class="form-control" onclick="kode_gl_sppn(1)"
                                                                    placeholder="Kode SAP (Nomor GL)" autocomplete="off"
                                                                    required>
                                                            </div>
                                                        </div>

                                                        {{-- @if (in_array($bagianid, [124, 126, 127])) --}}
                                                        <div id="nomor_customer_sppn_1" style="display:none">
                                                            <div class="col-sm-9">
                                                                <div class="row-fluid" id="parent_kbb_sppn_1">
                                                                    <select class="select2-customer slct_sppn"
                                                                        id="sap_customer_sppn_1"
                                                                        name="isi_sppn_customer"
                                                                        onchange="pilih_rekening_sppn(1, 'sap_customer_sppn_')">
                                                                        <option disabled selected>
                                                                            ---Pilih Kode Customer---
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="text" style="display:none;"
                                                                    id="sap_customer_sppn_id_1"
                                                                    name="isi_sppn[1][customer]" class="form-control"
                                                                    onclick="kode_customer_sppn(1)"
                                                                    placeholder="Kode SAP (Nomor Customer)"
                                                                    autocomplete="off" required>
                                                            </div>
                                                        </div>
                                                        {{-- @endif --}}
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Cost/Profit*</label>
                                                        <label class="col-sm-9">
                                                            <select class="form-control validate_sppn validate_spp_all"
                                                                id="jenis_center_sppn_1" onchange="jc_sppn(1)"
                                                                name="isi_sppn[1][jenis_center]">
                                                                <option value="" disabled selected>-- Pilih --
                                                                </option>
                                                                <option value="cost_center">Cost Center</option>
                                                                <option value="profit_center">Profit Center</option>
                                                            </select>
                                                        </label>
                                                        <label class="col-sm-3 col-form-label"></label>
                                                        <div class="col-sm-9" id="cost_center_sppn_1"
                                                            style="display: none">
                                                            <select class="select2-costcenter"
                                                                id="select_cost_center_sppn_1"
                                                                name="isi_sppn[1][cost_center]">
                                                                {{-- <option value="" disabled selected>-- Pilih Cost
                                                                Center --</option> --}}
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-9" id="profit_center_sppn_1"
                                                            style="display: none">
                                                            <select class="select2-profitcenter"
                                                                id="select_profit_center_sppn_1"
                                                                name="isi_sppn[1][profit_center]">
                                                            </select>
                                                        </div>
                                                        {{-- @if ($bagian_karyawan->pemisah_keb_bag == 1)
                                                    <label class="col-sm-9">
                                                        <input type="text" class="form-control" value="Cost Center"
                                                            readonly>
                                                        <input type="hidden" name="isi_sppn[1][jenis_center]"
                                                            value="cost_center" readonly>
                                                    </label>
                                                    <label class="col-sm-3 col-form-label"></label>
                                                    <div class="col-sm-9">
                                                        @foreach ($cost_center_id as $ccid)
                                                        <input type="text" class="form-control"
                                                            value="{{ $ccid->master_cost_center_kode }} {{ $ccid->master_cost_center_keterangan }}"
                                                            readonly>
                                                        <input type="hidden" name="isi_sppn[1][cost_center]"
                                                            value="{{ $cost->master_cost_center_id }}" readonly>
                                                        @endforeach
                                                    </div>
                                                    @elseif($bagian_karyawan->pemisah_keb_bag == 2)
                                                    <label class="col-sm-9">
                                                        <input type="text" class="form-control" value="Profit Center"
                                                            readonly>
                                                        <input type="hidden" name="isi_sppn[1][jenis_center]"
                                                            value="profit_center" readonly>
                                                    </label>
                                                    <label class="col-sm-3 col-form-label"></label>
                                                    <div class="col-sm-9">
                                                        @foreach ($cost_center_id as $ccid)
                                                        <input type="text" class="form-control"
                                                            value="{{ $ccid->master_profit_center_kode }} {{ $ccid->master_profit_unit }}"
                                                            readonly>
                                                        <input type="hidden" name="isi_sppn[1][profit_center]"
                                                            value="{{ $ccid->master_profit_center_id }}" readonly>
                                                        @endforeach
                                                    </div>
                                                    @endif --}}
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Cash Flow*</label>
                                                        <div class="col-sm-9">
                                                            <select
                                                                class="form-control validate_sppn validate_spp_all select2-cashflow"
                                                                id="cash_flow_sppn" name="isi_sppn[1][cash_flow]">
                                                                <option value="" disabled selected>-- Pilih Cash
                                                                    Flow --</option>
                                                                @foreach ($cashflow as $cash)
                                                                    <option value="{{ $cash->master_cash_flow_id }}">
                                                                        {{ $cash->master_cash_flow_key }}
                                                                        {{ $cash->master_cash_flow_keterangan }}</option>
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
                                                                    <div id="uraian_sppn_1_1"
                                                                        style="height:auto;min-height:100px;">
                                                                        <textarea class="form-control" id="ckeditors_1_1" contenteditable="true" name="uraian_sppn[1][1][ket]"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- PAJAK WAPU WABA PPH MANUAL SPPN -->
                                                        <div class="form-group row formInputPajakSppn">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">Pajak*</label>
                                                            <div class="col-sm-9">
                                                                <select name="uraian_sppn[1][1][type_pajak_sppn]"
                                                                    id="pilih_pajak_sppn_1_1"
                                                                    class="validasi_pajak form-control validate_sppn validate_spp_all"
                                                                    onchange="jenis_pajak_sppn(1,1);">
                                                                    <option value="" selected disabled>-- Pilih
                                                                        Pajak --</option>
                                                                    <option value="wapu_sppn_1_1">WAPU</option>
                                                                    <option value="waba_sppn_1_1">WABA</option>
                                                                    <option value="pph_sppn_1_1">PPh</option>
                                                                    <option value="tanpa_pajak_sppn_1_1">Tanpa Pajak
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="wapu_pph_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh</label>
                                                            <div class="col-sm-9">
                                                                <select name="uraian_sppn[1][1][pilih_wapu_sppn]"
                                                                    id="pilih_wapu_sppn_1_1" class="form-control">
                                                                    <option value="" disabled selected>-- Pilih --
                                                                    </option>
                                                                    <option value="wapu_normal_sppn_1_1">Normal</option>
                                                                    <option value="wapu_pph21_a_sppn_1_1">PPh 21 (2,5%)
                                                                    </option>
                                                                    <option value="wapu_pph21_b_sppn_1_1">PPh 21 (7,5%)
                                                                    </option>
                                                                    <option value="wapu_pph21_c_sppn_1_1">PPh 21 (12,5%)
                                                                    </option>
                                                                    <option value="wapu_pph22_a_sppn_1_1">PPh 22</option>
                                                                    <option value="wapu_pph23_a_sppn_1_1">PPh 23 (2%)
                                                                    </option>
                                                                    <option value="wapu_pph23_b_sppn_1_1">PPh 23 (15%)
                                                                    </option>
                                                                    <option value="wapu_pph23_c_sppn_1_1">PPh 23 (0%)
                                                                    </option>
                                                                    <option value="wapu_pph26_a_sppn_1_1">PPh 26 (0%)
                                                                    </option>
                                                                    <option value="wapu_pph26_b_sppn_1_1">PPh 26 (10%)
                                                                    </option>
                                                                    <option value="wapu_pph26_c_sppn_1_1">PPh 26 (20%)
                                                                    </option>
                                                                    <option value="wapu_pasal4ayat2_sppn_1_1">Pasal 4 Ayat
                                                                        2</option>
                                                                    <option value="wapu_nilai_lain_sppn_1_1">DPP Nilai
                                                                        Lain</option>
                                                                    <option value="wapu_manual_sppn_1_1">PPh Manual
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row" id="waba_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh</label>
                                                            <div class="col-sm-9">
                                                                <select name="uraian_sppn[1][1][pilih_waba_sppn]"
                                                                    id="pilih_waba_sppn_1_1" class="form-control">
                                                                    <option value="" disabled selected>-- Pilih --
                                                                    </option>
                                                                    <option value="waba_normal_sppn_1_1">Normal</option>
                                                                    <option value="waba_pph21_a_sppn_1_1">PPh 21 (2,5%)
                                                                    </option>
                                                                    <option value="waba_pph21_b_sppn_1_1">PPh 21 (7,5%)
                                                                    </option>
                                                                    <option value="waba_pph21_c_sppn_1_1">PPh 21 (12,5%)
                                                                    </option>
                                                                    <option value="waba_pph22_a_sppn_1_1">PPh 22</option>
                                                                    <option value="waba_pph23_a_sppn_1_1">PPh 23 (2%)
                                                                    </option>
                                                                    <option value="waba_pph23_b_sppn_1_1">PPh 23 (15%)
                                                                    </option>
                                                                    <option value="waba_pph23_c_sppn_1_1">PPh 23 (0%)
                                                                    </option>
                                                                    <option value="waba_pph26_a_sppn_1_1">PPh 26 (0%)
                                                                    </option>
                                                                    <option value="waba_pph26_b_sppn_1_1">PPh 26 (10%)
                                                                    </option>
                                                                    <option value="waba_pph26_c_sppn_1_1">PPh 26 (20%)
                                                                    </option>
                                                                    <option value="waba_pasal4ayat2_sppn_1_1">Pasal 4 Ayat
                                                                        2</option>
                                                                    <option value="waba_nilai_lain_sppn_1_1">DPP Nilai
                                                                        Lain</option>
                                                                    <option value="waba_manual_sppn_1_1">PPh Manual
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row" id="pph_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh</label>
                                                            <div class="col-sm-9">
                                                                <select name="uraian_sppn[1][1][pilih_pph_sppn]"
                                                                    id="pilih_pph_sppn_1_1" class="form-control">
                                                                    <option value="" disabled selected>-- Pilih --
                                                                    </option>
                                                                    <option value="pph21_a_sppn_1_1">PPh 21 (2,5%)
                                                                    </option>
                                                                    <option value="pph21_b_sppn_1_1">PPh 21 (7,5%)
                                                                    </option>
                                                                    <option value="pph21_c_sppn_1_1">PPh 21 (12,5%)
                                                                    </option>
                                                                    <option value="pph22_a_sppn_1_1">PPh 22</option>
                                                                    <option value="pph23_a_sppn_1_1">PPh 23 (2%)</option>
                                                                    <option value="pph23_b_sppn_1_1">PPh 23 (15%)</option>
                                                                    <option value="pph23_c_sppn_1_1">PPh 23 (0%)</option>
                                                                    <option value="pph26_a_sppn_1_1">PPh 26 (0%)</option>
                                                                    <option value="pph26_b_sppn_1_1">PPh 26 (10%)</option>
                                                                    <option value="pph26_c_sppn_1_1">PPh 26 (20%)</option>
                                                                    <option value="pphpasal4_ayat2_sppn_1_1">Pasal 4 Ayat
                                                                        2</option>
                                                                    <option value="pph_manual_sppn_1_1">Manual</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <!-- END PAJAK WAPU WABA PPH MANUAL SPPN -->
                                                        <!-- DPP LAIN DAN MANUAL PAJAK  -->
                                                        <div class="form-group row" id="dpp_nilai_lain_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">DPP Nilai Lain*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="1.1%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="waba_nilai_lain"
                                                                    id="44" value="0.011">
                                                            </div>
                                                        </div>

                                                        <!-- END DPP LAIN DAN MANUAL PAJAK  -->

                                                        <!-- GRUP INFO PERSEN PPh-->
                                                        <div class="form-group row" id="pph_normal_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="11%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_normal_sppn_1_1"
                                                                    id="11" value="0.11">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row" id="pph21_a_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 21 2,5%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="2,5%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph21_a_sppn_1_1"
                                                                    id="pph21a" value="0.025">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="pph21_b_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 21 7,5%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="7,5%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph21_b_sppn_1_1"
                                                                    id="pph21b" value="0.075">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="pph21_c_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 21 12,5%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="12,5%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph21_c_sppn_1_1"
                                                                    id="pph21c" value="0.125">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row" id="pph22_a_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 22*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="1,5%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph22_a_sppn_1_1"
                                                                    id="pph22" value="0.015">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row" id="pph23_a_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 23 2%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="2%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph23_a_sppn_1_1"
                                                                    id="pph23a" value="0.02">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="pph23_b_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 23 15%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="15%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph23_b_sppn_1_1"
                                                                    id="pph23b" value="0.15">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="pph23_c_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 23 0%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="0%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph23_c_sppn_1_1"
                                                                    id="pph23c" value="0">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row" id="pph26_a_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 26 0%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="0%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph26_sppn_1_1"
                                                                    id="pph26a" value="0">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="pph26_b_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 26 10%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="10%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph26_sppn_1_1"
                                                                    id="pph26b" value="0.1">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="pph26_c_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh 26 20%*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="20%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pph26_sppn_1_1"
                                                                    id="pph26c" value="0.2">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row" id="pasal4_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">Pasal 4 Ayat 2*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" placeholder="10%"
                                                                    class="form-control" disabled>
                                                                <input type="hidden" name="input_pasal4_sppn_1_1"
                                                                    id="pasal4" value="0.10">
                                                            </div>
                                                        </div>
                                                        <!-- END GRUP INFO PERSEN PPh-->
                                                        <!-- GRUP NOMINAL SPPN -->
                                                        <div class="form-group row" id="manual_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">Manual PPh*</label>
                                                            <div class="col-sm-2">
                                                                <input type="Number" name="uraian_sppn[1][1][manual]"
                                                                    id="nominal_manual_sppn_1_1" class="form-control">
                                                            </div>
                                                            <span>
                                                                <h3>%</h3>
                                                            </span>
                                                        </div>
                                                        <div class="form-group row" id="dpp_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">DPP</label>
                                                            <div class="col-sm-9">
                                                                <div class="group">
                                                                    <input type="text" id="nominal_sppn_1_1"
                                                                        name="uraian_sppn[1][1][jumlah]"
                                                                        class="form-control nominal validate_sppn validate_spp_all"
                                                                        placeholder="Nominal PPn" autocomplete="off"
                                                                        required>
                                                                    <input type="hidden" id="tidak_nominal_sppn_1_1"
                                                                        placeholder="Nominal tanpa titik PPn"
                                                                        autocomplete="off" required>
                                                                    <label
                                                                        class="col-sm-6 col-form-label sppncek_dana_gagal_1_1"
                                                                        style="display:none;color:red;">Dana melebihi sisa
                                                                        RKAP</label>
                                                                    <label
                                                                        class="col-sm-6 col-form-label sppncek_dana_berhasil_1_1"
                                                                        style="display:none;color:green;">Dana dibawah
                                                                        sisa RKAP</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="nominal_potongan_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">Nominal
                                                                Potongan</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" id="potongan_sppn_1_1"
                                                                    name="uraian_sppn[1][1][potongan]"
                                                                    class="form-control" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="nominal_pph_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPh</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" id="uraian_pph_sppn_1_1"
                                                                    name="uraian_sppn[1][1][pph]" class="form-control"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="nominal_ppn_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">PPn</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" id="ppn_sppn_1_1"
                                                                    name="uraian_sppn[1][1][pajak]" class="form-control"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="dpp_ppn_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">DPP + PPN</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" id="dppppn_sppn_1_1"
                                                                    name="uraian_sppn[1][1][dpp_ppn]"
                                                                    class="form-control" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="nominal_akhir_sppn_1_1"
                                                            style="display:none ;">
                                                            <label class="col-sm-1 col-form-label"></label>
                                                            <label class="col-sm-2 col-form-label">Nominal Akhir</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" id="akhir_sppn_1_1"
                                                                    name="uraian_sppn[1][1][total_pajak]"
                                                                    class="form-control" readonly>
                                                            </div>
                                                        </div>
                                                        <!-- END GRUP NOMINAL SPPN -->
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <div class="col-sm-12" style="margin-bottom: 10px">
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                onclick="tambah_sub_isi_sppn(1,1)">+</button>
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
                                        <button class="btn btn-success" type="submit" id="simpan"
                                            style="margin-bottom: 15px">Simpan</button>
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
    {{-- Modal Simpan --}}
    <div id="modal_simpan_spp" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- dialog body -->
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title" style="margin: 0 auto; font-style:bold;">Apakah anda ingin menyimpan dan
                        mencetak PP?</h3>
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
                            {{-- <tbody>
                            @foreach ($rekening as $key => $value)
                            <tr>
                                <td style="display:none">{{$value->master_rekening_id}}</td>
                                <td>{{$key+1}}</td>
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
                        </tbody> --}}
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
    {{-- Modal GL SPPb --}}
    <div id="modal_gl_sppb" class="modal fade" role="dialog">
        <div class="modal-dialog" style="width:800px">
            <!-- Modal content-->
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Pilih GL SPPb</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped nowrap" style="width: 100%">
                            <thead>
                                <tr>
                                    <th style="display:none">id</th>
                                    <th>No. </th>
                                    <th>Kode</th>
                                    <th>Keterangan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($gl as $key => $value)
                            <tr>
                                <td style="display:none">{{$value->master_gl_id}}</td>
                                <td>{{$key+1}}</td>
                                <td>{{$value->master_gl_kode}}</td>
                                <td>{{$value->master_gl_keterangan}}</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm"
                                        onclick="pilih_gl_sppb('{{$value->master_gl_id}}','{{$value->master_gl_kode}}', '{{$value->master_gl_keterangan}}')"
                                        title="Pilih"><i class="fa fa-check"></i></button>
                                </td>
                            </tr>
                            @endforeach --}}
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
    {{-- End Modal GL SPPb --}}
    {{-- Modal GL sppn --}}
    <div id="modal_gl_sppn" class="modal fade" role="dialog">
        <div class="modal-dialog" style="width:800px">
            <!-- Modal content-->
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Pilih GL SPPn</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped nowrap" style="width: 100%">
                            <thead>
                                <tr>
                                    <th style="display:none">id</th>
                                    <th>No. </th>
                                    <th>Kode</th>
                                    <th>Keterangan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($gl as $key => $value)
                            <tr>
                                <td style="display:none">{{$value->master_gl_id}}</td>
                                <td>{{$key+1}}</td>
                                <td>{{$value->master_gl_kode}}</td>
                                <td>{{$value->master_gl_keterangan}}</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm"
                                        onclick="pilih_gl_sppn('{{$value->master_gl_id}}','{{$value->master_gl_kode}}', '{{$value->master_gl_keterangan}}')"
                                        title="Pilih"><i class="fa fa-check"></i></button>
                                </td>
                            </tr>
                            @endforeach --}}
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
    {{-- End Modal GL sppn --}}
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
                                {{-- @foreach ($rekening as $key => $value)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td style="display:none">{{$value->master_rekening_id}}</td>
                                <td>{{$value->master_rekening_kode_kbb}}</td>
                                <td>{{$value->master_rekening_kode_sap}}</td>
                                <td>{{$value->master_rekening_keterangan}}</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm"
                                        onclick="pilih_rekening_sppn('{{$value->master_rekening_id}}','{{$value->master_rekening_kode_kbb}}', '{{$value->master_rekening_kode_sap}}', '{{$value->master_rekening_keterangan}}')"
                                        title="Pilih"><i class="fa fa-check"></i></button>
                                </td>
                            </tr>
                            @endforeach --}}

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
                        <table class="table table-bordered table-striped nowrap" style="width: 100%"
                            id="table-vendor-sppb">
                            <thead>
                                <tr>
                                    <th>No. </th>
                                    <th style="display:none;">Id</th>
                                    <th>Nama Vendor</th>
                                    <th>Nama Bank</th>
                                    <th>No Rekening</th>
                                    <th>Atas Nama</th>
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
                        <table class="table table-bordered table-striped nowrap" style="width: 100%"
                            id="table-vendor-sppn">
                            <thead>
                                <tr>
                                    <th>No. </th>
                                    <th>Nama Vendor</th>
                                    <th>Nama Bank</th>
                                    <th>No Rekening</th>
                                    <th>Atas Nama</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($vendor as $key => $value)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td style="display:none;">{{$value->master_vendor_id}}</td>
                                <td>{{$value->master_vendor_nama}}</td>
                                <td>{{$value->master_vendor_nama_bank}}</td>
                                <td>{{$value->master_vendor_rekening}}</td>
                                <td>{{$value->master_vendor_atas_nama}}</td>
                                <td style="text-align:center">
                                    <button type="button" class="btn btn-info btn-sm"
                                        onclick="pilih_bank_sppn('{{$value->master_vendor_id}}','{{$value->master_vendor_nama_bank}}', '{{$value->master_vendor_rekening}}', '{{$value->master_vendor_atas_nama}}')"
                                        title="Pilih"><i class="fa fa-check"></i></button>
                                </td>
                            </tr>
                            @endforeach --}}

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
                                <?php $i = 1; ?>
                                @foreach ($master_karyawan as $key => $value)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td style="display:none;">{{ $value->master_karyawan_id }}</td>
                                        <td>{{ $value->master_karyawan_nama }}</td>
                                        <td>{{ $value->master_karyawan_bank }}</td>
                                        <td>{{ $value->master_karyawan_no_rekening }}</td>
                                        <td style="text-align:center">
                                            <button type="button" class="btn btn-info btn-sm"
                                                onclick="pilih_karyawan_sppb('{{ $value->master_karyawan_id }}','{{ $value->master_karyawan_nama }}', '{{ $value->master_karyawan_bank }}', '{{ $value->master_karyawan_no_rekening }}')"
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
                                <?php $i = 1; ?>


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
    <script src="{{ asset('assets/vendor/select2/select2.min.js') }}"></script>
    {{-- Harusnya cdn nya pakai yang ini, tapi kalau ini diaktifkan form editornya ga muncul
<script src="https://cdn.ckeditor.com/ckeditor5/42.0.1/ckeditor5.js"></script> --}}
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
        var rekening_sppn_id_id = '';
        var bank_sppb_id = '';
        var rekening_bank_sppb_id = '';
        var atas_nama_bank_sppb_id = '';
        var bank_sppn_id = '';
        var rekening_bank_sppn_id = '';
        var atas_nama_bank_sppn_id = '';
        var id_bank_sppb_id = '';
        var id_bank_sppn_id = '';
        var uraian_sppb = [];
        var uraian_sppn = [];
        var kind_of_spp = '';
        var index_bank_sppb_karyawan = '';
        var nama_bank_sppb_karyawan_id = '';
        var atas_nama_bank_sppb_karyawan_id = '';
        var rekening_bank_sppb_karyawan_id = '';
        var index_bank_sppn_karyawan = '';
        var nama_bank_sppn_karyawan_id = '';
        var atas_nama_bank_sppn_karyawan_id = '';
        var rekening_bank_sppn_karyawan_id = '';
        var jumlah_bank_karyawan = 1;
        var gl_sppb_id = '';
        var gl_sppb_id_id = '';
        var gl_sppn_id = '';
        var gl_sppn_id_id = '';
        var jum_nom = [];
        var sisa = [];
        var sisasppn = [];

        // HITUNG PAJAK



        function jenis_pajak_sppb(index, sub_index) {
            console.log("sppb_" + sub_index);
            console.log("sppb_" + index);

            //var nominall = $('.nominal_pajak_'+index+'_'+sub_index).val();
            // var jenis_spp = $('#isi_karyawan').val();
            // if(jenis_spp == "karyawan")
            // {
            // 	$('.form-pajak_'+index+'_'+sub_index).hide();
            // 	document.getElementById('pajak_'+index+'_'+sub_index).className = "form-control";
            // }
            // else
            // {
            // 	$('.form-pajak_'+index+'_'+sub_index).show();
            // }

            var pajak = $('#pilih_pajak_sppb_' + index + '_' + sub_index).val();
            //alert(pajak);
            console.log(pajak);

            if (pajak == "wapu_sppb_" + index + '_' + sub_index) {
                $('#wapu_pph_sppb_' + index + '_' + sub_index).show();

                $('#pph_sppb_' + index + '_' + sub_index).hide();
                $('#waba_sppb_' + index + '_' + sub_index).hide();

                $('#manual_sppb_' + index + '_' + sub_index).hide();
                $('#dpp_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_potongan_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_akhir_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                $('#normal_sppb_' + index + '_' + sub_index).hide();
                $('#pph21_sppb_' + index + '_' + sub_index).hide();
                $('#pph22_sppb_' + index + '_' + sub_index).hide();
                $('#pph23_sppb_' + index + '_' + sub_index).hide();
                $('#pph26_sppb_' + index + '_' + sub_index).hide();
                $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();

                $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_pph_sppb_' + index + '_' + sub_index).hide();
                $('#normal_sppb_' + index + '_' + sub_index).hide();
                $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();

                $('#pilih_wapu_sppb_' + index + '_' + sub_index).change(function() {
                    if ($(this).val() == "wapu_normal_sppb_" + index + '_' + sub_index) {
                        //alert("wapu normal");
                        $('#normal_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph22").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;

                            // $("#potongan_sppb_"+index+'_'+sub_index).val(val4.toLocaleString('id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));


                        })
                    } else if ($(this).val() == "wapu_pph21_a_sppb_" + index + '_' + sub_index) {
                        //alert("wapu pph21");
                        $('#pph21_a_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#nominal_ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph21a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "wapu_pph21_b_sppb_" + index + '_' + sub_index) {
                        //alert("wapu pph22");
                        $('#pph21_b_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph21b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "wapu_pph21_c_sppb_" + index + '_' + sub_index) {
                        //alert("wapu pph23");
                        $('#pph21_c_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph21c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "wapu_pph22_a_sppb_" + index + '_' + sub_index) {
                        //alert("wapu pph26");
                        $('#pph22_a_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();

                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph22").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "wapu_pph23_a_sppb_" + index + '_' + sub_index) {
                        //alert("wapu pasal 4");
                        $('#pph23_a_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph23a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "wapu_pph23_b_sppb_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#pph23_b_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph23b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "wapu_pph23_c_sppb_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#pph23_c_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph23c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "wapu_pph26_a_sppb_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#pph26_a_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph26a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "wapu_pph26_b_sppb_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#pph26_b_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph26b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "wapu_pph26_c_sppb_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#pph26_c_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph26c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "wapu_pasal4ayat2_sppb_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#pasal4_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pasal4").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "wapu_nilai_lain_sppb_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("44").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            // $("#potongan_sppb_"+index+'_'+sub_index).val(val4.toLocaleString('id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "wapu_manual_sppb_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#manual_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = parseFloat($("#nominal_manual_sppb_" + index + '_' + sub_index)
                                .val());
                            let val13 = val2 / 100;
                            let val3 = val1 * val13; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else {
                        //alert("wapu manual");
                        $('#manual_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                    }
                });
            } else if (pajak == "waba_sppb_" + index + '_' + sub_index) {
                //alert("waba");
                $('#waba_sppb_' + index + '_' + sub_index).show();

                $('#wapu_pph_sppb_' + index + '_' + sub_index).hide();
                $('#pph_sppb_' + index + '_' + sub_index).hide();

                $('#manual_sppb_' + index + '_' + sub_index).hide();
                $('#dpp_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_potongan_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_akhir_sppb_' + index + '_' + sub_index).hide();
                $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                $('#normal_sppb_' + index + '_' + sub_index).hide();
                $('#pph21_sppb_' + index + '_' + sub_index).hide();
                $('#pph22_sppb_' + index + '_' + sub_index).hide();
                $('#pph23_sppb_' + index + '_' + sub_index).hide();
                $('#pph26_sppb_' + index + '_' + sub_index).hide();
                $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();

                $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_pph_sppb_' + index + '_' + sub_index).hide();
                $('#normal_sppb_' + index + '_' + sub_index).hide();
                $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();

                $('#pilih_waba_sppb_' + index + '_' + sub_index).change(function() {
                    if ($(this).val() == "waba_normal_sppb_" + index + '_' + sub_index) {
                        //alert("wapu normal");
                        $('#normal_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph22").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            // $("#potongan_sppb_"+index+'_'+sub_index).val(val4.toLocaleString('id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "waba_pph21_a_sppb_" + index + '_' + sub_index) {
                        //alert("wapu pph21");
                        $('#pph21_a_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph21a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "waba_pph21_b_sppb_" + index + '_' + sub_index) {
                        //alert("wapu pph21");
                        $('#pph21_b_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph21b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "waba_pph21_c_sppb_" + index + '_' + sub_index) {
                        //alert("wapu pph21");
                        $('#pph21_c_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph21c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "waba_pph22_a_sppb_" + index + '_' + sub_index) {
                        //alert("wapu pph22");
                        $('#pph22_a_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();

                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph22").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "waba_pph23_a_sppb_" + index + '_' + sub_index) {
                        //alert("wapu pph23");
                        $('#pph23_a_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph23a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "waba_pph23_b_sppb_" + index + '_' + sub_index) {
                        // alert("wapu pph23");
                        $('#pph23_b_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph23b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "waba_pph23_c_sppb_" + index + '_' + sub_index) {
                        // alert("wapu pph23");
                        $('#pph23_c_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph23c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "waba_pph26_a_sppb_" + index + '_' + sub_index) {
                        //alert("wapu pph26");
                        $('#pph26_a_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph26a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "waba_pph26_b_sppb_" + index + '_' + sub_index) {
                        //alert("wapu pph26");
                        $('#pph26_b_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph26b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "waba_pph26_c_sppb_" + index + '_' + sub_index) {
                        //alert("wapu pph26");
                        $('#pph26_c_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph26c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "waba_pasal4ayat2_sppb_" + index + '_' + sub_index) {
                        //alert("wapu pasal 4");
                        $('#pasal4_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pasal4").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "waba_nilai_lain_sppb_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph22").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            // $("#potongan_sppb_"+index+'_'+sub_index).val(val4.toLocaleString('id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "waba_manual_sppb_" + index + '_' + sub_index) {

                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#manual_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = parseFloat($("#nominal_manual_sppb_" + index + '_' + sub_index)
                                .val());
                            let val13 = val2 / 100;
                            let val3 = val1 * val13; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppb_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppb_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else {
                        //alert("wapu manual");
                        $('#manual_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                    }
                });
            } else if (pajak == "pph_sppb_" + index + '_' + sub_index) {
                //alert("pph");
                $('#pph_sppb_' + index + '_' + sub_index).show();

                $('#waba_sppb_' + index + '_' + sub_index).hide();
                $('#wapu_pph_sppb_' + index + '_' + sub_index).hide();

                $('#manual_sppb_' + index + '_' + sub_index).hide();
                $('#dpp_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_potongan_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_akhir_sppb_' + index + '_' + sub_index).hide();
                $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                $('#normal_sppb_' + index + '_' + sub_index).hide();
                $('#pph21_sppb_' + index + '_' + sub_index).hide();
                $('#pph22_sppb_' + index + '_' + sub_index).hide();
                $('#pph23_sppb_' + index + '_' + sub_index).hide();
                $('#pph26_sppb_' + index + '_' + sub_index).hide();
                $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();

                $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_pph_sppb_' + index + '_' + sub_index).hide();
                $('#normal_sppb_' + index + '_' + sub_index).hide();
                $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();


                $('#pilih_pph_sppb_' + index + '_' + sub_index).change(function() {
                    if ($(this).val() == "pph21_a_sppb_" + index + '_' + sub_index) {
                        //alert("pph21");
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).show();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph21a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppb_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppb_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph21_b_sppb_" + index + '_' + sub_index) {
                        //alert("pph22");
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).show();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph21b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppb_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppb_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph21_c_sppb_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).show();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph21c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppb_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppb_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })

                    } else if ($(this).val() == "pph22_a_sppb_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).show();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph22").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppb_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppb_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph23_a_sppb_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).show();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph23a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppb_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppb_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph23_b_sppb_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).show();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph23b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppb_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppb_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph23_c_sppb_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).show();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph23c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppb_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppb_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph26_a_sppb_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).show();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph26a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppb_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppb_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph26_b_sppb_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).show();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph26b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppb_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppb_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph26_c_sppb_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).show();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pph26c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppb_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppb_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pphpasal4_ayat2_sppb_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#manual_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).show();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = document.getElementById("pasal4").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppb_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppb_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph_manual_sppb_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        $('#manual_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                        $('#normal_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
                        $("#nominal_sppb_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppb = $("#nominal_sppb_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppb_" + index + '_' + sub_index).val(jumlah_sppb);
                            let val1 = parseFloat(jumlah_sppb); //100jt
                            let val2 = parseFloat($("#nominal_manual_sppb_" + index + '_' + sub_index)
                                .val());
                            let val13 = val2 / 100;
                            let val3 = val1 * val13; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppb_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppb_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppb_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppb_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else {
                        //alert("pasal 4");
                        $('#dpp_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppb_' + index + '_' + sub_index).show();
                        //$('#dpp_ppn_sppb_'+index+'_'+sub_index).show();
                        $('#pasal4_sppb_' + index + '_' + sub_index).show();
                        $('#nominal_sppb_' + index + '_' + sub_index).val('');
                        $("#potongan_sppb_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                        $("#ppn_sppb_" + index + '_' + sub_index).val('');
                        $("#akhir_sppb_" + index + '_' + sub_index).val('');

                        $('#pph21_sppb_' + index + '_' + sub_index).hide();
                        $('#pph22_sppb_' + index + '_' + sub_index).hide();
                        $('#pph23_sppb_' + index + '_' + sub_index).hide();
                        $('#pph26_sppb_' + index + '_' + sub_index).hide();
                    }

                });

            } else {
                //Tanpa Pajak
                $('#dpp_sppb_' + index + '_' + sub_index).show();
                $('#nominal_potongan_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_akhir_sppb_' + index + '_' + sub_index).hide();
                $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                $('#manual_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_sppb_' + index + '_' + sub_index).val('');
                $("#potongan_sppb_" + index + '_' + sub_index).val('');
                $("#dppppn_sppb_" + index + '_' + sub_index).val('');
                $("#ppn_sppb_" + index + '_' + sub_index).val('');
                $("#akhir_sppb_" + index + '_' + sub_index).val('');

                $('#wapu_pph_sppb_' + index + '_' + sub_index).hide();
                $('#waba_sppb_' + index + '_' + sub_index).hide();
                $('#pph_sppb_' + index + '_' + sub_index).hide();

                $('#dpp_ppn_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_ppn_sppb_' + index + '_' + sub_index).hide();
                $('#nominal_pph_sppb_' + index + '_' + sub_index).hide();
                $('#normal_sppb_' + index + '_' + sub_index).hide();
                $('#pph21_a_sppb_' + index + '_' + sub_index).hide();
                $('#pph21_b_sppb_' + index + '_' + sub_index).hide();
                $('#pph21_c_sppb_' + index + '_' + sub_index).hide();
                $('#pph22_a_sppb_' + index + '_' + sub_index).hide();
                $('#pph23_a_sppb_' + index + '_' + sub_index).hide();
                $('#pph23_b_sppb_' + index + '_' + sub_index).hide();
                $('#pph23_c_sppb_' + index + '_' + sub_index).hide();
                $('#pph26_a_sppb_' + index + '_' + sub_index).hide();
                $('#pph26_b_sppb_' + index + '_' + sub_index).hide();
                $('#pph26_c_sppb_' + index + '_' + sub_index).hide();
                $('#pasal4_sppb_' + index + '_' + sub_index).hide();
                $('#dpp_nilai_lain_sppb_' + index + '_' + sub_index).hide();
            }
        }

        function jenis_pajak_sppn(index, sub_index) {
            console.log("sppn_" + sub_index);
            console.log("sppn_" + index);

            //var nominall = $('.nominal_pajak_'+index+'_'+sub_index).val();
            // var jenis_spp = $('#isi_karyawan').val();
            // if(jenis_spp == "karyawan")
            // {
            // 	$('.form-pajak_'+index+'_'+sub_index).hide();
            // 	document.getElementById('pajak_'+index+'_'+sub_index).className = "form-control";
            // }
            // else
            // {
            // 	$('.form-pajak_'+index+'_'+sub_index).show();
            // }

            var pajak = $('#pilih_pajak_sppn_' + index + '_' + sub_index).val();
            //alert(pajak);
            console.log(pajak);

            if (pajak == "wapu_sppn_" + index + '_' + sub_index) {
                $('#wapu_pph_sppn_' + index + '_' + sub_index).show();

                $('#pph_sppn_' + index + '_' + sub_index).hide();
                $('#waba_sppn_' + index + '_' + sub_index).hide();

                $('#manual_sppn_' + index + '_' + sub_index).hide();
                $('#dpp_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_potongan_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_akhir_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                $('#normal_sppn_' + index + '_' + sub_index).hide();
                $('#pph21_sppn_' + index + '_' + sub_index).hide();
                $('#pph22_sppn_' + index + '_' + sub_index).hide();
                $('#pph23_sppn_' + index + '_' + sub_index).hide();
                $('#pph26_sppn_' + index + '_' + sub_index).hide();
                $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();

                $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_pph_sppn_' + index + '_' + sub_index).hide();
                $('#normal_sppn_' + index + '_' + sub_index).hide();
                $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();

                $('#pilih_wapu_sppn_' + index + '_' + sub_index).change(function() {
                    if ($(this).val() == "wapu_normal_sppn_" + index + '_' + sub_index) {
                        //alert("wapu normal");
                        $('#normal_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph22").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            // $("#potongan_sppn_"+index+'_'+sub_index).val(val4.toLocaleString('id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "wapu_pph21_a_sppn_" + index + '_' + sub_index) {
                        //alert("wapu pph21");
                        $('#pph21_a_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph21a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "wapu_pph21_b_sppn_" + index + '_' + sub_index) {
                        //alert("wapu pph22");
                        $('#pph21_b_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph21b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "wapu_pph21_c_sppn_" + index + '_' + sub_index) {
                        //alert("wapu pph23");
                        $('#pph21_c_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph21c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "wapu_pph22_a_sppn_" + index + '_' + sub_index) {
                        //alert("wapu pph26");
                        $('#pph22_a_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();

                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph22").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "wapu_pph23_a_sppn_" + index + '_' + sub_index) {
                        //alert("wapu pasal 4");
                        $('#pph23_a_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph23a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "wapu_pph23_b_sppn_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#pph23_b_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph23b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "wapu_pph23_c_sppn_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#pph23_c_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph23c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "wapu_pph26_a_sppn_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#pph26_a_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph26a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "wapu_pph26_b_sppn_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#pph26_b_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph26b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "wapu_pph26_b_sppn_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#pph26_b_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph26b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "wapu_pph26_c_sppn_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#pph26_c_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph26c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "wapu_pasal4ayat2_sppn_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#pasal4_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph26b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "wapu_nilai_lain_sppn_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph22").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100));; //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            // $("#potongan_sppn_"+index+'_'+sub_index).val(val4.toLocaleString('id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "wapu_manual_sppn_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#manual_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = parseFloat($("#nominal_manual_sppn_" + index + '_' + sub_index)
                                .val());
                            let val13 = val2 / 100;
                            let val3 = val1 * val13; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val10;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val10.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else {
                        //alert("wapu manual");
                        $('#manual_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                    }
                });
            } else if (pajak == "waba_sppn_" + index + '_' + sub_index) {
                //alert("waba");
                $('#waba_sppn_' + index + '_' + sub_index).show();

                $('#wapu_pph_sppn_' + index + '_' + sub_index).hide();
                $('#pph_sppn_' + index + '_' + sub_index).hide();

                $('#manual_sppn_' + index + '_' + sub_index).hide();
                $('#dpp_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_potongan_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_akhir_sppn_' + index + '_' + sub_index).hide();
                $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                $('#normal_sppn_' + index + '_' + sub_index).hide();
                $('#pph21_sppn_' + index + '_' + sub_index).hide();
                $('#pph22_sppn_' + index + '_' + sub_index).hide();
                $('#pph23_sppn_' + index + '_' + sub_index).hide();
                $('#pph26_sppn_' + index + '_' + sub_index).hide();
                $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();

                $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_pph_sppn_' + index + '_' + sub_index).hide();
                $('#normal_sppn_' + index + '_' + sub_index).hide();
                $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();

                $('#pilih_waba_sppn_' + index + '_' + sub_index).change(function() {
                    if ($(this).val() == "waba_normal_sppn_" + index + '_' + sub_index) {
                        //alert("wapu normal");
                        $('#normal_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph22").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            // $("#potongan_sppn_"+index+'_'+sub_index).val(val4.toLocaleString('id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "waba_pph21_a_sppn_" + index + '_' + sub_index) {
                        //alert("wapu pph21");
                        $('#pph21_a_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph21a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "waba_pph21_b_sppn_" + index + '_' + sub_index) {
                        //alert("wapu pph22");
                        $('#pph21_b_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph21b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "waba_pph21_c_sppn_" + index + '_' + sub_index) {
                        //alert("wapu pph23");
                        $('#pph21_c_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph21c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "waba_pph22_a_sppn_" + index + '_' + sub_index) {
                        //alert("wapu pph26");
                        $('#pph22_a_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();

                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph22").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "waba_pph23_a_sppn_" + index + '_' + sub_index) {
                        //alert("wapu pasal 4");
                        $('#pph23_a_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph23a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "waba_pph23_b_sppn_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#pph23_b_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph23b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "waba_pph23_c_sppn_" + index + '_' + sub_index) {
                        // alert("wapu pph23");
                        $('#pph23_c_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph23c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "waba_pph26_a_sppn_" + index + '_' + sub_index) {
                        //alert("wapu pph26");
                        $('#pph26_a_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph26a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "waba_pph26_b_sppn_" + index + '_' + sub_index) {
                        //alert("wapu pph26");
                        $('#pph26_b_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph26b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "waba_pph26_c_sppn_" + index + '_' + sub_index) {
                        //alert("wapu pph26");
                        $('#pph26_c_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph26c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "waba_pasal4ayat2_sppn_" + index + '_' + sub_index) {
                        //alert("wapu pasal 4");
                        $('#pasal4_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph26b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));
                        })
                    } else if ($(this).val() == "waba_nilai_lain_sppn_" + index + '_' + sub_index) {
                        //alert("wapu lain");
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph22").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            // $("#potongan_sppn_"+index+'_'+sub_index).val(val4.toLocaleString('id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "waba_manual_sppn_" + index + '_' + sub_index) {

                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#manual_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = parseFloat($("#nominal_manual_sppn_" + index + '_' + sub_index)
                                .val());
                            let val13 = val2 / 100;
                            let val3 = val1 * val13; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val8 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#dppppn_sppn_" + index + '_' + sub_index).val(val8.toLocaleString('id-ID'));
                            $("#ppn_sppn_" + index + '_' + sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else {
                        // alert("wapu manual");
                        $('#manual_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                    }
                });
            } else if (pajak == "pph_sppn_" + index + '_' + sub_index) {
                //alert("pph");
                $('#pph_sppn_' + index + '_' + sub_index).show();

                $('#waba_sppn_' + index + '_' + sub_index).hide();
                $('#wapu_pph_sppn_' + index + '_' + sub_index).hide();

                $('#manual_sppn_' + index + '_' + sub_index).hide();
                $('#dpp_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_potongan_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_akhir_sppn_' + index + '_' + sub_index).hide();
                $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                $('#normal_sppn_' + index + '_' + sub_index).hide();
                $('#pph21_sppn_' + index + '_' + sub_index).hide();
                $('#pph22_sppn_' + index + '_' + sub_index).hide();
                $('#pph23_sppn_' + index + '_' + sub_index).hide();
                $('#pph26_sppn_' + index + '_' + sub_index).hide();
                $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();

                $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_pph_sppn_' + index + '_' + sub_index).hide();
                $('#normal_sppn_' + index + '_' + sub_index).hide();
                $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();


                $('#pilih_pph_sppn_' + index + '_' + sub_index).change(function() {
                    if ($(this).val() == "pph21_a_sppn_" + index + '_' + sub_index) {
                        //alert("pph21");
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).show();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph21a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppn_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppn_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph21_b_sppn_" + index + '_' + sub_index) {
                        //alert("pph22");
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).show();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph21b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppn_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppn_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph21_c_sppn_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).show();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph21c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppn_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppn_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })

                    } else if ($(this).val() == "pph22_a_sppn_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).show();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph22").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppn_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppn_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph23_a_sppn_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).show();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph23a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppn_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppn_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph23_b_sppn_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).show();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph23b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppn_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppn_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph23_c_sppn_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).show();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph23c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppn_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppn_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph26_a_sppn_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).show();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph26a").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppn_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppn_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph26_b_sppn_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).show();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph26b").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppn_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppn_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph26_c_sppn_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).val('');
                        $("#uraian_pph_sppn_" + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).show();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pph26c").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppn_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppn_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pphpasal4_ayat2_sppn_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#manual_sppn_' + index + '_' + sub_index).hide();
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).show();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = document.getElementById("pasal4").value; //2%
                            let val3 = val1 * val2; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (1.1 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppn_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppn_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else if ($(this).val() == "pph_manual_sppn_" + index + '_' + sub_index) {
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        $('#manual_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_pph_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $('#uraian_pph_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                        $('#normal_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                        $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                        $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
                        $("#nominal_sppn_" + index + '_' + sub_index).on("keyup", function() {
                            var jumlah_sppn = $("#nominal_sppn_" + index + '_' + sub_index).val().replace(
                                /\./g, "");
                            $("#tidak_nominal_sppn_" + index + '_' + sub_index).val(jumlah_sppn);
                            let val1 = parseFloat(jumlah_sppn); //100jt
                            let val2 = parseFloat($("#nominal_manual_sppn_" + index + '_' + sub_index)
                                .val());
                            let val13 = val2 / 100;
                            let val3 = val1 * val13; // 2jt
                            let val4 = Math.floor(val3); //2jt
                            let val5 = val3 + val1;
                            let val6 = Math.round(val5);
                            let val7 = Math.floor(val1 * (11 / 100)); //11jt
                            let val8 = val7 + val1; //11jt+100jt=111jt
                            let val10 = val7 + val4;
                            let val11 = val1 + val7;
                            let val12 = val1 - val4;
                            $("#potongan_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            $("#uraian_pph_sppn_" + index + '_' + sub_index).val(val4.toLocaleString(
                                'id-ID'));
                            // $("#dppppn_sppn_"+index+'_'+sub_index).val(val8.toLocaleString('id-ID'));
                            // $("#ppn_sppn_"+index+'_'+sub_index).val(val7.toLocaleString('id-ID'));
                            $("#akhir_sppn_" + index + '_' + sub_index).val(val12.toLocaleString('id-ID'));

                        })
                    } else {
                        // alert("pasal 4");
                        $('#dpp_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_potongan_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_akhir_sppn_' + index + '_' + sub_index).show();
                        //$('#dpp_ppn_sppn_'+index+'_'+sub_index).show();
                        $('#pasal4_sppn_' + index + '_' + sub_index).show();
                        $('#nominal_sppn_' + index + '_' + sub_index).val('');
                        $("#potongan_sppn_" + index + '_' + sub_index).val('');
                        $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                        $("#ppn_sppn_" + index + '_' + sub_index).val('');
                        $("#akhir_sppn_" + index + '_' + sub_index).val('');

                        $('#pph21_sppn_' + index + '_' + sub_index).hide();
                        $('#pph22_sppn_' + index + '_' + sub_index).hide();
                        $('#pph23_sppn_' + index + '_' + sub_index).hide();
                        $('#pph26_sppn_' + index + '_' + sub_index).hide();
                    }

                });

            } else {
                //alert("manual");
                $('#dpp_sppn_' + index + '_' + sub_index).show();
                $('#nominal_potongan_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_akhir_sppn_' + index + '_' + sub_index).hide();
                $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                $('#manual_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_sppn_' + index + '_' + sub_index).val('');
                $("#potongan_sppn_" + index + '_' + sub_index).val('');
                $("#dppppn_sppn_" + index + '_' + sub_index).val('');
                $("#ppn_sppn_" + index + '_' + sub_index).val('');
                $("#akhir_sppn_" + index + '_' + sub_index).val('');

                $('#wapu_pph_sppn_' + index + '_' + sub_index).hide();
                $('#waba_sppn_' + index + '_' + sub_index).hide();
                $('#pph_sppn_' + index + '_' + sub_index).hide();

                $('#dpp_ppn_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_ppn_sppn_' + index + '_' + sub_index).hide();
                $('#nominal_pph_sppn_' + index + '_' + sub_index).hide();
                $('#normal_sppn_' + index + '_' + sub_index).hide();
                $('#pph21_a_sppn_' + index + '_' + sub_index).hide();
                $('#pph21_b_sppn_' + index + '_' + sub_index).hide();
                $('#pph21_c_sppn_' + index + '_' + sub_index).hide();
                $('#pph22_a_sppn_' + index + '_' + sub_index).hide();
                $('#pph23_a_sppn_' + index + '_' + sub_index).hide();
                $('#pph23_b_sppn_' + index + '_' + sub_index).hide();
                $('#pph23_c_sppn_' + index + '_' + sub_index).hide();
                $('#pph26_a_sppn_' + index + '_' + sub_index).hide();
                $('#pph26_b_sppn_' + index + '_' + sub_index).hide();
                $('#pph26_c_sppn_' + index + '_' + sub_index).hide();
                $('#pasal4_sppn_' + index + '_' + sub_index).hide();
                $('#dpp_nilai_lain_sppn_' + index + '_' + sub_index).hide();
            }
        }



        $('#jenis_spp').change(function(event) {

            if ($(this).val() == 'karyawan') {
                $('.formInputPajakSppb').hide();

                document.getElementById("kwitansi_sppb").className = "form-control";
                document.getElementById("kwitansi_spp").className = "form-control";
                document.getElementById("kwitansi_sppn").className = "form-control";
                document.getElementById("au58_sppn").className = "form-control";
                document.getElementById("label_kwitansi_sppb").innerHTML = "Kwitansi dan/atau Invoice";
                document.getElementById("label_kwitansi_spp").innerHTML = "Kwitansi dan/atau Invoice";
                document.getElementById("label_kwitansi_sppn").innerHTML = "Kwitansi dan/atau Invoice";
                document.getElementById("label_au58_sppn").innerHTML = "BA/AU.58";
                document.getElementById("label_berita_acara_opsional").innerHTML = "BA/dokumen pendukung lain";
                document.getElementById("label_dokumen_pendukung_sppb").innerHTML = "Upload Dokumen Pendukung";
                document.getElementById("label_dokumen_pendukung_sppn").innerHTML = "Upload Dokumen Pendukung";
                document.getElementById("berita_acara_sppb").className = "form-control";
                document.getElementById("sp_opl_sppb").className = "form-control";
                document.getElementById("kwitansi_sppb").style.cssText =
                    "border-width:2px;border-color:light-grey;border-style:solid;border-radius:1px;";
                document.getElementById("kwitansi_sppn").style.cssText =
                    "border-width:2px;border-color:light-grey;border-style:solid;border-radius:1px;";
                document.getElementById("au58_sppn").style.cssText =
                    "border-width:2px;border-color:light-grey;border-style:solid;border-radius:1px;";
                document.getElementById("kontrak_perjanjian_sppb").className = "file";
                document.getElementById("invoice_sppb").className = "file";
                document.getElementById("efaktur_sppb").className = "file";
                document.getElementById("berita_acara_file_sppb").className = "file";
                document.getElementById("dokumen_pendukung_sppb").className = "file";
                document.getElementById("dokumen_pendukung_sppn").className = "file";

                //$("#nominal_sppn_1_1").removeClass("validate_sppn");
                $('#panel_jenis_form').show();
                $('#panel_sumber_dana').show();
                $('#panel_dokumen_pendukung').hide();
                $('#atas_nama_karyawan_sppb').show();
                $('#atas_nama_vendor_sppb').hide();
                $('#alamat_vendor_sppb').hide();
                $('#atas_nama_karyawan_sppn').show();
                $('#atas_nama_vendor_sppn').hide();
                $('#kas_sppn').hide();
                $('#kas_sppb').hide();
                $('#jenis_form').change(function(event) {
                    if ($(this).val() == 'sppn') {
                        $('.formInputPajakSppn').show();
                    } else if ($(this).val() == 'sppb_sppn') {
                        $('.formInputPajakSppn').show();
                        $('#dpp_sppb_1_1').show();
                    } else if ($(this).val() == 'sppb') {
                        $('.formInputPajakSppb').hide();
                        $('#dpp_sppb_1_1').show();
                    }
                });

            } else if ($(this).val() == 'vendor') {
                kind_of_spp = 'vendor';
                $('.formInputPajakSppb').show();
                $('.formInputPajakSppn').show();
                //document.getElementById("pajak_1_1").className = "form-control validate_sppb validate_spp_all";
                document.getElementById("kwitansi_sppb").className = "form-control validate_sppb validate_spp_all";
                document.getElementById("kwitansi_spp").className = "form-control validate_spp_all";

                document.getElementById("kwitansi_sppn").className = "form-control validate_sppn validate_spp_all";
                document.getElementById("au58_sppn").className = "form-control validate_sppn validate_spp_all";
                document.getElementById("label_kwitansi_sppb").innerHTML = "Kwitansi dan/atau Invoice *";
                document.getElementById("label_kwitansi_sppn").innerHTML = "Kwitansi dan/atau Invoice *";
                document.getElementById("label_kwitansi_sppb").innerHTML = "Kwitansi dan/atau Invoice *";
                document.getElementById("label_au58_sppn").innerHTML = "BA/AU.58 *";
                document.getElementById("label_berita_acara_opsional").innerHTML = "BA/dokumen pendukung lain *";
                document.getElementById("label_dokumen_pendukung_sppb").innerHTML = "Upload Dokumen Pendukung *";
                document.getElementById("label_dokumen_pendukung_sppn").innerHTML = "Upload Dokumen Pendukung *";
                document.getElementById("berita_acara_sppb").className =
                    "form-control  validate_sppb validate_spp_all";
                document.getElementById("dokumen_pendukung_sppb").className =
                    "form-control  validate_sppb validate_spp_all";
                document.getElementById("dokumen_pendukung_sppn").className =
                    "form-control  validate_sppn validate_spp_all";
                document.getElementById("sp_opl_sppb").className = "form-control  validate_sppb validate_spp_all";

                //document.getElementById("kontrak_perjanjian_sppb").className = "file validate_file_sppb validate_spp_all";
                //document.getElementById("invoice_sppb").className = "file validate_file_sppb validate_spp_all";
                //document.getElementById("efaktur_sppb").className = "file ";
                //document.getElementById("berita_acara_file_sppb").className = "file validate_file_sppb validate_spp_all";
                //document.getElementsByClassName("validasi_sppb").className = "validate_sppb validate_spp_all";
                //$(".validasi_sppb").addClass("validate_sppb validate_spp_all");

                $('#panel_jenis_form').show();
                $('#panel_sumber_dana').show();
                $('#panel_dokumen_pendukung').show();
                $('#alamat_vendor_sppb').show();
                $('#atas_nama_vendor_sppb').show();
                $('#atas_nama_karyawan_sppb').hide();
                $('#atas_nama_vendor_sppn').show();
                $('#atas_nama_karyawan_sppn').hide();
                $('#kas_sppn').hide();
                $('#kas_sppb').hide();
                $('#dpp_sppb_1_1').hide();
                var inputs = document.getElementsByClassName("validate_file_sppb");
                for (var a = 0; a < inputs.length; a++) {
                    inputs[a].addEventListener("change", validatefile);
                }
            }

            // $( "#jumlah_sppb_1_1").on('keyup',function(e) {
            // 	console.log("First - #jumlah_sppb_"+'1'+"_"+'1');
            // 	jum_nom[1] = [];
            // 	jum_nom[1][1] = this.value.replace(/[^\d,]/g, "");
            // 	var jum_nom_total = 0;
            // 	for (let i = 1; i <= sub_index_sppb[1]; i++) {
            // 		var jum_nom_value = $("#jumlah_sppb_1_"+i) ? $("#jumlah_sppb_1_"+i).val().replace(/[^\d,]/g, "") : 0;
            // 		jum_nom_total = jum_nom_value ? parseInt(jum_nom_total) + parseInt(jum_nom_value) : jum_nom_total;
            // 		console.log(jum_nom_value);
            // 		console.log(jum_nom_total);
            // 	}
            // 	if (jum_nom_total > sisa[1]) {
            // 		$('.cek_dana_gagal_1_1').css('display','block');
            // 		$('.cek_dana_berhasil_1_1').css('display','none');
            // 		// $("#simpan").prop("disabled", true);
            // 	}else{
            // 		$('.cek_dana_berhasil_1_1').css('display','block');
            // 		$('.cek_dana_gagal_1_1').css('display','none');
            // 		// $("#simpan").prop("disabled", false);

            // 	}
            // });
            // $( "#jumlah_sppn_1_1").on('keyup',function(e) {
            // 	console.log("First - #jumlah_sppb_"+'1'+"_"+'1');
            // 	jum_nom[1] = [];
            // 	jum_nom[1][1] = this.value.replace(/[^\d,]/g, "");
            // 	var jum_nom_total = 0;
            // 	for (let i = 1; i <= sub_index_sppn[1]; i++) {
            // 		var jum_nom_value = $("#jumlah_sppn_1_"+i) ? $("#jumlah_sppn_1_"+i).val().replace(/[^\d,]/g, "") : 0;
            // 		jum_nom_total = jum_nom_value ? parseInt(jum_nom_total) + parseInt(jum_nom_value) : jum_nom_total;
            // 		console.log(jum_nom_value);
            // 		console.log(jum_nom_total);
            // 	}
            // 	if (jum_nom_total > sisa[1]) {
            // 		$('.sppncek_dana_gagal_1_1').css('display','block');
            // 		$('.sppncek_dana_berhasil_1_1').css('display','none');
            // 		// $("#simpan").prop("disabled", true);
            // 	}else{
            // 		$('.sppncek_dana_berhasil_1_1').css('display','block');
            // 		// $("#simpan").prop("disabled", false);
            // 		$('.sppncek_dana_gagal_1_1').css('display','none');
            // 	}
            // });
        });

        // ini format inisialisasi ckeditor versi lama
        // $(document).ready(function() {
        //     CKEDITOR.inline('ckeditor_1_1');
        //     CKEDITOR.inline('ckeditors_1_1');

        //     var btn_simpan = document.getElementById("simpan");

        //     if (btn_simpan) {
        //         btn_simpan.addEventListener("click", validateForm);
        //     }

        // ini format inisialisasi ckeditor versi baru
        $(document).ready(function() {
            // Inisialisasi CKEditor 5 dalam mode inline
            InlineEditor.create(document.querySelector('#ckeditor_1_1'))
                .catch(error => {
                    console.error(error);
                });

            InlineEditor.create(document.querySelector('#ckeditors_1_1'))
                .catch(error => {
                    console.error(error);
                });

            // Menambahkan event listener untuk tombol simpan
            var btn_simpan = document.getElementById("simpan");

            if (btn_simpan) {
                btn_simpan.addEventListener("click", validateForm);
            }

        });
        $('#atas_nama_bank_sppb_2').change(function(event) {

        });

        $('#jenis_form').change(function(event) {
            if ($("#jenis_form").val() == 'sppb') {
                var inputs = document.getElementsByClassName("validate_sppb");
                //console.log(inputs);
                for (var i in CKEDITOR.instances) {
                    if (i.substring(0, 9) == "ckeditor_") {
                        CKEDITOR.instances[i].on('change', function() {
                            var urai = document.getElementById(this.name).parentElement;
                            if (this.getData().replace(/<[^>]+>/g, '') == "") {
                                urai.style.cssText =
                                    "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                            } else {
                                urai.style.cssText =
                                    "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                            }
                        })
                    }
                }
            } else if ($("#jenis_form").val() == 'sppn') {
                var inputs = document.getElementsByClassName("validate_sppn");
                //console.log(inputs);
                for (var i in CKEDITOR.instances) {
                    if (i.substring(0, 9) == "ckeditors") {
                        CKEDITOR.instances[i].on('change', function() {
                            var urai = document.getElementById(this.name).parentElement;
                            if (this.getData().replace(/<[^>]+>/g, '') == "") {
                                urai.style.cssText =
                                    "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                            } else {
                                urai.style.cssText =
                                    "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                            }
                        })
                    }
                }
            } else {
                var inputs = document.getElementsByClassName("validate_spp_all");
                for (var i in CKEDITOR.instances) {
                    CKEDITOR.instances[i].on('change', function() {
                        var urai = document.getElementById(this.name).parentElement;
                        if (this.getData().replace(/<[^>]+>/g, '') == "") {
                            urai.style.cssText =
                                "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                        } else {
                            urai.style.cssText =
                                "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                        }
                    })
                }
            }


            if (inputs) {
                for (var i = 0; i < inputs.length; i++) {
                    inputs[i].addEventListener("change", validateInput);
                    inputs[i].addEventListener("keyup", validateInput);
                    inputs[i].addEventListener("focus", validateInput);

                }
            }
            var btn_simpan = document.getElementById("simpan");

            if (btn_simpan) {
                btn_simpan.addEventListener("click", validateForm);
            }
        });


        function hapus_karyawan_kas_sppb(index) {
            var a = index - 1;
            $('#atas_nama_karyawan_kas_' + index).remove();
            $('#btn_karyawan_kas_sppb_' + a).show();

        }

        function tambah_karyawan_kas_sppb_input(index) {
            index++;
            $('#kas_sppb_input').append(`<div class="form-group row" id="atas_nama_karyawan_kas_input_${index}">
													<label class="col-sm-2 col-form-label"></label>
													<div class="col-sm-8">
														<input type="text" class="form-control" id="karyawan_kas_sppb_input" name="karyawan_kas_sppb_input[${index}]" placeholder="Nama Karyawan ${index}">
													</div>
													<div class="col-sm-2" id="btn_karyawan_kas_sppb_input_${index}">
														<button type="button" class="btn btn-success btn-sm" onclick="tambah_karyawan_kas_sppb_input(${index})">+</button>
														<button type="button" class="btn btn-danger btn-sm" onclick="hapus_karyawan_kas_sppb_input(${index})">x</button>

													</div>
												</div>`);
            var a = index - 1;
            $('#btn_karyawan_kas_sppb_input_' + a).hide();

        }

        function hapus_karyawan_kas_sppb_input(index) {
            var a = index - 1;
            $('#atas_nama_karyawan_kas_input_' + index).remove();
            $('#alamat_karyawan_kas_input_' + index).remove();
            $('#btn_karyawan_kas_sppb_input_' + a).show();

        }



        // function hapus_karyawan_kas_sppn(index){
        // 	var a = index-1;
        // 	$('#atas_nama_karyawan_kas_'+index).remove();
        // 	$('#btn_karyawan_kas_sppn_'+a).show();

        // }

        function tambah_karyawan_bank_sppb(index) {
            index = index + 1;
            jumlah_bank_karyawan = jumlah_bank_karyawan + 1;
            $('#bank_sppb_karyawan_master').append(`<div id="bank_karyawan_sppb_${index}">
											<div class="form-group row" id="atas_nama_karyawan_bank_sppb_${index}">
													<label class="col-sm-2 col-form-label">Atas Nama Rekening ${index}*</label>
													<div class="col-sm-8">
														<input type="text" id="atas_nama_bank_sppb_karyawan_${index}"  onclick="bank_karyawan_sppb(${index})" name="karyawan_sppb[${index}][nama]" class="form-control"  placeholder="Atas Nama Bank SPPb" autocomplete="off">

													</div>
													<div class="col-sm-2" id="btn_karyawan_bank_sppb_${index}">
														<button type="button" class="btn btn-success btn-sm" id="btn_tambah_karyawan_bank_sppb_${index}" onclick="tambah_karyawan_bank_sppb(${index})">+</button>
														<button type="button" class="btn btn-danger btn-sm" id="btn_hapus_karyawan_bank_sppb_${index}" onclick="hapus_karyawan_bank_sppb(${index})">x</button>

													</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nama Bank ${index}*</label>
												<div class="col-sm-8">
												<input type="text" id="nama_bank_sppb_karyawan_${index}" name="karyawan_sppb[${index}][bank]" class="form-control" placeholder="Nama Bank SPPb ${index}" onclick="bank_karyawan_sppb(${index})" autocomplete="off">
												</div>

											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Nomor Rekening ${index}*</label>
												<div class="col-sm-8">
													<input type="text" id="rekening_bank_sppb_karyawan_${index}" name="karyawan_sppb[${index}][no_rek]" onclick="bank_karyawan_sppb(${index})" onclick="bank_karyawan_sppb(${index})" class="form-control"  placeholder="Nomor Rekening Bank SPPb ${index}" autocomplete="off">
												</div>
											</div>

												</div>
												</div>`);
            var a = index - 1;
            $('#btn_karyawan_bank_sppb_' + a).hide();

        }

        function hapus_karyawan_bank_sppb(index) {
            jumlah_bank_karyawan = jumlah_bank_karyawan - 1;
            var a = index - 1;
            $('#bank_karyawan_sppb_' + index).remove();
            $('#btn_karyawan_bank_sppb_' + a).show();

        }

        function tambah_karyawan_bank_sppb_input(index) {
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
            var a = index - 1;
            $('#btn_karyawan_bank_sppb_input_' + a).hide();

        }

        function hapus_karyawan_bank_sppb_input(index) {
            jumlah_bank_karyawan = jumlah_bank_karyawan - 1;
            var a = index - 1;
            $('#bank_karyawan_sppb_input_' + index).remove();
            $('#btn_karyawan_bank_sppb_input_' + a).show();
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

    // 									</div>`);
        // 	var a = index-1;
        // 	$('#btn_karyawan_bank_sppn_'+a).hide();

        // }
        // function hapus_karyawan_bank_sppn(index){
        // 	var a = index-1;
        // 	$('#bank_karyawan_sppn_'+index).remove();
        // 	$('#btn_karyawan_bank_sppn_'+a).show();

        // }
        function validateInput() {
            if (this.value == null || this.value == "") {
                this.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;";

            } else {
                this.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;";
            }
        }

        function validatefile() {
            var a = document.getElementById(this.name).parentElement.parentElement.parentElement;
            if (this.files[0]) {
                a.style.cssText = "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;";
            } else {
                a.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;";
            }
        }

        function validateForm(e) {
            var hasEmpty = false;

            if ($("#jenis_form").val() == 'sppb') {
                var inputs = document.getElementsByClassName("validate_sppb");
                //console.log(inputs);
                var slct_sppb = $('.selectpicker');
                //console.log(slct_sppb);

                for (var i = 0; i < slct_sppb.length; i++) {
                    //console.log(slct_sppb[i].value);
                    var a = slct_sppb[i].id;

                    if (slct_sppb[i].value == "" || slct_sppb[i].value == null) {
                        if (a.includes('sppb')) {
                            var p_slct_sppb = document.getElementById(slct_sppb[i].id).parentElement;
                            //console.log(p_slct_sppb);
                            p_slct_sppb.style.cssText =
                                "border-width:2px;border-color:red;border-style:solid;border-radius:1px;width:100%;";
                            hasEmpty = true;
                            console.log('ada yang kosong pada slct_sppb');
                        }

                    } else {
                        if (a.includes('sppb')) {
                            var p_slct_sppb = document.getElementById(slct_sppb[i].id).parentElement;
                            //console.log(p_slct_sppb);
                            p_slct_sppb.style.cssText =
                                "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;width:100%;";
                        }
                    }
                }
                if ($("#jenis_spp").val() == 'vendor') {
                    var fail = document.getElementsByClassName("validate_file_sppb");
                    for (var a = 0; a < fail.length; a++) {
                        var border = document.getElementById(fail[a].name).parentElement.parentElement.parentElement;
                        if (fail[a].files[0]) {
                            border.style.cssText =
                                "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;";
                        } else {
                            border.style.cssText =
                                "border-width:2px;border-color:red;border-style:solid;border-radius:1px;";
                            hasEmpty = true;

                            console.log('ada yang kosong pada fail');
                        }
                    }
                    var dokPendukungSppb = document.getElementById("dokumen_pendukung_sppb");
                    if (dokPendukungSppb.files.length == 0) {
                        dokPendukungSppb.parentElement.style.cssText =
                            "border-width:2px;border-color:red;border-style:solid;border-radius:1px;width:100%;";
                        hasEmpty = true;
                        console.log('ada yang kosong pada dokpendukungsppb');
                    } else {
                        dokPendukungSppb.parentElement.style.cssText =
                            "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;width:100%;";
                    }
                }
                for (var i in CKEDITOR.instances) {
                    if (i.substring(0, 9) == "ckeditor_") {
                        var urai = document.getElementById(CKEDITOR.instances[i].name).parentElement;
                        if (CKEDITOR.instances[i].getData().replace(/<[^>]+>/g, '') == "") {
                            urai.style.cssText =
                                "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                            hasEmpty = true;
                            console.log('ada yang kosong pada urai');
                        } else {
                            urai.style.cssText =
                                "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                        }
                    }
                }
            } else if ($("#jenis_form").val() == 'sppn') {
                var inputs = document.getElementsByClassName("validate_sppn");
                var slct_sppn = $('.selectpicker');
                //console.log(slct_sppn);

                for (var i = 0; i < slct_sppn.length; i++) {
                    //console.log(slct_sppn[i].value);
                    var a = slct_sppn[i].id;

                    if (slct_sppn[i].value == "" || slct_sppn[i].value == null) {
                        if (a.includes('sppn')) {
                            var p_slct_sppn = document.getElementById(slct_sppn[i].id).parentElement;
                            //console.log(p_slct_sppn);
                            p_slct_sppn.style.cssText =
                                "border-width:2px;border-color:red;border-style:solid;border-radius:1px;width:100%;";
                            hasEmpty = true;
                        }

                    } else {
                        if (a.includes('sppn')) {
                            var p_slct_sppn = document.getElementById(slct_sppn[i].id).parentElement;
                            //console.log(p_slct_sppn);
                            p_slct_sppn.style.cssText =
                                "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;width:100%;";
                        }
                    }
                }
                if ($("#jenis_spp").val() == 'vendor') {
                    var fail = document.getElementsByClassName("validate_file_sppn");
                    for (var a = 0; a < fail.length; a++) {
                        var border = document.getElementById(fail[a].name).parentElement.parentElement.parentElement;
                        if (fail[a].files[0]) {
                            border.style.cssText =
                                "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;";
                        } else {
                            border.style.cssText =
                                "border-width:2px;border-color:red;border-style:solid;border-radius:1px;";
                            hasEmpty = true;

                            console.log('ada yang kosong pada fail');
                        }
                    }
                    var dokPendukungSppn = document.getElementById("dokumen_pendukung_sppn");
                    if (dokPendukungSppn.files.length == 0) {
                        dokPendukungSppn.parentElement.style.cssText =
                            "border-width:2px;border-color:red;border-style:solid;border-radius:1px;width:100%;";
                        hasEmpty = true;
                        console.log('ada yang kosong pada dokpendukungsppn');
                    } else {
                        dokPendukungSppn.parentElement.style.cssText =
                            "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;width:100%;";
                    }
                }
                for (var i in CKEDITOR.instances) {
                    if (i.substring(0, 9) == "ckeditors") {
                        var urai = document.getElementById(CKEDITOR.instances[i].name).parentElement;
                        if (CKEDITOR.instances[i].getData().replace(/<[^>]+>/g, '') == "") {
                            urai.style.cssText =
                                "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                            hasEmpty = true;
                        } else {
                            urai.style.cssText =
                                "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                        }
                    }
                }
            } else if ($("#jenis_form").val() == 'sppb_sppn') {
                var slct_sppb = $('.selectpicker');
                //console.log(slct_sppb);

                for (var i = 0; i < slct_sppb.length; i++) {
                    //console.log(slct_sppb[i].value);
                    var a = slct_sppb[i].id;

                    if (slct_sppb[i].value == "" || slct_sppb[i].value == null) {
                        var p_slct_sppb = document.getElementById(slct_sppb[i].id).parentElement;
                        //console.log(p_slct_sppb);
                        p_slct_sppb.style.cssText =
                            "border-width:2px;border-color:red;border-style:solid;border-radius:1px;width:100%;";
                        hasEmpty = true;


                    } else {
                        var p_slct_sppb = document.getElementById(slct_sppb[i].id).parentElement;
                        //console.log(p_slct_sppb);
                        p_slct_sppb.style.cssText =
                            "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;width:100%;";

                    }
                }
                if ($("#jenis_spp").val() == 'vendor') {

                    var fail = document.getElementsByClassName("validate_file_sppb");
                    for (var a = 0; a < fail.length; a++) {
                        var border = document.getElementById(fail[a].name).parentElement.parentElement.parentElement;
                        if (fail[a].files[0]) {
                            border.style.cssText =
                                "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                        } else {
                            border.style.cssText =
                                "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                            hasEmpty = true;
                            // console.log(hasEmpty);
                        }
                    }
                    var dokPendukungSppb = document.getElementById("dokumen_pendukung_sppb");
                    if (dokPendukungSppb.files.length == 0) {
                        dokPendukungSppb.parentElement.style.cssText =
                            "border-width:2px;border-color:red;border-style:solid;border-radius:1px;width:100%;";
                        hasEmpty = true;
                        console.log('ada yang kosong pada dokpendukungsppb');
                    } else {
                        dokPendukungSppb.parentElement.style.cssText =
                            "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;width:100%;";
                    }
                    var dokPendukungSppn = document.getElementById("dokumen_pendukung_sppn");
                    if (dokPendukungSppn.files.length == 0) {
                        dokPendukungSppn.parentElement.style.cssText =
                            "border-width:2px;border-color:red;border-style:solid;border-radius:1px;width:100%;";
                        hasEmpty = true;
                        console.log('ada yang kosong pada dokpendukungsppn');
                    } else {
                        dokPendukungSppn.parentElement.style.cssText =
                            "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;width:100%;";
                    }
                }
                var inputs = document.getElementsByClassName("validate_spp_all");

                for (var i in CKEDITOR.instances) {
                    var urai = document.getElementById(CKEDITOR.instances[i].name).parentElement;
                    if (CKEDITOR.instances[i].getData().replace(/<[^>]+>/g, '') == "") {
                        urai.style.cssText =
                            "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                        hasEmpty = true;
                    } else {
                        urai.style.cssText =
                            "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                    }
                }
            }

            for (var i = 0; i < inputs.length; i++) {
                validateInput.call(inputs[i]);
                if (inputs[i].value == "" || inputs[i].value == null) {
                    // console.log(inputs[i]);
                    hasEmpty = true;
                }

            }


            if (hasEmpty == true) {
                // console.log(hasEmpty);
                e.preventDefault();
                Swal.fire("Form belum terisi dengan lengkap!", "", "warning");
            } else {

                e.preventDefault();
                simpan_spp.call();
            }
        }

        function refreshSelect2(whatFor, index = null, isSppb = false) {
            const select2Classes = {
                vendor: '.select2-vendor',
                gl: '.select2-gl',
                customer: '.select2-customer',
                cashflow: '.select2-cashflow',
                profit_center: '.select2-profitcenter',
                cost_center: '.select2-costcenter'
            };

            const placeholder = {
                vendor: '-- Pilih Kode Vendor --',
                gl: '-- Pilih Kode GL --',
                customer: '-- Pilih Kode Customer --',
                cashflow: '-- Pilih Cashflow --',
                profit_center: '-- Pilih Profit Center --',
                cost_center: '-- Pilih Profit Center --'
            };

            const routes = {
                vendor: '{{ route('mas_rek_t_v2') }}',
                gl: '{{ route('mas_gl_t_v2') }}',
                customer: '{{ route('master_customer') }}',
                cashflow: '{{ route('mas_cashflow') }}',
                profit_center: '{{ route('mas_profitcenter') }}',
                cost_center: '{{ route('mas_costcenter') }}'
            };

            var select2Class = select2Classes[whatFor];
            var route = routes[whatFor];
            if (!select2Class || !route) return;

            $(select2Class).select2({
                placeholder: placeholder, // Teks placeholder
                width: '100%', // Set lebar select2 sesuai dengan yang diinginkan
                ajax: {
                    url: route,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        console.log(whatFor);
                        return {
                            q: params.term, // Istilah pencarian
                            page: params.page || 1 // Halaman yang akan diminta
                        };
                    },
                    processResults: function(data) {
                        console.log(whatFor);
                        var mappedData = data.result.map(function(item) {
                            switch (whatFor) {
                                case 'vendor':
                                    return {
                                        id: item.master_rekening_id,
                                            text:
                                            `(${item.master_rekening_kode_sap}) ${item.master_rekening_keterangan}`
                                    };
                                case 'gl':
                                    return {
                                        id: item.master_gl_id,
                                            text:
                                            `(${item.master_gl_kode}) ${item.master_gl_keterangan}`,
                                    };
                                case 'customer':
                                    return {
                                        id: item.master_customer_id,
                                            text:
                                            `${item.master_customer_kode_sap} (${item.master_customer_nama})`,
                                    };
                                case 'cost_center':
                                    return {
                                        id: item.master_cost_center_id,
                                            text:
                                            `${item.master_cost_center_kode} - ${item.master_cost_center_keterangan}`,
                                    };
                                case 'profit_center':
                                    return {
                                        id: item.master_profit_center_id,
                                            text:
                                            `${item.master_profit_center_kode} - ${item.master_profit_unit}`,
                                    };
                                case 'cashflow':
                                    return {
                                        id: item.master_cash_flow_id,
                                            text:
                                            `${item.master_cash_flow_key} - ${item.master_cash_flow_keterangan}`,
                                    };
                                default:
                                    return {};
                            }
                        });
                        return {
                            results: mappedData,
                            pagination: {
                                more: data.incomplete_results
                            }
                        };
                    },
                    cache: true
                }
            });
        }

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
                // uploadUrl: '#',
                allowedFileTypes: ["image", "pdf"],
                browseClass: "btn btn-primary btn-block",
                showCaption: false,
                showRemove: false,
                showUpload: false,
                dropZoneTitle: "Drag & drop banyak file sekaligus disini..",
                fileActionSettings: {
                    showRemove: true,
                    showUpload: true,

                }
            });

            $('.nominal').mask('0.000.000.000.000.000.000.000', {
                reverse: true
            });

            $('#tanggal_sppb').change(function(event) {
                var tgl = $(this).val();
                $('#tanggal_sppn').val(tgl);
            });
            $('#tanggal_sppn').change(function(event) {
                var tgl = $(this).val();
                $('#tanggal_sppb').val(tgl);
            });

            $('#bagian_sppb').change(function(event) {
                var bagian = $(this).val();
                $('#bagian_sppn').val(bagian);
            });
            $('#bagian_sppn').change(function(event) {
                var bagian = $(this).val();
                $('#bagian_sppb').val(bagian);
            });
            $('#kwitansi_spp').change(function(event) {
                var kwitansi = $(this).val();
                $('#kwitansi_sppb').val(kwitansi);
                $('#kwitansi_sppn').val(kwitansi);
            });
            $('#referensi_spp').change(function(event) {
                var referensi = $(this).val();
                $('#referensi_sppb').val(referensi);
                $('#referensi_sppn').val(referensi);
            });
            $('#faktur_pajak_spp').change(function(event) {
                var faktur_pajak = $(this).val();
                $('#faktur_pajak_sppb').val(faktur_pajak);
                $('#faktur_pajak_sppn').val(faktur_pajak);
            });
            $('#sp_opl_spp').change(function(event) {
                var sp_opl = $(this).val();
                $('#sp_opl_sppb').val(sp_opl);
                $('#sp_opl_sppn').val(sp_opl);
            });
            $('#dokumen_pendukung_sppb').change(function(event) {
                var dokumen_pendukung_sppb = $(this).val();
                $('#dokumen_pendukung_sppb').val(dokumen_pendukung_sppb);
            });
            $('#dokumen_pendukung_sppn').change(function(event) {
                var dokumen_pendukung_sppn = $(this).val();
                $('#dokumen_pendukung_sppn').val(dokumen_pendukung_sppn);
            });

            $('')


            $('#jenis_form').change(function(event) {
                // $.ajaxSetup({
                //     headers: {
                //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //     }
                // });
                // $.ajax({
                //     type: 'POST',
                //     url: "{{ route('mas_rek_t') }}",
                //     success: function(data) {
                //         //console.log(data.rekening);

                //         $.each(data.rekening, function(no, value) {
                //             $("#kode_kbb_sppn_1").append('<option value="' + value
                //                 .master_rekening_id + '">' + value
                //                 .master_rekening_kode_kbb + '</option>');
                //         });
                //         $.each(data.rekening, function(no, value) {
                //             $("#sap_vendor_sppn_1").append('<option value="' + value
                //                 .master_rekening_id + '">' + '(' + value
                //                 .master_rekening_kode_sap + ')' + value
                //                 .master_rekening_keterangan + '</option>');
                //         });
                //         $.each(data.rekening, function(no, value) {
                //             $("#kode_kbb_sppb_1").append('<option value="' + value
                //                 .master_rekening_kode_kbb + '">' + value
                //                 .master_rekening_kode_kbb + '</option>');
                //         });
                //         $.each(data.rekening, function(no, value) {
                //             $("#sap_vendor_sppb_1").append('<option value="' + value
                //                 .master_rekening_id + '">' + '(' + value
                //                 .master_rekening_kode_sap + ')' + value
                //                 .master_rekening_keterangan + '</option>');
                //         });
                //         $('#kode_kbb_sppn_1').selectpicker('refresh');
                //         $('#sap_vendor_sppn_1').selectpicker('refresh');
                //         $('#kode_kbb_sppb_1').selectpicker('refresh');
                //         $('#sap_vendor_sppb_1').selectpicker('refresh');

                //     }
                // });

                if ($(this).val() == 'sppb') {
                    $('#form_kwitansi_sppb').show();
                    $('#form_referensi_sppb').show();
                    $('#form_faktur_pajak_sppb').show();
                    $('#form_sp_opl_sppb').show();
                    $('#form_kwitansi_sppn').show();
                    $('#form_referensi_sppn').show();
                    $('#form_faktur_pajak_sppn').show();
                    $('#form_sp_opl_sppn').show();
                    $('#panel_sppb').show();
                    $('#panel_sppn').hide();
                    $('#panel_sppb_sppn').hide();
                    $('#diterima_sppn_input').hide();
                    // $("#metode_pembayaran_sppn").attr('required',false);
                    $("#dokumen_pendukung_sppb").attr('required', true);
                    $("#dokumen_pendukung_sppn").attr('required', false);
                    $("#rekening_sppn_1").attr('required', false);
                    $("#jumlah_sppn_1_1").attr('required', false);
                    document.getElementById("nama_diterima_sppn_input").className = "form-control";
                    document.getElementById("alamat_diterima_sppn_input").className = "form-control";

                } else if ($(this).val() == 'sppn') {
                    $('#form_kwitansi_sppn').show();
                    $('#form_referensi_sppn').show();
                    $('#form_faktur_pajak_sppn').show();
                    $('#form_sp_opl_sppn').show();
                    $('#form_kwitansi_sppb').show();
                    $('#form_referensi_sppb').show();
                    $('#form_faktur_pajak_sppb').show();
                    $('#form_sp_opl_sppb').show();
                    $('#panel_sppb').hide();
                    $('#diterima_sppn_input').show();
                    $('#panel_sppn').show();
                    document.getElementById("nama_diterima_sppn_input").className =
                        "form-control validate_sppn validate_spp_all";
                    document.getElementById("alamat_diterima_sppn_input").className =
                        "form-control validate_sppn validate_spp_all";
                    $('#panel_sppb_sppn').hide();
                    $('#panel_dokumen_pendukung').show();
                } else {
                    $('#panel_sppb').show();
                    $('#panel_sppn').show();
                    $('#diterima_sppn_input').show();
                    $('#panel_sppb_sppn').show();
                    $('#form_kwitansi_sppb').hide();
                    $('#form_referensi_sppb').hide();
                    $('#form_faktur_pajak_sppb').hide();
                    $('#form_sp_opl_sppb').hide();
                    $('#form_kwitansi_sppn').hide();
                    $('#form_referensi_sppn').hide();
                    $('#form_faktur_pajak_sppn').hide();
                    $('#form_sp_opl_sppn').show();
                    //$('#dpp_sppb_1_1').show();
                    document.getElementById("nama_diterima_sppn_input").className =
                        "form-control validate_sppn validate_spp_all";
                    document.getElementById("alamat_diterima_sppn_input").className =
                        "form-control validate_sppn validate_spp_all";
                }
                $('#panel_submit').show();
            });

            // initializeSelectpicker($('#sap_vendor_sppb_1'));
            refreshSelect2('vendor');
            refreshSelect2('gl');
            refreshSelect2('customer');
            refreshSelect2('profit_center');
            refreshSelect2('cost_center');
            refreshSelect2('cashflow');
        });

        function simpan_spp() {
            // Kumpulkan data untuk cek anomali
            var jenisForm = $('#jenis_form').val();
            var tanggal = jenisForm == 'sppb' ? $('#tanggal_sppb').val() : 
                          jenisForm == 'sppn' ? $('#tanggal_sppn').val() : 
                          $('#tanggal_sppb').val();
            var bagianSppb = $('#bagian_sppb').val();
            var bagianSppn = $('#bagian_sppn').val();

            // Tampilkan loading
            Swal.fire({
                title: 'Mengecek urutan nomor...',
                text: 'Mohon tunggu, sedang memeriksa anomali urutan 2 minggu terakhir',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // Panggil endpoint cek anomali
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: "{{ route('checkUrutanAnomaly') }}",
                data: {
                    jenis_form: jenisForm,
                    tanggal: tanggal,
                    bagian_sppb: bagianSppb,
                    bagian_sppn: bagianSppn
                },
                success: function(response) {
                    Swal.close();

                    if (response.has_anomaly && response.warnings.length > 0) {
                        // Ada peringatan, tampilkan detail
                        var hasWarningOrDanger = response.warnings.some(function(w) {
                            return w.type === 'warning' || w.type === 'danger';
                        });

                        var warningHtml = '<div style="text-align:left; max-height:300px; overflow-y:auto;">';
                        response.warnings.forEach(function(w) {
                            var icon = w.type === 'danger' ? '🔴' : 
                                       w.type === 'warning' ? '🟡' : 'ℹ️';
                            var color = w.type === 'danger' ? '#dc3545' : 
                                        w.type === 'warning' ? '#ffc107' : '#17a2b8';
                            warningHtml += '<div style="padding:8px; margin:4px 0; border-left:4px solid ' + color + '; background:#f8f9fa; border-radius:4px; font-size:13px;">';
                            warningHtml += '<span>' + icon + ' ' + w.message + '</span>';
                            warningHtml += '</div>';
                        });
                        warningHtml += '</div>';

                        if (hasWarningOrDanger) {
                            // Ada warning/danger → tampilkan konfirmasi
                            Swal.fire({
                                title: '⚠️ Peringatan Anomali Urutan',
                                html: warningHtml + '<br><strong>Apakah anda tetap ingin menyimpan?</strong>',
                                icon: 'warning',
                                showDenyButton: true,
                                showCancelButton: true,
                                confirmButtonText: 'Simpan dan Cetak',
                                denyButtonText: 'Simpan Saja',
                                cancelButtonText: 'Batal',
                                confirmButtonColor: '#008000',
                                denyButtonColor: '#1E90FF',
                                width: '600px'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#form_spp').attr("target", "");
                                    $('#status_btn').val(1);
                                    document.getElementById("form_spp").submit();
                                } else if (result.isDenied) {
                                    $('#status_btn').val(0);
                                    document.getElementById("form_spp").submit();
                                }
                            });
                        } else {
                            // Hanya info, langsung lanjut simpan dengan dialog biasa
                            lanjutSimpan(warningHtml);
                        }
                    } else {
                        // Tidak ada anomali, langsung simpan
                        lanjutSimpan(null);
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    console.error('Error checking urutan anomaly:', xhr);
                    // Jika gagal cek, tetap izinkan simpan
                    lanjutSimpan(null);
                }
            });
        }

        function lanjutSimpan(infoHtml) {
            var htmlContent = '';
            if (infoHtml) {
                htmlContent = infoHtml + '<br>';
            }
            htmlContent += 'Apakah anda ingin menyimpan dan mencetak PP?';

            Swal.fire({
                title: infoHtml ? 'Info Urutan Nomor' : 'Apakah anda ingin menyimpan dan mencetak PP?',
                html: infoHtml ? htmlContent : undefined,
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: `Simpan dan Cetak`,
                denyButtonText: `Simpan Saja`,
                confirmButtonColor: '#008000',
                denyButtonColor: '#1E90FF',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#form_spp').attr("target", "");
                    $('#status_btn').val(1);
                    document.getElementById("form_spp").submit();
                } else if (result.isDenied) {
                    $('#status_btn').val(0);
                    document.getElementById("form_spp").submit();
                }
            })
        }

        $('#metode_pembayaran_sppb').change(function(event) {

            if ($('#jenis_spp').val() == 'karyawan') {
                $('#pilih_lampirkan_sppb_vendor').hide();

                // RADIO BUTTON TRANSFER
                if (this.value !== 'tidak_transfer') {
                    $('#karyawan_dari_master').show();
                    $('#catatan_tidak_transfer').hide();
                } else {
                    $('#karyawan_dari_master').hide();
                    $('#catatan_tidak_transfer').show();

                }

                $('input[type="radio"]').click(function() {
                    var inputValue = $(this).attr("value");
                    var targetBox = $("#" + inputValue);
                    $("#").not(targetBox).hide();
                    $(targetBox).show();
                });


                if ($('#metode_pembayaran_sppb').val() !== 'kas_negara') {
                    $('#pilih_lampirkan_sppb').show();
                    $('#kas_negara_sppb_input').hide();
                    document.getElementById("nama_kas_negara_sppb_input").className = "form-control";
                    document.getElementById("alamat_kas_negara_sppb_input").className = "form-control";
                    // console.log('ha');
                } else {
                    $('#pilih_lampirkan_sppb').hide();
                    $('#kas_negara_sppb_input').show();
                    document.getElementById("nama_kas_negara_sppb_input").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("alamat_kas_negara_sppb_input").className =
                        "form-control validate_sppb validate_spp_all";

                }
                pilih_data_sppb_karyawan.call();
            } else {
                $('#pilih_lampirkan_sppb').hide();

                // RADIO BUTTON TRANSFER
                if (this.value !== 'tidak_transfer') {
                    $('#vendor_dari_master').show();
                    $('#vendor_input_manual').show();
                    $('#catatan_tidak_transfer').hide();
                } else {
                    $('#vendor_dari_master').hide();
                    $('#vendor_input_manual').hide();
                    $('#catatan_tidak_transfer').show();
                }
                if ($('#metode_pembayaran_sppb').val() !== 'kas_negara' && $('#metode_pembayaran_sppb').val() !==
                    'kas') {
                    $('#pilih_lampirkan_sppb_vendor').show();
                    $('#kas_negara_sppb_input').hide();
                    document.getElementById("nama_kas_negara_sppb_input").className = "form-control";
                    document.getElementById("alamat_kas_negara_sppb_input").className = "form-control";
                    // console.log('ha');
                    // console.log('ha');
                } else {
                    $('#pilih_lampirkan_sppb_vendor').hide();
                    $('#kas_negara_sppb_input').show();
                    document.getElementById("nama_kas_negara_sppb_input").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("alamat_kas_negara_sppb_input").className =
                        "form-control validate_sppb validate_spp_all";

                }
                pilih_data_sppb_vendor.call();


            }
        });

        function pilih_data_sppb_karyawan() {
            var radio_check_val = "";
            for (var i = 0; i < document.getElementsByName('pilih_data_sppb').length; i++) {
                if (document.getElementsByName('pilih_data_sppb')[i].checked) {
                    radio_check_val = document.getElementsByName('pilih_data_sppb')[i].value;
                }
            }
            if (radio_check_val == 'master_data') {
                if ($('#metode_pembayaran_sppb').val() == 'bank') {
                    if ($('#jenis_spp').val() == 'karyawan') {
                        $('#kas_sppb').hide();
                        $('#bank_sppb_karyawan').show();
                        $('#bank_sppb_karyawan_master').show();
                        $('#bank_sppb_karyawan_input').hide();
                        $('#bank_sppb').hide();
                        $('#kas_sppb_master').hide();
                        $('#kas_sppb_input').hide();
                        document.getElementById("nama_bank_sppb_karyawan_1").className =
                            "form-control validate_sppb validate_spp_all";
                        document.getElementById("alamat_bank_sppb_karyawan_1").className =
                            "form-control validate_sppb validate_spp_all";
                        document.getElementById("rekening_bank_sppb_karyawan_1").className =
                            "form-control validate_sppb validate_spp_all";
                        document.getElementById("atas_nama_bank_sppb_karyawan_1").className =
                            "form-control validate_sppb validate_spp_all";
                        document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                        document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                        document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                        document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
                        document.getElementById("nama_bank_sppb_vendor").className = "form-control";
                        document.getElementById("alamat_bank_sppb_vendor").className = "form-control";
                        document.getElementById("rekening_bank_sppb_vendor").className = "form-control";
                        document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control";
                        //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                        document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                        document.getElementById("karyawan_kas_sppb_input").className = "form-control";
                        document.getElementById("karyawan_alamat_sppb_input").className = "form-control";


                    } else {
                        $('#bank_sppb').show();
                        $('#kas_sppb').hide();
                        $('#bank_sppb_karyawan').hide();
                        $('#kas_sppb_master').hide();
                        $('#kas_sppb_input').hide();
                        document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                        document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                        document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                        document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                        document.getElementById("alamat_bank_sppb_vendor").className =
                            "form-control validate_sppb validate_spp_all";
                        document.getElementById("nama_bank_sppb_vendor").className =
                            "form-control validate_sppb validate_spp_all";
                        document.getElementById("rekening_bank_sppb_vendor").className =
                            "form-control validate_sppb validate_spp_all";
                        document.getElementById("atas_nama_bank_sppb_vendor").className =
                            "form-control validate_sppb validate_spp_all";
                        //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                        document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                        document.getElementById("karyawan_kas_sppb_input").className = "form-control";
                        document.getElementById("karyawan_alamat_sppb_input").className = "form-control";

                        document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                        document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                        document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                        document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";

                    }


                    var inputs = document.getElementsByClassName("validate_sppb");
                    if (inputs) {
                        for (var i = 0; i < inputs.length; i++) {
                            inputs[i].addEventListener("change", validateInput);
                            inputs[i].addEventListener("focus", validateInput);
                        }
                    }
                } else if ($('#metode_pembayaran_sppb').val() == 'kas') {
                    if ($('#jenis_spp').val() == 'karyawan') {
                        $('#kas_sppb').show();
                        $('#kas_sppb_master').show();
                        $('#kas_sppb_input').hide();

                        document.getElementById("karyawan_alamat_sppb_input_1").className =
                            "form-control validate_sppb validate_spp_all";
                        //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control validate_sppb validate_spp_all";

                    } else {
                        $('#kas_sppb').hide();
                        document.getElementById("karyawan_alamat_sppb_input_1").className =
                            "form-control validate_sppb validate_spp_all";
                        //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control validate_sppb validate_spp_all";
                    }
                    $('#bank_sppb').hide();
                    $('#bank_sppb_karyawan').hide();

                    document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_vendor").className = "form-control ";
                    document.getElementById("nama_bank_sppb_vendor").className = "form-control";
                    document.getElementById("rekening_bank_sppb_vendor").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control";
                    document.getElementById("karyawan_kas_sppb_input").className = "form-control";
                    document.getElementById("karyawan_alamat_sppb_input").className = "form-control";

                    document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
                } else if ($('#metode_pembayaran_sppb').val() == 'skbdn') {
                    $('#kas_sppb').hide();
                    $('#bank_sppb').show();
                    $('#alamat_vendor_sppb').show();
                    $('#atas_nama_vendor_sppb').show();

                    $('#bank_sppb_karyawan').hide();
                    $('#kas_sppb_master').hide();
                    $('#kas_sppb_input').hide();
                    document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                    //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("nama_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("rekening_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("atas_nama_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";

                    document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("karyawan_kas_sppb_input").className = "form-control";
                    document.getElementById("karyawan_alamat_sppb_input").className = "form-control";
                    document.getElementById("nama_bank_sppb_vendor").onclick = function() {
                        data_bank_sppb()
                    };
                    document.getElementById("rekening_bank_sppb_vendor").onclick = function() {
                        data_bank_sppb()
                    };
                    document.getElementById("atas_nama_bank_sppb_vendor").onclick = function() {
                        data_bank_sppb()
                    };
                } else {
                    $('#pilih_lampirkan_sppb').hide();
                    $('#kas_sppb').hide();
                    $('#bank_sppb').hide();
                    $('#bank_sppb_karyawan').hide();
                    $('#kas_sppb_master').hide();
                    $('#kas_sppb_input').hide();
                    document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                    //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_vendor").className = "form-control ";
                    document.getElementById("nama_bank_sppb_vendor").className = "form-control";
                    document.getElementById("rekening_bank_sppb_vendor").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control";

                    document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
                }
            } else if (radio_check_val == 'input_data') {
                if ($('#metode_pembayaran_sppb').val() == 'kas') {
                    $('#kas_sppb').show();
                    $('#kas_sppb_master').hide();
                    $('#kas_sppb_input').show();
                    $('#bank_sppb').hide();
                    $('#bank_sppb_karyawan').hide();

                    document.getElementById("karyawan_kas_sppb_input").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("karyawan_alamat_sppb_input").className =
                        "form-control validate_sppb validate_spp_all";
                    //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                    document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_vendor").className = "form-control ";
                    document.getElementById("nama_bank_sppb_vendor").className = "form-control";
                    document.getElementById("rekening_bank_sppb_vendor").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control";

                    document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
                } else if ($('#metode_pembayaran_sppb').val() == 'bank') {
                    $('#bank_sppb_karyawan').show();
                    $('#bank_sppb_karyawan_input').show();
                    $('#bank_sppb_karyawan_master').hide();
                    $('#kas_sppb_master').hide();
                    $('#kas_sppb_input').hide();
                    $('#kas_sppb').hide();
                    $('#bank_sppb').hide();

                    document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("alamat_bank_sppb_karyawan_input_1").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("nama_bank_sppb_karyawan_input_1").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("rekening_bank_sppb_karyawan_input_1").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_vendor").className = "form-control ";
                    document.getElementById("nama_bank_sppb_vendor").className = "form-control";
                    document.getElementById("rekening_bank_sppb_vendor").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control";
                    document.getElementById("karyawan_kas_sppb_input").className = "form-control";
                    document.getElementById("karyawan_alamat_sppb_input").className = "form-control";
                    //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                    document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";

                } else if ($('#metode_pembayaran_sppb').val() == 'skbdn') {
                    $('#kas_sppb').hide();
                    $('#bank_sppb').show();
                    $('#kas_sppb_master').hide();
                    $('#kas_sppb_input').hide();
                    $('#bank_sppb_karyawan').hide();
                    $('#alamat_vendor_sppb').show();
                    $('#atas_nama_vendor_sppb').show();

                    document.getElementById("nama_bank_sppb_vendor").onclick = function() {};
                    document.getElementById("rekening_bank_sppb_vendor").onclick = function() {};
                    document.getElementById("atas_nama_bank_sppb_vendor").onclick = function() {};
                    document.getElementById("nama_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("rekening_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("alamat_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("atas_nama_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                    document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("karyawan_kas_sppb_input").className = "form-control";
                    document.getElementById("karyawan_alamat_sppb_input").className = "form-control";

                    document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";

                } else {
                    $('#pilih_lampirkan_sppb').hide();
                    $('#kas_sppb').hide();
                    $('#bank_sppb').hide();
                    $('#kas_sppb_master').hide();
                    $('#kas_sppb_input').hide();
                    $('#bank_sppb_karyawan').hide();
                    $('#alamat_vendor_sppb').hide();
                    $('#atas_nama_vendor_sppb').hide();
                    document.getElementById("alamat_bank_sppb_vendor").className = "form-control ";
                    document.getElementById("nama_bank_sppb_vendor").className = "form-control";
                    document.getElementById("rekening_bank_sppb_vendor").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control";
                    //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                    document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("karyawan_kas_sppb_input").className = "form-control";
                    document.getElementById("karyawan_alamat_sppb_input").className = "form-control";

                    document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
                }
            } else {
                $('#kas_sppb').hide();
                $('#bank_sppb').hide();
                $('#bank_sppb_karyawan').hide();
                $('#kas_sppb_master').hide();
                $('#kas_sppb_input').hide();
                if ($('#metode_pembayaran_sppb').val() == 'bank') {
                    if ($('#jenis_spp').val() == 'karyawan') {
                        document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                        document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                        document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                        document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                        document.getElementById("alamat_bank_sppb_vendor").className = "form-control ";
                        document.getElementById("nama_bank_sppb_vendor").className = "form-control";
                        document.getElementById("rekening_bank_sppb_vendor").className = "form-control";
                        document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control";
                        document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                        //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                        document.getElementById("karyawan_kas_sppb_input").className = "form-control";
                        document.getElementById("karyawan_alamat_sppb_input").className = "form-control";

                        document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                        document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                        document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                        document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";

                    } else {
                        document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                        document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                        document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                        document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                        document.getElementById("alamat_bank_sppb_vendor").className = "form-control ";
                        document.getElementById("nama_bank_sppb_vendor").className = "form-control";
                        document.getElementById("rekening_bank_sppb_vendor").className = "form-control";
                        document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control";
                        document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                        //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                        document.getElementById("karyawan_kas_sppb_input").className = "form-control";
                        document.getElementById("karyawan_alamat_sppb_input").className = "form-control";

                        document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                        document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                        document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                        document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
                    }

                } else {
                    if ($('#jenis_spp').val() == 'karyawan') {

                        document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                        //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";

                    } else {
                        document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                        //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                    }
                    document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_vendor").className = "form-control ";
                    document.getElementById("nama_bank_sppb_vendor").className = "form-control";
                    document.getElementById("rekening_bank_sppb_vendor").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control";
                    document.getElementById("karyawan_kas_sppb_input").className = "form-control";
                    document.getElementById("karyawan_alamat_sppb_input").className = "form-control";

                    document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
                }
            }

        };

        function pilih_data_sppb_vendor() {
            var radio_check_val = "";
            for (var i = 0; i < document.getElementsByName('pilih_data_sppb_vendor').length; i++) {
                if (document.getElementsByName('pilih_data_sppb_vendor')[i].checked) {
                    radio_check_val = document.getElementsByName('pilih_data_sppb_vendor')[i].value;
                }
            }
            if (radio_check_val == 'master_data') {
                if ($('#metode_pembayaran_sppb').val() == 'bank') {
                    $('#bank_sppb').show();
                    $('#kas_sppb').hide();
                    $('#bank_sppb_karyawan').hide();
                    document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_vendor").onclick = function() {
                        data_bank_sppb()
                    };
                    document.getElementById("rekening_bank_sppb_vendor").onclick = function() {
                        data_bank_sppb()
                    };
                    document.getElementById("atas_nama_bank_sppb_vendor").onclick = function() {
                        data_bank_sppb()
                    };
                    document.getElementById("alamat_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("nama_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("rekening_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("atas_nama_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                    //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";

                    document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
                } else if ($('#metode_pembayaran_sppb').val() == 'kas') {
                    $('#kas_sppb').hide();
                    $('#bank_sppb').hide();
                    document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                    //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_vendor").className = "form-control ";
                    document.getElementById("nama_bank_sppb_vendor").className = "form-control";
                    document.getElementById("rekening_bank_sppb_vendor").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control";

                    document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
                } else if ($('#metode_pembayaran_sppb').val() == 'skbdn') {
                    $('#kas_sppb').hide();
                    $('#bank_sppb').show();
                    document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                    //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_vendor").onclick = function() {
                        data_bank_sppb()
                    };
                    document.getElementById("rekening_bank_sppb_vendor").onclick = function() {
                        data_bank_sppb()
                    };
                    document.getElementById("atas_nama_bank_sppb_vendor").onclick = function() {
                        data_bank_sppb()
                    };
                    document.getElementById("alamat_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("nama_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("rekening_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("atas_nama_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";

                    document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
                } else {
                    $('#kas_sppb').hide();
                    $('#bank_sppb').hide();
                    $('#kas_sppb').hide();
                    document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                    //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_vendor").className = "form-control";
                    document.getElementById("nama_bank_sppb_vendor").className = "form-control";
                    document.getElementById("rekening_bank_sppb_vendor").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control";

                    document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
                }
            } else if (radio_check_val == 'input_data') {
                if ($('#metode_pembayaran_sppb').val() == 'bank') {
                    $('#bank_sppb').show();
                    $('#kas_sppb').hide();
                    $('#bank_sppb_karyawan').hide();
                    document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_vendor").onclick = function() {};
                    document.getElementById("rekening_bank_sppb_vendor").onclick = function() {};
                    document.getElementById("atas_nama_bank_sppb_vendor").onclick = function() {};
                    document.getElementById("alamat_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("nama_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("rekening_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("atas_nama_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                    //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";

                    document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
                } else if ($('#metode_pembayaran_sppb').val() == 'kas') {
                    $('#kas_sppb').hide();
                    $('#bank_sppb').hide();
                    document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                    //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_vendor").className = "form-control";
                    document.getElementById("nama_bank_sppb_vendor").className = "form-control";
                    document.getElementById("rekening_bank_sppb_vendor").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control";

                    document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
                } else if ($('#metode_pembayaran_sppb').val() == 'skbdn') {
                    $('#kas_sppb').hide();
                    $('#bank_sppb').show();
                    document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                    //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_vendor").onclick = function() {};
                    document.getElementById("rekening_bank_sppb_vendor").onclick = function() {};
                    document.getElementById("atas_nama_bank_sppb_vendor").onclick = function() {};
                    document.getElementById("alamat_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("nama_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("rekening_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";
                    document.getElementById("atas_nama_bank_sppb_vendor").className =
                        "form-control validate_sppb validate_spp_all";

                    document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
                } else {
                    $('#kas_sppb').hide();
                    $('#bank_sppb').hide();
                    $('#kas_sppb').hide();
                    document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                    //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                    document.getElementById("alamat_bank_sppb_vendor").className = "form-control";
                    document.getElementById("nama_bank_sppb_vendor").className = "form-control";
                    document.getElementById("rekening_bank_sppb_vendor").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control";

                    document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
                }
            } else {
                $('#bank_sppb').hide();
                $('#kas_sppb').hide();
                document.getElementById("karyawan_alamat_sppb_input_1").className = "form-control";
                //document.getElementById("atas_nama_bank_sppb_kas_1").className = "form-control";
                document.getElementById("alamat_bank_sppb_karyawan_1").className = "form-control";
                document.getElementById("nama_bank_sppb_karyawan_1").className = "form-control";
                document.getElementById("rekening_bank_sppb_karyawan_1").className = "form-control";
                document.getElementById("atas_nama_bank_sppb_karyawan_1").className = "form-control";
                document.getElementById("alamat_bank_sppb_vendor").className = "form-control";
                document.getElementById("nama_bank_sppb_vendor").className = "form-control";
                document.getElementById("rekening_bank_sppb_vendor").className = "form-control";
                document.getElementById("atas_nama_bank_sppb_vendor").className = "form-control";
                $('#pilih_lampirkan_sppb').hide();

                document.getElementById("alamat_bank_sppb_karyawan_input_1").className = "form-control";
                document.getElementById("nama_bank_sppb_karyawan_input_1").className = "form-control";
                document.getElementById("rekening_bank_sppb_karyawan_input_1").className = "form-control";
                document.getElementById("atas_nama_bank_sppb_karyawan_input_1").className = "form-control";
            }
        }
        $('#metode_pembayaran_sppn').change(function(event) {
            if ($('#jenis_spp').val() == 'karyawan') {
                $('#pilih_lampirkan_sppn').show();
                pilih_data_sppn.call();
            } else {
                $('#pilih_lampirkan_sppn').hide();
                if ($('#metode_pembayaran_sppn').val() == 'bank') {
                    $('#bank_sppn').show();
                    $('#kas_sppn').hide();
                    $('#bank_sppn_karyawan').hide();
                    document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppn_vendor").className =
                        "form-control validate_sppn validate_spp_all";
                    document.getElementById("rekening_bank_sppn_vendor").className =
                        "form-control validate_sppn validate_spp_all";
                    document.getElementById("atas_nama_bank_sppn_vendor").className =
                        "form-control validate_sppn validate_spp_all";
                    document.getElementById("atas_nama_bank_sppn_kas_1").className = "form-control";
                } else {
                    $('#kas_sppn').hide();
                    document.getElementById("atas_nama_bank_sppn_kas_1").className = "form-control";
                    document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppn_vendor").className = "form-control";
                    document.getElementById("rekening_bank_sppn_vendor").className = "form-control";
                    document.getElementById("atas_nama_bank_sppn_vendor").className = "form-control";
                }
            }
        });

        function pilih_data_sppn() {
            var radio_check_val = "";
            for (var i = 0; i < document.getElementsByName('pilih_data_sppn').length; i++) {
                if (document.getElementsByName('pilih_data_sppn')[i].checked) {
                    radio_check_val = document.getElementsByName('pilih_data_sppn')[i].value;
                }
            }
            if (radio_check_val == 'input_data') {
                if ($('#metode_pembayaran_sppn').val() == 'bank') {
                    if ($('#jenis_spp').val() == 'karyawan') {
                        $('#kas_sppn').hide();
                        $('#bank_sppn_karyawan').show();
                        $('#bank_sppn').hide();
                        document.getElementById("nama_bank_sppn_karyawan_1").className =
                            "form-control validate_sppn validate_spp_all";
                        document.getElementById("rekening_bank_sppn_karyawan_1").className =
                            "form-control validate_sppn validate_spp_all";
                        document.getElementById("atas_nama_bank_sppn_karyawan_1").className =
                            "form-control validate_sppn validate_spp_all";
                        document.getElementById("nama_bank_sppn_vendor").className = "form-control";
                        document.getElementById("rekening_bank_sppn_vendor").className = "form-control";
                        document.getElementById("atas_nama_bank_sppn_vendor").className = "form-control";
                        document.getElementById("atas_nama_bank_sppn_kas_1").className = "form-control";

                    } else {
                        $('#bank_sppn').show();
                        $('#kas_sppn').hide();
                        $('#bank_sppn_karyawan').hide();
                        document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control";
                        document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control";
                        document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control";
                        document.getElementById("nama_bank_sppn_vendor").className =
                            "form-control validate_sppn validate_spp_all";
                        document.getElementById("rekening_bank_sppn_vendor").className =
                            "form-control validate_sppn validate_spp_all";
                        document.getElementById("atas_nama_bank_sppn_vendor").className =
                            "form-control validate_sppn validate_spp_all";
                        document.getElementById("atas_nama_bank_sppn_kas_1").className = "form-control";
                    }


                    var inputs = document.getElementsByClassName("validate_sppn");
                    if (inputs) {
                        for (var i = 0; i < inputs.length; i++) {
                            inputs[i].addEventListener("change", validateInput);
                            inputs[i].addEventListener("focus", validateInput);
                        }
                    }
                } else {
                    if ($('#jenis_spp').val() == 'karyawan') {
                        $('#kas_sppn').show();
                        document.getElementById("atas_nama_bank_sppn_kas_1").className =
                            "form-control validate_sppn validate_spp_all";

                    } else {
                        $('#kas_sppn').hide();
                        document.getElementById("atas_nama_bank_sppn_kas_1").className = "form-control";
                    }
                    $('#bank_sppn').hide();
                    $('#bank_sppn_karyawan').hide();

                    document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppn_vendor").className = "form-control";
                    document.getElementById("rekening_bank_sppn_vendor").className = "form-control";
                    document.getElementById("atas_nama_bank_sppn_vendor").className = "form-control";
                }
            } else {
                $('#kas_sppn').hide();
                $('#bank_sppn').hide();
                $('#bank_sppn_karyawan').hide();
                if ($('#metode_pembayaran_sppn').val() == 'bank') {
                    if ($('#jenis_spp').val() == 'karyawan') {
                        document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control";
                        document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control";
                        document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control";
                        document.getElementById("nama_bank_sppn_vendor").className = "form-control";
                        document.getElementById("rekening_bank_sppn_vendor").className = "form-control";
                        document.getElementById("atas_nama_bank_sppn_vendor").className = "form-control";
                        document.getElementById("atas_nama_bank_sppn_kas_1").className = "form-control";

                    } else {
                        document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control";
                        document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control";
                        document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control";
                        document.getElementById("nama_bank_sppn_vendor").className = "form-control";
                        document.getElementById("rekening_bank_sppn_vendor").className = "form-control";
                        document.getElementById("atas_nama_bank_sppn_vendor").className = "form-control";
                        document.getElementById("atas_nama_bank_sppn_kas_1").className = "form-control";
                    }

                } else {
                    if ($('#jenis_spp').val() == 'karyawan') {

                        document.getElementById("atas_nama_bank_sppn_kas_1").className = "form-control";

                    } else {
                        document.getElementById("atas_nama_bank_sppn_kas_1").className = "form-control";
                    }
                    document.getElementById("nama_bank_sppn_karyawan_1").className = "form-control";
                    document.getElementById("rekening_bank_sppn_karyawan_1").className = "form-control";
                    document.getElementById("atas_nama_bank_sppn_karyawan_1").className = "form-control";
                    document.getElementById("nama_bank_sppn_vendor").className = "form-control";
                    document.getElementById("rekening_bank_sppn_vendor").className = "form-control";
                    document.getElementById("atas_nama_bank_sppn_vendor").className = "form-control";
                }
            }

        };

        function bank_karyawan_sppb(isi) {
            $('#modal_karyawan_sppb').modal('show');
            // console.log(isi);
            // console.log(jumlah_bank_karyawan);
            index_bank_sppb_karyawan = isi;
            nama_bank_sppb_karyawan_id = 'nama_bank_sppb_karyawan_' + isi;
            rekening_bank_sppb_karyawan_id = 'rekening_bank_sppb_karyawan_' + isi;
            atas_nama_bank_sppb_karyawan_id = 'atas_nama_bank_sppb_karyawan_' + isi;
        }

        function pilih_karyawan_sppb(id, nama, namabank, norek) {
            //window.alert(namabank);
            if (namabank == "") {
                $('#' + nama_bank_sppb_karyawan_id).val("");
                document.getElementById(nama_bank_sppb_karyawan_id).onclick = function() {};
            } else {
                $('#' + nama_bank_sppb_karyawan_id).val(namabank);
                document.getElementById(nama_bank_sppb_karyawan_id).onclick = function() {
                    bank_karyawan_sppb(index_bank_sppb_karyawan);
                }
            }
            if (norek == "") {
                $('#' + rekening_bank_sppb_karyawan_id).val("");
                document.getElementById(rekening_bank_sppb_karyawan_id).onclick = function() {};
            } else {
                $('#' + rekening_bank_sppb_karyawan_id).val(norek);
                document.getElementById(rekening_bank_sppb_karyawan_id).onclick = function() {
                    bank_karyawan_sppb(index_bank_sppb_karyawan);
                }

            }
            $('#' + atas_nama_bank_sppb_karyawan_id).val(nama);
            $('#modal_karyawan_sppb').modal('hide');
        }

        function bank_karyawan_sppn(isi) {
            $('#modal_karyawan_sppn').modal('show');
            index_bank_sppn_karyawan = isi;
            nama_bank_sppn_karyawan_id = 'nama_bank_sppn_karyawan_' + isi;
            rekening_bank_sppn_karyawan_id = 'rekening_bank_sppn_karyawan_' + isi;
            atas_nama_bank_sppn_karyawan_id = 'atas_nama_bank_sppn_karyawan_' + isi;
        }

        function pilih_karyawan_sppn(id, nama, namabank, norek) {
            //window.alert(namabank);
            if (namabank == "") {
                $('#' + nama_bank_sppn_karyawan_id).val("");
                document.getElementById(nama_bank_sppn_karyawan_id).onclick = function() {};
            } else {
                $('#' + nama_bank_sppn_karyawan_id).val(namabank);
                document.getElementById(nama_bank_sppn_karyawan_id).onclick = function() {
                    bank_karyawan_sppn(index_bank_sppn_karyawan);
                }
            }
            if (norek == "") {
                $('#' + rekening_bank_sppn_karyawan_id).val("");
                document.getElementById(rekening_bank_sppn_karyawan_id).onclick = function() {};
            } else {
                $('#' + rekening_bank_sppn_karyawan_id).val(norek);
                document.getElementById(rekening_bank_sppn_karyawan_id).onclick = function() {
                    bank_karyawan_sppn(index_bank_sppn_karyawan);
                }

            }
            $('#' + atas_nama_bank_sppn_karyawan_id).val(nama);
            $('#modal_karyawan_sppn').modal('hide');
        }

        function data_bank_sppb(isi) {
            $('#modal_bank_sppb').modal('show');
            if ($.fn.dataTable.isDataTable('#table-vendor-sppb')) {
                $('#table-vendor-sppb').DataTable().destroy();
            }
            $('#table-vendor-sppb').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('getVendor') }}',
                order: [],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'master_vendor_nama',
                        name: 'master_vendor_nama'
                    },
                    {
                        data: 'master_vendor_nama_bank',
                        name: 'master_vendor_nama_bank'
                    },
                    {
                        data: 'master_vendor_rekening',
                        name: 'master_vendor_rekening'
                    },
                    {
                        data: 'master_vendor_atas_nama',
                        name: 'master_vendor_atas_nama'
                    },
                    {
                        data: 'master_vendor_id',
                        "render": function(data, type, row) {
                            return `<button type="button" class="btn btn-info btn-sm" onclick="pilih_bank_sppb('${data}','${row.master_vendor_nama_bank}', '${row.master_vendor_rekening}', '${row.master_vendor_atas_nama}')" title="Pilih" ><i class="fa fa-check"></i></button>`
                        },
                        orderable: false,
                        searchable: false
                    },
                ]
            });
            bank_sppb_id = 'nama_bank_sppb_vendor';
            rekening_bank_sppb_id = 'rekening_bank_sppb_vendor';
            atas_nama_bank_sppb_id = 'atas_nama_bank_sppb_vendor';
            id_bank_sppb_id = 'id_bank_sppb_1';
        }

        function pilih_bank_sppb(id, nama, norekening, atasnama) {
            $('#' + id_bank_sppb_id).val(id);
            $('#' + bank_sppb_id).val(nama);
            document.getElementById(bank_sppb_id).focus();
            $('#' + rekening_bank_sppb_id).val(norekening);
            document.getElementById(rekening_bank_sppb_id).focus();
            $('#' + atas_nama_bank_sppb_id).val(atasnama);
            document.getElementById(atas_nama_bank_sppb_id).focus();
            $('#modal_bank_sppb').modal('hide');
        }

        function data_bank_sppn(isi) {
            $('#modal_bank_sppn').modal('show');
            bank_sppn_id = 'nama_bank_sppn_vendor';
            rekening_bank_sppn_id = 'rekening_bank_sppn_vendor';
            atas_nama_bank_sppn_id = 'atas_nama_bank_sppn_vendor';
            id_bank_sppn_id = 'id_bank_sppn_1';
        }

        function pilih_bank_sppn(id, nama, norekening, atasnama) {
            $('#' + id_bank_sppn_id).val(id);
            $('#' + bank_sppn_id).val(nama);
            document.getElementById(bank_sppn_id).focus();
            $('#' + rekening_bank_sppn_id).val(norekening);
            document.getElementById(rekening_bank_sppn_id).focus();
            $('#' + atas_nama_bank_sppn_id).val(atasnama);
            document.getElementById(atas_nama_bank_sppn_id).focus();
            $('#modal_bank_sppn').modal('hide');
        }

        function kode_rekening_sppb(isi) {
            $('#modal_rekening_sppb').modal('show');
            rekening_sppb_id = 'sap_vendor_sppb_' + isi;
            rekening_sppb_id_id = 'sap_vendor_sppb_id_' + isi;
        }

        function kode_gl_sppb(isi) {
            $('#modal_gl_sppb').modal('show');
            gl_sppb_id = 'sap_gl_sppb_' + isi;
            gl_sppb_id_id = 'sap_gl_sppb_id_' + isi;
        }

        function kode_gl_sppn(isi) {
            $('#modal_gl_sppn').modal('show');
            gl_sppn_id = 'sap_gl_sppn_' + isi;
            gl_sppn_id_id = 'sap_gl_sppn_id_' + isi;
        }

        function kode_customer_sppb(isi) {
            $('#modal_customer_sppb').modal('show');
            customer_sppb_id = 'sap_customer_sppb_' + isi;
            customer_sppb_id_id = 'sap_customer_sppb_id_' + isi;
        }

        function kode_customer_sppn(isi) {
            $('#modal_customer_sppn').modal('show');
            customer_sppn_id = 'sap_customer_sppn_' + isi;
            customer_sppn_id_id = 'sap_customer_sppn_id_' + isi;
        }

        function pilih_gl_sppb(id, kode, keterangan) {
            var rek = kode + ' (' + keterangan + ')';
            document.getElementById(gl_sppb_id).value = rek;
            document.getElementById(gl_sppb_id).focus();
            $('#' + gl_sppb_id_id).val(id);
            $('#modal_gl_sppb').modal('hide');
        }

        function pilih_gl_sppn(id, kode, keterangan) {
            var rek = kode + ' (' + keterangan + ')';
            document.getElementById(gl_sppn_id).value = rek;
            document.getElementById(gl_sppn_id).focus();
            $('#' + gl_sppn_id_id).val(id);
            $('#modal_gl_sppn').modal('hide');
        }

        function kode_rekening_sppn(isi) {
            $('#modal_rekening_sppn').modal('show');
            rekening_sppn_id = 'sap_vendor_sppn_' + isi;
            rekening_sppn_id_id = 'sap_vendor_sppn_id_' + isi;

        }

        // function pilih_rekening_sppb(id, kbb, sap, keterangan){
        // 	var rek = sap+' ('+keterangan+')';
        // 	document.getElementById(rekening_sppb_id).value = rek;
        // 	document.getElementById(rekening_sppb_id).focus();
        // 	$('#'+rekening_sppb_id_id).val(id);
        // 	$('#modal_rekening_sppb').modal('hide');
        // }

        // function pilih_rekening_sppn(id, kbb, sap, keterangan){
        // 	document.getElementById(rekening_sppn_id).value = sap+' ('+keterangan+')';
        // 	document.getElementById(rekening_sppn_id).focus();
        // 	$('#'+rekening_sppn_id_id).val(id);
        // 	$('#modal_rekening_sppn').modal('hide');
        // }

        function jc_sppn(index) {
            // console.log(index);
            var pilihan = $('#jenis_center_sppn_' + index).val();
            if (pilihan == 'cost_center') {
                document.getElementById("select_cost_center_sppn_" + index).className =
                    "form-control validate_sppn validate_spp_all select2-costcenter";
                document.getElementById("select_profit_center_sppn_" + index).className = "form-control";
                var inputs = document.getElementsByClassName("validate_sppn");
                if (inputs) {
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].addEventListener("change", validateInput);
                        inputs[i].addEventListener("keyup", validateInput);
                    }
                }
                $('#cost_center_sppn_' + index).show();
                $('#profit_center_sppn_' + index).hide();

                // fetchDataCostCenter($('#select_cost_center_sppn_' + index));
            } else {
                document.getElementById("select_profit_center_sppn_" + index).className =
                    "form-control validate_sppn validate_spp_all select2-profitcenter";
                document.getElementById("select_cost_center_sppn_" + index).className = "form-control";
                var inputs = document.getElementsByClassName("validate_sppn");
                if (inputs) {
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].addEventListener("change", validateInput);
                        inputs[i].addEventListener("keyup", validateInput);
                    }
                }
                $('#cost_center_sppn_' + index).hide();
                $('#profit_center_sppn_' + index).show();
            }

            refreshSelect2(pilihan);
        }

        function jc_sppb(index) {
            console.log(index);
            var pilihan = $('#jenis_center_sppb_' + index).val();
            if (pilihan == 'cost_center') {
                document.getElementById("select_cost_center_sppb_" + index).className =
                    "form-control validate_sppb validate_spp_all select2-costcenter";
                document.getElementById("select_profit_center_sppb_" + index).className = "form-control";
                var inputs = document.getElementsByClassName("validate_sppb");
                if (inputs) {
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].addEventListener("change", validateInput);
                        inputs[i].addEventListener("keyup", validateInput);
                    }
                }
                $('#cost_center_sppb_' + index).show();
                $('#profit_center_sppb_' + index).hide();

            } else {
                document.getElementById("select_profit_center_sppb_" + index).className =
                    "form-control validate_sppb validate_spp_all select2-profitcenter";
                document.getElementById("select_cost_center_sppb_" + index).className = "form-control";
                var inputs = document.getElementsByClassName("validate_sppb");
                if (inputs) {
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].addEventListener("change", validateInput);
                        inputs[i].addEventListener("keyup", validateInput);
                    }
                }
                $('#cost_center_sppb_' + index).hide();
                $('#profit_center_sppb_' + index).show();
            }

            refreshSelect2(pilihan);
        }

        function pilih_rekening_sppn(index, id) {
            var kbb = document.getElementById(id + index).parentElement;
            // console.log(kbb);
            if ($('#' + id + index).val() == "") {
                kbb.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;width:100%;";
            } else {
                kbb.style.cssText =
                    "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;width:100%;";
            }
            var a = document.getElementById(id + 'id_' + index);
            if (a !== null) {
                document.getElementById(id + 'id_' + index).value = $('#' + id + index).val();

            }
            js_sppn(index);
            bandingkan_dpp_sisa()
        }

        function pilih_rekening_sppb(index, id) {
            var kbb = document.getElementById(id + index).parentElement;
            // console.log('asdasdasdsada'+index);
            if ($('#' + id + index).val() == "") {
                kbb.style.cssText = "border-width:2px;border-color:red;border-style:solid;border-radius:1px;width:100%;";
            } else {
                kbb.style.cssText =
                    "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;width:100%;";
            }
            var a = document.getElementById(id + 'id_' + index);
            if (a !== null) {
                document.getElementById(id + 'id_' + index).value = $('#' + id + index).val();

            }
            js_sppb(index);
            bandingkan_dpp_sisa()


        }

        function js_sppb(index) {

            var pilihan = $('#jenis_sap_sppb_' + index).val();
            console.log('js_Spp', pilihan);
            // if (pilihan == 'vendor') {
            //     document.getElementById("sap_gl_sppb_" + index).className = "form-control";
            //     document.getElementById("sap_vendor_sppb_" + index).className = "selectpicker";
            //     var inputs = document.getElementsByClassName("validate_sppb");
            //     if (inputs) {
            //         for (var i = 0; i < inputs.length; i++) {
            //             inputs[i].addEventListener("change", validateInput);
            //             inputs[i].addEventListener("keyup", validateInput);
            //         }
            //     }
            //     $('#nomor_vendor_sppb_' + index).show();
            //     $('#nomor_gl_sppb_' + index).hide();
            //     $('#nomor_customer_sppb_' + index).hide();

            // } else {
            //     document.getElementById("sap_gl_sppb_" + index).className = "selectpicker";
            //     document.getElementById("sap_vendor_sppb_" + index).className = "form-control";
            //     var inputs = document.getElementsByClassName("validate_sppb");
            //     if (inputs) {
            //         for (var i = 0; i < inputs.length; i++) {
            //             inputs[i].addEventListener("change", validateInput);
            //             inputs[i].addEventListener("keyup", validateInput);
            //         }
            //     }
            //     $('#nomor_vendor_sppb_' + index).hide();
            //     $('#nomor_gl_sppb_' + index).show();
            //     $('#nomor_customer_sppb_' + index).show();

            //     $.ajaxSetup({
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         }
            //     });

            //     $('#sap_gl_sppb_' + index).on('change', function() {
            //         //ways to retrieve selected option and text outside handler
            //         var budeget = $(this).find(':selected').data('budget_' + index);
            //         var gl_id = $(this).find(':selected').val();
            //         var reverse = budeget.toString().split('').reverse().join('');
            //         ribuan = reverse.match(/\d{1,3}/g);
            //         ribuan = ribuan.join('.').split('').reverse().join('');
            //         $('.budget_gl_' + index).val('Rp. ' + ribuan);
            //         $('.budget_gl_hide_' + index).val(budeget);
            //         $.ajax({
            //             type: 'POST',
            //             url: "{{ route('realisasi') }}",
            //             data: {
            //                 gl_id: gl_id
            //             },
            //             success: function(data) {
            //                 realisasi = parseInt(data.realisasisppn) + parseInt(data.realisasi);
            //                 var reverse1 = realisasi.toString().split('').reverse().join(''),
            //                     ribuan1 = reverse1.match(/\d{1,3}/g);
            //                 ribuan1 = ribuan1.join('.').split('').reverse().join('');
            //                 $('.realisasi_' + index).val('Rp. ' + ribuan1);

            //                 onproses = parseInt(data.onproses) + parseInt(data.onprosessppn);
            //                 var reverse2 = onproses.toString().split('').reverse().join(''),
            //                     ribuan2 = reverse2.match(/\d{1,3}/g);
            //                 ribuan2 = ribuan2.join('.').split('').reverse().join('');
            //                 $('.onproses_' + index).val('Rp. ' + ribuan2);

            //                 sisa[index] = budeget - data.realisasi - data.onproses - data
            //                     .realisasisppn - data.onprosessppn;
            //                 var reverse3 = sisa[index].toString().split('').reverse().join(''),
            //                     ribuan3 = reverse3.match(/\d{1,3}/g);
            //                 ribuan3 = ribuan3.join('.').split('').reverse().join('');
            //                 $('.sisa_' + index).val('Rp. ' + ribuan3);
            //             }
            //         });
            //     });
            // }

            if (pilihan == 'vendor') {
                $("#sap_gl_sppn_" + index).attr("class", "form-control");
                $("#sap_vendor_sppn_" + index).attr("class", "control select2-vendor sap-vendor-sppn");
                $("#sap_customer_sppn_" + index).attr("class", "form-control");

                $("#sap_gl_sppb_" + index).attr('class', 'form-control');
                $('#sap_vendor_sppb_' + index).attr('class', 'form-control select2-vendor sap_vendor_sppb slct_sppb');
                $('#sap_customer_sppb_' + index).attr('class', 'form-control');
                // document.getElementById("sap_gl_sppb_" + index).className = "form-control";
                // document.getElementById("sap_vendor_sppb_" + index).className = "selectpicker";
                // document.getElementById("sap_customer_sppb_" + index).className = "form-control";
                var inputs = document.getElementsByClassName("validate_sppb");
                if (inputs) {
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].addEventListener("change", validateInput);
                        inputs[i].addEventListener("keyup", validateInput);
                        inputs[i].addEventListener("change", validateInput);
                        inputs[i].addEventListener("keyup", validateInput);
                    }
                }
                $('#nomor_vendor_sppb_' + index).show();
                $('#nomor_gl_sppb_' + index).hide();
                $('#nomor_customer_sppb_' + index).hide();

                refreshSelect2(pilihan);

            } else if (pilihan == 'gl') {
                $("#sap_gl_sppb_" + index).attr('class', 'form-control select2-gl');
                $('#sap_vendor_sppb_' + index).attr('class', 'form-control');
                $('#sap_customer_sppb_' + index).attr('class', 'form-control');
                // document.getElementById("sap_gl_sppb_" + index).className = "selectpicker";
                // document.getElementById("sap_vendor_sppb_" + index).className = "form-control";
                // document.getElementById("sap_customer_sppb_" + index).className = "form-control";
                var inputs = document.getElementsByClassName("validate_sppb");
                if (inputs) {
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].addEventListener("change", validateInput);
                        inputs[i].addEventListener("keyup", validateInput);
                        inputs[i].addEventListener("change", validateInput);
                        inputs[i].addEventListener("keyup", validateInput);
                    }
                }
                $('#nomor_vendor_sppb_' + index).hide();
                $('#nomor_gl_sppb_' + index).show();
                $('#nomor_customer_sppb_' + index).hide();

                refreshSelect2(pilihan);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var gl_id = $('#sap_gl_sppb_' + index).find(':selected').val();

                if (gl_id != undefined && gl_id != '') {
                    url = '{{ route('jumlah_budget', ['id' => 'gl_id']) }}';
                    url = url.replace('gl_id', gl_id);
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            var budeget = response.jumlah_budget;
                            // console.log(budeget, gl_id, $(this), index)
                            var reverse = budeget.toString().split('').reverse().join('');
                            ribuan = reverse.match(/\d{1,3}/g);
                            ribuan = ribuan.join('.').split('').reverse().join('');
                            $('.budget_gl_' + index).val('Rp. ' + ribuan);
                            $('.budget_gl_hide_' + index).val(budeget);
                            $.ajax({
                                type: 'POST',
                                url: "{{ route('realisasi') }}",
                                data: {
                                    gl_id: gl_id
                                },
                                success: function(data) {
                                    realisasi = parseInt(data.realisasisppn) + parseInt(data
                                        .realisasi);
                                    var reverse1 = realisasi.toString().split('').reverse().join(
                                            ''),
                                        ribuan1 = reverse1.match(/\d{1,3}/g);
                                    ribuan1 = ribuan1.join('.').split('').reverse().join('');
                                    $('.realisasi_' + index).val('Rp. ' + ribuan1);

                                    onproses = parseInt(data.onproses) + parseInt(data
                                        .onprosessppn);
                                    var reverse2 = onproses.toString().split('').reverse().join(''),
                                        ribuan2 = reverse2.match(/\d{1,3}/g);
                                    ribuan2 = ribuan2.join('.').split('').reverse().join('');
                                    $('.onproses_' + index).val('Rp. ' + ribuan2);

                                    sisa[index] = budeget - data.realisasi - data.onproses - data
                                        .realisasisppn - data
                                        .onprosessppn;
                                    var reverse3 = sisa[index].toString().split('').reverse().join(
                                            ''),
                                        ribuan3 = reverse3.match(/\d{1,3}/g);
                                    ribuan3 = ribuan3.join('.').split('').reverse().join('');
                                    $('.sisa_' + index).val('Rp. ' + ribuan3);
                                }
                            });
                        }
                    })
                }

                // $('#sap_gl_sppb_' + index).on('change', function() {
                //     //ways to retrieve selected option and text outside handler
                //     console.log(`find index ${index}`);

                //     var budeget = $(this).find(':selected').data('budget_' + index);
                //     var gl_id = $(this).find(':selected').val();
                //     var reverse = budeget.toString().split('').reverse().join('');
                //     ribuan = reverse.match(/\d{1,3}/g);
                //     ribuan = ribuan.join('.').split('').reverse().join('');
                //     $('.budget_gl_' + index).val('Rp. ' + ribuan);
                //     $('.budget_gl_hide_' + index).val(budeget);
                //     $.ajax({
                //         type: 'POST',
                //         url: "{{ route('realisasi') }}",
                //         data: {
                //             gl_id: gl_id
                //         },
                //         success: function(data) {
                //             realisasi = parseInt(data.realisasisppn) + parseInt(data.realisasi);
                //             var reverse1 = realisasi.toString().split('').reverse().join(''),
                //                 ribuan1 = reverse1.match(/\d{1,3}/g);
                //             ribuan1 = ribuan1.join('.').split('').reverse().join('');
                //             $('.realisasi_' + index).val('Rp. ' + ribuan1);

                //             onproses = parseInt(data.onproses) + parseInt(data.onprosessppn);
                //             var reverse2 = onproses.toString().split('').reverse().join(''),
                //                 ribuan2 = reverse2.match(/\d{1,3}/g);
                //             ribuan2 = ribuan2.join('.').split('').reverse().join('');
                //             $('.onproses_' + index).val('Rp. ' + ribuan2);

                //             sisa[index] = budeget - data.realisasi - data.onproses - data
                //                 .realisasisppn - data.onprosessppn;
                //             var reverse3 = sisa[index].toString().split('').reverse().join(''),
                //                 ribuan3 = reverse3.match(/\d{1,3}/g);
                //             ribuan3 = ribuan3.join('.').split('').reverse().join('');
                //             $('.sisa_' + index).val('Rp. ' + ribuan3);
                //         }
                //     });
                // });
            } else {
                $("#sap_gl_sppb_" + index).attr('class', 'form-control');
                $('#sap_vendor_sppb_' + index).attr('class', 'form-control');
                $('#sap_customer_sppb_' + index).attr('class', 'selectpicker');
                // document.getElementById("sap_gl_sppb_" + index).className = "form-control";
                // document.getElementById("sap_vendor_sppb_" + index).className = "form-control";
                // document.getElementById("sap_customer_sppb_" + index).className = "selectpicker";
                var inputs = document.getElementsByClassName("validate_sppb");
                if (inputs) {
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].addEventListener("change", validateInput);
                        inputs[i].addEventListener("keyup", validateInput);
                        inputs[i].addEventListener("change", validateInput);
                        inputs[i].addEventListener("keyup", validateInput);
                    }
                }
                $('#nomor_vendor_sppb_' + index).hide();
                $('#nomor_gl_sppb_' + index).hide();
                $('#nomor_customer_sppb_' + index).show();
            }
            bandingkan_dpp_sisa();

        }

        function js_sppn(index) {
            console.log(index);
            var pilihan = $('#jenis_sap_sppn_' + index).val();
            console.log('js_Spp', pilihan);
            // if (pilihan == 'vendor') {
            //     document.getElementById("sap_gl_sppn_" + index).className = "form-control";
            //     document.getElementById("sap_vendor_sppn_" + index).className = "selectpicker";
            //     var inputs = document.getElementsByClassName("validate_sppn");
            //     if (inputs) {
            //         for (var i = 0; i < inputs.length; i++) {
            //             inputs[i].addEventListener("change", validateInput);
            //             inputs[i].addEventListener("keyup", validateInput);
            //         }
            //     }
            //     $('#nomor_vendor_sppn_' + index).show();
            //     $('#nomor_gl_sppn_' + index).hide();
            //     $('#nomor_customer_sppn_' + index).hide();

            // } else {
            //     document.getElementById("sap_gl_sppn_" + index).className = "selectpicker";
            //     document.getElementById("sap_vendor_sppn_" + index).className = "form-control";
            //     var inputs = document.getElementsByClassName("validate_sppn");
            //     if (inputs) {
            //         for (var i = 0; i < inputs.length; i++) {
            //             inputs[i].addEventListener("change", validateInput);
            //             inputs[i].addEventListener("keyup", validateInput);
            //         }
            //     }
            //     $('#nomor_vendor_sppn_' + index).hide();
            //     $('#nomor_gl_sppn_' + index).show();
            //     $('#nomor_customer_sppn_' + index).show();
            //     $.ajaxSetup({
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         }
            //     });

            //     $('#sap_gl_sppn_' + index).on('change', function() {
            //         //ways to retrieve selected option and text outside handler
            //         var budeget = $(this).find(':selected').data('budgetsppn_' + index);
            //         var gl_id = $(this).find(':selected').val();
            //         var reverse = budeget.toString().split('').reverse().join('');
            //         ribuan = reverse.match(/\d{1,3}/g);
            //         ribuan = ribuan.join('.').split('').reverse().join('');
            //         $('.budget_glsppn_' + index).val('Rp. ' + ribuan);
            //         $('.budget_glsppn_hide' + index).val(budeget);
            //         $.ajax({
            //             type: 'POST',
            //             url: "{{ route('realisasi') }}",
            //             data: {
            //                 gl_id: gl_id
            //             },
            //             success: function(data) {

            //                 realisasi = parseInt(data.realisasisppn) + parseInt(data.realisasi);
            //                 var reverse1 = realisasi.toString().split('').reverse().join(''),
            //                     ribuan1 = reverse1.match(/\d{1,3}/g);
            //                 ribuan1 = ribuan1.join('.').split('').reverse().join('');
            //                 $('.realisasisppn_' + index).val('Rp. ' + ribuan1);

            //                 onproses = parseInt(data.onproses) + parseInt(data.onprosessppn);
            //                 var reverse2 = onproses.toString().split('').reverse().join(''),
            //                     ribuan2 = reverse2.match(/\d{1,3}/g);
            //                 ribuan2 = ribuan2.join('.').split('').reverse().join('');
            //                 $('.onprosessppn_' + index).val('Rp. ' + ribuan2);

            //                 sisa[index] = budeget - data.realisasisppn - data.onprosessppn - data
            //                     .realisasi - data.onproses;
            //                 var reverse3 = sisa[index].toString().split('').reverse().join(''),
            //                     ribuan3 = reverse3.match(/\d{1,3}/g);
            //                 ribuan3 = ribuan3.join('.').split('').reverse().join('');
            //                 $('.sisasppn_' + index).val('Rp. ' + ribuan3);
            //             }
            //         });
            //     });

            // }

            if (pilihan == 'vendor') {
                console.log("ini Vendor");
                // $(`#sap_gl_sppn_${index}`).addClass("form-control");
                // $(`#sap_vendor_sppn_${index}`).addClass("selectpicker");
                // $(`#sap_customer_sppn_${index}`).addClass("form-control");
                // var inputs = document.getElementsByClassName("validate_sppn");
                // if (inputs) {
                //     for (var i = 0; i < inputs.length; i++) {
                //         inputs[i].addEventListener("change", validateInput);
                //         inputs[i].addEventListener("keyup", validateInput);
                //         inputs[i].addEventListener("change", validateInput);
                //         inputs[i].addEventListener("keyup", validateInput);
                //     }
                // }
                // $('#nomor_vendor_sppn_' + index).show();
                // $('#nomor_gl_sppn_' + index).hide();
                // $('#nomor_customer_sppn_' + index).hide();

                $("#sap_gl_sppn_" + index).attr("class", "form-control");
                $("#sap_vendor_sppn_" + index).attr("class", "select2-vendor sap-vendor-sppn");
                $("#sap_customer_sppn_" + index).attr("class", "form-control");
                var inputs = document.getElementsByClassName("validate_sppn");
                if (inputs) {
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].addEventListener("change", validateInput);
                        inputs[i].addEventListener("keyup", validateInput);
                        inputs[i].addEventListener("change", validateInput);
                        inputs[i].addEventListener("keyup", validateInput);
                    }
                }
                $('#nomor_vendor_sppn_' + index).show();
                $('#nomor_gl_sppn_' + index).hide();
                $('#nomor_customer_sppn_' + index).hide();

                refreshSelect2('vendor');

            } else if (pilihan == 'gl') {
                console.log("ini GL");
                $('#sap_gl_sppn_' + index).attr("class", "select2-gl");
                $('#sap_vendor_sppn_' + index).attr("class", "form-control");
                $('#sap_customer_sppn_' + index).attr("class", "form-control");
                var inputs = document.getElementsByClassName("validate_sppn");
                if (inputs) {
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].addEventListener("change", validateInput);
                        inputs[i].addEventListener("keyup", validateInput);
                        inputs[i].addEventListener("change", validateInput);
                        inputs[i].addEventListener("keyup", validateInput);
                    }
                }
                $('#nomor_vendor_sppn_' + index).hide();
                $('#nomor_gl_sppn_' + index).show();
                $('#nomor_customer_sppn_' + index).hide();

                refreshSelect2('gl');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var gl_id = $('#sap_gl_sppn_' + index).find(':selected').val();

                console.log('gl id = ' + $('#sap_gl_sppn_' + index).find(':selected').val());

                if (gl_id != undefined && gl_id != '') {
                    var url = '{{ route('jumlah_budget', ['id' => 'gl_id']) }}';
                    url = url.replace('gl_id', gl_id);

                    $.ajax({
                        type: 'GET',
                        url: url,
                        success: function(response) {
                            var budeget = response.jumlah_budget;
                            // console.log(budeget, gl_id, $(this), index)
                            var reverse = budeget.toString().split('').reverse().join('');
                            ribuan = reverse.match(/\d{1,3}/g);
                            ribuan = ribuan.join('.').split('').reverse().join('');
                            $('.budget_glsppn_' + index).val('Rp. ' + ribuan);
                            $('.budget_glsppn_hide_' + index).val(budeget);
                            $.ajax({
                                type: 'POST',
                                url: "{{ route('realisasi') }}",
                                data: {
                                    gl_id: gl_id
                                },
                                success: function(data) {
                                    realisasi = parseInt(data.realisasisppn) + parseInt(data
                                        .realisasi);
                                    var reverse1 = realisasi.toString().split('').reverse().join(
                                            ''),
                                        ribuan1 = reverse1.match(/\d{1,3}/g);
                                    ribuan1 = ribuan1.join('.').split('').reverse().join('');
                                    $('.realisasisppn_' + index).val('Rp. ' + ribuan1);

                                    onproses = parseInt(data.onproses) + parseInt(data
                                        .onprosessppn);
                                    var reverse2 = onproses.toString().split('').reverse().join(''),
                                        ribuan2 = reverse2.match(/\d{1,3}/g);
                                    ribuan2 = ribuan2.join('.').split('').reverse().join('');
                                    $('.onprosessppn_' + index).val('Rp. ' + ribuan2);

                                    sisasppn[index] = budeget - data.realisasi - data.onproses -
                                        data.realisasisppn - data
                                        .onprosessppn;
                                    var reverse3 = sisasppn[index].toString().split('').reverse()
                                        .join(''),
                                        ribuan3 = reverse3.match(/\d{1,3}/g);
                                    ribuan3 = ribuan3.join('.').split('').reverse().join('');
                                    $('.sisasppn_' + index).val('Rp. ' + ribuan3);
                                }
                            })
                        }
                    });
                }

                // $('#sap_gl_sppn_' + index).on('change', function() {
                //     //ways to retrieve selected option and text outside handler
                //     var budeget = $(this).find(':selected').data('budgetsppn_' + index);
                //     var gl_id = $(this).find(':selected').val();
                //     var reverse = budeget.toString().split('').reverse().join('');
                //     ribuan = reverse.match(/\d{1,3}/g);
                //     ribuan = ribuan.join('.').split('').reverse().join('');
                //     $('.budget_glsppn_' + index).val('Rp. ' + ribuan);
                //     $('.budget_glsppn_hide_' + index).val(budeget);
                //     $.ajax({
                //         type: 'POST',
                //         url: "{{ route('realisasi') }}",
                //         data: {
                //             gl_id: gl_id
                //         },
                //         success: function(data) {
                //             realisasi = parseInt(data.realisasisppn) + parseInt(data.realisasi);
                //             var reverse1 = realisasi.toString().split('').reverse().join(''),
                //                 ribuan1 = reverse1.match(/\d{1,3}/g);
                //             ribuan1 = ribuan1.join('.').split('').reverse().join('');
                //             $('.realisasisppn_' + index).val('Rp. ' + ribuan1);

                //             onproses = parseInt(data.onproses) + parseInt(data.onprosessppn);
                //             var reverse2 = onproses.toString().split('').reverse().join(''),
                //                 ribuan2 = reverse2.match(/\d{1,3}/g);
                //             ribuan2 = ribuan2.join('.').split('').reverse().join('');
                //             $('.onprosessppn_' + index).val('Rp. ' + ribuan2);

                //             sisa[index] = budeget - data.realisasi - data.onproses - data
                //                 .realisasisppn - data.onprosessppn;
                //             var reverse3 = sisa[index].toString().split('').reverse().join(''),
                //                 ribuan3 = reverse3.match(/\d{1,3}/g);
                //             ribuan3 = ribuan3.join('.').split('').reverse().join('');
                //             $('.sisasppn_' + index).val('Rp. ' + ribuan3);
                //         }
                //     });
                // });
            } else {
                console.log("bukan keduanya");
                $('#sap_gl_sppn_' + index).attr("class", "form-control");
                $('#sap_vendor_sppn_' + index).attr("class", "form-control");
                $('#sap_customer_sppn_' + index).attr("class", "selectpicker");
                // document.getElementById("sap_gl_sppn_" + index).className = "form-control";
                // document.getElementById("sap_vendor_sppn_" + index).className = "form-control";
                // document.getElementById("sap_customer_sppn_" + index).className = "selectpicker";
                var inputs = document.getElementsByClassName("validate_sppn");
                if (inputs) {
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].addEventListener("change", validateInput);
                        inputs[i].addEventListener("keyup", validateInput);
                        inputs[i].addEventListener("change", validateInput);
                        inputs[i].addEventListener("keyup", validateInput);
                    }
                }
                $('#nomor_vendor_sppn_' + index).hide();
                $('#nomor_gl_sppn_' + index).hide();
                $('#nomor_customer_sppn_' + index).show();

                refreshSelect2('customer');
            }
            bandingkan_dpp_sisa();

        }



        function tambah_isi_sppb() {
            var bagianid = "{{ $bagianid }}";
            index_sppb++;
            // index = index_sppb-1;
            sub_index_sppb[index_sppb] = 1;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // $.ajax({
            //     type: 'POST',
            //     url: "{{ route('mas_rek_t') }}",
            //     success: function(data) {
            //         //console.log(data.rekening);


            //         // $.each(data.rekening,function(no,value){
            //         //     $("#kode_kbb_sppb_"+index_sppb).append('<option value="'+value.master_rekening_id+'">'+value.master_rekening_kode_kbb+'</option>');
            //         // });
            //         $.each(data.rekening, function(no, value) {
            //             $("#sap_vendor_sppb_" + index_sppb).append('<option value="' + value
            //                 .master_rekening_id + '">' + '(' + value.master_rekening_kode_sap +
            //                 ')' + value.master_rekening_keterangan + '</option>');
            //         });
            //         //$("#kode_kbb_sppb_"+index_sppb).selectpicker('refresh');
            //         $("#sap_vendor_sppb_" + index_sppb).selectpicker('refresh');


            //     }
            // });

            $('#tab-isi-sppb').append(`<div id="isi_sppb_${index_sppb}" class="col-sm-12">
											<div  style="padding-bottom: 10px; margin-bottom: 20px; border-bottom: 1px solid #eaeaea;">
												<font size="4" style="margin-right: 20px">Isi ${index_sppb}. </font>
												<button type="button" class="btn btn-info btn-sm" onclick="tambah_isi_sppb()">+</button>
												<button type="button" class="btn btn-danger btn-sm" onclick="hapus_isi_sppb(${index_sppb},'ckeditor_${index_sppb}_1')">X</button>
											</div>

											<div class="col-sm-5">
												<!-- <div class="form-group row">
													<label class="col-sm-3 col-form-label">Kode KBB *</label>
													<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppb_${index_sppb}" >
														<select class="selectpicker slct_sppb"  data-live-search="true" data-dropup-auto="false" id="kode_kbb_sppb_${index_sppb}" data-width="100%" name="isi_sppb[${index_sppb}][kode_kbb]" data-size="7" onchange="pilih_rekening_sppb(${index_sppb},'kode_kbb_sppb_')">
															<option disabled selected>---Pilih KBB---</option>
														</select>
														</div>

													</div>
												</div> -->
												<div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">Kode SAP *</label>
                                                    <label class="col-sm-9">
                                                        <select class="form-control validate_sppb validate_spp_all" id="jenis_sap_sppb_${index_sppb}" onchange="js_sppb(${index_sppb})" name="isi_sppb[${index_sppb}][jenis_sap]">
                                                            <option value="" disabled selected>-- Pilih Jenis Kode SAP --</option>
                                                            <option value="vendor">Nomor Vendor</option>
                                                            <option value="gl">Nomor GL</option>
                                                            <option value="customer">Nomor Customer</option>
                                                            {{-- @if (in_array($bagianid, [124, 126, 127]))
                                                                <option value="customer">Nomor Customer</option>
                                                            @endif --}}
                                                        </select>
                                                    </label>
                                                    <label class="col-sm-3 col-form-label"></label>

                                                    <div id="nomor_vendor_sppb_${index_sppb}" style="display:none">
                                                        <div class="col-sm-9">
                                                            <div class="row-fluid" id="parent_kbb_sppb_${index_sppb}">
                                                                <select class="select2-vendor sap_vendor_sppb slct_sppb" id="sap_vendor_sppb_${index_sppb}" name="isi_sppb[${index_sppb}][vendor]" onchange="pilih_rekening_sppb(${index_sppb},'sap_vendor_sppb_')">
                                                                    <option disabled selected>---Pilih SAP---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="nomor_gl_sppb_${index_sppb}" style="display:none">
                                                        <div class="col-sm-9">
                                                            <div class="row-fluid" id="parent_kbb_sppb_${index_sppb}">
                                                                <select class="select2-gl" id="sap_gl_sppb_${index_sppb}" name="isi_sppb[${index_sppb}][gl]" onchange="pilih_rekening_sppb(${index_sppb},'sap_gl_sppb_')">
                                                                    <option value="" disabled selected>-- Pilih Kode GL --</option>
                                                                    {{-- @foreach ($gl as $r)
                                                                    <option value="{{ $r->master_gl_id }}" data-budget_${index_sppb}="{{ $r->jumlah_budget }}" data-budget_${index_sppb}="{{ $r->master_gl_id }}">{{ $r->master_gl_kode }} ({{ $r->master_gl_keterangan }})</option>
                                                                    @endforeach --}}
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <label class="col-sm-12 col-form-label mt-2"></label>
                                                        <label class="col-sm-3 col-form-label">RKAP</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control budget_gl_${index_sppb} nominal" placeholder="Budget" autocomplete="off" readonly>
                                                            <input type="hidden" class="budget_gl_hide_${index_sppb}" name="budget">
                                                        </div>
                                                        <label class="col-sm-12 col-form-label mt-2"></label>
                                                        <label class="col-sm-3 col-form-label">Realisasi</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control realisasi_${index_sppb} nominal" placeholder="Realisasi" autocomplete="off" readonly>
                                                            <input type="hidden" class="budget_gl_hide" name="budget">
                                                        </div>
                                                        <label class="col-sm-12 col-form-label mt-2"></label>
                                                        <label class="col-sm-3 col-form-label">On Process</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control onproses_${index_sppb} nominal" placeholder="Onproses" autocomplete="off" readonly>
                                                            <input type="hidden" class="budget_gl_hide" name="budget">
                                                        </div>
                                                        <label class="col-sm-12 col-form-label mt-2"></label>
                                                        <label class="col-sm-3 col-form-label">Sisa</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control sisa_${index_sppb} nominal" placeholder="Sisa" autocomplete="off" readonly>
                                                            <input type="hidden" class="budget_gl_hide" name="budget">
                                                        </div>
                                                        <div class="col-sm-9">
                                                            <input type="text" style="display:none;" id="sap_gl_sppb_id_${index_sppb}" name="isi_sppb[${index_sppb}][gl]" class="form-control" onclick="kode_gl_sppb(1)" placeholder="Kode SAP (Nomor GL)" autocomplete="off" required>
                                                        </div>
                                                    </div>
                                                    <div id="nomor_customer_sppb_${index_sppb}" style="display:none">
                                                        <div class="col-sm-9">
                                                            <div class="row-fluid" id="parent_kbb_sppb_${index_sppb}">
                                                                <select class="select2-customer slct_sppb" id="sap_customer_sppb_${index_sppb}" name="isi_sppb[${index_sppb}][customer]" onchange="pilih_rekening_sppb(${index_sppb}, 'sap_customer_sppb_')">
                                                                        <option disabled selected>-- Pilih Kode Customer --</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- ${bagianid === 124 || bagianid === 126 || bagianid === 127 ? `
                                                    <div id="nomor_customer_sppb_${index_sppb}" style="display:none">
                                                        <div class="col-sm-9">
                                                            <div class="row-fluid" id="parent_kbb_sppb_${index_sppb}">
                                                                <select class="selectpicker slct_sppb" data-live-search="true"
                                                                    data-dropup-auto="false" data-width="100%" id="sap_customer_sppb_${index_sppb}"
                                                                    data-size="7" name="isi_sppb[${index_sppb}][customer]"
                                                                    onchange="pilih_rekening_sppb(${index_sppb}, 'sap_customer_sppb_')">
                                                                    <option disabled selected>-- Pilih Kode Customer --</option>
                                                                    ${customer.map(cs => `<option value="${cs.master_customer_id}">${cs.master_customer_kode_sap} (${cs.master_customer_nama})</option>`).join('')}
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    ` : ''} --}}
                                                </div>

												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Cost/Profit*</label>
													<label class="col-sm-9">
														<select class="form-control validate_sppn validate_spp_all" id="jenis_center_sppb_${index_sppb}" onchange="jc_sppb(${index_sppb})" name="isi_sppb[${index_sppb}][jenis_center]">
															<option value="" disabled selected>-- Pilih --</option>
															<option value="cost_center">Cost Center</option>
															<option value="profit_center">Profit Center</option>
														</select>
													</label>
													<label class="col-sm-3 col-form-label"></label>
													<div class="col-sm-9" id="cost_center_sppb_${index_sppb}" style="display: none">
														<select class="form-control select2-costcenter" id="select_cost_center_sppb_${index_sppb}" name="isi_sppb[${index_sppb}][cost_center]">
															<option value="" disabled selected>-- Pilih Cost Center --</option>
															{{-- @foreach ($costcenter as $cost)
																<option value="{{ $cost->master_cost_center_id }}">{{ $cost->master_cost_center_kode }} {{ $cost->master_cost_center_keterangan }}</option>
															@endforeach --}}
														</select>
													</div>

													<div class="col-sm-9" id="profit_center_sppb_${index_sppb}" style="display: none">
														<select class="form-control select2-profitcenter" id="select_profit_center_sppb_${index_sppb}" name="isi_sppb[${index_sppb}][profit_center]">
															{{-- @foreach ($profitcenter as $profit)
																<option value="{{ $profit->master_profit_center_id }}">{{ $profit->master_profit_center_kode }} ({{ $profit->master_profit_unit }})</option>
															@endforeach --}}
														</select>
													</div>

													{{-- @if ($bagian_karyawan->pemisah_keb_bag == 1)
													<label class="col-sm-9">
														<input type="text"  class="form-control" value="Cost Center" readonly>
														<input type="hidden"  name="isi_sppb[${index_sppb}][jenis_center]" value="cost_center" readonly>
													</label>
													<label class="col-sm-3 col-form-label"></label>
													<div class="col-sm-9">
														@foreach ($cost_center_id as $ccid)

														<input type="text"   class="form-control" value="{{ $ccid->master_cost_center_kode }} {{ $ccid->master_cost_center_keterangan }}" readonly>
														<input type="hidden"   name="isi_sppb[${index_sppb}][cost_center]" value="{{ $ccid->master_cost_center_id }}" readonly>
														@endforeach
													</div>
													@elseif ($bagian_karyawan->pemisah_keb_bag == 2)
													<label class="col-sm-9">
														<input type="text"  class="form-control" value="Profit Center" readonly>
														<input type="hidden"  name="isi_sppb[${index_sppb}][jenis_center]" value="profit_center" readonly>
													</label>
													<label class="col-sm-3 col-form-label"></label>
													<div class="col-sm-9">
														@foreach ($cost_center_id as $ccid)

														<input type="text"   class="form-control" value="{{ $ccid->master_profit_center_kode }} {{ $ccid->master_profit_unit }}" readonly>
														<input type="hidden"   name = "isi_sppb[${index_sppb}][profit_center]" value="{{ $ccid->master_profit_center_id }}" readonly>
														@endforeach
													</div>
													@endif --}}

												</div>

												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Cash Flow*</label>
													<div class="col-sm-9">
														<select class="form-control validate_sppb validate_spp_all select2-cashflow" id="cash_flow_sppb_${index_sppb}" name="isi_sppb[${index_sppb}][cash_flow]">
															<option value="" disabled selected>-- Pilih Cash Flow --</option>
                                                            @foreach ($cashflow as $cash)
                                                                <option value="{{ $cash->master_cash_flow_id }}">{{ $cash->master_cash_flow_key }} {{ $cash->master_cash_flow_keterangan }}</option>
                                                            @endforeach
														</select>
													</div>
												</div>
											</div>

											<div id="sub_isi_sppb_1_1">
												<div class="col-md-6 isi_karyawan" id="isi_karyawan_1_1" style="display:none ;">
													<div class="form-group row">
													<input type="hidden" value="karyawan" id="jenis_karyawan">
													</div>
												</div>
												<div class="col-sm-5"></div>
												<div class="col-md-6">
													<div class="form-group row">
														<label class="col-sm-1 col-form-label">1. </label>
														<label class="col-sm-2 col-form-label">Uraian *</label>
														<div class="col-sm-9">
															<div style="border: 1px solid hsla(0, 0%, 0%, 0.15);">
																<div id="uraian_sppb_${index_sppb}_1" style="height:auto;min-height:100px">
																	<!-- <input type="hidden" name="uraian_sppb[0][0][uraian]" id="uraian_sppb_value_${index_sppb}_1"> -->
																	<textarea class="form-control" id="ckeditor_${index_sppb}_1" name="uraian_sppb[${index_sppb}][1][ket]"></textarea>
																</div>
															</div>
														</div>
													</div>
													<!-- PAJAK WAPU WABA PPH MANUAL SPPB -->
													<div class="form-group row formInputPajakSppb id="pajak_${index_sppb}_1">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">Pajak*</label>
														<div class="col-sm-9">
															<select name="uraian_sppb[${index_sppb}][1][type_pajak_sppb]" id="pilih_pajak_sppb_${index_sppb}_1" class="form-control validasi_sppb" onchange="jenis_pajak_sppb(${index_sppb},1);">
																<option value="" selected disabled>-- Pilih Pajak --</option>
																<option value="wapu_sppb_${index_sppb}_1">WAPU</option>
																<option value="waba_sppb_${index_sppb}_1">WABA</option>
																<option value="pph_sppb_${index_sppb}_1">PPh</option>
																<option value="tanpa_pajak_sppb_${index_sppb}_1">Tanpa Pajak</option>

															</select>
														</div>
													</div>
													<div class="form-group row" id="wapu_pph_sppb_${index_sppb}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh</label>
														<div class="col-sm-9">
															<select name="uraian_sppb[${index_sppb}][1][pilih_wapu_sppb]" id="pilih_wapu_sppb_${index_sppb}_1" class="form-control">
																<option value="" disabled selected>-- Pilih --</option>
																<option value="wapu_normal_sppb_${index_sppb}_1">Normal</option>
																<option value="wapu_pph21_a_sppb_${index_sppb}_1">PPh 21 (2,5%)</option>
																<option value="wapu_pph21_b_sppb_${index_sppb}_1">PPh 21 (7,5%)</option>
																<option value="wapu_pph21_c_sppb_${index_sppb}_1">PPh 21 (12,5%)</option>
																<option value="wapu_pph22_a_sppb_${index_sppb}_1">PPh 22</option>
																<option value="wapu_pph23_a_sppb_${index_sppb}_1">PPh 23 (2%)</option>
																<option value="wapu_pph23_b_sppb_${index_sppb}_1">PPh 23 (15%)</option>
																<option value="wapu_pph23_c_sppb_${index_sppb}_1">PPh 23 (0%)</option>
																<option value="wapu_pph26_a_sppb_${index_sppb}_1">PPh 26 (0%)</option>
																<option value="wapu_pph26_b_sppb_${index_sppb}_1">PPh 26 (10%)</option>
																<option value="wapu_pph26_c_sppb_${index_sppb}_1">PPh 26 (20%)</option>
																<option value="wapu_pasal4ayat2_sppb_${index_sppb}_1">Pasal 4 Ayat 2</option>
																<option value="wapu_nilai_lain_sppb_${index_sppb}_1">DPP Nilai Lain</option>
																<option value="wapu_manual_sppb_${index_sppb}_1">PPh Manual</option>
															</select>
														</div>
													</div>

													<div class="form-group row" id="waba_sppb_${index_sppb}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh</label>
														<div class="col-sm-9">
															<select name="uraian_sppb[${index_sppb}][1][pilih_waba_sppb]" id="pilih_waba_sppb_${index_sppb}_1" class="form-control">
																<option value="" disabled selected>-- Pilih --</option>
																<option value="waba_normal_sppb_${index_sppb}_1">Normal</option>
																<option value="waba_pph21_a_sppb_${index_sppb}_1">PPh 21 (2,5%)</option>
																<option value="waba_pph21_b_sppb_${index_sppb}_1">PPh 21 (7,5%)</option>
																<option value="waba_pph21_c_sppb_${index_sppb}_1">PPh 21 (12,5%)</option>
																<option value="waba_pph22_a_sppb_${index_sppb}_1">PPh 22</option>
																<option value="waba_pph23_a_sppb_${index_sppb}_1">PPh 23 (2%)</option>
																<option value="waba_pph23_b_sppb_${index_sppb}_1">PPh 23 (15%)</option>
																<option value="waba_pph23_c_sppb_${index_sppb}_1">PPh 23 (0%)</option>
																<option value="waba_pph26_a_sppb_${index_sppb}_1">PPh 26 (0%)</option>
																<option value="waba_pph26_b_sppb_${index_sppb}_1">PPh 26 (10%)</option>
																<option value="waba_pph26_c_sppb_${index_sppb}_1">PPh 26 (20%)</option>
																<option value="waba_pasal4ayat2_sppb_${index_sppb}_1">Pasal 4 Ayat 2</option>
																<option value="waba_nilai_lain_sppb_${index_sppb}_1">DPP Nilai Lain</option>
																<option value="waba_manual_sppb_${index_sppb}_1">PPh Manual</option>
															</select>
														</div>
													</div>

													<div class="form-group row" id="pph_sppb_${index_sppb}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh</label>
														<div class="col-sm-9">
															<select name="uraian_sppb[${index_sppb}][1][pilih_pph_sppb]" id="pilih_pph_sppb_${index_sppb}_1" class="form-control">
																<option value="" disabled selected>-- Pilih --</option>
																<option value="pph21_a_sppb_${index_sppb}_1">PPh 21 (2,5%)</option>
																<option value="pph21_b_sppb_${index_sppb}_1">PPh 21 (7,5%)</option>
																<option value="pph21_c_sppb_${index_sppb}_1">PPh 21 (12,5%)</option>
																<option value="pph22_a_sppb_${index_sppb}_1">PPh 22</option>
																<option value="pph23_a_sppb_${index_sppb}_1">PPh 23 (2%)</option>
																<option value="pph23_b_sppb_${index_sppb}_1">PPh 23 (15%)</option>
																<option value="pph23_c_sppb_${index_sppb}_1">PPh 23 (0%)</option>
																<option value="pph26_a_sppb_${index_sppb}_1">PPh 26 (0%)</option>
																<option value="pph26_b_sppb_${index_sppb}_1">PPh 26 (10%)</option>
																<option value="pph26_c_sppb_${index_sppb}_1">PPh 26 (20%)</option>
																<option value="pphpasal4_ayat2_sppb_${index_sppb}_1">Pasal 4 Ayat 2</option>
																<option value="pph_manual_sppb_${index_sppb}_1">Manual</option>
															</select>
														</div>
													</div>
													<!-- END PAJAK WAPU WABA PPH MANUAL SPPB -->
													<!-- DPP LAIN DAN MANUAL PAJAK  -->
													<div class="form-group row" id="dpp_nilai_lain_sppb_${index_sppb}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">DPP Nilai Lain*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="1.1%" class="form-control" disabled>
															<input type="hidden" name="waba_nilai_lain" id="44" value="0.011">
														</div>
													</div>
													<div class="form-group row" id="manual_sppb_${index_sppb}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">Manual PPh*</label>
														<div class="col-sm-2">
														<input type="Number" name="uraian_sppb[${index_sppb}][1][manual]" id="nominal_manual_sppb_${index_sppb}_1" class="form-control">
														</div>
														<span><h3>%</h3></span>
													</div>
													<!-- END DPP LAIN DAN MANUAL PAJAK  -->

													<!-- GRUP INFO PERSEN PPh-->
													<div class="form-group row" id="pph_normal_sppb_${index_sppb}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="11%" class="form-control" disabled>
															<input type="hidden" name="input_normal_sppb_${index_sppb}_1" id="11" value="0.11">
														</div>
													</div>
													<div class="form-group row" id="pph21_sppb_${index_sppb}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh 21*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="21%" class="form-control" disabled>
															<input type="hidden" name="input_pph21_sppb_${index_sppb}_1" id="11" value="0.11">
														</div>
													</div>
													<div class="form-group row" id="pph22_sppb_${index_sppb}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh 22*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="22%" class="form-control" disabled>
															<input type="hidden" name="input_pph22_sppb_${index_sppb}_1" id="11" value="0.11">
														</div>
													</div>
													<div class="form-group row" id="pph23_sppb_${index_sppb}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh 23*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="2%" class="form-control" disabled>
															<input type="hidden" name="input_pph23_sppb_${index_sppb}_1" id="pph23" value="0.02">
														</div>
													</div>
													<div class="form-group row" id="pph26_sppb_${index_sppb}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh 26*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="26%" class="form-control" disabled>
															<input type="hidden" name="input_pph26_sppb_${index_sppb}_1" id="11" value="0.11">
														</div>
													</div>
													<div class="form-group row" id="pasal4_sppb_${index_sppb}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">Pasal 4 Ayat 2*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="4%" class="form-control" disabled>
															<input type="hidden" name="input_pasal4_sppb_${index_sppb}_1" id="11" value="0.11">
														</div>
													</div>
													<!-- END GRUP INFO PERSEN PPh-->
													<!-- GRUP NOMINAL SPPB -->
														<div class="form-group row" id="dpp_sppb_${index_sppb}_1" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">DPP</label>
															<div class="col-sm-9">
															<div class="group">
																<input type="text" id="nominal_sppb_${index_sppb}_1" name="uraian_sppb[${index_sppb}][1][jumlah]" class="form-control nominal validate_sppb validate_spp_all" placeholder="Nominal PPb" autocomplete="off" required>
																<input type="hidden" id="tidak_nominal_sppb_${index_sppb}_1"  placeholder="Nominal tanpa titik PPb" autocomplete="off" required>
																<label class="col-sm-6 col-form-label sppbcek_dana_gagal_${index_sppb}_1" style="display:none;color:red;">Dana melebihi sisa RKAP</label>
																<label class="col-sm-6 col-form-label sppbcek_dana_berhasil_${index_sppb}_1" style="display:none;color:green;">Dana dibawah sisa RKAP</label>
															</div>
															</div>
														</div>
														<div class="form-group row" id="nominal_potongan_sppb_${index_sppb}_1" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">Nominal Potongan</label>
															<div class="col-sm-9">
																<input type="text" id="potongan_sppb_${index_sppb}_1" name="uraian_sppb[${index_sppb}][1][potongan]" class="form-control" readonly>
															</div>
														</div>
														<div class="form-group row" id="nominal_pph_sppb_${index_sppb}_1" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">pph</label>
															<div class="col-sm-9">
																<input type="text" id="uraian_pph_sppb_${index_sppb}_1" name="uraian_sppb[${index_sppb}][1][pph]" class="form-control" readonly>
															</div>
														</div>
														<div class="form-group row" id="nominal_ppn_sppb_${index_sppb}_1" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">PPn</label>
															<div class="col-sm-9">
																<input type="text" id="ppn_sppb_${index_sppb}_1" name="uraian_sppb[${index_sppb}][1][pajak]" class="form-control" readonly>
															</div>
														</div>
														<div class="form-group row" id="dpp_ppn_sppb_${index_sppb}_1" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">DPP + PPN</label>
															<div class="col-sm-9">
																<input type="text" id="dppppn_sppb_${index_sppb}_1" name="uraian_sppb[${index_sppb}][1][dpp_ppn]" class="form-control" readonly>
															</div>
														</div>
														<div class="form-group row" id="nominal_akhir_sppb_${index_sppb}_1" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">Nominal Akhir</label>
															<div class="col-sm-9">
																<input type="text" id="akhir_sppb_${index_sppb}_1" name="uraian_sppb[${index_sppb}][1][total_pajak]" class="form-control" readonly>
															</div>
														</div>
													<!-- END GRUP NOMINAL SPPB -->
												</div>
												<div class="col-sm-1">
													<div class="col-sm-12" style="margin-bottom: 10px">
														<button type="button" class="btn btn-success btn-sm" onclick="tambah_sub_isi_sppb(${index_sppb})">+</button>
													</div>
												</div>
											</div>
										</div>`);

            // CKEDITOR.inline('ckeditor_' + index_sppb + '_1');
            // initializeSelectpicker($(`#sap_vendor_sppb_${index_sppb}`));
            // initializeSelectPickerGl($(`#sap_gl_sppb_${index_sppb}`), index_sppb, true);
            refreshSelect2('vendor');
            refreshSelect2('gl');
            refreshSelect2('customer');
            refreshSelect2('profit_center');
            refreshSelect2('cost_center');
            refreshSelect2('cashflow');

            InlineEditor
                .create(document.querySelector('#ckeditor_' + index_sppb + '_1'))
                .catch(error => {
                    console.error(error);
                });
            var jenis_spp = $('#jenis_spp').val();
            if (jenis_spp === 'karyawan') {
                $('.formInputPajakSppb').hide();
                $('#dpp_sppb_' + index_sppb + '_1').show();
                //document.getElementById('pajak_'+index_sppb+'_1').className = "form-control";
            } else {
                $('.formInputPajakSppn').show();
                //document.getElementById('pajak_'+index_sppb+'_1').className = "form-control validate_sppb validate_spp_all";
            }
            $('.nominal').mask('000.000.000.000.000.000.000', {
                reverse: true
            });
            var inputs = document.getElementsByClassName("validate_sppb");
            if (inputs) {
                for (var i = 0; i < inputs.length; i++) {
                    inputs[i].addEventListener("change", validateInput);
                    inputs[i].addEventListener("keyup", validateInput);
                    inputs[i].addEventListener("focus", validateInput);
                }
            }
            for (var i in CKEDITOR.instances) {
                if (i.substring(0, 9) == "ckeditor_") {
                    CKEDITOR.instances[i].on('change', function() {
                        var urai = document.getElementById(this.name).parentElement;
                        if (this.getData().replace(/<[^>]+>/g, '') == "") {
                            urai.style.cssText =
                                "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                        } else {
                            urai.style.cssText =
                                "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                        }
                    });
                }
            }
            $('.selectpicker').selectpicker();
            $("#jumlah_sppb_1" + index_sppb).on('keyup', function(e) {
                //("Isi - #jumlah_sppb_"+index_sppb+"_1");
                jum_nom[index_sppb] = [];
                jum_nom[index_sppb][1] = this.value.replace(/[^\d,]/g, "");
                var jum_nom_total = 0;
                for (let i = 1; i <= sub_index_sppb[index_sppb]; i++) {
                    var jum_nom_value = $("#jumlah_sppb_" + index_sppb + "_" + i) ? $("#jumlah_sppb_" + index_sppb +
                        "_" + i).val().replace(/[^\d,]/g, "") : 0;
                    jum_nom_total = jum_nom_value ? parseInt(jum_nom_total) + parseInt(jum_nom_value) :
                        jum_nom_total;
                    //console.log(jum_nom_total);
                }
                if (jum_nom_total > sisa[index_sppb]) {
                    $('.cek_dana_gagal_' + index_sppb + '_1').css('display', 'block');
                    $('.cek_dana_berhasil_' + index_sppb + '_1').css('display', 'none');
                    // $("#simpan").prop("disabled", true);
                } else {
                    $('.cek_dana_berhasil_' + index_sppb + '_1').css('display', 'block');
                    $('.cek_dana_gagal_' + index_sppb + '_1').css('display', 'none');
                    // $("#simpan").prop("disabled", false);
                }
            });

            set_listener_dpp();
        }

        function hapus_isi_sppb(isi, instance) {
            $("#isi_sppb_" + isi).remove();
            CKEDITOR.instances[instance].destroy();
            set_listener_dpp();
        }

        function tambah_sub_isi_sppb(isi) {
            sub_index_sppb[isi]++;
            $('#isi_sppb_' + isi).append(`<div id="sub_isi_sppb_${isi}_${sub_index_sppb[isi]}">
												<div class="col-sm-5"></div>
												<div class="col-md-6">
													<div class="form-group row">
														<label class="col-sm-1 col-form-label">${sub_index_sppb[isi]}. </label>
														<label class="col-sm-2 col-form-label">Uraian*</label>
														<div class="col-sm-9">
															<div style="border: 1px solid hsla(0, 0%, 0%, 0.15);">
																<div id="uraian_sppb_${isi}_${sub_index_sppb[isi]}" style="height:auto;min-height:100px">
																	<!-- <input type="hidden" name="uraian_sppb[0][0][uraian]" id="uraian_sppb_value${isi}_${sub_index_sppb[isi]}"> -->
																	<textarea class="form-control" id="ckeditor_${isi}_${sub_index_sppb[isi]}" name="uraian_sppb[${isi}][${sub_index_sppb}][ket]"></textarea>
																</div>
															</div>
														</div>
													</div>
													<!-- PAJAK WAPU WABA PPH MANUAL SPPB -->
													<div class="form-group row formInputPajakSppb" id="pajak_${isi}_${sub_index_sppb[isi]}">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">Pajak*</label>
														<div class="col-sm-9">
															<select name="uraian_sppb[${isi}][${sub_index_sppb}][type_pajak_sppb]" id="pilih_pajak_sppb_${isi}_${sub_index_sppb[isi]}" class="form-control" onchange="jenis_pajak_sppb(${index_sppb},${sub_index_sppb[index_sppb]});">
																<option value="" selected disabled>-- Pilih Pajak --</option>
																<option value="wapu_sppb_${isi}_${sub_index_sppb[isi]}">WAPU</option>
																<option value="waba_sppb_${isi}_${sub_index_sppb[isi]}">WABA</option>
																<option value="pph_sppb_${isi}_${sub_index_sppb[isi]}">PPh</option>
																<option value="tanpa_pajak_sppb_${isi}_${sub_index_sppb[isi]}">Tanpa Pajak</option>
															</select>
														</div>
													</div>
													<div class="form-group row" id="wapu_pph_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh</label>
														<div class="col-sm-9">
															<select name="uraian_sppb[${isi}][${sub_index_sppb}][pilih_wapu_sppb]" id="pilih_wapu_sppb_${isi}_${sub_index_sppb[isi]}" class="form-control">
																<option value="" disabled selected>-- Pilih --</option>
																<option value="wapu_normal_sppb_${isi}_${sub_index_sppb[isi]}">Normal</option>
																<option value="wapu_pph21_a_sppb_${isi}_${sub_index_sppb[isi]}">PPh 21 (2,5%)</option>
																<option value="wapu_pph21_b_sppb_${isi}_${sub_index_sppb[isi]}">PPh 21 (7,5%)</option>
																<option value="wapu_pph21_c_sppb_${isi}_${sub_index_sppb[isi]}">PPh 21 (12,5%)</option>
																<option value="wapu_pph22_a_sppb_${isi}_${sub_index_sppb[isi]}">PPh 22</option>
																<option value="wapu_pph23_a_sppb_${isi}_${sub_index_sppb[isi]}">PPh 23 (2%)</option>
																<option value="wapu_pph23_b_sppb_${isi}_${sub_index_sppb[isi]}">PPh 23 (15%)</option>
																<option value="wapu_pph23_c_sppb_${isi}_${sub_index_sppb[isi]}">PPh 23 (0%)</option>
																<option value="wapu_pph26_a_sppb_${isi}_${sub_index_sppb[isi]}">PPh 26 (0%)</option>
																<option value="wapu_pph26_b_sppb_${isi}_${sub_index_sppb[isi]}">PPh 26 (10%)</option>
																<option value="wapu_pph26_c_sppb_${isi}_${sub_index_sppb[isi]}">PPh 26 (20%)</option>
																<option value="wapu_pasal4ayat2_sppb_${isi}_${sub_index_sppb[isi]}">Pasal 4 Ayat 2</option>
																<option value="wapu_nilai_lain_sppb_${isi}_${sub_index_sppb[isi]}">DPP Nilai Lain</option>
																<option value="wapu_manual_sppb_${isi}_${sub_index_sppb[isi]}">PPh Manual</option>
															</select>
														</div>
													</div>

													<div class="form-group row" id="waba_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh</label>
														<div class="col-sm-9">
															<select name="uraian_sppb[${isi}][${sub_index_sppb}][pilih_waba_sppb]" id="pilih_waba_sppb_${isi}_${sub_index_sppb[isi]}" class="form-control">
																<option value="" disabled selected>-- Pilih --</option>
																<option value="waba_normal_sppb_${isi}_${sub_index_sppb[isi]}">Normal</option>
																<option value="waba_pph21_a_sppb_${isi}_${sub_index_sppb[isi]}">PPh 21 (2,5%)</option>
																<option value="waba_pph21_b_sppb_${isi}_${sub_index_sppb[isi]}">PPh 21 (7,5%)</option>
																<option value="waba_pph21_c_sppb_${isi}_${sub_index_sppb[isi]}">PPh 21 (12,5%)</option>
																<option value="waba_pph22_a_sppb_${isi}_${sub_index_sppb[isi]}">PPh 22</option>
																<option value="waba_pph23_a_sppb_${isi}_${sub_index_sppb[isi]}">PPh 23 (2%)</option>
																<option value="waba_pph23_b_sppb_${isi}_${sub_index_sppb[isi]}">PPh 23 (15%)</option>
																<option value="waba_pph23_c_sppb_${isi}_${sub_index_sppb[isi]}">PPh 23 (0%)</option>
																<option value="waba_pph26_a_sppb_${isi}_${sub_index_sppb[isi]}">PPh 26 (0%)</option>
																<option value="waba_pph26_b_sppb_${isi}_${sub_index_sppb[isi]}">PPh 26 (10%)</option>
																<option value="waba_pph26_c_sppb_${isi}_${sub_index_sppb[isi]}">PPh 26 (20%)</option>
																<option value="waba_pasal4ayat2_sppb_${isi}_${sub_index_sppb[isi]}">Pasal 4 Ayat 2</option>
																<option value="waba_nilai_lain_sppb_${isi}_${sub_index_sppb[isi]}">DPP Nilai Lain</option>
																<option value="waba_manual_sppb_${isi}_${sub_index_sppb[isi]}">PPh Manual</option>
															</select>
														</div>
													</div>

													<div class="form-group row" id="pph_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh</label>
														<div class="col-sm-9">
															<select name="uraian_sppb[${isi}][${sub_index_sppb}][pilih_pph_sppb]" id="pilih_pph_sppb_${isi}_${sub_index_sppb[isi]}" class="form-control">
																<option value="" disabled selected>-- Pilih --</option>
																<option value="pph21_a_sppb_${isi}_${sub_index_sppb[isi]}">PPh 21 (2,5%)</option>
																<option value="pph21_b_sppb_${isi}_${sub_index_sppb[isi]}">PPh 21 (7,5%)</option>
																<option value="pph21_c_sppb_${isi}_${sub_index_sppb[isi]}">PPh 21 (12,5%)</option>
																<option value="pph22_a_sppb_${isi}_${sub_index_sppb[isi]}">PPh 22</option>
																<option value="pph23_a_sppb_${isi}_${sub_index_sppb[isi]}">PPh 23 (2%)</option>
																<option value="pph23_b_sppb_${isi}_${sub_index_sppb[isi]}">PPh 23 (15%)</option>
																<option value="pph23_c_sppb_${isi}_${sub_index_sppb[isi]}">PPh 23 (0%)</option>
																<option value="pph26_a_sppb_${isi}_${sub_index_sppb[isi]}">PPh 26 (0%)</option>
																<option value="pph26_b_sppb_${isi}_${sub_index_sppb[isi]}">PPh 26 (10%)</option>
																<option value="pph26_c_sppb_${isi}_${sub_index_sppb[isi]}">PPh 26 (20%)</option>
																<option value="pphpasal4_ayat2_sppb_${isi}_${sub_index_sppb[isi]}">Pasal 4 Ayat 2</option>
																<option value="pph_manual_sppb_${isi}_${sub_index_sppb[isi]}">Manual</option>
															</select>
														</div>
													</div>
													<!-- END PAJAK WAPU WABA PPH MANUAL SPPB -->
													<!-- DPP LAIN DAN MANUAL PAJAK  -->
													<div class="form-group row" id="dpp_nilai_lain_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">DPP Nilai Lain*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="1.1%" class="form-control" disabled>
															<input type="hidden" name="waba_nilai_lain" id="44" value="0.011">
														</div>
													</div>
													<div class="form-group row" id="manual_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">Manual PPh*</label>
														<div class="col-sm-2">
														<input type="Number" name="uraian_sppb[${isi}][${sub_index_sppb}][manual]" id="nominal_manual_sppb_${isi}_${sub_index_sppb[isi]}" class="form-control">

														</div>
														<span><h3>%</h3></span>
													</div>
													<!-- END DPP LAIN DAN MANUAL PAJAK  -->

													<!-- GRUP INFO PERSEN PPh-->
													<div class="form-group row" id="pph_normal_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="11%" class="form-control" disabled>
															<input type="hidden" name="input_normal_sppb_${isi}_${sub_index_sppb[isi]}" id="11" value="0.11">
														</div>
													</div>
													<div class="form-group row" id="pph21_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh 21*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="21%" class="form-control" disabled>
															<input type="hidden" name="input_pph21_sppb_${isi}_${sub_index_sppb[isi]}" id="11" value="0.11">
														</div>
													</div>
													<div class="form-group row" id="pph22_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh 22*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="22%" class="form-control" disabled>
															<input type="hidden" name="input_pph22_sppb_${isi}_${sub_index_sppb[isi]}" id="11" value="0.11">
														</div>
													</div>
													<div class="form-group row" id="pph23_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh 23*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="2%" class="form-control" disabled>
															<input type="hidden" name="input_pph23_sppb_${isi}_${sub_index_sppb[isi]}" id="pph23" value="0.02">
														</div>
													</div>
													<div class="form-group row" id="pph26_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh 26*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="26%" class="form-control" disabled>
															<input type="hidden" name="input_pph26_sppb_${isi}_${sub_index_sppb[isi]}" id="11" value="0.11">
														</div>
													</div>
													<div class="form-group row" id="pasal4_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">Pasal 4 Ayat 2*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="4%" class="form-control" disabled>
															<input type="hidden" name="input_pasal4_sppb_${isi}_${sub_index_sppb[isi]}" id="11" value="0.11">
														</div>
													</div>
													<!-- END GRUP INFO PERSEN PPh-->
													<!-- GRUP NOMINAL SPPB -->
														<div class="form-group row" id="dpp_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">DPP</label>
															<div class="col-sm-9">
															<div class="group">
																<input type="text" id="nominal_sppb_${isi}_${sub_index_sppb[isi]}" name="uraian_sppb[${isi}][${sub_index_sppb}][jumlah]" class="form-control nominal validate_sppb validate_spp_all" placeholder="Nominal PPb" autocomplete="off" required>
																<input type="hidden" id="tidak_nominal_sppb_${isi}_${sub_index_sppb[isi]}"  placeholder="Nominal tanpa titik PPb" autocomplete="off" required>
																<label class="col-sm-6 col-form-label sppbcek_dana_gagal_${isi}_${sub_index_sppb[isi]}" style="display:none;color:red;">Dana melebihi sisa RKAP</label>
																<label class="col-sm-6 col-form-label sppbcek_dana_berhasil_${isi}_${sub_index_sppb[isi]}" style="display:none;color:green;">Dana dibawah sisa RKAP</label>
															</div>
															</div>
														</div>
														<div class="form-group row" id="nominal_potongan_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">Nominal Potongan</label>
															<div class="col-sm-9">
																<input type="text" id="potongan_sppb_${isi}_${sub_index_sppb[isi]}" name="uraian_sppb[${isi}][${sub_index_sppb}][potongan]" class="form-control" readonly>
															</div>
														</div>
														<div class="form-group row" id="nominal_pph_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">PPh</label>
															<div class="col-sm-9">
																<input type="text" id="uraian_pph_sppb_${isi}_${sub_index_sppb[isi]}" name="uraian_sppb[${isi}][${sub_index_sppb}][pph]" class="form-control" readonly>
															</div>
														</div>
														<div class="form-group row" id="nominal_ppn_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">PPn</label>
															<div class="col-sm-9">
																<input type="text" id="ppn_sppb_${isi}_${sub_index_sppb[isi]}" name="uraian_sppb[${isi}][${sub_index_sppb}][pajak]" class="form-control" readonly>
															</div>
														</div>
														<div class="form-group row" id="dpp_ppn_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">DPP + PPN</label>
															<div class="col-sm-9">
																<input type="text" id="dppppn_sppb_${isi}_${sub_index_sppb[isi]}" name="uraian_sppb[${isi}][${sub_index_sppb}][dpp_ppn]" class="form-control" readonly>
															</div>
														</div>
														<div class="form-group row" id="nominal_akhir_sppb_${isi}_${sub_index_sppb[isi]}" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">Nominal Akhir</label>
															<div class="col-sm-9">
																<input type="text" id="akhir_sppb_${isi}_${sub_index_sppb[isi]}" name="uraian_sppb[${isi}][${sub_index_sppb}][total_pajak]" class="form-control" readonly>
															</div>
														</div>
													<!-- END GRUP NOMINAL SPPB -->
												</div>
												<div class="col-sm-1">
													<div class="col-sm-12" id="hapus_sub_isi_sppb_${isi}_${sub_index_sppb[isi]}" onclick="hapus_sub_isi_sppb(${isi},${sub_index_sppb[isi]},'ckeditor_${isi}_${sub_index_sppb[isi]}')" style="margin-bottom: 10px">
														<button type="button" class="btn btn-danger btn-sm">X</button>
													</div>
														<div class="col-sm-12" id="tambah_sub_isi_sppb_${isi}_${sub_index_sppb[isi]}" onclick="tambah_sub_isi_sppb(${isi})" style="margin-bottom: 10px">
															<button type="button" class="btn btn-success btn-sm">+</button>
														</div>
													</div>
												</div>
											</div>`);

            // CKEDITOR.inline('ckeditor_' + isi + '_' + sub_index_sppb[isi]);
            InlineEditor
                .create(document.querySelector('#ckeditor_' + isi + '_' + sub_index_sppb[isi]))
                .catch(error => {
                    console.error(error);
                });
            var jenis_spp = $('#jenis_spp').val();
            //("spp : "+jenis_spp);
            if (jenis_spp === 'karyawan') {
                $('.formInputPajakSppb').hide();
                $('#dpp_sppb_' + isi + '_' + sub_index_sppb[isi]).show();
                //document.getElementById('pajak_'+isi+'_'+sub_index_sppb[isi]).className = "form-control";
            } else {
                $('.formInputPajakSppn').show();
                //document.getElementById('pajak_'+isi+'_'+sub_index_sppb[isi]).className = "validate_sppb validate_spp_all";
            }
            $('.nominal').mask('0.000.000.000.000.000.000', {
                reverse: true
            });
            // 				);
            var inputs = document.getElementsByClassName("validate_sppb");
            if (inputs) {
                for (var i = 0; i < inputs.length; i++) {
                    inputs[i].addEventListener("change", validateInput);
                    inputs[i].addEventListener("keyup", validateInput);
                    inputs[i].addEventListener("focus", validateInput);
                }
            }
            for (var i in CKEDITOR.instances) {
                if (i.substring(0, 9) == "ckeditor_") {
                    CKEDITOR.instances[i].on('change', function() {
                        var urai = document.getElementById(this.name).parentElement;
                        if (this.getData().replace(/<[^>]+>/g, '') == "") {
                            urai.style.cssText =
                                "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                        } else {
                            urai.style.cssText =
                                "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                        }
                    });
                }
            }

            var sub = sub_index_sppb[isi];
            // $( "#jumlah_sppb_"+isi+"_"+sub).on('keyup',function(e) {
            // 	console.log("Sub Isi - #jumlah_sppb_"+isi+"_"+sub);
            // 	jum_nom[isi][sub] = this.value.replace(/[^\d,]/g, "");
            // 	var jum_nom_total = 0;
            // 	for (let i = 1; i <= sub_index_sppb[isi]; i++) {
            // 		var jum_nom_value = $("#jumlah_sppb_"+isi+"_"+i) ? $("#jumlah_sppb_"+isi+"_"+i).val().replace(/[^\d,]/g, "") : 0;
            // 		jum_nom_total = jum_nom_value ? parseInt(jum_nom_total) + parseInt(jum_nom_value) : jum_nom_total;
            // 		console.log(jum_nom_total);
            // 	}
            // 	if (jum_nom_total > sisa[isi]) {
            // 		$('.cek_dana_gagal_'+isi+'_'+sub).css('display','block');
            // 		$('.cek_dana_berhasil_'+isi+'_'+sub).css('display','none');
            // 		// $("#simpan").prop("disabled", true);
            // 	}else{
            // 		$('.cek_dana_berhasil_'+isi+'_'+sub).css('display','block');
            // 		$('.cek_dana_gagal_'+isi+'_'+sub).css('display','none');
            // 		// $("#simpan").prop("disabled", false);
            // 	}
            // });
            set_listener_dpp();
        }

        function hapus_sub_isi_sppb(isi, sub_isi, instance) {
            $('#sub_isi_sppb_' + isi + '_' + sub_isi).remove();
            CKEDITOR.instances[instance].destroy();
            set_listener_dpp();
        }

        function tambah_isi_sppn() {
            var bagianid = "{{ $bagianid }}";
            index_sppn++;
            sub_index_sppn[index_sppn] = 1;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // $.ajax({
            //     type: 'POST',
            //     url: "{{ route('mas_rek_t') }}",
            //     success: function(data) {
            //         //console.log(data.rekening);

            //         $.each(data.rekening, function(no, value) {
            //             $("#kode_kbb_sppn_" + index_sppn).append('<option value="' + value
            //                 .master_rekening_id + '">' + value.master_rekening_kode_kbb +
            //                 '</option>');
            //         });
            //         $.each(data.rekening, function(no, value) {
            //             $("#sap_vendor_sppn_" + index_sppn).append('<option value="' + value
            //                 .master_rekening_id + '">' + '(' + value.master_rekening_kode_sap +
            //                 ')' + value.master_rekening_keterangan + '</option>');
            //         });
            //         $("#kode_kbb_sppn_" + index_sppn).selectpicker('refresh');
            //         $("#sap_vendor_sppn_" + index_sppn).selectpicker('refresh');

            //     }
            // });
            $('#tab-isi-sppn').append(`<div id="isi_sppn_${index_sppn}" class="col-sm-12">
											<div  style="padding-bottom: 10px; margin-bottom: 20px; border-bottom: 1px solid #eaeaea;">
												<font size="4" style="margin-right: 20px">Isi ${index_sppn}. </font>
												<button type="button" class="btn btn-info btn-sm" onclick="tambah_isi_sppn()">+</button>
												<button type="button" class="btn btn-danger btn-sm" onclick="hapus_isi_sppn(${index_sppn},'ckeditors_${index_sppn}_1')">X</button>
											</div>

											<div class="col-sm-5">
												<!-- <div class="form-group row">
													<label class="col-sm-3 col-form-label">Kode KBB *</label>
													<div class="col-sm-9">
														<div class="row-fluid" id="parent_kbb_sppn_${index_sppn}" >
														<select class="selectpicker slct_sppn"  data-live-search="true" data-dropup-auto="false" id="kode_kbb_sppn_${index_sppn}" data-width="100%" name="isi_sppn[${index_sppn}][kode_kbb]" data-size="7" onchange="pilih_rekening_sppn(${index_sppn},'kode_kbb_sppn_')">
															<option disabled selected>---Pilih KBB---</option>
														</select>
														</div>

													</div>
												</div> -->
												<div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">Kode SAP *</label>
                                                    <label class="col-sm-9">
                                                        <select class="form-control validate_sppn validate_spp_all" id="jenis_sap_sppn_${index_sppn}" onchange="js_sppn(${index_sppn})" name="isi_sppn[${index_sppn}][jenis_sap]">
                                                            <option value="" disabled selected>-- Pilih Jenis Kode SAP --</option>
                                                            <option value="vendor">Nomor Vendor</option>
                                                            <option value="gl">Nomor GL</option>
                                                            <option value="customer">Nomor Customer</option>
                                                            {{-- @if (in_array($bagianid, [124, 126, 127]))
                                                                <option value="customer">Nomor Customer</option>
                                                            @endif --}}
                                                        </select>
                                                    </label>
                                                    <label class="col-sm-3 col-form-label"></label>

                                                    <div id="nomor_vendor_sppn_${index_sppn}" style="display:none">
                                                        <div class="col-sm-9">
                                                            <div class="row-fluid" id="parent_kbb_sppn_${index_sppn}">
                                                                <select class="select2-vendor sap-vendor-sppn" id="sap_vendor_sppn_${index_sppn}" name="isi_sppn[${index_sppn}][vendor]" onchange="pilih_rekening_sppn(${index_sppn},'sap_vendor_sppn_')">
                                                                    <option disabled selected>---Pilih SAP---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="nomor_gl_sppn_${index_sppn}" style="display:none">
                                                        <div class="col-sm-9">
                                                            <div class="row-fluid" id="parent_kbb_sppn_${index_sppn}">
                                                                <select class="select2-gl" id="sap_gl_sppn_${index_sppn}" name="isi_sppn[${index_sppn}][gl]" onchange="pilih_rekening_sppn(${index_sppn},'sap_gl_sppn_')">
                                                                    <option value="" disabled selected>-- Pilih Kode GL --</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <label class="col-sm-12 col-form-label mt-2"></label>
                                                        <label class="col-sm-3 col-form-label">RKAP</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control budget_glsppn_${index_sppn} nominal" placeholder="Budget" autocomplete="off" readonly>
                                                            <input type="hidden" class="budget_glsppn_hide_${index_sppn}" name="budget">
                                                        </div>
                                                        <label class="col-sm-12 col-form-label mt-2"></label>
                                                        <label class="col-sm-3 col-form-label">Realisasi</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control realisasisppn_${index_sppn} nominal" placeholder="Realisasi" autocomplete="off" readonly>
                                                            <input type="hidden" class="budget_glsppn_hide" name="budget">
                                                        </div>
                                                        <label class="col-sm-12 col-form-label mt-2"></label>
                                                        <label class="col-sm-3 col-form-label">On Process</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control onprosessppn_${index_sppn} nominal" placeholder="Onproses" autocomplete="off" readonly>
                                                            <input type="hidden" class="budget_glsppn_hide" name="budget">
                                                        </div>
                                                        <label class="col-sm-12 col-form-label mt-2"></label>
                                                        <label class="col-sm-3 col-form-label">Sisa</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control sisasppn_${index_sppn} nominal" placeholder="Sisa" autocomplete="off" readonly>
                                                            <input type="hidden" class="budget_glsppn_hide" name="budget">
                                                        </div>
                                                        <div class="col-sm-9">
                                                            <input type="text" style="display:none;" id="sap_gl_sppn_id_${index_sppn}" name="isi_sppn[${index_sppn}][gl]" class="form-control" onclick="kode_gl_sppn(1)" placeholder="Kode SAP (Nomor GL)" autocomplete="off" required>
                                                        </div>
                                                    </div>
                                                    <div id="nomor_customer_sppn_${index_sppn}" style="display:none">
                                                        <div class="col-sm-9">
                                                            <div class="row-fluid" id="parent_kbb_sppn_${index_sppn}">
                                                                 <select class="select2-customer slct_sppn" id="sap_customer_sppn_${index_sppn}" name="isi_sppn[${index_sppn}][customer]" onchange="pilih_rekening_sppn(${index_sppn}, 'sap_customer_sppn_')">
                                                                    <option disabled selected>-- Pilih Kode Customer --</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- ${bagianid === 124 || bagianid === 126 || bagianid === 127 ? `
                                                    <div id="nomor_customer_sppn_${index_sppn}" style="display:none">
                                                        <div class="col-sm-9">
                                                            <div class="row-fluid" id="parent_kbb_sppn_${index_sppn}">
                                                                <select class="selectpicker slct_sppn" data-live-search="true"
                                                                    data-dropup-auto="false" data-width="100%" id="sap_customer_sppn_${index_sppn}"
                                                                    data-size="7" name="isi_sppn[${index_sppn}][customer]"
                                                                    onchange="pilih_rekening_sppn(${index_sppn}, 'sap_customer_sppn_')">
                                                                    <option disabled selected>-- Pilih Kode Customer --</option>
                                                                    ${customer.map(cs => `<option value="${cs.master_customer_id}">${cs.master_customer_kode_sap} (${cs.master_customer_nama})</option>`).join('')}
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    ` : ''} --}}
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
														<select class="form-control select2-costcenter" id="select_cost_center_sppn_${index_sppn}" name="isi_sppn[${index_sppn}][cost_center]">
															<option value="" disabled selected>-- Pilih Cost Center --</option>
															{{-- @foreach ($costcenter as $cost)
																<option value="{{ $cost->master_cost_center_id }}">{{ $cost->master_cost_center_kode }} {{ $cost->master_cost_center_keterangan }}</option>
															@endforeach --}}
														</select>
													</div>

													<div class="col-sm-9" id="profit_center_sppn_${index_sppn}" style="display: none">
														<select class="form-control select2-profitcenter" id="select_profit_center_sppn_${index_sppn}" name="isi_sppn[${index_sppn}][profit_center]">
															<option value="" disabled selected>-- Pilih Profit Center --</option>
															{{-- @foreach ($profitcenter as $profit)
																<option value="{{ $profit->master_profit_center_id }}">{{ $profit->master_profit_center_kode }} ({{ $profit->master_profit_unit }})</option>
															@endforeach --}}
														</select>
													</div>
													{{-- @if ($bagian_karyawan->pemisah_keb_bag == 1)
													<label class="col-sm-9">
														<input type="text"  class="form-control" value="Cost Center" readonly>
														<input type="hidden"  name="isi_sppn[${index_sppn}][jenis_center]" value="cost_center" readonly>
													</label>
													<label class="col-sm-3 col-form-label"></label>
													<div class="col-sm-9">
														@foreach ($cost_center_id as $ccid)

														<input type="text"   class="form-control" value="{{ $ccid->master_cost_center_kode }} {{ $ccid->master_cost_center_keterangan }}" readonly>
														<input type="hidden"   name="isi_sppn[${index_sppn}][cost_center]" value="{{ $ccid->master_cost_center_id }}" readonly>
														@endforeach
													</div>
													@elseif ($bagian_karyawan->pemisah_keb_bag == 2)
													<label class="col-sm-9">
														<input type="text"  class="form-control" value="Profit Center" readonly>
														<input type="hidden"  name="isi_sppn[${index_sppn}][jenis_center]" value="profit_center" readonly>
													</label>
													<label class="col-sm-3 col-form-label"></label>
													<div class="col-sm-9">
														@foreach ($cost_center_id as $ccid)

														<input type="text"   class="form-control" value="{{ $ccid->master_profit_center_kode }} {{ $ccid->master_profit_unit }}" readonly>
														<input type="hidden"   name = "isi_sppn[${index_sppn}][profit_center]" value="{{ $ccid->master_profit_center_id }}" readonly>
														@endforeach
													</div>
													@endif --}}

												</div>

												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Cash Flow*</label>
													<div class="col-sm-9">
														<select class="form-control validate_sppn validate_spp_all select2-cashflow" id="cash_flow_sppn_${index_sppn}" name="isi_sppn[${index_sppn}][cash_flow]">
															<option value="" disabled selected>-- Pilih Cash Flow --</option>
                                                            @foreach ($cashflow as $cash)
										<option value="{{ $cash->master_cash_flow_id }}">{{ $cash->master_cash_flow_key }} {{ $cash->master_cash_flow_keterangan }}</option>
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
																<div id="uraian_sppn_${index_sppn}_1" style="height:auto;min-height:100px">
																	<!-- <input type="hidden" name="uraian_sppn[0][0][uraian]" id="uraian_sppn_value_${index_sppn}_1"> -->
																	<textarea class="form-control" id="ckeditors_${index_sppn}_1" name="uraian_sppn[${index_sppn}][1][ket]"></textarea>
																</div>
															</div>
														</div>
													</div>
													<!-- PAJAK WAPU WABA PPH MANUAL SPPN -->
													<div class="form-group row formInputPajakSppn" id="pajak_${index_sppn}_1">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">Pajak*</label>
														<div class="col-sm-9">
															<select name="uraian_sppn[${index_sppn}][1][type_pajak_sppn]" id="pilih_pajak_sppn_${index_sppn}_1" class="form-control validate_sppn" onchange="jenis_pajak_sppn(${index_sppn},1);">
																<option value="" selected disabled>-- Pilih Pajak --</option>
																<option value="wapu_sppn_${index_sppn}_1">WAPU</option>
																<option value="waba_sppn_${index_sppn}_1">WABA</option>
																<option value="pph_sppn_${index_sppn}_1">PPh</option>
																<option value="tanpa_pajak_sppn_${index_sppn}_1">Tanpa Pajak</option>

															</select>
														</div>
													</div>
													<div class="form-group row" id="wapu_pph_sppn_${index_sppn}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh</label>
														<div class="col-sm-9">
															<select name="uraian_sppn[${index_sppn}][1][pilih_wapu_sppn]" id="pilih_wapu_sppn_${index_sppn}_1" class="form-control">
																<option value="" disabled selected>-- Pilih --</option>
																<option value="wapu_normal_sppn_${index_sppn}_1">Normal</option>
																<option value="wapu_pph21_a_sppn_${index_sppn}_1">PPh 21 (2,5%)</option>
																<option value="wapu_pph21_b_sppn_${index_sppn}_1">PPh 21 (7,5%)</option>
																<option value="wapu_pph21_c_sppn_${index_sppn}_1">PPh 21 (12,5%)</option>
																<option value="wapu_pph22_a_sppn_${index_sppn}_1">PPh 22</option>
																<option value="wapu_pph23_a_sppn_${index_sppn}_1">PPh 23 (2%)</option>
																<option value="wapu_pph23_b_sppn_${index_sppn}_1">PPh 23 (15%)</option>
																<option value="wapu_pph23_c_sppn_${index_sppn}_1">PPh 23 (0%)</option>
																<option value="wapu_pph26_a_sppn_${index_sppn}_1">PPh 26 (0%)</option>
																<option value="wapu_pph26_b_sppn_${index_sppn}_1">PPh 26 (10%)</option>
																<option value="wapu_pph26_c_sppn_${index_sppn}_1">PPh 26 (20%)</option>
																<option value="wapu_pasal4ayat2_sppn_${index_sppn}_1">Pasal 4 Ayat 2</option>
																<option value="wapu_nilai_lain_sppn_${index_sppn}_1">DPP Nilai Lain</option>
																<option value="wapu_manual_sppn_${index_sppn}_1">PPh Manual</option>
															</select>
														</div>
													</div>

													<div class="form-group row" id="waba_sppn_${index_sppn}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh</label>
														<div class="col-sm-9">
															<select name="uraian_sppn[${index_sppn}][1][pilih_waba_sppn]" id="pilih_waba_sppn_${index_sppn}_1" class="form-control">
																<option value="" disabled selected>-- Pilih --</option>
																<option value="waba_normal_sppn_${index_sppn}_1">Normal</option>
																<option value="waba_pph21_a_sppn_${index_sppn}_1">PPh 21 (2,5%)</option>
																<option value="waba_pph21_b_sppn_${index_sppn}_1">PPh 21 (7,5%)</option>
																<option value="waba_pph21_c_sppn_${index_sppn}_1">PPh 21 (12,5%)</option>
																<option value="waba_pph22_a_sppn_${index_sppn}_1">PPh 22</option>
																<option value="waba_pph23_a_sppn_${index_sppn}_1">PPh 23 (2%)</option>
																<option value="waba_pph23_b_sppn_${index_sppn}_1">PPh 23 (15%)</option>
																<option value="waba_pph23_c_sppn_${index_sppn}_1">PPh 23 (0%)</option>
																<option value="waba_pph26_a_sppn_${index_sppn}_1">PPh 26 (0%)</option>
																<option value="waba_pph26_b_sppn_${index_sppn}_1">PPh 26 (10%)</option>
																<option value="waba_pph26_c_sppn_${index_sppn}_1">PPh 26 (20%)</option>
																<option value="waba_pasal4ayat2_sppn_${index_sppn}_1">Pasal 4 Ayat 2</option>
																<option value="waba_nilai_lain_sppn_${index_sppn}_1">DPP Nilai Lain</option>
																<option value="waba_manual_sppn_${index_sppn}_1">PPh Manual</option>
															</select>
														</div>
													</div>

													<div class="form-group row" id="pph_sppn_${index_sppn}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh</label>
														<div class="col-sm-9">
															<select name="uraian_sppn[${index_sppn}][1][pilih_pph_sppn]" id="pilih_pph_sppn_${index_sppn}_1" class="form-control">
																<option value="" disabled selected>-- Pilih --</option>
																<option value="pph21_a_sppn_${index_sppn}_1">PPh 21 (2,5%)</option>
																<option value="pph21_b_sppn_${index_sppn}_1">PPh 21 (7,5%)</option>
																<option value="pph21_c_sppn_${index_sppn}_1">PPh 21 (12,5%)</option>
																<option value="pph22_a_sppn_${index_sppn}_1">PPh 22</option>
																<option value="pph23_a_sppn_${index_sppn}_1">PPh 23 (2%)</option>
																<option value="pph23_b_sppn_${index_sppn}_1">PPh 23 (15%)</option>
																<option value="pph23_c_sppn_${index_sppn}_1">PPh 23 (0%)</option>
																<option value="pph26_a_sppn_${index_sppn}_1">PPh 26 (0%)</option>
																<option value="pph26_b_sppn_${index_sppn}_1">PPh 26 (10%)</option>
																<option value="pph26_c_sppn_${index_sppn}_1">PPh 26 (20%)</option>
																<option value="pphpasal4_ayat2_sppn_${index_sppn}_1">Pasal 4 Ayat 2</option>
																<option value="pph_manual_sppn_${index_sppn}_1">Manual</option>
															</select>
														</div>
													</div>
													<!-- END PAJAK WAPU WABA PPH MANUAL SPPB -->
													<!-- DPP LAIN DAN MANUAL PAJAK  -->
													<div class="form-group row" id="dpp_nilai_lain_sppn_${index_sppn}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">DPP Nilai Lain*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="1.1%" class="form-control" disabled>
															<input type="hidden" name="waba_nilai_lain" id="44" value="0.011">
														</div>
													</div>
													<div class="form-group row" id="manual_sppn_${index_sppn}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">Manual PPh*</label>
														<div class="col-sm-2">
															<input type="Number" name="uraian_sppn[${index_sppn}][1][manual]" id="input_manual_${index_sppn}_1" class="form-control">
														</div>
														<span><h3>%</h3></span>
													</div>
													<!-- END DPP LAIN DAN MANUAL PAJAK  -->

													<!-- GRUP INFO PERSEN PPh-->
													<div class="form-group row" id="pph_normal_sppn_${index_sppn}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="11%" class="form-control" disabled>
															<input type="hidden" name="input_normal_sppn_${index_sppn}_1" id="11" value="0.11">
														</div>
													</div>
													<div class="form-group row" id="pph21_sppn_${index_sppn}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh 21*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="21%" class="form-control" disabled>
															<input type="hidden" name="input_pph21_sppn_${index_sppn}_1" id="11" value="0.11">
														</div>
													</div>
													<div class="form-group row" id="pph22_sppn_${index_sppn}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh 22*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="22%" class="form-control" disabled>
															<input type="hidden" name="input_pph22_sppn_${index_sppn}_1" id="11" value="0.11">
														</div>
													</div>
													<div class="form-group row" id="pph23_sppn_${index_sppn}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh 23*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="2%" class="form-control" disabled>
															<input type="hidden" name="input_pph23_sppn_${index_sppn}_1" id="pph23" value="0.02">
														</div>
													</div>
													<div class="form-group row" id="pph26_sppn_${index_sppn}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">PPh 26*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="26%" class="form-control" disabled>
															<input type="hidden" name="input_pph26_sppn_${index_sppn}_1" id="11" value="0.11">
														</div>
													</div>
													<div class="form-group row" id="pasal4_sppn_${index_sppn}_1" style="display:none ;">
														<label class="col-sm-1 col-form-label"></label>
														<label class="col-sm-2 col-form-label">Pasal 4 Ayat 2*</label>
														<div class="col-sm-9">
															<input type="text" placeholder="4%" class="form-control" disabled>
															<input type="hidden" name="input_pasal4_sppn_${index_sppn}_1" id="11" value="0.11">
														</div>
													</div>
													<!-- END GRUP INFO PERSEN PPh-->
													<!-- GRUP NOMINAL SPPB -->
														<div class="form-group row" id="dpp_sppn_${index_sppn}_1" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">DPP</label>
															<div class="col-sm-9">
															<div class="group">
																<input type="text" id="nominal_sppn_${index_sppn}_1" name="uraian_sppn[${index_sppn}][1][jumlah]" class="form-control nominal validate_sppn validate_spp_all" placeholder="Nominal PPn" autocomplete="off" required>
																<input type="hidden" id="tidak_nominal_sppn_${index_sppn}_1"  placeholder="Nominal tanpa titik PPn" autocomplete="off" required>
																<label class="col-sm-6 col-form-label sppncek_dana_gagal_${index_sppn}_1" style="display:none;color:red;">Dana melebihi sisa RKAP</label>
																<label class="col-sm-6 col-form-label sppncek_dana_berhasil_${index_sppn}_1" style="display:none;color:green;">Dana dibawah sisa RKAP</label>
															</div>
															</div>
														</div>
														<div class="form-group row" id="nominal_potongan_sppn_${index_sppn}_1" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">Nominal Potongan</label>
															<div class="col-sm-9">
																<input type="text" id="potongan_sppn_${index_sppn}_1" name="uraian_sppn[${index_sppn}][1][potongan]" class="form-control" readonly>
															</div>
														</div>
														<div class="form-group row" id="nominal_pph_sppn_${index_sppn}_1" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">PPh</label>
															<div class="col-sm-9">
																<input type="text" id="uraian_pph_sppn_${index_sppn}_1" name="uraian_sppn[${index_sppn}][1][pph]" class="form-control" readonly>
															</div>
														</div>
														<div class="form-group row" id="nominal_ppn_sppn_${index_sppn}_1" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">PPn</label>
															<div class="col-sm-9">
																<input type="text" id="ppn_sppn_${index_sppn}_1" name="uraian_sppn[${index_sppn}][1][pajak]" class="form-control" readonly>
															</div>
														</div>
														<div class="form-group row" id="dpp_ppn_sppn_${index_sppn}_1" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">DPP + PPN</label>
															<div class="col-sm-9">
																<input type="text" id="dppppn_sppn_${index_sppn}_1" name="uraian_sppn[${index_sppn}][1][dpp_ppn]" class="form-control" readonly>
															</div>
														</div>
														<div class="form-group row" id="nominal_akhir_sppn_${index_sppn}_1" style="display:none ;">
															<label class="col-sm-1 col-form-label"></label>
															<label class="col-sm-2 col-form-label">Nominal Akhir</label>
															<div class="col-sm-9">
																<input type="text" id="akhir_sppn_${index_sppn}_1" name="uraian_sppn[${index_sppn}][1][total_pajak]" class="form-control" readonly>
															</div>
														</div>
													<!-- END GRUP NOMINAL SPPB -->
												</div>
												<div class="col-sm-1">
													<div class="col-sm-12" style="margin-bottom: 10px">
														<button type="button" class="btn btn-success btn-sm" onclick="tambah_sub_isi_sppn(${index_sppn},1)">+</button>
													</div>
												</div>
											</div>
										</div>`);
            // CKEDITOR.inline('ckeditors_' + index_sppn + '_1');
            // initializeSelectpicker($(`#sap_vendor_sppn_${index_sppn}`));
            // initializeSelectPickerGl($(`#sap_gl_sppn_${index_sppn}`), index_sppn, false);
            refreshSelect2('vendor');
            refreshSelect2('gl');
            refreshSelect2('customer');
            refreshSelect2('cost_center');
            refreshSelect2('profit_center');
            refreshSelect2('cashflow');

            InlineEditor
                .create(document.querySelector('#ckeditors_' + index_sppn + '_1'))
                .catch(error => {
                    console.error(error);
                });

            var inputs = document.getElementsByClassName("validate_sppn");
            if (inputs) {
                for (var i = 0; i < inputs.length; i++) {
                    inputs[i].addEventListener("change", validateInput);
                    inputs[i].addEventListener("keyup", validateInput);
                    inputs[i].addEventListener("focus", validateInput);

                }
            }

            for (var i in CKEDITOR.instances) {
                if (i.substring(0, 9) == "ckeditors") {
                    CKEDITOR.instances[i].on('change', function() {
                        var urai = document.getElementById(this.name).parentElement;
                        if (this.getData().replace(/<[^>]+>/g, '') == "") {
                            urai.style.cssText =
                                "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                        } else {
                            urai.style.cssText =
                                "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                        }
                    })
                }
            }
            $('.nominal').mask('0.000.000.000.000.000.000', {
                reverse: true
            });
            $('.selectpicker').selectpicker();
            // $( "#jumlah_sppn_"+index_sppn+"_1").on('keyup',function(e) {
            // 	console.log("Isi - #jumlah_sppn_"+index_sppn+"_1");
            // 	jum_nom[index_sppn] = [];
            // 	jum_nom[index_sppn][1] = this.value.replace(/[^\d,]/g, "");
            // 	var jum_nom_total = 0;
            // 	for (let i = 1; i <= sub_index_sppn[index_sppn]; i++) {
            // 		var jum_nom_value = $("#jumlah_sppn_"+index_sppn+"_"+i) ? $("#jumlah_sppn_"+index_sppn+"_"+i).val().replace(/[^\d,]/g, "") : 0;
            // 		jum_nom_total = jum_nom_value ? parseInt(jum_nom_total) + parseInt(jum_nom_value) : jum_nom_total;
            // 		console.log(jum_nom_total);
            // 	}
            // 	if (jum_nom_total > sisa[index_sppn]) {
            // 		$('.sppncek_dana_gagal_'+index_sppn+'_1').css('display','block');
            // 		$('.sppncek_dana_berhasil_'+index_sppn+'_1').css('display','none');
            // 		// $("#simpan").prop("disabled", true);
            // 	}else{
            // 		$('.sppncek_dana_berhasil_'+index_sppn+'_1').css('display','block');
            // 		$('.sppncek_dana_gagal_'+index_sppn+'_1').css('display','none');
            // 		// $("#simpan").prop("disabled", false);
            // 	}
            // });
            set_listener_dpp();
        }

        function hapus_isi_sppn(isi, instance) {
            $("#isi_sppn_" + isi).remove();
            CKEDITOR.instances[instance].destroy();
            set_listener_dpp();
        }

        function tambah_sub_isi_sppn(isi) {
            sub_index_sppn[isi]++;
            console.log(sub_index_sppn[isi])
            $('#isi_sppn_' + isi).append(`<div id="sub_isi_sppn_${isi}_${sub_index_sppn[isi]}">
			<div class="col-sm-5"></div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <label class="col-sm-1 col-form-label">${sub_index_sppn[isi]}. </label>
                        <label class="col-sm-2 col-form-label">Uraian*</label>
                        <div class="col-sm-9">
                            <div style="border: 1px solid hsla(0, 0%, 0%, 0.15);">
                                <div id="uraian_sppn_${isi}_${sub_index_sppn[isi]}" style="height:auto;min-height:100px">
                                    <!-- <input type="hidden" name="uraian_sppn[0][0][uraian]" id="uraian_sppn_value${isi}_${sub_index_sppn[isi]}"> -->
                                    <textarea class="form-control" id="ckeditors_${isi}_${sub_index_sppn[isi]}" name="uraian_sppn[${isi}][${sub_index_sppn}][ket]"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- PAJAK WAPU WABA PPH MANUAL SPPB -->
                    <div class="form-group row formInputPajakSppn" id="pajak_${isi}_${sub_index_sppn[isi]}">
                        <label class="col-sm-1 col-form-label"></label>
                        <label class="col-sm-2 col-form-label">Pajak*</label>
                        <div class="col-sm-9">
                            <select name="uraian_sppn[${isi}][${sub_index_sppn}][type_pajak_sppn]" id="pilih_pajak_sppn_${isi}_${sub_index_sppn[isi]}" class="validasi_pajak form-control validate_sppn validate_spp_all" onchange="jenis_pajak_sppn(${index_sppn},${sub_index_sppn[index_sppn]});">
                                <option value="" selected disabled>-- Pilih Pajak --</option>
                                <option value="wapu_sppn_${isi}_${sub_index_sppn[isi]}">WAPU</option>
                                <option value="waba_sppn_${isi}_${sub_index_sppn[isi]}">WABA</option>
                                <option value="pph_sppn_${isi}_${sub_index_sppn[isi]}">PPh</option>
                                <option value="tanpa_pajak_sppn_${isi}_${sub_index_sppn[isi]}">Tanpa Pajak</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="wapu_pph_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none ;">
                        <label class="col-sm-1 col-form-label"></label>
                        <label class="col-sm-2 col-form-label">PPh</label>
                        <div class="col-sm-9">
                            <select name="uraian_sppn[${isi}][${sub_index_sppn}][pilih_wapu_sppn]" id="pilih_wapu_sppn_${isi}_${sub_index_sppn[isi]}" class="form-control">
                                <option value="" disabled selected>-- Pilih --</option>
                                <option value="wapu_normal_sppn_${isi}_${sub_index_sppn[isi]}">Normal</option>
                                <option value="wapu_pph21_a_sppn_${isi}_${sub_index_sppn[isi]}">PPh 21 (2,5%)</option>
                                <option value="wapu_pph21_b_sppn_${isi}_${sub_index_sppn[isi]}">PPh 21 (7,5%)</option>
                                <option value="wapu_pph21_c_sppn_${isi}_${sub_index_sppn[isi]}">PPh 21 (12,5%)</option>
                                <option value="wapu_pph22_a_sppn_${isi}_${sub_index_sppn[isi]}">PPh 22</option>
                                <option value="wapu_pph23_a_sppn_${isi}_${sub_index_sppn[isi]}">PPh 23 (2%)</option>
                                <option value="wapu_pph23_b_sppn_${isi}_${sub_index_sppn[isi]}">PPh 23 (15%)</option>
                                <option value="wapu_pph23_c_sppn_${isi}_${sub_index_sppn[isi]}">PPh 23 (0%)</option>
                                <option value="wapu_pph26_a_sppn_${isi}_${sub_index_sppn[isi]}">PPh 26 (0%)</option>
                                <option value="wapu_pph26_b_sppn_${isi}_${sub_index_sppn[isi]}">PPh 26 (10%)</option>
                                <option value="wapu_pph26_c_sppn_${isi}_${sub_index_sppn[isi]}">PPh 26 (20%)</option>
                                <option value="wapu_pasal4ayat2_sppn_${isi}_${sub_index_sppn[isi]}">Pasal 4 Ayat 2</option>
                                <option value="wapu_nilai_lain_sppn_${isi}_${sub_index_sppn[isi]}">DPP Nilai Lain</option>
                                <option value="wapu_manual_sppn_${isi}_${sub_index_sppn[isi]}">PPh Manual</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row" id="waba_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none ;">
                        <label class="col-sm-1 col-form-label"></label>
                        <label class="col-sm-2 col-form-label">PPh</label>
                        <div class="col-sm-9">
                            <select name="uraian_sppn[${isi}][${sub_index_sppn}][pilih_waba_sppn]" id="pilih_waba_sppn_${isi}_${sub_index_sppn[isi]}" class="form-control">
                                <option value="" disabled selected>-- Pilih --</option>
                                <option value="waba_normal_sppn_${isi}_${sub_index_sppn[isi]}">Normal</option>
                                <option value="waba_pph21_a_sppn_${isi}_${sub_index_sppn[isi]}">PPh 21 (2,5%)</option>
                                <option value="waba_pph21_b_sppn_${isi}_${sub_index_sppn[isi]}">PPh 21 (7,5%)</option>
                                <option value="waba_pph21_c_sppn_${isi}_${sub_index_sppn[isi]}">PPh 21 (12,5%)</option>
                                <option value="waba_pph22_a_sppn_${isi}_${sub_index_sppn[isi]}">PPh 22</option>
                                <option value="waba_pph23_a_sppn_${isi}_${sub_index_sppn[isi]}">PPh 23 (2%)</option>
                                <option value="waba_pph23_b_sppn_${isi}_${sub_index_sppn[isi]}">PPh 23 (15%)</option>
                                <option value="waba_pph23_c_sppn_${isi}_${sub_index_sppn[isi]}">PPh 23 (0%)</option>
                                <option value="waba_pph26_a_sppn_${isi}_${sub_index_sppn[isi]}">PPh 26 (0%)</option>
                                <option value="waba_pph26_b_sppn_${isi}_${sub_index_sppn[isi]}">PPh 26 (10%)</option>
                                <option value="waba_pph26_c_sppn_${isi}_${sub_index_sppn[isi]}">PPh 26 (20%)</option>
                                <option value="waba_pasal4ayat2_sppn_${isi}_${sub_index_sppn[isi]}">Pasal 4 Ayat 2</option>
                                <option value="waba_nilai_lain_sppn_${isi}_${sub_index_sppn[isi]}">DPP Nilai Lain</option>
                                <option value="waba_manual_sppn_${isi}_${sub_index_sppn[isi]}">PPh Manual</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row" id="pph_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none ;">
                        <label class="col-sm-1 col-form-label"></label>
                        <label class="col-sm-2 col-form-label">PPh</label>
                        <div class="col-sm-9">
                            <select name="uraian_sppn[${isi}][${sub_index_sppn}][pilih_pph_sppn]" id="pilih_pph_sppn_${isi}_${sub_index_sppn[isi]}" class="form-control">
                                <option value="" disabled selected>-- Pilih --</option>
                                <option value="pph21_a_sppn_${isi}_${sub_index_sppn[isi]}">PPh 21 (2,5%)</option>
                                <option value="pph21_b_sppn_${isi}_${sub_index_sppn[isi]}">PPh 21 (7,5%)</option>
                                <option value="pph21_c_sppn_${isi}_${sub_index_sppn[isi]}">PPh 21 (12,5%)</option>
                                <option value="pph22_a_sppn_${isi}_${sub_index_sppn[isi]}">PPh 22</option>
                                <option value="pph23_a_sppn_${isi}_${sub_index_sppn[isi]}">PPh 23 (2%)</option>
                                <option value="pph23_b_sppn_${isi}_${sub_index_sppn[isi]}">PPh 23 (15%)</option>
                                <option value="pph23_c_sppn_${isi}_${sub_index_sppn[isi]}">PPh 23 (0%)</option>
                                <option value="pph26_a_sppn_${isi}_${sub_index_sppn[isi]}">PPh 26 (0%)</option>
                                <option value="pph26_b_sppn_${isi}_${sub_index_sppn[isi]}">PPh 26 (10%)</option>
                                <option value="pph26_c_sppn_${isi}_${sub_index_sppn[isi]}">PPh 26 (20%)</option>
                                <option value="pphpasal4_ayat2_sppn_${isi}_${sub_index_sppn[isi]}">Pasal 4 Ayat 2</option>
                                <option value="pph_manual_sppn_${isi}_${sub_index_sppn[isi]}">Manual</option>
                            </select>
                        </div>
                    </div>
                    <!-- END PAJAK WAPU WABA PPH MANUAL SPPB -->
                    <!-- DPP LAIN DAN MANUAL PAJAK  -->
                    <div class="form-group row" id="dpp_nilai_lain_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none;">
                        <label class="col-sm-1 col-form-label"></label>
                        <label class="col-sm-2 col-form-label">DPP Nilai Lain*</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="1.1%" class="form-control" disabled>
                            <input type="hidden" name="waba_nilai_lain" id="44" value="0.011">
                        </div>
                    </div>
                    <div class="form-group row" id="manual_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none;">
                        <label class="col-sm-1 col-form-label"></label>
                        <label class="col-sm-2 col-form-label">Manual PPh*</label>
                        <div class="col-sm-2">
                        <input type="Number" name="uraian_sppn[${isi}][${sub_index_sppn}][manual]" id="nominal_manual_sppn_${isi}_${sub_index_sppn[isi]}" class="form-control">
                        </div>
                        <span><h3>%</h3></span>
                    </div>
                    <!-- END DPP LAIN DAN MANUAL PAJAK  -->

                    <!-- GRUP INFO PERSEN PPh-->
                    <div class="form-group row" id="pph_normal_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none ;">
                        <label class="col-sm-1 col-form-label"></label>
                        <label class="col-sm-2 col-form-label">PPh*</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="11%" class="form-control" disabled>
                            <input type="hidden" name="input_normal_sppn_${isi}_${sub_index_sppn[isi]}" id="11" value="0.11">
                        </div>
                    </div>
                    <div class="form-group row" id="pph21_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none ;">
                        <label class="col-sm-1 col-form-label"></label>
                        <label class="col-sm-2 col-form-label">PPh 21*</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="21%" class="form-control" disabled>
                            <input type="hidden" name="input_pph21_sppn_${isi}_${sub_index_sppn[isi]}" id="11" value="0.11">
                        </div>
                    </div>
                    <div class="form-group row" id="pph22_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none ;">
                        <label class="col-sm-1 col-form-label"></label>
                        <label class="col-sm-2 col-form-label">PPh 22*</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="22%" class="form-control" disabled>
                            <input type="hidden" name="input_pph22_sppn_${isi}_${sub_index_sppn[isi]}" id="11" value="0.11">
                        </div>
                    </div>
                    <div class="form-group row" id="pph23_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none ;">
                        <label class="col-sm-1 col-form-label"></label>
                        <label class="col-sm-2 col-form-label">PPh 23*</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="2%" class="form-control" disabled>
                            <input type="hidden" name="input_pph23_sppn_${isi}_${sub_index_sppn[isi]}" id="pph23" value="0.02">
                        </div>
                    </div>
                    <div class="form-group row" id="pph26_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none ;">
                        <label class="col-sm-1 col-form-label"></label>
                        <label class="col-sm-2 col-form-label">PPh 26*</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="26%" class="form-control" disabled>
                            <input type="hidden" name="input_pph26_sppn_${isi}_${sub_index_sppn[isi]}" id="11" value="0.11">
                        </div>
                    </div>
                    <div class="form-group row" id="pasal4_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none;">
                        <label class="col-sm-1 col-form-label"></label>
                        <label class="col-sm-2 col-form-label">Pasal 4 Ayat 2*</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="10%" class="form-control" disabled>
                            <input type="hidden" name="input_pasal4_sppn_${isi}_${sub_index_sppn[isi]}" id="11" value="0.11">
                        </div>
                    </div>
                    <!-- END GRUP INFO PERSEN PPh-->
                    <!-- GRUP NOMINAL SPPB -->
                        <div class="form-group row" id="dpp_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none;">
                            <label class="col-sm-1 col-form-label"></label>
                            <label class="col-sm-2 col-form-label">DPP</label>
                            <div class="col-sm-9">
                            <div class="group">
                                <input type="text" id="nominal_sppn_${isi}_${sub_index_sppn[isi]}" name="uraian_sppn[${isi}][${sub_index_sppn}][jumlah]" class="form-control nominal validate_sppn validate_spp_all" placeholder="Nominal PPn" autocomplete="off" required>
                                <input type="hidden" id="tidak_nominal_sppn_${isi}_${sub_index_sppn[isi]}"  placeholder="Nominal tanpa titik PPn" autocomplete="off" required>
                                <label class="col-sm-6 col-form-label sppncek_dana_gagal_${isi}_${sub_index_sppn[isi]}" style="display:none;color:red;">Dana melebihi sisa RKAP</label>
                                <label class="col-sm-6 col-form-label sppncek_dana_berhasil_${isi}_${sub_index_sppn[isi]}" style="display:none;color:green;">Dana dibawah sisa RKAP</label>
                            </div>
                            </div>
                        </div>
                        <div class="form-group row" id="nominal_potongan_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none;">
                            <label class="col-sm-1 col-form-label"></label>
                            <label class="col-sm-2 col-form-label">Nominal Potongan</label>
                            <div class="col-sm-9">
                                <input type="text" id="potongan_sppn_${isi}_${sub_index_sppn[isi]}" name="uraian_sppn[${isi}][${sub_index_sppn}][potongan]" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="form-group row" id="nominal_pph_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none;">
                            <label class="col-sm-1 col-form-label"></label>
                            <label class="col-sm-2 col-form-label">PPh</label>
                            <div class="col-sm-9">
                                <input type="text" id="uraian_pph_sppn_${isi}_${sub_index_sppn[isi]}" name="uraian_sppn[${isi}][${sub_index_sppn}][pph]" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="form-group row" id="nominal_ppn_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none;">
                            <label class="col-sm-1 col-form-label"></label>
                            <label class="col-sm-2 col-form-label">PPn</label>
                            <div class="col-sm-9">
                                <input type="text" id="ppn_sppn_${isi}_${sub_index_sppn[isi]}" name="uraian_sppn[${isi}][${sub_index_sppn}][pajak]" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="form-group row" id="dpp_ppn_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none ;">
                            <label class="col-sm-1 col-form-label"></label>
                            <label class="col-sm-2 col-form-label">DPP + PPN</label>
                            <div class="col-sm-9">
                                <input type="text" id="dppppn_sppn_${isi}_${sub_index_sppn[isi]}" name="uraian_sppn[${isi}][${sub_index_sppn}][dpp_ppn]" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="form-group row" id="nominal_akhir_sppn_${isi}_${sub_index_sppn[isi]}" style="display:none ;">
                            <label class="col-sm-1 col-form-label"></label>
                            <label class="col-sm-2 col-form-label">Nominal Akhir</label>
                            <div class="col-sm-9">
                                <input type="text" id="akhir_sppn_${isi}_${sub_index_sppn[isi]}" name="uraian_sppn[${isi}][${sub_index_sppn}][total_pajak]" class="form-control" readonly>
                            </div>
                        </div>
                    <!-- END GRUP NOMINAL SPPB -->
                </div>
                <div class="col-sm-1">
                    <div class="col-sm-12" id="hapus_sub_isi_sppn_${isi}_${sub_index_sppn[isi]}" onclick="hapus_sub_isi_sppn(${isi},${sub_index_sppn[isi]},'ckeditors_${isi}_${sub_index_sppn[isi]}')" style="margin-bottom: 10px">
                        <button type="button" class="btn btn-danger btn-sm">X</button>
                    </div>
                        <div class="col-sm-12" id="tambah_sub_isi_sppn_${isi}_${sub_index_sppn[isi]}" onclick="tambah_sub_isi_sppn(${isi},${sub_index_sppn[isi]})" style="margin-bottom: 10px">
                            <button type="button" class="btn btn-success btn-sm">+</button>
                        </div>
                    </div>
                </div>
            </div>`);

            // CKEDITOR.inline('ckeditors_' + isi + '_' + sub_index_sppn[isi]);
            InlineEditor
                .create(document.querySelector('#ckeditors_' + isi + '_' + sub_index_sppn[isi]))
                .catch(error => {
                    console.error(error);
                });

            $('.nominal').mask('000.000.000.000.000.000.000', {
                reverse: true
            });

            var inputs = document.getElementsByClassName("validate_sppn");
            if (inputs) {
                for (var i = 0; i < inputs.length; i++) {
                    inputs[i].addEventListener("change", validateInput);
                    inputs[i].addEventListener("keyup", validateInput);
                    inputs[i].addEventListener("focus", validateInput);
                }
            }

            for (var i in CKEDITOR.instances) {
                if (i.substring(0, 9) == "ckeditors") {
                    CKEDITOR.instances[i].on('change', function() {
                        var urai = document.getElementById(this.name).parentElement;
                        if (this.getData().replace(/<[^>]+>/g, '') == "") {
                            urai.style.cssText =
                                "border-width:2px;border-color:red;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                        } else {
                            urai.style.cssText =
                                "border-width:2px;border-color:limegreen;border-style:solid;border-radius:1px;height:auto;min-height:100px;";
                        }
                    });
                }
            }
            var sub = sub_index_sppn[isi];
            // $( "#jumlah_sppn_"+isi+"_"+sub).on('keyup',function(e) {
            // 	console.log("Sub Isi - #jumlah_sppn_"+isi+"_"+sub);
            // 	jum_nom[isi][sub] = this.value.replace(/[^\d,]/g, "");
            // 	var jum_nom_total = 0;
            // 	for (let i = 1; i <= sub_index_sppn[isi]; i++) {
            // 		var jum_nom_value = $("#jumlah_sppn_"+isi+"_"+i) ? $("#jumlah_sppn_"+isi+"_"+i).val().replace(/[^\d,]/g, "") : 0;
            // 		jum_nom_total = jum_nom_value ? parseInt(jum_nom_total) + parseInt(jum_nom_value) : jum_nom_total;
            // 		console.log(jum_nom_total);
            // 	}
            // 	if (jum_nom_total > sisa[isi]) {
            // 		$('.sppncek_dana_gagal_'+isi+'_'+sub).css('display','block');
            // 		$('.sppncek_dana_berhasil_'+isi+'_'+sub).css('display','none');
            // 		// $("#simpan").prop("disabled", true);
            // 	}else{
            // 		$('.sppncek_dana_berhasil_'+isi+'_'+sub).css('display','block');
            // 		$('.sppncek_dana_gagal_'+isi+'_'+sub).css('display','none');
            // 		// $("#simpan").prop("disabled", false);
            // 	}
            // });
            set_listener_dpp();
        }

        function hapus_sub_isi_sppn(isi, sub_isi, instance) {

            $('#sub_isi_sppn_' + isi + '_' + sub_isi).remove();
            CKEDITOR.instances[instance].destroy();
            console.log(sub_index_sppn[isi])
            set_listener_dpp();
        }

        function tambah_faktur_pajak_spp(index) {
            index++;
            $('#fp_spp').append(`<div id="faktur_pajak_spp_${index}">
									<div class="form-group row">
										<label class="col-sm-2 col-form-label"></label>
										<div class="col-sm-4">
											<input type="text" id="faktur_pajak_spp" maxlength="17" name="faktur_pajak_spp[${index}][fp]" class="form-control validate_spp_all" placeholder="Faktur Pajak ${index}" autocomplete="off">
										</div>
										<label class="col-sm-1">Tanggal Faktur Pajak</label>
											<div class="col-sm-3">
												<input type="date" class="form-control" id="tanggal_faktur_pajak_spp" name="tanggal_faktur_pajak_spp[${index}][tanggal]">
											</div>
										<div class="col-sm-2" id="btn_tambah_faktur_pajak_${index}">
											<button type="button" class="btn btn-success btn-sm" onclick="tambah_faktur_pajak_spp(${index})">+</button>
											<button type="button" class="btn btn-danger btn-sm" onclick="hapus_faktur_pajak_spp(${index})">x</button>

										</div>
									</div>
								</div>`);
            var a = index - 1;
            $('#btn_tambah_faktur_pajak_' + a).hide();
            $('#btn_hapus_faktur_pajak_' + a).hide();

        }

        function hapus_faktur_pajak_spp(index) {
            var a = index - 1;
            $('#faktur_pajak_spp_' + index).remove();
            $('#btn_tambah_faktur_pajak_' + a).show();
            $('#btn_hapus_faktur_pajak_' + a).show();

        }

        function tambah_faktur_pajak_sppb(index) {
            index++;
            $('#fp_sppb').append(`<div id="faktur_pajak_sppb_${index}">
									<div class="form-group row">
										<label class="col-sm-2 col-form-label"></label>
										<div class="col-sm-4">
											<input type="text" id="faktur_pajak_spp" maxlength="17" name="faktur_pajak_sppb[${index}][fp]" class="form-control validate_spp_all" placeholder="Nomor Faktur Pajak SPPb ${index}" autocomplete="off">
										</div>
										<label class="col-sm-1">Tanggal Faktur Pajak</label>
											<div class="col-sm-3">
												<input type="date" class="form-control" id="tanggal_faktur_pajak_sppb" name="tanggal_faktur_pajak_sppb[${index}][tanggal]">
											</div>
										<div class="col-sm-2" id="btn_faktur_pajak_sppb_${index}">
											<button type="button" class="btn btn-success btn-sm" onclick="tambah_faktur_pajak_sppb(${index})">+</button>
											<button type="button" class="btn btn-danger btn-sm" onclick="hapus_faktur_pajak_sppb(${index})">x</button>

										</div>
									</div>
								</div>`);
            var a = index - 1;
            $('#btn_faktur_pajak_sppb_' + a).hide();

        }

        function hapus_faktur_pajak_sppb(index) {
            var a = index - 1;
            $('#faktur_pajak_sppb_' + index).remove();
            $('#btn_faktur_pajak_sppb_' + a).show();
        }

        function tambah_faktur_pajak_sppn(index) {
            index++;
            $('#fp_sppn').append(`<div id="faktur_pajak_sppn_${index}">
									<div class="form-group row">
										<label class="col-sm-2 col-form-label"></label>
										<div class="col-sm-4">
											<input type="text" id="faktur_pajak_spp" maxlength="17" name="faktur_pajak_sppn[${index}][fp]" class="form-control validate_spp_all" placeholder="Nomor Faktur Pajak SPPn ${index}" autocomplete="off">
										</div>
										<label class="col-sm-1">Tanggal Faktur Pajak</label>
											<div class="col-sm-3">
												<input type="date" class="form-control" id="tanggal_faktur_pajak_sppn" name="tanggal_faktur_pajak_sppn[${index}][tanggal]">
											</div>
										<div class="col-sm-2" id="btn_faktur_pajak_sppn_${index}">
											<button type="button" class="btn btn-success btn-sm" onclick="tambah_faktur_pajak_sppn(${index})">+</button>
											<button type="button" class="btn btn-danger btn-sm" onclick="hapus_faktur_pajak_sppn(${index})">x</button>

										</div>
									</div>
								</div>`);
            var a = index - 1;
            $('#btn_faktur_pajak_sppn_' + a).hide();

        }

        function hapus_faktur_pajak_sppn(index) {
            var a = index - 1;
            $('#faktur_pajak_sppn_' + index).remove();
            $('#btn_faktur_pajak_sppn_' + a).show();
        }

        function set_listener_dpp() {
            $('.validate_sppb.nominal').on('keyup', function() {
                bandingkan_dpp_sisa();
            });
            $('.validate_sppn.nominal').on('keyup', function() {
                bandingkan_dpp_sisa();
            });
            bandingkan_dpp_sisa();

        }

        set_listener_dpp();

        function bandingkan_dpp_sisa() {
            let jenis = $('#jenis_form').val()
            let simpan = true;
            if (jenis == 'sppb' || jenis == 'sppb_sppn') {
                console.log($('.sisa_1').val());
                for (let i = 1; i <= index_sppb; i++) {
                    try {
                        let total_dpp = 0;
                        let sisa = $('.sisa_' + i).val();
                        let sisa_number = sisa.replace(/Rp\.|\./g, '');
                        //console.log(index_sppb, sub_index_sppb);
                        for (let j = 1; j <= sub_index_sppb[i]; j++) {
                            try {
                                //console.log(i, j);
                                let dpp = $('#nominal_sppb_' + i + '_' + j).val();
                                let dpp_number = dpp.replace(/Rp\.|\./g, '');
                                total_dpp += parseInt(dpp_number == '' ? 0 : dpp_number);
                            } catch (error) {
                                console.error('Error in inner loop for index_sppb:', error);
                                // Handle the error as needed
                            }
                        }

                        for (let j = 1; j <= sub_index_sppb[i]; j++) {
                            try {
                                //console.log('sppbcek_dana_gagal_', i, j, total_dpp, sisa_number);
                                if (parseInt(total_dpp) > parseInt(sisa_number == '' ? 0 : sisa_number) && sisa_number !=
                                    '') {
                                    $('.sppbcek_dana_gagal_' + i + '_' + j).css('display', 'block');
                                    $('.sppbcek_dana_berhasil_' + i + '_' + j).css('display', 'none');
                                    simpan = false;
                                } else if (total_dpp == 0) {
                                    $('.sppbcek_dana_gagal_' + i + '_' + j).css('display', 'none');
                                    $('.sppbcek_dana_berhasil_' + i + '_' + j).css('display', 'none');
                                    simpan = false;
                                } else {
                                    $('.sppbcek_dana_gagal_' + i + '_' + j).css('display', 'none');
                                    $('.sppbcek_dana_berhasil_' + i + '_' + j).css('display', 'block');
                                }
                            } catch (error) {
                                console.error('Error in inner loop for index_sppb:', error);
                                // Handle the error as needed
                            }
                        }
                    } catch (error) {
                        console.error('Error in outer loop for index_sppb:', error);
                        // Handle the error as needed
                    }
                }
            }
            if (jenis == 'sppn' || jenis == 'sppb_sppn') {
                console.log($('.sisasppn_1').val());

                for (let i = 1; i <= index_sppn; i++) {
                    try {
                        let total_dpp = 0;
                        //console.log(index_sppn, sub_index_sppn, i);
                        let sisa = $('.sisasppn_' + i).val() || '';
                        let sisa_number = sisa.replace(/Rp\.|\./g, '');
                        //console.log(index_sppn, sub_index_sppn);
                        for (let j = 1; j <= sub_index_sppn[i]; j++) {
                            try {
                                console.log(i, j);
                                let dpp = $('#nominal_sppn_' + i + '_' + j).val();
                                let dpp_number = dpp.replace(/Rp\.|\./g, '');
                                total_dpp += parseInt(dpp_number == '' ? 0 : dpp_number);
                            } catch (error) {
                                console.error('Error in inner loop for index_sppn:', error);
                                // Handle the error as needed
                            }
                        }
                        for (let j = 1; j <= sub_index_sppn[i]; j++) {
                            try {
                                if (parseInt(total_dpp) > parseInt(sisa_number == '' ? 0 : sisa_number) && sisa_number !=
                                    '') {
                                    $('.sppncek_dana_gagal_' + i + '_' + j).css('display', 'block');
                                    $('.sppncek_dana_berhasil_' + i + '_' + j).css('display', 'none');
                                    simpan = false;
                                } else if (total_dpp == 0) {
                                    $('.sppncek_dana_gagal_' + i + '_' + j).css('display', 'none');
                                    $('.sppncek_dana_berhasil_' + i + '_' + j).css('display', 'none');
                                    simpan = false;
                                } else {
                                    $('.sppncek_dana_gagal_' + i + '_' + j).css('display', 'none');
                                    $('.sppncek_dana_berhasil_' + i + '_' + j).css('display', 'block');
                                }
                            } catch (error) {
                                console.error('Error in inner loop for index_sppn:', error);
                                // Handle the error as needed
                            }
                        }
                    } catch (error) {
                        console.error('Error in outer loop for index_sppn:', error);
                        // Handle the error as needed
                    }
                }
            }
            console.log(simpan, index_sppb, index_sppn)
            if (simpan == true) {
                $('#simpan').prop("disabled", false);
            } else {
                $('#simpan').prop("disabled", true);
            }
        }
    </script>
    <!-- End Javascript -->
    <script></script>
@endsection

@section('footer')
    <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/piexif.min.js"></script>
    <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/sortable.min.js"></script>
    <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/fileinput.min.js"></script>
    <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/themes/fa/theme.js"></script>
    <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/locales/id.js"></script>
    <script src="{{ asset('') }}assets/vendor/ckeditor/ckeditor5-build-inline/build/ckeditor.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

@endsection
