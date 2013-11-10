<?
include_once("$D_R/lib/_db.php");
/* functions for inbox page */
function message_conversation($id){
$msgqry="select message_id from ex_message_conversation where id='$id'";

    $getallconv=exec_query($msgqry,1);
  	if(isset($getallconv)){
		return $getallconv;
		} else {
		return;
	}		
}

function inbox_message_count($userid,$read_status) {
  $conversation=inbox_conversation($userid,$read_status);
  if(is_array($conversation)){
  foreach($conversation as $id=>$value)
	{ 
		$sqlConversationDetails="select EPM.title,EPM.from_subscription_id,EPM.to_subscription_id,EPM.msg_date,EPMT.private_msg_id,EPMT.text,concat(SUB.fname,' ',SUB.lname) name from ex_private_message EPM ,ex_private_message_text EPMT,subscription SUB where EPM.id in ($value[message_id]) and EPM.id=EPMT.private_msg_id and EPM.from_subscription_id=SUB.id and EPM.from_subscription_id<>'$userid' order by EPM.msg_date desc";	
	$resConversationDetails=exec_query($sqlConversationDetails,1);

		if($resConversationDetails){
			$display_conversation[$value['id']]['from']=$resConversationDetails['from_subscription_id'];
		}
	}
    if(isset($display_conversation)){
		return $display_conversation;
	} else {
		return;
	}
 }	
}


 function inbox_delte_messsage($id,$conv_id){
	$conv_id=explode(',',$conv_id);
	$total_msg=count($conv_id);
	for($k=0;$k<$total_msg;$k++){
           if($conv_id[$k])
		{
		$strconv2[conversee2]="NULL";
		update_query('ex_message_conversation',$strconv2,array('id'=>$conv_id[$k],'conversee2'=>$id));
		$strconv1[conversee1]="NULL";
		update_query('ex_message_conversation',$strconv1,array('id'=>$conv_id[$k],'conversee1'=>$id));
	    }
	}
 }

?>