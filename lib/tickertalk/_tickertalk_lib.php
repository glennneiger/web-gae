<?php
class ticker{

	function getsearchTicker($ticker,$tickerid=NULL){  /*search ticker in tt_topic table*/
		$qry="select S.stocksymbol,TT.id,TT.topic_name,TT.is_archived,TT.stock_id,TT.is_deleted,TT.is_approved,TT.archive_date from tt_topic TT, ex_stock S where TT.stock_id=S.id ";
		if($ticker){
			$qry.=" and S.stocksymbol='".$ticker."'";
		}else {
			$qry.=" and TT.id='".$tickerid."'";
		}
	    $getSearch=exec_query($qry,1);
	   if(isset($getSearch)){
	   		return $getSearch;
	   }else{
	   		return 0;
	   }
	}

   function getTickerStock($ticker){ /*search ticker in ex_stock table if not exist in tt_topic table*/
   		$qry="select id,stocksymbol from ex_stock where stocksymbol='".$ticker."'";
   		$getStockid=exec_query($qry,1);
		if($getStockid){
			$gettickerid=$this->setTopicTicker($getStockid['id'],$getStockid['stocksymbol']); /*Insert data in the tt_topic table*/
			return $gettickerid;
		}else{
			$validateticker=$this->getstockdetails($ticker); /*varify ticker from yahoo*/
			if($validateticker[0]){
				 $insertTickerid=$this->settStockTicker($validateticker); /*Insert data in the ex_stock table if verify from yahoo*/
				if($insertTickerid){
					 $gettickerid=$this->setTopicTicker($insertTickerid,$ticker);	/*Insert data in the tt_topic table if verify from yahoo*/
					return $gettickerid;
				}
			}else{
				return 0;
			}

		}

   }

	function setTopicTicker($insertTickerid,$topicname){ /* Insert data in tt_topic table*/
		$parms['topic_name']=$topicname;
		$parms['stock_id']=$insertTickerid;
		$parms['is_approved']=1;
		$table="tt_topic";
		$id=insert_query($table,$parms);
		if(isset($id)){
			return $id;
		}else{
			return 0;
		}
	}

	function settStockTicker($validateticker){ /*Insert data in ex_stock table*/
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

	function setTickerComments($subid,$commentbody,$topicid,$stockid){  /*Insert data into tt_chat table if user post the comment */
	    $checkBody=$this->checkProfanityComment($commentbody); /*check the posted comment using profanity filter*/
		if($checkBody){
			$table="tt_chat";
			$parms['subscription_id']=$subid;
			$parms['body']=substr($commentbody,0,180);
			$parms['topic_id']=$topicid;
			$parms['stock_id']=$stockid;
			$parms['is_suspended']=0;

			$insertpost=insert_query($table,$parms);
			if(isset($insertpost)){
				return $insertpost; /*if data is inserted successfully in tt_chat*/
			}else{
				return 0;
			}
		}else{
				return 0;  /*return an appropriate message to the user from ex_lang table*/
		}
	}

	function getstockdetails($symbolname){ /*Validate ticker from Yahoo and if validate return value of ticker*/
	$tickersymbol=$symbolname;
		if (isset($tickersymbol))
		{
			$open = @fopen("http://download.finance.yahoo.com/d/quotes.csv?s=$tickersymbol&f=sl1d1t1c1ohgvnx&e=.csv", "r");
			$read = @fread($open, 2000);
			@fclose($open);
			unset($open);
			$read = str_replace("\"", "", $read);
			$read = explode(",", $read);
			IF ($read[1] == 0)
			{
				return 0;
			}ELSE{
				return $read;
			}
		}
	}

	function showSearchTicker($subid,$uniqueName){ /* Show the design of search ticker */
		global $D_R;
		include_once($D_R.'/lib/config/_tickertalk_config.php');
		global $lang,$autoFillCount,$tickertalk_size;
	    build_lang($uniqueName);
		$getblockeduser=$this->getBlockUser($subid);
		if($getblockeduser){
			$archmsg=$lang['blockeduser'];
		}else{
			$invalidmsg=$lang['invalidticker'];
		}
		if($tickertalk_size=='medium'){
			 $heightddsymbol='51';
			 $widthddsymbol='155';
		}elseif($tickertalk_size=='small'){
			 $heightddsymbol='75';
			 $widthddsymbol='155';

		}
	?>
		<div class="SymbolSearcharea">

			<input class="SymbolSearcharea_input" name="symbol" type="text" value="Symbol" id="symbol" onfocus="focusedsymbol(this)" onblur="blurredsymbol(this)" onkeyup="javascript:searchEnterKeyTicker(event);"/>
			<img style="cursor:pointer;vertical-align:middle" src="<?=$IMG_SERVER?>/images/tickertalk/go.jpg" border="0" align="absmiddle" alt="MV Ticker Talk" onclick="searchTicker('<?=$archmsg;?>','<?=$invalidmsg;?>');" />
		</div>
		<script>
				var obj = actb(document.getElementById('symbol'),customarray,'','<?=$autoFillCount;?>','<?=$heightddsymbol;?>','<?=$widthddsymbol;?>');
		</script>
	<? }

	function showCommentArea($uniqueName,$stockid,$topicid,$postid,$stocksymbol){ /*show the design of comment post */
		global $D_R,$CDN_SERVER;
		include_once($D_R.'/lib/config/_tickertalk_config.php');
	    global $tickerchatcharlimit,$lang,$HTPFX,$HTHOST,$D_R,$_SESSION,$autoFillCount;
		$fileProfanity = $CDN_SERVER."/js/profanity.txt";
		$handle = fopen ($fileProfanity, "r");
		$badwords = fread ($handle, filesize ($fileProfanity));
		fclose ($handle);
		build_lang($uniqueName);
		$longwmsg=$lang['longwords'];
		$notallowed=$lang['wordsnotallowed'];
		$emptytext=$lang['emptytext'];
		$topicname=$this->getTopicStatus($_COOKIE['currenttickerid']);
		if($postid){
			$symbolvalue=$stocksymbol;
		}elseif($topicname['topic_name']){
			$symbolvalue=$topicname['topic_name'];
		}else{
			$symbolvalue='Symbol';
		}
		$subid=$_SESSION['SID'];

	?>

		<div class="talkreply">
		<div class="enter_qoute" >
<!--<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="email_container">Enter Quote</td>
    <td align="right"><img vspace="3" hspace="4"  style="cursor: pointer;" align="right" onclick="cancelComment();" alt="Close" src="<?=$IMG_SERVER?>/images/tickertalk/closeLogin.jpg"/></td>
    </tr>
</table>-->
        <div class="close_button_reply"><span class="form_txt">Enter Quote</span>
        <img vspace="0" align="absmiddle" style="cursor: pointer;" onclick="cancelComment();" alt="Close" src="<?=$IMG_SERVER?>/images/tickertalk/closeLogin.jpg"/></div>
		<span class="form_input">
		<input class="SymbolSearcharea_input" name="postsymbol" type="text" value="<?=$symbolvalue;?>" id="postsymbol" onfocus="ShowLoginRegisteronSymbol(this,'<?=$subid;?>')" onblur="blurredsymbol(this)" onkeyup="javascript:searchEnterKeyTicker(event);"/>
	    </span>
		<span class="form_txt">
        Enter Reply:</span><span id="showcharcount"></span>
		<textarea class="expand68-200" name="reply" id="reply" cols="6"  maxlength="<?=$tickerchatcharlimit=180;?>" onfocus="showLoginRegisterbox('<?=$subid;?>');"; onkeyup="return countPostCharacters(this);" onmousedown="return countPostCharacters(this);" onmouseup="return countPostCharacters(this);" onclick="clearTextReplyDiv();" ></textarea>
        <div id="showerr" class="PostError">&nbsp;</div>

		<div id="disable_submit" class="talkreplyLeft" style="display:none; "><img src="<?=$IMG_SERVER?>/images/tickertalk/reply_submit_faded.jpg" border="0" align="absmiddle" style="margin-right:-5px;" vspace="5" alt="Submit Reply"/></div>
		<div id="enable_submit" class="talkreplyLeft">
        <form name="upload_image">
		<div id="uploaded_image"></div>
		<div class="btn_addchart">
			<span id="spanButtonPlaceholder"></span>
		</div>
		</form>
        <div id="uploadurl" class="upload_charturl" onclick="showLoginRegisterbox('<?=$subid;?>');">Add Chart from URL</div>
</div>

     	<!--<div id="uploadurl" class="upload_charturl" onclick="showLoginRegisterbox('<?=$subid;?>');">Add Chart from URL</div>-->
	    <div id="addurl" style="overflow:hidden;" class="upload_url">
			<div id="addurl_box" style="overflow:hidden;margin-top:-120px;">
			<? $this->showUploadImage(); ?>
			</div>
		</div>
        <span class="btn_reply"><img src="<?=$IMG_SERVER?>/images/tickertalk/reply_submit.jpg" border="0" align="absmiddle" vspace="0" alt="Submit Reply" onclick="showCommentPostDiv('<?=$badwords;?>','<?=$longwmsg;?>','<?=$notallowed;?>','<?=$emptytext;?>','<?=$topicid;?>','<?=$stockid;?>','<?=$postid;?>');"/></span>
		<?php
			if(!$_SESSION['userFacebookId']){
				echo '<div id="fconn">';
				$objFacebook=new FacebookConnect();
				echo $objFacebook->displayFBConnect();
				echo '</div>';
			}
		?>
		<div>
		<?
			// echo $this->showTwitterConnect();  // connect to twitter
		?>
		</div>
		</div>
	<? }
	/* show the design of Talk now button*/
	function showTalkNow($subid,$uniqueName){
	?>
		<img src="<?=$IMG_SERVER?>/images/tickertalk/TalkNow.jpg" border="0" onclick="showCommentPostDivArea('1','0','<?=$topicid;?>','<?=$stockid;?>');" style="cursor:pointer;" alt="Talk Now" />

	<?
	}

	function setPostComments($subid,$commentbody,$topicid,$stockid,$postid){ /*Insert chat in tt_chat table*/
			$parms[subscription_id]=$subid;
			$parms[body]=substr(strip_tags($commentbody),0,180);
			$parms[stock_id]=$stockid;
			$parms[created_on]=date("Y-m-d H:i:s");
			$parms[is_suspended]=0;
			// $parms[reply_post_id]=$postid;
			$catid=insert_query('tt_chat',$parms);
			if(isset($catid)){
				return $catid;
			}
	}

	function setTpoicChatId($topicid,$catid,$ismain=0){
		$parmstopic[topic_id]=$topicid;
		$parmstopic[chat_id]=$catid;
		$parmstopic[is_main]=$ismain;
    	$idtopic=insert_query('tt_topic_chat',$parmstopic);
		if(isset($idtopic)){
			return $idtopic;
		}else{
			return 0;
		}
	}

	function getTickerFromPost($body,$countstock=NULL){
		global $stockslimit;
		$objexchange= new Exchange_Element();
        $pattern = '/\(([A-Z]{1,5})\)|\$([A-Z]{1,5})/i';
		 preg_match_all($pattern, $body, $stocks_matches);
		 $unique_stocks=array();
		 $uniquestocks=array();
		 foreach($stocks_matches[1] as $id=>$value)
		 {
		  if($value){
		   $stock=$objexchange->is_stock($value);
			   if($stock)
			   {
				   if(is_array($unique_stocks)){
					   if(!in_array($value,$unique_stocks))
					   {
							$unique_stocks[]=$value;
							if(!$countstock){
								if (count($unique_stocks)>($stockslimit - 1)){ break;}
							}
						}
			   		}
				}
			}
		  }

		 foreach($stocks_matches[2] as $id=>$value)
		 {
		  if($value){
		   $stock=$objexchange->is_stock($value);
			   if($stock)
			   {
				   if(is_array($uniquestocks)){
					   if(!in_array($value,$uniquestocks))
					   {
							$uniquestocks[]=$value;
							if(!$countstock){
								if (count($uniquestocks)>($stockslimit - 1)){ break;}
							}
						}
			   		}
				}
			}
		  }
		  $uniquetickers=(array_merge($unique_stocks,$uniquestocks));
		  $uniquetickers=array_unique($uniquetickers);
	      return $uniquetickers;

	}

	function setPostCommentMemcache($idpost,$commentbody,$subid,$stock_symbol,$topicid,$artopicid){ /* set topic chat in memcache memcache */

	        $memCacheChat = new memCacheObj();
			$commentbody=substr(strip_tags($commentbody),0, 180);
			$qry="select fname,lname,email from subscription where id='".$subid."'";
			$getname=exec_query($qry,1);
			if($getname['fname']){
				$subname=$getname['fname'].' '.$getname['lname'];
			}else{
				$subname=$getname['email'];
			}
			$fname=$getname['fname'];
		    $setcache=array(
				 "$idpost" => array
				 (
				  "body" => "$commentbody",
				  "subscription_id" => "$subid",
				  "username" => "$subname",
				  "created_on" => date("Y-m-d H:i:s"),
				  "fname" => "$fname",
				  "id" => "$idpost",
				  "topic_id" => "$topicid",
				  "stocksymbol" => strtoupper($stock_symbol))
				 );
			foreach($artopicid as $value) // Set memcache object for every ticker associated to it including topic_0 i.e for all
			{
				$key="topic_".$value;
				$chatmerge = $memCacheChat->getKey($key); /* get topic memcache key */
				if($chatmerge){
                    $chatmerge[$idpost]=$setcache[$idpost];
					$topickey=$memCacheChat->setKey($key,$chatmerge,0); /*append chat in memcache*/
				}else{
					$this->setDataMemcache($value);
				}
			}
	}
	function setDataMemcache($topicid){
			global $tickerChatMemcacheLimit;
			$memCacheChat = new memCacheObj();
			$sql ="SELECT ttc.topic_id, tt.topic_name,tc.id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,sub.email,tc.subscription_id FROM tt_topic as tt, tt_chat as tc,tt_topic_chat as ttc, subscription as sub WHERE tt.id = ttc.topic_id AND ttc.chat_id = tc.id AND tc.subscription_id = sub.id AND tc.is_suspended = '0'";
			if($topicid > 0)
			{
				$sql .=" AND ttc.topic_id = '".$topicid."'";
			}
			else // Set key topic_0 which is for all post
			{
				$topicid = 0;
				$sql .=" AND ttc.is_main = '1'";
			}
			$sql .=" ORDER BY id DESC LIMIT 0,$tickerChatMemcacheLimit";
			$getdata=exec_query($sql);
			foreach($getdata as $value) {
				 $setcache[$value[id]]=array(
				  "body" => "$value[body]",
				  "subscription_id" => "$value[subscription_id]",
				  "username" => "$value[username]",
				  "created_on" => "$value[created_on]",
				  "id" => "$value[id]",
				  "topic_id" => "$value[topic_id]",
				  "stocksymbol" => strtoupper($value[topic_name]));
			}
			ksort($setcache); // Memcache array should always be in ascending order of keys
			$key="topic_".$topicid;
			$memCacheChat->setKey($key,$setcache,0);
	}
	function setUrlLink($commentbody)
	{
	 return $text = preg_replace_callback('@((https?://)?([-\w]+(\.){1})+(com|org|net|mil|edu|COM|ORG|NET|MIL|EDU)(:\d+)?(/[/\w\._-]*)?(\?\S+)?)@',array( &$this, 'matchURL'),$commentbody);
	}

   public function matchURL($matches)
	{
	  $matchedURL = $matches[0];
	  if(substr($matchedURL,0,8) == 'https://' || substr($matchedURL,0,7) == 'http://')
	  {
	   $href = $matchedURL;
	  }
	  else
	  {
	   $href="http://".$matchedURL;
	  }
	  return '<a href="'.$href.'" target="_blank">'.$matchedURL.'</a>';
	}


	function setUrlChange($commentbody)
	{
	 return $text = preg_replace_callback('@((https?://)?([-\w]+(\.){1})+(com|org|net|mil|edu|COM|ORG|NET|MIL|EDU)(:\d+)?(/[/\w\._-]*)?(\?\S+)?)@',array( &$this, 'matchURLDisplay'),$commentbody);
	}

   public function matchURLDisplay($matches)
	{
	  $matchedURL = $matches[0];
	  if(substr($matchedURL,0,8) == 'https://' || substr($matchedURL,0,7) == 'http://')
	  {
	   $href = $matchedURL;
	  }
	  else
	  {
	   $href="http://".$matchedURL;
	  }

		$findme='/';
		$pos = strpos($href, $findme,8);
		$strfirst=substr($href,0,$pos);
		$strsecond=substr($href,-5);
		if($pos){
			$strurl=$strfirst.'...'.$strsecond;
		}else{
			$strurl=$href;
		}
		return '<a href="'.$href.'" target="_blank">'.$strurl.'</a>';
	}

   function urlChange($commentbody){
		$body=$this->setUrlChange(strip_tags($commentbody));
   		return $body;
   }

	function getBlockUser($subid){ /*check user/IP is blocked or not*/
	    $userIPAddress=$_SERVER['REMOTE_ADDR'];
		$objIPBlockState=$this->isBlockedIP($userIPAddress);
		if($objIPBlockState){
		 	$userblock=1;
			return $userblock;
		}else{
		    $qry="select blockservice_id from ex_blockservices where subscription_id='".$subid."' and blockservice_id in ('3','4')";     		$getblockservice=exec_query($qry);
			if($getblockservice[0]['blockservice_id']){
				$userblock=1;
				return $userblock;
			}else{
				return 0;
			}

		}
	}
	/*
	++++++++++++++++++++++++++++++++++++ Code Base for Login and Register +++++++++++++++++++++++++++++++++++++++

	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	This function will display Banner, which will be having two buttons, one for Login and another for Register.
	*/
	function viewRegLogBox(){
		// Code to be build

	}// end of 	function viewRegLogBox

	/*
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	This function will display Registration box
	*/
	function viewRegBox(){
		// Code to be build

	}// end of function viewRegBox
	/*
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	This function will display Login box
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	*/
	function viewLogBox(){
		// Code to be build

	}// end of function viewLogBox
	/*

	/*
	This function will check user's IP is blocked.
	*/
	function isBlockedIP($strIPAddress){

		//$strQuery="select ip from subscription where ip='$strIPAddress' and is_blockip='1'";
		$strQuery="select BIP.ip from bocked_ip BIP, subscription SUB where BIP.subscription_id=SUB.id and SUB.is_blockip='1' and BIP.ip='$strIPAddress'";
		$strResult=exec_query($strQuery,1);
		// blocked ip
		if($strResult['ip']){
			return 1;
		}
		// allowed ip
		else{
			return 0;
		}
	} // end of isBlockedIP

	/*
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	This function performs login and returns its status
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	*/
	function doLogin($userid,$pwd){

		global $lang,$_SERVER,$_SESSION;
		build_lang('tickertalk');

		// user class initiated
		$objUser=new user();
		// get user ip address
		$userIPAddress=$_SERVER['REMOTE_ADDR'];
		// map with blocked ip addresses
		$objIPBlockState=$this->isBlockedIP($userIPAddress);

		// blocked IP
		if($objIPBlockState==1){
			$loginState=array(
				'status'=>false,
				'msg'=>$lang['blockedip']
			);

		}
		else{
			// call login
			$loginStatus=$objUser->login($userid,$pwd);

			//blocked User
			if($loginStatus['blocked']==1){
				$loginState=array(
					'status'=>false,
					'msg'=>$lang['blockeduser']
				);
			}
			// valid user
			elseif($loginStatus=='true'){
				$loginState=array(
					'status'=>true,
					'SID'=>$_SESSION['SID']
				);
			} // inactive account
			elseif($loginStatus=='Inactive account'){
				$loginState=array(
					'status'=>false,
					'msg'=>'Inactive account'
				);
			}
			// invalid login
			elseif(is_string($loginStatus) && $loginStatus!='true' ){
				$loginState=array(
					'status'=>false,
					'msg'=>$lang['invalidlogin']
				);
			}
		}
		return $loginState;
	}//end of function doLogin
	/*
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	This function performs registration and returns its status
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	*/
	function doRegister($firstname,$lastname,$userid,$pwd,$is_facebookuser){

		global $lang,$_SERVER,$_SESSION,$viaDefaultAddr;
		build_lang('tickertalk');

		// user class initiated
		$objUser=new user();

		// via class initiated
		$objVia=new Via();
		// error handler class initiated
		$errorObj=new ViaException();

		// if user is using from a blocked ipaddress
		$userIPAddress=$_SERVER['REMOTE_ADDR'];
		// map with blocked ip addresses
		$objIPBlockState=$this->isBlockedIP($userIPAddress);
		// blocked IP
		if($objIPBlockState==1){
			$registerState=array(
				'status'=>false,
				'blockip'=>$lang['blockedip']
			);
			return $registerState;
		}



		// check login availability
		$email = $userid;
		$fieldsArray['customerLogin']=$email;

		// function is defined in class user and script /lib/_via_controller_lib.php
		$userExistanceStatus=$objUser->checkUserViaAvailibilityByEmail($fieldsArray);

		if($userExistanceStatus!=''){
			$registerState=array(
				'status'=>false,
				'msg'=>'User name is unavailable. Try another email.'
			);
			return $registerState;
		}

		// login information
		$loginInfo=array(
			'login'=>$userid,
			'password'=>$pwd
		);

		// default address
		$addresses=$viaDefaultAddr;

		// add aux field
		if($is_facebookuser)
		{
			$account_activated=1; /*set account activation to via- 0,1*/
			$hardtrial=1;
		}
		else
		{
			$account_activated=0; /*set account activation to via- 0,1*/
			$hardtrial=0;
			$temp_orders="15".'-'."1";
		}

		$auxInfo=$objVia->setAuxilaryFields($account_activated,$temp_orders);

		// integrate customer information
		$custInfo=array(
			'loginInfo'=>$loginInfo,
			'addresses'=>$addresses,
			'email'=>$userid,
			'nameFirst'=>$firstname,
			'nameLast'=>$lastname,
			'email'=>$userid,
			'auxFields'=>$auxInfo
		);

		// cart details with exchange
		/*$orderDetails=array();

		$orderDetails['OrderItem'][0]['orderClassId']=9;
		$orderDetails['OrderItem'][0]['orderCodeId']=9;
		$orderDetails['OrderItem'][0]['sourceCodeId']=1;
		$orderDetails['OrderItem'][0]['orderItemType']='SUBSCRIPTION';
		$orderDetails['OrderItem'][0]['typeSpecificId']=15;
		$orderDetails['OrderItem'][0]['price']=0;
		$orderDetails['OrderItem'][0]['paymentAccountNumb']=1;
		$orderDetails['OrderItem'][0]['qty']=1;*/

		// create cart
		/*$cartDetails=array(
			'billDate'=>date('Y-m-d'),
			'items'=>$orderDetails
		);*/

		// set user name and password
		$objVia->nameFirst=$firstname;
		$objVia->nameLast=$lastname;

		// send request to via
		// defined in /lib/_via_data_lib.php
	//	$customerDetails=$objVia->addCustomerAndOrder( $custInfo,$cartDetails,$hardtrial);
		$customerDetails=$objVia->addCustomer($custInfo,$hardtrial=0); // only add customer info, order place after activation

		// via responded successfully
		if($customerDetails=='true'){
			$via_id=$objVia->customerIdVia;
			// insert record to minyanville db
			// defined in /lib/_via_data_lib.php
			// set exchange alerts to 0
			$alerts=0;
			$insertedId=$objVia->insertBasicUser($alerts);
			/* Insert into ex_user_email_settings + ex_profile_privacy tables */
			RegisterUser($insertedId);

			/* Insert into ex_user_profile table */
			$subarray = array('subscription_id'=>$insertedId);
			$profileid = insert_query('ex_user_profile', $subarray);
			// login new user to system
			$loginInfo=$this->doLogin($userid,$pwd);
			// account created successfully
			$this->setTickertalkLogs('registration',$insertedId);
			/*Insert logs for tickertalk registration*/



			$registerState=array(
				'status'=>true,
				'msg'=>''
			);
		}

		// minyanville db insertion failed
		else{

			// message handling
			//echo "MVIL DB insertion failed";
			$errMessage=$errorObj->getExactCustomError($customerDetails);
			if($errMessage==''){
				$pattern = '/Error:(.*)/';
				preg_match($pattern, $errViaMessage, $matches);
				$errMessage=$matches[1];
			}
			if($errMessage==''){
				$errMessage='An error occurred while processing your request. Please check your data.';
			}

			$registerState=array(
				'status'=>false,
				'msg'=>$errMessage
			);

		}
		return $registerState;
	}//end of function doRegister

	function checkLogin(){
		global $_SESSION;
		if($_SESSION['SID']){
			return true;
		}
		else{
			return false;
		}
	}
	function getMostTalkedTopic()
	{
		$stQuery = "select tt.id,tt.topic_name,count(ttc.topic_id) FROM tt_topic_chat as ttc,tt_topic AS tt ,tt_chat AS tc
					WHERE ttc.topic_id = tt.id AND  tt.is_archived = '0' AND tt.is_deleted = '0' AND is_approved = '1'
					and tc.is_suspended='0' AND ttc.chat_id = tc.id  AND tc.created_on BETWEEN  DATE_SUB(CURDATE(),INTERVAL 15 day)
					AND DATE_ADD(CURDATE(),INTERVAL 1 day) GROUP BY ttc.topic_id order by count(ttc.topic_id) DESC LIMIT 0,3";
		$resMostTalked = exec_query($stQuery);
		$arMostTalkedTopic = array();
		foreach($resMostTalked as $rowMostTalked)
		{
			$arMostTalkedTopic[$rowMostTalked[id]] = $rowMostTalked[topic_name];
		}
		if(count($arMostTalkedTopic) == 0)
		{
			global $arMVListedTicker;
 			$arMostTalkedTopic = $arMVListedTicker;
		}
		return $arMostTalkedTopic;
	}
	function displayMostTalkedTopic()
	{
		$arMostTalkedTopic = $this->getMostTalkedTopic();
		$stData = "";
		$stData .= '<div class="pop_ticker">';
		if(is_array($arMostTalkedTopic)){
		$stData .= '<div class="popticker_tags">';
			foreach($arMostTalkedTopic as $ticker_id => $ticker_name)
			{
				$stData .= '<span><a onclick="loadDiscussion('.$ticker_id.')">'.$ticker_name.'&nbsp;&raquo;&nbsp;</a></span>';
			}
		$stData .= '</div>';
		}
		$stData .= 'Popular Tickers:</div>';
		echo $stData;
	}

	function getLatestTopicComments($topic_id)
	{
		// Here is the special case to Load discussion of a specific user we pass $topic_id = 'USER_1' here 1 is sub_id of user
		$arUser = explode("_",$topic_id);
		if($arUser[0] == 'USER')
		{
			$stQuery = "SELECT tc.id,TT.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,sub.fname,tc.subscription_id,ES.stocksymbol FROM tt_chat as tc, subscription as sub,ex_stock ES,tt_topic_chat TT WHERE tc.subscription_id = sub.id AND tc.subscription_id = ".$arUser[1]." AND tc.is_suspended = '0' and tc.stock_id=ES.id and  TT.chat_id=tc.id group by tc.id ORDER BY id DESC LIMIT 0,11";
		}
		// follow MV Recommended
		else if($topic_id=='MR'){
			$stQuery = "SELECT tc.id,TT.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,sub.fname,
tc.subscription_id,ES.stocksymbol FROM tt_chat AS tc,subscription AS sub,tt_mvrecommended AS mv,ex_stock ES,tt_topic_chat TT
WHERE tc.subscription_id = sub.id AND  mv.subscription_id = tc.subscription_id
AND tc.is_suspended = '0' and tc.stock_id=ES.id and TT.chat_id=tc.id GROUP BY tc.id ORDER BY id DESC LIMIT 0,11";
		}
		// follow friends
		elseif($topic_id=='FF'){
			$stQuery = "SELECT tc.id,TT.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,sub.fname,
tc.subscription_id,ES.stocksymbol FROM tt_chat AS tc,subscription AS sub,ex_stock ES,tt_topic_chat TT WHERE tc.subscription_id = sub.id AND tc.subscription_id in (select friend_subscription_id from ex_user_friends
where subscription_id='".$_SESSION['SID']."') AND tc.is_suspended = '0' and tc.stock_id=ES.id and TT.chat_id=tc.id group by tc.id ORDER BY id DESC LIMIT 0,11";
		}
		// All people
		elseif($topic_id=='AP'){
			$stQuery = "SELECT tc.id,TT.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,sub.fname,
tc.subscription_id,ES.stocksymbol FROM tt_chat AS tc, subscription AS sub,ex_stock ES,tt_topic_chat TT WHERE tc.subscription_id = sub.id AND  tc.subscription_id in (select distinct(subscribed_to) from ex_blog_subscribed
where subscription_status='1' and user_id='".$_SESSION['SID']."') AND tc.is_suspended = '0' and tc.stock_id=ES.id and TT.chat_id=tc.id group by tc.id ORDER BY id DESC LIMIT 0,11";
		}
		// follow my watchlist
		elseif($topic_id=='MW'){

			$stQuery = "SELECT tc.id, ttc.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,sub.fname,tc.subscription_id,ES.stocksymbol FROM tt_chat AS tc,tt_topic_chat AS ttc,tt_topic as ttt, subscription AS sub,ex_stock ES
			WHERE ttc.chat_id = tc.id AND tc.subscription_id = sub.id AND ttc.topic_id=ttt.id and ttt.stock_id in (SELECT DISTINCT(stock_id) topic_id  FROM ex_user_stockwatchlist WHERE subscription_id='".$_SESSION['SID']."') AND tc.is_suspended = '0' and tc.stock_id=ES.id group by tc.id ORDER BY id DESC LIMIT 0,11";
		}
		// topic wise
		elseif($topic_id =="0")
		{

			$stQuery ="SELECT tc.id,ttc.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,fname,tc.subscription_id ,ES.stocksymbol FROM tt_chat AS tc, subscription AS sub,ex_stock ES,tt_topic_chat AS ttc
WHERE tc.subscription_id = sub.id AND tc.is_suspended = '0' AND ttc.is_main=1 AND ttc.chat_id = tc.id AND tc.stock_id=ES.id AND tc.stock_id=ES.id ORDER BY id DESC LIMIT 0,11";
		}
		// all posts
		else
		{
			$stQuery = "SELECT tc.id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,fname,tc.subscription_id FROM tt_chat as tc,tt_topic_chat as ttc, subscription as sub WHERE ttc.chat_id = tc.id AND tc.subscription_id = sub.id AND tc.is_suspended = '0' AND ttc.topic_id =  '".$topic_id."'
						ORDER BY id DESC LIMIT 0,11";
		}


		$resLatestComments = exec_query($stQuery);
		return $resLatestComments;
	}
	function getTopicStatus($topic_id)
	{
		$stQuery = "SELECT id,topic_name,is_archived,is_deleted,is_approved,stock_id FROM tt_topic WHERE id = ".$topic_id;
		$arTopic = exec_query($stQuery,1);
		$arTopicDetail['topic_name' ] = $arTopic['topic_name'];
		$arTopicDetail['stock_id'] = $arTopic['stock_id'];
		if($arTopic['is_deleted'] == 1)
		{
			$arTopicDetail['status'] = 'D';
		}
		else if($arTopic['is_approved'] == 0)
		{
			$arTopicDetail['status'] = 'U';
		}
		else if($arTopic['is_archived'] == 1)
		{
			$arTopicDetail['status'] = 'A';
		}
		else
		{
			$arTopicDetail['status'] = 'L';
		}
		return $arTopicDetail;
	}
	function getLiveTopicComments($topic_id,$comment_id)
	{
		$arUser = explode("_",$topic_id);
		if($arUser[0] == 'USER')
		{
			$stQuery = "SELECT tc.id,TT.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,sub.fname,tc.subscription_id,ES.stocksymbol FROM tt_chat as tc , subscription as sub,ex_stock ES,tt_topic_chat TT
				WHERE tc.subscription_id = sub.id AND tc.subscription_id = ".$arUser[1]." AND tc.is_suspended = '0' and tc.stock_id=ES.id and TT.chat_id=tc.id  AND tc.id > '".$comment_id."' group by tc.id ORDER BY id ASC";
		}
		//MV Recommended
		else if($topic_id=='MR'){
	$stQuery = "SELECT tc.id,TT.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,sub.fname,
tc.subscription_id,ES.stocksymbol FROM tt_chat AS tc,subscription AS sub,tt_mvrecommended AS mv,ex_stock ES,tt_topic_chat TT
WHERE tc.subscription_id = sub.id AND  mv.subscription_id = tc.subscription_id
AND tc.is_suspended = '0' and tc.stock_id=ES.id and TT.chat_id=tc.id AND tc.id > '".$comment_id."' GROUP BY tc.id ORDER BY id ASC ";
		}
		// Follow Friends
		elseif($topic_id=='FF'){
			$stQuery = "SELECT tc.id,TT.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,sub.fname,tc.subscription_id,ES.stocksymbol FROM tt_chat as tc ,subscription as sub,ex_stock ES,tt_topic_chat TT WHERE tc.subscription_id = sub.id AND tc.is_suspended = '0' AND tc.subscription_id in (select friend_subscription_id from ex_user_friends where subscription_id='".$_SESSION['SID']."') and tc.stock_id=ES.id and TT.chat_id=tc.id AND tc.id > '".$comment_id."' GROUP BY tc.id ORDER BY id ASC";
		}
		// All People
		elseif($topic_id=='AP'){
			$stQuery = "SELECT tc.id,TT.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,sub.fname,tc.subscription_id,ES.stocksymbol FROM tt_chat as tc , subscription as sub,ex_stock ES,tt_topic_chat TT WHERE  tc.subscription_id = sub.id and  tc.stock_id=ES.id and TT.chat_id=tc.id AND tc.is_suspended = '0' AND  tc.subscription_id in (select distinct(subscribed_to) from ex_blog_subscribed where  subscription_status='1' and user_id='".$_SESSION['SID']."') AND tc.id > '".$comment_id."' GROUP BY tc.id ORDER BY id ASC";
		}
		// Follow MY Watchlist
		elseif($topic_id=='MW'){

			$stQuery = "SELECT tc.id, ttc.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,sub.fname,tc.subscription_id,ES.stocksymbol FROM tt_chat AS tc,tt_topic_chat AS ttc,tt_topic as ttt, subscription AS sub,ex_stock ES
			WHERE ttc.chat_id = tc.id AND tc.subscription_id = sub.id AND ttc.topic_id=ttt.id and ttt.stock_id in (SELECT DISTINCT(stock_id) topic_id  FROM ex_user_stockwatchlist WHERE subscription_id='".$_SESSION['SID']."') AND tc.is_suspended = '0' and tc.stock_id=ES.id AND tc.id > '".$comment_id."' group by tc.id  ORDER BY id ASC";
		}
		elseif($topic_id == 0)
		{
			/*
			$stQuery = "SELECT tc.id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,sub.fname,tc.subscription_id FROM tt_chat as tc , subscription as sub
					WHERE tc.subscription_id = sub.id AND tc.is_suspended = '0' AND tc.id > '".$comment_id."' ORDER BY id ASC";
					*/
		$stQuery = "SELECT tc.id,ttc.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS
		username,fname,tc.subscription_id ,ES.stocksymbol FROM tt_chat AS tc, subscription AS sub,ex_stock ES,tt_topic_chat AS ttc 			WHERE tc.id > '".$comment_id."' AND tc.subscription_id = sub.id AND tc.is_suspended = '0' AND ttc.is_main=1 and
			ttc.chat_id = tc.id AND tc.stock_id=ES.id AND tc.stock_id=ES.id ORDER BY id ASC";
		}
		else
		{
			$stQuery = "SELECT tc.id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,sub.fname,tc.subscription_id
				FROM tt_chat as tc ,tt_topic_chat as ttc, subscription as sub
				WHERE ttc.chat_id = tc.id AND tc.subscription_id = sub.id AND tc.is_suspended = '0'
				AND ttc.topic_id = '".$topic_id."' AND tc.id > '".$comment_id."' ORDER BY id ASC";

		}

		$resLiveComments = exec_query($stQuery);
		return $resLiveComments;
	}

	function getMoreTopicComments($topic_id,$least_comment_id)
	{
		// MV Recommended
		$arUser = explode("_",$topic_id);
		if($arUser[0] == 'USER')
		{
			$stQuery = "SELECT tc.id,TT.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,fname,tc.subscription_id,ES.stocksymbol FROM tt_chat as tc , subscription as sub,ex_stock ES,tt_topic_chat TT WHERE tc.subscription_id = sub.id AND tc.subscription_id =".$arUser[1]." AND tc.is_suspended = '0' and tc.stock_id=ES.id and TT.chat_id=tc.id AND tc.id < '".$least_comment_id."' group by tc.id  ORDER BY id DESC LIMIT 0,16";
		}
		else if($topic_id=='MR'){
$stQuery = "SELECT tc.id,TT.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,sub.fname,
tc.subscription_id,ES.stocksymbol FROM tt_chat AS tc,subscription AS sub,tt_mvrecommended AS mv,ex_stock ES,tt_topic_chat TT
WHERE tc.subscription_id = sub.id AND  mv.subscription_id = tc.subscription_id
AND tc.is_suspended = '0' and tc.stock_id=ES.id and TT.chat_id=tc.id AND tc.id < '".$least_comment_id."' GROUP BY tc.id ORDER BY id DESC LIMIT 0,16";

		}
		// Follow Friends
		elseif($topic_id=='FF'){
			$stQuery = "SELECT tc.id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,fname,tc.subscription_id FROM tt_chat as tc ,subscription as sub
WHERE tc.subscription_id = sub.id AND tc.is_suspended = '0' AND tc.subscription_id in (select friend_subscription_id from ex_user_friends where subscription_id='".$_SESSION['SID']."') AND tc.id < '".$least_comment_id."' GROUP BY tc.id ORDER BY id DESC LIMIT 0,16";
		}
		// All people
		elseif($topic_id=='AP'){
			$stQuery = "SELECT tc.id,TT.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,sub.fname,tc.subscription_id,ES.stocksymbol FROM tt_chat as tc ,subscription as sub,ex_stock ES,tt_topic_chat TT WHERE tc.subscription_id = sub.id AND tc.is_suspended = '0' AND tc.subscription_id in (select distinct(subscribed_to) from ex_blog_subscribed where  subscription_status='1' and user_id='".$_SESSION['SID']."') and tc.stock_id=ES.id and TT.chat_id=tc.id  AND tc.id < '".$least_comment_id."' GROUP BY tc.id ORDER BY id DESC LIMIT 0,16";
		}
		// Follow MyWatchlist
		elseif($topic_id=='MW'){

			$stQuery = "SELECT tc.id, ttc.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,sub.fname,tc.subscription_id,ES.stocksymbol FROM tt_chat AS tc,tt_topic_chat AS ttc,tt_topic as ttt, subscription AS sub,ex_stock ES
			WHERE ttc.chat_id = tc.id AND tc.subscription_id = sub.id AND ttc.topic_id=ttt.id and ttt.stock_id in (SELECT DISTINCT(stock_id) topic_id  FROM ex_user_stockwatchlist WHERE subscription_id='".$_SESSION['SID']."') AND tc.is_suspended = '0' and tc.stock_id=ES.id AND tc.id < '".$least_comment_id."' GROUP BY tc.id ORDER BY id DESC LIMIT 0,16";
		}
		elseif($topic_id == 0)
		{
			/*
			$stQuery = "SELECT tc.id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,fname,tc.subscription_id FROM tt_chat as tc , subscription as sub WHERE tc.subscription_id = sub.id AND tc.is_suspended = '0' AND tc.id < '".$least_comment_id."' ORDER BY id DESC LIMIT 0,16";
			*/
			$stQuery = "SELECT tc.id,ttc.topic_id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,fname,tc.subscription_id ,ES.stocksymbol FROM tt_chat AS tc, subscription AS sub,ex_stock ES,tt_topic_chat AS ttc
WHERE tc.id < '".$least_comment_id."' AND tc.subscription_id = sub.id AND tc.is_suspended = '0' AND ttc.is_main=1 AND ttc.chat_id = tc.id
AND ttc.chat_id = tc.id AND tc.stock_id=ES.id AND tc.stock_id=ES.id ORDER BY id DESC LIMIT 0,16";
		}
		else
		{
			$stQuery = "SELECT tc.id,tc.body,tc.created_on,CONCAT(sub.fname,' ',sub.lname) AS username,fname,tc.subscription_id FROM tt_chat as tc ,tt_topic_chat as ttc, subscription as sub
WHERE ttc.chat_id = tc.id AND tc.subscription_id = sub.id AND tc.is_suspended = '0' AND ttc.topic_id = '".$topic_id."' AND tc.id < '".$least_comment_id."' ORDER BY id DESC LIMIT 0,16";

		}
		$resLiveComments = exec_query($stQuery);
		return $resLiveComments;
	}
	function getCommentChart($comment_id)
	{
		$stQuery = "SELECT tcc.id, tcc.chat_id, tcc.image_path,tcc.thumbnail_path,tcc.thumbnail_width,tcc.thumbnail_height FROM  tt_chat_chart as tcc
					WHERE tcc.chat_id = ".$comment_id;
		$resCharts = exec_query($stQuery);
		return $resCharts;
	}


	function createUploadFolder()
	{
		global $IMG_SERVER,$D_R;
		$pathname=$D_R."/assets/tickertalk/charts/original/".date('mdy');
		$mode="777";
		$createfolder=$this->mkdir_recursive($pathname,$mode);
		$paththumbnail=$D_R."/assets/tickertalk/charts/thumbnail/".date('mdy');
		$createfolder=$this->mkdir_recursive($paththumbnail,$mode);
		chmod($pathname, 0777);
		chmod($paththumbnail, 0777);
		return $pathname;
	}

	public function mkdir_recursive($pathname,$mode)
	{
		is_dir(dirname($pathname)) || $this->mkdir_recursive(dirname($pathname), $mode);
		return is_dir($pathname) || @mkdir($pathname, $mode);
	}

	function setTickerSuggestionJs($ticker){
	      global $D_R,$CDN_SERVER;
		  $addticker=",'".$ticker."');";
		  $filename=$CDN_SERVER."/js/suggestion.js";
          $f=fopen($filename,"r");
          $data=fread($f,filesize($filename));
		  $pos = strpos($data,$ticker,1);
		  if(!$pos){
			  $data= str_replace(');',$addticker,$data);
			  fclose($f);
			  $f=fopen($filename,"w");
			  fwrite($f,$data);
			  fclose($f);
		  }
	}

   function setthumbnails($chat_id,$image_path,$thumbnail_path,$thumbnail_width,$thumbnail_height){

    $value['chat_id']=$chat_id;
	$value['image_path']=$image_path;
	$value['thumbnail_path']=$thumbnail_path;
	$value['thumbnail_width']=$thumbnail_width;
	$value['thumbnail_height']=$thumbnail_height;
	insert_query("tt_chat_chart",$value);

   }

	function createThumbs($pathToImages,$pathToThumbs,$thumbWidth,$imagename)
	{
	  // open the directory
	  $dir = opendir($pathToImages);
	  $fname=$imagename;
	  // loop through it, looking for any/all JPG files:
		// parse path for the extension
		$info = pathinfo($pathToImages . $fname);
		// continue only if this is a JPEG image
		  // load image and get image size
		  $extension = pathinfo("{$pathToImages}{$fname}");
		 $extension = $extension[extension];
		  if($extension == "jpg" || $extension == "jpeg" || $extension == "JPG"){
		            $img = imagecreatefromjpeg( "{$pathToImages}{$fname}" );
		          }

		          if($extension == "png") {
		            $img = imagecreatefrompng( "{$pathToImages}{$fname}" );
		          }

		          if($extension == "gif") {
		           	$img = imagecreatefromgif( "{$pathToImages}{$fname}" );
          }

		  $width = imagesx( $img );
		  $height = imagesy( $img );
		  // calculate thumbnail size
		  if($width>$thumbWidth)
		  {
                $new_width = $thumbWidth;
			  	$new_height = floor(($height * $thumbWidth )/$width );
		  }
		  else
		  {
			  $new_width = $width;
			  $new_height = $height;
		  }

		  // create a new temporary image
		  $tmp_img = imagecreatetruecolor( $new_width, $new_height );

		  if($extension == "gif")
		 {
			$trnprt_indx = imagecolortransparent($img);

      		// If we have a specific transparent color
			if ($trnprt_indx >= 0)
			{
				// Get the original image's transparent color's RGB values
				@$trnprt_color    = imagecolorsforindex($image, $trnprt_indx);

				// Allocate the same color in the new image resource
				$trnprt_indx    = imagecolorallocate($tmp_img, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);

				// Completely fill the background of the new image with allocated color.
				imagefill($tmp_img, 0, 0, $trnprt_indx);

				// Set the background color for new image to transparent
				imagecolortransparent($tmp_img, $trnprt_indx);
			}
			//$transparent = imagecolorallocate($tmp_img, "255", "255", "255");
			//imagefill($tmp_img, 0, 0, $transparent);
		 }
		  // copy and resize old image into new image
		  imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
		  // save thumbnail into a file
		  if($extension == "jpg" || $extension == "jpeg" || $extension == "JPG")
		  {
		      $thumbImage = imagejpeg( $tmp_img, "{$pathToThumbs}{$fname}" );
		  }else if($extension == "png") {
			 $thumbImage = imagepng( $tmp_img, "{$pathToThumbs}{$fname}" );
		  }else if($extension == "gif") {
		      $thumbImage = imagegif( $tmp_img, "{$pathToThumbs}{$fname}" );
          }
	  	  // close the directory
		  closedir( $dir );
		  if($thumbImage)
		  {
		  	return true;
		  }
		  else
		  {
		  	return false;
		  }
	}

	function setCommentSuspend($chatid){ /*insert data in tt_chat table for suspend*/
		$table='tt_chat';
		$params['is_suspended']=1;
		$conditions['id']=$chatid;
		$isupdated=update_query($table,$params,$conditions);
		return $isupdated;
	}

	function setReportAbuse($abuser_id,$abuse_item_id,$reporttext){ /**/

		$params['abuser_id']=$abuser_id; // loginid
		$params['abuse_item_id']=$abuse_item_id; // comment id
		$params['abuse_item_type']='tickertalk';
		$params['date'] = date('Y-m-d, H:i:s');
		$params['comment'] =strip_tags($reporttext);
		$insertid=insert_query('ex_report_abuse',$params);
		if(isset($insertid)){
			return $insertid;
		}else{
			return 0;
		}

	}

	function getReportAbuseItem($abuser_id,$abuse_item_id){ /**/
		$qry="select id from ex_report_abuse where abuser_id='".$abuser_id."' and abuse_item_id ='".$abuse_item_id."' and abuse_item_type='tickertalk'";
		//echo "<br>",$qry;
		$getid=exec_query($qry,1);
		if(isset($getid['id'])){
			return 1;
		}else{
			return 0;
		}
	}

	function getSuspendedComment($id){
		$qry="select id from tt_chat where id='".$id."' and is_suspended='1'";
		$getid=exec_query($qry,1);
		if(isset($getid['id'])){
			return 1;
		}else{
			return 0;
		}

	}

	/*send mail on report abuse to administrator*/
	function sendReportAbuseMail($abusername,$abusedname,$abusercomment,$loginid,$abuse_item_id){
	    global $toTtReportAbuse,$fromTtReportAbuse,$subjectTtReportAbuse,$tickerTalkReportAbuseTemplate;
		$to=$toTtReportAbuse;
		$from=$fromTtReportAbuse;
		$subject=$subjectTtReportAbuse;
		$strurl = 'abusername=' . urlencode($abusername) . '&abusedname=' . urlencode($abusedname) . '&abusercomment=' . urlencode($abusercomment) . '&abuserid='.urlencode($loginid) . '&abuse_item_id='. urlencode($abuse_item_id);
	 	mymail($to,$from,$subject,inc_web("$tickerTalkReportAbuseTemplate?$strurl"));

	}

	function getReportAbuseData($abuse_item_id){
		$qry="select TC.id,TC.body,RA.comment,TC.subscription_id,RA.abuser_id,TC.created_on from tt_chat TC, ex_report_abuse RA where TC.id=RA.abuse_item_id  and RA.abuse_item_id='".$abuse_item_id."' and RA.abuse_item_type='tickertalk'";

		$result=exec_query($qry,1);
		if($result){
			return $result;
		}else{
			return 0;
		}
	}

	function setPeopleYouFollow($loginid,$subscribeid){
		$subscriptiondata=array(
			subscribed_to=>$subscribeid,
			user_id=>$loginid,
			subscribed_on=>date('Y-m-d H:i:s'),
			subscription_status=>1
			);

			$id=insert_query("ex_blog_subscribed",$subscriptiondata);
			if(isset($id)){
				return $id;
			}
	}

	function updatePeopleYouFollow($loginid,$subscribeid,$subscription_status){
		$params[subscription_status]=$subscription_status;
		$conditions['subscribed_to']=$subscribeid;
		$conditions['user_id']=$loginid;
		$isupdated=update_query("ex_blog_subscribed",$params,$conditions);
		if($isupdated){
			return $isupdated;
		}else{
			return 0;
		}

	}

    function getPeopleYouFollow($loginid,$subscribeid,$subscription_status=0){
		$qry="select id,subscription_status from ex_blog_subscribed where user_id='".$loginid."' and subscribed_to='".$subscribeid."'";
		if($subscription_status){
			$qry .= " and subscription_status='1'";
		}
		// echo "<br>",$qry;
		$getresult=exec_query($qry,1);
		if($getresult){
			return $getresult;
		}else{
			return 0;
		}

	}

	function getPeopleYouFollowByName($loginid){
		$qry="select EBS.subscribed_to,concat(CONCAT(UCASE(MID(SUB.fname,1,1)),LCASE(MID(SUB.fname,2))),' ',CONCAT(UCASE(MID(SUB.lname,1,1)),LCASE(MID(SUB.lname,2)))) name from ex_blog_subscribed EBS, subscription SUB where EBS.user_id='".$loginid."' and EBS.subscription_status='1' and EBS.subscribed_to=SUB.id order by SUB.fname";
		$getresult=exec_query($qry);
		if($getresult){
			return $getresult;
		}else{
			return 0;
		}

	}


function showFilter($loginid){
?><div id="showerrormsg" class="errordisplay" style="display:none;"></div>
<div class="select_drop_down" id="selectval" onclick="selectCombo();">Select</div>
<div class="ticker_combo"  style="float:left; width:100%; " >
<div style="position:absolute; float:left; z-index:100;">
	<div class="main_drop_down" id="drop_down" style="display:none;">
	    <div class="select_option" onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor = '#B1CFE9'" id="0" onclick="applyComboFilter(this.id);">All Markets</div>
<!--		<div class="select_option" onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor = '#B1CFE9'" id="MR" onclick="applyComboFilter(this.id);">Minyanville Professors</div>
-->		<? if($loginid){
	$getfollowname=$this->getPeopleYouFollowByName($loginid);
?>
		<div class="select_option" onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor = '#B1CFE9'" id="MW" onclick="applyComboFilter(this.id);">My Watchlist</div>
		<div class="select_option_follow" id="FA" >-- People You Follow</div>
		<div class="select_option_Peoplefollow" onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor = '#B1CFE9'" id="AP" onclick="applyComboFilter(this.id);">All People</div>
<?
	if(is_array($getfollowname)){
		 foreach($getfollowname as $value)
			{ ?>
				<div class="select_option_Peoplefollow" onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor = '#B1CFE9'" id="USER_<?=$value['subscribed_to']?>" onclick="applyComboFilter(this.id);"><?=ucwords(strtolower($value['name']));?></div>
		<? }

	}

 } ?>
	</div>
</div>

	<?

?> 	<!--<div id="showerrormsg" class="errordisplay" style="display:none;"></div>-->
<?
}
	function getFilteredMemPosts($filter,$maxId){
		// MV recommended
		if($filter=="MR"){
			$posts=$this->getMVMemCachePosts($maxId);
		}
		elseif($filter=="FF"){
			$posts=$this->getFFMemCachePosts($maxId);
		}
		elseif($filter=="FA"){
			$posts=$this->getFAMemCachePosts($maxId);
		}
elseif($filter=="MW"){
			$posts=$this->getMWMemCachePosts($maxId);
		}
		return $posts;
	}

	function getMVMemCachePosts($maxId){
		$memCacheChat = new memCacheObj();
		$arMemObj = $memCacheChat->getKey("topic_0");

		$strQuery="select subscription_id as SID from tt_mvrecommended ";
		$result = exec_query($strQuery);
		$mvRcmd=array();
		$index=0;
		foreach($result as $key=>$val){
			$mvRcmd[$index]=$val['SID'];
			$index++;
		}

		$posts=array();

		foreach($arMemObj as $key => $val){
			if(in_array($val['subscription_id'],$mvRcmd) && $maxId<$key){
				$posts[$key]=$val;
			}
		}
		return $posts;
	}

	function getFFMemCachePosts($maxId){
		$memCacheChat = new memCacheObj();
		$arMemObj = $memCacheChat->getKey("topic_0");
		$strQuery="select friend_subscription_id as SID from ex_user_friends
where subscription_id='".$_SESSION['SID']."'";

		$result = exec_query($strQuery);
		$friends=array();
		$index=0;
		foreach($result as $key=>$val){
			$friends[$index]=$val['SID'];
			$index++;
		}
		$posts=array();

		foreach($arMemObj as $key => $val){
			if(in_array($val['subscription_id'],$friends) && $maxId<$key){
				$posts[$key]=$val;
			}
		}
		return $posts;
	}

	function getFAMemCachePosts($maxId){
		$memCacheChat = new memCacheObj();
		$arMemObj = $memCacheChat->getKey("topic_0");
		$strQuery="select distinct(subscribed_to) as SID from ex_blog_subscribed where user_id='".$_SESSION['SID']."'";

		$result = exec_query($strQuery);
		$authors=array();
		$index=0;
		foreach($result as $key=>$val){
			$authors[$index]=$val['SID'];
			$index++;
		}
		$posts=array();

		foreach($arMemObj as $key => $val){
			if(in_array($val['subscription_id'],$authors) && $maxId<$key){
				$posts[$key]=$val;
			}
		}
		return $posts;
	}
function getMWMemCachePosts($maxId){
		$memCacheChat = new memCacheObj();
		$arMemObj = $memCacheChat->getKey("topic_0");
		$strQuery="SELECT DISTINCT (ttc.chat_id) CHAT_ID FROM tt_topic_chat ttc,tt_topic ttt WHERE ttc.topic_id=ttt.id AND ttt.stock_id IN (SELECT DISTINCT(stock_id) FROM ex_user_stockwatchlist WHERE subscription_id='".$_SESSION['SID']."')";

		$result = exec_query($strQuery);
		$stocks=array();
		$index=0;
		foreach($result as $key=>$val){
			$stocks[$index]=$val['CHAT_ID'];
			$index++;
		}
		$posts=array();
		htmlprint_r($arMemObj);
		foreach($arMemObj as $key => $val){
			if(in_array($val['id'],$stocks) && $maxId<$key){
				$posts[$key]=$val;
			}
		}
		return $posts;
	}

	function getFBuserSettings($loginid){
		$qry="select fbuser_id,publish_post,fb_permission from fb_user where subscription_id='".$loginid."'";
		$getresult=exec_query($qry,1);
		if($getresult){
			return $getresult;
		}else{
			return 0;
		}
	}

	function setFBuserSettings($loginid,$publishpost){

		$params['publish_post']=$publishpost;
		$conditions['subscription_id']=$loginid;
		$id=update_query('fb_user',$params,$conditions,$keynames=array());
		if($id){
			return $id;
		}


	}

	function deleteFBUser($loginid){
	    $param='subscription_id';
		$value=$loginid;
		$iddeleted=del_query('fb_user',$param,$value,$optimize=0);
		if($iddeleted){
			return $iddeleted;
		}
	}


	function make_ticker_links($body)
	{
		 $objexchange= new Exchange_Element();
		 $pattern = '/\(([A-Z]{1,5})\)|\$([A-Z]{1,5})/i';
		 preg_match_all($pattern, $body, $stocks_matches);
		 $unique_stocks=array();
		 $uniquestocks=array();
		 foreach($stocks_matches[1] as $id=>$value)
		 {
		   $stock=$objexchange->is_stock($value);
		   if($stock)
		   {
			   if(!in_array($value,$unique_stocks))
			   {
				 $replacement = '(<a href="http://finance.minyanville.com/minyanville?Page=QUOTE&Ticker='.$value.'" style="color:#308700;" target="_blank" title="'.$stock[SecurityName].'">'.$value.'</a>)';
				 $body=preg_replace ("/\(".$value."\)/",$replacement,$body,1);
				$unique_stocks[]=$value;
			   }
		  }
		 }

		foreach($stocks_matches[2] as $id=>$value)
		{
		 if($value){
			  $stock=$objexchange->is_stock($value);
			   if($stock)
			   {
				   if(!in_array($value,$uniquestocks))
				   {
					 $replacement = '$<a href="http://finance.minyanville.com/minyanville?Page=QUOTE&Ticker='.$value.'" style="color:#308700;" target="_blank" title="'.$stock[SecurityName].'">'.$value.'</a>';
					 $body=preg_replace ('/\$'.$value.'/',$replacement,$body,1);
					$uniquestocks[]=$value;

				   }
			  }
		 }
		}

		 return $body;
	}

	function getDefaultTicker(){
		$qry="select TP.id from tt_default_ticker TDT,tt_topic TP where TDT.stock_id=TP.stock_id";
		$result=exec_query($qry,1);
		if($result){
			return $result;
		}


	}
	function getUserDetail($sub_id)
	{
		$qry="select concat(fname,' ',lname) as name,fname,email FROM subscription WHERE id=".$sub_id;
		$result=exec_query($qry,1);
		if($result){
			return $result;
		}
	}

	function getUserStockWatchlist($loginid,$stock_id){
		 $qry="select id,stock_id from ex_user_stockwatchlist where subscription_id='".$loginid."' and stock_id='".$stock_id."'";
		 $result=exec_query($qry,1);
		 if($result){
			return $result;
		 }else{
			return 0;
		 }
	}

	function setUserStockWatchlist($loginid,$stock_id){
	    $params['subscription_id']=$loginid;
		$params['stock_id']=$stock_id;
		$id=insert_query('ex_user_stockwatchlist',$params);
		if($id){
			return $id;
		}else{
			return 0;
		}
	}


	function removeUserStockWatchlist($loginid,$stock_id){
       $qry="delete from ex_user_stockwatchlist where subscription_id='".$loginid."' and stock_id='".$stock_id."'";
		$id=exec_query_nores($qry);
		if($id){
			return $id;
		}else{
			return 0;
		}
	}

	function setTickertalkLogs($type,$subid){
	    $params['type']=$type;
		$params['subscription_id']=$subid;
		$params['date']=date("Y-m-d H:i:s");
		$id=insert_query('tt_logs',$params,$safe=0);
		if(isset($id)){
			return $id;
		}else{
			return 0;
		}
	}

	function showFeedBackForm($email){
		global $D_R,$CDN_SERVER;
		include_once($D_R.'/lib/config/_tickertalk_config.php');
	    global $tickerchatcharlimit,$lang,$HTPFX,$HTHOST,$D_R,$_SESSION;
		$stUserName=ucwords(strtolower($_SESSION['nameFirst']." ".$_SESSION['nameLast']));
		$fileProfanity = "$CDN_SERVER/js/profanity.txt";
		$handle = fopen ($fileProfanity, "r");
		$badwords = fread ($handle, filesize ($fileProfanity));
		fclose ($handle);
		build_lang($uniqueName);
		$longwmsg=$lang['longwords'];
		$notallowed=$lang['wordsnotallowed'];
		$emptytext=$lang['emptytextfeedback'];
	?>

		<div class="talkreply">
		<div class="close_button_reply">
		<div id="showerrfeedback" class="tickerError" style="display:none;">&nbsp;</div>
		<img vspace="3" align="absmiddle" style="cursor: pointer;" onclick="cancelComment();" alt="Close" src="<?=$IMG_SERVER?>/images/tickertalk/closeLogin.jpg"/></div>
        <div id="feedback">
		<span class="form_txt">Name:</span>
		<span class="email_container"><input id="username" type="text" value="<?=$stUserName?>" /></span>
		<span class="form_txt">Email:</span>
		<span class="email_container"><input id="uid" type="text" value="<?=$email?>" /></span>
        <span class="form_txt">Please enter your feedback:</span>

		<textarea class="expand68-200" name="reply" id="reply" cols="6"  maxlength="300" onfocus="return countPostCharacters(this);" onkeyup="return countPostCharacters(this);" onmousedown="return countPostCharacters(this);" onmouseup="return countPostCharacters(this);" onclick="clearErrFeedback();" ></textarea>
		<div id="disable_submit" class="talkreplyLeft" style="display:none; "><img src="<?=$IMG_SERVER?>/images/tickertalk/reply_submit_faded.jpg" border="0" align="absmiddle" vspace="5" alt="Submit Reply"/></div>
		<div id="enable_submit" class="feedbackreply">
		<span id="showcharcount" class="feedback_count" style="padding-top:6px;"></span>
        <img src="<?=$IMG_SERVER?>/images/tickertalk/submit_button.gif" border="0" align="absmiddle" vspace="0" alt="Submit Reply" onclick="sendFeedBack('<?=$badwords;?>','<?=$longwmsg;?>','<?=$notallowed;?>','<?=$emptytext;?>');"/></div></div>

		</div>


	<?
	}

	function setFeedback($uname,$email,$feedback){
	    $params['username']=$uname;
	    $params['email']=$email;
		$params['ip']=$_SERVER['REMOTE_ADDR'];
		$params['feedback']=$feedback;
		$params['date']=date("Y-m-d H:i:s");
		$id=insert_query('tt_feedback',$params,$safe=0);
		return $id;
	}

	function sendFeedBackEmail($username,$email,$feedback){
		global $toFeedBack,$subjectFeedBack,$feedBackTemplate;
		$fromFeedBack=$email;
		if($username){
			$subject=ucwords(strtolower($username)).' '.$subjectFeedBack;
		}else{
			$subject=$email.' '.$subjectFeedBack;
		}
		$strurl = 'username=' . urlencode($username) . '&email=' . $email . '&feedback=' . urlencode($feedback);
	 	mymail($toFeedBack,$fromFeedBack,$subject,inc_web("$feedBackTemplate?$strurl"));
	}

	function showUploadImage(){
	  ?>
	  <div style="float:left;margin-top:1px;">
	  <input name="urlupload" type="text" value="paste image url here" id="urlupload" onfocus="focusedsymbol(this)" onblur="blurredsymbol(this)" />
	  </div><div class="btn_upload ">
	  <img src="<?=$IMG_SERVER?>/images/tickertalk/uploadChart.jpg" border="0" align="absmiddle" vspace="0" alt="Upload" onclick="setuploadchart();"/>
	  </div>
	  <?
	}

	function setUploadUrlImage($url){
	   global $D_R;
		$maxSize = 2097152;
		$arAllowedExt = array("jpg","jpeg","gif","png","bmp");
		$arImageData = getimagesize($url);
		$is_allowed = false;
		if(isset($arImageData['mime']))
		{
			$arFileDetail = explode("/",$arImageData['mime']);
			$fileExt = $arFileDetail[1];
			if(in_array($fileExt,$arAllowedExt))
			{
				$is_allowed = true;
			}
		}
		if(!$is_allowed)
		{
			echo "File type isn't allowed";
			exit(0);
		}
		$newFileName = "url_".rand(2,4)."_".time().".".$fileExt;

/*		$fileName = strtolower(basename($url));
		$fileName = preg_replace('/\s/', '_', $fileName);
		$arFileDetail = preg_split("/\./",$fileName);
		$inParts = count($arFileDetail);
		$fileExt = $arFileDetail[$inParts-1];

		if(!in_array($fileExt,$arAllowedExt))
		{
			echo "File type isn't allowed";
			exit(0);
		}

		$front_name = substr($arFileDetail[0], 0, 15);
		$newFileName = $front_name."_".time().".".$fileExt;*/

		// Create folder of current date.
		$filePath = $this->createUploadFolder();
		$savePath = $filePath."/".$newFileName;
		$pathToImages=$D_R."/assets/tickertalk/charts/original/".date('mdy').'/';
		$pathToThumbs=$D_R."/assets/tickertalk/charts/thumbnail/".date('mdy').'/';

		$data = file_get_contents($url);
		$fp = fopen($pathToImages.$newFileName, 'w+');
		fwrite($fp, $data);
		fclose($fp);

		$thumbWidth="160";
		$imageThumb = $this->createThumbs($pathToImages, $pathToThumbs, $thumbWidth,$newFileName);
		$this->uploadFTPServer($newFileName);
		if($imageThumb)
		{
		  echo "FILEID:" . $newFileName; // Return the file name to the script
		}
		else
		{
			echo 'Error in File Upload';
		}

	}
	function getTopicAssociatedToPost($post_id)
	{
		$arAssociatedTopic[] = 0; // Any post is associated automatically with All Post with topic_id = 0
		$sqlQuery = "SELECT topic_id from tt_topic_chat where chat_id =".$post_id;
		$result=exec_query($sqlQuery);
		foreach($result as $row)
		{
			$arAssociatedTopic[] = $row['topic_id'];
		}
		return $arAssociatedTopic;
	}
	public function uploadFTPServer($file_name)
	{
		/*global $tickerFTPServer,$tickerFTPUser,$tickerFTPPassword,$D_R;
		$ftpConnectionId = ftp_connect($tickerFTPServer);
		$login_result = ftp_login($ftpConnectionId,$tickerFTPUser,$tickerFTPPassword);
		$dir_name = date('mdy');
		ftp_chdir($ftpConnectionId, "original");
		ftp_mkdir($ftpConnectionId, $dir_name);
		ftp_chdir($ftpConnectionId, $dir_name);
		ftp_chmod($ftpConnectionId, 0777,$dir_name);
		$fp = fopen($D_R."/assets/tickertalk/charts/original/".$dir_name."/".$file_name, 'r');
		ftp_fput($ftpConnectionId, $file_name, $fp, FTP_BINARY);
		ftp_cdup($ftpConnectionId);
		ftp_cdup($ftpConnectionId);
		ftp_chdir($ftpConnectionId, "thumbnail");
		ftp_mkdir($ftpConnectionId, $dir_name);
		ftp_chdir($ftpConnectionId, $dir_name);
		ftp_chmod($ftpConnectionId, 0777,$dir_name);
		$fp = fopen($D_R."/assets/tickertalk/charts/thumbnail/".$dir_name."/".$file_name, 'r');
		ftp_fput($ftpConnectionId, $file_name, $fp, FTP_BINARY);
		// close the connection
		ftp_close($ftpConnectionId);*/
		global $D_R,$S5;
		shell_exec("ssh -p16098 root@".$S5." -C 'rsync -avz -e \"ssh -p 16098\" root@".$_SERVER['SERVER_ADDR'].":".$D_R."/assets/tickertalk/charts ".$D_R."/assets/tickertalk/'");
	}

	function showBuzzTicker(){
		 if($_SESSION['SID']) { ?>
	      	<span id="showbuzz" style="float:right; cursor:pointer;"><a onclick="window.open('<?=$HTPFX.$HTHOST?>/buzz/buzz.php',
'Banter','width=350,height=708,resizable=yes,toolbar=no,scrollbars=no'); banterWindow.focus();" style="cursor: pointer;"><img src="<?=$IMG_SERVER?>/images/tickertalk/launch_buzz.jpg" border="0" align="right" alt="Buzz"  /></a></span>
			<? } else { ?>
			<span id="showbuzz" style="float:right;cursor:pointer;"> <a href="<?=$HTPFX.$HTHOST?>/buzzbanter/"><img src="<?=$IMG_SERVER?>/images/tickertalk/get_buzz.jpg" border="0" align="right" alt="Buzz"/></a></span>
			<? }
	}

	function sendReplyPostMail($chatid){
	    global $fromTtreply,$subjectTtreply,$tickerTalkReplyTemplate;
		$qry="select TC.id,TC.subscription_id as replyuserid,TT.subscription_id as userid, TC.body,TC.reply_post_id
	from tt_chat TC, tt_chat TT where TC.id='".$chatid."' and TC.reply_post_id=TT.id";
		$result=exec_query($qry,1);
		if(isset($result)){
			$getreplyusername=$this->getUserDetail($result['replyuserid']);
			$getusername=$this->getUserDetail($result['userid']);
			$username=$getusername['fname'];
			$replyusername=$getreplyusername['name'];
			$to=$getusername['email'];
			$from=$fromTtreply;
			$subject=$subjectTtreply;
			$strurl = 'username=' . urlencode($username) . '&replyusername=' . urlencode($replyusername).'&chatid='.$chatid;
			mymail($to,$from,$subject,inc_web("$tickerTalkReplyTemplate?$strurl"));

		}
	}

	/*Show twitter button*/
	function showTwitterConnect(){
	   if($_SESSION['status']!=="verified"){
			global $HTPFX,$HTHOST,$IMG_SERVER;
			$content = '<a target="_self" href="'.$HTPFX.$HTHOST.'/lib/twitter/redirect.php"><img src="'.$IMG_SERVER.'/images/tickertalk/lighter.png" alt="Sign in with Twitter"/></a>';
			return $content;
		}
	}

	/*Post send to twitter*/
	function sendPostTwitter($post){
	    /* Get user access tokens out of the session. */
		$access_token = $_SESSION['access_token'];
		// status set after verification from twitter
		if($_SESSION['status']=="verified"){
			/* Create a TwitterOauth object with consumer/user tokens. */
			$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
			/* Update post to twitter*/
			$connection->post('statuses/update', array('status' =>$post));
			$this->setTickertalkLogs("twitter",$_SESSION['SID']);
		}
	}

}// end of class ticker
?>