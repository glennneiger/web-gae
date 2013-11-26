<?php
global $lang, $EX_INVALIDLOGIN, $HTPFX, $HTHOST, $page_config,$D_R,$CDN_SERVER ;
$STOCKACTBJSSCRIPT=$CDN_SERVER."/js/stock_actb.js";
$ACTB_COMMONJSSCRIPT=$CDN_SERVER."/js/actb_common.js";
$STOCKSUGGESTIONJSSCRIPT=$CDN_SERVER."/js/stock_suggestion.js";
$transactioncnt=10;
$videoOffset = 5;
$page_config['qphome']['URL']=$HTPFX.$HTHOST."/qp/index.htm";
$page_config['search_alerts']['URL']=$HTPFX.$HTHOST."/qp/search_alerts.htm";
$page_config['search_av']['URL']=$HTPFX.$HTHOST."/qp/search_av.htm";
$page_config['closepositions']['URL']=$HTPFX.$HTHOST."/qp/closepositions.htm";
$page_config['openpositions']['URL']=$HTPFX.$HTHOST."/qp/openpositions.htm";
?>
