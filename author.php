<?php
$sql = "SELECT NAME,id FROM `contributors` C 
WHERE suspended= 0 ORDER BY NAME";
$results = exec_query($sql);
foreach($results as $key=>$val)
{
	if($val['NAME']!="")
	{
		$sqlA = "SELECT  COUNT(S.email) as cout FROM subscription S,email_alert_authorsubscribe EA WHERE S.id=EA.subscriber_id 
AND EA.email_alert='1' AND (S.trial_status<>'inactive' OR S.trial_status IS NULL) AND EA.subscriber_id
IN (SELECT subscriber_id FROM email_alert_authorsubscribe WHERE FIND_IN_SET(".$val[id].",author_id)) ";
		$res = exec_query($sqlA,1);
		$arr['author']=$val['NAME'];
		$arr['count']= $res['cout'];
		$result[] = $arr;
	}
	
}
csv_header("article_count.csv");
$datestr="%m/%d/%Y";
ksort($result);
data2csv($result); 
?>