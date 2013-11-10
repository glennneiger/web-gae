<?php

class googleAdWords{

	function googleAdConversionTracking(){
	if($_SESSION['showAdWords']=1){
	?>
			<!-- Email Transmit Conversion Code -->
		<script language="JavaScript" type="text/javascript"><!--
		var emt_conv_id = "55da743d939b02f65ec29bb8475a8a1c";
		var emt_conv_type = "purchase";
		//--></script><script language="JavaScript"
		src="https://app.emailtransmit.com/util/conversion.js"></script>
		<noscript><img
		src="https://app.emailtransmit.com/util/conversion.php?emt_conv_id=55da743d939b02f65ec29bb8475a8a1c&emt_conv_type=purchase"
		width="1" height="1" border="0" alt=""></noscript>
		<!-- End of Email Transmit Conversion Code -->
		
		<!-- Google Code for Subscription Purchase Conversion Page -->
		<script type="text/javascript">
		/* <![CDATA[ */
		var google_conversion_id = 1039530399;
		var google_conversion_language = "en";
		var google_conversion_format = "1";
		var google_conversion_color = "ffffff";
		var google_conversion_label = "t-zyCNWghAIQn_PX7wM";
		var google_conversion_value = 0;
		/* ]]> */
		</script>
		<script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js">
		</script>
		<noscript>
		<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt="" src="https://www.googleadservices.com/pagead/conversion/1039530399/?label=t-zyCNWghAIQn_PX7wM&amp;guid=ON&amp;script=0"/>
		</div>
		</noscript>
		<!-- End of Google Code for Subscription Purchase Conversion Page -->
		<?
		unset($_SESSION['showAdWords']);
	 }
	}

}

?>