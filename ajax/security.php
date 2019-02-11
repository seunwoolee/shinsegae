<?
header("Content-Type:text/html;charset=UTF-8");


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/json.inc.php";
include "../inc/mysql.inc.php";

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$db = new Mysql();

//Json Class
$json = new Json();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

session_start();
$_SESSION["OMember_id"] = "";
$_SESSION["OMember_admin"] = "";

if(isset($_COOKIE["Cds_cid"]))
{
	unset($_COOKIE['Cds_cid']);
    unset($_COOKIE['Cds_key']);
    setcookie('Cds_cid', null, -1, '/');
    setcookie('Cds_key', null, -1, '/');
}

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$json->add("message", "로그아웃되었습니다.");


echo $json->getResult();
$db->Close();
exit;

?>
