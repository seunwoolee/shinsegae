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
	$db->que  = " SELECT * from (";
	$db->que .= " SELECT op.costCode , d.departmentCode , d.code , op.jeogyo , d.parentSeq , departmentSeq, departmentName, '' as employeeNumber , '' as name, sum(totalCost) as totalCost , 'C'as gubun from oilCostPaid as op ";
	$db->que .= " LEFT JOIN department as d on op.departmentSeq = d.seq WHERE op.ymd BETWEEN '".$beginDate. "' AND '".$endDate."'";
	$db->que .= " group by op.departmentSeq  , costCode UNION ALL  ";
	$db->que .= " SELECT op.costCode , d.departmentCode , d.code , op.jeogyo , d.parentSeq , departmentSeq, departmentName, op.employeeNumber , op.name , sum(totalCost) as totalCost , 'D'as gubun from oilCostPaid as op ";
	$db->que .= " LEFT JOIN department as d on op.departmentSeq = d.seq WHERE op.ymd BETWEEN '".$beginDate. "' AND '".$endDate."'";
	$db->que .= " group by op.departmentSeq , op.employeeNumber) as q WHERE (1) ".$where." order by q.departmentSeq , gubun";

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
$sheetIndex->setCellValue('A1','계정코드');
$sheetIndex->setCellValue('B1','귀속부서');
$sheetIndex->setCellValue('C1','귀속사업장');
$sheetIndex->setCellValue('D1','적요');
$sheetIndex->setCellValue('E1','차변');
$sheetIndex->setCellValue('F1','대변');
$sheetIndex->setCellValue('G1','사원번호');
$sheetIndex->setCellValue('H1','지불예정일');


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
	'A1:H'. ($count+1)
);

//-------------------------------------------------------------------------------
//너비 설정
$sheetIndex->getColumnDimension('A')->setWidth('10');
$sheetIndex->getColumnDimension('B')->setWidth('10');
$sheetIndex->getColumnDimension('C')->setWidth('10');
$sheetIndex->getColumnDimension('D')->setWidth('20');
$sheetIndex->getColumnDimension('E')->setWidth('12');
$sheetIndex->getColumnDimension('F')->setWidth('12');
$sheetIndex->getColumnDimension('G')->setWidth('12');
$sheetIndex->getColumnDimension('H')->setWidth('10');
$sheetIndex->getColumnDimension('I')->setWidth('14.5');







//----------------------------------------------------------------------
// List 처리


$time = time()*10;
for($y=0; $y<$count; $y++)
{
	$index = $y+2;
	
	if($obj[$y]["gubun"] == "C")
	{
		$sheetIndex->setCellValue('A'.$index, $obj[$y]["costCode"]);
	}
	else
	{
		$sheetIndex->setCellValue('A'.$index, "-");
	}
	if($obj[$y]["code"] == "제조")
	{
		$sheetIndex->setCellValue('B'.$index, "-");
		$sheetIndex->setCellValue('C'.$index, $obj[$y]["departmentCode"]);
	}
	else if($obj[$y]["code"] == "판관")
	{
		$sheetIndex->setCellValue('B'.$index, $obj[$y]["departmentCode"]);
		$sheetIndex->setCellValue('C'.$index, "-");
	}

	$sheetIndex->setCellValue('D'.$index, $obj[$y]["jeogyo"]);
	if($obj[$y]["gubun"] == "C")
	{
		$sheetIndex->setCellValue('E'.$index, number_format($obj[$y]["totalCost"])."원");
		$sheetIndex->setCellValue('F'.$index, "-");
	}
	else if($obj[$y]["gubun"] == "D")
	{
		$sheetIndex->setCellValue('E'.$index, "-");
		$sheetIndex->setCellValue('F'.$index, number_format($obj[$y]["totalCost"])."원");
	}

	$sheetIndex->setCellValue('G'.$index, $obj[$y]["employeeNumber"]);
	$sheetIndex->setCellValue('H'.$index, "-");
}


//$totalDistance = $lastDistance - $firstDistance;
//$sheetIndex->setCellValue('D'. $bottom2, number_format($totalDistance));









//----------------------------------------------------------------------
// Create File
// Redirect output to a client’s web browser (Excel5)
$date = date("YmdHi"); 
$filename = iconv("utf-8","euc-kr", "정산전표_".$date.".xls");
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