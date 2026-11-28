<?php

namespace App\Http\Controllers;

use App\Services\PembebananService;
use App\Services\PembebananGol2Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $lockAcquired = DB::select("SELECT GET_LOCK('proses_pembebanan_lock', 2) as locked")[0]->locked ?? 0;
        if (!$lockAcquired) {
            return back()->with('error', 'Proses pembebanan sedang berjalan. Silakan tunggu.');
        }

        try {
            $this->pembebananService->processAllCurrentMonth();

            return back()->with('success', 'Proses pembebanan berhasil dijalankan.');
        } catch (\Throwable $e) {
            Log::error('Error prosesPembebanan: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        } finally {
            DB::statement("SELECT RELEASE_LOCK('proses_pembebanan_lock')");
        }
    }

    public function prosesPembebananGol2()
    {
        $lockAcquired = DB::select("SELECT GET_LOCK('proses_pembebanan_gol2_lock', 2) as locked")[0]->locked ?? 0;
        if (!$lockAcquired) {
            return back()->with('error', 'Proses pembebanan Gol 2 sedang berjalan. Silakan tunggu.');
        }

        try {
            $this->pembebananGol2Service->processAllCurrentMonth();

            return back()->with('success', 'Proses pembebanan Gol 2 berhasil dijalankan.');
        } catch (\Throwable $e) {
            Log::error('Error prosesPembebananGol2: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        } finally {
            DB::statement("SELECT RELEASE_LOCK('proses_pembebanan_gol2_lock')");
        }
    }
}
