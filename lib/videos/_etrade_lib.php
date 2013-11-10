<?php
function showEtradePlayer($videoid=NULL,$stillfile=NULL,$videotitle=NULL,$width="400"){
if($width=="300")
{
	$playerId="4b5f38fa37da3";
}else{
	$playerId="4b58b4e554089";
}
?>
<script type="text/javascript" src="http://objects.tremormedia.com/embed/js/embed.js"></script>
<script type="text/javascript" src="http://objects.tremormedia.com/embed/js/<?=$playerId;?>_p.js"></script>
<script type="text/javascript">
<? if($videoid)	{
?>
		tmObj.set("VideoURL", "<?=$HTPFX.$HTHOST;?>/rss/MVTVMediaPlaylist.rss?category_id=66&videoid=<?=$videoid?>");
<? }else{ ?>
		tmObj.set("VideoURL", "<?=$HTPFX.$HTHOST;?>/rss/MVTVMediaPlaylist.rss?category_id=66");
<? } ?>
		tmObj.set("AutoPlay", "0");
		tmObj.set("PreviewImageURL",  "<?=$stillpath;?>");
		tmObj.set("ContentType", "playlist");
		tmObj.set("VideoTitle", "<?=$stillfile;?>");
		tmObj.start("/<?=$playerId;?>");
		tmObj.set("wmode", "opaque");
	</script>
<?
}

function showEtradeVideoPagePlayer($videofile=NULL,$stillfile=NULL,$videotitle=NULL,$videodesc=NULL,$videoSection=NULL)
{
	global $HTPFX,$HTHOST;

?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td align="left" class="mvtv_heading"><?=ucwords($videotitle);?></td></tr>
<tr><td align="left"><h3><?=$videoSection;?></h3></td></tr>
<tr><td align="left"><?=ucfirst($videodesc)."<br /><br />";?></td></tr>
<tr><td align="left">
<object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="640" height="480">
							<param name="movie" value="<?=$HTPFX.$HTHOST;?>/mvtv/player-viral.swf" />
							<param name="allowfullscreen" value="true" />
							<param name="allowscriptaccess" value="always" />
							<param name="wmode" value="opaque" />
							<param name="flashvars" value="file=<?=$videofile;?>&image=<?=$stillfile;?>&autostart=true" />
							<embed
								type="application/x-shockwave-flash"
								id="player2"
								name="player2"
								src="<?=$HTPFX.$HTHOST;?>/mvtv/player-viral.swf"
								width="640"
								height="480"
								wmode="opaque"
								allowscriptaccess="always"
								allowfullscreen="true"
								flashvars="file=<?=$videofile;?>&image=<?=$stillfile;?>&autostart=true"					/>
	</object>
</td></tr>
</table>
<?
}
function getVideoPlayerCode($video_id,$cat_name,$video_title,$video_desc,$video_file,$still_file,$disbaleAds=NULL)
{
global $HTPFX,$HTHOST;
$player['8316']='4bec700618059';
$player['8317']='4bec6fadbe8d6';
$player['8318']='4bec6f8f3a5ab';
$player['8319']='4bec6f6f41581';
$player['8320']='4bec6f4859eab';
?>
<script type="text/javascript" src="<?=$HTPFX.$HTHOST?>/mvtv/flowplayer/flowplayer-3.2.0.min.js"></script>
<a href="<?=$video_file?>" style="display:block;width:650px;height:480px" id="audiovideo_player"></a>
<script>
var acudeoParams = {
<? if($cat_name=='E*trade+Securities'){ ?>
progId: "<?=$player[$video_id];?>",
<? }else{ ?>
progId: "4be27afd47882",
<? } ?>
version: 2,
videoId: "<?=$video_id?>",
title: "<?=urlencode(str_replace("'","",$video_title))?>",
videoDescriptionUrl: "<?=$HTPFX.$HTHOST?>/audiovideo/<?=$video_id?>",
description:"<?=urlencode(str_replace("'","",strip_tags($video_desc)))?>",
debug: false
};
prerollPlay=0;
prerollFinish=0;

flowplayer("audiovideo_player",
{src:"http://releases.flowplayer.org/swf/flowplayer-3.2.12.swf",wmode: "transparent"},
{
clip: {
		<? /* track start event for this clip */ ?>
		onStart: function(clip) {
			if(clip.url.indexOf("flv")>=0 && prerollPlay==1)
			{
				pageTracker._trackEvent("Videos", "Play", clip.section+ " | " +clip.title);
				ga("send", "event", "Videos", "Play",clip.section+" | " +clip.title);
			}else{
				prerollPlay=1;
			}
		},

		<? /* track pause event for this clip. time (in seconds) is also tracked */ ?>
		onPause: function(clip) {
			if(clip.url.indexOf("flv")>=0 && prerollFinish==1)
			{
				pageTracker._trackEvent("Videos", "Pause", clip.section+" | " +clip.title, parseInt(this.getTime()));
				ga("send", "event", "Videos", "Pause",clip.section+" | " +clip.title, parseInt(this.getTime()));
			}else{
				prerollFinish=1;
			}
		},

		<? /* track stop event for this clip. time is also tracked */ ?>
		onStop: function(clip) {
			if(clip.url.indexOf("flv")>=0)
			{
				pageTracker._trackEvent("Videos", "Stop", clip.section+" | " +clip.title, parseInt(this.getTime()));
				ga("send", "event", "Videos", "Stop",clip.section+" | " +clip.title, parseInt(this.getTime()));
			}
		},

		<? /* track finish event for this clip */ ?>
		onFinish: function(clip) {
			if(clip.url.indexOf("flv")>=0)
			{
				pageTracker._trackEvent("Videos", "Finish", clip.section+" | " +clip.title);
				ga("send", "event", "Videos", "Play",clip.section+" | " +clip.title);
			}
		}
	},
playlist: [
	{
		url:"<?=$still_file;?>"
	},
	{
		url:"<?=$video_file;?>",
		title:"<?=urlencode(str_replace("'","",$video_title));?>",
		section:"<?=urlencode(str_replace("'","",$cat_name));?>",
		autoPlay: false,
		autoBuffering: false
	}
]<? if(!$disbaleAds){ ?>,
plugins: {
	controls: {
	// change the default controlbar to air
	url: "http://releases.flowplayer.org/swf/flowplayer.controls-air-3.2.12.swf"
	},
    acudeo_plugin: {
        url: 'http://objects.tremormedia.com/embed/swf/acudeoflowplayerplugin32.swf',
        acudeoParams: acudeoParams
    },
}
<? } ?>
});
</script>
<?
}
?>