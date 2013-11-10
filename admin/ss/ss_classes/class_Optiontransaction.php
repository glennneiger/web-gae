<?
global $D_R;
include_once($D_R."/admin/ss/ss_classes/class_transaction.php");
class optiontransaction
{
public function alltransaction($msg=NULL,$start=NULL,$end=NULL,$fldname=NULL,$ord=NULL,$items=NULL){
	global $sstransactioncnt,$contractqty; // web\lib\ss\_ss_config.php
	if($fldname==''){
		$fldname='creation_date';
	}else{
		$fldname=$fldname;
	}if($ord==''){
		$ord='ASC';
		$asndesn=$IMG_SERVER.'/images/quint_images/down.jpg';
		$nextord='ASC';
	}else{
		$ord=$ord;
		if($ord=='ASC'){$nextord='DESC';$asndesn=$IMG_SERVER.'/images/quint_images/up.jpg';}else{$nextord='ASC';$asndesn=$IMG_SERVER.'/images/quint_images/down.jpg';}
	}if($items==''){
		$items='';
	}else{
		$items=$items;
	}

	if((!isset($start)) && (!isset($end))){
		$start=0;
		$end=0+$sstransactioncnt;
		$lmt='';
		$sratindx=0;
		$endindx=0+$sstransactioncnt;
	}
	else{
		$startlmt=$start;
		$endlmt=$end;
		$sratindx=$start;
		$endindx=$end;
		$lmt=" LIMIT ".$startlmt.",".$endlmt;
	}
		$errmsg=explode('-',$msg);
		$errstockid=$errmsg[1];

		$fixedtransaction="select id from ss_transaction where entity_type='1' and status='1'"; // status =0 for deleted records
		$fixedtransaction1 = exec_query($fixedtransaction);
		$fixedresults=count($fixedtransaction1);
		unset($fixedtransaction1);

		if($fldname!='' && $ord!='' && $items!='')
		{
			$qry_transaction="select QT.id,QT.strike_price,QT.option_type,QT.quote_id,et.stocksymbol,QT.base_stock_symbol as CompanyName,QT.transaction_type,QT.creation_date,QT.unit_price,QT.quantity,QT.description from ss_transaction QT,ex_stock et where  QT.entity_type='1' and  QT.status='1' and et.id=QT.quote_id and QT.id in($items) order by $fldname $ord";
			//echo "<br>***********".$qry_transaction;
		}else
		{
			$qry_transaction="select QT.id,QT.strike_price,QT.option_type,QT.quote_id,QT.base_stock_symbol as CompanyName,et.optionticker as stocksymbol,QT.transaction_type,QT.creation_date,QT.unit_price,QT.quantity,QT.description from ss_transaction QT,ex_option et where QT.entity_type='1' and QT.status='1' and et.id=QT.quote_id order by creation_date DESC $lmt";
		}
		//echo "<br>aswini ".$qry_transaction."<br>";

		$execqry = exec_query($qry_transaction);
		if(count($execqry)>0){
			$strout='';
			$strout='<div id="optionstrans_detail">
				<table width="100%" style="border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" cellspacing="0" cellpadding="5" border=0 bordercolor="black" class="quintportfolio">
				<tr><td class="quintportfolio">Option Ticker</a></td>
				<td class="quintportfolio">Option Base</td>
				<td class="quintportfolio">Opt. Type</a></td>
				<td class="quintportfolio">Type</td>
				<td class="quintportfolio">Date</td>
				<td class="quintportfolio"># Contracts</td>
				<td class="quintportfolio">Price Per Option</td>
				<td class="quintportfolio">Strike Price</td>
				<td class="quintportfolio" colspan="3">Notes</td></tr>';

			$cntlnk=0;
			global $contractqty,$IMG_SERVER;

			foreach($execqry as $result){
				$stocksymbol=$result['stocksymbol'];
				$companyname=$result['CompanyName'];
				$autoidget=$result['id'];
				$stockid_get=$result['quote_id'];
				$transaction_type_get=$result['transaction_type'];
				$indexedtransaction_type_get=$transaction_type_get;
				$creation_date_get=$result['creation_date'];
				//$unit_price_get=($result['unit_price']/$result['quantity']); // $result['unit_price'] is actually storing the total price
				$unit_price_get=$result['unit_price']; // $result['unit_price'] is actually storing the total price
				$quantity_get=$result['quantity']/$contractqty;
				$notes_get=$result['description'];
				$option_type=$result['option_type'];
				$strike_price=number_format($result['strike_price'], 2, '.', '');

				if($stockid_get_all==''){
					$stockid_get_all=$autoidget;
				}else{
					$stockid_get_all=$stockid_get_all.",".$autoidget;
				}

				if($transaction_type_get==0)
				{
					$transaction_type_get='Buy';
				}else if($transaction_type_get==1)
				{
					$transaction_type_get='Sell';
				}else if($transaction_type_get==2)
				{
					$transaction_type_get='Short Sell';
				}else if($transaction_type_get==3)
				{
					$transaction_type_get='Buy to cover';
				}

				$cnt1++;if($cnt1%2==0){$tog1="#FFFFFF";}else { $tog1="#EFEFEF"; }

				$creation_date_get=$this->formatdate2Local($creation_date_get);

				$strout.='<tr bgcolor='.$tog1.'><td>'.$stocksymbol.'</td><td>'.$companyname.'</td><td>'.ucwords($option_type).'</td><td>'.$transaction_type_get.'</td><td>'.$creation_date_get.'</td>
				<td><input type="text" id="contractnos_'.$autoidget.'" name="contractnos_'.$autoidget.'" value="'.$quantity_get.'" style="border:none;background-color:'.$tog1.';" size="8" readonly></td>
				<td><input type="text" onblur="javascript:checkNumeric(this.id);" id="contractunitprice_'.$autoidget.'" name="contractunitprice_'.$autoidget.'" value="'.$unit_price_get.'" style="border:none;background-color:'.$tog1.';" size="8" readonly></td>
				<td>'.$strike_price.'</td><td>';

				if($transaction_type_get=='Short Sell')
				{
					$autoidgetenc=	strrev(base64_encode($autoidget));
					// $btnurl=$IMG_SERVER.'/images/quint_images/by-to-cover.jpg';
					$cnt++;
					//** $btid="btc_".$cnt;
					$strout.='<td><input type="text" id="optionnotes_'.$autoidget.'" name="optionnotes_'.$autoidget.'" value="'.$notes_get.'" size="25" style="border:none;background-color:'.$tog1.';" size="8" readonly></td>';
				}
				else
				{
					$strout.='<td><input type="text" id="optionnotes_'.$autoidget.'" name="optionnotes_'.$autoidget.'" value="'.$notes_get.'" size="25" style="border:none;background-color:'.$tog1.';" size="8" readonly></td>';
				}
				$imgurl=$IMG_SERVER.'/images/optionsmith/delete.jpg';
				$saveimg=$IMG_SERVER.'/images/optionsmith/save.gif';
				$spinerimg=$IMG_SERVER.'/images/optionsmith/spinner.gif';
				$strout.='</td><td width="17">
					<img src="'.$imgurl.'" id="optdelete_'.$autoidget.'" alt="Delete" title="click here to delete this transaction" onclick="javascript:optiontransactionsave(\'delete\',\'option\',\''.$autoidget.'\',\''.$indexedtransaction_type_get.'\')"  style="cursor:pointer;display:none;" />
					</td><td width="35">
					<span style="display:none;cursor:pointer;" id="savetrans_'.$autoidget.'" onclick="javascript:optiontransactionsave(\'update\',\'option\',\''.$autoidget.'\',\''.$indexedtransaction_type_get.'\')" title="click for save">
					<img src="'.$saveimg.'" alt="SAVE" title="click here to save"  />
					</span>
					<span style="display:none;cursor:pointer;" id="spinnerimg_'.$autoidget.'" >
					<img src="'.$spinerimg.'" alt="In Progress.." title="Progress..."  />
					</span>
					<span id="edittrans_'.$autoidget.'" style="cursor:pointer;" onclick="javascript:transactionedit(\'option\',\''.$autoidget.'\')" title="click for edit">EDIT</span></td></tr>';
				unset($imgurl);
				unset($saveimg);
				unset($spinerimg);


	// Navigation starts here
	$cntlnk++;
	$chkres=$cntlnk+$start;
	if(($chkres<$fixedresults) && ($cntlnk==$sstransactioncnt))
	{
		$strout.='<tr><td colspan="11" style="padding-top:2px;"><table border="0" cellspacing=0 cellpadding=0 width="100%"><td width="50%" style="padding-left:10px;" align="left">';
		if($start!=0){$strout.='<a class="viewnav" style="cursor:pointer;padding-left:10px;" onClick=javascript:optiomakeprevlinks1("optionchangabletransactionview","'.$start.'","'.$end.'")>&lt; Previous</a>';}
		$strout.='</td><td width="50%" style="padding-right:10px;" align="right"><a class="viewnav"  style="cursor:pointer;" onClick=javascript:optionmakenextlinks1("optionchangabletransactionview","'.$start.'","'.$end.'")>Next ></a></td></tr></table></td></tr>';
		$totrecds=$cntlnk;
		break;
	}
	else if(($chkres>=$fixedresults))
	{
		$strout.='<tr><td colspan="11" style="padding-top:5px;"><table border="0" cellspacing=0 cellpadding=0 width="100%">
		<td width="50%" style="padding-left:10px;" align="left">';
		if($start!=0){$strout.='<a class="viewnav"  style="cursor:pointer;padding-left:10px;" onClick=javascript:optiomakeprevlinks1("optionchangabletransactionview","'.$start.'","'.$end.'")>&lt; Previous</a>';}
		$strout.='&nbsp;</td>
		<td width="50%" style="padding-right:10px;" align="right">
		&nbsp;</td></tr></table></td></tr>';
		$totrecds=$cntlnk;
		break;
	}
	// Navigation ends here
	}
	$strout.='</table><input type="hidden" name="optionid_get_all" id="optionid_get_all" value='.$stockid_get_all.'><input type="hidden" id="optiontotbtcrec" name="optiontotbtcrec" value="'.$cnt.'"><p><font color=red>'.$errmsg[0].'</font></p></div>';
	}
	else
	{
		//$strout='<div id="trans_detail"><b><font color="#081E33"><br/>Make Your First Transaction!!</font></b></div>';
		$strout='<div id="trans_detail"><b><font color="#081E33"><br/>Make Your First Transaction!!</font></b><input type="hidden" id="optiontotbtcrec" name="optiontotbtcrec" value="0" /></div>';
	}
	return $strout;
}
public function formatdate2Local($creation_date){
	$datetodisplay=explode(" ",$creation_date);
	$datesArray=explode('-',$datetodisplay[0]);
	$creation_date = $datesArray[1]."/".$datesArray[2]."/".$datesArray[0];
	return $creation_date;
}
public function updatetransaction($type,$description=null,$optionunitprice=null,$recordid,$noofcontracts=null)
{
	global $contractqty;
	$objPortfolio=new optionPortfolio();
	if($type=='update')
	{
		$tablename='ss_transaction';
		$req_update=array();
		$req_update['updation_date']=date('Y-m-d H:i:s');
		$req_update['unit_price']=$optionunitprice; // $contractprice*($noofcontract*$contractqty)
		$req_update['description']=$description;
		$ret_up=update_query($tablename,$req_update,array(id=>$recordid));
			$memCacheDelete = new memCacheObj();
			$key="ssOpenPositiontrade";
			$memCacheDelete->deleteKey($key);
			$key="ssOpenPosition";
			$memCacheDelete->deleteKey($key);
			$key="ssDisplayPerformance";
			$memCacheDelete->deleteKey($key);
			$objPortfolio->updateOpenPositionRecords();
		return $ret_up;
	}
	else if($type=='delete')
	{
     /**
	 ** Buy transaction deletion
	 ** set the status of transaction table to 0(for buy) then set all the records status=0 in sell table
	 ** Set the all sell records against this transaction in buy table to 0 also
	 **/
		if(isset($ret_up)) unset($ret_up);
		$tablename='ss_transaction';
		$req_update=array();
		$req_update['updation_date']=date('Y-m-d H:i:s');
		$req_update['status']='0';
		$ret_up=update_query($tablename,$req_update,array('id'=>$recordid));
		if(isset($ret_up)&&($ret_up>0))
		{
			$tablename='ss_sell_transaction';
			$req_update=array();
			$req_update['updation_date']=date('Y-m-d H:i:s');
			$req_update['status']='0';
			$retsell_up=update_query($tablename,$req_update,array('buy_trans_id'=>$recordid));
			$memCacheDelete = new memCacheObj();
			$key="ssOpenPositiontrade";
			$memCacheDelete->deleteKey($key);
			$key="ssOpenPosition";
			$memCacheDelete->deleteKey($key);
			$key="ssDisplayPerformance";
			$memCacheDelete->deleteKey($key);
			$objPortfolio->updateOpenPositionRecords();
		}
		$retid=$this->updatetransidsBuy($recordid);
		return $retid;
	}
}
/*
This function will update buy table's ids
by checking the sell transaction id from sell table in respect to buy_trans_id
*/
function updatetransidsBuy($buytransactionid)
{
	$tablename='ss_transaction';
	$req_update=array();
	$req_update['updation_date']=date('Y-m-d H:i:s');
	$req_update['status']='0';
	$retsell_up=0;
	$sql="select sell_trans_id from ss_sell_transaction where buy_trans_id='$buytransactionid'";
	$execqry = exec_query($sql);
	if(count($execqry)>0){
		foreach($execqry as $allresultss){
		//echo "<br>".$allresultss['sell_trans_id'];
			$retsell_up=update_query($tablename,$req_update,array('id'=>$allresultss['sell_trans_id']));
		}
			$memCacheDelete = new memCacheObj();
			$key="ssOpenPositiontrade";
			$memCacheDelete->deleteKey($key);
			$key="ssOpenPosition";
			$memCacheDelete->deleteKey($key);
			$key="ssDisplayPerformance";
			$memCacheDelete->deleteKey($key);
	}
	return $retsell_up;
}

/**
* Delet the record from ss_transaction table first bocz sell entry goes to buy + sell table also
* Then delet the same transaction from sell table
**/
public function deleteSellOption($optionorstock,$recordid,$transtypes)
{
	if($optionorstock=='option')
	{
		$tablename='ss_transaction';
		$req_update=array();
		$req_update['updation_date']=date('Y-m-d H:i:s');
		$req_update['status']='0';
		$ret_up=update_query($tablename,$req_update,array('id'=>$recordid));

		if(isset($ret_up)&&($ret_up>0))
		{
			$tablename='ss_sell_transaction';
			$req_update=array();
			$req_update['updation_date']=date('Y-m-d H:i:s');
			$req_update['status']='0';
			$retsell_up=update_query($tablename,$req_update,array('sell_trans_id'=>$recordid));

			$memCacheDelete = new memCacheObj();
			$key="ssOpenPositiontrade";
			$memCacheDelete->deleteKey($key);
			$key="ssOpenPosition";
			$memCacheDelete->deleteKey($key);
			$key="ssDisplayPerformance";
			$memCacheDelete->deleteKey($key);
			$objPortfolio = new optionPortfolio();
			$objPortfolio->updateOpenPositionRecords();
		}
		return $retsell_up;
	}
}

/**
* Delet the record from ss_transaction table first bocz sell entry goes to buy + sell table also
* Then delet the same transaction from sell table
**/
public function deleteShortSellOption($optionorstock,$recordid,$transtypes)
{
	/*
	Step:1 Delete from ss_transaction table whose transaction_type='2' base on the autoid
	======
	Step:2 On autoid check the related transaction ids in sell table
	======
	Step:3 then update the sell + buy table with transaction_type='3'
	======
	 */
	if($optionorstock=='option')
	{
		$tablename='ss_transaction';
		$req_update=array();
		$req_update['updation_date']=date('Y-m-d H:i:s');
		$req_update['status']='0';
		$ret_up=update_query($tablename,$req_update,array('id'=>$recordid,'transaction_type'=>'2','entity_type'=>'1'));

		if(isset($ret_up)&&($ret_up>0))
		{
			$sql="select buy_trans_id from ss_sell_transaction where sell_trans_id='$recordid' and entity_type='1' and status='1'";

		    $execqry = exec_query($sql);
			if(count($execqry)>0){
				foreach($execqry as $allresultss){
					$tablename='ss_sell_transaction';
					$req_update=array();
					$req_update['updation_date']=date('Y-m-d H:i:s');
					$req_update['status']='0';
					$retshortsell_up=update_query($tablename,$req_update,array('buy_trans_id'=>$allresultss['buy_trans_id'],'sell_trans_id'=>$recordid,'entity_type'=>'1'));
					unset($req_update);
					unset($retshortsell_up);

					if($allresultss['buy_trans_id']!=0)
					{
						$tablename='ss_transaction';
						$req_update=array();
						$req_update['updation_date']=date('Y-m-d H:i:s');
						$req_update['status']='0';
						$retbtc_up=update_query($tablename,$req_update,array('id'=>$allresultss['buy_trans_id'],'entity_type'=>'1'));
						unset($req_update);
					}
				}
			}
		}
			$memCacheDelete = new memCacheObj();
			$key="ssOpenPositiontrade";
			$memCacheDelete->deleteKey($key);
			$key="ssOpenPosition";
			$memCacheDelete->deleteKey($key);
			$key="ssDisplayPerformance";
			$memCacheDelete->deleteKey($key);
			$objPortfolio=new optionPortfolio();
			$objPortfolio->updateOpenPositionRecords();
		return 1;
	}
}

/**
* Delet the record from ss_transaction table first bocz sell entry goes to buy + sell table also
* Then delet the same transaction from sell table
**/
public function deleteBTCOption($optionorstock,$recordid,$transtypes)
{
	/*
	Step:1 Delete from ss_transaction table whose transaction_type='3' by checking autoid
	======
	Step:2 as ss_transaction.id=ss_sell_transaction.buy_trans_id  update the ss_sell_transaction tables record by checking buy_trans_id=$autoid
	======
	Step:3 then update the trans_status to pending in sell table
	======
	 */
	if($optionorstock=='option')
	{
		$tablename='ss_transaction';
		$req_update=array();
		$req_update['updation_date']=date('Y-m-d H:i:s');
		$req_update['status']='0';
		$ret_up=update_query($tablename,$req_update,array('id'=>$recordid,'transaction_type'=>'3','entity_type'=>'1'));

		if(isset($ret_up)&&($ret_up>0))
		{
			$sql="select sell_trans_id from ss_sell_transaction where buy_trans_id='$recordid' and entity_type='1' and status='1'";
			$execqry = exec_query($sql);
			if(count($execqry)>0){
				foreach($execqry as $allresultss){
					$parentShortselltransid=$allresultss['sell_trans_id'];

					$tablename='ss_sell_transaction';
					$req_update=array();
					$req_update['updation_date']=date('Y-m-d H:i:s');
					$req_update['status']='0';
					$retshortsell_up=update_query($tablename,$req_update,array('buy_trans_id'=>$recordid,'entity_type'=>'1'));
					unset($req_update);
					unset($retshortsell_up);
					// then update the parent short sell trans_status to 'pending'
					$req_update=array();
					$req_update['updation_date']=date('Y-m-d H:i:s');
					$req_update['trans_status']='pending';
					$retbtcsell_up=update_query($tablename,$req_update,array('buy_trans_id'=>'0','sell_trans_id'=>$parentShortselltransid,'entity_type'=>'1'));
					unset($req_update);
					unset($retbtcsell_up);
				}
			}
		}
			$memCacheDelete = new memCacheObj();
			$key="ssOpenPositiontrade";
			$memCacheDelete->deleteKey($key);
			$key="ssOpenPosition";
			$memCacheDelete->deleteKey($key);
			$key="ssDisplayPerformance";
			$memCacheDelete->deleteKey($key);
			$objPortfolio=new optionPortfolio();
			$objPortfolio->updateOpenPositionRecords();
		return 1;
	}
}
/**
 * This function checks the balance quantity that can be sell against a particular transaction
 * @param buy transaction id
 **/
public function CheckSellTransaction($optionorstock,$recordid,$transtypes)
{
	if($optionorstock=='option')
	{
		// For qyantity check
		$totalbuyqty=$this->CheckTotalBuyQty($optionorstock,$recordid);
		$allreadysellqty=$this->CheckTotalSellQty($optionorstock,$recordid);
		$result=$totalbuyqty-$allreadysellqty;
		if($result>0){
			$sellpossible=1;
		}else{
			$sellpossible=0;
		}
		return $sellpossible;

	}
}
public function CheckTotalBuyQty($optionorstock,$recordid)
{
	if($optionorstock=='option')
	{
		$sql="select quantity from ss_transaction where id=(select buy_trans_id as id from ss_sell_transaction where sell_trans_id='$recordid')and entity_type='1' and status='1' and transaction_type='0'";
		$execqry = exec_query($sql,1);
		return $execqry['quantity'];
	}
}

public function CheckTotalSellQty($optionorstock,$recordid)
{
	if($optionorstock=='option')
	{
		$sql="select sum(quantity) as totalsellqty from ss_transaction where transaction_type='1' and id in (select sell_trans_id as id from ss_sell_transaction where buy_trans_id=(select buy_trans_id as id from ss_sell_transaction where sell_trans_id='$recordid'))";
		$execqry = exec_query($sql,1);
		return $execqry['totalsellqty'];
	}
}

public function shortsellOptionTransactionView($msg=NULL,$start=NULL,$end=NULL,$fldname=NULL,$ord=NULL,$items=NULL){
	global $sstransactioncnt,$contractqty; // web\lib\ss\_ss_config.php
	if($fldname==''){
		$fldname='creation_date';
	}else{
		$fldname=$fldname;
	}if($ord==''){
		$ord='ASC';
		$asndesn=$IMG_SERVER.'/images/quint_images/down.jpg';
		$nextord='ASC';
	}else{
		$ord=$ord;
		if($ord=='ASC'){$nextord='DESC';$asndesn=$IMG_SERVER.'/images/quint_images/up.jpg';}else{$nextord='ASC';$asndesn=$IMG_SERVER.'/images/quint_images/down.jpg';}
	}if($items==''){
		$items='';
	}else{
		$items=$items;
	}

	if((!isset($start)) && (!isset($end))){
		$start=0;
		$end=0+$sstransactioncnt;
		$lmt='';
		$sratindx=0;
		$endindx=0+$sstransactioncnt;
	}
	else{
		$startlmt=$start;
		$endlmt=$end;
		$sratindx=$start;
		$endindx=$end;
		$lmt=" LIMIT ".$startlmt.",".$endlmt;
	}
	$errmsg=explode('-',$msg);
	$errstockid=$errmsg[1];

	$fixedtransaction="select id from ss_transaction where transaction_type='2' and entity_type='1' and status='1'"; // status =0 for deleted records
	$fixedtransaction1 = exec_query($fixedtransaction);
	$fixedresults=count($fixedtransaction1);
	unset($fixedtransaction1);

	if($fldname!='' && $ord!='' && $items!='')
	{
		$qry_transaction="select QT.id,QT.strike_price,QT.option_type,QT.quote_id,et.stocksymbol,QT.base_stock_symbol as CompanyName,QT.transaction_type,QT.creation_date,QT.unit_price,QT.quantity,QT.description from ss_transaction QT,ex_stock et where QT.transaction_type='2' and QT.entity_type='1' and  QT.status='1' and et.id=QT.quote_id and QT.id in($items) order by $fldname $ord";
		//***echo "<br>***********".$qry_transaction;
	}else
	{
		$qry_transaction="select QT.id,QT.strike_price,QT.option_type,QT.quote_id,QT.base_stock_symbol as CompanyName,et.optionticker as stocksymbol,QT.transaction_type,QT.creation_date,QT.unit_price,QT.quantity,QT.description from ss_transaction QT,ex_option et where QT.transaction_type='2' and QT.entity_type='1' and QT.status='1' and et.id=QT.quote_id order by creation_date DESC $lmt";
	}
	//echo "Without filter ".$qry_transaction;

	$execqry = exec_query($qry_transaction);
	if(count($execqry)>0)
	{
		$strout='';
		$strout='<div id="optionstrans_detail"><span id="optionbtcspan"></span>
			<table width="100%" style="border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" cellspacing="0" cellpadding="5" border=0 bordercolor="black" class="quintportfolio">
			<tr><td class="quintportfolio">Option Ticker</a></td>
			<td class="quintportfolio">Option Base</td>
			<td class="quintportfolio">Opt. Type</a></td>
			<td class="quintportfolio">Type</td>
			<td class="quintportfolio">Date</td>
			<td class="quintportfolio"># Contracts</td>
			<td class="quintportfolio">Price Per Option</td>
			<td class="quintportfolio">Strike Price</td>
			<td class="quintportfolio" colspan="3">Notes</td></tr>';
		$cntlnk=0;
		global $contractqty;
		foreach($execqry as $result)
		{
			$stocksymbol=$result['stocksymbol'];
			$companyname=$result['CompanyName'];
			$autoidget=$result['id'];
			$stockid_get=$result['quote_id'];
			$transaction_type_get=$result['transaction_type'];
			$indexedtransaction_type_get=$transaction_type_get;
			$creation_date_get=$result['creation_date'];
			//$unit_price_get=($result['unit_price']/$result['quantity']); // $result['unit_price'] is actually storing the total price
			$unit_price_get=$result['unit_price']; // $result['unit_price'] is actually storing the total price
			$quantity_get=$result['quantity']/$contractqty;
			$notes_get=$result['description'];
			$option_type=$result['option_type'];
			$strike_price=number_format($result['strike_price'], 2, '.', '');
			if($stockid_get_all==''){
				$stockid_get_all=$autoidget;
			}else{
				$stockid_get_all=$stockid_get_all.",".$autoidget;
			}

			if($transaction_type_get==0)
			{
				$transaction_type_get='Buy';
			}else if($transaction_type_get==1)
			{
				$transaction_type_get='Sell';
			}else if($transaction_type_get==2)
			{
				$transaction_type_get='Short Sell';
			}else if($transaction_type_get==3)
			{
				$transaction_type_get='Buy to cover';
			}
			$cnt1++;if($cnt1%2==0){$tog1="#FFFFFF";}else { $tog1="#EFEFEF"; }
			$creation_date_get=$this->formatdate2Local($creation_date_get);
			$strout.='<tr bgcolor='.$tog1.'><td>'.$stocksymbol.'</td><td>'.$companyname.'</td><td>'.ucwords($option_type).'</td><td>'.$transaction_type_get.'</td><td>'.$creation_date_get.'</td>
				<td><input type="text" id="contractnos_'.$autoidget.'" name="contractnos_'.$autoidget.'" value="'.$quantity_get.'" style="border:none;background-color:'.$tog1.';" size="8" readonly></td>
				<td><input type="text" onblur="javascript:checkNumeric(this.id);" id="contractunitprice_'.$autoidget.'" name="contractunitprice_'.$autoidget.'" value="'.$unit_price_get.'" style="border:none;background-color:'.$tog1.';" size="8" readonly></td>
				<td>'.$strike_price.'</td><td>';
			if($transaction_type_get=='Short Sell')
			{
				$autoidgetenc=	strrev(base64_encode($autoidget));
					$btnurl=$IMG_SERVER.'/images/quint_images/by-to-cover.jpg';
					$cnt++;
					$btid="btc_".$cnt;
					$strout.='<table width="100%" border=0>
					<tr><td>
					<input type="text" id="optionnotes_'.$autoidget.'" name="optionnotes_'.$autoidget.'" value="'.$notes_get.'" size="25" style="border:none;background-color:'.$tog1.';" size="8" readonly>
					</td>';
				$strout.='<td width="10%">';
				$strout.='<img src="'.$btnurl.'" id="'.$btid.'" onClick="optionbuytransaction(\''.$autoidgetenc.'\')" alt="BTC" title="click here for Buy to cover" style="cursor:pointer"/></td>';
				$strout.='</tr></table>';
			}
			else
			{
				$strout.='<td><input type="text" id="optionnotes_'.$autoidget.'" name="optionnotes_'.$autoidget.'" value="'.$notes_get.'" size="25" style="border:none;background-color:'.$tog1.';" size="8" readonly></td>';
			}
			$imgurl=$IMG_SERVER.'/images/optionsmith/delete.jpg';
			$saveimg=$IMG_SERVER.'/images/optionsmith/save.gif';
			$spinerimg=$IMG_SERVER.'/images/optionsmith/spinner.gif';
			$strout.='</td><td width="17">
									 <img src="'.$imgurl.'" id="optdelete_'.$autoidget.'" alt="Delete" title="click here to delete this transaction" onclick="javascript:optiontransactionsave(\'delete\',\'option\',\''.$autoidget.'\',\''.$indexedtransaction_type_get.'\')"  style="cursor:pointer;display:none;" />
				</td><td width="35">
							   <span style="display:none;cursor:pointer;" id="savetrans_'.$autoidget.'" onclick="javascript:optiontransactionsave(\'update\',\'option\',\''.$autoidget.'\',\''.$indexedtransaction_type_get.'\')" title="click for save">
				<img src="'.$saveimg.'" alt="SAVE" title="click here to save"  />
				</span>
				<span style="display:none;cursor:pointer;" id="spinnerimg_'.$autoidget.'" >
				<img src="'.$spinerimg.'" alt="In Progress.." title="Progress..."  />
				</span>
				<span id="edittrans_'.$autoidget.'" style="cursor:pointer;" onclick="javascript:transactionedit(\'option\',\''.$autoidget.'\')" title="click for edit">EDIT</span></td></tr>';
			unset($imgurl);
			unset($saveimg);
			unset($spinerimg);


	// Navigation starts here
			$cntlnk++;
			$chkres=$cntlnk+$start;
			if(($chkres<$fixedresults) && ($cntlnk==$sstransactioncnt))
			{
				$strout.='<tr><td colspan="11" style="padding-top:2px;"><table border="0" cellspacing=0 cellpadding=0 width="100%"><td width="50%" style="padding-left:10px;" align="left">';
				if($start!=0){$strout.='<a class="viewnav" style="cursor:pointer;padding-left:10px;" onClick=javascript:makeOptionBTCprevlinks1("optionchangabletransactionview","'.$start.'","'.$end.'")>&lt; Previous</a>';}
				$strout.='</td><td width="50%" style="padding-right:10px;" align="right"><a class="viewnav"  style="cursor:pointer;" onClick=javascript:makeOptionBTCnextlinks1("optionchangabletransactionview","'.$start.'","'.$end.'")>Next ></a></td></tr></table></td></tr>';
				$totrecds=$cntlnk;
				break;
			}
			else if(($chkres>=$fixedresults))
			{
				$strout.='<tr><td colspan="11" style="padding-top:5px;"><table border="0" cellspacing=0 cellpadding=0 width="100%">
					<td width="50%" style="padding-left:10px;" align="left">';
				if($start!=0){$strout.='<a class="viewnav"  style="cursor:pointer;padding-left:10px;" onClick=javascript:makeOptionBTCprevlinks1("optionchangabletransactionview","'.$start.'","'.$end.'")>&lt; Previous</a>';}
				$strout.='&nbsp;</td>
						<td width="50%" style="padding-right:10px;" align="right">
					&nbsp;</td></tr></table></td></tr>';
				$totrecds=$cntlnk;
				break;
				}
	// Navigation ends here
			}
			$strout.='</table><input type="hidden" name="optionid_get_all" id="optionid_get_all" value='.$stockid_get_all.'><input type="hidden" id="optiontotbtcrec" name="optiontotbtcrec" value="'.$cnt.'"><p><font color=red>'.$errmsg[0].'</font></p></div>';
			}
			else
			{
				$strout='<div id="trans_detail"><b><font color="#081E33"><br/>Make Your First Transaction!!</font></b><input type="hidden" id="optiontotbtcrec" name="optiontotbtcrec" value="0" /></div>';
			}
	return $strout;
}
public function buytocover($transid){
	 $fname='btc';
	 global $lang,$IMG_SERVER;
	 $id_stockname1=$this->allsymbolsarr();
	 //$id_comname1=$this->allsymbolscomapnyarr();
	 $id_comname1="Base stock";
	 $qry_transaction="select expirydate,strike_price,id,option_type,quote_id,transaction_type,base_stock_symbol,creation_date,unit_price,quantity,description from ss_transaction  where entity_type='1' and status='1' and id='$transid' union ALL select QT.expirydate,QT.strike_price,QT.id,QT.option_type,QT.quote_id,QT.transaction_type,QT.base_stock_symbol,QT.creation_date,QT.unit_price,QT.quantity,QT.description  from ss_transaction QT,ss_sell_transaction QST where QT.entity_type='1' and QST.sell_trans_id='$transid' and QST.status='1' and QST.buy_trans_id!=0 and QST.buy_trans_id=QT.id";
	 //echo $qry_transaction;
	 global $contractqty;
			 $execqry = exec_query($qry_transaction);
			 if(count($execqry)>0){

				 $strout='';
				 $strout='<div id="trans_detail">
					 <table width="100%" style="border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" cellspacing="0" cellpadding="5" border=0 bordercolor="black" class="quintportfolio">
					 <tr><td class="quintportfolio">Option Ticker</td><td class="quintportfolio">Option Base</td><td class="quintportfolio">Option Type</td><td class="quintportfolio">Type</td><td class="quintportfolio">Exp. Date</td><td class="quintportfolio">Date</td><td class="quintportfolio"># Contracts</td><td class="quintportfolio">Price Per Option</td><td class="quintportfolio">Strike Price</td><td class="quintportfolio">Notes</th></tr>
					 ';
				 $totalstockstobtc=0;
				 foreach(exec_query($qry_transaction) as $result){
				 $option_type=$result['option_type'];
				 $autoidget=$result['id'];
				 $stockid_get=$result['quote_id'];
				 $base_stock_symbol=$result['base_stock_symbol'];
				 $transaction_type_get=$result['transaction_type'];
				 $creation_date_get=$result['creation_date'];
				 $expirydate=$result['expirydate'];
				//**** $unit_price_get=$result['unit_price'];
				 //$unit_price_get=($result['unit_price']/$result['quantity']); // $result['unit_price'] is actually storing the total price
				 $unit_price_get=($result['unit_price']); // $result['unit_price'] is actually storing the total price
				 $strike_price=$result['strike_price'];

				 //****$quantity_get=$result['quantity'];
				 $contract_get=$result['quantity']/$contractqty;
				 $quantity_get=$contract_get;
				 $notes_get=$result['description'];
				 if($transaction_type_get==2){$totalstockstobtc=$totalstockstobtc+$quantity_get;}
				 if($transaction_type_get==3){$totalstockstobtc=$totalstockstobtc-$quantity_get;}
				 if($transaction_type_get==0){$transaction_type_get='Buy';	}
				 else if($transaction_type_get==1){$transaction_type_get='Sell';}
				 else if($transaction_type_get==2){$transaction_type_get='Short Sell';}
				 else if($transaction_type_get==3){$transaction_type_get='Buy to cover';}
				 $cnt1++;
				 if($cnt1%2==0){$tog1="#FFFFFF";}
				 else { $tog1="#EFEFEF"; }
				 $creation_date_get=$this->formatdate2Local($creation_date_get);
				 $expirydate_get=$this->formatdate2Local($expirydate);
				 $strout.='<tr bgcolor='.$tog1.'><td>'.$id_stockname1[$stockid_get].'</td><td>'.$base_stock_symbol.'</td><td>'.ucwords($option_type).'</td><td>';
				 if($transaction_type_get=='Short Sell')
				 {
					 $strout.='<input type="hidden" id="orgsortselldate" name="orgsortselldate" value="'.$creation_date_get.'">';
				 }
				 $strout.=$transaction_type_get.'</td><td>'.$expirydate_get.'</td><td>'.$creation_date_get.'</td><td>'.$contract_get.'</td>
				 <td>'.$unit_price_get.'</td><td>'.number_format($strike_price, 2, '.', '').'</td><td>';
				 $strout.=$notes_get;
				 $strout.='</td></tr>';
				 }
			 $strout.='</table>';
			 if($totalstockstobtc!=0)
			 {
				$strout.='<p class="quintportfolio">Buy To Cover (&nbsp;'.$lang["max_option_for_btc"].' '.$totalstockstobtc.' &nbsp;)</p>
				<form name="'.$fname.'" method="post">
				<table width="100%" style="border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" cellspacing="0" cellpadding="5" border="0" bordercolor="red" class="quintportfolio">
				 <tr>
					<td class="quintportfolio" width="15%">Date</td>
					<td class="quintportfolio" width="10%"># Contracts</td>
					<td class="quintportfolio" width="15%">Price Per Option</td>
					<td class="quintportfolio">Notes</td>
					</tr>
					<tr bgcolor="#EFEFEF">
 					 <td class="quintportfolio" width="15%">';
					 $stren=strrev(base64_encode($transid));
					 $strout.='<input type="hidden" name="quoteid_get" id="quoteid_get" value='.$stockid_get.'>';
					 $strout.='<input type="hidden" id="strike_price" name="strike_price" value='.$strike_price.'>';
					 $strout.='<input type="hidden" id="option_type" name="option_type" value='.$option_type.'>';
					 $strout.='<input type="hidden" id="trsactionid" name="trsactionid" value='.$stren.'>';
					 $strout.='<input type="hidden" id="expirydate" name="expirydate" value="'.$expirydate.'">';
					 $strout.='<input type="hidden" id="base_stock_symbol" name="base_stock_symbol" value="'.$base_stock_symbol.'">';
					 $strout.='<input type="hidden" id="orgssqty" name="orgssqty" value="'.$totalstockstobtc.'">
					 <input type="hidden" id="unitssprice" name="unitssprice" value="'.$unit_price_get.'">';
					 $strout.='<input type="text" size="10" name="selldate" id="selldate" value="" readonly>&nbsp;';
					 $str="selldate";
					 $strout.="<a href=\"javascript:NewCal('$str','mmddyyyy')\">";
					 $strout.='<img src="'.$IMG_SERVER.'/images/quint_images/cal.gif" alt="Pick a date" width="16" height="16" hspace="1" border="0px" align="absmiddle" name="img" id="img" ></a></td>
					 <td class="quintportfolio" width="10%"><input size="8" type="text" name="shareqty" id="shareqty"  value="" onKeyup=javascript:chknosvalidation("'.$fname.'",this)></td>
					 <td class="quintportfolio" width="10%"><input size="8" type="text" name="price" id="price"  value="" onKeyup=javascript:chknosvalidation("'.$fname.'",this)></td>
					 <td class="quintportfolio"><input type="text" size="40" name="notes" value="">
					 <input type="button" value="SAVE" onClick="javascript:chkoptionsubmit()">&nbsp;<input type="reset" value="RESET"></td>
					 </tr>
					 </table></form>';
				 }
			 $strout.='</div>';
		 }else{
			 $strout='<div id="trans_detail"><b><font color="#081E33"><br/>Make Your First Short Sell .... !!</font></b></div>';
		 }
		 return $strout;
 }

# This function returns the array of all symbols name
private function allsymbolsarr(){
	$quer_for_allsym="select id,optionticker stocksymbol from ex_option";
	$allressym=exec_query($quer_for_allsym);
	if(count($allressym)>0){
			//$id_comname=array();
		$id_stockname=array();

		foreach($allressym as $allresultsym){
			$storeid=$allresultsym['id'];
			$stocksymbol=$allresultsym['stocksymbol'];
			$id_stockname[$storeid]=$stocksymbol;
		}
		return $id_stockname;
	}
}
public function getTransactionDetailofTicker($serchsymname)
{
	global $contractqty,$IMG_SERVER;
	$tickerId=$this->getSymbolid($serchsymname);
	// if the symbol is valid
	if($tickerId!=0)
	{
		$sqlfortransdetail="select strike_price,base_stock_symbol,option_type,expirydate,id,creation_date,unit_price,round(quantity/$contractqty) noofcontract,quantity from ss_transaction where entity_type='1' and transaction_type='0' and quote_id='$tickerId' and status='1'";
		//echo "<br>".$sqlfortransdetail;
		$allressym='';
		$allressym=exec_query($sqlfortransdetail);
		$totalrecds=0;
		if(count($allressym)>0){
			$fname="symboltransa";
			$strdisp='<form name="symboltransa" id="symboltransa" method="POST">';
			$strdisp.='<table border="0" width="100%" style="border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" cellspacing="0" cellpadding="5" border=0 bordercolor="black" class="quintportfolio">
			<input type="hidden" name="quoteidserched" id="quoteidserched" value="'.$tickerId.'">';
			$strdisp.='<tr>
				<td class="quintportfolio"><nobr>Date</nobr></td>
				<td class="quintportfolio"><nobr>#Contr.</td>
			    <td class="quintportfolio"><nobr>Expiry</td>
				<td class="quintportfolio"><nobr>#Options</td>
				<td class="quintportfolio"><nobr>Price per Option</td>
				<td class="quintportfolio"><nobr>Sell Contract </td>
				<td class="quintportfolio"><nobr>Sell Per Option($)</td>
				<td class="quintportfolio"><nobr>Sell Date</td>
				<td class="quintportfolio"><nobr>Notes</td>
			    <td class="quintportfolio">Select</td></tr>';
			foreach($allressym as $alltransres){
				$totalrecds++;
				$pid=$alltransres['id'];
				$purcgdate=$alltransres['creation_date'];
				$unitprice=$alltransres['unit_price'];
				$qty=$alltransres['quantity'];
				$expirydate=$alltransres['expirydate'];
				$base_stock_symbol=$alltransres['base_stock_symbol'];
				$noofcontract=$alltransres['noofcontract'];
				$strike_price=$alltransres['strike_price'];
				$option_type=$alltransres['option_type'];
				$getprevsellqty=0;
				//$qyt=$qty-$getprevsellqty;
				$selltransactionarr1=$this->stocksales($pid); // returns in contract
				//***echo $qty."~~~~~".$selltransactionarr1[$pid];
				$qty=$qty-($selltransactionarr1[$pid]*$contractqty);
				$noofcontract=$noofcontract-$selltransactionarr1[$pid];
				$cnt++;
				if($cnt%2==0){$tog="#FFFFFF";}
				else { $tog="#EFEFEF"; }
				$strdisp.='<tr bgcolor="'.$tog.'"><td><nobr><input type="hidden" id="hid_'.$pid.'" value="'.$pid.'">'.$this->formatdate2LocalSCR($purcgdate).'<input type="hidden" name="buydate_'.$pid.'" id="buydate_'.$pid.'"  value="'.$this->formatdate2LocalSCR($purcgdate).'"></td>
				<td>'.$noofcontract.'</td><td><input type="hidden" name="strike_price_'.$pid.'" id="strike_price_'.$pid.'" value="'.$strike_price.'" ><input type="hidden" name="option_type_'.$pid.'" id="option_type_'.$pid.'" value="'.$option_type.'" >
				<input type="hidden" name="expiry_'.$pid.'" id="expiry_'.$pid.'" value="'.$expirydate.'" >'.$this->formatdate2LocalSCR($expirydate).'</td>
				<td>'.$qty.'<input type="hidden" id="base_stock_symbol_'.$pid.'" name="base_stock_symbol_'.$pid.'" value="'.$base_stock_symbol.'"><input type="hidden" name="buyqty_'.$pid.'" id="buyqty_'.$pid.'"  value="'.$noofcontract.'"></td>
				<td>'.$unitprice.'</td>
				<td><input type="text" id="sellqty_'.$pid.'" name="sellqty_'.$pid.'" value="0" size="8" maxlength="20"  onKeyup=javascript:chknosvalidation("'.$fname.'",this)></td>
				<td><input type="text" size="8" maxlength="25"  name="sellprice_'.$pid.'" id="sellprice_'.$pid.'" value="0.00" onKeyup=javascript:chknosvalidation("'.$fname.'",this)></td>
				<td><nobr>';
				$strdisp.='<input type="text" size="10" name="selldate_'.$pid.'" id="selldate_'.$pid.'" value="" readonly>&nbsp;';
				$str="selldate_".$pid;
				$strdisp.="<a href=\"javascript:NewCal('$str','mmddyyyy')\">";
				$strdisp.='<img src="'.$IMG_SERVER.'/images/quint_images/cal.gif" alt="Pick a date" width="16" height="16" hspace="1" border="0px" align="absmiddle" name="img_'.$pid.'" id="img_'.$pid.'" ></a></td>
				<td><input name="sellnote_'.$pid.'" id="sellnote_'.$pid.'"  type="text" size="35" maxlength="500"></td>
			        <td><input type="checkbox" name="sellchk_'.$pid.'" id="sellchk_'.$pid.'" style="border:0px" value="'.$pid.'" onClick="javascript:validateOptionentries('.$pid.')"></td>';
				$strdisp.='</tr>';
				if($stockid_get_all1=='')
				{
					$stockid_get_all1=$pid;
				}else
				{
					$stockid_get_all1=$stockid_get_all1.",".$pid;
				}
			}
			$strdisp.='</table>';
			if($totalrecds>0)
			{
				$strdisp.='<p><img src="'.$IMG_SERVER.'/images/quint_images/btn-savechanges.gif" align="left" onclick="javascript:saveOptionselltransactions();"/></p>';
			}
			$strdisp.='<input type="hidden" name="serchsymname" value='.$serchsymname.'>
			<input type="hidden" name="totrecords" value='.$totalrecds.'>
			<input type="hidden" name="stockid_get_all1" value='.$stockid_get_all1.'>
			<input type="hidden" id="mode"  name="mode" value="'.$updatemode.'">
			</form>';
			return $strdisp;
	}else if(count($allressym)==0)
		{
		echo "<b><font color=red>You have nothing to sell against this symbol</font></b>";
	}
}
	// else if the symbol is invalid as per our db
else if($tickerId==0){
	echo "<b>Entered Option Ticker is not Listed.....</b>";
}
}
public function getSymbolid($serchsymname){
	 $getidfromstocks="select id from ex_option where optionticker= trim('".$serchsymname."') ";
	 $allressym='';
	 $sid=0;
	 $allressym=exec_query($getidfromstocks);
	 if(count($allressym)>0){
		 foreach($allressym as $allresultsym){
		 $sid=$allresultsym['id'];
		 }
	 }
	 return $sid;
 }
public function stocksales($pid=NULL){
	global $contractqty;
	if(isset($pid)){
		$append=" and QST.entity_type='1' and  QST.buy_trans_id='$pid'";
	}else{
		$append='';
	}
	$qry="select QST.buy_trans_id as bti,round(sum(QT.quantity)/$contractqty) as totsell from ss_transaction QT, ss_sell_transaction QST where QT.entity_type='1' and QST.sell_trans_id=QT.id and QT.status='1' $append group by buy_trans_id";
	//echo '<br>'.$qry;
	$stockqry=exec_query($qry);
	$selltransactionarr=array();
	if(count($stockqry)>0){
		foreach($stockqry as $allresult){
			$bti=$allresult['bti'];
			$totsell=$allresult['totsell'];
			$selltransactionarr[$bti]=$totsell;
		}
	}
	//htmlprint_r($selltransactionarr);
	return $selltransactionarr;
}
# This function returns the avg unit price of particular quote_id used in edittransaction/option_sell.htm page
public function avgunitpriceofstockinhand($qid)
{
	$quer_for_buyids="select sum(quantity) as totpurchqty,sum((unit_price)*(quantity)) as totpurchamt  from ss_transaction where entity_type='1' and status='1' and transaction_type='0' and quote_id='$qid' group by quote_id";
	$allr=exec_query($quer_for_buyids);
	if(count($allr)>0){
		$avgunitpricearr=array();
		foreach($allr as $allresultbuy){
			$totalpurchsdqty=$allresultbuy['totpurchqty']; // Final for each qid
			$totpurchamtget=$allresultbuy['totpurchamt'];
			$totalsoldamt=0;
			$sellqty=0;
			$quer_for_sellid="select id, quantity as totsell from ss_transaction where status='1' and transaction_type='1' and quote_id='$qid'";
			$alls=exec_query($quer_for_sellid);
			if(count($alls)>0){
				$soldqty=0;
				$totalsellqty=0;
				foreach($alls as $allresultsell){
					$selltransid=$allresultsell['id'];
					$soldqty=$allresultsell['totsell'];
					$totalsellqty=$totalsellqty+$soldqty;
					$upqry="select QT.unit_price up,QT.id from ss_transaction QT, ss_sell_transaction QST where QT.entity_type='1' and QST.sell_trans_id='$selltransid' and QST.buy_trans_id=QT.id";
					$upsell=exec_query($upqry);
					if(count($upsell)>0) {
						$soldat=0;
						foreach($upsell as $upall){
							$soldat=$soldqty*$upall['up'];
							$totalsoldamt=$totalsoldamt+$soldat;
						}
					}
				}
			}
			$avgunitprice=($totpurchamtget-$totalsoldamt)/($totalpurchsdqty-$totalsellqty);
			$avgunitpricearr[$qid]=$avgunitprice;
		}
	}
	return $avgunitpricearr[$qid];
}
// This function returns the array of total stocks in hand by indexing the quote_id
public function getallbuystocks($dates=NULL){
	if(isset($dates)){
		$append=" and creation_date>='$dates'";
	}else{
		$append='';
	}
	$quer_for_buyids="select quote_id,sum(quantity) as totpurch from ss_transaction where entity_type='1' and status='1' and transaction_type='0' $append group by quote_id";
		//echo "<br>".$quer_for_buyids;
	$allr=exec_query($quer_for_buyids);
	if(count($allr)>0){
		$stockbuys=array();
		foreach($allr as $allresultbuy){
			$qid=$allresultbuy['quote_id'];
					// chek for rotal sell amt
			$sellqty=0;
			$quer_for_sellid="select sum(quantity) as totsell from ss_transaction where entity_type='1' and status='1' and transaction_type='1' and quote_id='$qid' $append group by quote_id";
					//echo "<br>",$quer_for_sellid;
			$alls=exec_query($quer_for_sellid);
			if(count($alls)>0){
				foreach($alls as $allresultsell){
					$sellqty=$allresultsell['totsell'];
				}
			}
			$buyqty=$allresultbuy['totpurch'];
			$stocksinhand=$buyqty-$sellqty;
			$stockbuys[$qid]=$stocksinhand;
		}
	}
	return $stockbuys;
}
public function formatdate2LocalSCR($creation_date){
	$datetodisplay=explode(" ",$creation_date);
	$datesArray=explode('-',$datetodisplay[0]);
	$year=$datesArray[0];
	$month=$datesArray[1];
	$date=$datesArray[2];
	if($date<10){
		// delete 0 from left
		$datepass=$date;
		$datepass=substr($datepass,1);
	}else{
		$datepass=$date;
	}
	if($month<10){
		// delete 0 from left
		$monthpass=$month;
		$monthpass=substr($monthpass,1);
	}else{
		$monthpass=$month;
	}
	$creation_date = $monthpass."/".$datepass."/".$year;
	return $creation_date;
}
public function formatdate2dbSTD($creation_date){
	$datetodisplay=explode("/",$creation_date);
	$years=$datetodisplay[2];
	$months=$datetodisplay[0];
	$dates=$datetodisplay[1];
	if($dates<10){
		$fdates="0".$dates;
	}else{
		$fdates=$dates;
	}
	if($months<10){
		$monthformt="0".$months;
	}else{
		$monthformt=$months;
	}
	$creation_date = $datetodisplay[2]."-".$monthformt."-".$fdates;
	return $creation_date;
}

public function validateOptionTicker($symbolname,$entitytype)
{
	$tickersymbol=$symbolname;
	if (isset($tickersymbol))
	{
		$valid=1;
		$read=$this->getcurrentquotefeed($tickersymbol,$entitytype);
		IF ($read == 0)
		{
			echo ("The option ticker (\"$tickersymbol\") doesn't appear to be registered<BR></FONT><BR>");

		}
		ELSE
		{
			//echo $read; // returns the current price of the option ticker
			echo '';
		}
	}
}

public function getstockdetails($symbolname,$entitytype)
{
	$tickersymbol=$symbolname;
	$read=$this->getcurrentquoteDetails($tickersymbol,$entitytype);
	return $read;
}

public function getcurrentquoteDetails($tickersymbol,$entitytype="")
{
	if (isset($tickersymbol))
	{
		$handle = fopen("http://download.finance.yahoo.com/d/quotes.csv?s=$tickersymbol&f=sl1d1t1c1ohgvn&e=.csv", "r");
		if(isset($handle))
		{
			$read = fread($handle, 2000);
			fclose($handle);
			$strreplaceread = str_replace("\"", "", $read);
			$read = explode(",", $strreplaceread);
			/*
			for valid
			==========
			<pre>
			Array([0] => MQFOC.X [1] => 0.42 [2] => 3/2/2009 [3] => 4:00pm [4] => 0.00 [5] => 0.38 [6] => 0.45 [7] => 0.34 [8] => 9249 [9] => MSFT Mar 2009 15.)
			</pre>

			for Invalid
			==========
			<pre>Array
			Array([0] => DSFDSF.X  [1] => 0.00 [2] => N/A [3] => 3:07pm [4] => N/A [5] => N/A [6] => N/A [7] => N/A [8] => N/A [9] => DSFDSF.X
			</pre>
			*/
			return $read;
		}else
		{
			return $read;
		}
	}
}
public function getcurrentquotefeed($tickersymbol,$entitytype="")
{
	if (isset($tickersymbol))
	{
		$getStockData=xml2array("http://feeds.financialcontent.com/XMLREST?Account=minyanville&Ticker=".$tickersymbol);
		if($getStockData)
		{
			return $getStockData['Quote']['Record']['Last'];
		}else{}
	}
}

public function getQuoteDetails($tickersymbol,$entitytype="")
{
	if (isset($tickersymbol))
	{
		$getStockData=xml2array("http://feeds.financialcontent.com/XMLREST?Account=minyanville&Ticker=".$tickersymbol);
		if($getStockData)
		{
			return $getStockData['Quote']['Record'];
		}else{}
	}
}

public function validateBaseStock($symbolname,$entitytype)
{
	$getidfromstocks="select id from ex_stock where stocksymbol= trim('".$symbolname."')";
	$allressym='';
	$sid=0;
	$allressym=exec_query($getidfromstocks);
	if(count($allressym)>0)
	{
		foreach($allressym as $allresultsym)
		{
			$sid=$allresultsym['id'];
		}
	}
	/*
	if the stock is not listed in our db then go for
	yahoo to check and enter the result in our db
	*/
	if($sid==0)
	{
		$getStockDeatils = $this->getstockdetails($symbolname,$entitytype);
		//htmlprint_r($getStockDeatils);
		if(is_array($getStockDeatils)&&( $getStockDeatils[1]!='0.00'))
		{
			# Yahoo returns valid array without st.exchange name
			# We will fetch this array and store in our local ex_stock table and will return the inserted id
			$stocktabldata=array('stocksymbol'=>$getStockDeatils[0],'exchange'=>'NASDAQ','CompanyName'=>addslashes(trim($getStockDeatils[9])));
			//htmlprint_r($stocktabldata);
			$sid=insert_query("ex_stock",$stocktabldata);
			return $sid;
		}
		else
		{
			return $sid;
		}
	}
	return $sid;
}
}
?>