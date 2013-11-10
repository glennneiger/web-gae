<?
include("../_header.htm");
include("../layout/dbconnect.php");
include("../lib/layout_functions.php");

$title= "TRADING RADAR";
$pageName = "trading_radar";
$category = "tradingradar";

//modules are in associative array - key (id)  => value[name]
$modules = getModules();
?>
	
		
		<!-- begin news area -->
		<table id="home-news" cellpadding="0" cellspacing="0"><tr>
		<td><img src="../images/lc.gif" width="13" alt="" /></td>
		<td class="top">&nbsp;</td>
		<td><img src="../images/cr.gif" width="13" alt="" /></td>
		</tr>
		<tr>
		<td class="left-border">&nbsp;</td>
		<td>
		  <div id="home-news-main">
		    <div id="home-news-content">
			  
			  <h1><?= $title; ?></h1>
			  <p class="simple-separator">&nbsp;</p>
			  <h3><?= displayDate() ?></h3>
			  <p class="double-separator">&nbsp;</p>
			  
			  <table id="news" cellpadding="0" cellspacing="0">
			  <tr>
			  
			  	  <!-- left column-->
			 <td class="main-content" style="padding:0px; vertical-align:top;">
				<div id="left-content">
				<? 
				//call_Column_Module_List($pageName,'column1',$modules,'show','left-content');
				
				//display the most recent article for this category
				// this is a special function for point & go figure, trading radar and retail roundup.
				displayRecentCategoryArticle($category);
				?>
				<p class="clearit">&nbsp;</p>
				</div>
		
		
				<!-- Begin archives area(bottom) --> 
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
				<td id="archive-news">
				<div id="lowerbox">
				
				<p class="simple-separator">&nbsp;</p>
				 <? call_Column_Module_List($pageName,"lowerbox",$modules,'show','lowerbox'); ?>
				 </div>
				</td>
				</tr>
				</table>
				<!-- End archives area(bottom) --> 
				 
			  </td>
			  
			  <!-- right content -->
			  <td class="right-news">
			  	<div id="right-content">
					<? call_Column_Module_List($pageName,'column2',$modules,'show','right-content'); ?>		
				</div>
				
				<!--spacer for fixed column width; do not delete -->
				<img src="../images/spacer.gif" width="186" border="2" height="1" alt="" /> 
			  </td>
			  </tr>
			  </table>
			  
			</div>
		  </div> 
		  </td>
		<td class="right-border">&nbsp;</td></tr>
		<tr>
		<td><img src="../images/lr_corner.gif" width="13" alt="" /></td>
		<td class="bottom">&nbsp;</td>
		<td><img src="../images/rb_corner.gif" width="13" alt="" /></td>
		</table>
		<!-- end news area -->
		
<? include("../_footer.htm"); ?>