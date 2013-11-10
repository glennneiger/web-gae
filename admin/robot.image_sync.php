<?php
global $D_R,$serverRsync,$serverS8PublicDns,$serverS9PublicDns;

$qry = "SELECT * FROM image_upload_log WHERE sent='0' limit 5";
$resList = exec_query($qry);

if(!empty($resList))
{
	foreach($resList as $key=>$val)
	{
		if($val["server"]=="ec2-54-225-111-153.compute-1.amazonaws.com")
		{
			$serverKey="admin01a";
		}
		else if($val["server"]=="ec2-54-225-111-137.compute-1.amazonaws.com")
		{
			$serverKey="admin01d";
		}

		if(!empty($serverKey))
		{
			$command='rsync -avz --timeout=30 -e "ssh -p 16098 -i /home/sites/minyanville/.'.$serverKey.'" '.$D_R.'/assets/'.$val["imagesource"].' minyanville@'.$val["server"].':'.$D_R.'/assets/'.$val["imagedestination"];

			$stream=shell_exec($command);
			if(!empty($stream))
			{
				 $id=$val['id'];
				 $data['sent']="1";
				 update_query("image_upload_log",$data,array(id=>$id));
			}
		}
	}
}

?>