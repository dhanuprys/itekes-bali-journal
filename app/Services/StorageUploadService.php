<?php

namespace App\Services;

use App\Enums\StorageUploadAction;
use App\Models\StorageUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $storedName = (string) Str::uuid() . '.' . $extension;
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        // Store the file
        $fullPath = $file->storeAs($path, $storedName, $disk);

        // create record
        return StorageUpload::create([
            'user_id' => Auth::id(),
            'action' => $action->value,
            'file_path' => $fullPath,
            'file_name' => $originalName,
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
     * @param  string|null  $tag
     * @return void
     */
    public function markAsUsed(string $path, ?string $tag = null): void
    {
        $upload = StorageUpload::where('file_path', $path)->first();

        if ($upload) {
            $data = ['is_used' => true];
            if ($tag) {
                $data['tag'] = $tag;
            }
            $upload->update($data);
        }
    }
}
