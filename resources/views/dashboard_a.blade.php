@extends('template.master')
@section('header')
    {{-- Chart js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
@endsection
@section('title', 'Dashboard Superman')
@section('konten')
    <?php

    $level = Session::get('level');
    $username = Session::get('username');
    $bagian = Session::get('bagian');
    $grup = Session::get('grup_ui');
    $hakAkses = Session::get('hak_akses');
    ?>
    {{-- @dd($level, $username, $bagian, $grup, $hakAkses); --}}
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <!-- OVERVIEW -->
                <div class="panel panel-headline">
                    <div class="panel-heading">
                        <h3 class="panel-title">Selamat Datang</h3>
                        <p class="panel-subtitle">{{ $username ?? 'Tamu' }}</p>
                    </div>
                    @if ($username)
                        @if (!in_array($hakAkses, [1, 20, 45, 46]))
                            <div class="panel-body">
                                <div class="row">
                                    @if ($level == 1)
                                        @if ($bagian !== 2)
                                            <div class="col-md-3">
                                                <div class="metric" id="info_spp" onclick="window.location.href='sppd'"
                                                    onmouseover="mouse()">
                                                    <span class="icon"><i class="fa fa-file-text-o"></i></span>
                                                    <p>
                                                        <span class="number">{{ $jumlah_proses }}</span>
                                                        <span class="title">SPP yang perlu diproses</span>
                                                    </p>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md-3">
                                                <div class="metric" id="info_spp"
                                                    onclick="window.location.href='spp_keuangan'" onmouseover="mouse()">
                                                    <span class="icon"><i class="fa fa-file-text-o"></i></span>
                                                    <p>
                                                        <span class="number">{{ $jumlah_proses }}</span>
                                                        <span class="title">SPP yang perlu diproses</span>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        @if ($bagian == 2)
                                            <div class="col-md-3">
                                                <div class="metric" id="info_spp" onclick="window.location.href='sppd'"
                                                    onmouseover="mouse()">
                                                    <span class="icon"><i class="fa fa-file-text-o"></i></span>
                                                    <p>
                                                        <span class="number">{{ $jumlah_spp }}</span>
                                                        <span class="title">SPP yang perlu diproses</span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="metric" id="info_spp_keuangan"
                                                    onclick="window.location.href='spp_keuangan'">
                                                    <span class="icon"><i class="fa fa-file-text-o"></i></span>
                                                    <p>
                                                        <span class="number">{{ $jumlah_spp_khusus }}</span>
                                                        <span class="title">SPP Khusus yang perlu diproses</span>
                                                    </p>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md-3">
                                                <div class="metric" id="info_spp" onclick="window.location.href='sppd'"
                                                    onmouseover="mouse()">
                                                    <span class="icon"><i class="fa fa-file-text-o"></i></span>
                                                    <p>
                                                        {{-- @if ($grup == 1)
                                                    <span class="number">{{$data_operator}}</span>
                                                    @elseif($user->master_bagian_id == 111)
                                                    <span class="number">{{$datas}}</span>
                                                    @else
                                                    <span class="number">{{$data_bagian}}</span>
                                                    @endif --}}
                                                        <span class="number">{{ $total_proses }}</span>
                                                        <span class="title">SPP yang perlu diproses</span>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif

                </div>
                <!-- END OVERVIEW -->
            </div>
            @if (in_array(SESSION::get('hak_akses'), [1, 20, 45, 46]) || !$username)
                @include('components.charts.terima_keluar')
                @include('components.charts.pie_chart_spp')
            @endif
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- END MAIN -->

    <script>
        // function mouse(){
        // 	document.getElementById("info_spp").style.cssText = "background-color:blue;color:white;"
        // }
        $(document).ready(function() {
            $('#info_spp').mouseenter(function() {
                document.getElementById("info_spp").style.cssText = "background-color:orange;color:white;"
            }).mouseleave(function() {
                document.getElementById("info_spp").style.cssText = "background-color:white;color:black;"

            });
            $('#info_spp_keuangan').mouseenter(function() {
                document.getElementById("info_spp_keuangan").style.cssText =
                    "background-color:red;color:white;"
            }).mouseleave(function() {
                document.getElementById("info_spp_keuangan").style.cssText =
                    "background-color:white;color:black;"

            });
        });
    </script>

@endsection
