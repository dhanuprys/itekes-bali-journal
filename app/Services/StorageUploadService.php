<?php

namespace App\Services;

use App\Enums\StorageUploadAction;
use App\Models\StorageUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
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
        
        // Backend validation for file naming convention
        if ($this->requiresConventionCheck($action)) {
            $parts = explode('_', pathinfo($originalName, PATHINFO_FILENAME));
            if (count($parts) < 4) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'file' => 'Format nama file tidak sesuai. Gunakan format: Nama_Kategori_NIM_JenisDokumen',
                ]);
            }
        }

        $extension = $file->getClientOriginalExtension();
        $filenameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);

        // Override original name for specific actions to ensure organized file names
        if ($action === StorageUploadAction::ETHICS_PAYMENT_PROOF) {
            $userName = Auth::check() ? Str::slug(Auth::user()->name, '_') : 'User';
            $filenameWithoutExt = 'Bukti_Pembayaran_Etik_' . $userName;
        }
        
        // Append a timestamp to the original filename to guarantee uniqueness and handle revisions seamlessly
        $storedName = $filenameWithoutExt . '_' . time() . '.' . $extension;
        
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

    /**
     * Determine if the action requires strict filename convention checking.
     */
    private function requiresConventionCheck(StorageUploadAction $action): bool
    {
        // Don't enforce on profile photos and payment proofs
        return !in_array($action, [
            StorageUploadAction::USER_PROFILE_PHOTO,
            StorageUploadAction::ETHICS_PAYMENT_PROOF,
        ]);
    }
}
