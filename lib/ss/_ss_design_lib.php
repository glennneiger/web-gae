<?
function createAddOptionDiv(){
	global $IMG_SERVER;
	$strout='<div style="float:left; width:100%;"><form id="optionform" method="post" onSubmit="return false;">
	<table style="border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" width="100%" border=0 cellspacing="0" cellpadding="3" bordercolor=black>
	<tr>
	<td width="15%" class="quintportfolio">Transaction Type</td>
	<td width="20%" class="quintportfolio" ><nobr>Date(mm/dd/yyyy)</td>
	<td width="20%" class="quintportfolio">Option Ticker</td>
    <td width="15%" class="quintportfolio" colspan="4">Type of Option</td>
    <td width="30%" class="quintportfolio">Base Stock</td>
	</tr>';
	$strout.='<tr bgcolor="#efefef" ><td><select id="optiontranstype" name="optiontranstype" onChange="javascript:redirectoptionpage();"><option value="0">Buy</option><option value="1">Sell</option><option value="2">Sell Short</option><option value="3">Buy to Cover</option></select></td>
	<td><input type="text" size="10" id="optPurdate" name="optPurdate" onClick=javascript:NewCal("optPurdate","mmddyyyy")>&nbsp;<img src="'.$IMG_SERVER.'/images/quint_images/cal.gif" width="16" height="16" border="0" alt="Pick a date" align="absmiddle" onClick=javascript:NewCal("optPurdate","mmddyyyy") style="cursor:pointer;"></td>
	<td><input size="8" type="text" id="optionticker" name="optionticker" onblur="javascript:validateoptionticker(\'optionticker\',\'optionerrormsgdiv\');" ></td>
	<td><input type="radio" id="optiontype" name="optiontype" value="p" style="border:0px;">P
	<input type="radio" id="optiontype" name="optiontype" value="c" style="border:0px;">C
	</td>
	<td colspan="4"><input type="hidden" name="stockBaseId" id="stockBaseId" value="0"><input type="text" id="basestock" name="basestock" value="" onBlur="javascript:validateBaseStock(this.id,\'optionerrormsgdiv\');" ></td></tr>
	<tr><td colspan="10">&nbsp;</td></tr>
	<tr>
	<td class="quintportfolio">Expiry</td>
    <td class="quintportfolio">Strike Price</td>
	<td class="quintportfolio">No. of Contracts</td>
	<td class="quintportfolio">Price per Option</td>
	<td colspan="4" class="quintportfolio">Notes</td>
	</tr>
	<tr bgcolor="#efefef">
	<td><select name="optionexpirymonth" id="optionexpirymonth"><option value="">Mon</option>';

	$months=explode(":","Jan:Feb:Mar:Apr:May:Jun:Jul:Aug:Sep:Oct:Nov:Dec");
	$j=1;
	foreach($months as $i)
	{
			$strout.= '<option value="'.$j.'">'.$i.'</option>';
			$j++;
	}
	$strout.= '</select>&nbsp;';
	$strout.= '<select name="optionexpiryyear" id="optionexpiryyear"><option value="">Year</option>';
	$start=(date("Y"));
	$end=(date("Y")+25);
	foreach(range($start,$end) as $i)
	{
		$strout.= '<option value="'.$i.'">'.$i.'</option>';
	}
	$strout.= '</select>';
	$strout.='</td>
	<td><input size="10" type="text" name="strikeprice" id="strikeprice" ></td>
	<td><input size="10" type="text" name="noofcontract" id="noofcontract" value="0"></td>
	<td><input size="10" type="text" name="contractprice" id="contractprice" value="0.00"></td>
	<td colspan="4"><input type="text" name="optionnotes" id="optionnotes" size="40"></td>
	</tr>
	<tr><td colspan="8"></td></tr>
	<tr><td colspan="2">
	<img id="addimg" src="'.$IMG_SERVER.'/images/quint_images/addtoportfolio.gif" vspace="10" onClick="javascript:processOptionForm();" />
	</td><td colspan="6"><span id="optionerrormsgdiv" style="color:red"></span>
	</td></tr>
	</form></table></div>';
	return $strout;
}

function displaySmithLanding($arPD){
global $performanceobj,$IMG_SERVER,$HTPFX,$HTPFXSSL,$HTHOST;
$performanceobj->calculatePerformance();

?>
	<div class="smithContainer">
		<div class="smithBanner">
		<? $trialText= '<b><u> START YOUR 14-DAY FREE TRIAL RIGHT NOW!</u></b>';?>
			<div class="smithBannerContentYlw">Let veteran options trader Steve Smith show you the way to cut risk and boost your returns through the strategic use of options.</div>
			<div class="smithBanneContentWhite">Whether you already trade options or are looking for another tool to strengthen your portfolio, you can now benefit from Steve's experiences as a trader, market maker and successful newsletter author - having returned <b>+73% from 2004-2008, +<?=$performanceobj->TotAvgReturn?>% return since March 2009</b> and was <b>+39.47%</b> in 2010.*
			<?=getSmithAddtoCartbtnsTrial('OptionSmith', $trialText , 'subscription','subOptionSmith','Landed');?>
			</div>
		</div>
		<div class="smithSubscription">
			<div class="smithSubscriptionContainer">
				<div class="smithSubscriptionGreen">Watch the video below to learn more</div>
				<div class="smithSubscriptionContent">
					<script type="text/javascript" src="<?=$HTPFX.$HTHOST?>/mvtv/flowplayer/flowplayer-3.2.0.min.js"></script>
                    <a href="<?=$HTPFX.$HTHOST;?>/optionsmith/Video/OpSmithFinal.flv"
                    style="display:block;width:300px;height:155px;"
                    id="player"><img src="<?=$IMG_SERVER?>/images/optionsmith/opstill.jpg" alt="OptionSmith Video" />
                    </a>
                    <script language="JavaScript">
                    flowplayer("player", "<?=$HTPFX.$HTHOST?>/mvtv/flowplayer/flowplayer-3.1.5.swf");
                    </script>
				</div>
			</div>
			<div class="smithSubscriptionMiddle">
				 <a href="<?=$HTPFXSSL.$HTHOST?>/subscription/register/" onclick="return checkcart('<?=$arPD['subscription_def_id']?>','<?=$arPD['oc_id']?>','<?=$arPD['orderItemType']?>','<?=$arPD['product_name']?>','<?=$arPD['product_type']?>','<?=$arPD['event_name']?>');">
                 <img style="cursor:pointer;" src="<?=$IMG_SERVER?>/images/subscription/smith_free.jpg" class="freeimg" alt="" >
                 </a>
			</div>
			<div class="smithSubscriptionContainer">
				<div class="smithSubscriptionGreen">Optionsmith Performance</div>
				<div class="smithSubscriptionContent"><?=$performanceobj->datadisplayperformance_newLanding($loginoption,$home);?></div>
				<div class="smithSubscriptionBottom"><img src="<?$IMG_SERVER?>/images/optionsmith/smith_box_bttm.jpg" /></div>
			</div>
		</div>
		<div class="smithSubscription">
			<div class="smithFeatures">
				<div class="smithFeaturesImage"><img src="<?$IMG_SERVER?>/images/optionsmith/smith_bigbox_top_940.png" /></div>
				<div class="smithFeaturesContent">
				<h1>Here are some of the fantastic features of Optionsmith:</h1>
				<div class="featuresContent">
				<ul>
					<li>Get email alerts every time Steve makes a trade</li>
					<li>Detailed analysis and rationale for each trade</li>
					<li>Trade management: (entries/exits/stops)</li>
					<li>Weekly review of all positions and a look at the week ahead</li></ul>
				</div>
				<div class="featuresContent">
				<ul>
					<li>Flexibility to follow all of Steve's trades or just the ones that meet your personal risk tolerance and guidelines</li>
					<li>The best strategies to use in volatile markets</li>
					<li>Access to the OptionSmith portfolio holdings, closed positions and performance.</li>
				</div>
				</ul>
				</div>
				<div class="smithFeaturesImage"><img src="<?$IMG_SERVER?>/images/optionsmith/smith_bigbox_bttm_940.png" /></div>
			</div>
		</div>
		<div class="smith14Day"><?=getSmithAddtoCartbtnsMonthly('OptionSmith','smith_bottom_adsignup.jpg', 'subscription','subOptionSmith','Landed');?></div>
		<div class="smithoffers">Subscribe to any other Minyanville product on an annual basis and get 20% off that product and OptionSmith. Subscribe to three Minyanville products at the annual rate and get 25% off all products.</div>
		<div class="smithStarNotes">
		* Results as of <?=date('F, Y');?>.  Past performance may not be indicative of future results. <br>
** One winner will be selected randomly every month for a 12 month period, and announced on Minyanville.com. Standard contest rules apply.
		</div>
	</div>
<? }

function displaySmithLandingV2(){
global $performanceobj,$HTPFX,$HTHOST,$IMG_SERVER;
$performanceobj->calculatePerformance();
?>
	<div class="smithContainer">
		<div class="smithBanner">
		<? $trialText= '<b><u> START YOUR 14-DAY FREE TRIAL RIGHT NOW!</u></b>';?>
			<div class="smithBannerContentYlw">Let veteran options trader Steve Smith show you the way to cut risk and boost your returns through the strategic use of options.</div>
			<div class="smithBanneContentWhite">Whether you already trade options or are looking for another tool to strengthen your portfolio, you can now benefit from Steve's experiences as a trader, market maker and successful newsletter author - having returned <b>+73% from 2004-2008, +<?=$performanceobj->TotAvgReturn?>% return since March 2009</b> and was <b>+39.47%</b> in 2010.*
			<?=getSmithAddtoCartbtnsTrial('OptionSmith', $trialText , 'subscription','subOptionSmith','Landed');?>
			</div>
		</div>
		<div class="smithSubscription">
			<div class="smithSubscriptionContainer">
				<div class="smithSubscriptionGreen">Watch the video below to learn more</div>
				<div class="smithSubscriptionContent">
                    <script type="text/javascript" src="<?=$HTPFX.$HTHOST?>/mvtv/flowplayer/flowplayer-3.2.0.min.js"></script>
                    <a href="<?=$HTPFX.$HTHOST;?>/optionsmith/Video/OpSmithFinal.flv"
                    style="display:block;width:300px;height:155px;"
                    id="player"><img src="<?=$IMG_SERVER?>/images/optionsmith/opstill.jpg" alt="OptionSmith Video" />
                    </a>
                    <script language="JavaScript">
                    flowplayer("player", "<?=$HTPFX.$HTHOST?>/mvtv/flowplayer/flowplayer-3.1.5.swf");
                    </script>
				</div>
				<!--<div class="smithSubscriptionBottom"><img src="<?=$IMG_SERVER?>/images/optionsmith/smith_box_bttm.jpg" /></div>-->
			</div>
			<div class="smithSubscriptionMiddle">
				<div class="smithSubscriptionGreen">Monthly Subscription Price</div>
				<div class="priceImage"><?=getSmithAddtoCartbtnsMonthly('OptionSmith','smith39_freetrial.jpg', 'subscription','subOptionSmith','Landed');?></div>
				<div class="cartImage"><?=getSmithAddtoCartbtnsMonthly('Optionsmith','smith_addtocart.jpg', 'subscription','subOptionSmith','Landed');?></div>
			</div>
			<div class="smithSubscriptionContainer">
				<div class="smithSubscriptionGreen">Annual Subscription Price</div>
				<div class="priceImage"><?=getSmithAddtoCartbtnAnnual('Optionsmith','smith349_signup.jpg', 'subscription','subOptionSmith','Landed');?></div>
				<div class="cartImage"><?=getSmithAddtoCartbtnAnnual('Optionsmith','smith_addtocart.jpg', 'subscription','subOptionSmith','Landed');?></div>
			</div>
		</div>
		<div class="smithSubscription">
			<div class="smithFeatures">
				<div class="smithFeaturesImage"><img src="<?$IMG_SERVER?>/images/optionsmith/smith_bigbox_top_940.png" /></div>
				<div class="smithFeaturesContent">
				<h1>Here are some of the fantastic features of Optionsmith:</h1>
				<div class="featuresContent">
				<ul>
					<li>Get email alerts every time Steve makes a trade</li>
					<li>Detailed analysis and rationale for each trade</li>
					<li>Trade management: (entries/exits/stops)</li>
					<li>Weekly review of all positions and a look at the week ahead</li></ul>
				</div>
				<div class="featuresContent">
				<ul>
					<li>Flexibility to follow all of Steve's trades or just the ones that meet your personal risk tolerance and guidelines</li>
					<li>The best strategies to use in volatile markets</li>
					<li>Access to the OptionSmith portfolio holdings, closed positions and performance.</li>
				</div>
				</ul>
				</div>
				<div class="smithFeaturesImage"><img src="<?$IMG_SERVER?>/images/optionsmith/smith_bigbox_bttm_940.png" /></div>
			</div>
		</div>
		<div class="smith14Day"><?=getSmithAddtoCartbtnsMonthly('OptionSmith','smith_bottom_adsignup.jpg', 'subscription','subOptionSmith','Landed');?></div>
		<div class="smithoffers">Subscribe to any other Minyanville product on an annual basis and get 20% off that product and OptionSmith. Subscribe to three Minyanville products at the annual rate and get 25% off all products.</div>
		<div class="smithStarNotes">
		* Results as of <?=date('Y');?> year end.  Past performance may not be indicative of future results. <br>
** One winner will be selected randomly every month for a 12 month period, and announced on Minyanville.com. Standard contest rules apply.
		</div>
	</div>
<? }


?>
