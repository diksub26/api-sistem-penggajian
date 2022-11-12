<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSummary extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'employee_id', 'attendance_import_config_id','attend',
        'leave', 'permitte', 'sick', 'late'
    ];

    public function importConfig()
    {
        return $this->belongsToMany(AttendanceImportConfig::class);
    }
}
