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
        // Tabel suppliers (pemasok)
        Schema::create('incoming_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('phone', 20);
            $table->string('email', 50)->nullable();
            $table->string('address', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel shipments (pengiriman barang)
        Schema::create('incoming_shipments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50);
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict'); // Relasi ke tabel branches
            $table->foreignId('supplier_id')->constrained('incoming_suppliers')->onDelete('restrict'); // Relasi ke tabel incoming_suppliers
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('restrict'); // Relasi ke tabel warehouses
            $table->string('drop_site', 50)->nullable();
            $table->string('phone_drop_site', 20)->nullable();
            $table->string('email_drop_site', 50)->nullable();
            $table->date('eta');
            $table->longText('notes')->nullable();
            $table->string('attachment', 50)->nullable();
            $table->enum('status', ['On Progress', 'Received'])->default('On Progress');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel inventory (barang)
        Schema::create('incoming_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->references('id')->on('incoming_shipments')->onDelete('cascade');
            $table->string('item_name', 50);
            $table->string('quantity', 50);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_inventory');
        Schema::dropIfExists('incoming_shipments');
        Schema::dropIfExists('incoming_suppliers');
    }
};
