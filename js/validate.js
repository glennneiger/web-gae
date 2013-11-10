// JavaScript Document

/*validate email*/
function isValidEmail(field){
	
if(!field.value) return true;
var emails=field.value.split(';');
for (var i=0;i<emails.length;i++)
{
	if(emails[i]=='')
	{
		  alert('Unexpected ;') 
		   field.select();
		  field.focus();
		  return false;
	}
	if(!(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/.test(emails[i])))
	{
		  alert('Not a valid E-mail id "'+emails[i]+'"') 
		   field.select();
		  field.focus();
		  return false;
	}
}
return true;
}
/*
Feild should only contain Numeric values.
*/
function validateNumericField(field) {
	string = trim(field.value);
	if (!string) return true;
	var bValid =new Boolean(true);
    var Chars = "0123456789";

    for (var i = 0; i < string.length; i++) {
       if (Chars.indexOf(string.charAt(i)) == -1)
	   {
		     bValid=false; 
		}
     }
	 if(!bValid)
	{
		var msg='Not a valid '
		var long="";
		if(field.name.substring(0,4)=='port')
			long="Port.";
		if(field.name=='pollinterval' || field.name.substring(0,12)=='pollInterval')
			long="Poll Interval.";

			
				alert(msg + "" + long);
			field.select();
			field.focus();
	}
    return bValid;
} 
/*
Feild should only contain Text values 
not any numeric not even Special characters are allowed.
*/

function validateAlphaNumericField(field) {
	string = trim(field.value);
    if (!string) return false;
	var bValid =new Boolean(true);
    var Chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ []-_";

	
    for (var i = 0; i < string.length; i++) {
       if (Chars.indexOf(string.charAt(i)) == -1)
        {
		    bValid=false; 
		}
    }
	
	//if(string.charAt(0)==" ") bValid=false;
	if(string.charAt(0)=="-") bValid=false;
	if(string.charAt(0)=="_") bValid=false;
		
	if(string.length==1 && string.charAt(0)=="[" || string.charAt(0)=="]") bValid=false;
	//if(!validateNumericField(field)) bValid=false;
	
	var nValid =new Boolean(true);
	var Chars = "0123456789";

    for (var i = 0; i < string.length; i++) {
		
       if (Chars.indexOf(string.charAt(i)) == -1)
	   {
		     nValid=false; 
		}
     }
	 if(nValid==true) bValid=false;
	if(!bValid)
	{
		 var msg='Not a valid'
		 var long=''
		if(field.name=='silo_name')
				long=msg+" "+'Silo Name.';
		if(field.name.substring(0,10)=='deviceName')
				long=msg+" "+ 'Device Name.';
		if(field.name=='actionName')
				long=msg+" "+ 'Action Name.';
		if(field.name=='view_name')
				long=msg+" "+ 'View Name.';		
		if(field.name=='bus1')
				long=msg+" "+ 'Display Name.';		
		if(field.name=='bus2')
				long=msg+" "+ 'Display Name.';		
				
			alert(long);
		  field.select();
		 field.focus();
	}
    return bValid;
} 

/* 
Text must be Alpha */
function validateAlphaField(field) {
	string = field.value
    if (!string) return true;
	var bValid =new Boolean(true);
    var Chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    for (var i = 0; i < string.length; i++) {
       if (Chars.indexOf(string.charAt(i)) == -1)
       {
		      bValid=false; 
		}
    }
	
	 if(!bValid)
	{
		var msg='Not a valid '
		var long="";
		if(field.name=='view_name')
			long="View Name.";
		if(field.name=='userFirstName')
			long="First Name.";
		if(field.name=='userSecondName')
			long="Last Name.";
		if(field.name.substring(0,4)=='unit')
			long="Unit.";
		
		alert(msg + long);
		
		  field.select();
		 field.focus();
	}
    return bValid;
} 

/*Is valid Password. Length must be 6-10
*Characters must be alpha numeric only.*/
function isValidPassword(field){
	string = field.value
    if (!string) return false;
	var bValid =new Boolean(true);
	
	if(string.length >15 || string.length <6) bValid=false;;
    var Chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	for (var i = 0; i < string.length; i++) {
    if (Chars.indexOf(string.charAt(i)) == -1)
       {
		      bValid=false; 
		}
    }
	 if(!bValid)
	{
		var msg='Not a valid '
		var long="";
		if(field.name=='password')
			long="Password.";
		if(field.name=='confirmPassword')
			long="Re-type Password.";
		alert(msg + long);
		  field.select();
		 field.focus();
	}
    return bValid;
}
// for  white space 
function trim(str, chars) {
	return ltrim(rtrim(str, chars), chars);
}

function ltrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}

function rtrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}

