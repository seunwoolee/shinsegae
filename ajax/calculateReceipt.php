<?
header("Content-Type:text/html;charset=UTF-8");
//###################################################
// 유류비 정산
// 2016
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
$year								= $_POST["year"];
$month							= $_POST["month"];
$ymd								= $year. "-". $month. "-01";


//시작시간 (끝났을때 너무빨리 끝나면 sleep 좀 주기)
$time = time();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$db = new Mysql();

//Json Class
$json = new Json();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$beginDate = $year. "-". $month. "-01";
$endDate = $year. "-". $month. "-31";

$db->que = "UPDATE receipt SET calculate='Y' WHERE companySeq=". $companySeq. " AND useDate>='". $beginDate. "' AND useDate <= '". $endDate. "' AND card='Personal' AND state='Y' AND calculate='N'";
$db->query();


$db->que = "SELECT SUM(amount) AS amount, userUid, COUNT(*) AS receiptCount FROM receipt WHERE companySeq=". $companySeq. " AND useDate >= '". $beginDate. "' AND useDate <= '". $endDate. "' AND card='Personal' AND state='Y' AND calculate='Y' GROUP BY userUid";
$db->query();

$insertReceiptPersonalPaids = "INSERT INTO receiptPersonalPaid (companySeq, userUid, receiptCount, amount, year, month, ymd) VALUES ";

if($db->affected_rows() > 0)
{
	while($row = $db->getRow())
	{
		$insertReceiptPersonalPaids .= "(". $companySeq. ", '". $row["userUid"]. "', ". $row["receiptCount"]. ",". $row["amount"]. ",'". $year. "','". $month. "', '". $ymd. "'),";
	}

	$insertReceiptPersonalPaids = substr($insertReceiptPersonalPaids, 0, -1);
	$db->Delete("receiptPersonalPaid", "where companySeq=". $companySeq. " AND year='". $year. "' AND month='". $month. "'", "receiptPersonalPaid delete error");
	
	//개인별 유류비 지급액 입력
	$db->que = $insertReceiptPersonalPaids;
	$db->query();


	$runTime = time() - $time;
	if($runTime < 2)
	{
		sleep(2);
	}


	$json->add("code", "OK");
	$json->add("message", $month. "월 개인 여비 정산이 완료 되었습니다.");
}
else
{
	$db->que = "SELECT COUNT(*) FROM receiptPersonalPaid WHERE companySeq=". $companySeq. " AND year='". $year. "' AND month='". $month. "'";
	$db->query();
	if($db->getOne() > 0)
	{
		$db->Delete("receiptPersonalPaid", "where companySeq=". $companySeq. " AND year='". $year. "' AND month='". $month. "'", "receiptPersonalPaid delete error");
		$json->add("code", "OK");
		$json->add("message", $month. "월 개인 여비 정산이 완료 되었습니다.");
	}
	else
	{
		$json->add("code", "ERROR");
		$json->add("message", $month. "월은 개인카드 영수증이 없습니다.");
	}
}



echo $json->getResult();
$db->Close();
exit;

?>
