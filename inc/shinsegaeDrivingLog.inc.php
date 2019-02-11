<?
class ShinsegaeDrivingLog
{
	var $db;
	
	
	function ShinsegaeDrivingLog($db)
	{
		$this->db = $db;	
	}

	function getUpperDepartmentOfApprover($approverUpperDep)
	{
		$this->db->que = "select Distinct parentSeq from departmentView where approverEmployeeNumber = '".$_SESSION["OMember_id"]."'";
		$this->db->query();
		$list = $this->db->getRows();
		$approverUpperDepList = $this->getUpperDepartmentOfApproverList($list,$approverUpperDep);
		return $approverUpperDepList;
	}
	
	function getUpperDepartmentOfApproverList($list,$approverUpperDep)
	{
		include_once "department.inc.php";
		$department	 = new Department($this->db);
		
		for($i=0;$i<count($list);$i++)
		{
			$row = $list[$i];
			$parentName = $department->getParentName($row["parentSeq"]);
			$approverUpperDepList .= "<option value='". $row["parentSeq"]. "'" .($approverUpperDep == $row["parentSeq"] ? 'selected="selected"' : ''  ). ">".$parentName. "</option>\n";
		}
		return $approverUpperDepList;
	}
	
	function getDepartmentsOfApproverArray()
	{
		$departmentsOfApproverArray = []; // In 비교할때 승인권자 넣을것
		$this->db->que = "select seq from departmentView where approverEmployeeNumber = '".$_SESSION["OMember_id"]."'";
		$this->db->query();
		while($row = $this->db->getRow())
		{
			array_push($departmentsOfApproverArray,$row["seq"]);
		}
		return $departmentsOfApproverArray;
	}
	
	function getLowerDepartmentOfApprover($approverUpperDep,$approverLowerDep)
	{
		$this->db->que = "select seq , name from departmentView where approverEmployeeNumber = '".$_SESSION["OMember_id"]."' AND parentSeq = ".$approverUpperDep;
		$this->db->query();
		while($row = $this->db->getRow())
		{
			$approverlowerDepList .= "<option value='". $row["seq"]. "'" .($approverLowerDep == $row["seq"] ? 'selected="selected"' : ''  ). ">".$row["name"]. "</option>\n";
		}	
		return $approverlowerDepList;
	}

	function getTotalCount($where)
	{
		$this->db->que = "SELECT COUNT(dl.seq) FROM drivingLog AS dl ";
		$this->db->que .= " LEFT JOIN department as d ON (dl.departmentSeq = d.seq) WHERE dl.deleteState = 'N' ".$where;
		$this->db->query();
		$totalCount = $this->db->getOne();
		return $totalCount;
	}
	
	function getRejectTotalCount($where)
	{

		$this->db->que = "SELECT COUNT(dl.seq) FROM drivingLog AS dl ";
		$this->db->que .= " LEFT JOIN department as d ON (dl.departmentSeq = d.seq) WHERE submitState = 'X' ".$where;
		$this->db->query();
		$totalCount = $this->db->getOne();
		return $totalCount;
	}
	
	function getMainListRows($where,$limitStartRow,$limitFetchSize)
	{
		$this->db->que = " select  dl.seq , dl.startDate, dl.submitState , dl.departmentName , dl.name , dl.employeeNumber  ";
		$this->db->que .= " , d.code , p.purposeName , dl.startAddress ,dl.stopAddress , dl.costName , dl.purposeLocation , dl.distance ";
		$this->db->que .= " , sum(r.amount) as amount , count(r.amount) as count , dl.submitdate , dl.approveDate";
		$this->db->que .= " , dv.approverName , dv.approverEmployeeNumber , dv.approverDepartmentName , dv.approverDepartmentCodeCode ";
		$this->db->que .= " from (select dl.seq from drivingLog as dl LEFT JOIN department as d ON (dl.departmentSeq = d.seq)  WHERE submitState <> 'X' and dl.deleteState = 'N'  ". $where ." ORDER BY  startDate DESC, seq DESC LIMIT  ". $limitStartRow. ", ". $limitFetchSize.") as q ";
		$this->db->que .= "  JOIN drivingLog as dl ON (q.seq = dl.seq) ";
		$this->db->que .= " LEFT JOIN department as d ON (dl.departmentSeq = d.seq) ";
		$this->db->que .= " LEFT JOIN purpose AS p ON (dl.purpose=p.purposeType) ";
		$this->db->que .= " LEFT JOIN receipt AS r ON (dl.seq=r.drivingLog_seq)  ";
		$this->db->que .= " LEFT JOIN departmentView2 AS dv ON (dl.departmentSeq=dv.seq) ";
		$this->db->que .= " group by dl.seq ORDER BY startDate DESC, seq DESC ";
		$this->db->query();
		$getMainListRows = $this->db->getRows();
		$LIST = $this->getMainList($getMainListRows);
		return $LIST;
	}
	
	function getRejectMainListRows($where,$limitStartRow,$limitFetchSize)
	{
		$this->db->que = " select dl.adminRejectYn , dl.seq , dl.startDate, dl.submitState , dl.departmentName , dl.name , dl.employeeNumber  ";
		$this->db->que .= " , d.code , p.purposeName , dl.startAddress ,dl.stopAddress , dl.costName , dl.purposeLocation  , dl.distance ";
		$this->db->que .= " , sum(r.amount) as amount , count(r.amount) as count , dl.submitdate , dl.rejectDate";
		$this->db->que .= " , dv.approverName , dv.approverEmployeeNumber , dv.approverDepartmentName , dv.approverDepartmentCodeCode ";
		$this->db->que .= " from (select dl.seq from drivingLog as dl LEFT JOIN department as d ON (dl.departmentSeq = d.seq)  WHERE submitState = 'X' ". $where ." ORDER BY  startDate DESC, seq DESC LIMIT  ". $limitStartRow. ", ". $limitFetchSize.") as q ";
		$this->db->que .= "  JOIN drivingLog as dl ON (q.seq = dl.seq) ";
		$this->db->que .= " LEFT JOIN department as d ON (dl.departmentSeq = d.seq) ";
		$this->db->que .= " LEFT JOIN purpose AS p ON (dl.purpose=p.purposeType) ";
		$this->db->que .= " LEFT JOIN receipt AS r ON (dl.seq=r.drivingLog_seq)  ";
		$this->db->que .= " LEFT JOIN departmentView2 AS dv ON (dl.departmentSeq=dv.seq) ";
		$this->db->que .= " group by dl.seq ORDER BY startDate DESC, seq DESC ";
		$this->db->query();
		$getRejectMainListRows = $this->db->getRows();
		$LIST = $this->getRejectMainList($getRejectMainListRows);
		return $LIST;
	}
	
	function isNullCheckApproveDate($checkApproveDate)
	{
		if($checkApproveDate == null)
		{
			$approveDate = "";
		}
		else
		{
			$approveDate = strToTime($checkApproveDate);
			$approveDate = date("Y.m.d", $approveDate);
		}
		return $approveDate;
	}
	
	function isNullCheckRejectDate($checkRejectDate)
	{
		if($checkRejectDate == null)
		{
			$rejectDate = "";
		}
		else
		{
			$rejectDate = strToTime($checkRejectDate);
			$rejectDate = date("Y.m.d", $rejectDate);
		}
		return $rejectDate;
	}
	
	function isNullCheckAmountAndCount($checkAmount,$checkCount)
	{
		if($checkAmount == null)
		{
			$amount = "";
			
		}
		else
		{
			$amount =  number_format($checkAmount). "원<br />" .$checkCount."건";
		}
		return $amount;
	}
	
	function isNullCheckGpsFaked($checkGpsFaked)
	{
		if($row["gpsFaked"] == 1) 
		{
			$gpsFake["mapBtnColor"] = "btn-new-find";
			$gpsFake["title"] 		= "GPS 모의 위치 어플 사용 추정";
		}
		else
		{
			$gpsFake["mapBtnColor"] = "btn-new-ok";
			$gpsFake["title"]		= "";
		}
		return $gpsFake;
	}
	
	function isNullCheckStartAddress($checkStartAddress)
	{
		if($checkStartAddress == "") 
		{
			$startAddress = "<span style='color:#e74c3c'>출발지 정보 없음</span>";
		}
		else
		{
			$startAddress = $checkStartAddress;
		}
		return $startAddress;
	}
	
	function approvalOrNotCheck($checkApproval)
	{
		if($checkApproval == "Y")
		{
			$enabledText = "<span class='label label-info'> 승인 </span>";
		} 					
		else if($checkApproval == "N")
		{
			$enabledText = "<span class='label label-danger'> 미승인 </span>";
		}
		return $enabledText;
	}
	
	function rejectorOrNotCheck($checkRejector)
	{
		if($checkRejector == "Y")
		{
			$enabledText = "<span class='label label-danger'> 관리자 </span>";
		} 					
		else
		{
			$enabledText = "<span class='label label-warning'> 승인권자 </span>";
		}
		return $enabledText;
	}
	
	function getReciptCount($seq)
	{
		$this->db->que = " select count(seq) from receipt Where drivingLog_seq = ".$seq;
		$this->db->query();
		$ReciptCount = $this->db->getOne();
		return $ReciptCount;
	}
	
	function isNullCheckRecipt($ReciptCount,$seq)
	{
		if($ReciptCount > 0)
		{
			$ReceiptRow = "<td align='center'><a href='javascript:popupReceiptImage(". $seq. ");' class='thumb btn btn-sm btn-new-find'><i class='fa fa-file-image-o'></i></a></td>";
		}
		else
		{
			$ReceiptRow = "<td align='center'></td>";
		}
		return $ReceiptRow;
	}
	
	function getMainList($getMainListRows)
	{
		$hour24 = 60 * 60 * 24;
		for($i=0;$i<count($getMainListRows);$i++)
		{
			$row = $getMainListRows[$i];
			
			$submitdate = strToTime($row["submitdate"]);
			$approveDate = $this->isNullCheckApproveDate($row["approveDate"]);
			$amount = $this->isNullCheckAmountAndCount($row["amount"],$row["count"]);
			$startDate = strToTime($row["startDate"]);
			$startTime = strToTime($row["startTime"]);
			$stopTime = strToTime($row["stopTime"]);
			$gpsFake = $this->isNullCheckGpsFaked($row["gpsFaked"]);
			$startAddress = $this->isNullCheckStartAddress($row["startAddress"]);
			$enabledText = $this->approvalOrNotCheck($row["submitState"]);
			$ReciptCount = $this->getReciptCount($row["seq"]);
			$ReceiptRow = $this->isNullCheckRecipt($ReciptCount,$row["seq"]);
			$LIST .= "<tr height='30'>
							<td align='center'><input type='checkbox' name='check[]' class='list-checkbox' value='". $row["seq"]. "' /></td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>".$enabledText."</td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>". $row["departmentName"]. "<br />". $row["departmentCode"]."</td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>". $row["name"]. "<br />". $row["employeeNumber"]. "</td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>". $row["code"]. "</td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>". $row["purposeName"]. "</td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>". $startAddress. "</td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>". $row["stopAddress"]. "</td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>". $row["costName"]. "</td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>". $row["purposeLocation"]. "</td>
							<td align='center' style='cursor:pointer' onclick='popupWrite(". $row["seq"]. ")'>". number_format($row["distance"]). " km</td>
							<td align='center' style='cursor:pointer' onclick='popupWrite(". $row["seq"]. ")'>". $amount."</td>
							<td align='center' style='cursor:pointer' onclick='popupWrite(". $row["seq"]. ")'><b>". date("Y.m.d", $submitdate) . "</b></td>
							<td align='center' style='cursor:pointer' onclick='popupWrite(". $row["seq"]. ")'>". $approveDate ."</td>
							<td align='center' style='cursor:pointer' onclick='popupWrite(". $row["seq"]. ")'>". $row["approverName"]. "<br />". $row["approverEmployeeNumber"]. "</td>
							<td align='center' style='cursor:pointer' onclick='popupWrite(". $row["seq"]. ")'>". $row["approverDepartmentName"]. "<br />". $row["approverDepartmentCodeCode"]. "</td>"
							.$ReceiptRow;

			$LIST .= "<td align='center'><a href='map.html?seq=". $row["seq"]. "' target='_blank' class='btn btn-sm ". $gpsFake["mapBtnColor"]. "' title='". $gpsFake["title"]. "'><i class='fa fa-map'></i> Map</a></td>";
			$LIST .= "</tr>";
		}
		
		if(empty($LIST))
		{
			$LIST = "	<tr height='30'>
							<td class='center' colspan='18' style='height:80px;'>데이터가 없습니다.</td>
						</tr>";
		}
		
		return $LIST;
	}		
	
	function getRejectMainList($getRejectMainListRows)
	{
		$hour24 = 60 * 60 * 24;
		for($i=0;$i<count($getRejectMainListRows);$i++)
		{
			$row = $getRejectMainListRows[$i];
			$submitdate = strToTime($row["submitdate"]);
			$rejectDate = strToTime($row["rejectDate"]);
			$amount = $this->isNullCheckAmountAndCount($row["amount"],$row["count"]);
			$startDate = strToTime($row["startDate"]);
			$startTime = strToTime($row["startTime"]);
			$stopTime = strToTime($row["stopTime"]);
			$gpsFake = $this->isNullCheckGpsFaked($row["gpsFaked"]);
			$startAddress = $this->isNullCheckStartAddress($row["startAddress"]);
			$enabledText = $this->rejectorOrNotCheck($row["adminRejectYn"]);
			$ReciptCount = $this->getReciptCount($row["seq"]);
			$ReceiptRow = $this->isNullCheckRecipt($ReciptCount,$row["seq"]);
			$LIST .= "<tr height='30'>
							<td align='center'><input type='checkbox' name='check[]' class='list-checkbox' value='". $row["seq"]. "' /></td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>".$enabledText."</td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>". $row["departmentName"]. "<br />". $row["departmentCode"]."</td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>". $row["name"]. "<br />". $row["employeeNumber"]. "</td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>". $row["code"]. "</td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>". $row["purposeName"]. "</td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>". $startAddress. "</td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>". $row["stopAddress"]. "</td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>". $row["costName"]. "</td>
							<td align='center' style='cursor:pointer;' onclick='popupWrite(". $row["seq"]. ")'>". $row["purposeLocation"]. "</td>
							<td align='center' style='cursor:pointer' onclick='popupWrite(". $row["seq"]. ")'>". number_format($row["distance"]). " km</td>
							<td align='center' style='cursor:pointer' onclick='popupWrite(". $row["seq"]. ")'>". $amount."</td>
							<td align='center' style='cursor:pointer' onclick='popupWrite(". $row["seq"]. ")'><b>". date("Y.m.d", $submitdate) . "</b></td>
							<td align='center' style='cursor:pointer' onclick='popupWrite(". $row["seq"]. ")'>". date("Y.m.d",$rejectDate) ."</td>
							<td align='center' style='cursor:pointer' onclick='popupWrite(". $row["seq"]. ")'>". $row["approverName"]. "<br />". $row["approverEmployeeNumber"]. "</td>
							<td align='center' style='cursor:pointer' onclick='popupWrite(". $row["seq"]. ")'>". $row["approverDepartmentName"]. "<br />". $row["approverDepartmentCodeCode"]. "</td>"
							.$ReceiptRow;

			$LIST .= "<td align='center'><a href='map.html?seq=". $row["seq"]. "' target='_blank' class='btn btn-sm ". $gpsFake["mapBtnColor"]. "' title='". $gpsFake["title"]. "'><i class='fa fa-map'></i> Map</a></td>";
			$LIST .= "</tr>";
		}
		
		if(empty($LIST))
		{
			$LIST = "	<tr height='30'>
							<td class='center' colspan='18' style='height:80px;'>데이터가 없습니다.</td>
						</tr>";
		}
		return $LIST;
	}	
}
	