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
        Schema::create('jenis_peralatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('param_peralatan_id');
            $table->string('nama_jenis', 100);
            $table->string('group_approval', 15);
            $table->string('group_bdd', 15)->nullable();
            $table->string('group_inventaris', 15)->nullable();
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
        Schema::dropIfExists('jenis_peralatans');
    }
};
