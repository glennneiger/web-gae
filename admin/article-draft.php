<?php
session_start();
global $D_R,$IMG_SERVER;
include_once("$D_R/admin/lib/_admin_data_lib.php");
include_once($D_R."/admin/lib/_contributor_class.php");
include_once($D_R."/admin/lib/_article_data_lib.php");
include_once($D_R."/lib/_misc.php");

//print_r($_POST);
//die;
$articleData['id']=$_POST['id'];
$artObj=new ArticleData();
		
		$objContributor=new contributor();
		$arrContributorDetails=$objContributor->getContributor($_POST['articles']['contrib_id']);
		$articleData['contributor']=addslashes(mswordReplaceSpecialChars(stripslashes($arrContributorDetails['name'])));
	
		$articleData['seo_title']=addslashes(mswordReplaceSpecialChars(stripslashes($_POST['articles']['seo_title'])));
		$articleData['title']=addslashes(mswordReplaceSpecialChars(stripslashes($_POST['articles']['title'])));
		$articleData['blurb']=addslashes(mswordReplaceSpecialChars(stripslashes($_POST['articles']['blurb'])));
		$articleData['character_text']=addslashes(mswordReplaceSpecialChars(stripslashes($_POST['articles']['character_text'])));
		$articleData['position']=addslashes(mswordReplaceSpecialChars(stripslashes($_POST['articles']['position'])));
		$articleData['note']=addslashes(mswordReplaceSpecialChars(stripslashes($_POST['articles']['note'])));
		$articleData['contrib_id']=$_POST['articles']['contrib_id'];
		$articleData['date']=$_POST['articles']['date'];
		if(is_array($_POST['articles'][email_category_ids])){
			$articleData['email_category_ids']=implode(",",$_POST['articles'][email_category_ids]);
		}else{
			$articleData['email_category_ids']=$_POST['articles'][email_category_ids];
		}
		$articleData['email_category_ids']=trim($articleData['email_category_ids'],",");
		
		if($_POST['articles'][subsection_ids]){
			$articleData['subsection_ids']=implode(",",$_POST['articles'][subsection_ids]);
		}else{
			$articleData['subsection_ids']=$_POST['articles']['navigation_section_id'][0];
		}

		$articleData['subsection_ids']=trim($articleData['subsection_ids'],",");

		$articleData['navigation_section_id']=$_POST['articles']['navigation_section_id'][0];
		$sectionId=$artObj->getArticleSectionDetail($articleData['navigation_section_id']);
		$articleData['subsection_ids']=$articleData['subsection_ids'].','.$sectionId['parent_section'];
		$articleData['section_id']=$sectionId['parent_section'];
		$articleData['layout_type']=$_POST['articles'][layout_type];
		$articleData['editor_note']=$_POST['articles'][editor_note];
		$articleData['featureimage']=$_POST['articles'][featureimage];
		$articleData['is_featured']=($_POST['articles'][is_featured]=="1")?1:0;
		$articleData['is_buzzalert']=($_POST['articles'][is_buzzalert]=="1")?1:0;
		$articleData['is_msnfeed']=($_POST['articles'][is_msnfeed]=="1")?1:0;
		$articleData['hosted_by']=$_POST['articles'][hosted_by];
		$articleData['is_yahoofeed']=($_POST['articles'][is_yahoofeed]=="1")?1:0;
		$articleData['is_marketwatch']=($_POST['articles'][is_marketwatch]=="1")?1:0;
		$articleData['sent']=($_POST['articles'][sent])?1:0;
		$articleData['is_live']=$_POST['articles']['is_live'];
		// syndication check for street articles
		if($articleData['layout_type']== 'thestreet' || $articleData['layout_type']== 'realmoneysilver' || $articleData['layout_type']== 'observer')
		{
			$articleData['is_msnfeed']=0;
			$articleData['is_yahoofeed']=0;
			$articleData['is_marketwatch']=0;
		}
		
		//$articleData['pages']= explode('{PAGE_BREAK}',$_POST['articles']['body']);
		$articleData['pages']= explode('{PAGE_BREAK}',$_POST['editorDatafld']);
		
		if(is_array($articleData['pages']))
			$articleData['body']=implode("<br/><br/>",$articleData['pages']);

		$articleData['tags']=addslashes(mswordReplaceSpecialChars(stripslashes(trim($_POST['articles']['tag']))));
		$articleData['keyword']=addslashes(mswordReplaceSpecialChars(stripslashes(trim($_POST['articles']['keyword']))));



		if($articleData['layout_type']== 'live-blog')
		{
			$articleData['cover_it_live']=	addslashes(mswordReplaceSpecialChars(stripslashes(trim($_POST['articles']['cover_it_live']))));
			$articleData['contr_live_login_desc']=	trim($_POST['articles']['contr_live_login_desc']);
		}
		
		if($_POST['articles']['is_streetfeed'])
		$articleData['is_streetfeed']=	trim($_POST['articles']['is_streetfeed']);
		
		/*Google Editor's Pick*/
		if($_POST['articles']['google_editors_pick'])
		$articleData['google_editors_pick']=	trim($_POST['articles']['google_editors_pick']);
		
		/*Google News Standout*/
		if($_POST['articles']['google_news_standout'])
		$articleData['google_news_standout']=	trim($_POST['articles']['google_news_standout']);
		
		if($_POST['articles']['streetfeed_type'])
		$articleData['streetfeed_type']=	trim($_POST['articles']['streetfeed_type']);

$articleData['admin_id']=$_SESSION['AID'];

$cond['admin_id']=$_SESSION['AID'];

$id=insert_or_update("articles_draft",$articleData,$cond);

?>