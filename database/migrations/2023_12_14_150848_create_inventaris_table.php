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
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('status_branch', 50);
            $table->string('kode_branch', 50);
            $table->string('kode_peralatan', 50);
            $table->string('jenis', 50);
            $table->string('merk', 50);
            $table->string('type', 50);
            $table->integer('jumlah');
            $table->string('harga_perolehan', 50);
            $table->integer('masa_penyusutan');
            $table->date('tanggal_pembelian');
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
        Schema::dropIfExists('inventaris');
    }
};
