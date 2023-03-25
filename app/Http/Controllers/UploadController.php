<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use Facade\FlareClient\Stacktrace\File;

class UploadController extends Controller
{
    public function createform()
    {
        return view('upload');
    }

    public function fileUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|pdf'
        ]);
        $fileModel = new Upload;
        if($request->fileUpload){
            
            $fileName = time().'_'.$request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');

            $fileModel->judul = time().'_'.$request->file->getClientOriginalName();
            $fileModel->file_path = '/storage/' . $filePath;
            $fileModel->save();

            return back()
            ->with('success','File has been uploaded.')
            ->with('file', $fileName);
        }
    }
}