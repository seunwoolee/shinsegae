<?
	include "../inc/config.php";
	include "../inc/mysql.inc.php";
	include "../inc/json.inc.php";
	include "../inc/lib.inc.php";
	include "../inc/push.inc.php";
	
	$db = new Mysql();
	$push = new Push($db);
    define( 'API_ACCESS_KEY', 'AAAAiPHcC_4:APA91bEQADwEOD6yOEACkqtv5fnmCRb0hoaUbt5IqrxpJK7r5koUlFN1K-fxLbCDKr9NPzhQut6_ji7n-ekFoaLjxTducs1VjROKKTNWjiyNXMKHkZNWWvcb56_hrdZVboSKt7wBrbD_' );
	
	$checkedArray = $_POST["checkedArray"];
	$checkedValue = implode(",",$checkedArray);
	$mode = $_POST["mode"];
	$title = [];
	

 
	if($mode == "reject")
	{
		for($i=0;$i<count($checkedArray);$i++)
		{
			$title[$i] = "[반려]".$push->getTitleMent($checkedArray[$i])." 운행건 반려";
		}	
		$message = $push->getRejectMessageMent($_POST["message"]);	
		$rows = $push->getPushIdRows($checkedValue);
	}
	else if($mode == "approval")
	{
		for($i=0;$i<count($checkedArray);$i++)
		{
			$title[$i] = "[승인]".$push->getTitleMent($checkedArray[$i])." 운행건 승인";
		}	
		$message = "정상적으로 승인 완료 되었습니다.";	
		$rows = $push->getPushIdRows($checkedValue);
	}	

	
	
	$registrationIds = [];
	for($i=0; $i<count($rows);$i++)
	{
		if(empty($rows[$i]["pushId"])==false)
		{
			array_push($registrationIds,$rows[$i]);
		}
	}
	
	for($i=0;$i<count($registrationIds);$i++) // registrationIds Array 형태가 아니면 안보내짐
	{
		$j = 0;
		$registrationIds[$i][$j] = $registrationIds[$i]["pushId"];
		unset($registrationIds[$i]["pushId"]);
	}
	
	lib::Plog(count($registrationIds));
	lib::Plog(count($title));
	
	
	if(count($registrationIds) == count($title))
	{
		for($j=0;$j<count($title);$j++)
		{
			$msg = array
				  (
						'body' 	=> $message,
						'title'	=> $title[$j]
				  );
			$fields = array
					(
						'registration_ids'		=> $registrationIds[$j],
						'notification'			=> $msg
					);
			
			
			$headers = array
					(
						'Authorization: key=' . API_ACCESS_KEY,
						'Content-Type: application/json'
					);

		#Send Reponse To FireBase Server	
				$ch = curl_init();
				curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
				curl_setopt( $ch,CURLOPT_POST, true );
				curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
				curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
				$result = curl_exec($ch );
				curl_close( $ch );
		#Echo Result Of FireBase Server	
		}
		echo $result;
	}
?>