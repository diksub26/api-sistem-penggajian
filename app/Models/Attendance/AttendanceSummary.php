<?php

namespace App\Models\Attendance;

use App\Models\Employee;
use App\Models\Transaction\Salary;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSummary extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'employee_id', 'attendance_import_config_id','attend',
        'leave', 'permitte', 'sick', 'late', 'is_final'
    ];

    public function importConfig()
    {
        return $this->belongsTo(AttendanceImportConfig::class, 'attendance_import_config_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function salary()
    {
        return $this->hasOne(Salary::class);
    }
}
