<?php 
class keeneDesign{
	function displayKeeneHeader($categoryName){
		global $HTPFX, $HTHOST, $objKeeneData,$IMG_SERVER; 
		$getMenu = $objKeeneData->getAllKeeneCategory();?>
		<div class="keene_logo"><a href="<?php echo $HTPFX.$HTHOST;?>/keene"><img src="<?php echo $IMG_SERVER;?>/images/keene/keeneHeadLogo.png" title="logo"/></a></div>
		<div class="clr"></div>
		<div id="keene_header">
	        <ul>
	        	<?php  foreach ($getMenu as $key=>$menu){
			    		if(strtolower($categoryName)===strtolower($menu['category_alias'])){?>
				        	<li class="active"><a href="<?php echo $HTPFX.$HTHOST.'/keene/'.$menu['category_alias']?>"><?php echo $menu['category_name'];?></a></li>
			 	<?php  } else { ?>
				        	<li><a href="<?php echo $HTPFX.$HTHOST.'/keene/'.$menu['category_alias']?>"><?php echo $menu['category_name'];?></a></li>
				        <?php } 
			        } ?>
	        </ul>
        	<div class="keene_emailQust">Email your questions <a href="mailto:support@minyanville.com">support@minyanville.com</a></div>
    	</div>
	<?php }
	
	function displayKeeneAlert($id,$alertDetails){
		global $HTPFX,$HTHOST,$objKeeneData, $objKeeneDesign;
		$categoryDetails = $objKeeneData->getKeeneCategoryDetails($alertDetails['category_id']);
		foreach($categoryDetails as $k=>$v)
		{
			$url =$HTPFX.$HTHOST."/keene/".$v['category_alias'];
			$categoryArr[]="<a href='".$url."'><span>".$v['category_name']."</span></a>";
		}
		$category = implode(", ", $categoryArr);
		if($alertDetails['publish_date']=="" || $alertDetails['publish_date']=="0000-00-00 00:00:00"){
			$publish_date=$alertDetails['creation_date'];
		}else{
			$publish_date=$alertDetails['publish_date'];
		}
		?>
		<div class="keene_lftPanel">
			<div class="keene_alertDetailBox" >
				<h1 id="keene_backTop"><?=$alertDetails['title'];?></h1>
				<h3><?=date('F d, Y',strtotime($publish_date));?></h3>
				<div id="keene_post" ><?=$alertDetails['body'];?></div>
				<div class="clr"></div>
				<div class="keene_alertBottom">
					<div class="keene_category">Category: <?=$category?></div>
					<div class="keene_goTop"><a href="#keene_backTop">Back to Top</a></div>
				</div>
			</div>
		</div>
	<? }
	
	function displayKeeneRightCol(){
		global $IMG_SERVER, $objKeeneData, $HTPFX, $HTHOST;
		$categoryList = $objKeeneData->getAllKeeneCategory();
		?>
			<div class="keene_rght_panel">
				<div class="keene_box_rght hr">
	            	<h2>Search</h2>
	                <div class="clr"></div>
	                <form name="frmKeeneSearch" id="frmKeeneSearch" action="<?=$HTPFX.$HTHOST?>/keene/search.htm" method="get">
	            		<input type="text" name="q" value="" id="q" onmouseover="enableclick();return true;" onblur="disableclick();return false;" />
	            		<input type="button" value="" name="" onclick="javascript:frmKeeneSearch.submit();" />
	            	</form>
            	</div>
				<div class="keene_box_rght hr">
	            	<h2>Categories</h2>
	                <div class="clr"></div>
	                <ul>
						<? foreach($categoryList as $key=>$val){ 
							$alertCount = $val['alertCount'];
							if($val['category_alias']=='andrew-keene'){
								$alertCount = '1';
							}?>
								<li class="keene_rightCol_list"><a href="<?=$HTPFX.$HTHOST?>/keene/<?=$val['category_alias']?>"><?=$val['category_name']." (".$alertCount.")"?></a></li><br/>
						<? } ?>
	                </ul>
            	</div>
			</div>
	<?php }
	
	function displayKeeneCatLeft($categoryName,$offset,$p){
		global $objKeeneData,$HTPFX,$HTHOST;
		$getAllAlerts = $objKeeneData->getAllAlerts($categoryName,$offset);
		?>
		<div class="keene_lftPanel">
			<? if(!empty($getAllAlerts)){
					foreach($getAllAlerts as $key=>$val){ ?>
						<div class="keene_alertBox">
							<h1><a href="<?=$HTPFX.$HTHOST.$val['url'];?>"><?=$val['title'];?></a></h1>
							<h3><?=date('F d, Y',strtotime($val['publish_date']));?></h3>
							<?php $body = substr(strip_tags($val['body']),0,322);
		                            $body=$body." ..."; ?>
							<div id="keene_alert_body"><?=$body;?></div>
							<div class="clr"></div>
							<a href="<?=$HTPFX.$HTHOST.$val['url'];?>">
							<div class="keeneButton">Read More</div></a>
						</div>
				<? }
					$this->displayKeenePagination($categoryName,$p);
			   }else{
					echo '<div class="no_post"> There are currently no posts in this category.</div>';
				}
		?>
		</div>
	<? }

	function displayKeeneSearch($search,$offset,$searchType,$p){
		global $objKeeneData,$HTPFX,$HTHOST;
		$numrows = $objKeeneData->getKeeneSearchCount($search,$searchType);
		$getAllAlerts = $objKeeneData->getKeeneSearch($search,$offset,$searchType);
		?>
		<div class="keene_lftPanel">
		<? if(!empty($getAllAlerts)){
				foreach($getAllAlerts as $key=>$val){ ?>
					<div class="keene_alertBox">
						<h1><a href="<?=$HTPFX.$HTHOST.$val['url'];?>"><?=$val['title'];?></a></h1>
						<h3><?=date('F d, Y',strtotime($val['publish_date']));?></h3>
						<?php $body = substr(strip_tags($val['body']),0,322);
		                            $body=$body." ..."; ?>
						<div id="keene_alert_body" ><?=$body;?></div>
						<div class="clr"></div>
						<a href="<?=$HTPFX.$HTHOST.$val['url'];?>"><div class="keeneButton">Read More</div></a>
					</div>
			<? 	}
				$this->showKeeneSearchPagination($numrows,$p,$searchType,$search);
			}
			else {
			?>
				<div class="no_result"> No Result Found</div>
			<?php
			}
			 ?>
		</div>
	<? }

	 function showKeeneSearchPagination($numrows,$p,$searchType,$wordToSearch){
		global $HTPFX,$HTHOST,$keenePostLimit;
		$contentcount = $keenePostLimit; 	
	 	$url= $HTPFX.$HTHOST."/keene/search.htm?q=".$wordToSearch;
		if($numrows>$contentcount) {
			$totalRows = $countnum=ceil(($numrows/$contentcount)); ?>
			<div class="keene_pagination">
				<? if(!$p=="0"){
						$j=$p-1;
						if($j<1){ ?>
							<a href="<?=$url?>"><div class="keenePageButton prv_button">&laquo; Previous Page</div></a>
						<? }else{ ?>
							<a target="_parent" href="<?=$url?>&p=<?=$j?>"><div class="keenePageButton prv_button">&laquo; Previous Page</div></a>
				  		}
					}
					$p=$p+1;
					if($numrows>(($p)*$contentcount)){ ?> 
						<a target="_parent" href="<?=$url?>&p=<?=$p?>"><div class="keenePageButton nxt_button">Next Page &raquo;</div></a>
				   <?php } ?>
			</div>
	<?  }
	}
	 }

	function displayKeenePagination($categoryName,$p){
		global $objKeeneData,$keenePostLimit;
		$numPost = $objKeeneData->getKeenePostCount($categoryName); ?>
		<div>
			<? 	$contentCount=$keenePostLimit;
				$this->showKeenePagination($numPost,$contentCount,$p,$categoryName); ?>
		</div>
	<? }

	function showKeenePagination($numrows,$contentcount,$p,$categoryName){
		global $HTPFX,$HTHOST,$objKeeneData;
		$url= $HTPFX.$HTHOST."/keene/".$categoryName."/";

		if($numrows>$contentcount) {
			$totalRows = $countnum=ceil(($numrows/$contentcount)); ?>
			<div class="keene_pagination">
			<? if(!$p=="0"){
					$j=$p-1;
					if($j<1){?>
						<a href="<?=$url?>"><div class="keenePageButton prv_button">&laquo; Previous Page</div></a>
					<? } else { ?>
						<a href="<?=$url?>p/<?=$j?>"><div class="keenePageButton prv_button">&laquo; Previous Page</div></a>
					<? }
				}
				$p=$p+1;
				if($numrows>(($p)*$contentcount)){  ?>
					<a target="_parent" href="<?=$url?>p/<?=$p?>"><div class="keenePageButton nxt_button">Next Page &raquo;</div></a>
				<? } ?>
			</div>
	<?  }
	}
	
	function displayKeeneBio(){
		global $contributorName,$IMG_SERVER; ?>
		<div class="keene_lftPanel">
			<div class="keene_alertDetailBox">
				<h1 id="keene_backTop" class="keene_about"><?php echo $contributorName;?></h1>
				<div class="keene_bioDesc">
					<span class="keene_bioImage"><img src="<?php echo $IMG_SERVER?>/images/keene/andrewKeene.jpg"></span>
					<div>Andrew Keene was an independent equity options trader on the Chicago Board Options Exchange for 11 years. He spent most of that time as a market maker in over 125 stocks, including Apple, General Electric, Goldman Sachs and Yahoo. From 2006-2009, Andrew was the largest, independent on-the-floor Apple trader in the world. Currently, Andrew is actively trading futures, equity options, currency pairs and commodities.</div><br/><div>Andrew has become one of the CBOE's most recognized faces in the media and financial community, making regular appearances on Bloomberg, BNN, CNBC, Fox Business, Sky Australia and his own show on CBOETV. He is also a regular contributor for Bloomberg Radio, DailyForex.com, Minyanville.com and Jim Cramer's TheStreet.com.</div><br/><div>Andrew recently finished writing his first book, KeeneOnTheMarket: Trade to Win Using Unusual Options Activity, Volatility and Earnings, due to be published by Wiley in June.</div><br><div>Andrew received a B.S. in Finance with a concentration in Accountancy from the University of Illinois.</div><br/><div>He can be contacted at <a href="mailto:Andrew@KeeneOnTheMarket.com" class="bioEmail">Andrew@KeeneOnTheMarket.com</a> or you can follow him on <br>Twitter <span class="bioEmail">@KeeneOnMarket</span>.</div>
				</div>
				<div class="clr"></div>
				<div class="keene_alertBottom">
					<div class="keene_goTop"><a href="#keene_backTop">Back to Top</a></div>
				</div>
			</div>
		</div>
		<?
	}
} //class end
?>