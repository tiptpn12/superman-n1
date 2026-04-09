<?php

use Illuminate\Database\Seeder;
use App\IsiSppb;
use App\IsiUraianSppb;
class multipleinputseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $isisppb=$request->isi_sppb;
        foreach( $isisppb as $isi =>$value){
            $isisppb = new IsiSppb;
            $isisppb->sppb_id=$request->sppb_id;
            $isisppb->sppb_isi_no=$isi;
            $isisppb->master_rekening_id = $value['rekening'];
            $isisppb->master_cost_center_id = $value['cost_center'];
            $isisppb->master_cash_flow_id = $value['cash_flow'];
            $isisppb->save();
            $request->request->add(['sppb_isi_id'=>$isisppb->sppb_isi_id]);

            foreach($request->uraian_sppb[$isi] as $urai =>$value2){
                $isiuraiansppb = new IsiUraianSppb;
                $isiuraiansppb ->sppb_isi_id = $request->sppb_isi_id;
                $isiuraiansppb ->sppb_uraian_uraian  = $value2['ket'];
                $isiuraiansppb ->sppb_uraian_nominal = $value2['jumlah'];
                $isiuraiansppb ->save();
            }    
        }
    }
}
