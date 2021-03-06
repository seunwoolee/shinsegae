<?
header("Content-Type:text/html;charset=UTF-8");
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/json.inc.php";
$json = new Json();	

	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	$licenceQuantity = $_POST["licenceQuantity"];
	$paymentTerm = $_POST["term"];
	
	$totalPrice = 5000*$licenceQuantity*$paymentTerm;
	$perDC = $paymentTerm;
	$valueDC = 0; 

	if($perDC == 1){
		$perDC = 1;
		$valueDC = 0;
	}
	else if($perDC == 6){
		$perDC = 0.95;
		$valueDC = 5;
	}
	else if($perDC == 12){
		$perDC = 0.9;
		$valueDC = 10;
	}
	else if($perDC == 24){
		$perDC = 0.9;
		$valueDC = 10;
	}
	else if($perDC == 36){
		$perDC = 0.85;
		$valueDC = 15;
	}

	$totalPriceWon = ($totalPrice*$perDC)+($totalPrice*$perDC)/10;
	$totalPriceVat = ($totalPrice*$perDC)/10;
	
  /**
  * 1건의 세금계산서를 즉시발행 처리합니다.
  * - 세금계산서 항목별 정보는 "[전자세금계산서 API 연동매뉴얼] > 4.1. (세금)계산서구성"을 참조하시기 바랍니다.
  */

	include '../Taxinvoice/common.php';

  // 팝빌회원 사업자번호, '-' 제외 10자리
	$testCorpNum = '6968600649';

  // 팝빌회원 아이디
	$testUserID = 'cartax';

  // 세금계산서 문서관리번호
  // - 최대 24자리 숫자, 영문, '-', '_' 조합으로 사업자별로 중복되지 않도록 구성
  $invoicerMgtKey = date("Y-m-d-h-i-s"). "_". generateRandomString(4);
	
  // 지연발행 강제여부
	$forceIssue = false;

  // 즉시발행 메모
	$memo = '즉시발행 메모';

  // 안내메일 제목, 미기재시 기본제목으로 전송
	$emailSubject = '';

  // 거래명세서 동시작성 여부
	$writeSpecification = false;

  // 거래명세서 동시작성시 명세서 관리번호
  // - 최대 24자리 숫자, 영문, '-', '_' 조합으로 사업자별로 중복되지 않도록 구성
	$dealInvoiceMgtKey = '';



  /************************************************************
  *                        세금계산서 정보
  ************************************************************/

  // 세금계산서 객체 생성
	$Taxinvoice = new Taxinvoice();

  // [필수] 작성일자, 형식(yyyyMMdd) 예)20150101
	$Taxinvoice->writeDate = date("Ymd");

  // [필수] 발행형태, '정발행', '역발행', '위수탁' 중 기재
	$Taxinvoice->issueType = '정발행';

  // [필수] 과금방향,
  // - '정과금'(공급자 과금), '역과금'(공급받는자 과금) 중 기재, 역과금은 역발행시에만 가능.
	$Taxinvoice->chargeDirection = '정과금';

  // [필수] '영수', '청구' 중 기재
	$Taxinvoice->purposeType = '영수';

  // [필수] 과세형태, '과세', '영세', '면세' 중 기재
	$Taxinvoice->taxType = '과세';

  // [필수] 발행시점, 발행예정시 동작, '직접발행', '승인시자동발행' 중 기재
	$Taxinvoice->issueTiming = '직접발행';



  /************************************************************
  *                         공급자 정보
  ************************************************************/

  // [필수] 공급자 사업자번호
	$Taxinvoice->invoicerCorpNum = $testCorpNum;

  // 공급자 종사업장 식별번호, 4자리 숫자 문자열
  $Taxinvoice->invoicerTaxRegID = '';

  // [필수] 공급자 상호
	$Taxinvoice->invoicerCorpName = '(주)카택스';

  // [필수] 공급자 문서관리번호, 최대 24자리 숫자, 영문, '-', '_' 조합으로 사업자별로 중복되지 않도록 구성
  $Taxinvoice->invoicerMgtKey = $invoicerMgtKey;

  // [필수] 공급자 대표자성명
  $Taxinvoice->invoicerCEOName = '안재희';

  // 공급자 주소
  $Taxinvoice->invoicerAddr = '대구광역시 남구 현충로 170, 304호(대명동, 영남이공대학 산학협력관)';

  // 공급자 종목
  $Taxinvoice->invoicerBizClass = '서비스업';

  // 공급자 업태
  $Taxinvoice->invoicerBizType = '컴퓨터 프로그래밍 서비스업';

  // 공급자 담당자 성명
  $Taxinvoice->invoicerContactName = '윤영준';

  // 공급자 담당자 메일주소
  $Taxinvoice->invoicerEmail = 'carbeast77@gmail.com';

  // 공급자 담당자 연락처
  $Taxinvoice->invoicerTEL = '070-8785-4799';

  // 공급자 휴대폰 번호
  $Taxinvoice->invoicerHP = '010-6646-8305';

  // 정발행시 공급받는자 담당자에게 알림문자 전송여부
  // - 안내문자 전송시 포인트가 차감되며 전송실패시 환불처리 됩니다.
  $Taxinvoice->invoicerSMSSendYN = false;



  /************************************************************
  *                      공급받는자 정보
  ************************************************************/

  // [필수] 공급받는자 구분, '사업자', '개인', '외국인' 중 기재
	$Taxinvoice->invoiceeType = '사업자';

  // [필수] 공급받는자 사업자번호
	$Taxinvoice->invoiceeCorpNum = $_POST["invoiceeCorpNum"]; //'8888888888';

  // 공급받는자 종사업장 식별번호, 4자리 숫자 문자열
    $Taxinvoice->invoiceeTaxRegID = '';

  // [필수] 공급자 상호
	$Taxinvoice->invoiceeCorpName = $_POST["invoiceeCorpName"];//'공급받는자 상호';

  // [역발행시 필수] 공급받는자 문서관리번호, 최대 24자리 숫자, 영문, '-', '_' 조합으로 사업자별로 중복되지 않도록 구성
	$Taxinvoice->invoiceeMgtKey = '';

  // [필수] 공급받는자 대표자성명
	$Taxinvoice->invoiceeCEOName = $_POST["invoiceeCEOName"];//'공급받는자 대표자성명';

  // 공급받는자 주소
	$Taxinvoice->invoiceeAddr = $_POST["invoiceeAddr"];//'공급받는자 주소';

  // 공급받는자 업태
  $Taxinvoice->invoiceeBizType = $_POST["invoiceeBizType"];//'공급받는자 업태';

  // 공급받는자 종목
  $Taxinvoice->invoiceeBizClass = $_POST["invoiceeBizClass"];//'공급받는자 종목';

  // 공급받는자 담당자 성명
	$Taxinvoice->invoiceeContactName1 = $_POST["invoiceeContactName1"];//'공급받는자 담당자성명';

  // 공급받는자 담당자 메일주소
	$Taxinvoice->invoiceeEmail1 = $_POST["invoiceeEmail1"];//'kkokkokim14@naver.com';

  // 공급받는자 담당자 연락처
	$Taxinvoice->invoiceeTEL1 = $_POST["invoiceeTEL1"];//'070-111-222';

  // 공급받는자 담당자 휴대폰 번호
	$Taxinvoice->invoiceeHP1 = $_POST["invoiceeHP1"];//'010-111-222';

  // 역발행요청시 공급자 담당자에게 알림문자 전송여부
  // - 문자전송지 포인트가 차감되며, 전송실패시 포인트 환불처리됩니다.
	$Taxinvoice->invoiceeSMSSendYN = false;



  /************************************************************
  *                       세금계산서 기재정보
  ************************************************************/

  // [필수] 공급가액 합계
	$Taxinvoice->supplyCostTotal = ($totalPrice*$perDC);

  // [필수] 세액 합계
	$Taxinvoice->taxTotal = ($totalPrice*$perDC)*0.1;

  // [필수] 합계금액, (공급가액 합계 + 세액 합계)
	$Taxinvoice->totalAmount = $totalPriceWon;

  // 기재상 '일련번호'항목
	$Taxinvoice->serialNum = '1';

  // 기재상 '현금'항목
	$Taxinvoice->cash = '';

  // 기재상 '수표'항목
	$Taxinvoice->chkBill = '';
  // 기재상 '어음'항목
	$Taxinvoice->note = '';

  // 기재상 '외상'항목
	$Taxinvoice->credit = '';

  // 기재상 '비고' 항목
	$Taxinvoice->remark1 = '';
	$Taxinvoice->remark2 = '';
	$Taxinvoice->remark3 = '';

  // 기재상 '권' 항목, 최대값 32767
	$Taxinvoice->kwon = '1';

  // 기재상 '호' 항목, 최대값 32767
	$Taxinvoice->ho = '1';

  // 사업자등록증 이미지파일 첨부여부
	$Taxinvoice->businessLicenseYN = false;

  // 통장사본 이미지파일 첨부여부
	$Taxinvoice->bankBookYN = false;



  /************************************************************
  *                     수정 세금계산서 기재정보
  * - 수정세금계산서 관련 정보는 연동매뉴얼 또는 개발가이드 링크 참조
  * - [참고] 수정세금계산서 작성방법 안내 - http://blog.linkhub.co.kr/650
  ************************************************************/

  // 수정사유코드, 수정사유에 따라 1~6중 선택기재
  //$Taxinvoice->modifyCode = '';

  // 원본세금계산서 ItemKey 기재, 문서확인 (GetInfo API)의 응답결과(ItemKey 항목) 확인
  //$Taxinvoice->originalTaxinvoiceKey = '';



  /************************************************************
  *                       상세항목(품목) 정보
  ************************************************************/

  $Taxinvoice->detailList = array();

$Taxinvoice->detailList[] = new TaxinvoiceDetail();
$Taxinvoice->detailList[0]->serialNum = 1;				      // [상세항목 배열이 있는 경우 필수] 일련번호 1~99까지 순차기재,
$Taxinvoice->detailList[0]->purchaseDT = date("Ymd");//'20161102';	  // 거래일자
$Taxinvoice->detailList[0]->itemName = '카택스(비즈)';	  	// 품명
$Taxinvoice->detailList[0]->spec = '';				      // 규격
$Taxinvoice->detailList[0]->qty = $licenceQuantity;					        // 수량
$Taxinvoice->detailList[0]->unitCost = '5000';		    // 단가
$Taxinvoice->detailList[0]->supplyCost = ($totalPrice*$perDC);		  // 공급가액
$Taxinvoice->detailList[0]->tax = ($totalPrice*$perDC)*0.1;				      // 세액
$Taxinvoice->detailList[0]->remark = '';		    // 비고

   //  $Taxinvoice->detailList[] = new TaxinvoiceDetail();
// 	$Taxinvoice->detailList[1]->serialNum = 2;				      // [상세항목 배열이 있는 경우 필수] 일련번호 1~99까지 순차기재,
// 	$Taxinvoice->detailList[1]->purchaseDT = '20161102';	  // 거래일자
// 	$Taxinvoice->detailList[1]->itemName = '품목명2번';	  	// 품명
// 	$Taxinvoice->detailList[1]->spec = '';				      // 규격
// 	$Taxinvoice->detailList[1]->qty = '';					        // 수량
// 	$Taxinvoice->detailList[1]->unitCost = '';		    // 단가
// 	$Taxinvoice->detailList[1]->supplyCost = '100000';		  // 공급가액
// 	$Taxinvoice->detailList[1]->tax = '10000';				      // 세액
// 	$Taxinvoice->detailList[1]->remark = '';		    // 비고



  /************************************************************
  *                      추가담당자 정보
  * - 세금계산서 발행안내 메일을 수신받을 공급받는자 담당자가 다수인 경우
  * 추가 담당자 정보를 등록하여 발행안내메일을 다수에게 전송할 수 있습니다. (최대 5명)
  ************************************************************/

	$Taxinvoice->addContactList = array();

// 	$Taxinvoice->addContactList[] = new TaxinvoiceAddContact();
// 	$Taxinvoice->addContactList[0]->serialNum = 1;				        // 일련번호 1부터 순차기재
// 	$Taxinvoice->addContactList[0]->email = 'test@test.com';	    // 이메일주소
// 	$Taxinvoice->addContactList[0]->contactName	= '팝빌담당자';		// 담당자명
// 
// 	$Taxinvoice->addContactList[] = new TaxinvoiceAddContact();
// 	$Taxinvoice->addContactList[1]->serialNum = 2;			        	// 일련번호 1부터 순차기재
// 	$Taxinvoice->addContactList[1]->email = 'test@test.com';	    // 이메일주소
// 	$Taxinvoice->addContactList[1]->contactName	= '링크허브';		  // 담당자명

	try {
		$result = $TaxinvoiceService->RegistIssue($testCorpNum, $Taxinvoice, $testUserID,
                  $writeSpecification, $forceIssue, $memo, $emailSubject, $dealInvoiceMgtKey);
		$code = $result->code;
		$message = $result->message;
	}
	catch(PopbillException $pe) {
		$code = $pe->getCode();
		$message = $pe->getMessage();
	}
	
	$json->add("code",$code);
	$json->add("message",$message);
	
	$db = new Mysql();
	if( $code != 1){
	
		$DATA["companySeq"]  	= $_POST["companySeq"];
		$DATA["orderNumber"]  	= $_POST["orderNumber"];
		$DATA["message"]  		=  $message;
		
		$db->Insert("taxErrorLog", $DATA, "insert error");
		
	}else{
	
		$DATA["invoiceeCorpNum"] 		= 	 $_POST["invoiceeCorpNum"];
		$DATA["invoiceeCorpName"] 		= 	 $_POST["invoiceeCorpName"];
		$DATA["invoiceeCEOName"] 		= 	 $_POST["invoiceeCEOName"];
		$DATA["invoiceeAddr"] 			= 	 $_POST["invoiceeAddr"];
		$DATA["invoiceeContactName1"] 	= 	 $_POST["invoiceeContactName1"];
		$DATA["invoiceeEmail1"] 		= 	 $_POST["invoiceeEmail1"];
		$DATA["invoiceeBizType"] 		= 	 $_POST["invoiceeBizType"];
		$DATA["invoiceeBizClass"] 		= 	 $_POST["invoiceeBizClass"];
		$DATA["invoiceeTEL1"] 			= 	 $_POST["invoiceeTEL1"];
		$DATA["invoiceeHP1"] 			= 	 $_POST["invoiceeHP1"];

		
		$db->que = "SELECT * FROM taxinvoice WHERE companySeq =" .$_POST["companySeq"];
		$db->query();
		
		if($db->affected_rows() > 0)
		{
			$db->Update("taxinvoice", $DATA, "where companySeq=". $_POST["companySeq"], "update error");
			
		}
		else
		{
			$DATA["companySeq"] =	$_POST["companySeq"];
			$db->Insert("taxinvoice", $DATA, "insert error");
		}		
	}
	
	
	
	
echo $json->getResult();	
exit;
?>