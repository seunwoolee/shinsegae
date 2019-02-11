<?php

include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/mailSender.inc.php";

function randomkeys($length)
{
    $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
    $key  = $pattern{rand(0,36)};
    for($i=1;$i<$length;$i++) {
        $key .= $pattern{rand(0,36)};
    }
    return $key;
}

$email = $_POST["email"];
$adminId = $_POST["adminId"];


$db = new Mysql();
//$db->que = "SELECT * FROM company WHERE cid='". $adminId ."'  AND email='". $email ."'  AND corporateNumber= '". $corporateNumber . "'";
$db->que = "SELECT * FROM user WHERE employeeNumber='admin' AND email='".$email."'";
$db->query();


if($db->affected_rows() > 0){

	$row = $db->getRow();

	
     $password =	randomkeys(6);
	 $securePassword = trim(base64_encode(hash('sha256',  $password, true)));
	 
	 
	$DATA["password"] = $securePassword;
	$db->Update("user", $DATA, "where employeeNumber='admin'", "X");
	
	$mailSender = new MailSender();
	$fromName = "카택스 oil";
	$toAddress = $row["email"];
	$toName = "카택스";
	$subject = "관리자 비밀번호 초기화";
	
	$mailSender->send($fromName, $toAddress, $toName, $subject, $mailSender->passwordContents($password,"admin","관리자") );
	 
	 LIB::Alert("임시 비밀번호를 전송 하였습니다.","../index.html");	
}else{

	LIB::Alert("계정 정보를 확인 부탁드립니다.","-1");
}

$db->close();
exit;

?>




