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


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
//$companySeq					= $COMPANY_SEQ;
$departmentSeq				= $_POST["departmentSeq"];


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$db = new Mysql();

//Json Class
$json = new Json();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -


//$db->que = "SELECT u.uid, u.name, u.enabled, du.name AS dutyName FROM user AS u JOIN duty AS du ON u.dutySeq=du.seq WHERE u.companySeq=". $companySeq. " AND u.departmentSeq=". $departmentSeq. " ORDER BY u.enabled, du.sort ASC, u.name ASC";
$db->que = "SELECT u.employeeNumber, u.name, u.enabled FROM user AS u WHERE u.departmentSeq=". $departmentSeq. " ORDER BY u.enabled ASC, u.name ASC";
$db->query();
lib::Plog($db->que);

$json->add("users", $db->getRows());


echo $json->getResult();
$db->Close();
exit;

?>
