<? 
class seanudallDesign{
	
	function displaySeanUdallHeader($categoryName){
	global $productSeanUdallName,$HTPFX,$HTHOST;	
	$classSelected="open_post";
	$classUnSelected="close_post";
	?>
    	<h1><?=$productSeanUdallName;?></h1>
        <div class="tab_area">
            <div class="posttabs">
                <? if($categoryName=="") { ?>
                <div class="<?=$classSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/seanudall/">All Posts</a></div>
                <? } else {?>
                <div class="<?=$classUnSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/seanudall/">All Posts</a></div>
                <? }
				if($categoryName=="Trades & Ideas") {	
				 ?>
                <div class="<?=$classSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/seanudall/tradeideas.htm">Trader and Ideas</a></div>
                <? } else { ?>
                <div class="<?=$classUnSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/seanudall/tradeideas.htm">Trader and Ideas</a></div>
                
                <? } 
				if($categoryName=="Mailbag") {	
				?>
                <div class="<?=$classSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/seanudall/mailbag.htm">MailBag</a></div>
                <? } else { ?>
					<div class="<?=$classUnSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/seanudall/mailbag.htm">MailBag</a></div>
				<? }
				if($categoryName=="Research Tank") {	
				?>
                <div class="<?=$classSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/seanudall/researchtank.htm">Research Tank</a></div>
                <? } else { ?>
                <div class="<?=$classUnSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/seanudall/researchtank.htm">Research Tank</a></div>
                <? }
				if($categoryName=="Positions and Performance") {	
				 ?>
                <div class="<?=$classSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/seanudall/performance.htm">Positions and Performance</a></div>
                <? } else {?>
                 <div class="<?=$classUnSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/seanudall/performance.htm">Positions and Performance</a></div>
                <? } ?>
                
            </div><!--Tabs end -->
            <div class="photo"><img src="<?=$IMG_SERVER;?>/images/seanudall/photo.jpg" alt="" /><br /><span class="phototag">Sean Udall</span></div>
        </div>

	<?
	}


	
	function displayPost($categoryName,$offSet,$seanUdallPostLimit){
	global $HTPFX,$HTHOST, $D_R;
	include_once($D_R."/lib/_content_data_lib.php");
	$objSeanUDallData= new seanudallData("seanudall_posts","");
	$objContent= new Content();
	$offSet=$offSet*$seanUdallPostLimit;
	$getResult=$objSeanUDallData->getSeanUdallPostsData($categoryName,$offSet,$seanUdallPostLimit);
		foreach($getResult as $key=>$value){
		$value['body']=$objContent->getCountWords($value['body'],$count="100");
		$tag=$this->displayPostTags($value['id']);
		if($key=="0"){
		?>
					<div class="first_postarea">
						<div class="posttime"><?=date("M j, Y g:i A",strtotime($value['publish_date']));?></div>
						<div class="postheading"><a class="postheading" href="<?=$HTPFX.$HTHOST.$value['url'];?>"><?=$value['title'];?></a></div>
                        <? if($tag) {?>
							<div class="post_tags">TAGS: <span><?=$tag;?></span></div>
                        <? } ?>
						<div class="postcontainer"><?=$value['body'];?><span class="read_more"><a href="<?=$HTPFX.$HTHOST.$value['url'];?>" target="_self"><?=strtoupper('Read More');?>....</a></span></div>
					</div><!--END of 1st Post -->
         <? } else { ?>           
					<div class="postarea">
						<div class="posttime"><?=date("M j, Y g:i A",strtotime($value['publish_date']));?></div>
						<div class="postheading"><a class="postheading" href="<?=$HTPFX.$HTHOST.$value['url'];?>"><?=$value['title'];?></a></div>
                        <? if($tag) { ?>
							<div class="post_tags">TAGS: <span><?=$tag;?></span></div>
                        <? } ?>
						<div class="postcontainer"><?=$value['body'];?><span class="read_more"><a href="<?=$HTPFX.$HTHOST.$value['url'];?>" target="_self"><?=strtoupper('Read More');?>....</a></span>
						</div>
						<div style="clear:both;"></div>
						
					</div>
			<?
		}	
		}        
	}
		
	function displayRightColumn(){
	$objSeanUDallData= new seanudallData("seanudall_posts","");
	?>
		<div class="content_blnk">
                <div class="rght_heading"><img src="<?=$IMG_SERVER;?>/images/seanudall/mailbag_questions.gif" alt="" /></div>
                <div class="grey_bg">
                    <div class="mailbag_qust">Email your question:<a href="mailto:mailbag@minyanville.com">mailbag@minyanville.com</a></div>
                </div>
            </div><!--MailBag Question End -->
            <div class="content_blnk">
                <div class="rght_heading"><img src="<?=$IMG_SERVER;?>/images/seanudall/search.gif" alt="" /></div>
                <div class="grey_bg">
                    <div class="searchpanel">
					<form name="frmSearch" id='frmSearch' action="<?=$HTPFX.$HTHOST.'/seanudall/search.htm';?>" method="get">
                    <input type="text" class="searchpanel" value="" name="q" id='q'/> <img src="<?=$IMG_SERVER;?>/images/seanudall/bttn_go.jpg" alt="" onclick="javascript:frmSearch.submit();"/>
					</form>
                    </div>
                </div>                    
            </div><!--Search End -->
            <div class="content_blnk">
                <?=$this->displayTickerMentioned();?>
            </div><!--Tickers Mentioned End -->
        </div><!--Rightside End -->						
	<?
	}
	
	
	function displayTickerMentioned(){
	$objSeanUDallData= new seanudallData("seanudall_posts","");
	$getResult=$objSeanUDallData->getTickerMentioned();
	$getResult[]=shuffle($getResult);
	?>
	<div class="rght_heading"><img src="<?=$IMG_SERVER;?>/images/seanudall/tickersmentioned.gif" alt="" /></div>
       <div class="grey_bg">
          <div class="tickers_mentioned">
                       <? foreach($getResult as $value){ 
							if(is_array($value)){
								echo '<a  href="'.$HTPFX.$HTHOST.'/seanudall/tid/'.$value['ticker_id'].'" target="_self">'.strtoupper($value['stocksymbol']).' '.'('.$value['countticker'].')'.'</a><br />';
							} 
						}
						?>
                        
       </div>
    </div>
	<?
	}
	function displaySearchResult($searchStr,$searchType,$offset)
	{
		global $D_R;
		include_once($D_R."/lib/_content_data_lib.php");
		$objContent= new Content();
		global $HTPFX,$HTHOST;
		$objSeanUDallData= new seanudallData("seanudall_posts","");
		if($searchType == 'text')
		{
		$searchResult = $objSeanUDallData->getSeanUdallSearch($searchStr,$searchType,$offset);
		}
		elseif($searchType == 'tag' or $searchType == 'tid')
		{
			$searchResult = $objSeanUDallData->getSeanUdallSearchByTopic($searchStr,$searchType,$offset);
		}
		if($searchResult && count($searchResult)>0)
		{
			foreach($searchResult as $key=>$value){
				//$value['body']=$objContent->getCountWords($value['body'],$count="100");
				$i=0;
				$url			=	  $objSeanUDallData->getPostUrl($value['id']);
				$tag			=	  $this->displayPostTags($value['id']);
				$description	=	  $objContent->getCountWords($value['body'],$count="23");
				if($key=="0"){
			?>
					<div class="first_postarea">
						<div class="posttime"><?=date("M j, Y g:i A",strtotime($value['publish_date']));?></div>
						<div class="postheading"><a class="postheading" href="<?=$HTPFX.$HTHOST.$url;?>"><?=$value['title'];?></a></div>
						 <? if($tag) {?>
							<div class="post_tags">TAGS: <span><?=$tag;?></span></div>
                        <? } ?>
      					<div class="postcontainer"><?=$description;?></div>
					</div><!--END of 1st Post -->
         <? } else { ?>           
					<div class="postarea">
						<div class="posttime"><?=date("M j, Y g:i A",strtotime($value['publish_date']));?></div>
						<div class="postheading"><a class="postheading" href="<?=$HTPFX.$HTHOST.$url;?>"><?=$value['title'];?></a></div>
						 <? if($tag) {?>
							<div class="post_tags">TAGS: <span><?=$tag;?></span></div>
                        <? } ?>
                      	<div class="postcontainer"><?=$description;?></div>
					</div>
					<div style="clear:both;"></div>
			<?
			}	
	   	}
	   }
	   else
		{
			echo "<div class='errormsg'>No Result Found!</div>";
		}
	}
	function displayPostDetail($id)
	{
		global $HTPFX,$HTHOST;
		$objSeanUDallData= new seanudallData("seanudall_posts","");
		$searchResult = $objSeanUDallData->getPostData($id);
		if($searchResult && count($searchResult)>0)
		{
				$tag	=	$this->displayPostTags($id);
				?>
					<div class="first_postarea">
						<div class="posttime"><?=date("M j, Y g:i A",strtotime($searchResult['publish_date']));?></div>
						<div class="postheading"><?=$searchResult['title'];?></div>
						 <? if($tag) {?>
							<div class="post_tags">TAGS: <span><?=$tag;?></span></div>
                        <? } ?>
      					<div class="postcontainer"><?=$searchResult['body'];?></div>
					</div><!--END of 1st Post -->
	   	
		<?php
	   }
	   else
		{
			echo "<div class='errormsg'>No Result Found!</div>";
		}
	}
	function displayPostTags($id)
	{
		$objSeanUDallData= new seanudallData("seanudall_posts","");
		$getTags		=	$objSeanUDallData->getSeanUdallTopics($id);
		$tickerValue	=	$objSeanUDallData->getSeanUdallTicker($id);
			/*if($getTags!='')
			{
					$tagValue   	=   explode(',',$getTags);
			}*/
			$i=0;
			$tag="";
			if(is_array($getTags)){
				foreach($getTags as $row){
	
						if($i==0){
							$tag='<a class="post_tags" href="'.$HTPFX.$HTHOST.'/seanudall/tag'.trim($row['tagurl']).'">'.ucfirst($row['tagname']).'</a>';
						}else{
							$tag.=','.'<a class="post_tags" href="'.$HTPFX.$HTHOST.'/seanudall/tag'.trim($row['tagurl']).'">'.ucfirst($row['tagname']).'</a>';
						}
						$i++;
		
				}
			}
			
			if(is_array($tickerValue)) 
			{
				foreach($tickerValue as $key=>$value)
				{
						if($i==0){
							$tag='<a class="post_tags" href="'.$HTPFX.$HTHOST.'/seanudall/tid/'.trim($value['id']).'">'.strtoupper($value['stocksymbol']).'</a>';
						}else{
							$tag.=','.'<a class="post_tags" href="'.$HTPFX.$HTHOST.'/seanudall/tid/'.trim($value['id']).'">'.strtoupper($value['stocksymbol']).'</a>';
						}
						$i++;
				}
			}
			if($tag!='')
			{
				return $tag;
			}
			else
			{
				return false;
			}
	}
	function displaySeanUdallLanding(){
	
	}
	/*************************************** Performance Page ******************************************/
	function displayPerformance()
	{
		global $HTPFX,$HTHOST;
		$objSeanUDallData= new seanudallData("seanudall_posts","");
		//$getResult=$objSeanUDallData->getSeanUdallPostsData($categoryName,$offSet,$seanUdallPostLimit);
		//$tagValue=array();
		?>
		<div class="performance_postarea">
						<div class="postcontainer"><span id="embed_object"></span></div>
						<div style="clear:both;"></div>
						
					</div>
	
	<?php
	}

function displayPagination($catName,$p)				/******************  Pagination Code ************************/
	{
		global $seanUdallPostLimit;
		$objSeanUDallData= new seanudallData("seanudall_posts","");
		$numPost=$objSeanUDallData->getSeanUdallPostCount($catName);
		?>
		<div>
		<? 	$contentCount=$seanUdallPostLimit;
			$this->showSeanUdallPagination($numPost,$contentCount,$p,$catName);
		?>
		</div>
	<? }
	
	function showSeanUdallPagination($numrows,$contentcount,$p,$catName){
	/*pagination start here*/
		global $HTPFX,$HTHOST;
	 
	if($catName=="Trades & Ideas"){
	 	$url= $HTPFX.$HTHOST."/seanudall/tradeideas.htm";
	}elseif($catName=="Mailbag"){
	 	$url= $HTPFX.$HTHOST."/seanudall/mailbag.htm";
	}elseif($catName=="Research Tank"){
	 	$url= $HTPFX.$HTHOST."/seanudall/researchtank.htm";
	}else{
	 	$url= $HTPFX.$HTHOST."/seanudall/";
	}
	 
	 
		if($numrows>$contentcount) {
				 $totalRows = $countnum=ceil(($numrows/$contentcount));
		?>
		<div class="pagination">
			<?php
				if(!$p=="0")
				{
					$j=$p-1;
					if($j<1){
					?>
					<a  href="<?=$url?>">&lt; Previous Page</a>
				<?php }else{ ?>
					<a  href="<?=$url?>p/<?=$j?>">&lt; Previous Page</a>
				 <? }
				}
				$p=$p+1;
				 if($numrows>(($p)*$contentcount)){  ?>
				<span class="nxt"><a target="_parent" href="<?=$url?>p/<?=$p?>">NextPage &gt;</a></span>
				   <?php }
			?>
		</div>
		<?php  }  // for end
	}

}

?>