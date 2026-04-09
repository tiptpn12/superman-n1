<table>
  <tr>
    <td style="text-align:center; font-weight:bold" colspan="11">DATA REKAP PENERIMAAN </td>
  </tr>
  <tr>
  @if($rentang_waktu == 'semua')
          @if(isset($data[0]))
          <td style="text-align:center; font-weight:bold" colspan="11">RENTANG TANGGAL {{$data[0]->tanggal}} - {{$data[count($data)-1]->tanggal}}</td>
          @endif
          @else
          <td style="text-align:center; font-weight:bold" colspan="11">RENTANG TANGGAL {{$rentang_waktu}}</td>
        @endif
  </tr> 
</table>

            <table style="border-style: dashed">
                <thead>
                  <tr >
                    <th style="font-weight:bold; text-align:center; border:1px solid black">No. </th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">Tanggal</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">Kode Cash Flow</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">No. Bukti Kas</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">Uraian</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">No SPPn</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">No Rekg Kas/Bank</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">No Rekg KBB</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">No Rekg SAP</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">Profit/Cost Center</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">Jumlah SPPn</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($data as $key => $value)
                  <tr>
                    <td style="text-align:center; vertical-align:middle; border:1px solid black">{{$key+1}}</td>
                    <td style="text-align:center; vertical-align:middle; border:1px solid black">{{$value->tanggal}}</td>
                    @if(isset($sppnisi[$key]) && $sppnisi[$key]->master_cash_flow_kode !== null)
                    <td style="text-align:center; vertical-align:middle; border:1px solid black">{{$sppnisi[$key]->master_cash_flow_kode}}</td>
                    @else
                    <td style="text-align:center; vertical-align:middle; border:1px solid black"></td>
                    @endif
                    @if(isset($sppn_terima[$key]) && $sppn_terima[$key]!== null)
                    <td style="text-align:center; vertical-align:middle; border:1px solid black">{{$sppn_terima[$key]->sppn_terima_nomor_bukti_kas}}</td>
                    @else
                    <td style="text-align:center; vertical-align:middle; border:1px solid black"></td>
                    @endif
                    <td style="border:1px solid black">{{strip_tags($value->sppn_uraian2)}}</td>
                    <td style="text-align:center; vertical-align:middle; border:1px solid black">{{$value->sppn_no}}</td>
                    @if(isset($sppnisi[$key]))
                    <td style="text-align:center; vertical-align:middle; border:1px solid black">{{$sppnisi[$key]->master_rekening_kode_kbb}}</td>
                    <td style="text-align:center; vertical-align:middle; border:1px solid black">{{$sppnisi[$key]->master_rekening_kode_kbb}}</td>
                    @if(isset($sppnisi[$key]->master_gl_id) && $sppnisi[$key]->master_gl_id !== null)
                      <td style="text-align:center; vertical-align:middle; border:1px solid black" >{{$sppnisi[$key]->master_gl_kode}}</td>
                    @else
                      <td style="text-align:center; vertical-align:middle; border:1px solid black">{{$sppnisi[$key]->master_rekening_kode_sap}}</td>
                    @endif 
                    <td style="text-align:center; vertical-align:middle; border:1px solid black">{{$sppnisi[$key]->master_profit_center_kode}}{{$sppnisi[$key]->master_cost_center_kode}}</td>
                    @else
                    <td style="text-align:center; vertical-align:middle; border:1px solid black"></td>
                    @endif
                    <td style="text-align:right; border:1px solid black">Rp. {{number_format($value->sppn_jumlah)}}</td>
                  </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                  <td colspan="10" style="border:1px solid black"> <center>Total</center> </td>
                  <td  style="text-align:center; border:1px solid black">Rp. {{number_format($totalNominalSppn)}}</td>
                </tr>
                </tfoot>
              </table>


