<?php

class lead {
	public function setLead($lead){
	    global $D_R;
	    include_once($D_R."/lib/email_alert/_lib.php");
		$id=insert_query("sub_leads",$lead);
		if($id){
			send_schwab_buzz_welcome_email($id);
			return $id;
		}else{
			return FALSE;
		}
	}

	public function getLeads($id=NULL,$limit=1000){
		$sqlGetLeads="SELECT * from sub_leads ";
		if($id){
			$sqlGetLeads.=" WHERE id='".$id."'";
		}
		$sqlGetLeads.=" limit 0,".$limit;
		$resGetLeads=exec_query($sqlGetLeads);
		return $resGetLeads;
	}
}

?>