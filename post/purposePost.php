<?
header("Content-Type:text/html;charset=UTF-8");

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -


$mode					=  $_POST["mode"];
$seq					=  $_POST["seq"];
$purposeName			=  $_POST["purposeName"];
$purposeState			=  $_POST["purposeState"];
$purposeCode			=  $_POST["purposeCode"];

$db = new Mysql();


if($mode=='add')
{
	
	$db->que = "SELECT MAX(sort) FROM purpose ";
	$db->query();
	$maxSort = $db->getOne();
	

	$DATA["purposeName"] = $purposeName;
	$DATA["purposeCode"] = $purposeCode;
	$DATA["purposeState"] = $purposeState;
	$DATA["sort"] = $maxSort + 1;
	
	$db->que = "Select * from purpose WHERE purposeName ='" . $purposeName  ."'";
	$db->query();
	

	//없으면 추가
	if($db->affected_rows() < 1)
	{
		$db->Insert("purpose", $DATA, "운행목적 추가");
		//$seq = $db->insert_id();
		//lib::Plog($seq);
		$db->que = "Select seq from purpose order by seq desc limit 1";
		$db->query();
		$purposeSeq = $db->getOne();
		unset($DATA);
		$DATA["purposeType"] = $purposeSeq;
		$db->Update("purpose", $DATA, "where seq=". $purposeSeq , "X");
		$message = "운행목적이 추가 되었습니다.";
	}
	else
	{
		//기존에 똑같은 상태가  삭제된것이 있으면 다시 살려냄
		$purpose = $db->getRow();
		if($purpose["purposeState"] == 'X')
		{
			unset($DATA);
			$DATA["purposeName"] = $purposeName;
			$DATA["purposeCode"] = $purposeCode;
			$DATA["purposeState"] = "Y";
			$DATA["sort"] = $maxSort + 1;
			$db->Update("purpose", $DATA, "where seq=". $purpose["seq"] , "운행목적 추가");
			$message = "운행목적이 추가 되었습니다.";
		}
		else
		{
			$message = "이미 사용중인 운행목적 입니다.";
		}
	}
	
}
else if($mode=='del')
{
	$db->que = "SELECT * FROM purpose WHERE seq=". $seq;
	$db->query();
	$choice = $db->getRow();

	$db->que = "UPDATE purpose SET sort=sort-1 WHERE purposeDefault='N' AND purposeState!='X' AND sort > ". $choice["sort"];
	$db->query();


	$DATA["sort"] = "0";
	$DATA["purposeState"] = "X";
	$DATA["seq"] = $seq;	
	$db->Update("purpose", $DATA, "where seq=". $seq, "운행목적 삭제");
	$message = "운행목적이 삭제 되었습니다.";
}
else if($mode=='modify')
{
	$DATA["purposeName"] = $purposeName;
	$DATA["purposeCode"] = $purposeCode;
	$DATA["purposeState"] = $purposeState;
	$DATA["seq"] = $seq;	
	$db->Update("purpose", $DATA, "where seq=". $seq, "운행목적 수정");
	$message = "운행목적이 수정 되었습니다.";
}
else if($mode=='up')
{
	$db->que = "SELECT * FROM purpose WHERE seq=". $seq;
	$db->query();
	$choice = $db->getRow();

	$db->que = "SELECT * FROM purpose WHERE purposeDefault='N' AND purposeState!='X' AND sort < ". $choice["sort"]. " ORDER BY sort DESC LIMIT 1";
	$db->query();
	if($db->affected_rows() > 0)
	{
		$prev = $db->getRow();
		$DATA["sort"] = $prev["sort"];
		$db->Update("purpose", $DATA, "where seq=". $choice["seq"], "X");

		$DATA["sort"] = $choice["sort"];
		$db->Update("purpose", $DATA, "where seq=". $prev["seq"], "X");
	}
}

else if($mode=='down')
{
	$db->que = "SELECT * FROM purpose WHERE seq=". $seq;
	$db->query();
	$choice = $db->getRow();

	$db->que = "SELECT * FROM purpose WHERE purposeDefault='N' AND purposeState!='X' AND sort > ". $choice["sort"]. " ORDER BY sort ASC LIMIT 1";
	$db->query();
	if($db->affected_rows() > 0)
	{
		$next = $db->getRow();
		$DATA["sort"] = $next["sort"];
		$db->Update("purpose", $DATA, "where seq=". $choice["seq"], "X");

		$DATA["sort"] = $choice["sort"];
		$db->Update("purpose", $DATA, "where seq=". $next["seq"], "X");
	}
}

LIB::Alert("", "openerReload");
LIB::Alert($message, "../purposeSetting.html");


?>
