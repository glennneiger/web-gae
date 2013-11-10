<?php
class peterTchirDesign{
	function peterTchirHeader($categoryName){
		global $HTPFX,$HTHOST; ?>
		<div class="ptc_logo"></div>
		<div class="clr"></div>
		<div id="ptc_header">
	        <ul>
	        	<? if($categoryName==""){ ?>
	            	<li class="active"><a href="<?=$HTPFX.$HTHOST?>/tchir-fixed-income/">All Posts</a></li>
	            <? }else{ ?>
	            	<li><a href="<?=$HTPFX.$HTHOST?>/tchir-fixed-income/">All Posts</a></li>
	            <? }

	            if($categoryName=="Weekly Report"){ ?>
	            	<li class="active"><a href="<?=$HTPFX.$HTHOST?>/tchir-fixed-income/weekly-report">Weekly Report</a></li>
	            <? }else{ ?>
	            	<li><a href="<?=$HTPFX.$HTHOST?>/tchir-fixed-income/weekly-report">Weekly Report</a></li>
	            <? }

	            if($categoryName=="Intra-Week Updates"){ ?>
	            	<li class="active"><a href="<?=$HTPFX.$HTHOST?>/tchir-fixed-income/intra-week-updates">Intra-Week Updates</a></li>
	            <? }else{ ?>
	            	<li><a href="<?=$HTPFX.$HTHOST?>/tchir-fixed-income/intra-week-updates">Intra-Week Updates</a></li>
	            <? } ?>
	        </ul>
        	<div class="ptc_emailQust">Email your questions <a href="mailto:tchir@minyanville.com">tchir@minyanville.com</a></div>
    	</div>
<? }

	function peterTchirRightCol(){
		global $IMG_SERVER,$objPeterData,$pageName,$HTPFX,$HTHOST;?>
		 <div class="rght-panel">
            <div class="box-rght hr">
            	<h2>Search</h2>
                <div class="clr"></div>
                <form name="frmPeterSearch" id="frmPeterSearch" action="<?=$HTPFX.$HTHOST?>/tchir-fixed-income/search.htm" method="get">
            		<input type="text" name="q" value="" id="q" onmouseover="enableclick();return true;" onblur="disableclick();return false;" />
            		<input type="button" value="" name="" onclick="javascript:frmPeterSearch.submit();" />
            	</form>
            </div>
             <? if($pageName=="" || $pageName!="peterTchirFeaturedMap"){?>
            <div class="box-rght hr ">
            	<h2>Heatmap</h2>
            	<? $heatMapImg = $objPeterData->getLatestHeatMap();?>
                <img src="<?=$IMG_SERVER;?>/assets/peter-tchir/<?=$heatMapImg['mapImg']?>" width="280px" height="268px" /><br /><br />
                <span id="heatmapEnlarge" onclick="javascript:showHeatMap();">Click to Enlarge</span>
            </div>
           <?php } ?>
            <? if($pageName=="" || $pageName!="peterTchirBio"){?>
	            <div class="box-rght">
	            	<h2>About Peter Tchir</h2>
	                <div class="clr"></div><p>Peter started TF Market Advisors in 2011 as a platform to trade and provide market information. The trading strategies are macro, but the direction and value decisions are based on insights into the credit markets.</p>
	                <div class="clr"></div>
					<a href="<?=$HTPFX.$HTHOST?>/tchir-fixed-income/bio"><div class="ptcBioButton">Read More</div></a>
	            </div>
			<? } ?>

        </div>
        <?= $this->displayPeterFancyBoxPopUp();
	 }




    function displayPeterTchirHeat(){
    		global $IMG_SERVER,$objPeterData,$pageName,$HTPFX,$HTHOST;
    	 $heatMapImg = $objPeterData->getLatestHeatMap();
    	 //htmlprint_r($heatMapImg);
    	 ?>
    	 <div class="ptc_lftPanel">
			<div class="ptc_alertDetailBox">
				<h1 id="ptc_backTop" class="ptc_about"><?=$heatMapImg['title'];?></h1>
				<p> <img src="<?=$IMG_SERVER;?>/assets/peter-tchir/<?=$heatMapImg['mapImg']?>" width="600px" height="700px" /><br /><br /></p>
				<p> dksfdo sdiojfsdi sidufsd sdiufsd diofu sdiofu sdfiosduf <br /><br /></p>
				<div class="clr"></div>
				<div class="ptc_alertBottom">
					<div class="ptc_goTop" onclick="javascript:backToTop('ptc_backTop');">Back to Top</div>
				</div>
			</div>
		</div>

     <?php
    }
	function peterTchirLeftCol($categoryName,$offset,$p){
		global $objPeterData,$HTPFX,$HTHOST;
		$getAllAlerts = $objPeterData->getAllAlerts($categoryName,$offset);
		?>
		<div class="ptc_lftPanel">
			<? if(!empty($getAllAlerts))
			   {
					foreach($getAllAlerts as $key=>$val){ ?>
					<div class="ptc_alertBox">
						<h1><a href="<?=$HTPFX.$HTHOST.$val['url'];?>"><?=$val['title'];?></a></h1>
						<h3><?=date('F d, Y',strtotime($val['publish_date']));?></h3>
						 <?//echo $val['body'];exit;?>
						 <?  $pPattern = '#(?:<br\s*/?>\s*?){2,}#';
	                        $articleBody = preg_match($pPattern,$val['body'],$matches, PREG_OFFSET_CAPTURE);
	                        if($articleBody!==false){
	                        	$body = substr($val['body'],0,$matches[0][1]);
	                        	$wordCount = count(str_word_count(strip_tags($body),'1'));
		                        if($wordCount<35)
		                        {
		                            $words=explode(" ",$val['body'],36);
		                            unset($words[35]);
		                            $body=implode(" ",$words);
		                            $body=$body." ...";
		                        }
		                        else if($wordCount>100)
		                        {
		                        	$words=explode(" ",$val['body'],101);
		                            unset($words[100]);
		                            $body=implode(" ",$words);
		                             $body=$body." ...";
		                        }
	                       }else{
	                            $words=explode(" ",$val['body'],101);
	                            unset($words[100]);
	                            $body=implode(" ",$words);
	                            $body=$body." ...";
	                      } ?>
						<div id="ptcAlert"><?=$body;?></div>
						<div class="clr"></div>
						<a href="<?=$HTPFX.$HTHOST.$val['url'];?>">
						<div class="ptcButton">Read More</div></a>
					</div>
				<? }
					$this->displayPeterPagination($categoryName,$p);
			   }
				else
				{
					echo '<div class="no_post"> No post has been posted in this Category</div>';
				}
		?>
		</div>
	<? }

	function peterTchirSearch($q,$offset){
		global $objPeterData,$HTPFX,$HTHOST;
		$numrows = $objPeterData->getPeterSearchCount($q);
		$getAllAlerts = $objPeterData->getPeterSearch($q,$offset);
		?>
		<div class="ptc_lftPanel">
			<? if(!empty($getAllAlerts))
			{
				foreach($getAllAlerts as $key=>$val){ ?>
					<div class="ptc_alertBox">
						<h1><a href="<?=$HTPFX.$HTHOST.$val['url'];?>"><?=$val['title'];?></a></h1>
						<h3><?=date('F d, Y',strtotime($val['publish_date']));?></h3>
						<?  $pPattern = '#(?:<br\s*/?>\s*?){2,}#';
	                        $articleBody = preg_match($pPattern,$val['body'],$matches, PREG_OFFSET_CAPTURE);
	                        if($articleBody!==false){
	                        	$body = substr($val['body'],0,$matches[0][1]);
	                        	$wordCount = count(str_word_count(strip_tags($body),'1'));
		                        if($wordCount<35)
		                        {
		                            $words=explode(" ",$val['body'],36);
		                            unset($words[35]);
		                            $body=implode(" ",$words);
		                            $body=$body." ...";
		                        }
		                        else if($wordCount>100)
		                        {
		                        	$words=explode(" ",$val['body'],101);
		                            unset($words[100]);
		                            $body=implode(" ",$words);
		                             $body=$body." ...";
		                        }
	                       }else{
	                            $words=explode(" ",$val['body'],101);
	                            unset($words[100]);
	                            $body=implode(" ",$words);
	                            $body=$body." ...";
	                      } ?>
						<div id="ptcAlert" ><?=$body;?></div>
						<div class="clr"></div>
						<a href="<?=$HTPFX.$HTHOST.$val['url'];?>"><div class="ptcButton">Read More</div></a>
					</div>
			<? }
				$p=$_GET['p'];
				$this->showPeterSearchPagination($numrows,$p);
			}
			else {
			?>
				<div class="no_result"> No Result Found</div>
			<?php
			}
			 ?>
		</div>
	<? }

	 function showPeterSearchPagination($numrows,$p){
                global $HTPFX,$HTHOST,$peterPostLimit;
                $contentcount = $peterPostLimit;
                if($numrows>$contentcount) {
                        $totalRows = $countnum=ceil(($numrows/$contentcount)); ?>
                <div class="ptc_pagination">
                <?
                        if($p=="0" || $p=="")
                        {
                                $p="1";
                        }
                        if($p<$totalRows){
                                $nxtPage = $p+1;
                                $url=$HTPFX.$HTHOST."/tchir-fixed-income/search.htm/p/".$nxtPage."/q/".$_GET['q'];
                                ?>
                                <a target="_parent" href="<?=$url?>"><div class="ptcPageButton nxt_button">Next Page &raquo;</div></a>
                                <?php
                        }
                        if($p<=$totalRows && $p>1)
                        {
                                $prvPage=$p-1;
                                if($prvPage<1){
                                        $url=$HTPFX.$HTHOST."/tchir-fixed-income/search.htm/q/".$_GET['q'];
                                        ?>
                                        <a href="<?=$url?>"><div class="ptcPageButton prv_button">&laquo; Previous Page</div></a>
                                <? } else {
                                        $url=$HTPFX.$HTHOST."/tchir-fixed-income/search.htm/p/".$prvPage."/q/".$_GET['q'];
                                        ?>
                                       <a href="<?=$url?>"><div class="ptcPageButton prv_button">&laquo; Previous Page</div></a>
                                <? }
                        }
                ?>
                </div>
        <?  }
        }


	function displayPeterPagination($catName,$p){
		global $objPeterData,$peterPostLimit;
		$numPost=$objPeterData->getPeterPostCount($catName); ?>
		<div>
			<? 	$contentCount=$peterPostLimit;
			$this->showPeterPagination($numPost,$contentCount,$p,$catName); ?>
		</div>
	<?}

	function showPeterPagination($numrows,$contentcount,$p,$catName){
		global $HTPFX,$HTHOST;
		if($catName=="Weekly Report"){
	 		$url= $HTPFX.$HTHOST."/tchir-fixed-income/weekly-report/";
		}elseif($catName=="Intra-Week Updates"){
	 		$url= $HTPFX.$HTHOST."/tchir-fixed-income/intra-week-updates/";
		}else{
	 		$url= $HTPFX.$HTHOST."/tchir-fixed-income/";
		}

		if($numrows>$contentcount) {
			$totalRows = $countnum=ceil(($numrows/$contentcount)); ?>
		<div class="ptc_pagination">
		<? if(!$p=="0"){
				$j=$p-1;
				if($j<1){?>
					<a href="<?=$url?>"><div class="ptcPageButton prv_button">&laquo; Previous Page</div></a>
				<? } else { ?>
					<a href="<?=$url?>p/<?=$j?>"><div class="ptcPageButton prv_button">&laquo; Previous Page</div></a>
				<? }
			}
			$p=$p+1;
			if($numrows>(($p)*$contentcount)){  ?>
				<a target="_parent" href="<?=$url?>p/<?=$p?>"><div class="ptcPageButton nxt_button">Next Page &raquo;</div></a>
			<? } ?>
		</div>
	<?  }
	}

	function displayPeterAlert($id){
		global $HTPFX,$HTHOST,$objPeterData,$peterDisclaimer;
		$alertDetails = $objPeterData->getAlertDetails($id);
		if($alertDetails['category_id']=="1"){
			$url= $HTPFX.$HTHOST."/tchir-fixed-income/weekly-report/";
			$category = "<a href='".$url."'><span>Weekly Report</span></a>";
		}elseif($alertDetails['category_id']=="2"){
			$url= $HTPFX.$HTHOST."/tchir-fixed-income/intra-week-updates/";
			$category = "<a href='".$url."'><span>Intra-Week Updates</span></a>";
		}elseif($alertDetails['category_id']=="1,2"){
			$url1= $HTPFX.$HTHOST."/tchir-fixed-income/weekly-report/";
			$url2= $HTPFX.$HTHOST."/tchir-fixed-income/intra-week-updates/";
			$category = "<a href='".$url1."'><span>Weekly Report</span></a>,<a href='".$url2."'><span> Intra-Week Updates</span></a>";
		}
		if($alertDetails['publish_date']=="" || $alertDetails['publish_date']=="0000-00-00 00:00:00")
		{
			$publish_date=$alertDetails['creation_date'];
		}
		else
		{
			$publish_date=$alertDetails['publish_date'];
		}
		//htmlprint_r($alertDetails);exit;
		?>
		<div class="ptc_lftPanel">
			<div class="ptc_alertDetailBox" >
				<h1 id="ptc_backTop"><?=$alertDetails['title'];?></h1>
				<h3><?=date('F d, Y',strtotime($publish_date));?></h3>
				<div id="ptcPost" ><?=$alertDetails['body'];?></div>
				<div class="clr"></div>
				<div class="ptc_position"><?=$alertDetails['position'];?></div>
				<div class="ptc_disclaimer"><?=$peterDisclaimer;?></div>
				<div class="ptc_alertBottom">
					<div class="ptc_category">Category: <?=$category?></div>
					<div class="ptc_goTop" id="ptc_goTop" onclick="javascript:backToTop('ptc_backTop');">Back to Top</div>
				</div>
			</div>
			<script>
				showTop();
			</script>
		</div>
	<? }

	function displayPeterTchirBio() {
		global $objPeterData,$IMG_SERVER;
		$bio = $objPeterData->getPeterBio();
		?>
		<div class="ptc_lftPanel">
			<div class="ptc_alertDetailBox">
				<h1 id="ptc_backTop" class="ptc_about">About Peter Tchir</h1>
				<h3>Feburay 26,2013</h3>
				<p><span class="ptc_bioImage"><img src="<?=$IMG_SERVER."/images/peter-tchir/peter_image.jpg"?>"></span><?=$bio;?></p>
				<div class="clr"></div>
				<div class="ptc_alertBottom">
					<div class="ptc_goTop" onclick="javascript:backToTop('ptc_backTop');">Back to Top</div>
				</div>
			</div>
		</div>
	<? }

	function displayPeterFancyBoxPopUp(){
		global $IMG_SERVER,$objPeterData; ?>
		<div><a id="heatMapWindow" href="#inline1"></a></div>
		<div style="display:none;">
			<div id="inline1" class="ptc_fancyBox">
				<div id="ptc_fancyboxContainer">
					<div id="ptc_dataContainer">
						<? $heatMapImg = $objPeterData->getLatestHeatMap();?>
		                <img style="width:580px;height:556px;" src="<?=$IMG_SERVER;?>/assets/peter-tchir/<?=$heatMapImg['mapImg']?>" />
					</div>
				</div>
			</div>
		</div>
	<? }
} //class end
?>