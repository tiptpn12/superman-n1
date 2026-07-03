<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--  -->

<head>
    <title> @yield('title') </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="_token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- VENDOR CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('') }}assets/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('') }}assets/vendor/linearicons/style.css">
    <link rel="stylesheet" href="{{ asset('') }}assets/vendor/chartist/css/chartist-custom.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <!-- MAIN CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/css/main.css">
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/css/timeline.css">
    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    <!-- ICONS -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" sizes="76x76" href="{{ asset('') }}assets/img/LOGO-PTPN-I.png">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('') }}assets/img/LOGO-PTPN-I.png">
    <!-- JAVASCRIPT -->
    <script src="{{ asset('') }}assets/vendor/jquery/jquery.min.js"></script>

    @yield('header')

    <style type="text/css">
        .swal2-popup {
            font-size: 1.6rem !important;
        }

        .modal {
            text-align: center;
        }

        @media screen and (min-width: 768px) {
            .modal:before {
                display: inline-block;
                vertical-align: middle;
                content: " ";
                height: 100%;
            }
        }

        .modal-dialog {
            display: inline-block;
            text-align: left;
            vertical-align: middle;
        }

        .table thead,
        .table th {
            text-align: center;
            vertical-align: middle !important;
        }

        .notification-div {
            position: relative;
        }

        .icon-notification {
            font-size: 26px;
        }

        .count-notification {
            position: absolute;
            top: -5px;
            right: -7px;
            font-size: 12px;
        }

        a.list-notification,
        .list-notification {
            width: 100%;
            padding: 12px 10px;
            background-color: #ffffff;
            border-bottom: 1px solid #a7bbcd;
            font-weight: 600;
            color: black;
            display: inline-block
        }

        a.list-notification {
            color: black;
        }

        a.list-notification:hover {
            /* color: rgb(110, 110, 110); */
            background-color: #efefef;
            cursor: pointer;
        }

        .markReadBtn {
            margin: 10px 7px;
        }

        .navbar-nav>li>.dropdown-notifications {
            min-width: 310px;
        }

        .dropdown-notifications {
            height: auto;
            max-height: 240px;
            overflow-x: hidden;
        }

        #loadingNotif {
            background-color: #e5e5e5;
            padding: 10px;
            text-align: center;
            display: block;
        }

        /*.dataTables_scrollHeadInner, .table{ width:100%!important; }*/
    </style>
</head>
<?php
$username = Session::get('username');
$hakakses = Session::get('hak_akses');
$bagian = Session::get('bagian');
$grup_id = Session::get('grup_ui');
$level = Session::get('level');
?>

<body class="layout-fullwidth">
    <!-- WRAPPER -->
    <div id="wrapper">
        <!-- NAVBAR -->
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="brand">
                <a href="{{ url('dashboard') }}"><img src="{{ asset('') }}assets/img/LOGO-PTPN-I.png"
                        alt="Klorofil Logo" class="img-responsive logo" style="height:64px"></a>
            </div>
            <div class="container-fluid">
                @if ($username && $hakakses != 46)
                    <div class="navbar-btn">
                        <button type="button" id="navbar_btn" class="btn-toggle-fullwidth"><i
                                class="lnr lnr-arrow-right-circle"></i></button>
                    </div>
                @endif

                {{-- <form class="navbar-form navbar-left">
					<div class="input-group">
						<input type="text" value="" class="form-control" placeholder="Search dashboard...">
						<span class="input-group-btn"><button type="button" class="btn btn-primary">Go</button></span>
					</div>
				</form>
				<div class="navbar-btn navbar-btn-right">
				</div> --}}
                @if ($username)
                    <div id="navbar-menu">
                        <ul class="nav navbar-nav navbar-right">

                            {{-- dibawah ini kodingan yang dikomen adalah kodingan untuk mendapatkan
                                notifikasi jika menggunakan SppdController kemudidan get dari database
                                artinya perlu refresh untuk mendapat notif baru --}}
                            <li class="dropdown" id="notificationToogle">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <div class="notification-div">
                                        <i class="lnr lnr-alarm icon-notification"></i>
                                        {{-- @if (isset($notifNewSpp))
                                            @if (count($notifNewSpp) != 0)
                                                <span class="badge bg-danger count-notification">{{ count($notifNewSpp) }}</span>
                                        @endif
                                        @endif --}}
                                        <span id="notifCounter" class="badge bg-danger count-notification"></span>
                                    </div>
                                </a>
                                <ul class="dropdown-menu" style="min-width: 390px;">
                                    <button type="button" class="btn btn-xs btn-success markReadBtn"
                                        onclick="markAllAsRead()">
                                        Tandai Semua dibaca
                                    </button>
                                    <button type="button" class="btn btn-xs btn-danger deleteNotifBtn"
                                        onclick="deleteAllNotifications()">
                                        Hapus Semua Notifikasi
                                    </button>
                                    <div id="dropdownNotif" class="dropdown-notifications">
                                        <div id="loadingNotif">
                                            <span style="font-size: 25px">Loading ... </span>
                                        </div>
                                        {{-- @if (isset($notifNewSpp))
                                            @if (count($notifNewSpp) != 0)
                                                @foreach ($notifNewSpp as $notif)
                                                    <li class="list-notification">
                                                        <span>{{ $notif->data['message'] }} dengan id {{ $notif->data['spp_id'] }} dibuat oleh {{ $notif->data['username'] }}</span>
                                                        <button type="button" class="btn btn-xs btn-warning markReadBtn" onclick="markAsRead('{{ $notif->id }}')">
                                                            mark as read
                                                        </button>
                                                    </li>
                                                @endforeach
                                            @else
                                                <li class="list-notification">Tidak ada notifikasi</li>
                                            @endif
                                        @else
                                            <li class="list-notification">Notifikasi Error</li>
                                        @endif --}}
                                    </div>
                                </ul>

                            </li>


                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                                        class="lnr lnr-users"></i> <span>{{ $username }}</span> <i
                                        class="icon-submenu lnr lnr-chevron-down"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="{{ route('change_password') }}"><i class="lnr lnr-lock"></i>
                                            <span>Ubah
                                                Password</span></a></li>
                                    <li><a href="{{ route('logout') }}"><i class="lnr lnr-exit"></i>
                                            <span>Logout</span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                @endif

            </div>
        </nav>
        <!-- END NAVBAR -->
        <!-- LEFT SIDEBAR --><br><br>
        @if ($username && $hakakses != 46)
            <div id="sidebar-nav" class="sidebar">
                <div class="sidebar-scroll">
                    <nav>
                        <ul class="nav">
                            @if ($hakakses == 3 && $bagian == 2)
                                <li><a href="{{ url('dashboard_kabag_kasi') }}" class="@yield('aktif')"><i
                                            class="lnr lnr-home"></i> <span>Dashboard</span></a></li>
                            @else
                                <li><a href="{{ url('dashboard') }}" class="@yield('aktif')"><i
                                            class="lnr lnr-home"></i>
                                        <span>Dashboard</span></a></li>
                            @endif
                            @if ($hakakses == 3)
                                <li><a href="{{ url('profil_bagian/' . $bagian) }}" class="@yield('kabag')"><i
                                            class="lnr lnr-user"></i><span>Profil Kepala Bagian</span></a></li>
                            @endif
                            {{-- @if ($hakakses < 4 && $bagian != 2)
                                    <li><a href="{{ url('sppd') }}"  class="@yield('open')"><i class="lnr lnr-file-empty"></i> <span>SPPb / SPPn</span></a></li>
                                @elseif ($hakakses > 3 && $bagian == 2)
                                    <li><a href="{{ url('sppd') }}"  class="@yield('open')"><i class="lnr lnr-file-empty"></i> <span>SPPb / SPPn</span></a></li>
                                @if ($hakakses == 3 && $bagian == 2)
                                    <li><a href="{{ url('sppd') }}"  class="@yield('open')"><i class="lnr lnr-file-empty"></i> <span>SPPb / SPPn</span></a></li>
                                @endif --}}
                            @if (($hakakses > 0 && $hakakses != 45))
                                <li><a href="{{ url('sppd') }}" class="@yield('open')"><i
                                            class="lnr lnr-file-empty"></i> <span>SPPb / SPPn</span></a></li>
                            @endif


                            @if ($hakakses == 25 || $hakakses == 38)
                                <li><a href="{{ url('pembayaran') }}" class="@yield('bukak')"><i
                                            class="lnr lnr-file-empty"></i> <span>Ajukan Pembayaran</span></a></li>
                            @endif
                            @if (($hakakses != 45 && $hakakses != 20) || $grup_id == 9)
                                <li><a href="{{ url('laporan') }}" class="@yield('yoro')"><i
                                            class="lnr lnr-database"></i> <span>Laporan</span></a></li>
                            @endif
                            @if ($hakakses == 1 || ($hakakses == 3 && $bagian == 2))
                                <li><a href="{{ url('histori_login') }}" class="@yield('active')"><i
                                            class="lnr lnr-history"></i> <span>Histori Login</span></a></li>
                            @endif
                            {{-- ini tambahan buat admin_regional --}}
                            @if (($grup_id == 8 && $hakakses != 50 && $hakakses != 1) || $hakakses == 45)
                                <li>
                                    <a href="#master_data" data-toggle="collapse" class="collapsed"><i
                                            class="lnr lnr-dice"></i> <span>Master Data</span> <i
                                            class="icon-submenu lnr lnr-chevron-left"></i></a>
                                    <div id="master_data" class="collapse ">
                                        <ul class="nav">
                                            <li><a href="{{ url('bagian') }}" class="">Bagian</a></li>
                                            <li><a href="{{ url('cetak_spp') }}" class="">Master Cetak SPP</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.cetakbuktikas.index') }}"
                                                    class="">Cetak Bukti Kas
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endif
                            @if (($grup_id == 8 && $hakakses != 20 && $hakakses != 50) || $hakakses == 1)
                                <li>
                                    @if ($hakakses == 1)
                                    <a href="#master_data" data-toggle="collapse" class="collapsed"><i
                                            class="lnr lnr-dice"></i> <span>Master Data</span> <i
                                            class="icon-submenu lnr lnr-chevron-left"></i></a>
                                    <div id="master_data" class="collapse ">
                                        <ul class="nav">
                                                <li><a href="{{ url('vendor') }}" class="">Vendor</a></li>
                                                <li><a href="{{ url('rekening') }}" class="">Kode Rekening</a>
                                                </li>
                                                <li><a href="{{ url('cost_center') }}" class="">Cost Center</a>
                                                </li>
                                                <li><a href="{{ url('profit_center') }}" class="">Profit
                                                        Center</a>
                                                </li>
                                                <li><a href="{{ url('cash_flow') }}" class="">Cash Flow</a>
                                                </li>
                                                <li><a href="{{ url('gl') }}" class="">GL</a></li>
                                                <li><a href="{{ url('gl_detail') }}" class="">GL Detail</a>
                                                </li>
                                                <li><a href="{{ url('customer') }}" class="">Customer</a></li>
                                                <li><a href="{{ url('bahan_jasa') }}" class="">Bahan & Jasa</a>
                                                </li>
                                                <li><a href="{{ url('rkap') }}" class="">Budget RKAP</a></li>
                                                <!-- <li><a href="{{ url('bank') }}" class="">Rekening Bank</a></li> -->
                                            @endif
                                            <li><a href="{{ url('cetak_spp') }}" class="">Master Cetak
                                                    SPP</a>
                                            <li>
                                                <a href="{{ route('admin.cetakbuktikas.index') }}"
                                                    class="">Cetak Bukti Kas
                                                </a>
                                            </li>
				        </ul>
                                    </div>
                                </li>
                            @endif
                            @if (($grup_id == 8 && $hakakses != 20 && $hakakses != 50) || $hakakses == 1)
                                <li>
                                    <a href="#master_sistem" data-toggle="collapse" class="collapsed"><i
                                            class="lnr lnr-dice"></i> <span>Master Sistem</span> <i
                                            class="icon-submenu lnr lnr-chevron-left"></i></a>
                                    <div id="master_sistem" class="collapse ">
                                        <ul class="nav">
                                            @if ($hakakses == 1)
                                                <!-- <li><a href="{{ url('bank') }}" class="">Rekening Bank</a></li> -->
                                                <li><a href="{{ url('company') }}" class="">Company</a></li>
                                                <li><a href="{{ url('bagian') }}" class="">Bagian</a></li>
                                                <li><a href="{{ url('hak_akses') }}" class="">Hak Akses</a>
                                                </li>
                                                <li><a href="{{ url('tampilan') }}" class="">Tampilan/UI</a>
                                                </li>
                                                <li><a href="{{ url('flow') }}" class="">Flow</a></li>
                                                <li><a href="{{ url('user') }}" class="">User</a></li>
                                            @else
                                                <li><a href="{{ url('bagian') }}" class="">Bagian</a></li>
                                                <li><a href="{{ url('user') }}" class="">User</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        @endif

        <!-- END LEFT SIDEBAR -->

        @yield('konten')

        <div class="clearfix"></div>
        <footer>
            <div class="container-fluid">
                <p class="copyright">&copy; 2024 <a href="https:///ptpn1.co.id/" target="_blank">PT. Perkebunan
                        Nusantara I</a>. All Rights Reserved.</p>
            </div>
        </footer>
    </div>
    <!-- END WRAPPER -->
    <!-- Javascript -->
    <script src="{{ asset('') }}assets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ asset('') }}assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <script src="{{ asset('') }}assets/vendor/jquery.easy-pie-chart/jquery.easypiechart.min.js"></script>
    <script src="{{ asset('') }}assets/vendor/chartist/js/chartist.min.js"></script>
    <script src="{{ asset('') }}assets/vendor/mask/jquery.mask.min.js"></script>
    <script src="{{ asset('') }}assets/scripts/klorofil-common.js"></script>
    @yield('footer')

    <script type="text/javascript">
        $('.date').datepicker({
            format: "dd-mm-yyyy"
        });

        $('.date-range').daterangepicker({
            locale: {
                format: 'DD-MM-YYYY'
            }
        });

        $("#navbar_btn").click(function() {
            setTimeout(function() {
                table.columns.adjust().draw();
            }, 300);
        });

        function markAsRead(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('markNotifAsRead') }}",
                method: 'POST',
                data: {
                    id
                }
            });
        }

        function markAllAsRead() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('markAllNotifAsRead') }}",
                method: 'POST'
            });
        }

        function deleteAllNotifications() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('deleteAllNotifications') }}",
                method: 'POST'
            });
        }

        function getUserNotification() {
            // let textNotifCounter = ;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('getUserNotification') }}",
                method: 'POST',
                success: function(result) {

                    $("#loadingNotif").hide();
                    console.log(result);
                    console.log(result.length);
                    if (result.length != 0) {
                        $("#notifCounter").show();
                        $("#notifCounter").text(result.length);
                        result.forEach(element => {
                            let routeUrl = "{{ route('detailspp', ['id' => ':spp_id']) }}";
                            routeUrl = routeUrl.replace(":spp_id", element.data.spp_id);
                            $("#dropdownNotif").append(
                                `<a class="list-notification"
                                        href="${routeUrl}"
                                        onclick="markAsRead('${element.id}')"
                                        target="_blank">
                                            <span onclick="markAsRead('${element.id}')">
                                                SPP masuk dengan nomor
                                                ${element.data.isSppCampuran ?
                                                    element.data.sppb_nomor +" dan "+element.data.sppn_nomor
                                                    : element.data.sppb_nomor ? element.data.sppb_nomor : element.data.sppn_nomor}
                                                dikirim dari ${element.data.username}
                                            </span>
                                    </a>`
                            );

                        });
                    } else {
                        $("#notifCounter").hide();
                        $("#dropdownNotif").append(
                            `<span class="list-notification">
                                    Tidak ada notifikasi
                                </span>`
                        )
                    }

                }
            });
        }

        // tampilkan badge jumlah notifikasi user
        // yang belum dibaca, jika 0 maka hide badge
        // function countUserNotification() {
        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        //         }
        //     });
        //     $.ajax({
        //         url: "{{ route('getUserNotification') }}",
        //         method: 'POST',
        //         success: function(result) {
        //             if (result.length != 0) {
        //                 $("#notifCounter").show();
        //                 $("#notifCounter").text(result.length);
        //             } else {
        //                 $("#notifCounter").hide();
        //             }
        //         }
        //     });
        // }


        // $(document).ready(function() {
        //     getUserNotification();

        //     setInterval(() => {
        //         // $(".list-notification").remove();
        //         countUserNotification();
        //         // $("#loadingNotif").show();
        //     }, 3000);

        //     $("#notificationToogle").on('shown.bs.dropdown', function() {
        //         $(".list-notification").remove();
        //         getUserNotification()
        //         $("#loadingNotif").show();
        //     });


        //     // $(".link-spp-notif").click(function(){
        //     //     markAsRead()
        //     // });
        // })



        function consoleHello(msg) {
            console.log(msg);
        }

        table = $('.table').not('#table').DataTable({
            "scrollX": true,
            "bAutoWidth": true,
        });
    </script>
</body>

</html>
