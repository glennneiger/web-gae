var host=window.location.protocol+"//"+window.location.host;
var image_server="http://storage.googleapis.com/mvassets";	

function saveLogo(){
		logoname=$("logoname").value;
		logourl=$("logourl").value;
		if($("logo_id").value=="-Select Logo-"){
			logo_id='';;
		}else {
			logo_id=$("logo_id").value;
		}
		var msg='';
		if(logourl==''){
			msg="Please enter url.";
			$("logourl").focus();
		}
		if(logoname==''){
			msg="Please enter name.";
			$("logoname").focus();
		}
		if(msg){
			var logo=1;
			var url = 'barndedlogo_mod.htm';
			var pars = 'msg=' + msg +'&logo=' + logo;
			var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars, onComplete:getlogoData});
		}else{
			frmlogo.submit();		
		}
	}

	function getlogoData(req){
		if(req.responseText){
			$("savemsg").innerHTML=req.responseText;
		}
	}

	function checkUpload(uploadUrl,uploadFile,uploadSize,uploadExt){
		var fileSize = uploadFile.files[0].size;
		fileSize = (fileSize / (1024*1024)).toFixed(2);
		alert(fileSize);
		alert(uploadSize);
		if(parseInt(fileSize)>parseInt(uploadSize))
		{
			alert('File is to large to upload');
			return false;
		}
		
		var filePath = $('#uploadFile').val();
		var fileName = $('#uploadFile').val().split('\\').pop();
		var fileExt = fileName.split('.').pop();
		var regexMatch = '/'+uploadExt+'/i'; 
		/*if((fileExt.match(regexMatch)))
		{
			alert('File type does not match');
			return false;
		}*/
		var boundary=Math.random().toString().substr(2);
		  var f = uploadFile.files[0];

          var reader = new FileReader();
          reader.readAsBinaryString(f);

          reader.onload = function(e){

              var fbinary = e.target.result;
              var fsize = f.size;
              $.ajax({
                  type: "POST",
                  url: checkUpload,
                  contentType: 'multipart/form-data; charset=utf-8; ',
                  ContentLength:fileSize ,
                  data: fbinary,
                  success: function () {
                      alert("Data Uploaded: ");
                  }
      	        });
          });
	}
	
	function cancelLogo(){
		$("logoname").value='';
		$("logourl").value='';
	}
	
	
	function countHeadLineCharacters(txtObject,maxLength)
	{   
		var maxLength;
		if (txtObject.value.length > maxLength) // if the current length is more than allowed
		{
		txtObject.value = txtObject.value.substring(0, maxLength); // don't allow further input
		$(showcharcount).html(maxLength + " characters allowed"); 
		}
	}

  function countExcerptCharacters(txtObject,maxLength)
	{   
		var maxLength;
		if (txtObject.value.length > maxLength) // if the current length is more than allowed
		{
			$(showcharExcerptcount).html(maxLength + " characters allowed"); 
		}
	}

	 function countQuickTitle(txtObject,maxLength)
	{   
		var maxLength;
		if (txtObject.value.length > maxLength) // if the current length is more than allowed
		{
			$(showcharQuickTitle).html(maxLength + " characters allowed"); 
		}
	}
	 function showEditor(divID,width,height){
		 editor= CKEDITOR.replace( divID,
					{
						extraPlugins : 'autosave,placeholder,tableresize,uicolor,iframe',
						skin : 'kama',
						height: height,
						width: width,						
						enterMode		: 2,
						shiftEnterMode	: 1,
						filebrowserBrowseUrl : host+'/admin/ckfinder/ckfinder.html',
						filebrowserImageBrowseUrl : host+'/admin/ckfinder/ckfinder.html?Type=Images',
						filebrowserFlashBrowseUrl : host+'/admin/ckfinder/ckfinder.html?Type=Flash',
						filebrowserUploadUrl : host+'/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
						filebrowserImageUploadUrl : host+'/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
			        	filebrowserFlashUploadUrl : host+'/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash',
						toolbar :
							[
								{ name: 'document', items : [ 'Source','-'] },
				{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
				{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
				{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
				{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
				{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
				{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'] },
				{ name: 'colors', items : [ 'TextColor','BGColor' ] },
				{ name: 'tools', items : [ 'Maximize','Iframe' ] },
				{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] }
							],
						// Remove the Resize plugin as it does not make sense to use it in conjunction with the AutoGrow plugin.
						removePlugins : 'resize'
					});
	 }
	 
	 function showMultiEditor(divID,width,height,id){
		 editor= CKEDITOR.replace( divID,
					{
						extraPlugins : 'autosave,placeholder,tableresize,uicolor',
						skin : 'kama',
						height: height,
						width: width,						
						enterMode		: 2,
						shiftEnterMode	: 1,
						filebrowserBrowseUrl : host+'/admin/ckfinder/ckfinder.html',
						filebrowserImageBrowseUrl : host+'/admin/ckfinder/ckfinder.html?Type=Images',
						filebrowserFlashBrowseUrl : host+'/admin/ckfinder/ckfinder.html?Type=Flash',
						filebrowserUploadUrl : host+'/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
						filebrowserImageUploadUrl : host+'/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
			        	filebrowserFlashUploadUrl : host+'/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash',
						toolbar :
							[
								{ name: 'document', items : [ 'Source','-'] },
				{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
				{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
				{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
				{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
				{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
				{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'] },
				{ name: 'colors', items : [ 'TextColor','BGColor' ] },
				{ name: 'tools', items : [ 'Maximize' ] },
				{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] }
							],
						// Remove the Resize plugin as it does not make sense to use it in conjunction with the AutoGrow plugin.
						removePlugins : 'resize'
					});
		
		 eval("editor"+id+" = editor;");
	 }
	 
	 
	 
	 function downloadSubscrptionReport(){
		var orderCodeId=$("#productid").val();
		var productName=$("#productid option:selected").text()
		if(orderCodeId==""){
		    alert('Please select product from dropdown');
		}else{
		    window.open("subscriptionemail.export.htm?orderCodeId="+orderCodeId + '&productName=' +productName);
		}
	       
	 }

