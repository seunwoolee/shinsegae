<?
class Department
{
    var $db;
	var $departments;
	var $tree;

	function Department($db)
	{
		$this->db = $db;
	}


	function loadData()
	{
		$this->db->que = "SELECT * FROM department ORDER BY depth ASC, sort ASC";
		$this->db->query();
		$this->departments = $this->db->getRows();
		$this->createTree(0);
	}
	
	function getDepartment($seq)
	{
		$this->db->que = "SELECT * FROM department WHERE seq=". $seq;
		$this->db->query();
		return $this->db->getRow();
	}
	
	function getUnsettingApprover()
	{
		$this->db->que = "select * from departmentView where deleteState = 'N' AND isnull(approverSeq) and depth = 1 ";
		$this->db->query();
		$rows = $this->db->getRows();
		$notHaveApproverOfDepartment = $this->getUnsettingApproverList($rows);
		return $notHaveApproverOfDepartment;
	}
	
	function getUnsettingApproverList($rows)
	{
		$count = count($rows);
		for($i=0;$i<count($rows);$i++)
		{
			$row = $rows[$i];
			$notHaveApproverOfDepartment .= "
			<tr>
				<td class='text-left' height='47'><a href='javascript:departmentWriteSub(". $row["seq"]. ");'>" .$row["name"]."</a></td>
			</tr>";
		}
		return $this->checkApproverVariable($notHaveApproverOfDepartment);
	}
	
	function checkApproverVariable($notHaveApproverOfDepartment)
	{	
		if(empty($notHaveApproverOfDepartment))
		{
			$notHaveApproverOfDepartment = "<tr>미설정 부서가 없습니다.</tr>";
		}
		return $notHaveApproverOfDepartment;
	}

	function getDefaultUpperDepartment()
	{
		$this->db->que = "select seq from department where deleteState = 'N' AND depth = 0 AND seq > 0 order by sort limit 1 ";
		$this->db->query();
		return $this->db->getOne();
	}
	
	function getUpperDepartment($upperDepartmentSeq)
	{
		$this->db->que = "select seq , name from department where deleteState = 'N' AND depth = 0 and seq > 1 ";
		$this->db->query();
		$rows = $this->db->getRows();
		$upperDepartment = $this->getUpperDepartmentList($rows,$upperDepartmentSeq);
		return $upperDepartment;
	}
	
	function getUpperDepartmentList($rows,$upperDepartmentSeq)
	{
		$count = count($rows);
		for($i=0;$i<count($rows);$i++)
		{
			$row = $rows[$i];
			if($upperDepartmentSeq == $row["seq"])
			{
				$upperDepartment .= "
							<tr>
								<td class='text-left active' style='padding:0 !important'><a href='javascript:callMyself(". $row["seq"]. ");' style='color:#444; display:block; padding:10px 7px 9px 7px;'>" .$row["name"]."</a><a href='javascript:departmentWrite(".$row["seq"]. ")' style='position:absolute; margin-top:-17px; margin-right:5px; top:50%; right:0; font-size:14px;' data-toggle='tooltip' data-placement='left' title='상위부서 수정'><i class='fa fa-gear'></i></a></td>
							</tr>";		
			}
			else
			{
				$upperDepartment .= "
							<tr>
								<td class='text-left' style='padding:0 !important'><a href='javascript:callMyself(". $row["seq"]. ");' style='color:#444; display:block; padding:10px 7px 9px 7px;'>" .$row["name"]."</a><a href='javascript:departmentWrite(".$row["seq"]. ")' style='position:absolute; margin-top:-17px; margin-right:5px; top:50%; right:0; font-size:14px;' data-toggle='tooltip' data-placement='left' title='상위부서 수정'><i class='fa fa-gear'></i></a></td>
							</tr>";
			}	
		}
		return $upperDepartment;
	}

	function getNoDepartmentOfUsers()
	{
		$this->db->que = "select count(seq) from user where departmentSeq = 1 and enabled <> 'X' ";
		$this->db->query();
		$noDepartmentUsers = $this->db->getOne();
		$noDepartmentUsers = "(".$noDepartmentUsers."명)";
		return $noDepartmentUsers;
	}

	function getNoDepartment($noDepartmentUsers)
	{
		$this->db->que = "select seq , name from department where depth = 0 and seq = 1 ";
		$this->db->query();
		$rows = $this->db->getRows();
		$noDepartment = $this->getNoDepartmentList($rows,$noDepartmentUsers);
		return $noDepartment;
	}	
	
	function getNoDepartmentList($rows,$noDepartmentUsers)
	{
		$count = count($rows);
		for($i=0;$i<count($rows);$i++)
		{
			$row = $rows[$i];
			$noDepartment .= "
				<tr>
					<td class='text-left' style='padding:0 !important'><a href='javascript:;' style='color:#444; display:block; padding:10px 7px 9px 7px; cursor:default; color:#d41217'>" .$row["name"].$noDepartmentUsers."</a></td>
				</tr>";
		}
		return $noDepartment;
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
		$this->db->que = " select count(*) from department where deleteState = 'N' AND depth = 0 ";
		$this->db->query();
		$upperRowSpan = $this->db->getOne();
		return $upperRowSpan;
	}
	
	function getLowerRowSpan($where)
	{
		$this->db->que = " select count(*) from department where deleteState = 'N' " .$where;
		$this->db->query();
		$lowerRowSpan = $this->db->getOne();
		return $lowerRowSpan;
	}
	
	
	function getLowerDepartment($where,$upperDepartmentSeq)
	{
		$this->db->que = " SELECT seq , name as departmentName ,   departmentCode as departmentNumber , code as departmentCode , "; 
		$this->db->que .= " IFNULL(approverDepartmentName,'') as approverDepName , IFNULL(approverName,'') as approverName, ";
		$this->db->que .= " IFNULL(approverEmployeeNumber,'') as approverNum , IFNULL(approverDepartmentCodeCode,'') as approverDepNumber, ";
		$this->db->que .= " updateTime , sort FROM departmentView2 where deleteState = 'N' ".$where." order by sort";
		$this->db->query();
		$rows = $this->db->getRows();
		$lowerDepartment = $this->getLowerDepartmentList($rows,$upperDepartmentSeq);
		return $lowerDepartment;
	}

	function getLowerDepartmentList($rows,$upperDepartmentSeq)
	{
		$count = count($rows);
		$firstRow = 1;
		for($i=0;$i<count($rows);$i++)
		{
			$row = $rows[$i];
			if($firstRow == 1)
			{
				$lowerDepartment .= "
							<td class='text-center' onclick='departmentWriteSub(".$row["seq"].")' style='cursor:pointer'>".$row["departmentName"]."</td>
							<td class='text-center' onclick='departmentWriteSub(".$row["seq"].")' style='cursor:pointer'>".$row["departmentNumber"]."</td>
							<td class='text-center' onclick='departmentWriteSub(".$row["seq"].")' style='cursor:pointer'>".$row["departmentCode"]."</td>								
							<td class='text-center' onclick='departmentWriteSub(".$row["seq"].")' style='cursor:pointer'>".$row["approverName"]."</td>
							<td class='text-center' onclick='departmentWriteSub(".$row["seq"].")' style='cursor:pointer'>".$row["approverNum"]."</td>
							<td class='text-center' onclick='departmentWriteSub(".$row["seq"].")' style='cursor:pointer'>".$row["approverDepName"]."</td>					
							<td class='text-center' onclick='departmentWriteSub(".$row["seq"].")' style='cursor:pointer'>".$row["approverDepNumber"]."</td>
							<td class='text-center'>
								<button class='up btn btn-xs btn-new-ok' onclick=\"move('up', ". $row["seq"]. ",". $upperDepartmentSeq .")\" ><i class='fa fa-caret-up'></i></button>
								<button class='down btn btn-xs btn-new-ok' onclick=\"move('down', ". $row["seq"]. ",". $upperDepartmentSeq .")\" ><i class='fa fa-caret-down'></i></button>
							</td>
							<td class='text-center' onclick='departmentWriteSub(".$row["seq"].")' style='cursor:pointer'>".$row["updateTime"]."</td>
						</tr>";
				$firstRow++;			
			}  
			else
			{
				$lowerDepartment .= "
						<tr>
							<td class='text-center' onclick='departmentWriteSub(".$row["seq"].")' style='cursor:pointer'>".$row["departmentName"]."</td>
							<td class='text-center' onclick='departmentWriteSub(".$row["seq"].")' style='cursor:pointer'>".$row["departmentNumber"]."</td>
							<td class='text-center' onclick='departmentWriteSub(".$row["seq"].")' style='cursor:pointer'>".$row["departmentCode"]."</td>						
							<td class='text-center' onclick='departmentWriteSub(".$row["seq"].")' style='cursor:pointer'>".$row["approverName"]."</td>
							<td class='text-center' onclick='departmentWriteSub(".$row["seq"].")' style='cursor:pointer'>".$row["approverNum"]."</td>
							<td class='text-center' onclick='departmentWriteSub(".$row["seq"].")' style='cursor:pointer'>".$row["approverDepName"]."</td>					
							<td class='text-center' onclick='departmentWriteSub(".$row["seq"].")' style='cursor:pointer'>".$row["approverDepNumber"]."</td>
							<td class='text-center'>
								<button class='up btn btn-xs btn-new-ok'   onclick=\"move('up', ". $row["seq"]. ",". $upperDepartmentSeq .")\"><i class='fa fa-caret-up'></i></button>
								<button class='down btn btn-xs btn-new-ok' onclick=\"move('down',". $row["seq"].",". $upperDepartmentSeq .")\"><i class='fa fa-caret-down'></i></button>
							</td>
							<td class='text-center' onclick='departmentWriteSub(".$row["seq"].")' style='cursor:pointer'>".$row["updateTime"]."</td>
						</tr>";	
			}
		}
		return $lowerDepartment;
	}
	
	function getlowerDepartmentInfo($seq)
	{
		$this->db->que = "SELECT * FROM departmentView2 WHERE seq=". $seq;
		$this->db->query();
		$row  = $this->db->getRow();
		return $row;
	}
	
	function getParentSeq($seq)
	{
		$department = $this->getDepartment($seq);
		return $department["parentSeq"];
	}

	function getParentName($seq)
	{
		$department = $this->getDepartment($seq);
		return $department["name"];
	}	
	
	function remove($seq)
	{
		if($this->checkUsersOfDeleteDepartment($seq))
		{
			return "삭제할 부서에 사용자가 있습니다. 타 부서로 이동 후 삭제해 주세요.";
		}
		else
		{
			if($this->checkIfSubDepartment($seq))
			{
				return "삭제할 부서에 하위 부서가 있습니다. 하위 부서를 제거후 삭제해 주세요.";
			}
			else
			{	
				$checkDepartment = $this->CheckIfUpperOrLowerDepartment($seq);
				if($checkDepartment == 0)
				{
					$this->departmentDeleteQuery($seq,"upper");			
				}
				else
				{
					$this->updateSort($seq);
					$this->departmentDeleteQuery($seq,"lower");
				}
			}
		}
	}

	function checkUsersOfDeleteDepartment($seq)
	{
		$this->db->que = "SELECT COUNT(*) FROM user WHERE departmentSeq=". $seq. " AND enabled != 'X'";
		$this->db->query();
		if($this->db->getOne() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function checkIfSubDepartment($seq)
	{
		$this->db->que = "SELECT COUNT(*) FROM department WHERE deleteState = 'N' AND parentSeq=". $seq;
		$this->db->query();
		if($this->db->getOne() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
		
	function CheckIfUpperOrLowerDepartment($seq)
	{
		$this->db->que = "SELECT parentSeq FROM department WHERE seq=". $seq;
		$this->db->query();
		$checkDepartment = $this->db->getOne();
		return $checkDepartment;
	}
	
	function departmentDeleteQuery($seq , $mode)
	{
		$DATA["seq"]			= $seq;
		$DATA["deleteState"]	= 'Y';
		if($mode == "upper")
		{
			$this->db->Update("department", $DATA, "where seq=". $seq, "상위 부서 삭제");		
		}
		else if($mode == "lower")
		{
			$this->db->Update("department", $DATA, "where seq=". $seq, "하위 부서 삭제");					
		}
	}
	
	
	/* TODO 삭제할것
	function add($name, $parentSeq)
	{
		$maxSort = $this->getMaxSort($parentSeq);
		$DATA["name"]					= $name;
		$DATA["fullName"]				= $name;
		$DATA["sort"]					= $maxSort + 1;
		$DATA["parentSeq"]			= $parentSeq;
		$DATA["depth"]				= 0;

		if($parentSeq > 0)
		{
			$parent						= $this->getDepartment($parentSeq);
			$DATA["depth"]				= $parent["depth"] + 1;
			$DATA["fullName"]			= $parent["fullName"]. ">". $name;
			//$DATA["fullName"]			= $parent["fullName"]. "/". $name;
		}
		$this->db->Insert("department", $DATA, "insert error");
	}

	
	function modify($seq, $name, $parentSeq)
	{
		$this->loadData();
		if($this->isMyGroup($seq, $parentSeq) == true)
		{
			return "현재 부서 또는 하위 부서로는 이동할 수 없습니다.";
		}
		else
		{
			$before = $this->getDepartment($seq);

			if($before["parentSeq"] != $parentSeq)
			{
				//기존 그룹 sort 조정
				$this->updateSort($seq);

				//하위 부서까지 Depth 일괄 증가/감소
				$this->modifyGroupDepth($seq, $parentSeq);


				$maxSort = $this->getMaxSort($parentSeq);
				$DATA["name"]					= $name;
				$DATA["sort"]					= $maxSort + 1;
				$DATA["parentSeq"]				= $parentSeq;
				$this->db->Update("department", $DATA, "where seq=". $seq, "update error");
				
			}
			else
			{
				$DATA["name"]					= $name;
				$this->db->Update("department", $DATA, "where seq=". $seq, "update error");
			}

			//하위 부서까지 fullName 일괄 수정 
			$this->modifyGroupFullName($seq, $name, $parentSeq);
		}
	}
*/
	
	function upperModify($seq, $name, $code)
	{		
		if($this->departmentCodeDuplicateCheck($code,$seq))
		{
			$this->upperModifyQuery($seq,$name,$code);
		}
		else
		{
			if($this->db->affected_rows() > 0)
			{
				return "중복된 부서코드가 있습니다.";
			}
			else
			{
				$this->upperModifyQuery($seq,$name,$code);
			}
		}
	}
	
	function departmentCodeDuplicateCheck($code,$seq)
	{
		$this->db->que = "SELECT seq FROM department WHERE  departmentCode ='" .$code."'" ;
		$this->db->query();	
		$previousSeq = $this->db->getOne();
		if($previousSeq == $seq)
		{
			return true;
		}
		else
		{
			return false;		
		} 
	}
	
	function upperModifyQuery($seq,$name,$code)
	{
		$DATA["name"]					= $name;
		$DATA["departmentCode"]			= $code;
		$DATA["seq"]					= $seq;
		$this->db->Update("department", $DATA, "where seq=". $seq, "상위 부서 수정");	
	}
	
	function lowerModify($seq, $upperDep, $lowerDep, $depCode, $code, $employeeNumber)
	{		
		if($this->departmentCodeDuplicateCheck($depCode,$seq))
		{
				$this->lowerModifyQuery($seq, $upperDep, $lowerDep, $depCode, $code, $employeeNumber);
		}
		else
		{
			if($this->db->affected_rows() > 0)
			{
				return "중복된 부서코드가 있습니다.";
			}
			else
			{
				$this->lowerModifyQuery($seq, $upperDep, $lowerDep, $depCode, $code, $employeeNumber);
			}
		}
	}
	
	
	function lowerModifyQuery($seq, $upperDep, $lowerDep, $depCode, $code, $employeeNumber)
	{		
		$this->db->que = "SELECT * FROM departmentView WHERE seq =" .$seq ;
		$this->db->query();
		$row = $this->db->getRow();
		$previousDep = $this->getParentSeq($seq);
		$maxSort = $this->getMaxSort($upperDep);
		$DATA["name"]					= $lowerDep;
		$parent							= $this->getDepartment($upperDep);
		$DATA["fullName"]				= $parent["fullName"]. " > ". $lowerDep;
		$DATA["parentSeq"]				= $upperDep;
		$DATA["departmentCode"]			= $depCode;
		$DATA["code"]					= $code;
		$DATA["seq"]					= $seq;
		if($upperDep <> $previousDep)
		{
			$this->updateSort($seq);
			$DATA["sort"]				= $maxSort + 1;
		}
		$this->db->Update("department", $DATA, "where seq=". $seq, "소속 부서 수정");
		
		//승인권자 변경
		$previousApproverUserSeq = $row["approverUserSeq"];
		$this->db->que = "SELECT seq FROM user WHERE  employeeNumber ='".$employeeNumber."'";
		$this->db->query();
		$newApproverUserSeq = $this->db->getOne();
		if(empty($newApproverUserSeq))
		{
			return "지정된 승인권자가 없습니다.";
		}
			if(empty($row["approverSeq"]))
			{
				//승인권자 삽입
				$DATAS["userSeq"]					= $newApproverUserSeq;
				$DATAS["departmentSeq"]				= $seq;
				$this->db->Insert("Approver", $DATAS, "승인권자 추가");

			}
			else
			{
				//승인권자 변경
				if($previousApproverUserSeq <> $newApproverUserSeq)
				{
					$DATAS["userSeq"]					= $newApproverUserSeq;
					$DATAS["departmentSeq"]				= $seq;
					$this->db->Update("Approver", $DATAS, "where seq=". $row["approverSeq"], "승인권자 변경");		
				}
			}
	}
	
	
	
	function upperAdd($name, $code)
	{		
		$this->db->que = "SELECT * FROM department WHERE departmentCode ='".$code."'";
		$this->db->query();
		if($this->db->affected_rows() > 0)
		{
			return "중복된 부서코드가 있습니다.";
		}
		$maxSort = $this->getMaxSort(0);
		$DATA["name"]					= $name;
		$DATA["fullName"]				= $name;
		$DATA["sort"]					= $maxSort + 1;
		$DATA["parentSeq"]			= 0;
		$DATA["depth"]				= 0;
		$DATA["departmentCode"]		= $code;
		$this->db->Insert("department", $DATA, "상위 부서 추가");
	}
	
	function lowerAdd($upperDep, $lowerDep, $depCode, $code, $employeeNumber)
	{		
		$this->db->que = "SELECT seq FROM department WHERE  departmentCode ='".$depCode."'";
		$this->db->query();
		if($this->db->affected_rows() > 0)
		{
			return "중복된 부서코드가 있습니다.";
		}
		$maxSort = $this->getMaxSort($upperDep);
		$DATA["name"]				= $lowerDep;
		$parent						= $this->getDepartment($upperDep);
		$DATA["fullName"]			= $parent["fullName"]. " > ". $lowerDep;
		$DATA["sort"]				= $maxSort + 1;
		$DATA["parentSeq"]			= $upperDep;
		$DATA["depth"]				= 1;
		$DATA["departmentCode"]		= $depCode;
		$DATA["code"]				= $code;
		$this->db->Insert("department", $DATA, "소속 부서 추가");
		
		if(empty($employeeNumber) == true)
		{
			return "해당 부서에 승인권자가 없습니다.";
		}
		else
		{
		//승인권자 insert
		$this->db->que = "SELECT seq FROM department WHERE  departmentCode ='".$depCode."'";
		$this->db->query();
		$approverDepSeq = $this->db->getOne();
		$this->db->que = "SELECT seq FROM user WHERE  employeeNumber ='".$employeeNumber."'";
		$this->db->query();  
		$approverUserSeq = $this->db->getOne();
		$DATAS["userSeq"]					= $approverUserSeq;
		$DATAS["departmentSeq"]				= $approverDepSeq;
		$this->db->Insert("Approver", $DATAS, "승인권자 추가");
		}
	}
	
	function loadUpperData()
	{
		$this->db->que = "SELECT * FROM department WHERE depth = 0 AND deleteState = 'N' ORDER BY depth ASC, sort ASC";
		$this->db->query();
		$this->departments = $this->db->getRows();
		$this->createTree(0);
	}
	
	function loadLowerData($upperDep)
	{
		$this->db->que = " SELECT * FROM department WHERE deleteState = 'N' AND parentSeq =".$upperDep." ORDER BY depth ASC, sort ASC";
		$this->db->query();
		$this->treeLower = $this->db->getRows();
	}
	
	
	function getLowerTreeSelectBoxOptions($selectedSeq=0)
	{
		$tree = $this->treeLower;
		$count = count($tree);
		for($i=0; $i<$count; $i++)
		{
			$name = $tree[$i]["name"];
			if($selectedSeq == $tree[$i]["seq"])
			{
				$selected = "selected";
			}
			else
			{
				$selected = "";
			}


			$LIST .= "<option value='". $tree[$i]["seq"]. "' ". $selected. ">". $name. "</option>\n";
		}
		
		return $LIST;
	}
	
	//트리구조 만들기
	function createTree($parentSeq)
	{
		$count = count($this->departments);
		for($i=0; $i<$count;)
		{
			if(empty($this->departments[$i]))
			{
				return;
			}
			else
			{ //바로일로
				if($this->departments[$i]["parentSeq"] == $parentSeq)
				{	
					$this->tree[] = $this->departments[$i];
					$seq = $this->departments[$i]["seq"];
					array_splice($this->departments,$i,1);
					$this->createTree($seq);
				}
				else
				{
					$i++;
				}
			}
		}
	}
	
	function approverChange($previousApprover, $newApprover)
	{		
		$this->db->que = "SELECT seq FROM user WHERE employeeNumber ='" .$previousApprover."'";
		$this->db->query();
		$previousApproverSeq = $this->db->getOne();
		$this->db->que = "SELECT seq FROM user WHERE employeeNumber ='" .$newApprover."'";
		$this->db->query();
		$newApproverSeq = $this->db->getOne();
		
		if($previousApproverSeq == $newApproverSeq)
		{
			return "동일한 승인권자입니다.";
		}
		else
		{
			$DATA["userSeq"]				= $newApproverSeq;
			$this->db->Update("Approver", $DATA, "where userSeq=". $previousApproverSeq, "승인권자 일괄변경");
		}
	}

	//하위 부서까지 fullName 일괄 수정 
	function modifyGroupFullName($seq, $name, $parentSeq)
	{
		$before = $this->getDepartment($seq);
		$groupSeqList = $this->getGroupSeqList($seq);

		if($parentSeq > 0)
		{
			$parent						= $this->getDepartment($parentSeq);
			$afterFullName				= $parent["fullName"]. " > ". $name;
		}
		else
		{
			$afterFullName				= $name;
		}
		
		$this->db->que = "UPDATE department SET ";
		$this->db->que .= "fullName=CONCAT('". $afterFullName. "', SUBSTRING(fullName, ". (mb_strlen($before["fullName"], "UTF-8")+1). ")) ";
		$this->db->que .= "WHERE seq in(". $groupSeqList. ")";
		$this->db->query();
	}

	//하위 부서까지 Depth 일괄 증가/감소
	function modifyGroupDepth($seq, $parentSeq)
	{
		if($parentSeq > 0)
		{
			$parent						= $this->getDepartment($parentSeq);
			$afterDepth					= $parent["depth"] + 1;
		}
		else
		{
			$afterDepth					= 0;
		}

		$before = $this->getDepartment($seq);
		$groupSeqList = $this->getGroupSeqList($seq);

		if($afterDepth > $before["depth"])
		{
			$increase = $afterDepth - $before["depth"];
			$this->db->que = "UPDATE department SET depth = depth + ". $increase. " WHERE seq in(". $groupSeqList. ")";
			$this->db->query();
		}
		else if($afterDepth < $before["depth"])
		{
			$decrease = $before["depth"] - $afterDepth;
			$this->db->que = "UPDATE department SET depth = depth - ". $decrease. " WHERE seq in(". $groupSeqList. ")";
			$this->db->query();
		}
	}


	function up($seq)
	{
		$choice = $this->getDepartment($seq);
		$this->db->que = "SELECT * FROM department WHERE deleteState = 'N' AND parentSeq=". $choice["parentSeq"]. " AND sort < ". $choice["sort"]. " ORDER BY sort DESC LIMIT 1";
		$this->db->query();
		if($this->db->affected_rows() > 0)
		{
			$prev = $this->db->getRow();
			$DATA["sort"] = $prev["sort"];
			$this->db->Update("department", $DATA, "where seq=". $choice["seq"], "X");

			$DATA["sort"] = $choice["sort"];
			$this->db->Update("department", $DATA, "where seq=". $prev["seq"], "X");
		}
	}


	function down($seq)
	{
		$choice = $this->getDepartment($seq);
		$this->db->que = "SELECT * FROM department WHERE deleteState = 'N' AND parentSeq=". $choice["parentSeq"]. " AND sort > ". $choice["sort"]. " ORDER BY sort ASC LIMIT 1";
		$this->db->query();
		if($this->db->affected_rows() > 0)
		{
			$next = $this->db->getRow();
			$DATA["sort"] = $next["sort"];
			$this->db->Update("department", $DATA, "where seq=". $choice["seq"], "X");

			$DATA["sort"] = $choice["sort"];
			$this->db->Update("department", $DATA, "where seq=". $next["seq"], "X");
		}
	}


	function getMaxSort($parentSeq)
	{
		$this->db->que = "SELECT MAX(sort) FROM department WHERE deleteState = 'N' AND parentSeq=". $parentSeq;
		$this->db->query();
		$max = $this->db->getOne();
		if(empty($max))
		{
			return 0;
		}
		else
		{
			return $max;
		}
	}

	function updateSort($seq)
	{
		//현재부서 다음 부서들 sort 1씩 마이너스 처리
		$department = $this->getDepartment($seq);
		$this->db->que = "UPDATE department SET sort=sort-1 WHERE deleteState = 'N' AND parentSeq=". $department["parentSeq"]. " AND sort > ". $department["sort"];
		$this->db->query();
	}


	//트리구조 만들기
	function createTreeLower($parentSeq)
	{
		$count = count($this->departments);
		for($i=0; $i<$count;)
		{
			if(empty($this->departments[$i]))
			{
				return;
			}
			else
			{ //바로일로
				if($this->departments[$i]["parentSeq"] == $parentSeq)
				{	
					$this->tree[] = $this->departments[$i];
					$seq = $this->departments[$i]["seq"];
					array_splice($this->departments,$i,1);
					$this->createTree($seq);
				}
				else
				{
					$i++;
				}
			}
		}
	}

	function isMyGroup($groupSeq, $seq)
	{
		$group = $this->getGroup($groupSeq);
		$count = count($group);
		for($i=0; $i<$count; $i++)
		{
			if($seq == $group[$i]["seq"])
			{
				return true;
				break;
			}
		}
		
		return false;
	}


	//특정 부서 선택시 해당 부서 및 하위부서 추출
	function getGroup($seq)
	{
		$count = count($this->tree);
		$depth = -1;
		$group = null;

		for($i=0; $i<$count; $i++)
		{
			if($depth > -1)
			{
				if($this->tree[$i]["depth"] <= $depth)
				{
					return $group;
				}
				$group[] = $this->tree[$i];
			}


			if($seq == $this->tree[$i]["seq"])
			{
				$depth = $this->tree[$i]["depth"];
				$group[] = $this->tree[$i];
			}
		}
		return $group;
	}

	//특정부서 선택시 하위부서 포함한 모든 seq 추출
	function getGroupSeqList($seq)
	{
		$tree = $this->getGroup($seq);
		$seqList = "";

		$count = count($tree);
		for($i=0; $i<$count; $i++)
		{
			$seqList .= $tree[$i]["seq"]. ",";
		}
		
		$seqList = substr($seqList, 0, -1);
		return $seqList;
	}


	function getTree()
	{
		return $this->tree;
	}


	function getBlanks($count)
	{
		for($i=0; $i<$count; $i++)
		{
			$blanks .= "&nbsp;&nbsp;";
		}

		return $blanks;
	}

	function getTreeSelectBoxOptions($selectedSeq=0)
	{
		$tree = $this->tree;
		$count = count($tree);
		echo '<script>console.log('. json_encode($tree) .')</script>';
		for($i=0; $i<$count; $i++)
		{
			if($tree[$i]["depth"] > 0)
			{
				$name = $this->getBlanks($tree[$i]["depth"]). "└ ". $tree[$i]["name"];
			}
			else
			{
				$name = $tree[$i]["name"];
			}

			if($selectedSeq == $tree[$i]["seq"])
			{
				$selected = "selected";
			}
			else
			{
				$selected = "";
			}


			$LIST .= "<option value='". $tree[$i]["seq"]. "' ". $selected. ">". $name. "</option>\n";
		}
		
		return $LIST;
	}

	function getDeletedDepSelectBoxOptions($selectedSeq=0)
	{
		
		$this->db->que = "SELECT * FROM department WHERE deleteState = 'Y' AND depth = 1 ";
		$this->db->query();
		$tree = $this->db->getRows();
		echo '<script>console.log('. json_encode($tree) .')</script>';
		$count = count($tree);
		for($i=0; $i<$count; $i++)
		{
			if($tree[$i]["depth"] > 1)
			{
				$name = $this->getBlanks($tree[$i]["depth"]). "└ ". $tree[$i]["name"];
			}
			else
			{
				$name = $tree[$i]["name"];
			}

			if($selectedSeq == $tree[$i]["seq"])
			{
				$selected = "selected";
			}
			else
			{
				$selected = "";
			}


			$LIST .= "<option value='". $tree[$i]["seq"]. "' ". $selected. ">". $name. "</option>\n";
		}
		
		return $LIST;
	}
}
?>