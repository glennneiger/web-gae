<?
function has_blacklist($url,$readfile,$field)
{
	for ($k=0; $k<=count($readfile)-1; $k++)
	 {
	 	$field[] = explode(",",$readfile[$k]);
	 }

	 for ($i=1; $i<count($field[0]);$i++)
	  {
		if(!empty($field[0][$i])){
	  		$url_exists=substr_count(trim($url),trim($field[0][$i]));
		}
		 if ($url_exists>0)
		 	return $url_exists=1;
		 else
		    $url_exists=0;
	  }
}


function error($msg="There was an error!",$custurl=""){
	if(!$custurl)
		$custurl="/error.php";
	location(urlqsa($custurl,array(error=>$msg)));
	exit();
}

function persistError($url,$msg="There was an error with the information you provided"){

	persist(urlqsa($url,array(error=>$msg)));
	exit();
}

if(!function_exists("floatval")){
	function floatval($dec){
		return (float) $dec;
	}
}
function dollars($amt){
	return number_format($amt,2);
}
function dollarF($amt){
	return sprintf("%01.2f",$amt);
}
function mysqlNow($date=0){
	//2003-08-23 16:18:10
	if(!$date)$date=time();
	return date("Y-m-d H:i:s",$date);
}

function getKeyVal($keys,$url)
{
	$articleArr=explode('?',$_SERVER['REQUEST_URI']);
	$articleArr=explode('/',$articleArr[0]);
	$keyArr = explode(',',$keys);
	foreach($keyArr as $key=>$val)
	{
		$k  = array_search($val,$articleArr);
		$k++;
		$artcileData[$val] = $articleArr[$k];
	}
	return $artcileData;
}

function parse_img_buttons(){
	# expects colon-separated keynames
	# NO UNDERSCORE VARS!!!!
	global $HTTP_POST_VARS;
	$ret=array();
	if(!is_array($HTTP_POST_VARS)){
		return $ret;
	}
	if( !count($HTTP_POST_VARS) ){
		return $ret;
	}

	foreach($HTTP_POST_VARS as $k=>$v){
		if(stristr($k,":") && stristr($k,"_")){
			list($kv,$crap)=explode("_",$k);
			list($key,$val)=explode(":",$kv);
			$ret[$key]=$val;
			unset($kv,$key,$val);
		}
	}
	return $ret;
}

function parse_submit_buttons(){
	# expects colon-separated keynames
	# underscore vars allowed
	global $_POST;
	$ret=array();
	if( !count($_POST) ){
		return $ret;
	}
	foreach($_POST as $k=>$v){
		$k=explode(",",$k);
		foreach($k as $action){
			if(stristr($action,":")){
				list($key,$val)=explode(":",$action);
			$ret[$key]=$val;
			}
		}
	}
	return $ret;
}
function smartquote($str,$quote="'"){
	//escapes the type of ticks you specify
	if(!stristr($str,$quote))
		return $str;
	$search="|[^\\]".$quote."|";
	$replace="\\".$quote;
	$str=@preg_replace($search,$replace,$str);
	return $str;
}
function xmlsafe($str,$noentities=0){
//this will plain wack foreign characters it can't turn into entities
//will wack everything foreign if noentities is resolves true
	if(!$noentities)
		$str=htmlentities($str);
	foreach(range(0,8) as $i){
		$str=str_replace(chr($i),"",$str);
	}
	foreach(range(11,12) as $i){
		$str=str_replace(chr($i),"",$str);
	}
	foreach(range(14,31) as $i){
		$str=str_replace(chr($i),"",$str);
	}
	$str=str_replace(chr(133),"...",$str);
	$str=str_replace(chr(147),'"',$str);
	$str=str_replace(chr(145),"'",$str);
	$str=str_replace(chr(148),'"',$str);
	$str=str_replace("&aacute","a",$str);
	$tmp=$str;//use string as a map
	$replaced=array();//dont' bother replacing > once
	for($i=0;$i<strlen($tmp);$i++){
		if(ord($tmp{$i})>127 && !$replaced[$tmp{$i}]){
			$str=str_replace($tmp{$i},"",$str);
			$replaced[$tmp{$i}]=1;
		}
	}
	if($noentities)
		return htmlentities($str);//--kill bad characters and encode what's left
	return $str;
}
function elipsewords($str,$wordlen,$morestr="..."){
	$words=preg_split("|[\s]+|",$str);
	if(count($words)>$wordlen){
		return (implode(" ",array_slice($words,0,$wordlen)).$morestr);
	}
	return $str;
}
function write_file($path,$txt="",$mode="w+"){
	if(!$path)return;
	if($fp=fopen($path,$mode)){
		fwrite($fp,$txt);
		fclose($fp);
		return 1;
	}
	return 0;
}

function read_file($path){
	if(!file_exists($path))return "";
	return strip(implode("",file($path)));
}

function include2str($phpfile,$mydata=array(),$importglobals=1 ){
	//$path=filename,$mydata=keys turned into vars avail in $path
	//$importglobals=turn on globals
	//*be careful when using this in other functions. vars created in outside
	//*functions only have local scope and the include won't see them.
	//all php in the file gets sandboxed
	$gignore=array("GLOBALS HTTP_POST_VARS HTTP_ENV_VARS HTTP_GET_VARS HTTP_SERVER_VARS");
	if(!is_file($phpfile)){
		return "";
	}
	if($importglobals){
		foreach($GLOBALS as $k=>$v){
			if(in_array($k,$gignore))continue;
			${$k}=$GLOBALS[$k];
		}
	}
	foreach($mydata as $k=>$v){
		if(preg_match("|^[_A-Za-z]|",$k)){
			${$k}=$v;
		}
	}
	ob_start();
	include($phpfile);

	$ret=ob_get_contents();
	ob_end_clean();
	return strip($ret);
}

function mymail($to,$from,$subject,$message_body,$text=null,$file=null,$ftype=" ",$return_str=0,$bcc=null){
	global $mailClass,$D_R;
	include_once($D_R."/lib/htmlMimeMail.php");
	
	if(!is_array($to)){
		$to=	explode(",",$to);
	}

	/*if(!is_array($to)){$to=array($to);
	$to = implode(',',$to);}*/	
	$mail_options = array(
		"sender" => $from,
		"subject" => $subject,
		"htmlBody" => $message_body
		);
	try {
		$mailClass->__construct($mail_options);
	if($file){
		$m=new htmlMimeMail();
		
			$dat=$m->getFile($file);
			$datname=basename($file);
			$dat = stripslashes($dat);
			$mailClass->addAttachment($datname,$dat);
			$dat=$datname="";
		}
	    $mailClass->addBcc($to);
		$mailClass->send();
	return '1';
	} catch (\InvalidArgumentException $e) {
	
	}
}

function mymail_old($to,$from,$subject,$message,$text="",$file="",$ftype="application/octet-stream",$return_str=0,$bcc=null){
	include_once(dirname(__FILE__)."/htmlMimeMail.php");
	if(!is_array($to))$to=array($to);
	$m=new htmlMimeMail();
	$m->setFrom($from);
	$m->setSubject($subject);
	$message=strip($message);
	if(!$text){//it creates a text version by default
		$text=html2text($message);
	}
	if($message && $text){
		$m->setHTML($message,$text);
	}else{
		$m->setText($text);
	}
	if($file){
		if(is_file($file)){
			$dat=$m->getFile($file);
			$datname=basename($file);
			$dat = stripslashes($dat);
			$m->addAttachment($dat,$datname,$ftype);
			$dat=$datname="";
		}
	}
	$m->setBcc($bcc);
	if($return_str){
		return $m->getRFC822($to);
	}else{
		return $m->send($to);
	}
}
function mymail2str($to,$from,$subject,$message,$text="",$file="",$ftype="application/octet-stream"){
	return mymail($to,$from,$subject,$message,$text,$file,$ftype,1);
}

function spacer($width=1,$height=1,$addl=""){
	global $HTPFX,$HTADMINHOST;
	$addl=$addl?" $addl":"";
	return '<img src="'.$HTPFX.$HTADMINHOST.'/space.gif" height="'.$height.'" width="'.$width.'" border=0 '.$addl.'>';
}

function defaultto(&$str,$default=""){
	$str=$str?$str:$default;
}

function inc_web($url){
	$tmpl="";
	if($fp = fopen($url,"r")){
		while(!feof($fp)){
			$tmpl.=fgets($fp,4096);
		}
		fclose($fp);
	}
	return strip($tmpl);
}


function getCmd($command){
	$comamnd=trim($command);
	$cmds = array(
		"find"=>"/usr/bin/find",
		"grep"=>"/bin/grep",
		"ls"=>"/bin/ls",
		"mkdir"=>"/bin/mkdir",
		"cp"=>"/bin/cp",
		"mv"=> "/bin/mv",
		"rm"=>"/bin/rm"
	);
	if($cmds[$command])
		return $cmds[$command];

	return trim(`/usr/bin/which $command`);
}

function parseCmd($command,$return_cmd=0){
	if(!($command=trim($command)))return;
	$cmds = preg_split("/[\|;]/",$command);
	foreach($cmds as $cmd){
		preg_match("/^(\w+)/",$cmd,$ms);
		$command=str_replace($ms[1],getCmd($ms[1]),$command);
	}
	$command=trim(str_replace("\\","",$command));

	return ($return_cmd)?$command:trim(`$command`);
}

/*function dirlist($path,$filter=""){
	if($filter)$filter="-iname '$filter'";
	return explode("\n",trim(parseCmd("find $path $filter")));
}*/

function dirlist($path="/assets/",$norecurse=0){
//$path should be relative to webroot, returns dirnames
	global $D_R;
	if(!$path)$path="/assets/";
	$path="$D_R$path";
	$norecurse=($norecurse?"-maxdepth 1":"");
	$cmd="find $path -type d $norecurse";
	$ret=array();
	$list=explode("\n",trim(parseCmd($cmd)));
	foreach($list as $f){
		if(stristr($f,"/.") && !stristr($path,"/."))continue;//ghetto
		$ret[]=substr($f,strlen($D_R));
	}
	return $ret;
}

function assetList($imgdir="/assets/",$types=array("*.jpg","*.gif","*.png"),$norecurse=0){
	global $D_R;
	$norecurse=($norecurse?"-maxdepth 1":"");
	if($types && !is_array($types))$types=array($types);
	$types= implode("|",$types);
	if($types)$types="|grep -E '$types'";
	$findcmd="find $D_R$imgdir -iname '*.*' $norecurse $types";
	$ret=array();
	$list=trim_arr(explode("\n",trim(parseCmd($findcmd))),1);
	sort($list);
	foreach($list as $f){
		if(stristr($f,"/.")  && !stristr($imgdir,"/."))continue;//ghetto
		$ret[]=substr($f,strlen($D_R));
	}
	return $ret;
}
function endswith($needle,$haystack){
	$offset=strlen($haystack)-strlen($needle);
	if($offset<0)return 0;
	return lc(substr($haystack,$offset))==lc($needle);
}
function startswith($needle,$haystack){
	$offset=strlen($needle);
	return lc(substr($haystack,0,$offset))==lc($needle);
}
function qw($str){
	if(!$str)return array();
	return preg_split("/\s+/",$str);
}

function bold($txt,$cond=1){
	return($cond?"<b>$txt</b>":$txt);
}

function italic($txt,$cond=1){
	echo ($cond?"<i>$txt</i>":$txt);
}

function strip($str,$escape=0){
	if($escape){
		$str=str_replace("\\","",$str);
		return str_replace("$escape","\\$escape",$str);
	}
	return str_replace("\\","",$str);
}
function strip2($str){
	return str_replace("\\","",$str);
}
function href($link,$text="",$addl="",$nolinkif=0,$page=NULL){
	if(!$text)$text=$link;
	if($page="article")
		$text="<span class='articlepagination'>".$text."</span>";
	if($nolinkif)return $text;
	if($page="article")
			return "<a class='articlepagination' href=\"$link\" $addl>$text</a>";
	return "<a href=\"$link\" $addl>$text</a>";
}

function image($src, $link=0,$alt="", $addl="" ){
	global $DOCUMENT_ROOT;
	$parts=parse_url($src);
	$path=$parts[path];
	$params=array();
	if(is_file($abspath="$DOCUMENT_ROOT$path"))
		list($w, $h)=getimagesize($abspath);
	if(!stristr($addl, "border="))$params[]="border=\"0\"";
	if(!stristr($addl, "alt=") && $alt)$params[]="alt=\"$alt\"";
	if($addl)$params[]="$addl";
	if($w)$params[]="width=\"$w\"";
	if($h)$params[]="height=\"$h\"";
	$img="<img src=\"$src\" ".implode(" ",$params)." $addl>";
	if(!$link)
		return $img;
	return href($link,$img,!$link,$addl);
}

function auth($user,$pass){//autheticate single pages
	global $_SERVER;
	if($_SERVER[SERVER_ADDR] == $_SERVER[REMOTE_ADDR] ){
	//allow internal requests
		return 1;
	}
	$p_user=$_SERVER[PHP_AUTH_USER];$p_pass=$_SERVER[PHP_AUTH_PW];
	if( (!$p_user || !$p_pass )|| ($p_user!=$user || $p_pass!=$pass)){
		auth_header();
	}
	return 1;
}
function auth_header(){
	global $HTTP_HOST;
	header("WWW-Authenticate: Basic realm=\"$HTTP_HOST\"");
    header("HTTP/1.0 401 Unauthorized");
	exit();
}


function display_countries($selected_country){
	if(!$selected_country)$selected_country="united states";
	global $D_R;
	$selected_country=strtolower(trim($selected_country));
	//$data=implode("",file("$DOCUMENT_ROOT/assets/data/countries.txt"));
	$data=implode("",file("$D_R/assets/data/countries.txt"));
	$data=str_replace("<option","",$data);
	foreach(explode(">",$data) as $country){
		if(!$country)continue;
		$sel=(strtolower(trim($selected_country))==strtolower(trim($country)))?" selected":"";
		echo "<option${sel}>$country";
	}
}


function display_countries_register($selected_country){
	global $D_R;
	if(!$selected_country)$selected_country="United States";
	$selected_country=strtolower(trim($selected_country));
	//$data=implode("",file("$DOCUMENT_ROOT/assets/data/countries.txt"));
	$data=implode("",file("$D_R/assets/data/countries.txt"));
	$data=str_replace("<option","",$data);
	foreach(explode(">",$data) as $country){
		if(!$country)continue;
		$sel=(strtolower(trim($selected_country))==strtolower(trim($country)))?" selected":"";
		echo "<option${sel} value=\"$country\">$country";
	}
}

function display_states($selected_state){ //abbrev
	global $D_R;
	//if(!$selected_state)$selected_state="ca";
	$selected_state=strtolower($selected_state);
	//$data=implode("",file("$DOCUMENT_ROOT/assets/data/states.txt"));
	$data=implode("",file("$D_R/assets/data/states.txt"));
	foreach(explode("<OPTION ",$data) as $state){
		if(!$state)continue;
		$abbr = substr($state,(strpos($state,"\"")+1),2);
		$sel=(strtolower($abbr)==strtolower($selected_state))?" selected":"";
		$name=array_pop(explode(">",$state));
		echo "<option ".$sel." value=\"$abbr\">$name";

	}
}

function display_canada_states($selected_state){ //abbrev
	global $D_R;
	//if(!$selected_state)$selected_state="ca";
	$selected_state=strtolower($selected_state);
	//$data=implode("",file("$DOCUMENT_ROOT/assets/data/states.txt"));
	$data=implode("",file("$D_R/assets/data/states_canada.txt"));
	foreach(explode("<OPTION ",$data) as $state){
		if(!$state)continue;
		$abbr = substr($state,(strpos($state,"\"")+1),2);
		$sel=(strtolower($abbr)==strtolower($selected_state))?" selected":"";
		$name=array_pop(explode(">",$state));
		echo "<option ".$sel." value=\"$abbr\">$name";
	}
}

function display_trading_years($trading_years){ //abbrev
	global $D_R;
	$trading_years=trim(lc($trading_years));
	$fpath="$D_R/assets/data/trading_years.txt";
	foreach(file($fpath) as $line){
		$line=trim($line);
		if(!$line)continue;
		$sel=(lc($line)==$trading_years?" selected":"");
		echo "<option value=\"".lc($line)."\"$sel>$line</option>\n";
	}
}

function display_jobtitles($selected_title){
	global $D_R;
	$selected_title=trim(lc($selected_title));
	$fpath="$D_R/assets/data/jobtitles.txt";
	foreach(file($fpath) as $line){
		$line=trim($line);
		if(!$line)continue;
		$sel=(lc($line)==$selected_title?" selected":"");
		echo "<option value=\"".lc($line)."\"$sel>$line</option>\n";
	}
}

function display_schooltypes($selected_title){
	global $D_R;
	$selected_title=trim(lc($selected_title));
	$fpath="$D_R/assets/data/skuletypes.txt";
	foreach(file($fpath) as $line){
		$line=trim($line);
		if(!$line)continue;
		$sel=(lc($line)==$selected_title?" selected":"");
		echo "<option value=\"".lc($line)."\"$sel>$line</option>\n";
	}
}

function display_jobfuncs($selected_func){
	global $D_R;
	$selected_func=trim(lc($selected_func));
	$fpath="$D_R/assets/data/jobfunctions.txt";
	foreach(file($fpath) as $line){
		$line=trim($line);
		if(!$line)continue;
		$sel=(lc($line)==$selected_func?" selected":"");
		echo "<option value=\"".lc($line)."\"$sel>$line</option>\n";
	}
}

function display_incomes($selected_inc){
	global $D_R;
	$selected_inc=trim(lc($selected_inc));
	$fpath="$D_R/assets/data/incomeranges.txt";
	foreach(file($fpath) as $line){
		$line=trim($line);
		if(!$line)continue;
		$sel=(lc($line)==$selected_inc?" selected":"");
		echo "<option value=\"".lc($line)."\"$sel>$line</option>\n";
	}
}

function display_heardfrom($selected_inc){
	global $D_R;
	$selected_inc=trim(lc($selected_inc));
	$fpath="$D_R/assets/data/heardfrom.txt";
	foreach(file($fpath) as $line){
		$line=trim($line);
		if(!$line)continue;
		$sel=(lc($line)==$selected_inc?" selected":"");
		echo "<option value=\"".lc($line)."\"$sel>$line</option>\n";
	}
}

function display_agegroups($selected_inc){
	global $D_R;
	$selected_inc=trim(lc($selected_inc));
	$fpath="$D_R/assets/data/agegroups.txt";
	foreach(file($fpath) as $line){
		$line=trim($line);
		if(!$line)continue;
		$sel=(lc($line)==$selected_inc?" selected":"");
		echo "<option value=\"".lc($line)."\"$sel>$line</option>\n";
	}
}

function display_contact_subjects($selected_inc){
	global $D_R;
	$selected_inc=trim(lc($selected_inc));
	$fpath="$D_R/assets/data/contact_subjects.txt";
	foreach(file($fpath) as $line){
		$line=trim($line);
		if(!$line)continue;
		$sel=(lc($line)==$selected_inc?" selected":"");
		echo "<option value=\"".lc($line)."\"$sel>$line</option>\n";
	}
}


function htmlprint_r($arr=array()){
	echo "<pre>";print_r($arr);echo "</pre>";
}

function getmicrotime(){
	list($usec, $sec) = explode(" ", microtime());
    $num= (float) $sec + ((float) $usec * 100000);
	return $num;
}

function mrand() {
	srand(getmicrotime());
	return rand();
}

function valid_email($email){
	if(preg_match("|[\w\-\.]{2,255}@[\w\-\.]{2,255}\.\w{2,255}|",$email))
		return 1;
	return 0;
}

function extract_email($str,$returnlist=0){
	if(preg_match_all("|([\w\-\.]{2,255}@[\w\-\.]{2,255}\.\w{2,255})|",$str,$ms)){
		if(!$returnlist)
			return lc($ms[1][0]);
		else{
			$ret=array();
			foreach($ms[1] as $mail){
				$ret[]=lc($mail);
			}
			return $ret;
		}
	}
	return 0;
}

function trim_arr($arr,$no_nulls=0){
	if(!is_array($arr))
		return array();
	foreach($arr as $k=>$v){
		if(is_array($v)){
			$arr[$k]=trim_arr($v,$no_nulls);
			if(!count($arr[$k]) && $no_nulls){//element had an empty array
				unset( $arr[$k] );
				continue;
			}
		}else{
			if($v==="" && $no_nulls){
				unset( $arr[$k] );
			}else{
				$arr[$k]=trim($v);
			}
		}
	}
	return $arr;
}
function array_match($needle,$haystack){
	foreach($haystack as $item){
		if(is_array($item))
			return array_match($needle, $item);
		if(stristr($item, $needle))
			return 1;
	}
	return 0;
}

function lose_keys($arr,$keylist){
//recursively scans $arr for keys matching items in $keylist and unsets them
	if(!is_array($arr))return $arr;
	foreach($arr as $k=>$row){
		if(in_array($k, $keylist)){
			unset($arr[$k]);
			continue;
		}
		if(is_array($row)){
			$arr[$k]=lose_keys($row,$keylist);
		}
	}
	return $arr;
}
function keep_keys($arr,$keylist){
//recursively scans $arr for keys !matching items in $keylist and unsets them
	if(!is_array($arr))return $arr;
	foreach($arr as $k=>$row){
		if(!in_array($k, $keylist)){
			unset($arr[$k]);
			continue;
		}
		if(is_array($row)){
			$arr[$k]=keep_keys($row,$keylist);
		}
	}
	return $arr;
}
function array_max_strlen($arr){
//would be cool if it were recursive
	return max(array_map("strlen",$arr));
}

function array_to_bool($arr,$recurse=0){
	global $recursion;
	$recursion++;
	if(!is_array($arr)){//just evaluate it as a boolean
		$ret=$arr?true:false;
		echo "arg wasn't an array: $ret<br>";
		return $ret;
	}

	if(is_array($arr)){//empty array: evaluate to true--Not "false"
		if(!count($arr)){
			echo "arg was an empty array -- assume true:<br>";
			return true;
		}
	}

	$ret=false;
	foreach($arr as $k=>$v){
		if(!is_array($v)){
			if($v){
				echo "one value in list was true&&!arr. true=on<br>";
				$ret=true; //works like an "OR ||" statement
			}
			continue;
		}else{
			$tmp=array_to_bool($v,1);
			if($tmp!=$ret){
				echo "recursed value changed: $tmp<br>";
			}
			$ret=$tmp;
		}
	}
	if(!$recurse){
		echo "<i>final value $ret</i>";
	}
	return $ret;
}
//function needs to only evalute homogeneous boolean arrays
//if there's a nested array it should take the aggregate of that one and convert it to a bool
//once the nested arrays are flattened it goes through the final list
//and converts it to something like $ret=$var1||$var2||$var_n?1:0;

function has_array($arr){
	if(!is_array($arr))return 0;
	$num_arrays=0;
	foreach($arr as $k=>$v){
		if(is_array($v))$num_arrays++;
	}
	return $num_arrays;
}

function extract_key($arr,$keyname){
//will recursively search $arr for a key $keyname and return value in a 1 dimensional array
	$ret=array();
	if(!is_array($arr))
		return array();
	foreach($arr as $k=>$v){
		if($k===$keyname){
			$ret[]=$v;
			continue;
		}
		if(is_array($v)){
			if(count( $app=extract_key($v,$keyname) )){
				foreach($app as $ap){
					$ret[]=$ap;
				}
			}
		}
	}
	return $ret;
}
function extract_key_match($arr,$keyname){
//will recursively search $arr for a key $keyname and return value in a 1 dimensional array
	$ret=array();
	if(!is_array($arr))
		return array();
	foreach($arr as $k=>$v){
		if(stristr($k,$keyname)){
			$ret[]=$v;
			continue;
		}
		if(is_array($v)){
			if(count( $app=extract_key_match($v,$keyname) )){
				foreach($app as $ap){
					$ret[]=$ap;
				}
			}
		}
	}
	return $ret;
}


function find_by_key($arr,$keyname,$val){
	//drills $arr looking for $arr[$keyname]==$val
	if($arr==$val)return $arr;
	if(is_array($arr)){
		if($arr[$keyname]==$val)
			return $arr;
		foreach($arr as $row){
			if($ret=find_by_key($row,$keyname,$val))
				return $ret;
		}
	}
}


function deepmap($cb, $x) {
	if (is_array($x)) {
		$it = array();
		foreach ($x as $k => $v) {
			$it["$k"] = deepmap($cb, $x["$k"]);
		}
		return $it;
	}else{
		$cmd = "return (" .str_replace('$__', $x, $cb) . ");";
		return eval($cmd);
	}
}


function strip_arr($arr){//this is broken --use unset
	if(!is_array($arr))return array();
	foreach($arr as $k=>$v){
		if(is_array($v)){
			$arr[$k]=strip_arr($v);
		}else{
			$arr[$k]=strip($v);
		}
	}
	return $arr;
}

function enc_arr($var){//encodes $var recursively until it's a string
	if(!is_array($var))return base64_encode($var);
	foreach($var as $k=>$v){
		if(is_array($v)){
			$var[$k]=enc_arr($v);
		}else{
			$var[$k]=base64_encode($v);
		}
	}
	return $var;
}

function unenc_arr($var){//unwraps enc_arr()
	if(!is_array($var))return strip(base64_decode($var));
	foreach($var as $k=>$v){
		if(is_array($v)){
			$var[$k]=unenc_arr($v);
		}else{
			$var[$k]=strip(base64_decode($v));
		}
	}
	return $var;
}

function mserial($var){
	//makes entire structure encoded, serialized string that's easily passed anywhere
	return base64_encode(serialize($var));
}
function munserial($var){
	//completely unwraps mserial and returns original code object
	return unserialize(base64_decode($var));
}

function getSContent($contentID,$return_one=0, $liveonly=0){
	global $CONTENT_TABLE;
	$contentID=strip($contentID);
	$contentID=addslashes($contentID);
	$qry="SELECT content
		  FROM $CONTENT_TABLE
		  WHERE mid(name,1,length('$contentID'))='$contentID' ";
	if($liveonly)
	$qry.=" AND is_live='1'";
	$qry.=" ORDER BY ordr";

	if($return_one){
		$res=munserial(strip( exec_query($qry,1,"content")));
		return strip_arr($res);
	}
	$ret=array();
	foreach(exec_query($qry,0,"content") as $content){
		$ret[]=munserial($content);
	}
	return strip_arr($ret);
}

function getSContentNames($contentID,$liveonly=0){
	global $CONTENT_TABLE;
	$contentID=strip($contentID);
	$contentID=addslashes($contentID);
	$qry="SELECT name,title
		  FROM $CONTENT_TABLE
		  WHERE mid(name,1,length('$contentID'))='$contentID' ";
	if($liveonly)
		$qry.=" AND is_live='1' ";
	$qry.="ORDER BY ordr";
	$ret=array();
	foreach(exec_query($qry) as $row){
		$key=strip(substr($row[name],strlen($contentID)));
		$ret[$key]=strip($row[title]);
	}
	return $ret;
}

function sort_hash_array($a,$b,$desc=1){
	global $s;
	if(!$s)return;
	if($a[$s]==$b[$s])return 0;
	if($desc)
		return ($a[$s] < $b[$s])?1:-1;
	else
		return ($a[$s] > $b[$s])?1:-1;
}

function sort_hash_array_desc($a,$b){
	return sort_hash_array($a,$b);
}
function sort_hash_array_asc($a,$b){
	return sort_hash_array($a,$b,0);
}

function js_txt($str){
	return str_replace(array("\n","\r",'"'),array("","",'\"'),$str);
}

function unhtmlentities ($string){
	$trans_tbl = get_html_translation_table (HTML_ENTITIES);
	$trans_tbl = array_flip ($trans_tbl);
	return strtr ($string, $trans_tbl);
}

function html2text($htmltext){
// $htmltext should contain an HTML document.
// This will remove HTML tags, javascript sections
// and white space. It will also convert some
// common HTML entities to their text equivalent.
	$search = array ("'<script[^>]*?>.*?</script>'si",  // Strip out javascript
                 "'<[\/\!]*?[^<>]*?>'si",           // Strip out html tags
                 "'([\r\n])[\s]+'",                 // Strip out white space
                 "'&(quot|#34);'i",                 // Replace html entities
                 "'&(amp|#38);'i",
                 "'&(lt|#60);'i",
                 "'&(gt|#62);'i",
                 "'&(nbsp|#160);'i",
                 "'&(iexcl|#161);'i",
                 "'&(cent|#162);'i",
                 "'&(pound|#163);'i",
                 "'&(copy|#169);'i",
                 "'&#(\d+);'e");                    // evaluate as php (???)
	$replace = array ("","","\\1","\"","&","<",">"," ",chr(161),chr(162),chr(163),chr(169),"chr(\\1)");
	return  preg_replace ($search, $replace, $htmltext);
}


function bbasename($filename){
	return array_shift(explode(".",basename($filename),2));
}

function bdirname($filename){
	return array_pop(explode("/",dirname($filename)));
}
function catbasename($filename){
	//assumes there's no "file" in the string
	return array_pop(explode("/",$filename));
}
function catdirname($filename){
	//returns everything but last directory
	$filename=explode("/",$filemame);
	array_pop($filename);
	return implode("/",$filename);
}
function getcatdirs($filename){
	$cats=explode("/",$filename);
	$ret=array();
	foreach($cats as $i){
		$ret[]=implode("/",$cats);
		array_pop($cats);
	}
	return $ret;
}
function calcAge($yyyymmdd){
	if (!$yyyymmdd)
		return 0;
	$age = floor((time()-strtotime($yyyymmdd))/(60*60*24*365.2422));
	return $age;
}

function lower($str){
	return strtolower($str);
}
function uc($str){
	return strtoupper($str);
}

function cleanfilename($filename){
	return preg_replace("/[^\w\.\-_]/","_",$filename);
}

function lc($str){
	return strtolower($str);
}

function alphanum($str){
	return preg_replace("/\W/","",$str);
}

function numsonly($str){
	return preg_replace("/^\-?[^\d]/","",$str);
}

function min_array($needle,$haystack){//acts like php's new support for needle being an arr
	if(is_array($haystack))
	{
	foreach($haystack as $v){
		if(is_array($needle)){
			foreach($needle as $nv){
				if(min_array($nv,$haystack)){
					return 1;
				}
			}
		}else{
			if($v==$needle){
				return 1;
			}
		}
	}
	}
	return 0;
}

function del_by_value($needle,$haystack){
//removes items matching $needle from $haystack
	if(!is_array($haystack))return array();
	foreach($haystack as $k=>$v){
		if(is_array($v)){
			$haystack[$k]=del_by_value($needle, $v);
		}else{
			if($needle==$v)
				unset($haystack[$k]);
		}
	}
	return $haystack;
}

function mouseover($upstate,$downstate,$link,$addlimg="",$addllink=""){
	global $IMG_SERVER;
	if(stristr($upstate,"/nav/"))
		$upstate="$IMG_SERVER$upstate";
	if(stristr($downstate,"/nav/"))
		$downstate="$IMG_SERVER$downstate";
	$imgname="i".mrand();
	if($addlimg)$addlimg=" $addlimg";
	if($addllink)$addllink=" $addllink";
  $pos=strpos($link,"5");
	if ($pos >0)
	{
		$link=substr($link,0,-1);
		?>
<a href="#" onClick=window.open(
	"<?=$link?>","Banter","width=300,height=500,resizable=yes,toolbar=no,scrollbars=no
	") onmouseover="rollOver('<?=$imgname?>','<?=$downstate?>')"
	onmouseout="rollOut()" <?=$addllink?>><img name="<?=$imgname?>"
	src="<?=$upstate?>" border=0 <?=$addlimg?>></a>
		<?
	}
	else
	{
		?>
<a href="<?=$link?>"
	onmouseover="rollOver('<?=$imgname?>','<?=$downstate?>')"
	onmouseout="rollOut()" <?=$addllink?>><img name="<?=$imgname?>"
	src="<?=$upstate?>" border=0 <?=$addlimg?>></a>
		<?
	}
}

function mOver($upstate,$downstate,$link,$downif=0,$addlimg="",$addllink=""){
	global $ASSET_PATH,$ABS_PATHS,$D_R,$IMG_SERVER;
	$downstate=$IMG_SERVER."$ASSET_PATH$downstate";
	$upstate=($downif?$downstate:"$IMG_SERVER$ASSET_PATH$upstate");
	list($w,$h)=getimagesize("$D_R$upstate");
	if(!stristr($addlimg,"width"))
		$addlimg.=" width=43";
	if(!stristr($addlimg,"height"))
		$addlimg.=" height=16";

	mouseover($upstate,$downstate,$link,$addlimg,$addllink);
}
function mOver2($upstate,$downstate,$selectedstate,$link,$downif){
	global $ASSET_PATH,$ABS_PATHS;
	if($selectedstate && $downif){//just use the selectedstate image
		mOver($selectedstate,$selectedstate,$link);
		return;
	}
	$upstate=($downif?$downstate:$upstate);//choose downstate if $downif
	mOver($upstate,$downstate,$link,$downif);
}

function hour_diff($starttime,$endtime){//mktimes
	$starttime=date("g:m:s m/d/Y",$starttime);
	$endtime=date("g:m:s m/d/Y",$endtime);
	$res=date_difftime($starttime,$endtime);
	return $res[h];

}

function minute_diff($starttime,$endtime){//mktimes
	$starttime=date("g:m:s m/d/Y",$starttime);
	$endtime=date("g:m:s m/d/Y",$endtime);
	$res=date_difftime($starttime,$endtime);
	return $res[m];

}

function date_difftime($date1, $date2) {
	 $s = strtotime($date2)-strtotime($date1);
	 $d = intval($s/86400);
	 $s -= $d*86400;
	 $h = intval($s/3600);
	 $s -= $h*3600;
	 $m = intval($s/60);
	 $s -= $m*60;
	 return array("d"=>$d,"h"=>$h,"m"=>$m,"s"=>$s);
}

function ampmtime($mktime){
	return date("g:ia",$mktime);
}

function mgmdate($stamp=0){
	if(!$stamp)$stamp=time();
	return gmdate("D, d M Y H:i:s", $stamp) . " GMT";
}

function file_age($path){
	if(!is_file($path))return 0;
	return (time()-intval(@filemtime($path)));
}

function munlink($path){
	if(!is_file($path)){
		return;
	}
	unlink($path);
}

function attachment_header($filename,$content_type){
	header("Content-type: $content_type");
	header("Content-Disposition: file; filename=\"$filename\"");
}

function csv_header($filename="report.csv"){
	attachment_header($filename,"application/ms-excel");
}

function csv_line($row,$encl='"',$sep=",",$line="\n"){
	if(is_array($row)){
		foreach($row as $k=>$v){
			$row[$k]=str_replace($encl,"",$row[$k]);//strip out enclosing chars
		}
		return "$encl". implode("$encl$sep$encl",$row) ."$encl$line";
	}
	return "";
}

function parseCsv($filename,$encl='"',$sep=",",$line="\n"){
	//returns hash of csv file using first col as keys
	$ret=array();
	$csv_data=read_file($filename);
	$csv_data=explode($line,$csv_data);
	foreach($csv_data as $i=>$line){
		$line=trim($line);//kill newline
		if($encl){//strip off enclosing characters
			$line=substr($line,strlen($encl));//first encloser
			$line=substr($line,0,strlen($line)-strlen($encl)  );//last encloser
		}
		$line=explode("$encl$sep$encl",$line);

		if(!$i){//first line
			$keys=$line;
			continue;
		}
		foreach($keys as $idx=>$key){//reverse map first line's keys to values in subs. lines
			$ret[($i-1)][$key]=$line[$idx];
		}
	}
	return $ret;
}


function default_image($path,$default="/space.gif"){
	global $D_R;
	if(is_file("$D_R$path")){
		return $path;
	}
	return $default;
}

function openTR($idx,$cols,$addl=""){
	if($addl)$addl=" $addl";
	if(!($idx%$cols)){
		return "<TR$addl>\n";
	}
	return "";
}

function closeTR($idx,$cols,$arrlength=false){
	$ret="";
	$atend=$idx%$cols==($cols-1);
	if($arrlength!==false){
		if($idx >= ($arrlength-1))//iterator is at the end
			$closeanyway=1;
			if(($remtds=$cols-($arrlength%$cols))!=$cols  && !$atend){
				//it's not the last cell and there's a remainder of slots open
				$ret=str_repeat("<TD>&nbsp;</TD>\n",$remtds);//pad extra tds to complete grid
			}
	}
	if( $atend || $closeanyway ){
		return "$ret</TR>";
	}
}

function flushOut($msg=""){
	global $FLUSHCOUNT;
	if(!$FLUSHCOUNT)$FLUSHCOUNT=0;
	$id= "L$FLUSHCOUNT".mrand();
	echo "$msg<span id='$id'></span><script>document.all('$id').scrollIntoView();</script>";
	flush();
	$FLUSHCOUNT++;
}

function paginate($numrows,$pagesize=20,$p,$imploder="|"){
	global $PHP_SELF;
	$numpages=ceil($numrows/$pagesize);
	if($numpages<2)return "";
	$links=array();
	foreach(range(0,$numpages-1) as $i){
		$cond=($p==$i?1:0);
		$links[]=href($PHP_SELF.qsa(array(p=>$i)),$i+1,0,$cond);
	}
	return implode($imploder,$links);
}

function debugscreen($msg=""){
	global $DEBUG;
	debug($msg);
	echo "\n".str_repeat(" ",256);
	pdebug();
	$DEBUG=array();
	flush();
}
function debug($msg=""){
	global $DEBUG,$DEBUGGING_ON;
	if(!$DEBUGGING_ON)
		return;
	$DEBUG[]="$msg\n";
}
function pdebug($filter=0){
	global $DEBUG,$DEBUGGING_ON;
	if(!$DEBUGGING_ON)
		return;
	$linesep=str_repeat("-",20)."\n";
	echo "<pre>";
	foreach((array)$DEBUG as $i=>$deb){
		if($filter && !preg_match($filter,$deb)){
			continue;
		}
		echo "$deb$linesep";
	}
	echo "</pre>";
}
function ldebug(){
	//write debugging messages to an error log

}
function notifyAdmin($subject,$message=""){
	global $SYSADMIN_EMAIL,$HTTP_HOST, $PHP_SELF,$NOFIY_STR;
	$subject="($HTTP_HOST/$PHP_SELF) $subject";
	if($NOTIFY_STR!=$subject){//makes it so the email only gets sent once per error
		$NOTIFY_STR=$subject;
		mymail( $SYSADMIN_EMAIL, $SYSADMIN_EMAIL, $subject, $message);
	}
}
function midnight($unixtime=0){
	//returns midnight of the day specified by $unixtime else now
	if(!$unixtime)$unixtime=time();
	return time(0,0,0,date("n",$unixtime),date("j",$unixtime),date("Y",$unixtime));
}
function b4midnight($unixtime=0){
	//returns 11:59:59pm of the day specified by $unixtime else now
	if(!$unixtime)$unixtime=time();
	return time(23,59,59,date("n",$unixtime),date("j",$unixtime),date("Y",$unixtime));
}
function sqlb4midnight($datefield){
	return "UNIX_TIMESTAMP(DATE_FORMAT($datefield,'%Y-%m-%d 23:59:59'))";
}
function sqlmidnight($datefield){
	return "UNIX_TIMESTAMP(DATE_FORMAT($datefield,'%Y-%m-%d'))";
}
function minute($numminutes=0){
	if(!$numminutes)$numminutes=1;
	return 60*$numminutes;
}
function hour($numhours=0){
	if(!$numhours)$numhours=1;
	return minute(60)*$numhours;
}
function day($numdays=0){
	//returns seconds
	if(!$numdays)$numdays=1;
	return hour(24)*$numdays;
}
function week($numweeks=0){
	if(!$numweeks)$numweeks=1;
	return day(7)*$numweeks;
}


function days_in_month($month, $year) {  return date('t', time(0, 0, 0, $month+1, 0, $year)); }

function month($nummonths=0){
	if(!$nummonths)
	{
		$nummonths=1;
	}
	if($nummonths==1)
	{
		$cur_year=date('Y');
		$cur_month=date('m');
		$days_in_month=days_in_month($cur_month, $cur_year);
		return day($days_in_month);
	}
	return day(30)*$nummonths;
}
function year($numyears=0){
	if(!$numyears)$numyears=1;
	return day(365)*$numyears;
}

function cache_server_page($srcurl, $outfile,$max_age=5){//in minutes
	//download srcurl to $outfile if $outfile is older than minute($max_age)
	if( cache_page_is_stale($outfile,$maxage) ){
		$output="\n\n\n<!-- modified: ".date("r")." -->\n\n\n";
		$output.=inc_web($srcurl);
		write_file($outfile,$output);
	}
}
function cache_page_is_stale($outfile,$maxage){
	if( file_age($outfile) > minute($max_age) || !@filesize($outfile) || !is_file($outfile)){
		return 1;
	}
	return 0;
}


function getTag($html, $tag){
	 preg_match_all('/<'.$tag.'\s+(.*?)\s*\/?>/si', $html, $matches);
     $links = $matches[1];
     $final_links = array();
     $link_count = count($links);
     for($n=0; $n<$link_count; $n++){
         $attributes = preg_split('/\s+/s', $links[$n]);
         foreach($attributes as $attribute){
             $att = preg_split('/\s*=\s*/s', $attribute, 2);
             if(isset($att[1])){
                 $att[1] = preg_replace('/([\'"]?)(.*)\1/', '$2', $att[1]);
                 $final_link[strtolower($att[0])] = $att[1];
             }
         }
         $final_links[$n] = $final_link;
     }
	 return $final_links;
}

function ftpPut($localPath, $user, $pass, $host,$path, $port=21){
	global $ftpError;
	//NOTE: improve to accept globs
	//--run commands like mkdir rm, etc
	//puts localPath's filename to $path on remote host
	//$path should be directory

	// debug("ftpPut($localPath, $user, $pass, $host,$path, $port)");
	if(!$port)$port=21;
	if(!is_file($localPath)){
		debug("ftpPut:$localPath doesn't exist");
		$ftpError=$localPath."doesn't exist";
		return 0;
	}

	$ftp=ftp_connect($host,$port);
	if(! ($ftp=ftp_connect($host, $port)) ){
		debug("ftpPut:couldn't connect to $host:$port");
		$ftpError="couldn't connect to ".$host.":".$port;
		return 0;
	}
	if( (!$login=ftp_login($ftp, $user, $pass))){
		debug("ftpPut: couldn't log in with $user:$pass");
		$ftpError="couldn't log in with ".$user.":".$pass;
		ftp_close($ftp);
		return 0;
	}
	ftp_pasv($ftp, true);

	$path="$path/".basename($localPath);

	if(! ($upload=ftp_put($ftp, $path, $localPath, FTP_BINARY)) ){
		debug("ftpPut: couldn't upload $localPath to $path");
		$ftpError="couldn't upload ".$localPath." to ".$path;
		ftp_close($ftp);
		return 0;
	}
	debug("ftpPut: successfully uploaded $localPath to $path");
	ftp_close($ftp);
	return 1;
}

function ftpDel($user, $pass, $host,$path, $port=21){
	//puts localPath's filename to $path on remote host
	//$path should be directory
	debug("ftPdel($user, $pass, $host,$path, $port=21)");
	if(!$port)$port=21;

	$ftp=ftp_connect($host,$port);
	if(! ($ftp=ftp_connect($host, $port)) ){
		debug("ftpPut:couldn't connect to $host:$port");
		return 0;
	}
	if( (!$login=ftp_login($ftp, $user, $pass))){
		debug("ftpDel: couldn't log in with $user:$pass");
		ftp_close($ftp);
		return 0;
	}

	if(! $removal=ftp_delete($ftp, $path) ){
		debug("ftpPut: couldn't remove $path");
		ftp_close($ftp);
		return 0;
	}
	debug("ftpDel: successfully removed $path");
	ftp_close($ftp);
	return 1;
}


function post2url($url,$postHash,$moreparams=array()){
	include_once(dirname(__FILE__)."/Request.php");
	$moreparams[method]="POST";
	$req=new HTTP_Request($url,$moreparams);
	$postHash=substr(qsa($postHash),1);//cut "?"
	$req->addRawPostData($postHash);
	$resp=$req->sendRequest();
	return $req->getResponseBody();
}

function post2urlcurl($url,$postHash,$moreparams=""){
	global $REQUEST_URI,$HTTP_HOST,$HTTPS;
	$proto="http".($HTTPS=="on"?"s":"")."://";
	$SCRIPT_URI="${proto}${HTTP_HOST}${REQUEST_URI}";
	$postHash=substr(qsa($postHash),1);
	$parts=parse_url($parts);
	$opts=array(
		A=>"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)",//usr agent
		e=>$SCRIPT_URI,//--http-referer
		d=>$postHash,//--post data
	);
	$optstring="";
	foreach($opts as $k=>$v){$optstring.="-".$k." \"$v\" ";}
	$cmd="/usr/local/bin/curl -k --location $optstring --url \"$url\"";
	echo $cmd;
	$res=shell_exec($cmd);
	return $res;
}

function fix_relative_urls($str){
	global $HTTP_HOST;
	$str=preg_replace('|href="/|i',"href=\"http://$HTTP_HOST/",$str);
	$str=preg_replace('|src="/|i',"src=\"http://$HTTP_HOST/",$str);
	return $str;
}

function valid_code($code)
{

	$qry="SELECT id,via_id,email,fname FROM subscription WHERE MD5(via_id)='".$code."'";
	$res=exec_query($qry,1);
	if($res)
		return $res;
	else
		return NULL;
}

function activate_email($email)
{
	include('_constants.php');
	$qry="SELECT * FROM subscription WHERE email='".$email."' AND trial_status ='inactive'";
	$res=exec_query($qry,1,"email");
    $expirydate=date('U')+$RBT_TRIAL_LENGTH;
	if($res) {
		$affected_rows=update_query('subscription',array(trial_status=>'active',expires=>$expirydate),array(email=>$email));
	}
	$qrycoop="SELECT id FROM subscription_ps WHERE email='".$email."' AND trial_status ='inactive'";
	$rescoop=exec_query($qrycoop,1);
	if($rescoop) {
		$affected_coop_rows=update_query('subscription_ps',array(trial_status=>'active',expires=>$expirydate),array(email=>$email));
	}
	if($res){
		return $affected_rows;
	} else if($rescoop) {
		$affected_rows=$affected_coop_rows;
		return $affected_rows;
	}else{
		return 0;
	}

}

function getLatestCategoryArticle($categoryid)
{
	$qry="select id from articles where instr(CONCAT(',',articles.category_ids,','),',$categoryid,')>0 and approved='1' and is_live='1' order by date Desc limit 0,1";
	$res=exec_query($qry);
	return $res[0];
}

function getDetails_ByProduct($subs_id,$prod_id)
{$subs_qry="select * from subscription_ps where subid='$subs_id' and prof_id='$prod_id' and cancelsubs='0'";
 $res=exec_query($subs_qry,1);
	return $res;
}

function getDetails_ByCombo($subs_id,$combo_id)
{$combo_qry="select * from subscription_ps where subid='$subs_id' and combo_id='$combo_id' and cancelsubs='0'";
 $res=exec_query($combo_qry,1);
	return $res;
}

function show_ads_openads($zoneid,$convert_quotes=0)
{
	if (isset($zoneid))
	{
		if (@include(getenv('DOCUMENT_ROOT').'/admin/phpads/phpadsnew.inc.php'))
		{
			if (!isset($phpAds_context)) $phpAds_context = array();
				$phpAds_raw = view_raw ('zone:'.$zoneid , 0, '', '', '0', $phpAds_context);
			 if($convert_quotes)
				echo str_replace("\"","'",$phpAds_raw['html']);
			 else
				 echo $phpAds_raw['html'];
		}
	}

}

function show_ads_operative($zone_name,$tile,$size,$pos="",$dcopt="",$script=1)
{
	global $ADS_SERVER,$ADS_TAG_TYPE,$ADS_SITE_NAME,$_SESSION;
	$referer_url=parse_url($_SESSION['referer']);
	list($subdomain, $domain, $domaintype) = explode(".", $referer_url['host']);
	$multipleSize=explode(",", $size);
	foreach ($multipleSize as $key => $val){
		list($width[],$height[])=explode("x",$val);
		sort($height);
		sort($width);
		$cntHeight=count($height);
		$cntWidht=count($width);
	}
	$height=$height[$cntHeight-1];
	$width=$width[$cntWidht-1];

	if($domain!="ameritrade")
	{
		if($script)
		{

		?>
		<SCRIPT LANGUAGE="JavaScript">
		document.write('<SCR'+'IPT LANGUAGE="JavaScript1.1" SRC="<?=$ADS_SERVER;?>/adj/<?=$ADS_SITE_NAME;?>/<?=$zone_name;?>;<? if($pos!=""){ ?>pos=<? echo $pos.";"; } ?><? if($domain!=""){ ?>!c=<? echo $domain.";"; } ?><? if($dcopt!=""){ ?>dcopt=<? echo $dcopt.";"; } ?>tile=<?=$tile;?>;sz=<?=$size;?>;ord=' + ord + '?" ><\/SCRIPT>');
		</SCRIPT>
		<SCRIPT>
		if ((!document.images && navigator.userAgent.indexOf("Mozilla/2.") >= 0)  || navigator.userAgent.indexOf("WebTV")>= 0) {
		document.write('<A HREF="<?=$ADS_SERVER;?>/jump/<?=$ADS_SITE_NAME;?>/<?=$zone_name;?>;<? if($pos!=""){ ?>pos=<? echo $pos.";"; } ?><? if($domain!=""){ ?>!c=<? echo $domain.";"; } ?>tile=<?=$tile;?>;sz=<?=$size?>;ord=' + ord + '?" TARGET="_blank">');
		document.write('<IMG SRC="<?=$ADS_SERVER;?>/ad/<?=$ADS_SITE_NAME;?>/<?=$zone_name;?>;<? if($pos!=""){ ?>pos=<? echo $pos.";"; } ?><? if($domain!=""){ ?>!c=<? echo $domain.";"; } ?>tile=<?=$tile;?>;sz=<?=$size?>;ord=' + ord + '?" WIDTH="<?=$width;?>" HEIGHT="<?=$height;?>" BORDER="0" ALT=""></A>');
		}
		</SCRIPT>
<NOSCRIPT><A
	HREF="<?=$ADS_SERVER;?>/jump/<?=$ADS_SITE_NAME;?>/<?=$zone_name;?>;<? if($pos!=""){ ?>pos=<? echo $pos.";"; } ?><? if($domain!=""){ ?>!c=<? echo $domain.";"; } ?>tile=<?=$tile;?>;sz=<?=$size?>;ord=123456789?"
	TARGET="_blank"> <IMG
	SRC="<?=$ADS_SERVER;?>/ad/<?=$ADS_SITE_NAME;?>/<?=$zone_name;?>;<? if($pos!=""){ ?>pos=<? echo $pos.";"; } ?><? if($domain!=""){ ?>!c=<? echo $domain.";"; } ?>tile=<?=$tile;?>;sz=<?=$size?>;ord=123456789?"
	WIDTH="<?=$width;?>" HEIGHT="<?=$height;?>" BORDER="0" ALT=""></A></NOSCRIPT>
		<?
		}else{
		?>
<A
	HREF="<?=$ADS_SERVER;?>/jump/<?=$ADS_SITE_NAME;?>/<?=$zone_name;?>;<? if($pos!=""){ ?>pos=<? echo $pos.";"; } ?><? if($domain!=""){ ?>!c=<? echo $domain.";"; } ?>tile=<?=$tile;?>;sz=<?=$size?>;ord=123456789?"
	TARGET="_blank"> <IMG
	SRC="<?=$ADS_SERVER;?>/ad/<?=$ADS_SITE_NAME;?>/<?=$zone_name;?>;<? if($pos!=""){ ?>pos=<? echo $pos.";"; } ?><? if($domain!=""){ ?>!c=<? echo $domain.";"; } ?>tile=<?=$tile;?>;sz=<?=$size?>;ord=123456789?"
	WIDTH="<?=$width;?>" HEIGHT="<?=$height;?>" BORDER="0" ALT=""></A>

<?
		}
	}
}
function show_ads_openx($zone_name,$key_name,$size,$script=1)
{
	global $HTHOST,$HTPFX,$SEC_TO_ZONE_OPENX;
	$zone=$SEC_TO_ZONE_OPENX[$zone_name];
	if($script){
?>
<script type='text/javascript'>
	<!--// <![CDATA[
	OA_adjs(<?=$zone?>,'<?=$key_name?>');
	// ]]> -->

	</script>
<?
	}
}

function checkValidUser($email, $action='')
{
	$chkUserPresence='';
	if($email!='')
	{
		$qry="SELECT s.*,s.id sid, sc.* FROM subscription s LEFT JOIN corp sc ON(s.corpuser=sc.corp_login) WHERE  lower(email)='".$email."'";
		$res=exec_query($qry,1);
		if(count($res)>0)
		{
			$chkUserPresence="valid";
		}
	}
	if($action=='checkForValid')
	{
		return $chkUserPresence;
	}
}

function makelinks_emailalert($keyword,$type,$field="")
{
        global $page_config;
        $taglink='<a style="letter-spacing:0px;text-decoration:none; color:#C17134;font-size:11px; font-weight:bold; font-family:Verdana, Arial, Helvetica, sans-serif;" href="'.$page_config['exchange_search']['URL'].'?q='.$keyword.'&type='.$type;
        if($field!=""){
                $taglink.="&advanced=1&field=".$field;
        }
        $taglink.='"'.'>'.$keyword.'</a>';
        return $taglink;

}
function seo_tags($pageName)
{
$cnt=0;
$modules=array();
$companyNames=array();
$strTags='';
$strCompanyNames='';

if(is_numeric($pageName))
{
	$condition= "id='$pageName'";
}
else
{
	$condition= "name='$pageName'";
}

$sql_layout_pages="select id, name from layout_pages where $condition";

	if(count(exec_query($sql_layout_pages))>0)
	{
		$result_layout_pages=exec_query($sql_layout_pages,1);
		$pageId=$result_layout_pages[id];

		$sql_layout_columns="select * from layout_columns where pageID=$pageId";

		if(count(exec_query($sql_layout_columns))>0)
		{
			foreach(exec_query($sql_layout_columns) as $row)
			{
				if($row[moduleOrder]!='')
				{
					$moduleOrder=explode(',',$row[moduleOrder]);
					for($i=0;$i<count($moduleOrder);$i++)
					{
						if(!in_array($moduleOrder[$i], $modules))
						{
							$modules[]=$moduleOrder[$i];
							$cnt++;
						}
					}
				}
			}
			sort($modules);
			$strModules = implode(",", $modules);

			$sql_ex_tags="select ET.tag,ET.type_id from ex_item_tags EIT,ex_tags ET where ET.id=EIT.tag_id and item_id in ($strModules) and item_type=8";

			if(count(exec_query($sql_ex_tags))>0)
			{
				foreach(exec_query($sql_ex_tags) as $row)
				{
					if($row[type_id]==1)
					{
						$sql_ex_stock="select CompanyName from ex_stock where stocksymbol='$row[tag]'";
						if(count(exec_query($sql_ex_stock))>0)
						{
							$result_ex_stock=exec_query($sql_ex_stock,1);
							$companyNames[]=$result_ex_stock[CompanyName];
						}
					}
					$tags[]=$row[tag];
				}
				$strTags = implode(", ", $tags);
				$strCompanyNames = implode(", ", $companyNames);
			}

		}
	}
	return $strTags."###".$strCompanyNames;
}

function showtremorplayer($videofilepath,$stillfilepath,$autoplay="False",$sessionAutoPlay=1,$usePlaylist=FALSE,$videoid=NULL,$pagename=NULL)
{
	global $HTPFX,$HTHOST,$latestVidDuration;
	$playerId="4b2246500ec7a";
	if($sessionAutoPlay)
	{
		if(!$_SESSION['tremorautoplay'])
		{
			$autoplay='TRUE';
			$_SESSION['tremorautoplay']=TRUE;
			$playerId="4b0be41fbaab9";
		}
	}
	if(!$videoid)
	{
		$sqlMVTV="SELECT * FROM mvtv WHERE approved='1' AND publish_time <'".mysqlNow()."' AND publish_time > '".mysqlNow()."'- INTERVAL ".$latestVidDuration." ORDER BY id DESC LIMIT 0,1";
		$resMVTV=exec_query($sqlMVTV,1);
		$videoid=$resMVTV['id'];
	}
if($usePlaylist)
{
?>
<script
	type="text/javascript"
	src="http://objects.tremormedia.com/embed/js/embed.js"></script>
<script
	type="text/javascript"
	src="http://objects.tremormedia.com/embed/js/<?=$playerId;?>_p.js"></script>
<script
	type="text/javascript"
	src="http://objects.tremormedia.com/embed/js/banners.js"></script>
	<script type="text/javascript">
			function displayCompanionBanners(banners) {
			tmDisplayBanner(banners, "adCompanionBanner", 300, 250);
		}
<? if($videoid)	{
?>
		tmObj.set("VideoURL", "<?=$HTPFX.$HTHOST;?>/rss/MVTVMediaPlaylist.rss?videoid=<?=$videoid?>");
<? }else{ ?>
		tmObj.set("VideoURL", "<?=$HTPFX.$HTHOST;?>/rss/MVTVMediaPlaylist.rss");
<? } ?>
		tmObj.set("AutoPlay", "<?php echo $autoplay;?>");
		tmObj.set("PreviewImageURL",  "<?php echo $stillfilepath;?>");
		tmObj.set("ContentType", "playlist");
		tmObj.set("VideoTitle", "");
		var event_banner=new Object;
		event_banner["type"]="compAd";
		event_banner["callback"]=displayCompanionBanners;
		tmObj.register(event_banner);
		tmObj.start("<?=$playerId;?>");
		tmObj.set("wmode", "opaque");
	</script>
<? }else{ ?>
<script
	type="text/javascript"
	src="http://objects.tremormedia.com/embed/js/embed.js"></script>
<script
	type="text/javascript"
	src="http://objects.tremormedia.com/embed/js/<?=$playerId;?>_p.js"></script>
<script type="text/javascript">
tmObj.set("AutoPlay", "<?php echo $autoplay;?>");
tmObj.set("PreviewImageURL",  "<?php echo $stillfilepath;?>");
tmObj.set("VideoURL", "<?php echo $videofilepath;?>");
tmObj.start("<?=$playerId;?>", "26");
tmObj.set("wmode", "opaque");

</script>
<?
	}
}

function filter_urls_msn($articleBody)
{
	global $readfile;
	$tag_start='<a';
	$tag_close='</a>';
	$tag_start_end='>';
	$length_end_tag=strlen($tag_close);

	$offsetposition=0;
	$offset_quotes=0;
	$counter=0;
	 //array initialized for file
	$field=array();
	 // CSV black listed file is read

	 $replaced_body=$articleBody;
	 $body_in_small=strtolower($replaced_body);

         //finds the  occurence of href tag in the body
         $no_of_links=substr_count($body_in_small, $tag_start);

	 if ($no_of_links>0)
	  {

	   while($counter<$no_of_links)
		{
			  // finds the posistion of each link in the body
			  $tag_start_pos=strpos($body_in_small,$tag_start,$offsetposition);

			  // finds the position of lenght of href tag in the body
			  $tag_close_pos=strpos($body_in_small,$tag_close,$offsetposition);
			 // echo "<br>tag_close_pos=".$tag_close_pos."<br>";

			  // extract the text from url
			  $body = substr($articleBody,$tag_start_pos,$tag_close_pos - $tag_start_pos + $length_end_tag);

				 $matched=has_blacklist($body,$readfile,$field);
				 if ($matched==1)
				 {

			       // extract the text between the links for eg <'a href'>
			       // this text to be extracted<'/a'>

					  $tag_start_end_pos=strpos($body,$tag_start_end,0);

					  $body_small=strtolower($body);
					//  $tag_close_pos=strpos($body_small,$tag_close,0);
                       $bodylen=strlen($body);
					 // $extracted_body=substr($body,$tag_start_end_pos + 1,$tag_close_pos - $tag_start_end_pos - 4);
					  $extracted_body=substr($body,$tag_start_end_pos + 1,$bodylen - $tag_start_end_pos-5);

				   // replaces the body  from <a till </a> tag with only text*/
				      $replaced_body= str_replace($body,$extracted_body,$replaced_body);
				 }

			  $offsetposition=$tag_close_pos+1;
			 // echo "<br>offsetposition=".$offsetposition."<br>";
			  $counter++;
		  }
         return $replaced_body;

	} else {
		return $articleBody;	 // if url not found in the body.
	}
}




function generateNewsML($recentArticlerow)
{
			global $D_R;
			include_once("$D_R/lib/_news.php");
			
			$latestArticle="";
			$articleId=$recentArticlerow['id'];
			$latestArticle=$articleId;
			$categoryId=$recentArticlerow['category_ids'];
			$articleTitle=$recentArticlerow['title'];
			$articleContrib=$recentArticlerow['contributor'];
			$articleDateOld=$recentArticlerow['date'];
			$articleDate=gmdate("Y-m-d", strtotime($articleDateOld));
			$articleDateChangeFormat=gmdate("Ymd", strtotime($articleDateOld));
			//$articleDateFirstCreated=gmdate("Y-m-d\TH:i:s\Z", strtotime($articleDateOld));
			$articleDateFirstCreated=gmdate("Ymd\THis\Z", strtotime($articleDateOld));
			$dateFileName=$articleDateOld;
			$articleKeyword=$recentArticlerow['keyword'];
			$articleBlurb=$recentArticlerow['blurb'];
			$hostedBy=$recentArticlerow['hosted_by'];
			//$patern1='/(\'|"|�|�|�|�)/';
			$recentArticlerowbody=get_full_article_body($recentArticlerow['id']);  // fetch body from article_revision table.
			$countbody=count($recentArticlerowbody);
			for($i=0;$i<=$countbody-1;$i++){
			  $minyanfeedarticlebody.=' '.$recentArticlerowbody[$i][body];
			}
			 $minyanfeedarticlebody=replaceArticleAds($minyanfeedarticlebody);
             $minyanfeedarticlebody=strip_tag_style($minyanfeedarticlebody);

			 $articlebody=$minyanfeedarticlebody;

			$articlebody = str_replace("�", "'", $articlebody);
			$articlebody = str_replace("�", "'", $articlebody);
			$articlebody = str_replace("�", '"', $articlebody);
			$articlebody = str_replace("�", '"', $articlebody);
			$articlebody = str_replace("�", "-", $articlebody);
			$articlebody = str_replace("�", "...", $articlebody);
			$articlebody=filter_urls_msn($articlebody);
			$articlebody=preg_replace("/<br \/>\s*<br \/>/", '</p><p>', $articlebody);
			$articlebody=preg_replace("/<br \/>/", "</p><p>", $articlebody);
			$articlebody=preg_replace("/<br>/", '</p><p>', $articlebody);
			$articlebody=preg_replace("/<br\/>/", '</p><p>', $articlebody);
			$articlebody=preg_replace("/&ldquo;/",'&quot;', $articlebody);
			$articlebody=preg_replace("/&rdquo;/",'&quot;', $articlebody);
			$articlebody=htmlentities($articlebody);
			$position=0;
			$category='';
			$tickers=array();
			$tags=array();

			$numWords = 100;

			$articleTempBody = htmlentities(strip_tags($minyanfeedarticlebody,'ENT_QUOTES'));



			$articleTempBodyArray = preg_split("/[\s,]+/",$articleTempBody);
			$smallBody='';
			for($i=0;$i<$numWords;$i++) {
				$smallBody .= $articleTempBodyArray[$i] . " ";
			}

			if (count($articleTempBodyArray) > 100) {
				$smallBody .= "...";
			}

			$queryRevision = "SELECT revision_number, updated_date FROM article_revision where article_id=".$articleId." order by revision_id desc ";
			$resultRevision = exec_query($queryRevision, 1);
			$revisionNumber='1';
			$revisionDate=$articleDateFirstCreated;
			$revisionUpdate='N';
			if(count($resultRevision)>0 and $resultRevision['revision_number'] > 1)
			{
				$revisionNumber=$resultRevision['revision_number'];
				$revisionDate=gmdate("Ymd\THis\Z", strtotime($resultRevision['updated_date']));;
				$revisionUpdate='U';
			}

			$currentRevision=$revisionNumber-1;

			$queryTags="select tag, type_id from ex_item_tags EIT,ex_tags ET where EIT.tag_id=ET.id and item_id=".$articleId;
			$resultTags=exec_query($queryTags);

			if(count($resultTags)>0)
			{
				foreach($resultTags as $row)
				{
					if($row[type_id]==1)
					{
							$tickers[]=$row[tag];
					}
					else
					{
						$tags[]=$row[tag];
					}
				}
			}

			$queryCategory="SELECT title as category FROM article_categories where id in (".$categoryId.")";
			$resultCategory=exec_query($queryCategory);

			$category='';
			$categoryData='';

			if(count($resultCategory) > 0)
			{


					for($i=0;$i<count($resultCategory);$i++)
					{

                        $categoryData.='<Metadata>
					                    <MetadataType FormalName="Topics"/>';
						$category=htmlentities($resultCategory[$i]['category']);

						if($category!='')
						{

							$categoryData.='<Property FormalName="Topic" Value="'.$category.'"/>';
						}
						else
						{
							$categoryData.='';
						}
						$categoryData.='</Metadata>';
					}

			}


			$tickerString='';

				foreach($tickers as $id=>$value)
				{
					$tickerString.='<Metadata>
						<MetadataType FormalName="Securities Identifier"/>
						<Property FormalName="Country" Value="US"/>
						<Property FormalName="Ticker Symbol" Value="'.$value.'"/>
					</Metadata>';
				}

			$tagString='';
			foreach($tags as $id=>$value)
			{
				$tagString.='<Property FormalName="Keyword" Value="'.$value.'"/>';
			}

			if($tagString!='')
			{
				$tagStringData.='<Metadata><MetadataType FormalName="Keywords"/>'.$tagString.'</Metadata>';
			}

			$positionStart=1;

			if(strpos(trim($articlebody),"<p>"))
			{
				$positionStart=strpos(trim($articlebody),"<p>");
			}

			if($positionStart==0)
			{

				$mainBody='<body>'.$articlebody.'</body>';
			}
			else
			{
				$mainBody='<body><p>'.$articlebody.'</p></body>';
			}

$mainBody=strip_tags($mainBody);
$smallBody=strip_tags($smallBody);

$content='<?xml version="1.0" encoding="UTF-8"?>'.'<NewsML><NewsItem>
        <Identification>
            <NewsIdentifier>
                <ProviderId>www.minyanville.com</ProviderId>
                <DateId>'.$articleDateChangeFormat.'</DateId>
                <NewsItemId>MV_'.$articleDate.'_'.$articleId.'</NewsItemId>
                <RevisionId Update="'.$revisionUpdate.'" PreviousRevision="'.$currentRevision.'">'.$revisionNumber.'</RevisionId>
				<PublicIdentifier>urn:newsml:minyanville.com:'.$articleDateChangeFormat.':MV_'.$articleDate.'_'.$articleId.':'.$revisionNumber.'</PublicIdentifier>
            </NewsIdentifier>
        </Identification>
        <NewsManagement>
            <NewsItemType FormalName="News"/>
            <FirstCreated>'.$articleDateFirstCreated.'</FirstCreated>
            <ThisRevisionCreated>'.$revisionDate.'</ThisRevisionCreated>
            <Status FormalName="Usable"/>
            <Urgency FormalName="3"/>
        </NewsManagement>
        <NewsComponent>
			<NewsLines>
				<ByLine>'.htmlentities($articleContrib).'</ByLine>
				<HeadLine>'.htmlentities($articleTitle).'</HeadLine>
				<DateLine>'.htmlentities($articleDate).'</DateLine>
				<CopyrightLine>'.htmlspecialchars(htmlentities("�2008 Minyanville Publishing and Multimedia, LLC. All Rights Reserved.")).'</CopyrightLine>
			</NewsLines>
		<AdministrativeMetadata>
			<Provider>
				<Party Logo="" FormalName="MinyanVille" ShortName="MinyanVille" />
			</Provider>
			<Property FormalName="hosted" Value="'.$hostedBy.'"/>
		</AdministrativeMetadata>
		<RightsMetaData>
			<UsageRights>
				<EndDate>'.$articleDateFirstCreated.'</EndDate>
			</UsageRights>
		</RightsMetaData>
		<DescriptiveMetadata>
			<Location HowPresent="Origin">
				<Property FormalName="Country" Value="US" />
			</Location>
			<Language FormalName="en-US" />
		</DescriptiveMetadata>';
		$content.=$tickerString;
		$content.=$categoryData;
		$content.=$tagStringData;
		$content.='<Role FormalName="Main Text"/>
		 <ContentItem>
			<MediaType FormalName="Text"/>
			<DataContent>';
			if($hostedBy=='MSN')
				{
					$content.='<html xmlns="http://www.w3.org/1999/xhtml">
					<head>
						<title/>
					</head>'.$mainBody.'
				</html>';
				}
			$content.='</DataContent>
		</ContentItem>
	</NewsComponent>
	<NewsComponent>
		<Role FormalName="Abstract" />';
		if($hostedBy=='MSN')
			{
				$content.='<ContentItem Duid="MV_'.$articleDate.'_'.$articleId.'_TEXT">';
			}
			else
			{
		        $content.='<ContentItem Duid="MV_'.$articleDate.'_'.$articleId.'_TEXT" Href="http://www.minyanville.com'.makeArticleslink($articleId,$articleKeyword,$articleBlurb).'?camp=syndication&amp;medium=portals&amp;from=msn">';
		    }
			$content.='<MediaType FormalName="Text" />
			<DataContent>
				<Description>'.$smallBody.'</Description>
			</DataContent>
		</ContentItem>
	</NewsComponent>
</NewsItem>
</NewsML>';

	if($content!='')
	{
		global $D_R;
		if(($hostedBy=='MSN')&& ($mainBody!="")){
			$feedTitle=substr($articleTitle, 0, 40);
			$paternText='/[^ a-z0-9A-Z]|-|_/';
			$feedTitle=preg_replace($paternText, '', $feedTitle);
			$special = array(' ','  ','   ','    ','     ');
			$feedTitle = str_replace(' ',' ',str_replace($special,' ',$feedTitle));
			$feedTitle=str_replace(' ', "_", $feedTitle);
			$feedTitle=strip_tags($feedTitle);
			$feedTitle=strtolower($feedTitle);
			$feedName="../assets/newsml/".date("Ymd",strtotime($dateFileName))."_".$latestArticle."_".$currentRevision."_".$feedTitle.".xml";
			$feedFile=fopen($feedName,"w+");
			fwrite($feedFile,$content);
			fclose($feedFile);
			chmod($feedName, 0777);
			echo "Feed for Article id :".$latestArticle." has generated<br>";
		}elseif($hostedBy=='Minyanville'){
			$feedTitle=substr($articleTitle, 0, 40);
			$paternText='/[^ a-z0-9A-Z]|-|_/';
			$feedTitle=preg_replace($paternText, '', $feedTitle);
			$special = array(' ','  ','   ','    ','     ');
			$feedTitle = str_replace(' ',' ',str_replace($special,' ',$feedTitle));
			$feedTitle=str_replace(' ', "_", $feedTitle);
			$feedTitle=strip_tags($feedTitle);
			$feedTitle=strtolower($feedTitle);
			$feedName="../assets/newsml/".date("Ymd",strtotime($dateFileName))."_".$latestArticle."_".$currentRevision."_".$feedTitle.".xml";
			$feedFile=fopen($feedName,"w+");
			fwrite($feedFile,$content);
			fclose($feedFile);
			chmod($feedName, 0777);
			echo "Feed for Article id :".$latestArticle." has generated<br>";
		}
	}
return $latestArticle;

}


function combine_array($keys, $values){
    if(count($keys) < 1 || count($keys) != count($values) || !is_array($keys) || !is_array($values)){
      return false;
    }

    $keys = array_values($keys);
    $values = array_values($values);
    for($x=0; $x < count($keys); $x++){
      $return_array[$keys[$x]] = $values[$x];
    }

    return $return_array;
  }

function show_ads_operative_iframe($zone_name,$tile,$size,$pos="",$dcopt="",$script=1,$timestamp=123456789)
{
	global $ADS_SERVER,$ADS_TAG_TYPE,$ADS_SITE_NAME,$_SESSION;
	$zone_name='articlepage';
	$referer_url=parse_url($_SESSION['referer']);
	list($subdomain, $domain, $domaintype) = explode(".", $referer_url['host']);
	$multipleSize=explode(",", $size);
	foreach ($multipleSize as $key => $val){
		list($width[],$height[])=explode("x",$val);
		sort($height);
		sort($width);
		$cntHeight=count($height);
		$cntWidht=count($width);
	}
	$height=$height[$cntHeight-1];
	$width=$width[$cntWidht-1];
	if($domain!="ameritrade")
	{
		if($script)
		{
		?>
		<nolayer>
<iframe
	src="<?=$ADS_SERVER;?>/adi/<?=$ADS_SITE_NAME;?>/<?=$zone_name;?>;tile=<?=$tile;?>;sz=<?=$size;?>;ord=<?=$timestamp;?>?"
	width="728" height="90" frameborder="0" marginwidth="0"
	marginheight="0" scrolling="no"> <a
	href="<?=$ADS_SERVER;?>/jump/<?=$ADS_SITE_NAME;?>/<?=$zone_name;?>;tile=<?=$tile;?>;sz=<?=$size;?>;abr=!ie4;abr=!ie5;abr=!ie6;ord=<?=$timestamp;?>?">
<img
	src="<?=$ADS_SERVER;?>/ad/<?=$ADS_SITE_NAME;?>/<?=$zone_name;?>;tile=<?=$tile;?>;sz=<?=$size;?>;abr=!ie4;abr=!ie5;abr=!ie6;ord=<?=$timestamp;?>?"
	width="728" height="90" border="0" alt=""></a> </iframe>
		</nolayer>
<table align="center" border="0">
	<tr>
		<td><ilayer id="layer1" visibility="hidden" width="728" height="90"></ilayer></td>
	</tr>
</table>
		<!-- To be placed at the end of the HTML page code, just before the /BODY tag -->
<layer
	src="<?=$ADS_SERVER;?>/adl/<?=$ADS_SITE_NAME;?>/<?=$zone_name;?>;tile=<?=$tile;?>;sz=<?=$size;?>;ord=<?=$timestamp;?>?"
	width="728" height="90" visibility="hidden"
	onLoad="moveToAbsolute(layer1.pageX,layer1.pageY);clip.width=728;clip.height=90;visibility='show';"></layer>
		<!-- End ad tag 728x90 -->
		<?
		}else{
		?>
<A
	HREF="<?=$ADS_SERVER;?>/jump/<?=$ADS_SITE_NAME;?>/<?=$zone_name;?>;<? if($pos!=""){ ?>pos=<? echo $pos.";"; } ?><? if($domain!=""){ ?>!c=<? echo $domain.";"; } ?>tile=<?=$tile;?>;sz=<?=$size?>;ord=123456789?"
	TARGET="_blank"> <IMG
	SRC="<?=$ADS_SERVER;?>/ad/<?=$ADS_SITE_NAME;?>/<?=$zone_name;?>;<? if($pos!=""){ ?>pos=<? echo $pos.";"; } ?><? if($domain!=""){ ?>!c=<? echo $domain.";"; } ?>tile=<?=$tile;?>;sz=<?=$size?>;ord=123456789?"
	WIDTH="<?=$width;?>" HEIGHT="<?=$height;?>" BORDER="0" ALT=""></A>
		<?
		}
	}
}

function cp_build_lang($prof_id)
{
	$lang=exec_query('SELECT unique_name,value FROM cp_prof_lang where prof_id='.$prof_id);
	for($i=0;$i<count($lang);$i++)
	{
		$prof_lang[$lang[$i]['unique_name']]=$lang[$i]['value'];
	}
	return $prof_lang;
}

function jack_build_lang($prof_id)
{
	$lang=exec_query('SELECT unique_name,value FROM jack_prof_lang where prof_id='.$prof_id);
	for($i=0;$i<count($lang);$i++)
	{
		$prof_lang[$lang[$i]['unique_name']]=$lang[$i]['value'];
	}
	return $prof_lang;
}

function show_adds_checkmate($pageName,$profile="",$topic_page_id=""){
	global $_SESSION,$objCache,$cm8Server,$cm8profile,$showFancybox,$showSurveybox,$adsVar,$icTag,$article_ic_tag,$scriptRefresh,$ad_author,$ad_author_id;
	global $pgCm8Cat,$pgCm8Tag;
	if(!is_object($objCache)){
		$objCache = new Cache();
	}
	if($pgCm8Tag=="")
	{
		$cm8cat=$objCache->getCM8Cat($pageName,$topic_page_id);
	}
	else
	{
		$cm8cat=$pgCm8Tag;
	}
	if($article_ic_tag!="")
	{
		$icTag=$article_ic_tag;
	}
	else
	{
		$icTag=$cm8cat['ic_tag'];
	}
	$adsVar[]="Pagetemplate=".$pageName;
	if(!empty($showFancybox) || $_SESSION['techstratFancybox']=="1"){
		$adsVar[]="fancypopup=1";
	}
	if(!empty($showSurveybox) || $_SESSION['surveyFancybox']=="1"){
		$adsVar[]="fancypopup=1";
	}
	if($_SESSION['AdsFree']){
		$adsVar[]="adsfree=1";
	}
	if($scriptRefresh=="1")
	{
		$adsVar[]="refresh=1";
	}
	if(!empty($ad_author))
	{
		$adsVar[]="author=".$ad_author;
	}
	if(!empty($ad_author_id))
	{
		$adsVar[]="author_id=".$ad_author_id;
	}

	if($_SESSION['referer']=="ameritrade" || $_SESSION['referer']=="etrade"){
		$adsVar[]="from=".$_SESSION['referer'];
	}elseif(!empty($_GET['from'])){
			$adsVar[]="from=".$_GET['from'];
	}
}

function show_adds_iframe_checkmate($pageName,$bannername,$height="90",$width="728"){
    global $HTPFX,$_SESSION;
	if(!($_SESSION['AdsFree'])) {
		global $cm8Server,$cm8profile;
		$cm8cat=getCheckmateCat($pageName);
		$cm8cat=$cm8cat[cm8cat];
		?>
<IFRAME
	src="<?=$HTPFX.$cm8Server;?>/adam/detect?tag=htm&cat=<?=$cm8cat;?>&format=<?=$bannername;?>&Key=Value"
	width="<?=$width;?>" height="<?=$height;?>" frameborder="no" border="0"
	marginwidth="0" marginheight="0" scrolling="no"></IFRAME>
		<?
	}
}

function show_cm8_noscript_ad($pageName,$bannername,$profilevalue)
{
	switch ($bannername){
		case "MediumRectangle_300x250_300x600":
			?>
			<!-- begin ad tag--><map name="adsonar_map_1587775_36403243"><area shape="rect" coords="0,0,300,79" href="http://newsletter.adsonar.com/nwrss/iMapRedirector?placementId=1587775&plid=826054&pid=3147767&ps=36403243&rotation=4&type=2&pos=0&zw=300&zh=250&v=5&url=NA&uid=" /><area shape="rect" coords="0,79,300,158" href="http://newsletter.adsonar.com/nwrss/iMapRedirector?placementId=1587775&plid=826054&pid=3147767&ps=36403243&rotation=4&type=2&pos=1&zw=300&zh=250&v=5&url=NA&uid=" /><area shape="rect" coords="0,158,300,237" href="http://newsletter.adsonar.com/nwrss/iMapRedirector?placementId=1587775&plid=826054&pid=3147767&ps=36403243&rotation=4&type=2&pos=2&zw=300&zh=250&v=5&url=NA&uid=" /><area shape="rect" coords="0,237,300,249" href="http://advertising.aol.com/technology/sponsored-listings" /></map><img src="http://newsletter.adsonar.com/nwrss/imgs/nwr_1587775_36403243_826054_3147767_4_2.PNG?placementId=1587775&plid=826054&pid=3147767&ps=36403243&rotation=4&type=2&zw=300&zh=250&v=5&url=NA&uid=" width="300" height="250" border="0" alt="" usemap="#adsonar_map_1587775_36403243" /><!-- End ad tag -->
			<?
			break;
		case "Leaderboard_728x90_970x66":
			?>
			<!-- begin ad tag--><map name="adsonar_map_1587777_36404228"><area shape="rect" coords="0,0,364,78" href="http://newsletter.adsonar.com/nwrss/iMapRedirector?placementId=1587777&plid=826054&pid=3147767&ps=36404228&rotation=4&type=2&pos=0&zw=728&zh=90&v=5&url=NA&uid=" /><area shape="rect" coords="364,0,728,78" href="http://newsletter.adsonar.com/nwrss/iMapRedirector?placementId=1587777&plid=826054&pid=3147767&ps=36404228&rotation=4&type=2&pos=1&zw=728&zh=90&v=5&url=NA&uid=" /><area shape="rect" coords="0,78,728,90" href="http://advertising.aol.com/technology/sponsored-listings" /></map><img src="http://newsletter.adsonar.com/nwrss/imgs/nwr_1587777_36404228_826054_3147767_4_2.PNG?placementId=1587777&plid=826054&pid=3147767&ps=36404228&rotation=4&type=2&zw=728&zh=90&v=5&url=NA&uid=" width="728" height="90" border="0" alt="" usemap="#adsonar_map_1587777_36404228" /><!-- End ad tag -->
			<?
			break;
		case "300x250_TextLinks":
			break;
	}
}

function CM8_ShowAd($bannername){
	global $_SESSION,$adsVar,$icTag,$HTPFX,$HTHOST;
	if($_SESSION['AdsFree']!="1")
	{
		if(empty($icTag)){
			$icTag="other";
		}
		if(is_array($adsVar)){
			$strAdsVar=implode("&",$adsVar);
		}
		switch($bannername){
			case 'Leaderboard_728x90_970x66': ?>
				<SCRIPT LANGUAGE="JavaScript">OAS_AD('Top');</SCRIPT>
				<?php break;
				
			case 'MediumRectangle_Art_300x250':
			case 'MediumRectangle_300x250_300x600':
			case 'MediumRectangle_Art_300x250_300x600': ?>
				<SCRIPT LANGUAGE="JavaScript">OAS_AD('TopRight');</SCRIPT>
				<?php break;
				
			case 'MediumRectangle_300x250':
			case 'MediumRectangle_300x250_inContent': ?>
				<SCRIPT LANGUAGE="JavaScript">OAS_AD('BottomRight');</SCRIPT>
				<?php break;
			case 'Undertone_Full_Page_Ad':
				echo "<div id='ut_piggyback'><script type='text/javascript' src='http://cdn.undertone.com/js/piggyback.js?zoneid=37903'></script></div>";
				break;
			case 'Undertone_Pagegrabber_Ad':
				echo '<div id="ut_piggyback"><script type="text/javascript" src="http://cdn.undertone.com/js/piggyback.js?zoneid=35228"></script></div>';
				break;
			case 'Undertone_Page_Skin_Ad':
				echo '<script type="text/javascript">var ut_ifurl="'.$HTPFX.$HTHOST.'/iframe.htm";</script>
<div id="ut_piggyback"><script type="text/javascript" src="http://cdn.undertone.com/js/piggyback.js?zoneid=37905"></script></div>';
				break;
				case 'adbladeNewCode':			//this is an IC ad not adblade.
					echo '<script type="text/javascript">adsonar_placementId=1593138;adsonar_pid=3167778;adsonar_ps=-1;adsonar_zw=610;adsonar_zh=300;adsonar_jv=\'ads.adsonar.com\';</script><script language="JavaScript" src="http://js.adsonar.com/js/adsonar.js"></script>';
				break;
			case 'partnerCenter':
			case 'button_partnerCenter': ?>
				<SCRIPT LANGUAGE="JavaScript">
					OAS_AD('Position1');
					OAS_AD('Position2');
					OAS_AD('Position3');
					OAS_AD('Position4');
				</SCRIPT>
				<?php break;
			case '88x31_QT':
				break;
			case '1x1_Text': ?>
				<SCRIPT LANGUAGE="JavaScript">OAS_AD('x13');</SCRIPT>
				<?php break;
			case '1x1_TextAd':
				break;
			case 'Button_160x30': ?>
				<SCRIPT LANGUAGE="JavaScript">OAS_AD('Position1');</SCRIPT>
				<?php break;
			case 'Ad_234x20':
				break;
			case 'Adblade':
				echo '<iframe style="overflow: hidden;" frameborder="0" scrolling="no" hspace="0" vspace="0" marginheight="0" marginwidth="0" width="595" height="275" src="http://web.adblade.com/impsc.php?cid=1550-1951681385&output=html"></iframe>';
				break;
			case 'Sponsorship_300x100':
				break;
		}
	}
}

function getCheckmateCat($pagename,$topic_page_id=""){
        if($topic_page_id=="")
		 {
	$qry="select cm8cat,ic_tag from layout_pages_adcategories CA,layout_pages LP  where CA.page_id=LP.id and LP.name='".$pagename."'";
		}
		else
		 {
		 $qry="select cm8cat,ic_tag from layout_pages_adcategories CA,layout_pages LP  where CA.page_id='".$topic_page_id."' limit 1";
		 }

	$result=exec_query($qry,1);
	if(isset($result)){
		 return $result;
	}else{
		return NULL;
	}
}

function getArticlePagename($id){
	$qry="select B.page_id,B.title from layout_menu A,layout_menu B where A.parent_id=B.id and A.page_id='".$id."'";
	$result=exec_query($qry,1);
	if(isset($result)){
		return $result;
	}else{
		return NULL;
	}
}

function preg_match_char($arReplacement){
	$patternchar = array("$");
	$replacementschar=array("\\$");
	$arReplacement=str_replace($patternchar, $replacementschar,$arReplacement);
	return $arReplacement;
}

function strip_tag_style($articlebody){
	$articlebody = preg_replace("/<style[^>]*>.*<\/style>/", "", $articlebody);
	return $articlebody;

}

/* array sort function for array value */

	function SortDataSet($aArray, $sField, $bDescending = false)
	{
		$bIsNumeric = IsNumeric($aArray);
		$aKeys = array_keys($aArray);
		$nSize = sizeof($aArray);

		for ($nIndex = 0; $nIndex < $nSize - 1; $nIndex++)
		{
			$nMinIndex = $nIndex;
			$objMinValue = $aArray[$aKeys[$nIndex]][$sField];
			$sKey = $aKeys[$nIndex];

				for ($nSortIndex = $nIndex + 1; $nSortIndex < $nSize; ++$nSortIndex)
				{
					if ($aArray[$aKeys[$nSortIndex]][$sField] < $objMinValue)
					{
						$nMinIndex = $nSortIndex;
						$sKey = $aKeys[$nSortIndex];
						$objMinValue = $aArray[$aKeys[$nSortIndex]][$sField];
					}
				}

			$aKeys[$nMinIndex] = $aKeys[$nIndex];
			$aKeys[$nIndex] = $sKey;
		}

		$aReturn = array();
		for($nSortIndex = 0; $nSortIndex < $nSize; ++$nSortIndex)
		{
			$nIndex = $bDescending ? $nSize - $nSortIndex - 1: $nSortIndex;
			$aReturn[$aKeys[$nIndex]] = $aArray[$aKeys[$nIndex]];
		}

		return $bIsNumeric ? array_values($aReturn) : $aReturn;
	}

	function IsNumeric($aArray)
	{
	$aKeys = array_keys($aArray);
		for ($nIndex = 0; $nIndex < sizeof($aKeys); $nIndex++)
		{
			if (!is_int($aKeys[$nIndex]) || ($aKeys[$nIndex] != $nIndex))
			{
				return false;
			}
		}

	return true;
	}

function article_body_word_replace($article_content) {

$articlebody=$article_content;

$articlebody = str_replace("�", "'", $articlebody);
$articlebody = str_replace("�", "'", $articlebody);
$articlebody = str_replace("�", '"', $articlebody);
$articlebody = str_replace("�", '"', $articlebody);
$articlebody = str_replace("�", "-", $articlebody);
$articlebody = str_replace("�", "...", $articlebody);
$articlebody=preg_replace("/<br \/>\s*<br \/>/", '</p><p>', $articlebody);
$articlebody=preg_replace("/<br \/>/", "</p><p>", $articlebody);
$articlebody=preg_replace("/<br>/", '</p><p>', $articlebody);
$articlebody=preg_replace("/<br\/>/", '</p><p>', $articlebody);
$articlebody=preg_replace("/&ldquo;/",'&quot;', $articlebody);
$articlebody=preg_replace("/&rdquo;/",'&quot;', $articlebody);
$articlebody=htmlentities($articlebody);
return $articlebody;
}

function article_categories($cid) {


$queryCategory="SELECT title as category FROM article_categories where id in (".$cid.")";
$resultCategory=exec_query($queryCategory);

		$category='';
		$categoryData='';

		if(count($resultCategory) > 0)
		{


				for($i=0;$i<count($resultCategory);$i++)
				{

					$categoryData.='<Metadata>
									<MetadataType FormalName="Topics"/>';
					$category=htmlentities($resultCategory[$i]['category']);

					if($category!='')
					{

						$categoryData.='<Property FormalName="Topic" Value="'.$category.'"/>';
					}
					else
					{
						$categoryData.='';
					}
					$categoryData.='</Metadata>';
				}

		}
return $categoryData;
}

function subscribeTopicAlertMailChimp($uid,$sec_id,$tradingRadar)
{
	global $D_R,$mailChimpApiKey,$authorSubsList, $authorListTemplate;
	include_once($D_R."/lib/config/_mailchimp_config.php");
	include_once($D_R."/lib/mailchipapi/MCAPI.class.php");
	include_once($D_R."/lib/mailchipapi/mailchimp_data_lib.php");
	
	$objApi = new MCAPI($mailChimpApiKey);
	$sql = "SELECT email,fname,lname FROM subscription S where id='".$uid."' ";
	$userResult=exec_query($sql,1);
	if($tradingRadar!="1")
	{
		if(is_array($sec_id))
		{
			$sec_id = explode(',',$sec_id['section_ids']);
			$sec_id = array_filter($sec_id);
			$sec_id = implode("','",$sec_id);
			$sqlSec = "SELECT `name` FROM section WHERE is_active='1' AND TYPE='subsection' AND subsection_type='article' AND section_id IN ('".$sec_id."') ";
			
		}
		else{
			$sqlSec = "SELECT `name` FROM section WHERE is_active='1' AND TYPE='subsection' AND subsection_type='article' AND section_id IN ('".$sec_id."') ";
		}
	
		$userSecResult=exec_query($sqlSec);
		foreach($userSecResult as $k=>$v)
		{
			$secName[] = $v['name'];
		}
		$secNameList = implode(',',$secName);
	}
	else{
		$secNameList="Trading Radar";
	}
	$secNameList = checkInterests($userResult['email'],$secNameList);
	
	$merge_vars = array("FNAME"=>$userResult['fname'], "LNAME"=>$userResult['lname'],
                             'INTERESTS'=>$secNameList);
	$resSend = $objApi->listSubscribe($authorSubsList, $userResult['email'], $merge_vars, 'html', false, true, true, false);

}

function sendTopicMailChimp($subject,$mailbody, $interest)
{
	global $D_R,$mailChimpApiKey,$authorSubsList, $authorListTemplate;
	include_once($D_R."/lib/config/_mailchimp_config.php");
	include_once($D_R."/lib/mailchipapi/MCAPI.class.php");
	include_once($D_R."/lib/mailchipapi/mailchimp_data_lib.php");

	$objApi = new MCAPI($mailChimpApiKey);
	$conditions = array();
	$conditions[] = array('field'=>'interests', 'op'=>'one', 'value'=>$interest);
	
	$segment_opts = array('match'=>'any', 'conditions'=>$conditions);
	 $options = array('list_id'=>$authorSubsList,'subject'=>$subject,'from_email'=>'support@minyanville.com',
	 'from_name'=>'Minyanville','to_name'=>'Minyanville','template_id'=>$authorListTemplate);
	$content = array('html'=>$mailbody,'text'=>strip_tags($mailbody));
	$res = $objApi->campaignCreate("regular", $options, $content, $segment_opts, $type_opts=NULL);
	if($res=="1")
	{
		//$resSend = $objApi->campaignSendNow($res);
		$resSend="1";
		if($resSend=="1")
		{
			return $resSend;
		}
		else
		{
			return "-1";
		}	
	}
	
}

function checkInterests($mail,$secNameList)
{

	global $D_R,$mailChimpApiKey,$authorSubsList, $authorListTemplate;
	include_once($D_R."/lib/config/_mailchimp_config.php");
	include_once($D_R."/lib/mailchipapi/MCAPI.class.php");
	include_once($D_R."/lib/mailchipapi/mailchimp_data_lib.php");
	
	$objApi = new MCAPI($mailChimpApiKey);
	$userData = $objApi->listMemberInfo($authorSubsList, $mail);
	$interestList = $userData['data']['0']['merges']['GROUPINGS']['0']['groups'];
	
	if($interestList!="")
	{
		$subsList = explode(",",$interestList);
	}
	$subsNewList = explode(',',$secNameList);
	$diff = array_diff($subsList, $subsNewList);
	if(!empty($diff))
	{
		$subsList = array_diff($subsNewList,$subsList);
		$subsList = implode(',',$subsList);
		return $subsList;
	}
	else{
		return $secNameList;
	}
	
}

function subscribeMailChimpUser($email,$listName,$first_name,$last_name)
{
	global $D_R,$mailChimpApiKey,$mailChimpListId,$prodList;
	include_once($D_R."/lib/config/_mailchimp_config.php");
	include_once($D_R."/lib/mailchipapi/MCAPI.class.php");
	include_once($D_R."/lib/mailchipapi/mailchimp_data_lib.php");

	$objApi = new MCAPI($mailChimpApiKey);
	$list = $mailChimpListId[$listName];
	
	$merge_vars = array("FNAME"=>$first_name, "LNAME"=>$last_name);
	$resSub = $objApi->listSubscribe($list, $email, $merge_vars);
	
	if($resSub!="1")
	{
		$to="noel@minyanville.com,philip@minyanville.com,nidhi.singh@mediaagility.co.in";
		$from="support@minyanville.com";
		$subject="Please subscribe the user in MailChimp";
	 	$msg = "Please subscribe the user ".$email." with first name '".$first_name."' and last name '".$last_name."' in the List '".$prodList[$listName]."' in MailChimp";
		 mymail($to,$from,$subject,$msg);
	}

	return $resSub;
}

function subscribeUser($email,$product,$first_name,$last_name)
{
	global $D_R,$mailChimpApiKey,$productListId,$mailChimpProduct;
	include_once($D_R."/lib/config/_mailchimp_config.php");
	include_once($D_R."/lib/mailchipapi/MCAPI.class.php");
	include_once($D_R."/lib/mailchipapi/mailchimp_data_lib.php");

	$objApi = new MCAPI($mailChimpApiKey);
	$list = $productListId[$mailChimpProduct[$product]];

	$merge_vars = array("FNAME"=>$first_name, "LNAME"=>$last_name);
	$resSub = $objApi->listSubscribe($list, $email, $merge_vars, 'html', false, true, false, false);
	if($resSub!="1")
	{
		$to="nidhi.singh@mediaagility.co.in";
		$from="support@minyanville.com";
		$subject="Subscribe error";
		$msg = "User ".$email." <br>
		name ".$first_name."".$last_name." <br>
		product '".$product."' <br>
		mailchimp product '".$mailChimpProduct[$product]."' <br>
		in the List '".$list."' in MailChimp <br>
		";
		mymail($to,$from,$subject,$msg);
	}
	return $resSub;
}

function unSubscribeUser($email,$product)
{
	global $D_R,$mailChimpApiKey,$productListId,$mailChimpProduct;
	include_once($D_R."/lib/config/_mailchimp_config.php");
	include_once($D_R."/lib/mailchipapi/MCAPI.class.php");
	include_once($D_R."/lib/mailchipapi/mailchimp_data_lib.php");

	$objApi = new MCAPI($mailChimpApiKey);
	$list = $productListId[$mailChimpProduct[$product]];

	$resUnSub = $objApi->listUnsubscribe($list, $email, false,false , false);
	if($resUnSub!="1")
	{
		$to="nidhi.singh@mediaagility.co.in";
		$from="support@minyanville.com";
		$subject="Unsubscribe error";
		$msg = "User ".$email." <br>
		product '".$product."' <br>
		mailchimp product '".$mailChimpProduct[$product]."' <br>
		in the List '".$list."' in MailChimp <br>
		";
		mymail($to,$from,$subject,$msg);
	}
	return $resUnSub;
}

function sendProductMails($subject,$body,$product)
{
	global $D_R,$mailChimpApiKey,$productTemplateId,$productListId,$productFromName;
	include_once($D_R."/lib/config/_mailchimp_config.php");
	include_once($D_R."/lib/mailchipapi/MCAPI.class.php");
	include_once($D_R."/lib/mailchipapi/mailchimp_data_lib.php");

	$objApi = new MCAPI($mailChimpApiKey);
	
	/**********  Update the Template *********/
	$mailbody = $body;
	$values = array("html"=>$mailbody);
	$objApi->templateUpdate($productTemplateId[$product], $values);
	
	$subject= stripslashes($subject);

	/**********  Create a Campaign *********/
	$options = array('list_id'=>$productListId[$product],'subject'=>$subject,'from_email'=>'support@minyanville.com','from_name'=>$productFromName[$product],'to_name'=>'Minyanville','template_id'=>$productTemplateId[$product]);
	$content = array('html'=>$mailbody,'text'=>strip_tags($mailbody));
	$res = $objApi->campaignCreate("regular", $options, $content, $segment_opts=NULL, $type_opts=NULL);
	if($res!="")
	{
		/******** Send the campign to all the users in the list **********/
		$resSend = $objApi->campaignSendNow($res);
		if($resSend=="1")
		{
			return $res;
		}
		else
		{
			return "-1";
		}
	}

}

function sendDailyDigestMails($msgurl,$subject)
{
	global $D_R,$mailChimpApiKey,$dailyDigestTemplateId,$dailyDigestListId;
	include_once($D_R."/lib/config/_mailchimp_config.php");
	include_once($D_R."/lib/mailchipapi/MCAPI.class.php");
	include_once($D_R."/lib/mailchipapi/mailchimp_data_lib.php");

	$objApi = new MCAPI($mailChimpApiKey);

	/**********  Update the Template *********/
	$mailbody=inc_web($msgurl);
	$values = array("html"=>$mailbody);
	$objApi->templateUpdate($dailyDigestTemplateId, $values);

	/**********  Create a Campaign *********/
	$options = array('list_id'=>$dailyDigestListId,'subject'=>$subject,'from_email'=>'support@minyanville.com','from_name'=>'Minyanville','to_name'=>'Minyanville','template_id'=>$dailyDigestTemplateId);
	$content = array('html'=>$mailbody,'text'=>strip_tags($mailbody));
	$res = $objApi->campaignCreate("regular", $options, $content, $segment_opts=NULL, $type_opts=NULL);
	if($res!="")
	{
		/******** Send the campign to all the users in the list **********/
		$resSend = $objApi->campaignSendNow($res);
		if($resSend=="1")
		{
			return $res;
		}
		else
		{
			return "-1";
		}
	}
}
function article_tickers($aid){

$tickers=array();
$tags=array();

$queryTags="select tag, type_id from ex_item_tags EIT,ex_tags ET where EIT.tag_id=ET.id and item_id=".$aid;
						$resultTags=exec_query($queryTags);

						if(count($resultTags)>0)
						{
							foreach($resultTags as $row)
							{
								if($row[type_id]==1)
								{
										$tickers[]=$row[tag];
								}
								else
								{
									$tags[]=$row[tag];
								}
							}
						}

				$tickerString='';

					// Metadata
					foreach($tickers as $id=>$value)
					{
						$tickerString.='<Metadata>
							<MetadataType FormalName="Securities Identifier"/>
							<Property FormalName="Country" Value="US"/>
							<Property FormalName="Ticker Symbol" Value="'.$value.'"/>
						</Metadata>';
					}
	return $tickerString;

}

function article_tags($aid) {

$tickers=array();
$tags=array();

$queryTags="select tag, type_id from ex_item_tags EIT,ex_tags ET where EIT.tag_id=ET.id and item_id=".$aid;
						$resultTags=exec_query($queryTags);

						if(count($resultTags)>0)
						{
							foreach($resultTags as $row)
							{
								if($row[type_id]==1)
								{
										$tickers[]=$row[tag];
								}
								else
								{
									$tags[]=$row[tag];
								}
							}
						}

				$tickerString='';

					// Metadata
					foreach($tickers as $id=>$value)
					{
						$tickerString.='<Metadata>
							<MetadataType FormalName="Securities Identifier"/>
							<Property FormalName="Country" Value="US"/>
							<Property FormalName="Ticker Symbol" Value="'.$value.'"/>
						</Metadata>';
					}

				$tagString='';
				foreach($tags as $id=>$value)
				{
					$tagString.='<Property FormalName="Keyword" Value="'.$value.'"/>';
				}

				if($tagString!='')
				{
					$tagStringData.='<Metadata><MetadataType FormalName="Keywords"/>'.$tagString.'</Metadata>';
				}
return $tagStringData;
}
function getstockdetailsfromYahoo($symbolname){ /*Validate ticker from Yahoo and if validate return value of ticker*/
$tickersymbol=$symbolname;
	if (isset($tickersymbol))
	{
		$open = @fopen("http://download.finance.yahoo.com/d/quotes.csv?s=$tickersymbol&f=sl1d1t1c1ohgvnx&e=.csv", "r");
		$read = @fread($open, 2000);
		@fclose($open);
		unset($open);
		$read = str_replace("\"", "", $read);
		$read = explode(",", $read);
		if ($read[1] == 0)
		{
			return 0;
		}else{
			return $read;
		}
	}
}
function settStockTickerforYahoo($validateticker){ /*Insert data in ex_stock table*/
	$stocktabldata=array(stocksymbol=>$validateticker[0],
						CompanyName=>addslashes(trim($validateticker[9])));
	$stocktabldata['stocksymbol']=$validateticker[0];
	$tickerexchange=trim($validateticker[10]);
	if($tickerexchange=="NYSE"){
		$stocktabldata['exchange']="NYSE";
	}else{
		$stocktabldata['exchange']="NASDAQ";
	}
	$stocktabldata['SecurityName']=addslashes(trim($validateticker[9]));
	$stocktabldata['CompanyName']=addslashes(trim($validateticker[9]));
	$sid=insert_query("ex_stock",$stocktabldata);
	if($sid){
		return $sid;
	}else{
		return 0;
	}

}


function generateYahooXml($recentArticlerow,$item_text,$yahooSyndication,$yahooFullBodySyndication){
	global $D_R;
	include_once($D_R.'/lib/config/_syndication_config.php');
	global $HTPFX,$HTHOST,$HTNOSSLDOMAIN,$yahoouser,$yahoopass,$yahoohost,$yahoofullpath,$yahoopath,$D_R,$feed_error_template,$NOTIFY_FEED_ERROR_TO,$NOTIFY_FEED_ERROR_FROM,$NOTIFY_FEED_ERROR_SUBJECT,$ftpError,$relatedArticleArr;


	if($yahooFullBodySyndication=="1") { $yahooSyndication="0";  }

	$tagobj= new Exchange_Element();
	$objArticle	= new ArticleData();
	$tagarray = getTagArr($recentArticlerow['id']);
	
	$issynd=1;
	$minyanhosted=1;
	$date=$recentArticlerow['date'];
	$uid=gmdate("Ymd",strtotime($date));
	$uid=$uid.$recentArticlerow['id'];
	
	$unique_stocks=array_unique($tagarray);
	foreach($unique_stocks as $value){
		$getExchange=exec_query("select exchange from ex_stock where stocksymbol='".$value."'",1);
		$strfeed.='<ticker>'.$getExchange['exchange'].':'.strtoupper($value).'</ticker>';
		$log[$i]['item_id']=$recentArticlerow['id'];
		$log[$i]['item_type']='1';
		$log[$i]['feed_name']=$uid.".xml";
		$log[$i]['ticker']=strtoupper($value);
		$i++;
	}
	if($yahooFullBodySyndication=="1")
	{
		insertSyndication($recentArticlerow['id'],$item_text,"yahoo_full_body",$issynd,$minyanhosted);
		echo "<br> Feed for Article id :".$uid." has generated for Full Body<br>";
	}
	else
	{
		insertSyndication($recentArticlerow['id'],$item_text,"yahoo",$issynd,$minyanhosted);
		echo "Feed for Article id :".$uid." has generated<br>";
	}
	foreach($log as $logValue){
		insert_query("yahoo_logs", $logValue);
	}
	
	return $uid.".xml";
}

function getTagArr($id)
{
	$objArticle	= new ArticleData();
	$gettag		=	$objArticle->getTagsOnArticles($id,'1');
		$tagarray=array();
	    foreach($gettag as $tagvalue)
		{
		    $validatetag=is_stock($tagvalue['tag']);
			if($validatetag['exchange']){ // if entry in ex_stock table
		 	$tagarray[]=$tagvalue['tag'];
			}
			else // Verify from Yahoo
			{
				$validateticker=getstockdetailsfromYahoo($tagvalue['tag']); /*varify ticker from yahoo*/
				if($validateticker[0])
				{
					 $insertTickerid=settStockTickerforYahoo($validateticker); /*Insert data in the ex_stock table if verify from yahoo*/
					 $tagarray[]=$tagvalue['tag'];
				}
			}
		 }
		 return $tagarray;
}

function setYahooOutboundDataCache($id)
{
	
	$objArticle	= new ArticleData();
	$sql = "select * from articles where id='".$id."'";
	$recentArticlerow = exec_query($sql);
	if(!empty($recentArticlerow))
	{
		if($recentArticlerow['is_audio']=='1'){
			$audio = $objArticle->getArticleAudio($recentArticlerow['id']);
			foreach($audio as $aud){
				if($aud['item_key']=='audiofile'){
					$audioFile = $aud['item_value'];
				}elseif($aud['item_key']=='radiofile'){
					$radioFile = $aud['item_value'];
				}
			}
		}else{
			$audioFile="";
			$radioFile="";
		}

		$gettag		=	$objArticle->getTagsOnArticles($recentArticlerow['id'],'1');
		$tagarray=array();
	    foreach($gettag as $tagvalue)
		{
		    $validatetag=is_stock($tagvalue['tag']);
			if($validatetag['exchange']){ // if entry in ex_stock table
		 	$tagarray[]=$tagvalue['tag'];
			}
			else // Verify from Yahoo
			{
				$validateticker=getstockdetailsfromYahoo($tagvalue['tag']); /*varify ticker from yahoo*/
				if($validateticker[0])
				{
					 $insertTickerid=settStockTickerforYahoo($validateticker); /*Insert data in the ex_stock table if verify from yahoo*/
					 $tagarray[]=$tagvalue['tag'];
				}
			}
		 }
		 
		 	$articleBody=$recentArticlerow['body'];
		 	$keywords=$recentArticlerow['keyword'];
			$articleBody = mswordReplaceSpecialChars($articleBody);
			$articleBody = replaceArticleAds($articleBody);
			$regex = '/<object(.*?)<\/object>/i';
			$articleBody = preg_replace($regex, '', $articleBody);
			$articleBody = str_replace('<strong','<b',$articleBody);
			$articleBody = str_replace('</strong>','</b>',$articleBody);
			$articleBody = str_replace('<em','<i',$articleBody);
			$articleBody = str_replace('</em>','</i>',$articleBody);
			if(substr_count($articleBody,"{FLIKE}") > 0){
					$articleBody = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $articleBody);
			}
			if(substr_count($articleBody,"{AUDIO}") > 0)
			{
				if($audioFile!='')
				{
					$articleBody = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $articleBody);
				}
				else
				{
					$articleBody = str_replace("{AUDIO}"," ", $articleBody);
				}
			}
			if(substr_count($articleBody,"{RADIO}") > 0)
			{
				if($radioFile!='')
				{
					$articleBody = str_replace("{RADIO}","<div>".displayRadioInArticle($radioFile,$layoutType)."</div>", $articleBody);
				}
				else
				{
					$articleBody = str_replace("{RADIO}"," ", $articleBody);
				}
			}
			preg_match_all('/\<a[^>]+>/i', $articleBody, $matches);
			$urlArr = array_map('removeParameters',$matches[0]);
			
			$linkCount = count(array_unique($urlArr));
			if($linkCount<4)
			{
				$objcache=new Cache();
				$articleBody = $objcache->setOutboundLinksCache($articleBody,$linkCount,$keywords,$tagarray,$recentArticlerow['id']);
			}		
	}
}
	
function generateYahooXml_old($recentArticlerow,$item_text,$yahooSyndication,$yahooFullBodySyndication){
	global $D_R;
	include_once($D_R.'/lib/config/_syndication_config.php');
    global $HTPFX,$HTHOST,$HTNOSSLDOMAIN,$yahoouser,$yahoopass,$yahoohost,$yahoofullpath,$yahoopath,$D_R,$feed_error_template,$NOTIFY_FEED_ERROR_TO,$NOTIFY_FEED_ERROR_FROM,$NOTIFY_FEED_ERROR_SUBJECT,$ftpError,$relatedArticleArr;
    $title=htmlentities(utf8_decode($recentArticlerow['title']));

	if($yahooFullBodySyndication=="1") { $yahooSyndication="0";  }

	$tagobj= new Exchange_Element();
	$objArticle	= new ArticleData();
	$gettag		=	$objArticle->getTagsOnArticles($recentArticlerow['id'],'1');

	$tagarray=array();
    foreach($gettag as $tagvalue)
	{
	    $validatetag=is_stock($tagvalue['tag']);
		if($validatetag['exchange']){ // if entry in ex_stock table
	 	$tagarray[]=$tagvalue['tag'];
		}
		else // Verify from Yahoo
		{
			$validateticker=getstockdetailsfromYahoo($tagvalue['tag']); /*varify ticker from yahoo*/
			if($validateticker[0])
			{
				 $insertTickerid=settStockTickerforYahoo($validateticker); /*Insert data in the ex_stock table if verify from yahoo*/
				 $tagarray[]=$tagvalue['tag'];
			}
		}
	 }
	/*$uniquestocks= make_stock_array(strip_tags($recentArticlerow['body']),$countstock="1");
	$unique_stocks=array_unique(array_merge($tagarray,$uniquestocks));*/

	if($yahooFullBodySyndication == "1")
	{
		$relatedArticles = getSyndRelatedArticles($recentArticlerow['id'],"");

		$relatedLinks = "<p><strong>Related Articles</strong><ul>";
		$relatedArticleArr[]=$recentArticlerow['id'];
		if(!empty($relatedArticles))
		{
			foreach($relatedArticles as $key=>$val)
			{
				$relatedArticleArr[]=$val['item_id'];
				$relatedLinks.="<li><a href='".$HTPFX.$HTHOST.$val['url']."?camp=syndication&medium=hostedportals&from=yahoo'>".$val['title']."</a></li>";
			}
			$relatedLinks.="</ul></p>";

		}
		else
		{
			$relatedKeyArticles = getKeyRelatedMatch($recentArticlerow['id'],$recentArticlerow['keyword']);
			foreach($relatedKeyArticles as $k=>$v)
			{
				$relatedArticleArr[]=$v['item_id'];
				$relatedLinks.="<li><a href='".$HTPFX.$HTHOST.$v['url']."?camp=syndication&medium=hostedportals&from=yahoo'>".$v['title']."</a></li>";
			}
			$relatedLinks.="</ul></p>";
		}
	}

	$unique_stocks=array_unique($tagarray);
	if($recentArticlerow['is_audio']=='1'){
		$audio = $objArticle->getArticleAudio($recentArticlerow['id']);
		foreach($audio as $aud){
			if($aud['item_key']=='audiofile'){
				$audioFile = $aud['item_value'];
			}elseif($aud['item_key']=='radiofile'){
				$radioFile = $aud['item_value'];
			}
		}
	}else{
		$audioFile="";
		$radioFile="";
	}

	$date=$recentArticlerow['date'];
	$articleId=$recentArticlerow['id'];
	$articleKeyword=$recentArticlerow['keyword'];
	$articleBlurb=$recentArticlerow['blurb'];
	$articleURL=makeArticleslink($articleId,$articleKeyword,$articleBlurb);
	$articleFullURL = $HTPFX.$HTHOST.$articleURL;
	$articleFullURL = recursive_array_replace($purifier,$articleFullURL);
	$layoutType = $recentArticlerow['layout_type'];
	$articleBody=$recentArticlerow['body'];
	$articleBody = mswordReplaceSpecialChars($articleBody);
	$articleBody = replaceArticleAds($articleBody);
	$regex = '/<object(.*?)<\/object>/i';
	$articleBody = preg_replace($regex, '', $articleBody);
	$articleBody = str_replace('<strong','<b',$articleBody);
	$articleBody = str_replace('</strong>','</b>',$articleBody);
	$articleBody = str_replace('<em','<i',$articleBody);
	$articleBody = str_replace('</em>','</i>',$articleBody);
	if(substr_count($articleBody,"{FLIKE}") > 0){
		$articleBody = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $articleBody);
	}
	if(substr_count($articleBody,"{AUDIO}") > 0)
	{
		if($audioFile!='')
		{
			$articleBody = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $articleBody);
		}
		else
		{
			$articleBody = str_replace("{AUDIO}"," ", $articleBody);
		}
	}
	if(substr_count($articleBody,"{RADIO}") > 0)
	{
		if($radioFile!='')
		{
			$articleBody = str_replace("{RADIO}","<div>".displayRadioInArticle($radioFile,$layoutType)."</div>", $articleBody);
		}
		else
		{
			$articleBody = str_replace("{RADIO}"," ", $articleBody);
		}
	}
	if($yahooFullBodySyndication == "1")
	{
		preg_match_all('/\<a[^>]+>/i', $articleBody, $matches);
		$urlArr = array_map('removeParameters',$matches[0]);
		$linkCount = count(array_unique($urlArr));
		if($linkCount<4)
		{
			$articleBody = addOutboundLinks($articleBody,$linkCount,$recentArticlerow['keyword'],$tagarray);
		}
		if(!empty($relatedArticles) || !empty($relatedKeyArticles))
		{
			$articleBody .= $relatedLinks;
		}
	}

	if(!$articleBlurb){
		$character_text=$recentArticlerow['character_text'];
	}else{
		$character_text=$articleBlurb;
	}
	$character_text=htmlentities($character_text);

	if($yahooFullBodySyndication == "1"){
		$url=$HTNOSSLDOMAIN.$articleURL."?camp=syndication&amp;medium=hostedportals&amp;from=yahoo";
	}else{
		$url=$HTNOSSLDOMAIN.$articleURL."?camp=syndication&amp;medium=portals&amp;from=yahoo";
	}

	$url=str_replace("https:","http:",$url);
    $author=$recentArticlerow['contributor'];
    $author=htmlentities($author, ENT_QUOTES);
	$uid=gmdate("Ymd",strtotime($date));
	$uid=$uid.$recentArticlerow['id'];
	$strfeed='';
	$strfeed='<?xml version="1.0" encoding="utf-8"?>
	<article>
	 <tickers>';
	 $i=1;
	 foreach($unique_stocks as $value){
		$getExchange=exec_query("select exchange from ex_stock where stocksymbol='".$value."'",1);
		$strfeed.='<ticker>'.$getExchange['exchange'].':'.strtoupper($value).'</ticker>';
		$log[$i]['item_id']=$articleId;
		$log[$i]['item_type']='1';
		$log[$i]['feed_name']=$uid.".xml";
		$log[$i]['ticker']=strtoupper($value);
		$i++;
	}
	$strfeed.='</tickers>
		<title><![CDATA['.$title.']]></title>
	  <subtitle></subtitle>
	  <description>'.$character_text.'</description>
	  <publicationdate>'.$date.'</publicationdate>
	  <url>'.$url.'</url>
	  <byline>'.$author.'</byline>
	  <body><![CDATA['.$articleBody.']]></body>
	  <uid>'.$uid.'</uid>
	  <related></related>
	</article>';

	$feedName="../assets/yahoofeed/minyanville/".$uid.".xml";
	$feedFile=fopen($feedName,"w+");
	fwrite($feedFile,$strfeed);
	fclose($feedFile);
	chmod($feedName, 0777);
	$localPath=$D_R."/assets/yahoofeed/minyanville/".$uid.".xml";
	$host=$yahoohost;
	$user=$yahoouser;
	$pass=$yahoopass;
	$path=$yahoopath;
	$fullbodypath = $yahoofullpath;

	/*$host='ftp.minyanville.com';
	$user='dave';
	$pass='@Gi3~5!R';
	$path='/home/sites/minyanville/tsp/';*/

	if($yahooFullBodySyndication == "1")
	{
		$chkfullftp=ftpPut($localPath, $user, $pass, $host,$fullbodypath);
	}
	else
	{
		$chkftp=ftpPut($localPath, $user, $pass, $host,$path);
	}

if($chkftp || $chkfullftp){
		$issynd=1;
		$minyanhosted=1;
		if($chkftp=="1")
		{
			insertSyndication($articleId,$item_text,"yahoo",$issynd,$minyanhosted);
			echo "Feed for Article id :".$uid." has generated<br>";
		}
		if($chkfullftp=="1")
		{
			insertSyndication($articleId,$item_text,"yahoo_full_body",$issynd,$minyanhosted);
			echo "<br> Feed for Article id :".$uid." has generated for Full Body<br>";
		}
		foreach($log as $logValue){
			insert_query("yahoo_logs", $logValue);
		}
	}
	else{
		$to=$NOTIFY_ERROR_TO_MA;
		$from=$NOTIFY_FEED_ERROR_FROM;
		$subject=$NOTIFY_FEED_ERROR_SUBJECT.'-'.$title;
		$file=$localPath;
		$strurl = 'title=' . urlencode($title) . '&syndchannel=' . urlencode($syndchannel)."&error=".$ftpError;
	 	mymail($to,$from,$subject,inc_web("$feed_error_template?$strurl"),$text,$file);

	}

	return $uid.".xml";

}

function removeParameters($arr)
{
        $url = explode('?', $arr);
        return $url[0];
}


function insertSyndication($articleId,$item_text,$syndchannel,$issynd,$minyanhosted){
	$getItemId=exec_query("select id from ex_item_type where item_text='".$item_text."'",1);
	$checkSyndication=getSyndication($articleId,$getItemId['id'],$syndchannel);
		$parsyndicate['syndication_channel']=$syndchannel;
		$parsyndicate['is_syndicated']=$issynd;
		$parsyndicate['is_hosted_by_minyanville']=$minyanhosted;
	if($checkSyndication){
		$parsyndicate['syndication_updated_on']=date("Y-m-d H:i:s");
		update_query("content_syndication",$parsyndicate,array('item_id'=>$articleId,'item_type'=>$getItemId['id'],'syndication_channel'=>$syndchannel));
	}else{
	    $parsyndicate['item_id']=$articleId;
		$parsyndicate['item_type']=$getItemId['id'];
		$parsyndicate['syndicated_on']=date("Y-m-d H:i:s");
		$idftpinsert=insert_query("content_syndication",$parsyndicate);

	}
}

function addOutboundLinks($body,$count,$keyword,$tickers)
{
	global $relatedArticleArr,$HTPFX,$HTHOST;

	$keyword = explode("-",strtolower($keyword));
	$tickers = array_map("strtolower", $tickers);
	$keyword = array_diff($keyword, $tickers);

	$keyword = implode("|",$keyword);
    $tickers = implode("|",$tickers);
    $linkCount= (4-$count);

    if($linkCount>0)
	{
		$body = "<p>".$body."</p>";
		$pPattern = '#(?:<br\s*/?>\s*?){2,}#';
		$body = preg_replace($pPattern, "</p> <p>", $body);
		$emptyPPattern  = "/<p[^>]*>[\s|&nbsp;]*<\/p>/";
		$body = preg_replace($emptyPPattern, " ", $body);
		$bodyBrk = explode("<p>",$body);
	
		$count="0";
		$wordCount="0";

		foreach($bodyBrk as $key=>$val)
		{
			$content .= $val;
			$artBody = strip_tags($content);
			preg_match_all('/\b('.$keyword.')\b/i', $artBody, $keyMatch);
			preg_match_all('/\b:('.$tickers.')\b/i', $artBody, $tickMatch);
	
	        if(!empty($keyMatch[0]) || !empty($tickMatch))
	        {
				$content="";
				$wordCount="0";
				$matchKeywords = array_unique(array_map("strtolower",$keyMatch[0]));
				$matchTickers = array_unique(array_map("strtolower",$tickMatch[0]));
				$matchTickers = array_map("filterTickers", $matchTickers);
				$relatedList = getRelatedMatch($matchKeywords,$matchTickers);
	
				if(is_array($relatedList) && !empty($relatedList))
				{
					$relatedArticleArr[] = $relatedList[item_id];
					$url = "<p>[More from Minyanville.com: <a href='".$HTPFX.$HTHOST.$relatedList['url']."?camp=syndication&medium=hostedportals&from=yahoo'>".$relatedList['title']." </a>]</p>";
					$bodyBrk[$key]=$bodyBrk[$key].$url;
					$count++;
				}
				if($count>=$linkCount)
				{
					break;
				}
			}
		}
		$artFullBody = implode('<p>',$bodyBrk);
	}
	else
	{
		$artFullBody = $body;
	}
	return $artFullBody;
}


function filterTickers($tickers)
{
	$tickers = substr($tickers, 1);
	return $tickers;
}

function getKeyRelatedMatch($id,$keywords)
{
	if(!empty($keywords)) { $keywordLst = str_replace("-","|",$keywords); }

	$sql="SELECT DISTINCT(EITOutter.item_id),
 IF(A.publish_date = '0000-00-0000:00:00',A.date,A.publish_date)
  AS pubDate, A.id,A.title,eim.url FROM ex_item_tags EITOutter,
  articles A,ex_item_meta AS eim WHERE eim.item_id=A.id AND eim.item_type='1' AND A.is_live = '1' AND A.approved = '1'
  AND A.date>(NOW() - INTERVAL 4 MONTH ) AND eim.is_live='1'
   AND eim.publish_date>(NOW() - INTERVAL 4 MONTH ) AND A.id=EITOutter.item_id AND
  EITOutter.item_type='1' AND A.keyword REGEXP '(".$keywordLst.")' and A.id NOT IN ('".$id."') ORDER BY pubDate DESC  LIMIT 3";
	$getList=exec_query($sql);
	if(!empty($getList)){
		return $getList;
	}else{
		return "-1";
	}
}


function getRelatedMatch($keywords,$tickers)
{
	global $relatedArticleArr;

	if(!empty($relatedArticleArr)) { $relatedArticleList = implode("','",$relatedArticleArr); }
	if(!empty($keywords)) { $keywordLst = implode("|",$keywords); }
	if(!empty($tickers)) { $tickerLst = implode("','",$tickers); }

	$sql="SELECT DISTINCT(EITOutter.item_id),
 IF(A.publish_date = '0000-00-0000:00:00',A.date,A.publish_date)
  AS pubDate, A.id,A.title,eim.url FROM ex_item_tags EITOutter,
  articles A,ex_item_meta AS eim WHERE eim.item_id=A.id AND eim.item_type='1' AND A.is_live = '1' AND A.approved = '1'
  AND A.date>(NOW() - INTERVAL 4 MONTH ) AND eim.is_live='1'
   AND eim.publish_date>(NOW() - INTERVAL 4 MONTH ) AND A.id=EITOutter.item_id AND
  EITOutter.item_type='1' AND (tag_id IN
 (SELECT id FROM `ex_tags` ET
WHERE ET.tag IN ('".$tickerLst."') ) ";
	if(!empty($keywords) && empty($tickers)) {
		$sql.=" OR A.keyword REGEXP '(".$keywordLst.")' ";
	}
	if(!empty($relatedArticleArr))
	{
		$sql.=" ) AND A.id NOT IN ('".$relatedArticleList."') ";
	}
	else
	{
		$sql.=" ) ";
	}
	$sql.=" ORDER BY pubDate DESC  LIMIT 1";
	$getList=exec_query($sql,1);
	if(!empty($getList)){
		return $getList;
	}else{
		return "-1";
	}
}

function getSyndication($itemid,$itemtype,$syndchannel){
	$getSyndicate="select id from content_syndication where item_id='".$itemid."' and item_type='".$itemtype."' and syndication_channel='".$syndchannel."'";
	$getValue=exec_query($getSyndicate,1);
	if(isset($getValue)){
		return $getValue;
	}else{
		return 0;
	}
}

function checkQueryString()
{
	foreach($_GET as $key=>$val)
	{
		if(!empty($val))
		{
			if(preg_match('/^[a-zA-Z0-9-&amp;&_, \"\']+$/',$val))
			{
				$_GET[$key]=$val;
			}
			else
			{
				$_GET[$key]="";
			}
		}
	}
}

function delSyndication($itemid,$itemtype,$syndchannel)
{
	$parsyndicate['item_id']=$itemid;
	$parsyndicate['item_type']=$itemtype;
	$parsyndicate['syndication_channel']=$syndchannel;
	$arSyndication = getSyndication($itemid,$itemtype,$syndchannel);
	del_query("content_syndication",'id',$arSyndication['id']);
}

function getquantcastlabel($pagename) {
$qry="select tracking_name from pages_tracking PT,layout_pages LP  where PT.page_id=LP.id and LP.name='".$pagename."'";
$result=exec_query($qry,1);
$tracking_name = $result['tracking_name'];
return $tracking_name;
}
function embedTickerTalk()
{
	global $setTickerTalk,$D_R;
	$setTickerTalk = 1;
	include($D_R."/tickertalk/index.htm");
}

/* MS-Word Characters Replacement */
function mswordReplaceSpecialChars($body){

   	 $find[] = '“';  // left side double smart quote
	  $find[] = '”';  // right side double smart quote
	  $find[] = '‘';  // left side single smart quote
	  $find[] = '’';  // right side single smart quote
	  $find[] = '…';  // elipsis
	  $find[] = '—';  // em dash
	  $find[] = '–';  // en dash
	  $find[] = ' ';
	  $find[] = '&nbsp;';


	  $replace[] = '"';
	  $replace[] = '"';
	  $replace[] = "'";
	  $replace[] = "'";
	  $replace[] = "...";
	  $replace[] = "-";
	  $replace[] = "-";
	  $replace[] = " ";
	  $replace[] = " ";



	$body = str_replace($find, $replace, $body);
	$body = str_replace("&#128;", "&euro;", $body); // Euro symbol
	$body = str_replace("&#133;", "...", $body); // ellipses
	$body = str_replace("&#8216;", "'", $body); // left single quote
	$body = str_replace("&#145;", "'", $body); // left single quote
	$body = str_replace("&#8217;", "'", $body); // right single quote
	$body = str_replace("&#146;", "'", $body); // right single quote
	$body = str_replace("&#8220;", '"', $body); // left double quote
	$body = str_replace("&#147;", '"', $body); // left double quote
	$body = str_replace("&#8221;", '"', $body); // right double quote
	$body = str_replace("&#148;", '"', $body); // right double quote
	$body = str_replace("&#149;", ".", $body); // bullet
	$body = str_replace("&#8211;", "-", $body); // en dash
	$body = str_replace("&#150;", "-", $body); // en dash
	$body = str_replace("&#8212;", "-", $body); // em dash
	$body = str_replace("&#151;", "-", $body); // em dash
	$body = str_replace("&#169;", "&copy;", $body); // copyright mark
	$body = str_replace("&#174;", "&reg;", $body); // registration mark
	$body = str_replace("&rsquo;", "'", $body);
	$body = str_replace("&lsquo;", "'", $body);
	$body = str_replace("&mdash;", "-", $body);
	$body = str_replace('&rdquo;', '"', $body);
	$body = str_replace('&ldquo;', '"', $body);
	$body = str_replace('%u201D', '"', $body);
	$body = str_replace('%u201C', '"', $body);
	$body = str_replace('%u2018', "'", $body);
	$body = str_replace('%u2019', "'", $body);
	$body = str_replace('%u2032', "'", $body);
	$body = str_replace('%u2033', '"', $body);
	$body = str_replace('&shy;', '-', $body);

	$body = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",html_entity_decode ($body));
	$body = html_entity_decode($body);

	return $body;
}



function xml2array($url, $get_attributes = 1, $priority = 'tag')
{
    $contents = "";
    if (!function_exists('xml_parser_create'))
    {
        return array ();
    }
    $parser = xml_parser_create('');
    if (!($fp = @ fopen($url, 'rb')))
    {
        return array ();
    }
    while (!feof($fp))
    {
        $contents .= fread($fp, 8192);
    }
    fclose($fp);
    $contents=stripslashes(trim($contents));
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser,$contents , $xml_values);
    xml_parser_free($parser);
    if (!$xml_values)
        return; //Hmm...
    $xml_array = array ();
    $parents = array ();
    $opened_tags = array ();
    $arr = array ();
    $current = & $xml_array;
    $repeated_tag_index = array ();
    foreach ($xml_values as $data)
    {
        unset ($attributes, $value);
        extract($data);
        $result = array ();
        $attributes_data = array ();
        if (isset ($value))
        {
            if ($priority == 'tag')
                $result = $value;
            else
                $result['value'] = $value;
        }
        if (isset ($attributes) and $get_attributes)
        {
            foreach ($attributes as $attr => $val)
            {
                if ($priority == 'tag')
                    $attributes_data[$attr] = $val;
                else
                    $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
            }
        }
        if ($type == "open")
        {
            $parent[$level -1] = & $current;
            if (!is_array($current) or (!in_array($tag, array_keys($current))))
            {
                $current[$tag] = $result;
                if ($attributes_data)
                    $current[$tag . '_attr'] = $attributes_data;
                $repeated_tag_index[$tag . '_' . $level] = 1;
                $current = & $current[$tag];
            }
            else
            {
                if (isset ($current[$tag][0]))
                {
                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                    $repeated_tag_index[$tag . '_' . $level]++;
                }
                else
                {
                    $current[$tag] = array (
                        $current[$tag],
                        $result
                    );
                    $repeated_tag_index[$tag . '_' . $level] = 2;
                    if (isset ($current[$tag . '_attr']))
                    {
                        $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                        unset ($current[$tag . '_attr']);
                    }
                }
                $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                $current = & $current[$tag][$last_item_index];
            }
        }
        elseif ($type == "complete")
        {
            if (!isset ($current[$tag]))
            {
                $current[$tag] = $result;
                $repeated_tag_index[$tag . '_' . $level] = 1;
                if ($priority == 'tag' and $attributes_data)
                    $current[$tag . '_attr'] = $attributes_data;
            }
            else
            {
                if (isset ($current[$tag][0]) and is_array($current[$tag]))
                {
                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                    if ($priority == 'tag' and $get_attributes and $attributes_data)
                    {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag . '_' . $level]++;
                }
                else
                {
                    $current[$tag] = array (
                        $current[$tag],
                        $result
                    );
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $get_attributes)
                    {
                        if (isset ($current[$tag . '_attr']))
                        {
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset ($current[$tag . '_attr']);
                        }
                        if ($attributes_data)
                        {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                    }
                    $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                }
            }
        }
        elseif ($type == 'close')
        {
            $current = & $parent[$level -1];
        }
    }
    return ($xml_array);
}


# This function makes any text into a url frienly
# This script is created by wallpaperama.com
function clean_url($url)
{
	$text=strtolower($url);
	$code_entities_match = array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','*','+','~','`','=');
	$code_entities_replace = array('','-','','','','','','','','','','','','','','','','','','','','','','','');
	$url = str_replace($code_entities_match, $code_entities_replace, $url);
	return $url;
}

function detectBrowser(){
	$browser = '';
	$navigator_user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
	if (stristr($navigator_user_agent, "msie"))
	{
		$browser = 'msie';
	}
	elseif (stristr($navigator_user_agent, "gecko"))
	{
		$browser = 'mozilla';
	}
	elseif (stristr($navigator_user_agent, "mozilla/4"))
	{
		$browser = 'ns4';
	}
}

function autoLogin(){
	/********** CHECK THE USER NAME FROM COOKIE START *******************/
	if(!$_SESSION['LoggedIn']){
		if($_COOKIE['password'] && $_COOKIE['email'])
		{
			$user=new user();
			if(($_COOKIE['password']!='') && ($_COOKIE['email']!=''))
			{
				if($_SESSION['sessautologin']=='') // $_SESSION['sessautologin'] set to 1 in Logout function
				{
					$isLoggedIn=$user->login(trim($_COOKIE['email']),base64_decode(trim($_COOKIE['password'])),$_COOKIE['autologin']);
				}
			}
		}
	}
	/********** CHECK THE USER NAME FROM COOKIE END *******************/
}
function setReferer($from=NULL){
	if($from){
		$_SESSION['referer']=$from;
	}else{
		list($subdomain, $domain, $domaintype) = explode(".", $_SERVER['HTTP_REFERER']);
		if($domain!="minyanville")
			$_SESSION['referer']=$domain;
	}
	setAmeritradeAdsFree();
}

function setAmeritradeAdsFree(){
	global $_SESSION;
	$refererArray = array();
	$refererArray[0] = "ameritrade";
	$refererArray[1] = "etrade";
	if(in_array($_SESSION['referer'],$refererArray)){
		$_SESSION['AdsFree']='1';
	}
	if(strpos($_SERVER['HTTP_REFERER'],"www.etrade.wallst.com"))
	{
		$_SESSION['AdsFree']='1';
	}
}

function loadjavascriptheader($pageCSS,$pageJS,$pos='H',$noDefaultLoad=FALSE){
	global $js, $css,$D_R,$HTPFX,$HTHOST,$CDN_SERVER,$SSL_CDN_SERVER,$HTPFXSSL;
	include_once($D_R."/lib/config/_js_css_lib.php");
	?><!-- CSS Configuration Start--><?
	foreach($css as $styleSheet)
	{
		if(in_array($styleSheet['name'],$pageCSS) || (!$noDefaultLoad && $styleSheet['defaultLoad']==TRUE))
		{
			if($styleSheet['linkType']=="external"){
				if($_SERVER['HTTPS']=="on")
				{
					echo "<link rel='stylesheet' href='".$CDN_SERVER.$styleSheet['file']."' type='text/css' media='all' />\n";
				}
				else
				{
					echo "<link rel='stylesheet' href='".$CDN_SERVER.$styleSheet['file']."' type='text/css' media='all' />\n";
				}
			}
			else{
				if($styleSheet['name']=='fontStylesheet')
				{
					if($_SERVER['HTTPS']=="on")
					{
						echo "<link rel='stylesheet' href='".$CDN_SERVER.$styleSheet['file']."' type='text/css' media='all' />\n";
					}
					else 
					{
						echo "<link rel='stylesheet' href='".$CDN_SERVER.$styleSheet['file']."' type='text/css' media='all' />\n";
					}
				}else{
					if($_SERVER['HTTPS']=="on")
					{
						echo "<link rel='stylesheet' href='".$CDN_SERVER.$styleSheet['file']."' type='text/css' media='all' />\n";
					}
					else 
					{
						echo "<link rel='stylesheet' href='".$CDN_SERVER.$styleSheet['file']."' type='text/css' media='all' />\n";
					}
				}
			}
		}
	}
	?><!-- CSS Configuration End--><!-- JS Configuration Start--><?
	foreach($js as $javaScript)
	{
	if(in_array($javaScript['name'],$pageJS) || (!$noDefaultLoad && $javaScript['defaultLoad']==TRUE)){
	if($javaScript['linkType']=="external"){
	echo "<script src='".$javaScript['file']."' type='text/javascript' ></script>\n";
	}else{
	if($_SERVER['HTTPS']=="on")
	{
	echo "<script src='".$CDN_SERVER.$javaScript['file']."' type='text/javascript' ></script>\n";
	}
	else 
	{
	echo "<script src='".$CDN_SERVER.$javaScript['file']."' type='text/javascript' ></script>\n";
	}
	}
	}
	}
	?><!-- JS Configuration End--><?
} 



function showMostViewed() {

	global $HTPFX,$HTHOST;
        $objcache=new Cache();
        $objMostViewedArt =$objcache->getMostRead('1,18',5);
        if(!empty($objMostViewedArt)){
        foreach ($objMostViewedArt as $row) {
              if ($i != "0") {
                    	  echo "<div class='mostPopularRow'>
					  <div class='mostPopularNumber'>
					  	<span id='big-num'>".($i+1)."</span>
					  </div>
						<div id='whats-popular-text' >
							<a href='".$HTPFX.$HTHOST.$row['url']."'>".mswordReplaceSpecialChars($row['title'])."</a>
                    		<br /><span id='contrib-name'><a href='".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$row['author_id']."'>".mswordReplaceSpecialChars($row['author_name'])."</a></span></div></div>";

               }
               $i++;
       }
}

}


function showMostRead($itemType) {
$objcache=new Cache();
	$objMostViewedArt =$objcache->getMostRead($itemType,$limit=3);
	if(!empty($objMostViewedArt)){
      foreach ($objMostViewedArt as $row) {
              if ($i != "0") {
                    echo "<div class=\"most_readComment_le\"><a href='".$HTPFX.$HTHOST.$row['url']."' class=\"most_readCommentTitle\">".mswordReplaceSpecialChars($row['title'])."</a><div class='most_contributor_in_s'><a class='most_contributor_in_s' href='".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$row['author_id']."' >".$row['author_name']."</a></div></div>";
               }
               $i++;
       }
}
}


function showMostEmailed() {
$objcache=new Cache();
$arrMostEmailedArt=$objcache->getCacheMostEmailArticle();
	if(!empty($arrMostEmailedArt->arrMostEmailedArt)){
      foreach ($arrMostEmailedArt->arrMostEmailedArt as $row) {
             if ($i != "0") {
                     echo "<div style='clear:both;'><div style='float:left;'>".($i+1).".&nbsp;</div><div class='article_list'><a href='".$HTPFX.$HTHOST.getItemURL($row['item_type'],$row['id'])."'>".mswordReplaceSpecialChars($row['title'])."</a></div></div>";
               }
               $i++;
       }
}
}

function getMostRecent() {
	global $HTPFX,$HTHOST;
	$arrMostRecentArt = mostRecenetArticle();
	$str="";
	foreach ($arrMostRecentArt as $row) {
		if ($i != "0") {
			$str.="<div style='clear:both;'>
			<div style='float:left;'>
				<span id='big-num'>".($i+1)."</span>
			</div>
			<div id='whats-popular-text' ><a href='".$HTPFX.$HTHOST.getItemURL($row['item_type'],$row['id'])."'>".mswordReplaceSpecialChars($row['title'])."</a><br /><span id='contrib-name'><a href='".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$row['contribID']."'>".mswordReplaceSpecialChars($row['name'])."</a></span></div></div>";
		}
		$i++;
	}
	return $str;
}
function displaySmallFlike($url,$funtion_type =NULL){ // layout options = standard,button_count
$str = '<iframe src="http://www.facebook.com/plugins/like.php?href='.urlencode($url).'&amp;layout=button_count&amp;show_faces=false&amp;width=100&amp;action=like&amp;colorscheme=light&amp;height=20" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:20px;vertical-align:middle;" allowTransparency="true"></iframe>';
	if($funtion_type == 'return')
	{
		return $str;
	}
	else
	{
		echo $str;
	}
}
function displayFlike($url,$funtion_type =NULL){ // layout options = standard,button_count
$str = '<iframe src="http://www.facebook.com/plugins/like.php?href='.urlencode($url).'&amp;layout=standard&amp;show_faces=true&amp;width=450&amp;action=like&amp;colorscheme=light&amp;height=80" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:80px;vertical-align:middle;" allowTransparency="true"></iframe>';
	if($funtion_type == 'return')
	{
		return $str;
	}
	else
	{
		echo $str;
	}
}

function displayRetweet(){
?>
<script type="text/javascript">tweetmeme_service = 'bit.ly';</script>
<script
	type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script>
<?
}

function displayDigg(){
?>
<script type="text/javascript">
(function() {
var s = document.createElement('SCRIPT'), s1 = document.getElementsByTagName('SCRIPT')[0];
s.type = 'text/javascript';
s.async = true;
s.src = 'http://widgets.digg.com/buttons.js';
s1.parentNode.insertBefore(s, s1);
})();
</script>
<a
	class="DiggThisButton DiggCompact"></a>
<?
}

function showDigg($url,$title){
global $IMG_SERVER;
?>
<a target="_blank" rel="nofollow"
	href="http://digg.com/submit?url=<?=$url;?>&title=<?=$title;?>"><img
	style="border: none;" src="<?=$IMG_SERVER;?>/images/icons/digg.gif"
	alt="Post to Digg"></a>
<?
}

function showTSDigg($url,$title){
global $IMG_SERVER;
$imgUrl	='/images/icons/TS_digg.gif';
?>
<a target="_blank" rel="nofollow"
	href="http://digg.com/submit?url=<?=$url;?>&title=<?=$title;?>"><img
	style="border: none;" src="<?=$IMG_SERVER.$imgUrl;?>"
	alt="Post to Digg"></a>
<?
}

function showReddit($url,$title){
global $IMG_SERVER;
?>
<a target="_blank" rel="nofollow"
	href="http://www.reddit.com/submit?url=<?=$url;?>&title=<?=$title;?>"><img
	style="border: none;" src="<?=$IMG_SERVER;?>/images/icons/reddit.gif"
	alt="Post to Reddit"></a>
<?
}
function showTSReddit($url,$title){
global $IMG_SERVER;
$imgUrl	='/images/icons/TS_reddit.gif';
?>
<a target="_blank" rel="nofollow"
	href="http://www.reddit.com/submit?url=<?=$url;?>&title=<?=$title;?>"><img
	style="border: none;" src="<?=$IMG_SERVER.$imgUrl;?>"
	alt="Post to Reddit"></a>
<?
}
function showDelicious($url,$title){
global $IMG_SERVER;
?>
<a href="http://del.icio.us/post"
	onclick="window.open('http://del.icio.us/post?v=4&noui&jump=close&url=<?= urlencode($url); ?>&title=<?= urlencode($title); ?>', 'delicious','toolbar=no,width=700,height=400'); return false;"><img
	src="<?=$IMG_SERVER;?>/images/icons/delicious.gif" width="16"
	height="16" alt="" /></a>
<?
}
function showTSDelicious($url,$title){
global $IMG_SERVER;
$imgUrl	='/images/icons/TS_delicious.gif';
?>
<a href="http://del.icio.us/post"
	onclick="window.open('http://del.icio.us/post?v=4&noui&jump=close&url=<?= urlencode($url); ?>&title=<?= urlencode($title); ?>', 'delicious','toolbar=no,width=700,height=400'); return false;"><img
	src="<?=$IMG_SERVER.$imgUrl;?>" width="16" height="16" alt="" /></a>
<?
}

function showTwitter($url,$title){
global $D_R,$IMG_SERVER;
$twitterurl = urlencode($url);
?>
<img
	onclick="callBitlyApi('<?=$title?>','<?=$twitterurl?>');"
	src="<?=$IMG_SERVER?>/images/icons/twitter.gif"
	style="vertical-align: top" height="18px" width="21px"
	alt="Share on Twitter" />
<?
}
function showTSTwitter($url,$title){
global $D_R,$IMG_SERVER;
 $imgUrl	='/images/icons/TS_twitter.gif';
 $twitterurl = urlencode($url);
?>
<a><img onclick="callBitlyApi('<?=$title?>','<?=$twitterurl?>');"
	src="<?=$IMG_SERVER.$imgUrl?>" style="vertical-align: top"
	height="18px" width="21px" alt="Share on Twitter" /></a>
<?
}

function checkCookies()
{
	$val = "cookie save";
	setcookie("CHECK_COOKIE", $val);
	if($_COOKIE['CHECK_COOKIE']==$val)
	{
		return $_COOKIE['CHECK_COOKIE'];
	}
	else
	{
		return 0;
	}
}

function showFacebook($url,$title){
global $IMG_SERVER;
?>
<a
	href="http://www.facebook.com/sharer.php?u=<? echo $url ?>&t=<? echo $title ?>"
	target="_blank"><img
	src="<?=$IMG_SERVER?>/assets/dailyfeed/facebook_image.jpg"
	style="vertical-align: middle" alt="Share on Facebook" /></a>

<?
}
function showTSFacebook($url,$title){
global $IMG_SERVER;
$imgUrl	='/assets/dailyfeed/TS_facebook_image.jpg';
?>
	<!--<a href="http://www.facebook.com/sharer.php?u=<? echo $url ?>&t=<? echo $title ?>" target="_blank"><img src="<?=$IMG_SERVER?>/assets/dailyfeed/facebook_image.jpg" style="vertical-align:middle" alt="Share on Facebook"/></a>-->
<a
	href="http://www.facebook.com/sharer.php?u=<? echo $url ?>&t=<? echo $title ?>"
	target="_blank"><img src="<?=$IMG_SERVER.$imgUrl?>"
	style="vertical-align: middle" alt="Share on Facebook" /></a>

<?
}

function showYahooBuzz($url,$title,$summary=NULL){
global $IMG_SERVER;
$yahooUrl = $fullUrl."?camp=syndication&medium=portals&from=yahoobuzz";
?>
<script type="text/javascript">
yahooBuzzArticleHeadline = "<?=$title;?>";
yahooBuzzArticleId = "<?=urldecode($HTPFX.$HTHOST.$url)?>";
<? if($summary) {?>
yahooBuzzArticleSummary = "<?=str_replace('"','&quot;',$summary);?>";
<? } ?>
</script>
<script
	type="text/javascript" src="http://d.yimg.com/ds/badge2.js"
	badgetype="logo"></script>
<?
}



function setVisitorPageCount(){
	global $_SESSION;
	if(!isset($_SESSION['visitorPageCount'])){
		$_SESSION['visitorPageCount']=1;
	} else{
		$_SESSION['visitorPageCount']=$_SESSION['visitorPageCount']+1;
	}
}


function comscoreBeacon(){
global $HTPFX;
?>
<!-- Begin comScore Tag -->
<script>document.write(unescape("%3Cscript src='" + (document.location.protocol == "https:" ? "https://sb" : "http://b") + ".scorecardresearch.com/beacon.js' %3E%3C/script%3E"));</script>
<script>COMSCORE.beacon({c1:2,c2:7716423,c3:"",c4:"",c5:"",c6:"",c15:""});</script>
<noscript><img
	src="<?=$HTPFX;?>b.scorecardresearch.com/p?c1=2&c2=7716423&c3=&c4=&c5=&c6=&c15=&cj=1" /></noscript>
<!-- End comScore Tag -->
<?
}

function replaceArticleAds($body){
global $articleAd;
  $pattern=array();

	foreach($articleAd as $key=>$value){
	    $patterns[]="/{".$key."\}/";
	}

	$body=preg_replace($patterns," ",$body);
	$body = replaceVideoCode($body,'rss');
	return $body;
}

function replaceVideoCode($body,$from='rss')
{
	global $D_R;
	include_once $D_R.'/lib/videos/_data_lib.php';
 	$obPlayer = new player();
	$body = $obPlayer->replaceVideoCode($body,'rss');
    return $body;
}

function setAutoPlayCookie()
{
	$expiry_time = time()+3600*24*7;
	if(!isset($_COOKIE['autoplay']))
	{
		if(isset($_SESSION['SID']) && $_SESSION['activeProducts'] != "")
		{
			setcookie("autoplay","yes",$expiry_time);
		}
		else
		{
			$intRand = rand(1,100);
			if($intRand <= 5)
			{
				mcookie("autoplay","no",$expiry_time);
			}
			else
			{
				mcookie("autoplay","yes",$expiry_time);
			}
		}
	}
	else
	{
		if(isset($_SESSION['SID']) && $_SESSION['activeProducts'] != "")
		{
			setcookie("autoplay","yes",$expiry_time);
		}
	}

}

function showSponsoredLinks(){
	 if(!($_SESSION['AdsFree'])) {
	  $str.='<div class="article-right-module">';
	  $str.='<script language="javascript">CM8ShowAd("Sponsored_300x250");</script>';
	  $str.='</div>';
	  echo $str;
	  }
 }
function createXML( $data, $root ) {

	$keys =array_keys($data);

	$str              =  "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$str             .=  '
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">';
	$str             .= "
<$root>";
	//$this->elementTag = 'item';
	$str             .= arraytoxml( $data , $root, $keys );

	$str             .= "
</$root>";
	$str             .= "
</rss>";

	return $str;
}

function arraytoxml( $data , $root_title ,$keys="",$append = true ) {
	//print_r($keys);
	foreach( $data as $k => $v )
	{

		if ( is_numeric($k) )  $str .= "
<item>";

			if( is_array( $v ) ) {

				//if ( trim( $k )=='answers' ) {
				//	$str            .= "<answers>";
				//	$this->elementTag='ansItem';
				//}

				//if (!is_numeric($k) && in_array($k,$keys)) {
				//	$this->elementTag="$k";
				//}
				$str .= arraytoxml($v , $root_title  ,$keys, $append);

				//if ( trim($k) == 'answers' )  {
				//	$str            .= "</answers>";
				//	$this->elementTag='item';
				//}
				//if (!is_numeric($k) && in_array($k,$keys)) {
				//	$str   .= "</$k>";
				//	$this->elementTag='item';
				//}
			}
			else
			{
				$pos = strpos( $v, '&' );
				/*
				if( $pos === false )

					$str .= "
<$k>$v</$k>";

				else

					$str .= "
<$k><![CDATA[$v]]></$k>";
				*/
				$str .= "
<$k><![CDATA[$v]]></$k>";
			}
				if ( is_numeric( $k ) )  $str .= "
</item>";
	}

	return $str;
}


	function xml_attribute($object, $attribute)
		{
		    if(isset($object[$attribute]))
			return (string) $object[$attribute];
		}

	function showDailyRecap(){
		global $IMG_SERVER;
		if(empty($_SESSION['email'])){
			$emailVal='enter your email';
		}else{
			$emailVal=$_SESSION['email'];
			$userid = $_SESSION['SID'];
		}
	?>
	<div id="wrap" class="nL-article-right-module">
		<div id="nL-sU-social">
			<ul class="social-options">
				<li class="cta-head">
						<h3 class="new-head-right">Stay Connected</h3>
					</li>
				<li class="icon">
					<a href="https://twitter.com/intent/follow?original_referer=&region=follow_link&screen_name=minyanville&tw_p=followbutton&variant=2.0" target="_blank"><img class="fade" src="<?=$IMG_SERVER;?>/images/newsletter/twtrBtn.png" alt="Twitter" height="" width="" /></a>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script> 
				</li>

				<li class="icon">
					<a href="http://www.facebook.com/MinyanvilleMedia" target="_blank"><img class="fade" src="<?=$IMG_SERVER;?>/images/newsletter/fcbkBtn.png" alt="Facebook" height="" width="" /></a>
				</li>

				<li class="icon">
					<a href="https://plus.google.com/+minyanville" target="_blank"><img class="fade" src="<?=$IMG_SERVER;?>/images/newsletter/googBtn.png" alt="Google +" height="" width="" /></a>
				</li>

				<li class="icon noMargin-Rt">
					<a href="http://www.minyanville.com/service/rss.htm" target="_blank"><img class="fade" src="<?=$IMG_SERVER;?>/images/newsletter/rssBtn.png" alt="RSS Feed" height="" width="" /></a>
				</li>
			</ul>
				<div class="nL-signUp-div">
					<div class="email-text-div" id="sU-center"><input class="email-text" id="sU-email" type="text" onclick="this.value='';" onfocus="this.select()" onblur="this.value=! this.value?'<?=$emailVal;?>':this.value;" value="<?=$emailVal;?>" onkeyup="dailyRecapEnterKeyChk(event,'sU-email','dailyrecap_subscriber_id','dailydigest');"/>
					<input type="hidden"  name="dailyrecap_subscriber_id" id="dailyrecap_subscriber_id" value="<?php echo $userid; ?>"></div>
					<div class="signUp-submit-div nL-btn"><input type="submit" value="Newsletter&#x00A;Sign Up" class="btn-submit" onclick="dailyRecapNewsLetter('sU-email','dailyrecap_subscriber_id','dailydigest');"></div>
			</div><!-- / end .nL-signUp-div -->
		</div>
	</div>
	<?
	}

	function displayRadioInArticle($audioFileFullPath,$layoutType){
		global $IMG_SERVER,$HTPFX,$VIDEO_SERVER,$CDN_SERVER,$HTHOST;
		if($layoutType=="radio"){
			$imageAudio = $IMG_SERVER.'/images/radio/MMFN-Logo.png';
		}else{
			$imageAudio = '';
		}
		//$swfPath = $CDN_SERVER."/mvtv/flowplayer/flowplayer-3.2.15.swf";
		$swfPath = "http://releases.flowplayer.org/swf/flowplayer-3.2.15.swf";
		$str ='<script src="'.$CDN_SERVER.'/mvtv/flowplayer/flowplayer-3.2.0.min.js"></script>';
		$str .='<div class="player" style="display:block;min-width:580px;height:325px;margin:10px auto;" id="player"></div>';

		$str .='<script>
			flowplayer("player", "'.$swfPath.'", {
				clip: {
					url: "'.$audioFileFullPath.'",
					coverImage: "'.$imageAudio.'",
					autoPlay: false
				},
				plugins:  {
					controls: {
						backgroundColor:"#002200",
						height: 30,
						fullscreen: true,
						autoHide: false
					}
				},
				canvas: {
					backgroundImage: "url('.$imageAudio.')",
					width:600,
					height:325
				},
			});
		</script>';
		return $str;
	}
	function CheckMobile($type,$itemID)
	{
		setCookieMVmobile();
		$poscellagility = strpos($_SERVER['HTTP_REFERER'],"m.minyanville.com");
		if(isset($_COOKIE['mvmobile']) || isset($_SESSION['ses_mvmobile']) || $poscellagility != 0)
		{
		// Do not redirect to mobile site
		}
		else
		{
			$mobile_detect = detect_mobile('');
			if($mobile_detect)
			{
			    mobileRedirect($type,$itemID);
			}
		}
	}
	function detect_mobile($ipad)
		{
		    if(preg_match('/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|'.$ipad.'ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|panasonic|philips|phone|playbook|sagem|sharp|sie-|silk|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i', $_SERVER['HTTP_USER_AGENT']))
		    {
		        return true;
		    }
		    else
		        return false;
	}

	function mobileRedirect($itemType,$itemID=NULL){
		switch($itemType){
			case '1':
			case '18':
				if(!empty($itemID))
					header("Location: http://m.minyanville.com/section/MvPremiumArticle?guid=".$itemID);
				else
					header("Location: http://m.minyanville.com/section/mvpremium");
			break;
			default:
				if(!empty($itemID))
					header("Location: http://m.minyanville.com/section/MvNewsArticle?guid=".$itemID);
				else
					header("Location: http://m.minyanville.com/section/mv-news");
			break;
		}

	}

	function generateNasdaqRss($recentArticlerow){
		global $HTPFX, $HTHOST;
		$articleFullURL = $HTPFX.$HTHOST.makeArticleslink($recentArticlerow["id"]);
		if($recentArticlerow['is_audio']=='1'){
			$audio = $objArticle->getArticleAudio($recentArticlerow['id']);
			foreach($audio as $aud){
				if($aud['item_key']=='audiofile'){
					$audioFile = $aud['item_value'];
				}elseif($aud['item_key']=='radiofile'){
					$radioFile = $aud['item_value'];
				}
			}
		}else{
			$audioFile="";
			$radioFile="";
		}

		$articleBody = $recentArticlerow['body'];
		$articleBody = substr(strip_tags($articleBody),0,300);
		$articleBody = htmlentities(mswordReplaceSpecialChars($articleBody),ENT_QUOTES);
		$articleBody = replaceArticleAds($articleBody);
		$articleBody = str_replace('<b','<strong',$articleBody);
		$articleBody = str_replace('</b>','</strong>',$articleBody);
		$articleBody = str_replace('<i>','<em>',$articleBody);
		$articleBody = str_replace('</i>','</em>',$articleBody);
		$articleBody = str_replace('<div','<p',$articleBody);
		$articleBody = str_replace('</div>','</p>',$articleBody);
		if(substr_count($articleBody,"{FLIKE}") > 0){
			$articleBody = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $articleBody);
		}
		if(substr_count($articleBody,"{AUDIO}") > 0)
		{
			if($audioFile!='')
			{
				$articleBody = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $articleBody);
			}
			else
			{
				$articleBody = str_replace("{AUDIO}"," ", $articleBody);
			}
		}
		if(substr_count($articleBody,"{RADIO}") > 0)
		{
			if($radioFile!='')
			{
				$articleBody = str_replace("{RADIO}","<div>".displayRadioInArticle($radioFile,$layoutType)."</div>", $articleBody);
			}
			else
			{
				$articleBody = str_replace("{RADIO}"," ", $articleBody);
			}
		}

		$rssNasdaq ='<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:nasdaq="http://nasdaq.com/reference/feeds/1.0/">
<channel>
<title>Minyanville.com - All Articles</title>
<description>The Trusted Choice for the Wall Street Voice</description>
<link>http://www.minyanville.com</link>
<copyright>2007 Minyanville Publishing and Multimedia, LLC. All Rights Reserved</copyright>
<ttl>1</ttl>
<item>
	<title>'.htmlentities(mswordReplaceSpecialChars(strip_tags($recentArticlerow["title"]))).'</title>
	<pubDate>'.date("D, d M Y H:i:s",strtotime($recentArticlerow['publishDate'])).'</pubDate>
	<guid>'.$recentArticlerow["id"].'</guid>
	<author>'.$recentArticlerow["author"].'</author>
	<description><![CDATA['.$description.']]></description>
	<link>'.$articleFullURL.'</link>
</item>
</channel></rss>';
		$nasdaqFeed=gmdate("Ymd",strtotime($recentArticlerow['publishDate'])).$recentArticlerow['id'];
		$feedName='../assets/yahoofeed/minyanville/'.$nasdaqFeed.'rss';
		$feedFile=fopen($feedName,"w+");
		fwrite($feedFile,$strfeed);
		fclose($feedFile);
		chmod($feedName, 0777);
	}

	function displayCurrentDate() {
		$dateInfo = getDate();
		$todaysDate = $dateInfo['weekday'] . " " . $dateInfo['month'] . " " . $dateInfo['mday'] . ", " . $dateInfo['year'];
		return $todaysDate;
	}
	
	function getItemURL($item_type,$item_id)
	{
		global $D_R;
		include_once("$D_R/admin/lib/_dailyfeed_data_lib.php");
		$objDailyfeed = new Dailyfeed();
		global $HTPFX,$HTHOST;
		if($item_type == 1)
		{
			$stQuery = "SELECT id,keyword,blurb FROM articles WHERE id ='$item_id'";
			$arResult = exec_query($stQuery,1);
			//  function defined in web/lib/layout_functions.php
			return makeArticleslink($arResult['id'],$arResult['keyword'],$arResult['blurb']);
		}
		if($item_type == 18)
		{
			$urltitle=$objDailyfeed->getDailyFeedUrl($item_id);
			return $urltitle;
		}
		else
		{
			$stQuery = "SELECT url FROM ex_item_meta WHERE item_type= '$item_type' AND item_id ='$item_id'";
			$arResult = exec_query($stQuery,1);
			return $arResult['url'];
		}
	}
	function getlayoutmenu(){
		$sql    =   "SELECT COUNT(l2.id) as cnt,l1.id,l1.title,l1.parent_id,l1.level,l1.menuorder,l1.active,l1.page_id
			     from layout_menu as l1 , layout_menu as l2  WHERE l1.id = l2.parent_id AND l1.parent_id='0' and
					 l1.navigation_type='H' Group By l1.id
				     ORDER BY l1.menuorder ASC";
		$result  =  exec_query($sql);
		if($result)
		{
			return $result;
		}
		else
		{
			return NULL;
		}
	}
	
	function getModules() {
		$listmodules = exec_query("SELECT id, name FROM layout_modules");
		foreach($listmodules as $row){
		$modules[$row['id']] = $row['name'];
		}
		
		return $modules;
	}
	
	function displayDate() {
		$dateInfo = getDate();
		$todaysDate = $dateInfo['weekday'] . " " . $dateInfo['month'] . " " . $dateInfo['mday'] . ", " . $dateInfo['year'];
		return $todaysDate;
	}
		

  ?>
