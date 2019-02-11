<?
header("Content-Type:text/html;charset=UTF-8");
//###################################################
// 로그인
// 2014.02.07
//###################################################


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/json.inc.php";
include "../inc/mysql.inc.php";
include "../inc/company.inc.php";
include "../inc/security.inc.php";
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -


//Json Class
$json = new Json();
$company = new Company($_POST);
$security = new Security($_POST);

//Code
$security->BeforeLogin($_SERVER['REMOTE_ADDR'],$_POST["cid"]);
$ActivatedYn = $security->getActivated();

$enabled = $company->getApproverOrAdmin();

if(empty($enabled) || $enabled == "X")
{
	$json->add("code", "ERROR");
	$json->add("message", "아이디가 존재하지 않습니다.");
} 
else if($enabled == "P") //PASSWORD is Expired
{
	if($company->checkPassword()) //When PASSWORD is correct , go to change PASSWORD
	{
		$companyInfo = $company->getCompany($_POST["cid"]);
		session_start();
		$_SESSION["OMember_id"] = $_POST["cid"];
		$_SESSION["OMember_admin"] = $companyInfo["adminYn"];	
		$_SESSION["passwordExpire"] = "passwordExpire";

		$json->add("code", "passwordExpire");
		$json->add("message", "패스워드를 변경하세요.");
	}
	else if($company->checkPassword() == false)
	{	
		$json->add("code", "ERROR");
		$json->add("message", "비밀번호가 일치하지 않습니다.");	
	}
}
else if($enabled != "Y")
{
	$json->add("code", "ERROR");
	$json->add("message", "승인권자가 아닙니다.");
}
else if($ActivatedYn == "N" && isset($ActivatedYn))
{
	$json->add("code", "ERROR");
	$json->add("message", "계정이 잠겼습니다. 1분뒤에 시도하세요.");
}
else if($company->checkPassword() == false)
{
	$security->AfterUnsuccessfulLogin($_SERVER['REMOTE_ADDR'],$_POST["cid"]);
	$json->add("code", "ERROR");
	$json->add("message", "비밀번호가 일치하지 않습니다.");
} 
else
{
	$companyInfo = $company->getCompany($_POST["cid"]);
	session_start();
	$_SESSION["OMember_id"] = $_POST["cid"];
	$_SESSION["OMember_admin"] = $companyInfo["adminYn"];

	$company->setAutoLogin();
	$company->eventAuditLogin($_POST["cid"]);
	
	$security->AfterSuccessfulLogin($_SERVER['REMOTE_ADDR'],$_POST["cid"]);
	$json->add("code", "OK");
	$json->add("message", "");
}


echo $json->getResult();
$company->dbClose();

exit;
?>
