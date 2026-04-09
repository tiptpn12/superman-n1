<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SppbIsi;
use App\SppnIsi;
use App\SppbBayar;
use App\SppnTerima;
use App\spp;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use DB;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\export_csv;


class laporanCSVController extends Controller
{
    function __construct()
    {
        
    }

    public function isi_csv()
    {
        $csvstr = 'confirmedlist.csv';
		return Excel::download(new export_csv, str_replace('"','',$csvstr));
    }
}
