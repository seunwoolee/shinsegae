<?
include "inc/config.php";
include "inc/lib.inc.php";
include "inc/mysql.inc.php";
include "inc/drivingLog.inc.php";
include "inc/paging.inc.php";

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$seq = $_GET["seq"];

?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>신세계 푸드 차량 운행일지</title>
<link href="css/upview.css" rel="stylesheet">
<script src="js/jquery.win_galley.js" type="text/javascript"></script>
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
</head>
<body>
<div class="sliderHolder">
    <div id="fullscreen_slider" class="slider" data-elem="slider">
        <div class="sliderBg darkGray"></div>
        <div class="slides" data-elem="slides" data-options="adjustHeight:-30; loadIndexOnly:false;" ></div>
        <div class="thumbsHolder" data-elem="thumbsHolder">
            <div class="thumbs blackBgAlpha60" data-elem="thumbs" data-options="space:2; setParentVisibility:true; initShow:true;  data-show="bottom:0px; position:absolute; display:block" data-hide="bottom:-100%; display:block" ></div>
        </div>
        <div class="captionHolder" data-elem="captionHolder">
            <div class="caption blackBgAlpha60" data-elem="caption" data-options="initShow:true; setHolderHeight:true;" data-show="top:0%; display:block; autoAlpha:1;" data-hide="top:-60px; display:none; autoAlpha:0; ease:Power4.easeIn"> </div>
        </div>
        <div class="controlHolder">
            <div class="autoPlayIcon controlPos1" data-elem="autoPlay" data-on="background-position:-50px 0px;" data-off="background-position:0px 0px;"> </div>
            <div class="prevIcon controlPos2" data-elem="prev" data-on="autoAlpha:1; cursor: pointer;" data-off="autoAlpha:0.5; cursor:default"> </div>
            <div class="nextIcon controlPos3" data-elem="next" data-on="autoAlpha:1; cursor: pointer;" data-off="autoAlpha:0.5; cursor:default"> </div>
            <div class="zoomOutIcon controlPos4" data-elem="zoomOut" data-on="autoAlpha:1; cursor: pointer;" data-off="autoAlpha:0.5; cursor:default"> </div>
            <div class="zoomInIcon controlPos5" data-elem="zoomIn" data-on="autoAlpha:1; cursor: pointer;" data-off="autoAlpha:0.5; cursor:default"> </div>
            <div class="thumbsOnIcon controlPos6" data-elem="thumbsToggle" data-on="autoAlpha:1; cursor: pointer;" data-off="autoAlpha:0.5; cursor:default"></div>
        </div>
        <ul data-elem="items">		
		<?
			
			$db->que = " select * from receipt Where drivingLog_seq = ".$seq;
			$db->query();
			while($row = $db->getRow())
			{?>
				<li>
				<!--<? $title = str_replace(" ","%20", $row["imageUrl"]) ?>		
				<? $img = str_replace("../..","", $row["imageUrl"]) ?>
				<?$url = "http://dev.jeycorp.com/kimchs/www/".$img?>-->


					<img src="<?="data/oil".$row["imageUrl"]?>"  />
					
					
				<!--<div data-elem="imgCaption">					
					<div class="superCaption"><?=$row["imageUrl"]?></div>		
					<div class="superCaption"><span><?=$row["grapher_id"]?></span><?=$row["imageUrl"]?></div>				
				</div>-->
				</li>
			<?}?>

		
        </ul>
    </div>
</div>
</body>
</html>
