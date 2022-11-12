<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TAllowance extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'name', 'amount', 'salary_id'
    ];
}
