<?php
require_once '../excel/Classes/PHPExcel.php';
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/receipt.inc.php";



//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$companySeq							= $COMPANY_SEQ;
$year										= $_POST["year"];
$departmentSeq						= $_POST["departmentSeq"];
$receiptProjectSeq					= $_POST["receiptProjectSeq"];
$receiptAccountCodeSeq			= $_POST["receiptAccountCodeSeq"];
$receiptAccountCodeDetailSeq	= $_POST["receiptAccountCodeDetailSeq"];
$card										= $_POST["card"];
$state									= $_POST["state"];
$beginDate								= $_POST["beginDate"];
$endDate								= $_POST["endDate"];

//5만개 이하로만 다운로드 가능;
$MAX_DOWNLOAD_COUNT			= 50000;



//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
if(empty($departmentSeq) == false)						$WHERE .= " AND r.departmentSeq=". $departmentSeq;
if(empty($receiptProjectSeq) == false)						$WHERE .= " AND r.receiptProjectSeq=". $receiptProjectSeq;
if(empty($receiptAccountCodeSeq) == false)				$WHERE .= " AND r.receiptAccountCodeSeq=". $receiptAccountCodeSeq;
if(empty($receiptAccountCodeDetailSeq) == false)		$WHERE .= " AND r.receiptAccountCodeDetailSeq=". $receiptAccountCodeDetailSeq;
if(empty($card) == false)										$WHERE .= " AND r.card='". $card. "'";
if(empty($state) == false)										$WHERE .= " AND r.state='". $state. "'";
if(empty($beginDate) == false)								$WHERE .= " AND r.useDate>='". $beginDate. "'";
if(empty($endDate) == false)									$WHERE .= " AND r.useDate<='". $endDate. "'";



$db->que = "SELECT r.*, u.orgUid, p.name AS projectName, p.code AS projectCode  FROM ";
$db->que .= " receiptView AS r ";
$db->que .= " LEFT OUTER JOIN user AS u ON r.userUid=u.uid ";
$db->que .= " LEFT OUTER JOIN receiptProject AS p ON r.receiptProjectSeq=p.seq ";
$db->que .= " WHERE r.companySeq=". $companySeq. $WHERE. " ORDER BY r.useDate DESC, r.createTime DESC";
$db->query();
$obj = $db->getRows();
$count = count($obj);


if($count > $MAX_DOWNLOAD_COUNT)
{
	LIB::Alert("다운로드는 최대 ". $MAX_DOWNLOAD_COUNT. "건 까지만 가능 합니다. 기간을 작게 설정해 주세요.", "-1");
	exit;
}


if($count>0)
{
	$rows .= "부서,";
	$rows .= "직책,";
	$rows .= "성명,";
	$rows .= "사번,";
	$rows .= "차량,";
	$rows .= "지출,";
	$rows .= "사용일자,";
	$rows .= "과목코드,";
	$rows .= "과목명,";
	$rows .= "세목,";
	$rows .= "상태,";
	$rows .= "메모,";
	$rows .= "프로젝트명,";
	$rows .= "프로젝트 코드,";
	$rows .= "금액\r\n";


	$objCount = count($obj);
	for($y=0; $y<$objCount; $y++)
	{
		$index = $y+2;
		
		$rows .= str_replace(",", "", $obj[$y]["departmentName"]). ",";
		$rows .= str_replace(",", "", $obj[$y]["dutyName"]). ",";
		$rows .= str_replace(",", "", $obj[$y]["name"]). ",";
		$rows .= $obj[$y]["orgUid"]. ",";
		$rows .= $obj[$y]["carModel"]. " ". $obj[$y]["carNumber"]. ",";
		$rows .= Receipt::getCardText($obj[$y]["card"]). ",";
		$rows .= $obj[$y]["useDate"]. ",";
		$rows .= $obj[$y]["receiptAccountCodeCode"]. ",";
		$rows .= $obj[$y]["receiptAccountCodeName"]. ",";
		$rows .= str_replace(",", "", $obj[$y]["receiptAccountCodeDetailName"]). ",";
		$rows .= Receipt::getStateText($obj[$y]["state"]). ",";
		$rows .= str_replace(",","",str_replace("\r\n", "", $obj[$y]["memo"])). ",";
		$rows .= $obj[$y]["projectName"]. ",";
		$rows .= $obj[$y]["projectCode"]. ",";
		$rows .= $obj[$y]["amount"]. "\r\n";
	}


	// CSV 파일로 저장합니다. 파일명을 날짜를 붙여 생성합니다. 
	$date = date("YmdHi"); 
	$filename = iconv("utf-8","euc-kr", "영수증_".$date.".csv"); 

	header( "Content-type: text/csv; charset=EUC-KR" ); 
	header("Content-Disposition: attachment; filename=". $filename); 
	header( "Content-Description: PHP4 Generated Data" ); 
	echo iconv("UTF-8","CP949//TRANSLIT", $rows); 

}
else
{
	LIB::Alert("다운로드할 영수증이 없습니다.", "-1");
}

$db->close();
exit;
?>