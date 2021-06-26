<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoanRepayment extends Model {
    use HasFactory;
    protected $table = 'user_loan_repayment';
    protected $primaryKey = 'id';
    protected $fillable = [
        "loan_id",
        "due_date",
        "actual_due_date",
        "payment_due",
        "status",
        "arrangement_fee",
        "status"
    ];
}