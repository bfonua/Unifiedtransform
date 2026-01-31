<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Myclass;
use App\Section;
use App\Services\User\UserService;


class allFinancePaymentListExport implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        $sheets = [];
        $year = date("Y");
        $classes = Myclass::query()
            ->bySchool(\Auth::user()->getSchoolId())
            ->pluck('id');
        $sections = Section::whereIn('class_id', $classes)
            ->where('active', 1)
            ->orderBy('class_id', 'asc')
            ->orderBy('section_number', 'asc')
            ->pluck('id');
        foreach($sections as $id){
            $sheets[] = new financePaymentListExport2($id);
        }
        return $sheets; 
    }


}


?>
