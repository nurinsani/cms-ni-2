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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_transaksi');
            $table->string('unit', 4);
            $table->uuid('kode_transaksi', 25);
            $table->string('kode_rekening', 20);
            $table->dateTime('tanggal_transaksi');
            $table->text('jenis_transaksi');
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
        Schema::dropIfExists('transaksi');
    }
};
