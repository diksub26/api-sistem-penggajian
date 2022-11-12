<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AttendaceTemplate implements FromView
{
    public function view () : View
    {
        return view('exports.attendance_template', [
            'employees' => Employee::select('no_induk', 'fullname', 'division')->get()
        ]);
    }
}
