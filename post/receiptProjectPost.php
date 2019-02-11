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
$mode				= $_POST["mode"];
$seq					= $_POST["seq"];
$name				= $_POST["name"];
$code				= $_POST["code"];
$enabled				= $_POST["enabled"];
$companySeq		= $COMPANY_SEQ;

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

	$db->que = "SELECT * FROM receiptProject WHERE seq=". $seq;
	$db->query();
	$choice = $db->getRow();

	$db->que = "UPDATE receiptProject SET sort=sort-1 WHERE companySeq=". $companySeq. " AND sort > ". $choice["sort"];
	$db->query();

	$DATA["enabled"]		= "X";
	$DATA["_sort"]		= "NULL";

	$db->Update("receiptProject", $DATA, "where seq=". $seq, "update error");
}
else if($mode == "up")
{
	$db->que = "SELECT * FROM receiptProject WHERE seq=". $seq;
	$db->query();
	$choice = $db->getRow();
	
	$db->que = "SELECT * FROM receiptProject WHERE companySeq=". $companySeq. " AND sort < ". $choice["sort"]. " ORDER BY sort DESC LIMIT 1";
	$db->query();
	if($db->affected_rows() > 0)
	{
		$prev = $db->getRow();
		$DATA["sort"] = $prev["sort"];
		$db->Update("receiptProject", $DATA, "where seq=". $choice["seq"], "sort update error");

		$DATA["sort"] = $choice["sort"];
		$db->Update("receiptProject", $DATA, "where seq=". $prev["seq"], "sort update error");
	}
}
else if($mode == "down")
{
	$db->que = "SELECT * FROM receiptProject WHERE seq=". $seq;
	$db->query();
	$choice = $db->getRow();
	
	$db->que = "SELECT * FROM receiptProject WHERE companySeq=". $companySeq. " AND sort > ". $choice["sort"]. " ORDER BY sort ASC LIMIT 1";
	$db->query();
	if($db->affected_rows() > 0)
	{
		$next = $db->getRow();
		$DATA["sort"] = $next["sort"];
		$db->Update("receiptProject", $DATA, "where seq=". $choice["seq"], "sort update error");

		$DATA["sort"] = $choice["sort"];
		$db->Update("receiptProject", $DATA, "where seq=". $next["seq"], "sort update error");
	}
}
else
{
	if($seq > 0)
	{
		$completeMessage	= "프로젝트가 수정 되었습니다.";
		$DATA["name"]			= $name;
		$DATA["code"]			= $code;
		$DATA["enabled"]		= $enabled;
		$db->Update("receiptProject", $DATA, "where seq=". $seq, "update error");
	}
	else
	{
		$completeMessage = "프로젝트가 추가 되었습니다.";
		$db->que = "SELECT MAX(sort) FROm receiptProject WHERE companySeq=". $companySeq;
		$db->query();
		$maxSort = $db->getOne();
		if(empty($maxSort))
		{
			$maxSort = 0;
		}

		$DATA["name"]			= $name;
		$DATA["code"]			= $code;
		$DATA["enabled"]		= $enabled;

		$DATA["sort"] = $maxSort + 1;
		$DATA["companySeq"] = $companySeq;
		$db->Insert("receiptProject", $DATA, "update error");
	}
}

$db->close();
LIB::Alert($completeMessage, "../receiptProject.html");
?>
