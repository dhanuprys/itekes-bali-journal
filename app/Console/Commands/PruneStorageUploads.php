<?php

namespace App\Console\Commands;

use App\Models\StorageUpload;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneStorageUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:prune-uploads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune unused storage uploads older than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Pruning unused uploads...');

        $uploads = StorageUpload::where('is_used', false)
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        foreach ($uploads as $upload) {
            if (Storage::disk($upload->disk)->exists($upload->file_path)) {
                Storage::disk($upload->disk)->delete($upload->file_path);
            }
            $upload->delete();
            $this->info("Deleted: {$upload->file_path}");
        }

        $this->info('Pruning complete.');
    }
}
