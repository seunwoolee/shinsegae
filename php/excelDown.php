<?php
require_once '../excel/Classes/PHPExcel.php';
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";



//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

define("PURPOSE_GENERAL", "a");
define("PURPOSE_COMMUTE", "e");
define("PURPOSE_ETC", "g");


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();


$objPHPExcel = new PHPExcel();

$sheet      = $objPHPExcel->getActiveSheet();
$sheet->getDefaultStyle()->getFont()->setName('맑은 고딕');	//글꼴
$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);


//카운트 
$listCount = count($obj);
$totalRows = $listCount+13;


//-------------------------------------------------------------------------------
//너비 설정
$sheetIndex->getColumnDimension('A')->setWidth('14');
$sheetIndex->getColumnDimension('B')->setWidth('10.6');
$sheetIndex->getColumnDimension('C')->setWidth('10.6');
$sheetIndex->getColumnDimension('D')->setWidth('12');
$sheetIndex->getColumnDimension('E')->setWidth('12');
$sheetIndex->getColumnDimension('F')->setWidth('12');
$sheetIndex->getColumnDimension('G')->setWidth('15.2');
$sheetIndex->getColumnDimension('H')->setWidth('15.2');
$sheetIndex->getColumnDimension('I')->setWidth('14.5');



//-------------------------------------------------------------------------------
//높이 설정
$sheetIndex->getRowDimension('1')->setRowHeight(22);
$sheetIndex->getRowDimension('2')->setRowHeight(22);
$sheetIndex->getRowDimension('3')->setRowHeight(22);
$sheetIndex->getRowDimension('4')->setRowHeight(22);
$sheetIndex->getRowDimension('5')->setRowHeight(22);
$sheetIndex->getRowDimension('6')->setRowHeight(22);
$sheetIndex->getRowDimension('7')->setRowHeight(22);
$sheetIndex->getRowDimension('8')->setRowHeight(22);
$sheetIndex->getRowDimension('9')->setRowHeight(22);
$sheetIndex->getRowDimension('10')->setRowHeight(22);
$sheetIndex->getRowDimension('11')->setRowHeight(44);


for($i=12; $i<$listCount+12; $i++)
{
	$sheetIndex->getRowDimension($i)->setRowHeight(22);
}

$sheetIndex->getRowDimension($totalRows-1)->setRowHeight(33);
$sheetIndex->getRowDimension($totalRows)->setRowHeight(33);




//-------------------------------------------------------------------------------
// 상단 border 설정
$sheetIndex->duplicateStyleArray(
	array(
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	),
	'A1:I2'
);

$sheetIndex->duplicateStyleArray(
	array(
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	),
	'A5:D6'
);


//-------------------------------------------------------------------------------
// 상단 스타일 적용
$sheetIndex->duplicateStyleArray(
	array(
		'font' => array(
			'size' => 11
		),
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	),
	'A1:I11'
);


//-------------------------------------------------------------------------------
// 하단 스타일 적용
$sheetIndex->duplicateStyleArray(
	array(
		'font' => array(
			'size' => 9
		),
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	),
	'A'. ($listCount+12). ':I'. ($listCount+13)
);


//-------------------------------------------------------------------------------
// 운행기록 데이터 영역 스타일 적용
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
	'A9:I'. ($listCount+13)
);




$sheetIndex->getStyle("E1")->getFont()->setSize(18)->setBold(true);
$sheetIndex->getStyle("A4")->getFont()->setSize(11)->setBold(true);
$sheetIndex->getStyle("A8")->getFont()->setSize(11)->setBold(true);
$sheetIndex->getStyle("A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$sheetIndex->getStyle("A8")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);




//-------------------------------------------------------------------------------
// 셀병합 및 텍스트 입력


$corporateNumber = substr($company['corporateNumber'],0,3). "-". substr($company['corporateNumber'],3,2). "-". substr($company['corporateNumber'],5,5);
$sheetIndex->setCellValue('I1', $company["name"]);
$sheetIndex->setCellValue('I2', $corporateNumber);
$sheetIndex->setCellValue('A6', $user["carModel"]);
$sheetIndex->setCellValue('C6', $user["carNumber"]);


$sheetIndex->mergeCells('C1:D2');
if(empty($beginDate) || empty($endDate))
{
	$sheetIndex->setCellValue('C1','.  .  .  ~  .  .  .');
}
else
{
	$sheetIndex->setCellValue('C1', str_replace("-", ".", $beginDate). " ~ ". str_replace("-", ".", $endDate));
}


$sheetIndex->mergeCells('A1:B2');
$sheetIndex->setCellValue('A1','과   세   기   간');




$sheetIndex->mergeCells('E1:G2');
$sheetIndex->setCellValue('E1','업무용승용차 운행기록부');
$sheetIndex->setCellValue('H1','상     호     명');
$sheetIndex->setCellValue('H2','사업자등록번호');

$sheetIndex->mergeCells('A4:B4');
$sheetIndex->mergeCells('A5:B5');
$sheetIndex->mergeCells('C5:D5');
$sheetIndex->mergeCells('A6:B6');
$sheetIndex->mergeCells('C6:D6');
$sheetIndex->setCellValue('A4','기본정보');
$sheetIndex->setCellValue('A5','차       종');
$sheetIndex->setCellValue('C5','자동차등록번호');


$sheetIndex->mergeCells('A8:C8');
$sheetIndex->mergeCells('A9:A11');
$sheetIndex->mergeCells('B9:C10');
$sheetIndex->mergeCells('D9:I9');
$sheetIndex->mergeCells('D10:D11');
$sheetIndex->mergeCells('E10:E11');
$sheetIndex->mergeCells('F10:F11');
$sheetIndex->mergeCells('G10:H10');
$sheetIndex->mergeCells('I10:I11');


$sheetIndex->setCellValue('A8','업무용 사용비율 계산');
$sheetIndex->setCellValue('A9','사용일자(요일)');
$sheetIndex->setCellValue('B9','사용자');
$sheetIndex->setCellValue('B11','부서');
$sheetIndex->setCellValue('C11','성명');

$sheetIndex->setCellValue('D9','운    행       내    역');
$sheetIndex->setCellValue('D10','주행 전 계기판의거리(Km)');
$sheetIndex->setCellValue('E10','주행 후 계기판의거리(Km)');
$sheetIndex->setCellValue('F10','주행거리(Km)');
$sheetIndex->setCellValue('G10','업무용 사용거리(Km)');
$sheetIndex->setCellValue('G11','출․퇴근용(Km)');
$sheetIndex->setCellValue('H11','일반 업무용(Km)');
$sheetIndex->setCellValue('I10','비    고');

$bottom1 = $totalRows - 1;
$bottom2 = $totalRows;
$sheetIndex->mergeCells("D". $bottom1. ":F". $bottom1);
$sheetIndex->mergeCells("G". $bottom1. ":H". $bottom1);
$sheetIndex->mergeCells("D". $bottom2. ":F". $bottom2);
$sheetIndex->mergeCells("G". $bottom2. ":H". $bottom2);
$sheetIndex->mergeCells("A". $bottom1. ":C". $bottom2);
$sheetIndex->getStyle("A".$bottom1)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('c0c0c0');

$sheetIndex->setCellValue('D'. $bottom1,'과세기간 총주행 거리(Km)');
$sheetIndex->setCellValue('G'. $bottom1,'과세기간 업무용 사용거리(Km)');
$sheetIndex->setCellValue('I'. $bottom1,'업무용 사용비율');


//----------------------------------------------------------------------
// List 처리

$weeks = ["일","월","화","수","목","금","토"]; 
$businessDistance = 0;

$time = time()*10;
for($y=0; $y<$listCount; $y++)
{
	$index = $y+12;
	
	$startDateTime = strtotime($obj[$y]["startDate"]);
	$weekIndex = date("w", $startDateTime);
	$startDate = date("Y-m-d", $startDateTime). "(". $weeks[$weekIndex]. ")";
	$sheetIndex->setCellValue('A'.$index, $startDate);

	$sheetIndex->setCellValue('B'.$index, $obj[$y]["departmentName"]);
	$sheetIndex->setCellValue('C'.$index, $obj[$y]["name"]);
	$sheetIndex->setCellValue('D'.$index, number_format($obj[$y]["startDistance"]));
	$sheetIndex->setCellValue('E'.$index, number_format($obj[$y]["stopDistance"]));
	$sheetIndex->setCellValue('F'.$index, number_format($obj[$y]["distance"]));

	if($obj[$y]["purpose"] == PURPOSE_COMMUTE)
	{
		$sheetIndex->setCellValue('G'.$index, number_format($obj[$y]["distance"]));
	}
	else if($obj[$y]["purpose"] == PURPOSE_GENERAL) 
	{
		$sheetIndex->setCellValue('H'.$index, number_format($obj[$y]["distance"]));
	}

	if($obj[$y]["purpose"] == PURPOSE_COMMUTE || $obj[$y]["purpose"] == PURPOSE_GENERAL)
	{
		$businessDistance += $obj[$y]["distance"];
	}

	$sheetIndex->setCellValue('I'.$index, $obj[$y]["bigo"]);
}



$firstDistance = $obj[0]["startDistance"];
$lastDistance = $obj[$listCount-1]["stopDistance"];
$totalDistance = $lastDistance - $firstDistance;

if($businessDistance > 0 && $totalDistance > 0)
{
	$percent = $businessDistance / $totalDistance * 100;
}

$sheetIndex->setCellValue('D'. $bottom2, number_format($totalDistance));
$sheetIndex->setCellValue('G'. $bottom2, number_format($businessDistance));
$sheetIndex->setCellValue('I'. $bottom2, number_format($percent,2). '%');










//----------------------------------------------------------------------
// Create File
// Redirect output to a client’s web browser (Excel5)
$date = date("YmdHi"); 
$filename = iconv("utf-8","euc-kr", "차량운행일지_".$date.".xlsx");
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

// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');


exit;
?>