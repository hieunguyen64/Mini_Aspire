<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLoansTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create("user_loans", function (Blueprint $table) {
            $table->increments("id");
            $table->integer("user_id");
            $table->double("loan_amount", 12, 2);
            $table->integer("duration")->comment("amount of months");
            $table->string("repay_frequency");
            $table->double("interest_rate");
            $table->string("arrangement_fee", 8, 2);
            $table->integer("status")->comment("0 = uncomplete; 1 = completed");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists("user_loans");
    }
}