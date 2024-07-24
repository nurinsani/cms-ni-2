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
        Schema::create('pengajuan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuans')->onDelete('cascade')->onUpdate('cascade');
            $table->string('kode_branch');
            $table->string('code_area');
            $table->string('nama_peralatan', 50);
            $table->string('coa', 25);
            $table->string('merk', 50);
            $table->integer('kuantitas');
            $table->string('harga', 50);
            $table->string('nama_toko', 50);
            $table->string('no_telp', 50)->nullable();
            $table->string('pengguna', 50);
            $table->date('tanggal_pembelian')->nullable();
            $table->string('bukti_pengembalian')->nullable();
            $table->text('keterangan');
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
        Schema::dropIfExists('pengajuan_details');
    }
};
