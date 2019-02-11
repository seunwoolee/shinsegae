<?
header("Content-Type:text/html;charset=UTF-8");
//###################################################
// 세금계산 명세서 정보 
// 2017
//###################################################


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/json.inc.php";
include "../inc/mysql.inc.php";
include "../inc/taxinvoice.inc.php";

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$companySeq					= $COMPANY_SEQ;
$invoiceeCorpNum			= $_POST['invoiceeCorpNum'];
$invoiceeCorpName			= $_POST['invoiceeCorpName'];
$invoiceeCEOName			= $_POST['invoiceeCEOName'];
$invoiceeAddr				= $_POST['invoiceeAddr'];
$invoiceeContactName1		= $_POST['invoiceeContactName1'];
$invoiceeEmail1				= $_POST['invoiceeEmail1'];
$invoiceeBizType			= $_POST['invoiceeBizType'];
$invoiceeBizClass			= $_POST['invoiceeBizClass'];
$invoiceeTEL1				= $_POST['invoiceeTEL1'];
$invoiceeHP1				= $_POST['invoiceeHP1'];



$db = new Mysql();


//Json Class
$json = new Json();


$taxinvoice = new TaxinvoiceInstance($db,$companySeq);
// $taxinvoice->setTaxinvoice($companySeq,$invoiceeCorpNum,$invoiceeCorpName,$invoiceeCEOName,$invoiceeAddr,$invoiceeContactName1,$invoiceeEmail1,$invoiceeBizType,$invoiceeBizClass,$invoiceeTEL1,$invoiceeHP1);
$aa = $taxinvoice->setTaxinvoice($companySeq,$invoiceeCorpNum,$invoiceeCorpName,$invoiceeCEOName,$invoiceeAddr,$invoiceeContactName1,$invoiceeEmail1,$invoiceeBizType,$invoiceeBizClass,$invoiceeTEL1,$invoiceeHP1);


$json->add("code", "OK");
$json->add("message", $aa);
	
echo $json->getResult();
$db->Close();
exit;

?>