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
        Schema::create('vehicle_type', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->longText('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('vehicle', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('type_id');
            $table->string('code', 50)->unique();
            $table->string('brand', 50)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('color', 50)->nullable();
            $table->string('license_plate', 20)->nullable();
            $table->string('transmission', 50)->nullable();
            $table->string('fuel', 50)->nullable();
            $table->string('mileage', 100)->nullable();
            $table->year('year')->nullable();
            $table->date('tax_year')->nullable();
            $table->date('tax_five_year')->nullable();
            $table->date('inspected')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->longText('description')->nullable();
            $table->string('origin')->nullable();
            $table->enum('status', ['Active', 'Maintenance', 'Inactive'])->default('Active');
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('owner_id')->references('id')->on('branches');
            $table->foreign('type_id')->references('id')->on('vehicle_type');
        });

        Schema::create('vehicle_maintenance_record', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vehicle_id');
            $table->string('code', 50)->unique();
            $table->string('mileage', 100)->nullable();
            $table->date('maintenance_date')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->date('next_maintenance')->nullable();
            $table->enum('status', ['In Progress', 'Cancelled', 'Completed'])->default('In Progress');
            $table->longText('notes')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vehicle_id')->references('id')->on('vehicle');
        });

        Schema::create('vehicle_insurance_policy', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vehicle_id');
            $table->string('code', 50)->unique();
            $table->string('insurance_provider', 50)->nullable();
            $table->string('policy_number', 50)->nullable();
            $table->date('coverage_start')->nullable();
            $table->date('coverage_end')->nullable();
            $table->decimal('premium', 15, 2)->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vehicle_id')->references('id')->on('vehicle');
        });

        Schema::create('vehicle_assignment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('user_id');
            $table->string('code', 50)->unique();
            $table->date('assignment_date')->nullable();
            $table->date('return_date')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vehicle_id')->references('id')->on('vehicle');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('vehicle_reimbursement', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vehicle_id');
            $table->date('date_recorded');
            $table->unsignedBigInteger('user_by')->nullable();
            $table->string('fuel', 50)->nullable();
            $table->string('amount', 100)->nullable();
            $table->decimal('price', 15, 2);
            $table->string('first_mileage', 100)->nullable();
            $table->string('last_mileage', 100)->nullable();
            $table->string('attachment_mileage')->nullable();
            $table->string('attachment_receipt');
            $table->longText('notes')->nullable();
            $table->string('reason')->nullable();
            $table->enum('status', ['Approved', 'Pending', 'Rejected'])->default('Pending');
            $table->date('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['Refueling', 'Parking', 'E-Toll']);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vehicle_id')->references('id')->on('vehicle');
            $table->foreign('user_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_reimbursement');
        Schema::dropIfExists('vehicle_assignment');
        Schema::dropIfExists('vehicle_insurance_policy');
        Schema::dropIfExists('vehicle_maintenance_record');
        Schema::dropIfExists('vehicle');
        Schema::dropIfExists('vehicle_type');
    }
};
