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
$mode										= $_POST["mode"];
$seq											= $_POST["seq"];
$amount										= $_POST["amount"];
$receiptAccountCodeDetailSeq		= $_POST["receiptAccountCodeDetailSeq"];

$state										= $_POST["state"];
$card											= $_POST["card"];
$useDate									= $_POST["useDate"];
$receiptProjectSeq						= $_POST["receiptProjectSeq"];
$memo										= $_POST["memo"];
$image										= $_FILES["image"];

$rejectReason  							= $_POST["rejectReason"];


if(empty($receiptProjectSeq)) {
	$receiptProjectSeq = 0;
}

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$fcm = new Fcm();



if($seq > 0)
{
	$db->que = "SELECT r.*, u.pushId, u.osType FROM receiptView AS r JOIN user AS u ON r.userUid=u.uid WHERE r.seq=". $seq;
	$db->query();
	$receipt = $db->getRow();

	if($mode == "modify")
	{
		$DATA["amount"]										= $amount;
		$DATA["card"]											= $card;
		$DATA["state"]											= $state;
		$DATA["receiptAccountCodeDetailSeq"]		= $receiptAccountCodeDetailSeq;
		$DATA["receiptProjectSeq"]						= $receiptProjectSeq;
		$DATA["useDate"]										= $useDate;
		$DATA["memo"]										= $memo;
		$DATA["rejectReason"]						= $rejectReason;
		
		$db->Update("receipt", $DATA, "where seq=". $seq, "insert error");

		unset($DATA);
		if(strlen($image["name"]) > 0)
		{
			//이미지 변경
			if(LIB::inFileType("JPG|JPEG|PNG", $image["name"]) == true)
			{
				Receipt::removeImage($receipt["thumbUrl"]);
				Receipt::removeImage($receipt["imageUrl"]);

				$receiptClass = new Receipt($db, $receipt["companySeq"]);
				$receiptClass->uploadImage($image, $seq);

				unset($DATA);
				$DATA["imageUrl"] = $receiptClass->getImageUrl();
				$DATA["thumbUrl"] = $receiptClass->getThumbUrl();
				$DATA["thumbWidth"] = $receiptClass->getThumbWidth();
				$DATA["thumbHeight"] = $receiptClass->getThumbHeight();
				$db->Update("receipt", $DATA, "where seq=". $seq, "update error");
			}
		}
		

		if($state != "N" && $state != $receipt["state"]) 
		{
			$message = "[영수증 ". Receipt::getStateText($state). "] ". $receipt["receiptAccountCodeName"]. "-". $receipt["receiptAccountCodeDetailName"];
			$fcm->addPushId($receipt["pushId"]);
			$fcm->addReceiptParam($receipt["osType"], $seq, $state, $message);
			$fcm->send();

			$log = "seq:". $seq. " > ". $message;
			Receipt::pushLog($log);
		}

		$message = "영수증 정보가 수정 되었습니다.";
	}
	else if($mode == "remove")
	{
		Receipt::removeImage($receipt["thumbUrl"]);
		Receipt::removeImage($receipt["imageUrl"]);
		$db->Delete("receipt", "where seq=". $seq, "delete error");
		$message = "영수증 정보가 삭제 되었습니다.";
	}
}

LIB::Alert($message, "openerReload");
LIB::Alert("", "close");
$db->close();
exit;
?>
