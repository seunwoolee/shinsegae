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
$password						= $_POST["password"];
$newPassword					= $_POST["newPassword"];
$newPasswordConfirm				= $_POST["newPasswordConfirm"];
$email							= $_POST["email"];


$defaultPurpose					= $_POST["defaultPurpose"];
$lockDistance					= $_POST["lockDistance"];
$lockDate						= $_POST["lockDate"];
$lockTime						= $_POST["lockTime"];
$lockSaveMapPoint				= $_POST["lockSaveMapPoint"];
$lockDeviceChange				= $_POST["lockDeviceChange"];
$lockAuto						= $_POST["lockAuto"];



//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

$securePassword = trim(base64_encode(hash('sha256', $password, true))); 
$secureNewPassword = trim(base64_encode(hash('sha256', $newPassword, true))); 
$secureNewPasswordConfirm = trim(base64_encode(hash('sha256', $newPasswordConfirm, true))); 


if(empty($password) == false)
{
	if(empty($newPassword) == true)
	{
		LIB::Alert("변경할 비밀번호를 입력해 주세요", "-1");
		exit;
	}
	else
	{
		$db->que = "SELECT * FROM user WHERE employeeNumber='". $LOGIN_ID. "'";
		$db->query();
		$row = $db->getRow();

		if($row["password"] != $securePassword)
		{
			LIB::Alert("현재 비밀번호가 맞지 않습니다. 다시 입력해 주세요", "-1");
			exit;
		}
		else if($secureNewPassword != $secureNewPasswordConfirm)
		{
			LIB::Alert("변경할 비밀번호가 다릅니다. 다시 입력해 주세요", "-1");
			exit;
		} 
		else 
		{
			$DATAS["password"] = $secureNewPassword;
			$_SESSION["passwordExpire"] = "";
		}
	}
}





//-----------------------------------------------------------------------------------------------
// DB 저장
$date = date("Y-m-d H:i:s", strtotime("+45 days"));
$DATAS["email"]					= $email;
$DATAS["lastReset"]				= $date;

$DATA["lockDate"]				= $lockDate;
$DATA["lockTime"]				= $lockTime;
$DATA["defaultPurpose"]			= $defaultPurpose;



if(empty($lockDistance) == false)
{
	$DATA["lockDistance"]		= $lockDistance;
}
if(empty($lockSaveMapPoint) == false)
{
	$DATA["lockSaveMapPoint"]	= $lockSaveMapPoint;
}
if(empty($lockDeviceChange) == false)
{
	$DATA["lockDeviceChange"]	= $lockDeviceChange;
}


$db->Update("company", $DATA, "where seq = 1", "설정");
$db->Update("user", $DATAS, "where adminYn = 'Y' AND employeeNumber = '".$LOGIN_ID."'", "설정");
LIB::Alert("수정 되었습니다.", "../setting.html");

?>
