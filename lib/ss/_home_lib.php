<?

function displayRecentAlert($selected_alert) {
	global $aid;
	$transobj=new Qtransaction();
	if($selected_alert)
	$sql = "select id, title, date, body from ss_alerts where type = 'alert' and id = '$selected_alert'";
	else
	$sql = "select id, title, date, body from ss_alerts where type = 'alert' and approved = '1' order by date desc limit 1";


        $results = exec_query($sql,1);
	if(count($results)>0){
	?>
		<table width="98%" border="0" cellspacing="0" cellpadding="0">
		<?
	//while ($row = mysql_fetch_assoc($results)) {
	//foreach($results as $row) {
		$aid = $results[id];
		$formatTime = date("g:i a ", strtotime($results['date']));
		$date=$transobj->formatdate2LocalSCR($results['date']);
		$results[body]=wordwrap($results[body], 70, "\n",true);
	?>
			<tr>
				<td height="20" valign="top" class="smith_title"  style="padding-left:0px;">
				    <?	echo "<div class=\"quint_date\">" .$date.' '.$formatTime ."</div>" . chr(13); ?>
					<div class="smith_title"><?= $results['title'] ?></div>

				</td>
			</tr>
		<tr>
		<td align="left"  colspan="2" class="folio_content">
		<?=$results[body];?>
		</td>
	</tr>
	<tr>
		<td align="left" class="folio_content" colspan="2"><br />Regards<br />
		</td>
	</tr>
	<tr>
		<td align="left" colspan="2" class="folio_content">Steve<br />
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

function recentArticles($loginoption,$contribid){
	global $lang;
	$transobj=new Qtransaction();
	$sql = "select id, title, date, body, contributor, contrib_id from articles where approved = '1' and contrib_id ='$contribid' order by id desc limit 0,6";
	$results = exec_query($sql);
	if($loginoption){?>
		<div class="option_views_header" ><h2><? echo $lang['News and Views']; ?></h2></div>
		<div class="option_recent_top_content"><? echo $lang['Free_articles']; ?></div>
		<div class="option_part_container">
	<?}else{ ?>
		<div class="right_common_head"><h2><h2><? echo $lang['News and Views']; ?></h2></h2></div>
		<div class="recent_option_alert_main">
	<?}?>
	<table width="95%" border="0" cellpadding="0" cellspacing="0">
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
			if($loginoption){
					?>
			<?php if(($key%2)=='0'){?>
				<div class="option_recent_left_block">
				<div class="quint_date"><?=$date.' '.$formatTime?></div>
				<a class="articleLink" href= "<?= makeArticleslink($blogrow['id']);?>"><div class="quint_heading"><?=$blogTitle;?></div></a></div><?php } ?>
			<?php if($key%2) { ?>
			<div class="option_recent_right_block">
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
	$sql = "select id, title, date, body from ss_alerts where type = 'alert' and approved = '1' and id not in ($aid) order by date desc limit 5";
	else
	$sql = "select id, title, date, body from ss_alerts where type = 'alert' and approved = '1' order by date desc limit 1, 5";

	$results = exec_query($sql);
	?>
		<div class="right_common_head"><h2><? echo $lang['Recent_Alerts'];?></h2></div>
		<div class="recent_option_alert_main">
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
	$sql = "select id, title from ss_alerts where type = 'blogs' and approved = '1' order by id desc limit 0, 3";
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

function build_ss_lang($page)
{
 	global $lang;
	if(is_numeric($page))
		$qryLang="select page_term,page_text from ss_lang where pageId=".$page;
	else
		$qryLang="select QL.page_term,page_text from ss_lang QL,layout_pages LP where QL.pageId=LP.id and LP.name='".$page."'";
	$resLang=exec_query($qryLang);
	foreach($resLang as $id=>$value)
	{
		$lang[$value['page_term']]=$value['page_text'];
	}
}

function optiondesc(){
?>

<div class="common_content"  >
Steve Smith is the author of OptionSmith by Steve Smith. OptionSmith contains Mr. Smith's own opinions, and none of the information contained therein constitutes a recommendation by Mr. Smith or Minyanville that any particular security, portfolio of securities, transaction or investment s trategy is suitable for any specific person. Mr. Smith will not advise you personally concerning the nature, potential, value or suitability of any particular security, portfolio of securities, transaction, investment strategy or other matter. All information contained within the OptionSmith product is impersonal and not tailored to the investment needs of any specific person. Do not email Mr. Smith seeking personalized investment advice, which he cannot provide. Mr. Smith's past results are not necessarily indicative of future performance. The performance represented here is for informational purposes only, and should not be construed as an offer or solicitation of an offer to sell or buy any security. Please keep in mind that this Portfolio does not necessarily account for the different risk tolerances, investment objectives, and other criteria used by individual investors when making an investment decision. You are encouraged to conduct your own research and due diligence, and to seek the advice of a qualified securities professional before you make any investment.

</div>
<?
}


function displayOpenPositions($loginoption)
{
	$objPortfolio=new optionPortfolio();
	build_lang('sshome');
	global $lang;
	if(!$loginoption)
	{
		$str = '<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" >
		<tr><td><div class="recent_alert_main"><div class="recent_heading">'.$lang['NotLoggedIN'].' </div></div></td></tr>';
			echo $str;
	}
	else
	{
		$dataOpenPositions=$objPortfolio->getOpenPositions();
		// htmlprint_r($dataOpenPositions);
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
		<td class="">Name/Exp/Strk/C-P</td> <!--company name for stocks-->
		<td>ticker</td>
        <td>current quote</td>
        <td>open date</td>
        <td>l/s</td>
        <td># of contracts</td>
	<td>Cost Basis/SHARE</td>
        <td>current value</td>
        <td>gain/loss $</td>
        <!-- <td>gain/loss%</td> -->
					</tr>
						<tr>
        <td colspan="9" valign="top"><div class="open_position_divider"></div> </td>
        </tr>
	<? 	if(count($dataOpenPositions)>0){
		$flag = false;

	 foreach($dataOpenPositions as $postkey=>$postval){
	 if($flag){
		 echo '<tr class="market_grey_row">';
		 $flag = false;
		 }else{
		   echo '<tr>';
		   $flag = true;
		 }
	 ?>	 	<td><?=$postval['compName'];?>&nbsp;&nbsp;<span style="text-transform:none;"><?=$postval['expirydate'];?></span>&nbsp;&nbsp;<?=$postval['option'];?></td>
	        <td><?=$postval['ticker'];?></td>
			<td >$<?=$postval['currentQuote'];?> </td>
			<td ><?=$postval['openDate'];?> </td>
			<td ><?=$postval['tradeType'];?> </td>
			<td ><?=$postval['contracts'];?> </td>
			<td ><?=$postval['costBasis'];?> </td>
			<? if(substr($postval['currentValue'],0,1)=='-'){
			?>
				<td >-$<?=substr($postval['currentValue'],1,15);?> </td>
			<? }else{ ?>
				<td >$<?=substr($postval['currentValue'],0,15);?> </td>
			<? }if(substr($postval['gainorloss'],0,1)=='-'){
				$gain=substr($postval['gainorloss'],1,15);
			?>
			<td  style="color:#FF0000">-$<?=$gain;?></td>
			<? } else {
			?>
			<td  style="color:#009900">$<?=$postval['gainorloss'];?></td>
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



function displayOpenPositionstrade($loginoption){
	$objPortfolio=new optionPortfolio();
	build_lang('sshome');
	global $lang;
	if(!$loginoption){
	$str = '<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" >
		    <tr><td><div class="recent_alert_main"><div class="recent_heading">'.$lang['NotLoggedIN'].' </div></div></td></tr>';
			echo $str;
	  } else {
		 $dataOpenPositionstrade=$objPortfolio->getOpenPositionsByTrade();
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
        <td class="">Name/Exp/Strk/C-P</td>  <!--company name for stocks-->
		<td>ticker</td>
        <td>current quote</td>
        <td>open date</td>
        <td>l/s</td>
        <td># of shares</td>
        <td>cost basis/SHAER</td>
        <td>current value</td>
        <td>gain/loss $</td>
        <!-- <td>gain/loss%</td> -->
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
	 ?>	 	<td><?=$postval['compName'];?>&nbsp;&nbsp;<span style="text-transform:none;"><?=$postval['expirydate'];?></span>&nbsp;&nbsp;<?=$postval['option'];?></td>
	        <td><?=$postval['ticker'];?></td>
			<td >$<?=$postval['currentQuote'];?> </td>
			<td ><?=$postval['openDate'];?> </td>
			<td ><?=$postval['tradeType'];?> </td>
			<td ><?=$postval['contracts'];?> </td>
			<td ><?=$postval['costBasis'];?> </td>
			<? if(substr($postval['currentValue'],0,1)=='-'){
			?>
				<td >-$<?=substr($postval['currentValue'],1,15);?> </td>
			<? }else{ ?>
				<td >$<?=substr($postval['currentValue'],0,15);?> </td>
			<? }if(substr($postval['gainorloss'],0,1)=='-'){
				$gain=substr($postval['gainorloss'],1,15);
			?>
			<td  style="color:#FF0000">-$<?=$gain;?></td>
			<? } else {
			?>
			<td  style="color:#009900">$<?=$postval['gainorloss'];?></td>
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

function getpageNamess($pagename){
	$qry="select id from layout_pages where name='$pagename'";
	$result=exec_query($qry,1);
	if(isset($result)){
		return $result;
	}
}

?>