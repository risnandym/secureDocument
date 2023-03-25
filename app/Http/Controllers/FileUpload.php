<?php

namespace App\Http\Controllers;

use App\Models\digest;
use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;

// require_once('../vendor/setasign/fpdf/fpdf.php');
// require_once('../vendor/setasign/fpdi/src/fpdi.php');

// require_once('../vendor/setasign/fpdi/src/autoload.php');
// require('../vendor/setasign/fpdf/fpdf.php');

class FileUpload extends Controller
{
    public function createForm(){
        return view('file-upload');
    }

    public function fileUpload(Request $req){
        // validate file type
        $req->validate([
            'file' => 'required|mimes:pdf|max:10000'
        ]);
        $fileModel = new File;

        // Salt encoded to QRC
        $rd_string = Str::random(150);
        $item = QrCode::format('png')->generate($rd_string);
        Storage::put('basic/QRC.png', $item);
        
        // upload new file
        if($req->file()) {
            $fileName = time().'_'.$req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('basic', 'basis.pdf');
            
            // get origin file as template
            $pdf = new FPDI();
            // create pdf multipage
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
            $anggeur = "tak ada yang berubah";
            $dir = "../storage/app/public/";
            $fileModel->name = time().'_'.$req->file->getClientOriginalName();
            $fileModel->file_path = '/storage/' . $pdf->Output($dir.$fileName, 'F');
            $fileModel->save();

            // $message = new digest;
            // hmac = origin content + additional key (salted key)
            $mess = new digest;
            $digy = hash_hmac_file('sha3-512', $dir.$fileName, $rd_string, false);
            
            $mess->message = $digy;
            $mess->save();
            // echo $label.$rd_string." <br> ";
            // echo $rd_string." <br> ";
            // echo $digy;
            return redirect('home');
        }
    }
    public function deleteFile($id) {
        $file = File::findWithoutFail($id);
        if (!$file) {
            echo 'File Tidak Ada';
        }
        $file->delete();
        return 'File berhasil dihapus';
    }
}