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
        Schema::create('appeals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained();
            $table->foreignId('application_id')->constrained();
            $table->foreignId('process_id')->constrained();
            $table->foreignId('appeal_stage_id')->constrained();
            $table->text('text');
            $table->text('response')->nullable();
            $table->char('result', 1)->nullable();
            $table->foreignId('evaluator_id')->nullable()->constrained('users');
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appeals');
    }
};
