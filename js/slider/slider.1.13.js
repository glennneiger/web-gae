_xMainFeature = {

_bTmpPaused : false, 
_nItemWidth : null, 
_nCurrentId: 1,
_nMaxItems: 1,
_nActive: 0,
_bQueueAdvance : false,
_bPlaying: false,
_bAutoPlay: true,
_xTimer: null,
_nTimeout: 6500,
_nTimeoutReplay: 7000,
_nAdTimeoutTmp: 0, 
_nFlipTimeout: 0, 
_nSpeed: 800, 
_nMinContentHeight: 110,
_bAnimating: false,
_sDivHolder: null,
init : function (p_sDiv,p_nWidth){
this._sDivHolder = p_sDiv;
  this._nItemWidth = parseInt(p_nWidth);
this._nMaxItems = parseInt(jQuery('#'+this._sDivHolder+' .display-holder .panel').length);
this._nAdTimeout = this._nTimeout;
//_xMainFeature.fixContentArea();
//this.hideAllExcept(_xMainFeature._nCurrentId); 
 
 if(navigator.userAgent.match(/MSIE ([0-9]+)\./))
 document.getElementById('main-feature').attachEvent('onselectstart', _xMainFeature.fixSelect); 
 
jQuery('#'+this._sDivHolder+' .mainfeature_pagination .featurenav').click( function(){ _xMainFeature.viewItem(  parseInt(jQuery(this).attr('id').substr(jQuery(this).attr('id').lastIndexOf('_')+1,2)) );return false; });
jQuery('#'+this._sDivHolder+' .mainfeature_pagination div.next').click( function(){_xMainFeature.viewItem(_xMainFeature._nCurrentId+1); return false; });
jQuery('#'+this._sDivHolder+' .mainfeature_pagination div.prev').click( function(){ _xMainFeature.viewItem(_xMainFeature._nCurrentId-1); return false; });
jQuery('#'+this._sDivHolder+' a.pause').click( function(){_xMainFeature.pauseFeature();return false; });
jQuery('#'+this._sDivHolder+' a.play').click( function(){_xMainFeature.viewItem(_xMainFeature._nCurrentId+1); _xMainFeature.playFeature();return false; });
 
jQuery('#'+this._sDivHolder+' .featurenav').hover( function(){ jQuery(this).addClass('hover');},function(){ jQuery(this).removeClass('hover');});

setTimeout( function(){ _xMainFeature.fixContentArea();  }, 200);
setTimeout( function(){ _xMainFeature.fixContentArea(1);  }, 500);
 
jQuery('#featurenav_'+_xMainFeature._nCurrentId).addClass('current');

setTimeout( function(){ (_xMainFeature._bAutoPlay ) ? (_xMainFeature.playFeature()) : _xMainFeature.pauseFeature();  }, 10);



},

fixSelect : function(){
return false;
},
 
fixContentArea : function(p_nVal){

jQuery('#'+this._sDivHolder+' .display-holder .panel').each( function() { 
 if( jQuery(this).children('.content-holder').length ){
var l_xHolder = jQuery(this).children('.content-holder');
var l_xImage = jQuery(this).find('.main-image');
var l_nHolderHeight = parseInt(l_xHolder.height());
if(l_nHolderHeight < _xMainFeature._nMinContentHeight){
l_nHolderHeight = _xMainFeature._nMinContentHeight;
l_xHolder.height(l_nHolderHeight);
}
var l_xImageHeight = parseInt(l_xImage.attr('height'));
if( !l_xImageHeight > 0)
l_xImageHeight = parseInt(l_xImage.height());

var l_nDiff = parseInt( parseInt(jQuery(this).height()) - l_xImageHeight - l_nHolderHeight);  
if(l_nDiff < 0){
 l_xHolder.css('top',(l_nDiff));
 l_xHolder.css('position','relative');
}
 }
}); 

jQuery('#'+this._sDivHolder+' .display-holder').width(this._nItemWidth*2);

if(p_nVal == 1){
if (navigator.userAgent.match(/MSIE ([0-9]+)\./)){
this.hideAllExcept(_xMainFeature._nCurrentId); 
jQuery('#'+this._sDivHolder+' .feature-wrapper').css('overflow','hidden'); 
}
}
 


},

viewItem: function (p_nVal){
_xMainFeature._bQueueAdvance = false;
p_nVal = parseInt(p_nVal);
if(p_nVal == this._nCurrentId)return;

if(!this._bAnimating){
if(g_nVideoPlaying !== false){
revertVideoWithImage();
}


this._bAnimating = !this._bAnimating;
jQuery('#'+this._sDivHolder+' .mainfeature_pagination .featurenav').removeClass('current');      
jQuery('#'+this._sDivHolder+' .feature-wrapper').css('overflow','hidden');

var l_nSpecialCase = -1;

if(p_nVal > this._nMaxItems)
l_nSpecialCase = 1;
else if(p_nVal <= 0)
l_nSpecialCase = this._nMaxItems;
  
if(l_nSpecialCase != -1){
this._nCurrentId = l_nSpecialCase;
(l_nSpecialCase == 1) ? l_nMultiplier = 1 : l_nMultiplier = -1;
jQuery('#'+this._sDivHolder+' .display-holder #feature-item-'+this._nCurrentId).css('position','absolute').css('left',( l_nMultiplier * this._nItemWidth) ).show();
jQuery('#'+this._sDivHolder+' .display-holder').css('left', 0).animate( { 'left': -(( l_nMultiplier * this._nItemWidth)) }, _xMainFeature._nSpeed, function(){ _xMainFeature.finishedAnimationOverRotation() });
jQuery('#featurenav_'+this._nCurrentId).addClass('current');
return;
}
 
jQuery('#featurenav_'+p_nVal).addClass('current');

(p_nVal > this._nCurrentId) ? l_nDirection = -1 : l_nDirection = 1; 
 
jQuery('#'+this._sDivHolder+' .display-holder .item').each( function() { 
 
var l_nPosition = parseInt(jQuery(this).attr('id').substr( jQuery(this).attr('id').lastIndexOf('-')+1,1));
if(l_nDirection < 0){
if( l_nPosition != _xMainFeature._nCurrentId && l_nPosition > _xMainFeature._nCurrentId)
jQuery(this).hide();
}else{
if( l_nPosition != _xMainFeature._nCurrentId && l_nPosition < _xMainFeature._nCurrentId)
jQuery(this).hide();
}
 
});
  
this._nCurrentId = p_nVal;
jQuery('#'+this._sDivHolder+' .display-holder #feature-item-'+this._nCurrentId).show();
var l_nNewPosition = -(this._nItemWidth);
if(l_nDirection > 0){
jQuery('#'+this._sDivHolder+' .display-holder').css('left', -this._nItemWidth);
l_nNewPosition = 0;
} 
jQuery('#'+this._sDivHolder+' .display-holder').animate( { 'left': l_nNewPosition }, _xMainFeature._nSpeed, function(){ _xMainFeature.finishedAnimation() });
}
},

hideAllExcept : function(p_nVal){
jQuery('#'+this._sDivHolder+' .display-holder .panel').each( function() {  
 var l_nPosition = parseInt(jQuery(this).attr('id').substr( jQuery(this).attr('id').lastIndexOf('-')+1,1));
  if( l_nPosition != p_nVal)
   jQuery(this).hide();
});
},

finishedAnimation: function (p_nVal){
 
this.hideAllExcept(_xMainFeature._nCurrentId);
if( parseInt(jQuery('#'+this._sDivHolder+' .display-holder').css('left')) != 0){
jQuery('#'+this._sDivHolder+' .display-holder').css('left', 0);
}
if (navigator.userAgent.match(/MSIE ([0-9]+)\./)){
jQuery('#'+this._sDivHolder+' .feature-wrapper').css('overflow','hidden'); 
}
this._bAnimating = false;
this.setPlayTimer();
 
},

finishedAnimationOverRotation: function (){
 
this.hideAllExcept(_xMainFeature._nCurrentId);
jQuery('#'+this._sDivHolder+' .display-holder #feature-item-'+_xMainFeature._nCurrentId).css('position','').css('left','');
jQuery('#'+this._sDivHolder+' .display-holder').css('left', 0);
if(navigator.userAgent.match(/MSIE ([0-9]+)\./)){
jQuery('#'+this._sDivHolder+' .feature-wrapper').css('overflow','hidden'); 
}
this._bAnimating = false;
this.setPlayTimer();
 
},

playFeature : function(){
//this._bTmpPaused = false;
jQuery('#'+this._sDivHolder+' .pause').show();
jQuery('#'+this._sDivHolder+' .play').hide();
this._bPlaying = true;
this.setPlayTimer();
},

pauseFeature : function(){
_xMainFeature._bQueueAdvance = false;
this._bTmpPaused = false;
this._bPlaying = false;
jQuery('#'+this._sDivHolder+' .pause').hide();
jQuery('#'+this._sDivHolder+' .play').show();
clearTimeout(this._xTimer);
},
 
 
tmpPauseFeature : function(){
if(this._bAutoPlay){
this._bTmpPaused = true;
//clearTimeout(this._xTimer);
}
},

revertFeature : function(){
_xMainFeature._bTmpPaused = false;
setTimeout(function(){
if( !_xMainFeature._bTmpPaused ){
if( _xMainFeature._bQueueAdvance ){
_xMainFeature._bQueueAdvance = false;
_xMainFeature.viewItem(_xMainFeature._nCurrentId+1);
_xMainFeature.playFeature();
}
else
_xMainFeature._bTmpPaused = false;
}
},200);
},


setPlayTimer: function(){

var adTimeOut=jQuery("#adslidetime").val();
var adUnitRefreshCheck=jQuery("#adUnitRefreshCheck").val();
adTimeOut=adTimeOut*1000;

/*if(adUnitRefreshCheck > 1){
	this._nTimeout=this._nAdTimeout;
	
	if(jQuery('#'+this._sDivHolder+' .display-holder #feature-item-'+_xMainFeature._nCurrentId+' #feature_ad').length >0){
			setTimeout("stopPlaySlideVideo()",8000);						   
		
		this._nTimeout=this._nTimeoutReplay;
	}
}else if(jQuery('#'+this._sDivHolder+' .display-holder #feature-item-'+_xMainFeature._nCurrentId+' #feature_ad').length >0){
	jQuery("#adUnitRefreshCheck").val('2');
	 setTimeout("playSlideAdUnitVideo()",8000);	
	this._nTimeout = adTimeOut + 8000;
}
else
{
	if(jQuery('#'+this._sDivHolder+' .display-holder #feature-item-'+_xMainFeature._nCurrentId+' #feature_ad').length >0){
		alert('her');
		setTimeout("stopPlaySlideVideo()",8000);	
	}
	this._nTimeout=this._nAdTimeout;
}*/


if(jQuery('#'+this._sDivHolder+' .display-holder #feature-item-'+_xMainFeature._nCurrentId+' #feature_ad').length >0)
{
	
	if(jQuery("#adUnitRefreshCheck").val() == 1)
	{		
		playSlideAdUnitVideo();
		jQuery("#adUnitRefreshCheck").val('2');		
	}
	else
	{		
		stopPlaySlideVideo();	
	}	
	this._nTimeout = adTimeOut;
}
else
{	
	if(navigator.userAgent.match(/MSIE ([0-9]+)\./))
	{
		stopPlaySlideVideo(); 
	}	
}

if(this._bPlaying){
clearTimeout(this._xTimer);
this._xTimer = setTimeout(function(){
if( _xMainFeature._bTmpPaused ){
	_xMainFeature._bQueueAdvance = true;
}
else
	_xMainFeature.viewItem(_xMainFeature._nCurrentId+1);
},this._nTimeout);
}
},

hideControls : function(){
jQuery('#'+this._sDivHolder+' .mainfeature_pagination').hide();
jQuery('#'+this._sDivHolder+' .pause').hide();
jQuery('#'+this._sDivHolder+' .play').hide();
},

showControls : function(){
jQuery('#'+this._sDivHolder+' .mainfeature_pagination').show();
(this._bPlaying) ? jQuery('#'+this._sDivHolder+' .pause').show() : jQuery('#'+this._sDivHolder+' .play').show();
}
 
}

//******************* Code to support Video switch: //

var g_nVideoPlaying = false;

var replaceImageWithVideo = function(p_nId, p_sVidUri, l_nVidType, p_nWidth, p_nHeight){

if(g_nVideoPlaying === false && !g_bCurrentlyRotating && !_xMainFeature._bAnimating ){

var l_sVidUri = Base64.decode(p_sVidUri);

/*var l_nVidType  = jQuery('#vid_link_'+p_nId).attr('rev'); */
//alert(1);
g_nVideoPlaying = p_nId;

/* check newsmaker */
if(document.newsmakerid){
pauseFeature();
jQuery('#main_feature_reel').find('#main_feature_current').find('#vid_link_'+p_nId).hide();
/*jQuery('#main_feature_reel').find('#main_feature_current').find('#content-holder_'+p_nId).hide();*/
if(l_nVidType==1){
l_sVidUri = document._sStaticServer + '/files/' + l_sVidUri;
jQuery('#main_feature_reel').find('#main_feature_current').find('#vid_holder_'+p_nId).html('<object width="469" height="275"><param name="movie" value="/swf/TheDailyBeastVideoPlayer.swf?r='+document._sReleaseVersion+'"></param><param name="quality" value="high">  </param>  <param name="menu" value="false">  </param>  <param name="wmode" value="transparent">  </param>  <param name="allowFullScreen" value="true">  </param>  <param name="allowScriptAccess" value="always">  </param>  <param name="flashvars" value="autoplay=true&mainfeature=true&newsmakerpage=true&video='+l_sVidUri+'&still=&title=">  </param>  <embed type="application/x-shockwave-flash" src="/swf/TheDailyBeastVideoPlayer.swf?r='+document._sReleaseVersion+'" id="tdbvideo" name="tdbvideo" bgcolor="#ffffff" quality="high" menu="false" wmode="transparent" allowFullScreen="true" allowScriptAccess="always" width="469" height="275" flashvars="autoplay=true&mainfeature=true&newsmakerpage=true&video='+l_sVidUri+'&still=&title="></embed></object>');
}else{
jQuery('#main_feature_reel').find('#main_feature_current').find('#vid_holder_'+p_nId).html(_xVideo.resizeEmbbedVideo(l_sVidUri, p_nWidth, p_nHeight  ));
}
}else{
_xMainFeature.pauseFeature();
jQuery('#vid_link_'+p_nId).hide();
jQuery('#vid_holder_'+p_nId).show();
/*jQuery('#content-holder_'+p_nId).hide();*/
if(l_nVidType==1){
l_sVidUri = document._sStaticServer + '/files/' + l_sVidUri;
 
jQuery('#vid_holder_'+p_nId).html('<object width="397" height="330"><param name="movie" value="/swf/TheDailyBeastVideoPlayer.swf?r='+document._sReleaseVersion+'"></param><param name="quality" value="high">  </param>  <param name="menu" value="false">  </param>  <param name="wmode" value="transparent">  </param>  <param name="allowFullScreen" value="true">  </param>  <param name="allowScriptAccess" value="always">  </param>  <param name="flashvars" value="autoplay=true&mainfeature=true&video='+l_sVidUri+'&still=&title=">  </param>  <embed type="application/x-shockwave-flash" src="/swf/TheDailyBeastVideoPlayer.swf?r='+document._sReleaseVersion+'" id="tdbvideo" name="tdbvideo" bgcolor="#ffffff" quality="high" menu="false" wmode="transparent" allowFullScreen="true" allowScriptAccess="always" width="397" height="330" flashvars="autoplay=true&mainfeature=true&video='+l_sVidUri+'&still=&title="></embed></object>');
}else{
jQuery('#vid_holder_'+p_nId).html( _xVideo.resizeEmbbedVideo(l_sVidUri, p_nWidth, p_nHeight )  );
}

}
}

}

var revertVideoWithImage = function(){

if(document.newsmakerid){
jQuery('#main_feature_reel').find('#main_feature_current').find('#vid_link_'+g_nVideoPlaying).show();
//jQuery('#main_feature_reel').find('#main_feature_current').find('#content-holder_'+g_nVideoPlaying).show();
jQuery('#main_feature_reel').find('#main_feature_current').find('#vid_holder_'+g_nVideoPlaying).empty();
}else{
jQuery('#vid_link_'+g_nVideoPlaying).show();
//jQuery('#content-holder_'+g_nVideoPlaying).show();
jQuery('#vid_holder_'+g_nVideoPlaying).empty();
jQuery('#vid_holder_'+g_nVideoPlaying).hide();
}
g_nVideoPlaying  = false;
}

function vidPlaying() {
		adTimeOut=jQuery("#adslidetime").val();
		jQuery("#adUnitReplayTime").val(adTimeOut);
}

function vidCompleted() {
	jQuery("#adUnitReplayTime").val('6.5');
}

function stopPlaySlideVideo(){
		
	object_id = jQuery('#feature_ad object').attr('id');
	if(object_id)
	{
		window.document[object_id].sendControlFromHtml('stop'); //chrome
	}		
}
function playSlideAdUnitVideo()
{	
	object_id = jQuery('#feature_ad object').attr('id');
	if(object_id)
	{
		window.document[object_id].sendControlFromHtml('play'); //chrome
	}				
}