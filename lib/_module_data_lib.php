<?
function getMostViewdAtriclesDailyfeed()
{
	$sqlMostViewdedArticle = "SELECT '1' as item_type,article_recent.id,article_recent.contrib_id, article_recent.title,article_recent.total FROM article_recent WHERE (DATE_ADD(article_recent.date,INTERVAL 7 DAY) >= '".mysqlNow()."') order by total desc LIMIT 0,5";

	$sqlMostViewdedDailyFeed = "SELECT '18' as item_type,DF.id,DF.contrib_id,DF.title, TEMP.countitem AS total FROM daily_feed DF, (SELECT ETV.item_id, COUNT(ETV.item_id) countitem
FROM ex_item_tracking_view ETV WHERE ETV.item_type='18' AND ETV.time BETWEEN DATE_SUB('".mysqlNow()."',INTERVAL 2 HOUR) AND '".mysqlNow()."'
GROUP BY ETV.item_id ORDER BY countitem DESC LIMIT 0,5) AS TEMP WHERE TEMP.item_id = DF.id AND DF.creation_date >= DATE_SUB('".mysqlNow()."',INTERVAL 7 DAY)";

	$sqlQuery ="(".$sqlMostViewdedArticle.") UNION (".$sqlMostViewdedDailyFeed.") ORDER BY total desc LIMIT 0,5";

	$resultMostViewed = exec_query($sqlQuery);
	if($resultMostViewed)
	{
		return $resultMostViewed;
	}
	else
	{
		return NULL;
	}

}
function mostViewedArticle()
{
	$sqlMostViewded = "select '1' as item_type,article_recent.id, article_recent.title,article_recent.keyword,article_recent.blurb,
						article_recent.total from article_recent order by total desc limit 5";
	$resultMostViewed = exec_query($sqlMostViewded);

	if($resultMostViewed)
	{
		return $resultMostViewed;
	}
	else
	{
		return NULL;
	}
}

function mostEmailedArticle($days = 1)
{
	$sqlMostEmailed = "select '1' as item_type,articles.id, articles.title,articles.keyword,articles.blurb,count(tracking_email.time) as total
					   from articles join tracking_email on articles.id = tracking_email.id where approved='1' and is_live='1'
					   and tracking_email.type = 'article' and tracking_email.time between date_sub('".mysqlNow()."',interval $days day)
					   and '".mysqlNow()."' group by tracking_email.id order by total desc limit 5";

	$resultMostEmailed = exec_query($sqlMostEmailed);

	if($resultMostEmailed)
	{
		return $resultMostEmailed;
	}
	else
	{
		return NULL;
	}
}

function mostRecenetArticle()
{
	/*$sqlRecenetArticle = "select '1' as item_type,articles.id, articles.title,articles.keyword,articles.blurb from articles where approved='1' and
						  is_live='1' order by date desc limit 4";*/
						  
	$sqlRecenetArticle = "select '1' as item_type,A.id, A.title,A.keyword,A.blurb,C.name,C.id as contribID from articles A,contributors C where A.approved='1' and A.contrib_id=C.id and A.is_live='1' order by date desc limit 4";					  
						  
	
	$resultRecenetArticle = exec_query($sqlRecenetArticle);

	if($resultRecenetArticle)
	{
		return $resultRecenetArticle;
	}
	else
	{
		return NULL;
	}
}


function featuredProfessor($featuredProfId)
{
	$sqlFeaturedProf = "select name,c.bio_asset,a.body,a.title,a.id as articleid,a.keyword,a.blurb from contributors c ,articles a
						where a.contrib_id=c.id and c.id='$featuredProfId' and a.approved='1'
						order by publish_date DESC limit 3";
	$resultFeaturedProf = exec_query($sqlFeaturedProf);

	if($resultFeaturedProf)
	{
		return $resultFeaturedProf;
	}
	else
	{
		return NULL;
	}
}


function selectedTenProfId($selectedTenProfIds)
{
	$expSelectedTenProfIds = implode(",", $selectedTenProfIds);

	$sqlTenProf = "select id,name from contributors where id IN($expSelectedTenProfIds)";
	$resultTenProf = exec_query($sqlTenProf);

	if($resultTenProf)
	{
		return $resultTenProf;
	}
	else
	{
		return NULL;
	}
}

function makeArticleslinkVille($id,$keyword,$blurb,$from=NULL,$page=NULL)
{
	if($blurb)
    {
		$newline_char=array("\r\n", "\n", "\r");
	    $blurb=str_replace($newline_char," ",$blurb);
	}
	if ($keyword && $blurb)
	{
		$link="/articles/".urlencode($keyword)."/".urlencode($blurb)."/index/a/".$id;
	}
	elseif($keyword && !$blurb)
	{
		$link="/articles/".urlencode($keyword)."/index/a/".$id;
	}
	elseif(!$keyword && $blurb)
	{
		$link="/articles/".urlencode($blurb)."/index/a/".$id;
	}
	else
	{
		$link="/articles/index/a/".$id;
	}
	if($page)
		$link.="/p/".$page;
	if($from)
		$link.="/from/ameritrade";
	return $link;
 }


function getLayoutMenuData(){
 $sql =  "SELECT l.*, lp.alias FROM layout_menu AS l
			LEFT JOIN layout_pages as lp ON l.page_id = lp.id
			WHERE l.navigation_type ='H' AND l.active = 1 order by l.parent_id,l.menuorder Asc";
 $result  = exec_query($sql);
 return $result;
}
// section menu
function sectionmenu()
{
	$memCache = new memCacheObj();
	$key="module_".$menuid;
	$module_data = $memCache->getKey($key);
	if(!$module_data){
		$module_data = array();
		$sql = "elect l.*, s.section_id from layout_menu as l left join section as s on l.id = s.section_id
          where l.active = 1 order by l.id,l.menuorder Asc";
		$res = exec_query($sql,1);
		$module_data = $res;
		$memCache->setKey($key, $module_data, '');
	}
	return $module_data;
}
/*function returns products details for given subscription_def_ids -- deepika*/
function getProdSubPriceVal($subdefids, $orderItemType){
	$orderItemType = strtoupper($orderItemType);
	$sql = "select oc_id,product ,order_code,subscription_def_id,source_code_id,package_id,price,order_code_id, orderItemType from product";
	$sql .= " where orderItemType IN ('".$orderItemType."') and subscription_def_id in (".$subdefids.") ORDER BY id ASC";//htmlprint_r($sql);
	$result  = exec_query($sql);
	return $result;
}
/*function returns page url as alias for given pagename -- deepika*/
function getpageurl($pname){
	$sql = "select alias from layout_pages";
	$sql .= " where name = '".$pname."'";
	$result  = exec_query($sql,1);
	return $result;
}
function iboxgetContributorsArray($authorstr="",$authornotstr=""){
		$config_arr=array("Todd Harrison");
		if(!$authorstr){
			$authorid=$authornotstr;
		}else {
            $strauth=explode(",",$authorstr);
			foreach($strauth as $key=>$value){
			   if($value==1){
			     $matchkey=$key;
			   }
			}
			if (array_key_exists($matchkey, $strauth)) {
			   $my_key=0;
			}
			$authorid=$authorstr.','.$authornotstr;
		}

		$sql_authors="select id,name,SUBSTRING_INDEX(name,' ',-1)`lname` from contributors where id not in($authorid) order by lname";
		$getauthors_arr=exec_query($sql_authors);

		$displayarr=array();
		foreach($getauthors_arr as $key=>$value){
		$displayarr[$value['id']]=$value['name'];
		}

		$keyarra=array();
		for($i=0;$i<count($config_arr);$i++){
			$key=array_search($config_arr[$i],$displayarr);
			$keyarra[$key]=$config_arr[$i];
		}
		$result_authors=$keyarra+$displayarr;


		$getauthors_arr_filterd=array();
		$l=0;
		foreach($result_authors as $ket=>$vat){
		$getauthors_arr_filterd[$l]['id']=$ket;
		$getauthors_arr_filterd[$l]['name']=$vat;
		$l++;
		}

		if(array_key_exists($my_key, $getauthors_arr_filterd)) {
        unset($getauthors_arr_filterd[$my_key]);
   		 }
		return $getauthors_arr_filterd;
}


function getSourceCode($activeprods){
	$sql = "select source_code_id from discount";
	$sql .= " where active_products = '".$activeprods."' ";
	$result  = exec_query($sql,1);
	return $result;

}
function getPromoStatus($pCode){
	$sql = "select promo_text, source_code_id from promo_codes";
 	$sql .= " where promo_text = '".$pCode."' ";
	$result  = exec_query($sql,1);
	return $result;
}

function addToCartRegister($sdefid){
	$objcart=new ViaCart('cart');
	$qry="select orderItemType from product where subscription_def_id='".$sdefid."'";
	$result=exec_query($qry,1);
	$orderItemType=$result['orderItemType'];
	$pdata = getProdSubPriceVal($sdefid, $orderItemType);
	$objcart->add_item($sdefid,$pdata['0'], $q=1);

}

function addToCartRegisterPromocode($pCode){
	if($pCode){
		$pCode  = trim(stripslashes($pCode));
		$result = getPromoStatus($pCode);
		set_sess_vars("promoCodeValue",$pCode);
		if($result['source_code_id']!=''){
		set_sess_vars("validPromoCode",true);
		set_sess_vars("promoCodeSourceCode",$result['source_code_id']);
			return NULL;
		}else{
			return 1;
		}
	}

}

function getViaAnnual(){
	$sqlGetViaAnnual="SELECT subscription_def_id FROM product WHERE subType IN ('Annual','Annual Trial') ORDER BY subscription_def_id";
	$resGetViaAnnual=exec_query($sqlGetViaAnnual);
	foreach($resGetViaAnnual as $product){
		$viaAnnualProducts[]=$product['subscription_def_id'];
	}
	return $viaAnnualProducts;
}

?>