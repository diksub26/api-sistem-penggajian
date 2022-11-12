<?php

namespace App\Models\Transaction;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory, HasUuids;
    
    protected $fillable = [
        'attendance_summary_id', 'employee_id', 'basic_salary',
        'total_allowances', 'total_salary_cuts', 'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function allowances()
    {
        return $this->hasMany(TAllowance::class);
    }

    public function salaryCut()
    {
        return $this->hasMany(TSalaryCut::class);
    }
}
