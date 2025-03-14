<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\It\ItController;
use App\Http\Controllers\It\DataUserController;

use App\Http\Controllers\Admin\LpjController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminKlaimController;
use App\Http\Controllers\Admin\LPjBddController;
use App\Http\Controllers\Admin\JurnalBddController;
use App\Http\Controllers\Admin\PengajuanController;
use App\Http\Controllers\Admin\PeralatanController;
use App\Http\Controllers\Admin\PemutusanBddController;
use App\Http\Controllers\Admin\PengajuanBddController;
use App\Http\Controllers\Admin\JurnalPembelianController;
use App\Http\Controllers\Admin\PengajuanMutasiController;
use App\Http\Controllers\Admin\RekeningController;
use App\Http\Controllers\Admin\TempPengajuanDetailController;
use App\Http\Controllers\AdminHead\klaimController as AdminHeadKlaimController;
use App\Http\Controllers\AdminHead\LpjAhController;
use App\Http\Controllers\Akunting\AkuntingController;
use App\Http\Controllers\Akunting\ParamPeralatanController;

use App\Http\Controllers\Finance\FinanceController;
use App\Http\Controllers\Finance\klaimController as FinanceKlaimController;

use App\Http\Controllers\Pengurus\PengurusController;
use App\Http\Controllers\Pengurus\klaimController as PengurusKlaimController;

use App\Http\Controllers\AdminHead\SupervisorController;
use App\Http\Controllers\Akunting\JenisPeralatanController;
use App\Http\Controllers\Akunting\klaimController as AkuntingKlaimController;
use App\Http\Controllers\AtasanIt\AtasanItController;
use App\Http\Controllers\AtasanIt\AtasanItKlaimController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\InventarisGol2Controller;
use App\Http\Controllers\It\ItKlaimController;
use App\Http\Controllers\It\ItLpjController;
use App\Http\Controllers\It\PengajuanItController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\MiscController;
use App\Http\Controllers\PenyusutanController;
use App\Http\Controllers\PenyusutanGol2Controller;
use App\Models\Admin\Peralatan;
use App\Models\Inventaris;

// DaftarBdd::jurnal_bdd();
// Inventaris::penyusutan();

Route::get('/', function () {
    return redirect('/login');
});


Route::get('/releases', function () {
    $title = 'releases';
 return view('releases', compact('title'));
});

Route::get('/dashboard', [IndexController::class, 'redirect_role'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'role:admin'], function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('pengajuan/peralatan', [PengajuanController::class, 'pengajuan_peralatan']);
        Route::get('pengajuan/inventaris', [PengajuanController::class, 'pengajuan_inventaris']);
        Route::post('temp-pengajuan', [TempPengajuanDetailController::class, 'store']);
        Route::get('temp-pengajuan/{id}', [TempPengajuanDetailController::class, 'hapus']);
        Route::resources([
            'data-user' => DataUserController::class,
            'pengajuan' => PengajuanController::class,
            'lpj' => LpjController::class,
            'pengajuan_detail' => PeralatanController::class,
            'pengajuan-bdd' => PengajuanBddController::class,
            'lpj-bdd' => LPjBddController::class,
            'jurnal-pembelian' => JurnalPembelianController::class,
            'jurnal-bdd' => JurnalBddController::class,
            'peralatan' => PeralatanController::class,
            'pengajuan-mutasi' => PengajuanMutasiController::class,
            'pemutusan-bdd' => PemutusanBddController::class,
            'rekening' => RekeningController::class,
        ]);

        Route::get('admin-menu', [AdminController::class, 'menu_admin']);
        Route::get('export-pdf-peralatan/{id}', [PengajuanController::class, 'pdf'])->name('peralatan.pdf');
        Route::get('inventaris', [AdminController::class, 'inventaris']);

        Route::get('penganjuan-bdd-sewa', [PengajuanBddController::class, 'pengajuan_sewa'])->name('pengajuan-bdd-sewa');
        Route::post('penganjuan-bdd-sewa', [PengajuanBddController::class, 'store_sewa'])->name('pengajuan-bdd.store-sewa');
        Route::get('penganjuan-bdd-sewa/edit/{id}', [PengajuanBddController::class, 'pengajuan_sewa_edit'])->name('pengajuan-bdd.edit-sewa');
        Route::put('penganjuan-bdd-sewa/{id}', [PengajuanBddController::class, 'update_sewa'])->name('pengajuan-bdd.update-sewa');

        Route::get('penganjuan-bdd-asuransi', [PengajuanBddController::class, 'pengajuan_asuransi'])->name('pengajuan-bdd-asuransi');
        Route::post('penganjuan-bdd-asuransi', [PengajuanBddController::class, 'store_asuransi'])->name('pengajuan-bdd.store-asuransi');
        Route::get('penganjuan-bdd-asuransi/edit/{id}', [PengajuanBddController::class, 'pengajuan_asuransi_edit'])->name('pengajuan-bdd.edit-asuransi');
        Route::put('penganjuan-bdd-asuransi/{id}', [PengajuanBddController::class, 'update_asuransi'])->name('pengajuan-bdd.update-asuransi');

        Route::get('ajukan_lpj/{id}', [LpjController::class, 'ajukan_lpj'])->name('ajukan_lpj');
        Route::put('lpj/upload-bukti-pengembalian/{id}', [LpjController::class, 'upload_bukti_pengembalian']);
        Route::put('lpj/upload-bukti-kurang/{id}', [LpjController::class, 'upload_bukti_pembelian_kurang']);

        Route::get('daftar-bdd', [PengajuanBddController::class, 'daftar_bdd'])->name('daftar-bdd');
        Route::get('sudah_approve_lpj/{id}', [LpjController::class, 'sudah_approve'])->name('sudah_approve');
        Route::get('buat-lpj-bdd/{id}', [PengajuanBddController::class, 'buat_lpj'])->name('buat-lpj');
        // Route::get('registrasi-peralatan/qrcode/{id}', [RegistrasiPeralatanController::class, 'generate'])->name('generate');
        Route::get('jurnal-kp', [JurnalController::class, 'jurnal_kp']);
        Route::get('jurnal-ap', [JurnalController::class, 'jurnal_ap']);

        Route::get('jurnal-penyusutan-kp', [JurnalController::class, 'jurnal_penyusutan_kp']);
        Route::get('jurnal-penyusutan-ap', [JurnalController::class, 'jurnal_penyusutan_ap']);
        Route::get('jurnal-pembebanan-kp', [JurnalController::class, 'jurnal_pembebanan_kp']);
        Route::get('jurnal-pembebanan-ap', [JurnalController::class, 'jurnal_pembebanan_ap']);

        Route::get('jurnal-penyusutan-kp-gol-2', [JurnalController::class, 'jurnal_penyusutan_kp_gol_2']);
        Route::get('jurnal-penyusutan-ap-gol-2', [JurnalController::class, 'jurnal_penyusutan_ap-gol-2']);
        Route::get('jurnal-pembebanan-kp-gol-2', [JurnalController::class, 'jurnal_pembebanan_kp-gol-2']);
        Route::get('jurnal-pembebanan-ap-gol-2', [JurnalController::class, 'jurnal_pembebanan_ap-gol-2']);

        Route::get('klaim-pembelian', [AdminKlaimController::class, 'index']);
        Route::get('klaim-pembelian/{id}', [AdminKlaimController::class, 'detail']);
        Route::get('klaim/peralatan', [AdminKlaimController::class, 'klaim_peralatan']);
        Route::get('klaim/inventaris', [AdminKlaimController::class, 'klaim_inventaris']);
        Route::post('klaim/pembelian', [AdminKlaimController::class, 'klaim_store']);
    });

    Route::group(['prefix' => 'akunting', 'as' => 'akunting.', 'middleware' => 'role:akunting'], function () {
        Route::get('dashboard', [AkuntingController::class, 'dashboard'])->name('dashboard');
        Route::get('pengajuan-lpj-bdd', [AkuntingController::class, 'pengajuan_lpj_bdd'])->name('pengajuan_lpj_bdd');
        Route::post('approve_bdd/{id}', [AkuntingController::class, 'approve_bdd'])->name('approve_bdd');

        Route::get('approval-peralatan', [App\Http\Controllers\Akunting\ApprovalPeralatanController::class, 'index']);
        Route::get('approval-peralatan/detail/{id}', [App\Http\Controllers\Akunting\ApprovalPeralatanController::class, 'detail']);
        Route::post('approval-peralatan/approve/{id}', [App\Http\Controllers\Akunting\ApprovalPeralatanController::class, 'approve']);
        Route::post('approval-peralatan/tolak/{id}', [App\Http\Controllers\Akunting\ApprovalPeralatanController::class, 'tolak']);

        Route::get('approval-lpj', [App\Http\Controllers\Akunting\ApprovalLpjController::class, 'index']);
        Route::get('approval-lpj/detail/{id}', [App\Http\Controllers\Akunting\ApprovalLpjController::class, 'detail']);
        Route::get('approval-lpj/detail-peralatan/{id}', [App\Http\Controllers\Akunting\ApprovalLpjController::class, 'detail_peralatan']);
        Route::post('approve-lpj/{id}', [App\Http\Controllers\Akunting\ApprovalLpjController::class, 'approve']);
        Route::post('tolak-lpj/{id}', [App\Http\Controllers\Akunting\ApprovalLpjController::class, 'tolak']);

        Route::get('approval-bdd', [App\Http\Controllers\Akunting\ApprovalBddController::class, 'index']);
        Route::get('approval-bdd/detail/{id}', [App\Http\Controllers\Akunting\ApprovalBddController::class, 'detail']);
        Route::post('approval-bdd/approve/{id}', [App\Http\Controllers\Akunting\ApprovalBddController::class, 'approve']);
        Route::post('approval-bdd/tolak/{id}', [App\Http\Controllers\Akunting\ApprovalLpjController::class, 'tolak']);
        Route::post('approve_lpj_bdd/{id}', [App\Http\Controllers\Akunting\ApprovalBddController::class, 'approve_lpj_bdd']);

        Route::get('approval-mutasi', [App\Http\Controllers\Akunting\ApprovalMutasiController::class, 'index']);
        Route::get('approval-mutasi/detail/{id}', [App\Http\Controllers\Akunting\ApprovalMutasiController::class, 'show']);
        Route::post('approval-mutasi/approve/{id}', [App\Http\Controllers\Akunting\ApprovalMutasiController::class, 'approve']);
        Route::post('approval-mutasi/tolak/{id}', [App\Http\Controllers\Akunting\ApprovalMutasiController::class, 'tolak']);

        Route::resources([
            'jenis-peralatan' => JenisPeralatanController::class,
            'param-peralatan' => ParamPeralatanController::class,
        ]);

        Route::get('klaim-pembelian', [AkuntingKlaimController::class, 'index']);
        Route::get('klaim/detail/{id}', [AkuntingKlaimController::class, 'detail']);
        Route::post('klaim/approve/{id}', [AkuntingKlaimController::class, 'approve']);
        Route::post('klaim/tolak/{id}', [AkuntingKlaimController::class, 'tolak']);

        Route::get('peralatan', [AkuntingController::class, 'peralatan']);
        Route::get('inventaris', [AkuntingController::class, 'inventaris']);
        Route::get('jurnal-kp', [JurnalController::class, 'jurnal_kp']);
        Route::get('jurnal-ap', [JurnalController::class, 'jurnal_ap']);
        Route::get('jurnal-penyusutan-kp', [JurnalController::class, 'jurnal_penyusutan_kp']);
        Route::get('jurnal-penyusutan-ap', [JurnalController::class, 'jurnal_penyusutan_ap']);
        Route::get('jurnal-pembebanan-kp', [JurnalController::class, 'jurnal_pembebanan_kp']);
        Route::get('jurnal-pembebanan-ap', [JurnalController::class, 'jurnal_pembebanan_ap']);

        Route::get('jurnal-penyusutan-kp-gol-2', [JurnalController::class, 'jurnal_penyusutan_kp_gol_2']);
        Route::get('jurnal-penyusutan-ap-gol-2', [JurnalController::class, 'jurnal_penyusutan_ap-gol-2']);
        Route::get('jurnal-pembebanan-kp-gol-2', [JurnalController::class, 'jurnal_pembebanan_kp-gol-2']);
        Route::get('jurnal-pembebanan-ap-gol-2', [JurnalController::class, 'jurnal_pembebanan_ap-gol-2']);
    });

    Route::group(['prefix' => 'admin-head', 'as' => 'admin-head.', 'middleware' => 'role:admin-head'], function () {
        Route::get('dashboard', [SupervisorController::class, 'dashboard'])->name('dashboard');

        Route::get('pengajuan', [App\Http\Controllers\AdminHead\PengajuanAhController::class, 'index']);
        Route::post('pengajuan', [App\Http\Controllers\AdminHead\PengajuanAhController::class, 'store']);
        Route::get('pengajuan/detail/{id}', [App\Http\Controllers\AdminHead\PengajuanAhController::class, 'detail']);
        Route::get('pengajuan/peralatan', [App\Http\Controllers\AdminHead\PengajuanAhController::class, 'pengajuan_peralatan']);
        Route::get('pengajuan/inventaris', [App\Http\Controllers\AdminHead\PengajuanAhController::class, 'pengajuan_inventaris']);

        Route::post('temp-pengajuan', [TempPengajuanDetailController::class, 'store']);
        Route::get('temp-pengajuan/{id}', [TempPengajuanDetailController::class, 'hapus']);

        Route::get('pengajuan-peralatan', [App\Http\Controllers\AdminHead\ApprovalPeralatanController::class, 'index']);
        Route::get('pengajuan-peralatan/detail/{id}', [App\Http\Controllers\AdminHead\ApprovalPeralatanController::class, 'detail']);
        Route::post('pengajuan-peralatan/approve/{id}', [App\Http\Controllers\AdminHead\ApprovalPeralatanController::class, 'approve']);
        Route::post('pengajuan-peralatan/tolak/{id}', [App\Http\Controllers\AdminHead\ApprovalPeralatanController::class, 'tolak']);

        Route::get('approval-lpj', [App\Http\Controllers\AdminHead\ApprovalLpjController::class, 'index']);
        Route::get('approval-lpj/detail/{id}', [App\Http\Controllers\AdminHead\ApprovalLpjController::class, 'detail']);
        Route::post('approval-lpj/approve/{id}', [App\Http\Controllers\AdminHead\ApprovalLpjController::class, 'approve']);
        Route::post('approval-lpj/tolak/{id}', [App\Http\Controllers\AdminHead\ApprovalLpjController::class, 'tolak']);

        Route::get('pengajuan-bdd', [App\Http\Controllers\AdminHead\ApprovalBddController::class, 'index']);
        Route::get('pengajuan-bdd/detail/{id}', [App\Http\Controllers\AdminHead\ApprovalBddController::class, 'detail']);
        Route::post('pengajuan-bdd/approve/{id}', [App\Http\Controllers\AdminHead\ApprovalBddController::class, 'approve']);
        Route::post('pengajuan-bdd/tolak/{id}', [App\Http\Controllers\AdminHead\ApprovalBddController::class, 'tolak']);

        Route::get('approval-mutasi', [App\Http\Controllers\AdminHead\ApprovalMutasiController::class, 'index']);
        Route::get('approval-mutasi/detail/{id}', [App\Http\Controllers\AdminHead\ApprovalMutasiController::class, 'show'])->name('mutasi.details');
        Route::post('approval-mutasi/approve/{id}', [App\Http\Controllers\AdminHead\ApprovalMutasiController::class, 'approve']);
        Route::post('approval-mutasi/tolak/{id}', [App\Http\Controllers\AdminHead\ApprovalBddController::class, 'tolak']);

        Route::resources([
            'lpj' => LpjAhController::class,
        ]);
        Route::get('lpj', [LpjAhController::class, 'index']);
        Route::get('lpj/{id}', [LpjAhController::class, 'detail']);
        Route::get('ajukan_lpj/{id}', [LpjAhController::class, 'ajukan_lpj']);
        Route::put('lpj/upload-bukti-pengembalian/{id}', [LpjController::class, 'upload_bukti_pengembalian']);

        Route::get('klaim-pembelian', [AdminHeadKlaimController::class, 'index']);
        Route::get('klaim/detail/{id}', [AdminHeadKlaimController::class, 'detail']);
        Route::post('klaim/approve/{id}', [AdminHeadKlaimController::class, 'approve']);
        Route::post('klaim/tolak/{id}', [AdminHeadKlaimController::class, 'tolak']);

        Route::get('peralatan', [SupervisorController::class, 'peralatan']);
        Route::get('jurnal-kp', [JurnalController::class, 'jurnal_kp']);
        Route::get('jurnal-ap', [JurnalController::class, 'jurnal_ap']);

        Route::get('jurnal-penyusutan-kp', [JurnalController::class, 'jurnal_penyusutan_kp']);
        Route::get('jurnal-penyusutan-ap', [JurnalController::class, 'jurnal_penyusutan_ap']);
        Route::get('jurnal-pembebanan-kp', [JurnalController::class, 'jurnal_pembebanan_kp']);
        Route::get('jurnal-pembebanan-ap', [JurnalController::class, 'jurnal_pembebanan_ap']);

        Route::get('export-excel', [PeralatanController::class, 'excel'])->name('peralatan.export');
    });

    Route::group(['prefix' => 'finance', 'as' => 'finance.', 'middleware' => 'role:finance'], function () {
        Route::get('dashboard', [FinanceController::class, 'dashboard'])->name('dashboard');
        Route::get('pengajuan-bdd', [FinanceController::class, 'pengajuan_bdd'])->name('pengajuan_bdd');
        Route::get('pengajuan-lpj-bdd', [FinanceController::class, 'pengajuan_lpj_bdd'])->name('pengajuan_lpj_bdd');
        Route::post('approve_bdd/{id}', [FinanceController::class, 'approve_bdd'])->name('approve_bdd');
        Route::post('approve_lpj_bdd/{id}', [FinanceController::class, 'approve_lpj_bdd'])->name('approve_lpj_bdd');
        Route::get('lpj-bdd', [FinanceController::class, 'lpj_bdd'])->name('lpj_bdd');
        Route::get('detail-bdd/{id}', [FinanceController::class, 'detail_bdd']);
        Route::post('tolak_bdd/{id}', [FinanceController::class, 'tolak_bdd']);

        Route::get('approval-peralatan', [App\Http\Controllers\Finance\ApprovalPeralatanController::class, 'index']);
        Route::get('approval-peralatan/detail/{id}', [App\Http\Controllers\Finance\ApprovalPeralatanController::class, 'detail']);
        Route::get('approval-peralatan/detail-cetak/{id}', [App\Http\Controllers\Finance\ApprovalPeralatanController::class, 'detail_cetak']);
        Route::post('approval-peralatan/approve/{id}', [App\Http\Controllers\Finance\ApprovalPeralatanController::class, 'approve']);
        Route::post('approval-peralatan/tolak/{id}', [App\Http\Controllers\Finance\ApprovalPeralatanController::class, 'tolak']);
        Route::get('approval-peralatan/approve', [App\Http\Controllers\Finance\ApprovalPeralatanController::class, 'approve_peralatan']);

        Route::get('approval-lpj', [App\Http\Controllers\Finance\ApprovalLpjController::class, 'index']);
        Route::get('approval-lpj/detail/{id}', [App\Http\Controllers\Finance\ApprovalLpjController::class, 'detail']);
        Route::get('approval-lpj/detail-cetak/{id}', [App\Http\Controllers\Finance\ApprovalLpjController::class, 'detail_cetak']);
        Route::post('approval-lpj/approve/{id}', [App\Http\Controllers\Finance\ApprovalLpjController::class, 'approve']);
        Route::post('approval-lpj/tolak/{id}', [App\Http\Controllers\Finance\ApprovalLpjController::class, 'tolak']);
        Route::get('approval-lpj/approve', [App\Http\Controllers\Finance\ApprovalLpjController::class, 'approve_lpj']);
        Route::post('approval-lpj/approve-inventaris/{id}', [App\Http\Controllers\Finance\ApprovalLpjController::class, 'approve_inventaris']);

        Route::get('approval-bdd', [App\Http\Controllers\Finance\ApprovalBddController::class, 'index']);
        Route::get('approval-bdd/detail/{id}', [App\Http\Controllers\Finance\ApprovalBddController::class, 'detail']);
        Route::post('approval-bdd/approve/{id}', [App\Http\Controllers\Finance\ApprovalBddController::class, 'approve']);
        Route::post('approval-bdd/tolak/{id}', [App\Http\Controllers\Finance\ApprovalBddController::class, 'tolak']);
        Route::get('approval-bdd/approve', [App\Http\Controllers\Finance\ApprovalBddController::class, 'approve_bdd']);

        Route::get('klaim-pembelian', [FinanceKlaimController::class, 'index']);
        Route::get('klaim/detail/{id}', [FinanceKlaimController::class, 'detail']);
        Route::post('klaim/approve/{id}', [FinanceKlaimController::class, 'approve']);
        Route::post('klaim/tolak/{id}', [FinanceKlaimController::class, 'tolak']);
        Route::get('approval-klaim/detail-cetak/{id}', [FinanceKlaimController::class, 'detail_cetak']);
        Route::get('approval-klaim/approve', [FinanceKlaimController::class, 'approve_klaim']);
    });

    Route::group(['prefix' => 'pengurus', 'as' => 'pengurus.', 'middleware' => 'role:pengurus'], function () {
        Route::get('dashboard', [PengurusController::class, 'dashboard'])->name('dashboard');
        Route::post('approve_bdd/{id}', [PengurusController::class, 'approve_bdd'])->name('approve_bdd');
        Route::get('pengajuan-bdd', [PengurusController::class, 'pengajuan_bdd'])->name('pengajuan_bdd');
        Route::get('detail-bdd/{id}', [PengurusController::class, 'detail_bdd']);

        Route::get('approval-peralatan', [App\Http\Controllers\Pengurus\ApprovalPeralatanController::class, 'index']);
        Route::get('approval-peralatan/detail/{id}', [App\Http\Controllers\Pengurus\ApprovalPeralatanController::class, 'detail']);
        Route::post('approval-peralatan/approve/{id}', [App\Http\Controllers\Pengurus\ApprovalPeralatanController::class, 'approve']);
        Route::post('approval-peralatan/tolak/{id}', [App\Http\Controllers\Pengurus\ApprovalPeralatanController::class, 'tolak']);

        Route::get('approval-lpj', [App\Http\Controllers\Pengurus\ApprovalLpjController::class, 'index']);
        Route::get('approval-lpj/detail/{id}', [App\Http\Controllers\Pengurus\ApprovalLpjController::class, 'detail']);
        Route::post('approval-lpj/approve/{id}', [App\Http\Controllers\Pengurus\ApprovalLpjController::class, 'approve']);
        Route::post('approval-lpj/tolak/{id}', [App\Http\Controllers\Pengurus\ApprovalLpjController::class, 'tolak']);

        Route::get('approval-bdd', [App\Http\Controllers\Pengurus\ApprovalBddController::class, 'index']);
        Route::get('approval-bdd/detail/{id}', [App\Http\Controllers\Pengurus\ApprovalBddController::class, 'detail']);
        Route::post('approval-bdd/approve/{id}', [App\Http\Controllers\Pengurus\ApprovalBddController::class, 'approve']);
        Route::post('approval-bdd/tolak/{id}', [App\Http\Controllers\Pengurus\ApprovalBddController::class, 'tolak']);

        Route::get('klaim-pembelian', [PengurusKlaimController::class, 'index']);
        Route::get('klaim/detail/{id}', [PengurusKlaimController::class, 'detail']);
        Route::post('klaim/approve/{id}', [PengurusKlaimController::class, 'approve']);
        Route::post('klaim/tolak/{id}', [PengurusKlaimController::class, 'tolak']);
    });

    Route::group(['prefix' => 'it', 'as' => 'it.', 'middleware' => 'role:it'], function () {
        Route::get('dashboard', [ItController::class, 'dashboard'])->name('dashboard');
        Route::get('pengajuan/peralatan-it', [App\Http\Controllers\It\PengajuanItController::class, 'pengajuan_peralatan']);
        Route::get('pengajuan/inventaris-it', [App\Http\Controllers\It\PengajuanItController::class, 'pengajuan_inventaris']);
        Route::post('temp-pengajuan', [TempPengajuanDetailController::class, 'store']);
        Route::get('temp-pengajuan/{id}', [TempPengajuanDetailController::class, 'hapus']);
        Route::resources([
            'data-user' => DataUserController::class,
            'pengajuan' => PengajuanItController::class,
            'lpj' => ItLpjController::class,
        ]);

        Route::get('ajukan_lpj/{id}', [ItLpjController::class, 'ajukan_lpj'])->name('ajukan_lpj');
        Route::put('lpj/upload-bukti-pengembalian/{id}', [ItLpjController::class, 'upload_bukti_pengembalian']);
        Route::put('lpj/upload-bukti-kurang/{id}', [ItLpjController::class, 'upload_bukti_pembelian_kurang']);

        Route::get('pengajuan-peralatan', [ItController::class, 'pengajuan_peralatan'])->name('pengajuan_peralatan');
        Route::get('pengajuan-bdd', [ItController::class, 'pengajuan_bdd'])->name('pengajuan_bdd');
        Route::post('approve/{id}', [ItController::class, 'approve'])->name('approve');
        Route::post('approve-bdd/{id}', [ItController::class, 'approve_bdd'])->name('approve_bdd');
        Route::post('get-branches', [DataUserController::class, 'getBranches']);
        Route::post('tolak/{id}', [ItController::class, 'tolak'])->name('tolak');
        Route::get('pengajuan-detail/{id}', [ItController::class, 'pengajuan_detail'])->name('pengajuan-detail');

        Route::get('klaim', [ItKlaimController::class, 'index']);
        Route::get('klaim/{id}', [ItKlaimController::class, 'detail']);
        Route::get('peralatan/klaim', [ItKlaimController::class, 'klaim_peralatan']);
        Route::get('klaim/inventaris', [ItKlaimController::class, 'klaim_inventaris']);
        Route::post('klaim', [ItKlaimController::class, 'klaim_store']);
    });

    Route::group(['prefix' => 'atasan-akunting', 'as' => 'atasan-akunting.', 'middleware' => 'role:atasan-akunting'], function () {
        Route::get('dashboard', [AkuntingController::class, 'dashboard'])->name('dashboard');

        Route::get('pengajuan', [App\Http\Controllers\AtasanAkunting\PengajuanAtasanAkuntingController::class, 'index']);
        Route::post('pengajuan', [App\Http\Controllers\AtasanAkunting\PengajuanAtasanAkuntingController::class, 'store']);
        Route::get('pengajuan/detail/{id}', [App\Http\Controllers\AtasanAkunting\PengajuanAtasanAkuntingController::class, 'detail']);
        Route::get('pengajuan/peralatan', [App\Http\Controllers\AtasanAkunting\PengajuanAtasanAkuntingController::class, 'pengajuan_peralatan']);
        Route::get('pengajuan/inventaris', [App\Http\Controllers\AtasanAkunting\PengajuanAtasanAkuntingController::class, 'pengajuan_inventaris']);

        Route::post('temp-pengajuan', [TempPengajuanDetailController::class, 'store']);
        Route::get('temp-pengajuan/{id}', [TempPengajuanDetailController::class, 'hapus']);

        Route::get('approval-peralatan', [App\Http\Controllers\AtasanAkunting\ApprovalController::class, 'index']);
        Route::get('approval-peralatan/detail/{id}', [App\Http\Controllers\AtasanAkunting\ApprovalController::class, 'detail']);
        Route::post('approval-peralatan/approve/{id}', [App\Http\Controllers\AtasanAkunting\ApprovalController::class, 'approve']);
        Route::post('approval-peralatan/tolak/{id}', [App\Http\Controllers\AtasanAkunting\ApprovalController::class, 'tolak']);
        Route::get('approval-peralatan/approve', [App\Http\Controllers\AtasanAkunting\ApprovalController::class, 'approve_peralatan']);

        Route::get('approval-lpj', [App\Http\Controllers\AtasanAkunting\ApprovalLpjController::class, 'index']);
        Route::get('approval-lpj/detail/{id}', [App\Http\Controllers\AtasanAkunting\ApprovalLpjController::class, 'detail']);
        Route::post('approve-lpj/{id}', [App\Http\Controllers\AtasanAkunting\ApprovalLpjController::class, 'approve']);
        Route::post('approval-lpj/tolak/{id}', [App\Http\Controllers\AtasanAkunting\ApprovalLpjController::class, 'tolak']);
    });

    Route::group(['prefix' => 'atasan-it', 'as' => 'atasan-it.', 'middleware' => 'role:atasan-it'], function () {
        Route::get('dashboard', [AtasanItController::class, 'dashboard'])->name('dashboard');
        Route::get('approval-peralatan', [App\Http\Controllers\AtasanIt\ApprovalController::class, 'index']);
        Route::get('approval-peralatan/detail/{id}', [App\Http\Controllers\AtasanIt\ApprovalController::class, 'detail']);
        Route::post('approval-peralatan/approve/{id}', [App\Http\Controllers\AtasanIt\ApprovalController::class, 'approve']);
        Route::post('approval-peralatan/tolak/{id}', [App\Http\Controllers\AtasanIt\ApprovalController::class, 'tolak']);
        Route::get('approval-peralatan/approve', [App\Http\Controllers\AtasanIt\ApprovalController::class, 'approve_peralatan']);

        Route::get('approval-lpj', [App\Http\Controllers\AtasanIt\ApprovalLpjController::class, 'index']);
        Route::get('approval-lpj/detail/{id}', [App\Http\Controllers\AtasanIt\ApprovalLpjController::class, 'detail']);
        Route::post('approve-lpj/{id}', [App\Http\Controllers\AtasanIt\ApprovalLpjController::class, 'approve']);
        Route::post('approval-lpj/tolak/{id}', [App\Http\Controllers\AtasanIt\ApprovalLpjController::class, 'tolak']);

        Route::get('klaim-pembelian', [AtasanItKlaimController::class, 'index']);
        Route::get('klaim/detail/{id}', [AtasanItKlaimController::class, 'detail']);
        Route::post('klaim/approve/{id}', [AtasanItKlaimController::class, 'approve']);
        Route::post('klaim/tolak/{id}', [AtasanItKlaimController::class, 'tolak']);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/update-password', [ProfileController::class, 'updatePasswordPage']);
    Route::post('/update-password', [ProfileController::class, 'updatePassword']);
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/export-excel-peralatan', [PeralatanController::class, 'excel'])->name('peralatan.export');
    Route::post('/export-excel-inventaris', [MiscController::class, 'inventarisExcel']);
    Route::post('/export-excel/jurnal-kp', [MiscController::class, 'jurnalKPExcel']);
    Route::post('/export-excel/jurnal-ap', [MiscController::class, 'jurnalAPExcel']);

    Route::get('jurnal-kp', [JurnalController::class, 'jurnal_kp']);
    Route::get('jurnal-ap', [JurnalController::class, 'jurnal_ap']);
    
    Route::get('jurnal-penyusutan-kp', [JurnalController::class, 'jurnal_penyusutan_kp']);
    Route::get('jurnal-penyusutan-ap', [JurnalController::class, 'jurnal_penyusutan_ap']);
    Route::get('jurnal-pembebanan-kp', [JurnalController::class, 'jurnal_pembebanan_kp']);
    Route::get('jurnal-pembebanan-ap', [JurnalController::class, 'jurnal_pembebanan_ap']);

    Route::get('jurnal-penyusutan-kp-gol-2', [JurnalController::class, 'jurnal_penyusutan_kp_gol_2']);
    Route::get('jurnal-penyusutan-ap-gol-2', [JurnalController::class, 'jurnal_penyusutan_ap_gol_2']);
    Route::get('jurnal-pembebanan-kp-gol-2', [JurnalController::class, 'jurnal_pembebanan_kp_gol_2']);
    Route::get('jurnal-pembebanan-ap-gol-2', [JurnalController::class, 'jurnal_pembebanan_ap_gol_2']);

    Route::get('/penyusutan', [PenyusutanController::class, 'index'])->name('penyusutan.index');
    Route::post('/penyusutan', [PenyusutanController::class, 'jalankanPenyusutan'])->name('jalankanPenyusutan');
    Route::post('/jurnal-penyusutan', [PenyusutanController::class, 'jalankanJurnalPenyusutan'])->name('jalankanJurnalPenyusutan');
    Route::post('/jurnal-pembebanan', [PenyusutanController::class, 'jalankanJurnalPembebanan'])->name('jalankanJurnalPembebanan');

    Route::get('/penyusutan-gol-2', [PenyusutanGol2Controller::class, 'index'])->name('penyusutanGol2.index');
    Route::post('/penyusutan-gol-2', [PenyusutanGol2Controller::class, 'jalankanPenyusutanGol2'])->name('jalankanPenyusutanGol2');
    Route::post('/jurnal-penyusutan-gol-2', [PenyusutanGol2Controller::class, 'jalankanJurnalPenyusutanGol2'])->name('jalankanJurnalPenyusutanGol2');

    Route::get('/get/area-by-branch/{id}', [MiscController::class, 'areaByBranch']);
    Route::post('/get-branches', [DataUserController::class, 'getBranches']);
    Route::get('/admin/inventaris', [InventarisController::class, 'index'])->name('inventaris.admin');
    Route::get('/akunting/inventaris', [InventarisController::class, 'index'])->name('inventaris.akunting');
    Route::get('/admin-head/inventaris', [InventarisController::class, 'index'])->name('inventaris.adminHead');

    Route::get('/admin/inventaris-gol-2', [InventarisGol2Controller::class, 'index'])->name('inventaris_gol_2.admin');
    Route::get('/akunting/inventaris-gol-2', [InventarisGol2Controller::class, 'index'])->name('inventaris_gol_2.akunting');
    Route::get('/admin-head/inventaris-gol-2', [InventarisGol2Controller::class, 'index'])->name('inventaris_gol_2.adminHead');
    Route::get('/inventaris/data', [InventarisGol2Controller::class, 'getData'])->name('inventaris_gol_2.data');
    Route::get('/fetch-data-gol-2', [InventarisGol2Controller::class, 'fetchData'])->name('inventaris_gol_2.fetchData');

    Route::get('/fetch-data', [InventarisController::class, 'fetchData'])->name('inventaris.fetchData');
    Route::get('/inventaris/data', [InventarisController::class, 'getData'])->name('inventaris.data');
    Route::get('/get-inventaris/{kodeBranch}', [PengajuanMutasiController::class, 'getInventaris']);
});


Route::get('jenis-peralatan-dropdown/{id}', [JenisPeralatanController::class, 'dropdown'])->name('jenis-peralatan-dropdown');

require __DIR__ . '/auth.php';
