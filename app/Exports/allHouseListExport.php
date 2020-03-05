<?php

namespace App\Exports;

use App\House;
use App\Myclass;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class allHouseListExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $sheets = [];
        $year = date('Y');
        // $formList = "";
        $house = House::where('active', 1)->pluck('id')->toArray();
        // $classes_id = Myclass::with('sections')->where('school_id',\Auth::user()->school->id)
        //     ->orderBy('class_id','asc')->pluck('id');
        foreach ($house as $id) {
            $sheets[] = new houseListExport($id);
        }

        return $sheets;
    }
}
