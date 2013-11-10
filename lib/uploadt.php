<?php
echo "ddddD";

$upload_url = "https://www.googleapis.com/upload/storage/v1beta2/b/mvassets/o?uploadType=media&name=myObject";

$options = stream_context_create(array(
        "gs" => [ "Content-Type" => "image/png", "acl" => "public-read" ],
        "http" => array(
            "method" => "POST",
            "header" => "Content-Type: multipart/form-data; boundary=--foo\r\n",
            "content" => "--foo\r\n"
                . "Content-Disposition: form-data; name=\"myFile\"; filename=\"buzz_safari.png\"\r\n"
                . "Content-Type: image/png\r\n\r\n"
                . file_get_contents("C:/Users/user/Desktop/buzz_safari.png") . "\r\n"
                . "--foo--"
        )
    ));
                
                $ctx = stream_context_create($params);
   $fp = fopen($upload_url, false, $ctx);
   $response = stream_get_contents($fp);
htmlprint_r($response);
echo "----------------";





?>