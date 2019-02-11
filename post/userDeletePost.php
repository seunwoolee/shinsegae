<?

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/payment.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$companySeq			= $COMPANY_SEQ;
$check					= $_POST["check"];


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$payment = new Payment($db, $companySeq);

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db->que = "SELECT * FROM company WHERE seq=". $companySeq;
$db->query();
$company = $db->getRow();

$count = count($check);
$message = "";

for($i=0; $i<$count; $i++)
{
	unset($user);
	unset($DATA);
	
	$db->que = "SELECT * FROM user WHERE seq=". $check[$i];
	$db->query();
	$user = $db->getRow();
	
	if($user != null){
	
		$changeUid = "*remove_". $company["ikey"]. "_". uniqid();
		$DATA["uid"] = $changeUid;
		$DATA["enabled"] = "X";
		$db->Update("user", $DATA, "where seq=". $check[$i], "user delete error");
		
	}
}


$db->close();
LIB::Alert("", "../user.html");
exit;
?>