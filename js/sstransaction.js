var xmlHttp
var vardiv
var height=0;

function validateentry(){
	valid = true;
	var symbolval=document.getElementById('symbol').value;
	var typeval=document.getElementById('type').selectedIndex;
	var dateval=document.getElementById('demo2').value;
	var sharesval=document.getElementById('shares').value;
	var priceval=document.getElementById('price').value;
	var notesval=document.getElementById('notes').value;
				   
	if ( symbolval == "" )
	{
		alert ( "Please fill in the 'Symbol' box." );
		document.getElementById('symbol').focus();
		valid = false;
	}
	else if ( dateval == "" )
	{
		alert ( "Please fill in the 'date' box." );
		valid = false;
	}	else if (sharesval == "")
	{
		alert ( "Please fill in the 'shares' box." );
		document.getElementById('shares').focus();
		valid = false;
	}
	else if (isNaN(sharesval)||(sharesval==0))
	{
		alert ( " Invalid 'shares' Entry." );
		document.getElementById('shares').focus();
		valid = false;
	}else if ( priceval == "" )
	{
		alert ( "Please fill in the 'price' box." );
		document.getElementById('price').focus();
		valid = false;
	}else if ( isNaN(priceval)||(priceval==0))
	{
		alert ( "Invalid 'price' Entry." );
		document.getElementById('price').focus();
		valid = false;
	}	
	
	if(valid==true){
		document.getElementById('mode').value='save';
		var modeval=document.getElementById('mode').value;
		if(modeval=='save'){
			inserttransaction(symbolval,typeval,dateval,sharesval,priceval,notesval,modeval);
		}
	}
	if(valid==false){
			document.getElementById('mode').value='';
			return valid;
	}
}

function inserttransaction(symbolval,typeval,dateval,sharesval,priceval,notesval,modeval,editval)
{
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Your browser does not support AJAX!");
		return;
	}
	if(editval!='edit'){
		var url=admin_server + '/admin/ss/transaction_handle.htm?text_pass='+modeval+'&symbolval='+symbolval+'&typeval='+typeval+'&dateval='+dateval+'&sharesval='+sharesval+'&priceval='+priceval+'&notesval='+notesval;
		document.getElementById('mode').value='';
		document.getElementById('symbol').value='';
		document.getElementById('type').selectedIndex=0;
		document.getElementById('demo2').value='';
		document.getElementById('shares').value='';
		document.getElementById('price').value='';
		document.getElementById('notes').value='';
	}else if(editval=='edit'){
		var url= admin_server + '/admin/ss/transaction_handle.htm?text_pass='+modeval+'&symbolval='+symbolval+'&typeval='+typeval+'&dateval='+dateval+'&sharesval='+sharesval+'&priceval='+priceval+'&notesval='+notesval+'&editaddform='+editval;
		document.getElementById('addmode').value='';
		document.getElementById('symbol').value='';
		document.getElementById('comotype').selectedIndex=0;
		document.getElementById('demo2').value='';
		document.getElementById('shares').value='';
		document.getElementById('price').value='';
		document.getElementById('notes').value='';
	}
		if(editval!='edit'){
		completinserttransaction(url);
		}else if(editval=='edit'){
		complettransactionedit(url,editval);
		}
	}


function complettransactionedit(url,editval){
	contntdiv='trans_detail';
	height=10;
	xmlHttp.open("GET",url,true);
	xmlHttp.onreadystatechange=function() { stateChange(contntdiv,0); };
	xmlHttp.send(null);
}

function completinserttransaction(url)
{
	contntdiv='trans_detail';
	height=10;
	xmlHttp.open("GET",url,true);
	xmlHttp.onreadystatechange=function() { stateChange(contntdiv,0); };
	xmlHttp.send(null);
}
function validateentryforedit(){
	valid = true;
	var symbolval=document.getElementById('symbol').value;
	var typeval=document.getElementById('comotype').selectedIndex;
	var dateval=document.getElementById('demo2').value;
	var sharesval=document.getElementById('shares').value;
	var priceval=document.getElementById('price').value;
	var notesval=document.getElementById('notes').value;

	if ( symbolval == "" )
	{
		alert ( "Please fill in the 'Symbol' box." );
		document.getElementById('symbol').focus();
		valid = false;
	}
	else if ( dateval == "" )
	{
		alert ( "Please fill in the 'date' box." );
		valid = false;
	}
	else if ( sharesval == "" )
	{
		alert ( "Please fill in the 'shares' box." );
		document.getElementById('shares').focus();
		valid = false;
	}
	else if (isNaN(sharesval)||(sharesval==0))
	{
		alert ( " Invalid 'shares' Entry." );
		document.getElementById('shares').focus();
		valid = false;
	}
	else if ( priceval == "" )
	{
		alert ( "Please fill in the 'price' box." );
		document.getElementById('price').focus();
		valid = false;
	}
	else if ( isNaN(priceval)||(priceval==0))
	{
		alert ( "Invalid 'price' Entry." );
		document.getElementById('price').focus();
		valid = false;
	}
	if(valid==true){
		document.getElementById('addmode').value='save';
		var modevals=document.getElementById('addmode').value;
		if(modevals=='save'){
			inserttransaction(symbolval,typeval,dateval,sharesval,priceval,notesval,modevals,"edit");

		}
	}
	if(valid==false){
		document.getElementById('addmode').value='';
		return valid;
	}
}

function makenextlinks1(start,end){
	links='nexttrans';
	var url=admin_server + '/admin/ss/transaction_handle.htm?text_pass='+links+'&start='+start+'&end='+end;
	displynextprev(url);
}

function makeprevlinks1(start,end){
	links='prevtrans';
	var url=admin_server + '/admin/ss/transaction_handle.htm?text_pass='+links+'&start='+start+'&end='+end;
	displynextprev(url);
}
function makenextlinks(start,end){
		links='next';
		var url=admin_server + '/admin/ss/transaction_handle.htm?text_pass='+links+'&start='+start+'&end='+end;
		displynextprev(url);
	}

	function makeprevlinks(start,end){
		links='prev';
		var url= admin_server + '/admin/ss/transaction_handle.htm?text_pass='+links+'&start='+start+'&end='+end;
		displynextprev(url);
	}

	function displynextprev(url)
	{
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null){
			alert ("Your browser does not support AJAX!");
			return;
		}
		contntdiv='trans_detail';
		height=10;
		xmlHttp.open("GET",url,true);
		xmlHttp.onreadystatechange=function() { stateChange(contntdiv,0); };
		xmlHttp.send(null);
	}
	function chknosonly(idpass){
		var idpassed=idpass;
		var valuefetch=document.getElementById(idpassed).value;
		if(isNaN(valuefetch)){
			alert("Please Enter Valid Nos");
			document.getElementById(idpass).focus();
			return;
		}
	}

	function splitstockval(){
		var symbolval=document.getElementById('symbol').value;
		if(symbolval==''){
		alert("Please Enter a Valid Stock");
		document.getElementById('symbol').focus();
		return;
		}
	}

function serachgetsymboldetail(){
	valid = true;
	enteredxchng=document.getElementById('exchangecmb').value;
	enteredsymbol=document.getElementById('symbolsearch').value;

	if(enteredxchng==''){
	alert("Please Select Valid Exchange");	
	document.getElementById('exchangecmb').focus();
	valid = false;
	return;
	}
	else if(enteredsymbol==''){
		alert("Please Select Valid Symbol");	
		document.getElementById('symbolsearch').focus();
		valid = false;
		return;
	}
	if(valid==true){
		var url=admin_server + '/admin/ss/transaction_handle.htm?text_pass=seachstock&exchangename='+enteredxchng+'&serhsymname='+enteredsymbol;
		getstockvals(url,'serchstockdetail');
	}
	
}


function getstockvals(url,divname)
{
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Your browser does not support AJAX!");
		return;
	}
	contntdiv=divname;
	height=10;
	xmlHttp.open("GET",url,true);
	xmlHttp.onreadystatechange=function() { stateChange(contntdiv,0); };
	xmlHttp.send(null);
}

function saveselltransactions(){
valid=true;

if(valid==true){
		document.getElementById('mode').value='save';
		var modeval=document.getElementById('mode').value;
		if(modeval=='save'){
	    document.symboltransa.submit(this.form);
		}	
			
}
}

function validateentries(id){
//var getchkid=(chkboxid.id);
var stockcode=parseInt(id);
//alert("stock code "+stockcode);
eval("chkboxid=document.symboltransa.sellchk_"+stockcode); // chkboxid== object
var getchkidstatus=chkboxid.checked;
var buydateref,buysharesref,saleqtyref,saledateref,sellpriceref,buydate,buyshares,saleqty,sellprice,saledate;
//if((getchkidstatus==true && getchkidstatus!='')||(getchkidstatus==false && getchkidstatus=='')){
eval("buydateref=document.symboltransa.buydate_"+stockcode);
eval("buysharesref=document.symboltransa.buyqty_"+stockcode);
eval("saleqtyref=document.symboltransa.sellqty_"+stockcode);
eval("sellpriceref=document.symboltransa.sellprice_"+stockcode);
eval("saledateref=document.symboltransa.selldate_"+stockcode);
//eval("imgref=document.symboltransa.img_"+stockcode);
		var buyDateTime=buydateref.value;
		var saleDateTime=saledateref.value;
		resCheckDate=chngdateformat(buyDateTime);
		sellDt=chngdateformat(saleDateTime);
if(getchkidstatus==true && getchkidstatus!='')
{
buydate=buydateref.value;
buyshares=parseFloat(buysharesref.value);
saleqty=parseFloat(saleqtyref.value);
saledate=saledateref.value;
sellprice=sellpriceref.value;

			if(saleqty=='' || saleqty==0){
				alert("Invalid Sale Qty");
				chkboxid.checked=0;
				saleqtyref.focus();
				return false;
			}else if(saleqty>buyshares){
				alert("Sale Qty must be less than equal to Buy Qty");
				chkboxid.checked=0;
				saleqtyref.focus();
				return false;
			}
			else if(sellprice=='' || sellprice=='0.00'){
				alert("Invalid Sale Price");
				chkboxid.checked=0;
				sellpriceref.focus();
				return false;
			}else if(saledate==''){
				alert("Invalid Sale Date");
				chkboxid.checked=0;
				NewCal(saledateref.id,'mmddyyyy');
				return false;
			}
			else if(sellDt<resCheckDate){
					alert("Sale Date Should be Greater than equal to Buy Date");
					NewCal(saledateref.id,'mmddyyyy');
					chkboxid.checked=0;
					return false;
			}
}else{
	if(getchkidstatus==false && getchkidstatus==''){
	//alert("unchecked");
	}
}
}


//function chknosvalidation(formname,element){
// call this like : onKeyup=javascript:chknosvalidation("'.$fname.'",this)
function chknosvalidation(formname,element){
var elementid=element.id;
var fname=formname;
eval("eleid=document."+fname+"."+elementid); 
var elval=eleid.value;
if(isNaN(elval)){ 
alert(" Numbers Only");
eleid.value=0;
}
}

function redirectpage(){
var selectedIndexval=document.getElementById('type').selectedIndex;
var optioval=document.getElementById('type').options[selectedIndexval].value;
switch(parseInt(optioval)){
case 1: // Sell
InsertContent('stock',optioval);
window.location=admin_server + "/admin/ss/ss_selltransaction.htm";
break
case 2: // Short Sell
InsertContent('stock',optioval);
break
case 3: // Buy to cover
///**** InsertContent(stockoroption,optioval);
InsertBTCContent('stock',optioval);
break
case 0: // Buy
InsertContent('stock',optioval);
break
}
}

function InsertContent(stockoroption,optioval){
	if(stockoroption=='stock')
	{
			$('symbol').disabled = false;
			$('notes').disabled = false;
			$('shares').disabled = false;
			$('price').disabled = false;
			$('addimg').style.display="";

			if (document.getElementById("btcspan") != null)
			{
				gettransactionView(optioval);
			}
	}else if(stockoroption=='option')
	{
		
		if (document.getElementById("optionbtcspan") != null)
		{
			var form = $('optionform');
			form[form.disabled ? 'enable' : 'disable']();
			form.disabled = !form.disabled;
			getoptiontransactionView(optioval);
		}

	}
}

function buytransaction(tranautoid){
window.location=admin_server + "/admin/ss/buytocover.htm?tranautoid="+tranautoid;
}
function optionbuytransaction(tranautoid){
	window.location=admin_server + "/admin/ss/buytocover.htm?tranautoid="+tranautoid+"&type=option";
}

function chknsubmit(){
	var sd=$('orgsortselldate').value;
	var ossqt=$('orgssqty').value;
	var ssunp=$('unitssprice').value;
	var trsactionidget=$('trsactionid').value;
	var enterdselldate=$('selldate').value;
	//alert("original short sale date "+sd+" You hv entered btc "+enterdselldate);
	var formatedsd=chngdateformat(sd);
	var formatedbtcd=chngdateformat(enterdselldate);
	//alert("ss date : "+formatedsd);
	//alert("btc date : "+formatedbtcd);
	var enteredshareqty=$('shareqty').value;
	var enteredprice=$('price').value;
if((enterdselldate=='')||(enterdselldate==0)){
	alert("Invalid Date");
	NewCal('selldate','mmddyyyy');
	return false;
}else if(formatedbtcd<formatedsd){alert(" Invalid Date for Buy to cover ");
NewCal('selldate','mmddyyyy');
return false;
}
else if((enteredshareqty=='')||(enteredshareqty==0)||(isNaN(enteredshareqty))){
	alert("Invalid Qty");
	$('shareqty').focus();
	return false;
}else if((parseFloat(enteredshareqty))>(parseFloat(ossqt))){
	alert("Buy to cover Quantity is upto : "+ossqt);
	$('shareqty').focus();
	return false;
}else if((enteredprice=='')||(enteredprice==0)||(isNaN(enteredprice))){
	alert("Invalid Buy Price");
	$('price').focus();
	return false;
}
if(trsactionidget!=''){
	document.btc.action=admin_server + "/admin/ss/buytocover.htm?mode=save&transactionid="+trsactionidget;
	document.btc.submit();
}else{
return false;
}
}

function sortby(start,end,sortfldname,type){
//alert(start+"  "+end+" "+sortfldname+" "+type);
links='sortbylinks';
var items=$('stockid_get_all').value;
var url= admin_server + '/admin/ss/transaction.htm?text_pass='+links+'&start='+start+'&end='+end+'&sortstr='+sortfldname+"="+type+'&items='+items;
alert(url);
window.location=url;

}

function submitform(divname)
{
var cashent=$('cash').value;
var idget=$('idget').value;
var dividentamt=$('divinterest').value;
var dividentid=$('divinterest_idget').value;
   if(cashent=='0.00' || isNaN(cashent)|| (parseFloat(cashent)<0)){
	alert("Inavlid Amount");
	$('cash').focus();
	return false;
}

if(confirm("Are you sure to save this ")){
   var url=admin_server + '/admin/ss/transaction_handle.htm?text_pass=cashentry&amt='+cashent+'&idget='+idget+'&dividentamt='+dividentamt+'&dividentid='+dividentid;
//   alert(url);
   savecashentry(url,divname);
}

if(idget==0)
	{
		window.location=admin_server + "/admin/home.htm";
	}
}

function savecashentry(url,divname)
{
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Your browser does not support AJAX!");
		return;
	}
	contntdiv='errormsg';
	height=10;
	xmlHttp.open("GET",url,true);
	xmlHttp.onreadystatechange=function() { stateChange(contntdiv,0); };
	xmlHttp.send(null);
}



function chngdateformat(date){
	var DateTime=date;
	var p1;//Index of Date Separator 1
	var p2;//Index of Date Separator 2 
	var p1=DateTime.indexOf('/',0)
	var p2=DateTime.indexOf('/',(parseInt(p1)+1));
	var strDate=DateTime.substring(p1+1,p2);

	if((strDate.length>0) && (strDate.length!=2)){
		strDate="0"+DateTime.substring(p1+1,p2);
	}

	var strMonth=DateTime.substring(0,p1);
	if((strMonth.length>0) && (strMonth.length!=2)){
		strMonth="0"+DateTime.substring(0,p1);
	}
	var strYear=DateTime.substring(p2+1,p2+5);
	//alert("buy Month "+strMonth+" Buy date "+strDate+" buy Year "+strYear);
	
	var retdate=strYear+"-"+strMonth+"-"+strDate;
	return retdate;

}
function makeBTCprevlinks1(start,end){
	links='prevbtctrans';
	var url=admin_server + '/admin/ss/transaction_handle.htm?text_pass='+links+'&start='+start+'&end='+end;
	displynextprev(url);
}
function makeBTCnextlinks1(start,end){
	links='nextbtctrans';
	var url=admin_server + '/admin/ss/transaction_handle.htm?text_pass='+links+'&start='+start+'&end='+end;
	displynextprev(url);
}

function InsertBTCContent(stockoroption,optioval)
{
	var totbtcrec=$('totbtcrec').value;
	if(totbtcrec==0 && (optioval==3))
	{
		alert(" You have Nothing to Buy to cover");
		$('type').selectedIndex=0;
		return false;
	}else{
		$('symbol').disabled = true;
		$('notes').disabled = true;
		$('shares').disabled = true;
		$('price').disabled = true;
		$('addimg').style.display="none";
		
		var url= admin_server + '/admin/ss/transaction_handle.htm';
		var pars="type=btcView";
		var myAjax4 = new Ajax.Request(url, {method: 'post',parameters: pars,
		onComplete:function getBTCViewStatus(req)
		{
			if(req.responseText!=='')
			{
				$('changabletransactionview').innerHTML=req.responseText;
			}else{
				$('changabletransactionview').innerHTML="OOPS some problem is there please try again";
			}
		}});

	}
}
function getoptiontransactionView(optioval){
	if(optioval!=3){
		var url= admin_server + '/admin/ss/optiontransaction_handle.htm';
		var pars="type=transView";
		var myAjax4 = new Ajax.Request(url, {method: 'post',parameters: pars,
		onComplete:function getTransactionViewd(req)
		{
			if(req.responseText!=='')
			{
				$('optionchangabletransactionview').innerHTML=req.responseText;
			}else{
				$('optionchangabletransactionview').innerHTML="OOPS some problem is there please try again";
			}
		}});
	}
}
function gettransactionView(optioval){
	if(optioval!=3){
		var url= admin_server + '/admin/ss/transaction_handle.htm';
		var pars="type=transView";
		var myAjax4 = new Ajax.Request(url, {method: 'post',parameters: pars,
		onComplete:function getTransactionViewss(req)
		{
			if(req.responseText!=='')
			{
				$('changabletransactionview').innerHTML=req.responseText;
			}else{
				$('changabletransactionview').innerHTML="OOPS some problem is there please try again";
			}
		}});
	}
}
function redirectoptionpage(){
	var selectedIndexval=document.getElementById('optiontranstype').selectedIndex;
	var optioval=document.getElementById('optiontranstype').options[selectedIndexval].value;
	switch(parseInt(optioval)){
		case 1: // Sell
		InsertContent('option',optioval);
		window.location=admin_server + "/admin/ss/option_sell.htm";
		break
		case 2: // Short Sell
		InsertContent('option',optioval);
		break
		case 3: // Buy to cover
		InsertOptionBTCContent('option',optioval);
		break
		case 0: // Buy
		InsertContent('option',optioval);
		break
	}
}
function InsertOptionBTCContent(optionorstock,optioval)
{
	var totbtcrec=$('optiontotbtcrec').value;
	if(totbtcrec==0 && (optioval==3))
	{
		alert(" You have Nothing to Buy to cover");
		$('optiontranstype').selectedIndex=0;
		return false;
	}else{
		var form = $('optionform');
		form[form.disabled ? 'enable' : 'disable']();
		form.disabled = !form.disabled;

		$('optiontranstype').disabled=false;

		var url= admin_server + '/admin/ss/optiontransaction_handle.htm';
		var pars="type=btcView";
		var myAjax4 = new Ajax.Request(url, {method: 'post',parameters: pars,
		onComplete:function getBTCViewOptionStatus(req)
		{
			if(req.responseText!=='')
			{
				$('optionchangabletransactionview').innerHTML=req.responseText;
			}else{
				$('optionchangabletransactionview').innerHTML="OOPS some problem is there please try again";
			}
		}});
	}
}
