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
        Schema::create('saldo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->references('id')->on('users');
            $table->decimal('saldo', 15, 2);
            $table->timestamps();
        });

        Schema::create('petty_cash', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->references('id')->on('users');
            $table->string('no_nota', 255)->nullable();
            $table->date('tanggal');
            $table->date('tanggal_nota')->nullable();
            $table->string('file_name', 255)->nullable();
            $table->string('status_budget_control', 255)->default('OPEN');
            $table->string('status_ap', 255)->default('OPEN');
            $table->date('approved_date')->nullable();
            $table->date('approved_ap_date')->nullable();
            $table->string('approved_by', 255)->nullable();
            $table->string('approved_ap_by', 255)->nullable();
            $table->string('reject_reason',255)->nullable();
            $table->decimal('balance', 15, 2)->nullable();
            $table->foreignId('saldo_id')->references('id')->on('saldo');
            $table->timestamps();
        });

        Schema::create('petty_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petty_id')->references('id')->on('petty_cash');
            $table->string('sku', 512)->nullable();
            $table->decimal('qty', 15, 2)->nullable();
            $table->string('keterangan',5100)->nullable();
            $table->decimal('debet', 15, 2)->nullable();
            $table->decimal('kredit', 15, 2)->nullable();
           
        });

        Schema::create('project_petty', function (Blueprint $table) {
            $table->id();
            $table->string('code_project', 255)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('coa', function (Blueprint $table) {
            $table->id();
            $table->string('coa', 255)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('item_group', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->foreignId('coa_id')->references('id')->on('coa');
            $table->timestamps();
        });

        Schema::create('sku', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 255)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->foreignId('item_group_id')->references('id')->on('item_group');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cash');
        Schema::dropIfExists('petty_recap');
        Schema::dropIfExists('saldo');
        Schema::dropIfExists('coa');
        Schema::dropIfExists('sku');
        Schema::dropIfExists('item_group');
        Schema::dropIfExists('project_petty');
    }
};
 