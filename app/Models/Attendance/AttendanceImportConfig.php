<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceImportConfig extends Model
{
    use HasFactory;
    protected $fillable = [
        'month', 'year', 'day_of_work',
        'start_period', 'end_period',
    ];

    public function attendanceSummary()
    {
        return $this->hasMany(AttendanceSummary::class);
    }

    public function attendanceSummaryByEmployeeId()
    {
        return $this->hasOne(AttendanceSummary::class)->where("employee_id", auth()->user()->employee->id);
    }

    public function getPeriod()
    {
        $month = [
            "01" => 'Januari',
            "02" =>'Februari',
            "03" => 'Maret',
            "04" => 'April',
            "05" => 'Mei',
            "06" => 'Juni',
            "07" => 'Juli',
            "08" => 'Agustus',
            "09" => 'September',
            "10" => 'Oktober',
            "11" => 'November',
            "12" => 'Desember',
        ];

        return $month[$this->month] . ' '.$this->year. '<br/>(' . $this->start_period .' s/d '. $this->end_period.')';
    }
}
