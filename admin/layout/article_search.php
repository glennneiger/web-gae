<?php
global $D_R;
include_once("$D_R/lib/_content_data_lib.php");
$obContent = new Content("","");
global $contentcount;
$contentcount = 100;
$searchResult=searchArticles($_POST['keyword'],$_POST['author_id'],$_POST['month'],$_POST['date'],$_POST['year'],"","");
?>					
<table cellpadding="0" border="0" cellspacing="0" width="100%">
	<tr><td  colspan="5"><table id="search_result_table">
	<tr id="searched_article">
		<td>
			<div style="overflow:scroll;width:400px;height:120px;border:1px solid black">
			<? 
			if(is_array($searchResult))
			{
				foreach($searchResult as  $articleRow)
				{ 
				$articleTitle = htmlentities(addslashes($articleRow['title']));
				
			?>			
			<a href='#' onclick="selectSerchedItems('<?= $articleTitle?>','<?= $articleRow['id'].":1" ?>')"><?=$articleRow['title']?> by <?= $articleRow['author'];?></a> - <span class="date"><?= date('F d, Y g:i a',strtotime($articleRow['date'])); ?></span><br>
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
	</table></td></tr>
</table>