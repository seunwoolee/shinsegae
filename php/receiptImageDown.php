<?php

require_once '../excel/Classes/PHPExcel.php';
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/fileHelper.php";




//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$companySeq							= $COMPANY_SEQ;
$year										= $_POST["year"];
$departmentSeq						= $_POST["departmentSeq"];
$receiptProjectSeq					= $_POST["receiptProjectSeq"];
$receiptAccountCodeSeq			= $_POST["receiptAccountCodeSeq"];
$receiptAccountCodeDetailSeq	= $_POST["receiptAccountCodeDetailSeq"];
$card										= $_POST["card"];
$state									= $_POST["state"];
$beginDate								= $_POST["beginDate"];
$endDate								= $_POST["endDate"];

//5만개 이하로만 다운로드 가능;
$MAX_DOWNLOAD_COUNT			= 100;
$DATA_PATH = _DATA_DIR. "/temp";
if(is_dir($DATA_PATH) == false)
{
	mkdir($DATA_PATH, 0747, true);
}

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$zip = new ZipArchive();

function getFileName($obj)
{
	Global $fileNameArr;
	$fileName = $obj["useDate"]. "_". $obj["name"]. "_". $obj["receiptAccountCodeCode"]. "_". $obj["receiptAccountCodeName"]. "_". $obj["receiptAccountCodeDetailName"];
	$count = count($fileNameArr);

	$finded = false;
	$index;
	for($i=0; $i<$count; $i++)
	{
		if($fileNameArr[$i]["name"] == $fileName)
		{
			$finded = true;
			$index = $i;
			break;
		}
	}

	if($finded == true)
	{
		$fileNameArr[$index]["seq"] =  $fileNameArr[$index]["seq"] + 1;
		return $fileName. "_". $fileNameArr[$index]["seq"]; 
	}
	else
	{
		$fileNameArr[$count]["seq"] = 0;
		$fileNameArr[$count]["name"] = $fileName;
		return $fileName;
	}
}
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
if(empty($departmentSeq) == false)						$WHERE .= " AND r.departmentSeq=". $departmentSeq;
if(empty($receiptProjectSeq) == false)						$WHERE .= " AND r.receiptProjectSeq=". $receiptProjectSeq;
if(empty($receiptAccountCodeSeq) == false)				$WHERE .= " AND r.receiptAccountCodeSeq=". $receiptAccountCodeSeq;
if(empty($receiptAccountCodeDetailSeq) == false)		$WHERE .= " AND r.receiptAccountCodeDetailSeq=". $receiptAccountCodeDetailSeq;
if(empty($card) == false)										$WHERE .= " AND r.card='". $card. "'";
if(empty($state) == false)										$WHERE .= " AND r.state='". $state. "'";
if(empty($beginDate) == false)								$WHERE .= " AND r.useDate>='". $beginDate. "'";
if(empty($endDate) == false)									$WHERE .= " AND r.useDate<='". $endDate. "'";




$db->que = "SELECT r.*  FROM receiptView AS r  ";
$db->que .= " WHERE r.companySeq=". $companySeq. $WHERE. " AND imageUrl != '' AND imageUrl IS NOT NULL ORDER BY r.useDate DESC, r.createTime DESC";
$db->query();
$obj = $db->getRows();
$count = count($obj);

if($count > $MAX_DOWNLOAD_COUNT)
{
	LIB::Alert("다운로드는 최대 ". $MAX_DOWNLOAD_COUNT. "건 까지만 가능 합니다. 기간을 작게 설정해 주세요.", "-1");
	exit;
}


if($count>0)
{
	$filePath = $DATA_PATH. "/". $LOGIN_ID. ".zip";
	if(is_file($filePath) == true)
	{
		unlink($filePath);
	}


	if ($zip->open($filePath, ZipArchive::CREATE) === TRUE)
	{
		for($y=0; $y<$count; $y++)
		{
			if(empty($obj[$y]["imageUrl"]) == false)
			{
				$imagePath = _DATA_DIR. $obj[$y]["imageUrl"];
				$saveFileName = getFileName($obj[$y]). ".". LIB::getFileType($obj[$y]["imageUrl"]);
				$saveFileName = iconv("UTF-8", "EUC-KR", $saveFileName);
				$zip->addFile($imagePath, $saveFileName);
			}
		}

		$zip->close();
		FileHelper::download(date("Y-m-d"). " 영수증.zip", $filePath);
	} 
	else 
	{
		LIB::Alert("다운로드 실패! 잠시 후 다시 시도하여 주세요.", "-1");
	}
}
else
{
	LIB::Alert("다운로드할 영수증이 없습니다.", "-1");
}

$db->close();
exit;
?>