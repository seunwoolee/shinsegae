<?
class User {
	var $db;
	var $fetchSize = 20;
	var $pageCount = 5;
	var $findkey;
	var $pageNum;
	var $where;

	
	function User($db)
	{
		$this->db = $db;
	}
	
	
	static function getEnabled($enabled)
	{
		$stateName["ALL"]			= "전체 사용자";
		$stateName["N"]				= "미승인";
		$stateName["C"]				= "기기변경";
		$stateName["Y"]				= "승인";
		$stateName["B"]				= "사용중지";
		
		
	
		return $stateName[$enabled];
	}

	function registUser($user,$device)
	{
		$this->db->que = "SELECT * FROM user WHERE employeeNumber='". $user["employeeNumber"]. "'";
		$this->db->query();
		if($this->db->affected_rows() > 0)
		{
			return "이미 사용중인 사번 입니다.";
		}
		else
		{
		
			$DATA["employeeNumber"]		= $user["employeeNumber"];
			$DATA["password"]			= $user["password"];
			$DATA["departmentSeq"]		= $user["departmentSeq"];
			$DATA["name"]				= $user["name"];			
			$DATA["enabled"]			= $user["enabled"];
			
			$DATA["deviceId"]				= $device["deviceId"];
			$DATA["osType"]					= $device["osType"];
			$DATA["osVersion"]				= $device["osVersion"];
			$DATA["versionName"]			= $device["versionName"];
			$DATA["country"]				= $device["country"];
			$DATA["language"]				= $device["language"];
			$DATA["model"]					= $device["model"];
			
			$this->db->Insert("user", $DATA, "user insert error");
		}
	}

	function modifyUser($user)
	{
		$DATA["name"]					= $user["name"];
		$DATA["seq"]					= $user["seq"];
		$DATA["departmentSeq"]			= $user["departmentSeq"];
		$DATA["employeeNumber"]			= $user["employeeNumber"];
		
	
		if(empty(trim($user["password"])) == false)		$DATA["password"]			= $user["password"];

		//관리자 웹에서 수정할때  BEGIN ---------------------
		if(is_int($user["totalDistance"]) == true)			$DATA["totalDistance"]		= $user["totalDistance"];
		if(empty(trim($user["enabled"])) == false)			$DATA["enabled"]				= $user["enabled"];
		
		if(empty(trim($user["email"])) == false)
		{
			$DATA["email"]			= $user["email"];
		}
		if(empty(trim($user["oilType"])) == false)
		{
			$DATA["oilType"]			= $user["oilType"];
			
		}
		
		if(empty(trim($user["oilMileage"])) == false)
		{
			
			$DATA["oilMileage"]		= $user["oilMileage"];
		}


		//관리자 웹에서 수정할때  END ---------------------

		$this->db->Update("user", $DATA, "WHERE seq='". $user["seq"]. "'",  "사용자 수정");
	}


	function modifyTotalDistance($employeeNumber, $totalDistance)
	{
		$DATA["totalDistance"] = $totalDistance;
		$this->db->Update("user", $DATA, "WHERE employeeNumber='". $employeeNumber. "'",  "update user error");
	}
	
	function setTempPassword($employeeNumber,$password)
	{
		$DATA["password"] = $password;
		$this->db->Update("user", $DATA, "WHERE employeeNumber='". $employeeNumber. "'",  "update user error");
	}

	function setDevice($employeeNumber, $device)
	{
		$user = $this->getUser($employeeNumber);

		//기기변경시 임시 사용 중지
		if($user["lockDeviceChange"] == "Y")
		{
			//기기변경시 임시 사용 중지
			if($user["deviceId"] == "ADMIN")
			{
				//pass (관리자가 추가한 계정은 기기변경 처리 1회 생략)
			}
			else
			{
				if(trim($user["deviceId"]) != trim($device["deviceId"]))
				{
					if(empty(trim($user["deviceId"])))
					{
						$DATA["enabled"] = "N";
					}
					else
					{
						$DATA["enabled"] = "C";
					}
				}
			}
		}
		else {

			// 새로가입시
			if(empty(trim($user["deviceId"])))
			{
				$DATA["enabled"] = "N";
			}
		}

		$DATA["deviceId"]				= $device["deviceId"];
		$DATA["osType"]					= $device["osType"];
		$DATA["osVersion"]				= $device["osVersion"];
		$DATA["versionName"]			= $device["versionName"];
		$DATA["country"]				= $device["country"];
		$DATA["language"]				= $device["language"];
		$DATA["model"]					= $device["model"];


		if(empty(trim($device["pushId"])) == false)
		{
			//같은 푸시ID가 있으면 푸시ID 초기화
			$this->db->que = "UPDATE user SET pushId='' WHERE pushId='". $device["pushId"]. "'";
			$this->db->query();
			$DATA["pushId"]				= $device["pushId"];
		}

		$this->db->Update("user", $DATA, "where employeeNumber='". $employeeNumber. "'", "device update error");
	}

	function checkAuthority($employeeNumber)
	{
		$this->db->que = "SELECT * FROM user WHERE employeeNumber='". $employeeNumber. "'";
		$this->db->query();
		if($this->db->affected_rows() < 1)
		{
			return "계정이 존재하지 않습니다.";
		}
		else
		{
			$row = $this->db->getRow();
			if($row["enabled"] == "X")
			{
				return "이용이 정지된 계정 입니다.";
			}else
			{
				return "";
			}
		}
	}

	function login($employeeNumber, $password)
	{
		$result = $this->checkAuthority($employeeNumber);
		if(empty($result))
		{
			$this->db->que = "SELECT * FROM user WHERE employeeNumber='". $employeeNumber. "'";
			$this->db->query();
			$user = $this->db->getRow();

			if($user["password"] != $password)
			{
				return "비밀번호가 일치하지 않습니다.";
			}
			else
			{
				return "";
			}
		} else {
			return $result;
		}
	}

	function logout($employeeNumber){
		$result = $this->checkAuthority($employeeNumber);
		if(empty($result))
		{
			$DATA["pushId"] = "";
			$this->db->Update("user", $DATA, "WHERE employeeNumber='". $employeeNumber. "'",  "update user error");
		}else{
			return $result;
		}
		
	}


	function getAppUser($employeeNumber)
	{
		$this->db->que = "SELECT u.*, de.name AS departmentName, du.name AS dutyName FROM user AS u ";
		$this->db->que .= " LEFT JOIN duty AS du ON u.dutySeq=du.seq ";
		$this->db->que .= " LEFT JOIN department AS de ON u.departmentSeq=de.seq ";
		$this->db->que .= " WHERE u.employeeNumber='". $employeeNumber. "'";
		
		$this->db->query();
		$user = $this->db->getRow();
		$user["password"] = "";

		if($user["logoEnabled"] != "Y")
		{
			$user["logoUrl"] = "";
		}

		$this->db->que = "	SELECT email AS adminEmail, lockDistance, lockDate, lockTime, lockSaveMapPoint, 
								lockDeviceChange, lockAuto, ikey, name AS companyName, logoEnabled, logoUrl, autoStartMessage, autoStopMessage 
									FROM company";
		$this->db->query();
		$company = $this->db->getRow();

		$user["adminEmail"]				= $company["adminEmail"];
		$user["lockDistance"]			= $company["lockDistance"];
		$user["lockDate"]				= $company["lockDate"];
		$user["lockTime"]				= $company["lockTime"];
		$user["lockSaveMapPoint"]		= $company["lockSaveMapPoint"];
		$user["lockDeviceChange"]		= $company["lockDeviceChange"];
		$user["lockAuto"]				= $company["lockAuto"];
		$user["ikey"]					= $company["ikey"];
		$user["companyName"]			= $company["companyName"];
		$user["logoEnabled"]			= $company["logoEnabled"];
		$user["logoUrl"]				= $company["logoUrl"];
		$user["autoStartMessage"]		= $company["autoStartMessage"];
		$user["autoStopMessage"]		= $company["autoStopMessage"];

		$begin = date("Y"). "-". date("m"). "-01";
		$end = date("Y"). "-". date("m"). "-31";
		$this->db->que = "SELECT SUM(distance) FROM drivingLog WHERE employeeNumber='". $employeeNumber. "' AND startDate >= '". $begin. "' AND startDate <= '". $end. "'";
		$this->db->query();
		$user["thisMonthDistance"] = $this->db->getOne();
		$user["thisMonth"] = date("m");
		return $user;
	}

	function getUser($employeeNumber)
	{
		$this->db->que = "SELECT u.*, de.fullName AS departmentName ";
		$this->db->que .= " FROM user AS u ";
		$this->db->que .= " LEFT JOIN department AS de ON u.departmentSeq=de.seq ";
		$this->db->que .= " WHERE u.employeeNumber='". $employeeNumber. "'";
		
		$this->db->query();
		$user = $this->db->getRow();
		$user["password"] = "";

	// 	if($user["logoEnabled"] != "Y")
// 		{
// 			$user["logoUrl"] = "";
// 		}


// 		$begin = date("Y"). "-". date("m"). "-01";
// 		$end = date("Y"). "-". date("m"). "-31";
// 		$this->db->que = "SELECT SUM(distance) FROM drivingLog WHERE userUid='". $employeeNumber. "' AND startDate >= '". $begin. "' AND startDate <= '". $end. "'";
// 		$this->db->query();
// 		$user["thisMonthDistance"] = $this->db->getOne();
// 		$user["thisMonth"] = date("m");
		return $user;
	}


	static function getPurposes($db)
	{
		$db->que = "SELECT * FROM purpose Where purposeState = 'Y' ORDER BY sort";
		$db->query();
		return $db->getRows();
	}

	static function getCostTypes($db)
	{
		$db->que = "SELECT * FROM costType ORDER BY name ASC";
		$db->query();
		return $db->getRows();
	}

	static function getReceiptAccounts($db)
	{
		$db->que = "SELECT c.name AS receiptAccountCodeName, d.name, d.seq 
						FROM shinsegae_db.receiptAccountCode AS c 
						JOIN receiptAccountCodeDetail AS d ON d.receiptAccountCodeSeq=c.seq
					WHERE d.enabled='Y' ORDER BY d.sort ASC"; 
		$db->query();
		return $db->getRows();
	}
	


}
