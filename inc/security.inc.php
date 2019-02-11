<?

class Security extends Mysql
{
	var $DATA;
	var $db;
	
	
	function Security($DATA)
	{
		$this->DATA = $DATA;
		$this->db = new Mysql();
	}
	
	function getActivated() 
	{
		$this->db->que = "SELECT * FROM user WHERE employeeNumber='". $this->DATA["cid"]. "'"; 
		$this->db->query();
		if($this->db->affected_rows() > 0)
		{
			$row = $this->db->getRow();
			return $row["activated"];
		}
		else
		{
			return null;
		}
	}
	
	
	
	function BeforeLogin($ip,$cid) 
	{		
	  $this->db->que = "SELECT attempts, LastLogin FROM LoginAttempts WHERE ip = '".$ip."' and employeeNumber = '".$cid."'";
	  $this->db->query();
	  $data = $this->db->getRow();
	
	  //if (!$data || !strlen($data["LastLogin"])) return true; 
	  
	  $atime = strtotime($data["LastLogin"]); 
	  $diff = (time()-$atime)/60;
	  //Verify that at least one login attempt is in database 

	
	  if ($data["attempts"] >= 3) 
	  { 
		if(intval($diff)<1) 
		{ 
			$this->db->que = "UPDATE user SET activated = 'N' WHERE employeeNumber = '$cid'"; 
			$this->db->query();
			//return false;
		} 
		else 
		{
			$this->db->que = "UPDATE user SET activated = 'Y' WHERE employeeNumber = '$cid'"; 
			$this->db->query();
		    $this->db->que =  "UPDATE LoginAttempts SET attempts=0, lastlogin=NOW() WHERE ip = '$ip' AND employeeNumber = '$cid' "; 
			$this->db->query();
			return $data["attempts"]; 
		}   
	  }
	
	 // return 0; 
		
	}


	function AfterUnsuccessfulLogin($ip,$cid) 
	{
	   //Increase number of attempts. Set last login attempt if required.
	   $this->db->que = "SELECT * FROM LoginAttempts WHERE IP = '".$ip."' AND employeeNumber = '".$cid."'"; 
	   $this->db->query();
	   $data = $this->db->getRow();
		  
	   
	   if(isset($data))
	   {
		 $attempts = $data["Attempts"]+1;         
		 if($attempts>=3) 
		 {
		   $this->db->que =  "UPDATE LoginAttempts SET attempts=".$attempts.", lastlogin=NOW() WHERE IP = '".$ip."' AND employeeNumber = '".$cid."'"; 
		   $this->db->query();
		 }
		 else 
		 {
		   $this->db->que = "UPDATE LoginAttempts SET attempts=".$attempts." WHERE IP = '".$ip."' AND employeeNumber = '".$cid."'";
		   $this->db->query();
		 }
	   }
	   else 
	   {
			$this->db->que ="INSERT INTO LoginAttempts (attempts,IP,lastlogin,employeeNumber) values (1, '$ip', NOW(),'$cid')";
	   }
		 $this->db->query();
	}
	
	function AfterSuccessfulLogin($ip,$cid) 
	{
		$this->db->que = "UPDATE LoginAttempts SET attempts = 0 WHERE IP = '".$ip."' AND employeeNumber = '".$cid."'";
	    $this->db->query();		
		//user lastLogin 
		$date = date("Y-m-d H:i:s");
		$this->db->que = "UPDATE user SET lastLogin = '".$date."' WHERE employeeNumber = '".$cid."'";
	    $this->db->query();
	  //return  $db->query();
	}
}
?>
