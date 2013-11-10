<?
global $D_R;
include_once($D_R."/lib/config/_disqus_config.php");
include_once($D_R."/lib/_via_data_lib.php");
require($D_R.'/lib/disqus/disqusapi/disqusapi.php');
global $shortName;
$objVia = new Via();

$secret_key = '4mn9BscckuGPVZYqWmLmZ65heykDtlQ3sNgHX2bVfs9GeRFNV6tvsOfDjGN9siSK';
$disqus = new DisqusAPI($secret_key);
$posts = $disqus->forums->listPosts(array("forum"=>$shortName,"limit"=>100));

foreach ($posts as $post){
$qryChkDup = "select id from ex_post_disqus where disqus_post_id='".$post->id."'";
$chkDup = exec_query($qryChkDup,1);
if($chkDup['id']==''){
	$threadDetail = $disqus->threads->details(array("thread"=>$post->thread));
	$threadURL = $threadDetail->identifiers['0'];
	
	$qryTable = "SELECT eit.item_table as tableName FROM ex_item_type eit,ex_item_meta eim WHERE eim.item_type=eit.id AND eim.url='".$threadURL."'";
	$restable = exec_query($qryTable,1);
	$table = $restable['tableName'];
	
	$qryThreadID = "SELECT eth.id AS id FROM ex_thread eth,ex_item_meta eim WHERE eth.content_id=eim.item_id AND
eim.url='".$threadURL."' AND eth.content_table='".$table."'";
	$qryThreadID = exec_query($qryThreadID,1);
	$threadId = $qryThreadID['id'];
	
	$email = $post->author->email;
	if($email){
		$qrySubId = "select id from subscription where email='".$email."'";
		$resSubId = exec_query($qrySubId,1);
		if($resSubId['id']==''){
			$objVia->registerDisqusUser($email);
			$qry = "select id from subscription where email='".$email."'";
			$resSubId = exec_query($qry,1);
		}
		if($resSubId['id']!=''){
			$posterId = $resSubId['id'];
		}else{
			$posterId = '0';
		}
	}
	$params['thread_id'] = $threadId;
	$params['poster_id'] = $posterId;
	$params['post_time'] = $post->createdAt;
	$params['poster_ip'] = $post->ipAddress;
	$params['created_on'] = $post->createdAt;
	$params['teasure'] = $post->message;
	
	$postId = insert_query("ex_post",$params);
	if($postId){
		$pars['post_id'] = $postId;
		$pars['body'] = $post->message;
		$content = insert_query("ex_post_content",$pars);
		
		$param['post_id'] = $postId;
		$param['disqus_post_id'] = $post->id;
		$param['disqus_author_id'] = $post->author->id;
		$param['disqus_username'] = $post->author->username;
		$disqusPost = insert_query("ex_post_disqus",$param);
	}
	echo '<br>';
	echo 'Export for '.$postId.' is done.';
	echo '<br>';
}else{
	echo '<br>';
	echo 'No New Comments to Export.';
	echo '<br>';
}
} //foreach

echo 'Comment Export Finish.';
?>
