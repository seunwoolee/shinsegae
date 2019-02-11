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

$password							= $_POST["password"];
$newPassword						= $_POST["newPassword"];
$newPasswordConfirm			= $_POST["newPasswordConfirm"];

$name								= $_POST["name"];
$adminName						= $_POST["adminName"];
$tel									= $_POST["tel"];
$phone								= $_POST["phone"];

$email								= $_POST["email"];
$address								= $_POST["address"];
$ceoName								= $_POST["ceoName"];

$corporateNumber = $_POST["invoiceeCorpNum1"].$_POST["invoiceeCorpNum2"].$_POST["invoiceeCorpNum3"];

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
		$db->que = "SELECT * FROM company WHERE cid='". $LOGIN_ID. "'";
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
			$DATA["password"] = $secureNewPassword;
		}
	}
}







//-----------------------------------------------------------------------------------------------
// DB 저장

$DATA["name"]						= $name;
$DATA["adminName"]				= $adminName;
$DATA["tel"]						= $tel;
$DATA["phone"]					= $phone;
$DATA["corporateNumber"]					= $corporateNumber;
$DATA["email"]						= $email;
$DATA["address"]					= $address;
$DATA["invoiceeCEOName"]			= $ceoName;

$db->Update("company", $DATA, "where cid='". $LOGIN_ID. "'", "update error");
LIB::Alert("수정 되었습니다.", "../setting.html");

?>
