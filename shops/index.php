<?
include("../_header.htm");
include("../layout/dbconnect.php");
include("../lib/layout_functions.php");

$modules = getModules();

$title= "MINYAN MAILBAG";
$pageName = 15; //PAGE NAME 'university' didn't work


?>
	
		
			<!-- begin news area -->
		<table id="fixed-center-area" cellpadding="0" cellspacing="0" border="0" width="630"><!-- <tr>
		<td colspan="3" style="vertical-align:bottom;"><img src="/images/university_header.gif" width="631" alt="" /></td>
		</tr> -->
		<tr>
		<td class="left-border">&nbsp;</td>
		<td width="616">
		  <div id="fixed-center-area-main">
		  	<div style="background: #FFF url(http://storage.googleapis.com/mvassets/images/university_header2.gif) top left no-repeat;">
				   	<div id="university-date">
						<img src="http://storage.googleapis.com/mvassets/images/1x52.gif" alt="" /><br>
						<h3><?= displayDate() ?></h3>					
					</div>
			</div>
			<div id="home-news-content"> <!-- style="background: #FFF url(http://storage.googleapis.com/mvassets/images/university_header_btm.gif) top left no-repeat;" -->
			<div class="space-right">
			  
				 <table id="news" cellpadding="0" cellspacing="0">
			  <tr>
			  <td> 
			 <table id="all-left-news" cellpadding="0" cellspacing="0"><tr>
				<!-- left column-->
				<td id="left-news">
					<div>
					<? call_Column_Module_List($pageName,"column1",$modules,'show','left-news'); ?>
					<p class="grey-sections-divider">&nbsp;</p>
					
				</div>
				</td>
				<!--center column-->
				<td id="center-news" width="50%">
				
				<div id="middle-news" class="indent10">
    				
    				<? call_Column_Module_List($pageName,"column2",$modules,'show','middle-news'); ?>
					<p class="grey-sections-divider">&nbsp;</p>
					
					
				</div>
				</td></tr>
				</table>
				
				<!-- begin left bottom area-->
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
				<td id="archive-news">
				
				<!-- begin advertisment area-->
				<div id="white-area">
				<p class="top-adv">&nbsp;</p>
				   <div id="frame-bg">
				     <img src="http://storage.googleapis.com/mvassets/images/comic_300x250.gif" width="301" height="251" alt="" /><br />
					 <p><strong>Interested in advertising on minyanville.com?</strong></p>
					 <p><a href="#">Click here</a> for rates and informations about the Portlets all need to be divided and Styled according to their own
						reference files (see portlets folder and portlets_overview)</p>
				   </div>
				 <p class="bottom-adv">&nbsp;</p>
				 </div>
				 <!-- end advertisment area-->
				 
				 <div id="lowerbox">
				
				<p class="simple-separator">&nbsp;</p>
				 <? call_Column_Module_List($pageName,"lowerbox",$modules,'show','lowerbox'); ?>
				 </div>
				 <img src="http://storage.googleapis.com/mvassets/images/spacer.gif" width="400" height="1" alt="" /> 
				</td>
				</tr></table>
				<!-- end left-bottom area-->
				
				 </td>
			  
			  <!-- right column -->
			  <td class="right-news" width="201">
			  
			  <div id="right-news">
    			<? call_Column_Module_List($pageName,"column3",$modules,'show','right-news'); ?>
				<p class="sections-divider">&nbsp;</p>
				
				<!--spacer for fixed line width; do not delete -->
				<img src="http://storage.googleapis.com/mvassets/images/spacer.gif" width="186" height="1" alt="" /> 
				</div>
				
			  </td>
			  </tr>
			  </table>
			  
				
			</div>
			</div>
		  </div> 
		</td>
		<td class="right-border">&nbsp;</td></tr>
		<tr>
		<td width="630" class="bottom" colspan="3">&nbsp;</td>
		</table>
		<!-- end news area -->
		
<?include("$D_R/_footer.htm");?>