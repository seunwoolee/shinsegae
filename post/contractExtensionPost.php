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


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$companySeq			= $COMPANY_SEQ;


$licenceOil					= $_POST["licenceOil"];
$paymentTerm				= explode("|", $_POST["paymentTerm"])[0];
$licenceQuantity			= $_POST["licenceQuantity"];
$estimateTotal			= $_POST["estimateTotal"];
$estimateDiscount	= $_POST["estimateDiscount"];
$estimateResult		= $_POST["estimateResult"];



if(empty($paymentTerm) == true)
{
	$paymentTerm = "1";
}



//유류비정산은 일단 무조건 Y
$licenceOil = "Y";


$db = new Mysql();
$db->que = "SELECT * FROM company WHERE seq=". $companySeq;
$db->query();
$company = $db->getRow();


$mailSender = new MailSender();
$fromAddress = "cartaxoil@carbeast.co.kr";
$fromName = "카택스 oil";
$toAddress = "carbeast77@gmail.com";
$toName = "카택스";
$subject = "카택스 oil 계약연장 신청";


$contents = "카택스 oil 계약연장 신청<br>";
$contents .= "<br>업체명 : ". $company["name"];
$contents .= "<br>담당자 : ". $company["adminName"];
$contents .= "<br>휴대폰 : ". $company["phone"];
$contents .= "<br>전화번호 : ". $company["tel"];
$contents .= "<br>사업자등록번호 : ". $company["corporateNumber"];
$contents .= "<br>이메일 : ". $company["email"];
$contents .= "<br>주소 : ". $company["address"];
$contents .= "<br>차량대수 : ". $licenceQuantity. "대";
$contents .= "<br>계약기간 : ". $paymentTerm. "개월";
$contents .= "<br>합계 : ". $estimateTotal. "원";
$contents .= "<br>할인적용 : ". $estimateDiscount. "원";
$contents .= "<br>결재금액(VAT 포함) : ". $estimateResult. "원";



$mailSender->send($fromAddress, $fromName, $toAddress, $toName, $subject, $contents);
LIB::Alert("계약연장 신청이 완료 되었습니다. 확인 후 메일 또는 유선 연락 드릴 수 있도록 하겠습니다.", "close");
?>
