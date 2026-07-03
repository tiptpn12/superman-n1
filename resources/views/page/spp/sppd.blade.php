@extends('template.master')
@section('title', 'SPP')

@section('header')
    <link rel="stylesheet" href="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/css/fileinput.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/select2/select2.min.css') }}" />
    <style>
        .btn-purple {
            background-color: #6200ea;
            border-color: #6200ea;
            color: white;
        }

        .btn-terima.disabled {
            background-color: grey;
            cursor: not-allowed;
            pointer-events: none;
        }

        .btn-belum-bayar.disabled {
            background-color: grey;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>


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
    $master_user_id = Session::get('id');
    ?>
    <!-- MAIN -->
    <style>
        /*.preloader {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        position: fixed;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        left: 0px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        top: 0px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        width: 100%;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        height: 100%;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        z-index: 9999;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        background: url('{{ asset('') }}assets/Ajux_loader.gif') 50% 50% no-repeat rgb(249, 249, 249);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        background-size: 200px 200px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    }*/
    </style>
    <script>
        // $(window).load(function() {
        //     $("#preloaders").fadeOut(1000);
        // });
    </script>
    <div id="preloaders" class="preloader"></div>
    <div class="main">
        {{-- @dd($sppn_terima_to_do_list) --}}
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <!-- <h3 class="page-title">SPP</h3> -->
                <div class="row">
                    <div class="col-md-12">
                        <!-- TABLE -->
                        <div class="panel">

                            @if ($grup_id == 8 || $grup_id == 9)
                                {{-- TAB Admin --}} 
                                <!-- <h2>{{ $company }}</h2> -->
                                <div class="tab-pane fade in active" id="tab-operator-bagian">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Tabel PPb / PPn</h3>
                                    </div>
                                    <div class="panel-body">
                                        <br>
                                        {{-- <button class="btn btn-primary" onclick="advanced_search(1,1)"
                                            style="margin-bottom: 15px">Advanced Search</button> --}}
                                        <a class="btn btn-danger" href="{{ url('sppd') }}"
                                            style="margin-bottom: 15px">Refresh</a>
                                        <button type="submit" data-toggle="modal" data-target="#advance_filter"
                                            class="btn btn-primary" style="margin-bottom: 15px">Filter</button>
                                        <button id="resetFilter" type="button" class="btn btn-warning"
                                            style="margin-bottom: 15px">
                                            Reset Filter
                                        </button>
                                        <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                            <ul class="nav" role="tablist">
                                                <li id="tab-to-do-list"><a href="#tab-to-do-list-petugas" role="tab"
                                                        data-toggle="tab">To Do List</a><span
                                                        class="badge bg-danger count-notification" id="total-to-do">0</span>
                                                </li>
                                                <li id="tab-revisi"><a href="#tab-revisi-petugas" role="tab"
                                                        data-toggle="tab">Revisi</a><span
                                                        class="badge bg-danger count-notification"
                                                        id="total-revisi">0</span>
                                                </li>
                                                <li id="tab-sedang-proses"><a href="#tab-sedang-proses-petugas"
                                                        role="tab" data-toggle="tab">Sedang Proses</a></li>
                                                <li id="tab-sudah-selesai"><a href="#tab-sudah-selesai-petugas"
                                                        role="tab" data-toggle="tab">Sudah Selesai</a></li>
                                                <li id="tab-dibatalkan"><a href="#tab-dibatalkan-petugas" role="tab"
                                                        data-toggle="tab">Dibatalkan</a></li>
                                            </ul>
                                        </div>
                                        <div class="tab-content">
                                            {{-- Panel Sedang Proses --}}
                                            <div class="tab-pane fade in active" id="tab-sedang-proses-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
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

                                                            <th>Uraian</th>
                                                            <th>Jumlah</th>
                                                            <th>No</th>
                                                            <th>Uraian</th>
                                                            <th>Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            {{-- End Panel Sedang Proses --}}

                                            {{-- Panel Sudah Selesai --}}
                                            <div class="tab-pane fade" id="tab-sudah-selesai-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">
                                                                <center>No. </center>
                                                            </th>
                                                            <th rowspan="2">Tanggal PP</th>
                                                            <th colspan="3">PPb</th>
                                                            <th colspan="3">PPn</th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel Sudah Selesai --}}

                                            {{-- Panel Dibatalkan --}}
                                            <div class="tab-pane fade" id="tab-dibatalkan-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">
                                                                <center>No. </center>
                                                            </th>
                                                            <th rowspan="2">Tanggal PP</th>
                                                            <th colspan="3">PPb</th>
                                                            <th colspan="3">PPn</th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel Dibatalkan --}}

                                            {{-- Panel To Do List --}}
                                            <div class="tab-pane fade in active" id="tab-to-do-list-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
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
                                                            <th>Uraian</th>
                                                            <th>Jumlah</th>
                                                            <th>No</th>
                                                            <th>Uraian</th>
                                                            <th>Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            {{-- End Panel To Do List --}}

                                            {{-- Panel Revisi --}}
                                            <div class="tab-pane fade in active" id="tab-revisi-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
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
                                                            <th>Uraian</th>
                                                            <th>Jumlah</th>
                                                            <th>No</th>
                                                            <th>Uraian</th>
                                                            <th>Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            {{-- End Panel Revisi --}}

                                        </div>
                                    </div>
                                </div>
                                {{-- END TAB OPERATOR BAGIAN --}}
                            @endif

                            @if ($grup_id == 1)
                                {{-- TAB OPERATOR BAGIAN --}}
                                <!-- <h2>{{ $company }}</h2> -->
                                <div class="tab-pane fade in active" id="tab-operator-bagian">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Tabel PPn / PPn</h3>
                                    </div>
                                    <div class="panel-body">
                                        @if ($company != 5 || $bagian == 129)
                                            <a class="btn btn-primary" href="{{ url('spp/tambah') }}" style="margin-bottom: 15px">Buat PP</a>
                                        @endif
                                        <br>
                                        {{-- <button class="btn btn-primary" onclick="advanced_search(1,1)"
                                            style="margin-bottom: 15px">Advanced Search</button> --}}
                                        <a class="btn btn-danger" href="{{ url('sppd') }}"
                                            style="margin-bottom: 15px">Refresh</a>

                                        <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                            <ul class="nav" role="tablist">
                                                <li id="tab-to-do-list"><a href="#tab-to-do-list-petugas" role="tab"
                                                        data-toggle="tab">To Do List</a><span
                                                        class="badge bg-danger count-notification"
                                                        id="total-to-do">0</span>
                                                </li>
                                                <li id="tab-revisi"><a href="#tab-revisi-petugas" role="tab"
                                                        data-toggle="tab">Revisi</a><span
                                                        class="badge bg-danger count-notification"
                                                        id="total-revisi">0</span>
                                                </li>
                                                <li id="tab-sedang-proses"><a href="#tab-sedang-proses-petugas"
                                                        role="tab" data-toggle="tab">Sedang Proses</a></li>
                                                <li id="tab-sudah-selesai"><a href="#tab-sudah-selesai-petugas"
                                                        role="tab" data-toggle="tab">Sudah Selesai</a></li>
                                                <li id="tab-dibatalkan"><a href="#tab-dibatalkan-petugas" role="tab"
                                                        data-toggle="tab">Dibatalkan</a></li>
                                            </ul>
                                        </div>
                                        <div class="tab-content">
                                            {{-- Panel Sedang Proses --}}
                                            <div class="tab-pane fade in active" id="tab-sedang-proses-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
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
                                                            <th>Uraian</th>
                                                            <th>Jumlah</th>
                                                            <th>No</th>
                                                            <th>Uraian</th>
                                                            <th>Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            {{-- End Panel Sedang Proses --}}

                                            {{-- Panel Sudah Selesai --}}
                                            <div class="tab-pane fade" id="tab-sudah-selesai-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">
                                                                <center>No. </center>
                                                            </th>
                                                            <th rowspan="2">Tanggal PP</th>
                                                            <th colspan="3">PPb</th>
                                                            <th colspan="3">PPn</th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel Sudah Selesai --}}

                                            {{-- Panel Dibatalkan --}}
                                            <div class="tab-pane fade" id="tab-dibatalkan-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">
                                                                <center>No. </center>
                                                            </th>
                                                            <th rowspan="2">Tanggal PP</th>
                                                            <th colspan="3">PPb</th>
                                                            <th colspan="3">PPn</th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel Dibatalkan --}}

                                            {{-- Panel To Do List --}}
                                            <div class="tab-pane fade in active" id="tab-to-do-list-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
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
                                                            <th>Uraian</th>
                                                            <th>Jumlah</th>
                                                            <th>No</th>
                                                            <th>Uraian</th>
                                                            <th>Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            {{-- End Panel To Do List --}}

                                            {{-- Panel Revisi --}}
                                            <div class="tab-pane fade in active" id="tab-revisi-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
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

                                                            <th>Uraian</th>
                                                            <th>Jumlah</th>
                                                            <th>No</th>
                                                            <th>Uraian</th>
                                                            <th>Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            {{-- End Panel Revisi --}}

                                        </div>
                                    </div>
                                </div>
                                {{-- END TAB OPERATOR BAGIAN --}}
                            @endif


                            @if ($grup_id == 2 || $grup_id == 7)
                                {{-- TAB PETUGAS PENERIMA --}}
                                <div class="tab-pane fade in active" id="tab-petugas-penerima">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Tabel SPPb / SPPn</h3>
                                    </div>
                                    <div class="panel-body">
                                        {{-- <button class="btn btn-primary" onclick="advanced_search(1,1)"
                                            style="margin-bottom: 15px">Advanced Search</button> --}}
                                        <a class="btn btn-danger" href="{{ url('sppd') }}"
                                            style="margin-bottom: 15px">Refresh</a>
                                        <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                            <ul class="nav" role="tablist">
                                                <li id="tab-to-do-list"><a href="#tab-to-do-list-petugas" role="tab"
                                                        data-toggle="tab">To Do List</a><span
                                                        class="badge bg-danger count-notification"
                                                        id="total-to-do">0</span>
                                                </li>
                                                <li id="tab-revisi"><a href="#tab-revisi-petugas" role="tab"
                                                        data-toggle="tab">Revisi</a><span
                                                        class="badge bg-danger count-notification"
                                                        id="total-revisi">0</span>
                                                </li>
                                                <li id="tab-sedang-proses"><a href="#tab-sedang-proses-petugas"
                                                        role="tab" data-toggle="tab">Sedang Proses</a></li>
                                                <li id="tab-sudah-selesai"><a href="#tab-sudah-selesai-petugas"
                                                        role="tab" data-toggle="tab">Sudah Selesai</a></li>
                                                <li id="tab-dibatalkan"><a href="#tab-dibatalkan-petugas" role="tab"
                                                        data-toggle="tab">Dibatalkan</a></li>
                                            </ul>
                                        </div>
                                        <div class="tab-content">

                                            {{-- Panel Sedang Proses --}}
                                            <div class="tab-pane fade in active" id="tab-sedang-proses-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">No. </th>
                                                            <th rowspan="2">Bagian</th>
                                                            <th rowspan="2">Tanggal PP </th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel Sedang Proses --}}

                                            {{-- Panel Sudah Selesai --}}
                                            <div class="tab-pane fade" id="tab-sudah-selesai-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">
                                                                <center>No. </center>
                                                            </th>
                                                            <th rowspan="2">Bagian</th>
                                                            <th rowspan="2">Tanggal PP</th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel Sudah Selesai --}}

                                            {{-- Panel Dibatalkan --}}
                                            <div class="tab-pane fade" id="tab-dibatalkan-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">
                                                                <center>No. </center>
                                                            </th>
                                                            <th rowspan="2">Bagian</th>
                                                            <th rowspan="2">Tanggal PP</th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel Dibatalkan --}}

                                            {{-- Panel To Do List --}}
                                            <div class="tab-pane fade in active" id="tab-to-do-list-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">No. </th>
                                                            <th rowspan="2">Bagian</th>
                                                            <th rowspan="2">Tanggal PP </th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel To Do List --}}

                                            {{-- Panel Revisi --}}
                                            <div class="tab-pane fade" id="tab-revisi-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">No. </th>
                                                            <th rowspan="2">Bagian</th>
                                                            <th rowspan="2">Tanggal PP </th>
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
                                                    {{-- <tbody>
                                                @php $a=1 @endphp
                                                @foreach ($revisi as $d => $s)
                                                <tr>
                                                    <td><strong>{{ $a++ }}</strong></td>
                                                    <td><strong>{{ $s->master_bagian_nama }}</strong></td>
                                                    <td><strong>{{ date('d-m-Y', strtotime($s->tanggal)) }}</strong>
                                                    </td>
                                                    <td><strong>{{ $s->sppb_no }}</strong></td>
                                                    <td><strong>{{
                                                            Str::limit(html_entity_decode(strip_tags($s->sppb_uraian2)),
                                                            75) }}</strong>
                                                    </td>
                                                    <td><strong>Rp.{{ number_format($s->sppb_total) }}</strong>
                                                    </td>
                                                    <td><strong>{{ $s->sppn_no }}</strong></td>
                                                    <td><strong>{{
                                                            Str::limit(html_entity_decode(strip_tags($s->sppn_uraian2)),
                                                            75) }}</strong>
                                                    </td>
                                                    <td><strong>Rp.{{ number_format($s->sppn_jumlah) }}</strong>
                                                    </td>
                                                    @if ($s->sppd_status == 100)
                                                    <td><strong>Selesai</strong></td>
                                                    @else
                                                    <td><strong>{{ $s->master_hak_akses_nama }}</strong>
                                                    </td>
                                                    @endif
                                                    <td>
                                                        @if ($s->sppd_status == 2 && $s->sppd_posisi == $hakakses)
                                                        <button type="button" class="btn btn-warning btn-sm"
                                                            onclick="revisi({{ $s->spp_id }})" title="Revisi"><i
                                                                class="fa fa-arrow-left"></i></button>

                                                        <button type="button" class="btn btn-success btn-sm"
                                                            onclick="kirim({{ $s->spp_id }})" title="Kirim"><i
                                                                class="fa fa-arrow-right"></i></button>
                                                        @if ($s->sppd_proses == 1 && $s->sppd_proses == 2)
                                                        @php $idspp = encrypt($s->spp_id) @endphp
                                                        <a type="button" class="btn btn-warning btn-sm"
                                                            href="{{ url('sppd/edit/' . $idspp) }}" title="Edit Data"><i
                                                                class="fa fa-pencil" aria-hidden="true"></i></a>
                                                        @endif
                                                        @if ($grup_id == 7)
                                                        @if ($s->spp_no_dokumen)
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                            onclick="upload_no_doc({{ $d }},{{ $s->spp_id }},{{ $s->spp_no_dokumen }})"
                                                            style="background-color: #800000; border-color: #800000"
                                                            title="Upload No Doc"><i class="fa fa-file"></i></button>
                                                        @else
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                            onclick="upload_no_doc({{ $d }},{{ $s->spp_id }},{{ $s->spp_no_dokumen }})"
                                                            style="background-color: #7CFC00 ;border-color: #7CFC00"
                                                            title="Upload No Doc"><i class="fa fa-file"></i></button>
                                                        @endif
                                                        @endif
                                                        @endif
                                                        @if ($s->sppd_status == 3 && $s->sppd_posisi == $hakakses)
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="kirim({{ $s->spp_id }})" title="Kirim"><i
                                                                class="fa fa-arrow-left"></i></button>
                                                        @endif
                                                        @php $idspp = encrypt($s->spp_id) @endphp
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                            onclick="window.open('{{ url('sppd/rekam_jejak/' . $idspp) }}')"
                                                            title="Rekam Jejak"><i class="fa fa-map-o"></i></button>
                                                        <button type="button" class="btn btn-info btn-sm"
                                                            onclick="window.open('{{ url('spp/detail/' . $s->spp_id) }}')"
                                                            title="Detail"><i class="fa fa-info"></i></button>
                                                        <button type="button" class="btn btn-info btn-sm"
                                                            onclick="upload_dokumen_pendukung({{ $s->spp_id }})"
                                                            title="Upload Dokumen Tambahan"><i
                                                                class="fa fa-upload"></i></button>
                                                    </td>
                                                </tr>
                                                @endforeach

                                            </tbody> --}}
                                                </table>
                                            </div>
                                            {{-- End Panel Revisi --}}

                                        </div>
                                    </div>
                                </div>
                                {{-- END TAB PETUGAS PENERIMA --}}
                            @endif

                            @if ($grup_id == 3)
                                {{-- TAB PETUGAS KAS BANK --}}
                                <div class="tab-pane fade in active" id="tab-petugas-kas-bank">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Tabel SPPb / SPPn</h3>
                                    </div>
                                    <div class="panel-body">
                                        {{-- <button class="btn btn-primary" onclick="advanced_search(1,1)"
                                            style="margin-bottom: 15px">Advanced Search</button> --}}
                                        <a class="btn btn-danger" href="{{ url('sppd') }}"
                                            style="margin-bottom: 15px">Refresh</a>
                                        <button class="btn btn-success" style="margin-bottom: 15px"
                                            onclick="approveSelected()">Approve</button>
                                        <button id="select-all-button" class="btn btn-warning"
                                            style="margin-bottom: 15px">Select All</button>
                                        <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                            <ul class="nav" role="tablist">
                                                <li id="tab-to-do-list"><a href="#tab-to-do-list-petugas" role="tab"
                                                        data-toggle="tab">To Do List</a><span
                                                        class="badge bg-danger count-notification"
                                                        id="total-to-do">0</span>
                                                </li>
                                                <li id="tab-revisi"><a href="#tab-revisi-petugas" role="tab"
                                                        data-toggle="tab">Revisi</a><span
                                                        class="badge bg-danger count-notification"
                                                        id="total-revisi">0</span>
                                                </li>
                                                <li id="tab-sedang-proses"><a href="#tab-sedang-proses-petugas"
                                                        role="tab" data-toggle="tab">Sedang Proses</a></li>
                                                <li id="tab-sudah-selesai"><a href="#tab-sudah-selesai-petugas"
                                                        role="tab" data-toggle="tab">Sudah Selesai</a></li>
                                                <li id="tab-dibatalkan"><a href="#tab-dibatalkan-petugas" role="tab"
                                                        data-toggle="tab">Dibatalkan</a></li>
                                            </ul>
                                        </div>
                                        <div class="tab-content">

                                            {{-- Panel Sedang Proses --}}
                                            <div class="tab-pane fade in active" id="tab-sedang-proses-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">No. </th>
                                                            <th rowspan="2">Bagian</th>
                                                            <th rowspan="2">Tanggal PP</th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel Sedang Proses --}}

                                            {{-- Panel Sudah Selesai --}}
                                            <div class="tab-pane fade" id="tab-sudah-selesai-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">
                                                                <center>No. </center>
                                                            </th>
                                                            <th rowspan="2">Bagian</th>
                                                            <th rowspan="2">Tanggal PP</th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel Sudah Selesai --}}

                                            {{-- Panel Dibatalkan --}}
                                            <div class="tab-pane fade" id="tab-dibatalkan-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">
                                                                <center>No. </center>
                                                            </th>
                                                            <th rowspan="2">Bagian</th>
                                                            <th rowspan="2">Tanggal PP</th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel Dibatalkan --}}

                                            {{-- Panel To Do List --}}
                                            <div class="tab-pane fade in active" id="tab-to-do-list-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">No. </th>
                                                            <th rowspan="2">Bagian</th>
                                                            <th rowspan="2">Tanggal PP </th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel To Do List --}}

                                            {{-- Panel Revisi --}}
                                            <div class="tab-pane fade" id="tab-revisi-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">No. </th>
                                                            <th rowspan="2">Bagian</th>
                                                            <th rowspan="2">Tanggal PP </th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel Revisi --}}

                                        </div>
                                    </div>
                                </div>
                                {{-- END TAB PETUGAS KAS BANK --}}
                            @endif

                            @if ($grup_id == 4)
                                {{-- TAB PETUGAS PEMBAYARAN --}}
                                <div class="tab-pane fade in active" id="tab-petugas-pembayaran">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Tabel SPPb / SPPn</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="d-flex align-items-center" style="gap: 15px;">
                                            {{-- <button class="btn btn-primary" onclick="advanced_search(1,1)">Advanced
                                                Search</button> --}}
                                            <a class="btn btn-danger" href="{{ url('sppd') }}">Refresh</a>

                                            <!-- Card untuk Ubah Status dan Select All -->
                                            <div class="card"
                                                style="background-color: #f5e6da; padding: 10px; display: inline-block; margin: 1px;">
                                                <h5 class="card-title"
                                                    style="text-align: center; font-weight: bold; margin-top: 0;">
                                                    Ganti Status Bayar</h5>
                                                <div class="d-flex align-items-center" style="gap: 2px;">
                                                    <button class="btn btn-success" onclick="approveSelectedStatus()">Ubah
                                                        Status</button>
                                                    <button id="select-all-bayar" class="btn btn-warning">Select
                                                        All</button>
                                                </div>
                                            </div>

                                            <!-- Card untuk Approve dan Select All -->
                                            <div class="card"
                                                style="background-color: #d0ebf9; padding: 10px; display: inline-block; margin: 1px;">
                                                <h5 class="card-title"
                                                    style="text-align: center; font-weight: bold; margin-top: 0;">
                                                    Terima SPP</h5>
                                                <div class="d-flex align-items-center" style="gap: 2px;">
                                                    <button class="btn btn-success"
                                                        onclick="approveSelected()">Approve</button>
                                                    <button id="select-all-button" class="btn btn-warning">Select
                                                        All</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                            <ul class="nav" role="tablist">
                                                <li id="tab-to-do-list"><a href="#tab-to-do-list-petugas" role="tab"
                                                        data-toggle="tab">To Do List</a><span
                                                        class="badge bg-danger count-notification"
                                                        id="total-to-do">0</span>
                                                </li>
                                                <li id="tab-revisi"><a href="#tab-revisi-petugas" role="tab"
                                                        data-toggle="tab">Revisi</a><span
                                                        class="badge bg-danger count-notification"
                                                        id="total-revisi">0</span>
                                                </li>
                                                <li id="tab-sedang-proses"><a href="#tab-sedang-proses-petugas"
                                                        role="tab" data-toggle="tab">Sedang Proses</a></li>
                                                <li id="tab-sudah-selesai"><a href="#tab-sudah-selesai-petugas"
                                                        role="tab" data-toggle="tab">Sudah Selesai</a></li>
                                                <li id="tab-dibatalkan"><a href="#tab-dibatalkan-petugas" role="tab"
                                                        data-toggle="tab">Dibatalkan</a></li>
                                            </ul>
                                        </div>
                                        <div class="tab-content">

                                            {{-- Panel Sedang Proses --}}
                                            <div class="tab-pane fade in active" id="tab-sedang-proses-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">No. </th>
                                                            <th rowspan="2">Bagian</th>
                                                            <th rowspan="2">Tanggal PP</th>
                                                            <th colspan="3">PPb</th>
                                                            <th colspan="3">PPn</th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel Sedang Proses --}}

                                            {{-- Panel Sudah Selesai --}}
                                            <div class="tab-pane fade" id="tab-sudah-selesai-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">
                                                                <center>No. </center>
                                                            </th>
                                                            <th rowspan="2">Bagian</th>
                                                            <th rowspan="2">Tanggal PP</th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel Sudah Selesai --}}

                                            {{-- Panel Dibatalkan --}}
                                            <div class="tab-pane fade" id="tab-dibatalkan-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">
                                                                <center>No. </center>
                                                            </th>
                                                            <th rowspan="2">Bagian</th>
                                                            <th rowspan="2">Tanggal PP</th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel Dibatalkan --}}

                                            {{-- Panel To Do List --}}
                                            <div class="tab-pane fade in active" id="tab-to-do-list-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">No. </th>
                                                            <th rowspan="2">Bagian</th>
                                                            <th rowspan="2">Tanggal PP</th>
                                                            <th colspan="3">PPb</th>
                                                            <th colspan="3">PPn</th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel To Do List --}}

                                            {{-- Panel Revisi --}}
                                            <div class="tab-pane fade" id="tab-revisi-petugas">
                                                <table class="table table-bordered table-striped nowrap"
                                                    style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">No. </th>
                                                            <th rowspan="2">Bagian</th>
                                                            <th rowspan="2">Tanggal PP</th>
                                                            <th colspan="3">PPb</th>
                                                            <th colspan="3">PPn</th>
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
                                                </table>
                                            </div>
                                            {{-- End Panel Revisi --}}

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

    <!-- Modal Filter -->
    <div class="modal fade" id="advance_filter" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter SPP</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Filter Tanggal -->
                    <div class="form-group">
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" id="start_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="end_date">Tanggal Akhir</label>
                        <input type="date" id="end_date" class="form-control">
                    </div>

                    @if ($akses == 46)
                        <!-- Dropdown Regional -->
                        <div class="form-group" id="regional_filter" style="display: block;">
                            <label for="regional">Regional</label>
                            <select id="regional" class="form-control">
                                <option value="">-- Pilih Regional --</option>
                            </select>
                        </div>
                    @endif

                    <!-- Filter Posisi -->
                    <div class="form-group">
                        <label for="sppd_posisi">Posisi</label>
                        <select id="sppd_posisi" class="form-control">
                            <option value="">-- Pilih Posisi --</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" id="applyFilter" class="btn btn-primary">Terapkan Filter</button>
                </div>
            </div>
        </div>
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
                                        {{ $bagian_id->master_bagian_nama }}
                                    </option>
                                </select>
                            </div>
                        @else
                            <div class="form-group" id="advanced_search_bagian">
                                <label>Divisi :</label><br>
                                <select class="form-control" name="bagian">
                                    <option value="semua">Tampilkan Semua</option>
                                    @foreach ($b as $key => $value)
                                        <option value="{{ $value->master_bagian_id }}">
                                            {{ $value->master_bagian_nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        {{-- <div class="form-group" id="advanced_search_bagian">
                        <label>Vendor :</label><br>
                        <select class="form-control" name="vendor">
                            <option value="semua">Tampilkan Semua</option>
                        </select>
                    </div> --}}
                        <div class="form-group" id="advanced_search_rentang_waktu">
                            <label>Rentang Waktu :</label><br>
                            <input type="text" class="form-control date-range" name="rentang_waktu">
                        </div>
                        <div class="form-group" id="advanced_search_kode_sap">
                            <label>Kode SAP Vendor SPPB</label>
                            <select class="form-control" name="kode_sap_sppb"
                                id="advanced_search_kode_sap_sppb"></select>
                        </div>
                        <div class="form-group" id="advanced_search_kode_sap">
                            <label>Kode SAP Vendor SPPN</label>
                            <select class="form-control" name="kode_sap_sppn"
                                id="advanced_search_kode_sap_sppn"></select>
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
                            <li id="tab_bayar"><a href="#tab-pembayaran" role="tab" data-toggle="tab">Pembayaran</a>
                            </li>
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
                                        <input type="text" class="form-control date" placeholder="Tanggal pembayaran"
                                            id="tanggal_bayar_sppb" name="tanggal_bayar_sppb"
                                            value="{{ DATE('d-m-Y') }}" required>
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
                                <button type="button" class="btn btn-danger" onclick="clear_spp_bayar(0)">Clear</button>
                            </div>
                            <div id="footer_edit_sppb" class="modal-footer">
                                <button type="button" class="btn btn-warning" onclick="edit_bayar_sppb(0)">Edit</button>

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
                                        <input type="text" class="form-control" value="-" id="rekening_sppn"
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
                            <input type="hidden" id="tanggal_cetak_sppb" name="tanggal_cetak_sppb">
                            <div id="bayar_sppb_cbk">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <input type="hidden" id="cetak_bukti_kas_metode_pembayaran"
                                            name="cetak_bukti_kas_metode_pembayaran">
                                    </div>
                                    <div class="form-group">
                                        <label>Nomor Cek/Giro :</label><br>
                                        <input type="text" class="form-control" id="nomor_bukti_kas_sppb_cbk"
                                            name="nomor_bukti_kas_sppb" placeholder="nomor cek/giro pembayaran" required>
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
                                    {{-- <div class="form-group">
                                    <label>Tanggal Posting:</label><br>
                                    <input type="date" class="form-control" id="tanggal_cetak_sppb"
                                        name="tanggal_cetak_sppb" autocomplete="off" required>
                                </div> --}}

                                    {{-- <div class="form-group">
                                    <label>Uraian :</label><br>
                                    <textarea class="form-control" id="uraian_cetak_sppb" name="uraian_cetak_sppb"
                                        autocomplete="off" required> </textarea>
                                </div> --}}


                                </div>
                            </div>
                            <div id="footer_submit_sppb_cbk" class="modal-footer">
                                <button type="button" class="btn btn-success pisan submit-cetak-kas">Submit</button>
                                <button type="button" class="btn btn-danger pisan"
                                    onclick="clear_spp_bayar(0)">Clear</button>
                            </div>
                            <div id="footer_edit_sppb_cbk" class="modal-footer">
                                <button type="button" class="btn btn-warning" onclick="edit_bukti_kas(0)">Edit</button>
                                <button type="button" id="cetakbuktikas" class="btn btn-success cetakbuktikas"
                                    value="">Cetak Bukti Kas</button>


                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade in" id="tab-penerimaan_cbk">
                        <form action="" id="form-terima_cbk" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" id="form_b_cbk" name="form_b" value="">
                            <input type="hidden" id="tanggal_cetak_sppn" name="tanggal_cetak_sppn">
                            <div id="bayar_sppn">
                                <div class="modal-body">
                                    <input type="hidden" id="id_sppn_terima_cbk" name="id_sppn_terima">
                                    <div class="form-group">
                                        <label>Nomor Cek/Giro :</label><br>
                                        <input type="text" class="form-control" id="nomor_bukti_kas_sppn_cbk"
                                            name="nomor_bukti_kas_sppn" placeholder="nomor cek/giro penerimaan" required>
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
                                            name="diterima_dari" id="diterima_dari_cbk" placeholder=" Diterima Dari"
                                            required>

                                    </div>
                                    <div class="form-group">
                                        <label>Alamat :</label><br>
                                        <input type="text" class="form-control" autocomplete="off"
                                            id="alamat_diterima_dari_cbk" name="alamat_sppn" placeholder=" Diterima Dari"
                                            required>
                                    </div>
                                    {{-- <div class="form-group">
                                    <label>Tanggal Posting :</label><br>
                                    <input type="date" class="form-control" id="tanggal_cetak_sppn"
                                        name="tanggal_cetak_sppn" autocomplete="off" required>
                                </div> --}}
                                    {{-- <div class="form-group">
                                    <label>Uraian :</label><br>
                                    <input type="text" class="form-control" id="uraian_cetak_sppn"
                                        name="uraian_cetak_sppn" autocomplete="off" required>
                                </div> --}}

                                </div>
                            </div>
                            <div id="footer_submit_sppn_cbk" class="modal-footer">
                                <button type="button" class="btn btn-success pisan submit-cetak-kas">Submit</button>
                                <button type="button" class="btn btn-danger pisan"
                                    onclick="clear_spp_bayar(1)">Clear</button>
                            </div>
                            <div id="footer_edit_sppn_cbk" class="modal-footer">
                                <button type="button" class="btn btn-warning" onclick="edit_bukti_kas(1)">Edit</button>
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
                            @if ($grup_id == 7)
                                <input type="text" id="no_doc" name="no_doc" class="form-control" required>
                            @else
                                <input type="text" id="no_doc" name="no_doc" class="form-control" required
                                    readonly>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        @if ($grup_id == 7)
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
                            <label>Upload File PP yang sudah di TTD Kepala Bagian (Wajib) :</label><br>
                            <input type="file" id="spp_kabag" name="spp_kabag" class="file"
                                accept="application/pdf, image/*" placeholder="PP tanda tangan Kabag"
                                autocomplete="off" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="pisanaeto" class="btn btn-success">Submit</button>
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
                                accept="application/pdf, image/*" multiple required>
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
                                onclick="edit_bukti_kas_upload()"><i class="fa fa-pencil" aria-hidden="true"></i></button>
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
                    @if ($hakakses == 39)
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Revisi PP dan Bukti Kas</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Keterangan Revisi PP dan Bukti Kas :</label><br>
                                <textarea class="form-control" id="keterangan_revisi" name="revisi" placeholder="Keterangan Revisi" required></textarea>
                            </div>
                        </div>
                    @else
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
                    @endif
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
                                    {{-- <th>No KBB</th> --}}
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
                        <table class="table table-bordered table-striped nowrap" style="width: 100%"
                            id="table_penerima">
                            <thead>
                                <tr>
                                    <th>No. </th>
                                    <th>Nama Bank</th>
                                    <th>Alamat</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($vendor as $key => $value)
                            <tr>
                                <td style="display:none">{{$value->master_vendor_id}}</td>
                                <td>{{$key+1}}</td>
                                <td>{{$value->master_vendor_nama_bank}}</td>
                                <td>(Alamat Kosong)</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm"
                                        onclick="pilih_penerima('{{$value->master_vendor_id}}','{{$value->master_vendor_nama_bank}}',0)"
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
                        <table class="table table-bordered table-striped nowrap" style="width: 100%"
                            id="table_diterima_dari">
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
                                {{-- @foreach ($vendor as $key => $value)
                            <tr>
                                <td style="display:none">{{$value->master_vendor_id}}</td>
                                <td>{{$key+1}}</td>
                                <td>{{$value->master_vendor_nama_bank}}</td>
                                <td>(Alamat Kosong)</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm"
                                        onclick="pilih_diterima_dari('{{$value->master_vendor_id}}','{{$value->master_vendor_nama_bank}}',0)"
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
    {{-- End Modal Diterima dari --}}
    <!-- End Modal -->

    <script src="{{ asset('assets/vendor/select2/select2.min.js') }}"></script>
    <script type="text/javascript">
        var grupId = {{ $grup_id }};
        var hakAkses = {{ $akses }};
        var masterUserId = {{ $master_user_id ?? 0 }};
        var index_adv = {{ $index }};

        var index_cetak = {{ $index_cetak }};
        var id_cetak = {{ $id_cetak }};

        console.log(index_adv, index_cetak, id_cetak);



        window.toggleReadMore = function(link) {
            // Cari container - bisa di td (DataTable) atau strong atau parent langsung
            var container = link.closest('td') || link.closest('strong') || link.parentElement;
            
            // Cari elemen dengan class text-short dan text-full
            var shortText = container.querySelector('.text-short');
            var fullText = container.querySelector('.text-full');

            if (!shortText || !fullText) {
                console.error("Elements not found. Container:", container);
                return;
            }

            if (shortText.style.display === 'none') {
                shortText.style.display = 'inline';
                fullText.style.display = 'none';
                link.innerHTML = 'Read more';
            } else {
                shortText.style.display = 'none';
                fullText.style.display = 'block';
                link.innerHTML = 'Read less';
            }
        }

        function dataColumnsToDo() {
            var columns = [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    console.log(row);
                    return '<strong>' + data + '</strong>';
                }
            }];

            if (grupId == 1 || grupId == 2 || grupId == 3 || grupId == 4 || grupId == 7 || grupId == 8 ||
                grupId == 9) {
                if (grupId == 2 || grupId == 3 || grupId == 4 || grupId == 7) {
                    columns.push({
                        data: 'master_bagian_nama',
                        name: 'master_bagian_nama',
                        render: function(data, type, row) {
                            if (!data) {
                                return '<strong></strong>';
                            }
                            return '<strong>' + data + '</strong>';
                        }
                    });
                }
                columns.push({
                    data: 'tanggal',
                    name: 'tanggal',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        return '<strong>' + data + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppb_no',
                    name: 'sppb_no',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        return '<strong>' + data + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppb_uraian2',
                    name: 'sppb_uraian2',
                    render: function(data, type, row, meta) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        // Menghapus semua tag HTML
                        // Menghapus semua tag HTML
                        var strippedText = data.replace(/<[^>]+>/g, "");

                        // Decode entitas HTML
                        var decodedText = $("<textarea />").html(strippedText).text();

                        var limit=75;

                        // Jika pendek, tampilkan langsung
                        if (decodedText.length <= limit) {
                            return '<strong>' + decodedText + '</strong>';
                        }

                        var shortText = decodedText.substring(0, limit);

                        // Mengembalikan teks dengan class-based approach
                        return '<strong>' +
                            '<span class="text-short" style="display:inline;">' + shortText + '...</span>' +
                            '<div class="text-full" style="display:none; margin-top:5px; white-space:normal;">' +
                                decodedText +
                            '</div>' +
                            '<a href="javascript:void(0)" ' +
                            'onclick="window.toggleReadMore(this)" ' +
                            'style="color:#007bff; font-weight:600; display:inline-block; margin-top:4px;">' +
                            'Read more' +
                            '</a>' +
                        '</strong>';

                    }
                });
                columns.push({
                    data: 'sppb_total',
                    name: 'sppb_total',
                    render: function(data, type, row) {
                        // Pastikan data valid dan bukan null atau undefined
                        if (!row.sppb_id) {
                            return '<strong></strong>';
                        }

                        // Format angka dengan toLocaleString (menambahkan tanda pemisah ribuan)
                        var formattedNumber = new Intl.NumberFormat('id', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0
                        }).format(data);

                        // Kembalikan nilai dalam tag <strong>
                        return '<strong>' + formattedNumber + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppn_no',
                    name: 'sppn_no',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        return '<strong>' + data + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppn_uraian2',
                    name: 'sppn_uraian2',
                    render: function(data, type, row, meta) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        // Menghapus semua tag HTML
                        // Menghapus semua tag HTML
                        var strippedText = data.replace(/<[^>]+>/g, "");

                        // Decode entitas HTML
                        var decodedText = $("<textarea />").html(strippedText).text();

                        var limit=75;

                        // Jika pendek, tampilkan langsung
                        if (decodedText.length <= limit) {
                            return '<strong>' + decodedText + '</strong>';
                        }

                        var shortText = decodedText.substring(0, limit);

                        // Mengembalikan teks dengan class-based approach
                         return '<strong>' +
                            '<span class="text-short" style="display:inline;">' + shortText + '...</span>' +
                            '<div class="text-full" style="display:none; margin-top:5px; white-space:normal;">' +
                                decodedText +
                            '</div>' +
                            '<a href="javascript:void(0)" ' +
                            'onclick="window.toggleReadMore(this)" ' +
                            'style="color:#007bff; font-weight:600; display:inline-block; margin-top:4px;">' +
                            'Read more' +
                            '</a>' +
                        '</strong>';
                    }
                });
                columns.push({
                    data: 'sppn_jumlah',
                    name: 'sppn_jumlah',
                    render: function(data, type, row) {
                        // Pastikan data valid dan bukan null atau undefined
                        if (!row.sppn_id || !data) {
                            return '<strong></strong>';
                        }

                        var formattedNumber = new Intl.NumberFormat('id', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0
                        }).format(data);

                        // Kembalikan nilai dalam tag <strong>
                        return '<strong>' + formattedNumber + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppd_status',
                    name: 'sppd_status',
                    render: function(data, type, row) {
                        if (data == 100) {
                            return '<strong>Selesai</strong>';
                        } else {
                            return '<strong>' + row.master_hak_akses_nama + '</strong>';
                        }
                    }
                });
                if (grupId == 4) {
                    columns.push({
                        data: 'sppd_status',
                        name: 'sppd_status',
                        render: function(data, type, row) {
                            if (data == 2 && row.sppd_posisi == hakAkses) {
                                if (typeof row.sppb_no !== 'undefined' && row.sppb_no !== null && !row
                                    .sppn_no) {
                                    if (row.spp_status_bayar == 0) {
                                        return `
                                            <button type="button" class="btn btn-warning btn-sm btn-belum-bayar" onclick="ubahStatusBayar(this, ${row.spp_id}, ${row.spp_status_bayar})">
                                                Belum Dibayar
                                            </button>
                                            <input type="checkbox" class="checkbox-item-bayar" value="${row.spp_id}" style="transform: scale(2); margin-left: 8px;">
                                        `;
                                    } else if (row.spp_status_bayar == 1) {
                                        return `
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="pembayaran(${row.sppb_id},0,0,true,false)">
                                                Sudah Dibayar
                                            </button>
                                        `;
                                    } else if (row.spp_status_bayar == 2) {
                                        return `
                                            <button type="button" class="btn btn-sm" style="background-color: #6E00FF; border-color: #6E00FF; color: white;"
                                                onclick="pembayaran(${row.sppb_id},0,0,true,false)">
                                                Sudah Dibayar Belum Upload Bukti Transfer
                                            </button>
                                        `;
                                    }
                                } else if (!row.sppb_no && typeof row.sppn_no !== 'undefined' && row.sppn_no !==
                                    null) {
                                    if (row.spp_status_terima == 0) {
                                        return `
                                            <button type="button" class="btn btn-warning btn-sm btn-belum-bayar" onclick="ubahStatusBayar(this, ${row.spp_id}, ${row.spp_status_bayar})">
                                                Belum Dibayar
                                            </button>
                                            <input type="checkbox" class="checkbox-item-bayar" value="${row.spp_id}" style="transform: scale(2); margin-left: 8px;">
                                        `;
                                    } else if (row.spp_status_terima == 1) {
                                        return `
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="pembayaran(0,${row.sppn_id},1,false,true)">
                                                Sudah Diterima
                                            </button>
                                        `;
                                    } else if (row.spp_status_terima == 2) {
                                        return `
                                            <button type="button" class="btn btn-sm" style="background-color: #6E00FF; border-color: #6E00FF; color: white;"
                                                onclick="pembayaran(0,${row.sppn_id},1,false,true)">
                                                Sudah Dibayar Belum Upload Bukti Transfer
                                            </button>
                                        `;
                                    }
                                } else if (typeof row.sppb_no !== 'undefined' && row.sppb_no !== null &&
                                    typeof row.sppn_no !== 'undefined' && row.sppn_no !== null) {

                                    if (row.spp_status_bayar == 0 && row.spp_status_terima == 0) {
                                        return `
                                            <button type="button" class="btn btn-warning btn-sm btn-belum-bayar" onclick="ubahStatusBayar(this, ${row.spp_id}, ${row.spp_status_bayar})">
                                                Belum Dibayar
                                            </button>
                                            <input type="checkbox" class="checkbox-item-bayar" value="${row.spp_id}" style="transform: scale(2); margin-left: 8px;">
                                        `;
                                    } else if (row.spp_status_bayar == 1 && row.spp_status_terima == 0) {
                                        return `
                                            <button type="button" class="btn btn-warning btn-sm"
                                                onclick="pembayaran(${row.sppb_id},${row.sppn_id},2,true,true)">
                                                Belum Dibayar
                                            </button>
                                        `;
                                    } else if (row.spp_status_bayar == 2 && row.spp_status_terima == 0) {
                                        return `
                                            <button type="button" class="btn btn-sm" style="background-color: #6E00FF; border-color: #6E00FF; color: white;"
                                                onclick="pembayaran(${row.sppb_id},${row.sppn_id},2,true,true)">
                                                Sudah Dibayar Belum Upload Bukti Transfer
                                            </button>
                                        `;
                                    } else if (row.spp_status_bayar == 0 && row.spp_status_terima == 1) {
                                        return `
                                            <button type="button" class="btn btn-warning btn-sm btn-belum-bayar" onclick="ubahStatusBayar(this, ${row.spp_id}, ${row.spp_status_bayar})">
                                                Belum Dibayar
                                            </button>
                                            <input type="checkbox" class="checkbox-item-bayar" value="${row.spp_id}" style="transform: scale(2); margin-left: 8px;">
                                        `;
                                    } else if (row.spp_status_bayar == 1 && row.spp_status_terima == 1) {
                                        return `
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="pembayaran(${row.sppb_id},${row.sppn_id},2,true,true)">
                                                Sudah Dibayar
                                            </button>
                                        `;
                                    } else if (row.spp_status_bayar == 2 && row.spp_status_terima == 1) {
                                        return `
                                            <button type="button" class="btn btn-sm" style="background-color: #6E00FF; border-color: #6E00FF; color: white;"
                                                onclick="pembayaran(${row.sppb_id},${row.sppn_id},2,true,true)">
                                                Sudah Dibayar Belum Upload Bukti Transfer
                                            </button>
                                        `;
                                    } else if (row.spp_status_bayar == 1 && row.spp_status_terima == 2) {
                                        return `
                                            <button type="button" class="btn btn-sm" style="background-color: #6E00FF; border-color: #6E00FF; color: white;"
                                                onclick="pembayaran(${row.sppb_id},${row.sppn_id},2,true,true)">
                                                Sudah Dibayar Belum Upload Bukti Transfer
                                            </button>
                                        `;
                                    } else if (row.spp_status_bayar == 2 && row.spp_status_terima == 2) {
                                        return `
                                            <button type="button" class="btn btn-sm" style="background-color: #6E00FF; border-color: #6E00FF; color: white;"
                                                onclick="pembayaran(${row.sppb_id},${row.sppn_id},2,true,true)">
                                                Sudah Dibayar Belum Upload Bukti Transfer
                                            </button>
                                        `;
                                    }
                                }
                            } else if (row.sppd_status == 1) {
                                return `
                                    <button type="button" class="btn btn-default btn-sm" disabled>
                                        Belum Dibayar
                                    </button>
                                `;
                            } else if (row.sppd_status == 100) {
                                return `
                                    <button type="button" class="btn btn-default btn-sm" disabled>
                                        Selesai Dibayar
                                    </button>
                                `;
                            }
                        }
                    });
                }
                columns.push({
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        console.log(row.sppd_posisi);

                        var action = '';
                        var cetakUrl = "{{ route('cetakspp', ':id') }}".replace(':id', row.spp_id);
                        var rekamJejakUrl = "{{ route('rekamjejak', ':id') }}".replace(':id', row.hashedId);
                        var detailUrl = "{{ route('detailspp', ':id') }}".replace(':id', row
                            .spp_id);
                        if (grupId == 1 || grupId == 2 || grupId == 3 || grupId == 4 || grupId == 7 || grupId == 8 || grupId == 9) {
                            if (grupId == 1 || grupId == 8 || grupId == 9) {
                                // grupId 9 bisa lihat semua action tanpa cek sppd_posisi
                                if (grupId == 9 || row.sppd_posisi == hakAkses) {
                                    var editUrl = "{{ route('viewupdatesppd', ':id') }}".replace(':id', row
                                        .hashedId);
                                    action += `
                                        <button type="button" class="btn btn-success btn-sm" onclick="upload_kirim(${row.spp_id},'${row.spp_kabag ?? ''}')"
                                            title="kirim">
                                            <i class="fa fa-check"></i>
                                        </button>
                                        <a type="button" class="btn btn-warning btn-sm" href="${editUrl}" title="Edit Data">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                        </a>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="window.open('${cetakUrl}').print();"
                                            title="Cetak">
                                            <i class="fa fa-print"></i>
                                        </button>
                                    `;
                                }
                            }

                            if (grupId == 1 || grupId == 2 || grupId == 3 || grupId == 4 || grupId == 7) {
                                if (grupId == 2 || grupId == 7) {
                                    if (row.sppd_status == 1 && row.sppd_posisi == hakAkses) {
                                        action += `
                                            <a class="btn btn-success btn-sm" onclick="terima(${row.spp_id})" title="Terima">
                                                <i class="fa fa-check"></i>
                                            </a>
                                        `;
                                    } else if (row.sppd_status == 2 && row.sppd_posisi == hakAkses) {
                                        action += `
                                            <button type="button" class="btn btn-warning btn-sm" onclick="revisi(${row.spp_id })" title="Revisi">
                                                <i class="fa fa-arrow-left"></i>
                                            </button>
                                        `;

                                        if (grupId == 2) {
                                            action += `
                                                <button type="button" class="btn btn-success btn-sm" onclick="kirim(${row.spp_id})" title="Kirim">
                                                    <i class="fa fa-arrow-right"></i>
                                                </button>
                                            `;
                                        }

                                        if (row.sppd_proses == 1 && row.sppd_proses == 2) {
                                            action += `
                                                <a type="button" class="btn btn-warning btn-sm" href="${editUrl}" title="Edit Data">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                </a>
                                            `;
                                        }

                                        if (grupId == 7) {
                                            if (row.spp_no_dokumen != null) {
                                                action += `
                                                    <button type="button" class="btn btn-success btn-sm" onclick="kirim(${row.spp_id})" title="Kirim">
                                                        <i class="fa fa-arrow-right"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        onclick="upload_no_doc(${row.DT_RowIndex-1}, ${row.spp_id}, ${row.spp_no_dokumen})"
                                                        style="background-color: #800000; border-color: #800000" title="Upload No Doc">
                                                        <i class="fa fa-file"></i>
                                                    </button>
                                                `;
                                            } else {
                                                action += `
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        onclick="upload_no_doc(${row.DT_RowIndex-1}, ${row.spp_id}, ${row.spp_no_dokumen})"
                                                        style="background-color: #7CFC00 ;border-color: #7CFC00" title="Upload No Doc">
                                                        <i class="fa fa-file"></i>
                                                    </button>
                                                `;
                                            }
                                        }
                                    }

                                    if (row.sppd_status == 3 && row.sppd_posisi == hakAkses) {
                                        action += `
                                            <button type="button" class="btn btn-danger btn-sm" onclick="kirim(${row.spp_id})" title="Kirim">
                                                <i class="fa fa-arrow-left"></i>
                                            </button>
                                        `;
                                    }
                                }

                                if (grupId == 3) {
                                    if (row.sppd_status == 1 && row.sppd_posisi == hakAkses) {
                                        action += `
                                            <input type="checkbox" id="checkbox-item" class="checkbox-item" value="${row.spp_id}" style="transform: scale(2); margin-right: 8px;">
                                            <a class="btn btn-success btn-sm btn-terima" onClick="terima(${row.spp_id})" title="Terima">
                                                <i class="fa fa-check"></i>
                                            </a>
                                        `;
                                    } else if (row.sppd_status == 2 && row.sppd_posisi == hakAkses) {
                                        action += `
                                            <button type="button" class="btn btn-warning btn-sm" onclick="revisi(${row.spp_id})" title="Revisi">
                                                <i class="fa fa-arrow-left"></i>
                                            </button>
                                        `;

                                        if (grupId != 3) {
                                            action += `
                                                <button type="button" class="btn btn-success btn-sm" onclick="kirim(${row.spp_id})" title="Kirim">
                                                    <i class="fa fa-arrow-right"></i>
                                                </button>
                                            `;
                                        } else if (grupId == 3 && row.sppb_bukti_kas_id != null) {
                                            action += `
                                                <button type="button" class="btn btn-success btn-sm" onClick="kirim(${row.spp_id})" title="Kirim">
                                                    <i class="fa fa-arrow-right"></i>
                                                </button>
                                            `;
                                        }

                                        if (typeof row.sppb_no !== 'undefined' && row.sppb_no !== null && !row
                                            .sppn_no) {

                                            if (row.nomor_byr == null) {
                                                action += `
                                                    <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},${row.sppb_id},0,0,false,false,true,true)">
                                                        <i class="fa fa-money"></i>
                                                    </button>
                                                `;
                                            } else if (row.nomor_byr != null) {
                                                action += `
                                                    <button type="button" class="btn btn-primary btn-sm" style="background-color: #6E00FF;border-color: #6E00FF" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}', ${row.spp_id}, ${row.sppb_id}, 0, 0, true, false, true, true)">
                                                        <i class="fa fa-money"></i>
                                                    </button>
                                                `;
                                            }
                                        } else if (!row.sppb_no && typeof row.sppn_no !== 'undefined' && row
                                            .sppn_no !== null) {
                                            if (row.nomor_pnr == null) {
                                                action += `
                                                    <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id },0,${row.sppn_id},1,false,false,true,true)">
                                                        <i class="fa fa-money"></i>
                                                    </button>
                                                `;
                                            } else if (row.nomor_pnr != null) {
                                                action += `
                                                    <button type="button" class="btn btn-success btn-sm" onclick="kirim(${row.spp_id})" title="Kirim">
                                                        <i class="fa fa-arrow-right"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-sm" style="background-color: #6E00FF; border-color: #6E00FF" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},0,${row.sppn_id},1,false,true,true,true)">
                                                        <i class="fa fa-money"></i>
                                                    </button>
                                                `;
                                            }
                                        } else if (typeof row.sppb_no !== 'undefined' && row.sppb_no !== null &&
                                            typeof row.sppn_no !== 'undefined' && row.sppn_no !== null) {
                                            if (row.nomor_byr == null && row.nomor_pnr == null) {
                                                action += `
                                                    <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onclick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},${row.sppb_id},${row.sppn_id},2,false,false,true,true)">
                                                        <i class="fa fa-money"></i>
                                                    </button>
                                                `;
                                            } else if (row.nomor_byr != null && row.nomor_pnr == null) {
                                                action += `
                                                    <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},${row.sppb_id},${sppn_id},2,true,false,true,true)">
                                                        <i class="fa fa-money"></i>
                                                    </button>
                                                `;
                                            } else if (row.nomor_byr == null && row.nomor_pnr != null) {
                                                action += `
                                                    <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},${row.sppb_id},${row.sppn_id},2,false,true,true,true)">
                                                        <i class="fa fa-money"></i>
                                                    </button>
                                                `;
                                            } else if (row.nomor_byr != null && row.nomor_pnr != null) {
                                                // var sppbCetak = encodeURIComponent(JSON.stringify(row.sppb_cetak_bukti_kas[row.DT_RowIndex-1]));
                                                // var sppnCetak = encodeURIComponent(JSON.stringify(row.sppn_cetak_bukti_kas[row.DT_RowIndex-1]));
                                                // var dataTerima = encodeURIComponent(JSON.stringify(row.data_penerima[row.DT_RowIndex-1]));
                                                // var dataDiterima = encodeURIComponent(JSON.stringify(row.data_diterima[row.DT_RowIndex-1]));
                                                action += `
                                                    <button type="button" class="btn btn-primary btn-sm" style="background-color: #6E00FF; border-color: #6E00FF" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},${row.sppb_id},${row.sppn_id},2,true,true,true,true)">
                                                        <i class="fa fa-money"></i>
                                                    </button>
                                                `;
                                            }
                                        }
                                    }
                                }

                                if (grupId == 4) {
                                    if (typeof row.sppb_no !== 'undefined' && row.sppb_no !== null && !
                                        row
                                        .sppn_no) {
                                        if (row.spp_status_bayar == 1 && row.spp_bukti_kas_bank != 0) {
                                            action += `
                                                <button type="button" class="btn btn-info btn-sm" onclick="selesai(${row.spp_id})" title="Selesai">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            `;
                                        } else {
                                            if (row.sppd_status == 1 && row.sppd_posisi == hakAkses) {
                                                action += `
                                                    <input type="checkbox" id="checkbox-item" class="checkbox-item" value="${row.spp_id}" style="transform: scale(2); margin-right: 8px;">
                                                    <a class="btn btn-success btn-sm btn-terima" onclick="terima(${row.spp_id})" title="Terima">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                `;
                                            } else if (row.sppd_status == 2 && row.sppd_posisi == hakAkses) {
                                                if (row.spp_status_bayar != 2) {
                                                    action += `
                                                        <button type="button" class="btn btn-warning btn-sm" onclick="revisi(${row.spp_id})" title="Revisi">
                                                            <i class="fa fa-arrow-left"></i>
                                                        </button>
                                                    `;
                                                } else {
                                                    action += `
                                                        <button type="button" class="btn btn-warning btn-sm" onclick="revisi(${row.spp_id})" title="Revisi">
                                                            <i class="fa fa-arrow-left"></i>
                                                        </button>
                                                    `;
                                                }
                                            }
                                        }
                                    } else if (!row.sppb_no && typeof row.sppn_no !== 'undefined' && row
                                        .sppn_no !== null) {
                                        if (row.spp_status_terima == 1 && row.spp_bukti_kas_bank != 0) {
                                            action += `
                                                <button type="button" class="btn btn-info btn-sm" onclick="selesai(${row.spp_id})" title="Selesai">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            `;
                                        } else {
                                            if (row.sppd_status == 1 && row.sppd_posisi == hakAkses) {
                                                action += `
                                                    <input type="checkbox" id="checkbox-item" class="checkbox-item" value="${row.spp_id}" style="transform: scale(2); margin-right: 8px;">
                                                    <a class="btn btn-success btn-sm btn-terima" onclick="terima(${row.spp_id})" title="Terima">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                `;
                                            } else if (row.sppd_status == 2 && row.sppd_posisi == hakAkses) {
                                                if (row.spp_status_bayar != 2 && row.spp_status_terima != 2) {
                                                    action += `
                                                        <button type="button" class="btn btn-warning btn-sm" onclick="revisi(${row.spp_id})" title="Revisi">
                                                            <i class="fa fa-arrow-left"></i>
                                                        </button>
                                                    `;
                                                } else {
                                                    action += `
                                                        <button type="button" class="btn btn-warning btn-sm" onclick="revisi(${row.spp_id})" title="Revisi">
                                                            <i class="fa fa-arrow-left"></i>
                                                        </button>
                                                    `;
                                                }
                                            }
                                        }
                                    } else if (typeof row.sppb_no !== 'undefined' && row.sppb_no !== null &&
                                        typeof row.sppn_no !== 'undefined' && row.sppn_no !== null) {
                                        if (row.spp_status_bayar == 1 && row.spp_status_terima == 1 && row
                                            .spp_bukti_kas_bank != 0) {
                                            action += `
                                                <button type="button" class="btn btn-info btn-sm" onclick="selesai(${row.spp_id})" title="Selesai">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            `;
                                        } else {
                                            if (row.sppd_status == 1 && row.sppd_posisi == hakAkses) {
                                                action += `
                                                    <input type="checkbox" id="checkbox-item" class="checkbox-item" value="${row.spp_id}" style="transform: scale(2); margin-right: 8px;">
                                                    <a class="btn btn-success btn-sm btn-terima" onclick="terima(${row.spp_id})" title="Terima">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                `;
                                            } else if (row.sppd_status == 2 && row.sppd_posisi == hakAkses) {
                                                if (row.spp_status_bayar == 0 && row.spp_status_terima == 0) {
                                                    action += `
                                                        <button type="button" class="btn btn-warning btn-sm" onclick="revisi(${row.spp_id})" title="Revisi">
                                                            <i class="fa fa-arrow-left"></i>
                                                        </button>
                                                    `;
                                                } else {
                                                    action += `
                                                        <button type="button" class="btn btn-warning btn-sm" onclick="revisi(${row.spp_id})" title="Revisi">
                                                            <i class="fa fa-arrow-left"></i>
                                                        </button>
                                                    `;
                                                }
                                            }
                                        }
                                    }
                                    action += `
                                        <button type="button" class="btn btn-info btn-sm" onclick="window.open('${detailUrl}')" title="Detail">
                                            <i class="fa fa-info"></i>
                                        </button>
                                    `;

                                    if (row.spp_bukti_kas_bank == null && row.sppd_status == 2) {
                                        action += `
                                            <button type="button" class="btn btn-warning btn-sm" onClick="upload_bukti_kas(${row.spp_id},'${row.spp_bukti_kas_bank}')">
                                                <i class="fa fa-upload"></i>
                                            </button>
                                        `;
                                    } else if (row.spp_bukti_kas_bank == null && row.sppd_status == 1) {} else {
                                        action += `
                                        <button type="button" style="background-color: #6E00FF; border-color: #6E00FF" class="btn btn-warning btn-sm" onclick="upload_bukti_kas(${row.spp_id},'${row.spp_bukti_kas_bank}')">
                                            <i class="fa fa-upload"></i>
                                        </button>
                                        `;
                                    }

                                    if (row.sppd_status == 100) {
                                        action += `
                                            <div hidden>
                                                <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung(${row.spp_id})"
                                                    title="Upload Dokumen Tambahan">
                                                    <i class="fa fa-upload"></i>
                                                </button>
                                            </div>
                                        `;
                                    } else {
                                        action += `
                                            <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung(${row.spp_id})"
                                                title="Upload Dokumen Tambahan">
                                                <i class="fa fa-upload"></i>
                                            </button>
                                        `;
                                    }
                                }

                                action += `
                                    <button type="button" class="btn btn-primary btn-sm" onclick="window.open('${rekamJejakUrl}')" title="Rekam Jejak">
                                        <i class="fa fa-map-o"></i>
                                    </button>
                                `;

                                if (grupId == 1 || grupId == 2 || grupId == 3 || grupId == 7 || grupId == 8 ||
                                    grupId == 9) {
                                    action += `
                                        <button type="button" class="btn btn-info btn-sm" onclick="window.open('${detailUrl}')" title="Detail">
                                            <i class="fa fa-info"></i>
                                        </button>
                                        <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung(${row.spp_id})" title="Upload Dokumen Tambahan">
                                            <i class="fa fa-upload"></i>
                                        </button>
                                    `;
                                }
                                if (grupId == 1) {
                                    action += `
                                        <button type="button" class="btn btn-danger btn-sm" onclick="batal(${row.spp_id})" title="Batalkan">
                                            <i class="fa fa-ban" aria-hidden="true"></i>
                                        </button>
                                    `;
                                }

                                if (grupId == 8 || grupId == 9) {
                                    var editUrl = "{{ route('viewupdatesppd', ':id') }}".replace(':id', row
                                        .hashedId);
                                    action += `
                                        <a type="button" class="btn btn-warning btn-sm" href="${editUrl}" title="Edit Data">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                        </a>
                                    `;
                                }
                            }
                        }
                        return action;
                    }
                });
            };
            return columns;
        };

        function toggleReadMore(el) {
            var parent = el.parentElement;
            var shortText = parent.querySelector('.text-short');
            var fullText  = parent.querySelector('.text-full');

            if (fullText.style.display === 'none') {
                fullText.style.display = 'inline';
                shortText.style.display = 'none';
                el.innerText = 'Read less';
            } else {
                fullText.style.display = 'none';
                shortText.style.display = 'inline';
                el.innerText = 'Read more';
            }
        }

        function dataColumnsRevisi() {
            var columns = [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<strong>' + data + '</strong>';
                }
            }];

            if (grupId == 1 || grupId == 2 || grupId == 3 || grupId == 4 || grupId == 7 || grupId == 8 ||
                grupId == 9) {
                if (grupId == 2 || grupId == 3 || grupId == 4 || grupId == 7) {
                    columns.push({
                        data: 'master_bagian_nama',
                        name: 'master_bagian_nama',
                        render: function(data, type, row) {
                            if (!data) {
                                return '<strong></strong>';
                            }
                            return '<strong>' + data + '</strong>';
                        }
                    });
                }

                columns.push({
                    data: 'tanggal',
                    name: 'tanggal',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        return '<strong>' + data + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppb_no',
                    name: 'sppb_no',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        return '<strong>' + data + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppb_uraian2',
                    name: 'sppb_uraian2',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        // Menghapus semua tag HTML
                        // Menghapus semua tag HTML
                        var strippedText = data.replace(/<[^>]+>/g, "");

                        // Decode entitas HTML
                        var decodedText = $("<textarea />").html(strippedText).text();

                        var limit=75;

                        // Jika pendek, tampilkan langsung
                        if (decodedText.length <= limit) {
                            return '<strong>' + decodedText + '</strong>';
                        }

                         // ID unik per baris
                        var uid = 'uraian_sppb_' + row.spp_id;

                        var shortText = decodedText.substring(0, limit);

                        // Mengembalikan teks dalam tag <strong>
                        return `<strong>
                            <span class="text-short" style="display:inline;">${shortText}...</span>

                            <div class="text-full" style="display:none; margin-top:5px; white-space:normal;">
                                ${decodedText}
                            </div>

                            <a href="javascript:void(0)"
                            onclick="toggleReadMore(this)"
                            style="color:#007bff; font-weight:600; display:inline-block; margin-top:4px;">
                            Read more
                            </a>
                        </strong>`;
                    }
                });
                columns.push({
                    data: 'sppb_total',
                    name: 'sppb_total',
                    render: function(data, type, row) {
                        // Pastikan data valid dan bukan null atau undefined
                        if (!row.sppb_id) {
                            return '<strong></strong>';
                        }

                        // Format angka dengan toLocaleString (menambahkan tanda pemisah ribuan)
                        var formattedNumber = new Intl.NumberFormat('id', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0
                        }).format(data);

                        // Kembalikan nilai dalam tag <strong>
                        return '<strong>' + formattedNumber + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppn_no',
                    name: 'sppn_no',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        return '<strong>' + data + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppn_uraian2',
                    name: 'sppn_uraian2',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        // Menghapus semua tag HTML
                        // Menghapus semua tag HTML
                        var strippedText = data.replace(/<[^>]+>/g, "");

                        // Decode entitas HTML
                        var decodedText = $("<textarea />").html(strippedText).text();

                        var limit=75;

                        // Jika pendek, tampilkan langsung
                        if (decodedText.length <= limit) {
                            return '<strong>' + decodedText + '</strong>';
                        }

                         // ID unik per baris
                        var uid = 'uraian_sppn_' + row.spp_id;

                        var shortText = decodedText.substring(0, limit);

                        // Mengembalikan teks dalam tag <strong>
                        return `<strong>
                            <span class="text-short" style="display:inline;">${shortText}...</span>

                            <div class="text-full" style="display:none; margin-top:5px; white-space:normal;">
                                ${decodedText}
                            </div>

                            <a href="javascript:void(0)"
                            onclick="toggleReadMore(this)"
                            style="color:#007bff; font-weight:600; display:inline-block; margin-top:4px;">
                            Read more
                            </a>
                        </strong>`;
                    }
                });
                columns.push({
                    data: 'sppn_jumlah',
                    name: 'sppn_jumlah',
                    render: function(data, type, row) {
                        // Pastikan data valid dan bukan null atau undefined
                        if (!row.sppn_id || !data) {
                            return '<strong></strong>';
                        }

                        var formattedNumber = new Intl.NumberFormat('id', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0
                        }).format(data);

                        // Kembalikan nilai dalam tag <strong>
                        return '<strong>' + formattedNumber + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppd_status',
                    name: 'sppd_status',
                    render: function(data, type, row) {
                        if (data == 100) {
                            return '<strong>Selesai</strong>';
                        } else {
                            return '<strong>' + row.master_hak_akses_nama + '</strong>';
                        }
                    }
                });

                columns.push({
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var action = '';
                        var rekamJejakUrl = "{{ route('rekamjejak', ':id') }}".replace(':id', row
                            .hashedId);
                        var detailUrl = "{{ route('detailspp', ':id') }}".replace(':id', row.spp_id);

                        if (grupId == 1 || grupId == 8 || grupId == 9) {
                            var editUrl = "{{ route('viewupdatesppd', ':id') }}".replace(':id', row.hashedId);
                            var cetakUrl = "{{ route('cetakspp', ':id') }}".replace(':id', row.spp_id);
                            action += `
                                <button type="button" class="btn btn-success btn-sm" onClick="upload_kirim(${row.spp_id},'${row.spp_kabag}')" title="kirim">
                                    <i class="fa fa-check"></i>
                                </button>
                                <a type="button" class="btn btn-warning btn-sm" href="${editUrl}" title="Edit Data">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                </a>
                                <button type="button" class="btn btn-primary btn-sm" onclick="window.open('${cetakUrl}').print();" title="Cetak">
                                    <i class="fa fa-print"></i>
                                </button>
                            `;
                        } else if (grupId == 2 || grupId == 7) {
                            if (row.sppd_status == 2 && row.sppd_posisi == hakAkses) {
                                action += `
                                    <button type="button" class="btn btn-warning btn-sm" onclick="revisi(${row.spp_id})" title="Revisi">
                                        <i class="fa fa-arrow-left"></i>
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm" onclick="kirim(${row.spp_id})" title="Kirim">
                                        <i class="fa fa-arrow-right"></i>
                                    </button>
                                `;

                                if (row.sppd_proses == 1 && row.sppd_proses == 2) {
                                    var editUrl = "{{ route('viewupdatesppd', ':id') }}".replace(':id', row
                                        .hashedId);
                                    action += `
                                        <a type="button" class="btn btn-warning btn-sm" href="${editUrl}" title="Edit Data">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                        </a>
                                    `;
                                }

                                if (grupId == 7) {
                                    if (row.spp_no_dokumen) {
                                        action += `
                                            <button type="button" class="btn btn-primary btn-sm" onClick="upload_no_doc(${row.DT_RowIndex},${row.spp_id},${row.spp_no_dokumen})" style="background-color: #800000; border-color: #800000" title="Upload No Doc">
                                                <i class="fa fa-file"></i>
                                            </button>
                                        `;
                                    } else {
                                        action += `
                                            <button type="button" class="btn btn-primary btn-sm" onClick="upload_no_doc(${row.DT_RowIndex},${row.spp_id},${row.spp_no_dokumen})" style="background-color: #7CFC00 ;border-color: #7CFC00" title="Upload No Doc">
                                                <i class="fa fa-file"></i>
                                            </button>
                                        `;
                                    }
                                }
                            }

                            if (row.sppd_status == 3 && row.sppd_posisi == hakAkses) {
                                action += `
                                <button type="button" class="btn btn-danger btn-sm" onclick="kirim(${row.spp_id})" title="Kirim">
                                    <i class="fa fa-arrow-left"></i>
                                </button>
                                `;
                            }
                        } else if (grupId == 3) {
                            if (row.sppd_status == 2 && row.sppd_posisi == hakAkses) {
                                action += `
                                <button type="button" class="btn btn-warning btn-sm" onclick="revisi(${row.spp_id})" title="Revisi">
                                    <i class="fa fa-arrow-left"></i>
                                </button>
                                <button type="button" class="btn btn-success btn-sm" onClick="kirim(${row.spp_id})" title="Kirim">
                                    <i class="fa fa-arrow-right"></i>
                                </button>
                                `;
                            }

                            if (row.sppd_status == 3 && row.sppd_posisi == hakAkses) {
                                action += `
                                <button type="button" class="btn btn-success btn-sm" onclick="kirim(${row.spp_id})" title="Kirim">
                                    <i class="fa fa-check"></i>
                                </button>
                                `;

                                if (typeof row.sppb_no !== 'undefined' && row.sppb_no !== null && !row
                                    .sppn_no) {
                                    if (row.nomor_byr == null) {
                                        action += `
                                            <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},${row.sppb_id},0,0,false,false,true,true)">
                                                <i class="fa fa-money"></i>
                                            </button>
                                        `;
                                    } else if (row.nomor_byr != null) {
                                        action += `
                                            <button type="button" class="btn btn-primary btn-sm" style="background-color: #6E00FF;border-color: #6E00FF" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},${row.sppb_id},0,0,true,false,true,true)">
                                                <i class="fa fa-money"></i>
                                            </button>
                                        `;
                                    }
                                } else if (!row.sppb_no && typeof row.sppn_no !== 'undefined' && row.sppn_no !==
                                    null) {
                                    if (row.nomor_pnr == null) {
                                        action += `
                                        <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},0,${row.sppn_id},1,false,false,true,true)">
                                            <i class="fa fa-money"></i>
                                        </button>
                                        `;
                                    } else if (row.nomor_pnr != null) {
                                        action += `
                                            <button type="button" class="btn btn-primary btn-sm" style="background-color: #6E00FF; border-color: #6E00FF" title="Bukti Kas" onclick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},0,${row.sppn_id},1,false,true,true,true)">
                                                <i class="fa fa-money"></i>
                                            </button>
                                        `;
                                    }
                                } else if (typeof row.sppb_no !== 'undefined' && row.sppb_no !== null &&
                                    typeof row.sppn_no !== 'undefined' && row.sppn_no !== null) {
                                    if (row.nomor_byr == null && row.nomor_pnr == null) {
                                        action += `
                                            <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},${row.sppb_id},${row.sppn_id},2,false,false,true,true)">
                                                <i class="fa fa-money"></i>
                                            </button>
                                        `;
                                    } else if (row.nomor_byr != null && row.nomor_pnr == null) {
                                        action += `
                                            <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},${row.sppb_id},${sppn_id},2,true,false,true,true)">
                                                <i class="fa fa-money"></i>
                                            </button>
                                        `;
                                    } else if (row.nomor_byr == null && row.nomor_pnr != null) {
                                        action += `
                                        <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},${row.sppb_id},${row.sppn_id},2,false,true,true,true)">
                                            <i class="fa fa-money"></i>
                                        </button>
                                        `;
                                    } else if (row.nomor_byr != null && row.nomor_pnr != null) {
                                        // var sppbCetak = encodeURIComponent(JSON.stringify(row.sppb_cetak_bukti_kas[row.DT_RowIndex-1]));
                                        // var sppnCetak = encodeURIComponent(JSON.stringify(row.sppn_cetak_bukti_kas[row.DT_RowIndex-1]));
                                        // var dataPenerima = encodeURIComponent(JSON.stringify(row.data_penerima[row.DT_RowIndex-1]));
                                        // var dataDiterima = encodeURIComponent(JSON.stringify(row.data_diterima[row.DT_RowIndex-1]));
                                        action += `
                                        <button type="button" class="btn btn-primary btn-sm" style="background-color: #6E00FF; border-color: #6E00FF" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},${row.sppb_id},${row.sppn_id},2,true,true,true,true)">
                                            <i class="fa fa-money"></i>
                                        </button>
                                        `;
                                    }
                                }
                            }
                        } else if (grupId == 4) {
                            action += `
                            <button type="button" class="btn btn-info btn-sm" onClick="window.open(${detailUrl})" title="Detail">
                                <i class="fa fa-info"></i>
                            </button>
                            `;

                            if (row.sppd_status == 100) {
                                action += `
                                <div hidden>
                                    <button type="button" class="btn btn-info btn-sm" onClick="upload_dokumen_pendukung(${row.spp_id})"
                                        title="Upload Dokumen Tambahan">
                                        <i class="fa fa-upload"></i>
                                    </button>
                                </div>
                                `;
                            } else {
                                action += `
                                <button type="button" class="btn btn-info btn-sm" onClick="upload_dokumen_pendukung(${row.spp_id})"
                                    title="Upload Dokumen Tambahan">
                                    <i class="fa fa-upload"></i>
                                </button>
                                `;
                            }
                        }

                        action += `
                        <button type="button" class="btn btn-primary btn-sm" onclick="window.open('${rekamJejakUrl}')"
                            title="Rekam Jejak">
                            <i class="fa fa-map-o"></i>
                        </button>
                        `;

                        if (grupId == 1 || grupId == 2 || grupId == 3 || grupId == 7 || grupId == 8 ||
                            grupId == 9) {
                            action += `
                            <button type="button" class="btn btn-info btn-sm" onclick="window.open('${detailUrl}')" title="Detail">
                                <i class="fa fa-info"></i>
                            </button>
                            <button type="button" class="btn btn-info btn-sm" onclick="upload_dokumen_pendukung(${row.spp_id})"
                                title="Upload Dokumen Tambahan"><i class="fa fa-upload"></i>
                            </button>
                            `;
                        }

                        if (grupId == 1) {
                            action += `
                            <button type="button" class="btn btn-danger btn-sm" onclick="batal(${row.spp_id})" title="Batalkan">
                                <i class="fa fa-ban" aria-hidden="true"></i>
                            </button>
                            `;
                        }

                        if (grupId == 8 || grupId == 9) {
                            var editUrl = "{{ route('viewupdatesppd', ':id') }}".replace(':id', row.hashedId);
                            action += `
                            <a type="button" class="btn btn-warning btn-sm" href="${editUrl}" title="Edit Data">
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                            </a>
                            `;
                        }

                        return action;
                    }
                })
            }

            return columns;
        }

        function dataColumnsProses() {
            var columns = [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<strong>' + data + '</strong>';
                }
            }];

            if (grupId == 1 || grupId == 2 || grupId == 3 || grupId == 4 || grupId == 7 || grupId == 8 ||
                grupId == 9) {
                if (grupId == 2 || grupId == 3 || grupId == 4 || grupId == 7) {
                    columns.push({
                        data: 'master_bagian_nama',
                        name: 'master_bagian_nama',
                        render: function(data, type, row) {
                            if (!data) {
                                return '<strong></strong>';
                            }
                            return '<strong>' + data + '</strong>';
                        }
                    });
                }

                columns.push({
                    data: 'tanggal',
                    name: 'tanggal',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        return '<strong>' + data + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppb_no',
                    name: 'sppb_no',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        return '<strong>' + data + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppb_uraian2',
                    name: 'sppb_uraian2',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        // Menghapus semua tag HTML
                        // Menghapus semua tag HTML
                        var strippedText = data.replace(/<[^>]+>/g, "");

                        // Decode entitas HTML
                        var decodedText = $("<textarea />").html(strippedText).text();

                        var limit=75;

                        // Jika pendek, tampilkan langsung
                        if (decodedText.length <= limit) {
                            return '<strong>' + decodedText + '</strong>';
                        }

                         // ID unik per baris
                        var uid = 'uraian_sppb_' + row.spp_id;

                        var shortText = decodedText.substring(0, limit);

                        // Mengembalikan teks dalam tag <strong>
                        return `<strong>
                            <span class="text-short" style="display:inline;">${shortText}...</span>

                            <div class="text-full" style="display:none; margin-top:5px; white-space:normal;">
                                ${decodedText}
                            </div>

                            <a href="javascript:void(0)"
                            onclick="toggleReadMore(this)"
                            style="color:#007bff; font-weight:600; display:inline-block; margin-top:4px;">
                            Read more
                            </a>
                        </strong>`;
                    }
                });
                columns.push({
                    data: 'sppb_total',
                    name: 'sppb_total',
                    render: function(data, type, row) {
                        // Pastikan data valid dan bukan null atau undefined
                        if (!row.sppb_id) {
                            return '<strong></strong>';
                        }

                        // Format angka dengan toLocaleString (menambahkan tanda pemisah ribuan)
                        var formattedNumber = new Intl.NumberFormat('id', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0
                        }).format(data);

                        // Kembalikan nilai dalam tag <strong>
                        return '<strong>' + formattedNumber + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppn_no',
                    name: 'sppn_no',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        return '<strong>' + data + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppn_uraian2',
                    name: 'sppn_uraian2',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        // Menghapus semua tag HTML
                        // Menghapus semua tag HTML
                        var strippedText = data.replace(/<[^>]+>/g, "");

                        // Decode entitas HTML
                        var decodedText = $("<textarea />").html(strippedText).text();

                        var limit=75;

                        // Jika pendek, tampilkan langsung
                        if (decodedText.length <= limit) {
                            return '<strong>' + decodedText + '</strong>';
                        }

                         // ID unik per baris
                        var uid = 'uraian_sppn_' + row.spp_id;

                        var shortText = decodedText.substring(0, limit);

                        // Membatasi teks hingga 75 karakter
                        //var limitedText = decodedText.length > 75 ? decodedText.substring(0, 75) + '...' :
                         //   decodedText;

                        // Mengembalikan teks dalam tag <strong>
                       return `<strong>
                            <span id="${uid}_short" style="display:inline;">${shortText}...</span>

                            <div id="${uid}_full" style="display:none; margin-top:5px; white-space:normal;">
                                ${decodedText}
                            </div>

                            <a href="javascript:void(0)"
                            onclick="toggleReadMore('${uid}', this)"
                            style="color:#007bff; font-weight:600; display:inline-block; margin-top:4px;">
                            Read more
                            </a>
                        </strong>`;
                    }
                });
                columns.push({
                    data: 'sppn_jumlah',
                    name: 'sppn_jumlah',
                    render: function(data, type, row) {
                        // Pastikan data valid dan bukan null atau undefined
                        if (!row.sppn_id || !data) {
                            return '<strong></strong>';
                        }

                        var formattedNumber = new Intl.NumberFormat('id', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0
                        }).format(data);

                        // Kembalikan nilai dalam tag <strong>
                        return '<strong>' + formattedNumber + '</strong>';
                    }
                });

                columns.push({
                    data: 'sppd_status',
                    name: 'sppd_status',
                    render: function(data, type, row) {
                        if (data == 100) {
                            return '<strong>Selesai</strong>';
                        } else {
                            return '<strong>' + row.master_hak_akses_nama + '</strong>';
                        }
                    }
                });

                columns.push({
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        action = '';
                        var cetakUrl = "{{ route('cetakspp', ':id') }}".replace(':id', row.spp_id);
                        var rekamJejakUrl = "{{ route('rekamjejak', ':id') }}".replace(':id', row.hashedId);
                        var detailUrl = "{{ route('detailspp', ':id') }}".replace(':id', row.spp_id);

                        if (grupId == 1 || grupId == 2 || grupId == 3 || grupId == 4 || grupId == 7 || grupId ==
                            8 || grupId == 9) {
                            action += `
                            <button type="button" class="btn btn-primary btn-sm"
                                onClick="window.open('${cetakUrl}').print();" title="Cetak">
                                <i class="fa fa-print"></i>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" onClick="window.open('${rekamJejakUrl}')"
                                title="Rekam Jejak">
                                <i class="fa fa-map-o"></i>
                            </button>
                            <button type="button" class="btn btn-info btn-sm" onClick="window.open('${detailUrl}')"
                                title="Detail">
                                <i class="fa fa-info"></i>
                            </button>
                            <button type="button" class="btn btn-info btn-sm"
                                onClick="upload_dokumen_pendukung(${row.spp_id})" title="Upload Dokumen Tambahan">
                                <i class="fa fa-upload"></i>
                            </button>
                            `;
                        }

                        return action;
                    }
                });
            }

            return columns;
        }

        function dataColumnsSelesai() {
            var columns = [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<strong>' + data + '</strong>';
                }
            }, ];

            if (grupId == 1 || grupId == 2 || grupId == 3 || grupId == 4 || grupId == 7 || grupId == 8 ||
                grupId == 9) {
                if (grupId == 2 || grupId == 3 || grupId == 4 || grupId == 7) {
                    columns.push({
                        data: 'master_bagian_nama',
                        name: 'master_bagian_nama',
                        render: function(data, type, row) {
                            if (!data) {
                                return '<strong></strong>';
                            }
                            return '<strong>' + data + '</strong>';
                        }
                    });
                }

                columns.push({
                    data: 'tanggal',
                    name: 'tanggal',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        return '<strong>' + data + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppb_no',
                    name: 'sppb_no',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        return '<strong>' + data + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppb_uraian2',
                    name: 'sppb_uraian2',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        // Menghapus semua tag HTML
                        // Menghapus semua tag HTML
                        var strippedText = data.replace(/<[^>]+>/g, "");

                        // Decode entitas HTML
                        var decodedText = $("<textarea />").html(strippedText).text();

                        var limit=75;

                        // Jika pendek, tampilkan langsung
                        if (decodedText.length <= limit) {
                            return '<strong>' + decodedText + '</strong>';
                        }

                         // ID unik per baris
                        var uid = 'uraian_sppb_' + row.spp_id;

                        var shortText = decodedText.substring(0, limit);

                        // Mengembalikan teks dalam tag <strong>
                        return `<strong>
                            <span class="text-short" style="display:inline;">${shortText}...</span>

                            <div class="text-full" style="display:none; margin-top:5px; white-space:normal;">
                                ${decodedText}
                            </div>

                            <a href="javascript:void(0)"
                            onclick="toggleReadMore(this)"
                            style="color:#007bff; font-weight:600; display:inline-block; margin-top:4px;">
                            Read more
                            </a>
                        </strong>`;
                    }
                });
                columns.push({
                    data: 'sppb_total',
                    name: 'sppb_total',
                    render: function(data, type, row) {
                        // Pastikan data valid dan bukan null atau undefined
                        if (!row.sppb_id) {
                            return '<strong></strong>';
                        }

                        // Format angka dengan toLocaleString (menambahkan tanda pemisah ribuan)
                        var formattedNumber = new Intl.NumberFormat('id', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0
                        }).format(data);

                        // Kembalikan nilai dalam tag <strong>
                        return '<strong>' + formattedNumber + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppn_no',
                    name: 'sppn_no',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        return '<strong>' + data + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppn_uraian2',
                    name: 'sppn_uraian2',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        // Menghapus semua tag HTML
                        // Menghapus semua tag HTML
                        var strippedText = data.replace(/<[^>]+>/g, "");

                        // Decode entitas HTML
                        var decodedText = $("<textarea />").html(strippedText).text();

                        var limit=75;

                        // Jika pendek, tampilkan langsung
                        if (decodedText.length <= limit) {
                            return '<strong>' + decodedText + '</strong>';
                        }

                         // ID unik per baris
                        var uid = 'uraian_sppn_' + row.spp_id;

                        var shortText = decodedText.substring(0, limit);

                        // Mengembalikan teks dalam tag <strong>
                        return `<strong>
                            <span class="text-short" style="display:inline;">${shortText}...</span>

                            <div class="text-full" style="display:none; margin-top:5px; white-space:normal;">
                                ${decodedText}
                            </div>

                            <a href="javascript:void(0)"
                            onclick="toggleReadMore(this)"
                            style="color:#007bff; font-weight:600; display:inline-block; margin-top:4px;">
                            Read more
                            </a>
                        </strong>`;
                    }
                });
                columns.push({
                    data: 'sppn_jumlah',
                    name: 'sppn_jumlah',
                    render: function(data, type, row) {
                        // Pastikan data valid dan bukan null atau undefined
                        if (!row.sppn_id || !data) {
                            return '<strong></strong>';
                        }

                        var formattedNumber = new Intl.NumberFormat('id', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0
                        }).format(data);

                        // Kembalikan nilai dalam tag <strong>
                        return '<strong>' + formattedNumber + '</strong>';
                    }
                });

                columns.push({
                    data: 'sppd_status',
                    name: 'sppd_status',
                    render: function(data, type, row) {
                        if (data == 100) {
                            return '<strong>Selesai</strong>';
                        } else {
                            return '<strong>' + row.master_hak_akses_nama + '</strong>';
                        }
                    }
                });

                columns.push({
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        action = '';
                        var rekamJejakUrl = "{{ route('rekamjejak', ':id') }}".replace(':id', row.hashedId);
                        var detailUrl = "{{ route('detailspp', ':id') }}".replace(':id', row.spp_id);
                        if (grupId == 1 || grupId == 2 || grupId == 3 || grupId == 4 || grupId == 7 || grupId ==
                            8 || grupId == 9) {
                            action += `
                                <button type="button" class="btn btn-primary btn-sm"
                                    onclick="window.open('${rekamJejakUrl}')" title="Rekam Jejak">
                                    <i class="fa fa-map-o"></i>
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="window.open('${detailUrl}')"
                                    title="Detail">
                                    <i class="fa fa-info"></i>
                                </button>
                            `;
                        }

                        // Tombol Cetak Bukti Kas Bank khusus masterUserId == 307 dan grupId == 4
                        if (masterUserId == 307 && grupId == 4) {
                            if (typeof row.sppb_no !== 'undefined' && row.sppb_no !== null && !row.sppn_no) {
                                if (row.nomor_byr == null) {
                                    action += `
                                        <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},${row.sppb_id},0,0,false,false,true,true)">
                                            <i class="fa fa-money"></i>
                                        </button>
                                    `;
                                } else if (row.nomor_byr != null) {
                                    action += `
                                        <button type="button" class="btn btn-primary btn-sm" style="background-color: #6E00FF;border-color: #6E00FF" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}', ${row.spp_id}, ${row.sppb_id}, 0, 0, true, false, true, true)">
                                            <i class="fa fa-money"></i>
                                        </button>
                                    `;
                                }
                            } else if (!row.sppb_no && typeof row.sppn_no !== 'undefined' && row.sppn_no !== null) {
                                if (row.nomor_pnr == null) {
                                    action += `
                                        <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},0,${row.sppn_id},1,false,false,true,true)">
                                            <i class="fa fa-money"></i>
                                        </button>
                                    `;
                                } else if (row.nomor_pnr != null) {
                                    action += `
                                        <button type="button" class="btn btn-primary btn-sm" style="background-color: #6E00FF; border-color: #6E00FF" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},0,${row.sppn_id},1,false,true,true,true)">
                                            <i class="fa fa-money"></i>
                                        </button>
                                    `;
                                }
                            } else if (typeof row.sppb_no !== 'undefined' && row.sppb_no !== null && typeof row.sppn_no !== 'undefined' && row.sppn_no !== null) {
                                if (row.nomor_byr == null && row.nomor_pnr == null) {
                                    action += `
                                        <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onclick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},${row.sppb_id},${row.sppn_id},2,false,false,true,true)">
                                            <i class="fa fa-money"></i>
                                        </button>
                                    `;
                                } else if (row.nomor_byr != null && row.nomor_pnr == null) {
                                    action += `
                                        <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},${row.sppb_id},${row.sppn_id},2,true,false,true,true)">
                                            <i class="fa fa-money"></i>
                                        </button>
                                    `;
                                } else if (row.nomor_byr == null && row.nomor_pnr != null) {
                                    action += `
                                        <button type="button" class="btn btn-warning btn-sm" style="background-color: #FF9000; border-color: #FF9000" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},${row.sppb_id},${row.sppn_id},2,false,true,true,true)">
                                            <i class="fa fa-money"></i>
                                        </button>
                                    `;
                                } else if (row.nomor_byr != null && row.nomor_pnr != null) {
                                    action += `
                                        <button type="button" class="btn btn-primary btn-sm" style="background-color: #6E00FF; border-color: #6E00FF" title="Bukti Kas" onClick="cetak_bukti_kas('${row.metode_pembayaran}',${row.spp_id},${row.sppb_id},${row.sppn_id},2,true,true,true,true)">
                                            <i class="fa fa-money"></i>
                                        </button>
                                    `;
                                }
                            }
                        }

                        return action;
                    }
                });
            }

            return columns;
        }

        function dataColumnsBatal() {
            var columns = [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<strong>' + data + '</strong>';
                }
            }, ];

            if (grupId == 1 || grupId == 2 || grupId == 3 || grupId == 4 || grupId == 7 || grupId ==
                8 || grupId == 9) {
                if (grupId == 2 || grupId == 3 || grupId == 4 || grupId == 7 || grupId == 9) {
                    columns.push({
                        data: 'master_bagian_nama',
                        name: 'master_bagian_nama',
                        render: function(data, type, row) {
                            if (!data) {
                                return '<strong></strong>';
                            }
                            return '<strong>' + data + '</strong>';
                        }
                    });
                }

                columns.push({
                    data: 'tanggal',
                    name: 'tanggal',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        return '<strong>' + data + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppb_no',
                    name: 'sppb_no',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        return '<strong>' + data + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppb_uraian2',
                    name: 'sppb_uraian2',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        // Menghapus semua tag HTML
                        // Menghapus semua tag HTML
                        var strippedText = data.replace(/<[^>]+>/g, "");

                        // Decode entitas HTML
                        var decodedText = $("<textarea />").html(strippedText).text();

                        var limit=75;

                        // Jika pendek, tampilkan langsung
                        if (decodedText.length <= limit) {
                            return '<strong>' + decodedText + '</strong>';
                        }

                         // ID unik per baris
                        var uid = 'uraian_sppb_' + row.spp_id;

                        var shortText = decodedText.substring(0, limit);

                        // Mengembalikan teks dalam tag <strong>
                        return `<strong>
                            <span class="text-short" style="display:inline;">${shortText}...</span>

                            <div class="text-full" style="display:none; margin-top:5px; white-space:normal;">
                                ${decodedText}
                            </div>

                            <a href="javascript:void(0)"
                            onclick="toggleReadMore(this)"
                            style="color:#007bff; font-weight:600; display:inline-block; margin-top:4px;">
                            Read more
                            </a>
                        </strong>`;
                    }
                });
                columns.push({
                    data: 'sppb_total',
                    name: 'sppb_total',
                    render: function(data, type, row) {
                        // Pastikan data valid dan bukan null atau undefined
                        if (!row.sppb_id) {
                            return '<strong></strong>';
                        }

                        // Format angka dengan toLocaleString (menambahkan tanda pemisah ribuan)
                        var formattedNumber = new Intl.NumberFormat('id', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0
                        }).format(data);

                        // Kembalikan nilai dalam tag <strong>
                        return '<strong>' + formattedNumber + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppn_no',
                    name: 'sppn_no',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        return '<strong>' + data + '</strong>';
                    }
                });
                columns.push({
                    data: 'sppn_uraian2',
                    name: 'sppn_uraian2',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<strong></strong>';
                        }
                        // Menghapus semua tag HTML
                        // Menghapus semua tag HTML
                        var strippedText = data.replace(/<[^>]+>/g, "");

                        // Decode entitas HTML
                        var decodedText = $("<textarea />").html(strippedText).text();

                        var limit=75;

                        // Jika pendek, tampilkan langsung
                        if (decodedText.length <= limit) {
                            return '<strong>' + decodedText + '</strong>';
                        }

                         // ID unik per baris
                        var uid = 'uraian_sppn_' + row.spp_id;

                        var shortText = decodedText.substring(0, limit);

                        // Mengembalikan teks dalam tag <strong>
                        return `<strong>
                            <span class="text-short" style="display:inline;">${shortText}...</span>

                            <div class="text-full" style="display:none; margin-top:5px; white-space:normal;">
                                ${decodedText}
                            </div>

                            <a href="javascript:void(0)"
                            onclick="toggleReadMore(this)"
                            style="color:#007bff; font-weight:600; display:inline-block; margin-top:4px;">
                            Read more
                            </a>
                        </strong>`;
                    }
                });
                columns.push({
                    data: 'sppn_jumlah',
                    name: 'sppn_jumlah',
                    render: function(data, type, row) {
                        // Pastikan data valid dan bukan null atau undefined
                        if (!row.sppn_id || !data) {
                            return '<strong></strong>';
                        }

                        var formattedNumber = new Intl.NumberFormat('id', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0
                        }).format(data);

                        // Kembalikan nilai dalam tag <strong>
                        return '<strong>' + formattedNumber + '</strong>';
                    }
                });

                columns.push({
                    name: 'sppd_status',
                    render: function(data, type, row) {
                        if (row.sppd_status == 4) {
                            return 'Dibatalkan sistem';
                        } else if (row.sppd_status == 5) {
                            return 'Dibatalkan melalui API';
                        } else {
                            return '-'; 
                        }
                    }
                });


                columns.push({
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        action = '';
                        var detailUrl = "{{ route('detailspp', ':id') }}".replace(':id', row.spp_id);
                        action += `
                            <button type="button" class="btn btn-info btn-sm"
                                onClick="window.open('${detailUrl}')" title="Detail">
                                <i class="fa fa-info"></i>
                            </button>
                            `;
                        if (grupId == 4) {
                            var rekamJejakUrl = "{{ route('rekamjejak', ':id') }}".replace(':id', row
                                .hashedId);
                            action += `
                            <button type="button" class="btn btn-primary btn-sm"
                                onClick="window.open('${rekamJejakUrl}')" title="Rekam Jejak">
                                <i class="fa fa-map-o"></i>
                            </button>
                            `;
                        }
                        return action;
                    }
                });
            }

            return columns;
        }

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

            // $('#table_penerima').DataTable({
            //     processing: false,
            //     serverSide: true,
            //     "bDestroy": true,
            //     ajax: '{{ route('getVendor') }}',
            //     order: [],
            //     columns: [{
            //             data: 'DT_RowIndex',
            //             name: 'DT_RowIndex',
            //             orderable: false,
            //             searchable: false
            //         },
            //         {
            //             data: 'master_vendor_nama_bank',
            //             name: 'master_vendor_nama_bank'
            //         },
            //         {
            //             data: 'master_vendor_rekening',
            //             render: function(data, type, row) {
            //                 return `(Alamat Kosong)`
            //             }
            //         },
            //         {
            //             data: 'master_vendor_id',
            //             "render": function(data, type, row) {
            //                 return `<button type="button" class="btn btn-info btn-sm" onclick="pilih_penerima('${data}','${row.master_vendor_nama_bank}',0)" title="Pilih" ><i class="fa fa-check"></i></button>`
            //             },
            //             orderable: false,
            //             searchable: false
            //         },
            //     ]
            // })
            // $('#table_diterima_dari').DataTable({
            //     processing: false,
            //     serverSide: true,
            //     "bDestroy": true,
            //     ajax: '{{ route('getVendor') }}',
            //     order: [],
            //     columns: [{
            //             data: 'DT_RowIndex',
            //             name: 'DT_RowIndex',
            //             orderable: false,
            //             searchable: false
            //         },
            //         {
            //             data: 'master_vendor_nama_bank',
            //             name: 'master_vendor_nama_bank'
            //         },
            //         {
            //             data: 'master_vendor_rekening',
            //             render: function(data, type, row) {
            //                 return `(Alamat Kosong)`
            //             }
            //         },
            //         {
            //             data: 'master_vendor_id',
            //             "render": function(data, type, row) {
            //                 return `<button type="button" class="btn btn-info btn-sm" onclick="pilih_diterima_dari('${data}','${row.master_vendor_nama_bank}',0)" title="Pilih" ><i class="fa fa-check"></i></button>`
            //             },
            //             orderable: false,
            //             searchable: false
            //         },
            //     ]
            // })
            $.ajax({
                url: "{{ route('getSppdPosisiOptions') }}",
                method: "GET",
                success: function(data) {
                    var posisiSelect = $('#sppd_posisi');
                    posisiSelect.empty();
                    posisiSelect.append('<option value="">-- Pilih Posisi --</option>');
                    data.forEach(function(item) {
                        posisiSelect.append('<option value="' + item.master_hak_akses_id +
                            '">' + item.master_hak_akses_nama + '</option>');
                    });
                },
                error: function() {
                    alert("Gagal mengambil data posisi spp.");
                }
            });
            var userHakAkses = "{{ $akses }}";

            if (userHakAkses == "46") {
                $('#regional_filter').show();

                loadRegionalOptions();
            } else {
                $('#regional_filter').hide();
            }

            function loadRegionalOptions() {
                $.ajax({
                    url: "{{ route('getRegionalOptions') }}",
                    method: "GET",
                    success: function(data) {
                        console.log(data);
                        var regionalSelect = $('#regional');
                        regionalSelect.empty();
                        regionalSelect.append('<option value="">-- Pilih Regional --</option>');
                        data.forEach(function(item) {
                            regionalSelect.append('<option value="' + item.company_id + '">' +
                                item
                                .company_nama + '</option>');
                        });
                    },
                    error: function() {
                        alert("Gagal mengambil data regional.");
                    }
                });
            }

            if ($.fn.DataTable.isDataTable('#tab-to-do-list-petugas table')) {
                $('#tab-to-do-list-petugas table').DataTable().destroy();
            }

            var tableToDo = $('#tab-to-do-list-petugas table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                // ajax: {
                //     url: "{{ route('getTodo') }}",
                //     dataSrc: function(json) {
                //         $('#total-to-do').text(json.recordsTotal);

                //         return json.data;
                //     }
                // },
                ajax: {
                    url: "{{ route('getTodo') }}",
                    data: function(d) {
                        console.log("regional value: ", $('#regional').val());
                        // Menambahkan filter tanggal dan posisi pada request Ajax
                        d.start_date = $('#start_date').val(); // Tanggal mulai
                        d.end_date = $('#end_date').val(); // Tanggal akhir
                        d.sppd_posisi = $('#sppd_posisi').val(); // Posisi sppd
                        d.regional = $('#regional').val(); // regional
                    },
                    dataSrc: function(json) {
                        $('#total-to-do').text(json.recordsTotal);
                        return json.data;
                    }
                },
                columns: [
                    ...dataColumnsToDo(),
                ],
            });
            // Menangani klik tombol Terapkan Filter
            $('#applyFilter').on('click', function() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                var sppdPosisi = $('#sppd_posisi').val();
                var regional = $('#regional').val();
                console.log(startDate, endDate, sppdPosisi, regional);
                // Validasi tanggal
                if (startDate && endDate && startDate > endDate) {
                    alert("Tanggal mulai tidak boleh lebih besar dari tanggal akhir.");
                    return;
                }

                // Memuat ulang DataTable dengan filter baru
                tableToDo.ajax.reload();
                $('#advance_filter').modal('hide');
            });
            $('#resetFilter').on('click', function() {
                // Reset nilai input filter
                $('#start_date').val('');
                $('#end_date').val('');
                $('#sppd_posisi').val('');
                $('#regional').val('');

                // Memuat ulang DataTable tanpa filter
                tableToDo.ajax.reload();

                // Menutup modal jika perlu
                $('#advance_filter').modal('hide');
            });

            // Memeriksa apakah DataTable sudah dibuat untuk tabel dengan id 'tab-revisi-petugas table'
            if ($.fn.DataTable.isDataTable('#tab-revisi-petugas table')) {
                // Jika sudah, maka hapus DataTable tersebut
                $('#tab-revisi-petugas table').DataTable().destroy();
            }

            // Buat DataTable baru untuk tabel dengan id 'tab-revisi-petugas table'
            var tableRevisi = $('#tab-revisi-petugas table').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                // ajax: {
                //     url: "{{ route('getRevisi') }}",
                //     dataSrc: function(json) {
                //         // Memperbarui jumlah total record
                //         $('#total-revisi').text(json.recordsTotal);

                //         return json.data;
                //     }
                // },
                ajax: {
                    url: "{{ route('getRevisi') }}",
                    data: function(d) {
                        // Menambahkan filter tanggal dan posisi pada request Ajax
                        d.start_date = $('#start_date').val(); // Tanggal mulai
                        d.end_date = $('#end_date').val(); // Tanggal akhir
                        d.sppd_posisi = $('#sppd_posisi').val(); // Posisi sppd
                        d.regional = $('#regional').val(); // regional
                    },
                    dataSrc: function(json) {
                        $('#total-revisi').text(json.recordsTotal);
                        return json.data;
                    }
                },
                columns: [
                    ...dataColumnsRevisi(),
                ],
            });
            // Menangani klik tombol Terapkan Filter
            $('#applyFilter').on('click', function() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                var sppdPosisi = $('#sppd_posisi').val();
                var regional = $('#regional').val();
                console.log(startDate, endDate, sppdPosisi, regional);
                // Validasi tanggal
                if (startDate && endDate && startDate > endDate) {
                    alert("Tanggal mulai tidak boleh lebih besar dari tanggal akhir.");
                    return;
                }

                // Memuat ulang DataTable dengan filter baru
                tableRevisi.ajax.reload();
                $('#advance_filter').modal('hide');
            });
            $('#resetFilter').on('click', function() {
                // Reset nilai input filter
                $('#start_date').val('');
                $('#end_date').val('');
                $('#sppd_posisi').val('');
                $('#regional').val('');

                // Memuat ulang DataTable tanpa filter
                tableRevisi.ajax.reload();

                // Menutup modal jika perlu
                $('#advance_filter').modal('hide');
            });

            // Memeriksa apakah DataTable sudah dibuat untuk tabel dengan id 'tab-sedang-proses-petugas table'
            if ($.fn.DataTable.isDataTable('#tab-sedang-proses-petugas table')) {
                // Jika sudah, maka hapus DataTable tersebut
                $('#tab-sedang-proses-petugas table').DataTable().destroy();
            }

            // Buat DataTable baru untuk tabel dengan id 'tab-sedang-proses-petugas table'
            var tableProses = $('#tab-sedang-proses-petugas table').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                // ajax: {
                //     url: "{{ route('getProses') }}",
                // },
                ajax: {
                    url: "{{ route('getProses') }}",
                    data: function(d) {
                        // Menambahkan filter tanggal dan posisi pada request Ajax
                        d.start_date = $('#start_date').val(); // Tanggal mulai
                        d.end_date = $('#end_date').val(); // Tanggal akhir
                        d.sppd_posisi = $('#sppd_posisi').val(); // Posisi sppd
                        d.regional = $('#regional').val(); // regional
                    },
                },
                columns: [
                    ...dataColumnsProses(),
                ]
            });
            // Menangani klik tombol Terapkan Filter
            $('#applyFilter').on('click', function() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                var sppdPosisi = $('#sppd_posisi').val();
                var regional = $('#regional').val();
                console.log(startDate, endDate, sppdPosisi, regional);
                // Validasi tanggal
                if (startDate && endDate && startDate > endDate) {
                    alert("Tanggal mulai tidak boleh lebih besar dari tanggal akhir.");
                    return;
                }

                // Memuat ulang DataTable dengan filter baru
                tableProses.ajax.reload();
                $('#advance_filter').modal('hide');
            });
            $('#resetFilter').on('click', function() {
                // Reset nilai input filter
                $('#start_date').val('');
                $('#end_date').val('');
                $('#sppd_posisi').val('');
                $('#regional').val('');

                // Memuat ulang DataTable tanpa filter
                tableProses.ajax.reload();

                // Menutup modal jika perlu
                $('#advance_filter').modal('hide');
            });

            // Memeriksa apakah DataTable sudah dibuat untuk tabel dengan id 'tab-sudah-selesai-petugas table'
            var tableSelesai;
            if ($.fn.DataTable.isDataTable('#tab-sudah-selesai-petugas table')) {
                // Jika sudah, maka hapus DataTable tersebut
                $('#tab-sudah-selesai-petugas table').DataTable().destroy();
            }

            // Buat table baru untuk tabel dengan id 'tab-sudah-selesai-petugas table'
            tableSelesai = $('#tab-sudah-selesai-petugas table').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('getSelesai') }}",
                    data: function(d) {
                        // Menambahkan filter tanggal dan posisi pada request Ajax
                        d.start_date = $('#start_date').val(); // Tanggal mulai
                        d.end_date = $('#end_date').val(); // Tanggal akhir
                        d.sppd_posisi = 39; // Posisi sppd untuk tabel selesai
                        d.regional = $('#regional').val(); // regional
                    },
                },
                columns: [
                    ...dataColumnsSelesai(),
                ]
            });

            // Menangani klik tombol Terapkan Filter untuk tab selesai
            $('#applyFilter').on('click', function() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                var sppdPosisi = 39;
                var regional = $('#regional').val();
                console.log(startDate, endDate, sppdPosisi, regional);
                // Validasi tanggal
                if (startDate && endDate && startDate > endDate) {
                    alert("Tanggal mulai tidak boleh lebih besar dari tanggal akhir.");
                    return;
                }

                // Memuat ulang DataTable dengan filter baru
                tableSelesai.ajax.reload();
                $('#advance_filter').modal('hide');
            });

            // Reset filter untuk tab selesai
            $('#resetFilter').on('click', function() {
                // Reset nilai input filter
                $('#start_date').val('');
                $('#end_date').val('');
                $('#sppd_posisi').val('');
                $('#regional').val('');

                // Memuat ulang DataTable tanpa filter
                tableSelesai.ajax.reload();

                // Menutup modal jika perlu
                $('#advance_filter').modal('hide');
            });

            var tableBatal;
            // Memeriksa apakah DataTable sudah dibuat untuk tabel dengan id 'tab-dibatalkan-petugas table'
            if ($.fn.DataTable.isDataTable('#tab-dibatalkan-petugas table')) {
                // Jika sudah, maka hapus DataTable tersebut
                $('#tab-dibatalkan-petugas table').DataTable().destroy();
            }

            // Buat DataTable baru untuk tabel dengan id 'tab-dibatalkan-petugas table'
            tableBatal = $('#tab-dibatalkan-petugas table').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('getBatal') }}",
                    data: function(d) {
                        // Menambahkan filter tanggal dan posisi pada request Ajax
                        d.start_date = $('#start_date').val(); // Tanggal mulai
                        d.end_date = $('#end_date').val(); // Tanggal akhir
                        d.sppd_posisi = 34; // Posisi sppd
                        d.regional = $('#regional').val(); // regional
                    },
                },
                columns: [
                    // Gunakan spread operator untuk menambahkan kolom-kolom yang diperlukan
                    ...dataColumnsBatal(),
                ]
            });
            // Menangani klik tombol Terapkan Filter
            $('#applyFilter').on('click', function() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                var sppdPosisi = 34;
                var regional = $('#regional').val();
                console.log(startDate, endDate, sppdPosisi, regional);
                // Validasi tanggal
                if (startDate && endDate && startDate > endDate) {
                    alert("Tanggal mulai tidak boleh lebih besar dari tanggal akhir.");
                    return;
                }

                // Memuat ulang DataTable dengan filter baru
                tableBatal.ajax.reload();
                $('#advance_filter').modal('hide');
            });
            $('#resetFilter').on('click', function() {
                // Reset nilai input filter
                $('#start_date').val('');
                $('#end_date').val('');
                $('#sppd_posisi').val('');
                $('#regional').val('');

                // Memuat ulang DataTable tanpa filter
                tableBatal.ajax.reload();

                // Menutup modal jika perlu
                $('#advance_filter').modal('hide');
            });

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

        // Fungsi untuk menginisialisasi select2 dengan data asynchron dari API
        function initializeSAP(selector, placeholder) {
            // Menggunakan arrow function agar this lebih fleksible
            $(selector).select2({
                placeholder: placeholder, // Teks placeholder
                width: '100%', // Set lebar select2 sesuai dengan yang diinginkan
                ajax: {
                    url: "{{ route('fetchRekening') }}", // URL API untuk mendapatkan data
                    dataType: 'json', // Tipe data yang akan dikembalikan
                    delay: 250, // Penundaan untuk menunggu user selesai mengetik sebelum request
                    data: function(params) {
                        return {
                            search: params.term, // Istilah pencarian
                            page: params.page || 1 // Halaman yang akan diminta
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items.map(item => ({
                                id: item.master_rekening_kode_sap,
                                text: `${item.master_rekening_kode_sap} - ${item.master_rekening_keterangan}`
                            })),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                }
            });
        }

        function advanced_search(index, user) {
            $('#index_advanced_search').val(index);
            if (index == 1) {
                $('#advanced_search_rentang_waktu').show();

                // if (user == 1) {
                //     $('#advanced_search_posisi_terkini').hide();
                //     $('#advanced_search_status_bayar1').hide();

                // } else {
                //     $('#advanced_search_posisi_terkini').show();
                //     $('#advanced_search_status_bayar1').show();

                // }
                $('#advanced_search_bagian').show();

                // $('#advanced_search_status_bayar2').hide();
                $('#modal_advanced_search').modal('show');
                initializeSAP('#advanced_search_kode_sap_sppb', '-- Pilih Kode SAP Vendor SPPB --')
                initializeSAP('#advanced_search_kode_sap_sppn', '-- Pilih Kode SAP Vendor SPPN --')
            } else if (index == 2) {
                $('#advanced_search_bagian').show();
                $('#advanced_search_rentang_waktu').show();
                // $('#advanced_search_posisi_terkini').hide();
                // $('#advanced_search_status_bayar1').hide();
                // $('#advanced_search_status_bayar2').hide();
                $('#modal_advanced_search').modal('show');
            } else {
                $('#advanced_search_bagian').show();
                $('#advanced_search_rentang_waktu').show();
                // $('#advanced_search_posisi_terkini').hide();
                // $('#advanced_search_status_bayar1').hide();
                // $('#advanced_search_status_bayar2').hide();
                $('#modal_advanced_search').modal('show');
            }
        }

        function clear_spp_bayar(id) {

            if (id == 0) {
                document.getElementById('form-bayar_cbk').reset();
                $("#bukti_sppb").hide();
                $("#remove_bukti_sppb").hide();
                $("#bukti_transfer_sppb").show();
            } else {
                document.getElementById('form-terima_cbk').reset();
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
            // $("#submit-cetak-kas").click();
            // var str = $(".cetakbuktikas").val();

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

        function pembayaran(id_sppb, id_sppn, form, data_sppb = false, data_sppn = false) {
            // var data_sppb = JSON.parse(decodeURIComponent(dataSppbJson))
            // var data_sppn = JSON.parse(decodeURIComponent(dataSppnJson))
            console.log(data_sppb, data_sppn);
            $("#form_a").val(form);
            $("#form_b").val(form);
            if (data_sppb) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('getSppbBayar', ':id') }}".replace(':id', id_sppb),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // Sertakan token CSRF untuk keamanan
                    },
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        data = response.data;
                        var now = new Date(data.sppb_bayar_tanggal);
                        var tanggal = moment(now).format("DD-MM-YYYY");
                        $("#nomor_bukti_kas_sppb").val(data.sppb_bayar_nomor_bukti_kas);
                        $("#id_sppb_bayar").val(data.sppb_bayar_id);
                        $("#nomor_bukti_kas_sppb").attr('readonly', true);
                        $("#rekening_sppb").val(data.master_rekening_kode_kbb + ' / ' + data
                            .master_rekening_kode_sap +
                            '(' + data.master_rekening_keterangan + ')');
                        $("#rekening_sppb").attr('disabled', 'disabled');
                        $("#tanggal_bayar_sppb").val(tanggal);
                        $("#tanggal_bayar_sppb").attr('disabled', 'disabled');
                        $("#rekening_sppb_1").val(data.master_rekening_id);
                        $("#bukti_transfer_sppb").hide();
                        $("#bukti_sppb").show();
                        $("#remove_bukti_sppb").hide();
                        document.getElementById("bukti_sppb").href = 'dokumen/' + data.sppb_bayar_bukti;
                        document.getElementById("bukti_sppb").innerHTML = data.sppb_bayar_bukti;
                        $("#footer_submit_sppb").hide();
                        $("#footer_edit_sppb").show();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
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
                })
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
            if (data_sppn) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('getSppnTerima', ':id') }}".replace(':id', id_sppn),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // Sertakan token CSRF untuk keamanan
                    },
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        var data = response.data;
                        var now = new Date(data.sppn_terima_tanggal);
                        var tanggal = moment(now).format("DD-MM-YYYY");
                        $("#id_sppn_terima").val(data.sppn_terima_id);
                        $("#nomor_bukti_kas_sppn").val(data.sppn_terima_nomor_bukti_kas);
                        $("#nomor_bukti_kas_sppn").attr('readonly', 'readonly');
                        $("#rekening_sppn").val(data.master_rekening_kode_kbb + ' / ' + data
                            .master_rekening_kode_sap +
                            '(' + data.master_rekening_keterangan + ')');
                        $("#rekening_sppn").attr('disabled', 'disabled');
                        $("#tanggal_terima_sppn").val(tanggal);
                        $("#tanggal_terima_sppn").attr('disabled', 'disabled');
                        $("#rekening_sppn_1").val(data.master_rekening_id);
                        $("#bukti_transfer_sppn").hide();
                        $("#bukti_sppn").show();
                        $("#remove_bukti_sppn").hide();
                        document.getElementById("bukti_sppn").href = 'dokumen/' + data.sppn_terima_bukti;
                        document.getElementById("bukti_sppn").innerHTML = data.sppn_terima_bukti;
                        $("#footer_submit_sppn").hide();
                        $("#footer_edit_sppn").show();
                    },
                    error: function(xhr, status, error) {
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
                })
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

        function ubahStatusBayar(buttonElement, id_spp, status) {
            Swal.fire({
                title: 'Apakah Anda yakin ingin mengubah status menjadi "Sudah Dibayar Belum Upload Bukti Transfer"?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#41B314',
                cancelButtonColor: '#F9354C',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                showCloseButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    // Simpan keadaan asli tombol sebelum mengubahnya
                    const originalClassName = buttonElement.className;
                    const originalText = buttonElement.textContent;
                    const originalBackgroundColor = buttonElement.style.backgroundColor;
                    const originalBorderColor = buttonElement.style.borderColor;
                    const originalColor = buttonElement.style.color;

                    // Mengubah tampilan tombol secara langsung
                    buttonElement.style.backgroundColor = '#6E00FF';
                    buttonElement.style.borderColor = '#6E00FF';
                    buttonElement.style.color = 'white';
                    buttonElement.textContent = 'Sudah Dibayar Belum Upload Bukti Transfer';

                    const data = {
                        ids: [id_spp],
                        spp_status_bayar: 2
                    };
                    console.log('Sending data:', data);

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: `{{ route('ubah_status') }}`,
                        method: 'POST',
                        data: data,
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Status Berhasil Diubah!',
                                    text: 'Status pembayaran telah berhasil diubah.',
                                    icon: 'success',
                                    confirmButtonColor: '#41B314'
                                }).then((result) => {
                                    location.reload();
                                });
                            } else {
                                // Kembalikan tampilan tombol ke keadaan semula jika gagal
                                buttonElement.style.backgroundColor = originalBackgroundColor;
                                buttonElement.style.borderColor = originalBorderColor;
                                buttonElement.style.color = originalColor;
                                buttonElement.textContent = originalText;

                                Swal.fire({
                                    title: 'Status Gagal Diubah!',
                                    text: 'Terjadi kesalahan saat mengubah status pembayaran.',
                                    icon: 'error',
                                    confirmButtonColor: '#F9354C'
                                });
                            }

                        },
                        error: function() {
                            // Kembalikan tampilan tombol ke keadaan semula jika terjadi error
                            buttonElement.style.backgroundColor = originalBackgroundColor;
                            buttonElement.style.borderColor = originalBorderColor;
                            buttonElement.style.color = originalColor;
                            buttonElement.textContent = originalText;

                            Swal.fire({
                                title: 'Status Gagal Diubah!',
                                text: 'Terjadi kesalahan saat mengubah status pembayaran.',
                                icon: 'error',
                                confirmButtonColor: '#F9354C'
                            });
                        }
                    });
                }
            });
        }

        function approveSelectedStatus() {
            var selectedCheckboxes = document.querySelectorAll('.checkbox-item-bayar:checked');
            var selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (selectedIds.length === 0) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Silakan Pilih Setidaknya Satu PP.',
                    icon: 'warning',
                    confirmButtonColor: '#41B314',
                });
                return;
            }

            Swal.fire({
                title: 'Apakah Anda Yakin Ingin Mengubah Status?',
                text: `Ubah Status ${selectedIds.length} Data PP yang Dipilih!`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#41B314',
                cancelButtonColor: '#F9354C',
                confirmButtonText: 'Ubah Status'
            }).then((result) => {
                if (result.isConfirmed) {
                    const data = {
                        ids: selectedIds,
                        spp_status_bayar: 2
                    };
                    console.log('Sending data:', data);

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: `{{ route('ubah_status') }}`,
                        method: 'POST',
                        data: data,
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Status Berhasil Diubah!',
                                    text: 'Status pembayaran telah berhasil diubah.',
                                    icon: 'success',
                                    confirmButtonColor: '#41B314'
                                }).then(() => {
                                    location.reload(); // Reload halaman setelah sukses
                                });
                            } else {
                                Swal.fire({
                                    title: 'Status Gagal Diubah!',
                                    text: 'Terjadi kesalahan saat mengubah status pembayaran.',
                                    icon: 'error',
                                    confirmButtonColor: '#F9354C'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Status Gagal Diubah!',
                                text: 'Terjadi kesalahan saat mengubah status pembayaran.',
                                icon: 'error',
                                confirmButtonColor: '#F9354C'
                            });
                        }
                    });
                }
            });
        }

        function edit_cetak_bukti(form) {
            console.log('test')
            if (form == 0) {
                var id = $("#id_sppb_bayar_cbk").val();
                $("#form").val(0);
                $("#form-bayar").attr('action', 'spp/update_bayar/' + id);
                document.getElementById('nomor_bukti_kas_sppb_cbk').removeAttribute('readonly');
                document.getElementById('rekening_sppb_cbk').disabled = false;
                $("#footer_submit_sppb_cbk").show();
                $("#footer_edit_sppb_cbk").hide();
                console.log($("#footer_edit_sppb_cbk"), $("#footer_submit_sppb_cbk"))
            } else {
                console.log('test2')
                $("#form").val(1);
                var id = $("#id_sppn_terima_cbk").val();
                $("#form-terima").attr('action', 'spp/update_bayar/' + id);
                document.getElementById('nomor_bukti_kas_sppn').removeAttribute('readonly');
                document.getElementById('rekening_sppn').disabled = false;
                document.getElementById('tanggal_terima_sppn').disabled = false;
                $("#bukti_transfer_sppn").hide();
                $("#footer_submit_sppn").show();
                $("#footer_edit_sppn").hide();
            }
        }


        function cetak_bukti_kas(metode_pembayaran, id_spp, id_sppb, id_sppn, form, data_sppb = false, data_sppn =
            false,
            penerima = false,
            diterima = false) {
            // var data_sppb = dataSppb == 0 ? dataSppb : JSON.parse(decodeURIComponent(dataSppb));
            // var data_sppn = dataSppn == 0 ? dataSppn : JSON.parse(decodeURIComponent(dataSppn));
            // var penerima = JSON.parse(decodeURIComponent(penerimaJson));
            // var diterima = JSON.parse(decodeURIComponent(diterimaJson));

            $('#table_rekeningsppn').DataTable().clear().destroy();
            $('#table_rekeningsppn').DataTable({
                processing: false,
                serverSide: true,
                ajax: '{{ route('mas_gl') }}',
                order: [],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    // { data: 'master_rekening_kode_kbb', name: 'master_rekening_kode_kbb' },
                    {
                        data: 'master_gl_kode',
                        name: 'master_gl_kode'
                    },
                    {
                        data: 'master_gl_keterangan',
                        name: 'master_gl_keterangan'
                    },
                    {
                        data: 'master_gl_kode',
                        "render": function(data, type, row) {
                            return `<button type="button" class="btn btn-info btn-sm" onclick="pilih_rekening_sppn('${row.master_gl_id}','${row.master_rekening_kode_kbb}', '${row.master_gl_kode}', '${row.master_gl_keterangan}')" title="Pilih" ><i class="fa fa-check"></i></button>`
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
                ajax: '{{ route('mas_gl') }}',
                order: [],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'master_gl_kode',
                        name: 'master_gl_kode'
                    },
                    {
                        data: 'master_gl_keterangan',
                        name: 'master_gl_keterangan'
                    },
                    {
                        data: 'master_gl_kode',
                        "render": function(data, type, row) {
                            return `<button type="button" class="btn btn-info btn-sm" onclick="pilih_rekening_sppb('${row.master_gl_id}','${row.master_rekening_kode_kbb}', '${row.master_gl_kode}', '${row.master_gl_keterangan}')" title="Pilih" ><i class="fa fa-check"></i></button>`
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

            if (data_sppb) {
                $.ajax({
                    type: 'get',
                    url: "{{ route('getSppbCetakBuktiKas', ':id') }}".replace(':id', id_sppb),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // Sertakan token CSRF untuk keamanan
                    },
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        var data = response.data;
                        $("#nomor_bukti_kas_sppb_cbk").val(data.cek_giro);
                        $("#id_sppb_bayar_cbk").val(data.sppb_bayar_id);
                        //$("#nomor_bukti_kas_sppb_cbk").attr('readonly','readonly');
                        $("#rekening_sppb_cbk").val(data.master_gl_kode + '(' + data.master_gl_keterangan +
                            ')');
                        //$("#rekening_sppb_cbk").attr('disabled','disabled');
                        $("#rekening_sppb_1_cbk").val(data.master_rekening_id);
                        $("#penerima_cbk").val(data.master_vendor_id);
                        $("#alamat_penerima_cbk").val(data.alamat_sppb);
                        $("#tanggal_cetak_sppb").val(data.sppb_bukti_kas_tanggal);
                        // $("#uraian_cetak_sppb").val(data_sppb.sppb_bukti_kas_uraian);
                        $("#footer_submit_sppb_cbk").hide();
                        $("#footer_edit_sppb_cbk").show();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        $("#penerima_cbk").val('');
                        $("#alamat_penerima_cbk").val('');
                        $("#nomor_bukti_kas_sppb_cbk").val('');
                        $("#id_sppb_bayar_cbk").val('');
                        //$("#nomor_bukti_kas_sppb_cbk").attr('readonly',false);
                        $("#rekening_sppb_cbk").val('');
                        // $("#rekening_sppb_cbk").attr('disabled',false);
                        $("#rekening_sppb_1_cbk").val('');
                        $("#footer_submit_sppb_cbk").show();
                        $("#footer_edit_sppb_cbk").hide();
                    }
                })
                // alert('a');
            } else {
                $("#penerima_cbk").val('');
                $("#alamat_penerima_cbk").val('');
                $("#nomor_bukti_kas_sppb_cbk").val('');
                $("#id_sppb_bayar_cbk").val('');
                //$("#nomor_bukti_kas_sppb_cbk").attr('readonly',false);
                $("#rekening_sppb_cbk").val('');
                // $("#rekening_sppb_cbk").attr('disabled',false);
                $("#rekening_sppb_1_cbk").val('');
                $("#footer_submit_sppb_cbk").show();
                $("#footer_edit_sppb_cbk").hide();
                // alert('b');

            }
            if (data_sppn) {
                $.ajax({
                    type: 'get',
                    url: "{{ route('getSppnCetakBuktiKas', ':id') }}".replace(':id', id_sppn),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // Sertakan token CSRF untuk keamanan
                    },
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        var data = response.data;
                        $("#diterima_dari_cbk").val(data.master_vendor_id ?? '');
                        $("#alamat_diterima_dari_cbk").val(data.alamat_sppn ?? '');
                        $("#id_sppn_terima_cbk").val(data.sppn_terima_id ?? '');
                        $("#nomor_bukti_kas_sppn_cbk").val(data.cek_giro ?? '');
                        //$("#nomor_bukti_kas_sppn_cbk").attr('readonly','readonly');
                        $("#rekening_sppn_cbk").val(data.master_gl_kode + '(' + data.master_gl_keterangan +
                            ')' ?? '');
                        //$("#rekening_sppn_cbk").attr('disabled','disabled');
                        $("#rekening_sppn_1_cbk").val(data.master_rekening_id ?? '');
                        $("#footer_submit_sppn_cbk").hide();
                        $("#footer_edit_sppn_cbk").show();
                    },
                    error: function(xhr, status, error) {
                        if (penerima != null && diterima == null) {
                            $("#diterima_dari_cbk").val('');
                            $("#alamat_diterima_dari_cbk").val('');
                        } else {
                            $.ajax({
                                type: 'get',
                                url: "{{ route('getDiterima', ':id') }}".replace(':id', id_sppn),
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content') // Sertakan token CSRF untuk keamanan
                                },
                                contentType: false,
                                processData: false,
                                success: function(response) {
                                    diterima = response.data;
                                    $("#diterima_dari_cbk").val(diterima.karyawan_nama);
                                    $("#alamat_diterima_dari_cbk").val(diterima
                                        .karyawan_alamat);
                                },
                                error: function(xhr, status, error) {
                                    console.log(xhr.responseText);
                                }
                            })
                        }
                        $("#id_sppn_terima_cbk").val('');
                        //$("#nomor_bukti_kas_sppn_cbk").val('');
                        //$("#nomor_bukti_kas_sppn_cbk").attr('readonly',false);
                        $("#rekening_sppn_cbk").val('');
                        $("#rekening_sppn_cbk").attr('disabled', false);
                        $("#rekening_sppn_1_cbk").val('');
                        $("#footer_submit_sppn_cbk").show();
                        $("#footer_edit_sppn_cbk").hide();
                    }
                })
            } else {
                if (penerima === true && diterima === false) {
                    $("#diterima_dari_cbk").val('');
                    $("#alamat_diterima_dari_cbk").val('');
                } else {
                    $.ajax({
                        type: 'get',
                        url: "{{ route('getDiterima', ':id') }}".replace(':id', id_sppn),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                'content') // Sertakan token CSRF untuk keamanan
                        },
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            diterima = response.data;
                            $("#diterima_dari_cbk").val(diterima.karyawan_nama);
                            $("#alamat_diterima_dari_cbk").val(diterima.karyawan_alamat);
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                        }
                    })
                }
                $("#id_sppn_terima_cbk").val('');
                //$("#nomor_bukti_kas_sppn_cbk").val('');
                //$("#nomor_bukti_kas_sppn_cbk").attr('readonly',false);
                $("#rekening_sppn_cbk").val('');
                $("#rekening_sppn_cbk").attr('disabled', false);
                $("#rekening_sppn_1_cbk").val('');
                $("#footer_submit_sppn_cbk").show();
                $("#footer_edit_sppn_cbk").hide();
                // alert('d');
            }

            if (id_sppb != 0 && id_sppn != 0) {
                if (data_sppb === false || data_sppn === false) {
                    $('#cetakbuktikas').attr('disabled', 'disabled');
                }

            }

            if (form == 0) {
                $("#tab_bayar_cbk").show();
                $("#tab_terima_cbk").hide();
                document.getElementById("tab_bayar_cbk").className = "active";
                document.getElementById("tab-pembayaran_cbk").className = "tab tab-pane active";
                document.getElementById("tab_terima_cbk").className = "";
                document.getElementById("tab-penerimaan_cbk").className = "tab tab-pane";
                $("#modal_cetak_bukti_kas").modal('show');
                $(".submit-cetak-kas").click(function() {
                    if (
                        $("#nomor_bukti_kas_sppb_cbk").val() == '' ||
                        $("#rekening_sppb_cbk").val() == '' ||
                        $("#penerima_cbk").val() == '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Lengkapi Data Bukti Kas Terlebih Dahulu!',
                        })
                        return;
                    } else {
                        let csrfToken = $('meta[name="csrf-token"]').attr('content');

                        let formBayarData = new FormData(document.getElementById('form-bayar_cbk'));
                        formBayarData.forEach((value, key) => {
                            console.log(key, value);
                        });
                        $.ajax({
                            type: 'POST',
                            url: `{{ url('') }}/sppd/bukti_kas/${id_sppb}`,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken // Sertakan token CSRF untuk keamanan
                            },
                            data: formBayarData,
                            contentType: false,
                            processData: false,
                            success: function(data) {
                                $('#modal_cetak_bukti_kas').modal('hide');
                                console.log(data);

                                Swal.fire({
                                    title: 'Sukses',
                                    text: 'Data berhasil dikirim.',
                                    icon: 'success',
                                    showConfirmButton: true,
                                    timer: 3000,
                                    timerProgressBar: true
                                }).then((result) => {
                                    if (result.isConfirmed || result.dismiss === Swal
                                        .DismissReason.timer) {
                                        window.location.href = `{{ url('') }}/sppd`;
                                    }
                                });
                            },
                            error: function(xhr, status, error) {
                                // Tampilkan pesan error
                                console.log('Error:', xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Terjadi kesalahan.',
                                    showConfirmButton: true,
                                })
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

                $(".submit-cetak-kas").click(function() {
                    if (
                        $("#nomor_bukti_kas_sppn_cbk").val() == '' ||
                        $("#rekening_sppn_cbk").val() == '' ||
                        $("#diterima_dari_cbk").val() == '' ||
                        $("#alamat_diterima_dari_cbk").val() == '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Lengkapi Data Bukti Kas Terlebih Dahulu!',
                        })
                        return;
                    } else {
                        let csrfToken = $('meta[name="csrf-token"]').attr('content');

                        let formTerimaData = new FormData(document.getElementById('form-terima_cbk'));

                        formTerimaData.forEach((value, key) => {
                            console.log(key, value);
                        });

                        $.ajax({
                            type: 'POST',
                            url: `{{ url('') }}/sppd/bukti_kas/${id_sppn}`,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken // Sertakan token CSRF untuk keamanan
                            },
                            data: formTerimaData,
                            contentType: false,
                            processData: false,
                            success: function(data) {
                                $('#modal_cetak_bukti_kas').modal('hide');
                                console.log(data);

                                Swal.fire({
                                    title: 'Sukses',
                                    text: 'Data berhasil dikirim.',
                                    icon: 'success',
                                    showConfirmButton: true,
                                    timer: 3000,
                                    timerProgressBar: true
                                }).then((result) => {
                                    if (result.isConfirmed || result.dismiss === Swal
                                        .DismissReason.timer) {
                                        window.location.href = `{{ url('') }}/sppd`;
                                    }
                                });
                            },
                            error: function(xhr, status, error) {
                                // Tampilkan pesan error
                                console.log('Error:', xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Terjadi kesalahan.',
                                    showConfirmButton: true,
                                })
                            }
                        });
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
                $('.submit-cetak-kas').click(function() {
                    if (
                        $("#nomor_bukti_kas_sppb_cbk").val() == '' ||
                        $("#rekening_sppb_cbk").val() == '' ||
                        $("#penerima_cbk").val() == '' ||
                        $("#nomor_bukti_kas_sppn_cbk").val() == '' ||
                        $("#rekening_sppn_cbk").val() == '' ||
                        $("#diterima_dari_cbk").val() == '' ||
                        $("#alamat_diterima_dari_cbk").val() == ''
                    ) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Lengkapi Data Bukti Kas Terlebih Dahulu!',
                        })
                        return;
                    } else {
                        let csrfToken = $('meta[name="csrf-token"]').attr('content');
                        // Ambil data dari form bayar
                        let formBayarData = new FormData(document.getElementById('form-bayar_cbk'));
                        // Ambil data dari form terima
                        let formTerimaData = new FormData(document.getElementById('form-terima_cbk'));

                        for (let [key, value] of formTerimaData.entries()) {
                            formBayarData.append(key, value);
                        }

                        formBayarData.append('id_sppn', id_sppn);
                        formBayarData.append('id_sppb', id_sppb);

                        $.ajax({
                            type: 'POST',
                            url: '{{ url('') }}/sppd/bukti_kas',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken // Sertakan token CSRF untuk keamanan
                            },
                            data: formBayarData,
                            contentType: false,
                            processData: false,
                            success: function(data) {
                                $('#modal_cetak_bukti_kas').modal('hide');

                                Swal.fire({
                                    title: 'Sukses',
                                    text: 'Data berhasil dikirim.',
                                    icon: 'success',
                                    showConfirmButton: true,
                                    timer: 5000,
                                    timerProgressBar: true
                                }).then((result) => {
                                    if (result.isConfirmed || result.dismiss === Swal
                                        .DismissReason.timer) {
                                        window.location.href = `{{ url('') }}/sppd`;
                                    }
                                });
                            },
                            error: function(xhr, status, error) {
                                // Tampilkan pesan error
                                console.log('Error:', xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Terjadi kesalahan.',
                                    showConfirmButton: true,
                                })
                            }
                        });
                    }
                });
                $(".cetakbuktikas").click(function() {
                    $("#modal_cetak_bukti_kas").modal('hide');
                    $("#submit-cetak-kas").click();
                    $("#form-terima_cbk").attr('action', 'sppd/bukti_kas/' + id_sppn);
                });
            }
        }

        function edit_bukti_kas(form) {
            var str = $(".cetakbuktikas").val();
            if (form == 0) {
                var id = $("#id_sppb_bayar_cbk").val();
                console.log(id);
                $("#form_a_cbk").val(0);
                $("#form-bayar_cbk").attr('action', 'sppd/update_bukti_kas/' + str);
                $("#form-bayar_cbk").submit();
            } else {
                var id = $("#id_sppn_terima_cbk").val();
                $("#form_b_cbk").val(1);
                $("#form-terima_cbk").attr('action', 'sppd/update_bukti_kas/' + str);
                $("#form-terima_cbk").submit();
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

            $('#rekening_sppn_cbk').val(sap + ' (' + keterangan + ')');
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
            $("#pisanae").click(function(e) {

                var radio_check_val = "";
                for (var i = 0; i < document.getElementsByName('upload_file').length; i++) {
                    if (document.getElementsByName('upload_file')[i].checked) {
                        radio_check_val = document.getElementsByName('upload_file')[i].value;
                    }
                }
                if (radio_check_val == 'file_lama') {
                    if (document.getElementById("spp_kabag").files.length == 0) {
                        $("#modal_kirim").modal('hide');
                        Swal.fire({
                            title: 'Apakah Anda Yakin?',
                            text: "Kirim  Data PP Tanpa Upload Dokumen!",
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
                        return;
                    }
                    // kirim(id);

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

            $("#pisanaeto").click(function(e) {
                e.preventDefault(); // Mencegah form submit default sebelum konfirmasi

                var radio_check_val = "";
                for (var i = 0; i < document.getElementsByName('upload_file').length; i++) {
                    if (document.getElementsByName('upload_file')[i].checked) {
                        radio_check_val = document.getElementsByName('upload_file')[i].value;
                    }
                }

                if (radio_check_val == 'file_lama') {
                    if (document.getElementById("spp_kabag").files.length == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Upload File PP yang sudah di TTD Kepala Bagian terlebih dahulu!'
                        });
                        return;
                    } else {
                        // Menampilkan konfirmasi sebelum pengiriman
                        Swal.fire({
                            title: 'Apakah Anda Yakin?',
                            text: "Kirim Data PP!",
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonColor: '#41B314',
                            cancelButtonColor: '#F9354C',
                            confirmButtonText: 'Kirim PP',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Jika konfirmasi, kirim form
                                $("#form-kirim").attr('action', 'sppd/upload/' + id);
                                $("#form-kirim").submit(); // Kirim form ke URL
                            }
                        });
                    }
                } else {
                    if (document.getElementById("spp_kabag").files.length == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Upload File PP yang sudah di TTD Kepala Bagian terlebih dahulu!'
                        });
                        return;
                    } else {
                        // Menampilkan konfirmasi sebelum pengiriman
                        Swal.fire({
                            title: 'Apakah Anda Yakin?',
                            text: "Kirim Data PP!",
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonColor: '#41B314',
                            cancelButtonColor: '#F9354C',
                            confirmButtonText: 'Kirim PP',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Jika konfirmasi, kirim form
                                $("#form-kirim").attr('action', 'sppd/upload/' + id);
                                $("#form-kirim").submit(); // Kirim form ke URL
                            }
                        });
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

        document.addEventListener('DOMContentLoaded', function() {
            function toggleTerimaButton() {
                let allCheckboxes = document.querySelectorAll('.checkbox-item');
                let terimaButtons = document.querySelectorAll('.btn-terima');

                allCheckboxes.forEach(function(checkbox, index) {
                    let terimaButton = terimaButtons[index];

                    if (checkbox.checked) {
                        terimaButton.classList.add('disabled');
                    } else {
                        terimaButton.classList.remove('disabled');
                    }
                });
            }

            function updateSelectAllButtonText() {
                let allCheckboxes = document.querySelectorAll('.checkbox-item');
                let checkedCheckboxes = document.querySelectorAll('.checkbox-item:checked').length;
                let selectAllButton = document.getElementById('select-all-button');

                if (selectAllButton) {
                    selectAllButton.textContent = 'Select All';
                    if (checkedCheckboxes === 0) {
                        selectAllButton.textContent = 'Select All';
                    } else if (checkedCheckboxes === allCheckboxes.length) {
                        selectAllButton.textContent = 'Deselect All';
                    } else {
                        selectAllButton.textContent = 'Select All';
                    }
                }
            }
            document.getElementById('select-all-button').addEventListener('click', function() {
                let allCheckboxes = document.querySelectorAll('.checkbox-item');
                let isAllChecked = Array.from(allCheckboxes).some(checkbox => !checkbox.checked);


                allCheckboxes.forEach(function(checkbox) {
                    checkbox.checked = isAllChecked;
                });

                updateSelectAllButtonText();
                toggleTerimaButton();
            });
            document.querySelectorAll('.checkbox-item').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    updateSelectAllButtonText();
                    toggleTerimaButton();
                });
            });
            updateSelectAllButtonText();
            toggleTerimaButton();

            //Ubah status belum bayar
            function toggleBelumBayarButton() {
                let allCheckboxes = document.querySelectorAll('.checkbox-item-bayar');
                let belumBayarButtons = document.querySelectorAll('.btn-belum-bayar');

                allCheckboxes.forEach(function(checkbox, index) {
                    let belumBayarButton = belumBayarButtons[index];

                    if (checkbox.checked) {
                        belumBayarButton.classList.add('disabled');
                    } else {
                        belumBayarButton.classList.remove('disabled');
                    }
                });
            }

            function updateSelectAllBayarText() {
                let allCheckboxes = document.querySelectorAll('.checkbox-item-bayar');
                let checkedCheckboxes = document.querySelectorAll('.checkbox-item-bayar:checked').length;
                let selectAllButton = document.getElementById('select-all-bayar');

                if (selectAllButton) {
                    selectAllButton.textContent = 'Select All';
                    if (checkedCheckboxes === 0) {
                        selectAllButton.textContent = 'Select All';
                    } else if (checkedCheckboxes === allCheckboxes.length) {
                        selectAllButton.textContent = 'Deselect All';
                    } else {
                        selectAllButton.textContent = 'Select All';
                    }
                }
            }
            document.getElementById('select-all-bayar').addEventListener('click', function() {
                let allCheckboxes = document.querySelectorAll('.checkbox-item-bayar');
                let isAllChecked = Array.from(allCheckboxes).some(checkbox => !checkbox.checked);


                allCheckboxes.forEach(function(checkbox) {
                    checkbox.checked = isAllChecked;
                });

                updateSelectAllBayarText();
                toggleBelumBayarButton();
            });
            document.querySelectorAll('.checkbox-item-bayar').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    updateSelectAllBayarText();
                    toggleBelumBayarButton();
                });
            });

            updateSelectAllBayarText();
            toggleBelumBayarButton();
        });

        function approveSelected() {
            var selectedCheckboxes = document.querySelectorAll('.checkbox-item:checked');
            var selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (selectedIds.length === 0) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Silakan Pilih Setidaknya Satu PP.',
                    icon: 'warning',
                    confirmButtonColor: '#41B314',
                });
                return;
            }

            Swal.fire({
                title: 'Apakah Anda Yakin sudah menerima PP?',
                text: `Terima ${selectedIds.length} Data PP yang Dipilih!`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#41B314',
                cancelButtonColor: '#F9354C',
                confirmButtonText: 'Terima PP'
            }).then((result) => {
                if (result.isConfirmed) {
                    var idsString = selectedIds.join(',');

                    window.location.href = `{{ url('sppd/accept') }}/${idsString}`;

                    Swal.fire({
                        title: 'Terima PP',
                        text: 'PP berhasil anda Terima. Harap Menunggu...',
                        icon: 'success',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                    });
                }
            });
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
                document.getElementById('keterangan_revisi').value = data;
                $("#keterangan_revisi").attr('readonly', 'readonly');
            } else {
                $("#keterangan_revisi").removeAttr('readonly').attr('required', true);
            }
            $("#form-revisi").attr('action', 'sppd/revisi/' + id);

            // Tampilkan opsi revisi jika hak akses 39
            const hakAkses = @json(session('hak_akses')); // Mengambil hak akses dari session
            if (hakAkses == 39) {
                $("#opsi-revisi").show();
            } else {
                $("#opsi-revisi").hide();
            }
        }

        function confirm_revisi() {
            let ket = $('#keterangan_revisi').val();
            if (ket === "") {
                alert("Isi Keterangan!!");
                return;
            }

            const hakAkses = @json(session('hak_akses')); // Mengambil hak akses dari session

            if (hakAkses == 39) {
                // Hak akses 39: Tampilkan opsi revisi terlebih dahulu
                Swal.fire({
                    title: 'Pilih Opsi Revisi',
                    text: "Pilih jenis revisi yang diinginkan:",
                    input: 'radio',
                    inputOptions: {
                        'spp': 'SPP',
                        'kas_bank': 'Kas Bank'
                    },
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Harap pilih salah satu opsi revisi!';
                        }
                    },
                    confirmButtonText: 'Lanjutkan'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const opsiRevisi = result.value;
                        let confirmText = opsiRevisi === 'kas_bank' ? 'Kembalikan Bukti Kas' : 'Kembalikan PP';

                        Swal.fire({
                            title: 'Apakah Anda Yakin?',
                            text: "Mengembalikan Data PP!",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#41B314',
                            cancelButtonColor: '#F9354C',
                            confirmButtonText: confirmText
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#form-revisi').append(
                                    `<input type="hidden" name="jenis_revisi" value="${opsiRevisi}">`
                                );
                                $('#form-revisi').submit();
                                Swal.fire({
                                    title: confirmText,
                                    text: 'PP berhasil dikembalikan.',
                                    icon: 'success',
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                })
                            }
                        });
                    }
                });
            } else {
                // Hak akses selain 39: Langsung tampilkan konfirmasi
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
                        $('#form-revisi').submit();
                        Swal.fire({
                            title: 'Mengembalikan PP',
                            text: 'PP berhasil dikembalikan.',
                            icon: 'success',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                        })
                    }
                });
            }
        }


        // function revisi(id, data) {
        //     $("#modal_revisi").modal('show');
        //     if (data != null) {
        //         $('#keterangan_revisi').val(data).attr('readonly', 'readonly');
        //     } else {
        //         $('#keterangan_revisi').attr('required', true);
        //     }
        //     // Setel URL aksi formulir secara dinamis berdasarkan jenis revisi
        //     $("#form-revisi").attr('action', 'sppd/revisi/' + id);
        // }

        // function confirm_revisi() {
        //     let ket = $('#keterangan_revisi').val();
        //     if (ket === "") {
        //         alert("Isi Keterangan!!");
        //         return;
        //     }

        //     let opsiRevisi = $('input[name="opsi_revisi"]:checked').val(); // Dapatkan opsi yang dipilih

        //     Swal.fire({
        //         title: 'Apakah Anda Yakin?',
        //         text: "Mengembalikan Data PP!",
        //         icon: 'question',
        //         showCancelButton: true,
        //         confirmButtonColor: '#41B314',
        //         cancelButtonColor: '#F9354C',
        //         confirmButtonText: 'Kembalikan PP'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             let actionUrl = $('#form-revisi').attr('action');

        //             // Tambahkan jenis revisi ke URL berdasarkan opsi yang dipilih
        //             if (opsiRevisi === 'spp') {
        //                 actionUrl = 'sppd/revisi/' + id + '/spp';
        //             } else if (opsiRevisi === 'kas_bank') {
        //                 actionUrl = 'sppd/revisi/' + id + '/kas_bank';
        //             }

        //             // Perbarui URL aksi formulir
        //             $('#form-revisi').attr('action', actionUrl);
        //             $('#form-revisi').submit();

        //             Swal.fire({
        //                 title: 'Mengembalikan PP',
        //                 text: 'PP berhasil dikembalikan.',
        //                 icon: 'success',
        //                 allowOutsideClick: false,
        //                 showConfirmButton: false,
        //             });
        //         }
        //     });
        // }




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

        function edit_bukti_kas_upload() {
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
        $("#no_doc").keypress(function(event) {
            var character = String.fromCharCode(event.keyCode);
            return isValid(character);
        });

        function isValid(str) {
            return !/[~`!@#$%\^&*()+=\-\[\]\\';,/{}|\\":<>\?]/g.test(str);
        }
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
