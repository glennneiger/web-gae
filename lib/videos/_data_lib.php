<?
class player{
	function replaceVideoCode($body,$from=null)
	{
		$pattern="/{VIDEO_([\/A-Za-z1-9-]+)}/"; // Video Embed Code pattren VIDEO_URLPattern
		preg_match_all($pattern, $body, $video_matches);
		foreach($video_matches[0] as $id=>$value)
		{
				$video_url = $video_matches[1][$id];
				$video_code = $this->getVideoCode($video_url);
				if($video_code)
				{
					$arPattren[] = $value;
					$arReplacemant[] = $video_code;
				}
			}
		if(is_array($arPattren))
		{
			$body=str_replace ($arPattren,$arReplacemant,$body);
		}
		return $body;
	}
	function getVideoCode($video_url)
	{
		$objCache= new Cache();
		$video_id = $objCache->getItemIdByURL($video_url,'11');
		if($video_id)
		{
			ob_start();
			$this->showPlayer($video_id,"","embed");
			$video_code =  ob_get_contents();
			ob_end_clean();
			return "<div style='float:left;padding:5px 0px 5px 0px;'>".$video_code."</div>";
		}
		else
		{
			return false;
		}

	}
	function getVideoId($url)
	{
		$sql = "SELECT item_id from ex_item_meta where (url = '".$url."' or concat(url,'/') = '".$url."') and item_type = 11 ";
		if(!$_SESSION['AMADMIN'])
		{
			$sql.=" AND is_live='1'";
		}
		$arResult = exec_query($sql,1);

		if(is_array($arResult))
		{
			return $arResult['item_id'];
		}
		return false;
	}
	function getdefaultPlaylist()
	{
		global $latestVidDuration;
		$sqlGetPlaylistlatest = "SELECT * FROM mvtv WHERE approved='1' AND publish_time <'".mysqlNow()."' AND publish_time > '".mysqlNow()."'- INTERVAL ".$latestVidDuration." ORDER BY publish_time DESC LIMIT 0,3";
		$sqlGetPlaylistPopBiz = "SELECT * FROM mvtv WHERE approved='1' AND publish_time < '".mysqlNow()."' - INTERVAL ".$latestVidDuration." AND FIND_IN_SET(cat_id,'26') ORDER BY id DESC LIMIT 0,2";
		$sqlGetPlaylistHB = "SELECT * FROM mvtv WHERE approved='1'  AND publish_time < '".mysqlNow()."' - INTERVAL ".$latestVidDuration." AND FIND_IN_SET(cat_id,'23') ORDER BY RAND() LIMIT 0,2";

		$resGetPlaylistPopBiz=exec_query($sqlGetPlaylistPopBiz);
		$resGetPlaylistSpecial=exec_query($sqlGetPlaylistSpecial);
		$resGetPlaylistTodd=exec_query($sqlGetPlaylistTodd);
		$resGetPlaylistHB=exec_query($sqlGetPlaylistHB);
		$resGetPlaylistlatest=exec_query($sqlGetPlaylistlatest);
		$playlist=array_merge($resGetPlaylistSpecial,$resGetPlaylistPopBiz,$resGetPlaylistTodd,$resGetPlaylistHB);
		shuffle($playlist);
		$playlist=array_merge($resGetPlaylistlatest,$playlist);
		return $playlist;
	}

	function getVideoPlaylist($categoryId=NULL,$curVideoId=NULL){
		global $latestVidDuration;
		if($curVideoId){
			$sqlGetCurVideo="SELECT * FROM mvtv WHERE id='".$curVideoId."'";
			$currentVideo=exec_query($sqlGetCurVideo);
		}
		if($categoryId){
			$sqlGetPlaylistCat="SELECT * FROM mvtv WHERE approved='1' and publish_time < '".mysqlNow()."' and id!='".$curVideoId."' and find_in_set(cat_id,'".$categoryId."') ORDER BY publish_time desc LIMIT 0,9";
			$playlist=exec_query($sqlGetPlaylistCat);
		}else{
			$playlist=$this->getdefaultPlaylist();
		}
		if($currentVideo)
			$playlist=array_merge($currentVideo,$playlist);
		foreach($playlist as $id=>$videoitem){
			$patterns = "/’/";
			$replacements = "'";
			$videoitem['description']=preg_replace($patterns, $replacements, $videoitem['description']);
			$patternTitle = "/’/";
			$replacementTitle = "'";
			$videoitem['title']=preg_replace($patternTitle, $replacementTitle, $videoitem['title']);
			$order = "…";
			$replace = "...";
			$videoitem['description'] = str_replace($order, $replace, $videoitem['description']);
			$orderTitle = "…";
			$replaceTitle = "...";
			$videoitem['title'] = str_replace($orderTitle, $replaceTitle, $videoitem['title']);
			$playlist[$id]=$videoitem;
		}
		return $playlist;
	}

	function showPlayer($videoId=NULL,$catId=NULL,$position='videosection',$disableAds=NULL,$forcedplay=NULL,$progId=NULL){
		global $HTPFX,$VIDEOHOST, $player,$_SESSION,$arrEtradeVideos,$article,$player_count,$STORAGE_SERVER;
		$player_count++;
		$objCache= new Cache();
		$playlist=$this->getVideoPlaylist($catId,$videoId);

		if($forcedplay){
			$autoplay=TRUE;
		}elseif($_SESSION['AdsFree']){
			$autoplay=FALSE;
		}
		else if(!$_SESSION['videoautoplay'] || $_SESSION['videoautoplay'] <=4) // Auto play 5 times per session.
		{
			$_SESSION['videoautoplay']=$_SESSION['videoautoplay']+1;
			$autoplay=TRUE;
		}else{
			$autoplay=FALSE;
		}
		if($_GET['preroll']=="0" || $_GET['preroll']=="")
		{
			$preroll="0";
		}

		?>
		<script language="javascript" src="http://cdn-static.liverail.com/js/companions.js"></script>
		<script language="javascript">
			lrCompanionPurge["300x250"] = true;
		</script>
        <?
		if(!strpos($article->fullbody,'flowplayer')){ ?>
		<script type="text/javascript" src="<?=$STORAGE_SERVER;?>/mvtv/flowplayer/flowplayer-3.2.0.min.js"></script>
		<? } ?>
        <a href="<?=$playlist[0]['videofile']?>" style="display:block;width:<?=$player[$position]['width'];?>;height:<?=$player[$position]['height'];?>;clear:both:" id="<?=$position.$player_count;?>_player">
        <? if($position == "embed") {?>
        <img src="<?=$playlist[0]['stillfile'];?>" alt="" width="<?=$player[$position]['width'];?>" height="<?=$player[$position]['height'];?>" />
        <? }?>
        </a>
		<script>
		reloadstatus=0;
		var acudeoParams = {
		<? switch($position){
				case "videosection":
					if(in_array($videoId,$arrEtradeVideos)){ ?>
						progId: "<?=$player[$position][$videoId];?>",
				<? 	}else{ ?>
					progId: "<?=$player[$position]['default'];?>",
				<? }
					break;
				case "embed": ?>
					progId: "<?=$player[$position]['default'];?>",
				<? break;
				case "rightcolumn":
					if($autoplay){ ?>
						progId: "<?=$player[$position]['autoplay'];?>",
				<? }else { ?>
						progId: "<?=$player[$position]['clicktoplay'];?>",
				<? }
					break;
		 } ?>
		version: 2,
		videoId: "<?=$playlist[0]['id'];?>",
		title: "<?=urlencode(str_replace("'","",$playlist[0]['title']))?>",
		videoDescriptionUrl: "<?php echo $HTPFX.$VIDEOHOST.$objCache->getItemLink($playlist[0]['id'], '11');?>",
		description:"<?=urlencode(str_replace(array("\"","'"),array("",""),mswordReplaceSpecialChars(strip_tags($playlist[0]['description']))))?>",
		debug: false
		};
		prerollPlay=0;
		prerollFinish=0;
		videoStart=0;
		flowplayer("<?=$position.$player_count;?>_player",
		{src:"http://releases.flowplayer.org/swf/flowplayer-3.2.12.swf",wmode: "transparent"},
		{
		clip: {
				<? /* track start event for this clip */ ?>
				onBegin: function(clip) {

					if(clip.url.indexOf("flv")>=0 && prerollPlay>=1 && videoStart==0)
					{
						if(clip.autoPlay){
							pageTracker._trackEvent("Videos", "Autoplay", clip.section+ " | " +clip.title);
							ga('send', 'event', 'Videos', 'Autoplay',clip.section+ ' | ' +clip.title);

        }else{
							pageTracker._trackEvent("Videos", "Play", clip.section+ " | " +clip.title);
							ga('send', 'event', 'Videos', 'Play',clip.section+ ' | ' +clip.title);
						}
						videoStart=1;
					}else{
						prerollPlay=prerollPlay+1;

					}
				},

				<? /* track pause event for this clip. time (in seconds) is also tracked */ ?>
				onPause: function(clip) {
					if(clip.url.indexOf("flv")>=0 && prerollFinish>=1)
					{
						pageTracker._trackEvent("Videos", "Pause", clip.section+" | " +clip.title, parseInt(this.getTime()));
						ga("send", "event", "Videos", "Pause",clip.section+" | " +clip.title, parseInt(this.getTime()));

					}else{
						prerollFinish=prerollFinish+1;
		}
				},

				<? /* track stop event for this clip. time is also tracked */ ?>
				onStop: function(clip) {
					if(clip.url.indexOf("flv")>=0)
					{
						pageTracker._trackEvent("Videos", "Stop", clip.section+" | " +clip.title, parseInt(this.getTime()));
						ga("send", "event", "Videos", "Stop",clip.section+" | " +clip.title, parseInt(this.getTime()));
						prerollFinish=0;

			}
				},

				<? /* track finish event for this clip */ ?>
				onFinish: function(clip) {
				videoStart=0;
					if(clip.url.indexOf("flv")>=0)
					{
						pageTracker._trackEvent("Videos", "Finish", clip.section+" | " +clip.title);
						ga("send", "event", "Videos", "Finish",clip.section+" | " +clip.title);
						prerollFinish=0;
					}
				}
			},
			<? if($autoplay) { ?>
			onLoad: function () {
				this.setVolume(50);
			},
			<?
			}
			if($position != 'embed')
			{
			?>
			playlist: [
				{
					url:"<?=$playlist[0]['stillfile'];?>"
				},
				<? 	$playlistSize=sizeof($playlist);
					foreach($playlist as $video) {
		?>
				{
					url:"<?=$video['videofile'];?>",
					title:"<?=urlencode(str_replace("'","",$video['title']));?>",
					section:"<?=urlencode(str_replace("'","",getSectionName($video['cat_id'])));?>",
					<? if($autoplay || $playlist[0]['id']!=$video['id']) { ?>
						autoPlay: true,
					<? }else{ ?>
						autoPlay: false,
					<? } ?>
						autoBuffering: false
				<? if($video['id']==$playlist[$playlistSize-1]['id']) { ?>
				}
				<? }else{ ?>
				},
				<? }
				} ?>
			],
        <?
			} // End check for embeded code
		?>
		<? if(!$_SESSION['AdsFree'] || $disableAds){
			if($video['cat_id']!="23" || $preroll!="0")
			{
			?>
		plugins: {
			 liverail: {
            	url: 'http://vox-static.liverail.com/swf/v4/plugins/flowplayer/LiveRailPlugin320.swf',
            	LR_PUBLISHER_ID: "11721",
            	LR_LAYOUT_SKIN_MESSAGE: 'Advertisement. Video will resume in {COUNTDOWN} seconds.'
         	}
						}
		<? } }?>
		});
	</script>
	<?	}


	function getVideoCatId($catName){
		$qry="SELECT section_id,NAME FROM section WHERE TYPE='subsection' AND subsection_type='video' AND is_active=1 AND LOWER(NAME) LIKE '%".$catName."%'";
		$getCatId=exec_query($qry,1);
		if(!empty($getCatId)){
			return $getCatId;
		}

	}

}
?>