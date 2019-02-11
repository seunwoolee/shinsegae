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
$previousApprover								= $_GET["previousApprover"];
$newApprover									= $_GET["newApprover"];
$mode											= $_GET["mode"];
//하위부서 추가 및 수정 시 승인권자 이름
$approverName									= $_GET["approverName"];
$approverEmployeeNumber							= $_GET["approverEmployeeNumber"];
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$db = new Mysql();

//Json Class
$json = new Json();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
if(empty($approverEmployeeNumber) == false)
{
	$db->que = " select u.name  as username , employeeNumber, d.name as depname , d.departmentCode from user as u , department as d where u.departmentSeq = d.seq AND u.employeeNumber='".$approverEmployeeNumber."'";
	$db->query();
	$row = $db->getRow();
	$lowerDepartmentInfo["approverDepartmentCodeCode"]	= $row["departmentCode"];
	$lowerDepartmentInfo["approverDepartmentName"]		= $row["depname"];
	$lowerDepartmentInfo["approverEmployeeNumber"]		= $row["employeeNumber"];
	$lowerDepartmentInfo["approverName"] 				= $row["username"];

	$json->add("list",$lowerDepartmentInfo);
}


if(empty($previousApprover) == false && $mode == "previous")
{

	$where .= "WHERE u.name LIKE '%". $previousApprover. "%' OR u.employeeNumber LIKE '%". $previousApprover. "%'  ";
	$db->que = " select u.seq as seq , u.name as username , u.employeeNumber as employeeNumber , d.name as depname , d.departmentCode as departmentCode from user as u left join department as d ON u.departmentSeq = d.seq ".$where ;
	$db->query();
	$list = $db->getRows();
	$previousApproverDepArray = [];
	for($i=0;$i<count($list);$i++)
	{
		$row = $list[$i];
		$db->que = " select * from departmentView WHERE approverUserSeq= ".$row["seq"];
		$db->query();
		if($db->affected_rows() > 0)
		{
			while($result = $db->getRow())
			{
				$previousApproverDepArray[$i][] = array("name" => $row["username"], "department" => $result["name"]);
			}
		}
		else
		{
				$previousApproverDepArray[$i][] = array("name" => $row["username"]);
		}
		
	}

	$json->add("list",$list);
	$json->add("previousApproverDepArray",$previousApproverDepArray);
	
}


if(empty($newApprover) == false && $mode == "new")
{
	$where .= "WHERE u.name LIKE '%". $newApprover. "%' OR u.employeeNumber LIKE '%". $newApprover. "%'  ";
	$db->que = " select u.seq as seq , u.name as username , u.employeeNumber as employeeNumber , d.name as depname , d.departmentCode as departmentCode from user as u left join department as d ON u.departmentSeq = d.seq ".$where ;
	$db->query();
	$list = $db->getRows();
	$newApproverDepArray = [];
	//LIB::Plog("dddd");
	for($i=0;$i<count($list);$i++)
	{
		$row = $list[$i];
		$db->que = " select * from departmentView WHERE approverUserSeq= ".$row["seq"];
		$db->query();
		if($db->affected_rows() > 0)
		{
			while($result = $db->getRow())
			{
				$newApproverDepArray[$i][] = array("name" => $row["username"], "department" => $result["name"]);
			}
		}
		else
		{
				$newApproverDepArray[$i][] = array("name" => $row["username"]);
		}
		
	}

	$json->add("list",$list);
	$json->add("newApproverDepArray",$newApproverDepArray);

}
//하위부서 추가 및 수정 시
if(empty($approverName) == false && $mode == "select")
{

	$where .= "WHERE u.name LIKE '%". $approverName. "%'";
	$db->que = " select u.seq as seq , u.name as username , u.employeeNumber as employeeNumber , d.name as depname , d.departmentCode as departmentCode from user as u left join department as d ON u.departmentSeq = d.seq ".$where ;
	$db->query();
	//LIB::Plog($db->que);
	$list = $db->getRows();
	
	
	//LIB::Plog($list);
	$json->add("list",$list);
	//$json->add("previousApproverDepArray",$previousApproverDepArray);
	
}
echo $json->getResult();
$db->Close();
exit;

?>
