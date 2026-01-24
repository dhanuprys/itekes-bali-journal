<?php

namespace App\Services;

use App\Enums\StorageUploadAction;
use App\Models\StorageUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StorageUploadService
{
    /**
     * Upload a file and log it to the database.
     *
     * @param  UploadedFile  $file
     * @param  string  $path
     * @param  StorageUploadAction  $action
     * @param  string  $disk
     * @return StorageUpload
     */
    public function upload(UploadedFile $file, string $path, StorageUploadAction $action, string $disk = 'public'): StorageUpload
    {
        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        // Store the file
        $fullPath = $file->storeAs($path, $fileName, $disk);

        // create record
        return StorageUpload::create([
            'user_id' => Auth::id(),
            'action' => $action->value,
            'file_path' => $fullPath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'disk' => $disk,
            'is_used' => false,
        ]);
    }

    /**
     * Mark a file as used by its path.
     *
     * @param  string  $path
     * @return void
     */
    public function markAsUsed(string $path): void
    {
        $upload = StorageUpload::where('file_path', $path)->first();

        if ($upload) {
            $upload->update(['is_used' => true]);
        }
    }
}
