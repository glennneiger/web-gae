<?
class admin{
	function admin($user,$pass){
		if(empty($_SESSION['AMADMIN'])){
		$this->user=trim($user);
		$this->pass=trim($pass);
		$qry = "SELECT *,id user_id from admin_users WHERE
				username='$this->user' AND password=md5('$this->pass') AND suspend='0'";
		$this->userInfo=exec_query($qry,1);
		$this->isAuthed=count($this->userInfo)?1:0;
		if($this->isAuthed){
			$this->userInfo[f_id_list]=explode(",",$this->userInfo[feature_ids]);
			$qry="select script s from admin_features
					WHERE find_in_set(id,'".$this->userInfo[feature_ids]."')";
			$this->userInfo[f_script_list]=exec_query($qry,0,"s");
			$qry="SELECT id FROM contributors WHERE user_id=".$this->userInfo[user_id];
			$this->userInfo[contrib_id]=exec_query($qry,1,"id");
			$qry="SELECT intname i FROM admin_features
				  WHERE find_in_set(id,'".$this->userInfo[feature_ids]."')";
			$this->userInfo[f_name_list]=exec_query($qry,0,"i");
				set_sess_vars("AUSER",$this->user);
				set_sess_vars("APASS",$this->pass);
				set_sess_vars("AMADMIN",1);
				set_sess_vars("AID",$this->userInfo['user_id']);
				set_sess_vars("userInfo",$this->userInfo);				
			}
		}else{
			$this->userInfo=$_SESSION['userInfo'];
			$this->isAuthed=$_SESSION['AMADMIN'];
		}
	}
	function getPerm($permname){
		if(!$permname)return 0;
		if(!$this->isAuthed)return 0;
		return min_array($permname,$this->userInfo[f_name_list]);

	}
}

function admin_logout(){
	mcookie("auser");
	mcookie("apass");
	mcookie("AUSER");
	mcookie("APASS");
	set_sess_vars("AUSER");
	set_sess_vars("APASS");
	set_sess_vars("AMADMIN");
	set_sess_vars("AID");
}


class corpreg{
	function corpreg($user,$pass){
		global $_COOKIE;
		$this->user=trim($user);
		$this->pass=trim($pass);
		if( (!$this->user || !$this->pass) && ($_COOKIE[corplogin] && $_COOKIE[corppass]) ){
			$this->user=$_COOKIE[corplogin];
			$this->pass=$_COOKIE[corppass];
			set_sess_vars("CORPLOGIN",$this->user);
			set_sess_vars("CORPPASS",$this->pass);
		}
		$qry = "SELECT *,id corp_id from corp
			WHERE corp_login='$user' AND corp_password='$pass'";
		$qry2 = "SELECT count(*) numusers,corpuser from subscription WHERE corpuser='$user' GROUP BY corpuser";
		$this->corpInfo=exec_query($qry,1);
		$this->curUsers=exec_query($qry2,1);
		$this->numUsers = count($this->curUsers)?$this->curUsers['numusers']:0;

		$this->isAuthed=count($this->corpInfo)?1:0;

		if($this->corpInfo['max_users'] > $this->numUsers ) {
			if($this->isAuthed){
				set_sess_vars("CORPLOGIN",$this->user);
				set_sess_vars("CORPPASS",$this->pass);
				mcookie("corplogin",$this->user);
				mcookie("corppass",$this->pass);
			}
		}
		else {
		$error="Maximun user registration exceeded: Only <?echo $this->corpInfo['max_users'] ?> accounts are allowed to register under ".$this->corpInfo['corp_login'].". Please contact the account manager for assistence.\n";
		 }
	}
	function isValidCorpSession(){
		global $_SESSION;
		$qry="SELECT 'found' f
			  FROM corp
			  WHERE corp_login='${_SESSION[CORPLOGIN]}'
			  AND corp_password='${_SESSION[CORPPASS]}'";
		return ('found'==exec_query($qry,"1","f"));
	}
}

function get_page_perms($phpself){
	$script=lc(basename($phpself));
	return exec_query("SELECT id FROM admin_features WHERE lower(script)='$script'",0,"id");

}


function auth_from_cookie(){
	global $_SESSION,$_COOKIE;
	$s=&$_SESSION;$c=&$_COOKIE;
	if(!$s[EMAIL] && !$s[PASSWORD] && ($c[email] && $c[password] && $c[autologin])){
		$s[EMAIL]=$c[email];
		$s[PASSWORD]=$c[password];
		$s[AID]=$c[AID];
	}
}
?>
