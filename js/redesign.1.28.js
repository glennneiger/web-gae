function hideeleid(id){
	 
	if($(id)){
			$(id).hide();
	}else{		 
		return true;
	}
}


function cartSubmit(){
	var url = host+'/subscription/_carthandler.php';
	var pars = 'action=submitcart';
	var postAjax= new Ajax.Request(url, {method: 'post',
		parameters: pars,
		onComplete:function sumitcartaction(req)
		{
 		  var response = req.responseText;
		
	}});	

}

function alreadyCartCheckout(str){
	iboxclose();
	window.location.href= str +"/subscription/register/";
}

function callCancelfromInline(to,frm,viacase,orderItemType){
	var url = host+"/subscription/confirm.php?showcall=showcall&frm="+frm+"&to="+to+"&orderItemType="+orderItemType;	
	init_ibox('cartconfirm',url);
}

function cancelViaProd(frm,to,relocate,arrcase,orderItemType){
	var url = host+'/subscription/_carthandler.php';
	var pars="action=editProdSubs&frm="+frm+"&to="+to+"&arrcase="+arrcase+"&orderItemType="+orderItemType;
	var myAjax = new Ajax.Request(url,
	{method: 'post',
	parameters: pars,
	onComplete: function(req)
	{
		if(relocate=='relocate'){
			iboxclose();
			window.location.href= host +"/subscription/register/";
		}
		else
		{
			rendervalidatedcart(req);
		}
	}
	});
	
}

function cancelViaAdsFreeProd(frm,to,relocate,arrcase,orderItemType){
	var url = host+'/subscription/_carthandler.php';
	var pars="action=editProdSubs&frm="+frm+"&to="+to+"&arrcase="+arrcase+"&orderItemType="+orderItemType;
	var myAjax = new Ajax.Request(url,
	{method: 'post',
	parameters: pars,
	onComplete: function(req)
	{	
		return true;
	}
	});
	
}

function validateloggedcart(){
	var url = host+'/subscription/_carthandler.php'; 
	var pars = 'action=validateloggedcart';
	var postAjax= new Ajax.Request(url, {method: 'post', parameters: pars, onComplete:rendervalidatedcart}); 
	 
}

function rendervalidatedcart(req){	

		if($("yourcart")){
			$("yourcart").innerHTML=req.responseText;
		}
		 
}

function checkcart(id, orderClassid, orderItemType,productName,producttype,eventtype){
	if($("trialshowbtn-"+id))
	{
	$("trialshowbtn-"+id).hide();
	$("trialhidebtn-"+id).show();
	}
	var url = host+'/subscription/_carthandler.php'; 
	var pars = 'action=checkcart&sdefid='+id+'&oc_id='+orderClassid +'&orderItemType='+orderItemType;	
	pars +='&productName=' + productName;
	pars +='&producttype=' + producttype;
	pars +='&eventtype=' + eventtype;
	
	var postAjax= new Ajax.Request(url, {method: 'post', parameters: pars,
			onLoading:document.body.style.cursor='progress',
			onComplete:function usercartopt(req){
				document.body.style.cursor='';
				if($("trialshowbtn-"+id))
				{
				$("trialshowbtn-"+id).show();
				$("trialhidebtn-"+id).hide();
				}
				var res = req.responseText; 
				var arr = res.split(",") ;	
				if( (arr['0']==arr['1']) && (arr['2']=='add_to_cart' )){						 
					var url_1 = host+'/subscription/_carthandler.php';
					var pars_1 = 'action=addtocart&sdefid='+id+'&oc_id='+orderClassid+'&orderItemType='+orderItemType;
					pars_1 +='&productName=' + productName;
					pars_1 +='&producttype=' + producttype;
					var postAjax_1= new Ajax.Request(url_1, {method: 'post', parameters: pars_1, onComplete:displaycart});
				}
				else if(arr['2']=='trial_not_allowed'){
					var add=arr['1'];
					addProductChoice(add,orderItemType);
					return false;
				}
				else if(arr['2']=='trial_already_in_via'){
					var add=arr['1'];
					addProductChoice(add,orderItemType);
					return false;
				}
				else{
					var url_2 = host+'/subscription/confirm.php?frm='+arr['0']+'&to='+arr['1']+'&case='+arr['2']+'&orderItemType='+orderItemType;
					init_ibox('cartconfirm',url_2);	
					return false;
				}		
		
	}});
	return false;
	 	
	 
}
function toaddcheckcart(id, orderClassid, orderItemType,productName,producttype,eventtype){	

	var ids  = id.split('-');
	var orderClassids  = orderClassid.split('-');
	var orderItemTypes  = orderItemType.split('-');
	var redirect = false;
	

	var url_1 = host+'/subscription/_carthandler.php'; 
	var pars_1 = 'action=checkcart&sdefid='+ids['0']+'&oc_id='+orderClassids['0'] +'&orderItemType='+orderItemTypes['0'];	
	pars_1 +='&productName=' + productName;
	pars_1 +='&producttype=' + producttype;
	pars_1 +='&eventtype=' + eventtype;
	var postAjax_1= new Ajax.Request(url_1, {method: 'post', parameters: pars_1,
			onComplete:function (req_1){
			var res_1 = req_1.responseText; 
			var arr_1 = res_1.split(",") ;
			if( (arr_1['0']==arr_1['1']) && (arr_1['2']=='add_to_cart' )){	
					redirect = true;
					var url_1_1 = host+'/subscription/_carthandler.php';
					var pars_1_1 = 'action=addtocart&sdefid='+ids['0']+'&oc_id='+orderClassids['0']+'&orderItemType='+orderItemTypes['0'];
					var postAjax_1_1= new Ajax.Request(url_1_1, {method: 'post', parameters: pars_1_1, onComplete:function(){ 
							var url_2 = host+'/subscription/_carthandler.php'; 
							var pars_2 = 'action=checkcart&sdefid='+ids['1']+'&oc_id='+orderClassids['1'] +'&orderItemType='+orderItemTypes['1'];	
							var postAjax_2= new Ajax.Request(url_2, {method: 'post', parameters: pars_2,
							onComplete:function (req_2){
							var res_2 = req_2.responseText; 
							var arr_2 = res_2.split(",") ;
							if( (arr_2['0']==arr_2['1']) && (arr_2['2']=='add_to_cart' )){						 
								var url_2_2 = host+'/subscription/_carthandler.php';
								var pars_2_2 = 'action=addtocart&sdefid='+ids['1']+'&oc_id='+orderClassids['1']+'&orderItemType='+orderItemTypes['1'];
								var postAjax_2_2= new Ajax.Request(url_2_2, {method: 'post', parameters: pars_2_2, onComplete:function(req_2_2){
									if(!redirect){
										var url_3 = host+'/subscription/confirm.php?frm='+arr_1['0']+'&to='+arr_1['1']+'&case='+arr_1['2']+'&orderItemType='+orderItemTypes['0'];
										init_ibox('cartconfirm',url_3);
	}else{
										if($("yourcart")){
												$("yourcart").innerHTML=req_2_2.responseText;
	}

										window.location.href= host +"/subscription/register/";
									}


								}});
							}else{									
								if(!redirect){
									var url_3 = host+'/subscription/confirm.php?frm='+arr_1['0']+'&to='+arr_1['1']+'&case='+arr_1['2']+'&orderItemType='+orderItemTypes['0'];
									init_ibox('cartconfirm',url_3);
								}else{
									window.location.href= host +"/subscription/register/";
									//var url_3 = host+'/subscription/confirm.php?frm='+arr_2['0']+'&to='+arr_2['1']+'&case='+arr_2['2']+'&orderItemType='+orderItemTypes['1'];
									//init_ibox('cartconfirm',url_3);								

								}

							}

							}});
					}});
					
			} 	else{
					var url_3 = host+'/subscription/confirm.php?frm='+arr_1['0']+'&to='+arr_1['1']+'&case='+arr_1['2']+'&orderItemType='+orderItemTypes['0'];
					init_ibox('cartconfirm',url_3);



}

	}});

 

	 
}
function toaddcheckcart_1(id, orderClassid, orderItemType){	
	var ids  = id.split('-');
	var orderClassids  = orderClassid.split('-');
	var orderItemTypes  = orderItemType.split('-');
	var redirect = false;
	

	var url_1 = host+'/subscription/_carthandler.php'; 
	var pars_1 = 'action=checkcart&sdefid='+ids['0']+'&oc_id='+orderClassids['0'] +'&orderItemType='+orderItemTypes['0'];	
	var postAjax_1= new Ajax.Request(url_1, {method: 'post', parameters: pars_1,
			onComplete:function (req_1){
			var res_1 = req_1.responseText; 
			var arr_1 = res_1.split(",") ;
			if( (arr_1['0']==arr_1['1']) && (arr_1['2']=='add_to_cart' )){						 
					var url_1_1 = host+'/subscription/_carthandler.php';
					var pars_1_1 = 'action=addtocart&sdefid='+ids['0']+'&oc_id='+orderClassids['0']+'&orderItemType='+orderItemTypes['0'];
					var postAjax_1_1= new Ajax.Request(url_1_1, {method: 'post', parameters: pars_1_1, onComplete:rendervalidatedcart});
					redirect = true;
			} 
				var url_2 = host+'/subscription/_carthandler.php'; 
				var pars_2 = 'action=checkcart&sdefid='+ids['1']+'&oc_id='+orderClassids['1'] +'&orderItemType='+orderItemTypes['1'];	
				var postAjax_2= new Ajax.Request(url_2, {method: 'post', parameters: pars_2,
				onComplete:function (req_2){
				var res_2 = req_2.responseText; 
				var arr_2 = res_2.split(",") ;
				if( (arr_2['0']==arr_2['1']) && (arr_2['2']=='add_to_cart' )){						 
					var url_2_2 = host+'/subscription/_carthandler.php';
					var pars_2_2 = 'action=addtocart&sdefid='+ids['1']+'&oc_id='+orderClassids['1']+'&orderItemType='+orderItemTypes['1'];
					var postAjax_2_2= new Ajax.Request(url_2_2, {method: 'post', parameters: pars_2_2, onComplete:displaycart});
				}else{		
					if(!redirect){
						var url_3 = host+'/subscription/confirm.php?frm='+arr_2['0']+'&to='+arr_2['1']+'&case='+arr_2['2']+'&orderItemType='+orderItemTypes['1'];
						init_ibox('cartconfirm',url_3);
					}else{
						window.location.href= host +"/subscription/register/";

					}

				}

				}});			
		
	}});

 

	 
}
function confirmchoice(flag,add,remove,orderItemType){	
	if(flag){ 		 		  
		var url = host+'/subscription/_carthandler.php';
		var pars = 'action=confirmcart&remove='+remove+'&add='+add+'&orderItemType='+orderItemType;//alert(pars);
		var postAjax= new Ajax.Request(url, {method: 'post', parameters: pars, onComplete:displaycart});	
	}
			
}
function confirmchoicelogged(flag,add,remove,cancel,confirmDivId,CancelDivId){	
	if(flag){ 
		 if(cancel=='true'){ 
		 	 if($(confirmDivId)){ 
				$(confirmDivId).hide();
			}
			if($(CancelDivId)){
				$(CancelDivId).show();
			}   
		 		return true;
		 }else{			  
			var url = host+'/subscription/_carthandler.php';//alert(url);
			var pars = 'action=confirmcart&remove='+remove+'&add='+add;
			var postAjax= new Ajax.Request(url, {method: 'post', parameters: pars,
			onComplete:function dispaypage(req){ 
			if($("yourcart")){
			$("yourcart").innerHTML=req.responseText;
		}	iboxclose();
			window.location.href= host +"/subscription/register/";
			
			
		
	}});	
		 }
			
	}else{
		return true;
	}
	 
}
function addtocart(id){		
	var url = host+'/subscription/_carthandler.php';//alert(url);
	var pars = 'action=addtocart&sdefid='+id;
	var postAjax= new Ajax.Request(url, {method: 'post', parameters: pars, onComplete:displaycart});	
}

function removefrmcart(id, orderItemType,redirectCart){	

	var url = host+'/subscription/_carthandler.php';
	var pars = 'action=deletecart&sdefid='+id+'&orderItemType='+orderItemType+'&redirectCart='+redirectCart;
	var postAjax= new Ajax.Request(url, {method: 'post',
		parameters: pars,
		onComplete:function updatecart(req)
		{
			if(req.responseText==""){
				window.location.href=host+"/subscription/";
			}else{
				window.location.reload();
				/*$("yourcart").innerHTML=req.responseText;				
				var parsProd = 'action=updateProductDiv';
				var myAjax = new Ajax.Request(url, {method: 'post',
								parameters: parsProd,
								onComplete:function updateproduct(req){
										$("product-display").innerHTML=req.responseText;
									}
								});
				
				var parsProdStep3 = 'action=updateProductBillingDiv';
				var myAjax = new Ajax.Request(url, {method: 'post',
								parameters: parsProdStep3,
								onComplete:function updateproduct(req){
										$("billingProductInfo").innerHTML=req.responseText;
									}
								});*/
			}
	}});	
	 
}

function logAddtoCart(redirectURL)
{		
	var url = host+'/subscription/register/logAddToCart.php';
	var pars="url="+redirectURL;
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,
	onComplete:finish_logAddtoCart});
}
function finish_logAddtoCart(req)
{
	window.location.href=req.responseText;
}

function displaycart(req){		
			
	if($('purchase_from') && $('purchase_from').value == 'housing_market')
	{
		window.location.href=""; 		
	}
	else
	{
	var url;
	if(req.responseText==""){
		url= host +"/subscription/register/";	
	}else{
		url= host +"/subscription/register/billing/";
	}
	logAddtoCart(url);
}
}

function showibox(){
	var url="confirm.html";
	init_ibox('yes',url);
}
/*function for CP watchlist*/
function getMarketWatchlist(userid,pageName){
	var url = host + '/subscription/watchlist.htm';
	var listticker=1;
	var pars = 'listticker=' + listticker + '&userid=' + userid + '&pageName=' + pageName;
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars, onComplete:getResultMarketWatch});
}

function getResultMarketWatch(req){
	if(req.responseText){
		var post;
		post = req.responseText;		
		$("showwatchlist").innerHTML=post;
	}
}

function getPaginationWatchlist(userid,offset){
	var url = host + '/subscription/watchlist.htm';
	var youraccount=0;
	var pars = 'userid=' + userid + '&offset=' + offset + '&youraccount=' + youraccount;
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars, onComplete:getMarketWatchListData});
}

function getMarketWatchListData(req){
	if(req.responseText){
		var post;
		var res = req.responseText;
		//res = eval(req.responseText);
		//alert(res);
		$("tickerlist").innerHTML=res;
	}
}

function getYourAccount(hide){
    var url =host + '/subscription/watchlist.htm';
	var youraccount=1;
	var pars ='youraccount=' + youraccount + '&hide=' + hide;
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars, onComplete:getResultYourAccount});
}

function getResultYourAccount(req){
	if(req.responseText && $("youraccount")){ 
		$("youraccount").innerHTML=req.responseText;
	}
}

function searchtextboxClear() {
    $searcval=$F('search');
	if(trim($searcval)=='Search Keywords or Symbols' || trim($searcval)=='Stock Symbols' ) {
		$('search').clear();
	}
	window.location.href= host + "/library/search.htm?q="+$F('search');
}

/*strid - search box element id
profid - professor id*/
function qpsearchalert(strid,profid,frm,oid){  
		window.location.href= host + "/library/search.htm?q="+$F(strid)+"&frm="+frm+"&oid="+oid;
}

function optionsearchalert(strid,profid,frm,oid){  
         var strsearch=1;
		window.location.href= host + "/library/search.htm?q="+$F(strid)+"&oid="+oid+"&search="+strsearch;
}

function searchalert(strid,profid,frm,oid){  
        var strsearch=1;
		window.location.href= host + "/library/search.htm?q="+$F(strid)+"&oid="+oid+"&search="+strsearch+"&contrib_id="+profid;
}

function showHidediv(idname,className,id,num)
{
	for(var i=1;i<=num;i++)
	{
		if(document.getElementById(idname+i)){
			if(i==id)
			{
				document.getElementById(idname+i).style.display='block';
				document.getElementById(className+i).className='selected';	
			}
			else
			{	
				document.getElementById(idname+i).style.display='none';
				document.getElementById(className+i).className='';
			}
		}
	}
}

function voteR(item_id,rating_type,voter_id,object_id,object_name)
{ 
	var url = host+'/mvtv/vote.php';	
	var pars="item_id="+item_id+"&rating_type="+rating_type+"&voter_id="+voter_id+"&object_id="+object_id+"&action=rate&object_name="+object_name;
	var myAjax4 = new Ajax.Request(url, 
						{method: 'post',
						parameters: pars,						
						onComplete: function(req) 
									{										
										//alert(req.responseText);
										$('vote').innerHTML = req.responseText;
						            }
		               });	
}

function votefeedR(item_id,rating_type,voter_id,object_id,object_name)
{ 
	var url = host+'/mvtv/vote.php';	
	var pars="item_id="+item_id+"&rating_type="+rating_type+"&voter_id="+voter_id+"&object_id="+object_id+"&action=rate&object_name="+object_name;
	var myAjax4 = new Ajax.Request(url, 
						{method: 'post',
						parameters: pars,						
						onComplete: function(req) 
									{										
										//alert(req.responseText);
										$('vote'+item_id).innerHTML = req.responseText;
						            }
		               });	
}

function editVote(item_id)
{
	$('vote_rating_edit'+item_id).hide();
	$('vote_rating'+item_id).show();
}

function get_element(id) 
{
	if (document.getElementById)
		return document.getElementById(id);
	else if (document.all)
		return document.all[id];
	return null;
}


function bio(num) 
{
	var bios = get_element('div-bios');
    if (bios) 
	{
		var arrBio = bios.getElementsByTagName('div');
		if (arrBio)
        for (var i = 0;  i < arrBio.length;  i++) 
		{
          var id = arrBio[i].id;
          if (id && id.length && (id.substr(0, 7) == 'div-bio'))
            arrBio[i].style.display = (parseInt(id.substr(7, id.length)) == num) ? 'block' : 'none';
        }
    }
  }
  
function showSmallProgress(id) { // puts spinner in specified div
var x = $(id);
x.innerHTML = '<div id="showprogress"><table border="0" align="center"><tr><td style="text-align:right;padding-top:50px;padding-bottom:0px;">Loading...</td><td style="text-align:left;padding-top:50px; padding-bottom:0px;"><img src="'+ image_server +'/images/community_images/smallspinner.gif"></td></tr></table></div>';
}
  
  
function getPhotos(divid,url)
{
	height=60;
	var  pars= "";
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,										
										onLoading:showSmallProgress(divid),
										
										onComplete:function(req)
											 {
													$(divid).innerHTML = req.responseText;
													//document.getElementById("loder").style.display="none";
											 }
											});
}
// for professor module 
function select_td(id,commaseparated,count)
{
	var id = id;  
	var commaseparated  = commaseparated;
	var count = count;
	var n=navigator.appName;
	 
	  if(n=="Netscape")
    {
     var display ="block";
    }
    else
    {
     var display ="block";
     }
	 var mytool_array=  new Array();
	 mytool_array= commaseparated.split("~");
    for (i=0;i<count;i++)
    {
		if(mytool_array[i]== id)
		{ 
			document.getElementById('Pof'+mytool_array[i]).style.display = "block";
		}
		else
		{
			
			document.getElementById('Pof'+mytool_array[i]).style.display = "none";
			document.getElementById('Pof_id').style.display = "none";
		}

    }
 
}

function addLoadEventMarket(func) { 
	var oldonload = window.onload; 
	if (typeof window.onload != 'function') { 
		window.onload = func; 
	} else { 
		window.onload = function() { 
		if (oldonload) { 
			oldonload(); 
		} 
		func(); 
		} 
	} 
} 

function  loadMarket(){
	var url = host+'/stocks/marketdata.php';
	var pars="module=summary,watchlist,currency,marketmovers";
	var myAjax = new Ajax.Request(url,
	{method: 'post',
	parameters: pars,
	onComplete: function(req)
		{ 
			var marketArray = eval('(' + req.responseText + ')');	
			for(i=0;i<marketArray.length;i++)
			{
				var startIndex = marketArray[i].search("</style>");
				var showStr = marketArray[i].substr(startIndex+8);
				$('market'+(i+1)).innerHTML = showStr;//req4.responseText;
			}
		}
	});
}
function addLoadEventWatchList(func,sValue,divId) {  
	var oldonload = window.onload; 
	if (typeof window.onload != 'function') { 
		window.onload = func(sValue,divId);
	} else { 
	
		window.onload = function() { 
		if (oldonload) { 
			oldonload(); 
		} 
		func(sValue,divId); 
		} 
	} 
} 

function addLoadEventStocks(func,sValue,divId) { 
	var oldonload = window.onload; 
	if (typeof window.onload != 'function') { 
		window.onload = func(sValue,divId); 
	} else { 
		window.onload = function() { 
		if (oldonload) { 
			oldonload(); 
		} 
		func(sValue,divId); 
		} 
	} 
} 
function loadStockQuote(sValue,divId){ 
	var url_1 = host+'/stocks/marketdata.php';
	var pars_1="module=stockquote&value="+sValue;  
	var myAjax_1 = new Ajax.Request(url_1,
	{method: 'post',
	parameters: pars_1,
	onComplete: function(req)
	{	
		var marketArray = eval('(' + req.responseText + ')');	
		var divArray = divId.split(',');
		for(i=0;i<marketArray.length;i++)
		{
			var startIndex = marketArray[i].search("</style>");
			var showStr = marketArray[i].substr(startIndex+8);
			$(divArray[i]).innerHTML = showStr;//req4.responseText;
		}				
	}
	});
}

function addLoadEventQunintVideo(func,sValue1,sDim1,sDim2,divId) {  
	var oldonload = window.onload; 
	if (typeof window.onload != 'function') { 
	    window.onload= function(){func(sValue1,sDim1,sDim2,divId);}
		<!--window.onload = func; -->
	} else { 
		window.onload = function() { 
		if (oldonload) { 
			oldonload(); 
		} 
		func(sValue1,sDim1,sDim2,divId);
		  
		} 
	} 
} 


function validatePromoCode(divId,errDiv,textBoxMsg){
	var msg ='';
	if(trim($F('promocode'))=='' || $F('promocode')  ==trim(textBoxMsg) ) {
		//msg = 'Please enter valid Promo Code';
		msg = 'The promo code you are attempting to use is either invalid, expired or not applicable to this order.';
	}
	if(msg!=''){
		$(errDiv).show();
		$(errDiv).innerHTML=msg;
		return false;
	}else{
		$(errDiv).style.display=='block';
		// $(errDiv).innerHTML='';
		// $(errDiv).hide();
		return true;
	}
	
}
function applyPromoCode(errDiv,textBoxMsg,pageName){
	
	if(validatePromoCode('promocode',errDiv,textBoxMsg)){
		var pcode = $F('promocode');	
		var url = host+'/subscription/_carthandler.php';
		var pars="action=validatePromoCode&pCode="+encodeURIComponent(pcode);  
		var myAjax = new Ajax.Request(url,
		{method: 'post',parameters: pars,onComplete: function(req){		 
			var res = req.responseText;
			if(res=='error'){				
				var url_1 = host+'/subscription/_carthandler.php';
				var pars_1="action=default"+"&pageName="+pageName;  
				var myAjax_1 = new Ajax.Request(url_1,
				{method: 'post',parameters: pars_1,onComplete: function(req_1){	
					 if($("yourcart")){
						$("yourcart").innerHTML=req_1.responseText;
					}
					//var msg = 'Please enter valid Promo Code';
					var msg = 'The promo code you are attempting to use is either invalid, expired or not applicable to this order.';
					$(errDiv).show();
					$(errDiv).innerHTML=msg;
				}
				});

			}else{
				if($("yourcart")){
					$("yourcart").innerHTML=req.responseText;
				}
				 
			}
		 
		}}
		); 

	}else{
		return false;
	}

}

function resetPromoCode(){
	var url = host+'/subscription/_carthandler.php';
	var pars="action=resetPromoCode";
	var myAjax = new Ajax.Request(url,
	{method: 'post',
	parameters: pars,
	onComplete: function(req)
	{	
	 if($("yourcart")){
		$("yourcart").innerHTML=req.responseText;
	}
	}
	});
}


 
function get_register_key_Scott(event){
	var keyVal=event.keyCode;//  for IE 
	if(keyVal==undefined){
		keyVal=event.which; // for Firefox/Opera/Netscape
	}
	
	if(keyVal==13){ // Enter key pressed
		validateScottBuzz('statusmsgstep1');
	}
}
	function validateScottBuzz(errordiv){
	var status=true;
	if($('firstname').value==''){
		$(errordiv).innerHTML='Enter First name.';
		
		$('firstname').select(); 
		return false; 
	}
	if(validateAlphaFieldsOnly(errordiv,'firstname','First name')==false){
		
		$('firstname').select();
		return false;
	}

	if($('lastname').value==''){
		$(errordiv).innerHTML='Enter Last name.';
		
		$('lastname').select();
		return false;
	}

	if(validateAlphaFieldsOnly(errordiv,'lastname','Last name')==false){
		
		$('lastname').select();
		return false;
	}

	if(iboxisValidEmail(errordiv,'viauserid')==false){
		
		$('viauserid').select();
		return false;
	}
	if(iboxisValidPasswordRegistration(errordiv,'viapass')==false){
		
		$('viarepass').select();
		return false;
	}
	 
	/***	if($('viapass').value!=$('viarepass').value){
		$(errordiv).innerHTML='Password and Confirm password does not match.';		
		
		$('viarepass').select();
		return false;
	} ***/
	if($("viapass").value.toUpperCase()=='PASSWORD'){
		$(errordiv).innerHTML="Please Enter Any other Password Except 'Password'";
		
		$('viarepass').select();
		return false;
	}	
	if($("terms").checked==false){
		$(errordiv).innerHTML="You did not agree with our privacy terms.";
		
		$('terms').select();
		return false;
	}
	buzzScottregistration();
}
function buzzScottregistration(){
	//checkcart('41,'9','SUBSCRIPTION');
	var url = host+'/subscription/register/registration_ajax.php';	
	var pars="type=scottBuzzRegister";
	pars+='&uid='+$('viauserid').value;
	pars+='&pwd='+$('viapass').value;
	pars+='&firstname='+$('firstname').value;
	pars+='&lastname='+$('lastname').value;
	pars+="&rememeber=1";
	/**if($('viauserremember').checked==true){
		pars+="&rememeber=1";
	}
	else{
		pars+="&rememeber=0";	
	}	
	if($('alerts').checked==true){
		pars+='&alerts=1';
	}
	else{
			pars+='&alerts=0';
	}***/
	/*
	if($('referralcode').value!=''){
		pars+='&refcode='+$('referralcode').value;
	}
	*/
	$('statusmsgstep1').innerHTML='';
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,
	 
	onComplete:function(req){var post = eval('('+req.responseText+')');if(post.status==false)$('statusmsgstep1').innerHTML=post.msg; else $('statusmsgstep1').innerHTML='You have sucessfully subscribed Buzz Banter.'}});

}
 
function navigate(){
	var myVal;
	var oSel = document.getElementById("navsel");
	myVal = oSel.options[oSel.selectedIndex].value;
	if(myVal != "")
	{
		location.href = myVal;
	}
}  

function googleAnalytics(profileid,track){
	try {
		pageTracker._trackPageview(track);
		ga('send', 'pageview', track);
	}catch(err) {}
}

function sendActivation(){
	$('showactivatemsg').innerHTML="";
	var txtval=$('txtemail').value;
	if(txtval!=='Email Address'){
	
	if(!(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/.test(txtval)))
	{
		$('showactivatemsg').innerHTML='Not a valid E-mail id "'+txtval+'"';
		return false;
	}
	
	var url=host + '/subscription/activate_mod.php';	
	var pars='email=' + txtval;
	var postAjax= new Ajax.Request(url,{method:'post',parameters:pars,onComplete: function getResult(req){
		$('showactivatemsg').innerHTML=req.responseText;
	}});	
	}
	
}

function openoptiondiv(divid){
	if($(divid).style.display=='none'){
		document.getElementById(divid).style.display="block";
	}else{
		document.getElementById(divid).style.display="none";
	}
}

function setValues(divid,value,hidediv,settitle){
	document.getElementById(hidediv).style.height="50";
	document.getElementById(divid).innerHTML=settitle;
	document.getElementById(hidediv).value=value;
	hideoptiondiv(hidediv);
}

function hideoptiondiv(hidediv){
	document.getElementById(hidediv).style.display="none";
}


function expandAd(){
	if(document.getElementById("ad_427765")!==null){
		document.getElementById("leatherboard-container").style.height ="400px";
	}
}


function collapseAd(){
	if(document.getElementById("ad_427765")!==null){
		document.getElementById("leatherboard-container").style.height ="90px";
	}
}

function addProductChoice(add,orderItemType){
	var url = host+'/subscription/_carthandler.php';
	var pars = 'action=addProduct&add='+add+'&orderItemType='+orderItemType;//alert(pars);
	var postAjax= new Ajax.Request(url, {method: 'post', parameters: pars, onComplete:displaycart});
}

function getUserOrderStatus(viaId){
	var url = host+'/subscription/orders-status.php';
	var pars = 'viaId='+viaId;
	var postAjax= new Ajax.Request(url, {method: 'post', parameters: pars, onComplete:{}});
}
function checkcartHousingMarketReport(id, orderClassid, orderItemType,productName,producttype,eventtype){
	if($("trialshowbtn-"+id))
	{
	$("trialshowbtn-"+id).hide();
	$("trialhidebtn-"+id).show();
	}
	var subdefId=id;
	var orderItemType=orderItemType;
	var oc_id=orderClassid;
	var productName=productName;
	
	updateCartCheckBox(subdefId, orderItemType, oc_id, productName);
}

function updateCartCheckBox(subdefId, orderItemType, oc_id, productName){
	var removeProduct=0;
	if(!$('chkIssue-'+subdefId).checked)
	{
		removeProduct=1;
	}
	var url = host+'/subscription/_carthandler.php';
	var pars="action=updateCartAddmultipleProduct&subdefId="+subdefId+"&orderItemType="+orderItemType+"&oc_id="+oc_id+"&removeproduct="+removeProduct;
	var myAjax = new Ajax.Request(url,{ 
		method: 'post', 
		parameters: pars,
		onLoading:loadingProduct('issueerror'),
		onComplete: function(req) 
		{
			if($('issueerror').value != ""){
				$('issueerror').style.display='block';
				$('issueerror').innerHTML="&nbsp;";
				
			}
			rendervalidatedcart(req); 

		} 
	});
}

function showCooperPostsByMonths(month,year){
	var combo = month+year;
	if(jQuery('#mnth'+combo+'Catg').is(':visible')){
		return false;
	}else{
		jQuery('*.innerCat').fadeOut(500);
		jQuery.ajax({
			type : "POST",
			url : host+"/cooper/archive.php",
			data : "month="+month+"&year="+year,
			error : function(){},
			success : function(res){
				if(res){
					jQuery('#mnth'+combo+'Catg').fadeIn(500);
					jQuery('ul#showMonthlyWisePost').html(res);
				}
			}
		});
	}
}

function displayCooperLeavingWindow(productUrl){
	startTimer = setTimeout(function(){
		window.location.target = "_blank";
		window.location.href = productUrl;
	},5000);
}

function closeCooperFancyBox(){
	jQuery.fancybox.close();
}