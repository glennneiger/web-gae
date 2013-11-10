<?php

/*twitter site configuration for host */
global $HTPFX,$HTHOST;

define("CONSUMER_KEY", "4KdlS9lqeaeSAOmFU65eQ");
define("CONSUMER_SECRET", "WdXSyUeDyWhFMBs1AwEiBUJjWg6Yf0K17UvxyIxfhE");
//define("OAUTH_CALLBACK",$HTPFX.$HTHOST);
define("OAUTH_CALLBACK",$HTPFX.$HTHOST."/lib/twitter/callback.php");