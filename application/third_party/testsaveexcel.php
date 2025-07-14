<?php
require_once "application/third_party/PHPExcel/PHPExcel.php";
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(‘A1’, ‘Ini di A No 1 ya’)
            ->setCellValue(‘B2’, ‘kalau ini B 2’)
            ->setCellValue(‘C1’, ‘Ini di C1’)
            ->setCellValue(‘D2’, ‘Terakhir di D 2’);
// Redirect output to a client’s web browser (Excel2007)
header(‘Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet’);
header(‘Content-Disposition: attachment;filename=”contoh01.xlsx”‘);
header(‘Cache-Control: max-age=0’);
// If you’re serving to IE 9, then the following may be needed
header(‘Cache-Control: max-age=1’);
// If you’re serving to IE over SSL, then the following may be needed
header (‘Expires: Mon, 26 Jul 1997 05:00:00 GMT’); // Date in the past
header (‘Last-Modified: ‘.gmdate(‘D, d M Y H:i:s’).’ GMT’); // always modified
header (‘Cache-Control: cache, must-revalidate’); // HTTP/1.1
header (‘Pragma: public’); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, ‘Excel2007’);
$objWriter->save(‘php://output’);
unset($objPHPExcel);
?>