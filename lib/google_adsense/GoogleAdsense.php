<?php
class GoogleAdsense
{
	public $client_id = "pub-2096421600361956";
	
	function __construct()
	{
	}
	function displayAdsense($width,$height)
	{		
?>
		<script type="text/javascript"><!--
		google_ad_client = "<?=$this->client_id?>";
		/* 468x15, 5 text links,created 4/27/10 */
		google_ad_slot = "4882722988";
		google_ad_width = <?=$width?>;
		google_ad_height = <?=$height?>;
		//-->
		</script>
		<script type="text/javascript"
		src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>
<?
	}
}
?> 