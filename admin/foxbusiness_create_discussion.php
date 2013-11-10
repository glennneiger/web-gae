<?
	global $D_R,$FoxBusiness;
	include_once($D_R.'/lib/_exchange_lib.php');
	require_once($D_R.'/lib/magpierss/rss_fetch.inc');
	$rss = fetch_rss($FoxBusiness["TopStories"]);
	$qry_foxsub="SELECT id FROM subscription WHERE email = 'foxbusiness@minyanville.com'";
	$res_foxid=exec_query($qry_foxsub,1);
	$tag = array();
    $tags = array();
	foreach($rss->items as $id=>$value){ 
			$qry_latesttitle="SELECT id,title FROM ex_thread WHERE title='".htmlentities($value['title'][0],ENT_QUOTES)."' and author_id = ".$res_foxid['id']." order by id desc";
			echo $qry_latesttitle;
	$res_latesttitle=exec_query($qry_latesttitle,1);

		if(count($res_latesttitle)>0)
			break;
		else{
				$thread['title']=htmlentities($value['title'][0],ENT_QUOTES);
				$thread['teaser']=htmlentities($value['description'][0],ENT_QUOTES);
				$thread['author_id']=$res_foxid['id'];
				$thread['created_on']=date('Y-m-d, H:i:s');
				$thread['approved']='1';
				$thread['content_table']="ex_thread_content";
				$thread_id=insert_query("ex_thread",$thread);
				$thread_content['thread_id']=$thread_id;
				$thread_content['body']="<a href='".$value['link'][0]."' target='_blank'>Click Here</a> to read complete article";
				$thread_content['body']=htmlentities($thread_content['body'],ENT_QUOTES);
				$thread_content['is_body']='1';
				$thread_content_id=insert_query('ex_thread_content',$thread_content);
				$update_thread['content_id']=$thread_content_id;
				update_query("ex_thread",$update_thread,array("id"=>$thread_id));
				$objCreateThread=new Thread();
				foreach($value['category'] as $tagkey => $tagvalue)
				{
					$extag=$objCreateThread->tag_exists($tagvalue,&$tags,&$tag);
					if ($extag==1)
					{
						$tag[item_id] = $thread_id;
						$tag[item_type] = '4';
						$strQry = "select tag_id from ex_item_tags where tag_id = $tag[tag_id] and item_id = $thread_id and item_type='4'";
						$strResult = exec_query($strQry);		
			
						if(count($strResult)==0){
							$item_tag_id=insert_query("ex_item_tags",$tag);
						}
					}
					else
					{	
						$exstock=$objCreateThread->is_stock($tagvalue);
							
						if ($exstock==1)
						{
							$tags[type_id] = '1';
						}
						else
						{
							$tags[type_id] = '0';
						}
						
						$tag_id=insert_query("ex_tags",$tags);
						$tag[tag_id]=$tag_id;
						$tag[item_id] = $thread_id;
						$tag[item_type] = '4';
						$strQry = "select tag_id from ex_item_tags where tag_id = $tag[tag_id] and item_id = $thread_id and item_type='4'";
						$strResult = exec_query($strQry);		
						if(count($strResult)==0){
							$item_tag_id=insert_query("ex_item_tags",$tag);
						}
					}
				}
			}
	  }
?>
