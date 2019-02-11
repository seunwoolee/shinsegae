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
$companySeq						= $COMPANY_SEQ;
$password							= $_POST["leavePassword"];


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$securePassword = trim(base64_encode(hash('sha256', $password, true))); 


if(empty($password) == false)
{
	$db->que = "SELECT * FROM company WHERE seq=". $companySeq;
	$db->query();
	if($db->affected_rows() > 0)
	{
		$row = $db->getRow();
		if($row["password"] == $securePassword)
		{
			$DATA["enabled"] = "X";
			$db->Update("company", $DATA, "where seq=". $companySeq, "update error");
			LIB::Alert("정상적으로 탈퇴 되었습니다. 감사합니다.", "../php/logout.php");
		}
		else
		{
			LIB::Alert("비밀번호가 일치하지 않습니다.", "-1");
		}
	}
}


exit;
?>
