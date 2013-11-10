/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	CKEDITOR.config.autosave_delay=20;
};

CKEDITOR.on('instanceReady', function(ev) {
         //catch ctrl+clicks on <a>'s in edit mode to open hrefs in new tab/window
       	$('iframe').contents().click(function(e) {              												
				if(e.ctrlKey == true)
				{
					var target_link = "";
					if(e.target.nodeName == 'A')
					{
						target_link = e.target.href;
					}
					else if(e.target.parentNode.nodeName == 'A')
					{
						if($.browser.mozilla) // in forefox bold or strong anchor elements were already opening in new window with ctr+c therefor causing double window open
						{
						}
						else
						{
						target_link = e.target.parentNode.href;
						}						
					}
					if(target_link !="" && target_link !="undefined")
					{
                  	    window.open(target_link, 'new' + e.screenX);
					}
                }
         });
});
