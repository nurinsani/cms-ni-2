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
        Schema::create('transaksi_ap', function (Blueprint $table) {
            $table->id();
            $table->string('kode_branch', 4);
            $table->string('kode_transaksi', 36);
            $table->string('kode_rekening', 20);
            $table->dateTime('tanggal_transaksi');
            $table->string('jenis_transaksi', 20);
            $table->text('keterangan');
            $table->bigInteger('debet');
            $table->bigInteger('kredit');
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
        Schema::dropIfExists('transaksi_ap');
    }
};
