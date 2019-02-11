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
	$db->Delete("duty", "where seq=". $seq, "delete error");
}
else if($mode == "up")
{
	$db->que = "SELECT * FROM duty WHERE seq=". $seq;
	$db->query();
	$choice = $db->getRow();
	
	$db->que = "SELECT * FROM duty WHERE sort < ". $choice["sort"]. " ORDER BY sort DESC LIMIT 1";
	$db->query();
	if($db->affected_rows() > 0)
	{
		$prev = $db->getRow();
		$DATA["sort"] = $prev["sort"];
		$db->Update("duty", $DATA, "where seq=". $choice["seq"], "sort update error");

		$DATA["sort"] = $choice["sort"];
		$db->Update("duty", $DATA, "where seq=". $prev["seq"], "sort update error");
	}
}
else if($mode == "down")
{
	$db->que = "SELECT * FROM duty WHERE seq=". $seq;
	$db->query();
	$choice = $db->getRow();
	
	$db->que = "SELECT * FROM duty WHERE sort > ". $choice["sort"]. " ORDER BY sort ASC LIMIT 1";
	$db->query();
	if($db->affected_rows() > 0)
	{
		$next = $db->getRow();
		$DATA["sort"] = $next["sort"];
		$db->Update("duty", $DATA, "where seq=". $choice["seq"], "sort update error");

		$DATA["sort"] = $choice["sort"];
		$db->Update("duty", $DATA, "where seq=". $next["seq"], "sort update error");
	}
}
else
{
	if($seq > 0)
	{
		$completeMessage = "직책 정보가 수정 되었습니다.";
		$DATA["name"] = $name;
		$db->Update("duty", $DATA, "where seq=". $seq, "update error");
	}
	else
	{
		$completeMessage = "직책이 추가 되었습니다.";
		$db->que = "SELECT MAX(sort) FROm duty ";
		$db->query();
		$maxSort = $db->getOne();
		if(empty($maxSort))
		{
			$maxSort = 0;
		}

		$DATA["name"] = $name;
		$DATA["sort"] = $maxSort + 1;
		//$DATA["companySeq"] = $companySeq;
		$db->Insert("duty", $DATA, "update error");
	}
}

$db->close();
LIB::Alert($completeMessage, "../duty.html");
?>
