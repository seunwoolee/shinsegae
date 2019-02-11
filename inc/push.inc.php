<?
class Push
{

	function Push($db)
	{
		$this->db = $db;
	}

	function getDefaultUpperDepartment()
	{
		$this->db->que = "select seq from department where deleteState = 'N' AND depth = 0 AND seq > 0 order by sort limit 1 ";
		$this->db->query();
		return $this->db->getOne();
	}
	
	function getUpperDepartment()
	{
		$this->db->que = "select seq , name from department where deleteState = 'N' AND depth = 0 and seq > 1 ";
		$this->db->query();
		$rows = $this->db->getRows();
		$upperDepartment = $this->getUpperDepartmentList($rows);
		return $upperDepartment;
	}

	function getUpperDepartmentList($rows)
	{
		$count = count($rows);
		for($i=0;$i<count($rows);$i++)
		{
			$row = $rows[$i];
			$upperDepartment .= "
							<tr>
								<td class='text-left' height='47' style='padding:0 !important'><a href='javascript:callLowerDepartment(". $row["seq"]. ");'>".$row["name"]."</a></td>
							</tr>";	
		}
		return $upperDepartment;
	}
	
	function getLowerDepartment($where)
	{
		$this->db->que = "select u.seq , u.employeeNumber , u.name , d.name as departmentName from user as u LEFT JOIN department as d ON (u.departmentSeq = d.seq) WHERE  deleteState = 'N' ".$where." order by u.departmentSeq ";
		$this->db->query();
		$rows = $this->db->getRows();
		$lowerDepartment = $this->getLowerDepartmentList($rows);
		return $lowerDepartment;
	}

	function getLowerDepartmentList($rows)
	{
		$count = count($rows);
		$firstRow = 1;
		for($i=0;$i<count($rows);$i++)
		{
			$row = $rows[$i];
			if($firstRow == 1)
			{
				$lowerDepartment .= "
									<td class='text-left'>	".$row["departmentName"]."</td>
									<td class='text-center'>".$row["name"]."</td>
									<td class='text-center'>".$row["employeeNumber"]."</td>
									<td class='text-center'><input type='checkbox' name='check[]' class='list-checkbox' value='". $row["seq"]. "' /></td>	
								</tr>";	
				$firstRow++;						
			}  
			else
			{
				$lowerDepartment .= "
								<tr>
									<td class='text-left'>	".$row["departmentName"]."</td>
									<td class='text-center'>".$row["name"]."</td>
									<td class='text-center'>".$row["employeeNumber"]."</td>
									<td class='text-center'><input type='checkbox' name='check[]' class='list-checkbox' value='". $row["seq"]. "' /></td>	
								</tr>";		
			}
		}
		return $lowerDepartment;
	}

	function getRowSpan($where)
	{
		$upperRowSpan = $this->getUpperRowSpan();
		$lowerRowSpan = $this->getLowerRowSpan($where);
		if ($lowerRowSpan > $upperRowSpan)
		{
			$rowSpan = $lowerRowSpan; 
		}
		else
		{
			$rowSpan = $upperRowSpan;
		}
		return $rowSpan;
	}
	
	function getUpperRowSpan()
	{
		$this->db->que = "select count(*) from department where deleteState = 'N' AND depth = 0 and seq > 1 ";
		$this->db->query();
		$upperRowSpan = $this->db->getOne();
		return $upperRowSpan;
	}
	
	function getLowerRowSpan($where)
	{
		$this->db->que = "select count(*) from user as u LEFT JOIN department as d ON (u.departmentSeq = d.seq) WHERE  deleteState = 'N' ".$where." order by u.departmentSeq ";
		$this->db->query();
		$lowerRowSpan = $this->db->getOne();
		return $lowerRowSpan;
	}
	
	function getCheckedArrayToStringUserseq($checkedArray)
	{
		if(is_array($checkedArray))
		{
			$userSeq =explode(",", $checkedArray);
		}
		else
		{
			$userSeq = $checkedArray;
		}
		return $userSeq;
	}
	
	function CheckUserseqStringtoArray($checkedUserSeq)
	{
		$checkedUserSeqArray = [];
		$checkedUserSeqArray = explode(",", $checkedUserSeq);
		$departmentSeq = $this->getDepartmentOfChoicedUser($checkedUserSeqArray[0]);
		return $departmentSeq;
	}
	
	function getDepartmentOfChoicedUser($seq)
	{
		$this->db->que = "select departmentSeq from user where seq=".$seq;
		$this->db->query();
		$departmentSeq = $this->db->getOne();
		return $departmentSeq;
	}

	function getPushHistory($year,$month)
	{
		$this->db->que = "select * from push where DATE_FORMAT(createDate,'%Y') =".$year." AND DATE_FORMAT(createDate,'%m') =".$month;
		$this->db->query();
		$rows = $this->db->getRows();
		
		for($i=0;$i<count($rows);$i++)
		{
			$row = $rows[$i];
			if($row["type"] == "all")
			{
				$LIST .= $this->getAllTypeList($row);// 함수
			}
			else if($row["type"] == "approver")
			{
				$LIST .= $this->getApproverTypeList($row);
			}
			else if($row["type"] == "choice")
			{
				$LIST .= $this->getChoiceTypeList($row);
			}
		}
		return $LIST;
		
	}
	
	function getAllTypeList($row)
	{
		$LIST .= '<tr>
						<th></th>
						<td>
							<div class="txtTbl1">'.$row["createDate"].'</div>
							<div class="txtTbl2 mt10">전체 푸시</div>
							<div class="txtTbl3 mt10">
								'.$row["message"].'
							</div>
							<div class="tdArrow"><i class="fa fa-chevron-down fa-2x"></i></div>
						</td>
				</tr>';
		return $LIST; 
	}
	
	function getApproverTypeList($row)
	{
		$LIST .= '<tr>
						<th></th>
						<td>
							<div class="txtTbl1">'.$row["createDate"].'</div>
							<div class="txtTbl2 mt10">승인권자 푸시</div>
							<div class="txtTbl3 mt10">
								'.$row["message"].'
							</div>
							<div class="tdArrow"><i class="fa fa-chevron-down fa-2x"></i></div>
						</td>
				</tr>';
		return $LIST; 
	}
	
	function getChoiceTypeList($row)
	{
		$departmentFullName = $this->getChoicedUserDepartment($row["departmentSeq"]);
		$userDATA = $this->getFirstChoicedNameAndCount($row["userSeq"]);
		$LIST .=	'<tr>
					<th></th>
					<td>
						<div class="txtTbl1">'.$row["createDate"].'</div>
						<div class="txtTbl2 mt10">'.$departmentFullName.'->'.$userDATA["userName"].' 외 '.$userDATA["count"].'건</div>
						<div class="txtTbl3 mt10">
							'.$row["message"].'
						</div>
						<div class="tdArrow"><i class="fa fa-chevron-down fa-2x"></i></div>
					</td>
				</tr>';
		return $LIST; 
	}
	
	function getChoicedUserDepartment($departmentSeq)
	{
		$this->db->que = "SELECT fullName FROM department where seq =".$departmentSeq;
		$this->db->query();
		$departmentFullName = $this->db->getOne();
		return $departmentFullName; 
	}

	function getFirstChoicedNameAndCount($userSeq)
	{
		$Arr = [];
		$userDATA = [];
		$Arr = explode(",",$userSeq);
		$userDATA["count"] = count($Arr) -1;
		$this->db->que = "SELECT name From user WHERE seq=".$Arr[0];
		$this->db->query();
		$userDATA["userName"] = $this->db->getOne();
		return $userDATA; 
	}

	function getTitleMent($checkedValue)
	{
		$this->db->que = "SELECT startTime FROM drivingLog where seq=".$checkedValue;
		$this->db->query();
		$startTime = $this->db->getOne();
		$startTime = strtotime($startTime); 
		$month = date("m",$startTime);
		$day = date("d",$startTime);
		$time = date("H:i",$startTime);
		return $month."월".$day."일".$time;
	}
	
	function getPushIdRows($checkedValue)
	{
		$this->db->que = " SELECT u.pushId FROM drivingLog as dl LEFT JOIN user as u ON (dl.employeeNumber = u.employeeNumber) ";
		$this->db->que .= " WHERE dl.seq IN (".$checkedValue.") AND LENGTH(pushId) > 10 ";
		$this->db->query();
		$rows = $this->db->getRows();
		return $rows;
	}
	
	function getRejectMessageMent($message)
	{
		if($message == 1)
		{
			$message = "업무 연관성 확인 필요";
			return $message;
		}
		else if($message == 2)
		{
			$message = "부서 변경 미처리";
			return $message;
		}
		return $message;
	}
/*	
	function getApproveMessageMent($message)
	{

	}
*/
}
?>