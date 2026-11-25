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
            if (!Schema::hasColumn('inventaris_gol_2', 'old_nip')) {
                $table->string('old_nip')->nullable()->after('nip');
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
            if (Schema::hasColumn('inventaris_gol_2', 'old_nip')) {
                $table->dropColumn('old_nip');
            }
        });
    }
};
