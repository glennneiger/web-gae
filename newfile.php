<?php  global $CDN_SERVER; ?>
<script src="<?=$CDN_SERVER?>/js/jquery-1.9.1.min.js"></script>
<?php
	global $bucketName;
    function signedURL( $filename, $bucket, $method = 'PUT' ) {
    	global $bucket,$bucketName;
        $signature  = "";
        $duration   = 30;
        $emailID    = "980000000000-ytyertyr@developer.gserviceaccount.com";
        $certs      = array();
        $priv_key   = file_get_contents("9999999999999999999999999999-privatekey.p12");

      if (!openssl_pkcs12_read($priv_key, $certs, 'notasecret')) {
      	echo "Unable to parse the p12 file. OpenSSL error: " . openssl_error_string(); exit();
       }

        $expires = time() + $duration;
        $to_sign = ( $method . "\n\n\n" . $expires . "\n" . "/" . $bucket . "/" . $filename ); 

        $RSAPrivateKey = openssl_pkey_get_private($certs["pkey"]);

      if (!openssl_sign( $to_sign, $signature, $RSAPrivateKey, 'sha256' ))
      {
        error_log( 'openssl_sign failed!' );
        $signature = 'failed';
      } else {
        $signature =  urlencode( base64_encode( $signature ) );
      }

      return ( 
        'http://storage.googleapis.com/' . $bucket . '/' . $filename . '?GoogleAccessId=' . $emailID . '&Expires=' . $expires . '&Signature=' . $signature
             );
        openssl_free_key($RSAPrivateKey);
    } 
    ?>
    <script>
		var bucketName = '<?=$bucketName?>';
        var xhr        = new XMLHttpRequest();
        //PUT test - PUT status "(Canceled)" - OPTION status 200 (OK)
        xhr.open("PUT", "<?php echo signedURL('C:/keys/Doug-Casey.mp3', bucketName); ?>");
        //xhr.setRequestHeader("Content-type", "image/png");
        xhr.setRequestHeader("x-goog-acl", "public-read"); //try to set public read on file
        xhr.setRequestHeader("Content-Length", fsize); // Chrome throws error (Refused to set unsafe header "Content-Length" )
        xhr.send( fbinary );
   
    </script>

