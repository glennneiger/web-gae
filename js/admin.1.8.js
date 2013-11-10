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
        
        
	function uploadArtFile(fileInput,postUrl){

		jQuery.ajax({
                type: "POST",
                url: host+"/admin/lib/uploadFileBucket.php",
                data:"text=getUploadUrl&posturl="+postUrl,
                success: function (req){
                             var post = eval('('+req+')');
                             if(post.status=="1" )
                             {
				var fd = new FormData();
				fd.append( "fileInput", jQuery("#"+fileInput)[0].files[0]);
				fd.append( "text", "artUpload");
				jQuery.ajax({
				    url: post.url,
				    type: 'POST',
				    cache: false,
				    data: fd,
				    processData: false,
				    contentType: false,
				    beforeSend: function () {
					jQuery("#output").html("Uploading, please wait....");
				    },
				    success: function (req) { 
					var post = eval('('+req+')');
					jQuery("#output").html(post.msg);
					jQuery("#audioFile").val(post.file);
				    },
				    error: function () {
					alert("ERROR in upload");
				    }
				});
                             }
			     else{
				alert("Upload URL could not be created");
			    }
                }
                }); 
   }

        
        function editUser(id)
        {
                var action = $('#edit_'+id).val();
                if(action=="Edit")
                {
                                $('#user_'+id+'_name').removeAttr('readonly');
                                $('#user_'+id+'_name').css('border','1px solid');
                                $('#user_'+id+'_name').css('background-color','#ffffff');
                                $('#user_'+id+'_email').removeAttr('readonly');
                                $('#user_'+id+'_email').css('border','1px solid');
                                $('#user_'+id+'_email').css('background-color','#ffffff');
                                $('#user_'+id+'_username').removeAttr('readonly');
                                $('#user_'+id+'_username').css('border','1px solid');
                                $('#user_'+id+'_username').css('background-color','#ffffff');
                                $('#user_'+id+'_password').removeAttr('readonly');
                                $('#user_'+id+'_password').css('border','1px solid');
                                $('#user_'+id+'_password').css('background-color','#ffffff');
                                $('#edit_'+id).val('Save');
                }
                else
                {
                                var featureId = new Array();
                                $('#user_'+id+'_feature_ids option:selected').each(function(i, selected){
                                                featureId[i]=$(selected).val(); 
                                }
                                );

                                var name = $('#user_'+id+'_name').val();
                                var email = $('#user_'+id+'_email').val();
                                var username = $('#user_'+id+'_username').val();
                                var password = $('#user_'+id+'_password').val();
                                var suspend = $('#user_'+id+'_suspend').is(':checked');
                                
                                var url = host+'/admin/admin_users_mod.htm';	
                                var pars="action=save&id="+id+"&name="+name+"&email="+email+"&suspend="+suspend+"&username="+username+"&password="+password+"&feature_ids="+featureId;
                                $.ajax({
                                   type: "POST",
                                   url: url,
                                   data:pars,
                                   success: function getUnsubscribeStatus(req){
                                                var post = eval('('+req+')');
                                                if(post.status=="1" )
                                                {
                                                     window.location=host+'/admin/admin_users.htm?error='+post.msg;    
                                                }
                                   }
                                   }); 
                }
        }

        function deleteUser(id)
        {
                var r = confirm("Are you sure you want delete this user");
                if (r==true)
                {
                var url = host+'/admin/admin_users_mod.htm';	
                var pars="action=delete&id="+id;
                $.ajax({
                   type: "POST",
                   url: url,
                   data:pars,
                   success: function getUnsubscribeStatus(req){
                                var post = eval('('+req+')');
                                if(post.status=="1" )
                                {
                                      window.location=host+'/admin/admin_users.htm?error='+post.msg;    
                                }
                   }
                   });
                }
        }

	function getlogoData(req){
		if(req.responseText){
			$("savemsg").innerHTML=req.responseText;
		}
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
		 var host=window.location.protocol+"//"+window.location.host;
		 var image_server="http://storage.googleapis.com/mvassets";
		 editor= CKEDITOR.replace( divID,
					{
						extraPlugins : 'autosave,placeholder,tableresize,uicolor,iframe,imagebrowser',
						skin : 'kama',
						height: height,
						width: width,						
						enterMode		: 2,
						shiftEnterMode	: 1,
						imageBrowser_listUrl: host+"/admin/imagepath.php",
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
						extraPlugins : 'autosave,placeholder,tableresize,uicolor,imagebrowser',
						skin : 'kama',
						height: height,
						width: width,						
						enterMode		: 2,
						shiftEnterMode	: 1,
						imageBrowser_listUrl: host+"/admin/imagepath.php",
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

