<?
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
require_once '../excel/Classes/PHPExcel.php';
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/drivingLog.inc.php";

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$companySeq			= $COMPANY_SEQ;
$year						= $_POST["year"];
$month					= $_POST["month"];


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$db->que = "SELECT * FROM oilCost WHERE companySeq=". $companySeq. " AND year='". $year. "' AND month='". $month. "'";
$db->query();
lib::Plog($db->que);
$oilCost =  $db->getRow();



$db->que = "SELECT o.*, u.name, u.orgUid, de.fullName, du.name AS dutyName FROM ";
$db->que .= " oilCostPaid AS o JOIN user AS u ON o.userUid=u.uid ";
$db->que .= " LEFT JOIN department AS de ON u.departmentSeq=de.seq ";
$db->que .= " LEFT JOIN duty AS du ON u.dutySeq=du.seq ";
$db->que .= " WHERE o.companySeq=". $companySeq. " AND o.year='". $year. "' AND o.month='". $month. "' ORDER BY de.fullName ASC, u.name ASC";
$db->query();



$objPHPExcel = new PHPExcel();

$sheet      = $objPHPExcel->getActiveSheet();
$sheet->getDefaultStyle()->getFont()->setName('맑은 고딕');	//글꼴
$sheetIndex = $objPHPExcel->setActiveSheetIndex(0);


//카운트 
$totalRows = $db->affected_rows() + 2;


//-------------------------------------------------------------------------------
//너비 설정
$sheetIndex->getColumnDimension('A')->setWidth('40');
$sheetIndex->getColumnDimension('B')->setWidth('10');
$sheetIndex->getColumnDimension('C')->setWidth('10');
$sheetIndex->getColumnDimension('D')->setWidth('10');
$sheetIndex->getColumnDimension('E')->setWidth('10');
$sheetIndex->getColumnDimension('F')->setWidth('10');
$sheetIndex->getColumnDimension('G')->setWidth('10');
$sheetIndex->getColumnDimension('H')->setWidth('10');
$sheetIndex->getColumnDimension('I')->setWidth('10');
$sheetIndex->getColumnDimension('J')->setWidth('10');
$sheetIndex->getColumnDimension('K')->setWidth('10');





//-------------------------------------------------------------------------------
// 전체 스타일
// $sheetIndex->duplicateStyleArray(
// 	array(
// 		'font' => array(
// 			'size' => 9
// 		),
// 		'alignment' => array(
// 			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
// 			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
// 		),
// 		'borders' => array(
// 			'allborders' => array(
// 				'style' => PHPExcel_Style_Border::BORDER_THIN
// 			)
// 		)
// 	),
// 	'A1:K'. $totalRows
// );



// -------------------------------------------------------------------------------
// 텍스트 정렬 별도 처리
// $sheetIndex->duplicateStyleArray(
// 	array(
// 		'alignment' => array(
// 			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
// 			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
// 		)
// 	),
// 	'A3:A'. $totalRows
// );
// 
// $sheetIndex->duplicateStyleArray(
// 	array(
// 		'alignment' => array(
// 			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
// 			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
// 		)
// 	),
// 	'G3:K'. $totalRows
// );


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
	'A1:K1'
);

$dataRowCount = $db->affected_rows() + 3;

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
	'A3:K' . $dataRowCount
);



if($oilCost["type"] == "Basic")
{
	$sheetIndex->mergeCells('A1:G1');//셀병합
	$sheetIndex->getStyle("A1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('c0c0c0');

	$sheetIndex->setCellValue('H1', '운행년월');
	$sheetIndex->setCellValue('I1', $year."/". $month);
	$sheetIndex->setCellValue('J1', '1KM');
	$sheetIndex->setCellValue('K1', $oilCost["cost"]. "원");
}
else if($oilCost["type"] == "Mileage")
{
	$oilCost["gasolineCost"] < 1 ?		$oilCost["gasolineCost"] = ""		: $oilCost["gasolineCost"] = $oilCost["gasolineCost"]. "원/L";
	$oilCost["dieselCost"] < 1 ?		$oilCost["dieselCost"] = ""			: $oilCost["dieselCost"] = $oilCost["dieselCost"]. "원/L";
	$oilCost["gasCost"] < 1 ?			$oilCost["gasCost"] = ""				: $oilCost["gasCost"] = $oilCost["gasCost"]. "원/L";
	$oilCost["electricCost"] < 1 ?		$oilCost["electricCost"] = ""		: $oilCost["electricCost"] = $oilCost["electricCost"]. "원/L";

	$sheetIndex->getStyle("A1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('c0c0c0');
	$sheetIndex->setCellValue('B1', '운행년월');
	$sheetIndex->setCellValue('C1', $year."/". $month);
	$sheetIndex->setCellValue('D1', '휘발유');
	$sheetIndex->setCellValue('E1', $oilCost["gasolineCost"]);
	$sheetIndex->setCellValue('F1', '경유');
	$sheetIndex->setCellValue('G1', $oilCost["dieselCost"]);
	$sheetIndex->setCellValue('H1', 'LPG');
	$sheetIndex->setCellValue('I1', $oilCost["gasCost"]);
	$sheetIndex->setCellValue('J1', '전기');
	$sheetIndex->setCellValue('K1', $oilCost["electricCost"]);
}


$sheetIndex->setCellValue('A3','부서');
$sheetIndex->setCellValue('B3','직책');
$sheetIndex->setCellValue('C3','성명');
$sheetIndex->setCellValue('D3','사번');

$sheetIndex->setCellValue('E3','유종');
$sheetIndex->setCellValue('F3','연비 (Km/L)');

$sheetIndex->setCellValue('G3','주행거리 (KM)');
$sheetIndex->setCellValue('H3','정체구간 (KM)');
$sheetIndex->setCellValue('I3','유류비 (원)');
$sheetIndex->setCellValue('J3','추가지급 (원)');
$sheetIndex->setCellValue('K3','합계 (원)');


$index = 4;
while($row = $db->getRow())
{
	$sheetIndex->setCellValue('A'.$index, str_replace("/", " > ", $row["fullName"]));
	$sheetIndex->setCellValue('B'.$index, $row["dutyName"]);
	$sheetIndex->setCellValue('C'.$index, $row["name"]);
	$sheetIndex->setCellValue('D'.$index, $row["orgUid"]);
	
	$sheetIndex->setCellValue('E'.$index, DrivingLog::getOilTypeName($row["oilType"]));
	$sheetIndex->setCellValue('F'.$index, $row["oilMileage"]);

	$sheetIndex->setCellValue('G'.$index, $row["distance"]);
	$sheetIndex->setCellValue('H'.$index, $row["bonusDistance"]);
	$sheetIndex->setCellValue('I'.$index, $row["cost"]);
	$sheetIndex->setCellValue('J'.$index, $row["bonusCost"]);
	$sheetIndex->setCellValue('K'.$index, $row["totalCost"]);

	$index++;
}


//----------------------------------------------------------------------
// Create File
// Redirect output to a client’s web browser (Excel5)
$date = date("YmdHi"); 
$filename = iconv("utf-8","euc-kr", "유류비_".$date.".xls");

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

?>