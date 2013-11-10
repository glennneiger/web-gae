<?
//include_once("../admin/FCKeditor/fckeditor.php");
function rich_editor($fieldname,$value="",$w=500,$h=550,$params=array() ){
	//k=>v in params end up as JS variables in the rich editor
	if(!$fieldname)return;
	global ${$fieldname},$PATH_FR;
	$value=($value)?$value:${$fieldname};
	$value=strip(trim($value));
	$r=mrand();
	$oFCKeditor = new FCKeditor($fieldname);
	$oFCKeditor->Value($value);
	$oFCKeditor->Height=$h;
	$oFCKeditor->Width=$w;
    $oFCKeditor->Create();
?>
	<script>
	htmlmode=false;

	function init<?=$r?>(){
		cntnt<?=$r?> = document.all("<?=$fieldname?>")
		objRef<?=$r?>=document.frames["composer<?=$fieldname?>"].document.all("htmleditor");
		objRef<?=$r?>.docsource=cntnt<?=$r?>.innerText;
		setInterval("SaveEditor<?=$r?>()",1000)
	}


	function toggleMode<?=$r?>(){
		saveSrc()
		if(!htmlmode){
			cntnt<?=$r?>.innerText=objRef<?=$r?>.docsource;
			cntnt<?=$r?>.style.display="block"
			document.all["composer<?=$fieldname?>"].style.display="none"
			htmlmode=true;
			return;
		}else{
			objRef<?=$r?>.docsource=cntnt<?=$r?>.innerText;
			cntnt.style.display="none"
			document.all["composer<?=$fieldname?>"].style.display="block"
			htmlmode=false;
		}
	}

	function SaveEditor<?=$r?>(){
		saveSrc<?=$r?>();
	}
	function fixUrl(urlval){
		if(typeof(urlval)=="undefined")urlval="";
		var loc="http://"+location.hostname
		if(location.port)
			loc+=":"+location.port;
	<?if(!$params[absolute_urls]){?>
		while(urlval.indexOf(loc)>-1){
			urlval=urlval.replace(loc,"");
		}
	<?}?>
		urlval=urlval.replace(/\/lib\/richedit\/?/g,"");
		return urlval;
	}

	function saveSrc<?=$r?>(){
		if(htmlmode){
			objRef<?=$r?>.docsource=fixUrl(objRef<?=$r?>.docsource);
			objRef<?=$r?>.docsource=fixUrl(cntnt<?=$r?>.value);

		}else{
			cntnt<?=$r?>.value=fixUrl(cntnt<?=$r?>.value);
			cntnt<?=$r?>.value=fixUrl(objRef<?=$r?>.docsource);
		}
	}
	</script>
<?}?>
