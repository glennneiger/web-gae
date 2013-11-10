function submitTechCashForm(divname){
	var cashent=jQuery('#techstratCash').val();
	var idget=jQuery('#techstratIdget').val();
	
	var dividentamt=jQuery('#techstratDivInterest').val();
	var dividentid=jQuery('#techstratDivInterestIdget').val();
	
	if(cashent==''){
		alert("Initial Cash can't be blank.");
		jQuery('#techstratCash').focus();
		return false;
	}
	
	if(dividentamt==''){
		alert("Divident/Interest Cash can't be blank.");
		jQuery('#techstratDivInterest').focus();
		return false;
	}

	if(cashent=='0.00' || isNaN(cashent)|| (parseFloat(cashent)<0)){
		alert("Inavlid Amount");
		jQuery('#techstratCash').focus();
		return false;
	}
   
	if(confirm("Are you sure to save these values?")){
		jQuery.ajax({
			type : "POST",
			url : host+"/admin/techstrat/transaction_handle.htm",
			data : "techPortType=cashentry&amt="+cashent+"&idget="+idget+"&dividentamt="+dividentamt+"&dividentid="+dividentid,
			error : function(){},
			success : function(res){
				var result = eval('(' + res + ')');
				if(result.status==true){
					jQuery('#techErrorMsg').html(result.msg);
				}
			}
		});
	}
}

function techstratSortBy(start,end,sortfldname,type){
	links='sortbylinks';
	var items=jQuery('#stockid_get_all').val();	
	var url= admin_server + '/admin/techstrat/transaction.htm?text_pass='+links+'&start='+start+'&end='+end+'&sortstr='+sortfldname+"="+type+'&items='+items;
	window.location=url;
}

function techAddTransValidate(){
	valid = true;
	var symbolval = jQuery('#symbol').val();
	var typeval = jQuery('#type').val();
	var dateval = jQuery('#date').val();
	var sharesval = jQuery('#shares').val();
	var priceval = jQuery('#price').val();
	var notesval = jQuery('#notes').val();
				   
	if ( symbolval == "" ){
		alert ( "Please fill in the 'Symbol' box." );
		jQuery('#symbol').focus();
		valid = false;
	}else if ( dateval == "" ) {
		alert ( "Please fill in the 'date' box." );
		valid = false;
	}else if (sharesval == ""){
		alert ( "Please fill in the 'shares' box." );
		jQuery('#shares').focus();
		valid = false;
	}else if (isNaN(sharesval)||(sharesval==0)){
		alert ( " Invalid 'shares' Entry." );
		jQuery('#shares').focus();
		valid = false;
	}else if ( priceval == "" ){
		alert ( "Please fill in the 'price' box." );
		jQuery('#price').focus();
		valid = false;
	}else if ( isNaN(priceval)||(priceval==0)){
		alert ( "Invalid 'price' Entry." );
		jQuery('#price').focus();
		valid = false;
	}	
	
	if(valid==true){
		jQuery('#mode').val('save');
		var modeval=jQuery('#mode').val();
		if(modeval=='save'){
			inserttransaction(symbolval,typeval,dateval,sharesval,priceval,notesval,modeval);
		}
	}
	if(valid==false){
		jQuery('#mode').val('');
			return valid;
	}
}

function inserttransaction(symbolval,typeval,dateval,sharesval,priceval,notesval,modeval,editval){
	if(editval!='edit'){
		jQuery.ajax({
			type : "POST",
			url : host+"/admin/techstrat/transaction_handle.htm",
			data : 'techPortType='+modeval+'&symbolval='+symbolval+'&typeval='+typeval+'&dateval='+dateval+'&sharesval='+sharesval+'&priceval='+priceval+'&notesval='+notesval,
			error : function(){},
			beforeSend : function(){
				jQuery('#techAddloadImg').show();
			},
			success : function(res){
				var result = eval('(' + res + ')');
				if(result.status==true){
					jQuery('#successMsg').html('Transaction has been done successfully.');
					jQuery('#techAddloadImg').hide();
					jQuery('#trans_detail').html(result.msg);
					jQuery('#mode').val('');
					jQuery('#symbol').val('');
					jQuery('#type').val('0');
					jQuery('#date').val('');
					jQuery('#shares').val('');
					jQuery('#price').val('');
					jQuery('#notes').val('');
				}
			}
		});
	}else if(editval=='edit'){
		jQuery.ajax({
			type : "POST",
			url : host+"/admin/techstrat/transaction_handle.htm",
			data : 'techPortType='+modeval+'&symbolval='+symbolval+'&typeval='+typeval+'&dateval='+dateval+'&sharesval='+sharesval+'&priceval='+priceval+'&notesval='+notesval+'&editaddform='+editval,
			error : function(){},
			beforeSend : function(){
				jQuery('#techAddloadImg').show();
			},
			success : function(res){
				var result = eval('(' + res + ')');
				if(result.status==true){
					jQuery('#successMsg').html('Transaction has been done successfully.');
					jQuery('#techAddloadImg').hide();
					jQuery('#trans_detail').html(result.msg);
					jQuery('#addmode').val('');
					jQuery('#symbol').val('');
					jQuery('#comotype').val('0');
					jQuery('#date').val('');
					jQuery('#shares').val('');
					jQuery('#price').val('');
					jQuery('#notes').val('');
				}
			}
		});
	}
}

function techStratRedirectOpt(){
	var selectedIndexval=jQuery('#type').val();
	var optioval=jQuery('#type').val();
	switch(parseInt(optioval)){
		case 1: // Sell
			InsertContent(optioval);
			window.location=admin_server + "/admin/techstrat/selltransaction.htm";
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

function makeTechNextLinks(start,end){
	links='next';
	jQuery.ajax({
		type : "POST",
		url : host+"/admin/techstrat/transaction_handle.htm",
		data : 'techPortType='+links+'&start='+start+'&end='+end,
		error : function(){},
		success : function(res){
			var result = eval('(' + res + ')');
			if(result.status==true){
				jQuery('#trans_detail').html(result.msg);
			}
		}
	});
}

function makeTechPrevLinks(start,end){
	links='prev';
	jQuery.ajax({
		type : "POST",
		url : host+"/admin/techstrat/transaction_handle.htm",
		data : 'techPortType='+links+'&start='+start+'&end='+end,
		error : function(){},
		success : function(res){
			var result = eval('(' + res + ')');
			if(result.status==true){
				jQuery('#trans_detail').html(result.msg);
			}
		}
	});
}

function makeTechNextLinks1(start,end){
	links='nexttrans';
	jQuery.ajax({
		type : "POST",
		url : host+"/admin/techstrat/transaction_handle.htm",
		data : 'techPortType='+links+'&start='+start+'&end='+end,
		error : function(){},
		success : function(res){
			var result = eval('(' + res + ')');
			if(result.status==true){
				jQuery('#trans_detail').html(result.msg);
			}
		}
	});
}

function makeTechPrevLinks1(start,end){
	links='prevtrans';
	jQuery.ajax({
		type : "POST",
		url : host+"/admin/techstrat/transaction_handle.htm",
		data : 'techPortType='+links+'&start='+start+'&end='+end,
		error : function(){},
		success : function(res){
			var result = eval('(' + res + ')');
			if(result.status==true){
				jQuery('#trans_detail').html(result.msg);
			}
		}
	});
}

function InsertContent(optioval){
	var optioval;
	var totbtcrec=jQuery('#totbtcrec').val();
	if(totbtcrec=='' && (optioval==3)){
		alert(" You have Nothing to Buy to cover");
		jQuery('#type').val('0');
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
	window.location=admin_server + "/admin/techstrat/buytocover.htm?tranautoid="+tranautoid;
}

function chknsubmit(){
	var sd=jQuery('#orgsortselldate').val();
	var ossqt=jQuery('#orgssqty').val();
	var ssunp=jQuery('#unitssprice').val();
	var trsactionidget=jQuery('#trsactionid').val();
	var enterdselldate=jQuery('#selldate').val();

	var enteredshareqty=jQuery('#shareqty').val();
	var enteredprice=jQuery('#price').val();

	if((enteredshareqty=='')||(enteredshareqty==0)||(isNaN(enteredshareqty))){
		alert("Invalid Qty");
		jQuery('#shareqty').focus();
		return false;
	}else if((parseFloat(enteredshareqty))>(parseFloat(ossqt))){
		alert("Buy to cover Quantity is upto : "+ossqt);
		jQuery('#shareqty').focus();
		return false;
	}else if((enteredprice=='')||(enteredprice==0)||(isNaN(enteredprice))){
		alert("Invalid Buy Price");
		jQuery('#price').focus();
		return false;
	}
	
	if(trsactionidget!=''){
		document.btc.action=admin_server + "/admin/techstrat/buytocover.htm?mode=save&transactionid="+trsactionidget;
		document.btc.submit();
	}else{
		return false;
	}
}

function techStratSearchSell(){
	valid = true;
	var sellSymbol=jQuery('#symbolsearch').val();

	if(sellSymbol==''){
		alert("Please Enter Symbol.");	
		jQuery('#symbolsearch').focus();
		valid = false;
		return;
	}
	if(valid==true){
		jQuery.ajax({
			type : "POST",
			url : host+"/admin/techstrat/transaction_handle.htm",
			data : 'techPortType=sellSearchStock&sellSymbol='+sellSymbol,
			error : function(){},
			beforeSend : function(){
				if(jQuery('#techSearchStockDetail').html()!=''){
					jQuery('#techSearchStockDetail').empty();
				}
				var content = "<center><img src='http://storage.googleapis.com/mvassets/images/recurly/submitting.gif' /></center>";
				jQuery('#techSearchStockDetail').html(content);
			},
			success : function(res){
				var result = eval('(' + res + ')');
				if(result.status==true){
					jQuery('#techSearchStockDetail').html(result.msg);
					jQuery(".techSellDate" ).datepicker({
						dateFormat: "mm/dd/yy",
						changeMonth: true,
						changeYear: true,
						showOn: "button",
						buttonImage: host+"/images/datepicker/calendar_icon.png",
						buttonImageOnly: true,
						buttonText: 'Choose Date'
					});
				}
			}
		});
	}
}

function saveTechStratSellTrans(){
	var valid = false;
	var numberChecked=jQuery("input.techSellCheckBox[type=checkbox]:checked").length;
	if(numberChecked<1){
		valid = false;
	}else{
		valid=true;
	}
	    		
	if(valid==true){
		jQuery('#mode').val('save');
		var modeval=jQuery('#mode').val();
		if(modeval=='save'){
		    jQuery('#symboltransa').submit();
		}				
	}else if(valid==false){
        alert('Please select the transaction.');
        return false;
    }
}

function validateTechSellData(id){
	var stockcode=parseInt(id);
	eval("chkboxid=document.symboltransa.sellchk_"+stockcode); // chkboxid== object
	var getchkidstatus=chkboxid.checked;
	var buydateref, buysharesref, saleqtyref, saledateref, sellpriceref, buydate, buyshares, saleqty, sellprice, saledate;
	eval("buydateref=document.symboltransa.buydate_"+stockcode);
	eval("buysharesref=document.symboltransa.buyqty_"+stockcode);
	eval("saleqtyref=document.symboltransa.sellqty_"+stockcode);
	eval("sellpriceref=document.symboltransa.sellprice_"+stockcode);
	eval("saledateref=document.symboltransa.selldate_"+stockcode);
	var buyDateTime=buydateref.value;
	var saleDateTime=saledateref.value;

	resCheckDate=chngdateformat(buyDateTime);
	sellDt=chngdateformat(saleDateTime);

	if(getchkidstatus==true && getchkidstatus!=''){
		buydate=buydateref.value;
		buyshares=parseFloat(buysharesref.value);
		saleqty=parseFloat(saleqtyref.value);
		saledate=saledateref.value;
		sellprice=sellpriceref.value;

		if(saleqty=='' || saleqty==0){
			alert("Invalid Sell Qty.");
			chkboxid.checked=0;
			saleqtyref.focus();
			return false;
		}else if(saleqty>buyshares){
			alert("Sell Qty must be less than equal to Buy Qty.");
			chkboxid.checked=0;
			saleqtyref.focus();
			return false;
		}else if(sellprice=='' || sellprice=='0.00'){
			alert("Invalid Sell Price.");
			chkboxid.checked=0;
			sellpriceref.focus();
			return false;
		}else if(saledate==''){
			alert("Invalid Sell Date.");
			chkboxid.checked=0;
			NewCal(saledateref.id,'mmddyyyy');
			return false;
		}else if(sellDt<resCheckDate){
			alert("Sell Date Should be Greater than or equal to Buy Date.");
			chkboxid.checked=0;
			return false;
		}
	}else{
		if(getchkidstatus==false && getchkidstatus==''){
		}
	}
}
