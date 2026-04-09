<?php

namespace App\Http\Controllers;

use App\Bank;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
   
    public function index()
    {
        $bank = Bank::All();

        $data = array(
            'bank' => $bank, 
        );

        return view('page.bank.bank', $data);
    }

    public function store(Request $request)
    {
        Bank::create([
            'master_bank_no_rekening' => $request->no_rekening,
            'master_bank_atas_nama' => $request->atas_nama,
            'master_bank_nama' => $request->nama
        ]);
 
        return redirect('/bank');
    }

    public function update(Request $request)
    {
        $bank = Bank::find($request->id);
        $bank->master_bank_no_rekening = $request->no_rekening;
        $bank->master_bank_atas_nama = $request->atas_nama;
        $bank->master_bank_nama = $request->nama;
        $bank->save();

        return redirect('/bank');
    }

    public function destroy($id, $status)
    {
        $bank = Bank::find($id);
        $bank->master_bank_status = $status==1?0:1;
        $bank->save();

        return redirect('/bank');
    }
}
