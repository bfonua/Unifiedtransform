<?php 

namespace App\Exports;

use App\Section;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class financePaymentListExport2 implements WithEvents, WithTitle
{
    // private $form_id;

    protected $userService;

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
                        'color' => array('argb' => 'FFEEBABA')
                    ),
                );
                $inactiveStyle = array(
                    'fill' => array(
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => array('argb' => 'FF909090')
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
                $section_id = $this->section_id;
                $students = \App\StudentInfo::with('student')
                ->where('session', now()->year)
                ->where('form_id', $section_id)
                ->orderBy('form_num', 'asc')->get();
                $feeTypes = \App\FeeType::withCount(['fees' => function($q) {
                    $q->where('session', now()->year);
                }])
                    ->where('fee_types.active', 1)
                    ->get();
                $row = 3;
                $count = 1;

                foreach($students as $student){
                    // Skip records with missing student or house relationships
                    if(!$student->student || !$student->house){
                        continue;
                    }
                    
                    $class_num = $student->form_num;
                    // move to next class number if the current number is not assigned to any student
                    while($count < $class_num){
                        $sheet->setCellValue('B'.$row, $count);
                        $count++;
                        $row++;
                    }
                    // name string incl. role as prefect
                    $name = $this->split_name($student->student->given_name)[0]." ". $this->split_name($student->student->given_name)[1]." ".$student->student->lst_name;
                        if($student->group == "Head Prefect"){
                            $name .= ' (HP)';
                        } elseif(ucfirst($student->group) == "Prefect"){
                            $name .= " (P)";
                        }
                        // paste student into onto sheet
                        $sheet->setCellValue('A'.$row, $student->student_code)
                                ->setCellValue('B'.$row, $student->form_num)
                                ->setCellValue('C'.$row, $name)
                                ->setCellValue('D'.$row, $student->house->house_abbrv);
                                
                        // if not assigned then all payments should be nil
                        if($student->assigned == "0"){
                            $sheet->setCellValue('E'.$row, 'NA');
                            $sheet->setCellValue('F'.$row, 'NA');
                            $sheet->setCellValue('G'.$row, 'NA');
                            $sheet->setCellValue('H'.$row, 'NA');
                            $sheet->setCellValue('I'.$row, 'NA');
                            $sheet->setCellValue('J'.$row, 'NA');
                            $sheet->getStyle("E".$row.":J".$row)->applyFromArray($unassigned_style);
                        } else{
                            $payment = [];
                            $totalPaid = 0;
                            foreach($feeTypes as $type){
                                $amountPaid = (!empty($student->student->fee_types_paid->where('id', $type->id)->first()))? $student->student->fee_types_paid->where('id', $type->id)->first()->aggregate : 0;
                                $payment[$type->name] = $amountPaid;
                                $totalPaid += $amountPaid;
                            }
                            $sheet
                                ->setCellValue('E'.$row, ($payment['Term 1']==0)?"-":$payment['Term 1'])
                                ->setCellValue('F'.$row, ($payment['Term 2']==0)?"-":$payment['Term 2'])
                                ->setCellValue('G'.$row, ($payment['Term 3']==0)?"-":$payment['Term 3'])
                                ->setCellValue('H'.$row, ($payment['Term 4']==0)?"-":$payment['Term 4'])
                                ->setCellValue('I'.$row, ($payment['Late Registration']==0)?"-":$payment['Late Registration'])
                                ->setCellValue('J'.$row, ($totalPaid == 0)? '-': $totalPaid);
                            $sheet->getStyle("E".$row.":J".$row)->applyFromArray($center);
                        }
                    if($student->student->active == '0'){
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
            }
        ];
    }
}
?>
