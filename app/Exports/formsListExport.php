<?php 

namespace App\Exports;

use App\Users;
use App\Section;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class formsListExport implements WithEvents, WithTitle
{
    private $form_id;

    public function __construct(int $section_id)
    {
        $this->section_id = $section_id;
    }

    public function title(): string
    {
        $formRec = Section::find($this->section_id);
        return $formRec->class->class_number.$formRec->section_number;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){
                $sheet = $event->sheet;
                $sheet->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $formRec = \App\Section::find($this->section_id);
                // == SHEET TITLE
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', $formRec->class->class_number.$formRec->section_number);
                $title_style = array(
                    'font' => array(
                        'bold' => true,
                        'size' => 14,
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
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                    ),
                    'fill' => array(
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => array('argb' => 'c6e0b4'),
                    ),
                );
                $inactiveStyle = array(
                    'fill' => array(
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => array('')
                    ),
                );

                $sheet->getStyle('A1')->applyFromArray($title_style);
                $event->sheet->setCellValue('A2', "TCT ID")
                    ->setCellValue('B2', '#')
                    ->setCellValue('C2', 'Name')
                    ->setCellValue('D2', 'House');
                $sheet->getStyle('A2:D2')->applyFromArray($heading_style);

                $reslist = \App\StudentInfo::where('form_id', $this->section_id)
                    ->where('session', now()->year)
                    ->orderBy('form_num', 'asc')->get();
                $formList = array();
                $row = 3;
                $count = 1;
                foreach($reslist as $res){
                    $class_num = $res->form_num;
                    while($count < $class_num){
                        $sheet->setCellValue('B'.$row, $count);
                        $count++;
                        $row++;
                    }
                    $name = $res->student->given_name." ".$res->student->lst_name;
                    if($res->group == "Head Prefect"){
                        $name .= '(HP)';
                    } elseif($res->group == "Prefect"){
                        $name .= "(P)";
                    }
                    $sheet->setCellValue('A'.$row, $res->tct_id)
                        ->setCellValue('B'.$row, $res->form_num)
                        ->setCellValue('C'.$row, $name)
                        ->setCellValue('D'.$row, $res->house->house_abbrv);
                        if($res->student->active == 0){
                            $sheet->getStyle("A".$row.":D".$row)->applyFromArray($inactiveStyle);
                        }
                        $row++;
                        $count++;
                }
                $last_row = $row - 1;
                $last_border = $last_row + 5;

                $center = array(
                    'alignment' => array(
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                    )
                );
                $sheet->getStyle("B3:B{$last_border}")->applyFromArray($center);
                $sheet->getStyle("D3:D{$last_border}")->applyFromArray($center);

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
                $sheet->getColumnDimension('B')->setWidth(5);
                $sheet->getColumnDimension('C')->setWidth(35);
                $sheet->getColumnDimension('D')->setWidth(7);
                foreach(range('E','M') as $columnID){
                    $sheet->getColumnDimension($columnID)->setWidth(4);
                }


            }
        ];
    }
}



?>
