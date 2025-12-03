<?php

namespace App\Http\Controllers;

use App\Services\PembebananService;
use App\Services\PembebananGol2Service;
use Illuminate\Http\Request;

class PembebananController extends Controller
{
    protected $pembebananService;
    protected $pembebananGol2Service;

    public function __construct(PembebananService $pembebananService, PembebananGol2Service $pembebananGol2Service)
    {
        $this->pembebananService = $pembebananService;
        $this->pembebananGol2Service = $pembebananGol2Service;
        
    }

    /**
     * Proses pembebanan penyusutan bulan berjalan.
     */
    public function prosesPembebanan()
    {
        try {
            $this->pembebananService->processAllCurrentMonth();

            return back()->with('success', 'Proses pembebanan berhasil dijalankan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memproses pembebanan: ' . $e->getMessage());
        }
    }

    public function prosesPembebananGol2()
    {
        try {
            $this->pembebananGol2Service->processAllCurrentMonth();

            return back()->with('success', 'Proses pembebanan Gol 2 berhasil dijalankan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memproses pembebanan: ' . $e->getMessage());
        }
    }
}
