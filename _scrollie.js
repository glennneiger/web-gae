// Courtesy of SimplytheBest.net - http://simplythebest.net/scripts/
<!--
var scrollingText
function initScroll(contentLength_, clipHeight) {
    var contentLength = contentLength_;
    var contents = new Array(contentLength)
    for (i=0;i<contentLength;i++) {
        contents[i] = "BUZZ" + i
    }
    scrollingText = new ClinkScroll("BANTER",contents)
    scrollingText.init(clipHeight)
    scrollingText.timerOn = true;
    scrollingText.scroll()
}
function Clink_show() {
    var obj = scrollingText.pop(0)
//    window.alert('y : ' + obj.y+ ':h: ' + obj.h + ':top:' + obj.css.top)
//    window.alert('offset_y : ' +  scrollingText.offset_y + ' x:' + scrollingText.offset_x)
}

function stopScroll() {
    scrollingText.stop()
}
function startScroll() {
    if (scrollingText.inited) {
        scrollingText.canScroll = true
        scrollingText.timerOn = true;
        scrollingText.scroll()
     }
}
function ClinkBrowser() {
    this._n = (document.layers)? true:false
    this._ie = (document.all)? true:false
    this._n = (document.layers)? true:false
    this._ie = (document.all)? true:false
    var agt = navigator.userAgent.toLowerCase();
    var ar = new Array();
    ar = agt.split(";")
    var id1 = ar[1].indexOf("e")
    var version = ar[1].substring(id1+1,ar[1].length)
    ar = version.split(".")
    this._major = ar[0]
    this._minor = ar[1]
}
function ClinkScroll(windowLayer, contentLayers) {
    this.browser = new ClinkBrowser()
   // alert(this.browser._ie);
    this.win = new ClinkLayer(windowLayer, null);
    this.length = contentLayers.length
    this.contentLayers = contentLayers
    this.push = Clink_push
    this.pop = Clink_pop
    this.timeinterval = 35
    this.init = Clink_init
    this.stop = Clink_stop
    this.scroll = Clink_scroll
    this.offsetIt = Clink_offsetIt
    this.canScroll = false
    this.inited = false
    this.obj = (this.browser._n)? windowLayer : eval("windowLayer.style")
    eval(this.obj + "=this")
}
function Clink_push(clink_layer, i) {
    if (i > 9) window.alert('Error: this setup works only for 10 scrolling objects')
    switch(i) {
       case 0:
           this.content_0 = clink_layer
           break
       case 1:
           this.content_1 = clink_layer
           break
       case 2:
           this.content_2 = clink_layer
           break
		case 3:
           this.content_3 = clink_layer
           break
        case 4:
           this.content_4 = clink_layer
           break
       case 5:
           this.content_5 = clink_layer
           break
       case 6:
           this.content_6 = clink_layer
           break
       case 7:
           this.content_7 = clink_layer
           break
       case 8:
           this.content_8 = clink_layer
           break
        case 9:
           this.content_9 = clink_layer   
           break
    }
}
function Clink_pop(i) {
    if (i > 9) window.alert('Error: this setup works only for 10 scrolling objects')
    switch(i) {
       case 0:
           return this.content_0 
       case 1:
           return this.content_1
       case 2:
           return this.content_2
       case 3:
           return this.content_3
       case 4:
           return this.content_4
       case 5:
           return this.content_5
       case 6:
           return this.content_6
       case 7:
           return this.content_7
       case 8:
           return this.content_8
       case 9:
           return this.content_9
    }
}
function Clink_stop() { 
    this.canScroll = false
    if (this.timerOn) clearTimeout(this.timerID)
    this.timerOn = false
}
function Clink_init(clipHeight) {
    var band = 0
    this.doOffSet = (this.browser._major <= 4 && !this.browser._n)
    this.offset_x = 0
    for (i=0;i<this.length;i++) {
    var mylayer = this.contentLayers[i]
    var content = new Object()
    content = new ClinkLayer(mylayer, this.win)
    if (this.doOffSet && content.event.scrollWidth > this.offset_x) 
       this.offset_x = content.event.scrollWidth
    band = band + content.h
//    if (!this.browser._n) content.y = content.y - this.win.event.scrollTop
    this.push(content, i)
    }
    this.band = band
    this.clipHeight = this.win.clipHeight
    if (!this.clipHeight || this.clipHeight <= 0) {
       this.clipHeight = clipHeight
    }
    if (this.doOffSet) 
       this.offset_y = this.win.y+this.band+this.clipHeight/2
    this.canScroll = true
    this.inited = true
}
function Clink_scroll() {
    if (!this.canScroll) return 0
    
    for (i=0; i<this.length; i++) {
        var obj = this.pop(i)
        if (obj.y <= -this.clipHeight) {
        obj.reset(305)
        //  obj.reset(this.band)
        } else {
           obj.moveBy(-1)
        }
    }
    if (this.doOffSet) this.offsetIt()
//   repeat
    this.timerID = setTimeout(this.obj+".scroll()", this.timeinterval)
    return 1
}
function Clink_offsetIt() {
    myTop = this.offset_y-this.clipHeight-document.body.scrollTop-25;
	myRight = this.offset_x+119;
	myBot = this.offset_y-document.body.scrollTop-10;
    this.win.css.clip = "rect("+myTop+"px " + myRight+"px " +myBot+"px -100px)";
	//myBot = this.offset_y-document.body.scrollTop;
    //myTop = this.offset_y-this.clipHeight-document.body.scrollTop;
	//myRight = this.offset_x;
    //document.write("rect("+myTop+"px " + myRight+"px " +myBot+"px -100px)");
	//this.win.css.clip = "rect("+myTop+"px " + this.offset_x+"px " +myBot+"px 0px)
	//This section values affect the positioning of the clip on IE4
	//this.win.css.clip = "rect(300,360,430,-100)";
}	
function ClinkLayer(mylayer, myParent) {
    this.name = mylayer
    this.browser = new ClinkBrowser()
    if (this.browser._n) {
         if (myParent != null) {
         this.doc = eval('myParent.doc.document.'+mylayer)
         } else {
         this.doc = eval('document.'+mylayer)
         }
         this.y = this.doc.top
         this.clipHeight = this.doc.clip.height
         this.h = this.doc.clip.height
    } else {
    
    	 //this.doc = eval(mylayer + '.style')
         this.doc = document.getElementById(mylayer).style;
         //this.event = eval("document.all."+mylayer)
         this.event = document.getElementById(mylayer);
         this.css = this.event.style
         this.height = this.event.offsetHeight
         this.y = this.event.offsetTop
         this.h = this.height
         if (myParent == null && this.event.currentStyle) {
         this.clipHeight = parseInt(this.event.currentStyle.clipBottom)
         } else {
         this.clipHeight = 0
         }
    }
    this.moveBy = Clink_moveBy
    this.moveTo = Clink_moveTo
    this.reset = Clink_reset
}
function Clink_reset(y) {
    this.moveTo(y)
}
function Clink_moveTo(y) {
        this.y = y
        if (this.browser._n) {
        this.doc.top = this.y
        } else {
        this.css.top = this.y
        }
}
function Clink_moveBy(dy) {
        this.y += dy
        if (this.browser._n) {
        this.doc.top = this.y
        } else {
        this.css.top = this.y
        }
}
//--
finish=0;
function init() {goor = 0;
  isLoaded = true;
}
function DisplayMsg(msg) {
  status=msg;
  document.ReturnValue = true;
  }
//--
//-->