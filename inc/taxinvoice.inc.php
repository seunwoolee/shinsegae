<?

// require_once '../Taxinvoice/common.php';

class TaxinvoiceInstance{
	
	
	
	var $db;
	var $companySeq;
	
	
	function TaxinvoiceInstance($db, $companySeq){
	
		$this->db = $db;
		$this->companySeq = $companySeq;
		
	}
	
	function getTaxinvoice(){
		
		$this->db->que = "SELECT * FROM taxinvoice WHERE companySeq=". $this->companySeq;
		
		$this->db->query();
		if($this->db->affected_rows() > 0)
		{
			return $this->db->getRow();
		}
		else
		{
			return null;
		}
	}//getTaxinvoice
	
	
	function setTaxinvoice($companySeq,$invoiceeCorpNum,$invoiceeCorpName,$invoiceeCEOName,$invoiceeAddr,$invoiceeContactName1,$invoiceeEmail1,$invoiceeBizType,$invoiceeBizClass,$invoiceeTEL1,$invoiceeHP1){

		$DATA['invoiceeCorpNum'] 		= $invoiceeCorpNum;
		$DATA['invoiceeCorpName'] 		= $invoiceeCorpName;
		$DATA['invoiceeCEOName'] 		= $invoiceeCEOName;
		$DATA['invoiceeAddr'] 			= $invoiceeAddr;
		$DATA['invoiceeContactName1'] 	= $invoiceeContactName1;
		$DATA['invoiceeEmail1'] 		= $invoiceeEmail1;
		$DATA['invoiceeBizType'] 		= $invoiceeBizType;
		$DATA['invoiceeBizClass'] 		= $invoiceeBizClass;
		$DATA['invoiceeTEL1'] 			= $invoiceeTEL1;
		$DATA['invoiceeHP1'] 			= $invoiceeHP1;


		
		$this->db->que = "SELECT * FROM taxinvoice WHERE companySeq =" .$this->companySeq;
		$this->db->query();

		if($this->db->affected_rows() > 0)
		{
			$this->db->Update("taxinvoice", $DATA, "where companySeq=". $this->companySeq, "update error");
			return 'Update';
		}
		else
		{
			$DATA["companySeq"] =	$this->companySeq;
			$this->db->Insert("taxinvoice", $DATA, "insert error");
			return 'Insert';
		}	
		
	}//setTaxinvoice

	
	function setNoneTaxinvoice($orderNumber){
	
	
		$DATA["companySeq"] =	$this->companySeq;
		$DATA["orderNumber"] =	$orderNumber;
		$DATA["message"] =	'세금명세서 정보가 없습니다.';
		
		$this->db->Insert("taxErrorLog", $DATA, "insert error");
		
	}//setNoneTaxinvoice
	

}



?>