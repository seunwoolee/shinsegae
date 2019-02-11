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
$companySeq							= $COMPANY_SEQ;
$departmentSeq						= $_POST["departmentSeq"];
$receiptProjectSeq					= $_POST["receiptProjectSeq"];
$receiptAccountCodeSeq			= $_POST["receiptAccountCodeSeq"];
$receiptAccountCodeDetailSeq	= $_POST["receiptAccountCodeDetailSeq"];
$card										= $_POST["card"];
$state									= $_POST["state"];
$beginDate								= $_POST["beginDate"];
$endDate								= $_POST["endDate"];
$widhImage								= $_POST["widhImage"];



//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$db = new Mysql();

//Json Class
$json = new Json();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -


if(empty($departmentSeq) == false)						$WHERE .= " AND departmentSeq=". $departmentSeq;
if(empty($receiptProjectSeq) == false)						$WHERE .= " AND receiptProjectSeq=". $receiptProjectSeq;
if(empty($receiptAccountCodeSeq) == false)				$WHERE .= " AND receiptAccountCodeSeq=". $receiptAccountCodeSeq;
if(empty($receiptAccountCodeDetailSeq) == false)		$WHERE .= " AND receiptAccountCodeDetailSeq=". $receiptAccountCodeDetailSeq;
if(empty($card) == false)										$WHERE .= " AND card='". $card. "'";
if(empty($state) == false)										$WHERE .= " AND state='". $state. "'";
if(empty($beginDate) == false)								$WHERE .= " AND useDate>='". $beginDate. "'";
if(empty($endDate) == false)									$WHERE .= " AND useDate<='". $endDate. "'";
if($widhImage == true)											$WHERE .= " AND imageUrl != '' AND imageUrl IS NOT NULL ";


$db->que = "SELECT COUNT(*) FROM receiptView WHERE companySeq=". $companySeq. $WHERE;
$db->query();
$totalCount = $db->getOne();




$json->add("receiptCount", number_format($totalCount));
echo $json->getResult();
$db->Close();
exit;

?>
