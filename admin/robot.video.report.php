<?php
global $D_R;
function mail_attachment ($from , $to, $cc="", $subject, $message, $attachment){
	global $TD;
	$fileatt = $attachment; // Path to the file
	$fileatt_type = "application/octet-stream"; // File Type
    $start=	strrpos($attachment, '/') == -1 ? strrpos($attachment, '//') : strrpos($attachment, '/')+1;
	$fileatt_name = substr($attachment, $start, strlen($attachment)); // Filename that will be used for the file as the 	attachment

	$email_from = $from; // Who the email is from
	$email_subject =  $subject; // The Subject of the email
	$email_txt = $message; // Message that the email has in it

	$email_to = $to; // Who the email is to

	$headers = "From: ".$email_from. "\r\n";;
	$headers .= 'Cc: '. $cc;
	$file = fopen($fileatt,'rb');
	$data = fread($file,filesize($fileatt));
	fclose($file);
	$msg_txt="\n\n";

	$semi_rand = md5(time());
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

	$headers .= "\nMIME-Version: 1.0\n" .
            "Content-Type: multipart/mixed;\n" .
            " boundary=\"{$mime_boundary}\"";

	$email_txt .= $msg_txt;

	$email_message .= "This is a multi-part message in MIME format.\n\n" .
                "--{$mime_boundary}\n" .
                "Content-Type:text/html; charset=\"iso-8859-1\"\n" .
               "Content-Transfer-Encoding: 7bit\n\n" .
	$email_txt . "\n\n";

	$data = chunk_split(base64_encode($data));

	$email_message .= "--{$mime_boundary}\n" .
                  "Content-Type: {$fileatt_type};\n" .
                  " name=\"{$fileatt_name}\"\n" .
                  //"Content-Disposition: attachment;\n" .
                  //" filename=\"{$fileatt_name}\"\n" .
                  "Content-Transfer-Encoding: base64\n\n" .
                 $data . "\n\n" .
                  "--{$mime_boundary}--\n";


	$ok = @mail($email_to, $email_subject, $email_message, $headers);

	if($ok) {
	} else {
		die("Sorry but the email could not be sent. Please go back and try again!");
	}
}

$result=exec_query("select mvtv.title, count(*) num from trackingPixel,mvtv  where trackingPixel.videoid=mvtv.id and time >".strtotime(date('Y-m-1 00:00:00'))." and pos='end' group by videoid order by num desc");

$tempdir=$D_R."/assets/data/";

$filename="MV_Video_Info_Weekly_".date('m-d-y').".csv";
$path=$tempdir.$filename;
$file = fopen($path, 'wb') or die('Unable to open file');
$r=array("Video Title","No of views");
for($x = 0; $x < count($r); $x++)
{
  if (@	eregi(",", $r[$x]))
  {
	 $r[$x] = '"'.$r[$x].'"';
  }
}
fwrite($file, implode(',', $r) . "\n");
foreach($result as $index=>$value)
{
	$r[0]=$value['title'];
	$r[1]=$value['num'];

	for($x = 0; $x < count($r); $x++)
	{
		if (@eregi(",", $r[$x]))
		{
			$r[$x] = '"'.$r[$x].'"';
		}
	}
	fwrite($file, implode(',', $r) . "\n");
}
fclose($file);
$result=exec_query("Select count(*) num from trackingPixel,mvtv where trackingPixel.videoid=mvtv.id and time >".strtotime(date('Y-m-1 00:00:00'))." and pos='end'",1);
$msg="Total Video views in the month of ".date("M'y")." are:".$result['num'];
$to="kevin@minyanville.com,suarez@minyanville.com,schwartz@minyanville.com";
$cc="kamal.puri@ebusinessware.com,sudeer@minyanville.com";
$from="kamal@minyanville.com";
mail_attachment($from, $to,$cc, "MV Video Monthly Stats", $msg, $path);
?>