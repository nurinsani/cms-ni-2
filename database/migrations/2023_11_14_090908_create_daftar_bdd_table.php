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
        Schema::create('daftar_bdd', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_bdd_id');
            $table->foreignId('user_id');
            $table->string('kode_ap', 4);
            $table->date('tanggal_awal');
            $table->date('tanggal_akhir');
            $table->string('jangka_waktu', 2);
            $table->string('status_amortisasi', 2);
            $table->date('tanggal_mutasi')->nullable();
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
        Schema::dropIfExists('daftar_bdd');
    }
};
