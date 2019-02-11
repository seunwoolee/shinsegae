<?
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/payment.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$check				= $_POST["check"];
$checkValue			= $_POST["checkValue"];
$mode				= $_POST["mode"];
$rejectReason		= $_POST["rejectReason"];
$memo				= $_POST["memo"];



if(empty($checkValue) == false)
{
	$check = explode (",", $checkValue); 
}

if($rejectReason == 2)
{
	$rejectReason = "부서 변경 미처리";
}
else if($rejectReason == 1)
{
	$rejectReason = "업무 연관성 확인 필요";
}
else
{
	$rejectReason = $memo;	
}




//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

if($mode == "approval")
{
	$count = count($check);
	for($i=0; $i<$count; $i++)
	{
		
		$seq = $check[$i];
		$date = date("Y-m-d H:i:s");
		$DATA["submitState"] = "Y";
		$DATA["seq"] 		 = $seq;
		$DATA["approveDate"] = $date;
		$db->Update("drivingLog", $DATA, "where seq=". $seq, "선택 승인");
		$message = "선택 승인이 완료되었습니다.";
	}

	LIB::Alert($message, "../drivingLogs.html");
	exit;
}
else if($mode == "reject")
{

	$count = count($check);
	for($i=0; $i<$count; $i++)
	{
		
		$seq = $check[$i];
		$date = date("Y-m-d H:i:s");
		$DATA["submitState"]	= "X";
		$DATA["rejectReason"] 	= $rejectReason;
		$DATA["rejector"] 		= $_SESSION["OMember_id"];
		$DATA["adminRejectYn"] 	= $_SESSION["OMember_admin"];
		$DATA["seq"] 			= $seq;		
		$DATA["rejectDate"] 	= $date;		
		$db->Update("drivingLog", $DATA, "where seq=". $seq, "선택 반려");
		$message = "선택 반려가 완료되었습니다.";
	}

	LIB::Alert($message, "closeAndRefresh");
	exit;
}
/*
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
