<?php

namespace App\Models\Attendance;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'employee_id', 'overtime_date', 'start_time', 'end_time', 'manager_id',
        'description', 'project', 'status', 'total'
    ];

    public static function boot() {
        parent::boot();

        static::creating(function($overtime) {
            $to = Carbon::createFromFormat('Y-m-d H:s:i', date('Y-m-d') . $overtime->start_time . ':00');
            $from = Carbon::createFromFormat('Y-m-d H:s:i', date('Y-m-d') . $overtime->from . ':00');
            $overtime->total = $to->diffInHours($from);
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id', 'id' );
    }
}
