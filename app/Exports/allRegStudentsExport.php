<?php

namespace App\Exports;

use App;
use App\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
// use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
ini_set('memory_limit', '-1');
class allRegStudentsExport implements FromView
{

    public function __construct(int $year){
        $this->year = $year;
    }
    
    public function view(): View
    {
        $data = \App\StudentInfo::where('session', now()->year)->get();
        return view('exports.regStudents', [
            'students' => $data
        ]);
    }

}
