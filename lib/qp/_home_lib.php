<?

function displayRecentAlert($selected_alert) {
	global $aid;
	$transobj=new Qtransaction();
	if($selected_alert)
	$sql = "select id, title, date, body from qp_alerts where type = 'alert' and approved = '1' and id = '$selected_alert'";
	else
	$sql = "select id, title, date, body from qp_alerts where type = 'alert' and approved = '1' order by date desc limit 1";
	
        $results = exec_query($sql,1);
	if(count($results)>0){
	?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<?			
	//while ($row = mysql_fetch_assoc($results)) {
	//foreach($results as $row) {
		$aid = $results[id];
		$formatTime = date("g:i a ", strtotime($results['date']));
		$date=$transobj->formatdate2LocalSCR($results['date']);
		$results[body]=wordwrap($results[body], 70, "\n",true);
	?>
			<tr>
				<td height="20" valign="top" class="folio_title"  style="padding-left:0px;">
				    <?	echo "<div class=\"quint_date\">" .$date.' '.$formatTime ."</div>" . chr(13); ?>
					<div class="folio_title"><?= $results['title'] ?></div>
				
				</td>
			</tr>
		<tr>
		<td align="left"  colspan="2" class="folio_content">
		<?=$results[body];?><h6>
		</td>
	</tr>	
	<tr>
		<td>&nbsp;</td>
	</tr>
		</table></td>
	</tr>
	
					<?php

		}else{}
}

function recentArticles($loginquint){
	global $lang;
	$transobj=new Qtransaction();
	$sql = "select id, title, date, body, contributor, contrib_id from articles where approved = '1' and contrib_id ='97' order by id desc limit 0,6";
	$results = exec_query($sql);
	if($loginquint){?>
		<div class="recent_views_header"><h2><? echo $lang['News and Views']; ?></h2></div>
		<div class="recent_top_content"><? echo $lang['Free_articles']; ?></div>		
		<div class="recent_part_container">
	<?}else{ ?>
		<div class="right_common_head"><h2><h2><? echo $lang['News and Views']; ?></h2></h2></div>
		<div class="recent_alert_main">	
	<?}?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
					<?php
		foreach($results as $key=>$blogrow){
		//htmlprint_r($blogrow);
					$blogrow[title]=htmlentities($blogrow[title],ENT_QUOTES);
					$blogTitle=wordwrap($blogrow[title], 30, "\n",true);
						$blogrow[body]=substr($blogrow[body],0,100);
						$blogrow[body]=wordwrap($blogrow[body], 70, "\n",true);
						$body=html_entity_decode(htmlentities($blogrow[body],ENT_QUOTES));
						$formatTime = date("g:i a ", strtotime($blogrow['date']));
						$date=$transobj->formatdate2LocalSCR($blogrow['date']);		
			if($loginquint){	
					?>
			<?php if(($key%2)=='0'){?>
				<div class="recent_left_block">
				<div class="quint_date"><?=$date.' '.$formatTime?></div>
				<a class="articleLink" href= "<?= makeArticleslink($blogrow['id']);?>"><div class="quint_heading"><?=$blogTitle;?></div></a></div><?php } ?>
			<?php if($key%2) { ?>
			<div class="recent_right_block">
				<div class="quint_date"><?=$date.' '.$formatTime?></div>
				<a class="articleLink" href= "<?= makeArticleslink($blogrow['id']);?>"><div class="quint_heading"><?= $blogTitle;?></div></a></div><?php } ?>
					<?php
	}else{ ?>
		
		<a class="articleLink" href= "<?= makeArticleslink($blogrow['id']);?>">
		<div class="recent_heading"><?=$blogTitle;?></div></a>	
		<div class="quint_sub_heading"><?=$date.' '.$formatTime;?></div>			
		<div class="quint_sub_heading">&nbsp;</div>
	
		
	<?}

}  // for each loop ends here ?>
					</td>
				</tr>
</table>

</div>
<?
}



function recentAlerts($selected_alert){
	global $aid, $lang;
	$transobj=new Qtransaction();
	if($selected_alert)
	$sql = "select id, title, date, body from qp_alerts where type = 'alert' and approved = '1' and id not in ($aid) order by date desc limit 5";
	else
	$sql = "select id, title, date, body from qp_alerts where type = 'alert' and approved = '1' order by date desc limit 1, 5";
	$results = exec_query($sql);
	?>
		<div class="right_common_head"><h2><? echo 'Recent_Alerts'	?></h2></div>
		<div class="recent_alert_main">
			<?php
			foreach($results as $row){
				$row[body]=substr($row[body],0,100);
				$row[body]=wordwrap($row[body], 70, "\n",true);
				$body=html_entity_decode(htmlentities($row[body],ENT_QUOTES));
				$formatTime = date("g:i a ", strtotime($row['date']));
			        $date=$transobj->formatdate2LocalSCR($row['date']);	
			?>
				<a class="articleLink" href= <?= makeAlertlink($row['id'],1);?>>
				<div class="recent_heading"><?= $row['title'] ?></div></a>
				<div class="quint_sub_heading"><?=$date.' '.$formatTime;?></div>
				<div class="quint_sub_heading">&nbsp;</div>
       
					<?php
			}
			?>
	</div>
	<?php
}


function freeBlogs(){
	$sql = "select id, title from qp_alerts where type = 'blogs' and approved = '1' order by id desc limit 0, 3";
	$results = exec_query($sql);
	?>
	<tr>
		<td class="FeatureHeaderGrayBgDouble">Latest from News & Views</td>
	</tr>
	<tr>
		<td>
			<table border="0" cellpadding="5" cellspacing="5">
				<tr><br />
					<?php
					//while ($blogrow = mysql_fetch_assoc($results)) {
					foreach($results as $blogrow){
					$blogrow[title]=htmlentities($blogrow[title],ENT_QUOTES);
					$blogTitle=wordwrap($blogrow[title], 30, "\n",true);
					?>
					<td class="NewsArticleTitle">
						
						<a class="articleLink" href= <?= makeAlertlink($blogrow['id'],$blogrow['keyword'],$blogrow['title']);?>><div class="NewsArticleTitle" style="padding-left:10px;"><?= $blogTitle;?></div></a>						

						<span><h5 style="padding-left:10px; padding-bottom:5px;"><?=$blogrow[keyword];?></h5></span>
					</td>
				</tr>
				<?php
					}
}



function displayquintregisterbox($USER)
{
	global $D_R,$_COOKIE,$IMG_SERVER;
	$html='<form method="post" action="/auth-2.htm" name="signinform">';
	if (!$USER->isAuthed) { // display log in form
		$html.='<div class="Loginbox">';
		$html.='<div class="LoginTitle" align="center">Log-in</div>';
		$html.='<input type="text" size="15" maxlength="255" style="text-align:left,padding-top:7px;width:155px;border-color:#718697;" name="email" onFocus="javascript:textboxToEmail(this)" value="email" id="email" />';
		$html.='<input type="text" size="15" name="password" maxlength="255" style="text-align:left;width:155px;border-color:#718697; margin-top:4px;" onFocus="javascript:textboxToPassword(this)" value=" password" id="password" />';
		$html.='<div>';
		//$html.='<input type="image" id="loginbutton" class="LoginButton" src="'.$IMG_SERVER.'/images/button_go.gif" alt="" disabled=true />';
		$html.='<img id="loginbutton" class="LoginButton" src="'.$IMG_SERVER.'/images/button_go.gif" style="cursor:pointer; border:none;" alt="" disabled=true onclick="javascript:validateUserId()">';
		$html.='</div>';
		$html.='<div class="LoginText">';
		$html.='<input type="checkbox" style="border:none;margin-top:2px;" name="setcookie("'.$_COOKIE[autologin].'" checked":"") id="autologinbox">';
		$html.='<p>Remember Me</p>';
		$html.='</div>';

    	$html.='<div class="LoginTextmain" style="width:95%;"><a href="/register/new">Register</a> | <a href="/register/forgotpass.htm">Forgot Password</a></div></div>';
		$html.='</form>';
	}
	else
	{
$html.='<div valign="top" class="ControlPanel" style="padding-right:4px;" >';
		$html.=makecontrolpanel();
		$html.='</form>';
		$html.=' <form method="post" name="referralFrm" action="http://app.peersuasion.com/public/registration.php?q=4f83dc988bba42f42a12374d518fe38d" target="_blank">';
		$html.='<input type="hidden" name="case" value="submit">';
        $html.='<input type="hidden" name="firstname" value="'.$USER->fname.' ">';
        $html.='<input type="hidden" name="lastname" value="'.$USER->lname .'">';
        $html.='<input type="hidden" name="email" value="'.$USER->email.'">';
		$html.='</form>';
      	$html.='</div> ';
	
	}

	echo $html;
}

function build_qp_lang($page)
{
 	global $lang;
	if(is_numeric($page))
		$qryLang="select QL.page_term,page_text from qp_lang QL where pageId=".$page;
	else
		$qryLang="select QL.page_term,page_text from qp_lang QL,layout_pages LP where QL.pageId=LP.id and LP.name='".$page."'";
	$resLang=exec_query($qryLang);
	foreach($resLang as $id=>$value)
	{
		$lang[$value['page_term']]=$value['page_text'];
	}
}

function quintdesc(){
?>

<div class="common_content"  >
   
  	 
				 Quint Tatro is president and founder of Tatro Capital, LLC, an investment management firm, as well as managing member over a multi-strategy investment fund.  The FlexFolio contains Mr. Tatro's own opinions, and none of the information contained therein constitutes a recommendation by Mr. Tatro or Minyanville that any particular security, portfolio of securities, transaction or investment s trategy is suitable for any specific person.  Mr. Tatro will not advise you personally concerning the nature, potential, value or suitability of any particular security, portfolio of securities, transaction, investment strategy or other matter. All information contained within the FlexFolio product is impersonal and not tailored to the investment needs of any specific person. Do not email Mr. Tatro seeking personalized investment advice, which he cannot provide.  Mr. Tatro's past results are not necessarily indicative of future performance.  The performance represented here is for informational purposes only, and should not be construed as an offer or solicitation of an offer to sell or buy any security. Please keep in mind that this Portfolio does not necessarily account for the different risk tolerances, investment objectives, and other criteria used by individual investors when making an investment decision. You are encouraged to conduct your own research and due diligence, and to seek the advice of a qualified securities professional before you make any investment.
			 
</div>	
<?
}
/***************deepika jain********/
function displayOpenPositions($loginquint){
	build_lang('qphome');
	global $lang;

if(!$loginquint){
	$str = '<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" >   
			
		 <tr><td><div class="recent_alert_main"><div class="recent_heading">'.$lang['NotLoggedIN'].' </div></div></td></tr>';
			
			echo $str;
	  } else {	
		
		$dataOpenPositions=getOpenPositions();
		  /**  $dataOpenPositions  = array('0'=>array(			 
		 'companyname'=>'AIRTRAN HLDGS INC-AAI GTR GH ',
		 'currentquote'=>'18 ',			  
			 'creation_date_get'=>'1/10/2009  ',
			 'LS'=>'L',
			 'noofShares'=>'353',
			 'costbasispershare'=>'$333.00',
			 'currentval'=>'6,537.56',
			 'gainloss'=>'111,011.44',
			 'gainloss'=>'94.44',
			 'gainlosspercent'=>'987'
		 
		 ),
		 '1'=>array(			 
		 'companyname'=>'AIRTRAN HLDGS INC-AAI CCD ',
		 'currentquote'=>'28 ',			  
			 'creation_date_get'=>'11/10/2009  ',
			 'LS'=>'L',
			 'noofShares'=>'399',
			 'costbasispershare'=>'$333.00',
			 'currentval'=>'6,537.56',
			 'gainloss'=>'111,011.44',
			 'gainloss'=>'94.44',
			 'gainlosspercent'=>'987'
		 
		 ),
		 '2'=>array(			 
		 'companyname'=>'HLDGS INC-AAI AIRTRAN ',
		 'currentquote'=>'18 ',			  
			 'creation_date_get'=>'1/10/2009  ',
			 'LS'=>'L',
			 'noofShares'=>'353',
			 'costbasispershare'=>'$333.00',
			 'currentval'=>'6,537.56',
			 'gainloss'=>'111,011.44',
			 'gainloss'=>'94.44',
			 'gainlosspercent'=>'987'
		 
		 ));  ***/
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" >    
    <tr class="viewby_heading">
   <form style="margin:0px; padding:0px;" name="form1" id="form1" method="post" action="">
    <td width="50" valign="bottom" >view by:</td>
       <td width="20"> <input type="radio" name="rad" value="1" checked="checked" onclick="javascript:redirectpage('1');" /> </td>
        <td width="50">company</td>
        <td width="20"><input type="radio" name="rad" value="2"  onclick="javascript:redirectpage('2');"/></td>
        <td width="50">Trade</td>
        <td colspan="4" width="800">&nbsp;</td>
               </form>
						</tr>
  
 
     <td colspan="9" valign="top">
    <div class="open_main_container">
    <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
		<tr>
        <td class="">company name</td>
        <td>current quote</td>
        <td>open date</td>
        <td>l/s</td>
        <td># of shares</td>
        <td>cost basis/share</td>
        <td>current value</td>
        <td>gain/loss $</td>
        <td>gain/loss% 
        </td>
					</tr>
						<tr>
        <td colspan="9" valign="top"><div class="open_position_divider"></div> </td>
        </tr>	
	<? 	if(count($dataOpenPositions)>0){	
		$flag = false;
	if(is_array($dataOpenPositions)){
	 foreach($dataOpenPositions as $postkey=>$postval)
	 { 
	 if($flag){
		 echo '<tr class="market_grey_row">';
		 $flag = false;
		 }else{
		   echo '<tr>';
		   $flag = true;
		 }	 
	 ?>	 	<td ><?=$postval['companyname'];?> </td>
			<td >$<?=$postval['currentquote'];?> </td>
			<td ><?=$postval['creation_date_get'];?> </td>
			<td ><?=$postval['LS'];?> </td>
			<td ><?=$postval['noofShares'];?> </td>
			<td ><?=$postval['costbasispershare'];?> </td>
			<td ><?=$postval['currentval'];?> </td>
							<? if(substr($postval['gainloss'],0,1)=='-'){	
								$gain=substr($postval['gainloss'],1,15);
							?>
			<td  style="color:#FF0000">-$<?=$gain;?></td>
			<? } else {
			  
			?>
			<td  style="color:#009900">$<?=$postval['gainloss'];?></td>
							<? } 
							if(substr($postval['gainlosspercent'],0,1)=='-'){	?>
			<td  style="color:#FF0000"><?=$postval['gainlosspercent'];?>%</td>
							<? } else {?>
			<td  style="color:#009900"><?=$postval['gainlosspercent'];?>%</td>
							<? } ?>
						</tr>
<?	}} // for loop end 
}

?>
                  </table>
  </div></td></tr>
					
<?
   }


}



function getOpenPositions()
{
	$memCacheOpenPosition = new memCacheObj();
	$key="qpOpenPosition";
	//$memCacheOpenPosition->deleteKey($key);
	$dataOpenPosition = $memCacheOpenPosition->getKey($key);
	if(!$dataOpenPosition){
	// Start Calculate Open Postion data
	global $transobj,$ytdobj;
	$transobj=new Qtransaction();
	$unitpriceofstockinhand=$transobj->unitpriceofstockinhand();
	$costbasispershareforshortsell=$transobj->costbasispershareforshortsell();
	$qry="select QT.quote_id,QT.transaction_type,ES.companyname,ES.stocksymbol,QT.creation_date,QT.unit_price,QT.quantity,QT.description from qp_transaction QT, ex_stock ES where  QT.quote_id=ES.id and (QT.transaction_type='0' or QT.transaction_type='2') and QT.status='1' group by QT.quote_id,QT.transaction_type order by QT.transaction_type,ES.companyname,QT.creation_date";
	$result=exec_query($qry);
	$stockinhandarr=$transobj->getallbuystocks();
	$stockinhandforshortsell=$transobj->getallshortselltocks();
	$totalcurrentvalue=0;
	foreach($result as $postkey=>$postval){  
		$transtype=$postval['transaction_type'];
		$symbolname=$postval['stocksymbol'];
		$currentquote=0;
		$currentquote= $transobj->getcurrentquote($postval['stocksymbol']);
		$sid=$postval['quote_id'];
		if(($transtype=='0' && $stockinhandarr[$sid]!=0) || ($transtype=='2' && $stockinhandforshortsell[$sid]!=0)){
					$companyname=$postval['companyname'].'-'.$postval['stocksymbol'];
					$currentquote=number_format($currentquote, 2, '.', ',');
					// initial purchase date
					$dd=$postval['creation_date'];
					$creation_date_get=$transobj->formatdate2LocalSCR($dd);
				
					if($postval['transaction_type']==0){
						$LS="L";
					}else{
						$LS="S";
					}
				// no of shares 
				$stockinhand=0;
				if($postval['transaction_type']==0){ 
					$stockinhand=$stockinhandarr[$sid];
				} else {
					$stockinhand='-'.$stockinhandforshortsell[$sid];
				}
				// cost base per share
				$sid=$postval['quote_id'];
				$costbasispershare=0;
				if($postval['transaction_type']==0){  
					$costbasispershare='$'.number_format($unitpriceofstockinhand[$sid], 2, '.', ',');
				} else {  // Cost Basis / Share for shortsell
					$costbasispershare='$'.number_format($costbasispershareforshortsell[$sid], 2, '.', ',');
				}
		
				// current value for buy
				if($postval['transaction_type']==0){ 
					$sid=$postval['quote_id'];
					$currentval=$currentquote * $stockinhandarr[$sid];
					// $totalcurrentvalue=$totalcurrentvalue+$currentval;
					$currentvalstock='$'.number_format($currentval, 2, '.', ',');
				} else {
					$sid=$postval['quote_id'];
					$currentval=$currentquote * $stockinhandforshortsell[$sid];
					$currentvalstock='-($'.number_format($currentval, 2, '.', ',').')';
				} 
				// gain loss
				if($postval['transaction_type']==0){ 
					$currentval=$currentquote * $stockinhandarr[$sid]; 
					$gainloss=$currentval-($unitpriceofstockinhand[$sid]*$stockinhandarr[$sid]);
						$gain_loss=number_format($gainloss, 2, '.', ',');
				} else {
					$sid=$postval['quote_id'];
					$currentval=$currentquote * $stockinhandforshortsell[$sid]; 
					$gainloss=($costbasispershareforshortsell[$sid]*$stockinhandforshortsell[$sid])-$currentval;
					$gain_loss=number_format($gainloss, 2, '.', ',');
				} 
				 //gainlosspercent
				if($postval['transaction_type']==0){ 
					$currentval=$currentquote * $stockinhandarr[$sid]; 
					$gainloss=$currentval-($unitpriceofstockinhand[$sid]*$stockinhandarr[$sid]);
					$gainlosspercent=($gainloss/($unitpriceofstockinhand[$sid]*$stockinhandarr[$sid]))*100;		
					$gainlosspercent=number_format($gainlosspercent, 2, '.', ',');
				} else {
					$currentval=$currentquote * $stockinhandforshortsell[$sid]; 
					$gainloss=($costbasispershareforshortsell[$sid]*$stockinhandforshortsell[$sid])-$currentval;
					$gainlosspercent=($gainloss/$currentval)*100;		
					$gainlosspercent=number_format($gainlosspercent, 2, '.', ',');
				}
		$dataOpenPosition[]=array( "companyname" =>$companyname, "currentquote" =>$currentquote,"creation_date_get"=>$creation_date_get,"LS"=>$LS,"noofShares"=>$stockinhand,"costbasispershare"=>$costbasispershare,"currentval"=>$currentvalstock,"gainloss"=>$gain_loss,"gainlosspercent"=>$gainlosspercent);		
		} // if end

	} // for loop end
// End Calculate Open Postion data	
// cache for 30 minutes
		global $flexFolio_memcache_expire;
		$memCacheOpenPosition->setKey($key,$dataOpenPosition,$flexFolio_memcache_expire);
	}
	return $dataOpenPosition;
}


function displayOpenPositionstrade($loginquint){
	build_lang('qphome');
	global $lang;
	if(!$loginquint){ 
	$str = '<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" >   
			
		 <tr><td><div class="recent_alert_main"><div class="recent_heading">'.$lang['NotLoggedIN'].' </div></div></td></tr>';
			
			echo $str;
	  } else {	
		 $dataOpenPositionstrade=getOpenPositionstrade();
 		/**$dataOpenPositionstrade = array('0'=>array(			 
		 'companyname'=>'AIRTRAN HLDGS INC-AAI ',
		 'currentquote'=>'18 ',			  
			 'creation_date_get'=>'1/10/2009  ',
			 'LS'=>'L',
			 'noofShares'=>'353',
			 'costbasispershare'=>'$333.00',
			 'currentval'=>'6,537.56',
			 'gainloss'=>'111,011.44',
			 'gainloss'=>'94.44',
			 'gainlosspercent'=>'987'
		 
		 ),
		 '1'=>array(			 
		 'companyname'=>'AIRTRAN HLDGS INC-AAI CCD ',
		 'currentquote'=>'28 ',			  
			 'creation_date_get'=>'11/10/2009  ',
			 'LS'=>'L',
			 'noofShares'=>'399',
			 'costbasispershare'=>'$333.00',
			 'currentval'=>'6,537.56',
			 'gainloss'=>'111,011.44',
			 'gainloss'=>'94.44',
			 'gainlosspercent'=>'987'
		 
		 ),
		 '2'=>array(			 
		 'companyname'=>'HLDGS INC-AAI AIRTRAN ',
		 'currentquote'=>'18 ',			  
			 'creation_date_get'=>'1/10/2009  ',
			 'LS'=>'L',
			 'noofShares'=>'353',
			 'costbasispershare'=>'$333.00',
			 'currentval'=>'6,537.56',
			 'gainloss'=>'111,011.44',
			 'gainloss'=>'94.44',
			 'gainlosspercent'=>'987'
		 
		 ));**/
		
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" >    
     <tr class="viewby_heading">
   <form style="margin:0px; padding:0px;" name="form1" id="form1" method="post" action="">
    <td width="50" valign="bottom" >view by:</td>
       <td width="20"> <input type="radio" name="rad" value="1"  onclick="javascript:redirectpage('1');" /> </td>
        <td width="50">company</td>
        <td width="20"><input type="radio" name="rad" value="2" checked="checked" onclick="javascript:redirectpage('2');"/></td>
        <td width="50">Trade</td>
        <td colspan="4" width="800">&nbsp;</td>
      </form>	
  </tr> 
 <td colspan="9" valign="top">
    <div class="open_main_container">
    <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
      <tr>
        <td class="">company name</td>
        <td>current quote</td>
        <td>open date</td>
        <td>l/s</td>
        <td># of shares</td>
        <td>cost basis/share</td>
        <td>current value</td>
        <td>gain/loss $</td>
        <td>gain/loss% 
        </td>
      </tr>
      <tr>
      <td valign="top" colspan="9"><div class="open_position_divider"></div></td>
      </tr>
	<? 	if(count($dataOpenPositionstrade)>0){	
		$flag = false;
	
	 foreach($dataOpenPositionstrade as $postkey=>$postval){ 
	 if($flag){
		 echo '<tr class="market_grey_row">';
		 $flag = false;
		 }else{
		   echo '<tr>';
		   $flag = true;
		 }	 
	 ?>	 	<td ><?=$postval['companyname'];?> </td>
			<td >$<?=$postval['currentquote'];?> </td>
			<td ><?=$postval['creation_date_get'];?> </td>
			<td ><?=$postval['LS'];?> </td>
			<td ><?=$postval['noofShares'];?> </td>
			<td >$<?=$postval['costbasispershare'];?> </td>
			<td ><?=$postval['currentval'];?> </td>
			<? if(substr($postval['gainloss'],0,1)=='-'){
				$gain=substr($postval['gainloss'],1,15);
			?>
			<td  style="color:#FF0000">-$<?=$gain;?></td>
			<? } else {
			  
			?>
			<td  style="color:#009900">$<?=$postval['gainloss'];?></td>
			<? } 
			if(substr($postval['gainlosspercent'],0,1)=='-'){	?>
			<td  style="color:#FF0000"><?=$postval['gainlosspercent'];?>%</td>
			<? } else {?>
			<td  style="color:#009900"><?=$postval['gainlosspercent'];?>%</td>
			<? } ?>
		</tr>
<?	} // for loop end 
}

?>
  </table>
  </div></td></tr>
					
<?
   }


}

function displayOpenPositionstrade_old($loginquint){
	if(!$loginquint){
?>
	    <table border="0" align="left" cellpadding="0" cellspacing="0" width="770px">
		<tr>
			<td  width="8%"></td>
			<td width="92%" colspan="0">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr><td></td></tr>
              <tr><td class="quintsubheding"></td></tr>
              <tr><td height="20" valign="bottom"class="quintheding"></td></tr>
	          <tr><td></td></tr>
			  <tr><td><div align="center" style="color:#990000;"><?php echo 'You are not loggedin or not authorized to view this page.Please register to Quint portfolio product to view this page.
'?></div></td></tr>
        </table>
	<? } else {	
		 //  $dataOpenPositionstrade=getOpenPositionstrade();
		 $dataOpenPositions = array('companyname'=>'MVIL');
?>

	<table border="0" align="left" cellpadding="0" cellspacing="0" width="970px">
		<tr>
			<td  width="8%"><div class="quint"><img src="<?=$IMG_SERVER?>/images/quint_images/quint-photo.gif" /></br>QUINT TATRO</div></td>
			<td valign="top" width="92%" colspan="0">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr><td class="quintsubheding"></td></tr>
                <tr><td height="20" align="left" valign="bottom"class="quintheding">FlexFolio's - Open Positions </td></tr>
		       <tr><td align="left"><br />
		       <form style="margin: 0px;" name="form1" id="form1" method="post" action="">&nbsp;&nbsp;View By:&nbsp;&nbsp;&nbsp;
		       <input type="radio" style="border:0px" name="rad" value="1" onclick="javascript:redirectpage('1');">&nbsp;&nbsp;Company
		       <input type="radio" style="border:0px" name="rad" value="2" checked="checked" onclick="javascript:redirectpage('2');">&nbsp;&nbsp;Trade
               </form>
				</td>
			</tr>
		 </table>
		 </td></tr>
		<tr>
			<td colspan="2"><br />
				<table cellpadding="0" cellspacing="0" border="1" width="100%" style="border-collapse: collapse" bordercolor="#666666">
					<tr bgcolor="#D7D7D7" >
						<td class="quintsubheding">Company name</td>
						<td class="quintsubheding">Current Quote</td>
						<td class="quintsubheding">Open Date </td>
					        <td class="quintsubheding"> L/S </td>
						<td class="quintsubheding"># of Shares</td>
						<td class="quintsubheding">Cost Basis / Share </td>
						<td class="quintsubheding">Current Value </td>
						<td class="quintsubheding">Gain/Loss $</td>
						<td class="quintsubheding">Gain/Loss %</td>
					</tr>
					
					<? foreach($dataOpenPositionstrade as $postkey=>$postval){  ?>
						<tr>
							<td class="quintsubhedingname"><?=$postval['companyname'];?> </td>
							<td class="quintsubhedingname">$<?=$postval['currentquote'];?> </td>
							<td class="quintsubhedingname"><?=$postval['creation_date_get'];?> </td>
							<td class="quintsubhedingname"><?=$postval['LS'];?> </td>
							<td class="quintsubhedingname"><?=$postval['noofShares'];?> </td>
							<td class="quintsubhedingname">$<?=$postval['costbasispershare'];?> </td>
							<td class="quintsubhedingname"><?=$postval['currentval'];?> </td>
							<? if(substr($postval['gainloss'],0,1)=='-'){
								$gain=substr($postval['gainloss'],1,15);
							?>
							<td class="quintsubhedingname" style="color:#FF0000">-$<?=$gain;?></td>
							<? } else {
							  
							?>
							<td class="quintsubhedingname" style="color:#009900">$<?=$postval['gainloss'];?></td>
							<? } 
							if(substr($postval['gainlosspercent'],0,1)=='-'){	?>
							<td class="quintsubhedingname" style="color:#FF0000"><?=$postval['gainlosspercent'];?>%</td>
							<? } else {?>
							<td class="quintsubhedingname" style="color:#009900"><?=$postval['gainlosspercent'];?>%</td>
							<? } ?>
						</tr>
				<?	} // for loop end ?>
                  </table>
					
<?
   }
}


function getOpenPositionstrade(){
  $memCacheOpenPosition = new memCacheObj();
  $key="qpOpenPositiontrade";
 // $memCacheOpenPosition->deleteKey($key);
   $dataOpenPositiontrade = $memCacheOpenPosition->getKey($key);
   if(!$dataOpenPositiontrade){
    global $transobj;
	global $ytdobj;
	$transobj=new Qtransaction();
	$unitpriceofstockinhand=$transobj->tradeunitpriceofstockinhand();
	$stockaftersale=$transobj->stocksales(); 
	$stockinhandarr=$transobj->getsinglebuystocks();
	$qry="select QT.id,QT.quote_id,QT.transaction_type,ES.companyname,ES.stocksymbol,QT.creation_date,QT.unit_price,QT.quantity,QT.description from qp_transaction QT, ex_stock ES where  QT.quote_id=ES.id and (QT.transaction_type='0' or QT.transaction_type='2')  and QT.status='1' order by ES.companyname,QT.creation_date";
	$result=exec_query($qry);
	$totalcurrentvalue=0;		
	foreach($result as $postkey=>$postval){ 	
		$qtyinhand =0;
					$qtyshortsellinhand=0;
					if($postval['transaction_type']==0){ 
						$symbolname=$postval['stocksymbol'];
						$currentquote= $transobj->getcurrentquote($postval['stocksymbol']);
						$autoid=$postval['id'];
						$Stoclsale=0;
						$Stoclsale=$stockaftersale[$autoid];
						$qtyinhand=$stockinhandarr[$autoid]-$Stoclsale;
					} else {
					    $currentquote= $transobj->getcurrentquote($postval['stocksymbol']);
					    $autoid=$postval['id'];
					    $stockshortsellinhandarr=$transobj->geshortsellstocks();
						// htmlprint_r ($stockshortsellinhandarr);
						$qtyshortsellinhand=$stockshortsellinhandarr[$autoid];
						  // stock of short sell inhand
					}
					if($qtyinhand || $qtyshortsellinhand){
						$companyname=$postval['companyname'].'-'.$postval['stocksymbol'];
						$currentquote=number_format($currentquote, 2, '.', ',');
						// open date
						$dd=$postval['creation_date'];
						$creation_date_get=$transobj->formatdate2LocalSCR($dd);

						if($postval['transaction_type']==0){
							$LS="L";
						}else{
							$LS="S";
						}
						if($postval['transaction_type']==0){ 
							$qtyinhand=$qtyinhand;
						} else {  // for short sell
							$qtyinhand='-'.$qtyshortsellinhand;						
						} 
						// Cost Basis / Share
						$sid=$postval['quote_id'];
						$CostBasis_Share=number_format($postval['unit_price'], 2, '.', ',');
				 // current value		
						$sid=$postval['id'];
						if($postval['transaction_type']==0){
							$currentval=$currentquote * $qtyinhand; 
							$totalcurrentvalue=$totalcurrentvalue+$currentval;
							$current_val='$'.number_format($currentval, 2, '.', ',');
						} else { 
						   $currentvalforshortsell=$currentquote*$qtyshortsellinhand;
						   //$totalcurrentvalue=$totalcurrentvalue+$currentvalforshortsell;
							$current_val='-($'.number_format($currentvalforshortsell, 2, '.', ',').')'; 
						 } 
						
				// gainloss
						if($postval['transaction_type']==0){ 
								$currentval=$currentquote * $qtyinhand; 
								$gainloss=$currentval-($postval['unit_price']*$qtyinhand);
								$gain_loss=number_format($gainloss, 2, '.', ',');
						} else { 
								$currentvalforshortsell=$currentquote*$qtyshortsellinhand; 
								$gainloss=($postval['unit_price']*$qtyshortsellinhand)-$currentvalforshortsell;
								$gain_loss=number_format($gainloss, 2, '.', ',');
						} 
					  if($postval['transaction_type']==0){ 	
								$currentval=$currentquote * $qtyinhand; 
								$gainloss=$currentval-($postval['unit_price']*$qtyinhand);											
								$gainlosspercent=($gainloss/($postval['unit_price']*$qtyinhand))*100;		
								$gainlosspercent=number_format($gainlosspercent, 2, '.', ',');
					  } else {
						   $currentvalforshortsell=$currentquote*$qtyshortsellinhand; 
						   $gainloss=($postval['unit_price']*$qtyshortsellinhand)-$currentvalforshortsell;
						   $gainlosspercent=($gainloss/$currentvalforshortsell)*100;		
							$gainlosspercent=number_format($gainlosspercent, 2, '.', ',');
					  }
					  	 $dataOpenPositiontrade[]=array( "companyname" =>$companyname, "currentquote" =>$currentquote,"creation_date_get"=>$creation_date_get,"LS"=>$LS,"noofShares"=>$qtyinhand,"costbasispershare"=>$CostBasis_Share,"currentval"=>$current_val,"gainloss"=>$gain_loss,"gainlosspercent"=>$gainlosspercent);		

	 }
	 

	} // for loop end	
	global $flexFolio_memcache_expire;
	$memCacheOpenPosition->setKey($key,$dataOpenPositiontrade,$flexFolio_memcache_expire);
	}
	return $dataOpenPositiontrade;	
}

function displayOpenPositions_old($loginquint)
{
if(!$loginquint){
?>
	    <table border="0" align="left" cellpadding="0" cellspacing="0" width="770px">
		<tr>
			<td  width="8%"></td>
			<td width="92%" colspan="0">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr><td></td></tr>
              <tr><td class="quintsubheding"></td></tr>
              <tr><td height="20" valign="bottom"class="quintheding"></td></tr>
	          <tr><td></td></tr>
			  <tr><td><div align="center" style="color:#990000;"><?php echo 'You are not loggedin or not authorized to view this page.Please register to Quint portfolio product to view this page.
'?></div></td></tr>
        </table>
	<? } else {	
		 //  $dataOpenPositions=getOpenPositions();
		 $dataOpenPositions = array('companyname'=>'MVIL');
?>
	<table border="0" align="left" cellpadding="0" cellspacing="0" width="970px">
		<tr>
			<td  width="8%">
			<div class="quint"><img src="<?=$IMG_SERVER?>/images/quint_images/quint-photo.gif" /></br>QUINT TATRO</div>
			</td>
				<td width="92%" colspan="0" valign="top">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr><td class="quintsubheding"></td></tr>
						<tr><td height="20" align="left" valign="bottom"class="quintheding">FlexFolio's - Open Positions </td></tr>
						<tr>
							<td align="left"><br />
							   <form style="margin: 0px;" name="form1" id="form1" method="post" action="">&nbsp;&nbsp;View By:&nbsp;&nbsp;&nbsp;
							   <input type="radio" style="border:0px" name="rad" value="1" checked="checked" onclick="javascript:redirectpage('1');">&nbsp;&nbsp;Company
							   <input type="radio" style="border:0px" name="rad" value="2" onclick="javascript:redirectpage('2');">&nbsp;&nbsp;Trade
							   </form>
							</td>
						</tr>
				  </table>

			 </td>
		 </tr>
		<tr>
			<td colspan="2"><br />
				<table cellpadding="0" cellspacing="0" border="1" width="100%" style="border-collapse: collapse" bordercolor="#666666">
					<tr bgcolor="#D7D7D7" >
						<td class="quintsubheding">Company name</td>
						<td class="quintsubheding">Current Quote</td>
						<td class="quintsubheding">Open Date </td>
					        <td class="quintsubheding"> L/S </td>
						<td class="quintsubheding"># of Shares</td>
						<td class="quintsubheding">Cost Basis / Share </td>
						<td class="quintsubheding">Current Value </td>
						<td class="quintsubheding">Gain/Loss $</td>
						<td class="quintsubheding">Gain/Loss %</td>
					</tr>
                    
					<? 
					
					foreach($dataOpenPositions as $postkey=>$postval){
					     ?>
						
							<td class="quintsubhedingname"><?=$postval['companyname'];?> </td>
							<td class="quintsubhedingname">$<?=$postval['currentquote'];?> </td>
							<td class="quintsubhedingname"><?=$postval['creation_date_get'];?> </td>
							<td class="quintsubhedingname"><?=$postval['LS'];?> </td>
							<td class="quintsubhedingname"><?=$postval['noofShares'];?> </td>
							<td class="quintsubhedingname"><?=$postval['costbasispershare'];?> </td>
							<td class="quintsubhedingname"><?=$postval['currentval'];?> </td>
							<? if(substr($postval['gainloss'],0,1)=='-'){	
								$gain=substr($postval['gainloss'],1,15);
							?>
							<td class="quintsubhedingname" style="color:#FF0000">-$<?=$gain;?></td>
							<? } else {?>
							<td class="quintsubhedingname" style="color:#009900">$<?=$postval['gainloss'];?></td>
							<? } 
							if(substr($postval['gainlosspercent'],0,1)=='-'){	?>
							<td class="quintsubhedingname" style="color:#FF0000"><?=$postval['gainlosspercent'];?>%</td>
							<? } else {?>
							<td class="quintsubhedingname" style="color:#009900"><?=$postval['gainlosspercent'];?>%</td>
							<? } ?>
						</tr>
				<?	
				

				$flag = true;} // for loop end ?>
                  </table>
					
<?
   }
}

?>