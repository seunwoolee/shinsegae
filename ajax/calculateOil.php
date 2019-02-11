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

if($upperDep > 0 )
{
	$where .= " AND d.parentSeq =". $upperDep;
}

if($lowerDep > 0 )
{
	$where .= " AND dl.departmentSeq =". $lowerDep;
}



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


$db->que = "SELECT employeeNumber, oilType, oilMileage FROM user ";
$db->query();
while($row = $db->getRow())
{
	$user[$row["employeeNumber"]]["oilType"] = $row["oilType"];
	$user[$row["employeeNumber"]]["oilMileage"] = $row["oilMileage"]; //참고 oilMileage는 다 0임
}




$db->que = " SELECT * FROM calculateOilSetting ";
$db->query();
$setting = $db->getRow();


$db->que = " SELECT dl.departmentSeq , dl.departmentName , dl.employeeNumber , dl.name , sum(dl.distance) as distance , dl.startDate  , ifnull(sum(r.amount), 0)  as receiptCost ,  dl.costName , dl.costCode ";
$db->que .= " FROM drivingLog AS dl LEFT JOIN receipt AS r ON dl.seq=r.drivingLog_seq ";
$db->que .= " LEFT JOIN department AS d ON d.seq=dl.departmentSeq ";
$db->que .= " WHERE dl.deleteState='N' AND dl.submitState = 'Y' AND dl.startDate >= '". $beginDate. "' AND dl.startDate <= '". $endDate. "'".$where." GROUP BY  dl.startDate , dl.costCode , dl.employeeNumber ORDER BY dl.employeeNumber , dl.startDate ";
$db->query();
lib::Plog($db->que);

$insertOilCostPaids = "INSERT INTO oilCostPaid (departmentSeq , departmentName , employeeNumber, name, distance, ymd, cost, receiptCost , totalCost, costCode , costName, jeogyo) VALUES ";

if($db->affected_rows() > 0)
{
	while($row = $db->getRow())
	{
		$uid = $row["employeeNumber"];
		$oilMileage = 0;
		$oilType = "";

		 
		if($setting["type"] == "Basic")
		{
			$cost = $row["distance"] * $setting["basicCost"];
		}
		else if($setting["type"] == "Mileage")
		{
			//개인 OR 기본연비 적용
			if($user[$uid]["oilMileage"] > 0)
			{
				$oilMileage = $user[$uid]["oilMileage"];
			}
			else
			{
				$oilMileage = $setting["defaultMileage"];
			}
			
			
			 
			//유종에 따른 유가 설정
			$oilCost = $setting["gasolineDown1800Cost"];
			$oilType = "Gasoline_Down_1800";
			if($user[$uid]["oilType"] == "Diesel" && $setting["dieselCost"] > 0)
			{
				$oilCost = $setting["dieselCost"];
				$oilType = "Diesel";
			}
			else if($user[$uid]["oilType"] == "Gas" && $setting["gasCost"] > 0)
			{
				$oilCost = $setting["gasCost"];
				$oilType = "Gas";
			}
			else if($user[$uid]["oilType"] == "Gasoline_Up_1800" && $setting["gasolineUp1800Cost"] > 0)
			{
				$oilCost = $setting["gasolineUp1800Cost"];
				$oilType = "Gasoline_Up_1800";
			}
			else if($user[$uid]["oilType"] == "Hybbrid_Gasoline" && $setting["hybbridGasoilneCost"] > 0)
			{
				$oilCost = $setting["hybbridGasoilneCost"];
				$oilType = "Hybbrid_Gasoline";
			}
			else if($user[$uid]["oilType"] == "Hybbrid_LPI" && $setting["hybbridLpiCost"] > 0)
			{
				$oilCost = $setting["hybbridLpiCost"];
				$oilType = "Hybbrid_LPI";
			}
			//유류비 산출
			
			if($row["distance"] > 0 && $oilMileage > 0 && $oilCost > 0)
			{
				$cost = $row["distance"] / $oilMileage * $oilCost;
			}
			else
			{
				$cost = 0;
			}
		}
		$totalCost = $cost + $row["receiptCost"];
		
		$insertOilCostPaids .= "(". $row["departmentSeq"]. ",'".$row["departmentName"]."', '". $row["employeeNumber"]. "', '". $row["name"]. "',". $row["distance"]. ", '". $row["startDate"]. "', ". $cost. ", ". $row["receiptCost"]. ", ". $totalCost. ", '". $row["costCode"]. "', '". $row["costName"]. "', '".date(n)."월 유류대외 정산". "'),";
	}

	$insertOilCostPaids = substr($insertOilCostPaids, 0, -1);

	
	if($upperDep > 0 && empty($lowerDep) )
	{ // 상위부서만
		$arr = []; 
		$db->que = " SELECT seq FROM department WHERE parentSeq=".$upperDep;
		$db->query();
		while($row = $db->getRow())
		{
			array_push($arr,$row["seq"]);
		}
		$db->Delete("oilCostPaid", "where ymd BETWEEN '". $beginDate. "' AND '". $endDate. "' AND departmentSeq IN (".implode(',',$arr).")", "X");
	}
	else if($upperDep > 0 && $lowerDep > 0 )
	{ // 상위 , 하위부서
		$db->Delete("oilCostPaid", "where ymd BETWEEN '". $beginDate. "' AND '". $endDate. "' AND departmentSeq =".$lowerDep , "X");
	}
	else
	{ // 기간만
		$db->Delete("oilCostPaid", "where ymd BETWEEN '". $beginDate. "' AND '". $endDate. "'", "X");
	}

		//개인별 유류비 지급액 입력
	$db->que = $insertOilCostPaids;
	$db->query();
	//lib::Plog($insertOilCostPaids);
	//$db->Delete("oilCost", "where  year='". $year. "' AND month='". $month. "'", "oilCost delete error");

	
	if($lowerDep > 0) //상,하위부서 모두
	{
		$db->que = " SELECT fullName FROM department WHERE seq=".$lowerDep;
		$db->query();		
		$dep = $db->getOne();
	}
	else if ($upperDep > 0 && $lowerDep == 0) //상위부서만
	{
		$db->que = " SELECT fullName FROM department WHERE seq=".$upperDep;
		$db->query();		
		$dep = $db->getOne();
	}	
	else //부서가 전체일때
	{
		$dep = "전체부서";
	}
	
	$afterDATA = $beginDate.",".$endDate.",".$dep;
	//eventAudit 기록 남기기
	$evnetAuditQuery = "INSERT INTO eventAudit (employeeNumber , eventType , description , afterDATA, ip, TableName) VALUES ";
	$evnetAuditQuery .= "('".$_SESSION[OMember_id]. "','입력', '정산', '". $afterDATA. "', '". $_SERVER['REMOTE_ADDR']. "','oilCostPaid')";
	lib::Plog($evnetAuditQuery);
	$db->que = $evnetAuditQuery;
	$db->query();



	$DATA["upperDep"]				= $upperDep;
	$DATA["lowerDep"]				= $lowerDep;
	$DATA["year"]					= date(Y);
	$DATA["month"]					= date(m);
	$DATA["day"]					= date(d);
	$DATA["type"]					= $setting["type"];
	$DATA["basicCost"]				= $setting["basicCost"];
	$DATA["gasolineUp1800Cost"]		= $setting["gasolineUp1800Cost"];
	$DATA["gasolineDown1800Cost"]	= $setting["gasolineDown1800Cost"];
	$DATA["dieselCost"]				= $setting["dieselCost"];
	$DATA["gasCost"]				= $setting["gasCost"];
	$DATA["hybbridGasoilneCost"]	= $setting["hybbridGasoilneCost"];
	$DATA["hybbridLpiCost"]			= $setting["hybbridLpiCost"];

	$DATA["defaultMileage"]			= $setting["defaultMileage"];
	$DATA["bonusCost"]				= $setting["bonusCost"];
	$DATA["bonusSection"]			= $setting["bonusSection"];
	$DATA["bonusPercent"]			= $setting["bonusPercent"];
	
	//해당월의 유류비 기준단가 입력
	$db->Insert("oilCost", $DATA, "X");
	



	$runTime = time() - $time;
	if($runTime < 2)
	{
		sleep(2);
	}


	$json->add("code", "OK");
	$json->add("message"," 유류비 정산이 완료 되었습니다.");
}
else
{
	$json->add("code", "ERROR");
	$json->add("message"," 운행기록이 없습니다.");
}



echo $json->getResult();
$db->Close();
exit;

?>
