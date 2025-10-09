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
        Schema::create('appeal_stages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->foreignId('process_id')->constrained();
            $table->date('submission_start_date');
            $table->date('submission_end_date');
            $table->date('result_start_date');
            $table->date('result_end_date');
            $table->boolean('has_attachments')->default(false);
            $table->boolean('allow_many')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appeal_stages');
    }
};
