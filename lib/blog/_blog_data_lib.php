<?
session_start();
include_once($D_R."/lib/magpierss/rss_fetch.inc");
include_once($D_R."/lib/MemCache.php");
include_once($D_R."/lib/_includes.php");

class blogData{
	
	function getBlogs()
	   {
		 $objCache= new Cache();
		 $blogResult=$objCache->getCacheBlogPage();
		 return $blogResult->content;
	   }

}//class end
?>