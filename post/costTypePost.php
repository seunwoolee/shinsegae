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

$mode					=  $_POST["mode2"];
$seq					=  $_POST["seq2"];
$costName				=  $_POST["costName"];
$costCode				=  $_POST["costCode"];

$db = new Mysql();


if($mode=='add')
{
	
	$DATA["Name"] = $costName;
	$DATA["Code"] = $costCode;
	
	$db->que = "Select * from costType WHERE code ='" .$costCode."'";
	$db->query();
	

	//없으면 추가
	if($db->affected_rows() < 1)
	{
		$db->Insert("costType", $DATA, "X");
		$message = "비용성격이 추가 되었습니다.";
	}
	else
	{
		$message = "이미 사용중인 비용코드 입니다.";
	}
}
else if($mode=='del')
{
	$db->que = "DELETE from costType WHERE seq = ".$seq;
	$db->query();
	$message = "비용성격이 삭제 되었습니다.";
}
else if($mode=='modify')
{
	$DATA["Name"] = $costName;
	$DATA["Code"] = $costCode;
	$db->Update("costType", $DATA, "where seq=". $seq, "X");
	$message = "비용성격이 수정 되었습니다.";
}

LIB::Alert("", "openerReload");
LIB::Alert($message, "../purposeSetting.html");


?>
