<?
include_once("../lib/_db.php");
function compose_message() {
    global $REMOTE_ADDR;
 	$ex_priv_msg[title]=$Subject;
	$ex_priv_msg[from_subscription_id]=$id;
	$ex_priv_msg[to_subscription_id]=$id;
	$ex_priv_msg[msg_date]=date('Y-m-d, H:i:s');
	$ex_priv_msg[ip]=$REMOTE_ADDR;

 	$id=insert_query("ex_private_message",$ex_priv_msg);

 }
 function subscriptionid($to) {
  $qry="select id, concat(fname,' ',lname) name from subscription where email='$to'";
  $idqry=exec_query($qry);
  if(isset($idqry)){
		return $idqry;
	} else {
		return;
	}
 
 }
 function sub_email($to) {
 $idqry=exec_query("select id,concat(fname,' ',lname) name,email from subscription where id=$to",1);
  if(isset($idqry)){
		return $idqry;
	} else {
		return;
	}
 }
 
 function compose_friends_list($id){
		
		$objfriendlist=new friends(); 
		$friends=$objfriendlist->get_friend_list($id,'Alphabetically');
		foreach($friends as $postkey=> $postval)
		{
			
			if($strcomp==""){
				$strcomp.=ucwords(strtolower($postval[name]));
				$strcompname.='"'.ucwords(strtolower($postval[name])).'"'.'<'.$postval[email].'>';
				 
			}
			else{
				$strcomp.= "','".ucwords(strtolower($postval[name]));
				$strcompname.= "','".'"'.ucwords(strtolower($postval[name])).'"'.'<'.$postval[email].'>';
			}
		}
		?>
	    <script language="javascript">
			var customarrayname=new Array('<?=$strcompname?>');
			var customarray=new Array('<?=$strcomp?>');
		</script>		
	
<?
}
?>