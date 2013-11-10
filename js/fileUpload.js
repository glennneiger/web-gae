
	var swfu;
	function showUploadTool() {
			swfu = new SWFUpload({
				// Backend Settings
				upload_url: host+"/admin/lib/file_upload/upload.php",
				
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
				button_image_url : image_server+"/images/fileupload/upload_chart.gif",
				button_placeholder_id : "spanButtonPlaceholder",
				button_width: 75,
				button_height: 18.9,
				button_text_top_padding: 0,
				button_text_left_padding: 0,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,
				
				// Flash Settings
				flash_url : host+"/admin/lib/file_upload/swf/swfupload.swf",

				custom_settings : {
					upload_target : "divFileProgressContainer"
				},
				
				// Debug Settings
				debug: false
			});

		};		
		function showUploadVideoStill() {
				
			swfu = new SWFUpload({
				// Backend Settings
				
				upload_url: host+"/admin/lib/file_upload/uploadVideoStill.php",
				
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
				button_image_url : image_server+"/images/fileupload/uploadStill_btn.gif",
				button_placeholder_id : "spanButtonPlaceholderVideoStill",
				button_width: 105,
				button_height: 18.9,
				button_text_top_padding: 0,
				button_text_left_padding: 0,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,
				
				// Flash Settings
				flash_url : host+"/admin/lib/file_upload/swf/swfupload.swf",

				custom_settings : {
					upload_progress : "stillError",
					upload_target : "stillUploaded"
				},
				
				// Debug Settings
				debug: false
			});						
		};
		function showUploadVideo() {
			
			swfu = new SWFUpload({
				// Backend Settings
				upload_url: host+"/admin/lib/file_upload/uploadVideo.php",
				
				// File Upload Settings
				file_size_limit : "200 MB",	// 2MB
				file_types : "*.flv;",
				file_types_description : "Videos Only",
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
				button_image_url : image_server+"/images/fileupload/uploadVideo_btn.gif",
				button_placeholder_id : "spanButtonPlaceholderVideo",
				button_width: 105,
				button_height: 18.9,
				button_text_top_padding: 0,
				button_text_left_padding: 0,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,
				
				// Flash Settings
				flash_url : host+"/admin/lib/file_upload/swf/swfupload.swf",

				custom_settings : {
					upload_progress : "videoError",
					upload_target : "videoUploaded"
				},
				
				// Debug Settings
				debug: false
			});

		};		function showUploadPodcastVideo() {
			
			swfu = new SWFUpload({
				// Backend Settings
				upload_url: host+"/admin/lib/file_upload/uploadPodcastVideo.php",
				
				// File Upload Settings
				file_size_limit : "200 MB",	// 2MB
				file_types : "*.mp4",
				file_types_description : "Videos Only",
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
				button_image_url : image_server+"/images/fileupload/uploadVideo_btn.gif",
				button_placeholder_id : "spanButtonPlaceholderPodcastVideo",
				button_width: 105,
				button_height: 18.9,
				button_text_top_padding: 0,
				button_text_left_padding: 0,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,
				
				// Flash Settings
				flash_url : host+"/admin/lib/file_upload/swf/swfupload.swf",

				custom_settings : {
					upload_progress : "podcastVideoError",
					upload_target : "podcastVideoUploaded"
				},
				
				// Debug Settings
				debug: false
			});

		};