<?php

namespace App\Http\Controllers;
// namespace App\Services\Utilities;

use Illuminate\Http\Request;
use Spatie\PdfToImage\Pdf;
use App\Models\digest;
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
        // validate file type
        $check->validate([
            'file' => 'required|mimes:pdf'
        ]);

        // set location source
        $filePath = $check->file('file')->storeAs('basic', 'check.pdf');
        $outpath = "..\storage\app\basic\check.png";
         $dir = "../storage/app/";

        // pdf into image 
        $pdf = new Pdf(Storage::path($filePath));
        $pagecount = $pdf->getNumberOfPages();

        $pdf->setPage($pagecount);
        $pdf->setOutputFormat('png');
        $pdf->saveImage($outpath);
        
        // decoder QRC 
        $QRCodeReader = new QrReader(Storage::path('basic\check.png'));
        $qrcode_text = $QRCodeReader->text();
        echo $qrcode_text;
        // rehash for verify
        $cdigest = hash_hmac_file('sha3-512', $dir.$filePath, $qrcode_text, false);
        // echo $cdigest;
        $dig = digest::where('message',$cdigest)->first();
        if($dig){
            return redirect('result');
        }
        else{
            echo 'dokumen palsu';
        }
    }
}
