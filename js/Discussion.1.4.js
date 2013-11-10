function checkprevilages(divId,url,chk){
	if(chk='true')
	{
		alert("Sorry! You're not authorized to do this!");
		return false;
	}
}
// remove white spaces from left and right
function trim(str)
{
	
	while(''+str.charAt(0)==' ')
		str=str.substring(1,str.length);
	
	while(''+str.charAt(str.length-1)==' ')
		str=str.substring(0,str.length-1);
	
	return str;
}

function go_search(url,type,txt){
	var txtsearch=document.getElementById(txt).value;
	if(txtsearch=='Search Keywords or Symbols'){
		txtsearch="";
	}
	var txtsort=document.getElementById('Selectcat').value;
	
	var str='';

	str=url+'?q='+txtsearch+'&type='+type;
	
	if(txtsort!='')
		str=str+'&Selectcat='+txtsort;
	
	location.href=str;
	
}

function setRequestTimeout(divId){
	window.clearTimeout(timeoutId);
	timeoutId=window.setTimeout("showFailure("+divId+");",5000);
}

function showFailure(divId){

	var strDiv=document.getElementById(divId);

	if(strDiv==null){
		strDiv=divId;
	}
	
	http.abort();
	strDiv.innerHTML="<center><font color='red'>Try Again.</font></center>";
}

// This function does the AJAX request
function preHttpRequest(divId,url,width) {
	if(divId=='divPost'){
		document.getElementById("hid").style.display="";
	}
	
	http.open("GET",url, true);
    
	http.onreadystatechange=function() {
		if (http.readyState==4 && http.status == 200) {
			http.onreadystatechange = getHttpRes(divId);
			// add for loading refreshing
			divId='';
		}
		else{
			//alert (http.readyState+"---"+http.status);
			height=20;
			if (divId!="")
			{
				showProgress(divId,width);
			}
			
		}
	}

	http.send(null); 

}// end of preHttpRequest function


// This function does the AJAX post request
function preHttpRequestPost(divId,url,post) {	
	url= host + '/community/Save.php';
	if(divId=='divPost'){
		document.getElementById("hid").style.display="";
	}
	
	height=20;
	
	http.open("POST",url, true);
	
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
	http.onreadystatechange=function() {
		
		if (http.readyState==4 && http.status == 200) {
			
			http.onreadystatechange = getHttpRes(divId);
		}
		else{
			height=20;
			showProgress(divId);
		}
		
	}
	
	http.send(post);
}// end of preHttpRequestPost function


// get response text
function getHttpRes(divId) {

	// These following lines get the response and update the page
	res = http.responseText;
		
	document.getElementById(divId).innerHTML = "";
	document.getElementById(divId).innerHTML = res; 

	var strLocation=window.location;
	var strQueryString=strLocation.toString();
	var strFinalString=strQueryString.split('#'); 
	
	if(divId=='txtpost'){
		
		if(strFinalString.length>1){
			location.href=strFinalString[0]+'#txtpostform';
		}
		else{
			location.href=window.location+'#txtpostform';
		}
	}
		
	if(divId=='commentposting'){
		var str=document.getElementById('post_id');
		
		if(strFinalString.length>1){
			
			location.href=strFinalString[0]+'#'+str.value;
		}
		else{
			location.href=window.location+'#'+str.value;
		}		
	}

	if(divId=='readmore'){
		if(strFinalString.length>1){
			location.href=strFinalString[0]+'#threadbody';
		}
		else{
			location.href=window.location+'#threadbody';
		}		
	}
	
}

function getXHTTP() {
	var xhttp;
	// The following "try" blocks get the XMLHTTP object for various browsers…
	try {   
		xhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } 
	catch (e) {
		try {
			xhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} 
		catch (e2) {
			// This block handles Mozilla/Firefox browsers...
			try {
				xhttp = new XMLHttpRequest();
			} 
			catch (e3) {
				xhttp = false;
			}
		}
    }
	return xhttp; // Return the XMLHTTP object
}

function matchBadWords(badwords,strbody,matchedbadwords){	
	// getting array of bad words
	badwords=badwords.toLowerCase();
	var strbadwords=badwords.split(',');
	
	// getting array of posted words
	var strpostwords=strbody.split('\n');
	

	//convert all text to lowercase
	strbody=strbody.toLowerCase();

	var intIndexOfMatch = strbody.indexOf( '\n' );
 	while (intIndexOfMatch != -1){
		strbody = strbody.replace( '\n', ' ' )
		intIndexOfMatch = strbody.indexOf( "\n" );
	}
	re = /\$|,|@|#|~|`|\%|\*|\^|\&|\(|\)|\+|\=|\[|\-|\_|\]|\[|\}|\{|\;|\:|\'|\"|\<|\>|\?|\||\\|\!|\$|\./g;
	strbody = strbody.replace(re, " ");
	strbody=' '+strbody+' ';
	// get bad word
	for(i=0; i<strbadwords.length;i++){
		strbadwords[i]=' '+strbadwords[i]+' ';
		
		//if bad word matched with text
		if(strbody.search(strbadwords[i])!=-1){
			//if badword is not already in the list.
			if(matchedbadwords.search(strbadwords[i])==-1){
				matchedbadwords+=strbadwords[i]+'\n';
			}
				
		}
		
	}//end of badword loop
	
	
	
	return matchedbadwords;
}
function stripHTML(oldString) {

   var newString = "";
   var inTag = false;
   for(var i = 0; i < oldString.length; i++) {
   
        if(oldString.charAt(i) == '<') inTag = true;
        if(oldString.charAt(i) == '>') {
              if(oldString.charAt(i+1)=="<")
              {
              		//dont do anything
	}
	else
	{
		inTag = false;
		i++;
	}
        }
   
        if(!inTag) newString += oldString.charAt(i);

   }
   return newString;
}

function postHttpRequest(frm,type,badwords,blog,content_table) {
	if(frm.id=='divPost'){
		frm='divPost';
	}
	
	str='txtTitle'+frm;
	var strsubject=document.getElementById(str).value;
	
	str='txtBody'+frm;
	var strbody=document.getElementById(str).value;
	
	str='subscription_id'+frm;
	var strsubscription_id=document.getElementById(str).value;
	
	str='thread_id'+frm;
	var strthread_id=document.getElementById(str).value;
	
	str='comment_id'+frm;
	var strcomment_id=document.getElementById(str).value;
	
	if(blog==1){
		str='blog'+frm;
		var strblog_id=document.getElementById(str).value;
	}
	

	if(type=='1'){
		str='owner_id'+frm;
		var strowner_id=document.getElementById(str).value;
		
		str='pcomment_id'+frm;
		var strpcomment_id=document.getElementById(str).value;
	}
	

/*
	if(strsubject==''){
		alert('Enter Subject');
		return false;
	}
*/	
	if(strbody==''){
		alert('Enter Comment');
		return false;
	}


	var matchedbadwords="";
	//var objWordLength=new Object();
	//objWordLength.myParameter=false;
	
	// match bad words in subject
	matchedbadwords+=matchBadWords(badwords,strsubject,matchedbadwords);	
	//match bad words in comment
	matchedbadwords=matchBadWords(badwords,strbody,matchedbadwords);
	/*
	if(objWordLength.myParameter==true){
		alert('Some words are too long.');
		return false;
	}
	*/
	//if bad words are found in posted words.
	if(matchedbadwords.length>0){
		alert('Following words are not allowed.\n'+matchedbadwords);
		return false;
	}

	
	var url='title='+encodeURIComponent(stripHTML(strsubject));
	url=url+'&post_text='+encodeURIComponent(stripHTML(strbody));
	url=url+'&poster_id='+strsubscription_id;	
	url=url+'&thread_id='+strthread_id;
	url=url+'&comment_id='+strcomment_id;
	url=url+'&Ptype=post';
	
	
	if(strblog_id==1){
		url=url+'&blog=1';
	}
		
	if(type=='1'){
		url=url+'&type=replynquote';
		url=url+'&owner_id='+strowner_id;
		url=url+'&pcomment_id='+strpcomment_id;
	}
	if(content_table){
		url=url+'&contenttable='+content_table;
	}
	//document.getElementById("hid").style.display="";
	document.getElementById('txtpost').innerHTML="";
	//preHttpRequestPost("divPost",'Save.php',url);
	
	preHttpRequestPost('commentposting','Save.php',url);
}


function msgHttpRequest(frm,badwords) {
	
	str='txtTitle';
	var strsubject=document.getElementById(str).value;
	
	str='txtBody';
	var strbody=document.getElementById(str).value;
	
	
	str='subscription_id';
	var strsubscription_id=document.getElementById(str).value;
	
	str='receiver_id';
	var strreceiver_id=document.getElementById(str).value;

	str='private_msg_id';
	var strprivate_msg_id=document.getElementById(str).value;
	

	if(strsubject==''){
		alert('Enter Subject');
		return false;
	}
	
	if(strbody==''){
		alert('Enter Message');
		return false;
	}
	var matchedbadwords="";
	
	//var objWordLength=new Object();
	//objWordLength.myParameter=false;

	// match bad words in subject
	matchedbadwords+=matchBadWords(badwords,strsubject,matchedbadwords);	
	//match bad words in comment
	matchedbadwords=matchBadWords(badwords,strbody,matchedbadwords);
	/*
	if(objWordLength.myParameter==true){
		alert('Some words are too long.');
		return false;
	}
	*/
	//if bad words are found in posted words.
	if(matchedbadwords.length>0){
		alert('Following words are not allowed.\n'+matchedbadwords);
		return false;
	}
	
	
	var url='title='+encodeURIComponent(strsubject);
	url=url+'&text='+encodeURIComponent(strbody);
	url=url+'&from_subscription_id='+strsubscription_id;	
	url=url+'&to_subscription_id='+strreceiver_id;
	url=url+'&private_msg_id='+strprivate_msg_id;
	url=url+'&Ptype=sendmsg';

	preHttpRequestPost(frm,'Save.php',url);
	
	
}

function doCancel(frm){
	if(frm.id=='divPost')
		frm='divPost';
	else if(frm.id=='txtpost')
		frm='txtpost';	
	
	document.getElementById(frm).innerHTML="";	
	//document.getElementById("hid").style.display="none";
}