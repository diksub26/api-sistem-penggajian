<?php

namespace App\Models\Transaction;

use App\Models\MasterData\MasterSalaryCuts;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryCut extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [ 'master_salary_cut_id', 'employee_id'];

    public function masterSalaryCut()
    {
        return $this->belongsTo(MasterSalaryCuts::class);
    }
}
