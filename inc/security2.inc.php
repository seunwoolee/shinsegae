<?
class security2
{
	var $DATA;						//$_POST 로 넘어온 파라메터
	var $db;							//mysql Class

	function security2($DATA,$db)
	{
		$this->DATA = $DATA;
		$this->db = $db;
	}
	
	function getActivated()
	{
		$this->db->que = "SELECT * FROM company WHERE cid='". $this->DATA["cid"]. "'";
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
	
	
	
	function BeforeLogin($value,$cid) {	
	  $this->db->que = "SELECT attempts, LastLogin FROM LoginAttempts WHERE ip = '$value'";
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
			$this->db->que = "UPDATE company SET activated = 'N' WHERE cid = '$cid'"; 
			$this->db->query();
			//return false;
		} 
		else 
		{
			$this->db->que = "UPDATE company SET activated = 'Y' WHERE cid = '$cid'"; 
			$this->db->query();
		    $this->db->que =  "UPDATE LoginAttempts SET attempts=0, lastlogin=NOW() WHERE ip = '$value'"; 
			$this->db->query();
		 // return 0; 
		}   
	  }
	
	 // return 0; 
		
	}


	function AfterUnsuccessfulLogin($value) {
	   //Increase number of attempts. Set last login attempt if required.
	   $this->db->que = "SELECT * FROM LoginAttempts WHERE IP = '$value'"; 
	   $this->db->query();
	   $data = $this->db->getRow();
	   //var_dump ($data[Attempts]);
		  
	   
	   if($data)
	   {
		 $attempts = $data["Attempts"]+1;         
		 if($attempts>=3) {
		   $this->db->que =  "UPDATE LoginAttempts SET attempts=".$attempts.", lastlogin=NOW() WHERE ip = '$value'"; 
		   $this->db->query();
		 }
		 else {
		   $this->db->que = "UPDATE LoginAttempts SET attempts=".$attempts." WHERE ip = '$value'"; 
		   $this->db->query();
		 }
	   }
	   else {
		 $this->db->que ="INSERT INTO LoginAttempts (attempts,IP,lastlogin) values (1, '$value', NOW())";
		 $this->db->query();
	   }
	}
	
	function AfterSuccessfulLogin($value) {
		$this->db->que = "UPDATE LoginAttempts SET attempts = 1 WHERE ip = '$value'"; 
	    $this->db->query();
	  //return  $this->db->query();
	}
}
?>
