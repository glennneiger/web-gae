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

function inserttransaction(symbolval,typeval,dateval,sharesval,priceval,notesval,modeval,editval){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Your browser does not support AJAX!");
		return;
	}
	
	if(editval!='edit'){
		var url=admin_server + '/admin/qp/transaction_handle.htm?text_pass='+modeval+'&symbolval='+symbolval+'&typeval='+typeval+'&dateval='+dateval+'&sharesval='+sharesval+'&priceval='+priceval+'&notesval='+notesval;
		document.getElementById('mode').value='';
		document.getElementById('symbol').value='';
		document.getElementById('type').selectedIndex=0;
		document.getElementById('demo2').value='';
		document.getElementById('shares').value='';
		document.getElementById('price').value='';
		document.getElementById('notes').value='';
	}else if(editval=='edit'){
		var url= admin_server + '/admin/qp/transaction_handle.htm?text_pass='+modeval+'&symbolval='+symbolval+'&typeval='+typeval+'&dateval='+dateval+'&sharesval='+sharesval+'&priceval='+priceval+'&notesval='+notesval+'&editaddform='+editval;
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
	var url=admin_server + '/admin/qp/transaction_handle.htm?text_pass='+links+'&start='+start+'&end='+end;
	displynextprev(url);
}

function makeprevlinks1(start,end){
	links='prevtrans';
	var url=admin_server + '/admin/qp/transaction_handle.htm?text_pass='+links+'&start='+start+'&end='+end;
	displynextprev(url);
}
function makenextlinks(start,end){
		links='next';
		var url=admin_server + '/admin/qp/transaction_handle.htm?text_pass='+links+'&start='+start+'&end='+end;
		displynextprev(url);
	}

	function makeprevlinks(start,end){
		links='prev';
		var url= admin_server + '/admin/qp/transaction_handle.htm?text_pass='+links+'&start='+start+'&end='+end;
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
		var url=admin_server + '/admin/qp/transaction_handle.htm?text_pass=seachstock&exchangename='+enteredxchng+'&serhsymname='+enteredsymbol;
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

		/*
		var Bp1;//Index of Date Separator 1
		var Bp2;//Index of Date Separator 2 
		var Bp1=buyDateTime.indexOf('/',0)
		var Bp2=buyDateTime.indexOf('/',(parseInt(Bp1)+1));
		var strDateB=buyDateTime.substring(Bp1+1,Bp2);
		var strMonthB=buyDateTime.substring(0,Bp1);
		var strYearB=buyDateTime.substring(Bp2+1,Bp2+5);
//alert("buy Month "+strMonthB+" Buy date "+strDateB+" buy Year "+strYearB);

		var saleDateTime=saledateref.value;
		var Sp1;//Index of Date Separator 1
		var Sp2;//Index of Date Separator 2 
		var Sp1=saleDateTime.indexOf('/',0)
		var Sp2=saleDateTime.indexOf('/',(parseInt(Sp1)+1));
		var strDate=saleDateTime.substring(Sp1+1,Sp2);
		var strMonth=saleDateTime.substring(0,Sp1);
		var strYear=saleDateTime.substring(Sp2+1,Sp2+5);
//alert("sale Month "+strMonth+" Sale date "+strDate+" sale Year "+strYear);

*/
		resCheckDate=chngdateformat(buyDateTime);
		sellDt=chngdateformat(saleDateTime);

		
		//var sellDt=strYear+"-"+strMonth+"-"+strDate;
		//alert("Sale date : "+sellDt);
	//***	var resCheckDate = strYearB+"-"+strMonthB+"-"+strDateB;
		//alert("Buy date : "+resCheckDate);


if(getchkidstatus==true && getchkidstatus!=''){

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
InsertContent(optioval);
window.location=admin_server + "/admin/qp/QP_selltransaction.htm";
break
case 2: // Short Sell
InsertContent(optioval);
break
case 3: // Buy to cover
InsertContent(optioval);
break
case 0: // Buy
InsertContent(optioval);
break
}
}

function InsertContent(optioval){
	var optioval;
	var totbtcrec=document.getElementById('totbtcrec').value;
	if(totbtcrec=='' && (optioval==3)){
		alert(" You have Nothing to Buy to cover");
		document.getElementById('type').selectedIndex=0;
	}

//**	var items = document.getElementsByTagName("img");
// for each item in the list,
// append the entire element as a string of HTML
	for (var i = 0; i < totbtcrec; i++) {
		n=i+1;
		var elmid='btc_'+n;
		eval("imgget=document.getElementById('"+elmid+"')");
		if(optioval==3){
		imgget.style.display="";
		}else{
		imgget.style.display="none";
		}
	}
	if(totbtcrec>0){
		if(optioval==3){
		document.getElementById('symbol').disabled = true;
		document.getElementById('notes').disabled = true;
		document.getElementById('shares').disabled = true;
		document.getElementById('price').disabled = true;
		document.getElementById('addimg').style.display="none";
		//****document.getElementById('btcmsg').innerHTML="";
		}else{
			document.getElementById('symbol').disabled = false;
			document.getElementById('notes').disabled = false;
			document.getElementById('shares').disabled = false;
			document.getElementById('price').disabled = false;
			document.getElementById('addimg').style.display="";
			//****document.getElementById('btcmsg').innerHTML="";
		}
	}
}

function buytransaction(tranautoid){
window.location=admin_server + "/admin/qp/buytocover.htm?tranautoid="+tranautoid;
}

function chknsubmit(){
	var sd=document.getElementById('orgsortselldate').value;
	var ossqt=document.getElementById('orgssqty').value;
	var ssunp=document.getElementById('unitssprice').value;
	var trsactionidget=document.getElementById('trsactionid').value;
	var enterdselldate=document.getElementById('selldate').value;

	//alert("original short sale date "+sd+" You hv entered btc "+enterdselldate);
	var formatedsd=chngdateformat(sd);
	var formatedbtcd=chngdateformat(enterdselldate);
	//alert("ss date : "+formatedsd);
	//alert("btc date : "+formatedbtcd);

	
	

	var enteredshareqty=document.getElementById('shareqty').value;
	var enteredprice=document.getElementById('price').value;


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
	document.getElementById('shareqty').focus();
	return false;
}else if((parseFloat(enteredshareqty))>(parseFloat(ossqt))){
	alert("Buy to cover Quantity is upto : "+ossqt);
	document.getElementById('shareqty').focus();
	return false;
}else if((enteredprice=='')||(enteredprice==0)||(isNaN(enteredprice))){
	alert("Invalid Buy Price");
	document.getElementById('price').focus();
	return false;
}
if(trsactionidget!=''){
	document.btc.action=admin_server + "/admin/qp/buytocover.htm?mode=save&transactionid="+trsactionidget;
	document.btc.submit();
//	window.location=admin_server + "/admin/qp/buytocover.htm?mode=save&transactionid="+trsactionidget;
}else{
return false;
}
}

function sortby(start,end,sortfldname,type){
//alert(start+"  "+end+" "+sortfldname+" "+type);

links='sortbylinks';
var items=document.getElementById('stockid_get_all').value;

var url= admin_server + '/admin/qp/transaction.htm?text_pass='+links+'&start='+start+'&end='+end+'&sortstr='+sortfldname+"="+type+'&items='+items;
//alert(url);
window.location=url;
//displynextprev(url);
}

function submitform(divname){
var cashent=document.getElementById('cash').value;
var idget=document.getElementById('idget').value;

var dividentamt=document.getElementById('divinterest').value;
var dividentid=document.getElementById('divinterest_idget').value;

   if(cashent=='0.00' || isNaN(cashent)|| (parseFloat(cashent)<0)){
	alert("Inavlid Amount");
	document.getElementById('cash').focus();
	return false;
}

   /*
   if(cashent=='0.00' || isNaN(cashent)|| (parseFloat(cashent)<0)){
	   alert("Inavlid Amount");
	   document.getElementById('cash').focus();
	   
   }*/
   

if(confirm("Are you sure to save this ")){
   var url=admin_server + '/admin/qp/transaction_handle.htm?text_pass=cashentry&amt='+cashent+'&idget='+idget+'&dividentamt='+dividentamt+'&dividentid='+dividentid;
//   alert(url);
 //  return false;
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
