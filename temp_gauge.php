<?php
global $IMG_SERVER,$HTPFX,$HTHOST,$HTADMINHOST,$D_R;
$pageJS=array('config','redesign','Articles','jquery','global');
$pageCSS=array("global","nav","articlepage",'zoomimage');
include($D_R."/admin/lib/_article_data_lib.php");
$pageName = "Temp_Gauge";
include($D_R."/_header.htm");
?>
<div class="shadow">
<div id="content-container" style="position:relative;">
<?php
$objArticle = new ArticleData();
$tempGaugeArr = $objArticle->getTempGaugeData($_GET['id']);

if(empty($tempGaugeArr))
{
?>
	<div style="float:left;	margin:70px 178px 70px 178px;">
		<div style='float:left;	width:600px;height:11px;background-image: url("<?=$IMG_SERVER?>/images/temp_gauge/error_top.png");'></div>
		<div style='float:left;width:600px;min-height:100px;color:#000000;font-size:26px;font-weight:bold;text-align:center;padding:65px 0px 0px 0px;font-family:Helvetica,Arial,sans-serif;background-repeat: repeat-y;background-image: url("<?=$IMG_SERVER?>/images/temp_gauge/error_mid.png");'>This is an invalid URL. Please try again</div>
		<div style='float:left;width:600px;height:11px;background-image: url("<?=$IMG_SERVER?>/images/temp_gauge/error_bottom.png");'></div>
	</div>
	</div>
	<?php include($D_R."/_footer.htm"); ?>
	</div>
<?php
	exit;
}
$tempGauge = $tempGaugeArr[0];
$level = strtolower($tempGauge['gauge']);
$pageName="Daily_Digest_Email";
	if($tempGauge['gauge']=="A")
	{
		$style="background: url(".$IMG_SERVER."/images/temp_gauge/meter_a.png) no-repeat 111px 175px;";
	}
	else if($tempGauge['gauge']=="B")
	{
		$style="background: url(".$IMG_SERVER."/images/temp_gauge/meter_b.png) no-repeat 111px 225px; ";
	}
	else if($tempGauge['gauge']=="C")
	{
		$style="background: url(".$IMG_SERVER."/images/temp_gauge/meter_c.png) no-repeat 111px 285px;";
	}
	else if($tempGauge['gauge']=="D")
	{
		$style="background: url(".$IMG_SERVER."/images/temp_gauge/meter_d.png) no-repeat 111px 337px; ";
	}
?>

<div class="temp_gauge" style="float: left;width: 800px;margin: 20px 80px;">
	<div class="gauge_wrapper" style="font: normal 9pt arial;margin: 0pt auto; background-color: rgb(250, 250, 250); float: left; width: 830px;">
	<div class="content_white_area" style="float: left; width: 780px; background-color: rgb(255, 255, 255); margin: 10px; padding: 15px;">
	<div class="left_meter_img" style="float:left;<?=$style?>"><img src="<?php echo $IMG_SERVER?>/images/temp_gauge/left_meter.png" /></div>
	<div class="gauge_hdr_heading" style="float:left;margin-left:30px;"><img src="<?php echo $IMG_SERVER?>/images/temp_gauge/hdr_heading.png" /></div>

	<div class="gauge_para_content" style="float:left;background-image:url(<?php echo $IMG_SERVER?>/images/temp_gauge/para_bg.png);width:480px;
	height:246px;margin-left:30px;margin-top:20px;"><div class="gauge_para_text" style="float:left;width:272px;
	height:166px;font-size:13px;color:#0e4f73;margin-left:190px;margin-top:50px;">The Minyanville Market Temperature is based on the ATAC (Accelerated Time And Capital) models used by Pension Partners, LLC to manage client accounts.  The concept is simple. Conditions matter more than predictions when it comes to investing. Much like driving, you have a higher chance of getting to your destination faster and safer is its sunny outside. If it's raining, your odds of getting into an accident are much higher, and you're better off driving slower.</div></div>
	<div style="float:left;width:510px;">
		<div id="outlook_heading" style="font-size: 22px;font-weight: bold;color: #176b9c;float: left;
	width:100%;min-height: 0;margin-left:30px;margin-top:20px;">This Week's Outlook:</div>
		<div id="text_below_para" style="font-family:arial;float:left;font-size:13px;color:#0e4f73;width:480px;min-height: 0;margin-left:30px;margin-top:10px;"><?=$tempGauge['tg_desc']?></div>
	</div>

	<div class="gauge_total_text" style="font-family:arial;float:left;font-size:13px;color:#0e4f73;margin-top:20px;">In the context of investing, sunny conditions for risk-assets occur when inflation expectations are rising. Risk-on into risk assets makes more sense when the crowd expects inflation going forward. Risk-off occurs in periods where inflation expectations are falling. Notice that what we're talking about here is crowd expectations, not actual inflation. The crowd could be right or wrong, but regardless of that it is the crowd that sets price.<br /><br />
	The ATAC models run by Pension Partners are designed to address first and foremost what the direction of inflation expectations are, and then determine how to best position based on various intermarket trends and relationships. In an exclusive team-up with Minyanville, Pension Partners is now providing Minyans with an exclusive look into what ATAC suggests market conditions are in terms of whether the odds favor being risk-on or risk-off.<br /><br />
	There are no guarantees when it comes to investing, but there are times when probabilities are higher for certain kinds of trades than others.  A higher temperature favors being risk-on, while a lower temperature means the odds are increasing of a difficult environment to invest in risk-assets.<br /><br />
	The weekly updates to the Minyanville Temperature gauge are meant to give you another way of looking at markets to either help confirm or question existing thinking based on what a quantitative models is sensing about how the crowd thinks. How to best take advantage of this is up to you.<br /><br />
	To learn more about ATAC and Pension Partners, LLC, visit <a style="color:#167fc5;font-weight:normal;" href="#" target="_parent">www.pensionpartners.com</a>. In addition, you can discover more about the thinking by following the writings of Minyanville's very own Michael A. Gayed, CFA through his weekly Lead-Lag Reports and various articles on markets.<br /><br />
	</div>
	<div id="gauge_link" style="font-family:arial;float:left;width:760px;margin:10px 0px;"><a href="<?=$tempGauge['link']?>" style="text-decoration:none;" ><span style="color:#167fc5;font-weight:normal;font-size:15px;">See This Week's Lead-Lag Report</span></a>
	</div>
	<div class="gauge_footer" style="font-family:arial;float:left;background-image:url(<?php echo $IMG_SERVER?>/images/temp_gauge/footer_bg.png);width:781px;height:152px;"><div class="gauge_footer_text" style="float:left;font-size:11px;color:#0e4f73;width:620px;height:120px;margin:20px 0px 0px 20px;">This writing is for informational purposes only and does not constitute an offer to sell, a solicitation to buy, or a recommendation regarding any securities transaction, or as an offer to provide advisory or other services by Pension Partners, LLC and Minyanville Media, Inc. in any jurisdiction in which such offer, solicitation, purchase or sale would be unlawful under the securities laws of such jurisdiction.<br /><br />

	The information contained in this writing should not be construed as financial or investment advice on any subject matter. Pension Partners, LLC and Minyanville Media, Inc. expressly disclaims all liability in respect to actions taken based on any or all of the information on this writing.
	</div></div>

	</div>

	</div>
</div>
</div>
</div>
<?php include($D_R."/_footer.htm"); ?>
</div>