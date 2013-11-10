function validateOptionentry()
{
	$('optionerrormsgdiv').style.color="red";
	if($F('optPurdate')=='')
	{
		$('optionerrormsgdiv').innerHTML="Please fill in the 'date' box.";
		NewCal("optPurdate","mmddyyyy");
		return false;
	}
	else if($F('optionticker')==''){
		$('optionerrormsgdiv').innerHTML="Option Ticker is Blank.";
		$('optionticker').focus();
		return false;
	}
	var checked=false;
	$('optionform').getInputs('radio', 'optiontype').each(function(e)
	{
		if(e.checked)
		{
		checked=true;
	}});
	if(!checked)
	{
		$('optionerrormsgdiv').innerHTML="Type of Option Required.";
		return false;
	}else if($F('basestock')==''){
		$('optionerrormsgdiv').innerHTML="Base Option Required.";
		$('basestock').focus();
		return false;
	}else if($F('optionexpirymonth')==''){
		$('optionerrormsgdiv').innerHTML="Expiry Month required.";
		$('optionexpirymonth').focus();
		return false;
		}
	else if($F('optionexpiryyear')==''){
		$('optionerrormsgdiv').innerHTML="Expiry Year required.";
		$('optionexpiryyear').focus();
		return false;
	}
	else if(($F('strikeprice')=='')||(isNaN($F('strikeprice')))){
		$('optionerrormsgdiv').innerHTML="Invalid Strike Price.";
		$('strikeprice').focus();
		return false;
	}
	else if(($F('noofcontract')=='')||(isNaN($F('noofcontract')))){
		$('optionerrormsgdiv').innerHTML="Invalid no of contract.";
		$('noofcontract').focus();
		return false;
	}
	else if(($F('contractprice')=='')||(isNaN($F('contractprice')))){
		$('optionerrormsgdiv').innerHTML="Invalid contract Price.";
		$('contractprice').focus();
		return false;
	}
	else if($F('stockBaseId')==0){
		$('optionerrormsgdiv').innerHTML="Please Enter a valid Base stock";
		return false;
	}
	else{
		$('optionerrormsgdiv').innerHTML="";
		return true;
	}
	
}

function processOptionForm()
{
	var optiontype;
	if(validateOptionentry())
	{
			var optiontranstype=$F('optiontranstype');
			var optPurdate=$F('optPurdate');
			var optionticker=$F('optionticker');

			$('optionform').getInputs('radio', 'optiontype').each(function(e)
			{
				if(e.checked){
				optiontype=$F(e);
			}});
			var basestock=$F('basestock');
			var optionexpirymonth=$F('optionexpirymonth');
			var optionexpiryyear=$F('optionexpiryyear');
			var strikeprice=$F('strikeprice');
			var noofcontract=$F('noofcontract');
			var contractprice=$F('contractprice');
			var notes=$F('optionnotes');
			var stockBaseId=$F('stockBaseId');
			var url= admin_server + '/admin/ss/optiontransaction_handle.htm';
			
			var pars="type=save&optiontranstype="+optiontranstype+"&optPurdate="+optPurdate+"&optionticker="+optionticker+"&optiontype="+optiontype+"&basestock="+basestock+"&optionexpirymonth="+optionexpirymonth+"&optionexpiryyear="+optionexpiryyear+"&strikeprice="+strikeprice+"&noofcontract="+noofcontract+"&contractprice="+contractprice+"&notes="+notes+'&stockBaseId='+stockBaseId;
			var myAjax4 = new Ajax.Request(url, {method: 'post',parameters: pars,
			onComplete:function getProcessStatus(req)
			{
				if(parseInt(req.responseText)==1)
				{
					$('optionerrormsgdiv').style.color="green";
					$('optionerrormsgdiv').innerHTML="Transaction Done";
					$('optionform').reset();
					// send the request to get the latest result for View
					getOptionsView();
				}else
				{
					$('optionerrormsgdiv').innerHTML="OOPS some problem is there please try again";
				}
			}});
	}
}
function getOptionsView(){
	var url= admin_server + '/admin/ss/optiontransaction_handle.htm';
	var pars="type=view";
	var myAjax4 = new Ajax.Request(url, {method: 'post',parameters: pars,
	onComplete:function getViewStatus(req)
	{
		if(req.responseText!='')
		{
			$('optionchangabletransactionview').innerHTML=req.responseText;
		}else{
			//**** *$('optionerrormsgdiv').innerHTML="OOPS some problem is there please try again";
		}
	}});
}

function transactionedit(optionorstock,fieldid)
{
	if(optionorstock=='option')
	{
		$('edittrans_'+fieldid).hide();
		$('savetrans_'+fieldid).show();
		$('optdelete_'+fieldid).show();
		//** addBorder('contractnos_'+fieldid, "#CCCCCC");
		addBorder('contractunitprice_'+fieldid, "#CCCCCC");
		addBorder('optionnotes_'+fieldid, "#CCCCCC");
		//** $('contractnos_'+fieldid).readOnly=false;
		$('contractunitprice_'+fieldid).readOnly=false;
		$('optionnotes_'+fieldid).readOnly=false;
		$('contractunitprice_'+fieldid).focus();
	}
	else if(optionorstock=='stock')
	{
		$('stockedittrans_'+fieldid).hide();
		$('stocksavetrans_'+fieldid).show();
		$('stockdelete_'+fieldid).show();
		//** addBorder('contractnos_'+fieldid, "#CCCCCC");
		addBorder('stockunitprice_'+fieldid, "#CCCCCC");
		addBorder('stocknotes_'+fieldid, "#CCCCCC");
		//** $('contractnos_'+fieldid).readOnly=false;
		$('stockunitprice_'+fieldid).readOnly=false;
		$('stocknotes_'+fieldid).readOnly=false;
		$('stockunitprice_'+fieldid).focus();
	}
}

function optiontransactionsave(operation,optionorstock,fieldid,transtype)
{
	if(operation=='update')
	{
		if(optionorstock=='option')
		{
			if(($F('optionnotes_'+fieldid)!=$('optionnotes_'+fieldid).defaultValue)||($F('contractunitprice_'+fieldid)!=$('contractunitprice_'+fieldid).defaultValue)){
				$('optdelete_'+fieldid).hide();
				$('savetrans_'+fieldid).hide();
				$('spinnerimg_'+fieldid).show();
				// oroginal field ids
				var fieldstrings = 'optionnotes_'+fieldid+"~"+'contractunitprice_'+fieldid+"~"+'contractnos_'+fieldid;
				//send the request to update the record
				goForRecordUpdate(operation,fieldid,fieldstrings,transtype,optionorstock);
			}else{
				resetToEditMode('option',fieldid);
				return false;
			}
		}
		else if(optionorstock=='stock')
		{
			if(($F('stocknotes_'+fieldid)!=$('stocknotes_'+fieldid).defaultValue)||($F('stockunitprice_'+fieldid)!=$('stockunitprice_'+fieldid).defaultValue)){
				$('stockdelete_'+fieldid).hide();
				$('stocksavetrans_'+fieldid).hide();
				$('stockspinnerimg_'+fieldid).show();
				// oroginal field ids
				var fieldstrings = 'stocknotes_'+fieldid+"~"+'stockunitprice_'+fieldid;
				//send the request to update the record
				goForRecordUpdate(operation,fieldid,fieldstrings,transtype,optionorstock);
			}else{
				resetToEditMode('stock',fieldid);
				return false;
			}
		}
	}
	else if(operation=='delete')
	{
		if(optionorstock=='option')
		{
		if (confirm("This will delete all related transactions to this.."))
		{
			removeBorder('contractunitprice_'+fieldid, "#8DA9E6");
			removeBorder('optionnotes_'+fieldid, "#8DA9E6");
			$('optdelete_'+fieldid).hide();
			$('savetrans_'+fieldid).hide();
			$('spinnerimg_'+fieldid).show();
			fieldstrings='';
			goForRecordUpdate('delete',fieldid,fieldstrings,transtype,optionorstock);
		} else {
			return false;
		}
		}
		else if(optionorstock=='stock')
		{
			if (confirm("This will delete all related transactions to this.."))
			{
				removeBorder('stockunitprice_'+fieldid, "#8DA9E6");
				removeBorder('stocknotes_'+fieldid, "#8DA9E6");
				$('stockdelete_'+fieldid).hide();
				$('stocksavetrans_'+fieldid).hide();
				$('stockspinnerimg_'+fieldid).show();
				var fieldstrings = 'stocknotes_'+fieldid+"~"+'stockunitprice_'+fieldid;
				goForRecordUpdate('delete',fieldid,fieldstrings,transtype,optionorstock);
			} else {
				return false;
			}

			
		}
	}
}
function addBorder(element, color){return $(element).setStyle({border: "1px solid " + (color || "red")});}
function removeBorder(element, color){return $(element).setStyle({border: "0px solid " + (color || "red")});}

function checkNumeric(id){
	if(isNaN($F(id))){
	alert('Invalid Value');
	$(id).value=$(id).defaultValue;
	$(id).focus();
	return false;
	}
}

function goForRecordUpdate(operation,id,fieldstring,transtype,optionorstock)
{
	var stockid=0;
	if(optionorstock=='option')
	{
		var url= admin_server + '/admin/ss/optiontransaction_handle.htm';
	}
	else if(optionorstock=='stock')
	{
		var url= admin_server + '/admin/ss/stocktransaction_handle.htm';
	}

	if(operation=='update')
	{
		var fieldsarray=fieldstring.split("~");
		var optionnotesval=$F(fieldsarray[0]);
		var optionunitpriceval=$F(fieldsarray[1]);
		if(optionorstock!='stock')
		{
			var noofcontracts=$F(fieldsarray[2]);
			var quoteid=0;
		}else
		{
			var noofcontracts=0; // for stock no contract is there
			var quoteid=$F('qtid_'+id); // for lot table it required
		}
		var pars='type=update&optionnotes='+encodeURIComponent(optionnotesval)+'&optionunitprice='+optionunitpriceval+'&recordid='+id+'&noofcontracts='+noofcontracts+'&transtype='+transtype+'&quoteid='+quoteid;
		stockid=quoteid; // assigned defined on top may require for delete case
	}
	
	else if(operation=='delete')
	{
		if(optionorstock=='stock')
		{
			var fieldsarray=fieldstring.split("~");
			var optionnotesval=$F(fieldsarray[0]);
			var optionunitpriceval=$F(fieldsarray[1]);
			var stockid=$F('qtid_'+id); // for lot table it required
			var pars='type=delete&optionnotes='+encodeURIComponent(optionnotesval)+'&optionunitprice='+optionunitpriceval+'&recordid='+id+'&transtype='+transtype+'&quoteid='+stockid;
		}else
		{
			var pars='type=delete&recordid='+id+'&transtype='+transtype+'&quoteid='+stockid;
		}
	}
	
	var myAjax4 = new Ajax.Request(url, {method: 'post',parameters: pars,
	onComplete:function getUpdatedViewStatus(req)
	{
		if(req.responseText!='')
		{
			if(operation=='update')
			{
				goForNormalView(optionorstock,transtype,id,optionnotesval,optionunitpriceval);
			}
			else if(operation=='delete')
			{
				window.location.reload();
			}

		}else{
			alert("Not done 1111111")
		}
	}});
}
function goForNormalView(optionorstock,transtype,id,optionnotesval,optionunitpriceval){
	
		if(optionorstock=='option')
		{
		$('spinnerimg_'+id).hide();
		removeBorder('contractunitprice_'+id, "#8DA9E6");
		removeBorder('optionnotes_'+id, "#8DA9E6");
		// reste with updated price and notes
		$('contractunitprice_'+id).defaultValue=optionunitpriceval;
		$('optionnotes_'+id).defaultValue=optionnotesval;
		$('contractunitprice_'+id).readOnly=true;
		$('optionnotes_'+id).readOnly=true;
		$('edittrans_'+id).show();
		}
		else if(optionorstock=='stock')
		{
			$('stockspinnerimg_'+id).hide();
			removeBorder('stockunitprice_'+id, "#8DA9E6");
			removeBorder('stocknotes_'+id, "#8DA9E6");
			// reste with updated price and notes
			$('stockunitprice_'+id).defaultValue=optionunitpriceval;
			$('stocknotes_'+id).defaultValue=optionnotesval;
			$('stockunitprice_'+id).readOnly=true;
			$('stocknotes_'+id).readOnly=true;
			$('stockedittrans_'+id).show();
		}
}
function resetToEditMode(type,fieldid){
	if(type=='option')
	{
	$('optdelete_'+fieldid).hide();
	$('savetrans_'+fieldid).hide();		
	removeBorder('contractunitprice_'+fieldid, "#8DA9E6");
	removeBorder('optionnotes_'+fieldid, "#8DA9E6");
	$('contractunitprice_'+fieldid).readOnly=true;
	$('optionnotes_'+fieldid).readOnly=true;
	$('edittrans_'+fieldid).show();
	}
	else if(type=='stock')
	{
		$('stockdelete_'+fieldid).hide();
		$('stocksavetrans_'+fieldid).hide();		
		removeBorder('stockunitprice_'+fieldid, "#8DA9E6");
		removeBorder('stocknotes_'+fieldid, "#8DA9E6");
		$('stockunitprice_'+fieldid).readOnly=true;
		$('stocknotes_'+fieldid).readOnly=true;
		$('stockedittrans_'+fieldid).show();
	}
}
function optionmakenextlinks1(contentdiv,start,end){
	var url= admin_server + '/admin/ss/optiontransaction_handle.htm';
	var pars='type=nexttrans&start='+start+'&end='+end;
	var myAjax4 = new Ajax.Request(url, {method: 'post',parameters: pars,
	onComplete:function getNextRecords(req)
	{
	if(req.responseText!='')
	{
		$(contentdiv).innerHTML=req.responseText;

	}else{
		alert("Not done 2222222")
	}
}});
}

function optiomakeprevlinks1(contentdiv,start,end){
	var url= admin_server + '/admin/ss/optiontransaction_handle.htm';
	var pars='type=prevtrans&start='+start+'&end='+end;
	var myAjax4 = new Ajax.Request(url, {method: 'post',parameters: pars,
onComplete:function getNextRecords(req)
	{
		if(req.responseText!='')
		{
			$(contentdiv).innerHTML=req.responseText;

		}else{
			alert("Not done 33333")
		}
	}});
}

function makeOptionBTCprevlinks1(contentdiv,start,end){
	var url= admin_server + '/admin/ss/optiontransaction_handle.htm';
	var pars='type=prevbtctrans&start='+start+'&end='+end;
	var myAjax4 = new Ajax.Request(url, {method: 'post',parameters: pars,
onComplete:function getNextRecords(req)
	{
		if(req.responseText!='')
		{
			$(contentdiv).innerHTML=req.responseText;

		}else{
			//*** alert("Not done 33333")
		}
	}});
}
function makeOptionBTCnextlinks1(contentdiv,start,end){
	//optionstrans_detail
	var url= admin_server + '/admin/ss/optiontransaction_handle.htm';
	var pars='type=nextbtctrans&start='+start+'&end='+end;
	var myAjax4 = new Ajax.Request(url, {method: 'post',parameters: pars,
	onComplete:function getNextRecords(req)
	{
		if(req.responseText!='')
		{
			$(contentdiv).innerHTML=req.responseText;

		}else{
			//**** alert("Not done 2222222")
		}
	}});
	
}
function chkoptionsubmit(){
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

if((enterdselldate=='')||(enterdselldate==0))
{
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
	document.btc.action=admin_server + "/admin/ss/buytocover.htm?mode=save&transactionid="+trsactionidget+'&type=option';
	document.btc.submit();
}else{
return false;
}
}
function submitCashform(divname)
{
	var cashent=$('optioncash').value;
	var idget=$('optionidget').value;
	var dividentamt=$('divintgetopt').value;
	var dividentid=$('divinterestopt_idget').value;
	if((cashent=='0.00')||(cashent=='')|| (isNaN(cashent))|| (parseFloat(cashent)<0))
	{
		alert("Invalid Amount");
		$('optioncash').focus();
		return false;
	}

	if(confirm("Are you sure to save this ")){
		var url= admin_server + '/admin/ss/optiontransaction_handle.htm';
		var pars='type=cashentry&amt='+cashent+'&idget='+idget+'&dividentamt='+dividentamt+'&dividentid='+dividentid;
		var myAjax4 = new Ajax.Request(url, {method: 'post',parameters: pars,
		onComplete:function saveCashRecords(req)
		{
			if(req.responseText!='')
			{
				$(divname).innerHTML=req.responseText;

			}else
			{
				$(divname).innerHTML="Please retry";
			}
		}});
	}
}
function serachOptiondetail(){
	var valid = true;
	enteredsymbol=$F('symbolsearch');
	if(enteredsymbol=='')
	{
		showDialog('<b>Error</b>','<font color=#A43120>Invalid Option Ticker</font>','prompterror')

		$('symbolsearch').focus();
		valid = false;
		return valid;
	}
	if(valid==true){
		getOptionDetails('serchoptiondetail');
	}

}

function getOptionDetails(divname)
{
	var url= admin_server + '/admin/ss/optiontransaction_handle.htm';
	var pars='type=seachstock&ticker='+$F('symbolsearch');
	var myAjax4 = new Ajax.Request(url, {method: 'post',parameters: pars,
	onComplete:function saveCashRecords(req)
	{
		if(req.responseText!='')
		{
			$(divname).innerHTML=req.responseText;

		}else
		{
			$(divname).innerHTML="Please retry";
		}
	}});
}
function saveOptionselltransactions()
{
	var checkedAny=false;
	if ($('symboltransa').getInputs('checkbox').pluck('checked').any()){
		checkedAny=true;
	}
	if(!checkedAny){
		alert("Please Select related Checkbox");
		return checkedAny;
	}
	
	
	// check any checkbox selected
	
	valid=true;
	if(valid==true)
	{
		document.getElementById('mode').value='save';
		var modeval=document.getElementById('mode').value;
		if(modeval=='save')
		{
			document.symboltransa.submit(this.form);
		}	

	}
}
function validateOptionentries(id){
	var stockcode=parseInt(id);
	eval("chkboxid=document.symboltransa.sellchk_"+stockcode); // chkboxid== object
	var getchkidstatus=chkboxid.checked;
	var buydateref,buysharesref,saleqtyref,saledateref,sellpriceref,buydate,buyshares,saleqty,sellprice,saledate;
	eval("buydateref=document.symboltransa.buydate_"+stockcode);
	eval("buysharesref=document.symboltransa.buyqty_"+stockcode);
	eval("saleqtyref=document.symboltransa.sellqty_"+stockcode);
	eval("sellpriceref=document.symboltransa.sellprice_"+stockcode);
	eval("saledateref=document.symboltransa.selldate_"+stockcode);
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
		}
	}
}
function validateoptionticker(fieldId,divname)
{
	if($F('optionticker')!='')
	{
		var url= admin_server + '/admin/ss/optiontransaction_handle.htm';
		var pars='type=validateoption&ticker='+$F('optionticker')+'&enity_type=1';
		var myAjax4 = new Ajax.Request(url, {method: 'post',parameters: pars,
		onLoading:inProgressLabel(divname,'Verifying option ticker..'),
		onComplete:function verifyOptionticker(req)
		{
			if(req.responseText!='')
			{
				$(divname).innerHTML=req.responseText;
				$(fieldId).focus();
				return false;
			}else
			{
				$(divname).innerHTML="";
			}
		}});

	}else{
		$(divname).innerHTML="";
		return false;
	}
}
function inProgressLabel(fieldId,msg)
{
	$(fieldId).innerHTML=msg;
}

function validateBaseStock(fieldId,divname)
{
	if($F(fieldId)!='')
	{
		var url= admin_server + '/admin/ss/optiontransaction_handle.htm';
		var pars='type=validateBaseStock&ticker='+$F(fieldId)+'&enity_type=1';
		var myAjax4 = new Ajax.Request(url, {method: 'post',parameters: pars,
		onLoading:inProgressLabel(divname,'Verifying base stock..'),
		onComplete:function verifyOptionticker(req)
		{
			if(req.responseText!=0)
			{
				$(stockBaseId).value=parseInt(req.responseText);
				$(divname).innerHTML='';
			}else
			{
				$(stockBaseId).value=0;
				$(divname).innerHTML="Please Enter a Valid Base Stock ";
				$(fieldId).focus();
				return false;
			}
		}});

	}else{
		$(divname).innerHTML="";
		return false;
	}
}