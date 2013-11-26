<?php
global $D_R,$HTPFX,$HTHOST,$redirectArr;
include_once($D_R."/lib/config/_redirect_config.php");
class redirectPage{
	
	public $pageUrl;
	
	public function redirectPage()
	{
		$pageUrlArr = explode('?',$_SERVER['REQUEST_URI']);
		$this->pageUrl = $pageUrlArr['0'];
		$this->pageHost = $_SERVER['HTTP_HOST'];
	}
	
	public function redirectUrl($red_url)
	{
		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".$red_url);
		exit;	
	}
	
	public function checkRedirection()
	{
		global $redirectArr;
		$url = $this->pageUrl;
		$host = $this->pageHost;	
			
		switch($host)
		{
			case (preg_match('/^local\.(minyanville)\.com$/', $host) ? true : false):
			case (preg_match('/^uklocal\.(minyanville)\.com$/', $host) ? true : false):
			case (preg_match('/^canadalocallocal\.(minyanville)\.com$/', $host) ? true : false):
			case (preg_match('/^401-k-plans\.(.*)$/', $host) ? true : false):
			case (preg_match('/^investment-advisors\.(.*)$/', $host) ? true : false):
			case (preg_match('/^broker-dealers\.(.*)$/', $host) ? true : false):
			case (preg_match('/^investment-banks\.(.*)$/', $host) ? true : false):
			case (preg_match('/^online-trading\.(.*)$/', $host) ? true : false):
			case (preg_match('/^finance-jobs\.(.*)$/', $host) ? true : false):
			case (preg_match('/^financial-advisors\.(.*)$/', $host) ? true : false):
			case (preg_match('/^m\.(.*)$/', $host) ? true : false):
			case (preg_match('/^blogs\.(.*)$/', $host) ? true : false):
			case (preg_match('/^newblogs\.(.*)$/', $host) ? true : false):
			case (preg_match('/^ads\.minyanville\.com/', $host) ? true : false):
			case (preg_match('/^model\.minyanville\.com/', $host) ? true : false):
				$this->redirectUrl($HTPFX.$HTHOST.'/');
				break;
			case (preg_match('/^84\.40\.30\.14\.?(:[0-9]+)?$/', $host,$matches) ? true : false):
				$this->redirectUrl($HTPFX.$HTHOST.'/'.$matches[1]);
				break;
			default:
				break;
		}

		switch($url){			
			case ((isset($redirectArr[$url]) && $redirectArr[$url]!="")? true : false):
				$this->redirectUrl($redirectArr[$url]);
				break;
			case (preg_match('/^\/support\/buzzandbanter$/', $url) ? true : false):
				$this->redirectUrl('http://mvp.minyanville.com/support-buzzandbanter/');
				break;
			case (preg_match('/^\/customproducts(.*)/', $url) ? true : false):
			case (preg_match('/^\/subscription\/softtrials\/flexfolio(.*)/', $url) ? true : false):
			case (preg_match('/^\/subscription\/softtrials\/schwab(.*)/', $url) ? true : false):	
			case (preg_match('/^\/subscription/softtrials\/?/', $url) ? true : false):
			case (preg_match('/^\/laveryinsight(.*)/', $url) ? true : false):
			case (preg_match('/(.*)\/buyhedge\/(.*)\/?$/', $url) ? true : false):
			case (preg_match('/^\/garyk\/?/', $url) ? true : false):
			case (preg_match('/^\/housing-market-report\/all-post\/p\/(.*)/', $url) ? true : false):
			case (preg_match('/^\/housing-market-report\/all-post\//', $url) ? true : false):
			case (preg_match('/^\/housing-market-report\/archive\/p\/(.*)/', $url) ? true : false):
			case (preg_match('/^\/housing-market-report\/archive\//', $url) ? true : false):
			case (preg_match('/^\/housing-market-report\/pdf\/(.*)/', $url) ? true : false):
			case (preg_match('/^\/housing-market-report\/issue\/(.*)/', $url) ? true : false):
			case ( preg_match('/^\/housing-market-report\/(.*)\//', $url) ? true : false):
			
			case (preg_match('/^\/active-investor\/post\/id\/([0-9]*)\/$/', $url) ? true : false):				
				$this->redirectUrl($HTPFX.$HTHOST.'/subscription/');
				break;
				
			
			case (preg_match('/^\/community\/register\/(.*)/', $url) ? true : false):				
				$this->redirectUrl($HTPFX.$HTHOST.'/community/home.htm');
				break;
			case (preg_match('/^\/section\/businessmarket\/?/', $url) ? true : false):
			case (preg_match('/^\/subsection\/stockideas\/?/', $url) ? true : false):	
			case (preg_match('/^\/news_views\/?/', $url) ? true : false):	
			case (preg_match('/^\/subsection\/options\/?/', $url) ? true : false):	
			case (preg_match('/^\/subsection\/economy\/?/', $url) ? true : false):	
			case (preg_match('/^\/subsection\/advancetrading\/?/', $url) ? true : false):	
			case (preg_match('/^\/subsection\/fivethings\/?/', $url) ? true : false):	
			case (preg_match('/^\/subsection\/randomthoughts\/?/', $url) ? true : false):	
			case (preg_match('/^\/subsection\/twowaystoplay\/?/', $url) ? true : false):
			case (preg_match('/^\/businessmarkets\/?$/', $url) ? true : false):
			case (preg_match('/^\/markets\/?$/', $url) ? true : false):
			case (preg_match('/^\/markets\/business-news\/?$/', $url) ? true : false):
			case '/section/businessmarket/tradingideas.htm':					
				$this->redirectUrl($HTPFX.$HTHOST.'/business-news/');
				break;	
			case (preg_match('/^\/section\/lifemoney\/?/', $url) ? true : false):
			case (preg_match('/^\/subsection\/mindyourbusiness\/?/', $url) ? true : false):
			case (preg_match('/^\/subsection\/fiscalhygiene\/?/', $url) ? true : false):
			case (preg_match('/^\/subsection\/whyshouldicare\/?/', $url) ? true : false):
			case (preg_match('/^\/subsection\/economicsnapshot\/?/', $url) ? true : false):
			case (preg_match('/^\/subsection\/householdceo\/?/', $url) ? true : false):
			case (preg_match('/^\/subsection\/umv\/?/', $url) ? true : false):
			case (preg_match('/^\/subsection\/teachyourkids\/?/', $url) ? true : false):
			case (preg_match('/^\/section\/familyfinance\/?/', $url) ? true : false):
			case (preg_match('/^\/investing\/?$/', $url) ? true : false):					
				$this->redirectUrl($HTPFX.$HTHOST.'/trading-and-investing/');
				break;	
			case (preg_match('/^\/economy\/?/', $url) ? true : false):					
				$this->redirectUrl($HTPFX.$HTHOST.'/business-news/the-economy/');
				break;
			case (preg_match('/^\/subsection\/features\/?/', $url) ? true : false):	
			case (preg_match('/^\/special-features\/products\/?$/', $url) ? true : false):	
			case (preg_match('/^\/special-features\/companies\/?$/', $url) ? true : false):	
			case (preg_match('/^\/special-features\/people\/?$/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/special-features/');
				break;
			case (preg_match('/^\/dailyfeed\/tag\/(.*)\/p\/(.*)/', $url, $matches) ? true : false):						
				$url = $HTPFX.$HTHOST."/mvpremium/index.htm?tag=".$matches['1']."&p=".$matches['2'];
				$this->redirectUrl($url);
				break;
			/*case (preg_match('/^\/investing\/lloyds-wall-of-worry\/(.*)\//', $url,$matches) ? true : false):	
				$url = $HTPFX.$HTHOST."/investing/lloyds-wall-of-worry/index.htm?pubdate=".$matches['1'];
				$this->redirectUrl($url);
				break;	*/
			case (preg_match('/^\/audiovideo\/lightspeed\/?/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/audiovideo/');
				break;
			case (preg_match('/^\/articles\/(.*)index\/ap\/(.*)/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/articles/articlelisting.htm');
				break;
			case (preg_match('/^\/active-investor(.*)/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/subscription/');
				break;
			case (preg_match('/^\/subscription\/softtrials\/optionsmith(.*)/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/optionsmith/');
				break;
			case (preg_match('/^\/subscription\/softtrials\/cooper(.*)/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/cooper/');
				break;
			case (preg_match('/^\/subscription\/softtrials\/jacklavery(.*)/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/laveryinsight/');
				break;
			case (preg_match('/^\/beyond-the-crisis-2011(.*)/', $url) ? true : false):
			case (preg_match('/^\/getpositivemoney(.*)/', $url) ? true : false):
			case (preg_match('/^\/schoolhouse\/search.htm(.*)/', $url) ? true : false):
			case (preg_match('/^\/slideshow\/print.php?(.*)/', $url) ? true : false):	
			case (preg_match('/^\/laveryinsight\/articles\/(.*)\/index\/a\/(.*)$/', $url) ? true : false):
			case (preg_match('/^\/laveryinsight\/articles\/index\/a\/(.*)$/', $url) ? true : false):				
				$this->redirectUrl($HTPFX.$HTHOST.'/');
				break;
			case (preg_match('/^\/markets\/precious-metals\/?$/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/sectors/precious-metals/');
				break;
			case (preg_match('/^\/markets\/stocks-and-markets\/?$/', $url) ? true : false):	
			case (preg_match('/^\/investing\/stocks\/?$/', $url) ? true : false):				
				$this->redirectUrl($HTPFX.$HTHOST.'/trading-and-investing/stocks/');
				break;
			case (preg_match('/^\/markets\/emerging-markets\/?$/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/sectors/emerging-markets/');
				break;
			case (preg_match('/^\/markets\/energy\/?$/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/sectors/energy/');
				break;
			case (preg_match('/^\/markets\/macro-outlook\/?$/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/business-news/the-economy/');
				break;
			case (preg_match('/^\/markets/commodities/?$/', $url) ? true : false):						
			case (preg_match('/^\/markets\/options\/?$/', $url) ? true : false):
			case (preg_match('/^\/investing\/options\/?$/', $url) ? true : false):
				$this->redirectUrl($HTPFX.$HTHOST.'/trading-and-investing/commodities-and-options/');
				break;
			case (preg_match('/^\/investing\/retirement-planning\/?$/', $url) ? true : false):
			case (preg_match('/^\/investing\/savings\/?$/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/trading-and-investing/personal-finance/');
				break;
			case (preg_match('/^\/investing\/etfs\/?$/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/trading-and-investing/etfs/');
				break;
			case (preg_match('/^\/investing\/biotech-pharma\/?$/', $url) ? true : false):
				$this->redirectUrl($HTPFX.$HTHOST.'/sectors/etfs/');
				break;
			case (preg_match('/^\/investing\/real-estate\/?$/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/sectors/real-estate/');
				break;	
			case (preg_match('/^\/investing\/technology\/?$/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/sectors/technology/');
				break;	
			case (preg_match('/^\/investing\/credit-debit\/?$/', $url) ? true : false):	
			case (preg_match('/^\/investing\/bonds\/?$/', $url)? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/trading-and-investing/fixed-income/');
				break;	
			case (preg_match('/^\/investing\/currencies\/?$/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/trading-and-investing/currencies/');
				break;
			case (preg_match('/^\/investing\/taxes\/?$/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/trading-and-investing/taxes/');
				break;
			case (preg_match('/^\/investing\/financial-planning\/?$/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/sectors/financial/');
				break;
			case (preg_match('/^\/special-features\/automotives\/?$/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/sectors/transportation/');
				break;
			case (preg_match('/^\/tchir$/', $url) ? true : false):						
				$this->redirectUrl('http://mvp.minyanville.com/peter-tchir-landing-page-organic');
				break;
			case (preg_match('/^\/dailyfeed\/?$/', $url) ? true : false):
			case (preg_match('/^\/mvpremium\/tag\/?$/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/mvpremium/');
				break;
			case (preg_match('/^\/dailyfeed\/(.*)\/?$/', $url, $matches) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/mvpremium/'.$matches[1]);
				break;
			case (preg_match('/^\/audiovideo\/(.*)/', $url) ? true : false):
			case (preg_match('/^\/pages\/(.*)/', $url) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/video/');
				break;	
			case (preg_match('/^\/dailyfeed\/tid\/(.*)\/p\/(.*)/', $url,$matches) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/mvpremium/index.htm?tid='.$matches[1].'&p='.$matches[2]);
				break;
			case (preg_match('/^\/dailyfeed\/tag\/(.*)\/p\/(.*)/', $url,$matches) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/mvpremium/index.htm?tag='.$matches[1].'&p='.$matches[2]);
				break;
			case (preg_match('/^\/dailyfeed\/source\/(.*)\/p\/(.*)/', $url,$matches) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/mvpremium/index.htm?source='.$matches[1].'&p='.$matches[2]);
				break;
			case (preg_match('/\dailyfeed\/cid\/(.*)\/p\/(.*)/', $url,$matches) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/mvpremium/index.htm?cid='.$matches[1].'&p='.$matches[2]);
				break;
			case (preg_match('/^\/dailyfeed\/category\/(.*)\/p\/(.*)/', $url,$matches) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/mvpremium/index.htm?category='.$matches[1].'&p='.$matches[2]);
				break;
			case (preg_match('/^\/dailyfeed\/p\/(.*)/', $url,$matches) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/mvpremium/index.htm?p='.$matches[1]);
				break;
			case (preg_match('/^\/dailyfeed\/id\/(.*)/', $url,$matches) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/mvpremium/index.htm?feedid='.$matches[1]);
				break;
			case (preg_match('/^\/dailyfeed\/tag\/(.*)/', $url,$matches) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/mvpremium/index.htm?tag='.$matches[1]);
				break;
			case (preg_match('/^\/dailyfeed\/source\/(.*)/', $url,$matches) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/mvpremium/index.htm?source='.$matches[1]);
				break;
			case (preg_match('/^\/dailyfeed\/cid\/(.*)/', $url,$matches) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/mvpremium/index.htm?cid='.$matches[1]);
				break;
			case (preg_match('/^\/dailyfeed\/tid\/(.*)/', $url,$matches) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/mvpremium/index.htm?tid='.$matches[1]);
				break;
			case (preg_match('/^\/dailyfeed\/category\/(.*)/', $url,$matches) ? true : false):						
				$this->redirectUrl($HTPFX.$HTHOST.'/mvpremium/index.htm?category='.$matches[1]);
				break;
			case (preg_match('/^\/rss\/articlerss\.rss\?sec_id=([0-9]*)/', $_SERVER['REQUEST_URI'],$matches) ? true : false):
				if(preg_match('/!^MediafedMetrics/', $agent))
				{
					$this->redirectUrl($HTPFX.$HTHOST.'/rss/articlerss.rss/sec_id/'.$matches[1].'/');
				}
				break;
			case (preg_match('/^\/rss\/podCastFeed\.xml\?cat_id=([0-9]*)/', $_SERVER['REQUEST_URI'],$matches) ? true : false):
				if(preg_match('/!^MediafedMetrics/', $agent))
				{
					$this->redirectUrl($HTPFX.$HTHOST.'/rss/podCastFeed.xml/cat_id/'.$matches[1].'/');
				}
				break;
			case (preg_match('/^\/rss\/minyanfeed\.php$/', $_SERVER['REQUEST_URI'],$matches) ? true : false):
				if(preg_match('/!^MediafedMetrics/', $agent))
				{
					$this->redirectUrl('http://minyanville.com.feedsportal.com/c/35270/f/656247/index.rss');
				}
				break;
			case (preg_match('/^\/rss\//', $_SERVER['REQUEST_URI'],$matches) ? true : false):
				if(preg_match('/!^MediafedMetrics/', $agent))
				{
					$this->redirectUrl('http://minyanville.com.feedsportal.com/c/35270/f/657508/index.rss');
				}
				break;
			case (preg_match('/^\/rss/podCastFeed\.xml$/', $_SERVER['REQUEST_URI'],$matches) ? true : false):
				if(preg_match('/!^MediafedMetrics/', $agent))
				{
					$this->redirectUrl('http://minyanville.com.feedsportal.com/c/35270/f/657543/index.rss');
				}
				break;
			case (preg_match('/^\/rss/dailyfeed\.rss$/', $_SERVER['REQUEST_URI'],$matches) ? true : false):
				if(preg_match('/!^MediafedMetrics/', $agent))
				{
					$this->redirectUrl('http://minyanville.com.feedsportal.com/c/35270/f/657509/index.rss');
				}
				break;
			default:
				break;
		}	
	}
	
	public function getUrl()
	{
		$url = substr($this->pageUrl,1);
		switch($url)
		{
			case ($url=="" ? true : false):
				return 'index.htm';
				break;
			case 'feed/':
				return "/feed/getFeed.htm";
				break;
			case 'manual_sitemap.xml':
				return "manual_sitemap.xml";
				break;
			case 'sitemap/':
				return "/sitemap/gsitemap.xml";
				break;
			case (preg_match('/^[^\/\.]+$/', $url,$matches) ? true : false):		
					return $matches[0]."/index.htm";
					break;
			case (preg_match('/^[^\/]+$/', $url,$matches) ? true : false):					
					return $matches['0'];
					break;				
			case (preg_match('/^[^\.]+$/', $url,$matches) ? true : false):		
					return substr($matches['0'], -1, 1)=="/" ? $matches[0]."index.htm" : $matches[0]."/index.htm";
					break;					
			default:
				return $url;
			break;
		}
	}
}

$objRedirection = new redirectPage();
$objRedirection->checkRedirection();
$mvUrl = $objRedirection->getUrl();
$includeUrl = ( empty($D_R) ? $D_R.$mvUrl : $D_R."/".$mvUrl);

if(!include_once($includeUrl))
{
	
	location("/errors/index.htm?code=404");
	exit;
}




















?>