<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLoanRepaymentTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create("user_loan_repayment", function (Blueprint $table) {
            $table->increments("id");
            $table->integer("loan_id");
            $table->dateTime("due_date");
            $table->dateTime("actual_due_date");
            $table->double("payment_due", 12, 2);
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
        Schema::dropIfExists("user_loan_repayment");
    }
}