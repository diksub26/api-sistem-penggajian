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

    public function getPeriod()
    {
        $month = [
            1 => 'Januari',
            2 =>'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $month[$this->month] . ' '.$this->year. '<br/>(' . $this->start_period .' s/d '. $this->end_period.')';
    }
}
