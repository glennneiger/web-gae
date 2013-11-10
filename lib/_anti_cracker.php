<?php
/*
    Protects better diverse attempts of Cross-Site Scripting attacks
*/
function mvilSecureInput()
{

    global $HTNOSSLDOMAIN;
	
	// Cross-Site Scripting attack defense
    // some syntax checking against injected javascript

	if((preg_match("3Cscript",$_SERVER[REQUEST_URI])) || 
		(preg_match("3Cobject",$_SERVER[REQUEST_URI])) ||
		(preg_match("3Ciframe",$_SERVER[REQUEST_URI])) ||
        (preg_match("3Capplet",$_SERVER[REQUEST_URI])) ||
        (preg_match("3Cmeta",$_SERVER[REQUEST_URI])) ||
        (preg_match("3Cstyle",$_SERVER[REQUEST_URI])) ||
        (preg_match("3Cform",$_SERVER[REQUEST_URI])) ||
        (preg_match("3Cwindow",$_SERVER[REQUEST_URI])) ||
        (preg_match("3Calert",$_SERVER[REQUEST_URI])) ||
        (preg_match("3Cimg",$_SERVER[REQUEST_URI])) ||
        (preg_match("3Cdocument",$_SERVER[REQUEST_URI])) ||
        (preg_match("3Ccookie",$_SERVER[REQUEST_URI])) ||
		(preg_match("<[^>]*script.*\"?[^>]*>",$_SERVER[REQUEST_URI])) ||
        (preg_match(".*[[:space:]](or|and)[[:space:]].*(=|like).*",$_SERVER[REQUEST_URI])) ||
        (preg_match("<[^>]*object.*\"?[^>]*>",$_SERVER[REQUEST_URI])) ||
        (preg_match("<[^>]*iframe.*\"?[^>]*>",$_SERVER[REQUEST_URI])) ||
        (preg_match("<[^>]*applet.*\"?[^>]*>",$_SERVER[REQUEST_URI])) ||
        (preg_match("<[^>]*meta.*\"?[^>]*>",$_SERVER[REQUEST_URI])) ||
        (preg_match("<[^>]*style.*\"?[^>]*>",$_SERVER[REQUEST_URI])) ||
        (preg_match("<[^>]*form.*\"?[^>]*>",$_SERVER[REQUEST_URI])) ||
        (preg_match("<[^>]*window.*\"?[^>]*>",$_SERVER[REQUEST_URI])) ||
        (preg_match("<[^>]*alert.*\"?[^>]*>",$_SERVER[REQUEST_URI])) ||
        (preg_match("<[^>]*img.*\"?[^>]*>",$_SERVER[REQUEST_URI])) ||
        (preg_match("<[^>]*document.*\"?[^>]*>",$_SERVER[REQUEST_URI])) ||
        (preg_match("<[^>]*cookie.*\"?[^>]*>",$_SERVER[REQUEST_URI])))
	
	{
		mvilMailHackAttempt('mvilAntiCracker',__LINE__,'MVIL Security Alert: GET Intrusion detection.');
		location($HTNOSSLDOMAIN."/errors/?code=404");
		exit;
	}

	if (count($_GET) > 0) {
        //        Lets now sanitize the GET vars
        foreach ($_GET as $secvalue) {
            if (!is_array($secvalue)) {
                if ((preg_match("<[^>]*script.*\"?[^>]*>", $secvalue)) ||
                        (preg_match(".*[[:space:]](or|and)[[:space:]].*(=|like).*", $secvalue)) ||
                        (preg_match("<[^>]*object.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*iframe.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*applet.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*meta.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*style.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*form.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*window.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*alert.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*img.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*document.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*cookie.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("\"", $secvalue))) {
                        mvilMailHackAttempt('mvilAntiCracker',__LINE__,'MVIL Security Alert: GET Intrusion detection.');
                        Header("Location: ".$HTNOSSLDOMAIN."/errors/?code=404");
						exit;
                }
            }
        } /*for end*/
		
    }

    //        Lets now sanitize the POST vars
    if ( count($_POST) > 0) {
        foreach ($_POST as $secvalue) {
            if (!is_array($secvalue)) {
                if ((preg_match("<[^>]*script.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*object.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*iframe.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*applet.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*window.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*alert.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*document.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*cookie.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*meta.*\"?[^>]*>", $secvalue))
                        ) {

                        mvilMailHackAttempt('mvilAntiCracker',__LINE__,'MVIL Security Alert: POST Intrusion detection.');
                        Header("Location: ".$HTNOSSLDOMAIN."/errors/?code=404");
						exit;
                }
             }
        }
    }

    //        Lets now sanitize the COOKIE vars
    if ( count($_COOKIE) > 0) {
	        foreach ($_COOKIE as $secvalue) {
            if (!is_array($secvalue)) {
				if ((preg_match("<[^>]*script.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*object.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*iframe.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*applet.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*meta.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*style.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*form.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*window.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*alert.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*document.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*cookie.*\"?[^>]*>", $secvalue)) ||
                        (preg_match("<[^>]*img.*\"?[^>]*>", $secvalue))
                        ) {
                        mvilMailHackAttempt('mvilAntiCracker',__LINE__,'MVIL Security Alert: COOKIE Intrusion detection.');
                        Header("Location: ".$HTNOSSLDOMAIN."/errors/?code=404");
						exit;
                }
            }
        }
    }
}

function mvilMailHackAttempt( $detecting_file        =        "(no filename available)",
                            $detecting_line        =        "(no line number available)",
                            $hack_type             =        "(no type given)",
                            $message               =        "(no message given)" ) {
		global $HTNOSSLDOMAIN;
        $output         =        "Hello Admin,<br />";
        $output        .=        "<p> At ".date('r');
        $output        .=        " the Minyanville.com code has detected that somebody tried to"
                                ." send information to your site that may have been intended"
                                ." as a hack. Do not panic, it may be harmless: maybe this"
                                ." detection was triggered by something you did! Anyway, it"
                                ." was detected and blocked. </p>";
        $output        .=        "<p>The suspicious activity was recognized in $detecting_file "
                                ."on line $detecting_line, and is of the type <b>$hack_type</b>. </p>";
        $output        .=        "<p>Below you will find a lot of information obtained about "
                                ."this attempt, that may help you to find  what happened and "
                                ."maybe who did it.</p>";
		$output		   .=		"The requested URL is: ".$_SERVER["REQUEST_URI"]."<br /><br />";
        $output        .=        "=====================================<br />";
        $output        .=        "Information about this user:<br />";
        $output        .=        "=====================================<br />";
		$output        .=        "<p>IP numbers: [note: when you are dealing with a real cracker "
                                ."these IP numbers might not be from the actual computer he is "
                                ."working on]<p>"
                                ."<ul><li>IP according to HTTP_CLIENT_IP: ".mvilServerGetVar( 'HTTP_CLIENT_IP' )."</li>"
                                ."<li> IP according to REMOTE_ADDR: ".mvilServerGetVar( 'REMOTE_ADDR' )."</li>"
                                ."<li> IP according to GetHostByName(\$_SERVER['REMOTE_ADDR']): ".$_SERVER['REMOTE_ADDR']."</li>"
                                ."</ul><br />";

        $output        .=        "<br />=====================================<br />";
        $output        .=        "Browser information<br />";
        $output        .=        "=====================================<br />";

        $output        .=        "<p>HTTP_USER_AGENT: ".$_SERVER['HTTP_USER_AGENT'] ."</p>";

        $browser = (array) get_browser();
        while ( list ( $key, $value ) = each ( $browser ) ) {
                $output .= "$key : $value <br />";
        }

        $output        .=        "<br />=====================================<br />";
        $output        .=        "Information in the \$_GET array\n <br />";
        $output        .=        "This is about variables that may have been ";
        $output        .=        "in the URL string or in a 'GET' type form.<br />";
        $output        .=        "=====================================<br /><br />";

        while ( list ( $key, $value ) = each ( $_GET ) ) {
                $output .= " $key : ".htmlentities($value)."<br />";
        }

        $output        .=        "=====================================<br />";
        $output        .=        "Information in the \$_POST array\n <br />";
        $output        .=        "This is about visible and invisible form elements.<br />";
        $output        .=        "=====================================<br />";

        while ( list ( $key, $value ) = each ( $_POST ) ) {
                $output .= " $key : ".htmlentities($value)."<br />";
        }

        $output        .=        "\n=====================================<br />";
        $output        .=        "Information in the \$_COOKIE array\n<br />";
        $output        .=        "=====================================<br />";

        while ( list ( $key, $value ) = each ( $_COOKIE ) )  {
                $output .=  htmlentities($key) . " : ".htmlentities($value)."<br />";
        }

        $output        .=        "\n=====================================<br />";
        $output        .=        "Information in the \$_FILES array<br />";
        $output        .=        "=====================================<br />";

        while ( list ( $key, $value ) = each ( $_FILES ) ) {
                $output .= " $key : ".htmlentities($value)."<br />";
        }
       //	mymail ("kamal.puri@ebusinessware.com","Minyanville.com",  'Attempted hack on your site '.$HTNOSSLDOMAIN.' (type: '.$hack_type.')', $output);
        return;
}

/**
* Gets a server variable
*
* Returns the value of $name from $_SERVER array.
* Accepted values for $name are exactly the ones described by the
* {@link http://www.php.net/manual/en/reserved.variables.html#reserved.variables.server PHP manual}.
* If the server variable doesn't exist void is returned.

* @access public
* @param name string the name of the variable
* @return mixed value of the variable
*/
function mvilServerGetVar()
{
   // Check the relevant superglobals
    if (!empty($name) && isset($_SERVER[$name])) {
        return $_SERVER[$name];
    }
    return null; // we found nothing here

}
?>
