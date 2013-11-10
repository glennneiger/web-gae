<?php
session_start();
global $D_R,$HTPFXSSL,$HTHOST,$_SESSION;
$id = $_SESSION['SID'];
if(!empty($id)){
	if($_POST['author']){ /* if authors are already subsxcribed by user.*/
		$authsubscribedid=',';
		$authsubscribedid.=implode(",",$_POST['author']);
	}



	if($_POST['authors']){ // new subscribed author.
		$author_ids.=$authsubscribedid;
		$author_ids.=',';
		$author_ids.=implode(",",$_POST['authors']);
		$author_ids.=',';
	}else{
		$author_ids.=$authsubscribedid;
		$author_ids.=',';
	}
	if($author_ids==','){
		$author_ids="";
	}




	if($_POST['topic']){ /* if topic are already subsxcribed by user.*/
		$topicsubscribedid=',';
		$topicsubscribedid.=implode(",",$_POST['topic']);
	}

	if($_POST['topics']){ // new subscribed topics.
		$topic_ids.=$topicsubscribedid;
		$topic_ids.=',';
		$topic_ids.=implode(",",$_POST['topics']);
		$topic_ids.=',';
	}else{
		$topic_ids.=$topicsubscribedid;
		$topic_ids.=',';
	}
	if($topic_ids==','){
		$topic_ids="";
	}




	if($_POST['category']){
		foreach($_POST['category'] as $key=>$subcatid){
			if($subcatid=="7")
			{ $tradingRadar ="1";  }
			if($subcatid=="on"){
				$category_ids.=',';
				$category_ids.=$key;
			}
		}
		$category_ids.=',';
	}
	if($author_ids || $category_ids || $topic_ids){

		if($category_ids!=","){
			$cat_id[category_ids]=$category_ids;
			$cat_id[email_alert]=1;
		}else{
			$cat_id[category_ids]="";
			$cat_id[email_alert]=0;
		}

		if($authors_id || $author_ids!=","){
			if(!$author_ids){
				$author_ids=",";
			}
			$auth_id[author_id]=$author_ids.$authors_id;
			$auth_id[email_alert]=1;
		}else{
			$auth_id[author_id]="";
			$auth_id[email_alert]=0;
		}

		if($auth_id[author_id]==","){
			$auth_id[author_id]="";
			$auth_id[email_alert]=0;
		}


		if($topic_ids || $topic_ids!=","){
			if(!$topic_ids){
				$topic_ids=",";
			}
			$topic_id[section_ids]=$topic_ids.$topic_id;
			$topic_id[email_alert]=1;
		}else{
			$topic_id[section_ids]="";
			$topic_id[email_alert]=0;
		}

		if($topic_id[section_ids]==","){
			$topic_id[section_ids]="";
			$topic_id[email_alert]=0;
		}


		$subs[subscriber_id]=$id;
		$cat_id[subscriber_id]=$id;
		$topic_id[subscriber_id]=$id;
		$auth_id[subscriber_id]=$id;

		insert_or_update("email_alert_sectionsubscribe",$topic_id,$subs);
		insert_or_update("email_alert_categorysubscribe",$cat_id,$subs);
		insert_or_update("email_alert_authorsubscribe",$auth_id,$subs);

		$categoryArr = explode(',',$cat_id['category_ids']);
		if(in_array('5',$categoryArr))
		{
			subscribeTopicAlertMailChimp($id,$topic_id,'1');
		}	
		subscribeTopicAlertMailChimp($id,$topic_id,'0');
die;
		if($_POST['digest']=="On"){
			$recv_digest[recv_daily_gazette]=1;
		}else{
			$recv_digest[recv_daily_gazette]=0;
		}

		if($_POST['recv_promo']=="On"){
			$recv_digest[recv_promo]=1;
		}else{
			$recv_digest[recv_promo]=0;
		}

		$digestsubs[id]=$id;
		update_query("subscription",$recv_digest,$digestsubs);

	}
}
location($HTPFXSSL.$HTHOST.'/subscription/register/controlPanel.htm');

?>
