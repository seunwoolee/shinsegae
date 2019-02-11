<?
header("Content-Type:text/html;charset=UTF-8");
//###################################################
// 유류비 정산
// 띤떼게
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
$beginDate							= $_POST["beginDate"];
$endDate							= $_POST["endDate"];
$upperDep							= $_POST["upperDep"];
$lowerDep							= $_POST["lowerDep"];
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$json = new Json();
lib::Plog($beginDate.",".$endDate.",".$lowerDep);

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db->que = " SELECT * FROM oilCostPaid WHERE ymd >= '". $beginDate. "' AND ymd <= '". $endDate. "' AND departmentSeq = ".$lowerDep." GROUP BY  ymd , employeeNumber , costCode ORDER BY employeeNumber , ymd ";
$db->query();
$list = $db->getRows();

if($db->affected_rows() > 0)
{
	$json->add("list",$list);
	$json->add("code", "OK");
	$json->add("message"," 검색 성공.");
}
else
{
	$json->add("code", "ERROR");
	$json->add("message"," 검색실패.");
}



echo $json->getResult();
$db->Close();
exit;

?>
