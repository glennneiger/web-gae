<?php
global $D_R;
include_once($D_R."/lib/config/_syndication_config.php");
include_once($D_R."/lib/_misc.php");
global $NOTIFY_FEED_ERROR_TO,$NOTIFY_FEED_ERROR_FROM,$NOTIFY_FEED_ERROR_SUBJECT,$feed_error_template;

$port=21;
$host=$yahoohost;
$user=$yahoouser;
$pass=$yahoopass;

$is_xml_generated="0";
$is_on_yahoo_ftp ="0";
$article = array();
$on_yahoo="0";
$sendmail = array();

$yahoo_feed_path = "/assets/yahoofeed/".$yahoopath."/";
$ftp_path =$yahoopath;
$ftp_full_path =$yahoofullpath;

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

$files_on_server = ftp_nlist($ftp,$ftp_path);
$full_files_on_server = ftp_nlist($ftp,$ftp_full_path);
$alert_TABLE="yahoo_logs";

$qryFeed = "SELECT YL.*,EIM.url,EIM.title,EIM.publish_date FROM `yahoo_logs` YL
LEFT JOIN ex_item_meta EIM ON EIM.item_id=YL.item_id AND EIM.Item_type IN ('1,18')
WHERE YL.datetime >= DATE_SUB(NOW(),INTERVAL 1 DAY)
AND YL.`on_yahoo`='0' AND YL.`invalid_ticker`='0' ORDER BY publish_date DESC ";
$resList = exec_query($qryFeed);
$id=0;
$item_id=NULL;
foreach($resList as $key=>$val)
{
	//$local_feed_path =$yahoo_feed_dir.$feed_file;


	if($val['item_id']==$item_id)
		continue;
	$item_id=$val['item_id'];
	$feed_file = $val['feed_name'];
	$local_feed_path = array('http://admin01d.minyanville.com','http://admin01a.minyanville.com','http://admin02d.minyanville.com');
	$ftp_path =$yahoopath;
	if($val['is_xml_generated']!="1")
	{
		foreach($local_feed_path as $path)
		{
			if(file_get_contents($path.$yahoo_feed_path.$feed_file))
			{
				$feed_generation_time = filemtime($D_R.$yahoo_feed_path.$feed_file);
				if(!empty($feed_generation_time))
				{
					$feed_generation_time = date('Y-m-d H:i:s',$feed_generation_time);
				}
				else
				{
					$feed_generation_time = date('Y-m-d H:i:s');
				}
				$data= array('is_xml_generated'=>'1','feed_generation_time'=>$feed_generation_time);
				update_query($alert_TABLE,$data,array(item_id=>$val['item_id']));
				$is_xml_generated ="1";
				break;
			}
		}
	}

	if($val['is_on_yahoo_ftp']!="1")
	{
		if(in_array("minyanville/".$feed_file,$files_on_server ) || in_array("yahoo/".$feed_file,$full_files_on_server ))
		{
			if(in_array("minyanville/".$feed_file,$files_on_server ))
			{
				$yahoo_ftp_time = ftp_mdtm($ftp,"minyanville/".$feed_file);
			}
			else if(in_array("yahoo/".$feed_file,$full_files_on_server)) {
				$yahoo_ftp_time = ftp_mdtm($ftp,"yahoo/".$feed_file);
			}
			if(!empty($yahoo_ftp_time))
			{
				$yahoo_ftp_time = date('Y-m-d H:i:s',$yahoo_ftp_time);
			}
			else
			{
				$yahoo_ftp_time = date('Y-m-d H:i:s');
			}
			$data= array('is_on_yahoo_ftp'=>'1','feed_yahoo_ftp_time'=>$yahoo_ftp_time);
			update_query($alert_TABLE,$data,array(item_id=>$val['item_id']));
			$is_on_yahoo_ftp="1";
		}
		else if($is_xml_generated=="1" || $val['is_xml_generated']=="1")
		{
			$localPath=$D_R."/assets/yahoofeed/minyanville/".$feed_file;
			$chkftp=ftpPut($localPath, $user, $pass, $host,$ftp_path);
			if($chkftp)
			{
				$files_on_server = ftp_nlist($ftp,$ftp_path);
				if(in_array("minyanville/".$feed_file,$files_on_server ) || in_array("yahoo/".$feed_file,$full_files_on_server ))
				{
					$ftpError="";
					if(in_array("minyanville/".$feed_file,$files_on_server ))
					{
						$yahoo_ftp_time = ftp_mdtm($ftp,"minyanville/".$feed_file);
					}
					else if(in_array("yahoo/".$feed_file,$full_files_on_server)) {
						$yahoo_ftp_time = ftp_mdtm($ftp,"yahoo/".$feed_file);
					}

					if(!empty($yahoo_ftp_time))
					{
						$yahoo_ftp_time = date('Y-m-d H:i:s',$yahoo_ftp_time);
					}
					else
					{
						$yahoo_ftp_time = date('Y-m-d H:i:s');
					}
					$data= array('is_on_yahoo_ftp'=>'1','feed_yahoo_ftp_time'=>$yahoo_ftp_time);
					update_query($alert_TABLE,$data,array(item_id=>$val['item_id']));
					$is_on_yahoo_ftp="1";
				}
			}
			else
			{
				debug($feed_file." could not be uploaded on Yahoo FTP Server");
				$ftpError=$feed_file." could not be uploaded on Yahoo FTP Server";
			}
		}
		else
		{
			debug("ftpPut:".$feed_file." doesn't exist on Yahoo FTP server");
			$ftpError=$feed_file." doesn't exist on Yahoo FTP server";
		}
	}
}
$item_id=NULL;
foreach($resList as $key=>$val)
{
	if($val['on_yahoo']!="1")
	{
			$on_yahoo="0";
			$rss_url = "http://finance.yahoo.com/rss/headline?s=".$val['ticker'];
			$rssArr = xml2array($rss_url,'1','tag');
			$yahooRssArr = $rssArr['rss']['channel']['item'];
			if($yahooRssArr['title']!="Yahoo! Finance: RSS feed not found")
			{
				foreach($yahooRssArr as $k=>$v)
				{
					if(strpos($v['link'],$val['url']))
					{
						$data= array('on_yahoo'=>'1','feed_on_yahoo_time'=>date('Y-m-d H:i:s'));
						update_query($alert_TABLE,$data,array(id=>$val['id']));
						$on_yahoo="1";
						continue;
					}
				}
				if($val['is_sent']!="20" && $on_yahoo!="1" )
					{
						$current_time = strtotime(date('Y-m-d H:i:s'));
						$pub_time = strtotime($val['publish_date']);
						$time_diff = round(abs($current_time - $pub_time)/60,2);
						if($time_diff>=12 && $time_diff<17)
						{
							if($val['is_sent']=="0")
							{
								$data= array('is_sent'=>'12');
								update_query($alert_TABLE,$data,array(id=>$val['id']));
								$sendmail[$val['item_id']]['ticker']=$sendmail[$val['item_id']]['ticker'].",".$val['ticker'];
								$sendmail[$val['item_id']]['time_diff'] = $time_diff;
								$sendmail[$val['item_id']]['title'] = $val['title'];
								debug("ftpPut: Article Not yet on Yahoo Finance");
								$ftpError="Feed File(".$feed_file.") not yet on Yahoo Finance";
							}
						}
						else if($time_diff>=17)
						{
							if($val['is_sent']=="0" || $val['is_sent']=="12")
							{
								$data= array('is_sent'=>'20');
								update_query($alert_TABLE,$data,array(id=>$val['id']));
								$sendmail[$val['item_id']]['ticker']=$sendmail[$val['item_id']]['ticker'].",".$val['ticker'];
								$sendmail[$val['item_id']]['time_diff'] = $time_diff;
								$sendmail[$val['item_id']]['title'] = $val['title'];
								debug("ftpPut: Article Not yet on Yahoo Finance");
								$ftpError="Feed File(".$feed_file.") not yet on Yahoo Finance";
							}
						}
					}
			}
			else
			{
				$data= array('invalid_ticker'=>'1');
				update_query($alert_TABLE,$data,array(id=>$val['id']));
			}
	}

}
if(!empty($sendmail))
{
	foreach($sendmail as $k1=>$v1)
	{

		$to="mvilsupport@mediaagility.com";
		$from=$NOTIFY_FEED_ERROR_FROM;
		$subject=substr($v1['title'], 0, 25)."-".$v1['time_diff']."min Not Found on Yahoo Finance under ticker ".$v1['ticker'];
		$file=$D_R."/assets/yahoofeed/minyanville/".$feed_file;
		$strurl = "id=".$k1."&syndchannel=yahoo&error=".urlencode($ftpError)."&ticker=".$v1['ticker'];
		mymail($to,$from,$subject,inc_web($feed_error_template."?".$strurl),$text,$file);
	}
	unset($sendmail);
}







?>