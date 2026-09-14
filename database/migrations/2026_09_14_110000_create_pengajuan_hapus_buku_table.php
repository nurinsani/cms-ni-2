<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengajuan_hapus_buku', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan', 100);
            $table->bigInteger('user_id');
            $table->string('kode_branch', 50);
            $table->string('jenis_aktiva', 100);
            $table->text('alasan');
            $table->string('surat_moa')->nullable();
            $table->string('bulan_hapus_buku', 50);
            $table->enum('status_approve', ['admin-head', 'akunting', 'selesai', 'ditolak'])->default('admin-head');
            $table->bigInteger('atasan_1')->nullable();
            $table->timestamp('tanggal_approve_atasan_1')->nullable();
            $table->text('catatan_atasan_1')->nullable();
            $table->bigInteger('atasan_2')->nullable();
            $table->timestamp('tanggal_approve_atasan_2')->nullable();
            $table->text('catatan_atasan_2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengajuan_hapus_buku');
    }
};
