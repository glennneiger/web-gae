<?
global $D_R, $IMG_SERVER;
include("$D_R/_header.htm");
include("$D_R/layout/dbconnect.php");
include("$D_R/lib/layout_functions.php");

$title= "TRADING RADAR";
$pageName = "trading_radar";
$category = "tradingradar";

//modules are in associative array - key (id)  => value[name]
$modules = getModules();
?>
	
		
		<!-- begin news area -->
		<table id="home-news" cellpadding="0" cellspacing="0"><tr>
		<td><img src="<?=$IMG_SERVER?>/images/lc.gif" width="10" alt="" /></td>
		<td class="top"><img src="<?= $pfx; ?>/images/10x1.gif" /></td>
		
		<td><img src="<?=$IMG_SERVER?>/images/cr.gif" width="13" alt="" /></td>
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
				 <img src="<?=$IMG_SERVER?>/images/spacer.gif" width="400" height="1" alt="" /> 
				</td>
				</tr>
				</table>
				<!-- End archives area(bottom) --> 
				 
			  </td>
			  
			  <!-- right content -->
			  <td class="right-news">
			  	<div id="right-content">
					<? 
					$dynamicModuleInfo['category'] = $category;
					call_Column_Module_List($pageName,'column2',$modules,$dynamicModuleInfo,'right-content');
					
					?>		
				</div>
				
				<!--spacer for fixed column width; do not delete -->
				<img src="<?=$IMG_SERVER?>/images/spacer.gif" width="186" border="2" height="1" alt="" /> 
			  </td>
			  </tr>
			  </table>
			  
			</div>
		  </div> 
		  </td>
		<td class="right-border">&nbsp;</td></tr>
		<tr>
		<td><img src="<?=$IMG_SERVER?>/images/lr_corner.gif" width="10" alt="" /></td>
<td class="bottom"><img src="<?=$pfx; ?>/images/10x1.gif" /></td>
		<td><img src="<?=$IMG_SERVER?>/images/rb_corner.gif" width="13" alt="" /></td>
		</table>
		<!-- end news area -->
		
<? include("$D_R/_footer.htm"); ?>