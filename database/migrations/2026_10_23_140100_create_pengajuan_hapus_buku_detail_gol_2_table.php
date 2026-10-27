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
        Schema::create('pengajuan_hapus_buku_detail_gol_2', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pengajuan_hapus_buku_id');
            $table->bigInteger('inventaris_gol_2_id');
            $table->string('foto')->nullable();
            $table->string('kondisi')->nullable();
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
        Schema::dropIfExists('pengajuan_hapus_buku_detail_gol_2');
    }
};
