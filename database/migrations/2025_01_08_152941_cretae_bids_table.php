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
        // Create cost_bids table
        Schema::create('cost_bids', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('project_name');
            $table->date('document_date')->DBdefault(DB::raw('CURRENT_DATE'));
            $table->date('bid_date');
            $table->string('selected_vendor', 50)->nullable();
            $table->string('attachment', 50)->nullable();
            $table->longText('notes')->nullable();
            $table->enum('status', ['Open', 'Approved', 'Rejected'])->default('Open');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->string('token', 50)->unique()->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('reason')->nullable();
            $table->date('approved_at')->nullable();
            $table->date('rejected_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create cost_bids_vendor table
        Schema::create('cost_bids_vendor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_bids_id')->constrained('cost_bids')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('phone', 20);
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->decimal('grand_total', 15, 2)->nullable();
            $table->decimal('discount', 5, 2)->nullable();
            $table->decimal('final_total', 15, 2)->nullable();
            $table->text('terms_of_payment')->nullable();
            $table->string('lead_time')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create cost_bids_inventory table
        Schema::create('cost_bids_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_bids_id')->constrained('cost_bids')->onDelete('cascade');
            $table->text('description');
            $table->integer('quantity');
            $table->string('uom');
            $table->timestamps();
            $table->softDeletes();
        });

        // Create cost_bids_analysis table
        Schema::create('cost_bids_analysis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_bids_item_id')->constrained('cost_bids_items')->onDelete('cascade');
            $table->foreignId('cost_bids_vendor_id')->constrained('cost_bids_vendor')->onDelete('cascade');
            $table->decimal('price', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order to avoid foreign key constraints
        Schema::dropIfExists('cost_bids_analysis');
        Schema::dropIfExists('cost_bids_items');
        Schema::dropIfExists('cost_bids_vendor');
        Schema::dropIfExists('cost_bids');
    }
};
