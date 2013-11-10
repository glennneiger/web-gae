<?php
global $HTPFX,$HTHOST;
ini_set('memory_limit', '256M');
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<rss version="2.0"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:dsq="http://www.disqus.com/"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:wp="http://wordpress.org/export/1.0/">
<channel>';
	 $qry = "SELECT df.id AS feedId,eth.id threadId , df.title AS title, df.body AS content,
IF(df.publish_date,df.publish_date,df.creation_date) AS publish_date, cs.url AS link,COUNT(ep.id) numcomment
FROM daily_feed df, ex_thread eth, content_seo_url cs,ex_post ep  WHERE
eth.content_id=df.id AND cs.item_id=df.id AND ep.thread_id=eth.id AND cs.item_type='18' AND df.is_live='1'
AND df.is_approved='1' AND is_deleted='0' AND eth.content_table='daily_feed'
 GROUP BY ep.thread_id HAVING numcomment>0 ORDER by eth.id desc";
$list =exec_query($qry);
foreach($list as $key){
	$content	=	strip_tags($key['content']);
	$content = html_entity_decode($content,ENT_QUOTES);
	$content=str_replace("&","&amp;",$content);
	$title=str_replace("&","&amp;",$key['title']);
	?>
	<item>
		<!-- title of article -->
		<title><?=strip_tags($title); ?></title>
		<!-- absolute URI to article -->
		<link><?=$HTPFX.$HTHOST.$key['link'];?></link>
		<!-- thread body; use cdata; html allowed (though will be formatted to DISQUS specs) -->
		<content:encoded><![CDATA[<?=$title?>]]></content:encoded>
		 <!-- value used within disqus_identifier; usually internal identifier of article -->
      	<dsq:thread_identifier><?=$HTPFX.$HTHOST.$key['link'];?></dsq:thread_identifier>
		<!-- creation date of thread (article) -->
     	<wp:post_date_gmt><?=gmdate("Y-m-d H:i:s",strtotime($key['publish_date']));?></wp:post_date_gmt>
		<wp:comment_status>open</wp:comment_status>
	<?php
		$cmntQry = "SELECT ep.id AS cmntId, CONCAT(sub.fname,' ',sub.lname) AS cmntAuthor,sub.email AS email, ep.poster_ip AS IP, epc.body AS cmntContent, ep.approved AS approved, ep.created_on AS cmntDate FROM ex_post ep, ex_post_content epc, subscription sub,ex_thread eth WHERE ep.thread_id='".$key['threadId']."' AND ep.thread_id=eth.id AND ep.poster_id=sub.id AND ep.id = epc.post_id";
		$comment = exec_query($cmntQry);

		 foreach($comment as $cmnt){
		 	$cmntContent	=	strip_tags($cmnt['cmntContent']);
		 	$cmntContent = html_entity_decode($cmntContent,ENT_QUOTES);
		 	?>
			  <wp:comment>
					<!-- internal id of comment -->
					<wp:comment_id><?=$cmnt['cmntId'];?></wp:comment_id>
					<!-- author display name -->
					<wp:comment_author><?=$cmnt['cmntAuthor'];?></wp:comment_author>
					<!-- author email address -->
					<wp:comment_author_email><?=$cmnt['email'];?></wp:comment_author_email>
					<!-- author url, optional -->
			        <wp:comment_author_url></wp:comment_author_url>
					<!-- author ip address -->
					<wp:comment_author_IP><?=$cmnt['IP'];?></wp:comment_author_IP>
					<!-- comment datetime -->
					<wp:comment_date_gmt><?=gmdate("Y-m-d H:i:s",strtotime($cmnt['cmntDate']));?></wp:comment_date_gmt>
					<!-- comment body; use cdata; html allowed (though will be formatted to DISQUS specs) -->
					<wp:comment_content><![CDATA[<?=$cmntContent;?>]]></wp:comment_content>
					<!-- is this comment approved? 0/1 -->
					<wp:comment_approved>1</wp:comment_approved>
					<!-- parent id (match up with wp:comment_id) -->
					 <wp:comment_parent>0</wp:comment_parent>
			  </wp:comment>
		 <?php } ?>
	</item>
<?php } ?>

 </channel>
</rss>