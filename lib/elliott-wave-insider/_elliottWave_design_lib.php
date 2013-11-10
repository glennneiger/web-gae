<?php
class elliottWaveDesign{
	function ewiHeader($sectionName){
		global $HTPFX,$HTHOST; ?>
		<div class="ewi_logo"></div>
		<div class="clr"></div>
		<div id="ewi_header">
	        <ul>
	        	<? if($sectionName=="Home"){ ?>
	            	<li class="active"><a href="<?=$HTPFX.$HTHOST?>/ewi/">Home</a></li>
	            <? }else{ ?>
	            	<li><a href="<?=$HTPFX.$HTHOST?>/ewi/">Home</a></li>
	            <? }

	            if($sectionName=="Analyst Bios"){ ?>
	            	<li class="active"><a href="<?=$HTPFX.$HTHOST?>/ewi/analysts/">Analyst Bios</a></li>
	            <? }else{ ?>
	            	<li><a href="<?=$HTPFX.$HTHOST?>/ewi/analysts/">Analyst Bios</a></li>
	            <? }

	            if($sectionName=="Extras"){ ?>
	            	<li class="active"><a href="<?=$HTPFX.$HTHOST?>/ewi/extras">Extras</a></li>
	            <? }else{ ?>
	            	<li><a href="<?=$HTPFX.$HTHOST?>/ewi/extras">Extras</a></li>
	            <? } ?>
	        </ul>
        	<div class="ewi_emailQust">Email your questions <a href="mailto:ewi@minyanville.com">ewi@minyanville.com</a></div>
    	</div>
<? 	}

	function ewiRightCol(){
		global $IMG_SERVER, $objElliottData, $HTPFX, $HTHOST, $elliotAnalystGroup, $elliotLaunchYear, $elliotLaunchMonth;
		$categoryList = $objElliottData->getAllElliottCategory();
		$analystList = $objElliottData->getAllAnalyst();
		$curMonth = date('n');
		$curYear = date('Y');
		?>
			<div class="ewi_rght_panel">
				<div class="ewi_box_rght hr">
	            	<h2>Categories</h2>
	                <div class="clr"></div>
	                <ul>
						<? foreach($categoryList as $key=>$val){ ?>
								<li class="ewi_rightCol_list"><a href="<?=$HTPFX.$HTHOST?>/ewi/category/<?=$val['id']?>"><?=$val['category_name']." (".$val['alertCount'].")"?></a></li><br/>
						<? } ?>
	                </ul>
            	</div>
            	<div class="ewi_box_rght hr">
	            	<h2>Analysts</h2>
	                <div class="clr"></div>
	                <ul>
						<? foreach($analystList as $key=>$val){
							$biosName = str_replace(" ","-",strtolower($val['name']));?>
								<li class="ewi_rightCol_list"><a href="<?=$HTPFX.$HTHOST?>/ewi/analyst/<?=$val['id']?>"><?=$val['name']." (".$val['alertCount'].")"?></a></li><br/>
						<? } ?>
	                </ul>
            	</div>
				<div class="ewi_box_rght hr">
	            	<h2>Search</h2>
	                <div class="clr"></div>
	                <form name="frmElliottSearch" id="frmElliottSearch" action="<?=$HTPFX.$HTHOST?>/ewi/search.htm" method="get">
	            		<input type="text" name="q" value="" id="q" onmouseover="enableclick();return true;" onblur="disableclick();return false;" />
	            		<input type="button" value="" name="" onclick="javascript:frmElliottSearch.submit();" />
	            	</form>
            	</div>
            	<div class="ewi_box_rght">
	            	<h2>Archive</h2>
	                <div class="clr"></div>
	                <ul>
						<? for($year=$elliotLaunchYear;$year<=$curYear;$year++){
							if($year==$elliotLaunchYear){
								$startMonth=$elliotLaunchMonth;
							}else{
								$startMonth=0;
							}
							for($month=$curMonth;$month>=$startMonth;$month--){
								$monthName= date('F',time(0,0,0,$month)); ?>
								<li class="ewi_rightCol_list"><a href="<?=$HTPFX.$HTHOST?>/ewi/mo/<?=$month?>/yr/<?=$year?>"><?=$monthName.' '.$year;?></a></li><br>
							<? }
						} ?>
	                </ul>
            	</div>
			</div>
	<? }

	function displayElliottWaveBio($contrib_id)
	{
		global $objElliottData,$IMG_SERVER,$HTPFX,$HTHOST;
		$bio = $objElliottData->getAnalystBio($contrib_id);
		if(empty($bio['bio_image'])){
			$image =$IMG_SERVER."/images/elliott-wave-insider/bio_image.png";
		}else{
			$image=$IMG_SERVER.$bio['bio_image'];
		}
		?>
		<div class="ewi_lftPanel">
			<div class="ewi_alertDetailBox">
				<h1 id="ewi_backTop" class="ewi_about">About <?=$bio['name']?></h1>
				<p><span class="ewi_bioImage"><img src="<?=$image?>"></span><?=$bio['description']."<br>".$bio['disclaimer']."<br>".$bio['editor_note'];?></p>
				<div class="clr"></div>
				<div class="ewi_alertBottom">
					<div class="ewi_goTop" onclick="javascript:backToTop('ewi_backTop');">Back to Top</div>
				</div>
			</div>
			<script>
				showTop();
			</script>
		</div>
		<?
	}

	function ewiLeftCol($sectionName,$offset,$p){
		global $objElliottData,$HTPFX,$HTHOST;
		$getAllAlerts = $objElliottData->getAllAlerts($sectionName,$offset);
		?>
		<div class="ewi_lftPanel">
			<? if(!empty($getAllAlerts))
			   {
					foreach($getAllAlerts as $key=>$val){ ?>
					<div class="ewi_alertBox">
						<h1><a href="<?=$HTPFX.$HTHOST.$val['url'];?>"><?=$val['title'];?></a></h1>
						<h3><?=date('F d, Y',strtotime($val['publish_date']));?></h3>
						<?php $body = substr(strip_tags($val['body']),0,322);
		                            $body=$body." ..."; ?>
						<div id="ewi_alert_body"><?=$body;?></div>
						<div class="clr"></div>
						<a href="<?=$HTPFX.$HTHOST.$val['url'];?>">
						<div class="ewiButton">Read More</div></a>
					</div>
				<? }
					$this->displayEwiPagination($sectionName,$p);
			   }else{
					echo '<div class="no_post"> No post has been posted in this Category</div>';
				}
		?>
		</div>
	<? }

	function ewiSearch($search,$offset,$searchType){
		global $objElliottData,$HTPFX,$HTHOST;
		$numrows = $objElliottData->getEwiSearchCount($search,$searchType);
		$getAllAlerts = $objElliottData->getEwiSearch($search,$offset,$searchType);
		?>
		<div class="ewi_lftPanel">
		<? if(!empty($getAllAlerts)){
				foreach($getAllAlerts as $key=>$val){ ?>
					<div class="ewi_alertBox">
						<h1><a href="<?=$HTPFX.$HTHOST.$val['url'];?>"><?=$val['title'];?></a></h1>
						<h3><?=date('F d, Y',strtotime($val['publish_date']));?></h3>
						<?php $body = substr(strip_tags($val['body']),0,322);
		                            $body=$body." ..."; ?>
						<div id="ewi_alert_body" ><?=$body;?></div>
						<div class="clr"></div>
						<a href="<?=$HTPFX.$HTHOST.$val['url'];?>"><div class="ewiButton">Read More</div></a>
					</div>
			<? 	}
				$p=$_GET['p'];
				$this->showEwiSearchPagination($numrows,$p,$searchType,$search);
			}
			else {
			?>
				<div class="no_result"> No Result Found</div>
			<?php
			}
			 ?>
		</div>
	<? }

	 function showEwiSearchPagination($numrows,$p,$searchType,$wordToSearch){
		global $HTPFX,$HTHOST,$elliottPostLimit;
		$contentcount = $elliottPostLimit;
		if($searchType=='text'){
			$url= $HTPFX.$HTHOST."/ewi/search.htm?q=".$wordToSearch;
	 	}elseif($searchType=='category'){
			$url= $HTPFX.$HTHOST."/ewi/category/".$wordToSearch;
		}elseif($searchType=='analyst'){
			$url= $HTPFX.$HTHOST."/ewi/analyst/".$wordToSearch;
		}elseif($searchType=='archive'){
			$archiveDet = explode("/",$wordToSearch);
			$url= $HTPFX.$HTHOST."/ewi/mo/".$archiveDet['1']."/yr/".$archiveDet['0'];
	 	}
		if($numrows>$contentcount) {
			$totalRows = $countnum=ceil(($numrows/$contentcount)); ?>
			<div class="ewi_pagination">
				<? if(!$p=="0"){
						$j=$p-1;
						if($j<1){ ?>
							<a href="<?=$url?>"><div class="ewiPageButton prv_button">&laquo; Previous Page</div></a>
						<? }else{
								if($searchType=='text'){ ?>
									<a target="_parent" href="<?=$url?>&p=<?=$j?>"><div class="ewiPageButton prv_button">&laquo; Previous Page</div></a>
							<? }else { ?>
									<a target="_parent" href="<?=$url?>/p/<?=$j?>"><div class="ewiPageButton prv_button">&laquo; Previous Page</div></a>
							<? }
				  		}
					}
					$p=$p+1;
					if($numrows>(($p)*$contentcount)){
				 		if($searchType=='text'){ ?>
							<a target="_parent" href="<?=$url?>&p=<?=$p?>"><div class="ewiPageButton nxt_button">Next Page &raquo;</div></a>
					<? 	}else { ?>
							<a target="_parent" href="<?=$url?>/p/<?=$p?>"><div class="ewiPageButton nxt_button">Next Page &raquo;</div></a>
					<?	}
				    }
			?>
			</div>
	<?  }
	}

	function displayEwiPagination($secName,$p){
		global $objElliottData,$elliottPostLimit;
		if($secName=="Analyst Bios"){
			$numPost=$objElliottData->getEwiAnalystCount();
		}else{
			$numPost=$objElliottData->getEwiPostCount($secName);
		}
		?>
		<div>
			<? 	$contentCount=$elliottPostLimit;
			$this->showEwiPagination($numPost,$contentCount,$p,$secName); ?>
		</div>
	<?}

	function showEwiPagination($numrows,$contentcount,$p,$secName){
		global $HTPFX,$HTHOST;
		if($secName=="Extras"){
	 		$url= $HTPFX.$HTHOST."/ewi/extras/";
		}elseif($secName=="Analyst Bios"){
			$url= $HTPFX.$HTHOST."/ewi/analysts/";
		}else{
	 		$url= $HTPFX.$HTHOST."/ewi/";
		}

		if($numrows>$contentcount) {
			$totalRows = $countnum=ceil(($numrows/$contentcount)); ?>
		<div class="ewi_pagination">
		<? if(!$p=="0"){
				$j=$p-1;
				if($j<1){?>
					<a href="<?=$url?>"><div class="ewiPageButton prv_button">&laquo; Previous Page</div></a>
				<? } else { ?>
					<a href="<?=$url?>p/<?=$j?>"><div class="ewiPageButton prv_button">&laquo; Previous Page</div></a>
				<? }
			}
			$p=$p+1;
			if($numrows>(($p)*$contentcount)){  ?>
				<a target="_parent" href="<?=$url?>p/<?=$p?>"><div class="ewiPageButton nxt_button">Next Page &raquo;</div></a>
			<? } ?>
		</div>
	<?  }
	}

	function displayElliottAlert($id){
		global $HTPFX,$HTHOST,$objElliottDesign, $objElliottData,$elliottDisclaimer;
		$alertDetails = $objElliottData->getElliottAlertDetails($id);
		$categoryDetails = $objElliottData->getElliottCategoryDetails($alertDetails['category_id']);
		foreach($categoryDetails as $k=>$v)
		{
			$url =$HTPFX.$HTHOST."/ewi/category/".$v['id'];
			$categoryArr[]="<a href='".$url."'><span>".$v['category_name']."</span></a>";
		}
		$category = implode(", ", $categoryArr);
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
		<div class="ewi_lftPanel">
			<div class="ewi_alertDetailBox" >
				<h1 id="ewi_backTop"><?=$alertDetails['title'];?></h1>
				<h3><?=date('F d, Y',strtotime($publish_date));?></h3>
				<div id="ewi_post" ><?=$alertDetails['body'];?></div>
				<div class="clr"></div>
				<div class="ewi_disclaimer"><?=$peterDisclaimer;?></div>
				<div class="ewi_alertBottom">
					<div class="ewi_category">Category: <?=$category?></div>
					<div class="ewi_goTop" id="ewi_goTop" onclick="javascript:backToTop('ewi_backTop');">Back to Top</div>
				</div>
			</div>
			<script>
				showTop();
			</script>
		</div>
	<? }

	function ewiAnalystLeftCol($sectionName,$offset,$p){
		global $objElliottData,$HTPFX,$HTHOST;
		$getAllAnalyst = $objElliottData->getAllAnalystBio($offset);
		//htmlprint_r($getAllAnalyst);exit;
		?>
		<div class="ewi_lftPanel">
			<? if(!empty($getAllAnalyst))
			   {
					foreach($getAllAnalyst as $key=>$val){ ?>
					<div class="ewi_alertBox">
						<h1><a href="<?=$HTPFX.$HTHOST?>/ewi/bio.htm?contrib_id=<?=$val['id']?>"><?=$val['name'];?></a></h1>
						<?php $body = substr(strip_tags($val['description']),0,322);
		                            $body=$body." ..."; ?>
						<div id="ewi_alert_body"><?=$body;?></div>
						<div class="clr"></div>
						<a href="<?=$HTPFX.$HTHOST?>/ewi/bio.htm?contrib_id=<?=$val['id']?>">
						<div class="ewiButton">Read More</div></a>
					</div>
				<? }
					//$this->displayEwiPagination($sectionName,$p);
			   }else{
					echo '<div class="no_post"> No post has been posted in this Category</div>';
				}
		?>
		</div>
	<? }


}