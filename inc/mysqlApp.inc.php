<?

/**
*
*
*		@package MySQL DB Interface Class
*/

class Mysql
{

    var $HostName = "";		// 특별한 이유없는한 localhost
    var $UserName = "";		// User ID
    var $UserPass = "";			// User Password
    var $DBName = "";			// Database Name


	var $Conn="";					// Database Handler
	var $result="";					// result
	var $row="";
	var $que="";

	var $Auto_Free	= 0;     ## Set to 1 for automatic mysql_free_result()
	var $Debug			= 0;     ## Set to 1 for debugging messages.

	/**
	* Function : 생성자
	* Input : Database Connection Information
	* Output : None
	*/
	function Mysql($HostName="", $UserName="", $UserPass="", $DBName="")
	{
		//$this->Language = LANGUAGE;
		if(empty($HostName))
		{
			$this->HostName=_DBHOST;
			$this->UserName=_DBID;
			$this->UserPass=_DBPASS;
			$this->DBName=_DBNAME;
		}
		else
		{
			$this->HostName=$HostName;
			$this->UserName=$UserName;
			$this->UserPass=$UserPass;
			$this->DBName=$DBName;
		}
		

		$this->connect();
	}



	/*
	** Function : Connect
	** Input : None
	** Output : None
	*/
	function connect()
	{
		$this->Conn=mysqli_connect($this->HostName, $this->UserName, $this->UserPass, $this->DBName);
		if (!$this->Conn) //연결실패
		{
			$this->errMsg("Database Connection Error, DB 계정과 암호를 체크하세요.", "");
		}
		//else //연결성공
		//{
		//	if (!(mysql_select_db($this->DBName))) //Database 선택 실패
		//		$this->errMsg("Database Select Error, Database 이름을 체크하세요.", "");
		//}
	}






	/*
	** Function : Connect
	** Input : None
	** Output : None
	*//*
	function connect()
	{
		$this->Conn=mysql_connect($this->HostName, $this->UserName, $this->UserPass, true);
		if (!$this->Conn) //연결실패
		{
			$this->errMsg("Database Connection Error, DB 계정과 암호를 체크하세요.", "");
		}
		else //연결성공
		{
			if (!(mysql_select_db($this->DBName))) //Database 선택 실패
				$this->errMsg("Database Select Error, Database 이름을 체크하세요.", "");
		}
	}*/


	/*
	** Function : nQuery
	** Input : Query String, Error Message
	** Output : Recordset
	** Descript : mysql_query()
	*/
	function Query($msg="")
	{
		if (!($this->result=mysqli_query($this->Conn,$this->que)))
		{
			$this->errMsg($msg);
		} else {
			if ($this->Debug == 1) $this->PrintQue($msg);
		}
		return $this->result;
	}


	/*
	** Function : rQuery
	** Input : Query string, Error message
	** Output : Record
	** Descript : mysqli_result()
	*/


	function InsertDB($table, $data, $msg="")
	{
		if(!is_array($data))
			$this->errMsg("입력데이터에 오류가 있습니다.", "$data가 배열이 아닙니다.");

		$i=0;
		foreach ($data as $field => $value)
		{
			if (0 < $i) {						// field와 value가 2개 이상일 경우 , 자동 입력
				$field_que.=",";
				$value_que.=",";
			}
			if(preg_match("/^_/", $field))			// field 이름이 _ 로 시작할경우에는 field에 들어가는 값을 함수로 가정 _ 를 때어내고, 따옴표를 넣지 않는다.
			{
				$field = substr($field, 1);
				$quot = "";
			} else {
				$quot = "'";
			}
			$field_que.=$field;							// field에 해당하는 쿼리
			$value_que.=$quot.$value.$quot;		// value에 해당하는 쿼리
			$i++;
		}
		$msg = "[ <i>$table</i> ] Table에 Data 입력 : $msg";
		$this->que = "insert into $table ($field_que) values ($value_que)";
		$this->query($msg);
	}


	#
	#	Insert DB
	#
	function Insert($table, $data, $msg="")
	{
		$this->InsertDB($table, $data, $msg);
	}


	function UpdateDB($table, $data, $where="", $msg="")
	{
		if(!is_array($data))
			$this->errMsg("입력데이터에 오류가 있습니다.", "$data가 배열이 아닙니다.");

		$i=0;
		foreach ($data as $field => $value) 
		{
			if (0 < $i) {						// field와 value가 2개 이상일 경우 , 자동 입력
				$field_que.=",";
				$sub_que.=",";
				$value_que.=",";
			}
			if(preg_match("/^_/", $field))			// field 이름이 _ 로 시작할경우에는 field에 들어가는 값을 함수로 가정 _ 를 때어내고, 따옴표를 넣지 않는다.
			{
				$field = substr($field, 1);
				$quot = "";
			} else {
				$quot = "'";
			}
			$field_que.=$field;						// field에 해당하는 쿼리
			$sub_que.=$field."=".$quot.$value.$quot; // 서브 쿼리 생성
			$value_que.=$quot.$value.$quot; // 로그남기기 쿼리 생성
			$i++;
		}

		if($where) {
			if(!preg_match("/^where/i", trim($where))) $where = "where " . $where;
			$sub_que.=" ".$where; // where 쿼리가 존재할 경우 입력
		}
		$msg = "[ <i>$table</i> ] Table에 Data 수정 : $msg";
		$this->que = "update $table set $sub_que"; //Update 쿼리문
		$this->query($msg);
	}

	#
	#	UpdateDB
	#
	function Update($table, $data, $where="", $msg="")
	{
		$this->UpdateDB($table, $data, $where, $msg);
	}

	#
	#	데이터 삭제
	#
	function DeleteDB($table, $where, $msg)
	{
		if($where) {
			if(!preg_match("/^where/i", trim($where))) $where = "where " . $where;
			$sub_que.=" ".$where; // where 쿼리가 존재할 경우 입력
		}
		$msg = "[ <i>$table</i> ] Table에 Data 삭제 : $msg";	
		$this->que = "delete from $table $sub_que";
		lib::Plog ($this->que);
		$this->Query($msg);
	}


	#
	#	DeleteDB
	#
	function Delete($table, $where, $msg)
	{
		$this->DeleteDB($table, $where, $msg);
	}

	#
	#	한개 값을 구함
	#

	function getOne()
	{ 
		$data=mysqli_fetch_row($this->result);
		return $data[0];
	}	
	
	#
	#	결과 값을 구함
	#
	function getRow()
	{
		$this->row = mysqli_fetch_assoc($this->result);

		$stat = is_array($this->row);
		if (!$stat && $this->Auto_Free) {
		  $this->free();
		}

		return $this->row;
	}
	
	function getRowN()
	{
		$this->row = mysqli_fetch_row($this->result);

		$stat = is_array($this->row);
		if (!$stat && $this->Auto_Free) {
		  $this->free();
		}

		return $this->row;
	}

	function getRows()
	{
		$i=0;
		while($row = $this->getRow())
		{
			$rows[$i] = $row;
			$i++;
		}

		return $rows;
	}


	function affected_rows()
	{
		return mysqli_affected_rows($this->Conn);
	}

	function insert_id()
	{
		return mysqli_insert_id ($this->Conn);
	}



	/*
	** Function : CLOSE
	** Input : None
	** Output : None
	** Descript : mysql_close()
	*/
	function close()
	{
		if($this->Conn)
		{
			mysqli_close($this->Conn);
			$this->destroy();
		}
	}


	function destroy()
	{
		unset($this->Conn); unset($this->UserName);
		unset($this->UserPass); unset($this->HostName);
		unset($this->DBName);
	}


	//Print Error Message and Exit
	function errMsg($msg)
	{
		$mesg="<p><b>DB Error Message !!</b></p>";
		$mesg.=mysqli_errno($this->Conn)." : ".mysqli_error($this->Conn)."<p><b>User Message</b> :<br> ".$msg."<p><b>Query String</b> :<br> ".$this->que;

		$this->PrintMsg($mesg);
		exit;
	}

	function PrintQue($msg)
	{
		$mesg = "<p><b>Query String</b> : $msg<br> ".$this->que;
		$this->PrintMsg($mesg);
	}

	function PrintMsg($msg) {
		echo $msg;
	}

}


?>