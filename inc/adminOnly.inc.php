<?
if($_SESSION["OMember_admin"] <> "Y")
{
	lib::Alert("권한이 없습니다.",'main.html');
}
?>