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
            ->paginate(10);

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
        // default max file size is 4MB
        switch ($action) {
            case StorageUploadAction::RESEARCH_PROPOSAL:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:doc,docx|max:4096',
                    ],
                    'path' => 'research/proposals',
                ];
            case StorageUploadAction::RESEARCH_PROGRESS_REPORT:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:doc,docx|max:4096',
                    ],
                    'path' => 'research/progress-reports',
                ];
            case StorageUploadAction::RESEARCH_FINAL_REPORT:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:doc,docx|max:4096',
                    ],
                    'path' => 'research/final-reports',
                ];
            case StorageUploadAction::RESEARCH_MANUSCRIPT:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:doc,docx|max:4096',
                    ],
                    'path' => 'research/manuscripts',
                ];
            case StorageUploadAction::RESEARCH_SUPPLEMENTARY:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:doc,docx|max:4096',
                    ],
                    'path' => 'research/supplementary',
                ];
            case StorageUploadAction::CS_PROPOSAL:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:doc,docx|max:4096',
                    ],
                    'path' => 'community-service/proposals',
                ];
            case StorageUploadAction::CS_PROGRESS_REPORT:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:doc,docx|max:4096',
                    ],
                    'path' => 'community-service/progress-reports',
                ];
            case StorageUploadAction::CS_FINAL_REPORT:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:doc,docx|max:4096',
                    ],
                    'path' => 'community-service/final-reports',
                ];
            case StorageUploadAction::CS_MANUSCRIPT:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:doc,docx|max:4096',
                    ],
                    'path' => 'community-service/manuscripts',
                ];
            case StorageUploadAction::CS_SUPPLEMENTARY:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:doc,docx|max:4096',
                    ],
                    'path' => 'community-service/supplementary',
                ];
            case StorageUploadAction::ETHICS_PROPOSAL:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:doc,docx|max:4096',
                    ],
                    'path' => 'ethics/proposals',
                ];
            case StorageUploadAction::ETHICS_OUTPUT:
                return [
                    'rules' => [
                        'file' => 'required|file|mimes:doc,docx|max:4096',
                    ],
                    'path' => 'ethics/outputs',
                ];
            case StorageUploadAction::ETHICS_PAYMENT_PROOF:
                return [
                    'rules' => [
                        'file' => 'required|file|image|mimes:jpg,jpeg,png,webp|max:4096',
                    ],
                    'path' => 'ethics/payment-proofs',
                ];
            case StorageUploadAction::USER_PROFILE_PHOTO:
                return [
                    'rules' => [
                        'file' => 'required|file|image|max:3072',
                    ],
                    'path' => 'users/profile-photos',
                ];
            default:
                abort(422, 'Invalid action');
        }
    }
}
