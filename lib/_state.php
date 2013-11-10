<?
function persist($url,$myarray=0){
	//this somewhat transparently persists post and get hashes
	global $HTTP_POST_VARS, $HTTP_GET_VARS;
	$varstr="";

	if($myarray){
		foreach($myarray as $key=>$val){
			$varstr.="<input type=\"hidden\" name=\"$key\" value=\"$val\">\n";
		}
	}else{
		if(count($HTTP_POST_VARS)){
			foreach($HTTP_POST_VARS as $key => $val){
					$varstr.="<input type=\"hidden\" name=\"$key\" value=\"$val\">\n";
			}
		}
		if(count($HTTP_GET_VARS)){
			foreach($HTTP_GET_VARS as $key => $val){
					$varstr.="<input type=\"hidden\" name=\"$key\" value=\"$val\">\n";
			}
		}
	}
	echo "
<html><head><title>-</title>
<script>
function postit(){
	document.sendpost.submit();
}
</script>
</head>
<body bgcolor=#FFFFFF onLoad=\"postit()\">
<form method=post action=\"$url\" name=sendpost>
$varstr
</form>
</body></html>" ;
}

function location($destination,$exit=0){
	echo "<html><head>
	<script>
		window.location.replace('".$destination."');
	</script></head><body>
	<META HTTP-EQUIV='Refresh' CONTENT='0;URL=". $destination ."'>
	</body></html>
";
	if($exit){exit();}
}

function mcookie($name,$val="",$exp=""){
	global $_COOKIE;
	$varname=$name;
	global $$varname;
	if($exp == "")
	{
		$exp = time()+28800;
	}
	$_COOKIE[$name]=$val;
	if(headers_sent()){
		$md=date("l, d-M-y H:i:s", $exp);
		echo "<META HTTP-EQUIV=\"Set-Cookie\" CONTENT=\"$name=$val; EXPIRES=$md EST; PATH=/\">";
	}else{
		setcookie("$name","$val", $exp,"/",".minyanville.com");
	}
}
/*---------------  Function added to make a domain based cookie for Magnify Video --- */
function domaincookie($name,$val="")
{
	global $_COOKIE;
	$varname=$name;
	global $$varname;
	$exp = time()+86400;
	$_COOKIE[$name]=$val;
	$domain = ".minyanville.com";
	if(headers_sent()){
			$md=date("l, d-M-y H:i:s", $exp);
			echo "<META HTTP-EQUIV=\"Set-Cookie\" CONTENT=\"$name=$val; EXPIRES=$md EST; PATH=/ DOMAIN=$domain\">";
		}
		else{
			setcookie("$name","$val", $exp,"/",$domain);
		}
}
/*----------------  domainCookie Function End  ---------------------------------------------- */

function set_sess_vars($k , $v=""){
	global $_SESSION;
	if( !$_SESSION[$k] ){
		$_SESSION[$k] = $$k;
	}
	$_SESSION[$k] = $GLOBALS["$k"] = $v;
}

function kill_sess($killcookie=0){
	session_start();
	if(is_array($_SERVER)){
		foreach($_SERVER as $k=>$v){
			session_unregister("$k");
		}
	}
	if($killcookie){
		if(is_array($_COOKIE)){
			foreach($_COOKIE as $k=>$v){
				if( $k!=session_name() ){
					mcookie("$k");
				}
			}
		}
	}
	//$p=session_get_cookie_params();
	//setcookie(session_name(),"",0,$p['PATH'],$p['DOMAIN']);
	session_start();
}


function input_hidden($fieldname,$value=""){
	if(!$fieldname)return;
	global ${$fieldname};
	$value=($value)?$value:${$fieldname};
	$value=strip(trim($value));
	echo "<input type=hidden name=\"$fieldname\" id=\"$fieldname\" value=\"".trim($value)."\" />\n";
}

function input_radio($fieldname,$value,$expvalue="",$addl=""){
	if(!$fieldname)return;
	$checked=($value==$expvalue?" checked":"");
	$id=" id=\"$fieldname$value\"";
	if($addl){
		$addl=" $addl";
		if(stristr(lc($addl),"id="))
			$id="";
	}
	$addl.=" $id";
	echo "<input type=radio name=\"$fieldname\" value=\"$value\"$checked$addl>\n";
}

function input_check($fieldname,$onoff=0,$addl="",$value=""){
	if(!$fieldname)return;
	global ${$fieldname};
	if(${$fieldname})$onoff=1;
	$checked=($onoff?" checked":"");
	$id=" id=\"$fieldname\"";
	if($addl){
		$addl=" $addl";
		if(stristr(lc($addl),"id="))
			$id="";
	}
	if($value == "")
	{
		echo "<input type=checkbox name=\"$fieldname\"$id$checked$addl>\n";
	}
	else
	{
		echo "<input value=\"$value\" type=checkbox name=\"$fieldname\"$id$checked$addl>\n";
	}
}

function input_text($fieldname,$value="",$size="20",$maxlength="255",$addl=""){
	if(!$fieldname)return;
	global ${$fieldname},$IE;
	$id=" id=\"$fieldname\"";
	if($addl){
		$addl=" $addl";
		if(stristr(lc($addl),"id="))
			$id="";
	}
	if(!$size)$size=20;
	if(!$maxlength)$maxlength=255;
	$addl.=$id;
	if($size>20&&!$IE)$size-=15;
	$value=($value)?$value:${$fieldname};
	$value=trim(strip($value));
	if(stristr($value,'"')){
		$value=str_replace('"',"&quot;",$value);
	}
	$class=stristr($addl,"class")?"":"class=\"textinput\"";
	echo "<input $class type=text size=\"$size\" maxlength=\"$maxlength\" name=\"$fieldname\" id=\"$fieldname\" value=\"$value\"$addl title='' onmouseover=void(this.title=this.value) />";
}

function input_textarea($fieldname,$value="",$size="20",$maxlength="255",$rows=2,$addl=""){
	if(!$fieldname)return;
	global ${$fieldname},$IE;
	$id=" id=\"$fieldname\"";
	if($addl){
		$addl=" $addl";
		if(stristr(lc($addl),"id="))
			$id="";
	}
	if(!$size)$size=20;
	if(!$maxlength)$maxlength=255;
	$addl.=$id;
	if($size>20&&!$IE)$size-=15;
	$value=($value)?$value:${$fieldname};
	$value=trim(strip($value));
	if(stristr($value,'"')){
		$value=str_replace('"',"&quot;",$value);
	}
	$class=stristr($addl,"class")?"":"class=\"textinput\"";
	echo "<textarea $class size=\"$size\" rows=\"$rows\" maxlength=\"$maxlength\" name=\"$fieldname\" id=\"$fieldname\" $addl title='' onmouseover=void(this.title=this.value)>$value</textarea>";
}


function input_pass($fieldname,$value="",$size="20",$maxlength="255",$addl=""){
	if(!$fieldname)return;
	$id=" id=\"$fieldname\"";
	if($addl){
		$addl=" $addl";
		if(stristr(lc($addl),"id="))
			$id="";
	}
	$addl.=$id;
	global ${$fieldname},$IE;
	if($size>20&&!$IE)$size-=15;
	$value=($value)?$value:${$fieldname};
	$value=trim(strip($value));
	$class=stristr($addl,"class")?"":"class=\"textinput\"";
	echo "<input $class type=password size=\"$size\" maxlength=\"$maxlength\" name=\"$fieldname\" value=\"$value\"$addl>";
}

function input_numsonly($fieldname,$value="",$size="20",$maxlength="255",$addl=""){
	//accepts numbers, dots
	global $NUMSONLY_JS,$IE;
	if(!$NUMSONLY_JS){
		?><script>function numsonly(str){
			return str.replace(/[^\d\.]+/g,"");}
		</script><?
		$NUMSONLY_JS=1;
	}
	$event=($IE?"onblur":"onclick");
	$addl="$addl $event=\"void(this.value=numsonly(this.value))\"";
	input_text($fieldname,$value,$size,$maxlength,$addl);
}

function input_intonly($fieldname,$value="",$size="20",$maxlength="255",$addl=""){
	global $INONLY_JS,$IE;
	if(!$INONLY_JS){
		?><script>function intonly(str){
			return str.replace(/[^\d]+/g,"");}
		</script><?
		$INONLY_JS=1;
	}
	$event=($IE?"onblur":"onclick");
	$addl="$addl $event=\"void(this.value=intonly(this.value))\"";
	input_text($fieldname,$value,$size,$maxlength,$addl);
}

function input_wordsonly($fieldname,$value="",$size="20",$maxlength="255",$addl=""){
	//accepts numbers, spaces, dashes
	global $WORDSONLY_JS,$IE;
	if(!$WORDSONLY_JS){
		?><script>function wordsonly(str){return str.replace(/[^a-zA-Z-\s]+/g,"");}</script><?
		$WORDSONLY_JS=1;
	}
	$event=($IE?"onblur":"onclick");
	$addl="$addl $event=\"void(this.value=wordsonly(this.value))\"";
	input_text($fieldname,$value,$size,$maxlength,$addl);
}

function input_email($fieldname,$value="",$size="20",$maxlength="255",$addl=""){
	global $EMAIL_JS,$IE;
	if(!$EMAIL_JS){
	 ?><script>function email_format(str){return str.replace(/[^a-zA-Z_\-0-9@\.]+/g,"");}</script><?
		$EMAIL_JS=1;
	}
	$event=($IE?"onblur":"onclick");
	$addl="$addl $event=\"void(this.value=email_format(this.value))\"";
	input_text($fieldname,$value,$size,$maxlength,$addl);
}
function input_alphaonly($fieldname,$value="",$size="20",$maxlength="255",$addl=""){
	global $ALPHAONLY_JS,$IE;
	if(!$ALPHAONLY_JS){
		?><script>function alphanum(str){return str.replace(/\W+/g,"");}</script><?
		$ALPHAONLY_JS=1;
	}
	$event=($IE?"onblur":"onclick");
	$addl="$addl $event=\"void(this.value=alphanum(this.value))\"";
	input_text($fieldname,$value,$size,$maxlength,$addl);
}

function input_lettersonly($fieldname,$value="",$size="20",$maxlength="255",$addl=""){
	global $LETTERS_ONLY_JS,$IE;
	if(!$LETTERS_ONLY_JS){
		?><script>function lettersonly(str){return str.replace(/[^a-zA-Z]+/g,"");}</script><?
		$LETTERS_ONLY_JS=1;
	}
	$event=($IE?"onblur":"onclick");
	$addl="$addl $event=\"void(this.value=lettersonly(this.value))\"";
	input_text($fieldname,$value,$size,$maxlength,$addl);
}

function input_submit($value="Submit",$name="",$addl=""){
	echo "<input type=\"submit\" value=\"$value\" name=\"$name\" class=but $addl>";
}
function input_button($value="Submit",$onclick="",$addl=""){
	echo "<input type=\"button\" value=\"$value\" onclick=\"$onclick\" class=but $addl>";
}
function selectHash($data,$selectedValue){//keys are text, value is value
	if(!is_array($data))return;
	foreach($data as $val=>$text){//this is changed from other libs watch out
		if(is_array($selectedValue)){
			$sel=(in_array($val,$selectedValue)?" selected":"");
		}else{
			$sel=($val==$selectedValue?" selected":"");
		}
		?><option value="<?=$val?>"<?=$sel?>><?=$text?></option><?
	}
}

function selectArr($data,$selectedValue){
	if(!is_array($data))return;
	foreach($data as $val){
		if(is_array($selectedValue)){
			$sel=(in_array($val,$selectedValue)?" selected":"");
		}else{
			$sel=($val==$selectedValue?" selected":"");
		}
		?><option value="<?=$val?>"<?=$sel?>><?=$val?></option><?
	}
}

function selectHashArr($data, $valueKey, $textKey, $selectedValue=NULL){
	if(!is_array($data))return ;
	foreach($data as $row){
		if(is_array($selectedValue)){
			$sel=(in_array($row[$valueKey],$selectedValue)?" selected":"");
		}else{
			$sel=($selectedValue==$row[$valueKey]?" selected":"");
		}
		?><option value="<?=$row[$valueKey]?>"<?=$sel?>><?=$row[$textKey]?></option><?
	}

}

function selectHashArr_professors($data, $valueKey, $textKey, $selectedValue){
print $data;

$blockcontributoremail = array("kadlec@minyanville.com","greg@minyanville.com","jay@optionstrader.com","weldon@minyanville.com","calianos@minyanville.com","james.brumley@minyanville.com","lederman@minyanville.com","adami@minyanville.com","saral@minyanville.com","mary@minyanville.com","payne@minyanville.com","ryan@minyanville.com","nelson@minyanville.com","lmcguirk@endeavourfunds.com","kathy@minyanville.com","adami@minyanville.com","sarah@minyanville.com","sally@minyanville.com","mary@minyanville.com","ryan@minyanville.com","nelson@minyanville.com","lmcguirk@endeavourfunds.com","ryan@minyanville.com","nelson@minyanville.com","lmcguirk@endeavourfunds.com","detullio@minyanville.com","shartsis@minyanville.com","jay@optionstrader.com","kathey@minyanville.com");

	if(!is_array($data))return ;
	foreach($data as $row){

		if(!$row[name] or (in_array($row['email'],$blockcontributoremail)))
				continue;

		if(is_array($selectedValue)){
			$sel=(in_array($row[$valueKey],$selectedValue)?" selected":"");
		}else{
			$sel=($selectedValue==$row[$valueKey]?" selected":"");
		}
		?><option value="<?=$row[$valueKey]?>"<?=$sel?>><?=$row[$valueKey]?></option><?
	}

}


function month_options($fieldname,$selectedval="",$addl="",$id="",$disable="",$class=""){
	global ${$fieldname};
	if(${$fieldname} && !$selectedval)$selectedval=${$fieldname};
	$months=explode(":","January:February:March:April:May:June:July:August:September:October:November:December");
	if($addl)$addl=" $addl";
	?><select id="<?=$id?>" name="<?=$fieldname?>"<?=$addl?><?=$disable?> <?=$class?> ><option value="">-Month-</option><?
	foreach($months as $i=>$_mo){
		$i=($i+1);
		$sel=($i==$selectedval?" selected":"");
		?><option value="<?=$i?>"<?=$sel?>><?=$_mo?></option><?
	}
	?></select><?
}

function month_options_articles($fieldname,$selectedval="",$addl=""){

	global ${$fieldname};
	if(${$fieldname} && !$selectedval)$selectedval=${$fieldname};
	$months=explode(":","01:02:03:04:05:06:07:08:09:10:11:12");
	if($addl)$addl=" $addl";
	?><select name="<?=$fieldname?>"<?=$addl?>><option value="">Mon</option><?
	foreach($months as $i=>$_mo){
		$i=($i+1);
		$sel=($i==$selectedval?" selected":"");
		?><option value="<?=$i?>"<?=$sel?>><?=$_mo?></option><?
	}
	?></select><?
}


function month_options_date($fieldname,$unixtime,$addl=""){
	if(!$unixtime)$unixtime=time();
	$selectedval=date("n",$unixtime);
	month_options($fieldname,$selectedval,$addl);
}

function day_options($fieldname,$selectedval="",$addl="",$id="",$disable=""){
	if($addl)$addl=" $addl";
	global ${$fieldname};
	if(${$fieldname} && !$selectedval)$selectedval=${$fieldname};
	?><select id="<?=$id?>" name="<?=$fieldname?>"<?=$addl?><?=$disable?>><option value="">-Day-</option><?
	foreach(range(1,31) as $i){
		$sel=($i==$selectedval?" selected":"");
		?><option<?=$sel?> value="<?=$i?>"><?=$i?></option><?
	}
	?></select><?

}

function day_options_articles($fieldname,$selectedval="",$addl=""){
	if($addl)$addl=" $addl";
	global ${$fieldname};
	if(${$fieldname} && !$selectedval)$selectedval=${$fieldname};
	?><select name="<?=$fieldname?>"<?=$addl?>><option value="">Day</option><?
	foreach(range(1,31) as $i){
		$sel=($i==$selectedval?" selected":"");
		?><option<?=$sel?> value="<?=$i?>"><?=$i?></option><?
	}
	?></select><?
}

function day_options_date($fieldname,$unixtime,$addl=""){
	if(!$unixtime)$unixtime=time();
	$selectedval=date("j",$unixtime);
	day_options($fieldname,$selectedval,$addl);
}
function year_options($fieldname,$selectedval="",$addl="",$s=0,$e=0,$id="",$disable="",$class=""){
	global ${$fieldname};
	if(${$fieldname} && !$selectedval)$selectedval=${$fieldname};
	if($addl)$addl=" $addl";
	if(!$s)$s=1900;
	if(!$e)$e=(date("Y")-13);
	?><select id="<?=$id?>" name="<?=$fieldname?>"<?=$addl?> <?=$disable?> <?=$class?> ><option value="">-Year-</option><?
	foreach(range($e,$s) as $i){
		$sel=($i==$selectedval?" selected":"");
		?><option<?=$sel?> value="<?=$i?>"><?=$i?></option><?
	}
	?></select><?
}

function year_options_articles($fieldname,$selectedval="",$addl="",$s=0,$e=0){
	global ${$fieldname};
	if(${$fieldname} && !$selectedval)$selectedval=${$fieldname};
	if($addl)$addl=" $addl";
	if(!$s)$s=1900;
	if(!$e)$e=(date("Y")-13);
	?><select name="<?=$fieldname?>"<?=$addl?>><option value="">Year</option><?
	foreach(range($e,$s) as $i){
		$sel=($i==$selectedval?" selected":"");
		?><option<?=$sel?>><?=$i?></option><?
	}
	?></select><?
}

function year_options_date($fieldname,$unixtime,$start="",$end="",$addl=""){
	if(!$unixtime)$unixtime=time();
	$selectedval=date("Y",$unixtime);
	year_options2($fieldname,$selectedval,$addl,$start,$end);
}
function year_options2($fieldname,$selectedval="",$addl="",$start="",$end=""){
	if($addl)$addl=" $addl";
	if(!$start)$start=(date("Y")-2);
	if(!$end)$end=date("Y");
	?><select id="<?=$fieldname?>" name="<?=$fieldname?>"<?=$addl?>><option value="">-Year-</option><?
	foreach(range($end,$start) as $i){
		$sel=($i==$selectedval?" selected":"");
		?><option<?=$sel?> value="<?=$i?>"><?=$i?></option><?
	}
	?></select><?
}
function hour_options($fieldname,$selectedval="",$addl=""){
	//returns hours in 24h format, displays ampm
	if($addl)$addl=" $addl";
	?><select name="<?=$fieldname?>"<?=$addl?>><option value="">-Hour-</option><?
	foreach(range(0,23) as $i){
		$ampm=$i>11?"pm":"am";
		$disp=($i>12)?$i-12:$i;
		if(!$i)$disp="12";
		$sel=strcmp($i,$selectedval)?"":" selected";//strcmp: false is true
		?><option value="<?=$i?>"<?=$sel?>><?=$disp.$ampm?></option><?
	}
	?></select><?
}

function hour_options_articles($fieldname,$selectedval="",$addl=""){
	//returns hours in 24h format, displays ampm
	if($addl)$addl=" $addl";
	?><select name="<?=$fieldname?>"<?=$addl?>><option value="">Hour</option><?
	foreach(range(0,23) as $i){
		$ampm=$i>11?"pm":"am";
		$disp=($i>12)?$i-12:$i;
		if(!$i)$disp="12";
		$sel=strcmp($i,$selectedval)?"":" selected";//strcmp: false is true
		?><option value="<?=$i?>"<?=$sel?>><?=$disp.$ampm?></option><?
	}
	?></select><?
}

function minute_options($fieldname,$selectedval="",$addl=""){
	//returns minutes
	if($addl)$addl=" $addl";
	?><select name="<?=$fieldname?>"<?=$addl?>><option value="">-Minute-</option><?
	foreach(range(0,59) as $i){
		$i=sprintf("%02d",$i);
		$sel=strcmp($i,$selectedval)?"":" selected";//strcmp: false is true
		?><option value="<?=$i?>"<?=$sel?>><?=$i?></option><?
	}
	?></select><?
}

function second_options($fieldname,$selectedval="",$addl=""){
	//returns seconds
	if($addl)$addl=" $addl";
	?><select name="<?=$fieldname?>"<?=$addl?>><option value="">-Second-</option><?
	foreach(range(0,59) as $i){
		$i=sprintf("%02d",$i);
		$sel=strcmp($i,$selectedval)?"":" selected";//strcmp: false is true
		?><option value="<?=$i?>"<?=$sel?>><?=$i?></option><?
	}
	?></select><?
}


function minute_options_articles($fieldname,$selectedval="",$addl=""){
	//returns minutes
	if($addl)$addl=" $addl";
	?><select name="<?=$fieldname?>"<?=$addl?>><option value="">Min</option><?
	foreach(range(0,59) as $i){
		$i=sprintf("%02d",$i);
		$sel=strcmp($i,$selectedval)?"":" selected";//strcmp: false is true
		?><option value="<?=$i?>"<?=$sel?>><?=$i?></option><?
	}
	?></select><?
}
function date_input($unixtime,$prefix="t",$startyear=2002,$addl="", $endyear=0){
	if(!$unixtime)$unixtime="mktime";
	if(!$startyear)$startyear=2002;
	if(!$prefix)$prefix="t";
	if(!$endyear)$endyear=date("Y")+5;
	month_options_date("${prefix}_mo",$unixtime,$addl)?>/<?
	day_options_date("${prefix}_day",$unixtime,$addl)?>/<?
	year_options_date("${prefix}_year",$unixtime,$startyear,$endyear,$addl);
}

function displayMeridiem($fieldname,$selectedval="",$addl=""){
	//returns hours in 24h format, displays ampm
	if($addl)$addl=" $addl";
	?><select name="<?=$fieldname?>"<?=$addl?>><?
	/*foreach(range(0,23) as $i){
		$ampm=$i>11?"pm":"am";
		$disp=($i>12)?$i-12:$i;
		if(!$i)$disp="12";
		$sel=strcmp($i,$selectedval)?"":" selected";//strcmp: false is true
	}*/
	?><option value="<?=$i?>"<?=$sel?>>AM</option><?
	?><option value="<?=$i?>"<?=$sel?>>PM</option><?
	?></select><?
}


function refer($returnstr=0){
	global $REQUEST_URI;
	$url = base64_encode($REQUEST_URI);
	if($returnstr){
		return $url;
	}
	echo "<input type=\"hidden\" name=\"refer\" value=\"$url\">";
}

function qsa($newvars=array(),$exclude="",$refer=0){
	global $_GET;
	$gets = $exclude=="*"? array():$_GET;
	$new = array_merge($gets,$newvars);
	$exclude=explode(" ",$exclude);
	$ret = array();
	foreach($new as $k=>$v){
		if(!trim($v))continue;
		if(!in_array($k,$exclude)){
			$ret[]=trim($k)."=".urlencode(trim($v));
		}
	}
	if($refer)$ret[]="refer=".refer(1);
	return (count($ret)?"?".implode("&",$ret):"");
}

function urlqsa($url, $newvars=array(),$exclude=""){
	$exist=array();
	if(stristr($url,"?")){
		list($url,$exist)=explode("?",$url,2);
		$exist=qs2hash($exist);
		if(count($exist))
			$newvars=array_merge($exist,$newvars);
	}
	return $url.qsa($newvars,$exclude);
}

function qs2hash($qs){
	$ret=array();
	if(stristr($qs,"?"))
		$qs=array_pop(explode("?",$qs,2));
	$qs=str_replace("?","&",$qs);//too many beginner separators
	$qs=explode("&",$qs);
	foreach($qs as $kv){
		list($k,$v)=explode("=",$kv);
		if($v==="")continue;
		$ret[$k]=urldecode($v);
	}
	return $ret;
}

function sqsa($newvars=array()){
	global $_GET;
	$g=array_merge($_GET,$newvars);
	foreach($g as $k=>$v){
		if(!$v)unset($g[$k]);
	}
	if(count($g)){
		return "?GETVARS=".mserial($g);
	}
	return "";
}
function usqsa(){
	global $GETVARS,$_GET;
	if(!$GETVARS)return;
	$GETVARS=munserial($GETVARS);
	foreach($GETVARS as $k=>$v){
		global ${$k};
		${$k}=$v;
	}
	unset($_GET[GETVARS]);
	$_GET=array_merge($GETVARS,$_GET);
}

function post_qsa($cust=array()){
	global $HTTP_POST_VARS;
	$ret=array();
	$new = count($cust)?$cust:$HTTP_POST_VARS;
	foreach($new as $k=>$v){
		if($v=trim($v)){
			$ret[]="$k=".urlencode($v);
		}
	}
	if(count($ret)){
		return ("?".implode("&",$ret));
	}
	return "";
}


  function err($varname,$ret_only=0,$force=0){
  	$varname = "${varname}_error";
	global $HTTP_POST_VARS,$HTTP_GET_VARS;
	if($HTTP_POST_VARS[$varname] || $HTTP_GET_VARS[$varname] || $force){
		if($ret_only)return 1;
		echo "<span class=errstar>*</span>";
	}
	return 0;
  }

  function err_newregistration($varname,$ret_only=0,$force=0){
  	$varname = "${varname}_error";
	global $HTTP_POST_VARS,$HTTP_GET_VARS;
	if($HTTP_POST_VARS[$varname] || $HTTP_GET_VARS[$varname] || $force){
		if($ret_only)return 1;
		echo "<span style='color:red; font-size:17px; vertical-align:middle; font-weight:bold; padding:0px;'>*</span>";
	}
	return 0;
  }
   function build_err_keys($req_fields,$user_values,$user_value_prefix=""){
 	//$req_fields=[flat array] ($i=>$v), $user_values=[flat hash] ($k=>$v)
 	$ret=array();
	$pfx=($user_value_prefix?$user_value_prefix."_":"");
 	foreach($req_fields as $field){
		if(!trim($user_values[$field])){
			$ret["${pfx}${field}_error"]=1;
		}
	}
	if(count($ret))$ret[is_error]=1;
	return $ret;
 }

 function cache_client(){
 	if(headers_sent()){
		echo "<META HTTP-EQUIV=\"Cache-Control\" content=\"max-age=604800\">";
	}else{
		header("Cache-Control: max-age=604800");
		header("Expires: Tue, 21 Dec 2004 12:05:56 GMT");
	}
 }
function cache_client_wholepage($morefiles=array()){
	global $HTTP_IF_MODIFIED_SINCE,$SCRIPT_FILENAME, $D_R, $_GET, $_POST;
	$files=array("$D_R/_header.htm",
				 "$D_R/_footer.htm",
				 "$D_R/lib",
				 $SCRIPT_FILENAME);
	$files=array_merge($files,$morefiles);
	foreach($files as $f)$fdates[]=filemtime($f);
	sort($fdates);
	$newest=$fdates[count($fdates)-1];
	$gmdate_mod=mgmdate($newest);
	if(count($_GET) || count($_POST)){//page isn't completely flat
		header("Last-Modified: $gmdate_mod");
		return;
	}
	$if_modified_since = preg_replace('/;.*$/', '', $HTTP_IF_MODIFIED_SINCE);
	if ($if_modified_since ==  $gmdate_mod) {
		header("HTTP/1.0 304 Not Modified");
		exit;
	}
	header("Last-Modified: $gmdate_mod");
}

function serialize_post($POSTDATA){
	return base64_encode(serialize($POSTDATA));
}

function unserialize_post($SERIALIZED_POSTDATA){
	return unserialize(base64_decode($SERIALIZED_POSTDATA));
}

function ser_get($POSTDATA){
	return base64_encode(serialize($POSTDATA));
}

function unser_get($SERIALIZED_POSTDATA){
	return unserialize(base64_decode($SERIALIZED_POSTDATA));
}

function input_text_email($fieldname,$value="",$size="20",$maxlength="255",$addl=""){
	if(!$fieldname)return;
	global ${$fieldname},$IE;
	$id=" id=\"$fieldname\"";
	if($addl){
		$addl=" $addl";
		if(stristr(lc($addl),"id="))
			$id="";
	}
	if(!$size)$size=20;
	if(!$maxlength)$maxlength=255;
	$addl.=$id;
	if($size>20&&!$IE)$size-=15;
	$value=($value)?$value:${$fieldname};
	$value=trim(strip($value));
	if(stristr($value,'"')){
		$value=str_replace('"',"&quot;",$value);
	}
	$class=stristr($addl,"class")?"":"class=\"textinput\"";
	echo "<input $class type=text size=\"$size\" maxlength=\"$maxlength\" name=\"$fieldname\" value=\"$value\"$addl title='' onmouseover=void(this.title=this.value)>";
}

// function for calculating duration in minutes and seconds
// function will return duration in minutes and seconds
function getMinutesAndSeconds($getDuration)
{
	$minutes='00';
	$seconds='00';
	if($getDuration > 60)
	{
		$minutes=(int) ($getDuration / 60);
		$seconds=$getDuration % 60;
		if($seconds==0)
		{
			$seconds='00';
		}
		$duration=$minutes.":".$seconds;
	}
	elseif($getDuration > 0 and  $getDuration < 60 )
	{
		$duration=$minutes.":".$results[$j]['duration'];
	}
	else
	{
		$duration=$minutes.":".$seconds;
	}
	return $duration;
}
?>