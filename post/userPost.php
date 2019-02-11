<?
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/user.inc.php";
include "../inc/payment.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$mode					= $_POST["mode"];
$seq					= $_POST["seq"];
$employeeNumber			= $_POST["employeeNumber"];
$password				= $_POST["p_assword"];
$departmentSeq			= $_POST["lowerDep"];
$name					= $_POST["name"];
$enabled				= $_POST["enabled"];

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$user = new User($db);
$payment = new Payment($db, $companySeq);

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
if($mode == "remove")
{
	$completeMessage = "사용자 정보가 삭제 되었습니다.";
	// insert into user_store
	$DATA["enabled"] = "X";
	$DATA["seq"] 	 = $seq;
	$db->Update("user", $DATA, "where seq=". $seq, "사용자 삭제");
}
else
{
	/*$totalDistance = (int) $totalDistance;
	$oilMileage = (int) $oilMileage;

	if($totalDistance < 1)
	{
		$totalDistance = 0;
	}

	if($oilMileage < 1)
	{
		$oilMileage = 0;
	}
	*/


	if(empty($password) == false)
	{
		$password = LIB::getHashPassword($password);
	}

	if($seq > 0)
	{
		$DATA["seq"]						= $seq;
		$DATA["employeeNumber"]				= $employeeNumber;
		$DATA["departmentSeq"]				= $departmentSeq;
		$DATA["name"]						= $name;
		$DATA["enabled"]					= $enabled;
		$completeMessage = "사용자 정보가 수정 되었습니다.";

		if(empty($password) == false)
		{
			$DATA["password"]				= $password;
		}

		$user->modifyUser($DATA);
	}
}


$db->close();
if(empty($result) == false)
{
	LIB::Alert($result, "-1");
}
else
{
	LIB::Alert("", "openerReload");
	LIB::Alert($completeMessage, "close");
}
exit;
?>
