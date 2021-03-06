<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Myclass;
use App\Section;
use App\House;

class allHouseListExport implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        $sheets = [];
        $year = date("Y");
        // $formList = "";
        $house = House::where('active',1)->pluck('id')->toArray();
        // $classes_id = Myclass::with('sections')->where('school_id',\Auth::user()->school->id)
        //     ->orderBy('class_id','asc')->pluck('id');
        foreach($house as $id){
            $sheets[] = new houseListExport($id);
        }
        return $sheets; 
    }


}


?>
