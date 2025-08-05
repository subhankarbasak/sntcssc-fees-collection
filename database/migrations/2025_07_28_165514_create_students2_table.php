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
        Schema::create('students2', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->string('admission_test_roll_no')->unique()->nullable();
            $table->string('programme_name');
            $table->string('batch');
            $table->string('student_id')->unique();
            $table->string('section')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('district')->nullable();
            $table->text('address')->nullable();
            $table->enum('category', ['Unreserved', 'SC', 'ST', 'OBC'])->default('Unreserved');
            $table->date('dob')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('email')->unique();
            $table->string('alternate_email')->nullable();
            $table->string('mobile', 15)->nullable();
            $table->string('alternate_mobile', 15)->nullable();
            $table->string('whatsapp', 15)->nullable();
            $table->boolean('is_pwbd')->default(false);
            $table->string('occupation')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->decimal('family_income', 10, 2)->nullable();
            $table->string('selection_type')->nullable();
            $table->decimal('score_A', 5, 2)->nullable();
            $table->decimal('score_B', 5, 2)->nullable();
            $table->decimal('score_C', 5, 2)->nullable();
            $table->decimal('score_D', 5, 2)->nullable();
            $table->string('status')->nullable();
            $table->text('note')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
