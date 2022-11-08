<?php

namespace App\Models\Attendance;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'employee_id', 'overtime_date', 'start_time', 'end_time', 'manager_id',
        'description', 'project', 'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id', 'id' );
    }
}
