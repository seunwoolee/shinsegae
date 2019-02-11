<?
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$companySeq			= $COMPANY_SEQ;
$check					= $_POST["check"];
$state					= $_POST["updateState"];
$params					= $_POST["params"];
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -


$count = count($check);
for($i=0; $i<$count; $i++)
{
	$seq = $check[$i];
	$db->que = "SELECT * FROM receipt WHERE seq=". $seq;
	$db->query();
	$receipt = $db->getRow();

	if($receipt["state"] != $state)
	{
		$seqList[] = $seq;
		$DATA["state"] = $state;
		$db->Update("receipt", $DATA, "where seq=". $seq, "update error");
	}
}

if(count($seqList) > 0)
{
	$procUrl = _HTTPHOST. "/". _PACKAGE. "/php/receiptSendPush.php?data=". implode(",", $seqList);
	exec("nohup /usr/bin/wget -c -O /tmp/push_receipt -q ". $procUrl. " > /dev/null 2>&1 &");
}

LIB::Alert("", "../receipt.html".$params);
exit;
?>
