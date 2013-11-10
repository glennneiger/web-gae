    CKEDITOR.plugins.add( 'charcount',
    {
       init : function( editor )
       {
          var defaultLimit = '800';
          var defaultFormat = '<div class="cke_charcount" style="margin-top: 6px;" ><span class="cke_charcount_count">%count%</span> of <span class="cke_charcount_limit">%limit%</span> characters </span>';
          var limit = defaultLimit;
          var format = defaultFormat;

          var intervalId;
          var lastCount = 0;
          var limitReachedNotified = false;
          var limitRestoredNotified = false;
          
          
          if ( true )
          {   
             function counterId( editor )
             {
                return 'cke_charcount_' + editor.name;
             }
             
             function counterElement( editor )
             {
                return document.getElementById( counterId(editor) );
             }
             
             function convertHtmlToText(inputText) {
            	    var returnText = "" + inputText;

            	    //-- remove BR tags and replace them with line break
            	    returnText=returnText.replace(/<br>/gi, "\n");
            	    returnText=returnText.replace(/<br\s\/>/gi, "\n");
            	    returnText=returnText.replace(/<br\/>/gi, "\n");

            	    //-- remove P and A tags but preserve what's inside of them
            	    returnText=returnText.replace(/<p.*>/gi, "\n");
            	    returnText=returnText.replace(/<a.*href="(.*?)".*>(.*?)<\/a>/gi, " $2 ($1)");

            	    //-- remove all inside SCRIPT and STYLE tags
            	    returnText=returnText.replace(/<script.*>[\w\W]{1,}(.*?)[\w\W]{1,}<\/script>/gi, "");
            	    returnText=returnText.replace(/<style.*>[\w\W]{1,}(.*?)[\w\W]{1,}<\/style>/gi, "");
            	    //-- remove all else
            	    returnText=returnText.replace(/<(?:.|\s)*?>/g, "");

            	    //-- get rid of more than 2 multiple line breaks:
            	    returnText=returnText.replace(/(?:(?:\r\n|\r|\n)\s*){2,}/gim, "\n\n");

            	    //-- get rid of more than 2 spaces:
            	    returnText = returnText.replace(/ +(?= )/g,'');

            	    //-- get rid of html-encoded characters:
            	    returnText=returnText.replace(/&nbsp;/gi," ");
            	    returnText=returnText.replace(/&amp;/gi,"&");
            	    returnText=returnText.replace(/&quot;/gi,'"');
            	    returnText=returnText.replace(/&lt;/gi,'<');
            	    returnText=returnText.replace(/&gt;/gi,'>');

            	    //-- return
            	    return returnText;
             }
             
             function updateCounter( editor )
             {
            	var editor_content = convertHtmlToText(editor.getData());
                var count = editor_content.length;
                if( count == lastCount ){
                   return true;
                } else {
                   lastCount = count;
                }
                if( !limitReachedNotified && count > limit ){
                   limitReached( editor );
                } else if( !limitRestoredNotified && count < limit ){
                   limitRestored( editor );
                }
                
                var html = format.replace('%count%', count).replace('%limit%', limit);
                counterElement(editor).innerHTML = html;
             }
             
             function limitReached( editor )
             {
                limitReachedNotified = true;
                limitRestoredNotified = false;
                editor.setUiColor( '#cf3e3e' );
             }
             
             function limitRestored( editor )
             {
                limitRestoredNotified = true;
                limitReachedNotified = false;
                editor.setUiColor( '#C4C4C4' );
             }

             editor.on( 'themeSpace', function( event )
             {
                if ( event.data.space == 'bottom' )
                {
                   event.data.html += '<div id="'+counterId(event.editor)+'" class="cke_charcount"' +
                      ' title="' + CKEDITOR.tools.htmlEncode( 'Character Counter' ) + '"' +
                      '>&nbsp;</div>';
                }
             }, editor, null, 100 );
             
             editor.on( 'instanceReady', function( event )
             {
                if( editor.config.charcount_limit != undefined )
                {
                   limit = editor.config.charcount_limit;
                }
                
                if( editor.config.charcount_format != undefined )
                {
                   format = editor.config.charcount_format;
                }
                
                
             }, editor, null, 100 );
             
             editor.on( 'dataReady', function( event )
             {
            	 var editor_content = convertHtmlToText(editor.getData());
                 var count = editor_content.length;
                if( count > limit ){
                   limitReached( editor );
                }
                updateCounter(event.editor);
             }, editor, null, 100 );
             
             editor.on( 'key', function( event )
             {
                //updateCounter(event.editor);
             }, editor, null, 100 );
             
             editor.on( 'focus', function( event )
             {
                editorHasFocus = true;
                intervalId = window.setInterval(function (editor) {
                     updateCounter(editor)
                }, 1000, event.editor);
             }, editor, null, 100 );
             
             editor.on( 'blur', function( event )
             {
                editorHasFocus = false;
                if( intervalId )
                   clearInterval(intervalId);
             }, editor, null, 100 );
          }
       }
    });