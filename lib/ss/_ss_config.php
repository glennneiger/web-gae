<?php
global $lang, $EX_INVALIDLOGIN, $HTPFX, $HTHOST, $page_config,$D_R ;
$STOCKACTBJSSCRIPT=$CDN_SERVER."/js/stock_actb.js";
$ACTB_COMMONJSSCRIPT=$CDN_SERVER."/js/actb_common.js";
$STOCKSUGGESTIONJSSCRIPT=$CDN_SERVER."/js/stock_suggestion.js";
$transactioncnt=5;
$videoOffset = 5;
$page_config['sshome']['URL']=$HTPFX.$HTHOST."/optionsmith/home.htm";
$page_config['search_alerts']['URL']=$HTPFX.$HTHOST."/optionsmith/search_alerts.htm";
$page_config['search_av']['URL']=$HTPFX.$HTHOST."/optionsmith/search_av.htm";
$page_config['closepositions']['URL']=$HTPFX.$HTHOST."/optionsmith/closepositions.htm";
$page_config['openpositions']['URL']=$HTPFX.$HTHOST."/optionsmith/openpositions.htm";
$sstransactioncnt=5;
?>
