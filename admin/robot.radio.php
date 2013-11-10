<?php
global $D_R,$VIDEO_SERVER;
include_once("$D_R/lib/_content_data_lib.php");
$port=21;
$host="ftp.minyanville.com";
$user="radio";
$pass="8cYhC1OxiMm1Xpr";

$ftp=ftp_connect($host,$port);
if(! ($ftp=ftp_connect($host, $port)) ){
	debug("ftpPut:couldn't connect to $host:$port");
	$ftpError="couldn't connect to ".$host.":".$port;
	return 0;
}
if((!$login=ftp_login($ftp, $user, $pass))){
	debug("ftpPut: couldn't log in with $user:$pass");
	$ftpError="couldn't log in with ".$user.":".$pass;
	ftp_close($ftp);
	return 0;
}
ftp_pasv($ftp, true);
$files_on_server = ftp_nlist($ftp,".");
$files = scandir($D_R.'/assets/radio/');

$uploadFiles = array_diff($files_on_server,$files);
if(!empty($uploadFiles))
{
	foreach($uploadFiles as $key=>$val)
	{
		if($val !="processed")
		{
			$ext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
			if($ext=="mp3")
			{
				$date = date('MdyHis');
				$local_file_path =$D_R.'/assets/radio/'.$date.'.mp3';
				$qry = "SELECT id FROM article_meta WHERE item_key='radiofile' AND `item_value` LIKE '%".$date.".mp3'";
				$resList = exec_query($qry,'1');

				if(empty($resList))
				{
					if(ftp_get($ftp, $local_file_path, $val, FTP_BINARY))
					{
						$modify_time = date('F d  H:i:s',ftp_mdtm($ftp, $val));
						$article['contrib_id'] ="809";
						$article['approved']="0";
						$article['sent']="0";
						$article['is_public']="0";
						$article['position']="No positions in stocks mentioned.";
						$article['date']=date('Y-m-d H:i:s');
						$article['title']="Money Matters Radio File(".$val.") uploaded on ".$modify_time;
						$article['body']="<br />
		{RADIO}<br />
		<br />
		<br />
		<em>Twitter: <a href='https://twitter.com/moneymattersfn'>@MoneyMattersFN</a></em>";
						$article['contributor']="Money Matters Radio";
						$article['keyword']="";
						$article['is_live']="";
						$article['publish_date']="";
						$article['section_id']="51";
						$article['navigation_section_id']="98";
						$article['is_marketwatch']="0";
						$article['is_fox']="0";
						$article['hosted_by']="Minyanville";
						$article['subsection_ids']="93,98,51";
						$article['is_msnfeed']="0";
						$article['is_yahoofeed']="0";
						$article['is_buzzalert']="0";
						$article['layout_type']="radio";

						$id=insert_query("articles",$article);
						if($id>0)
						{
							$upload = ftp_rename($ftp, $val, "processed/".$val);
							if($upload=="1")
							{
								$article_id[] = $id;
								$article_revision['article_id'] = $id;
								$article_revision['revision_number'] = "1";
								$article_revision['body'] = "<br />
				{RADIO}<br />
				<br />
				<br />
				<em>Twitter: <a href='https://twitter.com/moneymattersfn'>@MoneyMattersFN</a></em>";
								$article_revision['updated_by'] = "mediaagility";
								$article_revision['updated_date'] = date('Y-m-d H:i:s');
								$article_revision['page_no'] ="1";

								insert_query("article_revision",$article_revision);

							 	$article_meta['item_id'] =$id;
							 	$article_meta['item_type'] ="1";
							 	$article_meta['item_key'] ='radiofile';
							 	$article_meta['item_value'] =$VIDEO_SERVER."assets/radio/".$date.".mp3";
							 	insert_query("article_meta",$article_meta);
								$obContent = new Content(1,$id);
								$obContent->setArticleMeta();
							}
							else
							{
								del_query('articles','id',$id);
								$to="nidhi.singh@mediaagility.co.in";
								$from=$NOTIFY_FEED_ERROR_FROM;
								$subject="Radio Artcile Not Posted";
							 	$msg = "Article has been deleted for file ".$val." having article id ".$id;
								mymail($to,$from,$subject,$msg);
							}
						}
						else
						{
							unlink($local_file_path);
							$to="nidhi.singh@mediaagility.co.in";
							$from=$NOTIFY_FEED_ERROR_FROM;
							$subject="Radio Artcile Not Posted";
							$msg = "radio not posted for ".$val;
							mymail($to,$from,$subject,$msg);
						}
						shell_exec('rsync -avz --timeout=30 -e "ssh -p 16098 -i /home/sites/minyanville/.admin01a" /home/sites/minyanville/web/assets/radio/ minyanville@ec2-54-225-111-137.compute-1.amazonaws.com:/home/sites/minyanville/web/assets/radio/');
						shell_exec('rsync -avz --timeout=30 -e "ssh -p 16098 -i /home/sites/minyanville/.admin01d" /home/sites/minyanville/web/assets/radio/ minyanville@ec2-54-225-111-153.compute-1.amazonaws.com:/home/sites/minyanville/web/assets/radio/');
					}
				}
			}
			else
			{
				ftp_rename($ftp, $val, "processed/".$val);
				$to="nidhi.singh@mediaagility.co.in,news@minyanville.com";
				$from=$NOTIFY_FEED_ERROR_FROM;
				$subject="Radio Artcile Posted";
				$strurl = "?type=radio&error=1&file=".$val;
				mymail($to,$from,$subject,inc_web($feed_error_template.$strurl));
			}
		}
	}
	if(is_array($article_id)){
		$articleList = implode(",",$article_id);
		$to="nidhi.singh@mediaagility.co.in,news@minyanville.com";
		$from=$NOTIFY_FEED_ERROR_FROM;
		$subject="Radio Artcile Posted";
		$strurl = "?article_id=".$articleList."&type=radio";
		mymail($to,$from,$subject,inc_web($feed_error_template.$strurl));
	}
}



?>