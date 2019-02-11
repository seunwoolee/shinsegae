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



echo trim(base64_encode(hash('sha256', "aaaaaa", true))); 
 
?>