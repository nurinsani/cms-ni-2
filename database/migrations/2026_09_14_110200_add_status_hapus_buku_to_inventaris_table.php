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
        Schema::table('inventaris', function (Blueprint $table) {
            if (!Schema::hasColumn('inventaris', 'status')) {
                $table->string('status', 25)->default('aktif')->after('pengguna');
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
        Schema::table('inventaris', function (Blueprint $table) {
            if (Schema::hasColumn('inventaris', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
