<?
$pageJS=array('config','dailyfeed','jquery','global','featuredslider','mobileredirect','emailalert','Articles','stickycontent','sticky','stickyscroller','getset','fancybox','mailchimp');
$pageCSS=array("global","layout","nav","codaslider","fontStylesheet","homepage","rightColumn","fancybox","mailchimp","surveyBox");
//phpAdServe zone ID
$zoneIDSky = $SEC_TO_ZONE['eventstop'];
$zoneIDFooter = $SEC_TO_ZONE['eventsfooter'];
$zoneIDFooter2 = $SEC_TO_ZONE['eventsfooter2'];
$zoneIDBottom2 = $SEC_TO_ZONE['eventsbottom2'];
/* Operative Configuration *****/
$zone_name="events";
$tile728x90=1;
$tile160x600top=2;
$tile160x600bottom=3;
$tile468x60=4;
$tile300x250=5;
$tile125x125=5;
/* End Operative Configuration *****/
$page_navigation=$NAVIGATION["COMMUNITY"];
$title= "EVENTS";
$pageName = "events";
include("../layout/dbconnect.php");
include("../layout/layout_functions.php");
include("../_header.htm");

$modules = getModules();
?>
<script language="javascript" type="text/javascript">
</script>
<!------------------------ Main Content -------------->
<tr><td class="underheader">
<div class="columncontainer">
<div class="columnLeft">
 <div class="columnLeftInner">
  <table cellpadding="0" cellspacing="0">
  <tr><td>
			<table cellpadding="0" cellspacing="0">
    <!-- ********  News and Views area including featured article ********  -->
				<tr><td class="articletitlehome"><center><?=$title?></center></td></tr>
		   		<tr><td class="date" align="center"><?= displayDate() ?></td></tr>
                <tr><td>
                <table class="featuredarticle" cellpadding="0" cellspacing="0">
                <tr><td >
				<div > <? call_Column_Module_List_Image($pageName,'upperbox',$modules,'show','upperbox'); ?></td>
						</div>
				<tr><td><br>
					  <div>
					  <table width="96%" border="0" cellpadding="0" cellspacing="0">
			          <tr>
			          <td width="510px">

			          <? call_Column_Module_List($pageName,'upperleftcolumn1',$modules,'show','upperleftcolumn1'); ?></td></tr>

			          </td>
			          </tr></table>


		      </div>

				</td></tr>
                </td></tr>
                </table>
                </td></tr>
     		</table>
	    </td></tr>
	    <tr> <td>
	  <tr> <td class="greyline">&nbsp; </td></tr>
	    <tr>
	      <td colspan="3" align="center">
	           <table width="100%" border="0" cellpadding="0" cellspacing="0">
			          <tr>
			          <td width="510px">
			          <? call_Column_Module_List($pageName,'bottomleftcolumn1',$modules,'show','bottomleftcolumn1'); ?></td></tr>
			         </td>
			          </tr></table>

	   </td>
    </tr>
				</table>
				<!-- end left-bottom area-->
</div>
</div>
<!----------Middle column ------------------>
<div class="columnMiddle">
<table border="0" cellpadding="0" cellspacing="0">
<tr><td>
  <? call_Column_Module_List($pageName,'column3',$modules,'show','middlemoduleinner') ; ?>
</td></tr>
<tr><td><?  displayShareBox(); ?></td></tr>
<tr><td><br>
						<center>
	  				<? show_ads_operative($zone_name,$tile300x250,$ADS_SIZE['MediumRectangle']);?>
					</center>
</td></tr></table>

</div>
<?include("$D_R/_footer.htm");?>
