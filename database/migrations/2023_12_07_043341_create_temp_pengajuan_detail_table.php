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
        Schema::create('temp_pengajuan_detail', function (Blueprint $table) {
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
            $table->string('moa')->nullable();
            $table->string('group_approval', 25);
            $table->string('group_inventaris', 25);
            $table->text('keterangan')->nullable();
            $table->string('status_lpj');
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
        Schema::dropIfExists('temp_pengajuan_detail');
    }
};
