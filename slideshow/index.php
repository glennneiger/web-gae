<?
global $D_R,$IMG_SERVER;
/* Operative Configuration *****/
$zone_name="homepage";
$tile468x60=1;
/* End Operative Configuration *****/
include($D_R."/layout/dbconnect.php");
include($D_R."/lib/layout_functions.php");

//modules are in associative array - key (id)  => value[name]
//get slide information

if (($_GET['a'] != "") && (is_numeric($_GET['a']))) {
	$slideid = $_GET['a'];

	if($_GET['slide_no'] =="")
	 {
     $slide_no = 1;
	 }
	 elseif(($_GET['slide_no'] !=="") && (is_numeric($_GET['slide_no'])))
	 {
	  $slide_no = $_GET['slide_no'];
	 }

	$slide = getSlideShow($slideid,$slide_no);

	if ($slide != 0) {
		$slideSet = true;

		if (!$USER->isAuthed) {
			$loggedin = "no";
		} else {
			$loggedin = "yes";
		}

	} else {
		 $slideSet = false;
		/*again redirect to slide1*/
		  $slideshowid=slideshow_exists($slideid);
		  if (($_GET['a'] != "") && (is_numeric($_GET['a'])) && $slideshowid)
		   {
		   ?>
			<script>
			a=("<?php echo $slideid; ?>");
			slide_no=1;
			document.location.href =  "?a=" + a + "&slide_no=" + slide_no;
			</script>
		 <?
		  }

	}
}


//some old images will point to gazette/newsviews redirect this to slideshow/index.php
$slide['body'] = str_replace("gazette/newsviews/?id","slideshow/index.php?a",$slide['body']);

$hasNext=($slide['total_slides']!=$slide_no)?0:1;
$hasPrev=($slide_no==1)?1:0;
?>



		<!-- begin news area -->

 <!-- Including style sheet slideshow.css -->
<link rel="stylesheet" href="slideshow.css" type="text/css" />



		<!-- New main Table added  -->
<body onLoad="moveSlideshow(120000);">



<!-- Main Table  -->

<table id="main" align="center">
   <tr>
       <td>
		    <table id="home-news" cellpadding="0" cellspacing="0" >
		     <tr>
					<td><img src="<?=$IMG_SERVER?>/images/lc.gif" width="10" alt="" /></td>
					<td class="top"><img src="<?= $pfx; ?>/images/10x1.gif" /></td>
					<td><img src="<?=$IMG_SERVER?>/images/cr.gif" width="13" alt="" /></td>
				</tr>

				<tr>
					<td class="left-border"></td>
		<td>
               		    <div id="home-news-main">
		    <div id="home-news-content">

			  <table id="news" cellpadding="0" cellspacing="0" width="99%" align="center">
			  <tr>

			  <!-- left column-->
			                            <!-- <td class="main-content" style="padding:0px; vertical-align:top;">-->
			                            <td class="main-content">
				<div id="left-content">
                                                 <!-- Table for logo + adv-->
		                                         <table width="100%" cellpadding="0" cellspacing="0">
		                                             <tr>
		                                                 <td align="center" >
													                                  <!-- Space for Google adv-->
				                               <table cellpadding="5" cellspacing="5" align="center" width="100%">
				<tr>
				<td class="vertical">  <img src="images/slide_mvil_logo.gif">  </td>
				                                                          <td class="adv">
								  <? show_ads_operative($zone_name,$tile468x60,$ADS_SIZE['FullBanner']);?>
				                                                            </td>
				                                                      </tr>
		                                                         </table> <!-- adv table ends -->

				</td>
				</tr>
                                                 </table> <!--logo + adv table ends -->


					                             <!-- Div for Print Slide-->
					                             <div id="articleOptions">
				                                     <p><a href="javascript:printSlide(<?= $slideid; ?>,<?= $slide_no; ?>);" target="_self">print slide show</a></p>
					</div>

						<!--spacer for fixed column width; do not delete -->
						<img src="/images/spacer.gif" width="186" height="1" alt="" />
					                          </div>   <!-- left-content div ends -->


                                         <!-- Div for Slide Show -->
                          <div>
                                             <table id="slidemain" cellspacing="0" cellpadding="0" width="100%" border="1">
						       <tr>
							                         <td class="bgcol">

									     <table width="100%" border="0" cellspacing="0" cellpadding="0">
									                         <tr>
									                            <td>
																<? if($slide){?> <!--table visisble only when qry returns result-->
									                                    <!-- Table for Logo and Article heading -->

									                                        <table width="100%" cellspacing="0" cellpadding="0">
									                                    <tr>
									                                        <td class="logo"><img src="images/slide_m_logo.gif" width="136" height="71" /></td>
									                                        <td class="bgimg">
									   				                             <!-- space for article heading --->

									   					 	                                <?=$slide['title'] ?>

									   				                        </td>
									                                    </tr>
									                               </table>
									                               <!-- Table for Logo and Article heading  ends here-->
									                           </td>
									                        </tr>


									                        <tr>
									                            <td>
									                                <!-- Table for Slide Number, Title and Buttons -->
									                                 <table width="100%" border="0" cellspacing="0px" cellpadding="5px" align="center">
									                                     <tr>


									   				                        <td  class="slidetitle" >
									   				                             <!-- Space for Slide Title-->

									   					                                   <?=$slide['slide_title'] ?>



							</td>


									                                               <!-- back and next Buttons-->
			         <? if(!$hasPrev)//to show button till slideshow reaches firstslide
					  {?>
					  <td  class="btn">
					<input type="image" id="btnprev" src="images/slide_back.gif" width="63" height="19" hspace="5"
					vspace="2" onClick="prevslide();"/>
                       </td>
					<? } ?>
					<? if(!$hasNext)//to show button till the slideshow reaches last slide
					 {	?>
					 <td class="btn">
			      <input type="image" id="btnnext" src="images/slide_next.gif" width="63" height="19" hspace="2" vspace="2" onClick="nextslide();"/>
				     </td>
				    <? }?>

									                                   </tr>
									                                </table>
																	<? } ?>  <!--if ends to hide tables only if qry return result-->
																	<!--table for buttons ends here-->
									                            </td>
									                       </tr>

									                       <tr>
									                          <td>
									                              <!-- Table to display Slide Content and Adv-->
									                              <table width="100%" border="0" cellspacing="10" cellpadding="10">
									                                 <tr>
									                                    <td>
									                                       <!-- Space for Slide Content-->
																		  <div class="articleBody">
																		<p>
																		<?= $slide['body']?>
                                                                        </p>
																		</div>
									                                    </td>
							</tr>
								                                 </table>
									                         </td>
									                       </tr>
									               </table>
							                </td>
						               	</tr>
						         </table> <!-- New Code for  Slide Show ends here -->



									                                   </td>
									                                 </tr>
									                              </table>

   </div> <!-- home-news-content div ends -->
 </div> <!-- home-news-main div ends -->
				</td>

		      <td class="right-border">&nbsp;</td>
		   </tr>

		<tr height="10">
		<td><img src="images/lr_corner1.gif" width="10" alt="" /></td>
		<td class="bottom"><img src="<?=$pfx; ?>/images/10x1.gif" /></td>
		<td><img src="images/rb_corner1.gif" width="13" alt="" /></td>
</tr> <!-- closed tr here, as it was missing -->
		</table>
		<!-- end news area -->
</td> </tr> </table>

<!-- code for google analytics-->
<script src="/_externalLinks.js" language="JavaScript" type="text/javascript"></script>
</body>
<script>
     myCancel='';

	 function nextslide(){
	    var a= <?php echo $slideid; ?>;
		var i=1;
		var total_slides = <?php echo $slide[total_slides]; ?>;
		var slide_no= <?php echo $slide_no; ?>;
		if (slide_no<total_slides)
		 {
		  slide_no=++slide_no;
		  document.location.href =  "?a=" + a + "&slide_no=" + slide_no;
		 }

	   }

	  function prevslide(){
	        var a= <?php echo $slideid; ?>;
			var i=1;
			var total_slides = <?php echo $slide[total_slides]; ?>;

			var slide_no= <?php echo $slide_no; ?>;
			var diff = total_slides-slide_no;

			if ((diff>1) ||(diff==1) || (diff==0))
				{
			 	 	slide_no=--slide_no;
			 		document.location.href =  "?a=" + a + "&slide_no=" + slide_no;
			 	}
	   }

	  function printSlide(slideid,slideno){

			window.open("../slideshow/print.php?a="+slideid + "&slide_no=" +slideno,"print",
			"width=600,height=550,resizable=yes,toolbar=no,scrollbars=yes");
      }

      function moveSlides(){
	      var a= <?php echo $slideid; ?>;
		  var slide_no= <?php echo $slide_no; ?>;
	      slide_no=++slide_no;
		  document.location.href = "?a=" + a + "&slide_no=" + slide_no;

	   }

      function moveSlideshow(speed){

	       var hasnext=("<?php echo $hasNext; ?>");
		   var totalslides=<?php echo $slide['total_slides']; ?>;

	       if(hasnext==0 && totalslides!=''){
           		myCancel = window.setTimeout('moveSlides()',speed);
		   } else {
		     window.clearTimeout(myCancel); <!--function is nt called after the last slide is called-->
		   }

      }

	  </script>