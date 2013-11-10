/*-------------------------------------------------------------------------------
	A Better jQuery Tooltip
	Version 1.0
	By Jon Cazier
	jon@3nhanced.com
	01.22.08
-------------------------------------------------------------------------------*/

$.fn.betterTooltip = function(options){
	/* Setup the options for the tooltip that can be 
	   accessed from outside the plugin              */
	var defaults = {
		speed: 200,
		delay: 300
	};
	
	var options = jQuery.extend(defaults, options);
	
	/* Create a function that builds the tooltip 
	   markup. Then, prepend the tooltip to the body */
	getTip = function() {
			var tTip = 
				"<div id='tipBoxDiv' class='tipBox'>" +
					"<div class='tipSmallBox'>"	+
					"</div>" +
					"<div class='tipBigBox'></div>" +
				"</div>";
		return tTip;
	}
	


	jQuery("body").prepend(getTip());

	
	/* Give each item with the class associated with 
	   the plugin the ability to call the tooltip    */
	jQuery(this).each(function(){
		//console.log('start--- ');
		
		var $this = jQuery(this);
		var tip = jQuery('.tipBox');		
		var tipInner = jQuery('.tipBox .tipBigBox');
		var tipInnerSmall = jQuery('.tipBox .tipSmallBox');		
		var tTitle = (this.title);
		var tName = (this.name);
		this.title = "";
		this.name = "";
		var rightColumnOffset =jQuery(".wall-right").offset();
		var offset = jQuery(this).offset();
		var tLeft = offset.left;
		var tTop = offset.top;
		var tWidth = $this.width();
		var tHeight = $this.height();
		var $wall_title = jQuery('#wall-title');
        var $wall_right= jQuery('#wall-right');
		var $archive_left_wry= jQuery('#archive-left-wry');
		var $content_container=jQuery('#content-container');
		/* Mouse over and out functions*/
		$wall_title.hover(
			function() 
			{
			 hideTip();
			}
		);
		$wall_right.hover(
			function() 
			{
			 hideTip();
			}
		);
		$archive_left_wry.hover(
			function() 
			{
			 hideTip();
			}
		);
		/*
		$content_container.hover(
			function() 
			{
			 hideTip();
			}
		);
		*/
		$this.hover(
			function() {
					tipInnerSmall.html(tName);
					tipInner.html(tTitle);
				var imgid=this.id
				var tRight =285;
			//	console.log(tTop + '--' + tLeft + '--' + imgid + '--' + tRight);
				
				setTip(tTop,tLeft,imgid,tRight);
				showTip();
				
			} 
			
			
		);		   
		
		/* Delay the fade-in animation of the tooltip */
		setTimer = function() {
			$this.showTipTimer = setInterval("showTip()", defaults.delay);
		}
		
		stopTimer = function() {
			clearInterval($this.showTipTimer);
		}
		
		/* Position the tooltip relative to the class 
		   associated with the tooltip                */
		setTip = function(top,left,imgid,right){
			var topOffset = tip.height();
				if ((left + 530) > rightColumnOffset.left) {
					
					
					var xTip = (left-353)+"px";
					var yTip = (top)+"px";
					tip.css( {
						"top" : yTip,
						"left" : xTip,
						"display" : "block"
						
					});
					tipInnerSmall.css({
						"float":"right",
						"background-position":"left top",
						"padding" : "10% 4px 10% 18px"
					})
			} else {
				var xTip = (left)+"px";
				var yTip = (top)+"px";
				tip.css( {
					"top" : yTip,
					"left" : xTip,
					"display" : "block"
				});
				tipInnerSmall.css({
					"float":"left",
					"background-position":"right top",
					"padding" : "10% 18px 10% 4px"
				})
			}
					 
				 
			         
				/*
				tip2.css({"top": yTip, "right":xTip, "display" :"none"});
			*/
			
		}
		
		/* This function stops the timer and creates the
		   fade-in animation                          */
		showTip = function(){
		//	stopTimer();
			//tip.animate({"top": "+=20px", "opacity": "toggle"}, defaults.speed);
			//tip2.animate({"top": "+=20px", "opacity": "toggle"}, defaults.speed);
		}
		
		hideTip=function (){
	tip.hide();
}

	});
	
	
};

