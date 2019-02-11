<?php
require_once '../excel/Classes/PHPExcel.php';
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/drivingLog.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
//$uid								= $_POST["uid"];
$beginDate							= $_POST["beginDate"];
$endDate							= $_POST["endDate"];
//$companySeq						= $COMPANY_SEQ;
$purpose							= $_POST["purpose"];


//5만개 이하로만 다운로드 가능;
$MAX_DOWNLOAD_COUNT		= 30000;


define("PURPOSE_GENERAL", "a");
define("PURPOSE_COMMUTE", "e");
define("PURPOSE_ETC", "g");


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$where = "WHERE d.startDate >= '". $beginDate. "' AND d.startDate <= '". $endDate. "' ";


$db->que = "SELECT count(*)  FROM drivingLog AS d ". $where;
$db->query();
LIB::Plog($db->que);
if($db->getOne() > $MAX_DOWNLOAD_COUNT)
{
	LIB::Alert("운행기록 다운로드는 최대 ". $MAX_DOWNLOAD_COUNT. "건 까지만 가능 합니다. 기간을 작게 설정해 주세요.", "-1");
	exit;
}

$db->que = " SELECT d.*, u.employeeNumber ,u.oilType, p.purposeName, p.purposeCode FROM (drivingLog AS d LEFT JOIN user AS u ON d.employeeNumber=u.employeeNumber) ";
$db->que .= " LEFT OUTER JOIN purpose AS p ON (d.purpose=p.purposeType)  ". $where;
$db->que .= " ORDER BY d.startDate ASC, d.startDistance ASC, d.seq ASC";


$db->query();
$obj = $db->getRows();
$count = count($obj);


if($count>0)
{
	$rows .= "사용일자,";
	$rows .= "부서,";
	$rows .= "성명,";
	$rows .= "사번,";
	$rows .= "출발시 누적,";
	$rows .= "도착시 누적,";
	$rows .= "주행거리,";
	$rows .= "사용목적,";
	$rows .= "사용목적코드,";
	//$rows .= "유종,";
	$rows .= "출발지,";
	$rows .= "도착지,";
	$rows .= "운행시간,";
	$rows .= "시작일시,";
	$rows .= "종료일시,";
	$rows .= "유류비,";
	$rows .= "통행료,";
	$rows .= "기타비용,";
	$rows .= "비고\r\n";




	//----------------------------------------------------------------------
	// List 처리
	//$weeks = ["일","월","화","수","목","금","토"];

	$objCount = count($obj);
	for($y=0; $y<$objCount; $y++)
	{
		$index = $y+2;
		

		$startDateTime = strtotime($obj[$y]["startDate"]);
		//$weekIndex = date("w", $startDateTime);
		//$startDate = date("Y-m-d", $startDateTime). "(". $weeks[$weekIndex]. ")";
		$startDate = date("Y-m-d", $startDateTime);
		$driveTime = date("H:i", strtotime($obj[$y]["startTime"])). " ~ ". date("H:i", strtotime($obj[$y]["stopTime"]));

		
		$rows .= $startDate. ",";
		$rows .= str_replace(",", "", $obj[$y]["departmentName"]). ",";
		$rows .= str_replace(",", "", $obj[$y]["name"]). ",";
		$rows .= $obj[$y]["employeeNumber"]. ",";
		$rows .= $obj[$y]["startDistance"]. ",";
		$rows .= $obj[$y]["stopDistance"]. ",";
		$rows .= $obj[$y]["distance"]. ",";
		$rows .= str_replace(",", "", $obj[$y]["purposeName"]). ",";
		$rows .= str_replace(",", "", $obj[$y]["purposeCode"]). ",";
		//$rows .= str_replace(",","",DrivingLog::getOilTypeName($obj[$y]["oilType"])). ",";
		$rows .= str_replace(",", "", $obj[$y]["startAddress"]). ",";
		$rows .= str_replace(",", "", $obj[$y]["stopAddress"]). ",";
		$rows .= $driveTime. ",";
		$rows .= $obj[$y]["startTime"]. ",";
		$rows .= $obj[$y]["stopTime"]. ",";
		$rows .= $obj[$y]["oilAmount"]. ",";
		$rows .= $obj[$y]["tollAmount"]. ",";
		$rows .= $obj[$y]["gitaAmount"]. ",";	
// 		$rows .=   str_replace("\n","",str_replace(",", "", $obj[$y]["bigo"])). "\r\n";
		$rows .=   		str_replace(array("\r\n","\r","\n"),'', str_replace(",", "", $obj[$y]["bigo"]))  . "\r\n";
// 		$rows .=   str_replace(",", "", $obj[$y]["bigo"]). "\r\n";
	}



	// CSV 파일로 저장합니다. 파일명을 날짜를 붙여 생성합니다. 
	$date = date("YmdHi"); 
	$filename = iconv("utf-8","euc-kr", "운행기록_".$date.".csv"); 

	header( "Content-type: text/csv; charset=EUC-KR" ); 
	header("Content-Disposition: attachment; filename=". $filename); 
	header( "Content-Description: PHP4 Generated Data" ); 
	echo iconv("UTF-8","CP949//TRANSLIT", $rows); 

}
else
{
	LIB::Alert("운행기록이 없습니다.", "-1");
}

$db->close();
exit;








/* 임시 데이터 입력 소스
for($i=0; $i<10000; $i++)
{
	$query .= "('KT_1234', 9, 102, '박장원', '강북영업본부', '본부장', 0, 145340, '2016-12-23', '2016-12-23 15:40:10', '대구광역시 남구 대명5동', 145340, '2016-08-23 15:40:12', '대구광역시 남구 대명5동','', '', 1),";
}

$query = substr($query, 0, -1);
$query = "insert into drivingLog (userUid, companySeq, departmentSeq, name, departmentName, dutyName, distance, startDistance, startDate, startTime, startAddress, stopDistance, stopTime, stopAddress,purpose, bigo, saveMapPoint) values ". $query;
$db->que = $query;
$db->query();
*/
?>