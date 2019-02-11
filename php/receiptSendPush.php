<?
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/fcm.inc.php";
include "../inc/receipt.inc.php";



//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$seqList				= explode(",", $_GET["data"]);


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$fcm = new Fcm();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -


$count = count($seqList);
for($i=0; $i<$count; $i++)
{
	$seq = $seqList[$i];
	$db->que = "SELECT r.*, u.pushId, u.osType FROM receiptView AS r JOIN user AS u ON r.userUid=u.uid WHERE r.seq=". $seq;
	$db->query();
	$receipt = $db->getRow();

	$message = "[영수증 ". Receipt::getStateText($receipt["state"]). "] ". $receipt["receiptAccountCodeName"]. "-". $receipt["receiptAccountCodeDetailName"];
	$fcm->init();
	$fcm->addPushId($receipt["pushId"]);
	$fcm->addReceiptParam($receipt["osType"], $seq, $receipt["state"], $message);
	$fcm->send();

	$log = "seq:". $seqList[$i]. " > ". $message;
	Receipt::pushLog($log);
}

$db->close();
exit;
?>
