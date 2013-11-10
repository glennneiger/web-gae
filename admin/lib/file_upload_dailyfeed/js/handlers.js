function fileQueueError(file, errorCode, message) {
	try {
		var imageName = "error.gif";
		var errorName = "";
		if (errorCode === SWFUpload.errorCode_QUEUE_LIMIT_EXCEEDED) {
			errorName = "You have attempted to queue too many files.";
		}

		if (errorName !== "") {
			alert(errorName);
			return;
		}
		alert(errorCode);
		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			errMsg="Invalid Image";
			//imageName = "zerobyte.gif";
			break;
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			errMsg="Image is too big";
//			imageName = "toobig.gif";
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
		default:
			errMsg="There was a error while uploading image.";
			break;
		}
		jQuery("#showerr").html(errMsg);	

//		addImage("images/" + imageName);

	} catch (ex) {
		this.debug(ex);
	}

}

function fileDialogComplete(numFilesSelected, numFilesQueued) {			
try {
		if (numFilesQueued > 0) {
			this.startUpload();
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadProgress(file, bytesLoaded) {	

	try {
		var percent = Math.ceil((bytesLoaded / file.size) * 100);		
		jQuery("#showerr").html("Uploading...");
		
	} catch (ex) {
		this.debug(ex);
	}
}

function updateCoords(c)
			{
				$('#x').val(c.x);
				$('#y').val(c.y);
				$('#w').val(c.w);
				$('#h').val(c.h);
			};

function saveCroppedImage(val)
{
	jQuery('#jcropcontainer').hide();
	jQuery.ajax({
		   type: "POST",
		   url: host+"/admin/lib/file_upload_dailyfeed/upload.php",
		   data: "cropthis="+escape(jQuery('#cropbox').attr('src'))+"&x="+jQuery('#x').val()+"&y="+jQuery('#y').val()+"&w="+jQuery('#w').val()+"&h="+jQuery('#h').val(),
		   success: function(msg){
			
			jQuery('#cropped').val("1");
			
			if(msg.indexOf("Warning")<0)
			{
				if(jQuery('#cropped').val()=='1' )
				{
					image_link = '<input type="checkbox" checked="checked" value="'+msg+'" name="chkImage" id="chkImage"/>'+msg+'<br>';

					jQuery("#uploaded_image").html(image_link);
					jQuery("#showerr").html("");
					count += 1;
				}
				else
				{
					jQuery("#showerr").html(msg);
				}
			}
			else
			{
				jQuery("#showerr").html("An error occured.<br>Please try again.");
			}
		   }
		});
}


function skipCropping(val)
{
	var image_link = '<input type="checkbox" checked="checked" value="'+val+'" name="chkImage" id="chkImage"/>'+val+"<br>";
	jQuery("#uploaded_image").html(image_link);
	jQuery("#showerr").html("");
	count += 1;
	jQuery.fancybox.close();
	jQuery('#jcropcontainer').hide();	
}

function uploadSuccess(file, serverData) {	
	try {		
	
		//var progress = new FileProgress(file,  this.customSettings.upload_target);
		var splt=serverData.split("~width:");

		if (splt[0].substring(0, 7) === "FILEID:") {
			jQuery('#dialog').fadeOut(1000);
			jQuery('#cropped').val("0");
			
			/* Using custom settings */
			jQuery('#jcropcontainer').html('<div style="background-color:white; border:1px solid black;float:left;text-align: center;"><div style="float:left;clear:both;" id="imgContainer" ><img src="'+host+'/assets/dailyfeed/uploadimage/'+jQuery('#image_directory_name').val()+'/'+splt[0].substring(7)+'" id="cropbox" ><input type="hidden" id="x" name="x" value="50" /><input type="hidden" id="y" name="y" value="50" /><input type="hidden" id="w" name="w" value="140"  /><input type="hidden" id="h" name="h" value="86" /></div><div style="float:left;color:red;clear:both;text-align: center; width: 100%;height:74px;">&nbsp;&nbsp;Drag the transparent box to crop this photo.<br><img name="sub" id="sub"  style="border:none;cursor:pointer;" src="'+host+'/images/DailyFeed/bttn_cropit.jpg" onclick="saveCroppedImage();jQuery.fancybox.close();jQuery(\'#dialog\').fadeIn(1000);" >&nbsp;<img name="can" id="can" src="'+host+'/images/DailyFeed/bttn_cancel.jpg" onclick="skipCropping(\''+splt[0].substring(7)+'\');jQuery(\'#dialog\').fadeIn(1000);"  style="border:none;cursor:pointer;" ></div></div>');

			jQuery('#jcropcontainer').show();

			//Get the screen height and width
			var maskHeight = $(document).height();
			var maskWidth = $(window).width();
			//Set heigth and width to mask to fill up the whole screen
			$('#mask').css({'width':maskWidth,'height':maskHeight});

			//Get the window height and width
			var winH = $(window).height();
			var winW = $(window).width();
			
			
			//Set the popup window to center
			
			//var setLeftParam=eval(winW-eval(splt[1]))/2;
			
			var wdht=splt[1].split("~height:");
			jQuery("#imgContainer").css('width',eval(parseInt(wdht[0])+5));
			jQuery("#imgContainer").css('height',eval(parseInt(wdht[1])+75));
		
			jQuery("#inlineBox").css('text-align','center');								

			jQuery("#inlineBox").fancybox({
					'showCloseButton'	: false,
					'hideOnContentClick': false,
					'hideOnOverlayClick':false,
					'centerOnScroll': true,
					'width'  		: 'auto',
					'height'  		: 'auto',
					'scrolling':'auto',
					'autoDimensions': false	
					
				});
			jQuery("#inlineBox").trigger('click');
			

	        jQuery('#cropbox').Jcrop({
	            onSelect: updateCoords,
	            bgColor:     'black',
	            bgOpacity:   .4,
				setSelect:   [ 10, 10, 150, 96 ],
				minSize: 	 [ 140, 86 ],
				maxSize: 	 [ 1260, 774 ],
	            aspectRatio: 140 / 86
	        });
		jQuery("#showerr").html("");
		} else {	
			jQuery("#showerr").html(serverData);			
		}		

	} catch (ex) {
		this.debug(ex);
	}
}

function uploadComplete(file) {
	/*try {
		//  I want the next upload to continue automatically so I'll call startUpload here 
		if (this.getStats().files_queued > 0) {
			this.startUpload();
		} else {
			var progress = new FileProgress(file,  this.customSettings.upload_target);
			progress.setComplete();
			progress.setStatus("All images received.");
			progress.toggleCancel(false);
		}
	} catch (ex) {
		this.debug(ex);
	} */
}

function uploadError(file, errorCode, message) {	
	var imageName =  "error.gif";
	var progress;
	alert(errorCode);
	try {
		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			try {
				progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("Cancelled");
				progress.toggleCancel(false);
			}
			catch (ex1) {
				this.debug(ex1);
			}
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			try {
				progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("Stopped");
				progress.toggleCancel(true);
			}
			catch (ex2) {
				this.debug(ex2);
			}
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			imageName = "uploadlimit.gif";
			break;
		default:
			alert(message);
			break;
		}

	//	addImage("images/" + imageName);

	} catch (ex3) {
		this.debug(ex3);
	}

}


function addImage(src) {
	var newImg = document.createElement("img");
	newImg.style.margin = "5px";

	document.getElementById("thumbnails").appendChild(newImg);
	if (newImg.filters) {
		try {
			newImg.filters.item("DXImageTransform.Microsoft.Alpha").opacity = 0;
		} catch (e) {
			// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
			newImg.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + 0 + ')';
		}
	} else {
		newImg.style.opacity = 0;
	}

	newImg.onload = function () {
		fadeIn(newImg, 0);
	};
	newImg.src = src;
}

function fadeIn(element, opacity) {
	var reduceOpacityBy = 5;
	var rate = 30;	// 15 fps


	if (opacity < 100) {
		opacity += reduceOpacityBy;
		if (opacity > 100) {
			opacity = 100;
		}

		if (element.filters) {
			try {
				element.filters.item("DXImageTransform.Microsoft.Alpha").opacity = opacity;
			} catch (e) {
				// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
				element.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity + ')';
			}
		} else {
			element.style.opacity = opacity / 100;
		}
	}

	if (opacity < 100) {
		setTimeout(function () {
			fadeIn(element, opacity);
		}, rate);
	}
}



/* ******************************************
 *	FileProgress Object
 *	Control object for displaying file info
 * ****************************************** */

function FileProgress(file, targetID) {
	this.fileProgressID = "divFileProgress";

	this.fileProgressWrapper = document.getElementById(this.fileProgressID);
	if (!this.fileProgressWrapper) {
		this.fileProgressWrapper = document.createElement("div");
		this.fileProgressWrapper.className = "progressWrapper";
		this.fileProgressWrapper.id = this.fileProgressID;

		this.fileProgressElement = document.createElement("div");
		this.fileProgressElement.className = "progressContainer";

		var progressCancel = document.createElement("a");
		progressCancel.className = "progressCancel";
		progressCancel.href = "#";
		progressCancel.style.visibility = "hidden";
		progressCancel.appendChild(document.createTextNode(" "));

		var progressText = document.createElement("div");
		progressText.className = "progressName";
		progressText.appendChild(document.createTextNode(file.name));

		var progressBar = document.createElement("div");
		progressBar.className = "progressBarInProgress";

		var progressStatus = document.createElement("div");
		progressStatus.className = "progressBarStatus";
		progressStatus.innerHTML = "&nbsp;";

		this.fileProgressElement.appendChild(progressCancel);
		this.fileProgressElement.appendChild(progressText);
		this.fileProgressElement.appendChild(progressStatus);
		this.fileProgressElement.appendChild(progressBar);

		this.fileProgressWrapper.appendChild(this.fileProgressElement);

		document.getElementById(targetID).appendChild(this.fileProgressWrapper);
		fadeIn(this.fileProgressWrapper, 0);

	} else {
		this.fileProgressElement = this.fileProgressWrapper.firstChild;
		this.fileProgressElement.childNodes[1].firstChild.nodeValue = file.name;
	}

	this.height = this.fileProgressWrapper.offsetHeight;

}
FileProgress.prototype.setProgress = function (percentage) {
	this.fileProgressElement.className = "progressContainer green";
	this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
	this.fileProgressElement.childNodes[3].style.width = percentage + "%";
};
FileProgress.prototype.setComplete = function () {
	this.fileProgressElement.className = "progressContainer blue";
	this.fileProgressElement.childNodes[3].className = "progressBarComplete";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setError = function () {
	this.fileProgressElement.className = "progressContainer red";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setCancelled = function () {
	this.fileProgressElement.className = "progressContainer";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setStatus = function (status) {
	this.fileProgressElement.childNodes[2].innerHTML = status;
};

FileProgress.prototype.toggleCancel = function (show, swfuploadInstance) {
	this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
	if (swfuploadInstance) {
		var fileID = this.fileProgressID;
		this.fileProgressElement.childNodes[0].onclick = function () {
			swfuploadInstance.cancelUpload(fileID);
			return false;
		};
	}
};
