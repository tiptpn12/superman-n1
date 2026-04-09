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
                  <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Status</th>
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
                @foreach($data as $key => $value)
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
                  <td style="border:1px solid black">{{$posisi_dinamis[$key]->master_hak_akses_nama}}</td>
                  <td style="text-align:right; border:1px solid black">Rp. {{number_format($value->sppn_uraian_nominal)}}</td>
                </tr>
                
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                  <td colspan="13" style="border:1px solid black"> <center>Total</center> </td>
                  <td  style="text-align:center; border:1px solid black">Rp. {{number_format($totalNominalSppn)}}</td>
                </tr>
                </tfoot>
              </table>


