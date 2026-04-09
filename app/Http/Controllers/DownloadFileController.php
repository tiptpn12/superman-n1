<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class DownloadFileController extends Controller
{
    public function readFile($doc) {
        if (!Session::has('username')) {
            return redirect('/login');
        }

        $decrypt = $this->decrypt($doc);
        $file_path = public_path() . '/' . $decrypt;

        if(file_exists($file_path)) {
            $extension = File::extension($file_path);
            if ($extension == 'pdf') {
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . basename($file_path) . '"');
                header('Content-Length: ' . filesize($file_path));

                return readfile($file_path);
            } else {
                return response()->download($file_path, basename($file_path));
            }
        } else {
            return abort('404');
        }
    }

    protected function decrypt($doc) {
        $encrypt_key = 'perdamain';
		$iv = '1234567891011121';

		return openssl_decrypt(base64_decode(urldecode($doc)), 'AES-128-CTR', $encrypt_key, 0, $iv);
    }
}
