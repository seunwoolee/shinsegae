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
$companySeq					= $COMPANY_SEQ;
$receiptAccountCodeSeq	= $_POST["receiptAccountCodeSeq"];


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$db = new Mysql();

//Json Class
$json = new Json();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -


$db->que = "SELECT * FROM receiptAccountCodeDetail WHERE receiptAccountCodeSeq=". $receiptAccountCodeSeq. " AND enabled='Y' ORDER BY sort ASC";
$db->query();

$json->add("receiptAccountCodeDetails", $db->getRows());
echo $json->getResult();
$db->Close();
exit;

?>
