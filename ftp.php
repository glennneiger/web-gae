<?php
global $D_R,$VIDEO_SERVER;
$port=21;
$host="ftp.minyanville.com";
$user="radio";
$pass="8cYhC1OxiMm1Xpr";
echo "ssss";
$ftp=ftp_connect($host,$port);
if(! ($ftp=ftp_connect($host, $port)) ){
	echo "SdsadsaD";
	debug("ftpPut:couldn't connect to $host:$port");
	$ftpError="couldn't connect to ".$host.":".$port;
	return 0;
}
if((!$login=ftp_login($ftp, $user, $pass))){
	echo "dsafaf";
	debug("ftpPut: couldn't log in with $user:$pass");
	$ftpError="couldn't log in with ".$user.":".$pass;
	ftp_close($ftp);
	return 0;
}
echo "xnjnjwd";
echo $ftpError;
ftp_pasv($ftp, true);
htmlprint_r($ftp);
$files_on_server = ftp_nlist($ftp,".");
htmlprint_r($files_on_server);
$files = scandir($D_R.'/assets/radio/');
?>