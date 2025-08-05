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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type'); // identity, cv, certificate, etc.
            $table->string('file_path');
            $table->string('file_name');
            $table->string('size'); // in bytes
            $table->string('extension');
            $table->json('tags')->nullable(); // store tags as JSON
            $table->string('mime_type');
            $table->date('expires_at')->nullable();
            $table->boolean('is_public')->default(false);
            $table->text('description')->nullable();
            $table->string('source')->nullable(); // upload, scan, etc.
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->enum('verification_status', ['pending', 'under_review', 'verified', 'rejected'])->default('pending');
            $table->boolean('status')->default(true); // active/inactive
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });

        // Pivot table for polymorphic relationship
        Schema::create('documentables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('documentable_id');
            $table->string('documentable_type');
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->index(['documentable_id', 'documentable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentables');
        Schema::dropIfExists('documents');
    }
};
