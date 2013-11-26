<?php
global $HTPFX,$HTHOST, $IMG_SERVER,$CDN_SERVER;
?>
<script src="<?=$CDN_SERVER?>/js/min/jquery-min.1.2.js"></script>
<script src="<?=$CDN_SERVER?>/js/min/jquery-ui.min.js"></script>
<script src="<?=$CDN_SERVER?>/js/jquery.autocomplete.1.2.js"></script>
<script type="text/javascript" src="<?=$HTPFX.$HTADMINHOST?>/admin/ckeditor/ckeditor.js"></script>
<script>
var host=window.location.protocol+"//"+window.location.host;
function showEditor(divID,width,height){
		 var host=window.location.protocol+"//"+window.location.host;
		 var image_server= '<?=$IMG_SERVER?>';
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
</script>

<textarea id="article_body" name="articles[body]"><?php echo strip($pagedata[body]) ?></textarea>
<script language="javascript">
showEditor('article_body',800,600);
</script>