<?
header("Content-Type:text/html;charset=UTF-8");

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";



//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -


$companySeq						= $COMPANY_SEQ;

$type									= $_POST["type"];
$basicCost							= (int) $_POST["basicCost"];
$defaultMileage					= (int) $_POST["defaultMileage"];

$gasolineUp1800Cost						= (int) $_POST["gasolineUp1800Cost"];
$gasolineDown1800Cost						= (int) $_POST["gasolineDown1800Cost"];
$dieselCost							= (int) $_POST["dieselCost"];
$gasCost							= (int) $_POST["gasCost"];
$hybbridGasoilneCost						= (int) $_POST["hybbridGasoilneCost"];
$hybbridLpiCost						= (int) $_POST["hybbridLpiCost"];


$bonusCost							= $_POST["bonusCost"];
$bonusSection						= $_POST["bonusSection"];
$bonusPercent						= $_POST["bonusPercent"];

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();



if($type == "Basic")
{
	if($basicCost < 1)
	{
		LIB::Alert("1Km당 유류비 입력 오류!!", "-1");
		exit;
	}

	$DATA["type"]							= $type;
	$DATA["basicCost"]					= $basicCost;
	$DATA["bonusCost"]					= $bonusCost;
	$DATA["bonusSection"]				= $bonusSection;
	$DATA["bonusPercent"]				= $bonusPercent;
	$db->Update("calculateOilSetting", $DATA, "where seq = 1", "정산 설정 수정");

}
else if($type == "Mileage")
{
	if($defaultMileage < 1)
	{
		LIB::Alert("기본연비 입력 오류!!", "-1");
		exit;
	}

	if($gasolineDown1800Cost < 1)
	{
		LIB::Alert("휘발유값 입력 오류!!", "-1");
		exit;
	}

	if($dieselCost < 1)				$dieselCost=0;
	if($gasCost < 1)					$gasCost=0;



	$DATA["type"]							= $type;
	$DATA["defaultMileage"]				= $defaultMileage;
	$DATA["gasolineUp1800Cost"]				= $gasolineUp1800Cost;
	$DATA["gasolineDown1800Cost"]				= $gasolineDown1800Cost;
	$DATA["dieselCost"]					= $dieselCost;
	$DATA["gasCost"]						= $gasCost;
	$DATA["hybbridGasoilneCost"]				= $hybbridGasoilneCost;
	$DATA["hybbridLpiCost"]				= $hybbridLpiCost;
	$DATA["bonusCost"]					= $bonusCost;
	$DATA["bonusSection"]				= $bonusSection;
	$DATA["bonusPercent"]				= $bonusPercent;
	$db->Update("calculateOilSetting", $DATA, "where seq = 1", "정산 설정 수정");
}


$db->close();

LIB::Alert("", "openerReload");
LIB::Alert("수정 되었습니다.", "close");
exit;
?>
