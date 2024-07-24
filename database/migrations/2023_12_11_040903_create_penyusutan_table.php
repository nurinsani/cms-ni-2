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
        Schema::create('penyusutan', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('peralatan_id');
            $table->bigInteger('user_id');
            $table->string('kode_branch', 4);
            $table->string('kode_peralatan', 10);
            $table->string('nama_peralatan', 25);
            $table->string('penyusutan_per_bulan', 25);
            $table->date('tanggal_penyusutan');
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
        Schema::dropIfExists('penyusutan');
    }
};
