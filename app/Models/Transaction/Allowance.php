<?php

namespace App\Models\Transaction;

use App\Models\MasterData\MasterAllowance;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [ 'master_allowance_id', 'employee_id'];

    public function masterAllowance()
    {
        return $this->belongsTo(MasterAllowance::class);
    }
}
