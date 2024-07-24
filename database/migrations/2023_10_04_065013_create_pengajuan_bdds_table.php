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
        Schema::create('pengajuan_bdds', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50);
            $table->date('tanggal');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('atasan_1')->unsigned()->nullable();
            $table->bigInteger('atasan_2')->unsigned()->nullable();
            $table->bigInteger('atasan_3')->unsigned()->nullable();
            $table->bigInteger('atasan_4')->unsigned()->nullable();

            $table->string('jenis_asuransi', 50)->nullable(); 
            $table->string('produk_asuransi', 50)->nullable();
            $table->string('no_polis')->nullable();
            $table->string('nama_perusahaan', 50)->nullable();
            $table->string('harga_pertanggungan', 50)->nullable();
            $table->string('no_pic_ar', 50)->nullable();

            $table->string('lokasi', 50)->nullable();
            $table->string('jenis_sewa', 50)->nullable();
            $table->enum('type_bangunan', ['ruko', 'rumah'])->nullable();
            $table->string('nama_pemilik', 50)->nullable();
            $table->string('status_kepemilikan')->nullable();
            $table->string('no_shm_hgb')->nullable();
            $table->string('no_langganan_listrik', 50)->nullable();
            $table->string('no_langganan_pam', 50)->nullable();
            $table->string('no_langganan_telp', 50)->nullable();
            $table->string('harga_sewa', 50)->nullable();
            $table->string('pajak_ps', 50)->nullable();
            $table->string('harga_sewa_net')->nullable();
            $table->string('no_rek_bank_pemilik', 50)->nullable();
            $table->string('atas_nama', 50)->nullable();
            $table->string('no_hp_pemilik', 50)->nullable();
            $table->string('perjanjian')->nullable();
            $table->string('jangka_waktu_amortisasi', 4)->nullable();
            
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
        Schema::dropIfExists('pengajuan_bdds');
    }
};
