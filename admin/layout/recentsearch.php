<?php
global $D_R;
include_once("$D_R/lib/_content_data_lib.php");
$obContent = new Content("","");
global $contentcount;
$contentcount = 20;
$searchResult=searchObject($_POST['keyword'],$_POST['author_id'],$_POST['month'],$_POST['date'],$_POST['year'],"","");
if(is_array($searchResult))
{
	$arSearchResult = array();
	foreach($searchResult as $searchRow) 
	{  
	  $objectData=$obContent->getMetaSearch($searchRow['object_id'],$searchRow['object_type']);								
	  if(is_array($objectData) && count($objectData) > 0)
	  {
	  	$arSearchResult[$searchRow['object_type']][] = $objectData;
	}
	}
}
//echo "<pre>";
//print_r($arSearchResult);	  
?>					
<table cellpadding="0" border="0" cellspacing="0" width="100%">
	<tr class="article_tab_bar" id="search _header">
    <td width="20%" id="article_tab" onclick="toggleSearchTab('article')" class="article_tab_selected">Articles</td><td width="20%" id="video_tab" onclick="toggleSearchTab('video')" class="article_tab">Videos</td>
    </tr>
	<tr><td  colspan="5"><table id="search_result_table">
	<tr id="searched_article">
		<td>
			<div style="overflow:scroll;width:400px;height:120px;border:1px solid black">
			<? 
			if(is_array($arSearchResult[1]))
			{
				foreach($arSearchResult[1] as  $articleRow)
				{ 
				//$articleTitle = str_replace('"', '&quot;', $articleRow['title']); 				
				$articleTitle = htmlentities($articleRow['title']);
				
			?>			
			<a href='#' onclick="selectSerchedItems('<?= $articleTitle?>','<?= $articleRow['item_id'].":".$articleRow['item_type'] ?>')"><?= $articleRow['title']; ?> by <?= $articleRow['author'];?></a> - <span class="date"><?= date('F d, Y g:i a',$articleRow['date']); ?></span><br>
			<? } 
			}
			else
			{
				echo "<font color='#FF0000'>No Record Found</font>";
			}
			?>
			</div>
		</td>
	</tr>
	<tr id="searched_video" style="display:none;">
		<td>
			<div style="overflow:scroll;width:400px;height:120px;border:1px solid black">
			<? 
			if(is_array($arSearchResult[10]))
			{
				foreach($arSearchResult[10] as $videoRow)
				{ 
				$videoTitle = htmlentities($videoRow['title']); 
			?>
				<a href='#' onclick="selectSerchedItems('<?= addslashes($videoTitle)?>','<?= $videoRow['item_id'].":".$videoRow['item_type'] ?>')"><?= $videoRow['title']; ?> by <?= $videoRow['author'];?></a> - <span class="date"><?= date('F d, Y g:i a',$videoRow['date']); ?></span><br>
			<? }
			}
			else
			{
				echo "<font color='#FF0000'>No Record Found</font>";
			}
			?>
			</div>
		</td>
	</tr>
	<tr id="searched_buzzpost" style="display:none;">
		<td>
			<div style="overflow:scroll;width:400px;height:120px;border:1px solid black">
			<? 
			if(is_array($arSearchResult[11]))
			{
				foreach($arSearchResult[11] as $buzzPostRow)
				{ 
				$buzzPostTitle = htmlentities($buzzPostRow['title']);
			?>
			<a href='#' onclick="selectSerchedItems('<?= addslashes($buzzPostTitle)?>','<?= $buzzPostRow['item_id'].":".$buzzPostRow['item_type'] ?>')"><?= $buzzPostRow['title']; ?> by <?= $buzzPostRow['author'];?></a> - <span class="date"><?= date('F d, Y g:i a',$buzzPostRow['date']); ?></span><br>
			<? }
			}
			else
			{
				echo "<font color='#FF0000'>No Record Found</font>";
			}
			?>
			</div>
		</td>
	</tr>	
	<tr id="searched_discussion" style="display:none;">
		<td>
			<div style="overflow:scroll;width:400px;height:120px;border:1px solid black">
			<? 
			if(is_array($arSearchResult[4]))
			{
				foreach($arSearchResult[4] as $discussionRow){ 
				$discussionTitle = htmlentities($discussionRow['title']);
			?>
			<a href='#' onclick="selectSerchedItems('<?= addslashes($discussionTitle)?>','<?= $discussionRow['item_id'].":".$discussionRow['item_type'] ?>')"><?= $discussionRow['title']; ?> by <?= $discussionRow['author'];?></a> - <span class="date"><?= date('F d, Y g:i a',$discussionRow['date']); ?></span><br>
			<? }
			}
			else
			{
				echo "<font color='#FF0000'>No Record Found</font>";
			}
			?>
			</div>
		</td>
	</tr>
	<tr id="searched_blog" style="display:none;">
		<td>
			<div style="overflow:scroll;width:400px;height:120px;border:1px solid black">
			<? 
			if(is_array($arSearchResult[6]))
			{
				foreach($arSearchResult[6] as $blogRow)
				{ 
					$blogTitle = htmlentities($blogRow['title']);
			?>
			<a href='#' onclick="selectSerchedItems('<?= addslashes($blogTitle)?>','<?= $blogRow['item_id'].":".$blogRow['item_type'] ?>')"><?= $blogRow['title']; ?> by <?= $blogRow['author'];?></a> - <span class="date"><?= date('F d, Y g:i a',$blogRow['date']); ?></span><br>
			<? }
			}
			else
			{
				echo "<font color='#FF0000'>No Record Found</font>";
			}?>
			</div>
		</td>
	</tr>
	</table></td></tr>
</table>