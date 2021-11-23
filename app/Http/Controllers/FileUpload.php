<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;

require_once('../vendor/setasign/fpdf/fpdf.php');
require_once('../vendor/setasign/fpdi/src/fpdi.php');

require_once('../vendor/setasign/fpdi/src/autoload.php');
// require('../vendor/setasign/fpdf/fpdf.php');

class FileUpload extends Controller
{
    public function createForm(){
        return view('file-upload');
    }

    public function fileUpload(Request $req){
        $req->validate([
            'file' => 'required|mimes:pdf|max:10000'
        ]);
        $fileModel = new File;

        // Salt QRC
        $rd_string = Str::random(150);
        $item = QrCode::format('png')->generate($rd_string);
        Storage::put('basic/QRC.png', $item);
        
        if($req->file()) {
            $fileName = time().'_'.$req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('basic', 'basis.pdf');
        
            $pdf = new FPDI();

            $pagecount = $pdf->setSourceFile(Storage::path($filePath));
            for ($pageNo = 1; $pageNo <= $pagecount; $pageNo++) {
                // import a page
                $templateId = $pdf->importPage($pageNo);
                // get the size of the imported page
                $size = $pdf->getTemplateSize($templateId);
                $pdf->addPage();
                // use the imported page
                $pdf->useTemplate($templateId);
            }
            // input QRC to lastpage
            $pdf->Image(Storage::path('basic/QRC.png'), 175, 262, 30,30);
            
            // output and store to database
            $dir = "../storage/app/public/";
            $fileModel->name = time().'_'.$req->file->getClientOriginalName();
            $fileModel->file_path = '/storage/' . $pdf->Output($dir.$fileName, 'F');
            $fileModel->save();

            // $digest = hash_file('sha3-512', $dir.$fileName, true);
            $digest = hash_hmac_file('sha3-512', $dir.$fileName, $rd_string, true);
            // return $digest;
            // $getteks = file_get_contents($dir.$fileName, true);
            // $teks = file_put_contents('hashed.txt', $getteks);
            // return $teks;
            readfile($dir.$fileName, true);
        }
    }
}