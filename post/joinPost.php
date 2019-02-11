<?
header("Content-Type:text/html;charset=UTF-8");

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/company.inc.php";
include "../inc/mailSender.inc.php";
include "../inc/receipt.inc.php";
include "../inc/drivingLog.inc.php";

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$cid							= $_POST["cid"];
$password					= $_POST["password"];
$passwordConfirm			= $_POST["passwordConfirm"];

$ceoName 					= $_POST["ceoName"];				

$name						= $_POST["name"];
$adminName				= $_POST["adminName"];
$tel							= $_POST["tel"];
$phone						= $_POST["phone"];
$email						= $_POST["email"];
$address						= $_POST["address"];

$licenceOil					= $_POST["licenceOil"];
$paymentTerm				= explode("|", $_POST["paymentTerm"])[0];
$licenceQuantity			= $_POST["licenceQuantity"];

$corporateNumber = $_POST["invoiceeCorpNum1"].$_POST["invoiceeCorpNum2"].$_POST["invoiceeCorpNum3"];

$estimateTotal			= $_POST["estimateTotal"];
$estimateDiscount	= $_POST["estimateDiscount"];
$estimateResult		= $_POST["estimateResult"];
$marketer				= $_POST["marketer"];
$event					= $_POST["event"];

if(empty($event) == true)
{
	$event = "N";
}

if(empty($paymentTerm) == true)
{
	$paymentTerm = "1";
}



//유류비정산은 일단 무조건 Y
$licenceOil = "Y";



//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$securePassword = trim(base64_encode(hash('sha256', $password, true))); 
$securePasswordConfirm = trim(base64_encode(hash('sha256', $passwordConfirm, true))); 


if($securePassword != $securePasswordConfirm)
{
	LIB::Alert("비밀번호가 다릅니다. 다시 입력해 주세요", "-1");
	exit;
}
else
{

	if ($_FILES["corporateDoc"]["error"] > 0)
	{
		//echo "Error: " . $_FILES["filename"]["error"] . "<br />";
	}
	else
	{
		if(LIB::inFileType("JPG|JPEG|PNG|GIF|PDF|DOC|DOCX|HWP", $_FILES["corporateDoc"]["name"]) == true)
		{
			$dir = _DATA_DIR. "/doc";
			if(is_dir($dir) == false)
			{
				mkdir($dir, 0747, true);
			}

			move_uploaded_file($_FILES["corporateDoc"]["tmp_name"], $dir. "/". $cid);
			$DATA["corporateDocName"]	 = $_FILES["corporateDoc"]["name"];
		}
	}


	$DATA["cid"]						= $cid;
	$DATA["password"]				= $securePassword;
	$DATA["name"]						= $name;
	$DATA["adminName"]				= $adminName;
	$DATA["tel"]						= $tel;
	$DATA["phone"]					= $phone;
	$DATA["corporateNumber"]		= $corporateNumber;	
	$DATA["email"]						= $email;
	$DATA["address"]					= $address;
	$DATA["licenceOil"]				= $licenceOil;
	$DATA["marketer"]				= $marketer;
	$DATA["event"]					= $event;
	$DATA["invoiceeCEOName"]					= $ceoName;
	$DATA["licenceQuantity"]					= $licenceQuantity;
	
	if($event == "Y")
	{
		$DATA["payment"]			= "Y";
		$DATA["licenceExpireDate"]= date("Y-m-d", time() + (60*60*24*29));
	}

	if($licenceOil == "Y")
	{
		$DATA["lockDistance"]				= "Y";
		$DATA["lockSaveMapPoint"]		= "Y";
		$DATA["lockDeviceChange"]		= "Y";
	}
	

	$ikey = Company::createIkey($db, $cid);
	$DATA["ikey"] = $ikey;

	$db->Insert("company", $DATA, "insert error");


	//-----------------------------------------------------------------------------------------------
	// 샘플 직책 입력
	//-----------------------------------------------------------------------------------------------
	$companySeq = $db->insert_id();
	unset($DATA);

	$DATA["companySeq"] = $companySeq;
	$DATA["name"] = "팀장 (샘플)";
	$DATA["sort"] = "1";
	$db->Insert("duty", $DATA, "duty insert error");

	unset($DATA);
	$DATA["companySeq"] = $companySeq;
	$DATA["name"] = "팀원 (샘플)";
	$DATA["sort"] = "2";
	$db->Insert("duty", $DATA, "duty insert error");


	//-----------------------------------------------------------------------------------------------
	// 샘플 부서 입력
	//-----------------------------------------------------------------------------------------------
	unset($DATA);
	$DATA["companySeq"] = $companySeq;
	$DATA["parentSeq"] = 0;
	$DATA["depth"] = 0;
	$DATA["sort"] = 1;
	$DATA["name"] = "1부서 (샘플)";
	$DATA["fullName"] = "1부서 (샘플)";
	$db->Insert("department", $DATA, "department insert error"); 

	unset($DATA);
	$DATA["companySeq"] = $companySeq;
	$DATA["parentSeq"] = 0;
	$DATA["depth"] = 0;
	$DATA["sort"] = 2;
	$DATA["name"] = "2부서 (샘플)";
	$DATA["fullName"] = "2부서 (샘플)";
	$db->Insert("department", $DATA, "department insert error");

	$parentSeq = $db->insert_id();
	unset($DATA);
	$DATA["companySeq"] = $companySeq;
	$DATA["parentSeq"] = $parentSeq;
	$DATA["depth"] = 1;
	$DATA["sort"] = 1;
	$DATA["name"] = "1팀 (샘플)";
	$DATA["fullName"] = "2부서 (샘플)/1팀 (샘플)";
	$db->Insert("department", $DATA, "department insert error");

	unset($DATA);
	$DATA["companySeq"] = $companySeq;
	$DATA["parentSeq"] = $parentSeq;
	$DATA["depth"] = 1;
	$DATA["sort"] = 2;
	$DATA["name"] = "2팀 (샘플)";
	$DATA["fullName"] = "2부서 (샘플)/2팀 (샘플)";
	$db->Insert("department", $DATA, "department insert error");


	//-----------------------------------------------------------------------------------------------
	// 영수증 기본 정보 입력
	//-----------------------------------------------------------------------------------------------
	$receipt = new Receipt($db, $companySeq);
	$receipt->setDefaultReceiptInfo();

	//-----------------------------------------------------------------------------------------------
	// 유류비 정산 기본값
	//-----------------------------------------------------------------------------------------------
	unset($DATA);
	$DATA["companySeq"] = $companySeq;
	$db->Insert("calculateOilSetting", $DATA, "calculateOilSetting insert error");

	//-----------------------------------------------------------------------------------------------
	// 운행목적 기본 정보 입력
	//-----------------------------------------------------------------------------------------------
	DrivingLog::setDefaultPurpose($db, $companySeq);




	$mailSender = new MailSender();
	
	
	$fromAddress = "cartaxoil@carbeast.co.kr";
	$fromName = "카택스 oil";
	$toAddress = "carbeast77@gmail.com";
	$toName = "카택스";
	$subject = "카택스 oil 회원가입 및 견적요청";


	$contents = "카택스 oil 회원가입 및 견적요청<br>";
	$contents .= "<br>업체명 : ". $name;
	$contents .= "<br>담당자 : ". $adminName;
	$contents .= "<br>휴대폰 : ". $phone;
	$contents .= "<br>전화번호 : ". $tel;
	$contents .= "<br>사업자등록번호 : ". $corporateNumber;	
	$contents .= "<br>이메일 : ". $email;
	$contents .= "<br>주소 : ". $address;
	$contents .= "<br>차량대수 : ". $licenceQuantity. "대";
	
	if($event != "Y")
	{
		$mailSender->send($fromName, $toAddress, $toName, $subject, $contents);
	}
	
	$fromName = "카택스 oil";
	$subject = "카택스 회원가입 해주셔서 대단히 감사합니다.";
	$toName = "카택스";
	
	$mailSender->send($fromName, $email, $toName, $subject, $mailSender->joinContents($adminName,$cid,$licenceQuantity,$paymentTerm,$estimateResult) );

	session_start();
	$_SESSION["OMember_id"] = $cid;
	$_SESSION["OMember_seq"] = $companySeq;
	$_SESSION["OMember_ikey"] = $ikey;

	?>
	
	
	<!-- 다음 CTS 전환스크립트 연동 코드  (jeycorp2011 계정) -->
	<script type="text/javascript"> 
		 //<![CDATA[ 
		 var DaumConversionDctSv="type=W,orderID=,amount="; 
		 var DaumConversionAccountID="XYH-anq-x7Yeq1l9DC5Pzg00"; 
		 if(typeof DaumConversionScriptLoaded=="undefined"&&location.protocol!="file:"){ 
			var DaumConversionScriptLoaded=true; 
			document.write(unescape("%3Cscript%20type%3D%22text/javas"+"cript%22%20src%3D%22"+(location.protocol=="https:"?"https":"http")+"%3A//t1.daumcdn.net/cssjs/common/cts/vr200/dcts.js%22%3E%3C/script%3E")); 
		 } 
		 //]]> 
	 </script>
	<?
	

	$_SESSION['http_amount']=$estimateResult;	//로그2차변수
	LIB::Alert("회원 가입이 완료 되었습니다.", "../contract.html");
}
?>
