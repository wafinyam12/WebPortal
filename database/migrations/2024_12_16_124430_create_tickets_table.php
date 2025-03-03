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
        // Tickets Categories Table
        Schema::create('tickets_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('slug', 100);
            $table->longText('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tickets Table
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('category_id')->constrained('tickets_categories')->onDelete('restrict');
            $table->foreignId('assigned_id')->constrained('departments')->onDelete('restrict');
            $table->foreignId('user_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('code', 50)->unique();
            $table->string('title', 100);
            $table->longText('description')->nullable();
            $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent', 'Other'])->default('Low');
            $table->enum('status', ['Open', 'Closed', 'In Progress', 'Cancelled'])->default('Open');
            $table->longText('solution')->nullable();
            $table->string('attachment')->nullable();
            $table->date('closed_date')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tickets Comments Table
        Schema::create('tickets_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->longText('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets_comments');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('tickets_categories');
    }
};
