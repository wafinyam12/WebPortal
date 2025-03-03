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
        // Table: projects
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 50)->unique();
            $table->string('name', 50);
            $table->string('address', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('ppic', 50)->nullable();
            $table->longText('description')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
            $table->softDeletes();
        });

        // Table: tools_categories
        Schema::create('tools_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 50);
            $table->string('description', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Table: tools
        Schema::create('tools', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('owner_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('category_id')->constrained('tools_categories')->onDelete('restrict');
            $table->string('code', 50)->unique();
            $table->string('serial_number', 100)->nullable();
            $table->string('name', 100);
            $table->string('brand', 100)->nullable();
            $table->string('type', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->year('year')->nullable();
            $table->string('origin', 100)->nullable();
            $table->integer('quantity')->default(1);
            $table->string('unit', 50)->nullable();
            $table->enum('condition', ['New', 'Used', 'Broken'])->default('New');
            $table->enum('status', ['Active', 'Maintenance', 'Inactive'])->default('Active');
            $table->longText('description')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->string('warranty', 50)->nullable();
            $table->date('warranty_start')->nullable();
            $table->date('warranty_end')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Table: tools_maintenance
        Schema::create('tools_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_id')->constrained('tools')->onDelete('restrict');
            $table->string('code', 50)->unique();
            $table->date('maintenance_date');
            $table->decimal('cost', 15, 2)->nullable();
            $table->enum('status', ['In Progress', 'Completed', 'Cancelled'])->default('In Progress');
            $table->longText('description')->nullable();
            $table->date('completion_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Table: tools_transactions for EX: Delivery Note , Transfer and Return
        Schema::create('tools_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('source_project_id')->constrained('projects')->onDelete('restrict'); // Project asal
            $table->foreignId('destination_project_id')->constrained('projects')->onDelete('restrict'); // Project tujuan
            $table->string('document_code', 50);
            $table->date('document_date')->DBDefault(DB::raw('CURRENT_DATE'));
            $table->date('delivery_date');
            $table->string('ppic', 50)->nullable();
            $table->string('driver', 50)->nullable();
            $table->string('driver_phone', 20)->nullable();
            $table->string('transportation', 50)->nullable();
            $table->string('plate_number', 50)->nullable();
            $table->enum('status', ['In Progress', 'Completed', 'Cancelled'])->default('In Progress');
            $table->enum('type', ['Delivery Note', 'Transfer', 'Return'])->default('Delivery Note');
            $table->longText('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tools_transactions_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transactions_id')->references('id')->on('tools_transactions')->onDelete('cascade');
            $table->foreignId('tool_id')->constrained('tools')->onDelete('restrict');
            $table->integer('quantity')->default(1);
            $table->string('unit', 50)->nullable();
            $table->string('last_location', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('asuransi_project', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id')->refrences('id')->on('projects');
            $table->string('name', 255)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('masa_berlaku')->nullable();
            $table->date('tanggal_jatuh_tempo')->nullable();
            // $table->decimal('nominal_asuransi', 15, 2)->default(0.00);
            $table->string('status', 255)->default('OPEN');
            $table->longText('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools_transactions_shipments');
        Schema::dropIfExists('tools_transactions');
        Schema::dropIfExists('tools_maintenance');
        Schema::dropIfExists('tools');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('tools_categories');
        Schema::dropIfExists('asuransi_project');
    }
};
