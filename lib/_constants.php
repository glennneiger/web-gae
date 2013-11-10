<?php
global $HTTP_HOST,$PHP_SELF;
	$PHP_SELF=$_SERVER['PHP_SELF'];
	$is_dev="0";
	$SYSADMIN_EMAIL="mvilsupport@mediaagility.com";
	//$is_ssl=($_SERVER['HTTP_X_FORWARDED_PORT']==443);
	$HTPFX=($is_ssl?"https://":"http://");
	$HTPFX="http://";
	$HTPFXSSL="https://";
	$HTPFXNSSL="http://"; /*For Non SSL*/
	$HTPORT="";
	$NOSSLPORT="";
	if($_SERVER['HTTP_HOST'] =="localhost:8080")
	{
		$IMG_SERVER="http://storage.googleapis.com/mvassets";
		$SSL_IMG_SERVER="http://localhost:8080";
		$CDN_SERVER=$HTPFX."localhost:8080";
		$HTHOST="localhost:8080";
		$VIDEOHOST="localhost:8080";
		$HTADMINHOST="localhost:8080";
		$STORAGE_SERVER = "http://storage.googleapis.com/mvassets";
	}
	else
	{
		$IMG_SERVER="http://storage.googleapis.com/mvassets";
		$SSL_IMG_SERVER="http://storage.googleapis.com/mvassets";
		$CDN_SERVER="http://minyanville-buzz.appspot.com";
		$HTHOST="minyanville-buzz.appspot.com";
		$VIDEOHOST="minyanville-buzz.appspot.com";
		$HTADMINHOST="minyanville-buzz.appspot.com";
		$STORAGE_SERVER = "http://storage.googleapis.com/mvassets";
	} 
	$HTDOMAIN="$HTPFX$HTHOST$HTPORT";
	$HTNOSSLDOMAIN="http://$HTHOST$NOSSLPORT";
	
	$IMG_FTP_PARAMS=array(
			"host"=> $IMG_SERVER,
			"user"=> "minyanville",
			"pass"=> "IpQxa2X8%v",
			"path"=> "/home/sites/minyanville/web" //remote user is chrooted. rel path
		);

	$StorageListPath = "https://www.googleapis.com/storage/v1beta2/b/mvassets/o";
	$CKRootPath = "assets/FCK_Jan2011/images/";


		$SECURE_PATHS=array("store.checkout","/housing-market-report/");

		$S4="10.192.226.51";
		$S5="10.252.34.239";
		$IE = stristr(strtolower($_SERVER['HTTP_USER_AGENT']),"msie")?1:0;
		$MACIE = $IE && stristr(strtolower($_SERVER['HTTP_USER_AGENT']),"mac")?1:0;
		$SITEHOME="/home/sites/minyanville";
	$POSTBACK=(count($_POST)?1:0);

	//workaround for APACHE not returning querystring when ext is omitted
	//parse_str(substr($REQUEST_URI,strpos($REQUEST_URI,"?")+1));
	$PATH_FR = "";//path from the root of site, e.g.: minyanville.com/site where 'site' is root
	$ASSET_PATH="/assets/";
	$ADMIN_URL = "/admin";
	$ADMIN_PATH = "${D_R}$ADMIN_URL";
	$CHAR_PATH = "/assets/characters";
	$CHAR_BIO = array(126,140);
	$CHAR_ICON  = array(50,50);
	$DATE_STR = "m/j/Y h:i:s a";
	$SCRIPT_DIR="/home/sites/minyanville/scripts";
	$PROD_PATH="/assets/products";

/*==============subscription system=========================*/
	$REG_ALLOW_MULTI_LOGIN=0;//allow multiple logins per account
	$REG_TABLE="subscription";
	$REG_REQ_FIELDS=explode(" ","password fname lname email agegroup zip country jobtitle");
	$REG_EML_REPLYTO = "Minyanville<support@minyanville.com>";
	$SUBSCRIPTION_EML_REPLYTO = "Minyanville <subscriptions@minyanville.com>";
	$REG_EML_REPLYTO_TRIAL = "Minyanville <subscriptions@minyanville.com>";
	$REG_EML_SUBJECT = "Welcome to the 'Ville";
	$REG_EML_TMPL    = "$HTNOSSLDOMAIN/emails/_eml_register.htm";
	$COMBO_EML_TMPL    = "$HTNOSSLDOMAIN/emails/_eml_register_cooper.htm";
	$COMBO_QUINT_EML_TMPL    = "$HTNOSSLDOMAIN/emails/_eml_register_quint.htm";
	$REG_URL="/register/";
	$FORGOTPASS_TMPL="$HTNOSSLDOMAIN/emails/_eml_forgotpass.htm";
	$FORGOTPASS_SUBJECT="Your Minyanville.com password";
	$FORGOTPASS_FROM="Minyanville <support@minyanville.com>";
/****Kamal  Added on 28th Sep ,Trail Account Email*****/
	$TRIAL_ACTIVATE_EML_TMPL="$HTNOSSLDOMAIN/emails/_eml_trial_register.htm";
	$TRIAL_ACTIVATE_EML_SUBJECT="Welcome to Minyanville";
	$REG_EML_TRIAL_REPLYTO = "Minyanville <support@minyanville.com>";

	$AUTH_URL="/auth.htm";  //page that sets up session to auth them
	$UNAUTH_URL="/auth.htm?LOGOUT=1"; //page unauths them
	$UNAUTH_URL_KILLALL="/auth.htm?LOGOUT=1&KILL=1";
	$SIGNIN_URL="/"; //page where user fills out form
	$JOURNAL_URL="/gazette/journal/"; //default page
	$NEWSVIEWS_URL="/gazette/newsviews/";
	$DEFAULT_URL="/register/?signedin=1";
	$MANAGE_URL="$HTNOSSLDOMAIN/register/manage.htm";
	$DEFAULT_NO_POS="No positions in stocks mentioned.";

	$ARTICLE_TABLE="articles";

	$NOTIFY_JOURNAL_TMPL="$HTNOSSLDOMAIN/emails/_eml_post_notify.htm";
	$NOTIFY_JOURNAL_FROM="Minyanville <support@minyanville.com>";
	$NOTIFY_JOURNAL_FROM_NAME="Minyanville";
	$NOTIFY_JOURNAL_FROM_EMAIL="support@minyanville.com";
	$NOTIFY_JOURNAL_TO="Minyanville Mailing List <support@minyanville.com>";
	$NOTIFY_JOURNAL_SUBJECT="New Post on Minyanville.com";

	$SPAM_DEFAULT_TO="Minyanville Mailing List <support@minyanville.com>";
	$SPAM_DEFAULT_FROM="Minyanville <support@minyanville.com>";
	$SPAM_EML_TMPL="$HTDOMAIN/emails/_eml_opt.htm";

	$STU_FROM="Minyanville Customs Office <support@minyanville.com>";
	$STU_SUBJECT="Application for Student Visa";
	$STU_TMPL = "http://$HTTP_HOST/emails/_eml_stu_register.htm";

	$CONTACT_TO_EMAIL = "Minyanville <bill@minyanville.com>";
	$CONTACT_CHAR_EMAIL="Minyanville <todd@minyanville.com>";
	$CONTACT_SUBJECT_MAP=array(
		"General Inquiry"=>"Minyanville <support@minyanville.com>",
		"Customer Support"=>"Minyanville <support@minyanville.com>",
		"Technical Support"=>"Minyanville <support@minyanville.com>",
		"Advertiser Information"=>array("bill@minyanville.com","kevin@minyanville.com"),
		"Strategic Alliance Information"=>array("support@minyanville.com","kevin@minyanville.com"),
		"Media Relations"=>"Minyanville <kevin@minyanville.com>",
		"Ruby Peck Foundation"=>array("bill@minyanville.com","kevin@minyanville.com"),
		"Merchandise and Shipping"=>array("support@minyanville.com","Minyanville@JFCSales.com","kevin@minyanville.com","shipping@minyanville.com")/*,
		"Hoofy™"=>"Minyanville <todd@minyanville.com>",
		"Boo™"=>"Minyanville <todd@minyanville.com>",
		"Daisy the Cow™"=>"Minyanville <todd@minyanville.com>",
		"Sammy the Snake™"=>"Minyanville <todd@minyanville.com>",
		"Snapper the Turtle™"=>"Minyanville <todd@minyanville.com>",*/
	);


		$EMAIL_LISTS=array(
			"gazette" => "$SCRIPT_DIR/recv_daily_gazette.list",
			"promo"   => "$SCRIPT_DIR/recv_promo.list"
		);



	$SLIDESHOW_QUERY="SELECT s.*,sc.slideshow_id,sc.slide_title,sc.body
		     FROM
				 slideshow s,slideshow_content sc,contributors c
		     WHERE
				 sc.slideshow_id=s.id and
			     s.contrib_id=c.id ";

	$SLIDESHOWCONT_QUERY="SELECT s.id,sc.id,sc.slideshow_id,sc.slide_title,sc.body,sc.slide_image
		     FROM
				 slideshow s,slideshow_content sc,contributors c
		     WHERE
				 sc.slideshow_id=s.id and
			     s.contrib_id=c.id ";

	$ARTICLE_QUERY="
		SELECT a.*, a.id article_id, cont.bio_asset,
			position, cont.disclaimer,cont.name author,
c.name 'character',c.id character_id, ci.asset character_asset,bi.assets branded_asset,	UNIX_TIMESTAMP(a.date)udate
		FROM
contributors cont,$ARTICLE_TABLE a
LEFT JOIN character_images ci ON(a.character_img_id=ci.id)
LEFT JOIN branded_images bi ON(a.branded_img_id=bi.id)
LEFT JOIN characters c ON(c.id=ci.character_id)
		WHERE
			a.contrib_id=cont.id";


$ARTICLE_QUERY_EMAIL="
		SELECT a.*, a.id article_id, cont.bio_asset,
			position, cont.disclaimer,cont.name author,
c.name 'character',c.id character_id, ci.asset character_asset,bi.assets branded_asset,
			ac.title category, ac.url, ac.name category_code,
			UNIX_TIMESTAMP(a.date)udate
		FROM
contributors cont,email_categories ac,$ARTICLE_TABLE a
LEFT JOIN character_images ci ON(a.character_img_id=ci.id)
LEFT JOIN branded_images bi ON(a.branded_img_id=bi.id)
LEFT JOIN characters c ON(c.id=ci.character_id)
		WHERE
			a.contrib_id=cont.id
			AND find_in_set(ac.id,a.email_category_ids)
	";

	$ARTICLE_LIST_QUERY="SELECT a.id article_id, a.category_ids,
			a.character_img_id, a.character_text, a.position, a.date,
			a.title,a.blurb,cont.bio_asset,cont.name contributor,a.contrib_id,
			ac.title category, ac.url, ac.name category_code,
			UNIX_TIMESTAMP(a.date)udate
		FROM
			$ARTICLE_TABLE a, contributors cont,article_categories ac
		WHERE a.contrib_id=cont.id
		AND find_in_set(ac.id,a.category_ids)
	";
	$SEARCH_QRY="
		SELECT a.id, a.id article_id,a.contrib_id,UNIX_TIMESTAMP(a.date)udate,a.date,a.title,
		IF((LENGTH(a.blurb)<5), SUBSTRING(a.body,1,100) , a.blurb) blurb,a.keyword,a.blurb headline,c.name,c.name contributor,
		ac.name category,ac.title category_title,ac.id category_id,
		ac.url category_url,
		concat('$HTNOSSLDOMAIN',ac.url,'?id=',a.id)article_url
		FROM
			$ARTICLE_TABLE a, contributors c
		WHERE
			a.contrib_id=c.id ";

	$SEARCH_QRY_DIGEST="
	  SELECT a.id, a.keyword,a.id article_id,a.contrib_id,UNIX_TIMESTAMP(a.date)udate,a.date,a.title,
	  IF((LENGTH(a.blurb)<5), SUBSTRING(a.body,1,100) , a.blurb) blurb,c.name,c.name contributor
	  FROM
	   $ARTICLE_TABLE a, contributors c
	  WHERE
	   a.contrib_id=c.id  ";

		$SEARCH_PAGESIZE=10;

/*===========================share functions======================*/
	$SHARE_EML_SUBJECT = " from Minyanville";
	$SHARE_EML_TMPL    = "$HTNOSSLDOMAIN/emails/_eml_share.htm";

	/*=====================Added for Redesign=======================*/
	$NAVIGATION=array('HOME'=> array('logo'=>"minyanville_logo_home.jpg",
									'title' =>"Home",
									'below_nav_image'=>"home_whitebg.gif",
									'menuTitle'=> "Home"),
					'PROFESSIONAL PRODUCTS'=>array('logo'=>"mv_logo_mvpro.jpg",
									'title' =>"PROFESSIONAL PRODUCTS",
									'below_nav_image'=>"customproducts_whitebg.gif",
									'menuTitle'=> "MVPro"),
					'NEWS & VIEWS'=> array('logo'=>"mv_logo_newsviews.jpg",
									'title' =>"NEWS & VIEWS",
									'below_nav_image'=>"newsviews_whitebg.gif",
									'menuTitle'=> "NEWS & VIEWS"),
					'ENTERTAINMENT'=> array('logo'=>"mv_logo_entertainment.jpg",
									'title' =>"ENTERTAINMENT",
									'below_nav_image'=>"entertainment_whitebg.gif",
									'menuTitle'=> "ENTERTAINMENT"),
					'COMMUNITY'=> array('logo'=>"mv_logo_community.jpg",
									'title' =>"COMMUNITY",
									'below_nav_image'=>"community_whitebg.gif",
									'menuTitle'=> "COMMUNITY"),
					'EDUCATION'=> array('logo'=>"mv_logo_education.jpg",
										'title'=>"EDUCATION",
										'below_nav_image'=>"education_whitebg.gif",
									'menuTitle'=> "EDUCATION"),
					'KIDS & FAMILY'=> array('logo'=>"mv_logo_education.jpg",
									'title'=>"KIDS & FAMILY",
									'below_nav_image'=>"education_whitebg.gif",
									'menuTitle'=> "KIDS & FAMILY")
					);
					//For MVTV Adimin tool
					$mvtv_video_ext="flv";
					$mvtv_still_ext="jpg";
					$mvtv_thumb_ext="jpg";
					$Video_file_path='mvtv/videos/'; //Video file path
					$Podcast_video_file_path='mvtv/videos/podcasting/'; //Podcast Video file path
					$Still_file_path='mvtv/stills/'; //Still file path
					$Thumb_file_path='mvtv/thumbs/'; //Thumb file path
					$MVTVURL="/mvtv/";
					$VIDEO_SERVER="http://storage.googleapis.com/mvassets/";

/*========= Memcache Configuration ======*/
$MEMCACHE_SERVERS = array(
	"ec2-107-20-213-200.compute-1.amazonaws.com",
	"ec2-107-20-213-201.compute-1.amazonaws.com",
	"ec2-107-20-209-14.compute-1.amazonaws.com",
	"ec2-107-20-213-206.compute-1.amazonaws.com",
	"ec2-107-20-209-13.compute-1.amazonaws.com",
	"ec2-107-20-214-255.compute-1.amazonaws.com"
);
$KEY_TYPES = array (
	"MODULE"=>"module_",
	"ARTICLE"=>"article_"
);
/*========MemCache Configuration Ends=====*/
/*======Credit Card Transaction Error Alert Configuration Start======*/
$CREDIT_CARD_PAYMENT_ERROR_TO="support@minyanville.com, mvilsupport@mediaagility.com";
$CREDIT_CARD_PAYMENT_ERROR_FROM="mvilsupport@mediaagility.com";
$TransactionError_EmailAlert_Template=$HTPFX.$HTHOST."/emails/_eml_alert_transaction_error.htm";
/*======Credit Card Transaction Error Alert Configuration End======*/
/*======Podcasting Enclosure Type Configuration Start======*/
$PODCAST_ENCLOSURE_TYPES=array(
        '.mp3'=>"audio/mpeg",
        '.m4a'=>"audio/x-m4a",
        '.mp4'=>"video/mp4",
        '.m4v'=>"video/x-m4v",
        '.mov'=>"video/quicktime",
        '.pdf'=>"application/pdf",
    );
/*======Podcasting Enclosure Type Configuration End======*/
/*=====Fox FTP cron Configuration Start======*/
$addr_ftp = 'jetstream.foxnews.com' ;
$user_ftp = 'minyanville' ;
$pwd_ftp =  'F0xnews!!' ;
$root_ftp = '/users/miscusr/Minyanville/';
/*=====Fox FTP cron Configuration End======*/


/*Site redesign constant changes start here*/

$contentcount=10;
$maximagewidthvideo=88;
$maximageheightvideo=66;
$watchlistlimit=5;
$stockslimit=5;
$relatedarticlelimit=3;
$alsobylimit=3;
$LOGO_PATH="/assets/branded_images/";

$featuredProfssId = '1';
$selectedTenProfIds = array('1','122','15','48','108','90','179','173','109','132');
$businessmarketIds = array(1,15,59,108,48,50);
$lifemoneyIds = array(1,95,84,137,140,148);
$familyfinanceIds = array(1,95,115,123,155,166);
/*Site redesign constant changes end here*/

$SPAM_EML_MULTIPLE_ARTICLE_TMPL=$HTPFX.$HTADMINHOST."/emails/_eml_approve_articles.htm";


/*******MS-Word Characters Replacement conf Start**********/
$specialchar=array("/'/","/’/","/''/","/“/","/–/","/…/");
$replacechar=array("'","'",'"','"',"-","...");
/*******MS-Word Characters Replacement conf End**********/

/*==============Tracking Configuration ======*/
include_once($D_R.'/lib/config/_checkm8_config.php');
/*==============Tracking Configuration ======*/

/*==============Tracking Configuration ======*/
include_once($D_R.'/lib/config/_db_config.php');
/*==============Tracking Configuration ======*/

/*==============Tracking Configuration ======*/
include_once($D_R.'/lib/config/_exchange_config.php');
/*==============Tracking Configuration ======*/

/*==============Tracking Configuration ======*/
include_once($D_R.'/lib/config/_google_referral_config.php');
/*==============Tracking Configuration ======*/

/*==============Tracking Configuration ======*/
include_once($D_R.'/lib/config/_googleadwords_config.php');
/*==============Tracking Configuration ======*/

/*==============Tracking Configuration ======*/
include_once($D_R.'/lib/config/_gpom_config.php');
/*==============Tracking Configuration ======*/

/*==============Tracking Configuration ======*/
include_once($D_R.'/lib/config/_products_config.php');
/*==============Tracking Configuration ======*/

/*==============Tracking Configuration ======*/
include_once($D_R.'/lib/config/_slideshow_config.php');
/*==============Tracking Configuration ======*/

/*==============Tracking Configuration ======*/
include_once($D_R.'/lib/config/_store_config.php');
/*==============Tracking Configuration ======*/

/*==============Tracking Configuration ======*/
include_once($D_R.'/lib/config/_exchange_config.php');
/*==============Tracking Configuration ======*/

/*==============Tracking Configuration ======*/
include_once($D_R.'/lib/config/_syndication_config.php');
/*==============Tracking Configuration ======*/

/*==============Tracking Configuration ======*/
include_once($D_R.'/lib/config/_tickertalk_config.php');
/*==============Tracking Configuration ======*/

/*==============Tracking Configuration ======*/
include_once($D_R.'/lib/config/_tracking_config.php');
/*==============Tracking Configuration ======*/

/*==============Video Configuration ======*/
include_once($D_R.'/lib/config/_video_config.php');
/*==============Tracking Configuration ======*/

/*==============Google Analytics Configuration ======*/
include_once($D_R.'/lib/config/_ga_config.php');
/*==============Google Analytics Configuration ======*/

/*==============Yahoo Buzz Configuration ======*/
include_once($D_R.'/lib/config/_yahoobuzz_config.php');
/*==============Yahoo Buzz Configuration ======*/

/*==============Cache Configuration ======*/
include_once($D_R.'/lib/config/_cache_config.php');
/*==============Cache Configuration ======*/


/*bitly configuration*/
/* $bitlylogin='minyanville';
$bitlyAuthenticatekey='R_9f8295f466dd9017d0bbe5be7ef59e45'; */

$bitlylogin='minyanville11';
$bitlyAuthenticatekey='R_177206b2f8ef1b177e7a91cc5a2239ec';


/* Google ReCaptcha */
// $publicCaptchakey = "6LdlKMASAAAAAG-jwh0ddfwnAPNVpePUAzvHqDgs";
// $privateCaptchakey = "6LdlKMASAAAAAPP9ws8fitCKvi1eGJ6NHeu6JG5x";
$publicCaptchakey = "6Le_v8ESAAAAABp4gFRg3lwmIbn5BFwZXBad04jz";
$privateCaptchakey = "6Le_v8ESAAAAAM48lm12qquf1gLLnqHOZHWZNxEI ";
?>
