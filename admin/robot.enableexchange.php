<?
set_time_limit(60*30 );//1 hour
$sqlGetsubscribers="select id from subscription S where id Not In (select subscription_id from ex_user_profile) limit 0,1";
$resGetsubscribers=exec_query($sqlGetsubscribers);
foreach($resGetsubscribers as $sub)
{
	$subscriber['is_exchange']='1';
	update_query('subscription',$subscriber,array('id'=>$sub['id']));
	$ex_user_profile['subscription_id']=$sub['id'];
	insert_query('ex_user_profile',$ex_user_profile);
	$email_alert_categorysubscribe['subscriber_id']=$sub['id'];
	$email_alert_authorsubscribe['email_alert']='0';
	insert_query('email_alert_authorsubscribe',$email_alert_authorsubscribe);
	$email_alert_categorysubscribe['subscriber_id']=$sub['id'];
	$email_alert_categorysubscribe['email_alert']='0';
	insert_query('email_alert_categorysubscribe',$email_alert_categorysubscribe);
	$ex_user_email_settings['subscription_id']=$sub['id'];
	$ex_user_email_settings['alert']="off";
	$ex_user_email_settings['email_id']=1;
	insert_query('ex_user_email_settings',$ex_user_email_settings);
	$ex_user_email_settings['email_id']=2;
	insert_query('ex_user_email_settings',$ex_user_email_settings);
	$ex_user_email_settings['email_id']=3;
	insert_query('ex_user_email_settings',$ex_user_email_settings);
	$ex_user_email_settings['email_id']=4;
	insert_query('ex_user_email_settings',$ex_user_email_settings);
	$ex_user_email_settings['email_id']=5;
	insert_query('ex_user_email_settings',$ex_user_email_settings);
	$ex_user_email_settings['email_id']=6;
	insert_query('ex_user_email_settings',$ex_user_email_settings);
	$ex_profile_privacy['subscription_id']=$sub['id'];
	$ex_profile_privacy['enabled']="All";
	$ex_profile_privacy['privacy_attribute_id']="1";
	insert_query('ex_profile_privacy',$ex_profile_privacy);
	$ex_profile_privacy['privacy_attribute_id']="2";
	insert_query('ex_profile_privacy',$ex_profile_privacy);
	$ex_profile_privacy['privacy_attribute_id']="3";
	insert_query('ex_profile_privacy',$ex_profile_privacy);
	$ex_profile_privacy['privacy_attribute_id']="4";
	insert_query('ex_profile_privacy',$ex_profile_privacy);
	$ex_profile_privacy['privacy_attribute_id']="5";
	insert_query('ex_profile_privacy',$ex_profile_privacy);
	$ex_profile_privacy['privacy_attribute_id']="7";
	insert_query('ex_profile_privacy',$ex_profile_privacy);
}
?>