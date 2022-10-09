<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Myclass;
use App\Section;

class allFinanceRemainListExport implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        $sheets = [];
        $sections = Section::with('class')
            ->where('active', 1)
            ->orderBy('class_id', 'asc')
            ->orderBy('section_number', 'asc')
            ->pluck('id');
        foreach($sections as $id){
            $sheets[] = new financeRemainListExport2($id);
        }
        $sections = Section::with('class', 'students')
            ->where('active', 1)
            ->orderBy('class_id', 'asc')
            ->orderBy('section_number', 'asc')
            ->get();



        return $sheets; 
    }


}


?>
