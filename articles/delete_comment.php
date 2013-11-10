<?php

	$thread_id = $_POST['thread_id'];
	$post_comment_id = $_POST['post_comment_id'];
	
	$objPost=new Post();
	$articlecache= new Cache();
	$objPost->delete_thread_comment($thread_id,$post_comment_id);
	
	/*delete discussion cache is comment is post*/
	$articlecache->deleteDiscussionArticleCache($_POST['thread_id']);
	/*set new cache ofr article discussion comment*/
	$articlecache->setArticleDiscussionCache($_POST['thread_id'],$articleapprove=1);
?>
