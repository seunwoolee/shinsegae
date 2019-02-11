<?php

include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/receipt.inc.php";
include "../inc/mailSender.inc.php";


$db = new Mysql();

//select c.cid, c.name, c.adminName, c.paymentTerm, c.licenceQuantity, p.beginDate, p.endDate  from payment AS p  join company AS c on p.companySeq = c.seq WHERE p.state ='Complete' AND p.finally='Y' 
//date('Y-m-d',strtotime('+7 day'))
$db->que = "select c.cid, c.name, c.adminName, c.paymentTerm, c.licenceQuantity, c.email, p.beginDate, p.endDate  from payment AS p  join company AS c on p.companySeq = c.seq WHERE p.state ='Complete' AND p.finally='Y' AND p.endDate = '" .date('Y-m-d',strtotime('+7 day')) . "'";
$db->query();

$mailSender = new MailSender();
$fromName = "카택스 oil";
$toName = "카택스";
$subject = "카택스 계약이 만료될 예정입니다";


while($row = $db->getRow()){

	$mailSender->send($fromName, $row["email"], $toName, $subject, $mailSender->contractEmail($row["cid"],$row["name"],$row["paymentTerm"],$row["licenceQuantity"],$row["beginDate"],$row["endDate"]) );
}

?>



