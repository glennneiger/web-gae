// FancyPlayer.js - A spicy mix of FancyBox and Flowplayer

jQuery(document).ready(function() {

	var videoclip;
	var player;
	var vidid;	
	var capShow;
	var ccbutShow;
	
	jQuery(".video_link").hover(function(){
		vidid = jQuery(".video_link").index(this);
		videoclip = jQuery(".video_link").eq(vidid).attr("name");		
	});
	
	jQuery(".video_link").fancybox({
		'hideOnContentClick':false,
		'overlayOpacity' :.6,
		'zoomSpeedIn'    :400,
		'zoomSpeedOut'   :400,
		'easingIn'		 : 'easeOutBack',
		'easingOut'		 : 'easeInBack',
		
		'callbackOnShow' :function(){
			
			if (videoclip == 'image') {				
				
				jQuery("#fancy_right, #fancy_left").css({height:jQuery("#fancy_div").height(), bottom: '0'});
				
				} else {
			
				player = $f("fancy_content",{src: swfplayer, wmode: 'opaque'},{
							
				play:{opacity:0},
			  //key: '#$flowplayerkeycode',

				plugins: {
								
				/* 
					configure a content plugin so that it  
					looks good for showing subtitles 
				*/ 
				content: { 
					url:swfcontent, 
					bottom: 25, 
					height:40, 
					backgroundColor: 'transparent', 
					backgroundGradient: 'none', 
					border: 0, 
					textDecoration: 'outline', 
					style: {  
						body: {  
							fontSize: 16,  
							fontFamily: 'Arial', 
							textAlign: 'center', 
							color: '#ffffff' 
						}  
					}  
				},
				
				controls:  {
				backgroundColor: 'transparent',
				progressColor: 'transparent',
				bufferColor: 'transparent',
				all:false,
                                //fullscreen:true,
				scrubber:true,
				volume:true,
				mute:true,
				play:true,
				height:30,
				autoHide: 'always'		

				}

				},
				clip:{
					autoPlay:true,
					autoBuffering:true,
					url:videoclip,	
					onCuepoint: [[-5000,-4000,-3000,-2000,-1000], function(clip, point) {
player.seek(2);
player.pause();
		}],
					onStart:function(clip){
						var wrap=jQuery(this.getParent());
						var clipwidth = clip.metaData.width;
						var clipheight= clip.metaData.height;
						var pos = jQuery.fn.fancybox.getViewport();
						jQuery("#fancy_outer").css({width:clipwidth+20,height:clipheight+20});
						jQuery("#fancy_outer").css('left', ((clipwidth + 36) > pos[0] ? pos[2] : pos[2] + Math.round((pos[0] - clipwidth	- 36)	/ 2)));
						jQuery("#fancy_outer").css('top',  ((clipheight + 50) > pos[1] ? pos[3] : pos[3] + Math.round((pos[1] - clipheight - 50)	/ 2)));
						jQuery("#fancy_right, #fancy_left").css({height:clipheight-60, bottom: '70px'});
						
					},
					onFinish:function(){
						jQuery('#fancy_close').trigger('click');
					}
				}
			});
			
			
				
			player.load();
			
		}
			
			jQuery('#fancy_right, #fancy_right_ico').click(function(){
				vidid++;
				videoclip = jQuery(".video_link").eq(vidid).attr("name");				
			});
			
			jQuery('#fancy_left, #fancy_left_ico').click(function(){
				vidid--;
				videoclip = jQuery(".video_link").eq(vidid).attr("name");				
			});
		},
		'callbackOnClose':function(){
			jQuery("#fancy_content_api").remove();
		}
	});
	
}); 