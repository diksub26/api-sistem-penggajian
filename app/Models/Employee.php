<?php

namespace App\Models;

use App\Models\MasterData\EmployeePosition;
use App\Models\Transaction\Allowance;
use App\Models\Transaction\SalaryCut;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'no_induk', 'fullname', 'gender',
        'place_of_birth', 'gender', 'dob',
        'address', 'religion', 'no_hp',
        'employee_position_id', 'assignment_date',
        'division', 'basic_salary'
    ];

    public static function boot() {
        parent::boot();

        static::deleting(function($employee) {
            $employee->user()->delete();
            $employee->allowance()->delete();
            $employee->salaryCut()->delete();
        });
    }

    public function position()
    {
        return $this->belongsTo(EmployeePosition::class, 'employee_position_id');
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function allowance()
    {
        return $this->hasMany(Allowance::class);
    }

    public function salaryCut()
    {
        return $this->hasMany(SalaryCut::class);
    }
}
