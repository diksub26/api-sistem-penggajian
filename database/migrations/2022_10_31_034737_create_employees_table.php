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
        Schema::create('employees', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->uuid('id')->primary();
            $table->string("no_induk", 20);
            $table->string("fullname", 100);
            $table->enum("gender", ['L', 'P'] );
            $table->string("place_of_birth", 75);
            $table->date('dob');
            $table->text("address");
            $table->enum('religion', [
                'ISLAM', 'HINDU', 'BUDHA', 'KRISTEN KATOLIK', 'KRISTEN PROTESTAN', 'KONGHUCU'
            ]);
            $table->string('no_hp', 15);
            $table->bigInteger('employee_position_id');
            $table->date('assignment_date');
            $table->string('division', 75);
            $table->integer('basic_salary');
            $table->string('bank_acc_no', 75);
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
        Schema::dropIfExists('employees');
    }
};
