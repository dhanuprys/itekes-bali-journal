<?php

namespace App\Http\Controllers\General;

use App\Enums\StorageUploadAction;
use App\Http\Controllers\Controller;
use App\Models\StorageUpload;
use Illuminate\Http\Request;

use App\Services\StorageUploadService;
use Inertia\Inertia;

class StorageUploadController extends Controller
{
    protected $uploadService;

    public function __construct(StorageUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function index()
    {
        $userId = auth()->id();
        $query = StorageUpload::where('user_id', $userId);

        $stats = [
            'count' => $query->clone()->count(),
            'usage' => $query->clone()->sum('file_size'),
            'used_count' => $query->clone()->where('is_used', true)->count(),
        ];

        $files = $query->latest()
            ->paginate(15);

        return Inertia::render('general/storage/Index', [
            'files' => $files,
            'stats' => $stats,
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
        ]);

        $action = StorageUploadAction::tryFrom($request->input('action'));

        if (!$action) {
            return response()->json([
                'message' => 'Invalid action',
            ], 422);
        }

        $validation = $this->getValidatorByAction($action, $request);

        // store file
        $request->validate($validation['rules']);

        $file = $request->file('file');
        $path = $validation['path'];

        $upload = $this->uploadService->upload($file, $path, $action);

        return response()->json([
            'message' => 'File uploaded successfully',
            'id' => $upload->id,
            'path' => $upload->file_path,
        ]);
    }

    private function getValidatorByAction(StorageUploadAction $action, Request $request)
    {
        switch ($action) {
            case StorageUploadAction::RESEARCH_PROPOSAL:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:pdf|max:2048',
                    ],
                    'path' => 'research/proposals',
                ];
            case StorageUploadAction::RESEARCH_FINAL_REPORT:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:pdf|max:2048',
                    ],
                    'path' => 'research/final-reports',
                ];
            case StorageUploadAction::RESEARCH_MANUSCRIPT:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:pdf|max:2048',
                    ],
                    'path' => 'research/manuscripts',
                ];
            case StorageUploadAction::CS_PROPOSAL:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:pdf|max:2048',
                    ],
                    'path' => 'community-service/proposals',
                ];
            case StorageUploadAction::CS_FINAL_REPORT:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:pdf|max:2048',
                    ],
                    'path' => 'community-service/final-reports',
                ];
            case StorageUploadAction::CS_MANUSCRIPT:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:pdf|max:2048',
                    ],
                    'path' => 'community-service/manuscripts',
                ];
            default:
                abort(422, 'Invalid action');
        }
    }
}
