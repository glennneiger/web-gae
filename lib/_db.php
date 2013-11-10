<?

function del_query($table,$param,$value,$optimize=0){//all strings
	global $dbservwrite,$dbuserwrite,$dbpasswrite,$dbnamewrite;
	if(!$param || !$value || !$table){
		return 0;
	}
	if($link = mysql_connect($dbservwrite,$dbuserwrite,$dbpasswrite)){
		$qry = "DELETE FROM $table WHERE $param='".$value."'";
		debug("DB:SQL:$qry");
		if($e=mysql_error()){
			debug("DB:ERROR:$e");
			notifyAdmin("deletion failure",$e);
		}
		if(!mysql_select_db($dbnamewrite,$link)){
			notifyAdmin("Couldn't select database  $dbnamewrite with $dbuserwrite",mysql_error());
		}else {
			$res = mysql_query($qry,$link);
		if(mysql_affected_rows($link)>0){
			if($optimize){
				optimize_table($table);
			}
			return 1;
		}
		}
	}else{
		notifyAdmin("connection failure","couldn't connect to $dbserv.$dbname WITH $dbuser:$dbpass ",mysql_error());
	}
	return 0;
}

function insertsubs_query($qry){//str table,hash params, str keyName,no ins if safe=1

	global $dbservwrite,$dbuserwrite,$dbpasswrite,$dbnamewrite,$HTTP_HOST;
	if($link = mysql_connect($dbservwrite,$dbuserwrite,$dbpasswrite)){
		mysql_set_charset('utf8',$link);
		if(!mysql_select_db($dbnamewrite,$link)){
			notifyAdmin("Couldn't select database  $dbnamewrite with $dbuserwrite",mysql_error());
		}else {
			$res = mysql_query($qry,$link);
		debug("DB:SQL:$qry");
		if($e=mysql_error()){
			debug("DB:ERROR:$e");
			notifyAdmin("Insertion failure","ERROR:$e <br><br>SQL:$qry");
			//print "E: $e";
		}
		if(mysql_affected_rows($link)>0){
			return mysql_insert_id();
		}
		}
	}else{
		notifyAdmin("Couldn't connect to $dbservwrite.$dbnamewrite with $dbuserwrite:$dbpasswrite",mysql_error());
	}
	return 0;
}

function insert_query($table,$params,$safe=0){//str table,hash params, str keyName,no ins if safe=1
	global $dbservwrite,$dbuserwrite,$dbpasswrite,$dbnamewrite,$HTTP_HOST;
	if(!$table || !is_array($params) ){
		return 0;
	}
	if($safe){
		if(count_rows($table,$params)){
			return 0;
		}
	}
	$names = implode(",",array_keys($params));
	if(get_magic_quotes_runtime()){
		foreach($params as $k=>$v){
			$params[$k]=addslashes(strip($v));
		}
	}
	$values= "'".implode("','",array_values($params))."'";
	$qry = "INSERT INTO $table($names) VALUES($values)";
	//print "Q: $qry<br>\n";
	if($link = mysql_connect($dbservwrite,$dbuserwrite,$dbpasswrite)){
		mysql_set_charset('utf8',$link);
		if(!mysql_select_db($dbnamewrite,$link)){
			notifyAdmin("Couldn't select database  $dbnamewrite with $dbuserwrite",mysql_error());
		}else {
			$res = mysql_query($qry,$link);
		debug("DB:SQL:$qry");
		if($e=mysql_error()){
			debug("DB:ERROR:$e");
			notifyAdmin("Insertion failure","ERROR:$e <br><br>SQL:$qry");
			//print "E: $e";
		}
		if(mysql_affected_rows($link)>0){
			$insertid =  mysql_insert_id();
			mysql_close($link);
			return $insertid;
		}
		}
	}else{
		notifyAdmin("couldn't connect to $dbservwrite.$dbnamewrite with $dbuserwrite:$dbpasswrite",mysql_error());
	}

	return 0;
}

function update_query($table,$params,$conditions,$keynames=array()){
	global $dbservwrite,$dbuserwrite,$dbpasswrite,$dbnamewrite;

	if(!$table || !is_array($params) || !is_array($conditions)){
		return 0;
	}
	$arrparams = array();
	$arrconds = array();
	if(get_magic_quotes_runtime()){
		foreach($params as $k=>$v){
			$params[$k]=addslashes(strip($v));
		}
	}
	foreach($params as $k=>$v){ $arrparams[]="$k='$v'"; }
	foreach($conditions as $k=>$v){ $arrconds[]="$k='$v'";	}
	$strparams = " SET ".implode(",",$arrparams);
	$strconds  = " WHERE ".implode(" AND ",$arrconds);
	$qry = "UPDATE $table $strparams $strconds";
	if($link = mysql_connect($dbservwrite,$dbuserwrite,$dbpasswrite)){
		mysql_set_charset('utf8',$link);
		if(!mysql_select_db($dbnamewrite,$link)){
			notifyAdmin("Couldn't select database  $dbnamewrite with $dbuserwrite",mysql_error());
		}else {
			
			$res = mysql_query($qry,$link);
		debug("DB:SQL:$qry");
		if($e=mysql_error()){
			debug("DB:ERROR:$e");
			notifyAdmin("UPDATE FAILURE","ERROR:$e<br><br>SQL:$qry");
		}
		if(mysql_affected_rows($link)>0){
			return mysql_affected_rows($link);
		}
		}
	}else{
		notifyAdmin("couldn't connect to $dbservwrite.$dbnamewrite with $dbuserwrite:$dbpasswrite",mysql_error());
	}
	return 0;
}

function insert_or_update($table,$params,$condition){
	$sqry="SELECT ".key($params)." FROM $table ".stack_query($condition);
	if( num_rows($sqry) ){
		return update_query($table,$params,$condition);
	}else{
		return insert_query($table,$params);
	}
	return 0;
}

function exec_query($query,$return_one=0,$field_name=""){
	global $dbserv,$dbuser,$dbpass,$dbname,$dbservwrite,$dbuserwrite,$dbpasswrite,$dbnamewrite;
	if(substr_count(strtolower($query),'insert ')>0 or substr_count(strtolower($query),'update ')>0 or substr_count(strtolower($query),'delete ')>0)
	{
		$activedbserv=$dbservwrite;
		$activedbuser=$dbuserwrite;
		$activedbpass=$dbpasswrite;
		$activedbname=$dbnamewrite;
	}else{
	    $activedbserv=$dbserv;
		$activedbuser=$dbuser;
		$activedbpass=$dbpass;
		$activedbname=$dbname;
	}
	if($return_one && !stristr(strtolower($query),"limit"))$query.=" LIMIT 1";
	$ret = array();
	$mc=get_magic_quotes_runtime();
	//$query=str_replace(";","",$query);
	if($link = mysql_connect($activedbserv,$activedbuser,$activedbpass)){
		mysql_set_charset('utf8',$link);
		debug("DB:SQL:$query");
		if(!mysql_select_db($activedbname,$link)){
			debug("DB:ERROR:Not able to select Database");
		}else {
			$res = mysql_query($query,$link);
			if(!$res){
				debug("DB:ERROR:Could not successfully run query".$query." from DB: ". mysql_error());
			}else if(mysql_num_rows($res)>0){
					while($row = mysql_fetch_array($res,MYSQL_ASSOC)){
						if($mc){//when magic quots is on kill escaping
							$r=$field_name?strip($row[$field_name]):strip_arr($row);
						}else{
							$r=$field_name?$row[$field_name]:$row;
						}
						$ret[]=$r;
						unset($r);
					}
					mysql_free_result($res);
					mysql_close($link);
					return ($return_one?$ret[0]:$ret);
			}else{
				debug("DB:ERROR:".mysql_error());
			}
		}
	}
	unset($activedbserv);
	unset($activedbuser);
	unset($activedbpass);
	unset($activedbname);
	if($return_one && $field_name)return "";
	return array();
}

//this is for doing complex queries that are don't require results
function exec_query_nores($query){
	global $dbserv,$dbuser,$dbpass,$dbname,$dbservwrite,$dbuserwrite,$dbpasswrite,$dbnamewrite;
	if(substr_count(strtolower($query),'insert ')>0 or substr_count(strtolower($query),'update ')>0 or substr_count(strtolower($query),'delete ')>0)
		{
		$activedbserv=$dbservwrite;
		$activedbuser=$dbuserwrite;
		$activedbpass=$dbpasswrite;
		$activedbname=$dbnamewrite;
	}else{
	    $activedbserv=$dbserv;
		$activedbuser=$dbuser;
		$activedbpass=$dbpass;
		$activedbname=$dbname;
	}
	if($link = mysql_connect($activedbserv,$activedbuser,$activedbpass)){
		debug("DB:SQL:$qry");
		if($e=mysql_error())debug("DB:ERROR:$e");
		if(!mysql_select_db($activedbname,$link)){
			notifyAdmin("Couldn't select database  $activedbname with $activedbuserwrite",mysql_error());
		}else {
			mysql_query($query,$link);
		return mysql_error($link);
	}
	}
		unset($activedbserv);
		unset($activedbuser);
		unset($activedbpass);
		unset($activedbname);
	return "";
}

function stack_query($params,$USE_OR=0,$prefix=" WHERE "){
	if(!is_array($params))return "";
	$ANDOR = $USE_OR?"OR":"AND";
	$where=array();
	foreach($params as $k=>$v){
		if(strlen($k) && strlen($v)){
			$where[]="$k='$v'";
		}
	}
	if(count($where)){
		return $prefix.implode(" $ANDOR ",$where);
	}
	return "";
}

function count_rows($table,$params=array(),$USE_OR=0){
	if(!$table)return 0;
	$where=stack_query($params,$USE_OR);
	$res = exec_query("select count(*) as c from $table $where",1,"c");
	return $res;
}
function row_exists($table,$params=array(),$USE_OR=0){
	if(!$table)return 0;
	$where=stack_query($params,$USE_OR);
	$res = exec_query("select 'found' as c from $table $where",1,"c");
	return ('found'==$res);
}
function num_rows($query){
	global $dbserv,$dbuser,$dbpass,$dbname;
	if($link = mysql_connect($dbserv,$dbuser,$dbpass)){
		if(!mysql_select_db($dbname,$link)){
			notifyAdmin("Couldn't select database  $dbname with $dbuserwrite",mysql_error());
		}else {
			if($res = mysql_query($query,$link)){
			debug("DB:SQL:$query");
			$ret=intval(mysql_num_rows($res));
			mysql_free_result($res);
			return $ret;
		}else{
			debug("DB:ERROR:".mysql_error());
		}
	}
	}
	return 0;
}

function del_from_set($table,$indexkeyname, $setkeyname,$updateval){
//in a "set":(32,44,12) column this will remove $updateval from the list
	include_once(dirname(__FILE__)."/_misc.php");
	if(!$table || !$setcol || !$indexkeyname){
		return 0;
	}
	$ret=0;
	$qry="SELECT $indexkeyname, $setkeyname
		  FROM $table WHERE find_in_set('$updateval',$setkeyname)";
	$db=new dbObj($qry);
	while($row=$db->getRow()){
		$set=trim_arr(explode(",", $row[$setkeyname]),1);
		$set=del_by_value($updateval,$set);
		$set=implode(",",$set);
		$upd=array($setkeyname=>$set);
		$cond=array($indexkeyname=>$row[$indexkeyname]);
		$ret+=update_query($table,$upd, $cond);
	}
	$db->free();
	return $ret;
}

class dbObj{
	function dbObj($query,$return_one=0,$field_name=""){
		global $dbserv,$dbuser,$dbpass,$dbname,$dbservwrite,$dbuserwrite,$dbpasswrite,$dbnamewrite;
		if(substr_count(strtolower($query),'insert ')>0 or substr_count(strtolower($query),'update ')>0 or substr_count(strtolower($query),'delete ')>0)
		{
			$activedbserv=$dbservwrite;
			$activedbuser=$dbuserwrite;
			$activedbpass=$dbpasswrite;
			$activedbname=$dbnamewrite;
		}else{
			$activedbserv=$dbserv;
			$activedbuser=$dbuser;
			$activedbpass=$dbpass;
			$activedbname=$dbname;
		}
		$this->return_one=$return_one;
		$this->field_name=$fieldname;
		$this->mq=get_magic_quotes_runtime();
		$this->i=-1;
		if($return_one && !stristr(strtolower($query),"limit"))
			$query.=" LIMIT 1";
		if($this->link= mysql_connect($activedbserv,$activedbuser,$activedbpass)){
			if(!mysql_select_db($activedbname,$this->link)){
				notifyAdmin("Couldn't select database  $activedbname with $dbuserwrite",mysql_error());
			}else {
				if($this->res = mysql_query($query,$this->link)){
				debug("DB:SQL:$query");
				if(mysql_num_rows($this->res)>0){
					return $this->res;
				}
			}else{
				debug("DB:ERROR:".mysql_error());
			}
		}
		}
		unset($activedbserv);
		unset($activedbuser);
		unset($activedbpass);
		unset($activedbname);
		return 0;
	}
	function getRow($fieldname=0){
		if($fieldname)$this->field_name=$fieldname;
		if($this->res && $this->link){
			$row=mysql_fetch_array($this->res,MYSQL_ASSOC);
			$this->i++;
			if($this->field_name){
				return $this->mq?strip($row[$this->field_name]):$row[$this->field_name];
			}else{
				return $this->mq?strip_arr($row):$row;
			}
		}
		return array();
	}
	function numRows(){
		if($this->res){
			return mysql_num_rows($this->res);
		}
		return 0;
	}
	function free(){
		if($this->res){
			return mysql_free_result($this->res);
		}
		return 0;
	}
}

function optimize_table($tablename){
	if(!$tablename)return;
	return exec_query_nores("OPTIMIZE TABLE $tablename");
}

function qry2html($qry,$headings=0,$border=1){//mainly useful for debug
	global $SORT;
	$data=exec_query($qry);
	if(!count($data)){
		echo "No records found";
		return;
	}
	?>
<table border="<?=$border?>">
  <?if($headings){?>
	  <TR>
	<?foreach(array_keys($data[0]) as $col){?>
		<TD><?=href($PHP_SELF.qsa(array(s=>$col)),$col)?></TD>
		<?}?>
	  </TR>
  <?}?>
  <?foreach($data as $row){?>
  	 <TR>
	<?foreach($row as $val){?>
		<TD><?=$val?></TD>
		<?}?>
	 </TR>
  <?}?>
</table>
	<?

}//end function

function data2html($data,$headings=0,$border=1){//mainly useful for debug
	if(!count($data)){
		return "";
	}
	?>
<table border="<?=$border?>">
  <?if($headings){?>
	  <TR>
	<?foreach(array_keys($data[0]) as $col){?>
		<TD><?=$col?></TD>
		<?}?>
	  </TR>
  <?}?>
  <?foreach($data as $row){?>
  	 <TR>
	<?foreach($row as $val){?>
		<TD><?=$val?></TD>
		<?}?>
	 </TR>
  <?}?>
</table>
	<?

}//end function

function qry2csv($qry, $outfile="", $encl='"',$sep=",",$line="\n"){
//qry=sql string
//output file=filepath to output. if none given it will echo
//encl,sep,line are csv_line() args. see func in _misc.php
	global $LIBDIR;
	include_once(dirname(__FILE__)."/_misc.php");
	$db=new dbObj($qry);
	if(!$db->numRows()){
		return "";
	}
	if($outfile)	$fp=fopen($outfile,"w+");

	while( $row=$db->getRow() ){
		if(!$firstline){
			$ln=csv_line(array_keys($row),$encl,$sep,$line);
			$outfile ? fwrite($fp,$ln) : print($ln);
			$firstline=1;
		}
		$ln=csv_line(array_values($row),$encl,$sep,$line);
		$outfile ? fwrite($fp,$ln) : print($ln);
	}
	if($fp)fclose($fp);
}
function data2csv($data, $outfile="", $encl='"',$sep=",",$line="\n"){
//qry=sql string
//output file=filepath to output. if none given it will echo
//encl,sep,line are csv_line() args. see func in _misc.php
	global $LIBDIR;
	include_once(dirname(__FILE__)."/_misc.php");
	if(!count($data)){
		return;
	}
	if($outfile)	$fp=fopen($outfile,"w+");
	foreach($data as $i=>$row){
		if(!$firstline){
			$ln=csv_line(array_keys($row),$encl,$sep,$line);
			$outfile ? fwrite($fp,$ln) : print($ln);
			$firstline=1;
		}
		$ln=csv_line(array_values($row),$encl,$sep,$line);
		$outfile ? fwrite($fp,$ln) : print($ln);
		unset($data[$i]);
	}
	if($fp)fclose($fp);
}
function qry2xml($qry, $outfile="", $rowname="row"){
//qry=sql string
//output file=filepath to output. if none given it will echo
//$rowname=enclose every database row in <$rowname>...</$rowname>
	global $LIBDIR;
	include_once(dirname(__FILE__)."/_misc.php");
	$db=new dbObj($qry);
	if(!$db->numRows()){
		return "";
	}
	if($outfile)	$fp=fopen($outfile,"w+");//empty file will not return tru on fopen
	$xml="<?xml version=\"1.0\"?>\n";
	$xml.="<xml>\n";
	$outfile ? fwrite($fp,$xml) : print($xml);
	$charmap=array("’"=>"'","…"=>"...",'“'=>'"','”'=>'"',
				  "—"=>"-","‘"=>"'","™"=>"&#153;",
				  "•"=>"&#149;",
				  );
	while($row=$db->getRow()){
		$xml="\t<$rowname>\n";
		foreach($row as $tag=>$val){
			$val=strtr($val,$charmap);
			$xml.="\t\t<$tag>".htmlentities(strip($val))."</$tag>\n";
		}
		$xml.="\t</$rowname>\n";
		$outfile ? fwrite($fp,$xml):print($xml);
		if(!$fp)flush();
	}
	$xml="</xml>\n";
	$outfile ? fwrite($fp,$xml) : print($xml);
	if($fp)fclose($fp);
}

function data2xml($data, $outfile="", $rowname="row"){

	global $LIBDIR;
	include_once(dirname(__FILE__)."/_misc.php");
	if(!is_array($data)){
		return;
	}
	if(!count($data)){
		return;
	}
	if($outfile)	$fp=fopen($outfile,"w+");
	$xml="<?xml version=\"1.0\"?>\n";
	$xml.="<xml>\n";
	$outfile ? fwrite($fp,$xml) : print($xml);
	$charmap=array("’"=>"'","…"=>"...",'“'=>'"','”'=>'"',
				  "—"=>"-","‘"=>"'","™"=>"&#153;",
				  "•"=>"&#149;",
				  );
	foreach($data as $i=>$row){
		$xml="\t<$rowname>\n";
		foreach($row as $tag=>$val){
			$val=strtr($val,$charmap);
			$xml.="\t\t<$tag>".htmlentities(strip($val))."</$tag>\n";
		}
		$xml.="\t</$rowname>\n";
		$outfile ? fwrite($fp,$xml):print($xml);
		if(!$outfile)flush();
		unset($data[$i]);
	}
	$xml="</xml>\n";
	$outfile ? fwrite($fp,$xml) : print($xml);
	if($fp)fclose($fp);
}

function qry2csv2mail($qry,$to,$from,$subject="",$body="",$outputfilename=""){
	global $TMP;
	if(!$TMP)$TMP="/tmp";
	$outputfilename=basename($outputfilename);
	if(!$outputfilename){
		$outputfilename="report-".date("m-d-Y").".csv";
	}
}

function paginateLimit($pagesize=20,$p=0){
	$offset=(intval($p)*$pagesize);
	return " LIMIT $offset,$pagesize";
}
function getNumRows($qry){
//gets number of rows of a query without the limit statement
	debug("DB:INFO:getNumRows:$qry");
	$qry=preg_replace("/limit*/i","",$qry);
	return num_rows($qry);
}

function getNumRowsNoLimit($qry){
	//gets number of rows of a query without the limit statement
	debug("DB:INFO:getNumRows:$qry");
	$exp=explode("limit",strtolower($qry));
	return num_rows($exp[0]);
}

function getPageForId($qry, $pagesize, $idVal,$idKey="id"){
//runs query looking for $idKey=$idVal and returns the page it should be on
	debug("DB:INFO:getPageForId([qry],$pagesize, $idVal,$idKey)");
	$db=new dbObj($qry);
	if($db->numRows()<2){
		$db->free();
		return 0;
	}
	while($row=$db->getRow() ){
		if($row[$idKey]==$idVal){
			debug("DB:INFO:getPageForId:found at $db->i");
			$rowoffset=$db->i;
			break;
		}
	}
	if(!$rowoffset)
		return 0;
	return intval(ceil($rowoffset/$pagesize)-1);
}
// returns a db connection : 07 Jan 2010
function get_connection($dbserv,$dbuser ,$dbpass,$dbname){
	$tmplink = mysql_connect($dbserv,$dbuser,$dbpass,$dbname);
	return $tmplink;
}

function insert_query_logs($table,$params,$safe=0){//str table,hash params, str keyName,no ins if safe=1
	global $dbLogsservwrite,$dbLogsuserwrite,$dbLogspasswrite,$dbLogsnamewrite,$HTTP_HOST;
	if(!$table || !is_array($params) ){
		return 0;
	}
	if($safe){
		if(count_rows($table,$params)){
			return 0;
		}
	}
	$names = implode(",",array_keys($params));
	if(get_magic_quotes_runtime()){
		foreach($params as $k=>$v){
			$params[$k]=addslashes(strip($v));
		}
	}
	$values= "'".implode("','",array_values($params))."'";
	$qry = "INSERT INTO $table($names) VALUES($values)";
	if($linkLogs = mysql_connect($dbLogsservwrite,$dbLogsuserwrite,$dbLogspasswrite)){
		if(!mysql_select_db($dbLogsnamewrite,$linkLogs)){
			notifyAdmin("Couldn't select database  $dbLogsnamewrite with $dbLogsuserwrite",mysql_error());
		}else {
			$res = mysql_query($qry,$linkLogs);
			debug("DB:SQL:$qry");
			if($e=mysql_error()){
				debug("DB:ERROR:$e");
				notifyAdmin("Insertion failure","ERROR:$e <br><br>SQL:$qry",mysql_error());
			}
			if(mysql_affected_rows($linkLogs)>0){
				$insertid =  mysql_insert_id();
				mysql_close($linkLogs);
				return $insertid;
			}
		}
	}else{
		notifyAdmin("couldn't connect to $dbLogsservwrite.$dbLogsnamewrite with $dbLogsuserwrite:$dbLogspasswrite",mysql_error());
	}

	return 0;
}


function exec_query_logs($query,$return_one=0,$field_name=""){
	global $dbLogsserv,$dbLogsuser,$dbLogspass,$dbLogsname,$dbLogsservwrite,$dbLogsuserwrite,$dbLogspasswrite,$dbLogsnamewrite;
	if(substr_count(strtolower($query),'insert ')>0 or substr_count(strtolower($query),'update ')>0 or substr_count(strtolower($query),'delete ')>0)
	{
		$activedbserv=$dbLogsservwrite;
		$activedbuser=$dbLogsuserwrite;
		$activedbpass=$dbLogspasswrite;
		$activedbname=$dbLogsnamewrite;
	}else{
		$activedbserv=$dbLogsserv;
		$activedbuser=$dbLogsuser;
		$activedbpass=$dbLogspass;
		$activedbname=$dbLogsname;
	}
	if($return_one && !stristr(strtolower($query),"limit"))$query.=" LIMIT 1";
	$ret = array();
	$mc=get_magic_quotes_runtime();
	//$query=str_replace(";","",$query);
	if($linkLogs = mysql_connect($activedbserv,$activedbuser,$activedbpass)){
		debug("DB:SQL:$query");
		if(!mysql_select_db($activedbname,$linkLogs)){
			debug("DB:ERROR:Not able to select Database");
		}else {
			$res = mysql_query($query,$linkLogs);
			if(!$res){
				debug("DB:ERROR:Could not successfully run query".$query." from DB: ". mysql_error());
			}else if(mysql_num_rows($res)>0){
				while($row = mysql_fetch_array($res,MYSQL_ASSOC)){
					if($mc){//when magic quots is on kill escaping
						$r=$field_name?strip($row[$field_name]):strip_arr($row);
					}else{
						$r=$field_name?$row[$field_name]:$row;
					}
					$ret[]=$r;
					unset($r);
				}
				mysql_free_result($res);
				mysql_close($linkLogs);
				return ($return_one?$ret[0]:$ret);
			}else{
				debug("DB:ERROR:".mysql_error());
			}
		}
	}
	unset($activedbserv);
	unset($activedbuser);
	unset($activedbpass);
	unset($activedbname);
	if($return_one && $field_name)return "";
	return array();
}

?>