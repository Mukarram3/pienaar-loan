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
        Schema::create('loan_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->string('document_type', 50)->comment('original_agreement | supporting | other');
            $table->string('original_filename', 255);
            $table->string('file_path', 255);
            $table->string('mime_type', 80)->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('loan_id');
            $table->index('document_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_documents');
    }
};
