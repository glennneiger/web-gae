<?

header("location:index.htm");
exit;
include("_header.htm");
include("layout/dbconnect.php");
include("layout/layout_functions.php");

$modules = getModules();

$title= "index";
$pageName = 1;


?>

<!-- MVTV Flash teaser --><!-- end flash area -->

<!-- Main Flash movie 
		<div id="home-img">
		 <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="610" height="280" id="SnowShopsxmas" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="../flash/demo/mvMenu7.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><embed src="../flash/demo/mvMenu7.swf" quality="high" bgcolor="#ffffff" width="610" height="280" name="SnowShopsxmas" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
		</div>
		 -->
	
		
<!-- begin news area -->
		<div id="home-img"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="625" height="219">
              <param name="movie" value="mv_mvtv_april07.swf" />
              <param name="quality" value="high" />
              <embed src="mv_mvtv_april07.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="625" height="219"></embed>
            </object></div>
		<table id="home-news" cellpadding="0" cellspacing="0"><tr>
		<td><img src="http://storage.googleapis.com/mvassets/images/lc.gif" width="10" alt="" /></td>
		<td class="top"><img src="<?= $pfx; ?>/images/10x1.gif" /></td>
		<td><img src="http://storage.googleapis.com/mvassets/images/cr.gif" width="13" alt="" /></td>
		</tr>
		<tr>
		<td class="left-border">&nbsp;</td>
		<td id="home-news-main">
		  <div id="home-news-main">
		    <div id="home-news-content">
			  
			  <h1>TODAY IN MINYANVILLE</h1>
			  <h2>News And Views From The Minyanville Financial Network</h2>
			  <p class="simple-separator">&nbsp;</p>
			  <h3><?= displayDate() ?></h3>
			  <p class="double-separator">&nbsp;</p>
			  
				 <table id="news" cellpadding="0" cellspacing="0">
			  <tr>
			  <td> 
			 <table id="all-left-news" cellpadding="0" cellspacing="0"><tr>
				<!-- left column-->
				<td id="left-news">
				
				<?
				$sql = "select articles.id id, title, name author, date, blurb ";
				$sql .= "from articles, contributors ";
				$sql .="where articles.contrib_id = contributors.id and articles.approved='1' order by date desc limit 12";
				displayRecentHeadlines("Recent Headlines",$sql);
				
				?>
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
				 <? call_Column_Module_List(1,"lowerbox",$modules,'show','lowerbox'); ?>
				 </div>
				 
				</td>
				</tr></table>
				<!-- end left-bottom area-->
				
			    </td>
			  
			  <!-- right column -->
			  <td class="right-news" width="201">
			  
			  <div id="right-news">
    			<? call_Column_Module_List(1,3,$modules,'show','right-news'); ?>
				<p class="sections-divider">&nbsp;</p>
				
				<!--spacer for fixed line width; do not delete -->
				<img src="http://storage.googleapis.com/mvassets/images/spacer.gif" width="186" height="1" alt="" /> 
				</div>
				
			  </td>
			  </tr>
			  </table>
			  
			</div>
		  </div> 
		</td>
		<td class="right-border">&nbsp;</td></tr>
		<tr>
		<td><img src="http://storage.googleapis.com/mvassets/images/lr_corner.gif" width="10" alt="" /></td>
		<td class="bottom"><img src="<?=$pfx; ?>/images/10x1.gif" /></td>
		<td><img src="http://storage.googleapis.com/mvassets/images/rb_corner.gif" width="13" alt="" /></td>
		</table>
		<!-- end news area -->
		
<?include("$D_R/_footer.htm");?>