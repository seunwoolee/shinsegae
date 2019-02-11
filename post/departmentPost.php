<?
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/department.inc.php";

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
//상위
$mode				= $_POST["mode"];
$seq				= $_POST["seq"];
$parentSeq			= $_POST["parentSeq"];
$departmentName		= $_POST["departmentName"];
$departmentCode		= $_POST["departmentCode"];
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
//하위
$lowerDep					= $_POST["lowerDep"];
$code						= $_POST["code"];
$upperDep					= $_POST["upperDep"];
$depCode					= $_POST["depCode"]; // 하위면 depCode 상위면 departmentCode
$approverEmployeeNumber		= $_POST["approverEmployeeNumber"];
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
//승인권자
$previousApprover		= $_POST["previousApprover"];
$newApprover			= $_POST["newApprover"];
$previousApproverHidden = $_POST["previousApproverHidden"];
$newApproverHidden		= $_POST["newApproverHidden"];
//Up & Down
$previousSeq			= $_POST["previousSeq"];

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$department = new Department($db);
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
//	LIB::Plog($previousSeq);
if($mode == "remove")
{
	$completeMessage = "정상적으로 삭제 되었습니다.";
	$result = $department->remove($seq);
}
else if($mode == "up")
{
	$department->up($seq);
	LIB::Alert("", "../department.html?upperDepartmentSeq=".$previousSeq);
}
else if($mode == "down")
{
	$department->down($seq);
	LIB::Alert("", "../department.html?upperDepartmentSeq=".$previousSeq);
}
else if($mode == "upperModify")
{
	$completeMessage = "상위부서 정보가 수정 되었습니다.";
	$result = $department->upperModify($seq, $departmentName, $departmentCode);
}
else if($mode == "upperWrite")
{
	$completeMessage = "상위부서가 추가 되었습니다.";
	$result = $department->upperAdd($departmentName, $departmentCode);
}
else if($mode == "lowerWrite")
{
	$completeMessage = "소속부서가 추가 되었습니다.";
	$result = $department->lowerAdd($upperDep, $lowerDep, $depCode, $code, $approverEmployeeNumber);
}
else if($mode == "lowerModify")
{
	$completeMessage = "소속부서가 수정 되었습니다.";
	$result = $department->lowerModify($seq,$upperDep, $lowerDep, $depCode, $code, $approverEmployeeNumber);
}
else if($mode == "approverChange")
{
	$completeMessage = "승인권자 변경이 완료되었습니다.";
	$result = $department->approverChange($previousApproverHidden, $newApproverHidden);
}
/* TODO 삭제할것(클린코드중)
else
{
	if(strpos($name, "/") !== false) {
		$result = '특수문자 "/" 는 사용할 수 없습니다.';
	}
	else
	{
		if($seq > 0)
		{
			$completeMessage = "부서 정보가 수정 되었습니다.";
			$result = $department->modify($seq, $name, $parentSeq);
		}
		else
		{
			$completeMessage = "부서가 추가 되었습니다.";
			//$result = $department->add($name, $parentSeq);
			$result = $department->add($name, $code);
		}
	}
}
*/
$db->close();
if(empty($result) == false)
{
	LIB::Alert($result, "-1");
}
else
{
	LIB::Alert($completeMessage, "closeAndRefresh");
	//LIB::Alert($completeMessage, "../department.html");
}

?>
