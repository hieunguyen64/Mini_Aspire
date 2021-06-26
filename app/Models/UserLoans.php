<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoans extends Model {
    use HasFactory;
    protected $table = "user_loans";
    protected $primaryKey = "id";
    protected $fillable = [
        "user_id",
        "loan_amount",
        "duration",
        "repay_frequency",
        "interest_rate",
        "arrangement_fee",
        "status"
    ];
}