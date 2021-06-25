<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoans extends Model {
    use HasFactory;
    protected $table = 'user_loans';
    protected $primaryKey = 'id';
}