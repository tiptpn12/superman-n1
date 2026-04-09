
<head>
<title> Laporan | Web Export</title>
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
	<link rel="apple-touch-icon" sizes="76x76" href="{{asset('')}}assets/img/apple-icon.png">
	<link rel="icon" type="image/png" sizes="96x96" href="{{asset('')}}assets/img/favicon.png">
	<!-- JAVASCRIPT -->
	<script src="{{asset('')}}assets/vendor/jquery/jquery.min.js"></script>
  <style type="text/css">
    @media print {
      @page {
            size: A4 landscape;
            margin: 0mm !important;
        }
    }
  </style>
</head>
<body style="background-color:white; font-color:black;" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0">
  <center>
    @if($data_sppb !== [])
    @if($rentang_waktu == 'semua')
      @if(isset($data_sppb[0]))
        <h5 style="text-align:center; font-weight:bold" >DATA REKAP PEMBAYARAN <br> PERIODE {{$data_sppb[0]->tanggal}} - {{$data_sppb[count($data_sppb)-1]->tanggal}}</h5>
      @endif
    @else
        <h5 style="text-align:center; font-weight:bold" >DATA REKAP PEMBAYARAN <br> PERIODE {{$rentang_waktu}}</h5>
     
    @endif
            <table style="border-collapse:collapse">
                <thead>
                    <tr >
                    <th rowspan="2" style="font-weight:bold; text-align:center; border:1px solid black">No. </th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Tanggal</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Kode Cash Flow</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">No. Bukti Kas</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">No SPPb</th>
                    <th colspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Kode(D)</th>
                    <th colspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Kode(K)</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Profit/Cost Center</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Nama Customer/Kebun/Biaya</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Uraian</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Jumlah SPPb</th>

                  </tr>
                  <tr >
                    <th style="font-weight:bold; text-align:center; border:1px solid black" >SAP</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black" >KBB</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black" >SAP</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black" >KBB</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($data_sppb as $key => $value)
                <tr>
                    <td style="text-align:center; border:1px solid black">{{$key+1}}</td>
                    <td style="text-align:center; border:1px solid black">{{$value->tanggal}}</td>
                    <td style="text-align:center; border:1px solid black">{{$value->master_cash_flow_kode}}</td>  
                    <td style="text-align:center; border:1px solid black">{{$value->sppb_bayar_nomor_bukti_kas}}</td>
                    <td style="text-align:center; border:1px solid black">{{$value->sppb_no}}</td>
                    <td style="text-align:center; border:1px solid black">{{$value->sppb_kode_sap_bayar}}</td>
                    <td style="text-align:center; border:1px solid black">{{$value->sppb_kode_kbb_bayar}}</td>
                    @if($value->master_gl_id !== null)
                    <td style="text-align:center; border:1px solid black">{{$value->master_gl_kode}}</td>
                    @else
                    <td style="text-align:center; border:1px solid black">{{$value->master_vendor_rekening}}</td>
                    @endif  
                    <td style="text-align:center; border:1px solid black">{{$value->master_kode_kbb}}</td>
                    @if($value->master_cost_center_kode !== null)
                    <td style="text-align:center; border:1px solid black">{{$value->master_cost_center_kode}}</td>
                    @else
                    <td style="text-align:center; border:1px solid black">{{$value->master_profit_center_kode}}</td>
                    @endif
                    <td style="border:1px solid black">{{$value->master_vendor_nama}}</td>
                    <td style="border:1px solid black">{{strip_tags($value->sppb_uraian2)}}</td>
                    <td style="text-align:right; border:1px solid black">Rp. {{number_format($value->sppb_nominal_satuan)}}</td>
                  </tr>
                  @if(!isset($data_sppb[$key+1]) || ($data_sppb[$key]->sppb_isi_id !== $data_sppb[$key+1]->sppb_isi_id))
                  
                  <tr>
                    <td colspan="12" style="border:1px solid black"> <center>Total</center> </td>
                    <td style="text-align:center; border:1px solid black">Rp. {{number_format($value->sppb_total)}}</td>
                  </tr>
                  @endif
                @endforeach
                </tbody>
              </table>
    @endif
  @if($data_sppb !== [] && $data_sppn !== [])
  <p style="page-break-after: always;">&nbsp;</p>
  <p style="page-break-before: always;">&nbsp;</p>
  @endif

  @if($data_sppn !== []) 
  @if($rentang_waktu == 'semua')
      @if(isset($data_sppn[0]))
        <h5 style="text-align:center; font-weight:bold" >DATA REKAP PENERIMAAN <br> PERIODE {{$data_sppn[0]->tanggal}} - {{$data_sppn[count($data_sppn)-1]->tanggal}}</h5>
      @endif
    @else
        <h5 style="text-align:center; font-weight:bold" >DATA REKAP PENERIMAAN <br> PERIODE {{$rentang_waktu}}</h5>
     
    @endif
      

    <table style="border-collapse:collapse">
      <thead>
        <tr>
          <th rowspan="2" style="font-weight:bold; text-align:center; border:1px solid black">No. </th>
          <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Tanggal</th>
          <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Kode Cash Flow</th>
          <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">No. Bukti Kas</th>
          <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">No SPPn</th>
          <th colspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Kode(D)</th>
          <th colspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Kode(K)</th>
          <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Profit/Cost Center</th>
          <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Nama Customer/Kebun/Biaya</th>
          <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Uraian</th>
          <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Jumlah SPPn</th>

        </tr>
        <tr >
          <th style="font-weight:bold; text-align:center; border:1px solid black" >SAP</th>
          <th style="font-weight:bold; text-align:center; border:1px solid black" >KBB</th>
          <th style="font-weight:bold; text-align:center; border:1px solid black" >SAP</th>
          <th style="font-weight:bold; text-align:center; border:1px solid black" >KBB</th>
        </tr>
      </thead>
      <tbody>
      @foreach($data_sppn as $key => $value)
        <tr>
          <td style="text-align:center; border:1px solid black">{{$key+1}}</td>
          <td style="text-align:center; border:1px solid black">{{$value->tanggal}}</td>
          <td style="text-align:center; border:1px solid black">{{$value->master_cash_flow_kode}}</td>  
          <td style="text-align:center; border:1px solid black">{{$value->sppn_terima_nomor_bukti_kas}}</td>
          <td style="text-align:center; border:1px solid black">{{$value->sppn_no}}</td>
          <td style="text-align:center; border:1px solid black">{{$value->sppn_kode_sap_terima}}</td>
          <td style="text-align:center; border:1px solid black">{{$value->sppn_kode_kbb_terima}}</td>
          @if($value->master_gl_id !== null)
          <td style="text-align:center; border:1px solid black">{{$value->master_gl_kode}}</td>
          @else
          <td style="text-align:center; border:1px solid black">{{$value->master_vendor_rekening}}</td>
          @endif  
          <td style="text-align:center; border:1px solid black">{{$value->master_kode_kbb}}</td>
          @if($value->master_cost_center_kode !== null)
          <td style="text-align:center; border:1px solid black">{{$value->master_cost_center_kode}}</td>
          @else
          <td style="text-align:center; border:1px solid black">{{$value->master_profit_center_kode}}</td>
          @endif
          @if($value->master_vendor_nama !== null)
          <td style="border:1px solid black">{{$value->master_vendor_nama}}</td>
          @else
          <td style="border:1px solid black">{{$value->master_gl_keterangan}}</td>
          @endif
          <td style="border:1px solid black">{{strip_tags($value->sppn_uraian2)}}</td>
          <td style="text-align:right; border:1px solid black">Rp. {{number_format($value->sppn_uraian_nominal)}}</td>
        </tr>
        @if(!isset($data_sppn[$key+1]) || ($data_sppn[$key]->sppn_isi_id !== $data_sppn[$key+1]->sppn_isi_id))
        <tr>
          <td colspan="12" style="border:1px solid black"> <center>Total</center> </td>
          <td style="text-align:center; border:1px solid black">Rp. {{number_format($value->sppn_jumlah)}}</td>
        </tr>
        @endif
      @endforeach
      </tbody>
</table>
   @endif 
  @if($data !== [] && $data_sppn !== [])
  <p style="page-break-after: always;">&nbsp;</p>
  <p style="page-break-before: always;">&nbsp;</p>
  @endif
  @if($data !== [])
      
  @if($rentang_waktu == 'semua')
      @if(isset($data[0]))
        <h5 style="text-align:center; font-weight:bold" >DATA REKAP PEMBAYARAN DAN PENERIMAAN <br> PERIODE {{$data[0]->tanggal}} - {{$data[count($data)-1]->tanggal}}</h5>
      @endif
    @else
        <h5 style="text-align:center; font-weight:bold" >DATA REKAP PEMBAYARAN DAN PENERIMAAN <br> PERIODE {{$rentang_waktu}}</h5>
     
    @endif
            <table style="border-collapse: collapse">
                <thead>
                <tr>
                    <th rowspan="2" style="font-weight:bold; text-align:center; border:1px solid black">No. </th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Tanggal</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Kode Cash Flow</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">No. Bukti Kas</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">No SPPB/N</th>
                    <th colspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Kode(D)</th>
                    <th colspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Kode(K)</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Profit/Cost Center</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Nama Customer/Kebun/Biaya</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Uraian</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Jumlah SPPB/N</th>

                  </tr>
                  <tr >
                    <th style="font-weight:bold; text-align:center; border:1px solid black" >SAP</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black" >KBB</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black" >SAP</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black" >KBB</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($data as $key => $value)
                <tr>
                    <td style="text-align:center; border:1px solid black">{{$key+1}}</td>
                    <td style="text-align:center; border:1px solid black">{{$value->tanggal}}</td>
                    
                    @if(isset($value))
                    <td style="text-align:center; border:1px solid black">{{$value->master_cash_flow_kode}}</td>
                    @elseif(isset($value))
                    <td style="text-align:center; border:1px solid black">{{$value->master_cash_flow_kode}}</td>
                    @else
                    <td style="text-align:center; border:1px solid black"></td>
                    @endif

                    
                    @if(isset($value->sppb_bayar_nomor_bukti_kas) && $value->sppb_bayar_nomor_bukti_kas !== null)
                    <td style="text-align:center; border:1px solid black">{{$value->sppb_bayar_nomor_bukti_kas}}</td>
                    @elseif(isset($value->sppn_terima_nomor_bukti_kas) && $value->sppn_terima_nomor_bukti_kas!== null)
                    <td style="text-align:center; border:1px solid black">{{$value->sppn_terima_nomor_bukti_kas}}</td>
                    @else
                    <td style="text-align:center; border:1px solid black"></td>
                    @endif

                   

                    @if(isset($value->sppb_no))
                    <td style="text-align:center; border:1px solid black">{{$value->sppb_no}}</td>
                    <td style="text-align:center; border:1px solid black">{{$value->sppb_kode_sap_bayar}}</td>
                    <td style="text-align:center; border:1px solid black">{{$value->sppb_kode_kbb_bayar}}</td>
                    @elseif(isset($value->sppn_no))
                    <td style="text-align:center; border:1px solid black">{{$value->sppn_no}}</td>
                    <td style="text-align:center; border:1px solid black">{{$value->sppn_kode_sap_terima}}</td>
                    <td style="text-align:center; border:1px solid black">{{$value->sppn_kode_kbb_terima}}</td>
                    @endif

                    
                    @if(isset($value->master_kode_kb))
                      @if(isset($value->master_gl_id) && $value->master_gl_id !== null)
                        <td style="text-align:center; border:1px solid black">{{$value->master_gl_kode}}</td>
                      @else
                        <td style="text-align:center; border:1px solid black">{{$value->master_rekening_kode_sap}}</td>
                      @endif           
                        <td style="text-align:center; border:1px solid black">{{$value->master_kode_kbb}}</td>
                        <td style="text-align:center; border:1px solid black">{{$value->master_cost_center_kode}}</td>
                      
                    @elseif(isset($value))
                      @if(isset($value->master_gl_id) && $value->master_gl_id !== null)
                        <td style="text-align:center; border:1px solid black" > {{$value->master_gl_kode}}</td>
                      @else
                        <td style="text-align:center; border:1px solid black">{{$value->master_rekening_kode_sap  }}</td>
                      @endif    
                      @if(isset($value->master_kode_kbb))
                        <td style="text-align:center; border:1px solid black">{{$value->master_kode_kbb}}</td>
                        @endif
                      <td style="text-align:center; border:1px solid black">{{$value->master_profit_center_kode}}{{$value->master_cost_center_kode}}</td>
                    @endif

                    @if($value->master_vendor_nama !== null)
                    <td style="border:1px solid black">{{$value->master_vendor_nama}}</td>
                    @else
                    <td style="border:1px solid black">{{$value->master_gl_keterangan}}</td>
                    @endif
                    
                    @if(isset($value->sppb_uraian2))
                    <td style="border:1px solid black">{{strip_tags($value->sppb_uraian2)}}</td>
                    @elseif(isset($value->sppn_uraian2))
                    <td style="border:1px solid black">{{strip_tags($value->sppn_uraian2)}}</td>
                    @endif
                    @if(isset($value->sppb_uraian_nominal))
                    <td style="text-align:right; border:1px solid black">Rp. {{number_format($value->sppb_uraian_nominal)}}</td>

                    @elseif(isset($value->sppn_uraian_nominal))

                    <td style="text-align:right; border:1px solid black">Rp. {{number_format($value->sppn_uraian_nominal)}}</td>
                    @endif
                  </tr>
                    @php
                      $totall = isset($value->sppb_total) ? $value->sppb_total : $totall ?? '0';
                      $total2 = isset($value->sppn_jumlah) ? $value->sppn_jumlah : $sppn_jumlah ?? '0';
                    @endphp
                    @if(!isset($data[$key+1]) || ($data[$key]->spp_id !== $data[$key+1]->spp_id))
                  
                    <tr>
                      <td colspan="12" style="border:1px solid black"> <center>Total</center> </td>
                    @if(isset($value->sppn_jumlah))
                      <td style="text-align:center; border:1px solid black">Rp. {{number_format($totall+$value->sppn_jumlah)}}</td>
                    @elseif(isset($value->sppb_total))
                    <td style="text-align:center; border:1px solid black">Rp. {{number_format($total2+$value->sppb_total)}}</td>
                    @endif
                    </tr>
                    @endif
                @endforeach
                </tbody>
              </table>
              </center> 
              @endif 
</body> 
