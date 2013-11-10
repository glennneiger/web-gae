<?
function handle_ssl(){
	global $is_dev,$is_ssl,$REQUEST_URI,$PHP_SELF,$SECURE_PATHS,$_GET,$_POST,$HTHOST,$_SERVER,$S4,$S5;
	if($_SERVER['SERVER_ADDR']==$S4 || $_SERVER['SERVER_ADDR']==$S5)
		return;
	$ri=&$REQUEST_URI;
	$hthost=$HTHOST;

	$dest="";
	$path_found=0;
	if(is_array($SECURE_PATHS)){
	foreach($SECURE_PATHS as $p){
		if($t=stristr($PHP_SELF,$p)){
			$path_found=1;
			break;
		}
	}
	}
	if(!$path_found){//shouldn't be ssl
		if($is_ssl){//redirect to non-ssl
			$dest="http://$hthost".($is_dev?":8000":"").$ri;
		}else{//not ssl and doesn't need to be. leave
			return;
		}
	}elseif(!$is_ssl){//path isn't ssl but should be
		$dest="https://$hthost".($is_dev?":8443":"").$ri;
	}
	if($dest){//do redirection and exit
		header("Location: $dest",TRUE,301);
		exit();
	}
}
function cleanHtml($val,$key)
{
	$purifier = new HTMLPurifier($config);
	$clean_html = $purifier->purify($val);
	$data[$key] = $val;
}
function recursive_array_replace($purifier,$array)
{
	global $config;
	if (!is_array($array))
	{
		$purifier = new HTMLPurifier($config);
		$clean_html = $purifier->purify($array);
		$clean_html = $array;
		return $clean_html;
	}
	$newArray = array();
	foreach ($array as $key => $value)
	{
		$newArray[$key] = $value;
	}
	return $newArray;
}

function get_characters($validonly=1){
	$ret=exec_query("SELECT * FROM characters ".($validonly?"WHERE display!='0' ":"")."order by ordr");
	foreach($ret as $i=>$row){
		if(!is_file($D_R.$row[bio_asset])){
			unset($row[$i][bio_asset]);
		}
	}
	return $ret;
}

function get_character_images($charid){
	return exec_query("SELECT * FROM character_images WHERE character_id='$charid'");
}
function get_character_for_article($character_img_id){//pulls data based on a character_img_id
	$qry="SELECT ci.*, c.*
		  FROM character_images ci, characters c
		  WHERE ci.id='$character_img_id'
		  AND ci.character_id=c.id
		  GROUP BY ci.id";
	return exec_query($qry,1);
}

function get_character($charid){
	if($charid)return array();
	return exec_query("SELECT * FROM characters WHERE id='$charid'",1);
}

function get_dictionary(){
	return exec_query("SELECT * FROM dictionary ORDER BY name");
}
function get_dictionary_item($dict_id){
	if($dict_id)return array();
	return exec_query("SELECT * FROM dictionary WHERE id='$dict_id'",1);
}

function admin_error($msg="You're not allowed to use this page."){
	location("/admin/error.php?error=".urlencode($msg));
	exit;
}

function get_contributor($contrib_id){
	$qry="SELECT * FROM contributors WHERE id='$contrib_id'";
	return exec_query($qry,1);
}
function get_contributor_id_byname($name){
	$qry="SELECT * FROM contributors WHERE LOWER(name)='".trim(strtolower($name))."'";
	return exec_query($qry,1,"id");
}


function get_all_contributors(){
	$qry="SELECT * FROM contributors ORDER BY id";
	return exec_query($qry);
}

function get_active_contributors(){
	$qry="SELECT * FROM contributors WHERE suspended='0' ORDER BY NAME asc";
	$res=exec_query($qry);
	return $res;
}

function get_bio_contributors(){
	$qry="SELECT * FROM contributors WHERE suspended='0' and has_bio='1' ORDER BY NAME asc";
	$res=exec_query($qry);
	return $res;
}

function get_buzzactive_contributors(){
	global $ARTICLE_TABLE;
	$qry="SELECT C.name, C.id  AS cid
FROM contributor_groups_mapping CGM,contributor_groups CG ,
contributors C
WHERE C.id=CGM.contributor_id AND CGM.group_id=CG.id AND CG.group_name='Buzz & Banter' AND C.suspended='0' ORDER BY C.name";
	$res=exec_query($qry);
	return $res;
}

function get_contributor_byuserid($userid){
	$qry="SELECT id FROM contributors WHERE user_id='$userid'";
	return get_contributor(exec_query($qry,1,"id"));
}

/*-------------------------------reg-----------------------------*/
function email_taken($email){
	//returns whether a new account can be registered using $email
	//email isn't reserved on disabled accounts if
	//the the date_disabled is *older than* $90days
	global $RBT_STALE_LENGTH;
	$email=lc(trim($email));
	$stale=time() - $RBT_STALE_LENGTH;
	$qry="SELECT id FROM subscription
					WHERE lower(email)='$email'
					AND account_status='enabled'
					OR
					(	LOWER(email)='$email'
						AND account_status='disabled'
					    AND	date_disabled!='0'
						AND UNIX_TIMESTAMP(FROM_UNIXTIME(date_disabled)) > $stale
					)
					LIMIT 1";

	$ret=num_rows($qry);
	debug("email_taken($email)  $ret");
	return $ret;
}

	###change_taken-if user in the system
	###tries to change to another email also in the system
function email_change_taken($id,$email){//1 if dupe 0 if not
	if(!$id||!$email)return 0;
	$email=lc(trim($email));
	$idemail=exec_query("SELECT lower(email)e FROM subscription where id='$id'",1,"e");
	if($email==$idemail)
		return 0;//no change
	return email_taken($email);//it's different. is it taken?
}
function is_accttype_changed($id,$type){//returns 1 if chnaged 0 if no chnage
	if(!$id)return 0;
	$type=lc($type);
	$idtype=exec_query("SELECT lower(type)t FROM subscription WHERE id='$id'",1,"t");
	if($type==$idtype)
		return 0;//no change
	return 1;
}
function is_billing_changed($old_sub, $new_sub){
//pass old subscription row and row w/ possible changes
	$billfields=qw("fname lname address address2 city state zip cc_num cc_type cc_cvv2 cc_expire");
	foreach($billfields as $field){
		if($old_sub[$field]!=$new_sub[$field])
			return 1;
	}
	return 0;
}
function getNextBillDate($timestamp){
	//rounds date to 15th of the curr month or 1st of the next
	$timestamp=midnight($timestamp);
	$srchour=date("G",$timestamp);
	$srcmin=$srcsec=0;
	$srcyear=date("Y",$timestamp);
	$srcmonth=date("n",$timestamp);
	$targmonth=$srcmonth;
	$srcday=date("j",$timestamp);
	if($srcday==1){
		$targday=1;
		$targmonth=$srcmonth;
	}elseif($srcday<=15){//round day up
		$targday=15;
	}else{//1st of next month
		$targday=1;
		$targmonth+=1;
	}
	return time($srchour,$srcmin,$srcsec,$targmonth,$targday,$srcyear);
}
/*-------------------------------/end reg-----------------------------*/

function loginBox($refer=""){
	global $AUTH_URL,$_COOKIE,$BADLOGIN,$_SESSION,$refer,$USER;
		if($_COOKIE[autologin]&&$_COOKIE[EMAIL]){
			list($email,$password)=array($_COOKIE[EMAIL],$_COOKIE[PASSWORD]);
		}
	?>
	<table cellpadding=0 cellspacing=0 border=0>
	<form method="post" action="<?$D_R?>/auth-2.htm" name="signinform">
	<?if($refer){input_hidden("refer",$refer);}?>
	<TR>
	<TD colspan=3>
	<?if($BADLOGIN){?>
		<div class=error>Invalid Member ID/Password. If you don't have an account already please register.</div>
	<?}?>
	Please enter your Member ID:<br>

	<?input_text("email")?><br><?=spacer(1,5)?><br>
	Please enter your password:<br>
	<?input_pass("password")?><br>&nbsp;</TD>
	</TR>
	<TR valign=top>
	<TD><input type="submit" class=inbtn value="Log In"><?=spacer(5)?></TD>
	<!-- <TD><input type="button" class=inbtn value="Log In" onclick="validateSignin()"><?=spacer(5)?></TD> -->
	<TD><input type="checkbox" name="setcookie"<?=($_COOKIE[autologin]?" checked":"")?> id="autologinbox"><?=spacer(5)?></td>
	<TD><label for=autologinbox>Remember my Member ID and password on this computer.</label><br>
	<a href="/register/forgotpass.htm">Forgot your password?</a>
	</TD>
	</TR>
	</form>
	</table>
<?}

function spamJournalToUsers($article_id){
	//this just posts to the banter app now. much much lighter.
	global $HTPFX,$HTHOST;
	$objCache = new Cache();
	$objArticle = $objCache->getArticleCache($article_id);
	if(is_object($objArticle)){
		//buzz and banter alert
		$msg='<b>New MV article at '.date("g:i a",strtotime($objArticle->date)).
	     '<br /> <a href="javascript:void(0);"' .
	     ' onclick="launchPage(\'' . $HTPFX.$HTHOST.makeArticleslink($article_id). '\',\'article'. $article_id . '\',0)">' .
		mswordReplaceSpecialChars($objArticle->title) . '</a><br /> By ' . $objArticle->author . '</b><br/><br/>'.mswordReplaceSpecialChars($objArticle->character_text);
	 alertBanter($msg,1,$objArticle->author,$article_id,$objArticle->title,$objArticle->authorid);
	}
}

function alertBanter($msg="",$showInChat=0,$contributor=null,$article_id=null,$title,$contrib_id){
	debug("alertBanter($msg,$showInChat)");
	global $BANTER_ALERT_FILE;
	$objAction= new Action();
	write_file($BANTER_ALERT_FILE,$msg);
	if($showInChat){
		$ins=array(
			body=>addslashes(strip($msg)),
			is_live=>1,
			"date"=>mysqlNow(),
			show_in_app=>1,
			show_on_web=>0,
			approved=>1,
			author=>$contributor,
			position=>$article_id,
			login=>"(automated)",
			title=>addslashes(strip($title)),
			contrib_id=>$contrib_id);
		$buzzId=insert_query("buzzbanter",$ins);
		$buzzIns=$ins;
		$buzzIns['id']=$buzzId;
		insert_query("buzzbanter_today",$buzzIns);
		writeLatestBanterPostJSON();
		$objAction->trigger('buzzDataPublish',$buzzId);
	}
}

function writeLatestBanterPostJSON() {
	global $BANTER_LATEST_POST,$BANTER_LATEST_S4, $BANTER_LATEST_S5, $HTPFX,$HTADMINHOST;
	//write_file($BANTER_LATEST_POST, time());
	$writeAdmin= file_get_contents($HTPFX.$HTADMINHOST."/admin/writeBanterAlert.php?timestamp=".time());
	return $writeAdmin;
}


function bulk_mailer($listfile,$msgfile,$from="Minyanville <nobody@minyanville.com>"){
//doens't work with sendmail 8.1.2
	global $HTTP_HOST, $DATE_STR, $SITEHOME;
	$bulk_mailer=dirname(__FILE__)."/bulk_mailer2";
	$bulk_mailer="$bulk_mailer -domain 'www.minyanville.com'";
	$cmd="$bulk_mailer \"$from\" $listfile < $msgfile  >/dev/null 2>&1 &";
	echo "Sending mail...<br>";
	system($cmd,$res);
	echo "Done sending.<br>";
}
function softtrial_bulk_mailer($listfile,$msgfile,$from="Minyanville <nobody@minyanville.com>"){
//doens't work with sendmail 8.1.2
	global $HTTP_HOST, $DATE_STR, $SITEHOME;
	$bulk_mailer=dirname(__FILE__)."/bulk_mailer2";
	$bulk_mailer="$bulk_mailer -domain 'www.minyanville.com'";
	$cmd="$bulk_mailer \"$from\" $listfile < $msgfile  >/dev/null 2>&1 &";
	//echo "Sending mail...<br>";
	system($cmd,$res);
	//echo "Done sending.<br>";
}

function topButton(){
	?><div class=buttons><?mOver("topup.gif","topover.gif","#top")?></div><?
}

function get_skyscraper(){
	global $PHP_SELF;
	$PS=trim(lc($PHP_SELF));
	if(basename($PS)=="index.htm"){
		$PS=dirname($PS)."/";
	}
	if($PS=="//")$PS="/";
	$qry="SELECT a.tag FROM adverts a, pages p
		 WHERE p.url='$PS'
		 AND p.advert_id=a.id
		 AND a.type='skyscraper'";
	$res=exec_query($qry,1,"tag");

	if(!$res)return spacer(121,500);
	return $res;
}

function get_topbanner(){
	global $PHP_SELF,$NOBANNERFOUND;
	$PS=trim(lc($PHP_SELF));
	if(basename($PS)=="index.htm"){
		$PS=dirname($PS)."/";
	}
	if($PS=="//")$PS="/";
	$qry="SELECT a.tag FROM adverts a, pages p
		 WHERE p.url='$PS'
		 AND p.advert_id=a.id
		 AND a.type='banner'";
	$res=exec_query($qry,1,"tag");
	if(!$res){
		$NOBANNERFOUND=1;
		return spacer(1);
	}
	return $res;
}

function get_contentbanner(){
	global $PHP_SELF;
	$PS=trim(lc($PHP_SELF));
	if(basename($PS)=="index.htm"){
		$PS=dirname($PS)."/";
	}
	if($PS=="//")$PS="/";
	$qry="SELECT a.tag FROM adverts a, pages p
		 WHERE p.url='$PS'
		 AND p.advert_id=a.id
		 AND a.type='content'";
	$res=exec_query($qry,1,"tag");
	if(!$res){
		return spacer(1);
	}
	return $res;
}

function printpage(){
	global $PHPSESSID,$PHP_SELF;
?><a href="<?=$pfx.$PHP_SELF.qsa(array(mvpopup=>1,PHPSESSID=>$PHPSESSID))?>" target="_blank" class=journalprintthis>print this page</a><?
}

function mv_cache(){
	global $REQUEST_URI;
	$docache=0;
	$caches=array(
		"/ourtown/.*",
		"/library/\$",//only homepage
		"/library/foundation\.htm",
		"/library/dictionary\.htm",
		"/shops/.*",
		"/gazette/bios\.htm",
		"/gazette/journal/\?id=.*",
		"/gazette/newsviews/\?id=.*",
		"/company/.*"
	);
	foreach($caches as $page){
		if(preg_match("|$page|",$REQUEST_URI)){
			cache_client();
			return;
		}
	}
}
function valid_key($key1,$key2){
	if(!$key1 || !$key2)
		return 0;
	$qry="SELECT id FROM subscription_keys
		  WHERE key1='$key1'
		  AND key2='$key2'
		  AND user_id='0'";
	return num_rows($qry);
}
function taken_key($key1,$key2){
	if(!$key1 || !$key2)
		return 0;
	$qry="select id FROM subscription_keys WHERE key1='$key1' AND key2='$key2' AND user_id!='0'";
	return num_rows($qry);
}

function convertUserType($type){//used for url map: /register/$type/
	if(in_array(lc($type),qw("12mo 1mo 6mo prem newyear")))
		return "new";
	return lc($type);
}

function getAcctStatus($userid){
	if(!$userid)return 0;
	$qry="select account_status a FROM subscription WHERE id='$userid'";
	$res=exec_query($qry,1,"a");
	if(!$res)
		return 0;
	return $res;
}

function errormsg($msg=""){
	global $error;
	if(!$msg && $error)$msg=$error;
	if(!$error)return "";
	return '<div class="error">'.strip($error).'</div>';
}

function wgetArticle($id){
	global $ARTICLE_TABLE;
	//what a pain. can't believe we did this  crap. i knew it at the time too
	global $HTTP_HOST,$is_dev,$SITEHOME,$TODD_CONTRIB_ID;
	$contrib_id=exec_query("SELECT contrib_id i FROM $ARTICLE_TABLE WHERE id='$id'",1,"i");
	$archname=($is_dev?"dev":"web");
	$archivetmpl="$SITEHOME/archive/$archname/cdtemplate";
	$baseurls=array(
		dj=>"http://$HTTP_HOST/gazette/journal/?ARCHIVER=fishypop()&id=",
		nv=>"http://$HTTP_HOST/gazette/newsviews/?ARCHIVER=fishypop()&id="
	);
	$sourcedirs=array(
		dj=>"$archivetmpl/journal",
		nv=>"$archivetmpl/newsviews"
	);
	$wget="wget --http-user=ntonic --http-pass=adminuser";
	$key=($contrib_id==$TODD_CONTRIB_ID?"dj":"nv");
	$srcfile="${sourcedirs[$key]}/${id}.html";
	`$wget "${baseurls[$key]}$id" -O "$srcfile"`;
}

function createMVGiftCode($type="1mo"){
	$chars=qw("boo daisy hoofy sammy snapper");
	$key1=$type.uc(substr(base64_encode(mrand()),0,6));
	$key1=preg_replace("/\W/","",$key1);
	srand(getmicrotime());
	$key2=$chars[ rand(0,count($chars)-1) ];
	return array($key1, $key2);
}
function generateNewKeyCodes($length,$type="1mo"){
//very important that the user saves this data
//if they don't there is a dummy record in the database
	$codes=array();
	$length=intval($length);
	if(!$length)return array();
	while(count($codes)<$length){
		list($code,$pass)=createMVGiftCode($type);
		$codes[]="$code:$pass";
		$codes=array_unique($codes);
	}
	foreach($codes as $k=>$v){
		list($key1,$key2)=explode(":",$v);
		$codes[$k]=array(key1=>$key1,key2=>$key2,reserved=>1);
		insert_query("subscription_keys",$codes[$k]);
	}
	return $codes;
}

function getAreaPerms($permtypes=array()){
//returns category ids of areas perm types are allowed to edit
//Add more categories for articles.
if(!count($permtypes))return array();
	$perms=array(
		gazette=>qw("newsviews tradingradar univ_trading univ_derivatives
			univ_fixedincome univ_tech univ_fund univ_currencies univ_lounge newsviews_finance newsviews_technicals newsviews_economy newsviews_derivatives newsviews_fundamentals point roundup tradingideas kids_family"),
		schoolhouse=>qw("schoolhouse")
	);
	$retperms=array();
	foreach($permtypes as $type){
		if($perms[$type]){

			$retperms=array_merge($retperms,$perms[$type]);
		}
	}

	if(!count($retperms))return array();
	$qry="SELECT id FROM article_categories WHERE visible='1' and find_in_set(name,'".implode(",",$retperms)."')";
	return exec_query($qry,0,"id");
}


function pushToImgServer($localPath){
	debug("pushToImgServer($localPath)");
	//local path SHOULD NOT include document_root
	global $D_R, $IMG_FTP_PARAMS;
	$p=$IMG_FTP_PARAMS;
	$rpath=$p[path] . dirname($localPath);
	ftpPut("$D_R$localPath", $p[user], $p[pass], $p[host], $rpath);
}

function delFromImgServer($localPath){
	//NO docroot
	global $D_R, $IMG_FTP_PARAMS;
	$p=$IMG_FTP_PARAMS;
	$rpath=$p[path].$localPath;
	ftpdel($p[user],$p[pass],$p[host],$rpath);
}

function getNextBillDateByEmail($email){
	$email=lc($email);
	$qry="SELECT date_nextbill FROM subscription WHERE LOWER(email)='$email'";
	$date=exec_query($qry,1,"date_nextbill");
	return $date;
}
function incrementNextBillDateByEmail($email,$time){
	if(!valid_email($email=lc($email)))return 0;
	if(!($time=intval($time)))return 0;
	$date=getNextBillDateByEmail($email);
	if(!$date=intval($date))return 0;
	$date+=$time;
	if(!($date=intval($date)))return 0;
	$qry="UPDATE subscription SET date_nextbill='".$date."' WHERE LOWER(email)='$email'";
	exec_query_nores($qry);
	//echo "set next bill date successfully:";
	//echo $qry;
	return 1;
}

function getPhpAdsBanner($zoneName){
	if(!$zoneName)return "";
	global $phpAds_context;
	if (!$phpAds_context) $phpAds_context = array();
    $phpAds_raw = view_raw ($zoneName, 0, '', '', '0', $phpAds_context);
    return $phpAds_raw['html'];
}

function setCompanyAccount($row, $company_id=0){
	if($company_id=intval($company_id)){
		update_query("company_accts",$row, array(company_id=>$company_id));
	}else{
		$row[created]=mysqlNow();
		$company_id=insert_query("company_accts",$row);
	}
	return $company_id;
}
function removeCompanyAccount($company_id){
	if(!($company_id=intval($company_id)))return 0;
	del_query("company_accts","company_id",$company_id);
}
function getCompanyAccountList(){
	$qry="SELECT *, UNIX_TIMESTAMP(created)ucreated
		  FROM company_accts ORDER BY company_name";
	return exec_query($qry);
}
function getCompanyAccount($company_id){
	if(!($company_id=intval($company_id)))return array();
	$qry="SELECT *, UNIX_TIMESTAMP(created)ucreated, UNIX_TIMESTAMP(expires)uexpires
		  FROM company_accts
		  WHERE company_id='$company_id'
		  ORDER BY company_name";
	return exec_query($qry,1);
}
function isValidCompanyAccount($company_name, $password){
	$company_name=lc($company_name);
	$password=lc($password);
	$qry="SELECT 'exists' e
		  FROM company_accts
		  WHERE LOWER(company_name)='".smartquote($company_name)."'
		  AND LOWER(password)='".smartquote($password)."'";
	return ('exists'==exec_query($qry,1,"e"));
}

function setItemTracking($item_id,$item_type){
	   	$params["item_id"]=$item_id;
		$params["item_type"]=$item_type;
		$params["time"]=mysqlNow();
		$id=insert_query_logs("ex_item_tracking_view",$params,$safe=0);
		if(isset($id)){
			return $id;
		}
   }

function logEmail($type,$id=0,$url='') {
        $type = smartquote($type);
        $id = (int)$id;
        $url = smartquote($url);
		$date=mysqlNow();
	$qry = "INSERT INTO `tracking_email` (`type`,'time',`id`,`link`)
		  VALUES ('{$type}',{$date},{$id},'{$url}')";
	return exec_query($qry);
}
function loginBoxSignin($refer=""){
	global $AUTH_URL,$_COOKIE,$BADLOGIN,$_SESSION,$refer,$USER;
		if($_COOKIE[autologin]&&$_COOKIE[EMAIL]){
			list($email,$password)=array($_COOKIE[EMAIL],$_COOKIE[PASSWORD]);
		}
	?>
	<table width="100%" cellpadding=0 cellspacing=0 border="0">
	<form method="post" action="<?$D_R?>/auth-2.htm" name="signinform">
	<?if($refer){input_hidden("refer",$refer);}?>
	<TR>
	<td width="52%" style="text-align: right;" >
	<?if($BADLOGIN){?>
		<div class=error>Invalid Member ID/Password. If you don't have an account already please register.</div>
	<?}?>
	Please enter your Member ID:</td>
	<td>&nbsp;<?input_text("email")?><br><?=spacer(1,5)?>&nbsp;</center></TD></TR>
	<tr><td width="52%" style="text-align: right;">Please enter your password:</td>
	<td>&nbsp;<?input_pass("password")?><br>&nbsp;</center></td></tr>

	<TR valign=top>
	<TD width="50%" style="text-align: right;"><input type="image" align="top" class="button" src="<?=$IMG_SERVER?>/images/button_login.gif" value="Log In"><?=spacer(5)?>
	<!-- <TD><input type="button" class=inbtn value="Log In" onclick="validateSignin()"><?=spacer(5)?></TD> -->
	&nbsp;<input type="checkbox" align="right" name="setcookie"<?=($_COOKIE[autologin]?" checked":"")?> id="autologinbox"><?=spacer(5)?></TD>
	<TD width="50%"><label for=autologinbox>Remember my Member ID and password on this computer.</label><br>
	<a href="/register/forgotpass.htm">Forgot your password?</a>
	</TD>
	</TR>
	</form>
	</table>
<?}

function loginBoxSignin_customproduct($refer=""){
	global $AUTH_URL,$_COOKIE,$BADLOGIN,$_SESSION,$refer,$USER;
		if($_COOKIE[autologin]&&$_COOKIE[EMAIL]){
			list($email,$password)=array($_COOKIE[EMAIL],$_COOKIE[PASSWORD]);
		}
	?>

	<table width="100%" border="0" cellpadding=0 cellspacing=0 border="0">
	<form method="post" action="http://cooper.minyanville.com/auth-2.htm" name="signinform"><?//$D_R?>
	<?if($refer){input_hidden("refer",$refer);}?>
	<TR>
	<td width="1%">
	<?if($BADLOGIN){?>
		<div class=error>Invalid Member ID/Password. If you don't have an account already please register.</div>
	<?}?>
	</td>
	<tr>
	<td width="10%"  style="text-align: right;">&nbsp;</td>
	<td width="90%"><?input_text_email("email")?><?=spacer(1,5)?><?input_pass("password")?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="" onmousedown="document.getElementById('CustProdBuzzLogin').style.display='none'"><img src="<?=$IMG_SERVER?>/images/custom_products/xclose_button.gif" width="15" height="15" border="0" /></a><br></td></tr>

	<TR valign=top>
	<div class="LoginText"><TD width="10%" style="text-align: right;">
	<input type="checkbox" name="setcookie"<?=($_COOKIE[autologin]?" checked":"")?> id="autologinbox"></div><?=spacer(5)?></TD>
	<TD width="50%"><label for=autologinbox><div class="LoginText">Remember my Member ID and password on this computer.</div></label><input type="image" align="top" class="button" src="<?=$IMG_SERVER?>/images/custom_products/button_go.gif" value="Log In"><?=spacer(5)?>
	</br><div class="LoginTextmain"><a href="<?=$HTPFX.$HTHOST;?>/register/new/index.htm">Register</a> | <a href="<?=$HTPFX.$HTHOST;?>/register/forgotpass.htm">Forgot Password</a></div><br>
	</TD>
	</TR>
	</form>
	</table>

<?}

function getEmailTemplate($profid,$name){
	$qry="select * from product_lang where prof_id=$profid and name='$name' limit 0,1";
	$res=exec_query($qry);
	if(isset($res)){
		return $res;
	}
	else{
		return;
	}
}

function getCancelSubsBody($profid){
	$qry="select name,description from product where id=$profid";

	$res=exec_query($qry);

	if(isset($res)){
		$body="Your subscription for <b>".$res[0]['name']." ".$res[0]['description']."</b> has been cancelled.";
		return $body;
	}
	else{
		return;
	}
}

function getComboSubsBody($comboid){
	$qry="select description from option_deals where id=$comboid";

	$res=exec_query($qry);

	if(isset($res)){
		$body="Your subscription for <b>".$res[0]['description']."</b> has been cancelled.";
		return $body;
	}
	else{
		return;
	}
}

function is_stock($stock)
{
  $sql="select * from ex_stock where stocksymbol='$stock' and is_active='1'";
  $result=exec_query($sql,1);
  return $result;
}


function make_stock_links($body,$pubDate = NUll)
{
 	//$pattern = "/\((\b?\^?[A-Z-:. ]{1,10}\b)\)/";
 	$tickerFormatUpdatedDate='2012-10-01'; 		/*yyyy-mm-dd*/
 	$pattern = "/\(((\b)?(\^)?[A-Za-z0-9-:. ]{1,20}\b)\)/";

	preg_match_all($pattern, $body, $stocks_matches);
	$unique_stocks=array();
	foreach($stocks_matches[1] as $id=>$value)
	{
		$stockVal='';
		if(strtotime($pubDate)>=strtotime($tickerFormatUpdatedDate)){
			$pos = strpos($value,':');
			if($pos!=false){
	 			$stockVal = substr($value,$pos+1);
	 			$stockVal = str_replace('.','^',$stockVal);
			}
 		}else{
 			$stockVal = $value;
 		}
	   	$stock=is_stock($stockVal);
		if($stock){
   			if(!in_array($stockVal,$unique_stocks)){
				$pattren_value = str_replace('^','\^',$value);
				$replacement = '(<a href="http://finance.minyanville.com/minyanville?Page=QUOTE&Ticker='.$stockVal.'" style="color:#c27234;" title="'.$stock[SecurityName].'" target="_parent">'.$value.'</a>)';
				$body=preg_replace ("/\(".$pattren_value."\)/",$replacement,$body,1);
				$unique_stocks[]=$stockVal;
   			}
  		}
 	}
 	return $body;
}

function make_stock_array($body,$countstock=NULL,$pubDate = NULL)
{
	global $stockslimit;
	$pattern = "/\(((\b)?(\^)?[A-Za-z0-9-:. ]{1,20}\b)\)/";
	$tickerFormatUpdatedDate='2012-10-01'; 		/*yyyy-mm-dd*/
	preg_match_all($pattern, $body, $stocks_matches);
	$unique_stocks=array();
	foreach($stocks_matches[1] as $id=>$value)
	{
		$stockVal='';
		if(strtotime($pubDate)>=strtotime($tickerFormatUpdatedDate)){
			$pos = strpos($value,':');
			if($pos!=false){
	 			$stockVal = substr($value,$pos+1);
	 			$stockVal = str_replace('.','^',$stockVal);
			}
 		}else{
 			$stockVal = $value;
 		}
		$stock=is_stock($stockVal);
		if($stock){
			if(is_array($unique_stocks))
				if(!in_array($stockVal,$unique_stocks)){
					$unique_stocks[]=$stockVal;
					if(!$countstock){
						if (count($unique_stocks)>($stockslimit - 1)){
							break;
						}
					}
				}
			}
		}
	return $unique_stocks;
}


function change_ssl_url($body)
{
     global $IMG_SERVER;
     $replacement = $IMG_SERVER;
     $body=preg_replace ("/https:\/\/storage.googleapis.com\/mvassets/",$replacement,$body);
	 return $body;
}

function changeImageUrl($body){
	 global $IMG_SERVER;
	$body=str_replace("https://admin.minyanville.com/assets/FCK_May2009",$IMG_SERVER."/assets/FCK_May2009",$body);
	return $body;
}

function getLatestContributorArticle($contributorid)
{
	$sqlLatestContributorArticle="SELECT * FROM articles where approved='1' and is_live='1' and contrib_id=".$contributorid." order by id desc limit 0,1";
	$resLatestContributorArticle=exec_query($sqlLatestContributorArticle,1);
	return $resLatestContributorArticle;
}

function getBrandedLogo($id){
	$qry="select * from branded_images";
	if($id){
		$qry.=" where id=$id";
		$result=exec_query($qry,1);
	} else {
		$qry.=" order by name";
		$result=exec_query($qry);
	}
	if(isset($result)){
		return $result;
	}

}


function getbuzzbrandedlogo($id=NULL){
	$qry="select * from buzz_branded_images";
	if($id){
		$qry.=" where id=$id";
		$result=exec_query($qry,1);
	} else {
		$qry.=" order by name";
		$result=exec_query($qry);
	}
	if(isset($result)){
		return $result;
	}

}

// this function will delete buzz post from buzzbanter_today table
// and get the updated post from buzzbnater on the same id
// and insert it into buzzbanter_today
function updateBuzzTodayTable($buzzId){

	// initialize return value to 0
	$buzzTodayPost=0;

	// delete post form buzzbnater_today
	del_query('buzzbanter_today','id',$buzzId);

	// get buzz post from buzzbnater
	$buzzQuery="select * from buzzbanter where id=$buzzId";
	$buzzResult=exec_query($buzzQuery,1);

	// avoid db insertion failure
	// not working in AWS
	/*$buzzResult[title]=mysql_real_escape_string($buzzResult[title]);
	$buzzResult[body]=mysql_real_escape_string($buzzResult[body]);
	$buzzResult[author]=mysql_real_escape_string($buzzResult[author]);
	$buzzResult[login]=mysql_real_escape_string($buzzResult[login]);
	$buzzResult[position]=mysql_real_escape_string($buzzResult[position]);*/

	// if result found
	if($buzzResult && count($buzzResult)!=0){
		// insert post into buzzbanter_today table
		$buzzTodayPost=insert_query('buzzbanter_today',$buzzResult);
	}

	// return inserted id
	return $buzzTodayPost;
}

function setCookieMVmobile(){
   if($_GET['mv']==md5('1')){
		//mcookie("mvmobile","1");
		set_sess_vars("ses_mvmobile","1");
	}
}

function resetCookieMVmobile(){
   if($_GET['mv']==md5('1')){
		if($_COOKIE['mvmobile']=="1")
		{
			$setCookieTime=time()+2592000;  //30 days
			mcookie("mvweb","1",$setCookieTime);
			mcookie("mvmobile","0",$setCookieTime);
		}
		set_sess_vars("ses_mvmobile","1");
	}
}

function sendDailyDigestEmail($from,$subject,$dailyDigestID,$friendEmail,$comment,$name){
	    global $CDN_SERVER,$D_R,$HTPFX,$HTHOST;
	    require_once $D_R.'/lib/swift/lib/swift_required.php';
	    $dailyDigestAlertTmpl=$HTPFX.$HTHOST."/admin/email-digest/send_digest.php";

	    $mailer = Swift_MailTransport::newInstance();
	    $subject=$subject;
		$msgfile="/tmp/spam_".mrand().".eml";
		$msghtmlfile="$D_R/assets/data/".basename($msgfile);
		$msgurl=$dailyDigestAlertTmpl.qsa(array(id=>$dailyDigestID,mail=>1));
		$mailbody=inc_web($msgurl);
		if($comment!="")
		{
			$mailbody= 'Your friend '.$name.' has sent you this newsletter link from minyanville.com with a message <br> <br> <span style="float: left; font-size: 13px; margin:0 0 15px 0; font-style: italic; font-family: arial;">"'.$comment.' "</span><br><br>'.$mailbody;
		}
		 mymail(array($friendEmail),$from,$subject,$mailbody);
		/*$message = Swift_Message::newInstance();
		$message->setSubject($subject);
		$message->setBody($mailbody, 'text/html');
		$message->setSender($from);
		$message->setTo(array($friendEmail));
		$result = $mailer->send($message);*/
		return $result;
	}

function kissMetricsTracking($pageName){
	$pages[]='subscription_product_registration';
	$pages[]='cross_sell';
	$pages[]='subscription_product_welcome';
	if(in_array($pageName,$pages)){
		?>
<script type="text/javascript">
  var _kmq = _kmq || [];
  var _kmk = _kmk || '3db61857e6a179f2fd529809281ed96b62190373';
  function _kms(u){
    setTimeout(function(){
      var d = document, f = d.getElementsByTagName('script')[0],
      s = d.createElement('script');
      s.type = 'text/javascript'; s.async = true; s.src = u;
      f.parentNode.insertBefore(s, f);
    }, 1);
  }
  _kms('//i.kissmetrics.com/i.js');
  _kms('//doug1izaerwt3.cloudfront.net/' + _kmk + '.1.js');
</script>
		<?
	}
}


?>