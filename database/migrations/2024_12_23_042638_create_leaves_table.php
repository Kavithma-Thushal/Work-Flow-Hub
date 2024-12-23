<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('leave_policy_id')->references('id')->on('leave_policies')->onDelete('cascade');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('leave_policy_id');
            $table->integer('taken_casual_leaves')->default(0);
            $table->integer('taken_annual_leaves')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
