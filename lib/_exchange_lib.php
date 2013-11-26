<?php
	function RegisterEmailPrivacy($subid, $arremail)
	{
		$len=count($arremail);
		foreach($arremail as $key=>$value)
		{
			$qry = "select id from ex_email_template where event ='".$key."'";
			$result = exec_query($qry);
			$privacydata=array(
			subscription_id=>$subid,
			email_id=>$result[0][id],
			alert=>$value
			);
			insert_query("ex_user_email_settings", $privacydata);
		}
	}

	function RegisterProfilePrivacy($subid, $arr)
	{
		$len=count($arr);
		foreach($arr as $key=>$value)
		{
			$qry = "select id from ex_privacy_attribute where attribute ='".$key."'";
			$result = exec_query($qry);
			$privacydata=array(
			subscription_id=>$subid,
			privacy_attribute_id=>$result[0][id],
			enabled=>$value
			);
			insert_query("ex_profile_privacy", $privacydata);
		}
	}

//New Email-alert Registration
function NewEmailUser($GET,$hasbuzz){
	// For both case we are getting id by using this function
	$USER = new user($GET[lemail], $GET[lpassword]);
	$uid=$USER->id;

	if($GET[fname]==''){$fname=$USER->fname;}else{$fname=$GET[fname];}
	if($GET[lname]==''){$lname=$USER->lname;}else{$lname=$GET[lname];}
	if($GET[agegroup]==''){$agegroup=$USER->agegroup;}else{$agegroup=$GET[agegroup];}
	if($GET[zip]==''){$zip=$USER->zip;}else{$zip=$GET[zip];}

	if($GET['categories']!=''){
		// This check is for static entry of email-alert category - start
		$afterexplode=explode(",",$GET['categories']);
		$key=array_search('0',$afterexplode);
		if(empty($key)){
			$catgors=$GET['categories'];
			$recvdaliygazet=0;
		}else{
			unset($afterexplode[$key]);
			$GET['categories']=implode(",",$afterexplode);
			$catgors=$GET['categories'];
			$recvdaliygazet=1;
		}
		// This check is for static entry of email-alert category - end
	}
	// if an existing user comes then he may/may not be logged in
	if($GET[isloggedin]==1){
	$loggedin=1;
	$insertupdate=1;
	}elseif($GET[isloggedin]==0){
		$isauth=$USER->isAuthed;
			if($isauth==1){
			// the user is valid
			$insertupdate=1;
			}
	}
	// if correct then just login and insert / update the records
	$prodarr=explode(",",$GET['products']);

	// insert detail starts
	if($insertupdate==1){
		$sub = $GET;
		$sub[date] = date('U');
		$sub[account_status] = "enabled";
		$expires=mktime(0, 0, 0, date("m")  , date("d")+14, date("Y"));
		$sub[expires]=$expires;
		$sub[type] = $ptype;
		$sub[is_exchange] = 1; // this means we are making the exisiting user to exchange user
		$sub[password] = $sub[password];
		$sub[email] = $sub[email];
		if($sub[email]==''){$sub[email]=$sub[lemail];}
		if($sub[password]==''){$sub[password]=$sub[lpassword];}

		$sub[fullname]=strtoupper($sub[fname])." ".strtoupper($sub[lname]);

	// ****** ALL CASES TO INSERT IN SUBSCRIPTION AND SUBSCRIPTION_PS TABLE ********//
	if(!empty($prodarr)){
		if(($hasbuzz) && ((in_array("coper",$prodarr)) || (in_array("quint",$prodarr)))){
		 // insert into subscription_ps
			$isbuzzprev=1;
				if(in_array("cooper",$prodarr)){
					$prof_id=2;
					$arrsubscription_ps = array(date=>$sub[date], account_status=>$sub[account_status], prof_id=>$prof_id, type=>'trial', expires=>$sub[expires],trial_status=>'active',subid=>$uid,is_buzz=>$isbuzzprev,fname=>$fname,lname=>$lname,agegroup=>$agegroup,zip=>$zip,email=>$GET[lemail], password=>$GET[lpassword],premium=>1);
					$subps = insert_query(subscription_ps, $arrsubscription_ps);
				}
				if(in_array("quint",$prodarr)){
					$prof_id=3;
					$arrsubscription_ps = array(date=>$sub[date], account_status=>$sub[account_status], prof_id=>$prof_id, type=>'trial',expires=>$sub[expires],trial_status=>'active',subid=>$uid,is_buzz=>$isbuzzprev,fname=>$fname,lname=>$lname,agegroup=>$agegroup,zip=>$zip,email=>$GET[lemail], password=>$GET[lpassword],premium=>1);
					$subps = insert_query(subscription_ps, $arrsubscription_ps);
				}
				$isbuzzprev=0;
		}else if($hasbuzz==0){
		 // insert into subscription_ps while MVIL user has no B&B and he takes this type=trial and premium=1
			$isbuzzprev=1;
			if(in_array("buzz",$prodarr)){
				$ptype='trial';
				$buzzprmum=1;
				$arrsubscription = array(account_status=>$sub[account_status], type=>$ptype, trial_status=>'active',expires=>$sub[expires],premium=>$buzzprmum,recv_daily_gazette=>$recvdaliygazet);
				$subps_up = update_query(subscription, $arrsubscription,array(id=>$uid));
			}
			if(in_array("cooper",$prodarr)){
				$prof_id=2;
				$arrsubscription_ps = array(date=>$sub[date], account_status=>$sub[account_status], prof_id=>$prof_id, type=>'trial', expires=>$sub[expires],trial_status=>'active',subid=>$uid,is_buzz=>$isbuzzprev,fname=>$fname,lname=>$lname,agegroup=>$agegroup,zip=>$zip,email=>$GET[lemail], password=>$GET[lpassword],premium=>1);
				$subps_cop = insert_query(subscription_ps, $arrsubscription_ps);
			}
			if(in_array("quint",$prodarr)){
				$prof_id=3;
				$arrsubscription_ps = array(date=>$sub[date], account_status=>$sub[account_status], prof_id=>$prof_id, type=>'trial',expires=>$sub[expires],trial_status=>'active',subid=>$uid,is_buzz=>$isbuzzprev,fname=>$fname,lname=>$lname,agegroup=>$agegroup,zip=>$zip,email=>$GET[lemail], password=>$GET[lpassword],premium=>1);
				$subps_quint = insert_query(subscription_ps, $arrsubscription_ps);
			}
						if(isset($subps_up) && ($subps_up!=0)){
						$setisbuz=update_query(subscription_ps,array(is_buzz=>$isbuzzprev),array(subid=>$uid));
						}
						$isbuzzprev=0;
		}
		// Making all users as exchange user
		$setisexchg=update_query(subscription,array(is_exchange=>1),array(id=>$uid));
	    }

		// ****** ALL CASES TO INSERT IN SUBSCRIPTION AND SUBSCRIPTION_PS TABLE ********//
		// subscription_ps
		// email_alert_categorysubscribe
		// email_alert_authorsubscribe
		if($GET['categories']!=''){
		    if($GET['categories']!=","){
				$email_alert='1';
			}else{
				$email_alert='0';
				$catgors="";
			}
			$categoriesarr = array(subscriber_id=>$uid,category_ids=>$catgors,email_alert=>$email_alert);
			$catps = insert_query(email_alert_categorysubscribe, $categoriesarr);
		}
		if($GET['contributors']!=''){
			$contrbtors=$GET['contributors'];
			if($contrbtors!=","){
					$email_alert='1';
			}else{
					$email_alert='0';
					$contrbtors="";
			}
			$contributorarr = array(subscriber_id=>$uid,author_id=>$contrbtors,email_alert=>$email_alert);
			$catps = insert_query(email_alert_authorsubscribe, $contributorarr);
		}
	}
	return $uid;
}
//New Email-alert Registration

	//Registration
	function NewUser($GET)
	{
		$prodarr=explode(",",$GET['products']);
		if(in_array("buzz",$prodarr)){
				$ptype='trial';
		}elseif (empty($prodarr[0])){
				$ptype='exchange';
		}else{
			$ptype='nonbuzz';
		}
		$sub = $GET;
		$sub[date] = date('U');
		$sub[account_status] = "enabled";
		$expires=mktime(0, 0, 0, date("m")  , date("d")+14, date("Y"));
		$sub[expires]=$expires;
		$sub[type] = $ptype;
		//*** $sub[type] = 'exchange';
		$sub[is_exchange] = 1;
		$sub[password] = $sub[rpassword];
		$sub[fname] = strtoupper($sub[firstname]);
		$sub[lname] = strtoupper($sub[lastname]);
		$sub[email] = $sub[remail];
		$name = $sub[rusername];
		$serverip=$_SERVER['REMOTE_ADDR'];
		$agegroup=$sub[agegroup];
		$zip=$sub[zip];
		$premium=1;

		if($GET['categories']!=''){
						// This check is for static entry of email-alert category - start
						$afterexplode=explode(",",$GET['categories']);
						$key=array_search('0',$afterexplode);

						if(empty($key)){
							$catgors=$GET['categories'];
							$update=0;
						}else{
							unset($afterexplode[$key]);
							$GET['categories']=implode(",",$afterexplode);
							$GET['categories']=$GET['categories'];
							$catgors=$GET['categories'];
							$update=1;
						}
						// This check is for static entry of email-alert category - end
		}
		if($sub[type]=='trial'){
		$buzzprmum=1;
		}else{
		$buzzprmum=0;
		}
		$arrsubscription = array(date=>$sub[date], account_status=>$sub[account_status], type=>$sub[type], is_exchange=>$sub[is_exchange], password=>$sub[password], fname=>$sub[fname], lname=>$sub[lname], email=>$sub[email],expires=>$sub[expires],trial_status=>'inactive',ip=>$serverip,agegroup=>$agegroup,zip=>$zip,premium=>$buzzprmum,recv_daily_gazette=>$update);
		$sub_id = insert_query(subscription, $arrsubscription);

		// inserting in subscription_ps table
		if($sub_id){
			if(in_array("cooper",$prodarr)){
				$prof_id=2;
				$arrsubscription_ps = array(date=>$sub[date], account_status=>$sub[account_status], prof_id=>$prof_id, type=>'trial', password=>$sub[password], fname=>$sub[fname], lname=>$sub[lname], email=>$sub[email], expires=>$sub[expires],trial_status=>'inactive',subid=>$sub_id,agegroup=>$agegroup,zip=>$zip,premium=>$premium);
				$subps = insert_query(subscription_ps, $arrsubscription_ps);
			}
			if(in_array("quint",$prodarr)){
				$prof_id=3;
				$arrsubscription_ps = array(date=>$sub[date], account_status=>$sub[account_status], prof_id=>$prof_id, type=>'trial', password=>$sub[password], fname=>$sub[fname], lname=>$sub[lname], email=>$sub[email], expires=>$sub[expires],trial_status=>'inactive',subid=>$sub_id,agegroup=>$agegroup,zip=>$zip,premium=>$premium);
				$subps = insert_query(subscription_ps, $arrsubscription_ps);
			}
		}
		// inserting in subscription_ps table

		// inserting in email_alert_categorysubscribe+email_alert_authorsubscribe table

		if($sub_id){
			if($GET['categories']!=''){
			    if($GET['categories']!=","){
					$email_alert='1';
				}else{
					$email_alert='0';
					$catgors="";
				}
				$categoriesarr = array(subscriber_id=>$sub_id,category_ids=>$catgors,email_alert=>$email_alert);
				$catps = insert_query(email_alert_categorysubscribe, $categoriesarr);
			}
			if($GET['contributors']!=''){
				$contrbtors=$GET['contributors'];
				if($GET['contributors']!=","){
					$email_alert='1';
				}else{
					$email_alert='0';
					$contrbtors="";
				}
				$contributorarr = array(subscriber_id=>$sub_id,author_id=>$contrbtors,email_alert=>$email_alert);
				$contps = insert_query(email_alert_authorsubscribe, $contributorarr);
			}
	}
		// inserting in email_alert_categorysubscribe+email_alert_authorsubscribe table
		if($sub_id)
		{
			$subarray = array(subscription_id=>$sub_id) ;
			$profileid = insert_query(ex_user_profile, $subarray);
			if($profileid)
			{
				RegisterUser($sub_id);
			}
			 sendregistrationmail($sub_id);
			/* code for product welcomes and activation emails */
			$countprod=count($prodarr);
			if(in_array("buzz",$prodarr)){
			    $prod_buzz=1;
			}
			if(in_array("cooper",$prodarr)){
				$prod_jeff=2;
			}
			if(in_array("quint",$prodarr)){
			    $prod_quint=3;
			}
			if($countprod>0){
				productswelcomeemails($sub_id,$prod_buzz,$prod_jeff,$prod_quint,$countprod);
			}
			/**************************************************/

		}
		$USER = new user($sub[email], $sub[password]);
		return $profileid;
	}

	function NewRegistration($GET, $loginid)
	{
		$sub_id = update_query(subscription,array(is_exchange=>'1'),array(id=>$loginid));
		if($sub_id)
		{
			$subarray = array(subscription_id=>$loginid);
			$profileid = insert_query(ex_user_profile, $subarray);
			if($profileid)
			{
				RegisterUser($loginid);
			}
			 sendregistrationmail($loginid);
		}
		$USER = new user($GET['email'], $GET['password']);
		return $profileid;
	}

	function RegisterUser($subid)
	{
		global $ColumnEmailSetting, $ColumnPrivacySetting;
		RegisterEmailPrivacy($subid, $ColumnEmailSetting);
		RegisterProfilePrivacy($subid, $ColumnPrivacySetting);
	}

	function sendregistrationmail($receiver_id)
	{
		global $HTHOST, $HTPFX, $MODERATOR_EMAIL;
		$objfriends = new friends();
		$getsubs=exec_query("select fname, lname, email from subscription where id=$receiver_id",1);
		$fname =$getsubs[fname];
		$lname = $getsubs[lname];
		$toemail = $getsubs[email];
		$fullname = $fname.' '.$lname;
		$event='welcome';
		$res=$objfriends->getEmailTemplate($event); # call getemailtemplate function
		$subject=$res[email_subject];
		$pat[] = "/\[user's fname\]/";
		$rep[] = ucwords(strtolower($fname));
		$subject = preg_replace($pat,$rep,$subject);
		if(isset($res['template_path']))
		{
			$path=$res['template_path'];
			$WELCOME_EML_TMPL=$HTPFX.$HTHOST.$path;
			$from=$MODERATOR_EMAIL;
			# call mail function
			mymail($toemail,
				$from,
				$subject,
				inc_web("$WELCOME_EML_TMPL?event=$event&to_id=$receiver_id&to_email=$toemail")
				);
		}
	}


/*******************************************************/
/*New functions for product welcome emails and activation emails*/
// change
function productswelcomeemails($loginid,$prod_buzz,$prod_jeff,$prod_quint,$countprod){
		global $HTHOST, $HTPFX;
		$getsubs=exec_query("select fname, lname, email from subscription where id=$loginid",1);
		$fname =$getsubs[fname];
		$lname = $getsubs[lname];
		$toemail = $getsubs[email];
		$fullname = $fname.' '.$lname;
		if($countprod=='1') {
			$subject ="Welcome to the Minyanville Premium Product";
		} else {
			$subject ="Welcome to the Minyanville Premium Products";
		}
		    $code=md5($toemail);
			$path="/emails/_eml_alert_trial_register.htm?id=$loginid&name=$fname&prod_buzz=$prod_buzz&prod_jeff=$prod_jeff&prod_quint=$prod_quint&code=$code";
			$EML_TMPL=$HTPFX.$HTHOST.$path;
			$from="Minyanville <support@minyanville.com>";
			# call mail function
			mymail($toemail,
				$from,
				$subject,
				inc_web("$EML_TMPL")
				);
}

function productwelcomeemailbuzz($subid,$email){
    global $HTHOST, $HTPFX;
	$subject ="Welcome to the 'Ville";
	$path="/emails/_eml_bb_email_welcome.htm?subid=$subid";
	$EML_TMPL=$HTPFX.$HTHOST.$path;
	$from="Minyanville admin <support@minyanville.com>";
	# call mail function
	mymail($email,
		$from,
		$subject,
		inc_web("$EML_TMPL")
	);

}
function productwelcomeemailjeff($email,$pass){
    global $HTHOST, $HTPFX;
	$subject ="Welcome to the 'Ville";
	$path="/emails/_eml_register_cooper.htm?uid=$email&pwd=$pass";
	$EML_TMPL=$HTPFX.$HTHOST.$path;
	$from="Minyanville admin <support@minyanville.com>";
	# call mail function
	mymail($email,
		$from,
		$subject,
		inc_web("$EML_TMPL")
		//inc_web("$COMBO_EML_TMPL?uid=$email&pwd=$pass")
	);

}
function productwelcomeemailquint($email,$pass){
    global $HTHOST, $HTPFX;
	$subject ="Welcome to the 'Ville";
	$path="/emails/_eml_register_quint.htm?uid=$email&pwd=$pass";
	$EML_TMPL=$HTPFX.$HTHOST.$path;
	$from="Minyanville admin <support@minyanville.com>";
	# call mail function
	mymail($email,
		$from,
		$subject,
		inc_web("$EML_TMPL")
		);
}


/******************************************************/

	function implode_assoc($glue1, $glue2, $array){
		foreach($array as $key => $val)
		$array2[] = $key.$glue1.$val;
		return implode($glue2, $array2);
	}
	//Registration

	//Check ibox function
	function iboxCheck()
	{
		if(!($_SESSION['exchange']) && $_SESSION['SID']){
			$ret = "<a href='$HTHOST/community/exchange_register/welcome.htm?flag=welcome' rel='ibox'>";
		}
		elseif(!$_SESSION['SID']){
			//$ret = "<a href='$HTHOST/community/exchange_register/index.htm?flag=ibox' rel='ibox'>";
			$ret = "<a href='$HTHOST/community/exchange_register/index.htm' rel='ibox'>";
		}
		elseif($_SESSION['exchange'] && $_SESSION['SID']){
			$ret = "";
		}
		return $ret;
	}

/*Function to get most popular tags*/
	function get_most_popular_tags($populartag,$offset,$limit)
	{
	$tagQry="select ex.tag, count(item.item_id) as cnt from ex_tags ex, ex_item_tags item where ex.id = item.tag_id
			 and ex.tag in ('$populartag') group by ex.id order by cnt desc limit $offset, $limit";
	$populartags = exec_query($tagQry);
	return $populartags;
}

############################################################################
class Exchange_Element{

	function redirectExchangeDiscussionHome(){
		Header( "HTTP/1.1 301 Moved Permanently" );
		Header( "Location: ".$HTPFX.$HTHOST."/articles/articlelisting.htm" );
		exit;	
	}
	
	function redirectEchangeDiscussionThread($type,$threadId){
	   $qry="select content_id from ex_thread where content_table='".$type."' and id='".$threadId."'";
	   $getResult=exec_query($qry,1);
	   $getContentId=$getResult['content_id'];
	   if($getContentId){
		 switch($type){
			case "articles":
			    $getUrl=$this->getRedirectExchangeUrl($type,$getContentId);
				Header("HTTP/1.1 301 Moved Permanently");
				Header("Location: ".$HTPFX.$HTHOST.$getUrl);
			break;
			case "daily_feed":
			    $getUrl=$this->getRedirectExchangeUrl($type,$getContentId);
				Header("HTTP/1.1 301 Moved Permanently");
				Header("Location: ".$HTPFX.$HTHOST.$getUrl);
			break;
			case "mvtv":
				Header("HTTP/1.1 301 Moved Permanently");
				Header("Location:http://videos.minyanville.com/");
			break;
			case "slideshow":
			    $getUrl=$this->getRedirectExchangeUrl($type,$getContentId);
				Header("HTTP/1.1 301 Moved Permanently");
				Header("Location: ".$HTPFX.$HTHOST.$getUrl);
			break;
		   }
	   }
	}
	
	function getRedirectExchangeUrl($type,$itemId){
		$qry="select id from ex_item_type where item_table='".$type."'";
		$getResult=exec_query($qry,1);
		$getItemId=$getResult['id'];
		if($getItemId){
			$qry="select url from ex_item_meta where item_id='".$itemId."' and item_type='".$getItemId."' and is_live='1'";
			$getResult=exec_query($qry,1);
			$getUrl=$getResult['url'];
			if($getUrl){
				return $getUrl;
			}
		}
	   return false;
	}

		var $_normalize_tags = 1;
		var $_normalized_valid_chars = 'a-zA-Z0-9';

	# Function to call ibox.
	function checkiBox($pageid,$attributes)
	{
		global $page_config;
		$strAttribute="&";
		if(is_array($attributes))
		foreach($attributes as $id=>$value)
		{
			$strAttribute.=$id."=".$value."&";
		}
		//$strAttribute.="#txtpostform";
		if(!($_SESSION['exchange']) && $_SESSION['SID'])
		{
			//$strLink = "<a href='$HTHOST/community/exchange_register/welcome.htm?flag=welcome&page=".$pageid.$strAttribute."'  rel='ibox'>";
		}
		elseif(!$_SESSION['SID'])
		{
			$strLink = "<a href='$HTHOST/community/exchange_register/index.htm?page=".$pageid.$strAttribute."'  rel='ibox'>";
		}
		elseif($_SESSION['exchange'] && $_SESSION['SID'])
		{
			$Link=$page_config[$pageid]['URL']."?".$strAttribute;
			$strLink = "<a href=".$Link.">";
		}

		echo $strLink;
	}


/*Function to check unwanted space and charactes in tag field*/
function tag_space($tagnames){
	$tagnames = str_replace(",", " ", $tagnames);
	$tagnames = trim($tagnames);
	$pat[0] = "/^\s+/";
	$pat[1] = "/\s{2,}/";
	$pat[2] = "/\s+\$/";
	$rep[0] = "";
	$rep[1] = " ";
	$rep[2] = "";
	$tagnames = preg_replace($pat,$rep,$tagnames);
	if($tagnames !== ""){
		$tagnames=explode(" ",$tagnames);
	}
	return $tagnames;
	$count=count($tagnames);
}

/*Function to check special characters*/
function normalize_tag($tag){
	if ($this->_normalize_tags) {
	$normalized_valid_chars = $this->_normalized_valid_chars;
	$normalized_tag = preg_replace("/[^$normalized_valid_chars]/", ",", $tag);
		if(is_array($normalized_tag))
	foreach($normalized_tag as $id=>$value)
	{
		$normalized_tag[$id] = strtoupper($value);
	}
	return $normalized_tag;
	} else {
	$tag = strtoupper($tag);
	return $tag;
	}
}

/*Function to check whether a tag exists in the suggestions of Tag list*/
function tag_exists($tagexist,$tags,$tag){
	$extag='';
	$tagstr = "select id as tagid,tag,type_id from ex_tags where tag = '$tagexist'";
	$tagres = exec_query($tagstr);
	$tag_rows = count($tagres);
		if($tag_rows > 0){
			//foreach(exec_query($tagstr) as $tagrow)
			foreach($tagres as $tagrow)
			{
				$tagid=$tagrow['tagid'];
				$tag[tag_id] = $tagid;
			}
			$extag=1;
		}
		else{
			$extag=0;
			$tags[tag] = $tagexist;
		}
		return $extag;
}

/*Function to check whether it is a stock*/
function is_stock($stock)
{
		$stockQry="select * from ex_stock where stocksymbol='$stock'";
		$stocks = exec_query($stockQry);
		if($stocks[0]!=""){
		$stock=1;
		}
		else{
		$stock=0;
		}
		return $stock;
}

/*Function to check whether it is a stock*/
function tag_length($tags)
{
$uniqueTags = array_unique($tags);
$count = count($uniqueTags);
if ($count>6)
{
	for($i=0;$i<6;$i++){
			$tagname[$i] = $uniqueTags[$i];
	}
	$tagnames=$tagname;
		return $tagnames;
}
else
{
		$tagnames = $uniqueTags;
return $tagnames;
	}
}

	function userInfo($subscription_id){

		$sqlQuery="select fname,lname from subscription where id=$subscription_id";
		$strResult=exec_query($sqlQuery,1);
		if(isset($strResult)){
			return $strResult;
		}
		else{
			return;
		}
	}
	function generateQuery($tableName=NULL,$fields,$condition=NULL,$join=NULL,$orderby,$offset=0,$limit=25){

		$strQuery="select ";
		$strFields = implode(',',$fields);
		$strQuery.=" $strFields";

		if(isset($tableName)){
			$strQuery.=" from $tableName";
		}

		if(isset($condition)){
			$strQuery.=" where $condition";
		}

		if(isset($join)){
			$strQuery.=" where $join";
		}

		if(isset($orderby)){
			$strQuery.=" order by $orderby";
		}

		$strQuery.=" limit $offset,$limit";

		return $strQuery;
	}

	/*Function to get type of an object*/
	function get_object_type($type){

	$typeid = exec_query("SELECT id FROM ex_item_type WHERE item_text='$type'",1);
	$typeid=implode(' ',$typeid);
	return $typeid;
	}
	function get_items(){

		$strQuery="select id strid,item_table strtable,item_text strtext from ex_item_type";
		$strResult=exec_query($strQuery);
		$arrItems;

		if(!isset($strResult) || count($strResult)==0){
			return;
		}
		else{
			$index=0;
			foreach($strResult as $key=>$val){
				$arrItems[$index][strid]=$val[strid];
				$arrItems[$index][table]=$val[strtable];
				$arrItems[$index][text]=$val[strtext];
				$index++;
			}
			return $arrItems;
		}
	}//end of function get_items()

	/*Function to retrieve all the tags for an object*/
	function get_tags_on_objects($itemid, $type){
		$getContentId="select content_id,EIT.id itemTypeId from ex_thread ET,ex_item_type EIT where ET.content_table=EIT.item_table
 and ET.id='".$itemid."'";
 		$resContentId=exec_query($getContentId,1);
		$tagQry = "select ext.tag_id tag_id, ex.tag tag from ex_item_tags ext, ex_tags ex, ex_item_type typ where ext.tag_id =
				   ex.id and typ.id = ext.item_type and ext.item_id = '".$resContentId['content_id']."' and typ.id = '".$resContentId['itemTypeId']."'";
		$index=0;
		foreach(exec_query($tagQry) as $tagname)
		{
			$tagsArray[$index]['id']=$tagname['tag_id'];
			$tagsArray[$index]['tag']=$tagname['tag'];
			$index++;
		}
		return $tagsArray;
	}



	function count_all_article($userid){
		$cntArtQry = "select distinct(A.id) from articles A,ex_item_tags EIT, ex_tags ET,subscription S, ex_user_stockwatchlist EUSW,ex_stock ES
where A.id=EIT.item_id
and EIT.item_type=1
and EIT.tag_id=ET.id
and ET.type_id =1
and ET.tag=ES.stocksymbol
and ES.id=EUSW.stock_id
and EUSW.subscription_id=S.id
and S.id='$userid'";
		$cntBlogqry="select distinct(ET.id) from ex_thread ET,ex_blog_subscribed ESB, subscription S
where ET.author_id=ESB.subscribed_to
and ESB.user_id=S.id
and ET.is_user_blog=1
and S.id='$userid'";
		$cntArtCommentQry="select distinct(ET.id) from ex_post EP,ex_thread ET where EP.thread_id=ET.id and is_user_blog=0 and poster_id='$userid'";
		$cntBlogCommentQry="select distinct(ET.id) from ex_post EP,ex_thread ET where EP.thread_id=ET.id and is_user_blog=1 and poster_id='$userid'";

		   $cntUpdates = num_rows($cntArtQry);
		   $cntUpdates +=num_rows($cntBlogqry);
		   $cntUpdates +=num_rows($cntArtCommentQry);
		   $cntUpdates +=num_rows($cntBlogCommentQry);
		   return $cntUpdates;
	}

	function count_msg($userid){
	 $conversation=inbox_conversation($userid,'unread');
    foreach($conversation as $id=>$value)
	{
 		$sqlConversationDetails="select EPM.title,EPM.from_subscription_id,EPM.to_subscription_id,EPM.msg_date,EPMT.private_msg_id,EPMT.text,concat(SUB.fname,' ',SUB.lname) name from ex_private_message EPM ,ex_private_message_text EPMT,subscription SUB where EPM.id in ($value[message_id]) and EPM.id=EPMT.private_msg_id and EPM.from_subscription_id=SUB.id and EPM.from_subscription_id<>'$userid' order by EPM.msg_date desc";
	$resConversationDetails=exec_query($sqlConversationDetails,1);

		if($resConversationDetails){
			$display_conversation[$value['id']]['from']=$resConversationDetails['from_subscription_id'];
		}
	}
    $countMsg = count($display_conversation);
	return $countMsg;
	}

	function countUpdates($user_id)
	{
	global $bloglmt, $artclelmt, $newreplyonartlmt;
# 1.countblogs

		 $qry_authors="select subscribed_to from ex_blog_subscribed where User_id='$user_id' and subscription_status='1' limit $bloglmt";
		 $aindex=0;
		 $blogidstatus=array();
		 foreach(exec_query($qry_authors) as $author)
		 {
			 $author_id=$author['subscribed_to'];
			 $authorArray[$aindex]=$author_id;
			 $aindex++;
		 }
		 if(count($authorArray)>0)
		 {
			 $Subscribed_authors=count($authorArray);
			 $updatedthreads=array();
			 for($k=0;$k<$Subscribed_authors;$k++)
			 {
				 $authorid=$authorArray[$k];
				 $blogqry="select id from ex_thread where Author_id='$authorid' and is_user_blog='1' and enabled='1' and approved='1'";
				 $indexid=0;
				 if(isset($blogArray)) unset($blogArray);
				 foreach(exec_query($blogqry) as $blogids)
				 {
					 $bid=$blogids['id'];
					 $blogArray[$indexid]=$bid;
					 $indexid++;
				 }
				 for($ss=0;$ss<count($blogArray);$ss++)
				 {
					 $blogread="select item_id from ex_item_status where Subscription_id=$user_id and read_status='read' and
					 item_id=$blogArray[$ss]";
					 $Blogstatus = exec_query($blogread);
					 $status = count($Blogstatus);
					 $blogidstatus[$blogArray[$ss]]=$status;
				 }
			 }
		 }
		$countblog=array_count_values($blogidstatus);
		if(count($blogidstatus)!=0)
		{
			$totblogs=$countblog[0];
		}else
		{
			$totblogs=0;
		}

		$allarticlesreadstat="select item_id,read_status from ex_item_status where item_type='1' and subscription_id='$user_id'";
		$allarticlesreadexec = exec_query($allarticlesreadstat);
		if(count($allarticlesreadexec)>0)
		{
			$artid_stat_arr=array();
			//foreach(exec_query($allarticlesreadstat) as $result)
			foreach($allarticlesreadexec as $result)
			{
				$itemid=$result['item_id'];
				$artid_stat_arr[$itemid]=$result['read_status'];
			}
		}
			$allrelatedarticlesqry="select distinct(A.id) as aid from articles A,ex_item_tags EIT, ex_tags ET,subscription S,
				ex_user_stockwatchlist EUSW,ex_stock ES,contributors C where A.id=EIT.item_id and A.contrib_id=C.id and
				EIT.item_type=1 and EIT.tag_id=ET.id and ET.type_id =1 and ET.tag=ES.stocksymbol and ES.id=EUSW.stock_id and
				EUSW.subscription_id=S.id and S.id='$user_id' order by aid DESC LIMIT $artclelmt";
			$allrelatedarticlesqryexec = exec_query($allrelatedarticlesqry);

		if(count($allrelatedarticlesqryexec)>0){
			$artids_arr=array();
			if(isset($artid_stat_arr)){
				$countprerecods=count($artid_stat_arr);
			}else{
				$countprerecods=0;
			}

			$ind=0;
			$displayarticlids=array();

			//**** foreach(exec_query($allrelatedarticlesqry) as $results) already get the result set
			foreach($allrelatedarticlesqryexec as $results)
			{
				$aid=$results['aid'];
				if($countprerecods>0){
					if (array_key_exists($aid, $artid_stat_arr)) {
						if($artid_stat_arr[$aid]=='unread'){
							$displayarticlids[$aid]=0;
						}else{
							$displayarticlids[$aid]=1;
						}
					}else{
						$displayarticlids[$aid]=0;
					}
					$ind++;
				}else{
					$displayarticlids[$aid]=0;
					$ind++;
				}
			}
			}

         if(is_array($displayarticlids))
		 $countarticle=array_count_values($displayarticlids);
		if(count($displayarticlids)!=0){
		$totarticles=$countarticle[0];
		}else{
		$totarticles=0;
		}

		 $newarticlesqry="select distinct(ET.id) as tid from subscription S,ex_user_stockwatchlist EUSW, ex_stock ES,ex_thread ET,
		 ex_item_tags EIT, ex_tags ETAGS where S.id=EUSW.subscription_id and EUSW.stock_id=ES.id and ES.stocksymbol=ETAGS.tag and
		 ETAGS.id=EIT.tag_id and EIT.item_type='4' and EIT.item_id=ET.id and S.id='$user_id'";

		 $newthrdarr=array();
		 $newthrdarrstr='';
		 $newthrdarrread=array();
		 $newpost=0;
		 $postedids='';
		 $target_thredids='';
		 foreach(exec_query($newarticlesqry) as $artids){
			 $artidget=$artids['tid'];
			 $sql_stat_qry="select id,read_status,read_on from ex_item_status where item_type='4' and subscription_id='$user_id'
			 and item_id ='$artidget'";
			 $sqlstatresult = exec_query($sql_stat_qry);
			 $statcnt = count($sqlstatresult);
			 if($statcnt>0){
			 // Something get check for read / unread
				//****foreach(exec_query($sql_stat_qry) as $statres){
				 foreach($sqlstatresult as $statres)
				 {
						if($statres['read_status']=='read'){
							$newthrdarr[$artidget]=1;

							if($newthrdarrstr==''){
							$newthrdarrstr=$artidget.'_'."1";
							}else{
							$newthrdarrstr=$newthrdarrstr.','.$artidget.'_'.'1';
							}

							$newthrdarrread[$artidget]=$statres['read_on'];
							// Check for new posting for this id
							$qry_new_posting="select distinct(poster_id) as poster_id,id,post_time from ex_post where thread_id='$artidget' and poster_id!='$user_id' and post_time>'".$statres['read_on']."' order by post_time DESC LIMIT 1";
							foreach(exec_query($qry_new_posting) as $resst){
								//echo "<br>Post By ".$resst['poster_id'];
								if($target_thredids==''){
									$target_thredids=$artidget;
								}else{
								$target_thredids=$target_thredids.",".$artidget;
								}

								$newpost=$newpost+1;
								if($postedids==''){
									$postedids=$resst['id'];
								}else{
								$postedids=$postedids.",".$resst['id'];
								}
							}
						}else{
							// while read_status='unread'
							$newthrdarr[$artidget]=0;
							if($newthrdarrstr==''){
								$newthrdarrstr=$artidget.'_'."0";
							}else{
								$newthrdarrstr=$newthrdarrstr.','.$artidget.'_'.'0';
							}
							$qry_new_posting="select distinct(poster_id) as poster_id,id,post_time from ex_post where thread_id='$artidget' and poster_id!='$user_id' order by post_time DESC LIMIT 1";
							//echo "<br>".$qry_new_posting;
							foreach(exec_query($qry_new_posting) as $resst){
								//echo "<br>Post By ".$resst['poster_id'];
								if($target_thredids==''){
								$target_thredids=$artidget;
								}else{
									$target_thredids=$target_thredids.",".$artidget;
								}
								$newpost=$newpost+1;
								if($postedids==''){
									$postedids=$resst['id'];
								}else{
									$postedids=$postedids.",".$resst['id'];
								}
							}
						}
					}

			 }else{
					 $newthrdarr[$artidget]=0;
					 if($newthrdarrstr==''){
						 $newthrdarrstr=$artidget.'_'."0";
					 }else{
						 $newthrdarrstr=$newthrdarrstr.','.$artidget.'_'.'0';
					 }
					 $qry_new_posting="select distinct(poster_id) as poster_id,id,post_time from ex_post where
					 thread_id='$artidget' and poster_id!='$user_id' order by post_time DESC LIMIT 1";
					 foreach(exec_query($qry_new_posting) as $resst){
						 if($target_thredids==''){
							 $target_thredids=$artidget;
								}else{
								$target_thredids=$target_thredids.",".$artidget;
								}
						 $newpost=$newpost+1;
						 if($postedids==''){
							 $postedids=$resst['id'];
						 }else{
							 $postedids=$postedids.",".$resst['id'];
						 }
					 }
			 }
		 }

$obj=new BLOGDiscussion();
$blogdiscused1=$obj->countBLOGDiscussion($user_id);
$countblogdiscused=explode('~',$blogdiscused1);
$threadreply=$countblogdiscused[0];

$totalUpdates = $totblogs + $totarticles + $newpost + $threadreply;
return $totalUpdates;
}


	//**************************

	/*Function to retrieve comments for an item. this shud come inti thread class*//*$objthread, '$objthread->id'*/
	function get_all_related_objects($objthread, $offset, $cnt ){

		$commentQry = "select ps.teasure, concat(sub.fname,' ',sub.lname) name, ps.created_on date, ps.id postid, sub.id subid from ex_thread th, ex_post ps, subscription sub where th.id = ps.thread_id and ps.approved = 1 and ps.poster_id = sub.id and ps.suspended = 0 and th.id = '$objthread->thread_id' order by date desc limit $offset,$cnt";

		$index=0;
		foreach(exec_query($commentQry) as $commentname){
			$commentArray[$index]['postid']=$commentname['postid'];
			$commentArray[$index]['teasure']=$commentname['teasure'];
			$commentArray[$index]['name']=$commentname['name'];
			$commentArray[$index]['date']=$commentname['date'];
			$commentArray[$index]['subid']=$commentname['subid'];
			$index++;
		}
		return $commentArray;
	}

	/*Function to retrieve recent most threads related to an article*/
	function get_objects_with_tag_id($thread){
		$index=0;
		foreach(exec_query($thread) as $threadname)
		{
			$threadArray[$index]['teasure']=$threadname['teasure'];
			$threadArray[$index]['author_id']=$threadname['author_id'];
			$threadArray[$index]['created_on']=$threadname['created_on'];
			$index++;
		}
		return $threadArray;
	}

	/*Function to check the date/time of an object*/
	function check_date($date){
		$currDate=date('m-d-Y');
		$formatTime = date("g:i a ", strtotime($date));
		$formatDate = date("m-d-Y", strtotime($date));

		if ($formatDate==$currDate){
			$Date="Today";
		}
		else{
			$Date=$formatDate;
		}
		$formattedDate.=$Date.",&nbsp;".$formatTime;
		return $formattedDate;
	}

/*Function to retrieve thread details for an item*/
	function get_thread_on_object($article_id,$itemtype='articles'){

		$threadQry = "select th.id id, th.title title, th.created_on date from 	ex_thread th where th.content_id in ($article_id)
		and th.content_table='$itemtype'";

		foreach(exec_query($threadQry) as $threadres)
		{
				$threadArray['id']=$threadres['id'];
				$threadArray['title']=$threadres['title'];
				$threadArray['date']=$threadres['date'];
		}
			return $threadArray;
	}


    /*Function to make a link for tags*/
	function makeTagslink($tagid){
		if($tagid){
			$link="/library/search.htm";
		}
		return $link;
	}

	/*Function to display User's lastname*/
	function get_username($subid){
	$nameQry = "select fname from subscription where id = '$subid'";

		$fname = exec_query($nameQry);
		if(isset($fname)){
		return $fname;
		}
		else{
		return;
		}
	}


	/*Function to display Tags*/
	function displayTagsonly($type,$taginfo)
	{
		$index=0;
		$link='';
		if(is_array($taginfo)){
		foreach($taginfo as $tagkey=> $tagvalue)
		{
			$tag=strtoupper($tagvalue['tag']);
			$link=makelinks($tag,$type);
			if($index==0){
				$strtags.=$link;
			}
			else{
			    //$strtags.='class="searchSugg"'.','.$link;
				$strtags.='<a';
				$strtags.='" class="searchSugg"'.'>'.', '.'</a>';
				$strtags.=$link;
				//$strtags.=' ,'.$link;
			}
			$index++;
		}
		}
		return $strtags;
	}

	function displayTagsonDisshome($type,$taginfo)
	{
		$index=0;
		$link='';
		if(is_array($taginfo)){
		foreach($taginfo as $tagkey=> $tagvalue)
		{
			$tag=strtoupper($tagvalue['tag']);
			$link=makelinks($tag,$type);
			if($index==0){
				$strtags.=$link;
			}
			else{
			    //$strtags.='class="searchSugg"'.','.$link;
				//$strtags.='<a';
				//$strtags.='" class="searchSugg"'.'>'.' ,'.'</a>';
				//$strtags.=$link;
				$strtags.=',&nbsp;'.$link;
			}
			$index++;
		}
		}
		return $strtags;
	}
	/*Function to display Tags*/
	function displayTags($type,$taginfo){
		if ($taginfo!=""){
		echo "Tags:";
		}
		?>
		<span class="profilesug">
		<?php
		$index=0;
		if(is_array($taginfo)){
		foreach($taginfo as $tagkey=> $tagvalue)
		{
		  $tag=strtoupper($tagvalue['tag']);
		  $links=makelinks($tag,$type);
		  if($index==0){
			 echo $links;
		  }
		  else{
			 echo ", ".$links;
		  }
		 $index++;
		}
		}
		?>
		</span>
		<!--</div>-->
		<?php
	}


	# displays the all comments created against article/blog.
	function get_all_objects($strQuery){

		/*
		$strQuery="select S.fname,S.lname,P.poster_id,P.post_time,date_format(P.created_on,'%D %M %Y, %h:%i %p') created_on,P.updated_on,P.enabled,P.title,P.teasure from ex_post P,subscription S
where P.thread_id=$thread_id and P.poster_id=S.id order by P.created_on desc limit $offset,$limit";
		*/
		$strResult=exec_query($strQuery);

		if(isset($strResult)){
			return $strResult;
		}
		else{
			return;
		}

	}//end of function get_all_objects()

	function get_object_id($object_id,$object_type){

		$strQuery="select id from ex_thread where content_id=$object_id and content_table='$object_type'";

		$strResult=exec_query($strQuery);

		if(isset($strResult)){
			return $strResult;
		}
		else{
			return;
		}

	}//end of function get_thread_id()


	function make_ajax_pagination($divid,$link,$offset,$limit,$count,$item){

		if($count>$limit){
			if($offset > 0){

				$pageprev = $offset - $limit;//href='$link&pcount=$pageprev'
				$url=$link."&Pcount=$pageprev&count=$limit";
				$strPage="<b><a onclick=\"Javascript:preHttpRequest('$divid','$url');\" style='cursor:pointer;' ><span class='viewResults'> < </span></a></b>";
			}
			else{
				$strPage="&nbsp;<span class='leftArrow'> < </span>";
			}
			if($count>($offset+$limit)){
			 	$strPage.=" View Next $limit $item ";
			}
				else
			{
				$strPage.=" View Previous $limit $item ";
			}

			if($count>($offset+$limit)){
				$pagenext = $offset +$limit;//href='$link&pcount=$pagenext'
				$url=$link."&Pcount=$pagenext&count=$limit";
				$strPage.="<b><a onclick=\"Javascript:preHttpRequest('$divid','$url');\" style='cursor:pointer;' ><span class='viewResults'> > </span></a></b> ";
			}
			else{
				$strPage.="<span class='leftArrow'> > </span>&nbsp;";
			}
		}

		return $strPage;

	}//end of function function make_ajax_pagination()


	function make_pagination($link,$offset,$limit,$count,$item,$count_var="NULL"){

		if($count>$limit){
			if($offset > 0){
				$pageprev = $offset - $limit;
				$strPage="<b><a href='$link&";
				if($count_var=='read')
					$strPage.="r=$pageprev'><span class='viewResults'> < </span></a></b>";
				else if($count_var=='unread')
					$strPage.="u=$pageprev'><span class='viewResults'> < </span></a></b>";
				else
					$strPage.="pcount=$pageprev'><span class='viewResults'> < </span></a></b>";
			}
			else{
				$strPage="&nbsp;<span class='leftArrow'> < </span>";
			}
			if($count>($offset+$limit)){
			 	$strPage.=" View next $limit $item ";
			}
				else
			{
				$strPage.=" View previous $limit $item ";
			}

			if($count>($offset+$limit)){
				$pagenext = $offset +$limit;
				$strPage.="<b><a href='$link&";

				if($count_var=='read')
					$strPage.="r=$pagenext'><span class='viewResults'> > </span></a></b>";
				else if($count_var=='unread')
					$strPage.="u=$pagenext'><span class='viewResults'> > </span></a></b>";
				else
					$strPage.="pcount=$pagenext'><span class='viewResults'> > </span></a></b>";
			}
			else{
				$strPage.="<span class='leftArrow'> > </span>&nbsp;";
			}
		}

		return $strPage;

	}//end of function function make_pagination()
          // function for navigating record
	function navigation_information($offset,$limit,$count){
	 	if($count>($offset+$limit)){
	  		$strnav=($offset + '1')."".'-'."".($offset + $limit)."".'  '."". 'of' ."".'  '."".$count;

  	     } else {
		  $strnav=($offset + '1')."".'-'."".($count)."".'  '."". 'of' ."".'  '."".$count;
		 }
                 if($count=='0') {

		 	$strnav=($offset)."".'-'."".($count)."".'  '."". 'of' ."".'  '."".$count;

		 }
		 return  $strnav;
	 }
          // function for navigating record
	function is_moderator($subscription_id){
		$strQuery="select id from ex_moderator where subscription_id=$subscription_id";
		$getid=exec_query($strQuery,1);
		if(isset($getid['id'])){
			return 1;
		}else{
			return 0;
		}
	}//end of function is_moderator()

	function get_features($subscription_id){

		$strQuery="select feature_ids features from ex_moderator where subscription_id=$subscription_id";
		$strResult=exec_query($strQuery);
		$strModerator="";
		if(isset($strResult)){
			//return $strResult[0][features];
			$strFeatures=$strResult[0][features];
			$strFQuery="select intname from admin_features where id in ($strFeatures)";
			$strFResult=exec_query($strFQuery);

			if(isset($strFResult)){
				foreach($strFResult as $key => $val){
					$strModerator[$key]=$val[intname];
				}
				return $strModerator;

			}
			else{
				return 0;
			}
		}
		else{
			return 0;
		}
	}

	function findKeyValuePair($multiArray, $keyName, $value, $strict = false ){
		/* Doing this test here makes for a bit of redundant code, but
		 * improves the speed greatly, as it is not being preformed on every
		 * iteration of the loop.
		 */
		if (!$strict){
			foreach ($multiArray as $multiArrayKey => $childArray ){
				if (array_key_exists($keyName, $childArray ) && $childArray[$keyName] == $value ){
					return true;
				}
			}
		}
		else{
			foreach ($multiArray as $multiArrayKey => $childArray ){
				if (array_key_exists($keyName, $childArray ) && $childArray[$keyName] === $value ){
					return true;
				}
			}
		}

		return false;
	}



}//end of class Exchange


class Post extends Exchange_Element{
	var $thread_id;
	var $comment_id;
	var $subscription_id;
	var $title_id;
	var $body;
	var $poster_ip;

	# constructor
	function Post(){
		$this->comment_id="";
		$this->thread_id="";
		$this->subscription_id="";
		$this->title_id="";
		$this->body="";
		$this->poster_ip="";

	}

	function is_first_comment($object_id,$object_type){
		# CODE TO BE BUILD
	}//end of function is_first_comment()


	function get_comment($objPost){

		/*
		$strQuery="select P.title title,B.body body, from ex_post P, ex_post_content B
where P.id=$objPost->comment_id and B.post_id=P.id";
		*/

		$strQuery="select concat(S.fname,' ',S.lname) author,date_format(P.created_on,'On %M %D at %h:%i %p') date,P.title title,B.body body from ex_post P, ex_post_content B,subscription S where P.id=$objPost->comment_id and B.post_id=P.id and S.id=P.poster_id";
		$strResult=exec_query($strQuery,1);

		if(isset($strResult)){
			return $strResult;
		}
		else{
			return;
		}
	}

    function post_comment($objPost){
		global $REMOTE_ADDR,$TEASURE_COUNT, $lang;
		$content_table=$objPost['contenttable'];
		$pageName="article_template";
		build_lang($pageName);
		$objThread=new Thread();
		$objThread->thread_id=$objPost[thread_id];
		$subscription_id=$objPost[poster_id];
		$commentArray=$objPost;
		$date=date('Y-m-d H:i:s');
		$blog_status=$commentArray[blog];
		unset($commentArray[article]);
		unset($commentArray[blog]);
		unset($commentArray[comment_id]);
		unset($commentArray[replytype]);
		unset($commentArray[owner_id]);
		unset($commentArray[contenttable]);

		$commentArray[post_text]=mswordReplaceSpecialChars($commentArray[post_text]);
		$order = array("\r\n", "\n", "\r");
		$replace = ' <br />';
		$commentArray[post_text] = str_replace($order, $replace, $commentArray[post_text]);
		$order = array("\t");
		$replace = '&nbsp;&nbsp;&nbsp;&nbsp;';
		$commentArray[post_text] = str_replace($order, $replace, $commentArray[post_text]);

		$patterns = '/&acirc;€™/';
		$replacements = '&#039';
		$commentArray[post_text]=preg_replace($patterns, $replacements, $commentArray[post_text]);
		$commentArray[post_text] = strip_tags($commentArray[post_text],"<br />,<br>");
		$commentArray[title]=mswordReplaceSpecialChars($commentArray[title]);
		$patterns = '/&acirc;€™/';
		$replacements = '&#039';
		$commentArray[title]=preg_replace($patterns, $replacements, $commentArray[title]);
		$commentArray[title] = strip_tags($commentArray[title]);
		$commentArray[post_time]=$date;
		$commentArray[created_on]=$date;
		$commentArray[teasure]=substr($commentArray[post_text],0,$TEASURE_COUNT);
		$commentArray[teasure]=htmlentities($commentArray[teasure],ENT_QUOTES);
		$strBody=htmlentities($commentArray[post_text],ENT_QUOTES);
		unset($commentArray[post_text]);
		unset($commentArray[type]);
		unset($commentArray[Ptype]);

		$commentArray[approved]=0;
		$commentArray[suspended]=0;
		$commentArray[poster_ip]=$REMOTE_ADDR;
		$strId=insert_query('ex_post',$commentArray);
		if($strId>0){
			$commentText[post_id]=$strId;

			$commentText[body]=$strBody;

			insert_query('ex_post_content',$commentText);

			$commentCount=$objThread->count_all_comments($objThread->thread_id,1);
			$strThread[thread_posts]=$commentCount;
			$arrThread[id]=$objThread->thread_id;

			update_query('ex_thread',$strThread,$arrThread);

			if($objPost[type]=='replynquote'){
				$threadTitle=$objThread->get_thread($objThread);
				$event='ReplyDirectPost';
				$subId=$objPost[owner_id];
				$sender_id=$subscription_id;
				$resEmail=getAlertStatus($event,$subId);
				$sendEmail=sendAlert($subId,$sender_id,$resEmail,$event,$threadTitle[thread_id],$threadTitle[title]);
			}

			$url=$_SERVER[HTTP_REFERER];
			$urlArray=explode('&',$url);

			$strcount=0;

			foreach($urlArray as $key=>$val){
				if(strstr($val,'pcount')!=''){
					$strcount=$val;
				}
			}

			$count=0;

			$countArray=explode('=',$strcount);
			$comment_id=0;
			if($countArray[1]!="" && $countArray[1]!=0){
				$count=$countArray[1];
				$comment_id=$strId;
			}

			if($blog_status==1){
				$strHtml=$objThread->get_discussion_output($objThread->thread_id,$subscription_id,$count,"",1,1,$strId,$chk="",$chkmsg="",$content_table);
			}
			elseif(isset($objPost[article])){
				$strHtml="<div align='center' style='color:#990000; padding-left:17px;'>".$lang[pending_moderator_approval]."</div>";
			}
			else{
				$strHtml=$objThread->get_discussion_output($objThread->thread_id,$subscription_id,$count,"",0,1,$strId,$chk="",$chkmsg="",$content_table);
			}

			return $strHtml;
		}


	}//end of function post_comment()




/*Function to post a message to a friend*/
	function post_message($objPost){
		global $REMOTE_ADDR;

		$objPost['title']=htmlentities($objPost['title'],ENT_QUOTES);
		$messageArray=$objPost;
		$messageArray[msg_date]=date('Y-m-d H:i:s');
		$messageArray[ip]=$REMOTE_ADDR;
		$messageArray[enable]='0';
		unset($messageArray[private_msg_id]);
		unset($messageArray[text]);
                unset($messageArray[conv_ids]);

		$strId=insert_query('ex_private_message',$messageArray);

		if($strId>0){
			$messageText[private_msg_id]=$strId;
			$msgtext=htmlentities(mswordReplaceSpecialChars($_POST[text]),ENT_QUOTES);
			$bodyorder = array("\r\n", "\n", "\r");
		    $bodyreplace = '<br />';
		    $msgtext = str_replace($bodyorder,$bodyreplace,$msgtext);

			$messageText['text']=$msgtext;
			insert_query('ex_private_message_text',$messageText);
                       /*Code to update ex_message_conversation table */
			$convqry=exec_query("select message_id from ex_message_conversation where id='$objPost[conv_ids]'",1);
			$strconv[message_id]=$convqry['message_id']."".','."".$strId;
			$strconv[conv_date]=date('Y-m-d H:i:s');
			update_query('ex_message_conversation',$strconv,array('id'=>$objPost['conv_ids']));

			//$item[subscription_id]=$objPost[to_subscription_id];
			$item[item_id]=$objPost[conv_ids];
			$item[item_type]='5';
			$item[read_status]='unread';
			update_query('ex_item_status',$item,array('item_id'=>$objPost['conv_ids'],'item_type'=>'5','subscription_id'=>$objPost['to_subscription_id']));
			return $strId;
		}
	}//end of function post_message()


	function delete_thread_comment($thread_id,$comment_id)
	{
		$objThread=new Thread();
		$objThread->thread_id=$thread_id;
		$page="single_discussion";
		build_lang($page);
		$id=del_query('ex_post','id',$comment_id);
		if($id>0)
		{
			$strid=del_query('ex_post_content','post_id',$comment_id);
			$commentCount=$objThread->count_all_comments($objThread->thread_id,1);
			$strThread[thread_posts]=$commentCount;
			$arrThread[id]=$objThread->thread_id;
			update_query('ex_thread',$strThread,$arrThread);
			return true;
		}
		else
		{
			return false;
		}
	}
	function delete_comment($thread_id,$comment_id,$moderator_id,$is_blog=0,$content_table=NULL){
		global $lang;
		$objThread=new Thread();
		$objThread->thread_id=$thread_id;
		$page="single_discussion";
		build_lang($page);
		$id=del_query('ex_post','id',$comment_id);
		//$strid=del_query('ex_post_text','post_id',$comment_id);
		$strid=del_query('ex_post_content','post_id',$comment_id);

		if($id>0){
			$commentCount=$objThread->count_all_comments($objThread->thread_id,1);
			$strThread[thread_posts]=$commentCount;
			$arrThread[id]=$objThread->thread_id;
			update_query('ex_thread',$strThread,$arrThread);

			$msg= $lang[delete_done];
			if($is_blog==1){
				$htmlOutput=$objThread->get_discussion_output($thread_id,$moderator_id,0,$msg,1,0);
			}
			else{
				$htmlOutput=$objThread->get_discussion_output($thread_id,$moderator_id,0,$msg,0,0,$shift=0,$chk="",$chkmsg="",$content_table);
			}

		}
		else{
			$msg= $lang[delete_failed];
			if($is_blog==1){
				$htmlOutput=$objThread->get_discussion_output($thread_id,$moderator_id,0,$msg,1,1);
			}
			else{
				$htmlOutput=$objThread->get_discussion_output($thread_id,$moderator_id,0,$msg,0,1,$shift=0,$chk="",$chkmsg="",$content_table);
		}


		}

		return $htmlOutput;

	}//end of function delete_comment()

	function suspend_comment($thread_id,$comment_id,$moderator_id,$approved){
		global $lang,$HTPFX,$HTHOST;
		$page="single_discussion";
		build_lang($page);

		$strComment[suspended]='1';
		$strCommentId[id]=$comment_id;

		$strId=update_query('ex_post',$strComment,$strCommentId);

		// update thread post count
		$objThread=new Thread();
		$objThread->thread_id=$thread_id;
		$commentCount=$objThread->count_all_comments($objThread->thread_id,1);
		$strThread[thread_posts]=$commentCount;
		$arrThread[id]=$objThread->thread_id;
		update_query('ex_thread',$strThread,$arrThread);

		# url for reply button
		$url=$HTPFX.$HTHOST.'/community/Post.php?subscription_id='.$moderator_id;
		$url.='&thread_id='.$thread_id;
		$url.='&comment_id='.$comment_id;
		$url.='&type=replynquote';
		$url.='&div_id=txtpost';

		# url for delete button
		$urldelete=$HTPFX.$HTHOST.'/community/Save.php';
		$urldelete.='?Ptype=delete';
		$urldelete.='&subscription_id='.$moderator_id;
		$urldelete.='&comment_id='.$comment_id;
		$urldelete.='&thread_id='.$thread_id;

		# url for suspend button
		$urlapprove=$HTPFX.$HTHOST.'/community/Save.php';
		if($approved==0){
			$urlapprove.='?Ptype=approve';
		}
		else{
			$urlapprove.='?Ptype=disapprove';
		}

		$urlapprove.='&subscription_id='.$moderator_id;
		$urlapprove.='&comment_id='.$comment_id;
		$urlapprove.='&thread_id='.$thread_id;


		if($strId>0){
			$urlsuspend=$HTPFX.$HTHOST.'/community/Save.php';
			$urlsuspend.='?Ptype=sustain';
			$urlsuspend.='&subscription_id='.$moderator_id;
			$urlsuspend.='&comment_id='.$comment_id;
			$urlsuspend.='&thread_id='.$thread_id;
			$urlsuspend.='&approved='.$approved;

			$urlapprove.='&suspended=1';

			$htmlOutput="<font color='red'>$lang[suspend_done]</font><br/>";
			$htmlOutput.= "<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/Replyimg.gif' align='bottom' onclick=\"Javascript:preHttpRequest('txtpost','$url')\"/>&nbsp;";
			/*if($approved==0){
				$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/approve.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlapprove') \" />&nbsp;";
			}
			else{
			$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/disapprove.gif'  align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlapprove') \" />&nbsp;";
			}
			*/

			$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/sustain.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlsuspend',100)\" />&nbsp;<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/delete.gif'align='bottom' onclick=\"Javascript:preHttpRequest('commentposting','$urldelete',100)\" />";
			$htmlOutput.="<br/><br/>";
		}
		else{
			$urlsuspend=$HTPFX.$HTHOST.'/community/Save.php';
			$urlsuspend.='?Ptype=sustain';
			$urlsuspend.='&subscription_id='.$moderator_id;
			$urlsuspend.='&comment_id='.$comment_id;
			$urlsuspend.='&thread_id='.$thread_id;
			$urlsuspend.='&approved='.$approved;

			$urlapprove.='&suspended=0';

			$htmlOutput= "<font color='red'>$lang[suspend_failed]</font><br/>";
			$htmlOutput.= "<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/Replyimg.gif'  align='bottom' onclick=\"Javascript:preHttpRequest('txtpost','$url')\"/>&nbsp;";
			/*if($approved==0){
				$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/approve.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlapprove') \" />&nbsp;";
			}
			else{
				$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/disapprove.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlapprove') \" />&nbsp;";
			}*/

			$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/suspend.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlsuspend',100)\" />&nbsp;<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/delete.gif' align='bottom' onclick=\"Javascript:preHttpRequest('commentposting','$urldelete',100)\" />";
			$htmlOutput.="<br/><br/>";
		}

		return $htmlOutput;
	}//end of function suspend_comment()

	function sustain_comment($thread_id,$comment_id,$moderator_id,$approved){
		global $lang,$HTPFX,$HTHOST;
		$page="single_discussion";
		build_lang($page);
		$strComment[suspended]='0';
		$strCommentId[id]=$comment_id;

		$strId=update_query('ex_post',$strComment,$strCommentId);
		// update thread post count
		$objThread=new Thread();
		$objThread->thread_id=$thread_id;
		$commentCount=$objThread->count_all_comments($objThread->thread_id,1);
		$strThread[thread_posts]=$commentCount;
		$arrThread[id]=$objThread->thread_id;
		update_query('ex_thread',$strThread,$arrThread);

		# url for reply button
		$url=$HTPFX.$HTHOST.'/community/Post.php?subscription_id='.$moderator_id;
		$url.='&thread_id='.$thread_id;
		$url.='&comment_id='.$comment_id;
		$url.='&type=replynquote';
		$url.='&div_id=txtpost';

		# url for delete button
		$urldelete=$HTPFX.$HTHOST.'/community/Save.php';
		$urldelete.='?Ptype=delete';
		$urldelete.='&subscription_id='.$moderator_id;
		$urldelete.='&comment_id='.$comment_id;
		$urldelete.='&thread_id='.$thread_id;

		# url for suspend button
		$urlapprove=$HTPFX.$HTHOST.'/community/Save.php';
		if($approved==0){
			$urlapprove.='?Ptype=approve';
		}
		else{
			$urlapprove.='?Ptype=disapprove';
		}

		$urlapprove.='&subscription_id='.$moderator_id;
		$urlapprove.='&comment_id='.$comment_id;
		$urlapprove.='&thread_id='.$thread_id;


		if($strId>0){
			$urlsuspend=$HTPFX.$HTHOST.'/community/Save.php';
			$urlsuspend.='?Ptype=suspend';
			$urlsuspend.='&subscription_id='.$moderator_id;
			$urlsuspend.='&comment_id='.$comment_id;
			$urlsuspend.='&thread_id='.$thread_id;
			$urlsuspend.='&approved='.$approved;
			$urlapprove.='&suspended=0';
			$htmlOutput="<font color='red'>$lang[sustain_done]</font><br/>";
			$htmlOutput.= "<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/Replyimg.gif'
			 align='bottom' onclick=\"Javascript:preHttpRequest('txtpost','$url')\"/>&nbsp;";
			/*if($approved==0){
				$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/approve.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlapprove') \" />&nbsp;";
			}
			else{
			$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/disapprove.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlapprove') \" />&nbsp;";
			}
			*/

			$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/suspend.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlsuspend',100)\" />&nbsp;<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/delete.gif' align='bottom' onclick=\"Javascript:preHttpRequest('commentposting','$urldelete',100)\" />";
			$htmlOutput.="<br/><br/>";
		}
		else{
			$urlsuspend=$HTPFX.$HTHOST.'/community/Save.php';
			$urlsuspend.='?Ptype=sustain';
			$urlsuspend.='&subscription_id='.$moderator_id;
			$urlsuspend.='&comment_id='.$comment_id;
			$urlsuspend.='&thread_id='.$thread_id;
			$urlsuspend.='&approved='.$approved;

			$urlapprove.='&suspended=1';

			$htmlOutput= "<font color='red'>$lang[sustain_failed]</font><br/>";
			$htmlOutput.= "<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/Replyimg.gif'  align='bottom' onclick=\"Javascript:preHttpRequest('txtpost','$url')\"/>&nbsp;";
			/*if($approved==0){
				$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/approve.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlapprove') \" />&nbsp;";
			}
			else{
				$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/disapprove.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlapprove') \" />&nbsp;";
			}
			*/
			$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/sustain.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlsuspend',100)\" />&nbsp;<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/delete.gif' align='bottom' onclick=\"Javascript:preHttpRequest('commentposting','$urldelete',100)\" />";
			$htmlOutput.="<br/><br/>";

		}

		return $htmlOutput;

	}//end of function sustain_comment()



	function approve_comment($thread_id,$comment_id,$moderator_id,$suspend){
		global $lang;
		$strComment[approved]='1';
		$strCommentId[id]=$comment_id;
		$strId=update_query('ex_post',$strComment,$strCommentId);
		$page="single_discussion";
		build_lang($page);

		# url for reply button
		$url='Post.php?subscription_id='.$moderator_id;
		$url.='&thread_id='.$thread_id;
		$url.='&comment_id='.$comment_id;
		$url.='&type=replynquote';
		$url.='&div_id=txtpost';
		# url for delete button
		$urldelete='Save.php';
		$urldelete.='?Ptype=delete';
		$urldelete.='&subscription_id='.$moderator_id;
		$urldelete.='&comment_id='.$comment_id;
		$urldelete.='&thread_id='.$thread_id;

		# url for suspend button
		$urlsuspend='Save.php';
		if($suspend==0){
			$urlsuspend.='?Ptype=suspend';
		}
		else{
			$urlsuspend.='?Ptype=sustain';
		}
		$urlsuspend.='&subscription_id='.$moderator_id;
		$urlsuspend.='&comment_id='.$comment_id;
		$urlsuspend.='&thread_id='.$thread_id;
		$urlsuspend.='&suspended='.$suspend;

		if($strId>0){
			$urlapprove='Save.php';
			$urlapprove.='?Ptype=disapprove';
			$urlapprove.='&subscription_id='.$moderator_id;
			$urlapprove.='&comment_id='.$comment_id;
			$urlapprove.='&thread_id='.$thread_id;
			$urlapprove.='&suspended='.$suspend;

			$htmlOutput="<font color='red'>$lang[approve_done]</font><br/>";
			$htmlOutput.= "<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/Replyimg.gif' align='bottom' onclick=\"Javascript:preHttpRequest('txtpost','$url')\"/>&nbsp;<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/disapprove.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlapprove') \" />&nbsp;";
			if($suspend==0){
			$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/suspend.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlsuspend')\" />";
			}
			else{
			$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/sustain.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlsuspend')\" />";
			}
			$htmlOutput.="&nbsp;<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/delete.gif' align='bottom' onclick=\"Javascript:preHttpRequest('commentposting','$urldelete')\" />";
			$htmlOutput.="<br/><br/>";
		}
		else{
			$urlapprove='Save.php';
			$urlapprove.='?Ptype=approve';
			$urlapprove.='&subscription_id='.$moderator_id;
			$urlapprove.='&comment_id='.$comment_id;
			$urlapprove.='&thread_id='.$thread_id;
			$urlapprove.='&suspended='.$suspend;
			$htmlOutput= "<font color='red'>$lang[approve_failed]</font><br/>";
			$htmlOutput.= "<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/Replyimg.gif' align='bottom' onclick=\"Javascript:preHttpRequest('txtpost','$url')\"/>&nbsp;<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/approve.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlapprove') \" />&nbsp;";
			if($suspend==0){
			$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/suspend.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlsuspend')\" />";
		}
		else{
			$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/sustain.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlsuspend')\" />";
			}
			$htmlOutput.="&nbsp;<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/delete.gif' align='bottom' onclick=\"Javascript:preHttpRequest('commentposting','$urldelete')\" />";
			$htmlOutput.="<br/><br/>";

		}

		return $htmlOutput;

	}//end of function approve_comment()
	function disapprove_comment($thread_id,$comment_id,$moderator_id,$suspend){
		global $lang;
		$page="single_discussion";
		build_lang($page);
		$strComment[approved]='0';
		$strCommentId[id]=$comment_id;
		$strId=update_query('ex_post',$strComment,$strCommentId);
		# url for reply button
		$url='Post.php?subscription_id='.$moderator_id;
		$url.='&thread_id='.$thread_id;
		$url.='&comment_id='.$comment_id;
		$url.='&type=replynquote';
		$url.='&div_id=txtpost';

		# url for delete button
		$urldelete='Save.php';
		$urldelete.='?Ptype=delete';
		$urldelete.='&subscription_id='.$moderator_id;
		$urldelete.='&comment_id='.$comment_id;
		$urldelete.='&thread_id='.$thread_id;

		# url for suspend button
		$urlsuspend='Save.php';
		if($suspend==0){
			$urlsuspend.='?Ptype=suspend';
		}
		else{
			$urlsuspend.='?Ptype=sustain';
		}

		$urlsuspend.='&subscription_id='.$moderator_id;
		$urlsuspend.='&comment_id='.$comment_id;
		$urlsuspend.='&thread_id='.$thread_id;
		$urlsuspend.='&suspended='.$suspend;

		if($strId>0){
			$urlapprove='Save.php';
			$urlapprove.='?Ptype=approve';
			$urlapprove.='&subscription_id='.$moderator_id;
			$urlapprove.='&comment_id='.$comment_id;
			$urlapprove.='&thread_id='.$thread_id;
			$urlapprove.='&suspended='.$suspend;

			$htmlOutput= "<font color='red'>$lang[disapprove_done]</font><br/>";
			$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/Replyimg.gif'  align='bottom' onclick=\"Javascript:preHttpRequest('txtpost','$url')\"/>&nbsp;<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/approve.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlapprove')\" />&nbsp;";
			if($suspend==0){
				$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/suspend.gif'  align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlsuspend')\" />";
			}
			else{
				$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/sustain.gif'  align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlsuspend')\" />";
			}


			$htmlOutput.="&nbsp;<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/delete.gif' align='bottom' onclick=\"Javascript:preHttpRequest('commentposting','$urldelete')\" />";
			$htmlOutput.="<br/><br/>";

		}
		else{
			$urlapprove='Save.php';
			$urlapprove.='?Ptype=disapprove';
			$urlapprove.='&subscription_id='.$moderator_id;
			$urlapprove.='&comment_id='.$comment_id;
			$urlapprove.='&thread_id='.$thread_id;
			$urlapprove.='&suspended='.$suspend;

			$htmlOutput= "<font color='red'>$lang[disapprove_failed]</font><br/>";
			$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/Replyimg.gif'  align='bottom' onclick=\"Javascript:preHttpRequest('txtpost','$url')\"/>&nbsp;<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/disapprove.gif' width='86' height='17' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlapprove')\" />&nbsp;";
			if($suspend==0){
				$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/suspend.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlsuspend')\" />";
			}
			else{
				$htmlOutput.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/sustain.gif' align='bottom' onclick=\"Javascript:preHttpRequest($comment_id,'$urlsuspend')\" />";
			}


			$htmlOutput.="&nbsp;<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/delete.gif' align='bottom' onclick=\"Javascript:preHttpRequest('commentposting','$urldelete')\" />";
			$htmlOutput.="<br/><br/>";
		}

		return $htmlOutput;
	}//end of function approve_comment()

}//end of class Post

class Thread extends Exchange_Element{
	# Declaration of memeber variables
	var $thread_id;
	var $article_id;
	var $subscription_id;
	var $thread_title;
	var $thread_teasure;
	var $thread_body;

	# constructor
	# for initializing member variables
	function Thread(){
		$this->thread_id="";
		$this->article_id="";
		$this->subscription_id="";
		$this->thread_title="";
		$this->thread_teasure="";
		$this->thread_body="";
		$this->subscription_id="";
	}

	function get_parent_comment($id){
		$strQuery="select concat(S.fname,' ',S.lname) author, date_format(P.created_on,'On %M %D at %h:%i %p') date,B.body from ex_post P,ex_post_content B,subscription S where S.id=P.poster_id and P.id='$id' and B.post_id=P.id";
		$strResult=exec_query($strQuery,1);
		if(!$strResult){
			return;
		}
		return $strResult;
	}


	//get contributor id from subscription id
	function get_contributor_id($subid){
		$strQuery="select contributor_id from ex_contributor_subscription_mapping where subscription_id=$subid";

		$strResult=exec_query($strQuery,1);
		if(isset($strResult)){
			return $strResult[contributor_id];
		}
		else{
			return;
		}
	}//end of get_contributor_id($subid)

	function latest_post($threadid){
		$strQuery="select date_format(P.created_on,'%D %M %Y, %h:%i %p') date from ex_post P where P.thread_id=$threadid and P.suspended='0'";
		$strResult=exec_query($strQuery,1);
		return $strResult[date];
	}

	# get comments output
	function get_discussion_output($threadid,$subscription_id,$pcount,$msg,$is_blog=0,$latest=0,$shift=0,$chk="",$chkmsg="",$content_table=""){
		global $HTPFX,$HTHOST,$EXNG_PAGE_COUNT,$page_config,$lang;
		$page="single_discussion";
		build_lang($page);
		$objThread=new Thread();
		$objThread->thread_id=$threadid;
		# friends class object
		$objFriends=new friends();
		# get user's friends
		$friends=$objFriends->get_friend_list($subscription_id,'','','');

		# get comments those are reported by user
		$reportComments=$objFriends->CheckReportAbuse($subscription_id);
		$_GET[pcount]=$pcount;
		# get features
		$permissions=$objThread->get_features($subscription_id);

		if($permissions && in_array('mod_comment',$permissions)){
			$comment_count=$objThread->count_all_comments($objThread->thread_id,0);
		}
		else{
			$comment_count=$objThread->count_all_comments($objThread->thread_id,1);
		}

		$rand=rand();
		$urltop ="$HTPFX$HTHOST/community/Post.php?subscription_id=".$subscription_id;
		$urltop.="&thread_id=$objThread->thread_id";
		$urltop.="&comment_id=$rand";
		$urltop.="&div_id=txtpost";

		if($is_blog==1){
			$urltop.="&type=addcomment";
			$urltop.="&blog=1";
		}elseif($content_table=="slideshow"){
			$urltop.="&content_table=$content_table";
		}
	$html="
		<table align='left' cellpadding='0' cellspacing='0' width='660px' border='0' style='clear:both;'>";
	$html.="
					<tr>
						<td colspan='2' valign='top'>
							<table width='100%' cellspacing='0' cellpadding='0' border='0' align='left' >
								<tr id='commentcount'>";

	if($comment_count>0) {
		$html.="
									<td class='Article14Comments' style='padding-left:2px;'>$comment_count</td>";
	}else{
		$html.="
									<td class='ArticleComments' style='padding-left:2px; padding-right:2px; width:20px;'>NO </td>";
	}

	if($comment_count>1) {

		if($is_blog==1){
			$html.="<td width='400' class='ArticleComments'>COMMENTS ON THIS BLOG </td>";
		}
		else
		{
			if ($content_table=='mvtv')
			{
				$html.="<td class='ArticleComments'> COMMENTS ON THIS VIDEO </td>";
			}elseif($content_table=='slideshow'){
				$html.="<td class='ArticleComments'> COMMENTS ON THIS SLIDESHOW </td>";
			}
			else
			{

			$html.="<td class='ArticleComments'> $lang[comments_article] </td>";
			}
		}
	}
	else
	{
		if($is_blog==1){
			$html.="<td class='ArticleComments'> COMMENT ON THIS BLOG </td>";
		}
		else
		{
			if ($content_table=='mvtv')
			{
				$html.="<td class='ArticleComments'> COMMENT ON THIS VIDEO </td>";
			}elseif($content_table=='slideshow'){
				$html.="<td class='ArticleComments'> COMMENT ON THIS SLIDESHOW </td>";
			}
			else
			{
			$html.="<td class='ArticleComments'> $lang[comment_article] </td>";
			}
		}
	}
	if($is_blog)
	{
		if($comment_count>0){
			$html.="<td class='ArticleComments'>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td style='cursor: pointer; padding-left:0px; padding-top:7px;' onclick='hidecomments(\"hide\");'><div id='hidecomments'><span class='HideShowComments' >Hide comments</span></div><div id='showcomments' style='display:none'><span class='HideShowComments'>Show comments</span></div></td>";
		}
	}else
	{
		$chkmsg_prev=$chkmsg;
		if(!$chk)
		{
			$exchange_prevquery="select ebs.subscription_id,ebs.value,ebs.blockservice_id,es.serviceid from
						ex_blockservices ebs,
						ex_services es
						where ebs.blockservice_id=es.id
						and ebs.subscription_id='$subscription_id' and 	ebs.value='on' and es.serviceid='comment_posts'";
			$exchange_prevresult=exec_query($exchange_prevquery,1);
			if(count($exchange_prevresult)>0){
				$chk='true';
			}
		}

if($chk){
$html.="
									<td><img src='$IMG_SERVER/images/community_images/Post-a-comment.gif' alt='xyz' align='right' style='padding-bottom:7px; cursor:pointer' onclick=\"Javascript:checkprevilages('txtpost','$urltop',$chk);\">
									</td>";
}
else
{
$html.="
									<td><img src='$IMG_SERVER/images/community_images/Post-a-comment.gif' alt='xyz' align='right' style='padding-bottom:7px; cursor:pointer' onclick=\"Javascript:preHttpRequest('txtpost','$urltop',650);\">
									</td>";
}

	 }
							$html.="</tr>
							</table>
						</td>
					</tr>";

    if($msg!=""){
		$html.="
					<tr >
						<td colspan='2' class='Discussioncomments'>
							<center><font color='red'>$msg</font></center>
						</td>
					</tr>
		";
	}

	$html.="<tbody id='hide'>";
	if($comment_count>0){
		if($permissions && in_array('mod_comment',$permissions)){
			$commentsArray=$objThread->get_all_comments($objThread,'discussion','body',$_GET[pcount],$EXNG_PAGE_COUNT,0,$shift);
		}
		else{
			$commentsArray=$objThread->get_all_comments($objThread,'discussion','body',$_GET[pcount],$EXNG_PAGE_COUNT,1,$shift);
		}


		if(count($commentsArray)==0 && $_GET[pcount]>0){
			$_GET[pcount]-=25;
			$commentsArray=$objThread->get_all_comments($objThread,'discussion','body',$_GET[pcount],$EXNG_PAGE_COUNT,$shift);
		}
		# get list of all professors
		$strProfessors=$objThread->get_all_professors();

		foreach($commentsArray as $key=>$val){
		$str=$val[postid];
			$html.="<tr id='cmt$val[postid]'>
						<td colspan='2' height='2' valign='bottom'>&nbsp;
							<img src='$IMG_SERVER/images/community_images/hr.gif' border='0' width='100%' vspace='0' height='1px' />
						</td>
					</tr>

					<tr>
						<td valign='top' bgcolor='#eceaeb'>
							<table width='170px' border='0' cellspacing='0' cellpadding='0'>
								<tr>
									<td class='comment_heading' style='padding-top:10px; padding-left:6px;'>";
			$name="$val[fname]  $val[lname]";
                        $author=ucwords(strtolower($name));

			# comment author is also thread author

            $html.="
									<a href='".$page_config['profile']['URL']."?userid=$val[subid]'>$author  </a>
									<br />
									<span id='viewprofile' class='Discussioncomments'>";

			$randd=rand();
			$urlRequest="$HTPFX$HTHOST/community/Save.php";
			$urlRequest.="?Ptype=request";
			$urlRequest.="&sender_id=$subscription_id";
			$urlRequest.="&receiver_id=$val[subid]";


            if($val[subid]!=$subscription_id && !$objThread->findKeyValuePair($friends,'subid',$val[subid],false)){
				$html.="
									<div style='padding-left:0px;' class='Discussioncomments' id='".$randd."'></div>";
				$html.="
									<div align='left' id='professorView1'>
										<a  style='cursor:pointer' onclick=\"Javascript:preHttpRequest('$randd','$urlRequest',170);\" >Add to Friends</a>
									</div>";
					    }
                $html.="
									<div id='professorView1'><a href='".$page_config[search][URL]."?q=&type=4&userid=$val[subid]'>View Exchanges</a>
									</div>";

				if(is_msg_allowed($val[subid],$subscription_id)=='true'){

					$author=ucwords(strtolower($val[fname]));
                    $html.="
									<div id='professorView1'><a href='".$page_config[compose][URL]."?&from=Discussion.htm&thid=$objThread->thread_id&to=$val[subid]' style='cursor:pointer' >Send  $author a message</a>
									</div>";
				}
				if($objThread->findKeyValuePair($strProfessors,'author_id',$val[subid],false)){

			$html.="<div  style='padding-bottom:10px;padding-left:10px; padding-top:2px;'> <img src='$IMG_SERVER/images/community_images/professorWithLIne.gif'/></div>";
			}

				$html.="
									</span>
									</td>
								</tr>
							</table>
						</td>
						<td>
							<table width='490' border='0' cellspacing='0' cellpadding='0'>
								<tr>";

			$urlabuse="$HTPFX$HTHOST/community/Save.php?subscription_id=$subscription_id";
			$urlabuse.="&thread_id=$objThread->thread_id";
			$urlabuse.="&comment_id=$val[postid]";
			$urlabuse.="&reciever_id=$val[subid]";
			$urlabuse.="&Ptype=reportabuse";
			$urlabuse.="&item=discussion";
			$urlabuse.="&div_id=txtAbuse$val[postid]";
			if($is_blog==1){
				$urlabuse.="&pagename=blog_comment&strAttribute=blog_id=$objThread->thread_id";
			}else{
				$urlabuse.="&pagename=single_discussion&strAttribute=thid=$objThread->thread_id";
			}
			//echo "<br>Abouse:".$urlabuse;
			$teasure=wordwrap(html_entity_decode($val[teasure]), 40, "\n",true);
			$title=wordwrap($val[title], 30, "\n",true);

            $html.="
									<td style='margin-left:0px;'></td>
									<td valign='top' style='padding-top:10px;'>
										<table border='0' width='100%' cellspacing='0' cellpadding='0'>";
			$html.="						<TR>";
			if($title!=""){
			$html.="							<td class='Article1' style='margin-left:0px;' valign='top' width='70%' >$title</td>";
			}
			else{
			$html.="							<td	style='margin-left:0px;' valign='top' width='70%'><span  class='ArticleTodayDate'> ";
				$html.=$objThread->check_date($val[date]);
				$html.="						</span></td>	";
			}
			$html.="									<td id='Discussion-author3'
 valign='top' align='right' width='30%'>";

			if($reportComments==NULL){
				$reportComments=array();
			}
			if (isset($reportComments) && ($subscription_id!=$val[subid]) && (!in_array($val[postid],$reportComments))){
			//if(($subscription_id > 0) && ($postval['subid']!=$subscription_id) && (!in_array($commentid,$reportComments))){
				$html.="<div align='right' id='abuse$val[postid]'><a onclick=\"Javascropt:preHttpRequest('abuse$val[postid]','$urlabuse',200);\" style='cursor:pointer; font-size:11px; color:#0486B7; letter-spacing:0.5px; font:Arial, Helvetica, sans-serif;'>Report Abuse</a></div>";
			}
												$html.="</td>
											</tr>
										</table>";
			if($title!=""){
				$html.="								<span style='margin-left:10px;' class='ArticleTodayDate'> ";
            $html.=$objThread->check_date($val[date]);
				$html.="						</span> <br />	";
			}
			$parentDate="";
			$parentauthor="";
			$parentBody="";

			if($val[pcomment_id]!=0){
				$parentComment=$objThread->get_parent_comment($val[pcomment_id]);
				$parentDate=$parentComment[date];
				$parentAuthor=$parentComment[author]." wrote: ";
				$parentBody=html_entity_decode($parentComment[body]);
				
			}
			$html.="

											<div style='padding-top:2px;padding-bottom:2px; padding-left:10px;'class='Article_normal'><img src='$IMG_SERVER/images/community_images/hr.gif' width='100%' height='1px' /></div>
											<div class='Article_normal'style='padding-left:10px;'>$teasure</div><br>";
											if($parentBody!=""){
												$html.="<table border='0' width='100%'>";
												$html.="<TR><TD width='10%'>&nbsp;</TD><TD width='90%'><div class='Article_normal'><img src='$IMG_SERVER/images/community_images/hr.gif' width='100%' height='1px' /></div><div class='Article_normal'><i>$parentDate</i></div></TD></TR>";
												$html.="<TR><TD width='10%'>&nbsp;</TD><TD width='90%'><div class='Article_normal'><i>$parentAuthor</i></div></TD></TR>";
												$html.="<TR><TD width='10%'>&nbsp;</TD><TD width='90%'><div class='Article_normal'><i>$parentBody</i></div></TD></TR>";
												$html.="</table>";
											}
											$html.="
									<br></td>
								</tr>
								<tr>
									<td style='margin-left:10px;'>&nbsp;
									</td>
									<td class='Article' style='margin-left:10px;' >
										<div align='right' id='$val[postid]'>";
			$url="$HTPFX$HTHOST/community/Post.php?subscription_id=$subscription_id";
			$url.="&thread_id=$objThread->thread_id";
			$url.="&comment_id=$val[postid]";
			$url.="&owner_id=$val[subid]";
			$url.="&pcomment_id=$val[postid]";
			$url.="&div_id=txtpost";
			if($is_blog==1){
				$url.="&type=addcomments";
				$url.="&blog=1";
			}elseif($content_table=="slideshow"){
				$url.="&content_table=$content_table";
			}
			else{
				$url.="&type=replynquote";
			}


			/*$html.="
										<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/Replyimg.gif' width='70' height='17' align='bottom' onclick=\"Javascript:preHttpRequest('txtpost','$url');\">&nbsp;";*/
			if($chk)
			{
				$html.="
										<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/Replyimg.gif'
										 align='bottom' onclick=\"Javascript:checkprevilages('txtpost','$url',$chk);\">&nbsp;";
			}
			else
			{
				$html.="
										<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/Replyimg.gif'  align='bottom' onclick=\"Javascript:preHttpRequest('txtpost','$url');\">&nbsp;";
			}
			# if user is moderator
			if($permissions && in_array('mod_comment',$permissions)){

				$urldelete="$HTPFX$HTHOST/community/Save.php";
				$urldelete.="?Ptype=delete";
				$urldelete.="&subscription_id=$subscription_id";
				$urldelete.="&comment_id=$val[postid]";
				$urldelete.="&thread_id=$objThread->thread_id";
				if($is_blog==1){
					$urldelete.="&blog=1";
				}
				elseif($content_table=="slideshow"){
					$urldelete.="&content_table=$content_table";
				}


				$urlsuspend="$HTPFX$HTHOST/community/Save.php";

				if($val[suspended]==0){
					$urlsuspend.="?Ptype=suspend";
				}
				else{
					$urlsuspend.="?Ptype=sustain";
				}

				$urlsuspend.="&subscription_id=$subscription_id";
				$urlsuspend.="&comment_id=$val[postid]";
				$urlsuspend.="&thread_id=$objThread->thread_id";
				$urlsuspend.="&approved=$val[approved]";

				$urlapprove="$HTPFX$HTHOST/community/Save.php";
				if($val[approved]=='0'){
					$urlapprove.="?Ptype=approve";
				}
				else{
					$urlapprove.="?Ptype=disapprove";
				}
				$urlapprove.="&subscription_id=$subscription_id";
				$urlapprove.="&comment_id=$val[postid]";
				$urlapprove.="&thread_id=$objThread->thread_id";
				$urlapprove.="&suspended=$val[suspended]";

				if($val[suspended]==0){
					$html.="
										<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/suspend.gif' align='bottom' onclick=\"Javascript:preHttpRequest('$val[postid]','$urlsuspend',100);\">";
				}
				else{
					$html.="
										<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/sustain.gif' align='bottom' onclick=\"Javascript:preHttpRequest('$val[postid]','$urlsuspend',100);\">";
				}
				$html.=
										"&nbsp;<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/delete.gif' align='bottom' onclick=\"Javascript:preHttpRequest('commentposting','$urldelete',100);\">";
				}//end of moderator check

				$html.="</div>
						</td>
					</tr>
				</table>
			</td>
        </tr>";
		}//end of loop

		$html.="<input type='hidden' id='post_id' value='cmt$str' />";

	}

    $html.="

        <tr>
           <td colspan='2' bgcolor='#ffffff' align='left'><img src='$IMG_SERVER/images/community_images/hr.gif' width='100%' height='1px' />
		   </td>

        </tr>
         <tr id='txtpostform'>
			<td colspan='2'>
			<div style='padding-left:10px;' id='txtpost'></div>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td >
				<table width='100%' border='0' cellspacing='0' cellpadding='0'>
					<tr>";

	$rand=rand();
	$urlbottom ="$HTPFX$HTHOST/community/Post.php?subscription_id=$subscription_id";
	$urlbottom.="&thread_id=$objThread->thread_id";
	$urlbottom.="&comment_id=$rand";
	$urlbottom.="&div_id=txtpost";
    if($is_blog==1){
		$urlbottom.="&type=addcomment";
		$urlbottom.="&blog=1";
	}elseif($content_table=="slideshow"){
		$urlbottom.="&content_table=$content_table";
	}


	$html.="
						<td scope='col'>";
        if($comment_count>0 || $is_blog==1) {

if($chk){
$html.="
									<div  align='right'><img src='$IMG_SERVER/images/community_images/Post-a-comment.gif' alt='xyz' align='right' style='padding-bottom:7px; cursor:pointer' onclick=\"Javascript:checkprevilages('txtpost','$urlbottom',$chk);\">
									</div>";
}
else
{
$html.="
							<div  align='right'>
							<img src='$IMG_SERVER/images/community_images/Post-a-comment.gif' onclick=\"Javascript:preHttpRequest('txtpost','$urlbottom',650);\"   style='padding-top:10px; cursor:pointer' /></div>";}

		}
		$html.="
						</td>
					</tr>
				</table>
			</td>
        </tr>
		<tr>
			<td>&nbsp;</td>
			<td >&nbsp;</td>
			</tr>
			<tr>
           <td >&nbsp;</td>
           <td class='viewResults'>";

		   if($is_blog==1){
		   		$html.=$objThread->make_pagination("$HTPFX$HTHOST/community/blog_comments.htm?blog_id=$threadid",$pcount,$EXNG_PAGE_COUNT,$comment_count,'Comments');
		   }
		   else{
				$html.=$objThread->make_pagination("$HTPFX$HTHOST/community/Discussion.htm?thid=$threadid",$pcount,$EXNG_PAGE_COUNT,$comment_count,'Comments');
			}

           $html.="
		   </td>
        </tr>";
		$html.="</tbody>";
		$html.="
		</table>
		";
		return $html;

	}//end function get_discussion_output()


	# get list of all professors
	function get_all_professors(){
		$strProfessor;
		/*
		$strQuery="select S.id author_id,concat(S.fname,' ',S.lname) author from subscription S,ex_thread T
where S.id=T.author_id and T.is_user_blog=0";
		*/

		$strQuery="select distinct(CSM.subscription_id) author_id,concat(S.fname,' ',S.lname) author
from articles A, subscription S,ex_contributor_subscription_mapping CSM
where A.contrib_id=CSM.contributor_id and CSM.subscription_id=S.id
order by author asc";

		$strResult=exec_query($strQuery);
		if(isset($strResult)){
			$index=0;
			foreach($strResult as $key=>$val){
				$strProfessor[$index][author_id]=$val[author_id];
				$strProfessor[$index][author]=$val[author];
				$index++;
			}
			return $strProfessor;
		}
		else{
			return 0;
		}
	}

	# manage read unread states
	function manage_states($subid,$itemid,$itemtype,$state){
		$strRead;
		$strQuery="select id,read_status from ex_item_status where subscription_id=$subid and item_id=$itemid and item_type=$itemtype";

		$strResult=exec_query($strQuery,1);

		if(isset($strResult) && count($strResult)>0){

			if($strResult[read_status]!=$state){

				$strRead[read_status]=$state;
				$strId[id]=$strResult[id];

				if($state=='read'){
					$strRead[read_on]=date('Y-m-d H:i:s');
				}
				else{
					$strRead[read_on]=NULL;
				}

				$id=update_query('ex_item_status',$strRead,$strId);
				if($id==1){
					return 1;
				}
				else{
					return 0;
				}
			}
			else{
				return 1;
			}
		}
		else{
			unset($strRead);
			$strRead[subscription_id]=$subid;
			$strRead[item_id]=$itemid;
			$strRead[item_type]=$itemtype;
			$strRead[read_status]=$state;

			if($state=='read'){
				$strRead[read_on]=date('Y-m-d H:i:s');
			}
			else{
				$strRead[read_on]=NULL;
			}

			$id=insert_query('ex_item_status',$strRead);
			if($id>0){
				return 1;
			}
			else{
				return 0;
			}
		}
	}


# manage read unread states for blogs replied
	function manage_states_forBlogs($subid,$itemid,$itemtype,$state){
		$strRead;
		$strQuery="select id,read_status from ex_item_status where subscription_id=$subid and item_id=$itemid and item_type=$itemtype";

		$strResult=exec_query($strQuery,1);

		if(isset($strResult) && count($strResult)>0){

			if($strResult[read_status]!=$state){

				$strRead[read_status]=$state;
				$strId[id]=$strResult[id];

				if($state=='read'){
					$strRead[read_on]=date('Y-m-d H:i:s');
				}
				else{
					$strRead[read_on]=NULL;
				}

				$id=update_query('ex_item_status',$strRead,$strId);
				if($id==1){
					return 1;
				}
				else{
					return 0;
				}
			}else if(($strResult[read_status]==$state)&& ($state=='read')){


				$strRead[read_status]=$state;
				$strId[id]=$strResult[id];

				if($state=='read'){
					$strRead[read_on]=date('Y-m-d H:i:s');
				}
				else{
					$strRead[read_on]=NULL;
				}

				$id=update_query('ex_item_status',$strRead,$strId);
				if($id==1){
					return 1;
				}
				else{
					return 0;
				}

			}
			else{
				return 1;
			}
		}
		else{
			unset($strRead);
			$strRead[subscription_id]=$subid;
			$strRead[item_id]=$itemid;
			$strRead[item_type]=$itemtype;
			$strRead[read_status]=$state;

			if($state=='read'){
				$strRead[read_on]=date('Y-m-d H:i:s');
			}
			else{
				$strRead[read_on]=NULL;
			}

			$id=insert_query('ex_item_status',$strRead);
			if($id>0){
				return 1;
			}
			else{
				return 0;
			}
		}
	}


	# manage thread state
	function manage_thread_status($subid,$threadid){
		$strThreadState[viewer_id]=$subid;
		$strThreadState[thread_id]=$threadid;
		$strThreadState[viewed_on]=date('Y-m-d H:i:s');
		$id=insert_query('ex_thread_stats',$strThreadState);
		return $id;
	}

	# manage thread summary
	function manage_thread_summary($threadid){

		$date=date('Y-m-d');
		$hour=date('H');

		$strQuery="select id,views from ex_thread_summary where thread_id=$threadid and day='$date' and hour=$hour";
		$strResult=exec_query($strQuery,1);

		if(isset($strResult) && count($strResult)>0){
			$strThread[views]=$strResult[views]+1;
			$strThreadId[id]=$strResult[id];
			$id=update_query('ex_thread_summary',$strThread,$strThreadId);
			if($id==1){
				return 1;
			}
			else{
				return 0;
			}
		}
		else{
			$strThread[thread_id]=$threadid;
			$strThread[views]=1;
			$strThread[day]=$date;
			$strThread[hour]=$hour;
			$id=insert_query('ex_thread_summary',$strThread);
			if($id>0){
				return 1;
			}
			else{
				return 0;
			}
		}

	}

	function show_comment($threadid,$commentid,$count){
		global $HTPFX,$HTHOST;
		//get number of comments posted after specified comment
		$strQuery="select count(*) count from ex_post where thread_id=$threadid and id>$commentid and suspended='0'";
		$strResult=exec_query($strQuery,1);

		//if query failed
		if(!isset($strResult) || count($strResult)==0)
			return 0;

		//if comment will be found on first page
		if($strResult[count]<=$count){
			$strpcount=0;
		}
		//if comment will be found on second page
		elseif($strResult[count]>$count && $strResult[count]<($count*2)){
			$strpcount=$count;
		}
		//if comment will be found on later pages
		else{
			$strQuery="select ceil($strResult[count]/$count) pcount";
			$strResult=exec_query($strQuery,1);
			$strpcount=($strResult[pcount]*$count)-$count;
		}
		//generate expected url
		$strurl= $HTPFX.$HTHOST."/community/Discussion.htm?thid=$threadid&pcount=$strpcount#cmt$commentid";

		return $strurl;
	}

	function show_discussions($userid,$type,$date,$pcount='',$count=''){
		//change parameters to lowercase
		$type=strtolower($type);
		//array to hold all discussion output
		$strDiscussion="";
		//if user's discussions need to be shown
		if($type=='own'){
			$strQuery="select S.id authorid,concat(S.fname,' ',S.lname) author,T.id threadid,T.title title,T.teaser teasure,T.created_on created_on,T.content_table,T.content_id, T.thread_posts count from ex_post P,ex_thread T,subscription S where P.poster_id=$userid and S.id=T.author_id and P.thread_id=T.id and P.created_on >'$date' and P.suspended='0' and T.is_user_blog='0' order by P.created_on desc";		
		}
		//if all discussions need to be shown
		elseif($type=='all'){
			$strQuery="select S.id authorid,concat(S.fname,' ',S.lname) author,T.id threadid,T.title title,T.teaser teasure,T.created_on created_on,T.content_table,T.content_id, T.thread_posts count from ex_post P,ex_thread T,subscription S where P.thread_id=T.id and S.id=T.author_id and P.created_on >'$date' and P.suspended='0' and T.is_user_blog='0' order by P.created_on desc";
		}
		else
		{
			return 0;
		}
		if($count>0){
			$strQuery.=" limit $pcount,$count";
		}
		$strResult=exec_query($strQuery);
		//if query failed
		if(!isset($strResult) || count($strResult)==0){
			return;
		}
		//if query returns result
		else{
			//create discussion array
			$index=0;
			$strThreadId=array();
			foreach($strResult as $key => $val){
				if(!in_array($val['threadid'],$strThreadId)){
					$strDiscussion[$index]['authorid']=$val['authorid'];
					$strDiscussion[$index]['author']=$val['author'];
					$strDiscussion[$index]['threadid']=$val['threadid'];
					$strDiscussion[$index]['title']=$val['title'];
					$strDiscussion[$index]['content_table']=$val['content_table'];
					$strDiscussion[$index]['content_id']=$val['content_id'];
					$strDiscussion[$index]['teasure']=$val['teasure'];
					$strDiscussion[$index]['created_on']=$val['created_on'];
					$dateResult ="";
				    $val['created_on']; 
				   if($val['created_on'] == "" || $val['created_on'] == "0000-00-00 00:00:00")
					{				
						if($val['content_table'] == 'articles')
						{
							$dateQuery = "SELECT date as created_on from articles where id = ".$val['content_id'];
						}
						else if($val['content_table'] == 'daily_feed')
						{
							$dateQuery = "SELECT creation_date as created_on from daily_feed where id = ".$val['content_id'];
						}
						$dateResult = exec_query($dateQuery,1);							
						$strDiscussion[$index]['created_on']=$dateResult['created_on'];
					}					     						
					$strDiscussion[$index]['count']=$val['count'];
					$strThreadId[$index]=$val['threadid'];
					$index++;

				}
			}

			return $strDiscussion;
		}
		

	}//end of function show_discussions

	function show_exchanges($userid,$type,$date,$pcount='',$count=''){
		//change parameters to lowercase
		$type=strtolower($type);
		//array to hold all discussion output
		$strDiscussion="";
		//if user's discussions need to be shown
		if($type=='own'){
			$strQuery="select SQL_CALC_FOUND_ROWS S.id authorid,concat(S.fname,' ',S.lname) author,T.id threadid,T.title title,T.teaser teasure,T.created_on created_on, T.thread_posts count from ex_post P,ex_thread T,subscription S where P.poster_id=$userid and S.id=T.author_id and P.thread_id=T.id and P.created_on >'$date' and P.suspended='0'  group by T.id order by P.created_on desc";

			if($count>0){
				$strQuery.=" limit $pcount,$count";
			}


			$strResult=exec_query($strQuery);
            $searchDiscussion=array();
			$numrows=exec_query("SELECT FOUND_ROWS()c",1,"c");
			$searchDiscussion[0]=$numrows;
			//if query failed
			if(!isset($strResult) || count($strResult)==0){
				return;
			}
			//if query returns result
			else{
				//create discussion array
				$index=0;
				$strThreadId="";
				foreach($strResult as $key => $val){
					if(!in_array($val[threadid],$strThreadId)){
						$strDiscussion[$index][authorid]=$val[authorid];
						$strDiscussion[$index][author]=$val[author];
						$strDiscussion[$index][threadid]=$val[threadid];
						$strDiscussion[$index][title]=$val[title];
						$strDiscussion[$index][teasure]=$val[teasure];
						$strDiscussion[$index][created_on]=$val[created_on];
						$strDiscussion[$index][count]=$val[count];
						$strThreadId[$index]=$val[threadid];
						$index++;
					}
				}
				$searchDiscussion[1]=$strDiscussion;
				return $searchDiscussion;
				#return $strDiscussion;
			}
		}
		//if all discussions need to be shown
		elseif($type=='all'){
			$strQuery="select S.id authorid,concat(S.fname,' ',S.lname) author,T.id threadid,T.title title,T.teaser teasure,T.created_on created_on, T.thread_posts count from ex_post P,ex_thread T,subscription S where P.thread_id=T.id and S.id=T.author_id and P.created_on >'$date' and P.suspended='0' group by T.id order by P.created_on desc";

			if($count>0){
				$strQuery.=" limit $pcount,$count";
			}
			$strResult=exec_query($strQuery);

			//if query failed
			if(!isset($strResult) || count($strResult)==0){
				return;
			}
			//if query returns result
			else{
				//create discussion array
				$index=0;
				$strThreadId="";
				foreach($strResult as $key => $val){
					if(!in_array($val[threadid],$strThreadId)){
					$strDiscussion[$index][authorid]=$val[authorid];
						$strDiscussion[$index][author]=$val[author];
						$strDiscussion[$index][threadid]=$val[threadid];
						$strDiscussion[$index][title]=$val[title];
						$strDiscussion[$index][teasure]=$val[teasure];
						$strDiscussion[$index][created_on]=$val[created_on];
						$strDiscussion[$index][count]=$val[count];
						$strThreadId[$index]=$val[threadid];
						$index++;
					}
				}
				return $strDiscussion;
			}
		}
		else{
			return 0;
		}

	}//end of function show_discussions


	function discussion_layout($strall,$pcount,$count,$subscription_id,$strDiscussion,$type,$show,$date){

		global $HTPFX,$HTHOST,$EXNG_PAGE_COUNT,$DATETIMELIMIT,$page_config,$lang;
		//change parameter to lowercase
		$type=strtolower($type);
		$show=strtolower($show);
		$page='main_discussion';
		build_lang($page);
		$searchurl=$page_config['exchange_search']['URL'];
		$strCount=count($strDiscussion);
		$date=trim($date);
		$htmloutput="";
		$objThread=new Thread();
		/*
		if($strCount==0){
			return;
		}
		*/
		if($strCount==1){
			$discussion="Discussion";

		}
		else{
			$discussion="Discussions";

		}

		//if user's own discussions need to be shown
		if($show=='own'){

			//if section should be expanded
			if($type=="expand"){
				$url="$HTPFX$HTHOST/community/Save.php";
				$url.="?subscription_id=$subscription_id";
				$url.="&type=collapse";
				$url.="&show=own";
				$url.="&date=$date";
				$url.="&Ptype=discussion-main";//
				$htmloutput="
				<table border='0' style='border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; clear:both;  border-right:1px solid #cccccc;' width='670px' bgcolor='#ffffff' cellpadding='0' cellspacing='0' align='left'>
					<tr>
						<td style='border-left:0px; solid #cccccc;solid #cccccc; border-bottom:1px solid #cccccc; padding-left:1px;' colspan='2' valign='top'  class='SearchEditBorder-Big2'><span class='SearchEditBorder-Big3'> $count ";
						if($count>1){
							$htmloutput.=$lang[own_discussions_dot];
							//$htmloutput.="Discussions</span> <span class='homeeEditBorder_newblue1' >that you are participating in :<br /></span>";
						}
						else{
							$htmloutput.=$lang[own_discussion_dot];
							//$htmloutput.="Discussion <span class='homeeEditBorder_newblue1' >that you are participating in :<br /></span>";
						}
						$htmloutput.="</td>
						<td style='border-left:0px; solid #cccccc;solid #cccccc; border-bottom:1px solid #cccccc; padding-right:5px; padding-top:10px;padding-bottom:5px;' colspan='2'  valign='top'><input type='image' style='cursor:pointer;' href='#' src='$IMG_SERVER/images/community_images/Dash.gif' onclick=\" Javascript:preHttpRequest('own-discussions','$url');\">
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>";
					$index=0;
					if($strDiscussion){
					foreach($strDiscussion as $key => $val){
						if($val[count]>1){
							$comments='comments';
						}
						else{
							$comments='comment';
						}

					# get related tags
					$this->type = 'Discussions';
					$itemtype=$this->type;
					$item_type=$objThread->get_object_type($itemtype);
					$tags = $objThread->get_tags_on_objects($val[threadid], $item_type);
					//discussion-home//
					//$str=$objThread->displayTagsonly($item_type,$tags);
					$str=$objThread->displayTagsonDisshome($item_type,$tags);
					$val['content_id'];
					$val['content_table'];
					$sectionid=getVideoSubsectionid($val[content_id]);
					$subsectionName=getVideoSubsectionUrl($sectionid['page_id']);
					$sectionid['name'];
					$subsectionName['name'];

					if($index!=0){
					$htmloutput.="<tr>
						<td style='padding-left:10px; padding-bottom:10px;' colspan='3' valign='top' >
						<img src='$IMG_SERVER/images/community_images/hr.gif' width='100%' height='1px' /></td>
					</tr>";
					}
					$index=1;//
					$author=ucwords(strtolower($val[author]));
					$date=$objThread->check_date($val[created_on]);
					if($val['content_table']=='mvtv')
					{
					$author=ucwords(strtolower($sectionid['name']));
					}
					$htmloutput.="<tr>
						<td style='padding-left:0px; padding-top:0px;'class='ProfileEditBorder_newblue'>
							<table width='100%' border='0' cellspacing='0' cellpadding='0'>
								<tr>
									<td id='Discussion-author2' class='homeeEditBorder_newblue' style='padding-left:10px;'><a href='$HTPFX$HTHOST/community/Discussion.htm?thid=$val[threadid]'>$val[title]</a>
									</td>
								</tr>
								<tr>
									<td style='padding-left:10px;  height:20px;'  valign='top' >

		<span class='profilesugg' ><a href='$HTPFX$HTHOST/community/profile/index.htm?userid=$val[authorid]'>$author</a></span>
		<span class='Articletoday' > $date</span>

									</td>
								</tr>
								<tr>
									<td style='padding-left:10px;' class='DiscussionArticle'>$val[teasure]
									</td>
								</tr>
								<tr>
									<td style='padding-left:10px; color:#5792CA; height:20px; font-weight:normal;'><span class='profilesug'>$str</span></td>
								</tr>
								<tr>
									<td style='padding-left:10px;' class='DiscussionArticle'>$val[count] $comments
									</td>
								</tr>
							</table>
						</td>
						<td valign='top'  style='padding-right:5px; padding-top:10px' >&nbsp;
						</td>
					</tr>";
						}
						}
					$htmloutput.="</table>";
			}
			//if section should be collapsed
			elseif($type=="collapse"){
				$url="$HTPFX$HTHOST/community/Save.php";
				$url.="?subscription_id=$subscription_id";
				$url.="&type=expand";
				$url.="&show=own";
				$url.="&date=$date";
				$url.="&Ptype=discussion-main";
				/*
							<span class='homeeEditBorder_newblue'>You have not participated in any</span><span class='ProfileEditBorder_newblue2' valign='middle'>Discussions</span><span class='homeeEditBorder_newblue'> on the Exchange.</span>
				*/
				if($count==0){//$lang[no_own_discussion]
				$htmloutput="<table border='0' style='border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc;  border-right:1px solid #cccccc; padding:0px;margin:0px;' width='670px' bgcolor='#eceaeb' cellpadding='0' cellspacing='0' align='left' >
							<tr height='30px'>
								<td style='padding-left:5px;padding-top:5px;' class='ProfileEditBorder_newblue'>";
								$htmloutput.=$lang[no_own_discussion];
								$htmloutput.="</td>
								<td  valign='top'  style='padding-right:5px; padding-top:10px' >&nbsp;
								</td>
							</tr>
						</table>";
				}
				else{
					$htmloutput="<table border='0' align='left' style='border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc;  border-right:1px solid #cccccc; padding:0px;margin:0px;' width='670px' bgcolor='#eceaeb' cellpadding='0' cellspacing='0'>
							<tr width='150px;'>
								<td style='padding-left:5px;padding-top:5px;' valign='middle' class='ProfileEditBorder_newblue2'>$count ";
								if($count>1){
									$htmloutput.=$lang[own_discussions1];
									//$htmloutput.="Discussions</span> <span class='homeeEditBorder_newblue'>that you are participating in</span>";
								}
								else{
									$htmloutput.=$lang[own_discussion1];
									//$htmloutput.="Discussion</span> <span class='homeeEditBorder_newblue' >that you are participating in<br />				</span>";
								}

								$htmloutput.="
								</td>
								<td  valign='top'  style='padding-right:5px; padding-top:10px' ><input type='image' style='cursor:pointer;' src='$IMG_SERVER/images/community_images/plush.gif' onclick=\" Javascript:preHttpRequest('own-discussions','$url'); \" width='16' height='13' align='right' />
								</td>
							</tr>
							<tr>
								<td align='top' class='ProfileEditBorder_newblue' style='padding-left:10px;padding-top:2px;'>";
								if($count>1){
									$htmloutput.=$lang[own_discussions2];
								}
								else{
									$htmloutput.=$lang[own_discussion2];
								}
								$htmloutput.="<td style='padding-bottom:10px;padding-top:5px;padding-right:5px;'> <img onclick=\" Javascript: preHttpRequest('own-discussions','$url');\" src='$IMG_SERVER/images/community_images/Viewdscussionns.gif' style='cursor:pointer;' align='right' /></td>
								</td>
							</tr>
						</table>";
					}
						// if no update found
						//if($strCount==0)
							//$htmloutput="";
			}
			else{
				$htmloutput="";
			}

		}
		elseif($show=='all'){

			//if section should be expanded
			if($type=='expand'){
				//get current date
				$arrdate=date('Y-m-d');

				//get datetime which is set to updates
				$strDate=$objThread->get_datetime_limit($arrdate,$DATETIMELIMIT['INTERVAL'],$DATETIMELIMIT['UNIT']);

				$url="$HTPFX$HTHOST/community/Save.php";
				$url.="?subscription_id=$subscription_id";
				$url.="&type=collapse";
				$url.="&show=all";
				$url.="&date=$strDate";
				$url.="&Ptype=discussion-main";

				$urlPage="$HTPFX$HTHOST/community/Save.php";
				$urlPage.="?subscription_id=$subscription_id";
				$urlPage.="&type=expand";
				$urlPage.="&show=all";
				$urlPage.="&date=$strDate";
				$urlPage.="&Ptype=discussion-main";

				$urlBrowse="$HTPFX$HTHOST/community/Save.php";
				$urlBrowse.="?subscription_id=$subscription_id";
				$urlBrowse.="&type=expand";
				$urlBrowse.="&show=all";
				$urlBrowse.="&browse=all";
				$urlBrowse.="&date=$strDate";
				$urlBrowse.="&Ptype=discussion-main";


				$htmloutput="
				<table border='0' align='left' style='border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:0px solid #cccccc; border-bottom:1px solid #cccccc; clear:both; border-right:1px solid #cccccc; padding:0px;margin:0px;' width='670px' bgcolor='#ffffff' cellpadding='0' cellspacing='0' >
					<tr>";
				if($date!=""){
						if($count>1){
							$htmloutput.="<td style='padding-left:10px; padding-top:10px;' valign='bottom'  class='ProfileEditBorder_newblue1'> $count $lang[all_discussions_black]";
				}
				else{
							$htmloutput.="<td style='padding-left:10px; padding-top:10px;' valign='bottom'  class='ProfileEditBorder_newblue1'> $count $lang[all_discussion_black]";
						}

				//$htmloutput.="<td height='34' colspan='2' valign='top'  class='SearchEditBorder-Big1' style='padding-left:15px;'> $count Most Active $discussion <span class='searchEditBorder-Small' > in the Exchange.</span>";
				}
				else{

				$htmloutput.="<td height='34' colspan='2' valign='top'  class='SearchEditBorder-Big1' style='padding-left:10px;'> ALL $count $discussion <span class='searchEditBorder-Small' > in the Exchange.</span>";
				}
						$htmloutput.="</td>
						<td colspan='2' width='5%' valign='top'  style='padding-right:5px; padding-top:10px' ><input type='image' style='cursor:pointer;' src='$IMG_SERVER/images/community_images/Dash.gif' onclick=\" Javascript:preHttpRequest('all-discussions','$url');\">
						</td>
					</tr>
					<tr>
						<td colspan='3'>
							<table width='100%' border='0' cellspacing='0' cellpadding='0'  style=' padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; backgroung-color:red; padding-top:5px; padding-bottom:5px;  '>
								<tr>
								<td width='70%' colspan='7' valign='middle' id='professorView2' class='home_newblogs' 'nowrap' style='padding-left:10px; padding-bottom:5px; padding-top:8px;'><a style='cursor:pointer;' onclick=\" Javascript: preHttpRequest('all-discussions','$urlBrowse'); \">Browse All Discussions</td>
								</tr>
								<tr>
								<td valign='middle' id='professorView2' class='home_newblogs' 'nowrap' style='padding-left:10px; padding-bottom:5px; padding-top:4px;'>&nbsp;</td>
								<td >Search:</td>
								<td><input id='txtsearch' type='textbox' size='15' value='Search Keywords or Symbols' style='border-color:#cccccc; font-size:10px; width:150px; margin-left:5px; margin-right:5px;' onBlur=\" Javascript:blurred(this)\" onFocus=\" Javascript:focused(this)\" /></td>
								<td ><img style='cursor:pointer;' onclick=\" Javascript: go_search('$searchurl','4','txtsearch');\" src='$IMG_SERVER/images/community_images/go.gif'/></td>


								<td width='10%' valign='middle' class='friends_txt1' align='right' style='padding-bottom:5px;  padding-top:4px;'>Sort by:</td>
								<td width='15%' valign='middle' class='discussion_main_txt' align='right' style='padding-left:0px; padding-bottom:5px; padding-top:4px;' border='1'>";
								/*
								Sort By combo Box, indentical to availabe on the search page.
								*/
								$htmloutput.="<SELECT id='Selectcat' class='textsearch2' style='width:95px;height:17px;'>";
								$htmloutput.="<option value=''>--Select--</option>";
								$htmloutput.="<option value='1'>Recency</option>";
								$htmloutput.="<option value='2'>Relevance</option>";
								$htmloutput.="<option value='3'>Popularity</option>";
								$htmloutput.="</SELECT>";

								/*
								<input id='txtsort' type='textbox' size='15' value='' style='border-color:#cccccc; font-size:10px;'/>
								*/
								$htmloutput.="</td><td width='50%' valign='middle' >&nbsp;
									</td>
								</tr>
							</table>
						</td>
					</tr>";
					$index=0;
					foreach($strDiscussion as $key => $val){
						if($val[count]>1){
							$comments='comments';
						}
						else{
							$comments='comment';
						}
						if($index!=0){
							$htmloutput.="
					<tr>
						<td style='padding-left:10px; padding-right:10px;' colspan='3' valign='top' >
							<img src='$IMG_SERVER/images/community_images/hr.gif' width='100%' height='1px' />
						</td>
					</tr>
							";
						}
						$index=1;
						$author=ucwords(strtolower($val[author]));
						$date=$objThread->check_date($val[created_on]);
						$sectionid=getVideoSubsectionid($val[content_id]);
					    $subsectionName=getVideoSubsectionUrl($sectionid['page_id']);
					    $sectionid['name'];
					    $subsectionName['name'];
						if($val['content_table']=='mvtv')
						{
					     $author=ucwords(strtolower($sectionid['name']));
					    }
				$htmloutput.="
					<tr>
<td style='padding-left:0px; ' valign='top'  >
							<table width='100%' border='0' cellspacing='0' cellpadding='0'>
								<tr>
									<td id='Discussion-author2' class='homeeEditBorder_newblue' style='padding-left: 10px; padding-top:5px;'><a href='$HTPFX$HTHOST/community/Discussion.htm?thid=$val[threadid]'>$val[title]</a>
									</td>
								</tr>
								<tr>
									<td style='padding-left:10px; padding-top:3px;' ><span class='profilesugg'><a href='$HTPFX$HTHOST/community/profile/index.htm?userid=$val[authorid]'>$author</a></span> <span class='Articletoday'> $date</span>
									</td>
								</tr>

								<tr>
									<td style='padding-left:10px; padding-top:3px;' class='DiscussionArticle'>$val[count] $comments
									</td>
								</tr>
							</table>
						</td>
						<td width='5%' valign='top'  style='padding-right:5px; padding-top:10px' >&nbsp;
						</td>
					</tr>";

					}
					if($pcount==""){
						$pcount=0;
					}

					if($strall=='all'){
							$urlPage.="&page=all";
							$strPage=$objThread->make_ajax_pagination("all-discussions",$urlPage,$pcount,$EXNG_PAGE_COUNT,$count,"Threads");
						}
					$htmloutput.="<tr>
						<td colspan='3' align='center' class='viewResults' >
							<center>$strPage</center>
						</td>
					</tr>
				</table>";

				//if($strCount==0)
					//$htmloutput="";
			}
			//if section should be collapsed
			elseif($type=='collapse'){
				$url="$HTPFX$HTHOST/community/Save.php";
				$url.="?subscription_id=$subscription_id";
				$url.="&type=expand";
				$url.="&show=all";
				$url.="&date=$date";
				$url.="&Ptype=discussion-main";
				/*
							<span class='ProfileEditBorder_newblue2'>
							No Active Discussions
						 	</span>
						 	<span class='homeeEditBorder_newblue'>
							on Exchange.
						 	</span>
				*/
				if($count==0){//$lang[no_all_discussion]
				$htmloutput="<table border='0' style='border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:0px solid #cccccc; border-bottom:1px solid #cccccc;  border-right:0px solid #cccccc; padding:0px;margin:0px; clear:both; ' width='668px' bgcolor='#eceaeb' cellpadding='0'cellspacing='0'  >
					<tr>
						<td style='padding-left:10px;' class='ProfileEditBorder_newblue'>";
 						$htmloutput.=$lang[no_all_discussion];
						$htmloutput.="</td>
					</tr>
					</table>";
				}
				else{
				$htmloutput="<table border='0' align='left' style='border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:0px solid #cccccc; border-bottom:1px solid #cccccc; clear:both;  border-right:1px solid #cccccc; padding:0px;margin:0px;' width='670px' bgcolor='#eceaeb' cellpadding='0'cellspacing='0'  >
			          <tr>";
			if($date!=""){
						if($count>1){
							$htmloutput.="<td style='padding-left:10px; padding-top:10px;' valign='bottom'  class='ProfileEditBorder_newblue'> $count $lang[all_discussions]";
			}
			else{
							$htmloutput.="<td style='padding-left:10px; padding-top:10px;' valign='bottom'  class='ProfileEditBorder_newblue'> $count $lang[all_discussion]";
						}
					}
					else{
						if($count>1){
							$htmloutput.="<td style='padding-left:10px; padding-top:10px;' valign='bottom'  class='ProfileEditBorder_newblue'> All $count $lang[all_discussions]";
						}
						else{
							$htmloutput.="<td style='padding-left:10px; padding-top:10px;' valign='bottom'  class='ProfileEditBorder_newblue'> All $count $lang[all_discussion]";
						}

			}

			$htmloutput.="</span>			</td>
						      <td width='5%' valign='top'  style='padding-right:5px; padding-top:10px' ><input type='image' style='cursor:pointer;' src='$IMG_SERVER/images/community_images/plush.gif' onclick=\" Javascript: preHttpRequest('all-discussions','$url');\" width='16' height='13' align='right' />					          </td>
					      </tr>
			          <tr>
			            <td colspan='2' valign='top' align='right' style='padding-right:5px; padding-bottom:10px;'> <div align='right' ><img onclick=\" Javascript: preHttpRequest('all-discussions','$url');\" src='$IMG_SERVER/images/community_images/Viewdscussionns.gif' style='cursor:pointer;'  /></div></td>
					    </tr>
			          </table>";
				}
			}
			else{
				$htmloutput="";
			}

		}

		return $htmloutput;

	}//end of function discussion_layout

	function get_datetime_limit($date,$interval,$unit){
		$unit=strtoupper($unit);

		$strQuery="select date_format('$date' - interval $interval $unit,'%Y-%m-%d') date";

		$strResult=exec_query($strQuery,1);

		if (isset($strResult) && count($strResult)==1) {
			return $strResult[date];
		}
		else{
			return 0;
		}
	}

	function get_article_on_thread($objThread){
		$strQuery="select concat(S.fname,' ',S.lname) author,A.id article_id,A.title,A.contrib_id author_id,A.date,A.body from ex_thread T,articles A,subscription S, ex_contributor_subscription_mapping ECS where T.id=$objThread->thread_id and T.content_id=A.id and S.id=ECS.subscription_id and ECS.contributor_id=A.contrib_id";

		$strResult=exec_query($strQuery,1);
		if(isset($strResult)){
			return $strResult;
		}
		else{
			return;
		}
	}
	function get_item_on_thread($objThread){
		//echo "<br>getItemonThread:".
		$strQuery="select distinct(T.content_table) as item from ex_thread T,articles A where T.id=$objThread->thread_id ";

		$strResult=exec_query($strQuery,1);
		if(isset($strResult)){
			return $strResult;
		}
		else{
			return;
		}
	}
	function update_thread_views($object_id,$subscription_id){
		# CODE TO BE BUILD
	}//end of function update_thread_views()

	function get_section_id($object_id,$object_type){
		# CODE TO BE BUILD
	}//end of function get_section_id()

	# displays the thread created against article/blog.
	function get_thread($objThread){
		 $strThreadQuery="SELECT ET.id thread_id,S.id author_id,CONCAT(S.fname,' ',S.lname) name,
		 S.fname fname, ET.created_on , ET.updated_on, ET.enabled, ET.title,
		  ET.approved approved, ET.teaser, ET.content_id,ET.content_table,ET.thread_posts posts 
		  FROM ex_thread ET, subscription S WHERE ET.id=".$objThread->thread_id." AND S.id=ET.author_id ";
				
				/*		if(!$objThread->preview)
			$strThreadQuery.=" and ET.approved='1'";*/
		$strThreadResult=exec_query($strThreadQuery,1);
		if($strThreadResult['content_table']=='slideshow')
		{
			$strThreadBodyQuery="select body from ".$strThreadResult['content_table']." where slideshow_id=".$strThreadResult['content_id'];
		}
		else
		{
		  $strThreadBodyQuery="select body from ".$strThreadResult['content_table']." where id=".$strThreadResult['content_id'];
		}
		$strThreadBodyResult=exec_query($strThreadBodyQuery,1);
		$strThreadResult['body']=$strThreadBodyResult['body'];
		if($strThreadResult['created_on'] == "" || $strThreadResult['created_on'] == "0000-00-00 00:00:00")
		{				
			if($strThreadResult['content_table'] == 'articles')
			{
				$dateQuery = "SELECT date as created_on from articles where id = ".$strThreadResult['content_id'];
			}
			else if($strThreadResult['content_table'] == 'daily_feed')
			{
				$dateQuery = "SELECT creation_date as created_on from daily_feed where id = ".$strThreadResult['content_id'];
			}
			$dateResult = exec_query($dateQuery,1);
			$strThreadResult['created_on'] = $dateResult['created_on'];
		}
		if(isset($strThreadResult))
		{
			return $strThreadResult;
		}
		else{
			return;
		}

	}//end of function get_thread()

	function get_all_comments($objthread, $type, $output, $offset, $cnt,$user,$shift=0){
	$commentQry = "select ";

	if($output=='teasure'){
		$commentQry .= "ps.teasure teasure,";
	}

	elseif($output=='body'){
		$commentQry .= "pt.body teasure,";
	}

	$commentQry .= " sub.fname fname,sub.lname lname,concat(sub.fname,' ',sub.lname) name,sub.email email, ps.created_on date, ps.title title,ps.pcomment_id pcomment_id, ps.id postid, sub.id subid,ps.approved,ps.suspended from ex_thread th, ex_post ps,ex_post_content pt, subscription sub where th.id = ps.thread_id and ps.poster_id =sub.id and ps.id=pt.post_id and th.id = '$objthread->thread_id'";
	if($user==1){
		$commentQry .=" and ps.suspended=0 ";
	}

	if($shift!=0){
		$unionQuery.=$commentQry;
		$unionQuery.=" and ps.id=$shift ";
	}

	if($shift!=0){
		$commentQry .=" and ps.id!=$shift ";
	}

	if($shift!=0){
		$commentQry .=" union ".$unionQuery;
	}
	if($cnt!="")
	{
	$commentQry .=" order by date desc limit $offset,$cnt";
	}
	else
	{
	$commentQry .=" order by date desc";
	}
	$strNewQuery=exec_query($commentQry);
		$index=0;

	foreach($strNewQuery as $commentname){
			$commentArray[$index]['postid']=$commentname['postid'];
			$commentArray[$index]['pcomment_id']=$commentname['pcomment_id'];
			$commentArray[$index]['date']=$commentname['date'];
			$commentArray[$index]['title']=$commentname['title'];
			$commentArray[$index]['teasure']=$commentname['teasure'];
			$commentArray[$index]['name']=$commentname['name'];
			$commentArray[$index]['fname']=$commentname['fname'];
			$commentArray[$index]['lname']=$commentname['lname'];
			$commentArray[$index]['subid']=$commentname['subid'];
			$commentArray[$index]['approved']=$commentname['approved'];
			$commentArray[$index]['suspended']=$commentname['suspended'];
            $commentArray[$index]['email']=$commentname['email'];
			$index++;
		}

	$tempArray=$commentArray;
	if(is_array($tempArray))
	$commentArray=array_reverse($tempArray);
		return $commentArray;
	}

	/*Function to count approved comments against an object*/
	function count_approved_comments($objthread){
	$commentQry = "select expo.id from ex_post expo, ex_thread th, articles a where expo.thread_id = th.id and th.content_id =
				   a.id and th.id = '$objthread->thread_id' and expo.approved = 1 and expo.suspended = 0";

		   $commentRes = exec_query($commentQry);
	       $commentcnt = count($commentRes);
		   return $commentcnt;
	}

	/*Function to count all comments against an object*/
	function count_all_comments($thread_id,$user=0,$content_id=""){
		$commentQry = "select count(expo.id) commentcount from ex_post expo, ex_thread th where expo.thread_id = th.id and th.id = '$thread_id' ";
		if($user==1){
			$commentQry .=  " and expo.suspended = 0";
		}
		if($content_id){
		   $commentQry .=  " and content_id='$content_id'";
		}
		   $commentRes = exec_query($commentQry,1);
	       $commentcnt = $commentRes[commentcount];
		   return $commentcnt;
	}

	function get_all_related_objects($objthread, $offset, $cnt ){

	$commentQry = "select ps.teasure, concat(sub.fname,' ',sub.lname) name, ps.created_on date, ps.id postid, sub.id subid
				   from ex_thread th, ex_post ps, subscription sub where th.id = ps.thread_id and ps.approved = 1 and ps.poster_id =
				   sub.id and ps.suspended = 0 and th.id = '$objthread->thread_id' order by date desc limit $offset,$cnt";

		$index=0;
		foreach(exec_query($commentQry) as $commentname){
			$commentArray[$index]['postid']=$commentname['postid'];
			$commentArray[$index]['teasure']=$commentname['teasure'];
			$commentArray[$index]['name']=$commentname['name'];
			$commentArray[$index]['date']=$commentname['date'];
			$commentArray[$index]['subid']=$commentname['subid'];
			$index++;
		}
		return $commentArray;
	}

	/*Function for Related Exchange*/
	function RelatedExchange($tag,$threadid)
 	{
		global $lang, $relatedExchangeDuration, $relatedExchangeOffset, $relatedExchangeLimit;
		$index=0;
		foreach($tag as $tagrow){
				$tags[$index]=$tagrow['id'];
				$index++;
		}
		$tags = implode(',',$tags);
		$threads = "select ET.id,count(ET.id) matches,ET.title title,concat(S.fname,' ',S.lname) name,ET.created_on date,
					ET.author_id author_id from ex_thread ET, ex_item_tags EIT,ex_tags ETAGS,subscription S where
					ET.id=EIT.item_id and EIT.tag_id=ETAGS.id and ET.author_id=S.id and EIT.item_type=4 and ET.created_on > ('".mysqlNow()."'
					- interval $relatedExchangeDuration) and ETAGS.id in ($tags) and EIT.item_id <> $threadid group by ET.id order
					by matches desc, created_on desc limit $relatedExchangeOffset, $relatedExchangeLimit";
		$res_related_threads=exec_query($threads);
		if(count($res_related_threads)>0)
		{

		?>
		<div class="FeatureHeaderGrayBgDouble"><?php echo $lang['Related_Discussions']; ?></div>
		<div class="Sharebox">
		<table cellpadding="0" border="0" cellspacing="10" width="100%"  >
		<?
		$index=0;
		foreach($res_related_threads as $threadrow)
		{
			$date=$threadrow['date'];
			$datevalue = $this->check_date($date);
			$authorName=ucwords(strtolower($threadrow['name']));
			?>      <tr>
					  <td width="26" class="profile" style="padding-left:0px;">&nbsp;</td>
					  <td width="275" height="41" style="padding-left:0px;">
						<?php
						$discussionattr['q']=$threadrow['id'];
						$this->checkiBox('single_discussion',$discussionattr);?><span class="ArticleRelated" style="padding-left:
						0px;
						"><?php
						echo $threadrow['title']; ?></a></span><br />
						<?php
						$profileattr['userid']=$threadrow['author_id'];
						$this->checkiBox('profile',$profileattr);?><span class="profilesug"><?php
						echo $authorName;
						?></a></span>
						<span class="Articletoday"><?php echo $datevalue; ?></span></td>
						<?php }?>
					</tr>
		</table>
		</div>
		<?
		}
 }

 	/*Function to retrieve latest threads*/
	function get_latest_threads($duration){
	$recentThread = "select id,content_id from ex_thread where dayofyear(created_on) > dayofyear(ADDDATE(curdate(), INTERVAL -
				$duration))";
		$index=0;
		foreach(exec_query($recentThread) as $threadrow){
				$itemid[$index]=$threadrow['content_id'];
				$index++;
			}
		$threads = implode(",",$itemid);
		if(isset($threads)){
			return $threads;
		}
		else{
			return;
		}
	}

	/*Function to retrieve latest threads*/
	function get_most_related_items($tags,$itemids,$offset,$limit){
	$relatedItems = "select item_id, count(tag_id) tagcnt from ex_item_tags where tag_id in ($tags)
					group by item_id order by tagcnt desc limit $offset, $limit";

		$index=0;
		foreach(exec_query($relatedItems) as $item){
			$relatedItem[$index]=$item['item_id'];
			$index++;
		}
		$relatedItem = implode(',',$relatedItem);
		return $relatedItem;
	}
	# create a thread against an article
	function post_thread($id){
		global $D_R;
		include_once("$D_R/lib/layout_functions.php");
		
		if($id==""){
			return 0;
		}
		$article = getArticle($id);

		if(!isset($article)){
			return 0;
		}
		$strThread[title]=addslashes(mswordReplaceSpecialChars(stripslashes($article[title])));
		$strThread[teaser]=addslashes(mswordReplaceSpecialChars(stripslashes(substr($article[body],0,50))));

		$sqlCheckThread="SELECT id FROM ex_thread WHERE content_table = 'articles' AND content_id = '".$id."'";

		$arThread = exec_query($sqlCheckThread,1);
		if(count($arThread) > 0)
		{
			$arThreadCondition['content_table'] = 'articles';
			$arThreadCondition['content_id'] = $id;
			update_query('ex_thread',$strThread,$arThreadCondition);
			$threadid = $arThread['id'];
		}
		else
		{
			$strThread[enabled]=0;
			$strThread[created_on]=date("Y-m-d H:i:s",strtotime($article[date]));
			$strThread[thread_posts]=0;
			$authorSubsID =	getContributorSubsId($article[authorid]);
			$strThread[author_id]	=	$authorSubsID;
			//$strThread[author_id]=$article[authorid];
			$strThread[is_user_blog]=0;
			$strThread[content_table]='articles';
			$strThread[content_id]=$id;
			$strThread[approved]=0;

			$threadid=insert_query('ex_thread',$strThread);
		}
		if($threadid>0){
			return $threadid;
		}
		else{
			return 0;
		}
	}


	//to calculate total number of blogs that author has created
	function count_author_blogs($objBlog){
		$blogQry= " select count(th.id) count from ex_thread th, subscription sub, ex_thread_content thcon where th.author_id=sub.id and
					th.is_user_blog = 1 and th.approved = 1 and th.enabled = 1 and th.id = thcon.thread_id and
					th.author_id=$objBlog->author_id";
		$strBlogCount=exec_query($blogQry,1);
		return $strBlogCount[count];
	}

	function count_all_blogs($objBlog){
		$blogQry= " select count(th.id) count from ex_thread th, subscription sub, ex_thread_content thcon where th.author_id=sub.id and
					th.is_user_blog = 1 and th.approved = 1 and th.enabled = 1 and th.id = thcon.thread_id";
		$strBlogCount=exec_query($blogQry,1);
		return $strBlogCount[count];
	}

	 /*Function to get blogs against an author*/
	function get_author_blogs($objBlog,$offset){

		global $blogOffset, $blogLimit;
		/*
		$blogQry= " select th.id blog_id, th.title title, concat(sub.fname,' ',sub.lname) name, th.created_on date, thcon.body
					body, sub.id subid, th.approved, th.content_id content_id, th.content_table content_table, th.thread_posts
					postcount from ex_thread th, subscription sub, ex_thread_content thcon where th.author_id=sub.id and
					th.is_user_blog = 1 and th.approved = 1 and th.enabled = 1 and th.id = thcon.thread_id and
					th.author_id=$objBlog->author_id order by date desc limit $blogOffset,$blogLimit";
		*/
		$blogQry= " select th.id blog_id, th.title title, concat(sub.fname,' ',sub.lname) name, th.created_on date, thcon.body
					body, sub.id subid, th.approved, th.content_id content_id, th.content_table content_table, th.thread_posts
					postcount from ex_thread th, subscription sub, ex_thread_content thcon where th.author_id=sub.id and
					th.is_user_blog = 1 and th.approved = 1 and th.enabled = 1 and th.id = thcon.thread_id and
					th.author_id=$objBlog->author_id order by date desc limit $offset,$blogLimit";

			$index=0;
			foreach(exec_query($blogQry) as $blogname){
				$blogArray[$index]['blog_id']=$blogname['blog_id'];
				$blogArray[$index]['title']=$blogname['title'];
				$blogArray[$index]['name']=$blogname['name'];
				$blogArray[$index]['date']=$blogname['date'];
				$blogArray[$index]['body']=$blogname['body'];
				$blogArray[$index]['subid']=$blogname['subid'];
				$blogArray[$index]['postcount']=$blogname['postcount'];
				$blogArray[$index]['content_table']=$blogname['content_table'];
				$blogArray[$index]['approved']=$blogname['approved'];
				$index++;
			}
			return $blogArray;
	}

	function get_latest_blogs($offset){
			global $blogOffset, $blogLimit;
			$blogQry= " select th.id blog_id, th.title title, concat(sub.fname,' ',sub.lname) name, th.created_on date, thcon.body
						body, sub.id subid, th.approved, th.content_id content_id, th.content_table content_table, th.thread_posts
						postcount from ex_thread th, subscription sub, ex_thread_content thcon where th.author_id=sub.id and
						th.is_user_blog = 1 and th.approved = 1 and th.enabled = 1 and th.id = thcon.thread_id order by date desc limit $offset,$blogLimit";

				$index=0;
				foreach(exec_query($blogQry) as $blogname){
					$blogArray[$index]['blog_id']=$blogname['blog_id'];
					$blogArray[$index]['title']=$blogname['title'];
					$blogArray[$index]['name']=$blogname['name'];
					$blogArray[$index]['date']=$blogname['date'];
					$blogArray[$index]['body']=$blogname['body'];
					$blogArray[$index]['subid']=$blogname['subid'];
					$blogArray[$index]['postcount']=$blogname['postcount'];
					$blogArray[$index]['content_table']=$blogname['content_table'];
					$blogArray[$index]['approved']=$blogname['approved'];
					$index++;
				}
				return $blogArray;
	}


}//end of class Thread

class friends extends Exchange_Element{  // Class for Add to friends
function sendrequest($sender_id, $receiver_id)
{
	if(($sender_id!='' || $sender_id!=0) && ($receiver_id!='' || $receiver_id!=0)){
        global $HTHOST,$HTPFX;
        $req[request_status]='pending';
	$req[request_sender_id]=$sender_id;
	$req[request_receiver_id]=$receiver_id;
	$req[sent_on]=date('Y-m-d H:i:s');
	$id=insert_query("ex_friends_requests",$req); // insert new record in ex_friends_request
# call mail function
       if($id>0){
			$event='friendRequest';
			$subId=$receiver_id;
			$resEmail=getAlertStatus($event,$subId);
			$emailBody=getAlertBody($event,$sender_id,$subId);
			$sendEmail=sendAlert($subId,$sender_id,$resEmail,$event);
       }
	   if(isset($id)){
	   	  return $id;
	   }
	   else {
	      return;
	  }
	}else{ return;}
}

function searchrequest($id,$subid) {   // function to search the request.
//echo $sql="select id, sent_on from ex_friends_requests where request_status='pending' and request_sender_id='$id' and request_receiver_id='$subid'";
 $reqqry=exec_query("select id, sent_on from ex_friends_requests where request_status='pending' and request_sender_id='$id' and request_receiver_id='$subid'");
	 if(isset($reqqry)){
		return $reqqry;
	} else {
	    return;
	}
  }
   function get_all_request($id) {
   global $frndrequestlmt;
  $reqqry="select distinct ex.request_sender_id senderid ,concat(sub.fname,' ',sub.lname) name from ex_friends_requests ex, subscription sub where ex.request_sender_id=sub.id and ex.request_status='pending' and ex.request_receiver_id='$id' order by sent_on desc limit $frndrequestlmt";
			$index=0;
		    foreach(exec_query($reqqry) as $req){
			   $reqArray[$index]['senderid']=$req['senderid'];
			   $reqArray[$index]['name']=$req['name'];
			   $index++;
		    }
		   return $reqArray;
  }

 function approverequest($sender_id, $receiver_id){
     $tablename='ex_friends_requests';
	 $req['request_status']='approve';
	 $req_login_friend['date']=$req_friend['date']=$req['accepted_on']=date('Y-m-d H:i:s');
	 $req_login_friend['friend_subscription_id']=$req_friend['subscription_id']=$receiver_id;
	 $req_login_friend['subscription_id']=$req_friend['friend_subscription_id']=$sender_id;
	 $id=insert_or_update("ex_user_friends",$req_friend,array('subscription_id'=>$receiver_id,'friend_subscription_id'=>$sender_id)); // insert new record in ex_user_friends

	 $id_login_user=insert_or_update("ex_user_friends",$req_login_friend,array('subscription_id'=>$sender_id,'friend_subscription_id'=>$receiver_id)); // insert new record in ex_user_friends

     if($id>0){
     update_query($tablename,$req,array(request_sender_id=>$sender_id,request_receiver_id=>$receiver_id));
	 }
	 if(isset($id)){
	   	  return $id;
	   }
	   else {
	      return;
	  }

  }
   function rejectrequest($sender_id, $receiver_id){
     $tablename='ex_friends_requests';
	 $req['request_status']='disapprove';
	 update_query($tablename,$req,array(request_sender_id=>$sender_id,request_receiver_id=>$receiver_id));
  }
 function pendingrequest($subid){
	 $del_query="delete from ex_friends_requests where left(sent_on,10)<=DATE_SUB(curdate(),INTERVAL 7 DAY) and request_receiver_id='$subid'";
	 exec_query($del_query);
 }
  function count_pending_request($id){
	$pendingQry = "select distinct(ex.request_sender_id) senderid from ex_friends_requests ex where ex.request_status='pending' and ex.request_receiver_id='$id' and ex.request_sender_id!='0'";
		   $pendingres = exec_query($pendingQry);
	       $pendingcnt = count($pendingres);
		   return $pendingcnt;
	}

	# get list of friends
	function get_friends($subscription_id){
		$strQuery="select distinct(request_receiver_id) id
from ex_friends_requests where request_sender_id=$subscription_id and request_status='approve' order by request_receiver_id";

		$strResult=exec_query($strQuery);
		if(isset($strResult)){
			foreach($strResult as $key=>$val){
				$friends[$val[id]]=$val[id];
			}
		}
		return $friends;
	}

	# Function to validate report abuse.
	function CheckReportAbuse($subscription_id)
	{
		$strQuery="select abuse_item_id from ex_report_abuse where abuser_id='".$subscription_id."'";
		$strResult=exec_query($strQuery);
		if(isset($strResult))
		{
			foreach($strResult as $key=>$val)
			{
				$ra[$val[abuse_item_id]]=$val[abuse_item_id];
			}
		}
		return $ra;
	}

	#	An e-mail will be sent to 'support@minyanville.com' if somebody clicks on report abuse.
	function ReportAbuse($abuser_id, $abuse_item_id, $item, $to, $pagename, $reportattribute)
	{
		global $HTHOST, $HTPFX, $MODERATOR_EMAIL;
		$ra[abuser_id] = $abuser_id;
		$ra[abuse_item_id] = $abuse_item_id;
		$ra[abuse_item_type] = $item;
		$ra['date'] = date('Y-m-d, H:i:s');
		$id=insert_query("ex_report_abuse",$ra);
		$reportmoderator=$this->reportabusetomoderator($abuser_id, $to, $pagename, $reportattribute);
	   	if(isset($id))
		{
			return $id;
			}
	   	else
		{
	    	return;
		}
	}

	function reportabusetomoderator($from, $to, $pagename, $reportattribute)
	{
		global $HTHOST, $HTPFX, $MODERATOR_EMAIL;
		$event = 'ReportAbuseToModerator';
		$getinfoto=exec_query("select fname, lname, email from subscription where id=$to",1);
		$fname =$getinfoto[fname];
		$lname = $getinfoto[lname];
		$toemail = $getinfoto[email];
		$fullname = $fname.' '.$lname;
		$getinfo=exec_query("select fname, lname, email from subscription where id=$from",1);
		$fromfname =$getinfo[fname];
		$fromlname = $getinfo[lname];
		$fromemail = $getinfo[email];
		$fromfullname = $fromfname.' '.$fromlname;
		$res=$this->getEmailTemplate($event); # call getemailtemplate function
		$subject=$res[email_subject];
		$pat[] = "/\[Name\]/";
		$rep[] = ucwords(strtolower($fullname));
		$pat[] = "/\[recipient_email\]/";
		$rep[] = $toemail;
		$subject = preg_replace($pat,$rep,$subject);
		if(isset($res['template_path']))
		{
			$path=$res['template_path'];
			$SUB_EML_TMPL=$HTPFX.$HTHOST.$path;
			$template="$SUB_EML_TMPL?event=$event&pagename=$pagename&reportattribute=$reportattribute&to=$to";
			mymail($MODERATOR_EMAIL,
				"\"$fromfname $fromlname \"<$fromemail>",
				$subject,
				inc_web($template)
				);
		}
	}

	function getEmailTemplate($event){
		$res=exec_query("select * from ex_email_template where event='".$event."'",1);
		if(isset($res)){
			return $res;
		}
		else{
			return;
		}
  	}

 function get_friend_list($id,$Select="",$offset="",$limit="")
	{
	   $friendqry="select distinct(ef.subscription_id),sub.id subid,concat(sub.fname, ' ' ,sub.lname)name, sub.email email from ex_user_friends ef, subscription sub where ef.friend_subscription_id=sub.id and ef.subscription_id='$id'";
	   if($Select=='Recently updated'){
		$friendqry.=" order by ef.date desc";
		}
     if($Select=='Alphabetically'){
        $friendqry.=" order by name ";
	  }
	  if($offset!=="" && $limit!==""){
		$friendqry.=" limit $offset,$limit";
	   }
	   $getfriend_list=exec_query($friendqry);
		if(isset($getfriend_list)){
			return $getfriend_list;
		}
		else{
			return;
		}
	}

	function get_friend_list_count($id)
	{
	$friendqry="select count(distinct(sub.id)) count from ex_user_friends ef, subscription sub where ef.friend_subscription_id=sub.id and ef.subscription_id='$id'";
	  $getfriend_list_count=exec_query($friendqry);
		if(isset($getfriend_list_count)){
			return $getfriend_list_count;
		}
		else{
			return;
		}
	}

	function get_friend_attribute($id){
	$friend_attribute=exec_query("select em.id,ep.subscription_id,em.value,EPA.attribute,em.attribute_id from ex_user_profile ep, ex_profile_attribute_mapping em,ex_profile_attribute EPA where ep.id=em.profile_id and em.attribute_id=EPA.id and ep.subscription_id='$id' and EPA.attribute in ('City','State','Country','Company')");
	 if(isset($friend_attribute)){
			return $friend_attribute;
		}
		else{
			return;
		}
	}
	function delete_friend($id,$subid){
		$del_query="delete from ex_user_friends where subscription_id='$id' and friend_subscription_id='$subid'";
		exec_query($del_query);
		$del_query="delete from ex_user_friends where subscription_id='$subid' and friend_subscription_id='$id'";
		exec_query($del_query);

	}
         // function to insert new record in to ex_subscriber_invite & send email invitation
		function send_invitation($id,$recipient_email){
	    global $HTHOST,$HTPFX;
		$inv[subscription_id]=$id;
		$inv[recipient_email]=$recipient_email;
		$inv[sent_on]=date('Y-m-d, H:i:s');
		$idinv=insert_query("ex_subscriber_invite",$inv); // insert new record in ex_subscriber_invite
		if(isset($idinv)){
		        $event='Invite';
				$getsender=exec_query("select fname,lname,email from subscription where id=$id",1);
				$senderName = $getsender[fname].' '.$getsender[lname];
				//$emailBody=getAlertBody($event,$id);
				$res=$this->getEmailTemplate($event); # call getemailtemplate function


				$subject=$res[email_subject];
				$pat[] = "/\[Name\]/";
				$rep[] = ucwords(strtolower($senderName));
				$subject = preg_replace($pat,$rep,$subject);
				if(isset($res['template_path']))
				{
				   	$path=$res['template_path'];

					$INVITE_EML_TMPL="$HTPFX$HTHOST".$path;
					//$from='"'.ucfirst(strtolower($getsender[fname]))." ".ucfirst(strtolower($getsender[lname])).'"'.'<' .$getsender[email].'>';
					$from="MV Exchange <support@minyanville.com>";
			# call mail function
			     		mymail($recipient_email,
						$from,
						$subject,
						inc_web("$INVITE_EML_TMPL?event=$event&id=$id&recipient_email=$recipient_email")
						    );
				}
				return $idinv;
		} else {
			return;
		}
	}
} //end class friends

#  function for relevant thread search
function relivant_thread($q){
$relivant_threadqry  = "select count(th.teaser) count,th.teaser teasure from ex_thread th, subscription sub, ex_thread_summary sm where th.author_id=sub.id and sm.id=th.id and th.is_user_blog = 0";
if($q)
   {
	$relivant_threadqry.=" and instr(LOWER(th.teaser),'$q')";
	}
$relivant_threadqry.= " group by th.teaser order by count desc, th.teaser asc ";
$strrelivant="";
	foreach(exec_query($relivant_threadqry) as $row)
	{
		if($strrelivant==""){
   			$strrelivant.= $row['teasure'] ;
		}
		else{
			$strrelivant.= "','".$row['teasure'] ;
		}
	}
	return $strrelivant;
}

# function for thread search
function threadsearch($Selectcat,$q,$offset,$limit){
	$objExchange=new Exchange_Element();
	$arrDiscussion;
	$q=strtolower($q);
	$arrSearchString=$objExchange->tag_space($q);
	$index=0;
	$threadqry="select SQL_CALC_FOUND_ROWS ET.id threadid,concat(S.fname,' ',S.lname) author,ET.title title,ET.teaser teasure,ET.content_table,
					ETAGS.tag tag,ETAGS.id tagid,S.id authorid,date_format(ET.created_on,'%D %M %Y, %h:%i %p') date,ET.thread_posts postcount
					FROM ex_thread ET, ex_item_tags EIT, ex_tags ETAGS,subscription S
					WHERE  ET.id=EIT.item_id
					and S.id=ET.author_id
					and EIT.tag_id=ETAGS.id
					and EIT.item_type='4'
					and ET.is_user_blog='0' and";
		foreach($arrSearchString as $key=>$val){
					$threadqry.="(LOWER(ETAGS.tag)= '$val')";
				}

		if($Selectcat==1){
		$threadqry.=" group by ET.id";
			$threadqry.=" order by ET.created_on desc";
	  }
		elseif($Selectcat==2){
			$threadqry.=" group by ET.id,ETAGS.id";
		$threadqry.=" order by ET.id,ETAGS.id";
		}
		elseif($Selectcat==3){
			$threadqry.=" group by ET.id";
			$threadqry.=" order by ET.id";
	  }
		else{
			$threadqry.=" group by ET.id";
			$threadqry.=" order by ET.id";
	  }

	 $threadqry.=" limit $offset,$limit";
	 $getthread=exec_query($threadqry);
	$searchDiscussion=array();
    $numrows=exec_query("SELECT FOUND_ROWS()c",1,"c");
	$searchDiscussion[0]=$numrows;

 if(isset($getthread)){
			if($Selectcat==2){
				$arrDiscussion=discussion_search_array($getthread);
				rsort($arrDiscussion);
				$getthread=$arrDiscussion;
				$searchDiscussion[1]=$getthread;
				return $searchDiscussion;
				#return $getthread;
			}
			elseif($Selectcat==3){
				$arrDiscussion=discussion_search_array($getthread);
				$getthread=$arrDiscussion;
				foreach($getthread as $key=>$val){
					$threadViews[$val[threadid]][VIEWS]=get_thread_views($val[threadid]);
					$threadViews[$val[threadid]][id]=$val[threadid];
				}

				rsort($threadViews);

				foreach($threadViews as $key=>$val){
					$str[$val[id]]=$getthread[$val[id]];
				}
				$searchDiscussion[1]=$str;
				return $searchDiscussion;
				#return $str;
			}
			else{
				$searchDiscussion[1]=$getthread;
				return $searchDiscussion;
				#return $getthread;

			}
	}
	else{
		return;
	}

}  # function end for thread search

	function get_thread_views($threadid){
		$strQuery="select sum(views) views from ex_thread_summary where thread_id=$threadid";
		$strResult=exec_query($strQuery,1);
		if(isset($strResult)){
			return $strResult[views];
		}
		else{
			return;
		}
	}


function thread_search_count($Selectcat,$q){
	$objExchange=new Exchange_Element();
	$arrDiscussion;
	$arrSearchString=$objExchange->tag_space($q);
	$index=0;
		$threadqry="select concat(S.fname,' ',S.lname) author,ET.id threadid,ET.title title,ET.teaser teasure,ET.content_table,
					ETAGS.tag tag,ETAGS.id tagid,ECSM.subscription_id authorid,
					date_format(ET.created_on,'%D %M %Y, %h:%i %p') date,ET.thread_posts postcount
					from ex_thread ET, ex_item_tags EIT, ex_tags ETAGS,articles A,
					ex_contributor_subscription_mapping ECSM,subscription S
					where
					ECSM.subscription_id=S.id
					and ET.id=EIT.item_id
					and EIT.tag_id=ETAGS.id
					and ET.content_id=A.id
					and ECSM.contributor_id=A.contrib_id
					and ET.content_table='articles'
					and ET. is_user_blog='0'
					and EIT.item_type='4'";

		foreach($arrSearchString as $key=>$val){
			$threadqry.=" and (ETAGS.tag like '%$val%' or ETAGS.tag like '%$val%' or A.body like '%$val%')";
	}
		$threadqry.=" group by ET.id";
		$threadqry.=" order by ET.id,ETAGS.id";

		$getthread=exec_query($threadqry);

		if(isset($getthread)){
			return $getthread;
		}
		else{
			return;
		}

	}  # function end for thread search



	function discussion_search_array($arrDiscussion){

		if(!isset($arrDiscussion) || count($arrDiscussion) ==0){
		return;
	}
		else{
			$index=0;
			$arroutput;


			foreach($arrDiscussion as $key=> $val){

				if($index==0){
					$arroutput[$val['threadid']]['matchcount']=1;
					$arroutput[$val['threadid']]['threadid']=$val['threadid'];
					$arroutput[$val['threadid']][title]=$val['title'];
					$arroutput[$val['threadid']][date]=$val['date'];
					$arroutput[$val['threadid']][postcount]=$val['postcount'];
					$arroutput[$val['threadid']][author]=$val['author'];
					$arroutput[$val['threadid']][authorid]=$val['authorid'];
					$arroutput[$val['threadid']][teasure]=$val['teasure'];
					$arroutput[$val['threadid']][tag]="<a href='?q=$val[tag]&type=Articles'>$val[tag]</a>";
					$index=1;
				}
				elseif($arroutput[$val['threadid']]['threadid']!=$val['threadid']){
					//show here result
					$arroutput[$val['threadid']]['matchcount']=1;
					$arroutput[$val['threadid']]['threadid']=$val['threadid'];
					$arroutput[$val['threadid']]['title']=$val['title'];
					$arroutput[$val['threadid']][date]=$val['date'];
					$arroutput[$val['threadid']][postcount]=$val['postcount'];
					$arroutput[$val['threadid']][author]=$val['author'];
					$arroutput[$val['threadid']][authorid]=$val['authorid'];
					$arroutput[$val['threadid']][teasure]=$val['teasure'];
					$arroutput[$val['threadid']][tag]="<a href='?q=$val[tag]&type=Articles'>$val[tag]</a>";
				}
				elseif($arroutput[$val['threadid']]['threadid']==$val['threadid']){
					$arroutput[$val['threadid']]['matchcount']+=1;
					$arroutput[$val['threadid']][tag].=",<a href='?q=$val[tag]&type=Discussions'>$val[tag]</a>";
				}

			}//end of loop




			return $arroutput;
		}

	}//end of discussion_search_array()
# function for relevant comment search
function relivant_comment($q){
$relivant_commentqry  = "select count(ps.teasure) count, ps.teasure teasure from ex_thread th, ex_post ps, subscription sub, ex_thread_summary sm where th.id = ps.thread_id and ps.approved = 1 and ps.poster_id = sub.id and ps.suspended = 0 and sm.thread_id=th.id ";
if($q)
   {
	$relivant_commentqry.=" and instr(LOWER(ps.teasure),'$q')";
	}
$relivant_commentqry.= "group by ps.teasure order by count desc, ps.teasure asc";
$strrelivant="";
	foreach(exec_query($relivant_commentqry) as $row)
	{
		if($strrelivant==""){
   			$strrelivant.= $row['teasure'] ;
		}
		else{
			$strrelivant.= "','".$row['teasure'] ;

		}

	}
	return $strrelivant;

}

function buzz_search($Selectcat,$q,$offset,$EXNG_PAGE_COUNT){

	$objExchange=new Exchange_Element();
	$q=strtolower($q);
	$arrSearchString=$objExchange->tag_space($q);


	$strQuery="select SQL_CALC_FOUND_ROWS B.id buzzid,B.date date,B.title title,B.body body,C.id authorid, C.name author
				from buzzbanter B,contributors C
				where B.is_live='1' and B.show_on_web='1' and B.approved='1' and B.contrib_id=C.id ";
				foreach($arrSearchString as $key=>$val){
					$strQuery.=" and lower(body) like '%$val%'";
				}
	$strQuery.=" order by B.date desc,B.id";
	$strQuery.=" limit $offset,$EXNG_PAGE_COUNT";
	$getBuzz=exec_query($strQuery);
	return $getBuzz;
}

/*function for Article search*/
function article_search($Selectcat,$q,$offset,$limit){
	$objExchange=new Exchange_Element();
	$q=strtolower($q);
	$arrSearchString=$objExchange->tag_space($q);

	$articleqry="select distinct SQL_CALC_FOUND_ROWS A.id articleid,Th.thread_posts postcount,A.title title,C.name author,date_format(A.date,'%D %M %Y, %h:%i %p') date,A.character_text teasure from articles A,contributors C,ex_item_tags IT,ex_tags T,ex_thread Th where Th.content_id=A.id and A.contrib_id=C.id and IT.item_id=A.id and T.id=IT.tag_id and A.approved = '1'";

	if(is_array($arrSearchString)){
	foreach($arrSearchString as $key=>$val){
		$articleqry.=" and (instr(lower(A.Body),'$val') || instr(lower(T.tag),'$val'))";
	}
	}

	$articleqry.=" and IT.item_type=1";

	if($Selectcat==1){
		$articleqry.=" group by A.id";
		$articleqry.=" order by A.date desc";
	}
	elseif($Selectcat==2){
		$articleqry.=" group by A.id,T.id";
		$articleqry.=" order by A.id,T.id";
	}
	elseif($Selectcat==3){
		$articleqry.=" group by A.id";
		$articleqry.=" order by A.id";
	}
	else{
		$articleqry.=" group by A.id";
		$articleqry.=" order by A.id,T.id";
	}
	$articleqry.=" limit $offset,$limit";
    $getsearcharticles=exec_query($articleqry);
	$searchArticle=array();
    $numrows=exec_query("SELECT FOUND_ROWS()c",1,"c");
	$searchArticle[0]=$numrows;
	if(isset($getsearcharticles)){
		if($Selectcat==2){
			$arrDiscussion=get_article_output($getsearcharticles);
			rsort($arrDiscussion);
			$getsearcharticles=$arrDiscussion;
			$searchArticle[1]=$getsearcharticles;
		    return $searchArticle;
			#return $getsearcharticles;
		}
		elseif($Selectcat==3){

			$arrDiscussion=get_article_output($getsearcharticles);
			$getsearcharticles=$arrDiscussion;


			foreach($getsearcharticles as $key=>$val){
				$threadViews[$val[articleid]][VIEWS]=get_articles_views($val[articleid]);
				$threadViews[$val[articleid]][id]=$val[articleid];
			}

			rsort($threadViews);

			foreach($threadViews as $key=>$val){
				$str[$val[id]]=$getsearcharticles[$val[id]];
			}
            $searchArticle[1]=$str;
		    return $searchArticle;
			#return $str;
		}
		else{
            $searchArticle[1]=$getsearcharticles;
		    return $searchArticle;
			#return $getsearcharticles;
		}
	}
	else
	{
		return;
	}

 }

	function get_articles_views($article_id){
		$strQuery="select count(*) VIEWS from tracking_view where id=$article_id";

		$strResult=exec_query($strQuery,1);
		if(isset($strResult)){
			return $strResult[VIEWS];
		}
		else{
		return;
	}

	}

function article_search_count($Selectcat,$q){
	$objExchange=new Exchange_Element();

	$arrSearchString=$objExchange->tag_space($q);

	$q=strtolower($q);

	$articleqry="select Th.thread_posts postcount,T.id tagid,T.tag tag,A.id articleid,A.title title,C.name author,date_format(A.date,'%D %M %Y, %h:%i %p') date,A.character_text teasure from articles A,contributors C,ex_item_tags IT,ex_tags T,ex_thread Th where Th.content_id=A.id and A.contrib_id=C.id and IT.item_id=A.id and T.id=IT.tag_id and A.approved = '1'";

	foreach($arrSearchString as $key=>$val){
		$articleqry.=" and (instr(lower(A.Body),'$val') || instr(lower(T.tag),'$val'))";
	}

	$articleqry.=" and IT.item_type=1 group by A.id order by A.id,T.id";
      $getsearcharticles=exec_query($articleqry);

	if(isset($getsearcharticles)){
		return $getsearcharticles;
	}
	else
	{
		return;
	}
 }   # function end for comment search
	function  get_article_output($arrArticles){

		if(!isset($arrArticles) || count($arrArticles) ==0){
			return;
		}
		else{
			$index=0;
			$arroutput;
			foreach($arrArticles as $key=> $val){

				if($index==0){
					$arroutput[$val['articleid']]['matchcount']=1;
					$arroutput[$val['articleid']]['articleid']=$val['articleid'];
					$arroutput[$val['articleid']][title]=$val['title'];
					$arroutput[$val['articleid']][teasure]=$val['teasure'];
					$arroutput[$val['articleid']][date]=$val['date'];
					$arroutput[$val['articleid']][postcount]=$val['postcount'];
					$arroutput[$val['articleid']][author]=$val['author'];
					$arroutput[$val['articleid']][tag]="<a href='?q=$val[tag]&type=Articles'>$val[tag]</a>";
					$index=1;
				}
				elseif($arroutput[$val['articleid']]['articleid']!=$val['articleid']){
					//show here result
					$arroutput[$val['articleid']]['matchcount']=1;
					$arroutput[$val['articleid']][articleid]=$val['articleid'];
					$arroutput[$val['articleid']][title]=$val['title'];
					$arroutput[$val['articleid']][teasure]=$val['teasure'];
					$arroutput[$val['articleid']][date]=$val['date'];
					$arroutput[$val['articleid']][postcount]=$val['postcount'];
					$arroutput[$val['articleid']][author]=$val['author'];
					$arroutput[$val['articleid']][tag]="<a href='?q=$val[tag]&type=Articles'>$val[tag]</a>";
				}
				elseif($arroutput[$val['articleid']]['articleid']==$val['articleid']){
					$arroutput[$val['articleid']]['matchcount']+=1;
					$arroutput[$val['articleid']][tag].=",<a href='?q=$val[tag]&type=Articles'>$val[tag]</a>";

				}
			}//end of loop

			return $arroutput;
		}
	}

 /*function for Profile search*/
 function profile_search($Selectcat,$q,$advanced,$field,$offset,$limit){
  	$objExchange=new Exchange_Element();
	$q=strtolower($q);
	$arrSearchString=$objExchange->tag_space($q);
    if($Selectcat==1){
		$profileqry="(";
	}
	$profileqry.="select SQL_CALC_FOUND_ROWS S.id authorid,concat(S.fname,' ',(case when S.lname is null then '' else lname end)) author from subscription S left join ex_user_profile EUP on S.id=EUP.subscription_id
left join ex_profile_attribute_mapping EPAM on EUP.id=EPAM.profile_id
left join ex_profile_attribute EPA on EPAM.attribute_id = EPA.id ";
	if(isset($arrSearchString)){
		$profileqry.=" where (";
		$index=0;
		foreach($arrSearchString as $key=>$val){
			if($index!=0){
				$profileqry.=" or ";
			}
			$profileqry.=" LOWER(EPAM.value) like '%$val%'";
			$index++;
		}
		$profileqry.=" ) ";
		if($Selectcat==1){
		    $profileqry.=" order by S.date desc)";
		}

	}

	$profileqry.=" union ";
	if($Selectcat==1){
		$profileqry.="(";
	}
$profileqry.=" select S.id subscriptionid,concat(S.fname,' ',(case when S.lname is null then '' else lname end))
from subscription S left join ex_user_profile EUP on S.id=EUP.subscription_id
left join ex_profile_attribute_mapping EPAM on EUP.id=EPAM.profile_id
left join ex_profile_attribute EPA on EPAM.attribute_id = EPA.id ";

	if(isset($arrSearchString)){
		$profileqry.=" where (";
		$index=0;
		foreach($arrSearchString as $key=>$val){
			if($index!=0){
				$profileqry.=" or ";
			}
			$profileqry.=" LOWER(S.fname) like '%$val%' or LOWER(S.lname) like '%$val%'";
			$index++;
		}
		$profileqry.=" ) ";
	}


	if($Selectcat==1){
		$profileqry.=" order by S.date desc)";
	}
	elseif($Selectcat==3){
	$profileqry.=" order by authorid";
	}
	else{
		$profileqry.=" order by authorid";
	}

	if($advanced==1 && !is_numeric($field) && $field=='stockwatchlist'){
		$profileqry="select SQL_CALC_FOUND_ROWS S.id authorid,concat(S.fname,' ',(case when S.lname is null then '' else lname end)) author,ES.companyname company ,ES.stocksymbol stocksymbol from subscription S, ex_user_stockwatchlist EUSW, ex_stock ES where S.id=EUSW.subscription_id and ES.id=EUSW.stock_id";

		if(isset($arrSearchString)){
			$profileqry.=" and (";
			$index=0;
			foreach($arrSearchString as $key=>$val){
				if($index!=0){
					$profileqry.=" or ";
				}
				$profileqry.=" LOWER(ES.stocksymbol) like '$val'";
			}
			$profileqry.=" ) ";
		}
		$profileqry.="order by S.id,ES.id";
	}
    $profileqry.=" limit $offset,$limit";
    $getsearchprofile=exec_query($profileqry);
	$searchProfile=array();
    $numrows=exec_query("SELECT FOUND_ROWS()c",1,"c");
	$searchProfile[0]=$numrows;

	if(isset($getsearchprofile)){
		$arrOutput=getProfileAttribute($getsearchprofile,$val);
		if($Selectcat==2){
			rsort($arrOutput);
		}
		elseif($Selectcat==3){

			foreach($arrOutput as $key=>$val){
				$str[$key][VIEW]=get_profile_views($key);
				$str[$key][id]=$key;
			}

			rsort($str);

			foreach($str as $key=>$val){
				$strOutput[$val[id]]=$arrOutput[$val[id]];
			}
			$arrOutput=$strOutput;
		}
		$searchProfile[1]=$arrOutput;
		return $searchProfile;
		#return $arrOutput;

	}
	else
	{
		return;
	}
}


/***************************/
	function getProfileAttribute($arrProfile,$val){
		$match=array();
		$arroutput=array();
		foreach($arrProfile as $key=>$valProfile){
		    $authorid="";
		    $authorid= $valProfile['authorid'];
		    $qry="select EPAE.attribute,EPA.value from ex_profile_attribute_mapping EPA,ex_user_profile EUP,ex_profile_attribute EPAE where EPA.profile_id=EUP.id and EPA.attribute_id = EPAE.id and EUP.subscription_id=$authorid and ( LOWER(EPA.value) like '%$val%')";

		    $profileAttribute=exec_query($qry);
			$match=$profileAttribute;
			$matchcount=0;
			$matchcount=count($profileAttribute);
			$valProfile['matchcount']=$matchcount;
			$valProfile['match']=$match;
		    $arroutput[$authorid]=$valProfile;

		}
		if(isset($arroutput)){
		  return $arroutput;
		}

	}
/***************************/

 function profile_search_count($Selectcat,$q,$advanced,$field,$offset,$limit){

	$objExchange=new Exchange_Element();
	$arrSearchString=$objExchange->tag_space($q);
	$profileqry="select SQL_CALC_FOUND_ROWS S.id authorid,concat(S.fname,' ',S.lname) author,EPAM.value value,EPA.attribute attribute
				from subscription S left join ex_user_profile EUP on S.id=EUP.subscription_id
				left join ex_profile_attribute_mapping EPAM on EUP.id=EPAM.profile_id
				left join  ex_profile_attribute EPA on EPAM.attribute_id = EPA.id";

	$profileqry.=" where S.is_exchange='1'";

	if($advanced==1 && is_numeric($field))
		$profileqry.= " and EPA.id=$field";

	if(isset($arrSearchString)){
		foreach($arrSearchString as $key=>$val){

			if($advanced==1 && is_numeric($field))
			{
			$profileqry.="  and (LOWER(EPAM.value) like '%$val%' or LOWER(EPAM.value) like '%$val%')";
			//$profileqry.=" or LOWER(S.fname) like '%$val%' or LOWER(S.lname) like '%$val%' or LOWER(S.email) like '%$val%')";
			}
			else
			{
			$profileqry.=" and (LOWER(EPAM.value) like '%$val%' or LOWER(EPAM.value) like '%$val%'";
			$profileqry.=" or LOWER(S.fname) like '%$val%' or LOWER(S.lname) like '%$val%' or LOWER(S.email) like '%$val%')";
		}
	}
	}

	$profileqry.=" group by S.id order by S.id,EPA.attribute";

	if($advanced==1 && !is_numeric($field)){

		$profileqry.="select SQL_CALC_FOUND_ROWS S.id authorid,concat(S.fname,' ',S.lname) author,ES.companyname company ,ES.stocksymbol stocksymbol from subscription S, ex_user_stockwatchlist EUSW, ex_stock ES where S.id=EUSW.profile_id and ES.id=EUSW.stock_id and (ES.stocksymbol like '%$q%' or ES.stocksymbol like '%$q%')
order by S.id,ES.id";
	}
	$profileqry.=" limit $offset,$limit";
    $getsearchprofile=exec_query($profileqry);

	$searchProfile=array();
    $numrows=exec_query("SELECT FOUND_ROWS()c",1,"c");
	$searchProfile=array();
	$searchProfile[0]=$numrows;

	if(isset($getsearchprofile)){
		#$searchProfile[1]=$getsearchprofile;
		#return $searchProfile;
		#return $getsearchprofile;
		$arrOutput=$getsearchprofile;
		if($Selectcat==2){
			rsort($arrOutput);
		}
		elseif($Selectcat==3){
			foreach($arrOutput as $key=>$val){
				#$str[$key][VIEW]=get_profile_views($key);
				$str[$key][VIEW]=get_profile_views($val['authorid']);
				$str[$key][id]=$key;
			}
			rsort($str);
			foreach($str as $key=>$val){
				$strOutput[$val[id]]=$arrOutput[$val[id]];
			}
			$arrOutput=$strOutput;
		}
		$searchProfile[1]=$arrOutput;
		return $searchProfile;
	}
	else
	{
		return;
	}

 }   # function end for Profile search



	function get_profile_views($profileid){
		$strQuery="select sum(EPS.visitors) VIEW from ex_profileview_stat_summary EPS,ex_user_profile EUP where EUP.id=EPS.profile_id and EUP.subscription_id=$profileid";

		$strResult=exec_query($strQuery,1);
		if(isset($strResult)){
			return $strResult[VIEW];
		}
		else{
			return;
		}
	}



	function get_profile_output($arrProfile){
		if(!isset($arrProfile) || count($arrProfile) ==0){
			return;
		}
		else{

			$index=0;
			$arroutput;

			foreach($arrProfile as $key=> $val){

				if($index==0){

					$arroutput[$val['authorid']][authorid]=$val['authorid'];
					$arroutput[$val['authorid']][author]=$val['author'];
					if(isset($val['attribute']) && $val['value']!=""){
						$arroutput[$val['authorid']][matchcount]=1;
						$str[attribute]=$val['attribute'];
						$str[value]=$val['value'];
						$arroutput[$val['authorid']][match][1]=$str;
					}
					elseif(isset($val['stocksymbol']) && $val['company']!=""){
						$arroutput[$val['authorid']][matchcount]=1;
						$str[stock]=$val['stocksymbol'];
						$str[company]=$val['company'];
						$arroutput[$val['authorid']][stock][1]=$str;
					}
					$index=1;
				}
				elseif($arroutput[$val['authorid']]['authorid']!=$val['authorid']){
					//show here result

					$arroutput[$val['authorid']][authorid]=$val['authorid'];
					$arroutput[$val['authorid']][author]=$val['author'];
					if(isset($val['attribute']) && $val['value']!=""){
						$arroutput[$val['authorid']][matchcount]=1;
						$str[attribute]=$val['attribute'];
						$str[value]=$val['value'];
					$arroutput[$val['authorid']][match][1]=$str;
					}
					elseif(isset($val['stocksymbol']) && $val['company']!=""){
						$arroutput[$val['authorid']][matchcount]=1;
						$str[stock]=$val['stocksymbol'];
						$str[company]=$val['company'];
						$arroutput[$val['authorid']][stock][1]=$str;
					}
				}
				elseif($arroutput[$val['authorid']]['authorid']==$val['authorid']){

					if(isset($val['attribute']) && $val['value']!=""){
					$arroutput[$val['authorid']][matchcount]+=1;
						$str[attribute]=$val['attribute'];
						$str[value]=$val['value'];
						$arroutput[$val['authorid']][match][$arroutput[$val['authorid']][matchcount]]=$str;
					}
					elseif(isset($val['stocksymbol']) && $val['company']!=""){
						$arroutput[$val['authorid']][matchcount]+=1;
						$str[stock]=$val['stocksymbol'];
						$str[company]=$val['company'];
						$arroutput[$val['authorid']][stock][$arroutput[$val['authorid']][matchcount]]=$str;
					}
				}

			}//end of loop

			return $arroutput;
		}

	}

 # function article count
 function profile_count($Selectcat,$q){
    $profileqry="select count(pr.id) from ex_user_profile pr, subscription sub where sub.id = pr.subscription_id ";
	if($q){
	$profileqry.=" and instr(LOWER(ps.teasure),'$q')";

	}
	$getcountprofile=exec_query($profileqry);
	if(isset($getcountprofile)){
		return $getcountprofile;
	} else {
		return;
	}
 }


 # function for relevant blog search
function relivant_blog($q){
$relivant_blogqry  = "select count(ec.body) count,ec.body teasure from  ex_thread_content ec left join ex_thread th on th.id=ec.thread_id, subscription sub, ex_thread_summary sm where th.author_id=sub.id and sm.id=th.id and th.is_user_blog = 1 group by ec.body order by count desc";
if($q)
   {
	$relivant_blogqry.=" and instr(LOWER(th.body),'$q')";
	}
$relivant_blogqry.= "group by ec.body order by count desc";
$strrelivant="";
	foreach(exec_query($relivant_blogqry) as $row)
	{
		if($strrelivant==""){
   			$strrelivant.= $row['teasure'] ;
		}
		else{
			$strrelivant.= "','".$row['teasure'] ;

		}

	}
	return $strrelivant;

}


 function search_blog_count($Selectcat,$q){
 	$objExchange=new Exchange_Element();
	$arrSearchString=$objExchange->tag_space($q);

	$blogqry="select SQL_CALC_FOUND_ROWS concat(S.fname,' ',S.lname) author,ET.id blogid,ET.title title,ET.teaser teasure,ET.content_table,ETAGS.tag tag,
ETAGS.id tagid,ET.author_id authorid,date_format(ET.created_on,'%D %M %Y, %h:%i %p') date,
ET.thread_posts postcount
from ex_thread ET, ex_item_tags EIT, ex_tags ETAGS,ex_thread_content ETC,subscription S
where ET.id=EIT.item_id
and S.id=ET.author_id
and EIT.tag_id=ETAGS.id
and ET.content_id=ETC.id
and ET.content_table='ex_thread_content'
and ET. is_user_blog=1
and EIT.item_type=6 ";
	if(isset($arrSearchString)){
	foreach($arrSearchString as $key=> $val){
		$blogqry.=" and (ETAGS.tag like '%$val%' or ETAGS.tag like '%$val%' or ETC.body like '%$val%')";
	}
	}
	$blogqry.=" group by ET.id";
	$blogqry.=" order by ET.id,ETAGS.tag";

	$getsearchblog=exec_query($blogqry);

	if(isset($getsearchblog)){

		return $getsearchblog;
	}
	else
	{
		return;
	}
 }

 # function for blog
 function search_blog($Selectcat,$q,$offset,$limit){
	$objExchange=new Exchange_Element();
	$q=strtolower($q);
	$arrSearchString=$objExchange->tag_space($q);

	$blogqry="select distinct SQL_CALC_FOUND_ROWS ET.id blogid,concat(S.fname,' ',S.lname) author,ET.id blogid,ET.title title,ET.teaser teasure,ET.content_table,ET.author_id authorid,date_format(ET.created_on,'%D %M %Y, %h:%i %p') date,ET.thread_posts postcount from ex_thread ET, ex_item_tags EIT, ex_tags ETAGS,ex_thread_content ETC,subscription S where ET.id=EIT.item_id and S.id=ET.author_id and EIT.tag_id=ETAGS.id and ET.content_id=ETC.id
and ET.content_table='ex_thread_content' and ET. is_user_blog=1 and EIT.item_type=6 ";

	if(isset($arrSearchString)){
	foreach($arrSearchString as $key=> $val){
			$blogqry.=" and (LOWER(ETAGS.tag) like '%$val%' or LOWER(ETAGS.tag) like '%$val%' or LOWER(ETC.body) like '%$val%')";
	}
	}


	if($Selectcat==1){
	$blogqry.=" group by ET.id";
		$blogqry.=" order by ET.created_on desc";
	}
	elseif($Selectcat==2){
		$blogqry.=" group by ET.id,ETAGS.id";
		$blogqry.=" order by ET.id,ETAGS.id";
	}
	elseif($Selectcat==3){
		$blogqry.=" group by ET.id";
		$blogqry.=" order by ET.id";
	}
	else{
		$blogqry.=" group by ET.id";
		$blogqry.=" order by ET.id";
	}

	$blogqry.=" limit $offset,$limit";

	$getsearchblog=exec_query($blogqry);

	$searchblog=array();
    $numrows=exec_query("SELECT FOUND_ROWS()c",1,"c");
	$searchblog[0]=$numrows;
	if(isset($getsearchblog)){
			if($Selectcat==2){
			    #htmlprint_r ($getsearchblog);
				$arrDiscussion=get_blog_output($getsearchblog);
				#htmlprint_r($arrDiscussion);
				rsort($arrDiscussion);
				$getsearchblog=$arrDiscussion;
				$searchblog[1]=$getsearchblog;
				return $searchblog;
				#return $getsearchblog;
			}
			elseif($Selectcat==3){
				$arrDiscussion=get_blog_output($getsearchblog);
				$getsearchblog=$arrDiscussion;


				foreach($getsearchblog as $key=>$val){
					$threadViews[$val[blogid]][VIEWS]=get_thread_views($val[blogid]);
					$threadViews[$val[blogid]][id]=$val[blogid];
				}

				rsort($threadViews);

				foreach($threadViews as $key=>$val){
					$str[$val[id]]=$getsearchblog[$val[id]];
				}
				$searchblog[1]=$str;
				return $searchblog;
				#return $str;
			}
			else{
				$arrDiscussion=get_blog_output($getsearchblog);
				$getsearchblog=$arrDiscussion;
				$searchblog[1]=$getsearchblog;
				return $searchblog;
				#return $getsearchblog;
			}
		}
		else{
			return;
		}
 }# function end for blog

	function  get_blog_output($arrBlog){

		if(!isset($arrBlog) || count($arrBlog) ==0){
			return;
		}
		else{
			$index=0;
			$arroutput;

			foreach($arrBlog as $key=> $val){

				if($index==0){
					$arroutput[$val['blogid']]['matchcount']=1;
					$arroutput[$val['blogid']]['blogid']=$val['blogid'];
					$arroutput[$val['blogid']][title]=$val['title'];
					$arroutput[$val['blogid']][date]=$val['date'];
					$arroutput[$val['blogid']][postcount]=$val['postcount'];
					$arroutput[$val['blogid']][authorid]=$val['authorid'];
					$arroutput[$val['blogid']][author]=$val['author'];
					$arroutput[$val['blogid']][teasure]=$val['teasure'];
					$arroutput[$val['blogid']][tag]="<a href='?q=$val[tag]&type=Articles'>$val[tag]</a>";

					$index=1;
				}
				elseif($arroutput[$val['blogid']]['blogid']!=$val['blogid']){
					//show here result

					$arroutput[$val['blogid']][blogid]=$val['blogid'];
					$arroutput[$val['blogid']][title]=$val['title'];
					$arroutput[$val['blogid']][teasure]=$val['teasure'];
					$arroutput[$val['blogid']][date]=$val['date'];
					$arroutput[$val['blogid']][postcount]=$val['postcount'];
					$arroutput[$val['blogid']][authorid]=$val['authorid'];
					$arroutput[$val['blogid']][author]=$val['author'];
					$arroutput[$val['blogid']][tag]="<a href='?q=$val[tag]&type=Blogs'>$val[tag]</a>";
				}
				elseif($arroutput[$val['blogid']]['blogid']==$val['blogid']){
					$arroutput[$val['blogid']]['matchcount']+=1;
					$arroutput[$val['blogid']][tag].=",<a href='?q=$val[tag]&type=Blogs'>$val[tag]</a>";

				}

			}//end of loop

			return $arroutput;
		}
	}//end of fucntion

  # function blog count
 function blog_count($Selectcat,$q){
    $blogqry="select count(*) count from  ex_thread_content ec left join ex_thread th on th.id=ec.thread_id, subscription sub, ex_thread_summary sm where th.author_id=sub.id and sm.id=th.id and th.is_user_blog = 1'";
	if($q){
	   $blogqry.=" and instr(LOWER(th.body),'$q')";
	}
	$getcountblog=exec_query($blogqry);
	if(isset($getcountblog)){
		return $getcountblog;
	} else {
		return;
	}
 } # function end blog count


function build_lang($page)
{
 	global $lang;
	if(is_numeric($page))
		$qryLang="select EL.term,text from ex_lang EL where page_id=".$page;
	else
		$qryLang="select EL.term,text from ex_lang EL,layout_pages LP where EL.page_id=LP.id and LP.name='".$page."'";
	$resLang=exec_query($qryLang);
	foreach($resLang as $id=>$value)
	{
		$lang[$value['term']]=$value['text'];
	}
}

function makelinks($keyword,$type,$field="")
{
	global $page_config;
	if(!is_numeric($field) && $type==7 && $field!='stock')
	{
		$sqlGetProfileAttr='select id from ex_profile_attribute where attribute="'.$field.'"';
		$resGetProfileAttr=exec_query($sqlGetProfileAttr,1);
		$field=$resGetProfileAttr['id'];
	}
	else if($field=='stock'){
		$field='stockwatchlist';
	}
	$taglink='<a href="'.$HTPFX.$HTHOST."/library/search.htm".'?q='.$keyword.'&oid='.$type;
	if($field!=""){
		$taglink.="&advanced=1&field=".$field;
	}
	$taglink.='" class="searchSugg"'.'>'.$keyword.'</a>';
	return $taglink;
}


/* functions for inbox page end here */

 class Blogs {  // Class for BLOGS
	 function countblogs($login_user_id){
	 global $bloglmt;
# First check for subscribed authors on condition and assumption that a user can subscribe an author only single time
		 $qry_authors="select subscribed_to from ex_blog_subscribed where User_id='$login_user_id' and subscription_status='1'";
		 $aindex=0;
		 $blogidstatus=array();
		 foreach(exec_query($qry_authors) as $author){
			 $author_id=$author['subscribed_to'];
			 $authorArray[$aindex]=$author_id;
			 $aindex++;
		 }

		 if(count($authorArray)>0){
			 $Subscribed_authors=count($authorArray);
			 $updatedthreads=array();

			 for($k=0;$k<$Subscribed_authors;$k++){
				 $authorid=$authorArray[$k];
				 $blogqry="select id from ex_thread where Author_id='$authorid' and is_user_blog='1' and enabled='1' and approved='1'"; //24,25
				 $indexid=0;
				 if(isset($blogArray)) unset($blogArray);

				 foreach(exec_query($blogqry) as $blogids){
					 $bid=$blogids['id'];
					 $blogArray[$indexid]=$bid;
					 $indexid++;
				 }

				 for($ss=0;$ss<count($blogArray);$ss++){
					 $blogread="select item_id from ex_item_status where subscription_id=$login_user_id and read_status='read' and item_id=$blogArray[$ss] and item_type='6'";
			//echo $blogread;
					 $Blogstatus = exec_query($blogread);
					 $status = count($Blogstatus);

					 if($status==0){
						 $countarr1=array_count_values($blogidstatus);
						 if(count($blogidstatus)!=0){
							 $totblogs=$countarr1[0];
						 }
						 if($totblogs==$bloglmt) break;
					 $blogidstatus[$blogArray[$ss]]=$status;
				 }

				 }
			 }//end of for-loop

			 if(count($blogidstatus)!=0){
				 return $blogidstatus;
			 }else{
				 return 0;
			 }
		 }else{ return 0;}
	 }// end of function countblogs($login_user_id)




	 function countblogs1($fixedidspassed){
		 global $bloglmt,$USER;
		 $blogidstatus12=array();
		 $resids=explode(',',$fixedidspassed);
		 $login_user_id=$USER->id;
		 for($r=0;$r<count($resids);$r++){
			 $qry_ids="select read_status from ex_item_status where subscription_id='$login_user_id' and item_type='6' and item_id=$resids[$r] and read_status='read'";
			 $Blogstatus1 = exec_query($qry_ids);
			 $status1 = count($Blogstatus1);
			 $blogidstatus12[$resids[$r]]=$status1;
		 }
		 return $blogidstatus12;
	 }// end of function countblogs1($login_user_id)



	 function getsubscribedBlogs($login_user_id,$ss,$recnt_ids){
		 //	 $reqqry="select ET.id as thread_id_get,ET.author_id,ET.teaser,ET.title,concat(sub.fname,' ',sub.lname) as name,Tags.Tag as tags from ex_blog_subscribed EBS,ex_thread ET,subscription sub,ex_tags Tags,ex_item_tags EIT,ex_item_status EIS where EIS.item_id=ET.id and EIS.Subscription_id='1'and EIS.item_type='4' and EBS.subscribed_to= ET.author_id and ET.author_id=sub.id and EBS.user_id='1'and EIS.read_status='unread' and ET.is_user_blog='1' and EBS.subscription_status='1' and Tags.id=EIT.tag_id and EIT.item_id=ET.id and ET.created_on > (now() - interval 14 day)
		 //	 union select ET.id as thread_id_get,ET.author_id,ET.teaser,ET.title,concat(sub.fname,' ',sub.lname) as name,Tags.Tag as tags from ex_blog_subscribed EBS,ex_thread ET,subscription sub,ex_tags Tags,ex_item_tags EIT where EBS.subscribed_to= ET.author_id and ET.author_id=sub.id and Tags.id=EIT.tag_id and EIT.item_id=ET.id and ET.id in ($recnt_ids) order by thread_id_get DESC";
			//$reqqry="select ET.id as thread_id_get,ET.author_id,ET.teaser,ET.title,concat(sub.fname,' ',sub.lname) as name,Tags.Tag as tags from ex_blog_subscribed EBS,ex_thread ET,subscription sub,ex_tags Tags,ex_item_tags EIT where EBS.subscribed_to= ET.author_id and ET.author_id=sub.id and EBS.user_id='$login_user_id' and ET.is_user_blog='1' and EBS.subscription_status='1' and Tags.id=EIT.tag_id and EIT.item_id=ET.id and ET.created_on > (now() - interval 14 day) order by ET.created_on DESC";
			//$reqqry="select ET.id as thread_id_get,ET.author_id,ET.teaser,ET.title,concat(sub.fname,' ',sub.lname) as name from ex_blog_subscribed EBS,ex_thread ET,subscription sub where EBS.subscribed_to= ET.author_id and ET.author_id=sub.id and EBS.user_id='$login_user_id' and ET.is_user_blog='1' and EBS.subscription_status='1' and ET.created_on > (now() - interval 14 day) order by ET.created_on DESC";
		 $reqqry="select ET.id as thread_id_get,ET.author_id,ET.teaser,ET.title,concat(sub.fname,' ',sub.lname) as name from ex_blog_subscribed EBS,ex_thread ET,subscription sub where EBS.user_id='$login_user_id' and EBS.subscribed_to= ET.author_id and ET.author_id=sub.id  and  ET.id in ($recnt_ids) and ET.is_user_blog='1' order by thread_id_get DESC";
		 $index=0;
		 foreach(exec_query($reqqry) as $req){
			 $reqArray[$index]['title']=$req['title'];
			 $reqArray[$index]['name']=ucwords(strtolower($req['name']));
			 $reqArray[$index]['teaser']=ucwords(strtolower($req['teaser']));
			 $reqArray[$index]['thread_id']=$req['thread_id_get'];
			 $reqArray[$index]['subscribed_to']=$req['author_id'];
			 $index++;
		 }
		 return $reqArray;
	 }

	 function mark_blog($subscriber_id,$blog_id,$markas){
		 $obj= new Thread();
		 $item_type=$obj->get_object_type('Blogs');
		 if($item_type!=0){
			 $stat=$obj->manage_states($subscriber_id,$blog_id,$item_type,$markas);
			 return $stat;
		 }else{
			 return 0;
		 }
	 }

	 /*function interstate($login_user_id){
		 return $this->countblogs($login_user_id);
	 }*/

	 function interstate1($fixedidspassed){
		 return $this->countblogs1($fixedidspassed);
	 }

	 function retunids($passarray){
		 $arr1=array_keys($passarray);
		 $unreaids='';
		 for($i=0;$i<count($arr1);$i++){
			 if($unreaids==''){
				 if(($totblogs_arr1[$arr1[$i]])==0){
					 $unreaids=$arr1[$i];
				 }
			 }else{
				 if(($totblogs_arr1[$arr1[$i]])==0){
					 $unreaids=$unreaids.",".$arr1[$i];
				 }
			 }
		 }
		 return $unreaids;
	 }

	 function unsubscribeblog($login_user_id,$blog_id){
		 $cur_dat_time=date('Y-m-d H:i:s');
		 $tablename='ex_blog_subscribed';
		 $req_up['subscription_status']='0';
		 $req_up['unsubscribed_on']=$cur_dat_time;
		 $ret_up=update_query($tablename,$req_up,array(subscribed_to=>$blog_id,user_id=>$login_user_id));
		 if($ret_up){
			 return 1;
		 }else{
			 return 0;
		 }
	 }

	 function getAllBlogTags($itemid,$type_pass){
		 $type='';
		 $threadobj= new Thread();
		 $type_get=$threadobj->get_object_type($type_pass);
		 $type=$type_get['id'];
		 $tagsarray=$threadobj->get_tags_on_objects($itemid, $type);
		 $tagstodisplay=$threadobj->displayTagsonly($type,$tagsarray);
		 return $tagstodisplay;
	 }
 } //end class Blogs

// Class for NewArticles
 class NewArticles{

function unreadartids($login_user_id){
	global $artclelmt;
	$allarticlesreadstat="select item_id,read_status from ex_item_status where item_type='1' and subscription_id='$login_user_id'";
	$allarticlesreadexec = exec_query($allarticlesreadstat);
	if(count($allarticlesreadexec)>0){
		$artid_stat_arr=array();
		// foreach(exec_query($allarticlesreadstat) as $result)
		foreach($allarticlesreadexec as $result)
		{
			$itemid=$result['item_id'];
			$artid_stat_arr[$itemid]=$result['read_status'];
		}
	}
	$allrelatedarticlesqry="select distinct(A.id) as aid from articles A,ex_item_tags EIT, ex_tags ET,subscription S, ex_user_stockwatchlist EUSW,ex_stock ES,contributors C where A.id=EIT.item_id and A.contrib_id=C.id and EIT.item_type=1 and EIT.tag_id=ET.id and ET.type_id =1 and ET.tag=ES.stocksymbol and ES.id=EUSW.stock_id and EUSW.subscription_id=S.id and S.id='$login_user_id' order by aid DESC";
	$allrelatedarticlesqryexec = exec_query($allrelatedarticlesqry);

	if(count($allrelatedarticlesqryexec)>0){
		if(isset($artid_stat_arr)){
			$countprerecods=count($artid_stat_arr);
		}else{
			$countprerecods=0;
		}

		$ind=0;
		$displayarticlids=array();
		//*****foreach(exec_query($allrelatedarticlesqry) as $results)
		foreach($allrelatedarticlesqryexec as $results)
		{
			$aid=$results['aid'];
			if($countprerecods>0){
			 // we will chk whether this $aid has entry or not and its status
				if (array_key_exists($aid, $artid_stat_arr)) {
					if($artid_stat_arr[$aid]=='unread'){

						$countarr1=array_count_values($displayarticlids);
						if(count($displayarticlids)!=0){
							$totunreadarts=$countarr1[0];
						}
						if($totunreadarts==$artclelmt) break;
						$displayarticlids[$aid]=0;

					}else{
						//****$displayarticlids[$aid]=1;
					}
				}else{
					// unread
					//****$displayarticlids[$aid]=0;



				$countarr1=array_count_values($displayarticlids);
				if(count($displayarticlids)!=0){
					$totunreadarts=$countarr1[0];
				}
				if($totunreadarts==$artclelmt) break;
					$displayarticlids[$aid]=0;
				}
				$ind++;
			}else{//if($countprerecods==0)
				//****$displayarticlids[$aid]=0;
				$countarr1=array_count_values($displayarticlids);
				if(count($displayarticlids)!=0){
					$totunreadarts=$countarr1[0];
				}
				if($totunreadarts==$artclelmt) break;
				$displayarticlids[$aid]=0;
				$ind++;
			}
		}
		return $displayarticlids;
	}
}

function interstate1($login_user_id){
	return $this->unreadartids($login_user_id);
}


function unreadartarray($fixedidspassed){
	global $bloglmt,$USER;
	$blogidstatus12=array();
	$resids=explode(',',$fixedidspassed);
	$login_user_id=$USER->id;
	for($r=0;$r<count($resids);$r++){
		$qry_ids="select read_status from ex_item_status where item_type='1' and item_id=$resids[$r] and read_status='read' and subscription_id='$login_user_id'";
		$Blogstatus1 = exec_query($qry_ids);
		$status1 = count($Blogstatus1);
		$blogidstatus12[$resids[$r]]=$status1;
	}
	return $blogidstatus12;
}// end of function unreadartarray($fixedidspassed){

function unreadreadarr($fixedidspassed){
	return $this->unreadartarray($fixedidspassed);
}

function mark_article($subscriber_id,$article_id,$markas){
	$obj= new Thread();
	$item_type=$obj->get_object_type('Articles');
	if($item_type!=0){
		$stat=$obj->manage_states($subscriber_id,$article_id,$item_type,$markas);
		return $stat;
	}else{
		return 0;
	}
}

				 function getDetailsArticle($login_user_id,$ss,$recntartids){
					 $reqartqry="select distinct(A.id),A.contrib_id,A.title,A.character_text,A.date date,C.name as name,EIT.item_id as item_id,EIT.item_type from articles A,ex_item_tags EIT, ex_tags ET,subscription S, ex_user_stockwatchlist EUSW,ex_stock ES,contributors C where A.id=EIT.item_id and A.contrib_id=C.id and EIT.item_type=1 and EIT.tag_id=ET.id and ET.type_id =1 and ET.tag=ES.stocksymbol and ES.id=EUSW.stock_id and EUSW.subscription_id=S.id and S.id='$login_user_id' and A.id  in ($recntartids) order by id DESC";
					 //echo "<br>".$reqartqry;
					 $index=0;
					 foreach(exec_query($reqartqry) as $req){
						 $reqArray[$index]['id']=$req['id'];
						 $reqArray[$index]['title']=ucwords(strtolower($req['title']));
						 $reqArray[$index]['name']=ucwords(strtolower($req['name']));
						 $reqArray[$index]['teaser']=$req['character_text'];
						 $reqArray[$index]['date']=$req['date'];
						 $reqArray[$index]['contribtr_id']=$req['contrib_id'];
						 $reqArray[$index]['item_id']=$req['item_id'];
						 $reqArray[$index]['item_type']=$req['item_type'];
						 $reqArray[$index]['tags']=$req['Tag'];
						 $index++;
					 }
					 return $reqArray;
				 }

				 function getAllTags($itemid,$type){
				//echo $itemid,$type;
					 $threadobj= new Thread();
					 $tagsarray=$threadobj->get_tags_on_objects($itemid, $type);
					 $tagstodisplay=$threadobj->displayTagsonly($type,$tagsarray);
					 return $tagstodisplay;
				 }
			//function mark_blog($subscriber_id,$blog_id,$markas){}
 } //end class NewArticles


 // Class for NewDiscussion
 class NewDiscussion{
	 function countnewdiscussionpost($login_user_id){
		 global $newreplyonartlmt;
		 $newarticlesqry="select distinct(ET.id) as tid from subscription S,ex_user_stockwatchlist EUSW, ex_stock ES,ex_thread ET, ex_item_tags EIT, ex_tags ETAGS where S.id=EUSW.subscription_id and EUSW.stock_id=ES.id and ES.stocksymbol=ETAGS.tag and ETAGS.id=EIT.tag_id and EIT.item_type='4' and EIT.item_id=ET.id and S.id='$login_user_id'";
		 //echo $newarticlesqry;
		 $newthrdarrdiscusion=array();
		 $newthrdarrstr='';
		 $newthrdarrread=array();
		 $newpost=0;
		 $postedids='';
		 $target_thredids='';

		 foreach(exec_query($newarticlesqry) as $artids){
			 $artidget=$artids['tid'];
			 $sql_stat_qry="select id,read_status,read_on from ex_item_status where item_type='4' and subscription_id='$login_user_id' and item_id ='$artidget'";
			 //echo "<br>".$sql_stat_qry;
			 $sqlstatresult = exec_query($sql_stat_qry);
			 $statcnt = count($sqlstatresult);
			 if($statcnt>0){
			 // Something get check for read / unread

				//**** foreach(exec_query($sql_stat_qry) as $statres){
				 foreach($sqlstatresult as $statres){
						if($statres['read_status']=='read'){
							$newthrdarrdiscusion[$artidget]=1;

							if($newthrdarrstr==''){
							$newthrdarrstr=$artidget.'_'."1";
							}else{
							$newthrdarrstr=$newthrdarrstr.','.$artidget.'_'.'1';
							}

							$newthrdarrread[$artidget]=$statres['read_on'];
							// Check for new posting for this id
							$qry_new_posting="select distinct(poster_id) as poster_id,id,post_time from ex_post where thread_id='$artidget' and poster_id!='$login_user_id' and post_time>'".$statres['read_on']."' order by post_time DESC LIMIT 1";
							//echo "<br>".$qry_new_posting;
							foreach(exec_query($qry_new_posting) as $resst){
								//echo "<br>Post By ".$resst['poster_id'];
								$newthrdarrdiscusion[$artidget]=0;
								if($newthrdarrstr==''){
									$newthrdarrstr=$artidget.'_'."0";
								}else{
									$newthrdarrstr=$newthrdarrstr.','.$artidget.'_'.'0';
								}

								if($target_thredids==''){
									$target_thredids=$artidget;
								}else{
								$target_thredids=$target_thredids.",".$artidget;
								}

								$newpost=$newpost+1;
								if($postedids==''){
									$postedids=$resst['id'];
								}else{
								$postedids=$postedids.",".$resst['id'];
								}
							}
						}else{
							// while read_status='unread'
							$newthrdarrdiscusion[$artidget]=0;
							if($newthrdarrstr==''){
								$newthrdarrstr=$artidget.'_'."0";
							}else{
								$newthrdarrstr=$newthrdarrstr.','.$artidget.'_'.'0';
							}
							$qry_new_posting="select distinct(poster_id) as poster_id,id,post_time from ex_post where thread_id='$artidget' and poster_id!='$login_user_id' order by post_time DESC LIMIT 1";
							//echo "<br> Unread .... ".$qry_new_posting;
							foreach(exec_query($qry_new_posting) as $resst){
								//echo "<br>Post By ".$resst['poster_id'];
								if($target_thredids==''){
								$target_thredids=$artidget;
								}else{
									$target_thredids=$target_thredids.",".$artidget;
								}
								$newpost=$newpost+1;
								if($postedids==''){
									$postedids=$resst['id'];
								}else{
									$postedids=$postedids.",".$resst['id'];
								}
							}
						}
					}

			 }else{
						 //Nothing get defalut unread
						 $newthrdarrdiscusion[$artidget]=0;
						 if($newthrdarrstr==''){
							 $newthrdarrstr=$artidget.'_'."0";
						 }else{
							 $newthrdarrstr=$newthrdarrstr.','.$artidget.'_'.'0';
						 }
						 $qry_new_posting="select distinct(poster_id) as poster_id,id,post_time from ex_post where thread_id='$artidget' and poster_id!='$login_user_id' order by post_time DESC LIMIT 1";
						 //echo "<br>never read/unread.....".$qry_new_posting;
						 foreach(exec_query($qry_new_posting) as $resst){
							 if($target_thredids==''){
								 $target_thredids=$artidget;
									}else{
									$target_thredids=$target_thredids.",".$artidget;
									}
							 $newpost=$newpost+1;
							 if($postedids==''){
								 $postedids=$resst['id'];
							 }else{
								 $postedids=$postedids.",".$resst['id'];
							 }
							 //echo "<br>Post By ".$resst['poster_id'];
						 }
			 }
		 }
	 return $newpost."~".$postedids."~".$target_thredids.'~'.$newthrdarrstr;
	 }

	 function getDiscussionPosted($login_user_id,$fixthrdids,$fixpostids){
		 global $newreplyonartlmt;
		// $postqrylatest="select ET.id as Thread_id,EP.id as postid,EP.created_on,ET.title,ET.author_id,ET.teaser,concat(S.fname,' ',S.lname)as name from ex_post EP,ex_thread ET,subscription S where EP.thread_id=ET.id and is_user_blog=0 and S.id=ET.author_id and ET.id in ($fixthrdids) and EP.id in($fixpostids) order by created_on DESC LIMIT $newreplyonartlmt";
		 $postqrylatest="select ET.id as Thread_id,EP.id as postid,EP.created_on,ET.title,ET.author_id,ET.teaser,concat(S.fname,' ',S.lname)as name from ex_post EP,ex_thread ET,subscription S where EP.thread_id=ET.id and (is_user_blog is NULL or is_user_blog=0) and S.id=ET.author_id and ET.id in ($fixthrdids) and EP.id in($fixpostids) order by created_on DESC LIMIT $newreplyonartlmt";
			 foreach(exec_query($postqrylatest) as $reqposts){
				 $reqThreadArray[$index]['id']=$reqposts['Thread_id'];
				 $reqThreadArray[$index]['postid']=$reqposts['postid'];
				 $reqThreadArray[$index]['title']=$reqposts['title'];
				 $reqThreadArray[$index]['author_id']=$reqposts['author_id'];
				 $reqThreadArray[$index]['name']=$reqposts['name'];
				 $reqThreadArray[$index]['teaser']=$reqposts['teaser'];
				 $index++;
				 }
		 	return $reqThreadArray;
	 }
	 function getAllDiscussionTags($itemid,$type_pass){
		//echo $itemid,$type;
		 $type='';
		 $threadobj= new Thread();
		 $type_get=$threadobj->get_object_type($type_pass);
		 $type=$type_get['id'];
		 $tagsarray=$threadobj->get_tags_on_objects($itemid, $type);
		 $tagstodisplay=$threadobj->displayTagsonly($type,$tagsarray);
		 return $tagstodisplay;
	 }

	 function mark_discussion($subscriber_id,$tid,$markas){
		 $obj= new Thread();
		 $item_type=$obj->get_object_type('Discussions');
		 if($item_type!=0){
			 $stat=$obj->manage_states_forBlogs($subscriber_id,$tid,$item_type,$markas);
			 return $stat;
		 }else{
			 return 0;
		 }
	 }

	 function interstate2($login_user_id){
		 return $this->countnewdiscussionpost($login_user_id);
	 }


 } //end class NewDiscussion

  // Class for BLOGDiscussion
  class BLOGDiscussion{
	  function countBLOGDiscussion($login_user_id){
		  global $newreplyonbloglmt;

		  $newthrdarr1=array();
		  $newthrdarrstr1='';
		  $newthrdarrread1=array();
		  $newpost1=0;
		  $postedids1='';
		  $target_thredids1='';

		  // First check the authors I have subscribed
		  $qry_authors="select subscribed_to as aid from ex_blog_subscribed where user_id='$login_user_id' and subscription_status='1'";
		  $authorsres = exec_query($qry_authors);
		  $countauthrs=count($authorsres);
		  if($countauthrs>0){
		  //****foreach(exec_query($qry_authors) as $subscrbdauthors){
			  foreach($authorsres as $subscrbdauthors)
			  {
			  $authorid_get=$subscrbdauthors['aid'];
			  // Now check for each blog written by each author
			  $sql_activeblogs="select id as thread_id from ex_thread where author_id='$authorid_get' and enabled='1' and approved='1'";
			  $activeblogres = exec_query($sql_activeblogs);
			  $countblogs=count($activeblogres);

			  if($countblogs>0){
					 // foreach(exec_query($sql_activeblogs) as $subscrbdblog){
				  foreach($activeblogres as $subscrbdblog)
				  {
					  $blog_id_get=$subscrbdblog['thread_id'];
					  // Now check read status for each thread
					  $blogreadqry="select id,read_status,read_on from ex_item_status where item_type='6' and subscription_id='$login_user_id' and item_id ='$blog_id_get'";
					  //echo "<br>".$blogreadqry;
					  $blogreadqryres = exec_query($blogreadqry);
					  $statuscnt = count($blogreadqryres);
					  			  if($statuscnt>0){
									  //foreach(exec_query($blogreadqry) as $statres1){
									  foreach($blogreadqryres as $statres1)
									  {
										  if($statres1['read_status']=='read'){

											  if($newthrdarrstr1==''){
												  $newthrdarrstr1=$blog_id_get.'_'."1";
											  }else{
												  $newthrdarrstr1=$newthrdarrstr1.','.$blog_id_get.'_'.'1';
											  }
											  $newthrdarrread1[$blog_id_get]=$statres1['read_on'];
											  // Check for new posting for this id
											  $qry_new_posting="select distinct(poster_id) as poster_id,id,post_time from ex_post where thread_id='$blog_id_get' and poster_id!='$login_user_id' and post_time>'".$statres1['read_on']."' order by post_time DESC  LIMIT 1";
											  //echo "<br>".$qry_new_posting;
											  foreach(exec_query($qry_new_posting) as $resst1){
												  //echo "<br>Post By ".$resst1['poster_id'];
												  if($target_thredids1==''){
													  $target_thredids1=$blog_id_get;
												  }else{
													  $target_thredids1=$target_thredids1.",".$blog_id_get;
												  }
												  if($newthrdarrstr1==''){
													  $newthrdarrstr1=$blog_id_get.'_'."0";
												  }else{
													  $newthrdarrstr1=$newthrdarrstr1.','.$blog_id_get.'_'.'0';
												  }

												  $newpost1=$newpost1+1;
												  if($postedids1==''){
													  $postedids1=$resst1['id'];
												  }else{
													  $postedids1=$postedids1.",".$resst1['id'];
												  }
											  }

											  }else if($statres1['read_status']=='unread'){
											  // while read_status='unread'
											  $newthrdarr1[$blog_id_get]=0;
											  $qry_new_posting="select distinct(poster_id) as poster_id,id,post_time from ex_post where thread_id='$blog_id_get' and poster_id!='$login_user_id' order by post_time DESC  LIMIT 1";
											  //echo "<br>".$qry_new_posting;
											  unset($resst1);
											  foreach(exec_query($qry_new_posting) as $resst1){
											  //echo "<br>Unread Post By ".$resst1['poster_id'];
												  if($target_thredids1==''){
													  $target_thredids1=$blog_id_get;
												  }else{
													  $target_thredids1=$target_thredids1.",".$blog_id_get;
												  }
												  $newpost1=$newpost1+1;
												  if($postedids1==''){
													  $postedids1=$resst1['id'];
												  }else{
													  $postedids1=$postedids1.",".$resst1['id'];
												  }
											  }
											  }
										  }// 	foreach(exec_query($blogreadqry) as $statres1){
					  }else{
					   // if($statuscnt>0){
					  // I hvnt read/unread this thread yet
					  //echo "<br> **** Not read /unread ".$blog_id_get;
					  // So I will check the no of posts on this thread also

					  $newthrdarr1[$blog_id_get]=0;
					  $qry_new_posting="select distinct(poster_id) as poster_id,id,post_time from ex_post where thread_id='$blog_id_get' and poster_id!='$login_user_id' order by post_time DESC LIMIT 1";
					  //echo "<br>".$qry_new_posting;
					  unset($resst1);
					  foreach(exec_query($qry_new_posting) as $resst1){

						  if($newthrdarrstr1==''){
							  $newthrdarrstr1=$blog_id_get.'_'."0";
						  }else{
							  $newthrdarrstr1=$newthrdarrstr1.','.$blog_id_get.'_'.'0';
						  }

						  //echo "<br>Unread Post By ".$resst1['poster_id'];
						  if($target_thredids1==''){
							  $target_thredids1=$blog_id_get;
						  }else{
							  $target_thredids1=$target_thredids1.",".$blog_id_get;
						  }
						  $newpost1=$newpost1+1;
						  if($postedids1==''){
							  $postedids1=$resst1['id'];
						  }else{
							  $postedids1=$postedids1.",".$resst1['id'];
						  }
					  }

					  }
				  }
			  }//if($countblogs>0){
			  else{
				  //echo "<br>",$authorid_get." has no Blogs";
				  //$newpost1=0;
				  }
		  }
		  }else{
			  // when 	if($countauthrs==0){
			  //$newpost1=0;
			  //echo "I have No subscribed BLOGS ";
		  }
		  return $newpost1."~".$postedids1."~".$target_thredids1.'~'.$newthrdarrstr1;
	  }
	 function BLOGDiscussionDetail($login_user_id,$fixedthredblogids,$fixedpostblogids){
		 $BLOGDiscusDetailqry="select ET.id as Thread_id,EP.id as postid,EP.created_on,ET.title,ET.author_id,ET.teaser,concat(S.fname,' ',S.lname)as name from ex_post EP,ex_thread ET,subscription S where EP.thread_id=ET.id and is_user_blog='1' and S.id=ET.author_id and ET.id in ($fixedthredblogids) and EP.id in($fixedpostblogids) order by created_on DESC";
		 //echo $BLOGDiscusDetailqry;
		 $index=0;
		 foreach(exec_query($BLOGDiscusDetailqry) as $reqblogsdetail){
			 $rqeBLOGDiscussionDetail[$index]['title']=$reqblogsdetail['title'];
			 $rqeBLOGDiscussionDetail[$index]['authorid']=$reqblogsdetail['author_id'];
			 $rqeBLOGDiscussionDetail[$index]['teaser']=$reqblogsdetail['teaser'];
			 $rqeBLOGDiscussionDetail[$index]['thread_id']=$reqblogsdetail['Thread_id'];
			 $rqeBLOGDiscussionDetail[$index]['postid']=$reqblogsdetail['postid'];

			 $index++;
		 }
		 return $rqeBLOGDiscussionDetail;
		 }

	 function getAllBLOGTags($itemid,$type_pass){
		//echo $itemid,$type;
		 $threadobj= new Thread();
		 $type_get=$threadobj->get_object_type($type_pass);
		 $type=$type_get['id'];
		 $tagsarray=$threadobj->get_tags_on_objects($itemid, $type);
		 $tagstodisplay=$threadobj->displayTagsonly($type,$tagsarray);
		 return $tagstodisplay;
	 }

	 function mark_discussion($subscriber_id,$tid,$markas){
		 $obj= new Thread();
		 $item_type=$obj->get_object_type('Blogs');
		 if($item_type!=0){
			 $stat=$obj->manage_states_forBlogs($subscriber_id,$tid,$item_type,$markas);
			 return $stat;
		 }else{
			 return 0;
		 }
	 }

	 function interstate3($login_user_id){
		 return $this->countBLOGDiscussion($login_user_id);
	 }

	 } //end class BLOGDiscussion

 // Function for display of Blogs with Comments
 function display_recentblogswithcomments(){
	 global $SHOW_RECNT_BLOG,$page_config,$lang;
	 $qry="select id,Section_id,title,thread_posts,created_on from ex_thread where is_user_blog='1' and enabled='1' and approved='1' order by created_on DESC LIMIT $SHOW_RECNT_BLOG";
	 $displayresults = exec_query($qry);
	 if(count($displayresults)>0)
	 {
		 $displyblogs='<table border="0" cellpadding="0" cellspacing="0" width="100%">
		 <tr>
		 <td>
		 <table border="0" width="100%" style="border-left:1px solid #cccccc; padding:0px; margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc;  border-right:1px solid #cccccc;   padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="left">
		 <tr>
		 <td>
		 <table cellpadding="0" cellspacing="0" width="100%" border="0">
		 <tr>
		 <td>
		 <table width="100%" style="padding:0px; margin:0px; border-bottom:1px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="left">
		 <tr align="left" valign="top">
	     <td height="21"  colspan="2" valign="top" class="ProfileBlogs">Recent blog entries</td>
		 </tr>
		 </table>
		 </td>
		 </tr>';
        //****foreach(exec_query($qry) as $recntblogs)
		foreach($displayresults as $recntblogs)
		{
			 $id=$recntblogs['id'];
			 $title=ucfirst($recntblogs['title']);
			 $thread_posts=$recntblogs['thread_posts'];
			 $title_link='<a class="Blogpanal1" href="'.$page_config['blog_comment']['URL'].'?blog_id='.$id.'"'.'>'.$title.'</a>';
			 $displyblogs.='<tr>
			 <td class="Blogpanal" align="left" style="padding-left:10px;"><br />
			 '.$title_link.'<br />
			 <span class="Blogpanal1">Comments ('.$thread_posts.')</span>
			 </td>
			 </tr>';
		 }
	 $displyblogs.='<tr>
			 <td>&nbsp;</td>
			 </tr>
			 <tr>
			 <td>&nbsp;</td>
			 </tr>
			 <tr>
			 <td>&nbsp;</td>
			 </tr>
			 </table>
			 </td>
			 </tr>
			 </table>
			 </td>
			 </tr>
			 </table>';
	 echo $displyblogs;
		 }else{
			 if(count($displayresults)==0)
			 {
				 $displyblogs='<table border="0" cellpadding="0" cellspacing="0" width="100%">
					 <tr>
					 <td>
					 <table border="0" width="99%" style="border-left:1px solid #cccccc; padding:0px; margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
					 <tr>
					 <td>
					 <table cellpadding="0" cellspacing="0" width="100%" border="0">
					 <tr>
					 <td>
					 <table width="100%" style="padding:0px; margin:0px; border-bottom:1px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
					 <tr align="center" valign="top">
					 <td height="21"  colspan="2" valign="top" class="ProfileBlogs">Recent blog entries</td>
					 </tr>
					 </table>
					 </td>
					 </tr>
					 <tr>
					 <td style="padding-left:10px;" valign="top" class="profileRichBox"><br />';
					 $displyblogs.=$lang['no_recnt_blogs'].'
					 </td>
					 </tr>';
					 $displyblogs.='<tr>
						 <td>&nbsp;</td>
						 </tr>
						 <tr>
						 <td>&nbsp;</td>
						 </tr>
						 <tr>
						 <td>&nbsp;</td>
						 </tr>
						 </table>
						 </td>
						 </tr>
						 </table>
						 </td>
						 </tr>
						 </table>';
				 echo $displyblogs;
				 }


		 }
 }

// Function For Recommended Discussions
 function display_recomendeddiscussion($user_login_id,$caption,$content_table=""){
	 global $SHOW_RECNT_BLOG,$page_config,$lang,$newrecmndeddiscusionlmt;
	 $caption=$lang[$caption];
	 if($user_login_id>1)
		 $qry="select ET.id id,ET.title,ET.author_id,ET.thread_posts,ET.created_on created_on, ET.content_table,ET.content_id from ex_user_stockwatchlist EUSW,subscription S, ex_stock ES, ex_thread ET, ex_item_tags EIT,ex_tags ETAGS where EUSW.stock_id=ES.id  and ES.stocksymbol=ETAGS.tag and ETAGS.type_id=1 and ETAGS.id =EIT.tag_id and EIT.item_id=ET.id and EIT.item_type=4 and ET.approved='1' and S.id='$user_login_id' group by ET.id order by created_on DESC limit $newrecmndeddiscusionlmt";
	else
		$qry="SELECT ET.id id,ET.title,ET.author_id,ET.thread_posts,ET.created_on created_on, ET.content_table,ET.content_id FROM ex_thread ET, articles A where ET.content_id=A.id and A.approved='1' ORDER BY created_on DESC LIMIT 0,10";
	 $qryresultset = exec_query($qry);
	 $count=count($qryresultset);
	 if($count>0)
	 {
			 $display_recmended='<table border="0" cellpadding="0" cellspacing="0" width="100%">
			 <tr>
				 <td style="padding-right:0px;">
			 <table border="0" width="100%" style="border-left:1px solid #cccccc; padding:0px; margin:0px; border-top:0px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
			 <tr>
			 <td backgroundcolor="#00ff00">
			 <table cellpadding="0" cellspacing="0" width="100%" border="0">
			 <tr>
			 <td colspan="2" style="padding-bottom:10px;">
			 <table width="100%" style="padding:0px; margin:0px; border-bottom:1px solid #cccccc; border-top:1px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
			 <tr align="center" valign="top">
			 <td  colspan="3" valign="top" class="ProfileBlogs">'.$caption.'</td>
			 </tr>
			 </table>
			 </td>
			 </tr>';
			 //**** foreach(exec_query($qry) as $recntdiscrec){
			 foreach($qryresultset as $recntdiscrec)
			 {
			 $exobj=new Exchange_Element();
			 $id=$recntdiscrec['id'];
			 $sectionid=getVideoSubsectionid($recntdiscrec['content_id']);

			 if ($recntdiscrec['content_table']=='mvtv')
			 {
				$author_name=$sectionid['name'];
			 }
			 else
			 {
			 $author_id=$recntdiscrec['author_id'];
			 $author_name_arr=$exobj->userInfo($author_id);
			 $author_name=ucwords(strtolower($author_name_arr['fname'].' '.$author_name_arr['lname']));
			 }
			 $title=$recntdiscrec['title'];
			 $thread_posts=$recntdiscrec['thread_posts'];

			 $created_on=$exobj->check_date($recntdiscrec['created_on']);

			 $title_link='<a class="SuggestedArticleBox1" href="'.$page_config['single_discussion']['URL'].'?type=articles&thid='.$id.'"'.'>'.ucfirst($title).'</a>';
			 $display_recmended.='<tr>
			 <td valign="top">
			 <table width="100%" border="0" cellspacing="0" cellpadding="0">
			 <tr>
			 <td colspan="2"  style="padding-left:10px;" align="left"><span id="SearchBlogsYouFollow">'.$title_link.'</span></td>
			 </tr>

			 <tr>
			 <td class="DiscRecommendend">
			 <span id="DiscussionReadmore">
			 <span class="SuggestedArticleBox1"><a style="cursor: pointer;" href="./profile/index.htm?userid='.$author_id.'">'.$author_name.' </a></span></span>
			 <span style="padding-left:5px;" class="ArticleTodayDate"> '.$created_on.'</span> </td>
			 </tr>

			 <tr>
			 <td class="disccomments">'.$thread_posts.' comments</td>
 		     </tr>
			</table>
			</td>
		    </tr>';
		 }
			$display_recmended.='</table>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table><br />';
		 echo $display_recmended;
	 }
	 else{
		$display_recmended='<table border="0" style="border-left:1px solid #cccccc;padding:0px;margin:0px; border-top:1px solid #cccccc; border-right:1px solid #cccccc; border-bottom:1px solid #cccccc; padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center" width="100%">
<td>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
<td colspan="2">
<table width="100%" border="0" style="padding:0px;margin:0px; border-bottom:1px solid #cccccc; padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
<tr>
<td valign="top" class="ProfileBlogs">'.$caption.'</td>
</tr>
</table>
</td>
</tr>';
		$display_recmended.='<tr>
		<td style="padding-bottom:10px;padding-top:10px;padding-left:40px;" colspan="2" class="SuggestedArticleBox">No '.$caption.'
			<span class="profilesugg">'.$tagstodisplay.'</span></td></tr>
			</table></td></tr></table>';
		echo $display_recmended;
	 }
}

function display_userBlogs($user_login_id){
global $page_config,$lang,$USER,$HTPFX,$HTHOST,$CDN_SERVER;
$guest_id=0;
$retid=0;
$exobj=new Exchange_Element();

if(isset($user_login_id)){
	$curntuser=$USER->id;

	$name_arr=$exobj->userInfo($curntuser);
	$loginuserfullname=ucwords(strtolower($name_arr['fname'].' '.$name_arr['lname']));
	$loginuserfname=ucwords(strtolower($name_arr['fname']));
	if($user_login_id!=$curntuser){
		$guest_id=$user_login_id;
		$guestname_arr=$exobj->userInfo($guest_id);
		if(count($guestname_arr)!=0){
		$guestfullname=ucwords(strtolower($guestname_arr['fname'].' '.$guestname_arr['lname']));
		$guestfname=ucwords(strtolower($guestname_arr['fname']));
		}else{
			$guestfname="Stranger";
		}
	}else{
	// when u are watching your own profile then the guest_id='';
	$guest_id=0;
    }
}

// First decide who is guest and who is author
if(($guest_id==0)&&($curntuser==0)){
	echo "User Not Logged In......";
}else if(($guest_id==0)&&($curntuser!=0)){
	$usersblogs='<script src="'.$CDN_SERVER.'/js/blogsubscribe.js" type="text/javascript"></script>';
	//echo "$loginuserfullname trying to see his own BLOGS";
   //select those blogs created by u
	$LIMIT=5;
	$count=0;
	$userblogsdetailqry="select id,title,teaser,thread_posts from ex_thread where author_id='$curntuser' and is_user_blog='1' and enabled='1' and approved='1' order by created_on DESC LIMIT $LIMIT";
	//echo $userblogsdetailqry;
	$userblogsdetailqryresult = exec_query($userblogsdetailqry);
	$count=count($userblogsdetailqryresult);
	$bloginstance=new Blogs();
	$usersblogs.='<table border="0"  width="100%" style="border-left:1px solid #cccccc; padding:0px; margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
	<tr>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
	<td>
	<table width="100%" style="padding:0px; margin:0px; border-bottom:1px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
	<tr align="center" valign="top">
	<td colspan="2" valign="top" class="ProfileBlogs">';
	$usersblogs.=$loginuserfname."'s Blog</td>";
	$usersblogs.='</tr>
	</table>
	</td>
	</tr>';
	if($count>0){
					 foreach($userblogsdetailqryresult as $blgdetail)
					 {
					 $tags='';
					 $thread_id=$blgdetail['id'];
					 $title=$blgdetail['title'];
					 $title_link='';
					 $title_link='<a class="Blogpanal" href="'.$page_config['blog_comment']['URL'].'?blog_id='.$thread_id.'"'.'>'.ucwords($title).'</a>';
					 $viewlink='<a href="'.$page_config['blog_comment']['URL'].'?blog_id='.$thread_id.'"'.'>View Comments ('.$blgdetail["thread_posts"].')</a>';
					 $viewteaser='<a class="teaserblog" href="'.$page_config['blog_comment']['URL'].'?blog_id='.$thread_id.'"'.'>'.$blgdetail["teaser"].'</a>';
					 $readmorelnk='<a class="linkpanal1" href="'.$page_config['blog_comment']['URL'].'?blog_id='.$thread_id.'"'.'>Read More</a>';
					 $viewpre=$page_config['blog']['URL']."?author_id=$user_login_id";
					 $blogentry=$page_config['blog_entry']['URL'];
					 $tags=$bloginstance->getAllBlogTags($thread_id,'Blogs');
					 $usersblogs.='<tr>
					 <td align="left" style="padding-left:4px;"><br />
					 '.$title_link.'<br />
					 <span class="profilesugg">'.$tags.'</span>
					 <br /></td>
					 </tr>
					 <tr>
					 <td align="left" style="padding-left:4px;">'.$viewteaser.'<br />
					 <span class="profileReadMore">'.$readmorelnk.'</span><br />
					 <br />
					 <span class="profileBlogsYouFollowss">'.$viewlink.'</span>
					 </td>
					</tr>';
					 }
					 $usersblogs.='<tr>
					 <td style="padding-top:10px;padding-left:4px;" class="profileBlogsYouFollow"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>';
					 $usersblogs.="<td align='left' width='50%'><a href='$blogentry'><img src='$IMG_SERVER/images/community_images/add-new-entry.gif' vAlign='absmiddle' border='0' /></a></td><td width='55%' style='padding-top:5px;' align='right'><a href=$viewpre class='ViewPreviouslink'>View Previous Entries</a></td></tr></table> <br /></td></tr>";
					 $usersblogs.='</td></tr></table></td></tr></table>';
					 echo $usersblogs;
					 }else{
					 $blogentry=$page_config['blog_entry']['URL'];
					 $msg=$lang['no_ownblogs'];
					 $usersblogs.='<tr><td align="left" style="padding-left:10px;padding-top:10px;padding-bottom:10px;" class="profileBlogBox" >'.$msg.'</td></td></tr>';
					 $usersblogs.='<tr>
					 <td align="left" style="padding-left:10px;" class="profileBlogsYouFollow">';
					 $usersblogs.="<a href='$blogentry'><img src='$IMG_SERVER/images/community_images/add-new-entry.gif' border='0' /></a></td></tr>";
					 $usersblogs.='</td></tr><tr><td>&nbsp;</td></tr></table></td></tr></table>';
					 echo $usersblogs;
					 }
	}else if(($guest_id!=0)&&($curntuser!=0)){
	if($guestfname!='Stranger'){
	//echo $loginuserfullname." is trying to see ".$guestfname."<br>";
	// First Try to Display authors Blog
	$usersblogs='<script src="'.$CDN_SERVER.'/js/blogsubscribe.js" type="text/javascript"></script>';
	//echo "$loginuserfullname trying to see his own BLOGS";
   //select those blogs created by u
	$LIMIT=5;
	$count=0;
	$userblogsdetailqry="select id,title,teaser,thread_posts from ex_thread where author_id='$guest_id' and is_user_blog='1' and enabled='1' and approved='1' order by created_on DESC LIMIT $LIMIT";
	$userblogsdetailqryresult = exec_query($userblogsdetailqry);
	$count=count($userblogsdetailqryresult);
	$bloginstance=new Blogs();
	$usersblogs.='<table border="0"  width="100%" style="border-left:1px solid #cccccc; padding:0px; margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc;   padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
	<tr>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
	<td>
	<table width="100%" border="0" style="padding:0px; margin:0px; border-bottom:1px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="left">
	<tr align="center" valign="top">
	<td colspan="2" valign="top" class="ProfileBlogs">';
	$usersblogs.=$guestfname."'s Blog</td>";
	$usersblogs.='</tr>
	</table>
	</td>
	</tr>';
	if($count>0){
//***		foreach(exec_query($userblogsdetailqry) as $blgdetail){
			foreach($userblogsdetailqryresult as $blgdetail)
			{
			$tags='';
			$thread_id=$blgdetail['id'];
			$title=$blgdetail['title'];
			$title_link='';
			$title_link='<a class="Blogpanal" href="'.$page_config['blog_comment']['URL'].'?blog_id='.$thread_id.'"'.'>'.ucwords($title).'</a>';
			$viewlink='<a href="'.$page_config['blog_comment']['URL'].'?blog_id='.$thread_id.'"'.'>View Comments ('.$blgdetail["thread_posts"].')</a>';
			$viewteaser='<a class="teaserblog" href="'.$page_config['blog_comment']['URL'].'?blog_id='.$thread_id.'"'.'>'.$blgdetail["teaser"].'</a>';
			$readmorelnk='<a class="linkpanal1" href="'.$page_config['blog_comment']['URL'].'?blog_id='.$thread_id.'"'.'>Read More</a>';
			$viewpre=$page_config['blog']['URL']."?author_id=$user_login_id";
			$blogentry=$page_config['blog_entry']['URL'];
			$tags=$bloginstance->getAllBlogTags($thread_id,'Blogs');
			$usersblogs.='<tr>
			<td align="left" style="padding-left:10px;"><br />
			'.$title_link.'<br />
			<span class="profilesugg">'.$tags.'</span>
			<br /></td>
			</tr>
			<tr>
			<td align="left" style="padding-left:10px;">'.$viewteaser.'<br />
			<span class="profileReadMore">'.$readmorelnk.'</span><br />
			<br />
			<span class="Blogpanal1">'.$viewlink.'</span>
			</td>
			</tr>';
		}

		$qrychk="select id,subscription_status from ex_blog_subscribed where subscribed_to='$guest_id' and user_id='$curntuser'";
		//echo $qrychk;
		$authrs=exec_query($qrychk,1);
		if(count($authrs)>0){
		$retid=$authrs['id'];
		$curntstatus=$authrs['subscription_status'];
		$viewpre=$page_config['blog']['URL']."?author_id=$user_login_id";
			if($curntstatus=='1'){
				$action='remove';
				$img_display="$IMG_SERVER/images/community_images/unsubscribe-to-blog.gif";
			}else if($curntstatus=='0'){
				$action='update';
				$img_display="$IMG_SERVER/images/community_images/Subscribe-To-Blog.gif";
			}
			$usersblogs.='<tr><td style="padding-top:10px;padding-left:10px;" class="profileBlogsYouFollow"><div id="statusdiv"><table width="100%" cellpadding="0" cellspacing="0" border="0">';
			$usersblogs.='<tr><td width="50%"><a style="cursor:pointer" onClick=javascript:subscribe1('.$curntuser.','.$user_login_id.',"'.$action.'",'.$retid.') >';
			$usersblogs.="<img src='$img_display' vAlign='absmiddle' /></a></td><td width='50%' style='padding-top:5px;' align='right'><a href=$viewpre class='ViewPreviouslink'>View Previous Entries</a></td></tr></table></div><br /></td></tr>";
			}else{
				$alreadysubscrbd=0;
				$action='insert';
				$retid=0;
				$img_display="$IMG_SERVER/images/community_images/Subscribe-To-Blog.gif";
				$usersblogs.='<tr><td style="padding-left:10px;" class="profileBlogsYouFollow"><div id="statusdiv"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>';
				$usersblogs.='<td width="50%"><a style="cursor:pointer" onClick=javascript:subscribe('.$curntuser.','.$user_login_id.',"'.$action.'",'.$retid.') >';
				$usersblogs.="<img src='$img_display' vAlign='absmiddle'/></a></td><td width='50%' style='padding-top:5px;' align='right'><a href=$viewpre  class='ViewPreviouslink'>View Previous Entries</a></td></tr></table></div><br /></td></tr>";
			}
		$usersblogs.='</td></tr></table></td></tr></table>';
		echo $usersblogs;
	}else{
			$blogentry=$page_config['blog_entry']['URL'];
			$msg=$guestfname.' '.$lang['has_no_Blogs'];
			$usersblogs.='<tr><td align="left" style="padding-left:10px;padding-top:10px;padding-bottom:10px;" class="profileBlogBox" >'.$msg.'</td></td></tr>';
			// Display the Button for different conditions
			/*******************/
								$qrychk="select id,subscription_status from ex_blog_subscribed where subscribed_to='$guest_id' and user_id='$curntuser'";
								//echo $qrychk;
								$authrs=exec_query($qrychk,1);
								if(count($authrs)>0)
								{
									$retid=$authrs['id'];
									$curntstatus=$authrs['subscription_status'];
									$viewpre=$page_config['blog']['URL']."?author_id=$user_login_id";
									if($curntstatus=='1'){
										$action='remove';
										$img_display="$IMG_SERVER/images/community_images/unsubscribe-to-blog.gif";
									}else if($curntstatus=='0'){
										$action='update';
										$img_display="$IMG_SERVER/images/community_images/Subscribe-To-Blog.gif";
									}

									$usersblogs.='<tr><td style="padding-left:10px;" class="profileBlogsYouFollow"><div id="statusdiv"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>';
									$usersblogs.='<td width="50%"><a style="cursor:pointer" onClick=javascript:subscribe1('.$curntuser.','.$user_login_id.',"'.$action.'",'.$retid.') >';
									$usersblogs.="<img src='$img_display' vAlign='absmiddle' /></a></td><td width='50%' style='padding-top:5px;' align='right'><a href=$viewpre  class='ViewPreviouslink'>View Previous Entries</a></td></tr></table></div><br /></td></tr>";
								}else{
									$viewpre=$page_config['blog']['URL']."?author_id=$user_login_id";
									$alreadysubscrbd=0;
									$action='insert';
									$retid=0;
									$img_display="$IMG_SERVER/images/community_images/Subscribe-To-Blog.gif";
									$usersblogs.='<tr><td style="padding-left:10px;" class="profileBlogsYouFollow"><div id="statusdiv"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>';
									$usersblogs.='<td width="50%"><a style="cursor:pointer" onClick=javascript:subscribe('.$curntuser.','.$user_login_id.',"'.$action.'",'.$retid.') >';
									$usersblogs.="<img src='$img_display' vAlign='absmiddle' /></a></td><td width='50%' style='padding-top:5px;' align='right'><a href='$viewpre'  class='ViewPreviouslink'>View Previous Entries</a></td></tr></table></div><br /></td></tr>";
								}
			/*******************/

			$usersblogs.='</td></tr></table></td></tr></table>';
			echo $usersblogs;
		}
		}else{
		//echo $loginuserfullname." is trying to see ".$guestfname."'s Profile";
		$usersblogs='';
		$usersblogs.='<table border="0"  width="99%" style="border-left:1px solid #cccccc; padding:0px; margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
		<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
		<tr>
		<td>
		<table width="100%" style="padding:0px; margin:0px; border-bottom:0px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
		<tr>
		<td valign="top" style="padding:0px; margin:0px; border-bottom:1px solid #cccccc;padding:0px;margin:0px;" class="ProfileBlogs">';
		$usersblogs.=$guestfname."'s Blog</td>";
		$usersblogs.='</tr><tr><td style="padding-left:10px;padding-top:10px;padding-bottom:10px;" class="profileBlogBox">'.$guestfname.' '.$lang["stanger_profile"].'</td>
		</td><td>
		</tr>
		</table>
		</td>
		</tr></table></td></tr></table>';
		echo $usersblogs;
	}
}
}
function follow_subscrbd_blogs($user_login_id,$newemailchk,$msg,$unsubscribeid,$start,$end){
	global $blogsubscrcnt,$CDN_SERVER;

	if((!isset($start)) && (!isset($end))){$start=0;$end=0+$blogsubscrcnt;$lmt='';}else{
		$startlmt=$start;
		$endlmt=$end;
		$lmt=" LIMIT ".$startlmt.",".$endlmt;
		//$start=$start+5;
	}


					 global $page_config,$lang,$USER,$HTPFX,$HTHOST;
					 $pageName = "profile";
					 build_lang($pageName);
					 global $lang;

					 $visitors_id=0;
					 $retid=0;
					 $dontedit=0;
					 $exobj=new Exchange_Element();

					 if(isset($user_login_id)){
						 $curntuser=$USER->id;
						 $name_arr=$exobj->userInfo($curntuser);
						 $loginuserfullname=ucwords(strtolower($name_arr['fname'].' '.$name_arr['lname']));
						 $loginuserfname=ucwords(strtolower($name_arr['fname']));
						 if($user_login_id!=$curntuser){
							 $visitors_id=$user_login_id;
							 $guestname_arr=$exobj->userInfo($visitors_id);
							 if(count($guestname_arr)!=0){
								 $guestfullname=ucwords(strtolower($guestname_arr['fname'].' '.$guestname_arr['lname']));
								 $guestfname=ucwords(strtolower($guestname_arr['fname']));
							 }else{
								 $guestfname="Stranger";
							 }
						 }else{
							 // when u are watching your own profile then the guest_id='';
							 $visitors_id=0;
						 }
					 }


				 // global $page_config,$lang,$HTPFX,$HTHOST;
				 //$caption=$lang['blogs_u_follow'];
				 if(isset($unsubscribeid))
				 {
					 $curdate_time=date('Y-m-d H:i:s');
					 $tablename='ex_blog_subscribed';
					 $req_up['subscription_status']='0';
					 $req_up['unsubscribed_on']=$curdate_time;
					 $ret_up=update_query($tablename,$req_up,array(subscribed_to=>$unsubscribeid,user_id=>$user_login_id));
					 if($ret_up){
						//follow_subscrbd_blogs($user_login_id);
						 unset($unsubscribeid);
					 }else{
						 //echo "Error while updating.....";
						//follow_subscrbd_blogs($user_login_id);
						 unset($unsubscribeid);
					 }
				 }

				 if(!isset($newemailchk)){
					 global $dontedit;
					 $authorlink=$page_config['profile']['URL'];

					 if(($visitors_id!=0)&&($curntuser!=0)){
					 $qry_authors1="select concat(S.fname,' ',lname) name,EBS.subscribed_to author_id from subscription S,ex_blog_subscribed EBS where EBS.User_id='$visitors_id' and EBS.subscription_status='1' and EBS.subscribed_to=S.id order by EBS.subscribed_on DESC";
					 $count1=count(exec_query($qry_authors1));
					 $fixedresults=$count1;

					 $qry_authors="select concat(S.fname,' ',lname) name,EBS.subscribed_to author_id from subscription S,ex_blog_subscribed EBS where EBS.User_id='$visitors_id' and EBS.subscription_status='1' and EBS.subscribed_to=S.id order by EBS.subscribed_on DESC $lmt";
					 $dontedit=1;
					 }else{

						 $qry_authors1="select concat(S.fname,' ',lname) name,EBS.subscribed_to author_id from subscription S,ex_blog_subscribed EBS where EBS.User_id='$curntuser' and EBS.subscription_status='1' and EBS.subscribed_to=S.id order by EBS.subscribed_on DESC";
						 $count1=count(exec_query($qry_authors1));
						 $fixedresults=$count1;

						 $qry_authors="select concat(S.fname,' ',lname) name,EBS.subscribed_to author_id from subscription S,ex_blog_subscribed EBS where EBS.User_id='$curntuser' and EBS.subscription_status='1' and EBS.subscribed_to=S.id order by EBS.subscribed_on DESC $lmt";
						 $dontedit=0;
					 }
					 //echo "***********".$qry_authors;

					 $count=0;
					 $qryresultsets = exec_query($qry_authors);
					 $count=count($qryresultsets);

					 if($count>0){
						 $ownsubscribedBlogsdetail='<script src="'.$CDN_SERVER.'/js/followblogedit.js" type="text/javascript"></script>';
						 $ownsubscribedBlogsdetail.='<table border="0" style="border-left:1px solid #cccccc;padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center" width="100%">
						 <tr>
						 <td>
						 <table cellpadding="0" cellspacing="0" width="100%" border="0" bordercolor=red>
						 <tr>
						 <td colspan=2 width="100%">
						 <table width="100%" style="padding:0px; margin:0px; border-bottom:1px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
						 <tr align="center" valign="top">';

						 if($dontedit==0){
						 $ownsubscribedBlogsdetail.='<td height="21" valign="top" class="ProfileBlogs"> Blogs You Follow</td>';
						 }else{
						 $ownsubscribedBlogsdetail.='<td height="21" valign="top" class="ProfileBlogs"> Blogs '.$guestfname.' Follows</td>';
						 }
						 $ownsubscribedBlogsdetail.='</tr>
						 </table>
						 </td>
						 </tr><tr><td>&nbsp;</td></tr>';
						 $authorsids='';
						 $cntlnk=0;
						 //start of foreach(exec_query($qry_authors) as $subscrbdblog)
						 //foreach(exec_query($qry_authors) as $subscrbdblog){
							foreach($qryresultsets as $subscrbdblog)
							{
							 $author=ucwords(strtolower($subscrbdblog['name']));
							 $author_id=$subscrbdblog['author_id'];
							 $bloglink=$page_config['blog']['URL'].'?author_id='.$author_id;

							 $readbloglnk='<a class="Blogpanal1" href="'.$bloglink.'">Read Blog</a>';
							 if($authorsids==''){
								 $authorsids=$author_id;
							 }else{
								 $authorsids.=','.$author_id;
							 }
							 $ownsubscribedBlogsdetail.='<tr>
							 <td height="28" class="profileBlogBox" style="padding-left:10px;" width="50%"><span id="subauth_'.$author_id.'"><a class="Blogpanal" href="'.$authorlink.'?userid='.$author_id.'">'.$author.'</a></span></td><td><nobr>- <span class="profileBlogsYouFollow">'.$readbloglnk.'</span>';

							 if($msg==''){
							 $ownsubscribedBlogsdetail.='<span class="profileBlogsYouFollow" id="subauthlnk_'.$author_id.'"></span>';
							 }else{
								 $ownsubscribedBlogsdetail.='<span class="profileBlogsYouFollow" id="subauthlnk_'.$author_id.'"><a class="Blogpanal1" style="cursor:pointer" onclick=javascript:removeid("'.$user_login_id.'","'.$author_id.'")>&nbsp;&nbsp;Remove</a></span>';
							 }
							 $ownsubscribedBlogsdetail.='</td>
							 </tr>';
							 $cntlnk++;
							 $chkres=$cntlnk+$start;

							 if(($chkres<$fixedresults) && ($cntlnk==$blogsubscrcnt)){
 							 ///$ownsubscribedBlogsdetail.='<tr><td class="profileBlogBox" style="padding-left:10px;" width="50%"><a>Left</a></td><td><nobr>- <span class="profileBlogsYouFollow">Right</span></td></tr>';
								 $ownsubscribedBlogsdetail.='<tr><td colspan=2 style="padding-top:5px;"><table border="0" cellspacing=0 cellpadding=10 width="100%">
								 <td width="50%" style="padding-left:10px;" align="right">';
									 if($start!=0){
									 $ownsubscribedBlogsdetail.='<a class="viewnav" style="cursor:pointer;float:right;padding-right:10px;" onClick=javascript:makeprevlinks("'.$user_login_id.'","'.$newemailchk.'","'.$msg.'","'.$unsubscribeid.'","'.$start.'","'.$end.'")>&lt; Previous</a>';
									 }
								$ownsubscribedBlogsdetail.='</td>
								<td width="50%" style="padding-left:2px;"><a class="viewnav"  style="cursor:pointer;" onClick=javascript:makenextlinks("'.$user_login_id.'","'.$newemailchk.'","'.$msg.'","'.$unsubscribeid.'","'.$start.'","'.$end.'")>Next ></a></td></tr></table></td></tr>';
								break;
							 }
							 else if(($chkres>=$fixedresults)){
								 $ownsubscribedBlogsdetail.='<tr><td colspan=2 style="padding-top:5px;"><table border="0" cellspacing=0 cellpadding=0 width="100%">
								 <td width="50%" style="padding-left:10px;">';
								 if($start!=0){
								 $ownsubscribedBlogsdetail.='<a class="viewnav"  style="cursor:pointer;float:right;padding-right:10px;" onClick=javascript:makeprevlinks("'.$user_login_id.'","'.$newemailchk.'","'.$msg.'","'.$unsubscribeid.'","'.$start.'","'.$end.'")>&lt; Previous</a>';
							 }
								 $ownsubscribedBlogsdetail.='&nbsp;</td>
								 <td width="50%" style="padding-right:10px;padding-left:2px;">
								 &nbsp;</td></tr></table></td></tr>';
								 break;
								 }
							 }// end of foreach(exec_query($qry_authors) as $subscrbdblog)
							 if($msg!=''){
							 $ownsubscribedBlogsdetail.='<tr>
							 <td height="20" colspan="2"><div align="left" style="color:#990000; padding-left:10px">'.$msg.'</div></td>
							 </tr>
							 <tr>';
						 }

					 if($msg!=''){
					 $ownsubscribedBlogsdetail.='<td colspan="2">
					 <table border=0 width=100% cellpadding=0 cellspacing=0>
					 <tr><td vAlign="center" width="53%"><span class="homerequests" style="padding-left:10px;">
					 <input align="absmiddle" type="text" id="to_email" style="height:22px;">
					 </span></td>
					 <td vAlign="center">';
					 $ownsubscribedBlogsdetail.='<input type="image" style="border:hidden;" align="absmiddle" src="'.$IMG_SERVER.'/images/community_images/blog_subscribe.gif" onclick=javascript:validateemail("'.$user_login_id.'","'.$authorsids.'","'.$IMG_SERVER.'") onchange=javascript:validateemail("'.$user_login_id.'","'.$authorsids.'","'.$IMG_SERVER.'") />
					 </td></tr>
					 <tr><td colspan="2">&nbsp;</td></tr></table>
					 </td>
					 </tr>';
					 }else{
						 if($dontedit==0){
					 	 $ownsubscribedBlogsdetail.='<tr>
						 <td height="20" colspan="2" style="padding-bottom:5px;"><div id="editlnk"><span class="homerequests" style="padding-left:10px;"><a class="profilesugg" onClick=javascript:modifystructure("'.$user_login_id.'","'.$authorsids.'","'.$IMG_SERVER.'") style="cursor:pointer;">Edit List</a></span></div></td>
						 </tr>';
						 }
					 }

						 $ownsubscribedBlogsdetail.='</table></td></tr></table>';
						 echo $ownsubscribedBlogsdetail;
					 }else if($count==0){

						 if($dontedit==0){
							 global $lang;
								 $ownsubscribedBlogsdetail.='<table border="0"  width="100%" style="border-left:1px solid #cccccc; padding:0px; margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
								 <tr>
								 <td>
								 <table cellpadding="0" cellspacing="0" width="100%" border="0">
								 <tr>
								 <td>
								 <table width="100%" style="padding:0px; margin:0px; border-bottom:1px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
								 <tr align="center" valign="top">
								 <td height="21"  colspan="2" valign="top" class="ProfileBlogs">';
								 $ownsubscribedBlogsdetail.="Blogs You Follow</td>";
								 $ownsubscribedBlogsdetail.='</tr>
									 </table>
									 </td>
									 </tr>
									 <tr>
									 <td align="left" class="profileBlogBox" style="padding-left:10px;"><br />'.$lang["no_own_blogs_tofollow"].'<br />&nbsp; </td>
								 </tr>
								 </td></tr></table></td></tr></table>';


							 echo $ownsubscribedBlogsdetail.'</div>';
						 }else{

							 $ownsubscribedBlogsdetail.='<table border="0"  width="100%" style="border-left:1px solid #cccccc; padding:0px; margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc;   padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
							 <tr>
							 <td>
							 <table cellpadding="0" cellspacing="0" width="100%" border="0">
							 <tr>
							 <td>
							 <table width="100%" style="padding:0px; margin:0px; border-bottom:1px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
							 <tr align="center" valign="top">
							 <td colspan="2" valign="top" class="ProfileBlogs">';
							 $ownsubscribedBlogsdetail.="Blogs ".$guestfname." Follows</td>";
							 $ownsubscribedBlogsdetail.='</tr>
							 </table>
							 </td>
							 </tr>
							 <tr>
							 <td class="profileBlogBox" align="left" style="padding-left:10px;"><br />'.$guestfname.' '.$lang["no_blogsfollow"].'<br />&nbsp; </td>
							 </tr>
							 </td></tr></table></td></tr></table>';

									echo $ownsubscribedBlogsdetail.'</div>';
								 }
					 }
				 }else{
					 if(isset($newemailchk)){
						# check the email passed is an exchange user or not
						 $chkvaliduser="select id from subscription where email like '".$newemailchk."' and is_exchange='1'";
						 //echo $chkvaliduser;
						 $chkvaliduserarr=exec_query($chkvaliduser,1);

						 if(count($chkvaliduserarr)>0)
						 {
							 $desireauthor=$chkvaliduserarr['id'];
							 if($desireauthor==$user_login_id){
								 //$msg="U can not subscribe ur own BLOGS";
								 $msg=$lang['own_blogs_cant_subcrbd'];
								 unset($newemailchk);
								 follow_subscrbd_blogs($user_login_id,$aa,$msg);
							 }else{
							# U want to subscribe another $desireauthor
								 $chkforprevious="select id,subscription_status from ex_blog_subscribed where subscribed_to='$desireauthor' and user_id='$user_login_id'";
								 $authrs=exec_query($chkforprevious,1);
								 $retid=$authrs['id'];
								 $curntstatus=$authrs['subscription_status'];

								 if((count($authrs)>0)&&($curntstatus=='1')){// previously subscribed
									 // if status=1 already active and if status=0 just update....
									 //$msg="You have already subscribed this Author......";
									 $msg=$lang["u_subcbd_this_authr"];
									 unset($newemailchk);
									 follow_subscrbd_blogs($user_login_id,$aa,$msg);
								 }else if((count($authrs)>0)&&($curntstatus=='0')){
								# Try to update
								 $curdate_time=date('Y-m-d H:i:s');
								 $tablename='ex_blog_subscribed';
								 $req_update['subscription_status']='1';
								 $req_update['subscribed_on']=$curdate_time;
								 $ret_up=update_query($tablename,$req_update,array(id=>$retid,user_id=>$user_login_id));
								 follow_subscrbd_blogs($user_login_id);
								 }else if(count($authrs)==0){
								# Try to subscribe
									 $subscriptiondata=array(
										 subscribed_to=>$chkvaliduserarr['id'],
										 user_id=>$user_login_id,
										 subscribed_on=>date('Y-m-d H:i:s'),
										 subscription_status=>1
										 );
									 insert_query("ex_blog_subscribed",$subscriptiondata);
									 follow_subscrbd_blogs($user_login_id);
								 }
							 }
						 }else{
							 //$msg="Invalid Author Email id......";
							 global $lang;
							 $msg=$lang['invalidid'];
							 unset($newemailchk);
							 follow_subscrbd_blogs($user_login_id,$aa,$msg);
						 }
					 }
				 }
			 }//end of function follow_subscrbd_blogs


function get_user_profile($subscription_id)
{
$sqlGetProfile="select EPA.attribute,EPAM.value from subscription S,ex_user_profile EUP,ex_profile_attribute_mapping EPAM, 			ex_profile_attribute EPA where
S.id=EUP.subscription_id
and EUP.id=EPAM.profile_id
and EPAM.attribute_id=EPA.id
and S.id=".$subscription_id;
	$resGetProfile=exec_query($sqlGetProfile);
	foreach($resGetProfile as $id=>$value)
	{
		$profile[$value['attribute']]=$value['value'];
	}
	return $profile;
}

function match_watchlist($subscription_id)
{
	$sqlMatchWatchList="select ES.id,ES.stocksymbol,S.id sid from ex_stock ES,ex_user_stockwatchlist EUSW,subscription S where
S.id=EUSW.subscription_id
and EUSW.stock_id=ES.id
and S.id!=".$subscription_id." and ES.id in (select ES.id from ex_stock ES,ex_user_stockwatchlist EUSW where EUSW.stock_id=ES.id and EUSW.subscription_id=".$subscription_id.")";

	$resMatchWatchList=exec_query($sqlMatchWatchList);
	foreach($resMatchWatchList as $id=>$value)
	{
		$machedWatchlist[$value['sid']][]=$value['stocksymbol'];
	}
	return $machedWatchlist;
}

function getSubscriptionInfo($subscription_id)
{
	$sqlSub="select * from subscription where id=".$subscription_id;
	$resSub=exec_query($sqlSub,1);
	return $resSub;
}

//function for Display Recomended Articles
function display_recomendedArticles($user_login_id,$caption){
	global $SHOW_RECNT_BLOG,$page_config,$lang,$suggestedartlmt,$D_R;
	include_once("$D_R/lib/layout_functions.php");
	$caption=$lang[$caption];
	$qry="select A.id id,A.title,A.contrib_id,A.date created_on from ex_user_stockwatchlist EUSW,subscription S, ex_stock ES, articles A, ex_item_tags EIT,ex_tags ETAGS where EUSW.subscription_id=S.id and EUSW.stock_id=ES.id and ES.stocksymbol=ETAGS.tag and ETAGS.type_id=1 and ETAGS.id =EIT.tag_id and EIT.item_id=A.id and EIT.item_type=1 and  S.id='$user_login_id' group by A.id  order by created_on DESC LIMIT $suggestedartlmt";
	$qryresultset = exec_query($qry);
	$count=count($qryresultset);
	if($count>0){
		$display_recmended='<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
			<td>
			<table border="0" width="100%" style="border-left:1px solid #cccccc; padding:0px; margin:0px; border-top:0px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
			<tr>
			<td backgroundcolor="#00ff00">
				<table cellpadding="0" cellspacing="0" width="100%" border="0">
				<tr>
				<td colspan="2" style="padding-bottom:10px;">
				<table width="100%" style="padding:0px; margin:0px; border-bottom:1px solid #cccccc; border-top:1px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
				<tr align="center" valign="top">
				<td  colspan="3" valign="top" class="ProfileBlogs">'.$caption.'</td>
				</tr>
				</table>
			</td>
			</tr>';
			//**** foreach(exec_query($qry) as $recntdiscrec){
			foreach($qryresultset as $recntdiscrec)
			{
			$id=$recntdiscrec['id'];
			$author_id=$recntdiscrec['author_id'];
			$title=$recntdiscrec['title'];
			$tagstodisplay='';

			$articledetails=makeArticleslink_sql($id);
			$articlelink='';
			foreach($articledetails as $postkey1=> $postval1)
			{
				$keyword=$postval1['keyword'];
				$blurb=$postval1['blurb'];
				$articlelink=makeArticleslink($id,$keyword,$blurb);
			}
			$threadobj= new Thread();
			$type_get=$threadobj->get_object_type('Articles');
			$type=$type_get['id'];
			$tagsarray=$threadobj->get_tags_on_objects($id, $type);
			$tagstodisplay=$threadobj->displayTagsonly($type,$tagsarray);

			$display_recmended.='<tr>
				<td  align="left" valign="top">
				<table width="300px" border="0" cellspacing="3" cellpadding="0">
				<tr>
					<td colspan="2" style="padding-left:10px;" class="SearchBlogsYouFollow"><a class="SuggestedArticleBox1" href='.$articlelink.'>'.ucfirst($title).'</a></td>
					</tr>
					<tr><td style="padding-left:10px;"><span class="profilesugg">'.$tagstodisplay.'</span></td>
				</tr>
				</table>
				</td>
				</tr>';
		}
		$display_recmended.='</table>
				</td>
				</tr>
				</table>
				</td>
				</tr>
				</table><br />';
		echo $display_recmended;
			}else{
		$display_recmended='<table border="0" style="border-left:1px solid #cccccc;padding:0px;margin:0px; border-top:1px solid #cccccc; border-right:1px solid #cccccc; border-bottom:1px solid #cccccc; padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center" width="100%">
			<tr>
			<td>
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
			<td colspan="2">
			<table width="100%" border="0" style="padding:0px;margin:0px; border-bottom:1px solid #cccccc; padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
			<tr>
			<td valign="top" class="ProfileBlogs">'.$caption.'</td>
		    </tr>
			</table>
		   </td>
		   </tr>';
		$display_recmended.='<tr>
		<td align="left" style="padding-top:10px;padding-bottom:10px; padding-left:10px;" colspan="2" class="SuggestedArticleBox">'.$lang["No_Suggested_Articles"].'
		<span class="profilesugg">'.$tagstodisplay.'</span></td></tr>
		</table></td></tr></table>';
		echo $display_recmended;
		//echo "No result";
		}
}

	function getAlertStatus($event,$subId=NULL){
		$resEmail=exec_query("select ES.subscription_id, ES.email_id, ES.alert, ET.template_path, ET.event, email_body,
				  email_subject from ex_user_email_settings ES, ex_email_template ET where ES.email_id=ET.id and
				  ES.subscription_id='".$subId."' and ES.alert='On' and ET.event='".$event."'",1);
		if(count($resEmail)>0){
			return $resEmail;
		}
		else{
			return null;
		}
  	}

	function getAlertBody($event,$sender_id=NULL,$subId=NULL,$threadid=NULL,$threadTitle=NULL,$recipient_email=NULL,$pagename=NULL, $reportattribute=NULL,$articleTitle=NULL,$watchlist=NULL){

		global $HTHOST, $HTPFX, $page_config, $MODERATOR_EMAIL;
		$threadTitle=htmlentities($threadTitle,ENT_QUOTES);
		$threadTitle='<b>'.$threadTitle.'</b>';
		$articleTitle=htmlentities($articleTitle,ENT_QUOTES);
		$articleTitle='<b>'.$articleTitle.'</b>';
		$getsubs=exec_query("select fname, lname, email from subscription where id=$subId",1);
		$getsender=exec_query("select fname,lname,email from subscription where id=$sender_id",1);
		$subFname =ucwords(strtolower($getsubs[fname]));
		$subLname = $getsubs[lname];
		$subEmail = $getsubs[email];
		$senderName = $getsender[fname].' '.$getsender[lname];
		$resBody=exec_query("select * from ex_email_template where event='".$event."'",1);
		$path=$resBody['template_path'];
		$SUB_EML_TMPL="$HTPFX$HTHOST".$path;
		$body=$resBody[email_body];
		$pat[] = "/\[user's fname\]/";
		$rep[] = $subFname;
		if($event=='ReportAbuseToModerator')
		{
			$pat[] = "/\[Name\]/";
			$rep[] = $subFname." ".ucwords(strtolower($subLname));
		}
		else if($event=='NewArticlesRelatedWatchlist')
		{
			$pat[] = "/\[Name\]/";
			$rep[] = $subFname;
		}
		else
		{
			$pat[] = "/\[Name\]/";
			$rep[] = ucwords(strtolower($senderName));
		}
		if($event=='friendRequest'){
			$pat[] = "/\[link\]/";
			$rep[] = $page_config['home']['URL'];
			$pat[] = "/\[linktext\]/";
			$rep[] = $page_config['home']['URL'];
		}
		elseif($event=='NewMessage'){
			$pat[] = "/\[Message_body\]/";
			$rep[] = $threadid;
			$pat[] = "/\[link\]/";
			$rep[] = $page_config['inbox']['URL'];
			$pat[] = "/\[linktext\]/";
			$rep[] = $page_config['inbox']['URL'];
		}
        elseif($event=='Invite'){
            $pat[] = "/\[recipient_email\]/";
			$rep[] = $recipient_email;
			$pat[] = "/\[link\]/";
			$rep[] = $page_config['login']['URL'];
			$pat[] = "/\[linktext\]/";
			$rep[] = $page_config['login']['URL'];
		}
		elseif($event=='ReportAbuse'){
		 	$pat[] = "/\[user's fname\]/";
			$rep[] = $subFname;
			$pat[] = "/\[link\]/";
			$rep[] = $page_config[$pagename]['URL']."?".$reportattribute;
			$pat[] = "/\[administratoremail\]/";
			$rep[] = $MODERATOR_EMAIL;
		}
		elseif($event=='ReportAbuseToModerator'){
			$pat[] = "/\[recipient_email\]/";
			$rep[] = $subEmail;
			$pat[] = "/\[link\]/";
			$rep[] = $page_config[$pagename]['URL']."?".$reportattribute;
			$pat[] = "/\[administratoremail\]/";
			$rep[] = $MODERATOR_EMAIL;
		}
		elseif($event=='welcome'){
			$pat[] = "/\[link\]/";
			$rep[] = $page_config['login']['URL'];
			$pat[] = "/\[administratoremail\]/";
			$rep[] = $MODERATOR_EMAIL;
		}
		elseif($event=='ReplyDirectPost'){
			$pat[] = "/\[thread-title\]/";
			$rep[] = urlencode($threadTitle);
			$pat[] = "/\[link\]/";
			$rep[] = $page_config['single_discussion']['URL']."?thid=".$threadid;
			$pat[] = "/\[linktext\]/";
			$rep[] = $page_config['single_discussion']['URL']."?thid=".$threadid;
		}
		elseif($event=='NewArticlesRelatedWatchlist'){
			$pat[] = "/\[article-title\]/";
			$rep[] = urlencode($articleTitle);
			$pat[] = "/\[watchlist\]/";
			//$threadTitle='<b>'.$threadTitle.'</b>';
			$rep[] = '<b>'.$watchlist.'</b>';
			$pat[] = "/\[link\]/";
			$rep[] = $page_config['home']['URL'];
			$pat[] = "/\[linktext\]/";
			$rep[] = $page_config['home']['URL'];
		}
		elseif($event=='SubscribedBlogsAlert'){
				$pat[] = "/\[link\]/";
				$rep[] = $page_config['home']['URL'];
				$pat[] = "/\[linktext\]/";
				$rep[] = $page_config['home']['URL'];
		}
/*		elseif($event=='userblocking'){
			$pat[] = "/\[privileges\]/";
			$rep[] = '<b>'.$privileges.'</b>';
			$pat[] = "/\[link\]/";
			$rep[] = $page_config['home']['URL'];
			$pat[] = "/\[linktext\]/";
			$rep[] = $page_config['home']['URL'];
		}*/
		$emailBody = preg_replace($pat,$rep,$body);
		return urldecode($emailBody);
	}

	function sendAlert($subId=NULL,$sender_id=NULL,$resEmail,$event,$thread_id=NULL,$threadTitle=NULL,$pagename=NULL,$reportattribute=NULL,$articleTitle=NULL,$watchlist=NULL)
	{
		global $HTHOST,$HTPFX, $MODERATOR_EMAIL;
		$subId=$resEmail[subscription_id];
		$getsubs=exec_query("select fname, lname, email from subscription where id=$subId",1);
		$getsender=exec_query("select fname,lname,email from subscription where id=$sender_id",1);
		$subFname =$getsubs[fname];
		$subLname = $getsubs[lname];
		$subEmail = $getsubs[email];
		$senderName = $getsender[fname].' '.$getsender[lname];
		$subject=$resEmail[email_subject];
		$pat[] = "/\[Name\]/";
		$rep[] = $senderName;
		$subject = preg_replace($pat,$rep,$subject);

		if($subId!=$sender_id)
		{
			$path=$resEmail['template_path'];
			$SUB_EML_TMPL=$HTPFX.$HTHOST.$path;
			$from='"'.ucfirst(strtolower($getsender[fname]))." ".ucfirst(strtolower($getsender[lname])).'"<'.$getsender[email].'>';
			# call mail function
			$template="$SUB_EML_TMPL?subId=$subId&sender=$sender_id&event=$event";
			if($thread_id)
				$template.="&thread_id=$thread_id";
			if($threadTitle)
				$template.="&thread-titled=".urlencode($threadTitle);
			if($event=='ReportAbuse'){
				$from = $MODERATOR_EMAIL;
				$template="$SUB_EML_TMPL?event=$event&subid=$subId&pagename=$pagename&reportattribute=$reportattribute";
			}
			if($event=='NewArticlesRelatedWatchlist'){
				$from = $MODERATOR_EMAIL;
				$subject=$resEmail[email_subject]." ".$watchlist;
				$template="$SUB_EML_TMPL?event=$event&to=$subId&articleTitle=".urlencode($articleTitle)."&watchlist=$watchlist";
			}
			// if add to friend
			if($event=='friendRequest'){
				$from=$MODERATOR_EMAIL;
			}
			if($event=='NewMessage'){
				$template="$SUB_EML_TMPL?subId=$subId&sender=$sender_id&event=$event&message=".urlencode($thread_id);
			}

			mymail("\"$subFname $subLname \"<$subEmail>",
				$from,
				$subject,
				inc_web($template)
				);
		}
	}

/**************************************/
	function is_friend($subscriberid,$viewerid){
	$isfrnd=0;
	if($subscriberid==$viewerid){
	$isfrnd=1;
	return $isfrnd;
	}
		// check for friendship
		$qry_relate="select concat(sub.fname, ' ' ,sub.lname)name from ex_user_friends ef, subscription sub where ef.friend_subscription_id=sub.id and sub.id='$subscriberid' and ef.subscription_id='$viewerid'";
		if(count(exec_query($qry_relate))>0){
			$isfrnd=1;
		}


		return $isfrnd;
	}

	function is_msg_allowed($peopleid,$userloginid)
	{

		// This function will return boolean
		$isfrnd=0;
		$abletosend='false';
		if($peopleid==$userloginid){
		return $abletosend;
		}


		// check the privacy setting of $peopleid
		$qry_id="select id from ex_privacy_attribute where attribute='Messagesfrom'";
		if(count(exec_query($qry_id))>0){
			$getid=exec_query($qry_id,1);

			$sql_permit="select enabled from ex_profile_privacy where subscription_id='$peopleid' and privacy_attribute_id='$getid[id]'";

			//echo "<br>",$sql_permit;

			if(count(exec_query($sql_permit))>0){
				$get_enabled_val_arr=exec_query($sql_permit,1);
				$get_enabled_val=$get_enabled_val_arr['enabled'];

				if($get_enabled_val=='All'){$abletosend='true';}

				else if($get_enabled_val=='Only Friends'){
					$isfrnd=is_friend($peopleid,$userloginid);
					if($isfrnd==1){$abletosend='true';}else{$abletosend='false';}
				}
				else if($get_enabled_val=='None'){$abletosend='false';}

			}else{
				//echo "Error : While Fetching Records......";
			}

		}else{
			//echo "Error: While fetching Attribute";
		}
		return $abletosend;
	}

	function displayRecentArticlesonEmail() {

	global $HTPFX, $HTHOST;

	$recentArticleQry = "select articles.id, articles.title,articles.keyword,articles.blurb from articles ";
	$recentArticleQry .= "where approved='1' and is_live='1' ";
	$recentArticleQry .= "order by date desc limit 3";
	$index=0;
		foreach(exec_query($recentArticleQry) as $recentArticlerow)
		{
			$title=$recentArticlerow['title'];
			$articleTitle=ucfirst($title);
			?>
			<tr>
				 <td align="left"  style="padding-left:10px;"><font color="#0d2244" face="Arial, Helvetica, sans-serif" size="2px"><!-- Title of articles-->
				 <a href='<?=$HTPFX.$HTHOST?>/articles/index.php?a=<?=$recentArticlerow[id]?>' style="text-decoration:none; color:#004080">
				 <?php echo $articleTitle; ?></a></font>
				</td>
		   </tr>
	<?
		}
		?>
		<tr><td style="padding:0px; margin:0px; line-height:5px;">&nbsp;</td></tr>
	<?
	}

	function inbox_conversation($id,$read_status,$offset="",$limit="") {

$convqry="select EMC.id,EMC.message_id,EMC.conversee1,EMC.conversee2,EMC.conv_date from ex_message_conversation EMC,ex_item_status EIS where EMC.id=EIS.item_id and EIS.item_type='5' and EIS.read_status='$read_status 'and EMC.conversee1='$id'and EIS.subscription_id='$id' and LOCATE(',',EMC.message_id)<>0  and (select count(*) from ex_private_message EPM where EPM.id in (EMC.message_id) )>0
union
select EMC.id,EMC.message_id,EMC.conversee1,EMC.conversee2,EMC.conv_date from ex_message_conversation EMC,ex_item_status EIS where EMC.id=EIS.item_id and EIS.item_type='5' and EIS.read_status='$read_status'and EMC.conversee2='$id' and EIS.subscription_id='$id' and (select count(*) from ex_private_message EPM where EPM.id in (EMC.message_id))>0  order by conv_date desc";

if($offset!=="" && $limit!==""){
  $convqry.=" limit $offset,$limit";
}
   $getconv=exec_query($convqry);
  	if(isset($getconv)){
		return $getconv;
		} else {
		return;
	}

}
function getSlideShowDeatil($slideid)
{
	$sql = "select * from slideshow where id=$slideid";
	$getDetail=exec_query($sql,1);
	return $getDetail;
}

function getUserDetailsByViaid($viaid){
	$qry="select via_id,fname from subscription where via_id='".$viaid."'";
	$result=exec_query($qry,1);
	if(isset($result)){
		return $result;
	}else{
		return 0;
	}
}
/*delete thread for unapproved/deleted articles*/
function deleteThread($keys){
	$qry="delete from ex_thread where find_in_set(content_id,'$keys') and content_table='articles'";
	exec_query_nores($qry);
}

?>
