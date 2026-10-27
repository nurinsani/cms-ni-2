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
        Schema::table('inventaris_gol_2', function (Blueprint $table) {
            if (!Schema::hasColumn('inventaris_gol_2', 'status')) {
                $table->string('status', 25)->default('aktif')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventaris_gol_2', function (Blueprint $table) {
            if (Schema::hasColumn('inventaris_gol_2', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
