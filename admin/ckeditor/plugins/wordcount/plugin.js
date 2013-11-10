/**
* CKEditor Word Count Plugin
*
* Adds a word count to the bottom right hand corner of any CKEditor instance
*
* @package wordcount
* @author Tim Carr
* @version 1
* @copyright n7 Studios 2010
*/
(function() {
    CKEDITOR.plugins.wordcount = {
    };
    
    var plugin = CKEDITOR.plugins.wordcount;
    
    /**
    * Shows the word count in the DIV element created via setTimeout()
    * 
    * @param obj CKEditor Editor Object
    */
    function ShowWordCount(evt) {
        var editor = evt.editor;
        if ($('div#cke_wordcount_'+editor.name).length > 0) { // Check element exists
            // Because CKEditor uses Javascript to load parts of the editor, some of its elements are not immediately available in the DOM
            // Therefore, I use setTimeout.  There may be a better way of doing this.
            setTimeout(function() {
                editor.updateElement();
                var wordCount = GetWordCount(editor.getData());
                if (editor.config.wordcount_maxWords > 0) {
                    // Display with limit
                    $('div#cke_wordcount_'+editor.name).html('Word Count: '+wordCount+'/'+editor.config.wordcount_maxWords);
                } else {
                    // Just display word count
                    $('div#cke_wordcount_'+editor.name).html('Word Count: '+wordCount); 
                }
                
                // Check we are within word limit
                if (wordCount > editor.config.wordcount_maxWords) {
              
                    editor.setUiColor( '#F75D59' );
                    //editor.execCommand('undo'); 
                } else if (wordCount == editor.config.wordcount_maxWords)  {
                    // Create an undo snapshot as we are on the word limit - next word entered will be undone to return
                    // to this snapshot point.
                    editor.fire('saveSnapshot');
                    
                } else {
                    $('div#cke_wordcount_'+editor.name).css('color', 'black');
                    editor.setUiColor( '#C4C4C4' );
                }
            }, 500);
        }
    }
    
    
    function strip_tags (input, allowed) {
    	  allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
    	  var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
    	    commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    	  return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
    	    return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
    	  });
    	}
    
    function get_html_translation_table (table, quote_style) {
    	  var entities = {},
    	    hash_map = {},
    	    decimal;
    	  var constMappingTable = {},
    	    constMappingQuoteStyle = {};
    	  var useTable = {},
    	    useQuoteStyle = {};

    	  // Translate arguments
    	  constMappingTable[0] = 'HTML_SPECIALCHARS';
    	  constMappingTable[1] = 'HTML_ENTITIES';
    	  constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
    	  constMappingQuoteStyle[2] = 'ENT_COMPAT';
    	  constMappingQuoteStyle[3] = 'ENT_QUOTES';

    	  useTable = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
    	  useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';

    	  if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
    	    throw new Error("Table: " + useTable + ' not supported');
    	    // return false;
    	  }

    	  entities['38'] = '&amp;';
    	  if (useTable === 'HTML_ENTITIES') {
    	    entities['160'] = '&nbsp;';
    	    entities['161'] = '&iexcl;';
    	    entities['162'] = '&cent;';
    	    entities['163'] = '&pound;';
    	    entities['164'] = '&curren;';
    	    entities['165'] = '&yen;';
    	    entities['166'] = '&brvbar;';
    	    entities['167'] = '&sect;';
    	    entities['168'] = '&uml;';
    	    entities['169'] = '&copy;';
    	    entities['170'] = '&ordf;';
    	    entities['171'] = '&laquo;';
    	    entities['172'] = '&not;';
    	    entities['173'] = '&shy;';
    	    entities['174'] = '&reg;';
    	    entities['175'] = '&macr;';
    	    entities['176'] = '&deg;';
    	    entities['177'] = '&plusmn;';
    	    entities['178'] = '&sup2;';
    	    entities['179'] = '&sup3;';
    	    entities['180'] = '&acute;';
    	    entities['181'] = '&micro;';
    	    entities['182'] = '&para;';
    	    entities['183'] = '&middot;';
    	    entities['184'] = '&cedil;';
    	    entities['185'] = '&sup1;';
    	    entities['186'] = '&ordm;';
    	    entities['187'] = '&raquo;';
    	    entities['188'] = '&frac14;';
    	    entities['189'] = '&frac12;';
    	    entities['190'] = '&frac34;';
    	    entities['191'] = '&iquest;';
    	    entities['192'] = '&Agrave;';
    	    entities['193'] = '&Aacute;';
    	    entities['194'] = '&Acirc;';
    	    entities['195'] = '&Atilde;';
    	    entities['196'] = '&Auml;';
    	    entities['197'] = '&Aring;';
    	    entities['198'] = '&AElig;';
    	    entities['199'] = '&Ccedil;';
    	    entities['200'] = '&Egrave;';
    	    entities['201'] = '&Eacute;';
    	    entities['202'] = '&Ecirc;';
    	    entities['203'] = '&Euml;';
    	    entities['204'] = '&Igrave;';
    	    entities['205'] = '&Iacute;';
    	    entities['206'] = '&Icirc;';
    	    entities['207'] = '&Iuml;';
    	    entities['208'] = '&ETH;';
    	    entities['209'] = '&Ntilde;';
    	    entities['210'] = '&Ograve;';
    	    entities['211'] = '&Oacute;';
    	    entities['212'] = '&Ocirc;';
    	    entities['213'] = '&Otilde;';
    	    entities['214'] = '&Ouml;';
    	    entities['215'] = '&times;';
    	    entities['216'] = '&Oslash;';
    	    entities['217'] = '&Ugrave;';
    	    entities['218'] = '&Uacute;';
    	    entities['219'] = '&Ucirc;';
    	    entities['220'] = '&Uuml;';
    	    entities['221'] = '&Yacute;';
    	    entities['222'] = '&THORN;';
    	    entities['223'] = '&szlig;';
    	    entities['224'] = '&agrave;';
    	    entities['225'] = '&aacute;';
    	    entities['226'] = '&acirc;';
    	    entities['227'] = '&atilde;';
    	    entities['228'] = '&auml;';
    	    entities['229'] = '&aring;';
    	    entities['230'] = '&aelig;';
    	    entities['231'] = '&ccedil;';
    	    entities['232'] = '&egrave;';
    	    entities['233'] = '&eacute;';
    	    entities['234'] = '&ecirc;';
    	    entities['235'] = '&euml;';
    	    entities['236'] = '&igrave;';
    	    entities['237'] = '&iacute;';
    	    entities['238'] = '&icirc;';
    	    entities['239'] = '&iuml;';
    	    entities['240'] = '&eth;';
    	    entities['241'] = '&ntilde;';
    	    entities['242'] = '&ograve;';
    	    entities['243'] = '&oacute;';
    	    entities['244'] = '&ocirc;';
    	    entities['245'] = '&otilde;';
    	    entities['246'] = '&ouml;';
    	    entities['247'] = '&divide;';
    	    entities['248'] = '&oslash;';
    	    entities['249'] = '&ugrave;';
    	    entities['250'] = '&uacute;';
    	    entities['251'] = '&ucirc;';
    	    entities['252'] = '&uuml;';
    	    entities['253'] = '&yacute;';
    	    entities['254'] = '&thorn;';
    	    entities['255'] = '&yuml;';
    	    entities['34'] = '&quot;';
    	    entities['39'] = '&#39;';
    	    entities['60'] = '&lt;';
      	  	entities['62'] = '&gt;';
    	  }
    	  // ascii decimals to real symbols
    	  for (decimal in entities) {
    	    if (entities.hasOwnProperty(decimal)) {
    	      hash_map[String.fromCharCode(decimal)] = entities[decimal];
    	    }
    	  }

    	  return hash_map;
    	}
    
    function html_entity_decode(string, quote_style) {
    	  var hash_map = {},
    	    symbol = '',
    	    tmp_str = '',
    	    entity = '';
    	  tmp_str = string.toString();

    	  if (false === (hash_map = get_html_translation_table('HTML_ENTITIES', quote_style))) {
    	    return false;
    	  }

    	  // fix &amp; problem
    	  // http://phpjs.org/functions/get_html_translation_table:416#comment_97660
    	  delete(hash_map['&']);
    	  hash_map['&'] = '&amp;';

    	  for (symbol in hash_map) {
    	    entity = hash_map[symbol];
    	    tmp_str = tmp_str.split(entity).join(symbol);
    	  }
    	  tmp_str = tmp_str.split('&#039;').join("'");

    	  return tmp_str;
    	}
    /**
    * Takes the given HTML data, replaces all its HTML tags with nothing, splits the result by spaces, 
    * and outputs the array length i.e. number of words.
    * 
    * @param string htmlData HTML Data
    * @return int Word Count
    */
    function GetWordCount(htmlData) {    	
    	
    	htmlData = htmlData.replace(/[ ]{2,}/gi," ");
        htmlData = htmlData.replace(/\n /,"\n");  // replaces "\n " with "\n"
        htmlData = htmlData.replace(/<br ?\/?>/g, " ");   // replaces "<br> " with space
        htmlData = htmlData.replace(/,/g, " ");
        htmlData = htmlData.replace(/&nbsp;/g, " ");	  // replaces "&nbsp;" with space
        htmlData = strip_tags(htmlData);
        htmlData = htmlData.replace(/(^\s*)|(\s*$)/gi," ");
        htmlData = html_entity_decode(htmlData);
        var non_alphanumerics_rExp = rExp = /[^A-Za-z0-9'-]+/gi;
        htmlData = htmlData.replace(non_alphanumerics_rExp, " ");
        htmlData = htmlData.replace(/^\s+|\s+$/g,"");		// 
        htmlData = htmlData.replace( /\s\s+/g, ' ' );     // trims multiplae space into one space

    	if(htmlData.match(/^\s*$/)) 
    	{ 
    		htmlData=''; 
    		return '0';
    	}
    	else
    	{
    		return htmlData.split(' ').length;
    	}
       // return htmlData.replace(/<(?:.|\s)*?>/g, '').split(' ').length;    
    }
   
    /**
    * Adds the plugin to CKEditor
    */
    CKEDITOR.plugins.add('wordcount', {
        init: function(editor) {
            // Word Count Limit
            editor.config.wordcount_maxWords = $('input[name=contentWordCount]').val();
            
            // Word Count Label - setTimeout used as this element won't be available until after init
            setTimeout(function() {
                if (editor.config.wordcount_maxWords > 0) { 
                    // Display with limit
                    $('td#cke_bottom_'+editor.name).append('<div id="cke_wordcount_'+editor.name+'" style="display: inline-block; float: right; text-align: right; margin-top: 5px; cursor:auto; font:12px Arial,Helvetica,Tahoma,Verdana,Sans-Serif; height:auto; padding:0; text-align:left; text-decoration:none; vertical-align:baseline; white-space:nowrap; width:auto;">Word Count: '+GetWordCount(editor.getData())+'/'+editor.config.wordcount_maxWords+'</div>');
                } else {
                    // Just display word count
                    $('td#cke_bottom_'+editor.name).append('<div id="cke_wordcount_'+editor.name+'" style="display: inline-block; float: right; text-align: right; margin-top: 5px; cursor:auto; font:12px Arial,Helvetica,Tahoma,Verdana,Sans-Serif; height:auto; padding:0; text-align:left; text-decoration:none; vertical-align:baseline; white-space:nowrap; width:auto;">Word Count: '+GetWordCount(editor.getData())+'</div>');
                }
            }, 4000);
                                                                                                                        
            editor.on('key', ShowWordCount);
        }
    });
})();

// Plugin options
CKEDITOR.config.wordcount_maxWords = 500; 