<?php 

namespace App\Exports;

use App\Users;
use App\Section;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class financeAssignListExport implements WithEvents, WithTitle
{
    private $form_id;

    public function __construct(int $section_id)
    {
        $this->section_id = $section_id;
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
        $formRec = Section::find($this->section_id);
        return $formRec->class->class_number.$formRec->section_number;
    }

    public function registerEvents(): array
    {
        ini_set('memory_limit', '-1');
        return [
            AfterSheet::class => function(AfterSheet $event){
                $sheet = $event->sheet;
                $sheet->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $formRec = \App\Section::find($this->section_id);
                // == SHEET TITLE
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
                    // 'fill' => array(
                    //     'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    //     'color' => array('argb' => 'c6e0b4'),
                    // ),
                );
                $center = array(
                    'alignment' => array(
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                    )
                );
                $unassigned_style = array(
                    'alignment' => array(
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ),
                    'fill' => array(
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => array('argb' => 'eebaba')
                    ),
                );
                $inactiveStyle = array(
                    'fill' => array(
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => array('argb' => '909090')
                    ),
                );
                // TITLE 
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', $formRec->class->class_number.$formRec->section_number);
                $sheet->getStyle('A1')->applyFromArray($title_style);
                // SUB HEADERS
                $event->sheet->setCellValue('A2', "TCT ID")
                    ->setCellValue('B2', '#')
                    ->setCellValue('C2', 'Name')
                    ->setCellValue('D2', 'House')
                    ->setCellValue('E2', 'Term 1')
                    ->setCellValue('F2', 'Term 2')
                    ->setCellValue('G2', 'Term 3')
                    ->setCellValue('H2', 'Term 4')
                    ->setCellValue('I2', 'Late')
                    ->setCellValue('J2', 'Total');
                $sheet->getStyle('A2:J2')->applyFromArray($heading_style);
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
                    $name = $this->split_name($res->student->given_name)[0]." ". $this->split_name($res->student->given_name)[1]." ".$res->student->lst_name;
                    if($res->group == "Head Prefect"){
                        $name .= ' (HP)';
                    } elseif(ucfirst($res->group) == "Prefect"){
                        $name .= " (P)";
                    }
                    $sheet->setCellValue('A'.$row, $res->tct_id)
                        ->setCellValue('B'.$row, $res->form_num)
                        ->setCellValue('C'.$row, $name)
                        ->setCellValue('D'.$row, $res->house->house_abbrv);
                    if($res->assigned == "0"){
                        $sheet->setCellValue('E'.$row, '-');
                        $sheet->setCellValue('F'.$row, '-');
                        $sheet->setCellValue('G'.$row, '-');
                        $sheet->setCellValue('H'.$row, '-');
                        $sheet->setCellValue('I'.$row, '-');
                        $sheet->setCellValue('J'.$row, '-');
                        $sheet->getStyle("E".$row.":J".$row)->applyFromArray($unassigned_style);
                    } else{
                        $assigned = \App\Assign::where('session', now()->year)
                            ->where('user_id', $res->student->id)->pluck('fee_id')->toArray();
                        $feeList = [];
                        $total = 0;
                        foreach($assigned as $fee_id){
                            $feeName = \App\Fee::find($fee_id)->fee_type->name;
                            $feeList[$feeName] = \App\Fee::find($fee_id)->amount;
                            $total += $feeList[$feeName];
                        }
                        $sheet->setCellValue('E'.$row, (isset($feeList['Term 1']))?$feeList['Term 1']:'-')
                            ->setCellValue('F'.$row, (isset($feeList['Term 2']))?$feeList['Term 2']:'-')
                            ->setCellValue('G'.$row, (isset($feeList['Term 3']))?$feeList['Term 3']:'-')
                            ->setCellValue('H'.$row, (isset($feeList['Term 4']))?$feeList['Term 4']:'-')
                            ->setCellValue('I'.$row, (isset($feeList['Late Registration']))?$feeList['Late Registration']:'-')
                            ->setCellValue('J'.$row, $total);
                        $sheet->getStyle("E".$row.":J".$row)->applyFromArray($center);
                    }
                        if($res->student->active == '0'){
                            $sheet->getStyle("A".$row.":J".$row)->applyFromArray($inactiveStyle);
                        }
                        $row++;
                        $count++;
                }
                $last_row = $row - 1;
                $last_border = $last_row;
                $sheet->getStyle("B3:B{$last_border}")->applyFromArray($center);
                $sheet->getStyle("D3:D{$last_border}")->applyFromArray($center);
                $borderArray = array(
                    'borders' => array(
                        'allBorders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ),
                    ),
                );
                $sheet->getStyle("A1:J{$last_border}")->applyFromArray($borderArray);
                $sheet->getColumnDimension('A')->setWidth(7);
                $sheet->getColumnDimension('B')->setWidth(5);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(7);
                foreach(range('E','J') as $columnID){
                    $sheet->getColumnDimension($columnID)->setWidth(7);
                }
                // $sheet -> getHeaderFooter()->setDifferentOddEven(false)
				// 			->setOddHeader('&RForm List  - &D');
            }
        ];
    }
}
?>
