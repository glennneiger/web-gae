var CkEditorImageBrowser = {};

var host=window.location.protocol+"//"+window.location.host;
var imgPath = '<?=$IMG_SERVER?>';

CkEditorImageBrowser.folders = [];
CkEditorImageBrowser.images = {}; //folder => list of images
CkEditorImageBrowser.ckFunctionNum = null;
CkEditorImageBrowser.curImgFolder = null;

CkEditorImageBrowser.$folderSwitcher = null;
CkEditorImageBrowser.$imagesContainer = null;

CkEditorImageBrowser.init = function () {
	CkEditorImageBrowser.$folderSwitcher = $('#js-folder-switcher');
	CkEditorImageBrowser.$imagesContainer = $('#js-images-container');

	var baseHref = CkEditorImageBrowser.getQueryStringParam("baseHref");
	if (baseHref) {
		var h = (document.head || document.getElementsByTagName("head")[0]),
			el = h.getElementsByTagName("link")[0];
		el.href = location.href.replace(/\/[^\/]*$/,"/browser.css");
		(h.getElementsByTagName("base")[0]).href = baseHref;
	}
	
	CkEditorImageBrowser.ckFunctionNum = CkEditorImageBrowser.getQueryStringParam('CKEditorFuncNum');

	CkEditorImageBrowser.initEventHandlers();
	
	CkEditorImageBrowser.loadData(CkEditorImageBrowser.getQueryStringParam('listUrl'), function () {
		CkEditorImageBrowser.initFolderSwitcher();
	});
};

CkEditorImageBrowser.loadData = function (url, onLoaded) {
	CkEditorImageBrowser.folders = [];
	CkEditorImageBrowser.images = {};

	$.getJSON(url, function (list) {
		$.each(list, function (_idx, item) {
			if (typeof(item.folder) === 'undefined' || item.folder=="") {
				item.folder = 'Images';
			}

			if (typeof(item.thumb) === 'undefined') {
				item.thumb = item.image;
			}
			CkEditorImageBrowser.addImage(item.folder, item.image, item.thumb, item.name);
		});

		onLoaded();
	}).error(function(jqXHR, textStatus, errorThrown) {
		var errorMessage;
		if (jqXHR.status < 200 || jqXHR.status >= 400) {
			errorMessage = 'HTTP Status: ' + jqXHR.status + '/' + jqXHR.statusText + ': "<strong style="color: red;">' + url + '</strong>"';
		} else if (textStatus === 'parsererror') {
			errorMessage = textStatus + ': invalid JSON file: "<strong style="color: red;">' + url + '</strong>": ' + errorThrown.message;
		} else {
			errorMessage = textStatus + ' / ' + jqXHR.statusText + ' / ' + errorThrown.message;
		}
		CkEditorImageBrowser.$imagesContainer.html(errorMessage);
    });
};

CkEditorImageBrowser.addImage = function (folderName, imageUrl, thumbUrl,imageName) {

	if (typeof(CkEditorImageBrowser.images[folderName]) === 'undefined') {
		CkEditorImageBrowser.folders.push(folderName);
		CkEditorImageBrowser.images[folderName] = [];
	}

	CkEditorImageBrowser.images[folderName].push({
		"imageUrl": imageUrl,
		"thumbUrl": thumbUrl,
		"imageName": imageName
	});
};

CkEditorImageBrowser.initFolderSwitcher = function () {
	
	var $switcher = CkEditorImageBrowser.$folderSwitcher;

	$switcher.find('li').remove();

	$.getJSON(folderJsonUrl, function (list) {
		var $option='';
		$.each(list, function (idx, folderName) {
			var img='';
			var folName = folderName.folderPath.split('/');
			folName.pop();
			folName = folName.pop();
			if(idx=="0")
			{
				$option = $option+'<li style="margin:0px 0px 0px 20px"><img class="firstItemImg" src="'+imgPath+'/images/ckfminus.gif" style="position: relative;right: 10px;margin: 0px 0px 0px -15px;"><div id="'+folderName.folderPath+'" class="firstItem">'+folName+'</div></li><br/>';
			}
			else
			{
				if(folderName.haschild=="1")
				{
					var parFolNameId = folderName.folderPath.split('/');
					parFolNameId.pop();
					parFolNameId = parFolNameId.join('_');
					parFolNameId = parFolNameId.split(' ');
					parFolNameId = parFolNameId.join('_');
					var folderUlPath = '#sub_'+parFolNameId+'_fol';
					img = '<img class=" img_'+parFolNameId+' folderItemImg" onclick="showFolder(\''+folderUlPath+'\',\''+folderName.folderPath+'\',\''+parFolNameId+'\')" src="'+imgPath+'/images/ckfplus.gif" style="position: relative;right: -5px;">';
				}
				$option = $option+'<li class="foldersList"><div class="folderImg">'+img+'</div><div id="'+folderName.folderPath+'" class="folList '+parFolNameId+'">'+folName+'</div></li><br/>';
			}
		});
		$switcher.html($option);
	});
	
	if (CkEditorImageBrowser.folders.length === 0) {
		$switcher.remove();
		CkEditorImageBrowser.$imagesContainer.text('No images.');
	} else {
		$switcher.find('li:first').click();
	}
	
	CkEditorImageBrowser.renderImagesForFolder('images','','');
	
	$(document).on('click', '.firstItemImg', function () {
		if($('.foldersList').css("display")!="none")
		{
			$('.foldersList').slideUp("slow");
		}
		else
		{
			$('.foldersList').slideDown("slow");
		}
	});
};

CkEditorImageBrowser.renderFolders = function (folderPath,CurrObj) {
	
	fileJsonUrl = host+"/admin/imagepath.php?action=getImages&folder="+folderPath;

	$.getJSON(fileJsonUrl, function (images) {
		var count=0;
		var html='';
		$.each(images.prefixes, function (id, folderData) {
			var parFolName = folderPath.split('/');
			parFolName.pop();
			parFolName = parFolName.pop();
			parFolNameId = folderPath.replace(/\//g, '_');
			parFolNameId = parFolNameId.split(' ');
			parFolNameId = parFolNameId.join("_");
			if($('#sub_'+parFolNameId).length=="0")
			{				
				var folName = folderData.folderPath.split('/');
				folName.pop();
				folName = folName.pop();
				var img='';
				if(folderData.haschild=="1")
				{
					var parFolNameIds = folderData.folderPath.split('/');
					parFolNameIds.pop();
					parFolNameIds = parFolNameIds.join('_');
					parFolNameIds = parFolNameIds.split(' ');
					parFolNameIds = parFolNameIds.join('_');
					var folderUlPath = '#sub_'+parFolNameIds+'_fol';
					img = '<img class="img_'+parFolNameIds+' folderItemImg" onclick="showFolder(\''+folderUlPath+'\',\''+folderData.folderPath+'\',\''+parFolNameIds+'\')" src="'+imgPath+'/images/ckfplus.gif" style="position: relative;right: -5px;">';
				}				 
				html = html+'<li class="foldersList"><div class="folderImg">'+img+'</div><div id="'+folderData.folderPath+'" class="folList s '+parFolNameIds+'">'+folName+'</div></li><br/>';
			}
		});
		$( '<ul id="sub_'+parFolNameId+'fol">'+html+'</ul>' ).insertAfter('.'+CurrObj);
		$('.img_'+CurrObj).attr('src',imgPath+'/images/ckfminus.gif');
	});
};

CkEditorImageBrowser.renderImagesForFolder = function (folderName,folderPath,CurrObj) {
	
	var images = CkEditorImageBrowser.images[folderName],
		templateHtml = $('#js-template-image').html();

	CkEditorImageBrowser.$imagesContainer.html('Loading ....');

	if(folderName!="images")
	{
		fileJsonUrl = host+"/admin/imagepath.php?action=getImages&folder="+folderPath;
		$.getJSON(fileJsonUrl, function (images) {
			var count=0;
			$.each(images, function (_idx, imageData) {
				if(_idx=="prefixes")
				{
					var parFolName = folderPath.split('/');
					parFolName.pop();
					parFolName = parFolName.pop();
					parFolNameId = folderPath.replace(/\//g, '_');
					parFolNameId = parFolNameId.split(' ');
					parFolNameId = parFolNameId.join('_');
					if($('#sub_'+parFolNameId).length=="0")
					{
						
						var html = '';
						$.each(images.prefixes, function (id, folderData) {
							var folName = folderData.folderPath.split('/');
							folName.pop();
							folName = folName.pop();
							var img='';
							if(folderData.haschild=="1")
							{
								var parFolNameIds = folderData.folderPath.split('/');
								parFolNameIds.pop();
								parFolNameIds = parFolNameIds.join('_');
								parFolNameIds = parFolNameIds.split(' ');
								parFolNameIds = parFolNameIds.join('_');
								var folderUlPath = '#sub_'+parFolNameIds+'_fol';
								img = '<img class=" img_'+parFolNameIds+' folderItemImg" onclick="showFolder(\''+folderUlPath+'\',\''+folderName.folderPath+'\',\''+parFolNameIds+'\')" src="'+imgPath+'/images/ckfplus.gif" style="position: relative;right: -5px;">';
							}	
							html = html+'<li class="foldersList" ><div class="folderImg">'+img+'</div><div id="'+folderData.folderPath+'" class="folList s '+parFolNameIds+'">'+folName+'</div></li><br/>';
						});
						$( '<ul id="sub_'+parFolNameId+'fol">'+html+'</ul>' ).insertAfter(CurrObj);
					}					
				}
				else
				{
					var html = templateHtml;
					html = html.replace('%imageUrl%', imageData.image);
					html = html.replace('%thumbUrl%', imageData.thumb);
					html = html.replace('%imgName%', imageData.name);
					
					var $item = $($.parseHTML(html));
			
					if(count>0)
					{
						CkEditorImageBrowser.$imagesContainer.append($item);
					}
					else
					{
						CkEditorImageBrowser.$imagesContainer.html($item);
					}
					
				}
				count++;
			});
		});
	}
	else
	{
		var count=0;
		$.each(images, function (_idx, imageData) {
			var html = templateHtml;
			html = html.replace('%imageUrl%', imageData.imageUrl);
			html = html.replace('%thumbUrl%', imageData.thumbUrl);
			html = html.replace('%imgName%', imageData.imageName);
	
			var $item = $($.parseHTML(html));
	
			if(count>0)
			{
				CkEditorImageBrowser.$imagesContainer.append($item);;
			}
			else
			{
				CkEditorImageBrowser.$imagesContainer.html($item);;
			}
			count++;
		});
	}
};

CkEditorImageBrowser.initEventHandlers = function () {

	$(document).on('click', '.folList', function () {
		$('#js-images-container').html('Loading..');

		var idx = parseInt($(this).data('idx'), 10);
		folderName = $(this).html();
		var folderPath = $(this).attr('id');
		var currFolder = $('.currActive');
		currFolder.removeClass('active');
		currFolder.removeClass('currActive');
		$(this).addClass('active');
		$(this).addClass('currActive');

		CkEditorImageBrowser.renderImagesForFolder(folderName,folderPath,$(this));
	});	
	
	$(document).on("contextmenu", ".folList", function(e){
		var path = $(this).attr('id');
		var curFolObj = $(this);
		 $("div.custom-menu").remove();
		 $('<div class="custom-menu" onClick="createFolder(\''+path+'\',\''+curFolObj+'\');" >Create Folder</div>')
	        .appendTo("body")
	        .css({top: event.pageY + "px", left: event.pageX + "px"});
		   return false;
		}); 	
	
	/* $(document).bind("click", function(event) {
	    $("div.custom-menu").remove();
	});  */
	/* $(document).on('click', '#js-folder-switcher li', function () {
		var idx = parseInt($(this).data('idx'), 10);
		folderName = $('').html();
		var folderPath = $('#js-folder-switcher li:eq('+idx+')').attr('id');
		$(this).siblings('li').removeClass('active');
		$(this).addClass('active');

		CkEditorImageBrowser.renderImagesForFolder(folderName,folderPath);
	}); */

	$(document).on('click', '.js-image-link', function () {
		window.opener.CKEDITOR.tools.callFunction(CkEditorImageBrowser.ckFunctionNum, $(this).data('url'));
		window.close();
	});
};

CkEditorImageBrowser.getQueryStringParam = function (name) {
	var regex = new RegExp('[?&]' + name + '=([^&]*)'),
		result = window.location.search.match(regex);

	return (result && result.length > 1 ? decodeURIComponent(result[1]) : null);
};
