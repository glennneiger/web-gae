// JavaScript Document
var xmlHttp;
var vardiv;

function saveSubs(){

	var err="";	
	var stremail = document.getElementById("contestemail").value;
	var strname = document.getElementById("contestname").value;
	var strphone = document.getElementById("contestphone").value;
	var emails=stremail.split(';');
	for (var i=0;i<emails.length;i++)
	{
		if(emails[i]=='')
		{
			  alert('Email cannot be left as blank.');
			  return false;
		}
		if(!(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/.test(emails[i])))
		{
			  alert('Not a valid E-mail id "'+emails[i]+'"');
			  return false;
		}
	}
	if(strname==''){
		alert('Name cannot be left as blank.');
		  return false;
	}
	NewLogin_contest(emails,strname,strphone);
}

function NewLogin_contest(emails,strname,strphone)
{		
	xmlHttp=GetXmlHttpObject();
	height=50;
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
		return;
	}
	var url = host + '/articles/congrates.htm?flag=contest&email='+emails+'&name='+strname+'&phone='+strphone;
	
	vardiv='contest';
	xmlHttp.open("GET",url,true);
	xmlHttp.onreadystatechange=function() 
	{ 
		stateChange(vardiv); 
	};
	xmlHttp.send(null);
}



