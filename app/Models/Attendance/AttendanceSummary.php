<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSummary extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'employee_id', 'month', 'year', 'attend', 'leave',
        'permitte', 'sick', 'late'
    ];
}
