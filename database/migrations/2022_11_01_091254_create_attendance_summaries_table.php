<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_summaries', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->unsignedBigInteger('attendance_import_config_id');
            $table->tinyInteger('attend')->default(0);
            $table->tinyInteger('leave')->default(0);
            $table->tinyInteger('permitte')->default(0);
            $table->tinyInteger('sick')->default(0);
            $table->tinyInteger('late')->default(0);
            $table->boolean('is_final')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_summaries');
    }
};
