<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceImportConfig extends Model
{
    use HasFactory;
    protected $fillable = [
        'month', 'year', 'day_of_work'
    ];

    public function attendanceSummary()
    {
        return $this->hasMany(AttendanceSummary::class);
    }
}
