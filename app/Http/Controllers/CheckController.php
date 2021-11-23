<?php

namespace App\Http\Controllers;
// namespace App\Services\Utilities;

use Illuminate\Http\Request;
use Spatie\PdfToImage\Pdf;
use Zxing\QrReader;
use Illuminate\Support\Facades\Storage;
use Imagick;
// include_once('../vendor/khanamiryan/qrcode-detector-decoder/lib/QrReader.php');
class CheckController extends Controller
{
    public function welcome(){
        return view('welcome');
    }

    public function checkfile(Request $check){
        $check->validate([
            'file' => 'required|mimes:pdf'
        ]);
        $filePath = $check->file('file')->storeAs('basic', 'check.pdf');
        $outpath = "..\storage\app\basic\check.png";
        
        
        $pdf = new Pdf(Storage::path($filePath));
        $pdf->setOutputFormat('png');
        $pdf->saveImage($outpath);
        // include_once('lib/QrReader.php');
        $dir = "../storage/app/";
        $QRCodeReader = new QrReader(Storage::path('basic\check.png'));
        $qrcode_text = $QRCodeReader->text();
        $cdigest = hash_hmac_file('sha3-512', $dir.$filePath, $qrcode_text, false);
        echo $cdigest;

    }
}
