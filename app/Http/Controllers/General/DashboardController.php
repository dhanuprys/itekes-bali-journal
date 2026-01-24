<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('general/dashboard/Index');
    }

    public function changelog()
    {
        $changelog = [
            [
                'version' => 'v1.1.0',
                'date' => '2024-01-24',
                'changes' => [
                    ['type' => 'new', 'message' => 'Menambahkan Halaman Notifikasi'],
                    ['type' => 'new', 'message' => 'Menambahkan Halaman Changelog'],
                    ['type' => 'fix', 'message' => 'Memperbaiki layout pada halaman mobile'],
                    ['type' => 'change', 'message' => 'Penyempurnaan tampilan Dashboard'],
                ]
            ],
            [
                'version' => 'v1.0.0',
                'date' => '2024-01-01',
                'changes' => [
                    ['type' => 'new', 'message' => 'Rilis awal aplikasi'],
                    ['type' => 'new', 'message' => 'Fitur Manajemen Pengguna'],
                    ['type' => 'new', 'message' => 'Fitur Manajemen Role & Permission'],
                ]
            ],
        ];

        return Inertia::render('general/dashboard/Changelog', [
            'changelog' => $changelog
        ]);
    }
}
