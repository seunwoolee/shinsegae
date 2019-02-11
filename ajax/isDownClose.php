<?
header("Content-Type:text/html;charset=UTF-8");

	include "../inc/config.php";
	include "../inc/lib.inc.php";
	include "../inc/json.inc.php";
	include "../inc/mysql.inc.php";
	include "../Taxinvoice/common.php";

	$json = new Json();

 	if ( isset($_GET['CorpNum']) && $_GET['CorpNum'] != '' ) {

	    // 팝빌회원 사업자번호
		$MemberCorpNum = "6968600649";

    	// 조회 사업자번호
		$CheckCorpNum = $_GET['CorpNum'];

		try {
			$result = $ClosedownService->checkCorpNum($MemberCorpNum, $CheckCorpNum);
		}
		catch (PopbillException $pe) {
			$code = $pe->getCode();
			$message = $pe->getMessage();
		}	
	}
	
	if(isset($result)){
	
		$json->add("code", " error");	
		if($result->state==null){
			$json->add("message","알수 없음");
		}else if($result->state==0){
			$json->add("message","등록되지 않은 사업자번호");
		}else if($result->state==1){
			$json->add("code", "OK");	
			$json->add("message","사업중");
		}else if($result->state==2){
			$json->add("message","폐업");
		}else if($result->state==3){
			$json->add("message","휴업");
		}
		
	}else if(isset($code)){
		$json->add("code", $code);
		$json->add("message",$message);
	}
	
	echo $json->getResult();
	exit;
?>
