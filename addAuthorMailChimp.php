<?php
global $D_R;
include_once($D_R."/lib/config/_mailchimp_config.php");
include_once($D_R."/lib/mailchipapi/MCAPI.class.php");
include_once($D_R."/lib/mailchipapi/mailchimp_data_lib.php");
require_once($D_R.'/lib/mailchipapi/store-address.php');
$objMailChimp= new mailChimp();

$objApi = new MCAPI($mailChimpApiKey);

$sql="SELECT section_id,`name` FROM section WHERE is_active='1' AND TYPE='subsection' AND subsection_type='article' ";
$result = exec_query($sql);
foreach ($result as $k=>$v)
{
	if($v['name']!="")
	{
		$resSend = $objApi->listInterestGroupAdd('38d7c14aa9',$v['name'],'1669');
		if($resSend!="1")
		{
			echo "could not add ".$v['name']."<br>";
		}
	}
}
?>