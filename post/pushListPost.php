<?
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/push.inc.php";
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$push = new Push($db);
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$checkedUserSeq = $_POST["checkedArray"];
$mode = $_POST["mode"];
$message = $_POST["message"];

if($mode == "allSend")
{
	$type = 'all';
	$insertPush = "INSERT INTO push (type , message) VALUES ";
	$insertPush .="('".$type."','".$message."')";
	$db->que = $insertPush;
	$db->query();
	$ment = "푸시 전체 발송 완료";
}
else if($mode == "approverSend")
{
	$type = 'approver';
	$insertPush = "INSERT INTO push (type , message) VALUES ";
	$insertPush .="('".$type."','".$message."')";
	$db->que = $insertPush;
	$db->query();
	$ment = "푸시 승인권자 발송 완료";
}
else if($mode == "choiceSend")
{
	$departmentSeq = $push->CheckUserseqStringtoArray($checkedUserSeq);
	$type = 'choice';
	$insertPush = "INSERT INTO push (userSeq , departmentSeq ,type , message) VALUES ";
	$insertPush .="('".$checkedUserSeq."',".$departmentSeq.",'".$type."','".$message."')";
	$db->que = $insertPush;
	$db->query();
	$ment = "푸시 선택 발송 완료";
}
LIB::Alert($ment, "../push.html");

/*
$checkValue			= $_POST["checkValue"];
$newDep				= $_POST["newDep"];
$newDepHidden		= $_POST["newDepHidden"];
$depCode			= $_POST["depCode"];

if(empty($checkValue) == false)
{
	$check = explode (",", $checkValue); 
}

lib::Plog($newDepHidden);

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$push = new Push($db);
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

if($mode == "approve")
{
	$count = count($check);
	for($i=0; $i<$count; $i++)
	{
		
		$seq = $check[$i];
		$DATA["enabled"] = "Y";
		$DATA["seq"] 	 = $seq;		
		$db->Update("user", $DATA, "where seq=". $seq, "사용자 선택 승인");
		$message = "선택 승인이 완료되었습니다.";
	}

	LIB::Alert($message, "../user.html");
	exit;
}
else if($mode == "departmentModify")
{
	$count = count($check);
	$db->que = " SELECT seq FROM department WHERE departmentCode= ".$newDepHidden;
	$db->query();
	$newDepartment = $db->getOne();	
	for($i=0; $i<$count; $i++)
	{
		$seq = $check[$i];
		$DATA["departmentSeq"] = $newDepartment;
		$DATA["seq"] 		   = $seq;
		$db->Update("user", $DATA, "where seq=". $seq, "사용자 소속 부서 변경");
		$message = "소속 부서 변경이 완료되었습니다.";
	}
	
	LIB::Alert($message, "closeAndRefresh");
	exit;
}
else
{
	$seq = $check[0];
	$db->que = "SELECT departmentSeq FROM user WHERE seq=". $seq;
	$db->query();
	$biqo = $db->getOne();		
	$count = count($check);
	
	for($i=1; $i<$count;) //하나밖에없으면? 그냥 통과함
	{
		$seq = $check[$i];
		$db->que = "SELECT departmentSeq FROM user WHERE seq=". $seq;
		$db->query();
		$biqoo = $db->getOne();
		
		if($biqo == $biqoo)
		{
			$i++;
		}
		else
		{
			$message = "동일한 부서를 선택해주세요.";			
			LIB::Alert($message, "../user.html");
			exit;
		}
	}
	//echo "<script>window.open('../userChange.html')</script>";
	LIB::Alert($message, "../userChange.html?seq=".$seq);
	exit;
}
*/
?>
