<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contract', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('created_by')->constrained('users')->onDelete('restrict');
            $table->string('code', 255)->unique()->nullable();
            $table->string('name', 255)->nullable();
            $table->string('nama_perusahaan', 255)->nullable();
            $table->string('nama_pekerjaan', 255)->nullable();
            $table->string('status_kontrak', 255)->nullable();
            $table->string('jenis_pekerjaan', 255)->nullable();
            $table->decimal('nominal_kontrak', 15, 2)->default(0.00);
            $table->date('tanggal_kontrak')->nullable();
            $table->date('masa_berlaku')->nullable();
            $table->string('status_proyek', 255)->default('OPEN');
            $table->string('retensi', 255)->nullable();
            $table->string('masa_retensi')->nullable();
            $table->string('status_retensi', 255)->nullable();
            $table->string('pic_sales', 255)->nullable();
            $table->string('pic_pc', 255)->nullable();
            $table->string('pic_customer', 255)->nullable();
            $table->string('mata_uang', 255)->default('IDR');
            $table->date('bast_1')->nullable();
            $table->string('bast_1_nomor', 255)->nullable();
            $table->date('bast_2')->nullable();
            $table->string('bast_2_nomor', 255)->nullable();
            $table->string('overall_status', 255)->nullable();
            $table->string('kontrak_milik', 255)->nullable();
            $table->longText('keterangan')->nullable();
            $table->longText('memo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract');
    }
};
