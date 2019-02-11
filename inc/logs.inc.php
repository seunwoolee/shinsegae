<?
class LOGs {
	
	function LOGs($db)
	{
		$this->db = $db;
	}
	#
	#
	#테이블 정의 TODO 삭제
	static function getTable($state)
	{
		switch ($state)
		{
			case "department" : return "부서관리"; break;
			case "user"		  : return "사용자관리"; break;
			case "Login"	  : return "로그인"; break;
			case "Logout"	  : return "로그아웃"; break;
			case "Approver"	  : return "승인권자"; break;
			case "drivingLog" : return "차량운행내역"; break;
			case "previousReg": return "사용자 엑셀 업로드"; break;
			case "calculateOilSetting": return "정산 설정"; break;
			case "oilCostPaid": return "정산"; break;
			case "company"	  : return "설정"; break;
			case "purpose"	  : return "운행목적"; break;
		}
	}	
	#
	#
	#테이블별 컬럼 정의
	function getColumns($Table,$state)
	{
		switch ($Table)
		{
			case "department": 			return $this->getDepartmentColumns($state); break;
			case "Approver": 			return $this->getApproverColumns($state); break;
			case "user": 				return $this->getUserColumns($state); break;
			case "drivingLog": 			return $this->getDrivingLogColumns($state); break;
			case "calculateOilSetting": return $this->getcalculateOilSettingColumns($state); break;
			case "company": 			return $this->getCompanyColumns($state); break;
			case "purpose": 			return $this->getPurposeColumns($state); break;
		}
	}
	
	#
	#
	#대상(object) 컬럼 정의
	function getObject($Table,$inter)
	{
		switch ($Table)
		{
			case "department": 			return $this->getDepartmentObject($inter); break;
			case "Approver": 			return $this->getApproverObject($inter); break;
			case "user": 				return $this->getUserObject($inter); break;
			case "drivingLog": 			return $this->getDrivingLogObject($inter); break;
			case "purpose": 			return $this->getPurposeObject($inter); break;
		}
		
	}
	
	#
	#
	#테이블별 컬럼 정의
	function getKoreaData($Table,$col,$state,$inter)
	{
		switch ($Table)
		{
			case "department": 			return $this->getDepartmentKoreaData($col,$state,$inter); break;
			case "Approver": 			return $this->getApproverKoreaData($col,$state,$inter); break;
			case "user": 				return $this->getUserKoreaData($col,$state,$inter); break;
			case "drivingLog": 			return $this->getDrivingLogKoreaData($col,$state,$inter); break;
			case "calculateOilSetting": return $this->getCalculateOilSettingKoreaData($col,$state,$inter); break;
			case "company": 			return $this->getCompanyKoreaData($col,$state,$inter); break;
			case "purpose": 			return $this->getPurposeData($col,$state,$inter); break;
		}
		
	}
	
	#
	#
	#부서 관리 컬럼 정의
	function getDepartmentColumns($state)
	{
		switch ($state)
		{
			case "name"				: return "부서이름"; break;
			case "fullName"			: return "통합부서이름"; break;
			case "sort"				: return "순위"; break;
			case "parentSeq"		: return "상위부서"; break;
			case "code"				: return "판관/제조"; break;
			case "departmentCode"	: return "부서코드"; break;
			case "deleteState"		: return "부서삭제"; break;
		}
	}
	
	#
	#
	#사용자 관리 컬럼 정의
	function getUserColumns($state)
	{
		switch ($state)
		{
			case "name": 			return "이름"; break;
			case "enabled": 		return "상태"; break;
			case "password": 		return "패스워드"; break;
			case "departmentSeq": 	return "부서"; break;
			case "lastReset": 		return "패스워드 변경일"; break;
		}
	}
	
	
	#
	#차량운행내역  컬럼 정의
	function getDrivingLogColumns($state)
	{
		switch ($state)
		{
			case "purpose": 		return "운행목적"; break;
			case "startDistance": 	return "출발시 누적"; break;
			case "stopDistance": 	return "도착시 누적"; break;
			case "distance": 		return "거리"; break;
			case "startAddress": 	return "출발지"; break;
			case "stopAddress": 	return "도착지"; break;
			case "startDate": 		return "운행일자"; break;
			case "bigo": 			return "비고"; break;
			case "purposeLocation": return "목적지"; break;	
			case "deleteState": 	return "삭제여부"; break;	
			case "submitState": 	return "승인여부"; break;	
			case "rejectReason": 	return "반려사유"; break;	
			case "rejector": 		return "반려자"; break;	
			case "adminRejectYn": 	return "관리자/승인권자"; break;	
			case "rejectDate": 		return "반려일자"; break;	
			case "approveDate": 	return "승인일자"; break;	
		}
	}
	#
	#정산 설정 컬럼 정의
	function getcalculateOilSettingColumns($state)
	{
		switch ($state)
		{
			case "type": 				return "운행거리/운행거리+연비"; break;
			case "basicCost":	 		return "1Km당 유류비"; break;
			case "dieselCost": 			return "경유"; break;
			case "gasCost": 			return "LPG"; break;
			case "defaultMileage": 		return "기본연비"; break;
			case "bonusCost": 			return "bonusCost"; break;
			case "bonusSection": 		return "bonusSection"; break;
			case "bonusPercent": 		return "bonusPercent"; break;
			case "gasolineUp1800Cost": 	return "휘발유 1800이상"; break;	
			case "gasolineDown1800Cost":return "휘발유 1800미만"; break;	
			case "hybbridGasoilneCost": return "하이브리드_휘발유"; break;	
			case "hybbridLpiCost": 		return "하이브리드_LPI"; break;	
			
		}
	}
	#
	#설정 컬럼 정의
	function getCompanyColumns($state)
	{
		switch ($state)
		{
			case "defaultPurpose": 		return "운행목적 기본값"; break;
			case "lockDistance":	 	return "운행거리 변경"; break;
			case "lockDate": 			return "운행일자 변경"; break;
			case "lockTime": 			return "운행시간 변경"; break;
			case "lockSaveMapPoint": 	return "GPS 운행구간 지도 저장"; break;
			case "lockDeviceChange": 	return "기기변경시 재승인"; break;
		}
	}
	
	#
	#운행목적 컬럼 정의
	function getPurposeColumns($state)
	{
		switch ($state)
		{
			case "purposeName": 		return "운행목적 이름"; break;
			case "purposeType":	 		return "운행목적 고유번호"; break;
			case "purposeCode": 		return "운행목적 코드"; break;
			case "purposeState": 		return "운행목적 상태"; break;
			case "purposeDefault": 		return "운행목적 기본"; break;
			case "sort": 				return "우선순위"; break;
		}
	}
	
	
	#
	#
	#승인권자 컬럼 정의
	function getApproverColumns($state)
	{
		switch ($state)
		{
			case "userSeq": 		return "승인권자"; break;
		}
	}
	
	#
	#
	#승인권자 한국말로
	function getApproverKoreaData($col,$state,$inter)
	{
		switch ($col)
		{
			case "userSeq": $this->db->que = "SELECT name FROM user WHERE seq=". $state;
							$this->db->query();
							$state = $this->db->getOne();
							return $state;
							break;
		}
	}
	
	#
	#
	#부서 대상(object)
	function getDepartmentObject($inter)
	{
		if($inter["seq"] > 0)
		{
			$this->db->que = "SELECT name FROM department WHERE seq=". $inter["seq"];
			$this->db->query();
			$object = $this->db->getOne();
		}
		return $object;
	}
	
	
	#
	#
	#차량운행내역 대상(object)
	function getDrivingLogObject($inter)
	{
		if($inter["seq"] > 0)
		{
			$this->db->que = "SELECT name , departmentName , startTime FROM drivingLog WHERE seq=". $inter["seq"];
			$this->db->query();
			if($this->db->affected_rows() > 0)
			{
				$row = $this->db->getRow();
				$object = implode(" ",$row);
			}
		}
		return $object;
	}
	#
	#
	#운행목적 대상(object)
	function getPurposeObject($inter)
	{
		if($inter["seq"] > 0)
		{
			$this->db->que = "SELECT purposeName FROM purpose WHERE seq=". $inter["seq"];
			$this->db->query();
			$object = $this->db->getOne();
		}
		return $object;
	}

	
	#
	#
	#승인권자 대상(object)
	function getApproverObject($inter)
	{
		if($inter["departmentSeq"] > 0)
		{
			$this->db->que = "SELECT name FROM department WHERE seq=". $inter["departmentSeq"];
			$this->db->query();
			$object = $this->db->getOne();
		}
		return $object;
	}
	
	#
	#
	#차량운행내역 대상(object)
	function getUserObject($inter)
	{
		if($inter["seq"] > 0)
		{
			$this->db->que = "SELECT name FROM user WHERE seq=". $inter["seq"];
			$this->db->query();
			$object = $this->db->getOne();
		}
		return $object;
	}
	
	#
	#
	#사용자 한국말로
	function getUserKoreaData($col,$state,$inter)
	{
		switch ($col)
		{
			case "departmentSeq":	$this->db->que = "SELECT name FROM department WHERE seq=". $state;
									$this->db->query();
									$state = $this->db->getOne();
									return $state;
									break;
									
			case "enabled"		:	if($state == "N")
									{
										$state = "미승인";
									}
									else if($state == "Y")
									{
										$state = "승인";
									}
									else if($state == "X")
									{
										$state = "삭제";
									}
									else if($state == "C")
									{
										$state = "기기변경";
									}
									else if($state == "B")
									{
										$state = "사용중지";
									}
									return $state;
									break;
									
			case "name"			:	return $state;
									break;
									
			case "password"		:	return "?";
									break;
									
			case "lastReset"	:	return $state;
									break;
		
		}
	}
	
	#
	#
	#차량운행내역 한국말로
	function getDrivingLogKoreaData($col,$state,$inter)
	{
		switch ($col)
		{
			case "purpose"		:	$this->db->que = "SELECT purposeName FROM purpose WHERE purposeType='".$state."'";
									$this->db->query();
									$state = $this->db->getOne();
			
			case "startDistance":	return $state;
									break;
									
			case "stopDistance" :	return $state;
									break;
		
			case "distance"		:	return $state;
									break;
		
			case "startAddress"	:	return $state;
									break;
		
			case "stopAddress"	:	return $state;
									break;
		
			case "startDate"	:	return $state;
									break;
		
			case "bigo"			:	return $state;
									break;
		
			case "purposeLocation":	return $state;
									break;
									
			case "deleteState"	:	return $state;
									break;
									
			case "submitState"	:	if($state == "N")
									{
										$state = "미승인";
									}
									else if($state == "Y")
									{
										$state = "승인";
									}
									else if($state == "X")
									{
										$state = "반려";
									}
									return $state;
									break;
									
			case "rejectReason"	:	return $state;
									break;
									
			case "rejector"		:	return $state;
									break;
									
			case "rejectDate"	:	return $state;
									break;
									
			case "approveDate"	:	return $state;
									break;
									
			case "adminRejectYn":	if($state == "N")
									{
										$state = "승인권자";
									}
									else if($state == "Y")
									{
										$state = "관리자";
									}
									return $state;
									break;

		}				
	}
	
	#
	#
	#부서 한국말로
	function getDepartmentKoreaData($col,$state,$inter)
	{
		if($col == "parentSeq")
		{
			$this->db->que = "SELECT name FROM department WHERE parentSeq='".$state."'";
			$this->db->query();
			$state = $this->db->getOne();
			return $state;
		}
		else if($col == "deleteState")
		{
			if($state == "N")
			{
				$state = "미삭제";
			}
			else if($state == "Y")
			{
				$state = "삭제";
			}	
			return $state;
		}		
		else
		{
			return $state;
		}
	}
	#
	#
	#정산 설정 한국말로
	function getCalculateOilSettingKoreaData($col,$state,$inter)
	{
			return $state;
	}
	
	#
	#
	#설정 한국말로
	function getCompanyKoreaData($col,$state,$inter)
	{
		if($col == "purposeState")
		{
			if($state == "N")
			{
				$state = "미사용";
			}
			else if($state == "Y")
			{
				$state = "사용";
			}
			else if($state == "X")
			{
				$state = "삭제";
			}
		}
		return $state;
	}
	
	#
	#
	#목적 한국말로
	function getPurposeData($col,$state,$inter)
	{
		if($col == "defaultPurpose")
		{
			$this->db->que = "SELECT purposeName FROM purpose WHERE purposeType='".$state."'";
			$this->db->query();
			$state = $this->db->getOne();
		}
		
		if($state == "N")
		{
			$state = "불가능";
		}
		else if($state == "Y")
		{
			$state = "가능";
		}
		else if($state == "X")
		{
			$state = "삭제";
		}
		return $state;
	}
	
	
	#
	#
	#수정 시 비교 정의
	function getArrayDiff($arrColumns,$arrBefore,$arrAfter)
	{
		$arrayBefore = [];
		$arrayAfter  = [];
		$arrCol		 = [];
		$arrDiff 	 = [];
		$j=0;
		for($i=0;$i<count($arrColumns);$i++)
		{
			$arrayBefore[$arrColumns[$i]] = $arrBefore[$i];
			$arrayAfter[$arrColumns[$i]]  = $arrAfter[$i];
			//$arrCol[$i] = $arrColumns[$i];
		}
		$arrDiffAfter = array_diff_assoc($arrayAfter, $arrayBefore); // 승인권자 사번 및 이름 시 이쪽에서 바꿔줘야함 
		$arrDiffBefore = array_diff_assoc($arrayBefore, $arrayAfter);
		$arrIntersect	= array_intersect_assoc($arrayBefore,$arrayAfter);
		foreach($arrDiffAfter as $key => $value)
		{
			$arrCol[$j]= $key;
			$j++;
		}
		$arrDiff["before"] 	= $arrDiffBefore;
		$arrDiff["after"] 	= $arrDiffAfter;
		$arrDiff["col"]		= $arrCol;
		$arrDiff["inter"]	= $arrIntersect;		
		echo '<script>console.log('. json_encode($arrDiff) .')</script>';
		return $arrDiff;
	}
		
	#
	#
	#추가 및 삭제 시 네비게이터
	function getMent($eventType,$Table,$arrColumns,$arrBefore,$arrAfter)
	{
		if($Table == "department")
		{
			if($eventType == "입력")
			{
				//echo '<script>console.log('. json_encode($arrAfter) .')</script>';
				$ment = $this->getAddMentDepartment($arrColumns,$arrAfter);
				return $ment;
			}
			else
			{
				$ment = $this->getDeleteMentDepartment($arrColumns,$arrBefore);
				return $ment;
			}
		}
		else if($Table == "Approver")
		{
			if($eventType == "입력")
			{
				$ment = $this->getAddMentApprover($arrColumns,$arrAfter);
				return $ment;
			}
			else
			{
				$ment = $this->getDeleteMentApprover($arrColumns,$arrBefore);
				return $ment;
			}
		}
		else if($Table == "previousReg")
		{
				$ment = $this->getAddMentpreviousReg($arrColumns,$arrAfter);
				return $ment;
		}		
		else if($Table == "oilCostPaid")
		{
				$ment = $this->getAddMentOilCostPaid($arrColumns,$arrAfter);
				return $ment;
		}
		else if($Table == "purpose")
		{
				$ment = $this->getAddMentPurpose($arrColumns,$arrAfter);
				return $ment;
		}
		else if($Table == "Login")
		{
				$ment = $this->getAddMentLogin($arrColumns,$arrAfter);
				return $ment;
		}
	}
	
	#
	#
	#부서 Add 상세 Ment
	function getAddMentDepartment($arrColumns,$arrAfter)
	{
		$DepartmentColumn = $this->getDepartmentColumns("name");
		$ment = $DepartmentColumn." : ".$arrAfter[0];
		return $ment;
	}
	
	#
	#
	#부서 Delete 상세 Ment
	function getDeleteMentDepartment($arrColumns,$arrBefore)
	{
		$DepartmentColumn = $this->getDepartmentColumns("name");
		$ment = $DepartmentColumn." : ".$arrBefore[6];
		return $ment;
	}
	
	#
	#
	#승인권자 Add 상세 Ment
	function getAddMentApprover($arrColumns,$arrAfter)
	{
		$this->db->que = "SELECT name FROM user WHERE seq=". $arrAfter[0];
		$this->db->query();
		$userName = $this->db->getOne();
		$this->db->que = "SELECT name FROM department WHERE seq=". $arrAfter[1];
		$this->db->query();
		$depName = $this->db->getOne();
		$ment = $depName." : ".$userName;
		return $ment;
	}
	
	#
	#
	#사용자 엑셀 업로드 Add 상세 Ment
	function getAddMentpreviousReg($arrColumns,$arrAfter)
	{
		$employeeNumber = $arrAfter[0];
		$name			= $arrAfter[1];
		$ment = "사번 : ".$employeeNumber." 이름 : ".$userName." 등록";
		return $ment;
	}
	#
	#
	#사용자 엑셀 업로드 Add 상세 Ment
	function getAddMentOilCostPaid($arrColumns,$arrAfter)
	{
		$startDate    = $arrAfter[0];
		$endDate   	  = $arrAfter[1];
		$department   = $arrAfter[2];
		$ment = $startDate." ~ ".$endDate." 부서 : ".$department;
		return $ment;
	}
	
	#
	#
	#운행목적 Add 상세 Ment
	function getAddMentPurpose($arrColumns,$arrAfter)
	{
		$ment = $arrAfter[0];
		return $ment;
	}
	#
	#
	#Login 상세 Ment
	function getAddMentLogin($arrColumns,$arrAfter)
	{
		$ment = "";
		return $ment;
	}
	
	function getTotalCount($where)
	{
		$this->db->que = " SELECT count(seq) FROM eventAudit ";
		$this->db->que .= " WHERE (1) ".$where;
		$this->db->query();
		$totalCount = $this->db->getOne();
		return $totalCount;
	}
	
	function getMainListRows($where,$limitStartRow,$limitFetchSize)
	{
		$this->db->que = " SELECT ea.* , u.name FROM eventAudit as ea LEFT JOIN user as u On u.employeeNumber = ea.employeeNumber ";
		$this->db->que .= " WHERE (1) ". $where ." ORDER BY seq desc  LIMIT ". $limitStartRow. ", ". $limitFetchSize;
		$this->db->query();
		$MainListRows = $this->db->getRows();
		$LIST		  = $this->getMainList($MainListRows);
		return $LIST;
	}
	
	function getMainList($MainListRows)
	{
		for($i=0;$i<count($MainListRows);$i++)
		{
			$row = $MainListRows[$i];
			$arrColumns = explode(",",$row["columns"]);
			$arrBefore  = explode(",",$row["beforeData"]);
			$arrAfter 	= explode(",",$row["afterData"]);
			$ment		= "";
			$object		= "";
			$firstRow = 1;
			
			if($row["eventType"] == "수정")
			{
				$arrDiff = $this->getArrayDiff($arrColumns,$arrBefore,$arrAfter);
				for($j=0;$j<count($arrDiff["col"]);$j++)
				{
					$col 		= $arrDiff["col"][$j];
					$columnName = $this->getColumns($row["TableName"],$col);
					$object 	= $this->getObject($row["TableName"],$arrDiff["inter"]);		
					$beforeData	= $this->getKoreaData($row["TableName"],$col,$arrDiff["before"][$col],$arrDiff["inter"]);
					$afterData	= $this->getKoreaData($row["TableName"],$col,$arrDiff["after"][$col],$arrDiff["inter"]);
					$ment 		.= "&nbsp<b>".$columnName."</b> : ".$beforeData." -> ".$afterData;
				}
			}
			else// 입력 및 삭제 시 멘트
			{
				$ment = $this->getMent($row["eventType"],$row["TableName"],$arrColumns,$arrBefore,$arrAfter);
			}
			$nameAndEmployeeNumber = $row["name"]."(".$row["employeeNumber"].")";
			if($firstRow == 1)
			{

				$lowerDep .= "
								<td class='text-center'>".$row["createTime"]."</td>
								<td class='text-center'>".$nameAndEmployeeNumber."</td>
								<td class='text-center'><p>".$row["description"]." ".$object."</p>".$ment."</td>
								<td class='text-center'>".$row["ip"]."</td>
							</tr>";
				$firstRow++;
			}
			else
			{

				$lowerDep .= "
							<tr>
								<td class='text-center'>".$row["createTime"]."</td>
								<td class='text-center'>".$nameAndEmployeeNumber."</td>
								<td class='text-center'><p>".$row["description"]." ".$object."</p>".$ment."</td>
								<td class='text-center'>".$row["ip"]."</td>
							</tr>";
			}
		}
		
		if(empty($lowerDep))
		{
			$lowerDep = "	<tr>
							<td class='text-center' colspan='4' style='height:295px;'>데이터가 없습니다.</td>
							</tr>";
		}
		return $lowerDep; 
		
	}
	#
	#
	#사용자 Delete 상세 Ment
	function getDeleteMentApprover($arrColumns,$arrAfter)
	{

	}
}

?>