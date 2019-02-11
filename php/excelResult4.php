<?php
require_once '../excel/Classes/PHPExcel.php';
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";



//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$beginDate							= $_GET["beginDate"];
$endDate							= $_GET["endDate"];
$lowerDep							= $_GET["lowerDep"];
$upperDep							= $_GET["upperDep"];

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

if(empty($beginDate) == false)
{
	$where .= " AND ymd >= '". $beginDate. "' ";
}

if(empty($endDate) == false)
{
	$where .= " AND ymd <= '". $endDate. "' ";
}

if($upperDep > 0 )
{
	$where .= " AND parentSeq =". $upperDep;
}

if($lowerDep > 0 )
{
	$where .= " AND departmentSeq =". $lowerDep;
}


if(empty($beginDate) == false && empty($endDate) == false)
{
	$db->que = " SELECT departmentName , costCode , costName , d.code , op.employeeNumber , op.name , sum(totalCost) as totalCost FROM oilCostPaid as op  LEFT JOIN department as d ON op.departmentSeq = d.seq  WHERE (1) ".$where." GROUP BY employeeNumber order by d.parentSeq ,d.sort ";
	$db->query();
	$obj = $db->getRows();
	$count = count($obj);
}

//-------------------------------------------------------------------------------
//php Excel 정의
$objPHPExcel = new PHPExcel();
$sheet      = $objPHPExcel->getActiveSheet();
$sheet->getDefaultStyle()->getFont()->setName('맑은 고딕');	//글꼴
$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

//-------------------------------------------------------------------------------
//첫줄
$sheetIndex->getStyle("A1")->getFont()->setBold(true);
$sheetIndex->getStyle("B1")->getFont()->setBold(true);
$sheetIndex->getStyle("C1")->getFont()->setBold(true);
$sheetIndex->getStyle("D1")->getFont()->setBold(true);
$sheetIndex->getStyle("E1")->getFont()->setBold(true);
$sheetIndex->getStyle("F1")->getFont()->setBold(true);
$sheetIndex->getStyle("G1")->getFont()->setBold(true);
$sheetIndex->getStyle("H1")->getFont()->setBold(true);
$sheetIndex->setCellValue('A1','이름');
$sheetIndex->setCellValue('B1','사번');
$sheetIndex->setCellValue('C1','부서명');
$sheetIndex->setCellValue('D1','금액');
$sheetIndex->setCellValue('E1','이름');
$sheetIndex->setCellValue('F1','사번');
$sheetIndex->setCellValue('G1','부서명');
$sheetIndex->setCellValue('H1','금액');

//-------------------------------------------------------------------------------
// 가운데 정렬
$sheetIndex->duplicateStyleArray(
	array(
		'font' => array(
			'size' => 9
		),
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
		),
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	),
	'A1:H'. ($count)
);

//-------------------------------------------------------------------------------
//너비 설정
$sheetIndex->getColumnDimension('A')->setWidth('10');
$sheetIndex->getColumnDimension('B')->setWidth('10');
$sheetIndex->getColumnDimension('C')->setWidth('10');
$sheetIndex->getColumnDimension('D')->setWidth('10');
$sheetIndex->getColumnDimension('E')->setWidth('10');
$sheetIndex->getColumnDimension('F')->setWidth('10');
$sheetIndex->getColumnDimension('G')->setWidth('10');
$sheetIndex->getColumnDimension('H')->setWidth('10');







//----------------------------------------------------------------------
// List 처리

$index = 2;
$time = time()*10;
$totalCost = 0;
for($y=0; $y<$count; $y++)
{
	if(($y % 2) == 0)
	{
		$sheetIndex->setCellValue('A'.$index, $obj[$y]["name"]);
		$sheetIndex->setCellValue('B'.$index, $obj[$y]["employeeNumber"]);		
		$sheetIndex->setCellValue('C'.$index, $obj[$y]["departmentName"]);
		$sheetIndex->setCellValue('D'.$index, number_format($obj[$y]["totalCost"])."원");
		$totalCost = $totalCost + $obj[$y]["totalCost"];
	}
	else
	{
		$sheetIndex->setCellValue('E'.$index, $obj[$y]["name"]);
		$sheetIndex->setCellValue('F'.$index, $obj[$y]["employeeNumber"]);		
		$sheetIndex->setCellValue('G'.$index, $obj[$y]["departmentName"]);
		$sheetIndex->setCellValue('H'.$index, number_format($obj[$y]["totalCost"])."원");
		$totalCost = $totalCost + $obj[$y]["totalCost"];
		$index++;
	}
}
$sheetIndex->getStyle("G". $count)->getFont()->setBold(true);
$sheetIndex->getStyle("H". $count)->getFont()->setBold(true);
$sheetIndex->setCellValue('G'. $count, "총 합계");
$sheetIndex->setCellValue('H'. $count, number_format($totalCost)."원");









//----------------------------------------------------------------------
// Create File
// Redirect output to a client’s web browser (Excel5)
$date = date("YmdHi"); 
$filename = iconv("utf-8","euc-kr", "인별집계_".$date.".xls");
// header('Content-Type: application/vnd.ms-excel');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='. $filename);
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
ob_end_clean();
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');


exit;
?>