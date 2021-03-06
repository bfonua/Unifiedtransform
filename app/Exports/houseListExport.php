<?php 

namespace App\Exports;

use App\Users;
use App\Section;
use App\House;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class houseListExport implements WithEvents, WithTitle
{
    private $house_id;

    public function __construct(int $house_id)
    {
        $this->house_id = $house_id;
    }

    public function split_name($name) {
        $parts = explode(' ', $name); // $meta->post_title
        $name_first = array_shift($parts);
        $name_last = array_pop($parts);
        $name_middle = trim(implode(' ', $parts));
        return array($name_first, $name_last, $name_middle);
    }

    public function title(): string
    {
        $houseRec = House::find($this->house_id);
        return $houseRec->house_abbrv;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){
                $sheet = $event->sheet;
                $sheet->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $houseRec = \App\House::find($this->house_id);
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', $houseRec->house_name);
                $title_style = array(
                    'font' => array(
                        'bold' => true,
                        'size' => 14
                        ),
                    'alignment' => array(
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ),
                );
                $heading_style = array(
                    'font' => array(
                        'bold' => true,
                        ),
                    'alignment' => array(
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ),
                    'fill' => array(
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => array('argb' => 'c6e0b4'),
                        ),
                    );

                $inactiveStyle = array(
                    'fill' => array(
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => array('argb' => '909090'),
                        ),
                );

                $sheet->getStyle('A1')->applyFromArray($title_style);
                $event->sheet ->setCellValue('A2', "TCT ID")
                ->setCellValue('B2', "#")
                ->setCellValue('C2', "Status")
                ->setCellValue('D2', "Full Name")
                ->setCellValue('E2', "Form");
                $sheet->getStyle('A2:E2')->applyFromArray($heading_style);

                $groups = ['Head Prefect', 'prefect', 'old', 'new'];
                $groupsOrder = implode(',', $groups);


                $reslist = \App\StudentInfo::where('session', now()->year)
                    ->where('house_id', $this->house_id)
                    // ->orderBy('form_id', 'desc')
                    // ->orderByRaw(\DB::raw("CASE WHEN group = 'Head Prefect' THEN group END ASC"))
                    ->get();
                $row = 3; 
                $count = 1;
                foreach($reslist as $res){
                    $name = $this->split_name($res->student->given_name)[0]." ". $this->split_name($res->student->given_name)[1]." ".$res->student->lst_name;
                    if($res->group == "Head Prefect"){
                        $name .= ' (HP)';
                    }
                    elseif($res->group == "prefect"){
                        $name .= ' (P)';
                    }
                    $role = (ucfirst($res->group) == "Head Prefect")? 'HP': ucfirst($res->group);
                        
                    $sheet->setCellValue('A'.$row, $res->tct_id)
                    ->setCellValue('B'.$row, $count)
                    ->setCellValue('C'.$row, $role)
                    ->setCellValue('D'.$row, $name)
                    ->setCellValue('E'.$row, $res->section->class->class_number.$res->section->section_number);
                    if($res->student->active == 0){
                        $sheet->getStyle("A".$row.":E".$row)->applyFromArray($inactiveStyle);
                    }
                    $row++;
                    $count++;
                }
                $last_row = $row - 1;
                $last_border = $last_row + 5;
                // Center Align
                $center = array(
                    'alignment' => array(
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                    )
                );
                $sheet->getStyle("B3:B{$last_border}")->applyFromArray($center);
                $sheet->getStyle("C3:C{$last_border}")->applyFromArray($center);
                $sheet->getStyle("E3:E{$last_border}")->applyFromArray($center);

                $borderArray = array(
                    'borders' => array(
                        'allBorders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ),
                        ),
                    );


                $sheet->getStyle("A1:L{$last_border}")->applyFromArray($borderArray);

                $sheet -> getHeaderFooter()->setDifferentOddEven(false)
							->setOddHeader('&RForm List  - &D');

                // WIDTHS
                $sheet->getColumnDimension('A')->setWidth(7);
                $sheet->getColumnDimension('B')->setWidth(4);
                $sheet->getColumnDimension('C')->setWidth(7);
                $sheet->getColumnDimension('D')->setWidth(35);
                $sheet->getColumnDimension('E')->setWidth(7);
                foreach(range('F','M') as $columnID){
                    $sheet->getColumnDimension($columnID)->setWidth(4);
                }
            }
        ];
    }
}
?>
