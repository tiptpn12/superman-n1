<!DOCTYPE html>
<html>
    <head>
    <title> SPP Khusus | Cetak</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="_token" content="{{csrf_token()}}" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<!-- VENDOR CSS -->
	<link rel="stylesheet" href="{{asset('')}}assets/vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="{{asset('')}}assets/vendor/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="{{asset('')}}assets/vendor/linearicons/style.css">
	<link rel="stylesheet" href="{{asset('')}}assets/vendor/chartist/css/chartist-custom.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
	<!-- MAIN CSS -->
	<link rel="stylesheet" href="{{asset('')}}assets/css/main.css">
	<!-- CUSTOM CSS -->
	<link rel="stylesheet" href="{{asset('')}}assets/css/timeline.css">
	<!-- GOOGLE FONTS -->
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
	<!-- ICONS -->
	<link rel="apple-touch-icon" sizes="76x76" href="{{asset('')}}assets/img/logo-ptpn.png">
	<link rel="icon" type="image/png" sizes="96x96" href="{{asset('')}}assets/img/logo-ptpn.png">
	<!-- JAVASCRIPT -->
	<script src="{{asset('')}}assets/vendor/jquery/jquery.min.js"></script>
    <style type="text/css">
    @media print {
        .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
        float: left;
        }
        .col-sm-12 {
        width: 100%;
        }
        .col-sm-11 {
        width: 91.66666667%;
        }
        .col-sm-10 {
        width: 83.33333333%;
        }
        .col-sm-9 {
        width: 75%;
        }
        .col-sm-8 {
        width: 66.66666667%;
        }
        .col-sm-7 {
        width: 58.33333333%;
        }
        .col-sm-6 {
        width: 50%;
        }
        .col-sm-5 {
        width: 41.66666667%;
        }
        .col-sm-4 {
        width: 33.33333333%;
        }
        .col-sm-3 {
        width: 25%;
        }
        .col-sm-2 {
        width: 16.66666667%;
        }
        .col-sm-1 {
        width: 8.33333333%;
        }
        .panel {
            transform-origin : top;
            transform : scale(1.077,0.89);
            
        }
        table.table-bordered{
        border:1px solid black !important;
        margin-top:20px;
        }
        table.table-bordered > thead > tr > th{
            border:1px solid black !important;
        }
        table.table-bordered > tbody > tr > td {
            border:1px solid black !important;
            border-bottom-style:hidden !important;
            padding-left: 5px !important;
            padding-top: 3px !important; 
            padding-bottom: 1px !important; 
        }
        table.table-bordered > tbody > tr > th{
            border:1px solid black !important;
            padding-left: 5px !important;
            padding-top: 3px !important; 
            padding-bottom: 1px !important; 
        }
        table.table-bordered > tfoot > tr > td{
            border:1px solid black !important;
        }

        table.table-bordered > tfoot > tr > td.terbilang{
            border:1px solid black !important;
            border-right-style:hidden !important;
        }
    }
    </style>
    <input type="hidden" id="formspp" value="{{$formspp}}">
    </head>

    <body onload="window.print();">
    <div class="wrapper">
    <div class="container">
    <div class="row">
        <div class="col-sm-13">
        @if(isset($sppb))
            <div class="panel" id="panel-sppb">
                <div class="panel-body">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-sm-8">
                            <h5 style="text-align:left; font-weight:bold;" >PT PERKEBUNAN NUSANTARA XII</h5>
                        </div>
                    </div>
                    
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-7 invoice-col">
                            Nomor   : {{$sppb['sppb_no']}}
                            <br>
                            Tanggal : {{date('d-m-Y',strtotime($sppb['sppb_tanggal']))}}
                        </div>
                        <!-- /.col -->
                        
                        <!-- /.col -->
                        <div class="col-sm-5 invoice-col">
                            <div style="text-align:left">
                            
                            <b> Kepada Yth.</b>
                            <br>
                            <b>SEVP Business Support</b> <br>
                            <b>PT. Perkebunan Nusantara XII</b>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <div class="row">
                        <center>
                            <br><br>
                            <h4 style="font-weight:bold; text-decoration:underline;">SURAT PERMINTAAN PEMBAYARAN (SPPb)</h4>
                            <br>
                        </center>
                    </div>
                    <!-- Table row -->
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                        Dengan ini dimohon bantuannya untuk dibuatkan Bukti Keluar Kas / Bank untuk Pembayaran :
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th  style="vertical-align:middle">
                                            <div class="col-sm-6">
                                                Kwitansi dari  
                                            </div>
                                            <div class="col-sm-6">
                                                : {{$sppb['sppb_kwitansi']}}
                                            </div>
                                        <br>
                                            <div class="col-sm-6">
                                                AU.53 No
                                            </div>
                                            <div class="col-sm-6">
                                                : {{$sppb['sppb_au_53']}} 
                                            </div>
                                        </th>
                                        <th  style="vertical-align:middle">
                                        <div class="col-sm-8">Nomor Faktur Pajak</div>
                                        @if(isset($sppb[1][0]))
                                        <div class="col-sm-4">: {{$sppb[1][0]->faktur_pajak_nomor}} </div>
                                        @else
                                        <div class="col-sm-4">: - </div>  
                                        @endif  
                                        <br>
                                        @for($i=1;$i < count($sppb[1]); $i++)
                                        <div class="col-sm-8"></div><div class="col-sm-4">: {{$sppb[1][$i]->faktur_pajak_nomor}}</div>
                                        <br>
                                        @endfor
                                        
                                        <div class="col-sm-8">Nomor SP/OPL/SPK/Perjanjian</div><div class="col-sm-4"> : {{$sppb['sppb_sp_opl']}} </div>
                                        
                                       
                                        </th>
                                    </tr>
                                    </thead>
                                    </table>
                                <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan = "4" style="text-align:center"> KODE </th>
                                        <th rowspan = "2" style="text-align:center; vertical-align:middle" > URAIAN </th>
                                        <th rowspan = "2" style="text-align:center; vertical-align:middle"> Jumlah <br> Rp.</th>
                                    </tr>
                                    
                                    <tr>
                                        <th style="margin:0; text-align:center">KBB</th>
                                        <th style="text-align:center">SAP</th>
                                        <th style="text-align:center">CC/PC</th>
                                        <th style="text-align:center">CF</th>
                                    </tr>
                                </thead>
                                <tbody style="height:200px;">
                                @if(isset($sppb[0]))
								@for($i=0;$i< count($sppb[0]); $i++)
                                    <tr>
                                    <td style="text-align:center;" >{{$sppb[0][$i]['master_kode_kbb']}}</td>
											@if($sppb[0][$i]['master_gl_id'] != null)
                                            <td  style="text-align:center">{{$sppb[0][$i]['master_gl_kode']}}</td>
                                            @elseif($sppb[0][$i]['master_customer_id'] != null)
                                            <td  style="text-align:center">{{$sppb[0][$i]['master_customer_kode_sap']}}</td>
                                            @else
                                            <td  style="text-align:center">{{$sppb[0][$i]['master_rekening_kode_sap']}}</td>

                                            @endif
											<td style="text-align:center">{{$sppb[0][$i]['master_cost_center_kode']}}{{$sppb[0][$i]['master_profit_center_kode']}}</td>
                                            <td style="text-align:center">{{$sppb[0][$i]['master_cash_flow_kode']}}</td>
											<td>
                                                <?php $b = str_replace('</p>','</n>',$sppb[0][$i][0][0]->sppb_uraian_uraian)?>
											    {!! $b !!} <br>
                                            </td>
                                            <td style="text-align:right">
                                                {{number_format($sppb[0][$i][0][0]->sppb_uraian_nominal)}}</br>
                                            </td>
                                            </tr>
                                            @if(count($sppb[0][$i][0]) > 1)
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>
                                                @for($a=1; $a < count($sppb[0][$i][0]); $a++)
                                                    <?php $b = str_replace('</p>','</n>',$sppb[0][$i][0][$a]->sppb_uraian_uraian)?>
                                                    {!! $b !!} <br>
                                                @endfor
                                                </td>
                                                <td style="text-align:right">
                                                @for($a=1; $a < count($sppb[0][$i][0]); $a++)
                                                    {{number_format($sppb[0][$i][0][$a]->sppb_uraian_nominal)}}</br>
                                                @endfor
                                                </td>
                                            </tr>
                                            @endif
                                    @endfor
                                    @endif
                                    <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    @if(isset($sppb['sppb_catatan']) && $sppb['sppb_catatan']!== null)
                                    <td><b> Catatan : </b> <br>  {{$sppb['sppb_catatan']}} </td>
                                    @else
                                    <td></td>
                                    @endif
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    @if($sppb['sppb_metode_pembayaran'] == 'kas')
                                    <th><b> Dibayarkan dengan cash  <br>
                                        @if($sppb['sppb_jenis'] == "karyawan")
                                        @foreach($karyawan_sppb as $k)
                                                a/n. : {{$k->karyawan_nama}}<br>

                                        @endforeach
                                        Sebesar : <b> Rp{{number_format($sppb['sppb_total'])}} </b></th>

                                        @endif
                                        </th>
                                    @elseif($sppb['sppb_metode_pembayaran'] == 'bank')
                                    <th> 
                                        @if($sppb['sppb_jenis'] == "karyawan")
                                        @foreach($karyawan_sppb as $k)
                                            Transfer ke : {{$k->karyawan_nama_bank}} <br>
                                            A/C :   {{$k->karyawan_no_rek}} <br>
                                                a/n. : {{$k->karyawan_nama}} <br>
                                        @endforeach
                                        Sebesar : <b> Rp{{number_format($sppb['sppb_total'])}} </b></th>
                                   
                                        @else
                                            @if($sppb['sppb_data_metpen'] == 'input_data')
                                                Transfer ke : {{$karyawan_sppb[0]->karyawan_nama_bank}} <br>
                                                A/C :   {{$karyawan_sppb[0]->karyawan_no_rek}} <br>
                                                a/n. : {{$karyawan_sppb[0]->karyawan_nama}} <br>
                                                Sebesar : <b> Rp{{number_format($sppb['sppb_total'])}} </b></th>

                                            @elseif($sppb['sppb_data_metpen'] == 'lampirkan_data')
                                                Transfer ke : TERLAMPIR <br>
                                                A/C : TERLAMPIR <br>
                                                a/n. : TERLAMPIR <br>

                                            @else
                                            Transfer ke : {{$sppb['master_vendor_nama_bank']}} <br>
                                            A/C :   {{$sppb['master_vendor_rekening']}} <br>
                                            a/n. : {{$sppb['master_vendor_nama']}} <br>
                                            Sebesar : <b> Rp{{number_format($sppb['sppb_total'])}} </b></th>
                                            @endif
                                        @endif
                                    @elseif($sppb['sppb_metode_pembayaran'] == 'skbdn')
                                    <th></th>
                                    @else
                                    <th></th>
                                    @endif
                                    <th></th>
                                    </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                        <td  class="terbilang" style="text-align:left"><b>TERBILANG:</td>
                                        <td colspan="4" style="text-transform:uppercase; text-align:center">  <b style="font-style:italic"> {{Terbilang::angka($sppb['sppb_total'])}} rupiah </b></td>
                                        <td style="text-align:right"> <b>{{number_format($sppb['sppb_total'])}}</td>
                                    </tr>
                                </tfoot>
                            </table>
                            Dokumen-dokumen syarat pembayaran kami lampirkan dan kami bertanggung jawab atas kebenarannya
                         
                        </div>
                       
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                        <div class="col-sm-6">
                            <br><br>
                            {!! QrCode::size(50)->generate('103.169.31.35:9910/superman/public/spp_keuangan/validasi_login/'.$id); !!}

                        </div>
                        <div class="col-sm-6">
                            <br>
                            <p style="text-align:center;">Bagian {{$sppb['master_bagian_nama']}} </p>
                            <br>
                            <br>
                            <p style="text-align:center; text-decoration:underline;">{{$sppb['master_bagian_kepala_bagian']}}</p>
                        </div>

                </div>
               
            </div>
            @endif

            @if(isset($sppb) && isset($sppn))
            <p style="page-break-after: always;">&nbsp;</p>
            <p style="page-break-before: always;">&nbsp;</p>
            @endif
            
            @if(isset($sppn))
            <div class="panel" id="panel-sppn" >
                <div class="panel-body">
                    <!-- title row -->

                    <div class="row">
                        <div class="col-sm-8">
                            <h5 style="text-align:left; font-weight:bold;" >PT PERKEBUNAN NUSANTARA XII</h5>

                        </div>
                    </div>
                    
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-7 invoice-col" >
                            Nomor   : {{$sppn['sppn_no']}}
                            <br>
                            Tanggal : {{date('d-m-Y',strtotime($sppn['sppn_tanggal']))}}
                        </div>
                        <!-- /.col -->
                        
                        <!-- /.col -->
                        <div class="col-sm-5 invoice-col">
                            <div style="text-align:left" >
                            <b>Kepada Yth.</b>
                            <br>
                            <b>SEVP Business Support</b> <br>
                           
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <div class="row">
                        <center>
                            <br><br>
                            <h4 style="font-weight:bold; text-decoration:underline;">SURAT PERMINTAAN PENERIMAAN (SPPn)</h4>
                            <br>
                        </center>
                    </div>
                    <!-- Table row -->
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                        Dengan ini dimohon bantuannya untuk dibuatkan Bukti Masuk Kas / Bank untuk Penerimaan :
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan = "3" style="vertical-align:middle">
                                            <div class="col-sm-6"> Kwitansi dari </div>
                                            <div class="col-sm-6"> : {{$sppn['sppn_kwitansi']}}</div>
                                        <br>
                                        <div class="col-sm-6">BA / AU 58 No</div>
                                        <div class="col-sm-6">: {{$sppn['sppn_ba_au_53']}}</div>
                                        </th>
                                        <th colspan = "2">
                                        <div class="col-sm-6">Nomor Faktur Pajak</div>
                                        @if(isset($sppn[1][0]))
                                        <div class="col-sm-6">: {{$sppn[1][0]->faktur_pajak_nomor}} </div>  
                                        @else
                                        <div class="col-sm-6">: - </div>  
                                        @endif
                                        <br>
                                        @for($i=1;$i < count($sppn[1]); $i++)
                                        <div class="col-sm-6"></div><div class="col-sm-6">: {{$sppn[1][$i]->faktur_pajak_nomor}}</div>
                                        <br>
                                        @endfor
                                        @if(isset($sppb['sppb_no']))
                                        <div class="col-sm-6">Nomor SPPb</div>
                                        <div class="col-sm-6">: {{$sppb['sppb_no']}}</div>
                                        @else
                                        <div class="col-sm-6">Nomor SPPb</div>
                                        <div class="col-sm-6">: -</div>
                                        @endif
                                        </th>
                                        
                                    </tr>
                                </thead>
                            </table>
                            <table class="table table-bordered">
                                <thead>    
                                    <tr>
                                        <th colspan = "4" style="text-align:center"> KODE </th>
                                        <th rowspan = "2" style="text-align:center; vertical-align:middle" > URAIAN </th>
                                        <th rowspan = "2" style="text-align:center; vertical-align:middle"> Jumlah <br> Rp.</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center">KBB</th>
                                        <th style="text-align:center">SAP</th>
                                        <th style="text-align:center">CC/PC</th>
                                        <th style="text-align:center">CF</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if(isset($sppn[0]))
								
								@for($i=0;$i < count($sppn[0]); $i++)
                                    <tr>
                                    <td style="text-align:center">{{$sppn[0][$i]['master_kode_kbb']}}</td>
                                        @if($sppn[0][$i]['master_gl_id'] != null)
                                            <td rowspan="{{count($sppn[0][$i][0])}}" style="text-align:center">{{$sppn[0][$i]['master_gl_kode']}}</td>
                                            @elseif($sppn[0][$i]['master_customer_id'] != null)
                                            <td rowspan="{{count($sppn[0][$i][0])}}" style="text-align:center">{{$sppn[0][$i]['master_customer_kode_sap']}}</td>
                                            @else
                                            <td rowspan="{{count($sppn[0][$i][0])}}" style="text-align:center">{{$sppn[0][$i]['master_rekening_kode_sap']}}</td>
                                            @endif
                                            
                                            <td style="text-align:center">{{$sppn[0][$i]['master_cost_center_kode']}}{{$sppn[0][$i]['master_profit_center_kode']}}</td>
                                            <td style="text-align:center">{{$sppn[0][$i]['master_cash_flow_kode']}}</td>
                                            <td>
                                                <?php $b = str_replace('</p>','</n>',$sppn[0][$i][0][0]->sppn_uraian_uraian)?>
											    {!! $b !!} <br>
                                            </td>
                                            <td style="text-align:right">
                                                {{number_format($sppn[0][$i][0][0]->sppn_uraian_nominal)}}</br>
                                            </td>
                                        </tr>
                                        @if(count($sppn[0][$i][0]) > 1)
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>
											@for($a=1; $a < count($sppn[0][$i][0]); $a++)
                                                <?php $b = str_replace('</p>','</n>',$sppn[0][$i][0][$a]->sppn_uraian_uraian)?>
											    {!! $b !!} <br>
                                            @endfor
                                            </td>
                                            <td style="text-align:right">
											@for($a=1; $a < count($sppn[0][$i][0]); $a++)
                                                {{number_format($sppn[0][$i][0][$a]->sppn_uraian_nominal)}}</br>
											@endfor
                                            </td>
                                        </tr>
                                        @endif
                                    @endfor
                                    @endif
                                    <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    @if(isset($sppn['sppn_catatan']) && $sppn['sppn_catatan'] !== null)
                                    <td><b> Catatan : </b> <br>  {{$sppn['sppn_catatan']}} </td>
                                    @else
                                    <td></td>
                                    @endif
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <!-- @if($sppn['sppn_metode_pembayaran'] == 'kas')
                                    <th><b> Dibayarkan dengan cash <br>
                                        @foreach($karyawan_sppn as $k)
                                                a/n. : {{$k->karyawan_nama}}<br>
                                        @endforeach
                                    </th>
                                    @else
                                    <th> 
                                    @if($sppn['sppn_metode_pembayaran'] == 'karyawan')
                                        @foreach($karyawan_sppn as $k)

                                            Transfer ke : {{$k->karyawan_nama_bank}} <br>
                                            A/C :   {{$k->karyawan_no_rek}} <br>
                                            
                                                a/n. : {{$k->karyawan_nama}}<br>
                                            @endforeach
                                         
                                        @else
                                            Transfer ke : {{$sppn['master_vendor_nama_bank']}} <br>
                                            A/C :   {{$sppn['master_vendor_rekening']}} <br>
                                         
                                                a/n. : {{$sppn['master_vendor_nama']}}<br>
                                           
                                        @endif
                                        Sebesar : <b> Rp{{number_format($sppn['sppn_jumlah'])}} </b></th>
                                    @endif -->
                                    <th></th>
                                    <th></th>
                                    </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                        <td class="terbilang" style="text-align:left"><b>TERBILANG:</td>
                                        <td colspan="4" style="text-transform:uppercase; text-align:center">  <b style="font-style:italic"> {{Terbilang::angka($sppn['sppn_jumlah'])}} rupiah </b></td>
                                        <td style="text-align:right"> <b>{{number_format($sppn['sppn_jumlah'])}}</td>
                                    </tr>
                                </tfoot>
                            </table>
                            Dokumen-dokumen syarat pembayaran kami lampirkan dan kami bertanggung jawab atas kebenarannya
                        </div>
                       
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                        <div class="col-sm-6">
                            <br><br>
                            {!! QrCode::size(50)->generate('103.169.31.35:9910/superman/public/spp_keuangan/validasi_login/'.$id); !!}

                        </div>
                        <div class="col-sm-6">
                            <br>
                            <p style="text-align:center;"> Bagian {{$sppn['master_bagian_nama']}} </p>
                            <br>
                            <br>
                            <p style="text-align:center; text-decoration:underline;">{{$sppn['master_bagian_kepala_bagian']}}</p>
                            
                        </div>

                    <br>
                    <br>
                    
                </div>
                
            </div>
            @endif
        </div>
    </div>
</div>
</div>
</body>

<script type="text/javascript">
    
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
    
</script>
    <script src="{{asset('')}}assets/vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap.min.js"></script>
	<script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
	<script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
	<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<script src="{{asset('')}}assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js"></script>
	<script src="{{asset('')}}assets/vendor/jquery.easy-pie-chart/jquery.easypiechart.min.js"></script>
	<script src="{{asset('')}}assets/vendor/chartist/js/chartist.min.js"></script>
	<script src="{{asset('')}}assets/vendor/mask/jquery.mask.min.js"></script>
	<script src="{{asset('')}}assets/scripts/klorofil-common.js"></script>
</html>