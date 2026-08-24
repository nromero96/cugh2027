<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemporaryFile;
use Illuminate\Validation\ValidationException;

class UploadController extends Controller
{
    public function store(Request $request){

        if ($request->hasFile('document_file')) {
            $request->validate([
                'document_file' => 'file|mimetypes:application/pdf,image/jpeg,image/png|max:10240',
            ]);
            $this->validateRegistrationFileExtension($request->file('document_file'), 'document_file');
        }

        if ($request->hasFile('voucher_file')) {
            $request->validate([
                'voucher_file' => 'file|mimetypes:application/pdf,image/jpeg,image/png|max:10240',
            ]);
            $this->validateRegistrationFileExtension($request->file('voucher_file'), 'voucher_file');
        }

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

    private function validateRegistrationFileExtension($file, string $field): void
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['pdf', 'jpg', 'jpeg', 'png'], true)) {
            throw ValidationException::withMessages([
                $field => 'Only PDF, JPG, JPEG, and PNG files are allowed.',
            ]);
        }
    }
}
