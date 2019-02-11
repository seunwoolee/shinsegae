<?
header("Content-Type:text/html;charset=UTF-8");
//###################################################
// 메인화면 그래프
// 2017
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


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$db = new Mysql();

//Json Class
$json = new Json();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -




$purpose["-oil-"] = "유류비";
$db->que = "SELECT * FROM purpose WHERE purposeState='Y' ORDER BY sort";
$db->query();
while($row = $db->getRow())
{
	$purpose[$row["purposeType"]] = $row["purposeName"];
}


$begin = date("Y-m-01", strtotime('-5 months'));
$end = date("Y-m-t");


$db->que = "SELECT SUM(distance) AS distance, purpose, DATE_FORMAT(startDate, '%m') AS month FROM drivingLog ";
$db->que .= " WHERE startDate >= '". $begin. "' AND startDate <= '". $end. "' GROUP BY purpose, month";
$db->query();
while($row = $db->getRow())
{
	$distance[$row["purpose"]][$row["month"]] = $row["distance"];
}


$db->que = "SELECT sum(totalCost) AS cost, month FROM oilCostPaid WHERE ymd >= '". $begin. "' AND ymd <= '". $end. "' GROUP BY month";
$db->query();
while($row = $db->getRow())
{
	//$oilCost[
	$distance["-oil-"][$row["month"]] = $row["cost"]/100;
}


$index=1;
$DATA[0][0] = "month";
foreach($purpose as $key => $value)
{
	$DATA[0][$index] = $value;

	for($i=1; $i<=6; $i++)
	{
		$time = strtotime(($i-6). " months");
		$month = date("m", $time);
		$DATA[$i][0] = $month. "월";
		
		if($distance[$key][$month] == null)
		{
			$DATA[$i][$index] = 0;
		}
		else
		{
			$DATA[$i][$index] = (int) $distance[$key][$month];
		}
	}

	$index++;
}


$json->add("data", $DATA);
echo $json->getResult();
$db->Close();
exit;

?>
