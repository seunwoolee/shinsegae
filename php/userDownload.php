<?php
require_once '../excel/Classes/PHPExcel.php';
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/user.inc.php";
include "../inc/drivingLog.inc.php";
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();



// $db->que = "SELECT * FROM user WHERE companySeq='157'";
// $db->query();
// $obj = $db->getRows();

$db->que = "SELECT u.*, de.name AS departmentName, de.fullName, du.name AS dutyName FROM user AS u";
$db->que .= " LEFT JOIN duty AS du ON u.dutySeq=du.seq ";
$db->que .= " LEFT JOIN department AS de ON u.departmentSeq=de.seq ";
$db->que .= " WHERE u.companySeq='157'";

$db->query();
$obj = $db->getRows();

$objPHPExcel = new PHPExcel();

$sheet      = $objPHPExcel->getActiveSheet();
$sheet->getDefaultStyle()->getFont()->setName('맑은 고딕');	//글꼴
$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);


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
	'A1'
);



//-------------------------------------------------------------------------------
//너비 설정
$sheetIndex->getColumnDimension('A')->setWidth('14');
$sheetIndex->getColumnDimension('B')->setWidth('30');
$sheetIndex->getColumnDimension('C')->setWidth('17');
$sheetIndex->getColumnDimension('D')->setWidth('14');
$sheetIndex->getColumnDimension('E')->setWidth('14');
$sheetIndex->getColumnDimension('F')->setWidth('14');
$sheetIndex->getColumnDimension('G')->setWidth('14');
$sheetIndex->getColumnDimension('H')->setWidth('20');


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

$sheetIndex->setCellValue('A1','순서');
$sheetIndex->setCellValue('B1','상태');
$sheetIndex->setCellValue('C1','부서');
$sheetIndex->setCellValue('D1','직위');
$sheetIndex->setCellValue('E1','성명');
$sheetIndex->setCellValue('F1','사번');
$sheetIndex->setCellValue('G1','차종');
$sheetIndex->setCellValue('H1','유종');
$sheetIndex->setCellValue('I1','연비');
$sheetIndex->setCellValue('J1','등록일');

//카운트 
$listCount = count($obj);
$totalRows = $listCount;

for($y=0; $y<$listCount; $y++){
	
	$index = $y+1;
	$indexPosition =  $index+1;
	$enabledText = User::getEnabled($obj[$y]["enabled"]);

	
	$car = $obj[$y]["carModel"]. " ". $obj[$y]["carNumber"];
	$createTime = strToTime($obj[$y]["createTime"]);
	
	
	$sheetIndex->setCellValue('A'.$indexPosition, $index-1);
	$sheetIndex->setCellValue('B'.$indexPosition, $enabledText);
	$sheetIndex->setCellValue('C'.$indexPosition, $obj[$y]["fullName"]);
	$sheetIndex->setCellValue('D'.$indexPosition, $obj[$y]["dutyName"]);
	$sheetIndex->setCellValue('E'.$indexPosition, $obj[$y]["name"]);
	$sheetIndex->setCellValue('F'.$indexPosition, $obj[$y]["orgUid"]);
	$sheetIndex->setCellValue('G'.$indexPosition, $car);
	$sheetIndex->setCellValue('H'.$indexPosition, DrivingLog::getOilTypeName($obj[$y]["oilType"]));
	$sheetIndex->setCellValue('I'.$indexPosition, $obj[$y]["oilMileage"]);
	$sheetIndex->setCellValue('J'.$indexPosition, date("Y.m.d", $createTime)) ;			

}




//----------------------------------------------------------------------
// Create File
// Redirect output to a client’s web browser (Excel5)
$date = date("YmdHi"); 
$filename = iconv("utf-8","euc-kr", "차량운행일지_".$date.".xls");
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename='. $filename);
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

exit;
?>