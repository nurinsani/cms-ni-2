<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('param_jabatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jabatan');
            $table->string('kode_pembebanan', 20);
            $table->string('kategori')->nullable();
            $table->enum('status_aktif', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Insert initial data
        $initialData = [
            ['nama_jabatan' => 'Business Head', 'kode_pembebanan' => '9999', 'kategori' => 'Kantor Pusat / Regional'],
            ['nama_jabatan' => 'QA', 'kode_pembebanan' => '9999', 'kategori' => 'Kantor Pusat / Regional'],
            ['nama_jabatan' => 'BM Jateng 1', 'kode_pembebanan' => '2200', 'kategori' => 'Branch Manager (BM) & Area Manager (AM)'],
            ['nama_jabatan' => 'AM Area 1', 'kode_pembebanan' => '1101', 'kategori' => 'Branch Manager (BM) & Area Manager (AM)'],
            ['nama_jabatan' => 'AM Area 2', 'kode_pembebanan' => '8888', 'kategori' => 'Branch Manager (BM) & Area Manager (AM)'],
            ['nama_jabatan' => 'AL Jabar 1', 'kode_pembebanan' => '1101', 'kategori' => 'Area Leader (AL) - Jawa Barat'],
            ['nama_jabatan' => 'AL Jabar 2', 'kode_pembebanan' => '1102', 'kategori' => 'Area Leader (AL) - Jawa Barat'],
            ['nama_jabatan' => 'AL Jabar 3', 'kode_pembebanan' => '1103', 'kategori' => 'Area Leader (AL) - Jawa Barat'],
            ['nama_jabatan' => 'AL Jabar 4', 'kode_pembebanan' => '1104', 'kategori' => 'Area Leader (AL) - Jawa Barat'],
            ['nama_jabatan' => 'AL Jateng 1', 'kode_pembebanan' => '2200', 'kategori' => 'Area Leader (AL) - Jawa Tengah'],
            ['nama_jabatan' => 'AL Jateng 2', 'kode_pembebanan' => '2205', 'kategori' => 'Area Leader (AL) - Jawa Tengah'],
            ['nama_jabatan' => 'AL Jateng 3', 'kode_pembebanan' => '2203', 'kategori' => 'Area Leader (AL) - Jawa Tengah'],
            ['nama_jabatan' => 'AL Jateng 4', 'kode_pembebanan' => '2204', 'kategori' => 'Area Leader (AL) - Jawa Tengah'],
            ['nama_jabatan' => 'AL Jateng 5', 'kode_pembebanan' => '2202', 'kategori' => 'Area Leader (AL) - Jawa Tengah'],
            ['nama_jabatan' => 'AL Jateng 6', 'kode_pembebanan' => '2206', 'kategori' => 'Area Leader (AL) - Jawa Tengah'],
            ['nama_jabatan' => 'AL Jateng 7', 'kode_pembebanan' => '2207', 'kategori' => 'Area Leader (AL) - Jawa Tengah'],
            ['nama_jabatan' => 'AL Jateng 8', 'kode_pembebanan' => '2208', 'kategori' => 'Area Leader (AL) - Jawa Tengah'],
            ['nama_jabatan' => 'AL Jateng 9', 'kode_pembebanan' => '2209', 'kategori' => 'Area Leader (AL) - Jawa Tengah'],
            ['nama_jabatan' => 'AL Jateng 10', 'kode_pembebanan' => '2210', 'kategori' => 'Area Leader (AL) - Jawa Tengah'],
            ['nama_jabatan' => 'AL Jateng 11', 'kode_pembebanan' => '2211', 'kategori' => 'Area Leader (AL) - Jawa Tengah'],
            ['nama_jabatan' => 'MM Pedurungan', 'kode_pembebanan' => '2208', 'kategori' => 'Marketing Manager (MM) & Unit'],
            ['nama_jabatan' => 'MM Semarang Timur', 'kode_pembebanan' => '2208', 'kategori' => 'Marketing Manager (MM) & Unit'],
            ['nama_jabatan' => 'MM Genuk 2', 'kode_pembebanan' => '2208', 'kategori' => 'Marketing Manager (MM) & Unit'],
        ];

        $now = now();
        foreach ($initialData as &$row) {
            $row['status_aktif'] = 'aktif';
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }

        DB::table('param_jabatans')->insert($initialData);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('param_jabatans');
    }
};

