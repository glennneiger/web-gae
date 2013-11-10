<?php

class googleWebOptimizer{

	function adsFreeWebOptimizer(){
	?>
		<!-----------------------   For Ads Free Landing Page   ------------------------------->
		<!-- Google Website Optimizer Conversion Script -->
		<script type="text/javascript">
		if(typeof(_gat)!='object')document.write('<sc'+'ript src="http'+
		(document.location.protocol=='https:'?'s://ssl':'://www')+
		'.google-analytics.com/ga.js"></sc'+'ript>')</script>
		<script type="text/javascript">
		try {
		var gwoTracker=_gat._getTracker("UA-30222-16");
		gwoTracker._trackPageview("/1957662600/goal");
		}catch(err){}</script>
		<!-- End of Google Website Optimizer Conversion Script -->
	
	<?
	}
	
	function optionSmithWebOptimizer(){
	?>
		<!-----------------------   For OptionSmith Landing Page   ------------------------------->
		<!-- Google Website Optimizer Conversion Script -->
		<script type="text/javascript">
		if(typeof(_gat)!='object')document.write('<sc'+'ript src="http'+
		(document.location.protocol=='https:'?'s://ssl':'://www')+
		'.google-analytics.com/ga.js"></sc'+'ript>')</script>
		<script type="text/javascript">
		try {
		var gwoTracker=_gat._getTracker("UA-30222-16");
		gwoTracker._trackPageview("/3760028665/goal");
		}catch(err){}</script>
		<!-- End of Google Website Optimizer Conversion Script -->
	<?
	}
	
	function housingMarketReportWebOptimizer(){
	?>
		<!-----------------------   For Housing Market Report Landing Page   ------------------------------->
		<!-- Google Website Optimizer Tracking Script -->
		<script type="text/javascript">
		  var _gaq = _gaq || [];
		  _gaq.push(['gwo._setAccount', 'UA-30222-16']);
		  _gaq.push(['gwo._trackPageview', '/2093952710/goal']);
		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		</script>
		<!-- End of Google Website Optimizer Tracking Script -->
	
	<?
	}
	function buzzbanterWebOptimizer(){ // Goal tracking Page
	?>
		<!-----------------------   For Buzz and Banter Landing Page   ------------------------------->
		<!-- Google Website Optimizer Tracking Script -->
		<script type="text/javascript">
          var _gaq = _gaq || [];
          _gaq.push(['gwo._setAccount', 'UA-30222-16']);
          _gaq.push(['gwo._trackPageview', '/0782899463/goal']);
          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();
        </script>
        <!-- End of Google Website Optimizer Tracking Script -->
	<?
	}
	
	function webOptimizerControllerCode()
	{
			switch($this->page_name)
			{
				case 'adFree':
				if($this->version == 1)
				{
				?>
                	<script>
function utmx_section(){}function utmx(){}
(function(){var k='1957662600',d=document,l=d.location,c=d.cookie;function f(n){
if(c){var i=c.indexOf(n+'=');if(i>-1){var j=c.indexOf(';',i);return escape(c.substring(i+n.length+1,j<0?c.length:j))}}}var x=f('__utmx'),xx=f('__utmxx'),h=l.hash;d.write('<sc'+'ript src="'+'http'+(l.protocol=='https:'?'s://ssl':'://www')+'.google-analytics.com'+'/siteopt.js?v=1&utmxkey='+k+'&utmx='+(x?x:'')+'&utmxx='+(xx?xx:'')+'&utmxtime='+new Date().valueOf()+(h?'&utmxhash='+escape(h.substr(1)):'')+'" type="text/javascript" charset="utf-8"></sc'+'ript>')})();</script><script>utmx("url",'A/B');</script>                
                <?
				}
				break;
				case 'buzzbanter':
				if($this->version == 1)
				{
				?>
                <!-- Google Website Optimizer Control Script -->
				<script>
                function utmx_section(){}function utmx(){}
                (function(){var k='0782899463',d=document,l=d.location,c=d.cookie;function f(n){
                if(c){var i=c.indexOf(n+'=');if(i>-1){var j=c.indexOf(';',i);return escape(c.substring(i+n.
                length+1,j<0?c.length:j))}}}var x=f('__utmx'),xx=f('__utmxx'),h=l.hash;
                d.write('<sc'+'ript src="'+
                'http'+(l.protocol=='https:'?'s://ssl':'://www')+'.google-analytics.com'
                +'/siteopt.js?v=1&utmxkey='+k+'&utmx='+(x?x:'')+'&utmxx='+(xx?xx:'')+'&utmxtime='
                +new Date().valueOf()+(h?'&utmxhash='+escape(h.substr(1)):'')+
                '" type="text/javascript" charset="utf-8"></sc'+'ript>')})();
                </script><script>utmx("url",'A/B');</script>
                <!-- End of Google Website Optimizer Control Script -->
                <?
				}				
				?>
                <!-- Google Website Optimizer Tracking Script -->
				<script type="text/javascript">
                  var _gaq = _gaq || [];
                  _gaq.push(['gwo._setAccount', 'UA-30222-16']);
                  _gaq.push(['gwo._trackPageview', '/0782899463/test']);
                  (function() {
                    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                  })();
                </script>
                <!-- End of Google Website Optimizer Tracking Script -->
                <?
				break;				
			}	
		}
	}
?>