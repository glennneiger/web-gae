// Confiuration File
host=window.location.protocol+"//"+window.location.host;
image_server=window.location.protocol+"//"+window.location.host;
admin_server=window.location.protocol+"//"+window.location.host;
skip_alert="Are you sure, you dont' want to subscribe for Minyanville Preimum Products";
mvilFinanaceHost='http://www.minyanville.com';
if(!included_files)
{
var included_files = new Array();
}
function in_array(needle, haystack) {
    for (var i = 0; i < haystack.length; i++) {
        if (haystack[i] == needle) {
            return true;
        }
    }
   return false;
}
function include_once(script_filename) 
{
	if(!in_array(script_filename, included_files)) 
	{
	included_files[included_files.length] = script_filename;
	include_dom(script_filename)
	}
}
function include_dom(script_filename) 
{
	t=script_filename.substring(script_filename.lastIndexOf('.')+1);
	if (t=='js')
	{
	document.write('<' + 'script');
    document.write(' language="javascript"');
    document.write(' type="text/javascript"');
    document.write(' src="' + script_filename + '">');
    document.write('</' + 'script' + '>');
	}else if (t=='css'){
	document.write('<' + 'link');
    document.write(' rel="stylesheet"');
    document.write(' type="text/css"');
    document.write(' href="' + script_filename + '">');
    document.write('</' + 'link' + '>');
	}	
}