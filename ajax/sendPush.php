<?
	include "../inc/config.php";
	include "../inc/mysql.inc.php";
	include "../inc/json.inc.php";
	include "../inc/lib.inc.php";

	$checkedArray = $_POST["checkedArray"];
	$mode = $_POST["mode"];
	$message = $_POST["message"];	
    define( 'API_ACCESS_KEY', 'AAAAiPHcC_4:APA91bEQADwEOD6yOEACkqtv5fnmCRb0hoaUbt5IqrxpJK7r5koUlFN1K-fxLbCDKr9NPzhQut6_ji7n-ekFoaLjxTducs1VjROKKTNWjiyNXMKHkZNWWvcb56_hrdZVboSKt7wBrbD_' );

	$db = new Mysql();
	
	if($mode == "allSend")
	{
		$db->que = " SELECT pushId FROM user where LENGTH(pushId) > 10 ";
		$db->query();
	}
	else if($mode == "approverSend")
	{
		$db->que = " SELECT Distinct u.pushId FROM departmentView as dv";
		$db->que .= " left join user as u On (dv.approverUserSeq = u.seq) WHERE approverUserSeq IS NOT NULL ";
		$db->query();
	}
	else if($mode == "choiceSend")
	{
		$db->que = " SELECT pushId FROM user WHERE seq IN (".$checkedArray.") AND LENGTH(pushId) > 10";
		$db->query();
	}

	$registrationIds = [];
	$rows = $db->getRows();
	
	for($i=0; $i<count($rows);$i++)
	{
		if(empty($rows[$i]["pushId"])==false){
			array_push($registrationIds,$rows[$i]["pushId"]);
		}
	}

	if(count($registrationIds) > 0)
	{
		$msg = array
			  (
					'body' 	=> $message
					//'title'	=> '카택스 TMS 배차알림',
			  );
		$fields = array
				(
					'registration_ids'		=> $registrationIds,
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
		echo $result;
	}
lib::Plog($result);
?>