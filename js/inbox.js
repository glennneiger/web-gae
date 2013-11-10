var xmlHttp
var vardiv

function readmessage(userid,conv_ids,vardiv1,read_status,PHP_SELF,offsetread,offsetunread,pageName)
{      	/* check for read message to change status - save entry in table */

		var readstatus='1';
		/* check end here */
	    xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
		{
			alert ("Your browser does not support AJAX!");
			return;
		} 
        var vardiv='message';
		targetdiv='cntrl_pnl_inbox';
		height=200;
		var url= host + '/community/inbox_message.htm?userid=' + userid + '&conv_ids=' + conv_ids + '&vardiv1=' + vardiv1 + '&read_status=' + read_status + '&PHP_SELF=' + PHP_SELF + '&offsetread=' + offsetread + '&offsetunread=' + offsetunread + '&pageName=' + pageName + '&readstatus=' + readstatus;
		xmlHttp.open("GET",url,true);
		xmlHttp.onreadystatechange=function() { statesChanges(vardiv,targetdiv); };
		xmlHttp.send(null);
}

function markunread(userid,conv_ids,vardiv1,read_status,PHP_SELF,offsetread,offsetunread,pageName)
{      
        varmark_read_unread='1';
	    xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
		{
			alert ("Your browser does not support AJAX!");
			return;
		} 
        var vardiv='message';
		targetdiv='cntrl_pnl_inbox';
		height=400;
		var url= host + '/community/inbox_message.htm?userid=' + userid + '&conv_ids=' + conv_ids + '&vardiv1=' + vardiv1 + '&read_status=' + read_status + '&PHP_SELF=' + PHP_SELF + '&offsetread=' + offsetread + '&offsetunread=' + offsetunread + '&varmark_read_unread=' + varmark_read_unread + '&pageName=' + pageName;
		xmlHttp.open("GET",url,true);
		xmlHttp.onreadystatechange=function() { statesChanges(vardiv,targetdiv); };
		xmlHttp.send(null);
}

function deleterecordread(userid,vardiv,read_status,PHP_SELF,offsetread,offsetunread,pageName){
  var delselected='1';
  var conv_id= new Array();
  for(i=0;i<document.inbox.elements.length;i++){
	 if(document.inbox.elements[i].checked){
		    for (j=i;j<=i;j++) {
			conv_id[j]=document.inbox.elements[i].value;  
			}	
		}
   }   
   if(conv_id==''){
	   alert('Select check box');
   } else {
    deletemessage(userid,conv_id,vardiv,read_status,PHP_SELF,offsetread,offsetunread,delselected,pageName);
   }

 }
 
function deleterecord(userid,vardiv,read_status,PHP_SELF,offsetread,offsetunread,pageName){
  var delselected='1';
  var conv_id= new Array();
  for(i=0;i<document.inbox.elements.length;i++){
	 if(document.inbox.elements[i].checked){
		   for (j=i;j<=i;j++) {
			conv_id[j]=document.inbox.elements[i].value;  
			}	
		}
   }   
  if(conv_id==''){
	   alert('Select check box');
   } else {
    deletemessage(userid,conv_id,vardiv,read_status,PHP_SELF,offsetread,offsetunread,delselected,pageName);
   }

 }

function deletemessage(userid,conv_ids,vardiv1,read_status,PHP_SELF,offsetread,offsetunread,delselected,pageName)
{       vardel='1';
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
		{
			alert ("Your browser does not support AJAX!");
			return;
		} 
        var vardiv='message';
		targetdiv='cntrl_pnl_inbox';
		height=200;
		var url= host + '/community/inbox_message.htm?userid=' + userid + '&conv_ids=' + conv_ids + '&vardel=' + vardel + '&vardiv1=' + vardiv1 + '&read_status=' + read_status + '&PHP_SELF=' + PHP_SELF + '&offsetread=' + offsetread + '&offsetunread=' + offsetunread + '&delselected=' + delselected + '&pageName=' + pageName;
	
		xmlHttp.open("GET",url,true);
		xmlHttp.onreadystatechange=function() { statesChanges(vardiv,targetdiv); };
		xmlHttp.send(null);
}

/* function to check mail id */
function chek_email_id(id,vardiv,lang_msg,lang_email,rand,attr,page_from,badwords){	   
	    var to = document.getElementById('to_email').value;
        strless=to.lastIndexOf('<');
		if(strless>0){
			strgtr=to.lastIndexOf('>');
			str=to.substring(strless+1,strgtr)
			to=str;
		}
	    xmlHttp=GetXmlHttpObject();
		if(to!==''){
		if (xmlHttp==null)
		{
			alert ("Your browser does not support AJAX!");
			return;
		} 
         height=20;
		var url=host + '/community/compose_email_check.htm?id=' + id + '&to=' + to + '&lang_email=' + lang_email + '&rand=' + rand;
         xmlHttp.open("GET",url,true);
		
		xmlHttp.onreadystatechange=function() { stateChangeforemail(rand,id,vardiv,lang_msg,attr,page_from,badwords); };
		
		
		xmlHttp.send(null);
		} else {
		alert("Enter email-id");	
		document.getElementById('to_email').focus();
		}
}

/* function to compose message    */
function composemessage(id,vardiv,lang_msg,attr,page_from,badwords,email)
{   
	 if(email==null){
        var to = document.getElementById('to_email').value;
	 } else {
		var to=email;  
	 }
	 
        strless=to.lastIndexOf('<');
		if(strless>0){
			strgtr=to.lastIndexOf('>');
			str=to.substring(strless+1,strgtr)
			to=str;
		}
	 
	    var title = document.getElementById('Subject').value;
		var message = document.getElementById('message[body]').value;
		var matchedbadwords="";
	var objWordLength=new Object();
	objWordLength.myParameter=false;
	// match bad words in subject
	matchedbadwords+=matchBadWords(badwords,title,matchedbadwords,objWordLength,43);	
	//match bad words in comment
	matchedbadwords=matchBadWords(badwords,message,matchedbadwords,objWordLength,43);
	
	if(objWordLength.myParameter==true){
		alert('Some words are too long.');
		return false;
	}
	
	// match bad words in subject
	//matchedbadwords+=matchBadWords(badwords,strtitle,matchedbadwords);	
	//match bad words in comment
	//matchedbadwords=matchBadWords(badwords,strbody,matchedbadwords);
		
	//if bad words are found in posted words.
	if(matchedbadwords.length>0){
		alert('Following words are not allowed.\n'+matchedbadwords);
		return false;
	}	

		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
		{
			alert ("Your browser does not support AJAX!");
			return;
		} 
        height=20;
		var url=host + '/community/compose_message.htm';
		
		var post='id=' + id + '&to=' + to  + '&title=' + title + '&message=' + message + '&lang_msg=' + lang_msg  + '&page_from=' + page_from + '&attr=' + attr;
		xmlHttp.open("POST",url,true);
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
		xmlHttp.onreadystatechange=function() { stateChange(vardiv); };
		xmlHttp.send(post);
}   /* function end here to compose message */


function stateChangeforemail(vardivid,id,vardiv,lang_msg,attr,page_from,badwords) 
{  
	if (xmlHttp.readyState==4)
	{  
		if (xmlHttp.status==200)
		{ 
		
			document.getElementById(vardivid).innerHTML=xmlHttp.responseText;
					if(xmlHttp.responseText==''){
                       
						composemessage(id,vardiv,lang_msg,attr,page_from,badwords);
						
		   			}
		}
		else
		{
			document.getElementById(vardivid).innerHTML=xmlHttp.statusText;
		}
	}
}
