<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use setasign\Fpdi\Fpdi;


class FileUpload extends Controller
{
    public function createForm(){
        return view('file-upload');
    }

    public function fileUpload(Request $req){
        $req->validate([
        'file' => 'required|mimes:pdf|max:10000'
        ]);
        // require_once('vendor/autoload.php');
        $fileModel = new File;

        if($req->file()) {
            $fileName = time().'_'.$req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');

            $rd_string = Str::random(50); 
            QrCode:: format('png')->generate($rd_string);
            $pdf = new Fpdi('p', 'mm', 'A4');
            $pdf -> AddPage();
            $pdf -> setSourceFile($fileModel);
            $tplId = $pdf->importPage(1);
            $pdf->useTemplate($tplId, 10, 10, 100);

            $pdf -> Output();
            // $fileModel->name = time().'_'.$req->file->getClientOriginalName();
            // $fileModel->file_path = '/storage/' . $filePath;
            // $fileModel->save();

            // return view('home')
            // ->with('success','File has been uploaded.')
            // ->with('file', $fileName);
        }
    }
}
