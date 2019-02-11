<?
include "../../inc/config.php";
include "../../inc/lib.inc.php";
include "../../inc/mysql.inc.php";
include "../../inc/json.inc.php";
require_once "PHPExcel.php";

$json = new Json();

$objPHPExcel = new PHPExcel();
$upload_file_path				= "../data";
$db = new Mysql();
$db->que = "SELECT employeeNumber FROM previousReg";
$db->query();
$userSingleIdList = $db->getRows();

if(strlen($_FILES["upload_file"]["name"]) > 0)
{
	if(LIB::inFileType("xls|xlsx", $_FILES["upload_file"]["name"]) == true)
	{
		$dir = "../data";
		$ext = LIB::getFileType($_FILES["upload_file"]["name"]);
		if(is_dir($dir) == false)
		{
			mkdir($dir, 0747, true);
		}
		$fileName = "userdata";

		move_uploaded_file($_FILES["upload_file"]["tmp_name"], $dir. "/".$fileName.".".$ext);

		$filename = '../data/userdata.'.$ext;
		try {
		  // 업로드 된 엑셀 형식에 맞는 Reader객체를 만든다.
			$objReader = PHPExcel_IOFactory::createReaderForFile($filename);
			// 읽기전용으로 설정
			$objReader->setReadDataOnly(true);
			// 엑셀파일을 읽는다
			$objExcel = $objReader->load($filename);
			// 첫번째 시트를 선택
			$objExcel->setActiveSheetIndex(0);
			$objWorksheet = $objExcel->getActiveSheet();
			$rowIterator = $objWorksheet->getRowIterator();
			foreach ($rowIterator as $row) { // 모든 행에 대해서
					   $cellIterator = $row->getCellIterator();
					   $cellIterator->setIterateOnlyExistingCells(false); 
			}
			$maxRow = $objWorksheet->getHighestRow();

			
			for ($i = 0 ; $i <= $maxRow ; $i++) {
				$employeeNumber = $objWorksheet->getCell('A' . $i)->getValue(); // A열
				//$singleMail = $objWorksheet->getCell('B' . $i)->getValue(); // B열
				$name = $objWorksheet->getCell('B' . $i)->getValue(); // B열
				$isDuplicateId = false;
				LIB::PLog($employeeNumber);
				for($j = 0 ; $j < count($userSingleIdList); $j++){
					if($employeeNumber==$userSingleIdList[$j]["employeeNumber"]){
						$isDuplicateId = true;
					}
				}
				if($isDuplicateId==false){
					$DATA["employeeNumber"]				= $employeeNumber;
					$DATA["name"]						= $name;

					if($employeeNumber!=''){
						$db->Insert("previousReg", $DATA,  "사용자 엑셀 업로드");
						unset($DATA);
					}
				}
			}
			//lib::Plog("dd");
			//echo "ok";
			
			$result = "사용자 등록이 완료되었습니다.";
			$json->add("result",$result);
			echo $json->getResult();
		} 
		 catch (exception $e) {
			$result = "엑셀파일을 읽는도중 오류가 발생하였습니다.";
			$json->add("result",$result);
			echo $json->getResult();
		}
	}
	else
	{
		$result = "xls,xlsx 파일만 업로드 가능 합니다.";
		$json->add("result",$result);
		echo $json->getResult();
	}
}


?>

