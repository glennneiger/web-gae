
	var swfu;
	function showUploadTool() {
			swfu = new SWFUpload({
							
				// Backend Settings
				upload_url: host+"/admin/lib/file_upload_dailyfeed/upload.php",
				
				// File Upload Settings
				file_size_limit : "2 MB",	// 2MB
				file_types : "*.jpg;*.gif",
				file_types_description : "Images Only",
				file_upload_limit : "0",

				// Event Handler Settings - these functions as defined in Handlers.js
				//  The handlers are not part of SWFUpload but are part of my website and control how
				//  my website reacts to the SWFUpload events.
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,

				// Button Settings
				button_image_url : image_server+"/images/DailyFeed/upload-image.gif",
				button_placeholder_id : "spanButtonPlaceholder",
				button_width: 152,
				button_height: 38,
				button_text_top_padding: 0,
				button_text_left_padding: 0,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,
				
				// Flash Settings
				flash_url : host+"/tickertalk/file_upload/swf/swfupload.swf",

				custom_settings : {
					upload_target : "divFileProgressContainer"
				},
				
				// Debug Settings
				debug: false
			});

		};