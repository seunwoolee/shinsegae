<?

class Company extends Mysql
{
	var $DATA;						//$_POST 로 넘어온 파라메터
	var $db;							//mysql Class

	var $autoLoginParamUid = "Cds_cid";
	var $autoLoginParamKey = "Cds_key";


	function Company($DATA)
	{
		$this->DATA = $DATA;
		$this->db = new Mysql();
	}	


	//##########################################################################
	//##########################################################################
	// 로그인
	//##########################################################################
	//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
	// 신세계 계정 존재 여부
	//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
	function getEnabled()
	{
		$this->db->que = "SELECT * FROM user WHERE employeeNumber='". $this->DATA["cid"]. "'";

		$this->db->query();
		if($this->db->affected_rows() > 0)
		{
			$row = $this->db->getRow();
			return $row["enabled"];
		}
		else
		{
			return null;
		}
	}
	
	//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
	// 신세계 승인권자 로그인
	//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
	function getApproverOrAdmin()
	{
		
		$this->db->que = "SELECT adminYn , lastReset FROM user WHERE employeeNumber='". $this->DATA["cid"]. "'";
		$this->db->query();
		$checkAdminAndLastPasswordChagne = $this->db->getRow();
		if($checkAdminAndLastPasswordChagne["adminYn"] == "Y")
		{
			if(date("Y-m-d H:i:s") > $checkAdminAndLastPasswordChagne["lastReset"])
			{
				return "P";
			}
			else
			{
				return "Y";
			}
		}
		
		$this->db->que = "SELECT seq FROM user WHERE employeeNumber='". $this->DATA["cid"]. "'";
		$this->db->query();
		if($this->db->affected_rows() > 0) //승인권자 확인
		{	
			$this->db->que = "SELECT count(seq) FROM departmentView WHERE approverEmployeeNumber ='". $this->DATA["cid"]. "'";
			$this->db->query();
			$count = $this->db->getOne();
			if($count > 0)
			{
				return "Y";
			}
			else
			{
				return "N";
			}
		}
		else
		{
			return null;
		}
	}

	//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
	// 유저 정보
	//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
	function getCompany($cid)
	{
		$this->db->que = "SELECT * FROM user WHERE employeeNumber='". $cid. "'";

		$this->db->query();
		if($this->db->affected_rows() > 0)
		{
			return $this->db->getRow();
		}
		else
		{
			return null;
		}
	}

	
	//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
	// 비밀번호 일치 여부
	//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
	
	
	function checkPassword()
	{
 		
		$securePassword = trim(base64_encode(hash('sha256', $this->DATA["password"], true))); 
		$this->db->que = "SELECT seq FROM user WHERE employeeNumber='". $this->DATA["cid"]. "' AND  password='". $securePassword. "'";
		//$this->db->que = "SELECT seq FROM user WHERE employeeNumber='". $this->DATA["cid"]. "'";
		$this->db->query();

		if($this->db->affected_rows() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	


	function setAutoLogin()
	{
		if($this->DATA["autoLogin"] == "Y")
		{
			$DATA["autoLoginKey"] = uniqid();
			$this->db->Update("user", $DATA, "where employeeNumber='". $this->DATA["cid"]. "'", "X");

			$time = time()+60*60*24*365;
			setcookie($this->autoLoginParamUid, $this->DATA["cid"], $time , '/');
			setcookie($this->autoLoginParamKey, $DATA["autoLoginKey"], $time , '/');
		}
		else
		{
			//$DATA["autoLoginKey"] = "";
			//$this->db->Update("admin", $DATA, "where uid='". $this->DATA["uid"]. "'", "update error");
			setcookie($this->autoLoginParamUid, "", 0 , '/');
			setcookie($this->autoLoginParamKey, "", 0 , '/');
		}
	}
	/*
	function eventAuditLogin($cid)
	{
		$DATA["employeeNumber"] = $cid;
		$DATA["eventType"] 		= "Login";
		$DATA["description"]	= $cid."로그인";
		$DATA["ip"] 			= $_SERVER['REMOTE_ADDR'];
		$DATA["TableName"]		= "Login";
		$this->db->Insert("eventAudit", $DATA, "eventAudit insert error");
	}	
	*/
	function eventAuditLogin($cid)
	{
		$DATA["employeeNumber"] = $cid;
		$DATA["eventType"] 		= "입력";
		$DATA["description"]	= $cid."로그인";
		$DATA["ip"] 			= $_SERVER['REMOTE_ADDR'];
		$DATA["TableName"]		= "Login";
		$DATA["afterData"]		= "Login";
		$this->db->Insert("eventAudit", $DATA, "eventAudit insert error");
	}	
	

	function eventAuditLogout($cid)
	{
		$DATA["employeeNumber"] = $cid;
		$DATA["eventType"] 		= "Logout";
		$DATA["description"]	= $cid."로그아웃";
		$DATA["ip"] 			= $_SERVER['REMOTE_ADDR'];
		$DATA["TableName"]		= "Logout";
		$this->db->Insert("eventAudit", $DATA, "eventAudit insert error");
	}

	function checkAutoLogin()
	{
		$cid = $_COOKIE[$this->autoLoginParamUid];
		$key = $_COOKIE[$this->autoLoginParamKey];

		if(strlen($key) > 5)
		{
			$this->db->que = "select * from user where employeeNumber= '".$cid."'";
			$this->db->query();

			if($this->db->affected_rows() > 0)
			{
				$row = $this->db->getRow();
				if(strcmp($key, $row["autoLoginKey"]) == false)
				{
					session_start();
					$_SESSION["OMember_id"] = $cid;
					$_SESSION["OMember_admin"] = $row["adminYn"];
					$this->eventAuditLogin($cid);
					return true;
				}
			}
		}
		
		return false;
	}
	

	function logout($cid)
	{
		session_start();
		$_SESSION["OMember_id"] = "";
		$_SESSION["OMember_admin"] = "";
		$_SESSION["passwordExpire"] = "";
		setcookie($this->autoLoginParamUid, "", 0 , '/');
		setcookie($this->autoLoginParamKey, "", 0 , '/');
		//$this->eventAuditLogout($cid);
	}

	function dbClose()
	{
		$this->db->close();
	}
}