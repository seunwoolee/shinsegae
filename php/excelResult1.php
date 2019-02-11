<?php
require_once '../excel/Classes/PHPExcel.php';
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/drivingLog.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$beginDate							= $_GET["beginDate"];
$endDate							= $_GET["endDate"];
$lowerDep							= $_GET["lowerDep"];
$upperDep							= $_GET["upperDep"];

//5만개 이하로만 다운로드 가능;
$MAX_DOWNLOAD_COUNT		= 50000;


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
if($lowerDep > 0 )
{
	$where = " AND departmentSeq =". $lowerDep;
}

if($upperDep > 0 )
{
	$where .= " AND d.parentSeq =". $upperDep;
}

if(empty($beginDate) == false)
{
	$where .= " AND ymd >= '". $beginDate. "' ";
}

if(empty($endDate) == false)
{
	$where .= " AND ymd <= '". $endDate. "' ";
}

// List 쿼리
if(empty($beginDate) == false && empty($endDate) == false)
{
	$db->que = " SELECT d.parentSeq , departmentName , costCode , d.code , op.name , employeeNumber , SUM(distance) as distance , SUM(COST) as oilCost , SUM(receiptCost) as receiptCost, sum(totalCost)  as totalCost FROM oilCostPaid as op LEFT JOIN department as d ON op.departmentSeq = d.seq  WHERE (1) ".$where." GROUP BY d.seq , employeeNumber , costCode order by d.parentSeq ";
	$db->query();
	$obj = $db->getRows();
	$count = count($obj);
}

// 부서 및 승인권자
if($upperDep > 0 && $lowerDep > 0)
{
	$db->que = " SELECT fullName , approverName FROM departmentView2 WHERE seq = ".$lowerDep;
	$db->query();	
	$row = $db->getRow();
	$department = $row["fullName"];
	$departmentApprover = $row["approverName"];
}
else
{
	$department = "전체";
	$departmentApprover = "전체";
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

$sheetIndex->getStyle("A2")->getFont()->setBold(true);
$sheetIndex->getStyle("B2")->getFont()->setBold(true);
$sheetIndex->getStyle("C2")->getFont()->setBold(true);
$sheetIndex->getStyle("D2")->getFont()->setBold(true);
$sheetIndex->getStyle("E2")->getFont()->setBold(true);
$sheetIndex->getStyle("F2")->getFont()->setBold(true);
$sheetIndex->getStyle("G2")->getFont()->setBold(true);
$sheetIndex->getStyle("H2")->getFont()->setBold(true);
$sheetIndex->getStyle("I2")->getFont()->setBold(true);
$sheetIndex->getStyle("J2")->getFont()->setBold(true);

$sheetIndex->setCellValue('A1','부서');
$sheetIndex->setCellValue('C1','승인권자');
$sheetIndex->setCellValue('E1','건수');
$sheetIndex->setCellValue('G1','총 정산금액');

$sheetIndex->setCellValue('A2','NO');
$sheetIndex->setCellValue('B2','소속부서');
$sheetIndex->setCellValue('C2','계정코드');
$sheetIndex->setCellValue('D2','제조/판관');
$sheetIndex->setCellValue('E2','성명');
$sheetIndex->setCellValue('F2','사원번호');
$sheetIndex->setCellValue('G2','주행거리(km)');
$sheetIndex->setCellValue('H2','유류비(원)');
$sheetIndex->setCellValue('I2','지출비용(원)');
$sheetIndex->setCellValue('J2','총 정산금액(원)');


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
	'A1:J'. ($count+2)
);

//-------------------------------------------------------------------------------
//너비 설정
$sheetIndex->getColumnDimension('A')->setWidth('20');
$sheetIndex->getColumnDimension('B')->setWidth('20');
$sheetIndex->getColumnDimension('C')->setWidth('10');
$sheetIndex->getColumnDimension('D')->setWidth('20');
$sheetIndex->getColumnDimension('E')->setWidth('10');
$sheetIndex->getColumnDimension('F')->setWidth('10');
$sheetIndex->getColumnDimension('G')->setWidth('10');
$sheetIndex->getColumnDimension('H')->setWidth('10');
$sheetIndex->getColumnDimension('I')->setWidth('10');
$sheetIndex->getColumnDimension('J')->setWidth('20');



//----------------------------------------------------------------------
// List 처리


$time = time()*10;
$totalCost = 0;
$no = 1;
for($y=0; $y<$count; $y++)
{
	$index = $y+3;
	
	$sheetIndex->setCellValue('A'.$index, $no);
	$sheetIndex->setCellValue('B'.$index, $obj[$y]["departmentName"]);
	$sheetIndex->setCellValue('C'.$index, $obj[$y]["costCode"]);
	$sheetIndex->setCellValue('D'.$index, $obj[$y]["code"]);
	$sheetIndex->setCellValue('E'.$index, $obj[$y]["name"]);
	$sheetIndex->setCellValue('F'.$index, $obj[$y]["employeeNumber"]);
	$sheetIndex->setCellValue('G'.$index, $obj[$y]["distance"]."km");
	$sheetIndex->setCellValue('H'.$index, $obj[$y]["oilCost"]."원");
	$sheetIndex->setCellValue('I'.$index, $obj[$y]["receiptCost"]."원");
	$sheetIndex->setCellValue('J'.$index, $obj[$y]["totalCost"]."원");
	$totalCost = $totalCost + $obj[$y]["totalCost"];
	$no++;
}

$sheetIndex->setCellValue('B1',$department);
$sheetIndex->setCellValue('D1',$departmentApprover);
$sheetIndex->setCellValue('F1',$count."건");
$sheetIndex->setCellValue('H1',$totalCost."원");

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












//////////////////////기존/////////////////////////////
/*
if($count>0)
{
	$rows .= "No,";
	$rows .= "소속부서,";
	$rows .= "계정코드,";
	$rows .= "판관,";
	$rows .= "성명,";
	$rows .= "사원번호,";
	$rows .= "주행거리(km),";
	$rows .= "유류비(원),";
	$rows .= "지출비용(원),";
	$rows .= "총 정산금액(원)\r\n";





	//----------------------------------------------------------------------
	// List 처리
	//$weeks = ["일","월","화","수","목","금","토"];

	$objCount = count($obj);
	for($y=0; $y<$objCount; $y++)
	{
		$index = $y+1;
		

		$startDateTime = strtotime($obj[$y]["startDate"]);
		
		$rows .= $index. ",";
		$rows .= $obj[$y]["departmentName"]. ",";
		$rows .= $obj[$y]["costCode"]. ",";
		$rows .= $obj[$y]["code"]. ",";
		$rows .= $obj[$y]["name"]. ",";
		$rows .= $obj[$y]["employeeNumber"]. ",";
		$rows .= $obj[$y]["distance"]. ",";
		$rows .= $obj[$y]["oilCost"]. ",";
		$rows .= $obj[$y]["receiptCost"]. ",";
		$rows .= $obj[$y]["totalCost"]. "\r\n";
}



	// CSV 파일로 저장합니다. 파일명을 날짜를 붙여 생성합니다. 
	$date = date("YmdHi"); 
	$filename = iconv("utf-8","euc-kr", "정산기본보기_".$date.".csv"); 

	header( "Content-type: text/csv; charset=EUC-KR" ); 
	header("Content-Disposition: attachment; filename=". $filename); 
	header( "Content-Description: PHP4 Generated Data" ); 
	echo iconv("UTF-8","CP949//TRANSLIT", $rows); 

}
else
{
	LIB::Alert("정산기록이 없습니다.", "-1");
}

$db->close();
exit;
*/
?>