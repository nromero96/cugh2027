<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemporaryFile;

class UploadController extends Controller
{
    public function store(Request $request){

        $documentFields = ['file_1', 'file_2', 'file_3', 'file_4', 'file_5', 'file_6','document_file','voucher_file','poster'];
        $uploadedFolder = '';

        foreach ($documentFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $originalFilename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
        
                // Remover la extensión del nombre de archivo
                $filenameWithoutExtension = pathinfo($originalFilename, PATHINFO_FILENAME);

                // Generar el nuevo nombre de archivo
                $newFilename = $filenameWithoutExtension . '-' . uniqid() . '.' . $extension;

                $folder = uniqid() . '-' . now()->timestamp;
                $file->storeAs('public/uploads/tmp/' . $folder, $newFilename);

                TemporaryFile::create([
                    'folder' => $folder,
                    'filename' => $newFilename,
                ]);

                $uploadedFolder = $folder;
                break;
            }
        }
        return $uploadedFolder;
    }
}
