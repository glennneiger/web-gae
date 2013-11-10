<?
global $cloudStorageTool,$HTPFX,$HTHOST;
include("$ADMIN_PATH/_header.htm");


/*============set up permissions=================*/
$adminall=$AD->getPerm(array("admin_users_all", "shops_edit"));

/*=============throw error if they're not allowed anything on this page====*/
if(!$adminall){
	admin_error("You're not allowed to use this page.");
}

$title="Manage Orders";
if(!$s)$s="date_created";
if(!$d)$d="DESC";
if(!$pagesize)$pagesize=20;
$p=intval($p);

$qry="SELECT id, date_format(date_created,'%m/%d/%Y %r') mdate_created,
	   b_fname,b_lname,total,status,was_shipped
	   FROM $ORDER_TABLE";

/*search parameters*/
$params=array();
if($bydate){
	if(($bmo&&$bday&&$byr)){
		$params[]="date_format(date_created,'%m%d%Y')>='".sprintf("%02d%02d%4d",$bmo,$bday,$byr)."'";
	}

	if(($emo&&$eday&&$eyr)){
		$params[]="date_format(date_created,'%m%d%Y')<='".sprintf("%02d%02d%4d",$emo,$eday,$eyr)."'";
	}
}
if($bybill){
	$params[]="$billcol='$billrow'";
}
if($byship){
	$params[]="$shipcol='$shiprow'";
}
if($byccproc){
	$params[]="$ccproccol='$ccprocrow'";
}
if($byccname){
	if($ccname){
		$params[]="lower(cc_type)='$ccname'";
	}
	if($ccnum){
		$params[]="cc_num='$ccnum'";
	}
	if($ccexpmo && $ccexpyear){
		$ccexp=sprintf("%02d/%4d",$ccexpmo,$ccexpyear);
		$params[]="cc_expiry='$ccexp'";
	}
}
if($bystatus){
	$params[]="status='$status'";
}
if($bywasshipped){
	$params[]="was_shipped='$was_shipped'";
}
if(count($params)){
	$qry.=" WHERE ".implode(" AND\n ",$params);
}
/*--- end search parameters ----*/

/*------- pagination -------------*/
$numrows=num_rows($qry);
$numpages=ceil($numrows/$pagesize);
$offset=($p*$pagesize);
$qry.=" ORDER BY $s $d LIMIT $offset,$pagesize";

if($numpages>1){
	$pagination=array();
	foreach(range(0,$numpages-1) as $i){
		$cond=($p==$i?1:0);
		 $pagination[]=href($PHP_SELF.qsa(array(p=>$i)),$i+1,$cond);
	}
	$pagination=implode(" | ",$pagination);
}
/*------- end pagination -------------*/




$d=($d=="DESC"?"ASC":"DESC");

$authinfo="http://$PHP_AUTH_USER:".urlencode($PHP_AUTH_PW)."@$HTTP_HOST".dirname($PHP_SELF);
//PHP_AUTH vars not showing in this env for some reason
$authinfo="."


/*=========== stats ==============*/

?>
<script>
var refer="<?=refer(1)?>";

function orderRemove(id){
	if(confirm("Are you sure you want to remove this order?")){
		location.replace("orders.mod.php?ACTION=remove&id="+id+"&refer="+refer);
	}
}

function orderEdit(id){
	var params="width=490,"
			  +"height=450,"
			  +"scrollbars=1,"
			  +"resizable=1";
	window.open("<?=$authinfo?>/orders.edit.php?id="+id,"editWnd",params);
}
function orderInvoice(id){
	var params="width=680,"
			  +"height=600,"
			  +"scrollbars=1,"
			  +"resizable=1";
	window.open("<?=$authinfo?>/orders.invoice.php?id="+id,"invoiceWnd",params);
}

function searchBox(){
window.open("orders.search.php<?=qsa()?>",
				"_search",
			"width=500,height=350,"
		    +"resizable=1,scrollbars=1"
			);
}
function summary(){
	window.open("orders.summary.php","_search");
}

window.onload=function(){
	window.name="mainwindow";
}
window.name="mainwindow";

function deleteSelected(){
	var theurl="orders.mod.php?refer="+refer+"&ACTION=remove";
	var oids=getChecks();
	if(!oids.length){
		alert("You don't have any orders selected");
		return;
	}
	if(!confirm("Are you sure you want to remove these orders? This action cannot be undone")){
		return;
	}
	for(i=0;i<oids.length;i++){
		theurl+="&id["+i+"]="+oids[i];
	}
	location.replace(theurl);

}
function shipSelected(){
	var theurl="orders.mod.php?refer="+refer+"&ACTION=shiporder";
	var oids=getChecks();
	if(!oids.length){
		alert("You don't have any orders selected");
		return;
	}
	if(!confirm("Are you sure you want to ship these orders? This action cannot be undone. This performs the final auth on credit cards. The transactions cannot be modified any further through this application")){
		return;
	}
	for(i=0;i<oids.length;i++){
		theurl+="&id["+i+"]="+oids[i];
	}
	location.replace(theurl);

}
function getChecks(){
	var ret=new Array();
	var frm=findObj("theform");
	for(i=0;i<frm.elements.length;i++){
		var el=frm.elements[i];
		if(typeof(el.name)=="undefined")continue;
		if(el.name.indexOf("order_")==-1)continue;
		if(!el.checked)continue;
		var oid=el.name.split("_");
		oid=oid[1];
		ret[ret.length]=oid
	}
	return ret;
}

</script>
<div id="tester"></div>
<div align=left>
<span class=button onclick="searchBox()">search</span> &nbsp; <?=$numrows?> orders
<?if(count($_GET)){?>
<br>&nbsp;<br>
<span class="button" onclick="location.replace(location.pathname)">Reset Search</span>

<?}?>
</div>
<br>&nbsp;<br>
<?if($numrows){?>

<table width=100% border=0 cellpadding=3>
<TR bgcolor="#999999">
<TD><a href="<?=$PHP_SELF.qsa(array(s=>id,d=>$d))?>">ID</a></TD>
<TD><a href="<?=$PHP_SELF.qsa(array(s=>date_created,d=>$d))?>">Date</a></TD>
<TD><a href="<?=$PHP_SELF.qsa(array(s=>b_lname,d=>$d))?>">Billing</a></TD>
<TD width=1><nobr><a href="<?=$PHP_SELF.qsa(array(s=>status,d=>$d))?>">Order Status</a></nobr></TD>
<TD nowrap><a href="<?=$PHP_SELF.qsa(array(s=>total,d=>$d))?>">Order Total</a></TD>
<TD width=1><a href="<?=$PHP_SELF.qsa(array(s=>was_shipped,d=>$d))?>">Shipped</a></TD>
<TD align=center>Options</TD>
</TR>
<form name="theform" id="theform">
<?foreach(exec_query($qry) as $i=>$row){?>
	<TR valign=top bgcolor=#cccccc>
	<TD width=1<?if($s=="id"){?> bgcolor="#eeeeee"<?}?>><?=$row[id]?></TD>
	<TD width=1<?if($s=="date_created"){?> bgcolor="#eeeeee"<?}?>>
		<nobr><?=$row[mdate_created]?></nobr>
	</TD>
	<TD<?if($s=="b_lname"){?> bgcolor="#eeeeee"<?}?>><?=$row[b_lname].", ".$row[b_fname]?></TD>
	<TD<?if($s=="status"){?> bgcolor="#eeeeee"<?}?>><?=$STORE_STATUS_TYPES[$row[status]]?></TD>
	<TD width=1<?if($s=="total"){?> bgcolor="#eeeeee"<?}?>>
		<nobr>$<?=dollars($row[total])?></nobr>
	</TD>
	<TD width=1<?if($s=="was_shipped"){?> bgcolor="#eeeeee"<?}?>>
		<nobr><?=($row[was_shipped]?"Yes":"No")?></nobr>
	</TD>
	<TD align=center nowrap>
	<span onclick="orderEdit('<?=$row[id]?>')"
		class=button
		title="Edit This Order">edit</span>
	<span onclick="orderInvoice('<?=$row[id]?>')"
		class=button
		title="View Invoice">invoice</span>
	<span onclick="orderRemove('<?=$row[id]?>')"
		class=button
		title="Remove this order">&nbsp;x&nbsp;</span>
	<span style="padding-left:10px"><?input_check("sorder_${row[id]}")?></span>
<br>
	</TD>
	</TR>
<?}?>
</form>
<TR>
<TD colspan=6>&nbsp;</TD>
<TD bgcolor=#eeeeee align=center style="border:1px #999 solid">
		<span onclick="deleteSelected()"
			class=button
			title="Edit This Order">delete selected</span>
		<span onclick="shipSelected()"
			class=button
			title="Edit This Order">ship selected</span>
</TD>
</TR>
<TR>
<TD colspan=7 align=center>
<div class="boxheader"><?=spacer(1,3)?></div>
<?=$pagination?>
</TD>
</TR>
</table>
<?}else{?>
	No orders found!
<?}?>
<br><br>
<center>
<fieldset style="width:200;text-align:left;"><legend>Download Order Database:</legend>
<div style="padding:20px">
<li><a href="orders.export.php?format=csv" style="white">Excel Format</a><br><br>
<li><a href="orders.export.php?format=xml">XML Format</a></li>
</div>
</fieldset>


</center>



<?include("./_footer.php")?>