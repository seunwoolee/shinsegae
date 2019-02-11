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
$mode										= $_POST["mode"];
$seq											= $_POST["seq"];
$receiptAccountCodeSeq				= $_POST["receiptAccountCodeSeq"];
$type											= $_POST["type"];
$name										= $_POST["name"];
$sort											= $_POST["sort"];
$enabled										= $_POST["enabled"];
$companySeq								= $COMPANY_SEQ;

$bylaws 								= $_POST["bylaws"];

if(strlen(trim($sort)) > 0)
{
	if(is_numeric($sort) == false)
	{
		$sort = 1;
	}
}
else
{
	$sort = 1;
}


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -


if($mode == "remove")
{
	$completeMessage = "정상적으로 삭제 되었습니다.";
	$DATA["enabled"]		= "X";
	$db->Update("receiptAccountCodeDetail", $DATA, "where seq=". $seq, "update error");
}
else
{
	if($seq > 0)
	{
		$completeMessage	= "세목이 수정 되었습니다.";
		$DATA["receiptAccountCodeSeq"]			= $receiptAccountCodeSeq;
		$DATA["type"]										= $type;
		$DATA["name"]										= $name;
		$DATA["sort"]										= $sort;
		$DATA["enabled"]									= $enabled;
		$DATA["bylaws"]									= $bylaws;
		$db->Update("receiptAccountCodeDetail", $DATA, "where seq=". $seq, "update error");
	}
	else
	{
		$completeMessage = "세목이 추가 되었습니다.";
		$DATA["receiptAccountCodeSeq"]			= $receiptAccountCodeSeq;
		$DATA["type"]										= $type;
		$DATA["name"]										= $name;
		$DATA["sort"]										= $sort;
		$DATA["enabled"]									= $enabled;
		$DATA["sort"]										= $sort;
		$DATA["bylaws"]									= $bylaws;
		$db->Insert("receiptAccountCodeDetail", $DATA, "update error");
	}
}

$db->close();
LIB::Alert($completeMessage, "../receiptAccountCodeDetail.html");
?>
