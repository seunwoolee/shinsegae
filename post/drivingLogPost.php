<?
header("Content-Type:text/html;charset=UTF-8");

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/drivingLog.inc.php";



//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$seq									= $_POST["seq"];
$mode									= $_POST["mode"];
$purpose								= $_POST["purpose"];
$startDistance							= $_POST["startDistance"];
$stopDistance							= $_POST["stopDistance"];
$startAddress							= $_POST["startAddress"];
$stopAddress							= $_POST["stopAddress"];
$startDate								= $_POST["startDate"];
$bigo									= $_POST["bigo"];
//추가 목적지
$purposeLocation						= $_POST["purposeLocation"];







//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();



if($mode == "modify")
{

	$DATA["purpose"]							= $purpose;
	$DATA["startDistance"]						= $startDistance;
	$DATA["stopDistance"]						= $stopDistance;
	$DATA["distance"]							= $stopDistance - $startDistance;
	$DATA["startAddress"]						= $startAddress;
	$DATA["stopAddress"]						= $stopAddress;
	$DATA["startDate"]							= $startDate;
	$DATA["bigo"]								= $bigo;
	$DATA["purposeLocation"]					= $purposeLocation;
	$DATA["seq"]								= $seq;

	$db->Update("drivingLog", $DATA, "WHERE seq=". $seq, "차량운행내역 수정");

	//누적주행거리에 반영 (마지막 운행기록일때)
	DrivingLog::updateTotalDistance($db, $seq);


	LIB::Alert("운행기록이 수정 되었습니다.", "openerReload");
	LIB::Alert("", "close");
}
else if($mode == "remove")
{
	$DATA["seq"]		  = $seq;
 	$DATA["deleteState"]  = "Y";
 	$db->Update("drivingLog", $DATA, "WHERE seq=". $seq, "차량운행내역 삭제");
	LIB::Alert("운행기록이 삭제 되었습니다.", "openerReload");
	LIB::Alert("", "close");
}

$db->close();
exit;
?>
