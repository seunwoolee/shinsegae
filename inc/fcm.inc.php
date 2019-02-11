<?
class Fcm
{
	var $api_key = "AAAAacnvKZE:APA91bEQFqH_r2-7X6oqP0QCPBgw3ghvaGF7FtD5ZBn25bmVneAGdTWVm28WjktbVMo2wbtpX05oSRu0aa_GsxH19I_TVYm6OGYtNKh3uGgUmz-KwJErm7kucA0D52b35LUb81ilHFHB";
	var $recv_count = 0;
	var $data;
	var $fp;

	var $SEND_TYPE_RECEIPT = "RECEIPT";


	function Fcm()
	{

	}

	function addPushId($push_id)
	{
		if(empty($push_id) == false)
		{
			$this->data["registration_ids"][$this->recv_count] = $push_id;
			$this->recv_count += 1;
		}
	}

	function init() 
	{
		unset($this->data["registration_ids"]);
		$this->recv_count = 0;
	}

	/*function addParam($field, $text)
	{
		$this->data["data"][$field] = $text;
		$this->data["notification"][$field] = $text;
	}*/

	function addParam($osType, $field, $text)
	{
		if($osType == "iOS")
		{
			if($field == "title" || $field == "body")
			{
				$this->data["notification"][$field] = $text;
			}
			else
			{
				$this->data["data"][$field] = $text;
			}
		}
		else if($osType == "Android")
		{
			$this->data["data"][$field] = $text;
		}
	}



	function send()
	{
		$this->data["content_available"] = true;
		$this->data["priority"] = "high";

		$headers = array('Content-Type:application/json ; charset=UTF-8', 'Authorization:key='. $this->api_key, "Connection: Close");
		//https://android.googleapis.com/gcm/send
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,    'https://fcm.googleapis.com/fcm/send');
		curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
		curl_setopt($ch, CURLOPT_POST,    true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($this->data));

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if (curl_errno($ch))
		{
			$return = "curl_errno : ". curl_errno($ch);
		}
		else if ($httpCode != 200)
		{
			$return = "http Error Code : ". $httpCode;
		}
		else
		{
			//echo $i. " ==> ". $response;
			//echo "<br><br>";
			
			unset($resArr);
			$resArr = json_decode($response, JSON_UNESCAPED_UNICODE);

			$success_count = $resArr["success"];
			$failure_count = $resArr["failure"];
			$return = "success:". $success_count. " , failure:". $failure_count;

			//전송실패 push_id 수집
			//$this->setFailurePushId($resArr);
		}
		
		//registration_ids 초기화
		$this->init();
		curl_close($ch);

		//sleep(1);
		return $return;
	}

	function addReceiptParam($osType, $seq, $state, $message)
	{
		$this->addParam($osType, "sendType", $this->SEND_TYPE_RECEIPT);
		$this->addParam($osType, "seq", $seq);
		$this->addParam($osType, "state", $state);
		$this->addParam($osType, "body", $message);
	}
}

?>