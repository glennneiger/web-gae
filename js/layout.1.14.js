
function isNull(aStr)
{
	var index;		
	for (index=0; index < aStr.length; index++)
		if (aStr.charAt(index) != ' ')
			return false;
	return true;
}
function trim(str)
{
    s = str.replace(/^(\s)*/, '');
    s = s.replace(/(\s)*$/, '');
    return s;
}
function getPlaceholderList(page_id)
{
	
	$('placeholder_modules').innerHTML = "";
	for (count=$('selPlaceholder').options.length;count>=1;)
	{								  
		$('selPlaceholder').options[count] = null;		
		count--	;									
	}	
	var url = 'get_placeholder_detail.php';	
	var pars="page_id="+page_id;	
	new Ajax.Request(url, {
				method:'post',
				parameters: pars,
				onComplete:function(request) 
							{														
								var results = request.responseXML;
								var region  = results.getElementsByTagName("placeholder");				
								if(region.length>0)
								{															
									for(var i=0; i<= region.length-1; i++) 
									{											
										var placeholdername = region[i].getElementsByTagName('placeholdername')[0].childNodes[0].nodeValue;
										var placeholderid = region[i].getElementsByTagName('placeholderid')[0].childNodes[0].nodeValue;																				
										$('selPlaceholder').options[i+1] = new Option(placeholdername,placeholderid);		
									}
								}													
							}								
			});	
												
}	
function mangeModules(placeholder_id,module_id,action)	
{
	$("placeholder_modules").innerHTML = "";
	var page_id = $("selPage").value;
	var placeholder_id = $("selPlaceholder").value;
	var temp = new Array();
    temp = placeholder_id.split('-');
	var placeholder_id  =  temp[0];	
	var  mod_name  = temp[1];
	// for condition  chk
	if(mod_name == "Home Page Recently in Ville")
	{
	  mangemenu('placeholder_modules','',mod_name ,'manage_displayfeatured.php');	 
	}
	else
	{
		
		var timestamp = new Date().getTime();
		var url = 'manage_modules.php';
		var pars="page_id="+page_id+"&place_holder="+placeholder_id+"&time="+timestamp;	
		if(module_id)
		{
			pars += "&module_id="+module_id;
		}
		if(action)
		{
			pars += "&action="+action;
		}		
		var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,									   
											onComplete:function(req)
												  {
													$("placeholder_modules").innerHTML = req.responseText;
												  }
												}); 
	}
}
function loadTemplate(template_id)
{
	if($('selected_template'))
	{
		var tabList = $('selected_template').getElementsByTagName('tr');
		var tabArray = $A(tabList);
		for(i = 0;i<tabArray.length;i++)
		{			
			if(tabArray[i].id == "template"+template_id)
			{			
				tabArray[i].style.display = "";
			}
			else
			{
				tabArray[i].style.display = "none";
			}						
		}			
		$("final_content").innerHTML ="";
	}
}
function previewModule(action,module_id)
{	
	if(!module_id)
	{
		return;
	}
	var timestamp = new Date().getTime();
	var url = 'layout_module_preview.php';
	var pars="action="+action+"&time="+timestamp;	
	if(module_id)
	{
		pars += "&module_id="+module_id;
	}
	if(action == 'add')
	{
		pars += "&template_id="+$('selTemplate').value;
	}
	if($('selTemplate').value == 19){
		
	}else{
	
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,									   
										onLoading:showAdminProgress(),
										onComplete:function(req)
											  {		
												/*if($('selTemplate').value == 9)
												{
													$("final_content").innerHTML = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="672" height="270" title="Minyanville Feature"><param name="movie" value="/flash/featuredmodule/featured.swf?preview=1" /><param name="quality" value="high" /><param name="wmode" value="opaque" /><embed src="/flash/featuredmodule/featured.swf?preview=1" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="672" height="270" wmode="opaque"></embed></object>'
												}
												else*/ if($('selTemplate').value == 3)
												{
													$("final_content").innerHTML = "<div style='width:200px'>"+req.responseText+"</div>";
												}
												else if($('selTemplate').value == 9)
												{
													$("final_content").innerHTML = "<div style='width:215px'>"+req.responseText+"</div>";
												}
												else if($('selTemplate').value == 10)
												{
													$("final_content").innerHTML = "<div style='width:445px'>"+req.responseText+"</div>";
												}
												else if($('selTemplate').value == 12)
												{
													$("final_content").innerHTML = "<div style='width:215px'>"+req.responseText+"</div>";
												}else if($('selTemplate').value == 13)
												{
													$("final_content").innerHTML = "<div style='width:300px'>"+req.responseText+"</div>";
												}
												else if($('selTemplate').value == 15)
												{
													$("final_content").innerHTML = "<div style='width:223px'>"+req.responseText+"</div>";
												}
												else if($('selTemplate').value == 18)
												{
													$("final_content").innerHTML = "<div style='width:223px'>"+req.responseText+"</div>";
												}
												else if($('selTemplate').value == 20)
												{
													$("final_content").innerHTML = "<div style='width:215px'>"+req.responseText+"</div>";
												}
												else if($('selTemplate').value == 21)
												{
													$("final_content").innerHTML = "<div style='width:215px'>"+req.responseText+"</div>";
												}
												else if($('selTemplate').value == 22)
												{
													$("final_content").innerHTML = "<div style='width:215px'>"+req.responseText+"</div>";
												}
												else if($('selTemplate').value == 23)
												{
													$("final_content").innerHTML = "<div style='width:215px'>"+req.responseText+"</div>";
												}
												else if($('selTemplate').value == 24)
												{
													$("final_content").innerHTML = "<div style='width:215px'>"+req.responseText+"</div>";
												}
												else
 												{
 												 	$("final_content").innerHTML = eval(req.responseText);
												}
												finishAdminProgress();
 											  }
											}); 
	}
}
function openAddEditComponent(action,component_id,module_id)
{
	var timestamp = new Date().getTime();	
	var url="add_edit_module_component.php?action="+action+"&component_id="+component_id+"&module_id="+module_id+"&time="+timestamp;
	init_ibox('yes',url);
} 
function openAddEditfeaturedComponent(action,module_id)
{
	var timestamp = new Date().getTime();
	var url="edit_feature.php?action="+action+"&module_id="+module_id+"&time="+timestamp;
	init_ibox('yes',url);
} 
function getSelectedRadioButton(radObject)
{
	var selectedOption;
	for (var i=0; i<radObject.length; i++) 
	{        
		 if (radObject[i].checked) {
            selectedOption = radObject[i].value;
         }
    }
	return selectedOption
}
function saveFeatureTitle(action,component_id,module_id)
{
	var radObject = document.frm.radFeature;	
	var selectedOption = getSelectedRadioButton(radObject);	
	if(selectedOption == 'title')
	{
		var featureTitle = trim($('txtFeatureTitle').value)
		if(isNull(featureTitle))
		{
			$('txtFeatureTitle').focus();
			alert('Please enter a title');
			return;			
		}
		else
		{
			pars = "feature_title="+encodeURIComponent(featureTitle);
			saveModuleComponent(action,component_id,module_id,pars)
		}
	}
	else if(selectedOption == 'graphics')
	{
	}
	else
	{
		alert('Please select an option to proceed');
		return;
	}	
	
}
function saveTitle(action,component_id,module_id)
{
	var title = trim($('txtTitle').value)
	if(isNull(title))
	{
		$('txtTitle').focus();
		alert('Please enter a title');
		return;			
	}
	else
	{
		pars = "title="+encodeURIComponent(title);
		saveModuleComponent(action,component_id,module_id,pars)
	}
		
	
}
function saveLink(action,component_id,module_id)
{
	var title = trim($('txtTitle').value)
	var hyperlink = trim($('txtLink').value)
	var target = $('selLinkTarget').value
	if(isNull(title))
	{
		$('txtTitle').focus();
		alert('Please enter a title');
		return;			
	}
	else
	{
		pars = "title="+encodeURIComponent(title)+"&hyperlink="+encodeURIComponent(hyperlink)+"&target="+encodeURIComponent(target);
		saveModuleComponent(action,component_id,module_id,pars)
	}			
}
function saveFCKContent(action,component_id,module_id)
{	
	var text = trim($('txtFCKContent').value)	
	if(isNull(text))
	{
		$('txtFCKContent').focus();
		alert('Please enter the content');
		return;			
	}
	else
	{
		pars = "content="+text;
		saveModuleComponent(action,component_id,module_id,pars)
	}			
}
function searchArticles()
{
 
	 
	var keyword = $('txtKeyword').value;	
	var author_id = $('selAuthor').value;
	var month = $('selMonth').value;
	var date = $('selDate').value;
	var year = $('selYear').value;
	var url = 'article_search.php';
	var timestamp = new Date().getTime();
	var pars = "keyword="+keyword+"&author_id="+author_id+"&month="+month+"&date="+date+"&year="+year+"&time="+timestamp;
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,									   
										onComplete:function(req)
											  {												  
												  $('search_result_container').innerHTML = req.responseText;
											  }
											});
}

function searchItems()
{
	 
	var keyword = $('txtKeyword').value;	
	var author_id = $('selAuthor').value;
	var month = $('selMonth').value;
	var date = $('selDate').value;
	var year = $('selYear').value;
	var url = 'object_search.php';
	var timestamp = new Date().getTime();
	var pars = "keyword="+keyword+"&author_id="+author_id+"&month="+month+"&date="+date+"&year="+year+"&time="+timestamp;
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,									   
										onComplete:function(req)
											  {												  
												  $('search_result_container').innerHTML = req.responseText;
											  }
											});

}

function searchrecent()
{
	 
	var keyword = $('txtKeyword').value;	
	var author_id = $('selAuthor').value;
	var month = $('selMonth').value;
	var date = $('selDate').value;
	var year = $('selYear').value;
	var url = 'article_search.php';
	var timestamp = new Date().getTime();
	var pars = "keyword="+keyword+"&author_id="+author_id+"&month="+month+"&date="+date+"&year="+year+"&time="+timestamp;
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,									   
										onComplete:function(req)
											  {												  
												  $('search_result_container').innerHTML = req.responseText;
											  }
											});

}
function toggleSearchTab(object_type)
{
	if($('search_result_table'))
	{
		var searchTabList = $('search_result_table').getElementsByTagName('tr');
		var searchTabHeaderList = $('search _header').getElementsByTagName('td');
		var searchTabArray = $A(searchTabList);
		var searchHeaderArray = $A(searchTabHeaderList);
		for(i = 0;i<searchTabArray.length;i++)
		{		
			if(searchTabArray[i].id == "searched_"+object_type)
			{
				searchTabArray[i].style.display = "";
				searchHeaderArray[i].className = "article_tab_selected";
			}
			else
			{
				searchTabArray[i].style.display = "none";
				searchHeaderArray[i].className = "article_tab";
			}						
		}	
	}
}

function selectSerchedItems(title,value)
{
	var count=$('selItemList').options.length;
	$('selItemList').options[count] = new Option(title,value);
}

function addCustomUrl(){
	var custTitle=$('txtCustTitle').value;
	var custUrl=$('txtCustUrl').value;
	if(custTitle==""){
		alert('Please enter title.');	
		$('txtCustTitle').focus();
		return false;
	}
	if(custUrl==""){
		$('txtCustUrl').focus();
		alert('Please enter url.');	
		return false;
		
	}
	var urlCust=checkURL(custUrl);
	if(urlCust==true){
		var count=$('selItemList').options.length;
		
		var value=custUrl + ':' + custTitle;
		if(value.indexOf("http://")>=0){
			strUrl=value.replace("http://","");
		}
		if(value.indexOf("https://")>=0){
			strUrl=value.replace("https://","");
		}
		$('selItemList').options[count] = new Option(custTitle,strUrl);
	}else{
		$('txtCustUrl').focus();
		alert('Please enter valid url.');	
		return false;
	}
}

function checkURL(value) {
	//var urlregex = new RegExp("^(ftp|https?):\/\/(www\.)?[a-z0-9\-\.]{3,}\.[a-z]{3}$");
	var urlregex = new RegExp("^(https?|ftp):\/\/.*$");
	if(urlregex.test(value))
	{
		return(true);
	}
		return(false);
}

function removeItems(list_box) 
{
	var listbox = $(list_box);
	for(var i = 0;i < listbox.length;i++)
	{
		if (listbox.options[i].selected) 
		{
			listbox.remove(i);
		}
	}
}

function saveItemList(action,component_id,module_id)
{
	
	var selectedOption = "static_list";
	var selectedtype;
	if($('radCustomURL'))
	{
		var radOb = document.frm.radCustomURL;	
		selectedtype = getSelectedRadioButton(radOb);
	}
	if($('radOption'))
	{
		var radObject = document.frm.radOption;	
		selectedOption = getSelectedRadioButton(radObject);	
	}
	if(selectedtype && selectedtype == "customURL")
	{
		pars = "component_type=custom_url&custom_url="+encodeURIComponent($('txtCustomURL').value);
	}
	else if(selectedOption == 'dynamic_list')
	{
		var itemType = $('selItemType').value;
		if(itemType==32){
			var itemCat = $('selEduCat').value;
		}else{
			var itemCat = $('selItemCat').value;
		}
		var itemAuthor = $('selItemAuthor').value;
		var itemNumber = $('selItemNumber').value;
		var itemOrder = $('selItemCriteria').value;		
		pars = "item_type="+itemType+"&item_number="+itemNumber+"&item_order="+itemOrder+"&component_type="+selectedOption;
		if(itemCat != "")
		{
			pars += "&item_cat="+itemCat;
		}
		if(itemAuthor != "")
		{
			pars += "&item_author="+itemAuthor;
		}
	}
	else
	{		
		listbox = $('selItemList')
		if(listbox.length == 0)
		{
			alert("Please Select alteast one Item");
			return;
		}
		else
		{
			var selected_items="";
			for(var i = 0;i < listbox.length;i++)
			{	
				selected_items+=listbox[i].value+","
			}
			selected_items=encodeURIComponent(selected_items);
		}
		
		pars = "component_type="+selectedOption+"&selected_items="+selected_items;
	}	
	saveModuleComponent(action,component_id,module_id,pars)
}
function saveAuthor(action,component_id,module_id)
{		
		var desc = trim($('txaDesc').value);
		var author = $('selAuthor').value;		
		if(author == "")
		{
			alert("Please Select alteast one Proffesor");
			$('selAuthor').focus();
			return;
		}
		else
		{
			pars = "component_type=author_detail&author_desc="+encodeURIComponent(desc)+"&author="+encodeURIComponent(author);		
			saveModuleComponent(action,component_id,module_id,pars);
		}
}
function saveModuleComponent(action,component_id,module_id,pars)
{	
	// Apply encoding just for FCK content for rest its implemented from where they are passed
	if(pars.substring(0,10) == 'fckcontent')
	{
		arPars = pars.split("=");
		for(i=0;i<arPars.length;i++)
		{
			arPars[i] = encodeURIComponent(arPars[i]);
		}
		pars = arPars.join("=");
	}
	
	var timestamp = new Date().getTime();
	var url = 'save_module_component.php';		
	var pars = pars+"&action="+action+"&component_id="+component_id+"&module_id="+module_id+"&time="+timestamp;	
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,									   
										onLoading:showAdminProgress(),
										onComplete:function(req)
											  {
												 finishAdminProgress();
												 iboxclose();												 
												 previewModule('add',module_id);												  
											  }
											});
}
function deleteComponent(component_id,module_id)
{
	var timestamp = new Date().getTime();
	var url = 'save_module_component.php';		
	var pars = "action=delete&component_id="+component_id+"&module_id="+module_id+"&time="+timestamp;	
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,									   
										onLoading:showAdminProgress(),
										onComplete:function(req)
											  {
												 finishAdminProgress();
												 iboxclose();												 
												 previewModule('add',module_id);												  
											  }
											});
}
function saveModule()
{
	var moduleName = trim($('txtModuleName').value);
	var template = $('selTemplate').value;
	if(isNull(moduleName))
	{
		alert('Please enter a module name');
		$('txtModuleName').focus();
		return false;
	}
	else if(isNull(template))
	{
		alert('Please select a template');
		$('selTemplate').focus();
		return false;
	}
	return true;
}
function mangemenu(placeholder_id,module_id,action,url)	
{
	
	 
	$("placeholder_modules").innerHTML = "";
	if(placeholder_id != "")
	{
		var timestamp = new Date().getTime();
		var url = url;
		var pars="place_holder="+placeholder_id+"&time="+timestamp;	
		if(module_id)
		{
			pars += "&module_id="+module_id;
		}
		if(action)
		{
			pars += "&action="+action;
		}		
		var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,									   
											  onLoading:showAdminProgress(),
											  onComplete:function(req)
												  {
													$("placeholder_modules").innerHTML = req.responseText;
													finishAdminProgress();
												  }
												});  
	}
}
function mangemenuarticle(placeholder_id,module_id,action,url,method)	
{
	
	$("placeholder_modules").innerHTML = "";
	if(placeholder_id != "")
	{
		var timestamp = new Date().getTime();
		var url = url;
		var pars="place_holder="+placeholder_id+"&time="+timestamp;	
		if(module_id)
		{
			pars += "&module_id="+module_id;
		}
		if(action)
		{
			pars += "&action="+action;
		}
		if(method)
		{
			pars += "&method="+method;
		}
		var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,									   
											onLoading:showAdminProgress(),
											onComplete:function(req)
												  {
													$("placeholder_modules").innerHTML = req.responseText;
													finishAdminProgress();
												  }
												});  
	}
}
function moveprev(id)
{	
    var id = id;
	window.location = "manage_submenu.php?id="+id;
}
function preview(url)
{	
    var url = url;
	window.open (url);
}


function editmenu()
{	
  
	window.location = "manage_footermenu.php";
	
}
//js functions for  manage meta data page start here
	function returnpagename(){
		var pageid=$('pageid').value;
		var mod=1;
		var url = 'manage_metadata.htm';
		var pars = 'pageid=' + pageid + '&mod=' + mod;
		var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars, onComplete:getResultData});
	}

	function getResultData(req){
		if(req.responseText){
			$("showdiv").innerHTML=req.responseText;
			$("showlayout").innerHTML='';
		}
	}
	
	function insertPageData(){
		var pageid=$('pageid').value;
		var keyword=$("keyword").value;
		var title=$("title").value;
		var desc=$("description").value;
		if((pageid=='-All Pages-')||(pageid=="")){
			var msg='Please select page.';
			var mod=1;
			var pars = 'msg=' + msg + '&mod=' + mod;
			$('pageid').focus();
		} else if((keyword=='')||(title=='')){
		   if(keyword==''){
		   		var msg='Please enter keyword.';
				$("keyword").focus();
		   }
		    if(title==''){
		   		var msg='Please enter title.';
				$("title").focus();
		   }
		    var mod=1;
			var pars = 'msg=' + msg + '&mod=' + mod;
		 } else {
			var id=1;
			var mod=1;
			var keyword=$("keyword").value;
			var title=$("title").value;
			var desc=$("description").value;
			var pars = 'pageid=' + pageid + '&id=' + id + '&keyword=' + encodeURIComponent(keyword) + '&title=' + encodeURIComponent(title) + '&desc=' + encodeURIComponent(desc) + '&mod=' + mod;
		}
		    var url = 'manage_metadata.htm';
			var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars, onComplete:getInsertData});		
	}

	function getInsertData(req){
	   if(req.responseText){
		    $("showmsg").innerHTML="";
	   		$("showmsg").innerHTML=req.responseText;
	   }
		
	}

function save_featured_Component(action,component_id)
{
	var parsval = trim($('selItemList').value);
	if(isNull(parsval))
	{
	 $('selItemList').focus();
			alert('Please select an article.');
		return false;		 
	}
	// hidUploadedImageName this object comes from layout/admin/imageupload.php
	if($('hidImageAction').value == 'add' && !$('hidUploadedImageName'))
	{
		alert('Please upload an image.');
		return false;
	}
	else
	{
		if($('hidUploadedImageName'))
		{
			image_pars = "&image_name="+$('hidUploadedImageName').value; 
			// Image parameter will only go if user upload an image while adding/editing
		}	
	}
	var timestamp = new Date().getTime();
	var url = 'save_module.php';
	var  pars= "&selItemList="+parsval+"&action="+action+"&id="+component_id+"&time="+timestamp+image_pars;
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,
								         onLoading:showAdminProgress(),
										 onComplete:function(req)
											  {
												 iboxclose();
												 mangemenu('placeholder_modules','','HOME' ,'manage_displayfeatured.php');
												 finishAdminProgress();
											  }
											});
}
//js functions for  manage meta data page end here

function displayArticleCategory(obj)
{	
		if(obj == 1)
		{
			$('article_section_header').show();
			$('article_section').show();
			$('article_author_header').show();
			$('article_author').show();
			$('educategory').hide();
			$('eduCatHeader').hide();
		}
		else if(obj == 32){
			$('article_section_header').hide();
			$('educategory').show();
			$('eduCatHeader').show();
			$('article_section').hide();
			$('article_author_header').hide();
			$('article_author').hide();
		}
		else
		{
			$('article_section_header').hide();
			$('article_section').hide();
			$('article_author_header').hide();
			$('article_author').hide();
			$('educategory').hide();
			$('eduCatHeader').hide();
		}
}
function showAdminSubmenu(id,cnt,page_id){ 	
	$(selPage).value = page_id;
	getPlaceholderList(page_id);
	for(var i=1;i<=cnt;i++){	 
		if(id == i){			 
			$('tab_'+i).removeClassName('admin_menu_tab');
			$('tab_'+i).addClassName('admin_menu_tab_selected');						
			if(document.getElementById('sub_tab_'+id)){	
				var subMenuList = $('sub_tab_'+ i).getElementsByTagName('div');
				var subMenuArray = $A(subMenuList);
				subMenuCount = subMenuArray.length;
				showAdminSubmenuItem(id,null,subMenuCount,null); 			
				$('sub_tab_'+id).show();
			}			
		} else {			
			$('tab_'+i).removeClassName('admin_menu_tab_selected');
			$('tab_'+i).addClassName('admin_menu_tab');			
			if(document.getElementById('sub_tab_'+i)){						
				$('sub_tab_'+i).hide();
			}
		}				
	}
}
function showAdminSubmenuItem(id,itemid,cnt,page_id){  	
	if(page_id != null)
	{
	$(selPage).value = page_id;
	getPlaceholderList(page_id);
	}
	for(var i=1;i<=cnt;i++){	 
		if(itemid == i){			
			$('sub_tab_item_'+id+'_'+i).removeClassName('admin_menu_tab');		 			
			$('sub_tab_item_'+id+'_'+i).addClassName('admin_menu_tab_selected');					 
		} else {
			$('sub_tab_item_'+id+'_'+i).removeClassName('admin_menu_tab_selected');
			$('sub_tab_item_'+id+'_'+i).addClassName('admin_menu_tab');	
		}
	} 
}
function moveOptionsUp(selectId) {
 var selectList = document.getElementById(selectId);
 var selectOptions = selectList.getElementsByTagName('option');
 for (var i = 1; i < selectOptions.length; i++) {
  var opt = selectOptions[i];
  if (opt.selected) {
   selectList.removeChild(opt);
   selectList.insertBefore(opt, selectOptions[i - 1]);
     }
    }
}
function moveOptionsDown(selectId) {
 var selectList = document.getElementById(selectId);
 var selectOptions = selectList.getElementsByTagName('option');
 for (var i = selectOptions.length - 2; i >= 0; i--) {
  var opt = selectOptions[i];
  if (opt.selected) {
   var nextOpt = selectOptions[i + 1];
   opt = selectList.removeChild(opt);
   nextOpt = selectList.replaceChild(opt, nextOpt);
   selectList.insertBefore(nextOpt, opt);
     }
    }
}

function disp_confirm(placeholder_id,module_id,action,url,method,assets)
{
input_box=confirm("Are You Sure Want T0 Delete Branded Logo?");
       if (input_box==false)
		{ 
		
			return false;			
		}
		else
		{
		  listbrandedlogo(placeholder_id,module_id,action,url,method,assets)
		   return true;
		}	
 }


function listbrandedlogo(placeholder_id,module_id,action,url,method,assets)	
{
	$("placeholder_modules").innerHTML = "";
	if(placeholder_id != "")
	{
		var timestamp = new Date().getTime();
		var url = url;
		var pars="place_holder="+placeholder_id+"&time="+timestamp;	
		if(module_id)
		{
			pars += "&module_id="+module_id;
		}
		if(action)
		{
			pars += "&action="+action;
		}
		if(method)
		{
			pars += "&method="+method;
		}
		if(assets)
		{
			pars += "&assets="+assets;
		}
		var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,									   
											onLoading:showAdminProgress(),
											onComplete:function(req)
												  {
													
													$("placeholder_modules").innerHTML = req.responseText;
													finishAdminProgress();
												  }
												}
												); 
		
		
	}
}

function displayRow(){

var row = document.getElementById("hide");

if (row.style.display == '') row.style.display = 'none';

else row.style.display = '';

}
function module_confirm(placeholder_id,module_id,action,url,method)
{
input_box=confirm("Are You Sure Want To Delete Module?");
       if (input_box==false)
		{ 
		
			return false;			
		}
		else
		{
		  mangemenuarticle(placeholder_id,module_id,action,url,method)	
		   return true;
		}	
 }

function showAdminProgress () 
{ // puts spinner in specified div
	var x = $('progress_bar');
	x.innerHTML = '<center><div class="statusmsg">Loading...</div></center>';
}
function finishAdminProgress () { // puts spinner in specified div
	var x = $('progress_bar');
	x.innerHTML = '';
}
 
function buzzbrandedlogo_confirm(module_id,url,action)
{
	input_box=confirm("Are You Sure Want T0 Delete Branded Logo?");
       if (input_box==false)
		{ 
		
			return false;			
		}
		else
		{
		  listbuzzbrandedlogo(module_id,action,url);
		   return true;
		}	
 }

function listbuzzbrandedlogo(module_id,action,url)	
{
		var url = url;
		var pars="";	
		
		if(module_id)
		{
			pars += "module_id="+module_id;
		}
		if(action)
		{
			pars += "&method="+action;
		}
		
		
		var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,									   
											onLoading:showAdminProgress(),
											onComplete:function(req)
												  {
													$("placeholder_modules").innerHTML = req.responseText;
													finishAdminProgress();
												  }
												}
												); 
}

function savetemp19(action,component_id,module_id){
	var txtcontent = trim($('txtcontent').value);
	var timeDuration = $('timeDuration').value;
	var frequency = $('freq').value;
	if(txtcontent == ""){
		alert("Please Enter your Content");
		$('txtcontent').focus;
		return false;
	}else if(timeDuration == ""){
		alert("Please Enter Time Duration in sec.");
		$('timeDuration').focus;
		return false;
	}else if(isNaN(timeDuration)){
		alert('Please Enter only numeric value as time duration.');
		$('timeDuration').focus;
		return false;
	}else if(frequency == ""){
		alert("Please Enter the frequency cap.");
		$('frequency').focus;
		return false;
	}else if(isNaN(frequency)){
		alert('Please Enter only numeric value as frequency Cap.');
		$('timeDuration').focus;
		return false;
	}else{
		var pars= "content="+txtcontent+"&timeDuration="+timeDuration+"&frequency="+frequency;
		saveModuleComponent(action,component_id,module_id,pars);
	}
	
}