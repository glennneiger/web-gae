/*
ScrollBar 2.1
This Script is Freeware
created by: Martyn van Beek
mailto: martyn@sophistry.nl
web: http://www.sophistry.nl
web: http://www.devigner.com

for Questions:
Post your question's on the dedicated Forum of FlashKit
http://www.flashkit.com/movies/Scripting/Scrolling/Advanced-Martyn_v-5334/index.shtml

How to:
	see Content ("Page" symbol in Library)
*/
onClipEvent (load) {

	// Creating Objects (for the "Ghost status")
	gObj = new Array ( Thumb , UpButton , DownButton , Bar , InvisibleThumb , PositionBar );
	len = 6
	// setting not changeable properties
	if(direction == 1){
		frameH = _parent.frame._height
	}else if(direction ==2){
		frameH = _root.frame2._width
	}
	bottomH = Bar.bottom._height
	topH = Bar.top._height	
	// Creating ScrollBar (note: the MC _parent.frame's height is taken as height)	
	smartH = frameH - (UpButton._height + DownButton._height);
	PositionBar._yscale = smartH
	DownButton._y = smartH
	smartH2 = smartH - (topH + bottomH);
	Bar.middle._height = smartH2
	Bar.bottom._y = smartH2
	InvisibleThumb._alpha = 0
	bottomH = Bar.bottom._height
	topH = Bar.top._height
	BarH = Bar._height		
	scrollS = BarH/50	
	// delete Unnessesary	
	delete smartheight
	delete smartheight2	
	// functions	
	function getInfo(){
		if(direction == 1){
			//pageH = _parent.page._height;
			pageH = _parent.babyStoryHeight * 14;
			//trace(pageH);
		}else{
			pageH = _parent.babyThumbNav;
		}
		yposB = InvisibleThumb._y;
		ThumbH = InvisibleThumb._height;
	}
	function setOnOff () {
		if ( pageH >= frameH ) {
			var count = 0;
			while ( count < len ) {
				// If you want the Scrollbar to disappear:
				// gObj[count]._visible = true;
				gObj[count]._alpha = 100
				count++;
			}
			Thumb._y = 0;
		} else if ( pageH < frameH ) {
			var count = 0;
			while ( count < len ) {
				// If you want the Scrollbar to disappear:
				//gObj[count]._visible = false;
				gObj[count]._alpha = 0;
				count++;
			}
			//if(direction == 1){
				//_parent.page._y = 100;
			//j}else{
				_parent.page._x = 0;
			//}
			InvisibleThumb._y = 0;
		}
	}
	function calculate(){
		scrollA  = BarH - ThumbH;
		pageP = (yposB / ( scrollA / 100 )) * -( (pageH - BarH) /100 );
		InvisibleThumbH = ( BarH / 100 * ( BarH / ( pageH / 100 ) ) ) - ( topH + bottomH );
		ThumbH = InvisibleThumbH-(topH+bottomH)
	}
	function MoveDown () {
		if (InvisibleThumb._y <= scrollA - scrollS) {
			InvisibleThumb._y = down;
		} else {
			InvisibleThumb._y = scrollA;
		}
	}
	function MoveUp () {
		if (up > scrollS) {
			InvisibleThumb._y = up;
		} else {
			InvisibleThumb._y = 0;
		}
	}
	function stopDragging () {
		InvisibleThumb.stopDrag();
	}
	function gotoScroll () {
		InvisibleThumb._y = (PositionBar.invis._y/100) * scrollA;
		stopDrag ();
	}
	function moveUpDown(){
		up = yposB - scrollS;
		down = yposB + scrollS;
	}
	function apply(){
		if(direction == 1){
			_parent.page._y = pageP;
		}else{
			_parent.babyThumbNav._x = pageP;
		}
		Thumb._y = yposB
		Thumb.middle._height = ThumbH
		Thumb.bottom._y = ThumbH + topH
		InvisibleThumb._height = InvisibleThumbH
		pageHO = pageH
	}	
}
onClipEvent (enterFrame) {
	getInfo()
	setOnOff ()
	calculate()	
	moveUpDown()
	if ( pageH > frameH ) {
		apply()
	}
}
