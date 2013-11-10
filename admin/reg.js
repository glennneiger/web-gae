function validateUserPass(){
	var frm=findObj("regform");
	var err="";
	if(!valid_email(frm["subs[email]"].value))
		err+="  -Invalid e-mail address (username).\n";
	if( !getVal(frm["subs[password]"]).length)
		err+="  -Missing Password.\n";
	return err;
}

function validateName(){
	var frm=findObj("regform");
	var err="";
	var fpfx="subs";
	var fields={
		fname:"Missing first name.",
		lname:"Missing last name."
	};
	for(e in fields){
		fieldname=fpfx+"["+e+"]";
		if(!getVal(frm[fieldname]))
			err+="  -"+fields[e]+"\n";
	}

	return err;
}

function validateBilling(hideBill){
	var frm=findObj("regform");
	var billpfx="subs";
	var coun=selectedText("subs[country]");
	var err="";
	var minlen=5
	if(coun=="United States"){
		frm["subs[zip]"].value=numsonly(frm["subs[zip]"].value);
		if(!selectedValue("subs[state]"))
			err+="  -Missing "+(hideBill?"":"billing ")+"state.\n";
	}else{
		minlen=2;
		frm["subs[state]"].selectedIndex=0;
	}
	if(frm["subs[zip]"].value.length<minlen)
		err+="  -Missing or invalid "+(hideBill?"":"billing ")+"zip code.\n";
	var billfields={
		"address" : "Missing "+(hideBill?"":"billing ")+"address line 1.",
		"city"	   : "Missing "+(hideBill?"":"billing ")+"city."
	};
	for(e in billfields){
		fieldname=billpfx+"["+e+"]";
		if(!getVal(frm[fieldname]))
			err+="  -"+billfields[e]+"\n";
	}
	return err;
}

function validateShipping(){
	var frm=findObj("regform");
	var shippfx="subs";
	var coun=selectedText("ship[country]");
	var err="";
	var minlen=5;
	if(coun=="United States"){
		frm["subs[zip]"].value=numsonly(frm["subs[zip]"].value);
		if(!selectedValue("subs[state]"))
			err+="  -Missing shipping state.\n";		
	}else{
		minlen=2;
		frm["subs[state]"].selectedIndex=0;
	}
	if(frm["subs[zip]"].value.length<minlen)
		err+="  -Missing or invalid shipping zip code.\n";
	var shipfields={
		"address1" : "Missing shipping address line 1.",
		"city"	   : "Missing shipping city."
	};
	for(e in shipfields){
		fieldname=shippfx+"["+e+"]";
		if(!getVal(frm[fieldname]))
			err+="  -"+shipfields[e]+"\n";
	}
	return err;
}

function validateSchoolPsycho(){
	var frm=findObj("regform");
	var err="";
	if(!getVal(frm["subs[school]"]))
		err+="  -Please tell us what school you attend.\n";
	if( !selectedText("subs[school_major]",1) )
		err+="  -Please select what you are currently studying.\n";
	if( !selectedText("subs[agegroup]",1) )
		err+="  -Please select your age group.\n";
	if( !selectedText("subs[heardfrom]",1) )
		err+="  -Please tell us where you heard about Minyanville.\n";		
	return err;
}
function validatePsycho(){
	var frm=findObj("regform");
	var err="";
	
	if(!getVal(frm["subs[jobtitle]"]))
		err+="  -Please tell us your occupation.\n";
	if( !selectedText("subs[agegroup]",1) )
		err+="  -Please tell us your age group.\n";			
	
/*
	if(frm["subs[income]"].selectedIndex<1)
		err+="  -Please tell us your household income.\n";
	if(frm["subs[heardfrom]"].selectedIndex<1)
		err+="  -Please tell us where you heard about Minyanville.\n";
	if(frm["subs[connection]"].selectedIndex<1)
		err+="  -Please tell us your internet connection speed\n";*/
	return err;
}

function validateEduEmail(dev_site){
	var frm=findObj("regform");
	var err="";
	if(frm["subs[email]"].value.indexOf(".edu")==-1 && !dev_site){
		err+="  -You don't appear to be coming from a .edu address."
			+" You may not be eligable for a Student Visa.\n";
	}
	return err;
}

function validateCCInfo(){
	var frm=findObj("regform");
	var err="";
	var code=frm["subs[cc_cvv2]"].value;
	
	if(!validateCCNum(selectedValue("subs[cc_type]"),frm["subs[cc_num]"].value) )
		err+="  -Credit Card Number is invalid.\n";
	
	if(!isValidExpDate(frm["subs[cc_expire]"].value))
		err+="  -Credit Card Expiration is invalid or outdated.\n";
	if(!code||code.length<3)
		err+="  -Credit Card security code is invalid.\n";
	return err;
}



function validateCCNum(cardType,cardNum){
	cardType = cardType.toUpperCase();
	var cardLen = cardNum.length;
	var firstdig = cardNum.substring(0,1);
	var seconddig = cardNum.substring(1,2);
	var first4digs = cardNum.substring(0,4);

	switch (cardType){
		case "VISA":
			return (((cardLen == 16) || (cardLen == 13)) && (firstdig == "4"));
		case "AMEX":
			var validNums = "47";
			return ((cardLen == 15) && (firstdig == "3") && (validNums.indexOf(seconddig)>=0));
		case "MASTERCARD":
			var validNums = "12345";
			return ((cardLen == 16) && (firstdig == "5") && (validNums.indexOf(seconddig)>=0));
		case "DISCOVER":
			return  ((cardLen == 16) && (first4digs == "6011"));
		case "DINERS":
			var validNums = "068";
			return ((cardLen == 14) && (firstdig == "3") && (validNums.indexOf(seconddig)>=0));
	}
	return 0;
}

function LuhnCheck(str) {
  var result = true;
  var sum = 0; 
  var mul = 1; 
  var strLen = str.length;
  for (i = 0; i < strLen; i++) {
    var digit = str.substring(strLen-i-1,strLen-i);
    var tproduct = parseInt(digit ,10)*mul;
    if (tproduct >= 10)
      sum += (tproduct % 10) + 1;
    else
      sum += tproduct;
    if (mul == 1)
      mul++;
    else
      mul--;
  }
  if ((sum % 10) != 0)
    result = false;
  return result;
}

function isValidExpDate(mmyy){
	mmyy=mmyy.split("/");
	if(mmyy[0].charAt(0)=="0")mmyy[0]=mmyy[0].substring(1);
	mo=parseInt(mmyy[0]);yr=parseInt("20"+mmyy[1]);
	if(!mo||isNaN(mo) || !yr ||isNaN(yr) || (mo<1||mo>12))
		return 0;
	var thisYear=new Date().getYear();
	var thisMo=new Date().getMonth();
	if(thisYear>yr)return 0;
	return 1;
}


function Change(){
	with(document.theform){
		action="./";
		submit()
	}
}

function Change_trial(){
	with(document.theform){
		action="./manage.htm";
		submit()
	}
}


function toggletype(element)
{
	radio = document.getElementById(element);
	radio.checked = radio.checked ? false : true;
	if(radio.id=="subs[type]newyear" && radio.checked== true)
		checkbuzzcombo();
    return true;
}

function togglecombooptions(combo,combo_products,subtype)
{
	combo_radio=document.getElementById(combo);
	if(combo_radio.checked == true)
	{
		for (i=0; i<combo_products.length; i++) {
			if(combo_products[i]==0)
			{
				radio=document.getElementById('subs[type]'+subtype);
				radio.checked=false;
			}
			else{
				radio=document.getElementById("subs[ptype]["+combo_products[i]+"]"+subtype);
				radio.checked=false;
			}
		}
	}
}

function checkforcombo(product_id,subtype,combo_id)
{
		buzzradio = document.getElementById('subs[type]'+subtype);
		productradio = document.getElementById("subs[ptype]["+product_id+"]"+subtype);
		if(productradio.checked == true && buzzradio.checked==true)
		{
			alert("You have chosen an annual subscription to the Buzz & Banter and Cooper's Daily Market Report.  We have a discounted combo package of $999 per year for this subscription.");
			buzzradio.checked=false;
			productradio.checked=false;
			comboradio=document.getElementById("subs[ctype]["+combo_id+"]"+subtype);
			comboradio.checked=true;
		}
}


// Please aviod use of this function this is hardcoded on special request of client
function checkbuzzcombo()
{
		var buzzradio = document.getElementById("subs[type]newyear");
		var productradio = document.getElementById("subs[ptype][1]newyear");
		if(productradio.checked == true && buzzradio.checked==true)
		{
			alert("You have chosen an annual subscription to the Buzz & Banter and Cooper's Daily Market Report.  We have a discounted combo package of $999 per year for this subscription.");
			buzzradio.checked=false;
			productradio.checked=false;
			comboradio=document.getElementById("subs[ctype][1]newyear");
			comboradio.checked=true;
		}
		return true;
}