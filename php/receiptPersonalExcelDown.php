<?
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
require_once '../excel/Classes/PHPExcel.php';
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$companySeq			= $COMPANY_SEQ;
$year						= $_GET["year"];
$month					= $_GET["month"];

if(empty($year))
{
	$year = date("Y");
}

if(empty($month))
{
	$month = date("m");
}

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$db->que = "SELECT r.*, u.name, u.orgUid, de.fullName, du.name AS dutyName FROM ";
$db->que .= " receiptPersonalPaid AS r ";
$db->que .= " LEFT JOIN user AS u ON r.userUid=u.uid ";
$db->que .= " LEFT JOIN department AS de ON u.departmentSeq=de.seq ";
$db->que .= " LEFT JOIN duty AS du ON u.dutySeq=du.seq ";
$db->que .= " WHERE r.companySeq=". $companySeq. " AND r.year='". $year. "' AND r.month='". $month. "' ORDER BY de.fullName ASC, du.name ASC, u.name ASC";
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
$sheetIndex->getColumnDimension('B')->setWidth('20');
$sheetIndex->getColumnDimension('C')->setWidth('20');
$sheetIndex->getColumnDimension('D')->setWidth('20');
$sheetIndex->getColumnDimension('E')->setWidth('20');
$sheetIndex->getColumnDimension('F')->setWidth('20');


//-------------------------------------------------------------------------------
// 전체 스타일
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
	'A1:F'. $totalRows
);



//-------------------------------------------------------------------------------
// 텍스트 정렬 별도 처리
$sheetIndex->duplicateStyleArray(
	array(
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	),
	'A3:A'. $totalRows
);

$sheetIndex->duplicateStyleArray(
	array(
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	),
	'F3:F'. $totalRows
);



$index = 3;
$total = 0;
while($row = $db->getRow())
{
	if($row["userCount"] > 1)
	{
		$row["name"] = $row["name"]. " (외 ". ($row["userCount"]-1). "명)";
	}


	$sheetIndex->setCellValue('A'.$index, str_replace("/", " > ", $row["fullName"]));
	$sheetIndex->setCellValue('B'.$index, $row["dutyName"]);
	$sheetIndex->setCellValue('C'.$index, $row["name"]);
	$sheetIndex->setCellValue('D'.$index, $row["orgUid"]);
	$sheetIndex->setCellValue('E'.$index, $row["receiptCount"]);
	$sheetIndex->setCellValue('F'.$index, $row["amount"]);
	
	$total += $row["amount"];
	$index++;
}



$sheetIndex->mergeCells('A1:B1');//셀병합
$sheetIndex->getStyle("A1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('c0c0c0');


$sheetIndex->setCellValue('C1', '지출년월');
$sheetIndex->setCellValue('D1', $year."/". $month);
$sheetIndex->setCellValue('E1', '합계');
$sheetIndex->setCellValue('F1', number_format($total). " 원");

$sheetIndex->setCellValue('A2','부서');
$sheetIndex->setCellValue('B2','직책');
$sheetIndex->setCellValue('C2','성명');
$sheetIndex->setCellValue('D2','사번');
$sheetIndex->setCellValue('E2','건수');
$sheetIndex->setCellValue('F2','금액 (원)');


//----------------------------------------------------------------------
// Create File
// Redirect output to a client’s web browser (Excel5)
$date = date("YmdHi"); 
$filename = iconv("utf-8","euc-kr", "월별개인여비_".$date.".xls");

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