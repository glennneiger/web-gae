
function getSearchResult(object_type,searchmsg,contrib_id){
	var valtext=$("qsearch").value;
	var valtitle=$("titlesearch").value;
    var selObj = document.getElementById("day");
    var searchArchive = $("searchArchive").value;
    if(searchArchive=='' || searchArchive==undefined){
    	searchArchive='0';
    }
    var valday = selObj.options[selObj.selectedIndex].text;
	if(object_type==undefined){
		object_type='';
	}
	if((valday=='-Day-')||(object_type=='7')){
		var valday='';
	}
	var valmonth=$('mo').value;
	if((valmonth=='-Month-') || (object_type=='7')){
		var valmonth='';
	}
	var selObj = document.getElementById("year");
    var valyear = selObj.options[selObj.selectedIndex].text;
	if((valyear=='-Year-')||(object_type=='7')){
		var valyear='';
	}
	if(contrib_id != '') {
	valauthor = contrib_id;
	}
	else {
	var selObj = document.getElementById("contrib_id");
    var valauthor = selObj.options[selObj.selectedIndex].value;
		if((valauthor=='-All Authors-')||(object_type=='7')||(object_type=='6') || (object_type=='20')){
		var valauthor='';
	}	
	}
	searchResult(valtext,valday,valmonth,valyear,valauthor,object_type,searchmsg,valtitle,searchArchive);
}

function searchResult(valtext,valday,valmonth,valyear,valauthor,object_type,searchmsg,valtitle,searchArchive){
	var url = 'search_mod.htm';
	var serach=1;
	var showtabs=1;
	var pars = 'search=' + serach + '&showtabs=' + showtabs + '&q=' + valtext + '&day=' + valday + '&mo=' + valmonth + '&year=' + valyear + '&contrib_id=' + valauthor + '&object_type=' + object_type + '&searchmsg=' + searchmsg + '&title=' + valtitle + '&searchArchive=' + searchArchive;
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onLoading:inProgress('searchprogress','Loading..'), 
							onComplete:function(req)
										{
											if(req.responseText)
											{ 
											  inProgress('searchprogress','')
											  $("oid").value=object_type;
											  var $kids = jQuery("#search_tab>div");		
											  $kids.each(function()
														{
											  
													   if((this.id).split("_")[1] == object_type)
													   {
														   this.className = "search_nav_tab_selected";
													   }
													   else
													   {
														   this.className = "search_nav_tab";
													   }
													});																							
																						
											  $("searchcontent").innerHTML=req.responseText;
											}
										}
							});
}

/*function getResultData(req,object_type){
	if(req.responseText){
		inProgress('searchprogress','')
		$("searchcontent").innerHTML=req.responseText;
		$("searchcontent").innerHTML='';
		$("showdesignsearch").innerHTML='';
	}
}*/
function inProgress(fieldId,msg)
{
	$(fieldId).innerHTML=msg;
}

function searhboxempty(){
	$("qsearch").value="";
}

function searchtitleboxempty(){
	$("titlesearch").value="";
}

function searchEnterKeyChk(evt)
{
	evt = (evt) ? evt : ((event) ? event : null);
	var evver = (evt.target) ? evt.target : ((evt.srcElement)?evt.srcElement : null );
	var keynumber = evt.keyCode;
	if(keynumber==13)
	{
		submitSearch("archive");
	}
}

function submitSearch(type){
	if(type=="archive"){
		jQuery('#searchArchive').val('1');
	}else{
		jQuery('#searchArchive').val('0');
	}
	searchform.submit();
}