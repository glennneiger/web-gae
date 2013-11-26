<?
class Qtransaction{
	function getalldetailsofSymbol($exchangename,$serchsymname){
		global $IMG_SERVER;
	// if the symbol is valid
	if($this->getSymbolid($exchangename,$serchsymname)!=0){
	// Get the transaction details of this symbol
		$symbid=$this->getSymbolid($exchangename,$serchsymname);
		$sqlfortransdetail="select id,creation_date,unit_price,quantity from ss_transaction where entity_type='0' and transaction_type='0' and quote_id='$symbid' and status='1'";
		//echo $sqlfortransdetail;
		$allressym='';
		$allressym=exec_query($sqlfortransdetail);
		$totalrecds=0;
		if(count($allressym)>0){
			$fname="symboltransa";
			$strdisp='<form name="symboltransa" id="symboltransa" method="POST">
				<label align="left"><strong>Company : <font color="">'.$this->getcomapnyname($symbid).'</font></strong></label><br/><br />';
				$strdisp.='<table border="0" width="100%" style="border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" cellspacing="0" cellpadding="5" border=0 bordercolor="black" class="quintportfolio">
				<input type="hidden" name="quoteidserched" id="quoteidserched" value="'.$symbid.'"></font></strong>';
				$strdisp.='<tr><td width="10%" class="quintportfolio"><nobr>Date</td><td width="8%" class="quintportfolio">Shares</td><td width="9%" class="quintportfolio">Price </td><td width="9%" class="quintportfolio">Sell Qty </td><td width="7%" class="quintportfolio">Sell Price </td><td width="20%" class="quintportfolio">Sell Date</td><td width="30%" class="quintportfolio">Notes</td><td width="7%" class="quintportfolio">Select</td></tr>';
			foreach($allressym as $alltransres){
				$totalrecds++;
				$pid=$alltransres['id'];
				$purcgdate=$alltransres['creation_date'];
				$unitprice=$alltransres['unit_price'];
				$qty=$alltransres['quantity'];

				$getprevsellqty=0;
				//$qyt=$qty-$getprevsellqty;
				$selltransactionarr1=$this->stocksales($pid);
				$qty=$qty-$selltransactionarr1[$pid];
				$cnt++;
				if($cnt%2==0){$tog="#FFFFFF";}
				else { $tog="#EFEFEF"; }
				$strdisp.='<tr bgcolor="'.$tog.'"><td><nobr><input type="hidden" id="hid_'.$pid.'" value="'.$pid.'">'.$this->formatdate2LocalSCR($purcgdate).'<input type="hidden" name="buydate_'.$pid.'" id="buydate_'.$pid.'"  value="'.$this->formatdate2LocalSCR($purcgdate).'"></td><td>'.$qty.'<input type="hidden" name="buyqty_'.$pid.'" id="buyqty_'.$pid.'"  value="'.$qty.'"></td><td>'.$unitprice.'</td><td>
				<input type="text" id="sellqty_'.$pid.'" name="sellqty_'.$pid.'" value="0" size="8" maxlength="20"  onKeyup=javascript:chknosvalidation("'.$fname.'",this)></td>
				<td><input type="text" size="8" maxlength="25"  name="sellprice_'.$pid.'" id="sellprice_'.$pid.'" value="0.00" onKeyup=javascript:chknosvalidation("'.$fname.'",this)></td><td>';
				$strdisp.='<input type="text" size="10" name="selldate_'.$pid.'" id="selldate_'.$pid.'" value="" readonly>&nbsp;';
				$str="selldate_".$pid;
				$strdisp.="<a href=\"javascript:NewCal('$str','mmddyyyy')\">";
				$strdisp.='<img src="'.$IMG_SERVER.'/images/quint_images/cal.gif" alt="Pick a date" width="16" height="16" hspace="1" border="0px" align="absmiddle" name="img_'.$pid.'" id="img_'.$pid.'" ></a></td>
					<td><input name="sellnote_'.$pid.'" id="sellnote_'.$pid.'"  type="text" size="35" maxlength="500"></td><td><input type="checkbox" name="sellchk_'.$pid.'" id="sellchk_'.$pid.'" style="border:0px" value="'.$pid.'" onClick="javascript:validateentries('.$pid.')"></td>';
				$strdisp.='</tr>';

				if($stockid_get_all1==''){
					$stockid_get_all1=$pid;
				}else{
					$stockid_get_all1=$stockid_get_all1.",".$pid;
				}

			}
			$strdisp.='</table>';
				if($totalrecds>0){
				$strdisp.='<p><img src="'.$IMG_SERVER.'/images/quint_images/btn-savechanges.gif" align="left" onclick="javascript:saveselltransactions();"/></p>';
				}
				$strdisp.='<input type="hidden" name="serchsymname" value='.$serchsymname.'><input type="hidden" name="exchangename" value='.$exchangename.'><input type="hidden" name="totrecords" value='.$totalrecds.'><input type="hidden" name="stockid_get_all1" value='.$stockid_get_all1.'><input type="hidden" id="mode"  name="mode" value="'.$updatemode.'"></form>';
				return $strdisp;
		}else if(count($allressym)==0){
		  echo "<b><font color=red>You have nothing to sell against this symbol</font></b>";
		 }

	}
	// else if the symbol is invalid as per our db
	else if($this->getSymbolid($exchangename,$serchsymname)==0){
		echo "<b>Entered Symbol is not Listed.....</b>";
	}
	}

function getSymbolid($exchangename,$serchsymname){
// We have the exchange name and symbol value
// get the symbol id from ex_stock and search in transaction table
	$getidfromstocks="select id from ex_stock where stocksymbol= trim('".$serchsymname."') and exchange=trim('".$exchangename."')";
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
function getstockdetails($symbolname,$entitytype)
{
	$tickersymbol=$symbolname;
	$read=getcurrentquotefeed($tickersymbol,$entitytype);
	return $read;
}

function validatesymbol($symbolname,$entitytype){
$tickersymbol=$symbolname;
if (isset($tickersymbol))
{
	$valid=1;
	$read=getcurrentquotefeed($tickersymbol,$entitytype);
	IF ($read == 0)
	{
		//***echo ("The symbol (\"$tickersymbol\") doesn't appear to be registered<BR></FONT><BR>");
		$valid=0;
		$crntval=$read;
		return $valid."~".$crntval;
	}
	ELSE
	{
		$valid=1;
		$crntval=$read;
		return $valid."~".$crntval;
	}
}
}


function getcurrentquote($symbolname,$entitytype){
	// If the stock symbol is a valid symbol then get the value
	$chkvaliditystr=$this->validatesymbol($symbolname,$entitytype);
	$chkvalidityarr=explode('~',$chkvaliditystr);
		if($chkvalidityarr['0']==1){
		return $chkvalidityarr['1'];
		}else if($chkvalidityarr['0']==0){
		return $chkvalidityarr['1'];
		}
}
// This function returns the array of total stocks in hand by indexing the quote_id
	function getallbuystocks($dates=NULL){
		if(isset($dates)){
			$append=" and creation_date>='$dates'";
		}else{
			$append='';
		}
		$quer_for_buyids="select quote_id,sum(quantity) as totpurch from ss_transaction where entity_type='0' and status='1' and transaction_type='0' $append group by quote_id";
		//echo "<br>".$quer_for_buyids;

		$allr=exec_query($quer_for_buyids);
		if(count($allr)>0){
			$stockbuys=array();
			foreach($allr as $allresultbuy){
				$qid=$allresultbuy['quote_id'];
					// chek for rotal sell amt
					$sellqty=0;
					$quer_for_sellid="select sum(quantity) as totsell from ss_transaction where entity_type='0' and status='1' and transaction_type='1' and quote_id='$qid' $append group by quote_id";
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


// This function returns the array of total options in hand by indexing the quote_id
	function getallbuystocksoptions($dates=NULL){
		if(isset($dates)){
			$append=" and creation_date>='$dates'";
		}else{
			$append='';
		}
		$quer_for_buyids="select quote_id,sum(quantity) as totpurch from ss_transaction where entity_type='1' and status='1' and transaction_type='0' $append group by quote_id";
		// echo "<br>".$quer_for_buyids;

		$allr=exec_query($quer_for_buyids);
		if(count($allr)>0){
			$stockbuys=array();
			foreach($allr as $allresultbuy){
				$qid=$allresultbuy['quote_id'];
					// chek for rotal sell amt
					$sellqty=0;
					$quer_for_sellid="select sum(quantity) as totsell from ss_transaction where entity_type='1' and status='1' and transaction_type='1' and quote_id='$qid' $append group by quote_id";
					// echo "<br>",$quer_for_sellid;
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




// returns total stock inhand for short sell buy to cover for openposition companywise
function getallshortselltocks(){

		$query_for_shortsellids="select quote_id,sum(quantity) as totpurch from ss_transaction where entity_type='0' and status='1' and transaction_type='2' $append group by quote_id";
		// echo "<br>".$query_for_shortsellids;
		$allr=exec_query($query_for_shortsellids);
		if(count($allr)>0){
			$stocksellshort=array();
			foreach($allr as $allresultsortsell){
				$qid=$allresultsortsell['quote_id'];
				// echo "<br>",$qid;
					// chek for rotal sell amt
					$buytocoverqty=0;
					$query_for_buytocoverid="select sum(quantity) as totbuytocover from ss_transaction where entity_type='0' and status='1' and transaction_type='3' and quote_id='$qid' group by quote_id";
					// echo "<br>",$query_for_buytocoverid;
					$alls=exec_query($query_for_buytocoverid);
						if(count($alls)>0){
							foreach($alls as $allresultbuytocover){
								$buytocoverqty=$allresultbuytocover['totbuytocover'];
							}
						}
			$sortsellqty=$allresultsortsell['totpurch'];
			$stocksinhand=$sortsellqty-$buytocoverqty;
			$stocksellshort[$qid]=$stocksinhand;
			}
		}
		return $stocksellshort;
	}


// returns total options inhand for short sell buy to cover for openposition companywise
function getallshortselloptionstocks(){

		$query_for_shortsellids="select quote_id,sum(quantity) as totpurch from ss_transaction where entity_type='1' and status='1' and transaction_type='2' $append group by quote_id";
		// echo "<br>".$query_for_shortsellids;
		$allr=exec_query($query_for_shortsellids);
		if(count($allr)>0){
			$stocksellshort=array();
			foreach($allr as $allresultsortsell){
				$qid=$allresultsortsell['quote_id'];
				// echo "<br>",$qid;
					// chek for rotal sell amt
					$buytocoverqty=0;
					$query_for_buytocoverid="select sum(quantity) as totbuytocover from ss_transaction where entity_type='1' and status='1' and transaction_type='3' and quote_id='$qid' group by quote_id";
					// echo "<br>",$query_for_buytocoverid;
					$alls=exec_query($query_for_buytocoverid);
						if(count($alls)>0){
							foreach($alls as $allresultbuytocover){
								$buytocoverqty=$allresultbuytocover['totbuytocover'];
							}
						}
			$sortsellqty=$allresultsortsell['totpurch'];
			$stocksinhand=$sortsellqty-$buytocoverqty;
			$stocksellshort[$qid]=$stocksinhand;
			}
		}
		return $stocksellshort;
	}





// This function returns the total stocks in hand by passing the quote_id
	function getparticularstocksinh($qid){
		$qry_for_buyqty="select sum(quantity) as totpurch from ss_transaction where entity_type='0' and status='1' and transaction_type='0' and quote_id='$qid' group by quote_id";
		$allr=exec_query($qry_for_buyqty);
		if(count($allr)>0){
			$stocksinhand=0;
			foreach($allr as $allresultbuy){

				$sellqty=0;
				$qry_for_sellid="select sum(quantity) as totsell from ss_transaction where entity_type='0' and status='1' and transaction_type='1' and quote_id='$qid' group by quote_id";
				$alls=exec_query($qry_for_sellid);
				if(count($alls)>0){
					foreach($alls as $allresultsell){
						$sellqty=$allresultsell['totsell'];
					}
				}
				$buyqty=$allresultbuy['totpurch'];
				$stocksinhand=$buyqty-$sellqty;
			}
		}
		return $stocksinhand;
	}


# This function returns the avg unit price of particular quote_id used in edittransaction/ss_selltransaction.htm page
function avgunitpriceofstockinhand($qid){
		$quer_for_buyids="select sum(quantity) as totpurchqty,sum((unit_price)*(quantity)) as totpurchamt  from ss_transaction where entity_type='0' and status='1' and transaction_type='0' and quote_id='$qid' group by quote_id";
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
						$upqry="select QT.unit_price up,QT.id from ss_transaction QT, ss_sell_transaction QST where QT.entity_type='0' and QST.sell_trans_id='$selltransid' and QST.buy_trans_id=QT.id";
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



function getsinglebuystocks(){
		$quer_for_buyids="select QT.id,QT.quote_id,QT.quantity as totpurch from ss_transaction QT, ex_stock ES where QT.entity_type='0' and QT.quote_id=ES.id and QT.status='1' and QT.transaction_type='0' order by ES.companyname";
		$allr=exec_query($quer_for_buyids);
		if(count($allr)>0){
			$stockbuys=array();
			foreach($allr as $allresultbuy){
				$qid=$allresultbuy['id'];
					// chek for rotal sell amt
					$sellqty=0;
					$quer_for_sellid="select QT.quantity from ss_transaction QT, ss_sell_transaction QS where QT.entity_type='0' and QS.buy_trans_id='$qid' and  QS.buy_trans_id=QT.id and QT.transaction_type='1' and QT.status='1'";
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

function getsinglebuystocksOptions(){
		$quer_for_buyids="select QT.id,QT.quote_id,QT.quantity as totpurch from ss_transaction QT, ex_option ES where QT.entity_type='1' and QT.quote_id=ES.id and QT.status='1' and QT.transaction_type='0' order by ES.optionticker";
		$allr=exec_query($quer_for_buyids);
		if(count($allr)>0){
			$stockbuys=array();
			foreach($allr as $allresultbuy){
				$qid=$allresultbuy['id'];
					// chek for rotal sell amt
					$sellqty=0;
					$quer_for_sellid="select QT.quantity from ss_transaction QT, ss_sell_transaction QS where QT.entity_type='1' and QS.buy_trans_id='$qid' and  QS.buy_trans_id=QT.id and QT.transaction_type='1' and QT.status='1'";
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





	function geshortsellstocks(){  // for quantity inhand for shortsell & buytocover
		$shortsellqry="select QT.id,QT.quote_id,QT.quantity as totpurch from ss_transaction QT, ex_stock ES where QT.entity_type='0' and QT.quote_id=ES.id and QT.status='1' and QT.transaction_type='2' order by ES.companyname,QT.creation_date";
		$allr=exec_query($shortsellqry);
		if(count($allr)>0){
			// $stockbuys=array();
			$shortsell=array();
			foreach($allr as $allshortsell){
				$qid=$allshortsell['id'];
					// chek for total sort sell
					$butytocoverqty=0;
					// query for buy to cover
					$query_for_buytocover="select QT.quantity from ss_transaction QT, ss_sell_transaction QS where QT.entity_type='0' and QS.sell_trans_id='$qid' and QS.buy_trans_id=QT.id and QT.transaction_type='3' and QT.status='1'";
					$alls=exec_query($query_for_buytocover);
						if(count($alls)>0){
							foreach($alls as $allresultbuy){
								$butytocoverqty=$butytocoverqty + $allresultbuy['quantity'];
							}
						}

			$shortsellqty=$allshortsell['totpurch'];
			$stocksinhand=$shortsellqty-$butytocoverqty;
			$stockshortsell[$qid]=$stocksinhand;
			}
		}
		return $stockshortsell;
	}

// for options
	function geshortsellstocksoption(){  // for quantity inhand for shortsell & buytocover for options
		$shortsellqry="select QT.id,QT.quote_id,QT.quantity as totpurch from ss_transaction QT, ex_stock ES where QT.entity_type='1' and QT.quote_id=ES.id and QT.status='1' and QT.transaction_type='2' order by ES.companyname,QT.creation_date";
		$allr=exec_query($shortsellqry);
		if(count($allr)>0){
			// $stockbuys=array();
			$shortsell=array();
			foreach($allr as $allshortsell){
				$qid=$allshortsell['id'];
					// chek for total sort sell
					$butytocoverqty=0;
					// query for buy to cover
					$query_for_buytocover="select QT.quantity from ss_transaction QT, ss_sell_transaction QS where QT.entity_type='1' and QS.sell_trans_id='$qid' and QS.buy_trans_id=QT.id and QT.transaction_type='3' and QT.status='1'";
					$alls=exec_query($query_for_buytocover);
						if(count($alls)>0){
							foreach($alls as $allresultbuy){
								$butytocoverqty=$butytocoverqty + $allresultbuy['quantity'];
							}
						}

			$shortsellqty=$allshortsell['totpurch'];
			$stocksinhand=$shortsellqty-$butytocoverqty;
			$stockshortsell[$qid]=$stocksinhand;
			}
		}
		return $stockshortsell;
	}


	function unitpriceofstockinhand($dates=NULL){
	if(isset($dates)){
	$append=" and creation_date>='$dates'";
	}else{
	$append='';
	}

	$quer_for_buyids="select quote_id,sum(quantity) as totpurchqty,sum((unit_price)*(quantity)) as totpurchamt  from ss_transaction where entity_type='0' and status='1' and transaction_type='0' $append group by quote_id";
		$allr=exec_query($quer_for_buyids);
		if(count($allr)>0){
			$stockbuys=array();

			foreach($allr as $allresultbuy){
				$qid=$allresultbuy['quote_id'];
				$totalpurchsdqty=$allresultbuy['totpurchqty']; // Final for each qid
				$totpurchamtget=$allresultbuy['totpurchamt'];

				$totalsoldamt=0;

					// chek for total sell amt
					$sellqty=0;

					$quer_for_sellid="select id, quantity as totsell from ss_transaction where entity_type='0' and status='1' and transaction_type='1' and quote_id='$qid' $append";
					$alls=exec_query($quer_for_sellid);
					$totalsellqty=0;
					if(count($alls)>0){
						     $soldqty=0;
							 $totalsellqty=0;

							foreach($alls as $allresultsell){
							 $selltransid=$allresultsell['id'];
							 $soldqty=$allresultsell['totsell'];
							 	$totalsellqty=$totalsellqty+$soldqty;
        					$upqry="select QT.unit_price up,QT.id from ss_transaction QT, ss_sell_transaction QST where QT.entity_type='0' and QST.sell_trans_id='$selltransid' and QST.buy_trans_id=QT.id";

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
			if(!(($totalpurchsdqty-$totalsellqty)==0))
			$avgunitprice=($totpurchamtget-$totalsoldamt)/($totalpurchsdqty-$totalsellqty);
			else
				$avgunitprice=0;
			$stockbuys[$qid]=$avgunitprice;
			}
		}

	return $stockbuys;

	}

// for options- unit price of stocks

	function unitpriceofstockinhandoptions($dates=NULL){
	if(isset($dates)){
	$append=" and creation_date>='$dates'";
	}else{
	$append='';
	}

	$quer_for_buyids="select quote_id,sum(quantity) as totpurchqty,sum((unit_price)*(quantity)) as totpurchamt  from ss_transaction where entity_type='1' and status='1' and transaction_type='0' $append group by quote_id";
		$allr=exec_query($quer_for_buyids);
		if(count($allr)>0){
			$stockbuys=array();

			foreach($allr as $allresultbuy){
				$qid=$allresultbuy['quote_id'];
				$totalpurchsdqty=$allresultbuy['totpurchqty']; // Final for each qid
				$totpurchamtget=$allresultbuy['totpurchamt'];

				$totalsoldamt=0;

					// chek for total sell amt
					$sellqty=0;

					$quer_for_sellid="select id, quantity as totsell from ss_transaction where entity_type='1' and status='1' and transaction_type='1' and quote_id='$qid' $append";
					$alls=exec_query($quer_for_sellid);
					$totalsellqty=0;
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
			if(!(($totalpurchsdqty-$totalsellqty)==0))
			$avgunitprice=($totpurchamtget-$totalsoldamt)/($totalpurchsdqty-$totalsellqty);
			else
				$avgunitprice=0;
			$stockbuys[$qid]=$avgunitprice;
			}
		}

	return $stockbuys;

	}





// for stocks
 function costbasispershareforshortsell(){

  $query_for_shortsellids="select quote_id,sum(quantity) as totshortsellqty,sum((unit_price)*(quantity)) as totshortsellamt  from ss_transaction where entity_type='0' and status='1' and transaction_type='2' group by quote_id";

		$allr=exec_query($query_for_shortsellids);
		if(count($allr)>0){
			$stockbuys=array();
           $avgunitprice=0;
		   $totalbuyamt=0;
		   $totshortsellamtget=0;
		   $totalbuytocoverqty=0;
		   $totshortsellqty=0;
			foreach($allr as $allresultshortsell){
				$qid=$allresultshortsell['quote_id'];
				$totshortsellqty=$allresultshortsell['totshortsellqty']; // Final for each qid
				$totshortsellamtget=$allresultshortsell['totshortsellamt'];

				$totalbuyamt=0;

				$query_for_buytocoverid="select id, quantity as totbuytocover from ss_transaction where entity_type='0' and status='1' and transaction_type='3' and quote_id='$qid'";
					$alls=exec_query($query_for_buytocoverid);
					$buytocoverqty=0;
					$totalbuytocoverqty=0;
					if(count($alls)>0){
						$buytocoverqty=0;

						foreach($alls as $allresultbuytocover){
							 $buytocovertransid=$allresultbuytocover['id'];
							 $buytocoverqty=$allresultbuytocover['totbuytocover'];
							 $totalbuytocoverqty=$totalbuytocoverqty+$buytocoverqty;
        					 // $upqry="select QT.unit_price up,QT.id from ss_transaction QT, ss_sell_transaction QST where QST.buy_trans_id='$buytocovertransid' and QST.buy_trans_id=QT.id";
							 $upqry="select unit_price up from ss_transaction where entity_type='0' and id in (select sell_trans_id from ss_sell_transaction where buy_trans_id='$buytocovertransid')";
							 $upbuy=exec_query($upqry);
							 if(count($upbuy)>0) {
								 $buytocoverdat=0;
								 foreach($upbuy as $upall){
								 $buytocoverdat=$buytocoverqty*$upall['up'];
								 $totalbuyamt=$totalbuyamt+$buytocoverdat;
							  }
						}
				   }
			}

			 // echo "<br>",$totshortsellamtget.'-'.$totalbuyamt.'-'.$totshortsellqty.'-'.$totalbuytocoverqty;
			// $avgunitprice=($totalbuyamt-$totshortsellamtget)/($totalbuytocoverqty-$totshortsellqty);
			if(!(($totshortsellqty-$totalbuytocoverqty)==0))
			 $avgunitprice=($totshortsellamtget-$totalbuyamt)/($totshortsellqty-$totalbuytocoverqty);
			else
				$avgunitprice==0;
             $stockbuys[$qid]=$avgunitprice;
			}
		}

	return $stockbuys;

	}


// for options
 function costbasispershareforshortselloptions(){

  $query_for_shortsellids="select quote_id,sum(quantity) as totshortsellqty,sum((unit_price)*(quantity)) as totshortsellamt  from ss_transaction where entity_type='1' and status='1' and transaction_type='2' group by quote_id";

		$allr=exec_query($query_for_shortsellids);
		if(count($allr)>0){
			$stockbuys=array();
           $avgunitprice=0;
		   $totalbuyamt=0;
		   $totshortsellamtget=0;
		   $totalbuytocoverqty=0;
		   $totshortsellqty=0;
			foreach($allr as $allresultshortsell){
				$qid=$allresultshortsell['quote_id'];
				$totshortsellqty=$allresultshortsell['totshortsellqty']; // Final for each qid
				$totshortsellamtget=$allresultshortsell['totshortsellamt'];
				$totalbuyamt=0;
				$query_for_buytocoverid="select id, quantity as totbuytocover from ss_transaction where entity_type='1' and status='1' and transaction_type='3' and quote_id='$qid'";
					$alls=exec_query($query_for_buytocoverid);
					$buytocoverqty=0;
					$totalbuytocoverqty=0;
					if(count($alls)>0){
						$buytocoverqty=0;

						foreach($alls as $allresultbuytocover){
							 $buytocovertransid=$allresultbuytocover['id'];
							 $buytocoverqty=$allresultbuytocover['totbuytocover'];
							 $totalbuytocoverqty=$totalbuytocoverqty+$buytocoverqty;
        					 // $upqry="select QT.unit_price up,QT.id from ss_transaction QT, ss_sell_transaction QST where QST.buy_trans_id='$buytocovertransid' and QST.buy_trans_id=QT.id";
							 $upqry="select unit_price up from ss_transaction where entity_type='1' and id in (select sell_trans_id from ss_sell_transaction where buy_trans_id='$buytocovertransid')";
							 $upbuy=exec_query($upqry);
							 if(count($upbuy)>0) {
								 $buytocoverdat=0;
								 foreach($upbuy as $upall){
								 $buytocoverdat=$buytocoverqty*$upall['up'];
								 $totalbuyamt=$totalbuyamt+$buytocoverdat;
							  }
						}
				   }
			}

			 // echo "<br>",$totshortsellamtget.'-'.$totalbuyamt.'-'.$totshortsellqty.'-'.$totalbuytocoverqty;
			// $avgunitprice=($totalbuyamt-$totshortsellamtget)/($totalbuytocoverqty-$totshortsellqty);
			if(!(($totshortsellqty-$totalbuytocoverqty)==0))
			 $avgunitprice=($totshortsellamtget-$totalbuyamt)/($totshortsellqty-$totalbuytocoverqty);
			else
				$avgunitprice==0;
             $stockbuys[$qid]=$avgunitprice;
			}
		}

	return $stockbuys;

	}


	function tradeunitpriceofstockinhand(){
//	$stockpricearr=array(63=>23.50,5=>25.50);
	$quer_for_buyids="select QT.id,QT.quote_id,QT.quantity as totpurchqty,((QT.unit_price)*(QT.quantity)) as totpurchamt  from ss_transaction QT, ex_stock ES where status='1' and QT.entity_type='0' and QT.transaction_type='0' and QT.quote_id=ES.id order by ES.companyname";
		$allr=exec_query($quer_for_buyids);
		if(count($allr)>0){
			$stockbuys=array();

			foreach($allr as $allresultbuy){

				$qid=$allresultbuy['quote_id'];
					// chek for rotal sell amt
					$sellqty=0;

					$quer_for_sellid="select quantity as totsell from ss_transaction where entity_type='0' and status='1' and transaction_type='1' and quote_id='$qid' group by quote_id";
					//echo "<br>".$quer_for_sellid;
					$alls=exec_query($quer_for_sellid);
						if(count($alls)>0){
							foreach($alls as $allresultsell){
								$sellqty=$allresultsell['totsell'];
							}
						}
			$buyqty=$allresultbuy['totpurchqty'];
			$stocksinhand=$buyqty-$sellqty;
			$totalbuyamt=$allresultbuy['totpurchamt'];
			if($stocksinhand!=0){			$avgunitprice=$totalbuyamt/$stocksinhand;}
			$stockbuys[$qid]=$avgunitprice;
			}
		}

	return $stockbuys;

	}

// for options
function tradeunitpriceofstockinhandoption(){
//	$stockpricearr=array(63=>23.50,5=>25.50);
	$quer_for_buyids="select QT.id,QT.quote_id,QT.quantity as totpurchqty,((QT.unit_price)*(QT.quantity)) as totpurchamt  from ss_transaction QT, ex_stock ES where status='1' and QT.entity_type='1' and QT.transaction_type='0' and QT.quote_id=ES.id order by ES.companyname";
		$allr=exec_query($quer_for_buyids);
		if(count($allr)>0){
			$stockbuys=array();

			foreach($allr as $allresultbuy){

				$qid=$allresultbuy['quote_id'];
					// chek for rotal sell amt
					$sellqty=0;

					$quer_for_sellid="select quantity as totsell from ss_transaction where entity_type='1' and status='1' and transaction_type='1' and quote_id='$qid' group by quote_id";
					//echo "<br>".$quer_for_sellid;
					$alls=exec_query($quer_for_sellid);
						if(count($alls)>0){
							foreach($alls as $allresultsell){
								$sellqty=$allresultsell['totsell'];
							}
						}
			$buyqty=$allresultbuy['totpurchqty'];
			$stocksinhand=$buyqty-$sellqty;
			$totalbuyamt=$allresultbuy['totpurchamt'];
			if($stocksinhand!=0){			$avgunitprice=$totalbuyamt/$stocksinhand;}
			$stockbuys[$qid]=$avgunitprice;
			}
		}

	return $stockbuys;
}


# This function returns the array of all symbols name
	function allsymbolsarr(){
		$quer_for_allsym="select id,stocksymbol from ex_stock";
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


	function allsymbolscomapnyarr(){
		$quer_for_all="select id,companyname from ex_stock";
		$allrescomp=exec_query($quer_for_all);
		if(count($allrescomp)>0){
			$id_comname=array();

			foreach($allrescomp as $allresultcomp){
				$storeid=$allresultcomp['id'];
				$companyname=$allresultcomp['companyname'];
				$id_comname[$storeid]=$companyname;

			}
			return $id_comname;
		}
	}
	function getcomapnyname($autoid_stock){
		$quer_for_all="select companyname from ex_stock where id='$autoid_stock'";
		$allrescomp=exec_query($quer_for_all);
		if(count($allrescomp)>0){
			foreach($allrescomp as $allresultcomp){
				$companyname=$allresultcomp['companyname'];
			}
			return $companyname;
		}
	}

	function alltransactionediterr($msg,$start=NULL,$end=NULL){
		global $IMG_SERVER;
		$errmsg=explode('-',$msg);
		$errstockid=$errmsg[1];

		global $transactioncnt;

		if((!isset($start)) && (!isset($end))){
			$start=0;
			$end=0+$transactioncnt;
			$lmt='';
			$sratindx=0;
			$endindx=0+$transactioncnt;
		}
		else{
			$startlmt=$start;
			$endlmt=$end;
			$sratindx=$start;
			$endindx=$end;
			$lmt=" LIMIT ".$startlmt.",".$endlmt;
		}

		$id_stockname=$this->allsymbolsarr();
		$id_comname=$this->allsymbolscomapnyarr();

		$fixedtransaction="select id,quote_id,transaction_type,creation_date,unit_price,quantity,description from ss_transaction  where status='1' order by creation_date DESC";
		$fixedtransaction1 = exec_query($fixedtransaction);
		$fixedresults=count($fixedtransaction1);

		$qry_transaction="select id,quote_id,transaction_type,creation_date,unit_price,quantity,description from ss_transaction  where status='1' order by creation_date DESC $lmt";
		//echo "<br>FROM CALSS FILE : ".$qry_transaction."<br>";

		$execqry = exec_query($qry_transaction);
		$cntLT=count($execqry);

		if(count($execqry)>0){
			$strout='';
			$strout='<div id="trans_detail"><form name="edittransactionfrm" id="edittransactionfrm" method="POST" action="">
				<table style="border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" width="100%" cellspacing="0" cellpadding="2" bordercolor=black>
				<tr>
				<td class="quintportfolio">Symbol</td>
				<td class="quintportfolio">Name</td>
				<td class="quintportfolio">Type</td>
				<td class="quintportfolio">Date</td>
				<td class="quintportfolio">Shares</td>
				<td class="quintportfolio">Price </td>
				<td class="quintportfolio">Notes</td>
				<td class="quintportfolio">Remove</td>
				</tr>
				';
			$cntlnk=0;
			foreach(exec_query($qry_transaction) as $result){
				$cnt++;
				if($cnt%2==0){$tog="#FFFFFF";}
				else { $tog="#EFEFEF"; }
				$quote_get=$result['quote_id'];
				$stockid_get=$result['id'];
				$transaction_type_get=$result['transaction_type'];
				$creation_date_get=$result['creation_date'];
				$unit_price_get=$result['unit_price'];
				$quantity_get=$result['quantity'];
				$notes_get=$result['description'];
				if($stockid_get_all==''){
					$stockid_get_all=$stockid_get;
				}else{
					$stockid_get_all=$stockid_get_all.",".$stockid_get;
				}

				$creation_date_get=$this->formatdate2LocalSCR($creation_date_get);



				$strout.='<tr bgcolor="'.$tog.'"><td>'.$id_stockname[$quote_get].'</td><td>'.$id_comname[$quote_get].'</td>';
				$strout.='<td><select name="combo_'.$stockid_get.'" disabled>';
				$strout.='<option value="0"';
				if(0==$transaction_type_get){ $strout.=' selected ';}
				$strout.='>Buy</option>';
				$strout.='<option value="1"';
				if(1==$transaction_type_get){$strout.='selected';}
				$strout.='>Sell</option>
						</select>';
				$strout.='</td><td><input type="text" size="10" name="creatdate_'.$stockid_get.'" id="creatdate_'.$stockid_get.'" value="'.$creation_date_get.'" readonly>';
				$str="creatdate_".$stockid_get;
				//$strout.='<a href="javascript:NewCal("creatdate_'.$stockid_get.","mmddyyyy")>';
				$strout.="&nbsp;<a href=\"javascript:NewCal('$str','mmddyyyy')\">";
				$strout.='<img src="'.$IMG_SERVER.'/images/quint_images/cal.gif" width="16" height="16" align="absmiddle" border="0px" alt="Pick a date"></a>';
				$strout.='</td><td>';
				$strout.='<input size="8" type="text" name="shares_'.$stockid_get.'" id="shares_'.$stockid_get.'" value="'.$quantity_get.'" onBlur="javascript:chknosonly(\'shares_'.$stockid_get.'\');">';
				$strout.='</td><td>';
				$strout.='<input size="8" type="text" name="price_'.$stockid_get.'" id="price_'.$stockid_get.'"  value="'.$unit_price_get.'" onBlur="javascript:chknosonly(\'price_'.$stockid_get.'\');">';
				$strout.='</td><td>';
				$strout.='<input type="text" size="40" name="notes_'.$stockid_get.'" value="'.$notes_get.'">';
				$strout.='</td>';
				$strout.='<td align="center"><input type="checkbox" style="border:0px" name="selectids_'.$stockid_get.'" value="'.$stockid_get.'"></td></tr>';
				$cntlnk++;
				$chkres=$cntlnk+$start;

				if(($chkres<$fixedresults) && ($cntlnk==$transactioncnt)){
					$strout.='<tr><td colspan="8" style="padding-top:2px;"><table border="0" cellspacing=0 cellpadding=0 width="100%"><td width="50%" style="padding-left:10px;" align="left">';
					if($start!=0){
						$strout.='<a class="viewnav" style="cursor:pointer;padding-left:10px;" onClick=javascript:makeprevlinks("'.$start.'","'.$end.'")>&lt; Previous</a>';
					}
					$strout.='</td><td width="50%" style="padding-right:10px;" align="right"><a class="viewnav"  style="cursor:pointer;" onClick=javascript:makenextlinks("'.$start.'","'.$end.'")>Next ></a></td></tr></table></td></tr>';
					$totrecds=$cntlnk;
					break;
				}
				else if(($chkres>=$fixedresults)){
					$strout.='<tr><td colspan="8" style="padding-top:5px;"><table border="0" cellspacing=0 cellpadding=0 width="100%">
						<td width="50%" style="padding-left:10px;" align="left">';
					if($start!=0){
						$strout.='<a class="viewnav"  style="cursor:pointer;padding-left:10px;" onClick=javascript:makeprevlinks("'.$start.'","'.$end.'")>&lt; Previous</a>';
					}
					$strout.='&nbsp;</td>
							<td width="50%" style="padding-right:10px;" align="right">
						&nbsp;</td></tr></table></td></tr>';
					$totrecds=$cntlnk;
					break;
					}
				}
				$strout.='</table>';
				if($cntLT>0){
					$strout.='<table><tr><td>
							<br><img src="'.$IMG_SERVER.'/images/quint_images/btn-savechanges.gif" align="left" onclick="javascript:updatefilds();"/>
						</td></tr></table>';
					}
					$strout.='<input type="hidden" name="totrecords" value='.$totrecds.'><input type="hidden" name="stockid_get_all" value='.$stockid_get_all.'><input type="hidden" name="sratindx" value='.$sratindx.'><input type="hidden" name="endindx" value='.$endindx.'></form><br><font color=red>'.$errmsg[0].'</font></div>';
				}else{
					$strout='<div id="trans_detail"><b><font color="#081E33"><br/>Make Your First Transaction!!<input type="hidden" id="totbtcrec" name="totbtcrec" value="0" /></font></b></div>';
				}
				return $strout;
				}

public	function alltransactionedit($start=null,$end=null){
		global $transactioncnt,$IMG_SERVER;
		if((!isset($start)) && (!isset($end))){
			$start=0;
			$end=0+$transactioncnt;
			$lmt='';
			$sratindx=0;
			$endindx=0+$transactioncnt;
		}
		else{
			$startlmt=$start;
			$endlmt=$end;
			$sratindx=$start;
			$endindx=$end;
			$lmt=" LIMIT ".$startlmt.",".$endlmt;
			}

		$id_stockname=$this->allsymbolsarr();
		$id_comname=$this->allsymbolscomapnyarr();

		$fixedtransaction="select id,quote_id,transaction_type,creation_date,unit_price,quantity,description from ss_transaction  where entity_type='0' and status='1' order by creation_date DESC";
		$fixedtransaction1 = exec_query($fixedtransaction);
		$fixedresults=count($fixedtransaction1);

		$qry_transaction="select id,quote_id,transaction_type,creation_date,unit_price,quantity,description from ss_transaction  where entity_type='0' and status='1' order by creation_date DESC $lmt";
		///echo "<br>FROM CALSS FILE : ".$qry_transaction."<br>";

		$execqry = exec_query($qry_transaction);
		$cntLT=count($execqry);

		if(count($execqry)>0){
			$strout='';
			$strout='<div id="trans_detail"><form name="edittransactionfrm" id="edittransactionfrm" method="POST" action="">
				<table style="border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" width="100%" cellspacing="0" cellpadding="2" bordercolor=black>
				<tr>
				<td class="quintportfolio">Symbol</td>
				<td class="quintportfolio">Name</td>
				<td class="quintportfolio">Type</td>
				<td class="quintportfolio">Date(mm/dd/yyyy)</td>
				<td class="quintportfolio">Shares</td>
				<td class="quintportfolio">Price </td>
				<td class="quintportfolio">Notes</td>
				<td class="quintportfolio">Remove</td>
				</tr>
				';
			$cntlnk=0;
			foreach(exec_query($qry_transaction) as $result){
				$cnt++;
				if($cnt%2==0){$tog="#FFFFFF";}
				else { $tog="#EFEFEF"; }
				$quote_get=$result['quote_id'];
				$stockid_get=$result['id'];
				$transaction_type_get=$result['transaction_type'];
				$creation_date_get=$result['creation_date'];
				$unit_price_get=$result['unit_price'];
				$quantity_get=$result['quantity'];
				$notes_get=$result['description'];

				if($stockid_get_all==''){
					$stockid_get_all=$stockid_get;
				}else{
				$stockid_get_all=$stockid_get_all.",".$stockid_get;
				}
				$creation_date_get=$this->formatdate2LocalSCR($creation_date_get);
				$strout.='<tr bgcolor="'.$tog.'"><td>'.$id_stockname[$quote_get].'<input type="hidden" name="qtid_'.$stockid_get.'" id="qtid_'.$stockid_get.'" value="'.$quote_get.'" ></td><td>'.$id_comname[$quote_get].'</td>';
				$strout.='<td><input type="hidden" name="hcombo_'.$stockid_get.'" id="hcombo_'.$stockid_get.'" value="'.$transaction_type_get.'"><select name="combo_'.$stockid_get.'" disabled>';
				$strout.='<option value="0"';
				if(0==$transaction_type_get){ $strout.=' selected ';}
				$strout.='>Buy</option>';
				$strout.='<option value="1"';
				if(1==$transaction_type_get){$strout.='selected';}
				$strout.='>Sell</option>';
				$strout.='<option value="2"';
				if(2==$transaction_type_get){$strout.='selected';}
				$strout.='>Short Sell</option>';
				$strout.='<option value="3"';
				if(3==$transaction_type_get){$strout.='selected';}
				$strout.='>Buy to cover</option></select>';
				$strout.='</td><td><input type="text" size="10" name="creatdate_'.$stockid_get.'" id="creatdate_'.$stockid_get.'" value="'.$creation_date_get.'" readonly>';
				$str="creatdate_".$stockid_get;
				//$strout.='<a href="javascript:NewCal("creatdate_'.$stockid_get.","mmddyyyy")>';
				$strout.="&nbsp;<a href=\"javascript:NewCal('$str','mmddyyyy')\">";
				$strout.='<img src="'.$IMG_SERVER.'/images/quint_images/cal.gif" width="16" height="16" align="absmiddle" border="0px" alt="Pick a date"></a>';
				$strout.='</td><td>';
				$strout.='<input size="8" type="text" name="shares_'.$stockid_get.'" id="shares_'.$stockid_get.'" value="'.$quantity_get.'" onBlur="javascript:chknosonly(\'shares_'.$stockid_get.'\');" readonly>';
				$strout.='</td><td>';
				$strout.='<input size="8" type="text" name="price_'.$stockid_get.'" id="price_'.$stockid_get.'"  value="'.$unit_price_get.'" onBlur="javascript:chknosonly(\'price_'.$stockid_get.'\');">';
				$strout.='</td><td>';
				$strout.='<input type="text" size="40" name="notes_'.$stockid_get.'" value="'.$notes_get.'">';
				$strout.='</td>';
				$strout.='<td align="center"><input type="checkbox" style="border:0px" name="selectids_'.$stockid_get.'" value="'.$stockid_get.'"></td></tr>';
				$cntlnk++;
				$chkres=$cntlnk+$start;

				if(($chkres<$fixedresults) && ($cntlnk==$transactioncnt)){
					$strout.='<tr><td colspan="8" style="padding-top:2px;"><table border="0" cellspacing=0 cellpadding=0 width="100%"><td width="50%" style="padding-left:10px;" align="left">';
					if($start!=0){
						$strout.='<a class="viewnav" style="cursor:pointer;padding-left:10px;" onClick=javascript:makeprevlinks("'.$start.'","'.$end.'")>&lt; Previous</a>';
					}
					$strout.='</td><td width="50%" style="padding-right:10px;" align="right"><a class="viewnav"  style="cursor:pointer;" onClick=javascript:makenextlinks("'.$start.'","'.$end.'")>Next ></a></td></tr></table></td></tr>';
					$totrecds=$cntlnk;
					break;
				}
				else if(($chkres>=$fixedresults)){
						$strout.='<tr><td colspan="8" style="padding-top:5px;"><table border="0" cellspacing=0 cellpadding=0 width="100%">
						<td width="50%" style="padding-left:10px;" align="left">';
						if($start!=0){
							$strout.='<a class="viewnav"  style="cursor:pointer;padding-left:10px;" onClick=javascript:makeprevlinks("'.$start.'","'.$end.'")>&lt; Previous</a>';
						}
						$strout.='&nbsp;</td>
						<td width="50%" style="padding-right:10px;" align="right">
						&nbsp;</td></tr></table></td></tr>';
						$totrecds=$cntlnk;
						break;
						}
					}
				$strout.='</table>';
					if($cntLT>0){
							$strout.='<table><tr><td>
							<br><img src="'.$IMG_SERVER.'/images/quint_images/btn-savechanges.gif" align="left" onclick="javascript:updatefilds();"/>
							</td></tr></table>';
					}
					$strout.='<input type="hidden" name="totrecords" value='.$totrecds.'><input type="hidden" name="stockid_get_all" id="stockid_get_all" value='.$stockid_get_all.'><input type="hidden" name="sratindx" value='.$sratindx.'><input type="hidden" name="endindx" value='.$endindx.'></form></div>';

				}else{
				$strout='<div id="trans_detail"><b><font color="#081E33"><br/>Make Your First Transaction!!<input type="hidden" id="totbtcrec" name="totbtcrec" value="0" /></font></b></div>';
			}
		return $strout;
		}


public function alltransaction($msg=NULL,$start=NULL,$end=NULL,$fldname=NULL,$ord=NULL,$items=NULL){
	global $transactioncnt,$IMG_SERVER;// web\lib\qp\_qp_config.php

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
		$end=0+$transactioncnt;
		$lmt='';
		$sratindx=0;
		$endindx=0+$transactioncnt;
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

		$id_stockname1=$this->allsymbolsarr();
		$id_comname1=$this->allsymbolscomapnyarr();

		$fixedtransaction="select id,quote_id,transaction_type,creation_date,unit_price,quantity,description from ss_transaction  where entity_type='0' and status='1' order by creation_date DESC";
		$fixedtransaction1 = exec_query($fixedtransaction);
		$fixedresults=count($fixedtransaction1);

		if($fldname!='' && $ord!='' && $items!=''){
		$qry_transaction="select QT.id,QT.quote_id,et.stocksymbol,et.CompanyName,QT.transaction_type,QT.creation_date,QT.unit_price,QT.quantity,QT.description from ss_transaction QT,ex_stock et where QT.entity_type='0' and QT.status='1' and et.id=QT.quote_id and QT.id in($items) order by $fldname $ord";
		}else{
		$qry_transaction="select QT.id,QT.quote_id,et.stocksymbol,et.CompanyName,QT.transaction_type,QT.creation_date,QT.unit_price,QT.quantity,QT.description from ss_transaction QT,ex_stock et where QT.entity_type='0' and QT.status='1' and et.id=QT.quote_id order by creation_date DESC $lmt";
		}
		//****echo "<br>".$qry_transaction;

		$execqry = exec_query($qry_transaction);
		if(count($execqry)>0){
			$strout='';
			$strout='<div id="trans_detail">
				<form name="transactionform" method="POST">
				<table width="100%" style="border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" cellspacing="0" cellpadding="5" border=0 bordercolor="black" class="quintportfolio">
				<tr><td class="quintportfolio"><a href=javascript:sortby("'.$start.'","'.$end.'","stocksymbol","'.$nextord.'") class="headeer">Symbol';
				if($fldname=='stocksymbol'){
				$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
				}
				$strout.='</a></td>
				<td class="quintportfolio"><a href=javascript:sortby("'.$start.'","'.$end.'","CompanyName","'.$nextord.'") class="headeer">Name';
				if($fldname=='CompanyName'){
				$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
				}
				$strout.='</a></td>
				<td class="quintportfolio"><a href=javascript:sortby("'.$start.'","'.$end.'","transaction_type","'.$nextord.'") class="headeer">Type';
				if($fldname=='transaction_type'){
				$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
				}
				$strout.='</a></td>
				<td class="quintportfolio"><a href=javascript:sortby("'.$start.'","'.$end.'","creation_date","'.$nextord.'") class="headeer">Date';
				if($fldname=='creation_date'){
				$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
				}
				$strout.='</a></td>
				<td class="quintportfolio"><a href=javascript:sortby("'.$start.'","'.$end.'","quantity","'.$nextord.'") class="headeer">Shares';
				if($fldname=='quantity'){
				$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
				}
				$strout.='</a></td>
				<td class="quintportfolio"><a href=javascript:sortby("'.$start.'","'.$end.'","unit_price","'.$nextord.'") class="headeer">Price';
				if($fldname=='unit_price'){
				$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
				}
				$strout.='</a></td>
				<td class="quintportfolio" colspan="3">Notes</td></tr>
				';
			$cntlnk=0;
			//**** echo $qry_transaction;

			foreach(exec_query($qry_transaction) as $result){
				$stocksymbol=$result['stocksymbol'];
				$companyname=$result['CompanyName'];
				$autoidget=$result['id'];
				$stockid_get=$result['quote_id'];
				$transaction_type_get=$result['transaction_type'];
				$indexedtransaction_type_get=$transaction_type_get;
				$creation_date_get=$result['creation_date'];
				$unit_price_get=$result['unit_price'];
				$quantity_get=$result['quantity'];
				$notes_get=$result['description'];

				if($stockid_get_all==''){
					$stockid_get_all=$autoidget;
				}else{
					$stockid_get_all=$stockid_get_all.",".$autoidget;
				}


				if($transaction_type_get==0){
				$transaction_type_get='Buy';
				}else if($transaction_type_get==1){
				$transaction_type_get='Sell';
				}else if($transaction_type_get==2){
					$transaction_type_get='Short Sell';
				}else if($transaction_type_get==3){
					$transaction_type_get='Buy to cover';
				}

				$cnt1++;
				if($cnt1%2==0){$tog1="#FFFFFF";}
				else { $tog1="#EFEFEF"; }

				$creation_date_get=$this->formatdate2Local($creation_date_get);
				$strout.='<tr bgcolor='.$tog1.'><td>'.$stocksymbol.'<input type="hidden" name="qtid_'.$autoidget.'" id="qtid_'.$autoidget.'" value="'.$stockid_get.'"></td><td>'.$companyname.'</td><td>'.$transaction_type_get.'</td><td>'.$creation_date_get.'</td><td>'.$quantity_get.'</td><td>
						  <input type="text" onblur="javascript:checkNumeric(this.id);" id="stockunitprice_'.$autoidget.'" name="stockunitprice_'.$autoidget.'" value="'.$unit_price_get.'" style="border:none;background-color:'.$tog1.';" size="8" readonly></td><td>';
							if($transaction_type_get=='Short Sell')
							{
								$autoidgetenc=	strrev(base64_encode($autoidget));
								$btnurl=$IMG_SERVER.'/images/quint_images/by-to-cover.jpg';
								$cnt++;
								$btid="btc_".$cnt;
								$strout.='<table width="100%" border=0><tr><td><input type="text" id="stocknotes_'.$autoidget.'" name="stocknotes_'.$autoidget.'" value="'.$notes_get.'" size="25" style="border:none;background-color:'.$tog1.';" size="8" readonly></td>';
								//****$strout.='<td width="10%">';
								//***$strout.='<img src="'.$btnurl.'" id="'.$btid.'" onClick="buytransaction(\''.$autoidgetenc.'\')" alt="BTC" title="click here for Buy to cover" style="display:none;cursor:pointer"/></td>';
								$strout.='</tr></table>';
							}else{
								//**** $strout.=$notes_get;
								$strout.='<input type="text" id="stocknotes_'.$autoidget.'" name="stocknotes_'.$autoidget.'" value="'.$notes_get.'" size="25" style="border:none;background-color:'.$tog1.';" size="8" readonly>';
							}
							$imgurl=$IMG_SERVER.'/images/optionsmith/delete.jpg';
							$saveimg=$IMG_SERVER.'/images/optionsmith/save.gif';
							$spinerimg=$IMG_SERVER.'/images/optionsmith/spinner.gif';

				$strout.='</td>
					<td width="17">
					<img src="'.$imgurl.'" id="stockdelete_'.$autoidget.'" alt="Delete" title="click here to delete this transaction" onclick="javascript:optiontransactionsave(\'delete\',\'stock\',\''.$autoidget.'\',\''.$indexedtransaction_type_get.'\')"  style="cursor:pointer;display:none;" />
					</td><td width="35">
					<span style="display:none;cursor:pointer;" id="stocksavetrans_'.$autoidget.'" onclick="javascript:optiontransactionsave(\'update\',\'stock\',\''.$autoidget.'\',\''.$indexedtransaction_type_get.'\')" title="click for save">
					<img src="'.$saveimg.'" alt="SAVE" title="click here to save"  />
					</span>
					<span style="display:none;cursor:pointer;" id="stockspinnerimg_'.$autoidget.'" >
					<img src="'.$spinerimg.'" alt="In Progress.." title="Progress..."  />
					</span>
					<span id="stockedittrans_'.$autoidget.'" style="cursor:pointer;" onclick="javascript:transactionedit(\'stock\',\''.$autoidget.'\')" title="click for edit">EDIT</span></td>
					</tr>';

	// Navigation starts here
	$cntlnk++;
	$chkres=$cntlnk+$start;
	if(($chkres<$fixedresults) && ($cntlnk==$transactioncnt)){
	$strout.='<tr><td colspan="7" style="padding-top:2px;"><table border="0" cellspacing=0 cellpadding=0 width="100%"><td width="50%" style="padding-left:10px;" align="left">';
	if($start!=0){$strout.='<a class="viewnav" style="cursor:pointer;padding-left:10px;" onClick=javascript:makeprevlinks1("'.$start.'","'.$end.'")>&lt; Previous</a>';}
	$strout.='</td><td width="50%" style="padding-right:10px;" align="right"><a class="viewnav"  style="cursor:pointer;" onClick=javascript:makenextlinks1("'.$start.'","'.$end.'")>Next ></a></td></tr></table></td></tr>';
	$totrecds=$cntlnk;
	break;
	}
	else if(($chkres>=$fixedresults)){
	$strout.='<tr><td colspan="7" style="padding-top:5px;"><table border="0" cellspacing=0 cellpadding=0 width="100%">
	<td width="50%" style="padding-left:10px;" align="left">';
	if($start!=0){$strout.='<a class="viewnav"  style="cursor:pointer;padding-left:10px;" onClick=javascript:makeprevlinks1("'.$start.'","'.$end.'")>&lt; Previous</a>';}
	$strout.='&nbsp;</td>
	<td width="50%" style="padding-right:10px;" align="right">
	&nbsp;</td></tr></table></td></tr>';
	$totrecds=$cntlnk;
	break;
	}
	// Navigation ends here
			}
			$strout.='</table><input type="hidden" name="stockid_get_all" id="stockid_get_all" value='.$stockid_get_all.'><input type="hidden" id="totbtcrec" name="totbtcrec" value="'.$cnt.'"><p><font color=red>'.$errmsg[0].'</font></p></form></div>';
		}else{
			$strout='<div id="trans_detail"><b><font color="#081E33"><br/>Make Your First Transaction!!<input type="hidden" id="totbtcrec" name="totbtcrec" value="0" /></font></b></div>';
		}
return $strout;
}
public function shortsellTransactionView($msg=NULL,$start=NULL,$end=NULL,$fldname=NULL,$ord=NULL,$items=NULL){
				global $transactioncnt,$IMG_SERVER;// web\lib\qp\_qp_config.php
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
					$end=0+$transactioncnt;
					$lmt='';
					$sratindx=0;
					$endindx=0+$transactioncnt;
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

				$id_stockname1=$this->allsymbolsarr();
				$id_comname1=$this->allsymbolscomapnyarr();
				$fixedtransaction="select id from ss_transaction  where transaction_type='2' and entity_type='0' and status='1' order by creation_date DESC";
				$fixedtransaction1 = exec_query($fixedtransaction);
				$fixedresults=count($fixedtransaction1);

				if($fldname!='' && $ord!='' && $items!=''){
					$qry_transaction="select QT.id,QT.quote_id,et.stocksymbol,et.CompanyName,QT.transaction_type,QT.creation_date,QT.unit_price,QT.quantity,QT.description from ss_transaction QT,ex_stock et where QT.transaction_type='2' and QT.entity_type='0' and QT.status='1' and et.id=QT.quote_id and QT.id in($items) order by $fldname $ord";
				}else{
					$qry_transaction="select QT.id,QT.quote_id,et.stocksymbol,et.CompanyName,QT.transaction_type,QT.creation_date,QT.unit_price,QT.quantity,QT.description from ss_transaction QT,ex_stock et where QT.transaction_type='2' and QT.entity_type='0' and QT.status='1' and et.id=QT.quote_id order by creation_date DESC $lmt";
				}
				//***echo "<br>".$qry_transaction;

				$execqry = exec_query($qry_transaction);
				if(count($execqry)>0){
					$strout='';
					$strout='<div id="trans_detail"><span id="btcspan"></span>
						<form name="transactionform" method="POST">
						<table width="100%" style="border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" cellspacing="0" cellpadding="5" border=0 bordercolor="black" class="quintportfolio">
						<tr><td class="quintportfolio"><a href=javascript:sortby("'.$start.'","'.$end.'","stocksymbol","'.$nextord.'") class="headeer">Symbol';
					if($fldname=='stocksymbol'){
						$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
					}
					$strout.='</a></td>
							<td class="quintportfolio"><a href=javascript:sortby("'.$start.'","'.$end.'","CompanyName","'.$nextord.'") class="headeer">Name';
					if($fldname=='CompanyName'){
						$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
					}
					$strout.='</a></td>
							<td class="quintportfolio"><a href=javascript:sortby("'.$start.'","'.$end.'","transaction_type","'.$nextord.'") class="headeer">Type';
					if($fldname=='transaction_type'){
						$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
					}
					$strout.='</a></td>
							<td class="quintportfolio"><a href=javascript:sortby("'.$start.'","'.$end.'","creation_date","'.$nextord.'") class="headeer">Date';
					if($fldname=='creation_date'){
						$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
					}
					$strout.='</a></td>
							<td class="quintportfolio"><a href=javascript:sortby("'.$start.'","'.$end.'","quantity","'.$nextord.'") class="headeer">Shares';
					if($fldname=='quantity'){
						$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
					}
					$strout.='</a></td>
							<td class="quintportfolio"><a href=javascript:sortby("'.$start.'","'.$end.'","unit_price","'.$nextord.'") class="headeer">Price';
					if($fldname=='unit_price'){
						$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
					}
					$strout.='</a></td>
							<td class="quintportfolio" colspan="3">Notes</td></tr>
						';
					$cntlnk=0;
			//**** echo $qry_transaction;

					foreach(exec_query($qry_transaction) as $result){
						$stocksymbol=$result['stocksymbol'];
						$companyname=$result['CompanyName'];
						$autoidget=$result['id'];
						$stockid_get=$result['quote_id'];
						$transaction_type_get=$result['transaction_type'];
						$indexedtransaction_type_get=$transaction_type_get;
						$creation_date_get=$result['creation_date'];
						$unit_price_get=$result['unit_price'];
						$quantity_get=$result['quantity'];
						$notes_get=$result['description'];

						if($stockid_get_all==''){
							$stockid_get_all=$autoidget;
						}else{
							$stockid_get_all=$stockid_get_all.",".$autoidget;
						}


						if($transaction_type_get==0){
							$transaction_type_get='Buy';
						}else if($transaction_type_get==1){
							$transaction_type_get='Sell';
						}else if($transaction_type_get==2){
							$transaction_type_get='Short Sell';
						}else if($transaction_type_get==3){
							$transaction_type_get='Buy to cover';
						}

						$cnt1++;
						if($cnt1%2==0){$tog1="#FFFFFF";}
						else { $tog1="#EFEFEF"; }

						$creation_date_get=$this->formatdate2Local($creation_date_get);
						$strout.='<tr bgcolor='.$tog1.'><td>'.$stocksymbol.'<input type="hidden" name="qtid_'.$autoidget.'" id="qtid_'.$autoidget.'" value="'.$stockid_get.'"></td><td>'.$companyname.'</td><td>'.$transaction_type_get.'</td><td>'.$creation_date_get.'</td><td>'.$quantity_get.'</td><td>
							<input type="text" onblur="javascript:checkNumeric(this.id);" id="stockunitprice_'.$autoidget.'" name="stockunitprice_'.$autoidget.'" value="'.$unit_price_get.'" style="border:none;background-color:'.$tog1.';" size="8" readonly></td><td>';
						if($transaction_type_get=='Short Sell')
						{
							$autoidgetenc=	strrev(base64_encode($autoidget));
							$btnurl=$IMG_SERVER.'/images/quint_images/by-to-cover.jpg';
							$cnt++;
							$btid="btc_".$cnt;
							$strout.='<table width="100%" border=0><tr><td><input type="text" id="stocknotes_'.$autoidget.'" name="stocknotes_'.$autoidget.'" value="'.$notes_get.'" size="25" style="border:none;background-color:'.$tog1.';" size="8" readonly></td>';
								$strout.='<td width="10%">';
								$strout.='<img src="'.$btnurl.'" id="'.$btid.'" onClick="buytransaction(\''.$autoidgetenc.'\')" alt="BTC" title="click here for Buy to cover" style="cursor:pointer"/></td>';
							$strout.='</tr></table>';
						}else{
								//**** $strout.=$notes_get;
							$strout.='<input type="text" id="stocknotes_'.$autoidget.'" name="stocknotes_'.$autoidget.'" value="'.$notes_get.'" size="25" style="border:none;background-color:'.$tog1.';" size="8" readonly>';
						}
						$imgurl=$IMG_SERVER.'/images/optionsmith/delete.jpg';
						$saveimg=$IMG_SERVER.'/images/optionsmith/save.gif';
						$spinerimg=$IMG_SERVER.'/images/optionsmith/spinner.gif';

						$strout.='</td>
								<td width="17">
										  <img src="'.$imgurl.'" id="stockdelete_'.$autoidget.'" alt="Delete" title="click here to delete this transaction" onclick="javascript:optiontransactionsave(\'delete\',\'stock\',\''.$autoidget.'\',\''.$indexedtransaction_type_get.'\')"  style="cursor:pointer;display:none;" />
							</td><td width="35">
										   <span style="display:none;cursor:pointer;" id="stocksavetrans_'.$autoidget.'" onclick="javascript:optiontransactionsave(\'update\',\'stock\',\''.$autoidget.'\',\''.$indexedtransaction_type_get.'\')" title="click for save">
							<img src="'.$saveimg.'" alt="SAVE" title="click here to save"  />
							</span>
							<span style="display:none;cursor:pointer;" id="stockspinnerimg_'.$autoidget.'" >
							<img src="'.$spinerimg.'" alt="In Progress.." title="Progress..."  />
							</span>
							<span id="stockedittrans_'.$autoidget.'" style="cursor:pointer;" onclick="javascript:transactionedit(\'stock\',\''.$autoidget.'\')" title="click for edit">EDIT</span></td>
							</tr>';

	// Navigation starts here
						$cntlnk++;
						$chkres=$cntlnk+$start;
						if(($chkres<$fixedresults) && ($cntlnk==$transactioncnt)){
							$strout.='<tr><td colspan="7" style="padding-top:2px;"><table border="0" cellspacing=0 cellpadding=0 width="100%"><td width="50%" style="padding-left:10px;" align="left">';
							if($start!=0){$strout.='<a class="viewnav" style="cursor:pointer;padding-left:10px;" onClick=javascript:makeBTCprevlinks1("'.$start.'","'.$end.'")>&lt; Previous</a>';}
							$strout.='</td><td width="50%" style="padding-right:10px;" align="right"><a class="viewnav"  style="cursor:pointer;" onClick=javascript:makeBTCnextlinks1("'.$start.'","'.$end.'")>Next ></a></td></tr></table></td></tr>';
							$totrecds=$cntlnk;
							break;
						}
						else if(($chkres>=$fixedresults)){
							$strout.='<tr><td colspan="7" style="padding-top:5px;"><table border="0" cellspacing=0 cellpadding=0 width="100%">
								<td width="50%" style="padding-left:10px;" align="left">';
							if($start!=0){$strout.='<a class="viewnav"  style="cursor:pointer;padding-left:10px;" onClick=javascript:makeBTCprevlinks1("'.$start.'","'.$end.'")>&lt; Previous</a>';}
							$strout.='&nbsp;</td>
									<td width="50%" style="padding-right:10px;" align="right">
								&nbsp;</td></tr></table></td></tr>';
							$totrecds=$cntlnk;
							break;
							}
	// Navigation ends here
						}
						$strout.='</table><input type="hidden" name="stockid_get_all" id="stockid_get_all" value='.$stockid_get_all.'><input type="hidden" id="totbtcrec" name="totbtcrec" value="'.$cnt.'"><p><font color=red>'.$errmsg[0].'</font></p></form></div>';
						}else{
						$strout='<div id="trans_detail"><b><font color="#081E33"><br/>Make Your First Transaction!!<input type="hidden" id="totbtcrec" name="totbtcrec" value="0" /></font></b></div>';
						}
						return $strout;
}
function formatdate2Local($creation_date){
	$datetodisplay=explode(" ",$creation_date);
	$datesArray=explode('-',$datetodisplay[0]);
	$creation_date = $datesArray[1]."/".$datesArray[2]."/".$datesArray[0];
	return $creation_date;
}

function formatdate2dbSTD($creation_date){
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


function formatdate2LocalSCR($creation_date){
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

function addnewstock($msg){
	global $IMG_SERVER;
	$stradd.='<form name="addform">
	<table border="0" bordercolor=red cellspacing="0" cellpadding="2" width="100%"  class="quintportfolio">
	<tr><td width="100%">
    <table style="border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" width="100%" border=0 cellspacing="0" cellpadding="2" bordercolor=black>
	<tr>
	<td class="quintportfolio">Symbol</td>
	<td class="quintportfolio">Type</td>
	<td class="quintportfolio"><nobr>Date</td>
	<td class="quintportfolio">Shares</td>
	<td class="quintportfolio">Price </td>
	<td class="quintportfolio">Notes</td>
	</tr>
	<tr bgcolor="#EFEFEF"><td>';
	$stradd.=input_text("symbol",$symbol, 0, 40," autocomplete='off'");
	$stradd.='<input type="hidden" id="sid_hid">
	<script>
	var obj = actb(document.getElementById("symbol"),customarray);
	</script></td>
	<td><select name="comotype" id="comotype"><option value="0">Buy</option><option value="1">Sell</option></select></td>
	<td>
	<input type="text" size="10" id="demo2" name="date" readonly>';
	$stradd.='&nbsp;<a href=javascript:NewCal("demo2","mmddyyyy")><img src="<?=$IMG_SERVER?>/images/quint_images/cal.gif" width="16" height="16" align="absmiddle" border="0" alt="Pick a date"></a></td>';
	$stradd.='<td><input size="8" type="text" id="shares" onBlur=javascript:chknosonly("shares")></td>';
	$stradd.='<td><input size="8" type="text" name="price" id="price" onBlur=javascript:chknosonly("price")></td>
	<td><input type="text" name="notes" id="notes" size="55">
	<input type="hidden" name="addmode" id="addmode" value="">
	</td></tr>
	<tr><td colspan="6"></td></tr>
	<tr><td colspan="6"><img src="<?=$IMG_SERVER?>/images/quint_images/btn-addtoportfolio.gif" onClick="javascript:validateentryforedit();" /><br/></td></tr>
	</table>
	</td></tr>
	<tr><td>
	</td></tr>
	</table></form>';

	echo $stradd;
}
# This function avgunitpricebysid($sid) returns the avg. unit price of the particular stock

function avgunitpricebysid($sid){
	$quer_for_buyids="select sum(quantity) as totpurchqty,sum((unit_price)*(quantity)) as totpurchamt,((sum((unit_price)*(quantity)))/(sum(quantity))) as avgunitprice from ss_transaction where status='1' and transaction_type='0' and quote_id='$sid' group by quote_id";
	$allr=exec_query($quer_for_buyids);
	if(count($allr)>0){
		foreach($allr as $allresultbuy){
			$avgunitprice=$allresultbuy['avgunitprice'];
			$stocksinhand=$allresultbuy['totpurchqty'];
		}
	}else{
		$avgunitprice=0.00;
	}
	$avgstockarr=array();
	$avgstockarr['up']=$avgunitprice;
	$avgstockarr['totbuyqty']=$stocksinhand;
	return $avgstockarr;
}

function stocksales($pid=NULL){
	if(isset($pid)){
	$append=" and QST.buy_trans_id='$pid'";
	}else{
	$append='';
	}
	$qry="select QST.buy_trans_id as bti,sum(QT.quantity) as totsell from ss_transaction QT, ss_sell_transaction QST where QST.sell_trans_id=QT.id and QT.status='1' $append group by buy_trans_id";
	//echo $qry;
	$stockqry=exec_query($qry);
	$selltransactionarr=array();
	if(count($stockqry)>0){
		foreach($stockqry as $allresult){
			$bti=$allresult['bti'];
			$totsell=$allresult['totsell'];
			$selltransactionarr[$bti]=$totsell;
		}
	}
	return $selltransactionarr;
}

function selltrans() {
	$qry="select QS.sell_trans_id id,QT.creation_date,QT.unit_price from ss_sell_transaction QS,ss_transaction QT where QS.buy_trans_id=QT.id";
	$selldata=exec_query($qry);
	$selltransarr=array();
	$purctransarr=array();

	if(count($selldata)>0){
		foreach($selldata as $allresult){
			$sellid=$allresult['id'];
			$buydate=$allresult['creation_date'];
			$selltransarr[$sellid]=$buydate;
			$purtransarr[$sellid]=$allresult['unit_price'];;
		}
	}
	  //return $selltransarr.'~'.$purtransarr;
     return $selltransarr;
 }


 function selltransforsellshort() {
	$qry="select QS.buy_trans_id id,QT.creation_date,QT.unit_price from ss_sell_transaction QS,ss_transaction QT where QS.buy_trans_id=QT.id";
	$selldata=exec_query($qry);
	$selltransarr=array();
	$purctransarr=array();

	if(count($selldata)>0){
		foreach($selldata as $allresult){
			$sellid=$allresult['id'];
			$buydate=$allresult['creation_date'];
			$selltransarr[$sellid]=$buydate;
		}
	}
	  //return $selltransarr.'~'.$purtransarr;
     return $selltransarr;
 }


 function purpricetrans() {
	$qry="select QS.sell_trans_id id,QT.creation_date,QT.unit_price from ss_sell_transaction QS,ss_transaction QT where QS.buy_trans_id=QT.id";
	$selldata=exec_query($qry);
	$selltransarr=array();
	$purctransarr=array();

	if(count($selldata)>0){
		foreach($selldata as $allresult){
			$sellid=$allresult['id'];
			$purtransarr[$sellid]=$allresult['unit_price'];;
		}
	}
	  //return $selltransarr.'~'.$purtransarr;
     return $purtransarr;
 }

 function purpricetransforbuytocover() {
	// Make the array for all shorts sell unit prices
	 $ary_up_ss="select id,unit_price from ss_transaction where transaction_type='2' and status='1'";
	 $shortselldata=exec_query($ary_up_ss);
	 $shortsell_arr=array();
	 if(count($shortselldata)>0){
		 foreach($shortselldata as $allresultss){
			 $shortsell_arr[$allresultss['id']]=$allresultss['unit_price'];
		 }
	 }

	 $btc_arr=array();
	 if(count($shortsell_arr)>0){
	 // Make an array that stores all btc ids and their unit_price (= corresponding short sell's unit_price)
	 $qry_up_btc="select buy_trans_id,sell_trans_id from ss_sell_transaction where buy_trans_id in (select id from ss_transaction where transaction_type='3' and status='1')";
	 $btcdata=exec_query($qry_up_btc);
	 if(count($btcdata)>0){
		 foreach($btcdata as $allresultbtc){
			 $orgssid=$allresultbtc['sell_trans_id'];
			 $orgssid_price=$shortsell_arr[$orgssid];
			 $btc_arr[$allresultbtc['buy_trans_id']]=$orgssid_price;
		}
	}
	 }
	 return $btc_arr;
 }

// function to find sold date for short sell
function datesoldshortsell($id) {
//***	$qry="select sell_trans_id,updation_date as creation_date from ss_sell_transaction where buy_trans_id=0 and sell_trans_id in (select a.sell_trans_id from ss_sell_transaction a where a.buy_trans_id='$id')";
	$qry="select a.sell_trans_id,a.creation_date from ss_sell_transaction a where a.buy_trans_id='$id'";
        $datesold=exec_query($qry,1);
	if(isset($datesold)){
	     return $datesold;
	}
 }


 function creationdateshortsell($id) {
	$qry="select id, creation_date from ss_transaction where transaction_type=2 and status=1 and id in(
select a.sell_trans_id from ss_sell_transaction a where a.buy_trans_id='$id')";

    $creationdatesold=exec_query($qry,1);

	if(isset($creationdatesold)){
	     return $creationdatesold;
	}
 }


function shortsellsaleprice() {
	//***$qry="select unit_price from ss_transaction where id in (select a.sell_trans_id from ss_sell_transaction a where a.buy_trans_id='$id')";
	$qry="select id,unit_price from ss_transaction where transaction_type='3' and status='1'";
	$shortsellunitprice=exec_query($qry);

	$buytocover_up=array();
	if(count($shortsellunitprice)>0){
		foreach($shortsellunitprice as $allresultbtcup){
			$buytocover_up[$allresultbtcup['id']]=$allresultbtcup['unit_price'];
		}
	}
	if(isset($buytocover_up)){
	     return $buytocover_up;
	}
 }



 function noofshare(){
 $qry="select QS.sell_trans_id id,QT.creation_date,QT.unit_price from ss_sell_transaction QS,ss_transaction QT where QS.buy_trans_id=QT.id";
	$selldata=exec_query($qry);
	$selltransarr=array();
	$purctransarr=array();

	if(count($selldata)>0){
		foreach($selldata as $allresult){
			$sellid=$allresult['id'];
			$purtransarr[$sellid]=$allresult['unit_price'];;
		}
	}
	  //return $selltransarr.'~'.$purtransarr;
     return $purtransarr;
  }


	function getAllQuotescurntval(){
	$sql_chk="select cash_amt as tot,id from ss_cash where status='1'";
	//**** $sql_chk="select sum(cash_amt) as tot from ss_cash where (status='1' or status='2')";
	 //echo $sql_chk;

	 $searchfound=exec_query($sql_chk);
	 if($searchfound[0]['tot']==''){
		 $cashget='0.00';
	 }else{
		 if(count($searchfound)>0){
			 foreach($searchfound as $res){
				 $cashget=$res['tot'];
			 }
		 }
	 }
	 $getAllQuotescurntvalarr=array();
	 $allstockids=$this->getallbuystocks(); // returns all quote_id with qty in hand
	 $allsymbolnamesarr=$this->allsymbolsarr(); // returns all symbol names
	 $unitpriceofstockinhand=$this->unitpriceofstockinhand(); // returns an array with quote_id as key
		if(count($allstockids)>0){
			$allsymbolidsarr=array_keys($allstockids);
		}
		$qrystr='';
		if(count($allsymbolidsarr)>0){
		for($x=0;$x<count($allsymbolidsarr);$x++){
			// check the qty in hand !=0
			if(($allstockids[$allsymbolidsarr[$x]])!=0){
					if($qrystr==''){
					$qrystr=$allsymbolnamesarr[$allsymbolidsarr[$x]];
					}else{
					$qrystr=$qrystr."+".$allsymbolnamesarr[$allsymbolidsarr[$x]];
					}
			}
		}

	}

		# if there are some symbols go for yahoo and get there current quotes
		if($qrystr!=''){
		// cant change use multiple
			$filename="http://download.finance.yahoo.com/d/allquotes.csv?s=".$qrystr."&f=sl1&e=.csv";
			$open = file($filename);
			if(isset($open)){
				$currentsymvalarr=array();
				$totfetched=count($open);
				$totalcurrentvalue=0;
				$totaloriginalcost=0;
				$totalavgreturnpercnt=0;

				for($k=0;$k<$totfetched;$k++){
					$read = $open[$k];
					$read = str_replace("\"", "", $read);
					$read = explode(",", $read);

					if(isset($key))	unset($key);
					$key = array_search($read[0], $allsymbolnamesarr);
					if($read[1]==0){
					        $tickersymbol=$read[0];
							$read[1]=getcurrentquotefeed($tickersymbol,$entitytype);
					}
					$currentsymvalarr[$key]=$read[1];
					$thiskeyval=0;

					$thiskeyval=$allstockids[$key]*$read[1];
					$totalcurrentvalue=$totalcurrentvalue+$thiskeyval;
					$totaloriginalcost=$totaloriginalcost+($allstockids[$key]*$unitpriceofstockinhand[$key]);
				}
				$totalgainorloss=$totalcurrentvalue-$totaloriginalcost;
				$totalavgreturnpercnt=($totalgainorloss/$totaloriginalcost)*100;
			}
		}
		$getAllQuotescurntvalarr['oc']=$cashget; // changed as on 12022008 on request
		$getAllQuotescurntvalarr['cqv']=$totalcurrentvalue;
		$getAllQuotescurntvalarr['avg']=$totalavgreturnpercnt;
		return $getAllQuotescurntvalarr;


 }
// this function return the YTD Return
 function getytdreturnval(){
	 $getYTDAllQuotescurntvalarr=array();
	 $curyear=date('Y');
	 $qryfirsttrans="select creation_date from ss_transaction where creation_date>='$curyear-01-01 00:00:00' and status=1 LIMIT 1";
	 //echo $qryfirsttrans;
	 $firstdata=exec_query($qryfirsttrans);
	 if(count($firstdata)>0){
		 foreach($firstdata as $allresultss){
			 $dateget=$allresultss['creation_date'];
		 }
	 }
	 // getallbuystocks($dateget) This function returns the array of total stocks in hand by indexing the quote_id
	 // unitpriceofstockinhand($dates=NULL) This function returns the array of avg unit price of stocks  indexing the quote_id

	 $allstockids=$this->getallbuystocks($dateget); // returns all quote_id with qty in hand
	 $allsymbolnamesarr=$this->allsymbolsarr(); // returns all symbol names
	 $unitpriceofstockinhand=$this->unitpriceofstockinhand($dateget);

	 $allsymbolidsarr=array_keys($allstockids);
	 $qrystr='';
	 for($x=0;$x<count($allsymbolidsarr);$x++){
		// check the qty in hand !=0
		 if(($allstockids[$allsymbolidsarr[$x]])!=0){
			 if($qrystr==''){
				 $qrystr=$allsymbolnamesarr[$allsymbolidsarr[$x]];
			 }else{
				 $qrystr=$qrystr."+".$allsymbolnamesarr[$allsymbolidsarr[$x]];
			 }
		 }
	 }

# if there are some symbols go for yahoo and get there current quotes
	 if($qrystr!=''){
	    // cant change use multiple
		 $filename="http://download.finance.yahoo.com/d/allquotes.csv?s=".$qrystr."&f=sl1&e=.csv";
		//echo $filename;
		 $open = file($filename);
		 fclose($open);
		 if(isset($open)){
			 $currentsymvalarr=array();
			 $totfetched=count($open);
			 $totalcurrentvalue=0;
			 $totaloriginalcost=0;

			 for($k=0;$k<$totfetched;$k++){
				 $read = $open[$k];
				 $read = str_replace("\"", "", $read);
				 $read = explode(",", $read);
					//$read[0]=AET
					//$read[1]=12.50
					// get the code of AET i.e. $read[0]
				 if(isset($key))	unset($key);
				 $key = array_search($read[0], $allsymbolnamesarr);
				 if($read[1]==0){
					        $tickersymbol=$read[0];
							$read[1]=getcurrentquotefeed($tickersymbol,$entitytype);
					}
				 $currentsymvalarr[$key]=$read[1];
				 $thiskeyval=0;
				 $thiskeyval=$allstockids[$key]*$read[1];
				 $totalcurrentvalue=$totalcurrentvalue+$thiskeyval;
				 $totaloriginalcost=$totaloriginalcost+($allstockids[$key]*$unitpriceofstockinhand[$key]);
			 }
			 $totalgainorloss=$totalcurrentvalue-$totaloriginalcost;
			 $totalavgreturnpercnt=($totalgainorloss/$totaloriginalcost)*100;
			 $getYTDAllQuotescurntvalarr['avg']=$totalavgreturnpercnt;
			 return $getYTDAllQuotescurntvalarr;
		 }
	 }

 }
 public function buytocover($transid){
	 $fname='btc';
	 global $lang,$IMG_SERVER;
	 $id_stockname1=$this->allsymbolsarr();
	 $id_comname1=$this->allsymbolscomapnyarr();
			 $qry_transaction="select id,quote_id,transaction_type,creation_date,unit_price,quantity,description from ss_transaction  where entity_type='0' and status='1' and id='$transid' union ALL select QT.id,QT.quote_id,QT.transaction_type,QT.creation_date,QT.unit_price,QT.quantity,QT.description  from ss_transaction QT,ss_sell_transaction QST where QT.entity_type='0' and QST.sell_trans_id='$transid' and QST.status='1' and QST.buy_trans_id!=0 and QST.buy_trans_id=QT.id";

			 $execqry = exec_query($qry_transaction);
			 if(count($execqry)>0){
				 $strout='';
				 $strout='<div id="trans_detail">
					 <table width="100%" style="border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" cellspacing="0" cellpadding="5" border=0 bordercolor="black" class="quintportfolio">
					 <tr><td class="quintportfolio">Symbol</td><td class="quintportfolio">Name</td><td class="quintportfolio">Type</td><td class="quintportfolio">Date</td><td class="quintportfolio">Shares</td><td class="quintportfolio">Price </td><td class="quintportfolio">Notes</th></tr>
					 ';
				 $totalstockstobtc=0;
				 foreach(exec_query($qry_transaction) as $result){
				 $autoidget=$result['id'];
				 $stockid_get=$result['quote_id'];
				 $transaction_type_get=$result['transaction_type'];
				 $creation_date_get=$result['creation_date'];
				 $unit_price_get=$result['unit_price'];
				 $quantity_get=$result['quantity'];
				 $notes_get=$result['description'];

				 if($transaction_type_get==2){$totalstockstobtc=$totalstockstobtc+$quantity_get;}
				 if($transaction_type_get==3){$totalstockstobtc=$totalstockstobtc-$quantity_get;}
				 if($transaction_type_get==0){ $transaction_type_get='Buy';	}
				 else if($transaction_type_get==1){$transaction_type_get='Sell';}
				 else if($transaction_type_get==2){$transaction_type_get='Short Sell';}
				 else if($transaction_type_get==3){$transaction_type_get='Buy to cover';}
				 $cnt1++;
				 if($cnt1%2==0){$tog1="#FFFFFF";}
				 else { $tog1="#EFEFEF"; }
				 $creation_date_get=$this->formatdate2Local($creation_date_get);
				 $strout.='<tr bgcolor='.$tog1.'><td>'.$id_stockname1[$stockid_get].'</td><td>'.$id_comname1[$stockid_get].'</td><td>';
				 if($transaction_type_get=='Short Sell'){
				 $strout.='<input type="hidden" id="orgsortselldate" name="orgsortselldate" value="'.$creation_date_get.'">';
				 }
				 $strout.=$transaction_type_get.'</td><td>'.$creation_date_get.'</td><td>'.$quantity_get.'</td><td>'.$unit_price_get.'</td><td>';
				 $strout.=$notes_get;
				 $strout.='</td></tr>';
				 }
			 $strout.='</table>';
			 if($totalstockstobtc!=0)
			 {
				$strout.='<p class="quintportfolio">Buy To Cover (&nbsp;'.$lang["max_shares_for_btc"].' '.$totalstockstobtc.' &nbsp;)</p>
				<form name="'.$fname.'" method="post"><table width="100%" style="border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" cellspacing="0" cellpadding="5" border=0 bordercolor="black" class="quintportfolio">
				 <tr>
					<td class="quintportfolio" width="15%">Date</td>
					<td class="quintportfolio" width="10%">Shares</td>
					<td class="quintportfolio" width="10%">Price </td>
					<td class="quintportfolio">Notes</td>
					</tr>
					<tr bgcolor="#EFEFEF">
 					 <td class="quintportfolio" width="15%">';
					 $stren=strrev(base64_encode($transid));
					 $strout.='<input type="hidden" name="quoteid_get" id="quoteid_get" value='.$stockid_get.'><input type="hidden" id="trsactionid" name="trsactionid" value='.$stren.'>';
					 $strout.='<input type="hidden" id="orgssqty" name="orgssqty" value="'.$totalstockstobtc.'">
					 <input type="hidden" id="unitssprice" name="unitssprice" value="'.$unit_price_get.'">';
					 $strout.='<input type="text" size="10" name="selldate" id="selldate" value="" readonly>&nbsp;';
					 $str="selldate";
					 $strout.="<a href=\"javascript:NewCal('$str','mmddyyyy')\">";
					 $strout.='<img src="'.$IMG_SERVER.'/images/quint_images/cal.gif" alt="Pick a date" width="16" height="16" hspace="1" border="0px" align="absmiddle" name="img" id="img" ></a></td>
					 <td class="quintportfolio" width="10%"><input size="8" type="text" name="shareqty" id="shareqty"  value="" onKeyup=javascript:chknosvalidation("'.$fname.'",this)></td>
					 <td class="quintportfolio" width="10%"><input size="8" type="text" name="price" id="price"  value="" onKeyup=javascript:chknosvalidation("'.$fname.'",this)></td>
					 <td class="quintportfolio"><input type="text" size="40" name="notes" value="">
					 <input type="button" value="SAVE" onClick="javascript:chknsubmit()">&nbsp;<input type="reset" value="RESET"></td>
					 </tr>
					 </table></form>';
				 }
			 $strout.='</div>';
		 }else{
			 $strout='<div id="trans_detail"><b><font color="#081E33"><br/>Make Your First Short Sell .... !!</font></b></div>';
		 }
		 return $strout;
 }
public function updatetranstabl($creatdateconv,$updatedon,$updatedshares,$unitprice,$description,$status,$idtupdate){
				 $tablename='ss_transaction';
				 $req_update=array();
				 $req_update['creation_date']=$creatdateconv;
				 $req_update['updation_date']=$updatedon;
				 $req_update['quantity']=$updatedshares;
				 $req_update['unit_price']=$unitprice;
				 $req_update['description']=$description;
				 $req_update['status']=$status;
				 htmlprint_r($req_update);
				 $ret_up=update_query($tablename,$req_update,array(id=>$idtupdate));
				 $memCacheDelete = new memCacheObj();
					$key="ssOpenPositiontrade";
					$memCacheDelete->deleteKey($key);
					$key="ssOpenPosition";
					$memCacheDelete->deleteKey($key);
					$key="ssDisplayPerformance";
					$memCacheDelete->deleteKey($key);
				 return $ret_up;
			 }

public function updatelottabl($upqtid){
				 $totinhandqty=$this->getparticularstocksinh($upqtid);
				 $avg_unit_price=0;
				 $avg_unit_price=$this->avgunitpriceofstockinhand($upqtid);
				# Update tab-2
				 $transactiondata_up=array('quantity'=>$totinhandqty,'avg_unit_price'=>$avg_unit_price,'recent_trade_date'=>$datetime); // as we are not keeping any track for last updated so its default for datetime
				 $retstat=0;
				 $retstat=update_query("ss_user_portfolio",$transactiondata_up,array(quote_id=>$upqtid));
					$memCacheDelete = new memCacheObj();
					$key="ssOpenPositiontrade";
					$memCacheDelete->deleteKey($key);
					$key="ssOpenPosition";
					$memCacheDelete->deleteKey($key);
					$key="ssDisplayPerformance";
					$memCacheDelete->deleteKey($key);
				 return $retstat;
			 }

 public function updateselltabl($autoids,$transtype)
 {
				 $updatedon=date('Y-m-d H:i:s');
				 $selltransactiondata_up=array(status=>'0','updation_date'=>$updatedon);

				if($transtype==1)
				{ // while its sell
				$retstat=0;
				$retstat=update_query("ss_sell_transaction",$selltransactiondata_up,array(sell_trans_id=>$autoids));
				return $retstat;
				}
				else if($transtype==3)
				{
					$retstat=0;
					$retstat=update_query("ss_sell_transaction",$selltransactiondata_up,array(buy_trans_id=>$autoids));
					return $retstat;
				}
                else if($transtype==2)
				{
					$retstat=0;
					$retstat=update_query("ss_sell_transaction",$selltransactiondata_up,array(sell_trans_id=>$autoids));
					$sql_selectdisbledids="select buy_trans_id from ss_sell_transaction where entity_type='0' and sell_trans_id='$autoids' and status=0 and buy_trans_id!='0'";
					$allressym=exec_query($sql_selectdisbledids);
					$totalrecds=0;
					if(count($allressym)>0){
						foreach($allressym as $alltransres)
						{
							$idtoupdate=$alltransres['buy_trans_id'];
							unset($req_update);
							$req_update=array();
							$req_update['updation_date']=$updatedon;
							$req_update['status']=0;
							$retstat=update_query("ss_transaction",$req_update,array(id=>$idtoupdate));
						}
						$memCacheDelete = new memCacheObj();
						$key="ssOpenPositiontrade";
						$memCacheDelete->deleteKey($key);
						$key="ssOpenPosition";
						$memCacheDelete->deleteKey($key);
						$key="ssDisplayPerformance";
						$memCacheDelete->deleteKey($key);
					}
					return $retstat;
				}else
				{
				 $retstat=0;
				 $retstat=update_query("ss_sell_transaction",$selltransactiondata_up,array(buy_trans_id=>$autoids));
				 $sql_selectdisbledids="select sell_trans_id from ss_sell_transaction where buy_trans_id='$autoids' and status=0";
				 $allressym=exec_query($sql_selectdisbledids);
				 $totalrecds=0;
				 if(count($allressym)>0){
					 foreach($allressym as $alltransres){
						 $idtoupdate=$alltransres['sell_trans_id'];
						 unset($req_update);
						 $req_update=array();
						 $req_update['updation_date']=$updatedon;
						 $req_update['status']=0;
						 $retstat=update_query("ss_transaction",$req_update,array(id=>$idtoupdate));
					 }
				 }
					$memCacheDelete = new memCacheObj();
					$key="ssOpenPositiontrade";
					$memCacheDelete->deleteKey($key);
					$key="ssOpenPosition";
					$memCacheDelete->deleteKey($key);
					$key="ssDisplayPerformance";
					$memCacheDelete->deleteKey($key);
				 return $retstat; // 0 return means there was no entry against this buy transaction
				}
			 }

# This function calculates the total loss/gain of the short sell / btc
function getgainlossfrmssbtc(){
			 $totgainloss=0;
			 $totgainlossd=0;
			 $isbtcdone=0;

			 $sqlallsellids="select qt.id,qt.quote_id,ES.stocksymbol,qt.unit_price,qt.quantity from ss_transaction qt,ex_stock ES where qt.transaction_type='2' and qt.status='1' and qt.quote_id=ES.id";
			 $allressell=exec_query($sqlallsellids);
			 if(count($allressell)>0){//start
				 foreach($allressell as $allselltransres){
					 $transellid=$allselltransres['id']; // auto id of transaction table
					 $transellidup=$allselltransres['unit_price']; // unit price of each ss
					 $transellidqty=$allselltransres['quantity']; // total ss qty
					 $transellquote_id=$allselltransres['quote_id'];// symbol id
					 $transellsymbol=$allselltransres['stocksymbol']; // symbol name required for yahoo fetch

					# check the btc quantity against this ss
					 $sqlchkbtc="select qt.id,qt.unit_price,qt.quantity from ss_sell_transaction qst, ss_transaction qt where qst.sell_trans_id='$transellid' and qst.status='1' and qst.trans_status!='pending' and qt.id=qst.buy_trans_id";
					 $allresbtc=exec_query($sqlchkbtc);

					 if(count($allresbtc)>0){// agar mila
						 $isbtcdone=1;
							 $totqtybtcoved=0;
							 $totalbtcdup=0;
							 foreach($allresbtc as $allbtctransres){
							 $qtybtcoved=$allbtctransres['quantity'];
							 $btcovedup=$allbtctransres['unit_price'];
							 $btcdup=$qtybtcoved*$btcovedup;
							 $totalbtcdup=$totalbtcdup+$btcdup;
							 $totqtybtcoved=$totqtybtcoved+$qtybtcoved;
							 }

							 //echo "<br> Original ss qty : ".$transellidqty;
							 //echo "<br> Total btc qty : ".$totqtybtcoved."<br>";
							 //echo "<br> Total rest qty : ".($transellidqty-$totqtybtcoved)."<br>";
							 //echo "<br> Total sell amt of ($totqtybtcoved) shares : ".$totalbtcdup."<br>";
							 // calculate total gain or loss on abhi tak shares jo btc ho gaye
							 // chek $totqtybtcoved shares sell amt

							 $soldsharesorginalval=0;
							 $soldsharesorginalval=$transellidup*$totqtybtcoved;
							 $buysharesorginalval=$totalbtcdup;

							 $netprofitonbtcshares=0;
							 $restsharesqty=$transellidqty-$totqtybtcoved;

							 $netprofitonbtcshares=($soldsharesorginalval-$totalbtcdup);
							 $restsharesselltot=0;
							 $restsharesselltot=$restsharesqty*$transellidup;
							 $restsharescurntval=0;
							 $restsharescurntval=$restsharesqty*($this->getcurrentquote($transellsymbol,$entitytype));

//							 $totgainlossd=$totgainlossd+$netprofitonbtcshares;

							// echo "<br> un shares pe profit hua : ".$netprofitonbtcshares."<br>";
							 //echo "<br> rest shares tot amt : ".$restsharesselltot;
							 //echo "<br> rest shares market value : ".$restsharescurntval."<br>";
							 $netlossonrestshares=0;
							 $netlossonrestshares=$restsharesselltot-$restsharescurntval;

							 //echo "<br> rest shares gain/loss value : ".$netlossonrestshares."<br>";

							 $totgainlossd=$totgainlossd+($netprofitonbtcshares+$netlossonrestshares);
							 //echo "<br>............. $totgainlossd<br>";


					 }// end
					 else{
						 $isbtcdone=0; // no btc has done against this ss
						 //echo "<br>".$sqlchkbtc;
						 $currentquote=0;
						 $currentquote= $this->getcurrentquote($transellsymbol,$entitytype);
						// echo "<br>".$transellidqty." ----------- ".$transellsymbol."  : ".$currentquote;
						 $totsellvalue=0;
						 $totsellcurntvalue=0;
						 $totsellvalue=$transellidqty*$transellidup;
						 $totsellcurntvalue=$transellidqty*$currentquote;
						 $totgainloss=$totgainloss+($totsellvalue-$totsellcurntvalue);
					 }
				 }
				 return $totgainloss+$totgainlossd;
			 }//end
			 else{
				 return $totgainloss+$totgainlossd;
			 }
			 }

			 function getcashinhand(){
					 //***$initialcashqry="select cash_amt from ss_cash where status='1'";
					 $initialcashqry="select sum(cash_amt) as cash_amt from ss_cash where (status='1' or status='2')";
					 $initialcashamt=0;
					 $totbuycash=0;
					 $totsellcash=0;
					 $allresintcash=exec_query($initialcashqry);
					 if(count($allresintcash)>0){
						 $initialcashamt=exec_query($initialcashqry,1);
					 }

					 //**	120208 hided as we are only displaying by/sell
					 $sql_totbuyamtofalltrans="select sum(quantity*unit_price) as totbuyamt from ss_transaction where status='1' and (transaction_type='0')";
					 $allresbuy=exec_query($sql_totbuyamtofalltrans);
					 if(count($allresbuy)>0){
					 $totbuycash=exec_query($sql_totbuyamtofalltrans,1);
					 }

					 $sql_totsellamtofalltrans="select sum(quantity*unit_price) as totsell from ss_transaction where status='1' and (transaction_type='1')";
					 $allressell=exec_query($sql_totsellamtofalltrans);
					 if(count($allressell)>0){
					 $totsellcash=exec_query($sql_totsellamtofalltrans,1);
					 }
					 $totcashinhand=($initialcashamt['cash_amt']-$totbuycash['totbuyamt'])+$totsellcash['totsell'];
					 return $totcashinhand;

			 }

			 function getexchangevaldetails($exchangename,$inceptiondate){
				 if(isset($inceptiondate)){
					 if($exchangename=='S&P'){
						 $exchangename='5EGSPC';
					 }else if($exchangename=='nasdaq'){
						 $exchangename='5EIXIC';
					 }
					 $afterexp=explode('-',$inceptiondate);
			//htmlprint_r($afterexp);
					 $iy=$afterexp[0];
					 $im=$afterexp[1]-1;
					 $id=$afterexp[2];

					 if($id!=1){
					 $id=$afterexp[2]-1;
					 }else{
						 $id=$afterexp[2];
					 }
					 $curdare=date('Y-m-d');
					 $afterexpcur=explode('-',$curdare);
					 $iy1=$afterexpcur[0];
					 $im1=$afterexpcur[1]-1;
					 $id1=$afterexpcur[2];

					 if($id1!=1){
						 $id1=$afterexpcur[2]-1;
					 }else{
						 $id1=$afterexpcur[2];
					 }


			//2008-02-01
			//5EGSPC S&P
			//5EIXIC= nasdaq

				 //http://ichart.finance.yahoo.com/table.csv?s=%5EIXIC&a=01&b=7&c=2008&d=01&e=7&f=2008&g=d&ignore=.csv
					 $tickersymbol=$exchangename;
					 if (isset($tickersymbol))
					 {
					 //******$filename="http://ichart.finance.yahoo.com/table.csv?s=%$exchangename&a=$id&b=$im&c=$iy&d=$id1&e=$im1&f=$iy1&g=d&ignore=.csv";
					 if(isset($filename)){unset($filename);}
					 $filename="http://ichart.finance.yahoo.com/table.csv?s=%$exchangename&a=$im&b=$id&c=$iy&d=$im1&e=$id1&f=$iy1&g=d&ignore=.csv";
		/*******/
		if(isset($fileforlatest)){unset($fileforlatest);}
		$fileforlatest="http://download.finance.yahoo.com/d/quotes.csv?s=%$tickersymbol&f=sl1d1t1c1ohgvn&e=.csv";
		//echo "<br>".$fileforlatest;
		$openlatest = file($fileforlatest);
		//htmlprint_r($openlatest);
	    if(isset($currentsymvalarrlatest)){unset($currentsymvalarrlatest);}
		$currentsymvalarrlatest=array();
		$currentsymvalarrlatest=explode(',',($openlatest[0]));
		//htmlprint_r($currentsymvalarrlatest);
	    $currentopenpostionval=0;
		$currentopenpostionval=$currentsymvalarrlatest[1];
		//echo "<br>curent val : ".$currentopenpostionval;
		/*********************/
						 $open = file($filename);
						 if(isset($open)){
							 if(isset($currentsymvalarr)){unset($currentsymvalarr);}
							 $currentsymvalarr=array();
							 $totfetched=count($open);

							 $firsttotfetched=0;
							 $lasttotfetched=$totfetched-1;

							 $totalcurrentvalue=0;
							 $totaloriginalcost=0;
							 //$currentdateresarr=explode(',',($open[$firsttotfetched+1]));
							 $inceptiondateresarr=explode(',',($open[$lasttotfetched]));
							 $growthinpercnt=0;
							 //$currentopenpostionval=0;
							 $inceptionopenpostionval=0;
							// $currentopenpostionval=$currentdateresarr[1];
							 $inceptionopenpostionval=$inceptiondateresarr[1];
							 $growthinpercnt=(($currentopenpostionval-$inceptionopenpostionval)*100)*(1/$inceptionopenpostionval);
							 return number_format($growthinpercnt, 2, '.', ',');
						 }
					 }else{ return '0.00';}
				 }else{return '0.00';}

			 }

# This function returns the totalcurrent value of balance stocks ( those in hand & transaction_type=0 )
			 function totalcurrentvalueofallstocksbuy(){
				 $stockinhandarr=$this->getallbuystocks();
				 $totalcurrentvalue=0;
				 $qryforallbuytran="select QT.quote_id,QT.transaction_type,ES.stocksymbol,QT.unit_price,QT.quantity from ss_transaction QT, ex_stock ES where  QT.quote_id=ES.id and (QT.transaction_type='0') and QT.status='1' group by QT.quote_id,QT.transaction_type order by QT.transaction_type,ES.companyname,QT.creation_date";
				 $tottransarr=exec_query($qryforallbuytran);
				 if(count($tottransarr)>0){
				 foreach($tottransarr as $res){
				 $symbolname=$res['stocksymbol'];
				 $sid=$res['quote_id'];
				 $currentquote= $this->getcurrentquote($symbolname,$entitytype);
				 $currentval=$currentquote * $stockinhandarr[$sid];
				 $totalcurrentvalue=$totalcurrentvalue+$currentval;
				 }
				 return $totalcurrentvalue;
				 }else{
				 return 0;
				 }
			 }




				 function displayperformance(){
				   global $optionSmith_memcache_expire;
			      $displayperformance=array();
			       $memCacheOpenPosition = new memCacheObj();
				  $key="ssDisplayPerformance";
				   $displayperformance = $memCacheOpenPosition->getKey($key);
			   if(!$displayperformance){
				 global $ytdobj;
				 $inception_query="select substring((min(creation_date)),1,10) as date from ss_transaction where status = 1";
				 $inception_date=exec_query($inception_query,1);
				 //$getYTDAllQuotescurntvalarr=$this->getytdreturnval(); // removed when changes done
				 $getAllQuotescurntvalarr=$this->getAllQuotescurntval();
				 $cashinhand=$this->getcashinhand();
				 $gainorlossamt=$this->getgainlossfrmssbtc();
				 $orgcost=$getAllQuotescurntvalarr['oc'];
				 $curntval=$this->totalcurrentvalueofallstocksbuy();
				 $currentvalueofportfolio=$curntval+$cashinhand+$gainorlossamt;
				 if($orgcost>0){
					 $totavgreturn=number_format(((($currentvalueofportfolio-$orgcost)/$orgcost)*100), 2, '.', ',');
				 }
				 $snppencnt=$this->getexchangevaldetails("S&P",$inception_date[date]);
				 $nasdaqpercnt=$this->getexchangevaldetails("nasdaq",$inception_date[date]);
				 if($inception_date[date]!=''){
					 $dispinception_date=$this->formatdate2Local($inception_date[date]);
				 }else{
					 $dispinception_date="";
				 }
							 $cashinhanddispl=$cashinhand+$gainorlossamt;
							 $totalcashinhand=number_format(($cashinhand+$gainorlossamt), 2,'.',',');
							 $displayperformance['cashinhand']=$totalcashinhand;
							 // Formula : Market Value of Securities = current value of portfolio
							 // Formula ( changed ): 29022008
							// $curntval=$this->totalcurrentvalueofallstocksbuy();
							 $totvalueofflex=($curntval);//+($this->getgainlossfrmssbtc());
									//$totvalueofflex=0;
						    $marketvalueofsecurities=number_format(($totvalueofflex), 2,'.',',');
							$displayperformance['marketvalueofsecurities']=$marketvalueofsecurities;
							$totvalofporto=0;
							$totvalofporto=number_format(($cashinhanddispl+$totvalueofflex), 2,'.',',');
							$displayperformance['totvalofporto']=$totvalofporto;
							 $gainorlossamt=number_format($gainorlossamt,2,'.',',');
							 $orgcost=number_format($orgcost, 2, '.', ',');
							 $displayperformance['orgcost']=$orgcost;
							 $currentvalueofportfolio=number_format($currentvalueofportfolio, 2, '.', ',');
							 $displayperformance['currentvalueofportfolio']=$currentvalueofportfolio;
								if(Date('Y')>='2009'){
									 $ytdret=number_format($ytdobj->ytdreturnpercnt(), 2, '.', ',');
									 $displayperformance['ytdret']=$ytdret;
								}
								 $totavgreturn=number_format($totavgreturn, 2, '.', ',');
								 $displayperformance['totavgreturn']=$totavgreturn;
								 $snppencnt=number_format($snppencnt, 2, '.', ',');
								  $displayperformance['snppencnt']=$snppencnt;
								  $nasdaqpercnt=number_format($nasdaqpercnt, 2, '.', ',');
								  $displayperformance['nasdaqpercnt']=$nasdaqpercnt;
								  $displayperformance['dispinception_date']=$dispinception_date;
		                 $memCacheOpenPosition->setKey($key,$displayperformance,$optionSmith_memcache_expire);
				}
				return $displayperformance;
						// return $strdisp;

			 }

function datadisplayperformance_home($islogin,$home=""){
	 global $ytdobj;
 	 $dataperformance=$this->displayperformance();

?>
<div class="right_common_head">
<h2>flexfolio performance</h2>
</div>
<div>
<table class="quint_sub_heading" align="right" width="95%" border="0"
	cellspacing="5" cellpadding="0">
  <tr>
    <td>Original cost</td>
    <td>$ <?=$dataperformance['orgcost']?></td>
  </tr>
  <tr>
    <td>Current Value of Flexfolio</td>
    <td>$ <?=$dataperformance['currentvalueofportfolio']; ?></td>
  </tr>
  <tr>
    <td>Flexfolio Total Average Return</td>
    <td><?=$dataperformance['totavgreturn']?> %</td>
  </tr>
  <tr>
    <td>Nasdaq</td>
    <td><?=$dataperformance['nasdaqpercnt'];?> %</td>
  </tr>
  <tr>
    <td colspan="2" class="quint_small_heading">Since Inception <?=$dataperformance['dispinception_date'];?></td>
  </tr>
</table>
</div>
<? }







		 /*******************deepika***/
				 function datadisplayperformance($islogin,$home=""){
				 global $ytdobj;
	 $dataperformance=$this->displayperformance($islogin);

		$strdisp='<table width="100%" border="0" cellpadding="0" cellspacing="0" align="left">';

	if($home!=1){
		$strdisp.='<tr>	<td  valign="top" width="310">

		<div style="border:solid 1px #cccccc; margin-bottom:10px; width:310px;">
		<div class="right_common_head">
		<h2>current value of optionsmith</h2>
		</div><table width="309" class="market_heading" border="0" cellpadding="0" cellspacing="0">
		<tr>
		<td>cash in hand</td><td>$';
		 $strdisp .=$dataperformance['cashinhand'].'</td>
		</tr>
		<tr class="market_grey_row">
		<td>market value of securities</td>
		<td>$';
		$strdisp .=$dataperformance['marketvalueofsecurities'].'</td>
		</tr>
		<tr>
		<td>total flexfolio value</td>
		<td>$';
		$strdisp .=$dataperformance['totvalofporto'].'</td>
		</tr>
		</table>
		</div> ';

		//$strdisp.='<tr><td colspan=2>&nbsp;</td></tr>';
	}
 return $strdisp;

}

function displayoptionperformence($islogin,$home=""){
		global $ytdobj;
		$dataperformance=$this->displayperformance($islogin);

		$strdisp.='<div class="right_common_container_flexfolio">
		<div class="right_common_head">
		<h2>optionsmith performance</h2>
		</div>
		<table width="100%" class="market_heading" border="0" cellpadding="0" cellspacing="0">';

		if($islogin){
			$strdisp.='<tr><td >Original Cost </td><td >$ ';
			$strdisp.=$dataperformance['orgcost'].'</td></tr>
			<tr class="market_grey_row">
			<td>Current Value of OptionSmith </td><td >$ ';
			$strdisp.=$dataperformance['currentvalueofportfolio'].'</td>
			</tr>';
			if(Date('Y')>='2009'){
				$strdisp.='<tr>
				<td >';
				$strdisp.=Date('Y').' YTD Return </td>
				<td class="quintsubhedingname1">';
				$strdisp.=$dataperformance['ytdret'].'%</td>
				</tr>';
			}
		}
		$strdisp.='<tr>
		<td class="market_grey_row">OptionSmith Total Average Return </td>
		<td class="market_grey_row">';
		$strdisp.=$dataperformance['totavgreturn'].'%</td>
		</tr>
		<tr>
		<td class="market_grey_row">Nasdaq</td>
		<td class="market_grey_row">';
		$strdisp.=$dataperformance['nasdaqpercnt'].'%</td>
		</tr>
		<tr>
		<td colspan="2" >              <div class="divider_container">
		*since inception '.$dataperformance['dispinception_date'].'
		</div>  </td>
		</tr>
		</table>
		</td>
		</tr></table></div>';

		return $strdisp;
	}


function datadisplayperformance_old($islogin,$home=""){
global $ytdobj;
$dataperformance=$this->displayperformance(NULL);
				  if($home!=1){
				 	$strdisp='<table cellpadding="0" cellspacing="0" width="400px;">';
				   }else{
				 $strdisp='<table cellpadding="0" cellspacing="0" width="100%;">';
				   }
				 if($home!=1){
					$strdisp.='<tr>
					<td class="FeatureHeaderGrayBgDouble" >Current Value of Flexfolio</td>
					</tr>
					<tr>
					<td colspan="2" valign="top">
							<table width="402px" cellspacing="0" cellpadding="0" style="border-collapse: collapse;" border="0" bordercolor="#cccccc" >
							<tr>
							<td width="27%" class="quintsubhedingname1">Cash in Hand</td>
							<td width="26%" class="quintsubhedingname1">$ ';
							 $strdisp.=$dataperformance['cashinhand'].'</td>
							</tr>


						   <tr>
						   <td width="27%" class="quintsubhedingname1">Market Value of Securities</td>
						   <td width="26%" class="quintsubhedingname1">$ ';

							 // Formula : Market Value of Securities = current value of portfolio
							 // Formula ( changed ): 29022008
							//$totvalueofflex=0;
							$strdisp.=$dataperformance['marketvalueofsecurities'].'</td>
						</tr>
						<tr>
							<td width="27%" class="quintsubhedingname1">Total OptionSmith Value</td>
							<td width="26%" class="quintsubhedingname1">$ ';
							$strdisp.=$dataperformance['totvalofporto'].'</td>
						</tr>
					</table>
					</td></tr>';

					$strdisp.='<tr><td colspan=2>&nbsp;</td></tr>';
					}
					$strdisp.='<tr>
					<td class="FeatureHeaderGrayBgDouble">Flexfolio Performance</td>
					</tr>
					<tr>
					<td colspan="2" valign="top">
								<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;" border="0" bordercolor="#cccccc" >';
								if($islogin){

									$strdisp.='<tr>
								<td width="27%" class="quintsubhedingname1">Original Cost </td>
								<td width="26%" class="quintsubhedingname1">$ ';

								 $strdisp.=$dataperformance['orgcost'].'</td>
								</tr>
								<tr>
								<td class="quintsubhedingname1">Current Value of Flexfolio </td>
								<td class="quintsubhedingname1">$ ';
								 $strdisp.=$dataperformance['currentvalueofportfolio'].'</td>
								</tr>';

								if(Date('Y')>='2009'){
								$strdisp.='<tr>
								<td class="quintsubhedingname1">';
								$strdisp.=Date('Y').' YTD Return </td>
								<td class="quintsubhedingname1">';
								 $strdisp.=$dataperformance['ytdret'].'%</td>
									</tr>';
								}
								}
								$strdisp.='<tr>
								<td width="27%" class="quintsubhedingname1">Flexfolio Total Average Return </td>
								<td width="26%" class="quintsubhedingname1">';
								 $strdisp.=$dataperformance['totavgreturn'].'%</td>
								</tr>
								<tr>
								<td class="quintsubhedingname1">Nasdaq</td>
								<td class="quintsubhedingname1">';
								 $strdisp.=$dataperformance['nasdaqpercnt'].'%</td>
								</tr>
								<tr>
								<td colspan="2" class="Disctoday">*Since Inception '.$dataperformance['dispinception_date'].'</td>
								</tr>
								</table>
						</td>
						</tr></table>';

				 return $strdisp;

			 }


				 function displayperformanceforhome($islogin){
					 global $ytdobj;
					 $inception_query="select substring((min(creation_date)),1,10) as date from ss_transaction where status = 1";
					 $inception_date=exec_query($inception_query,1);
					 //$getYTDAllQuotescurntvalarr=$this->getytdreturnval(); // removed when changes done
					 $getAllQuotescurntvalarr=$this->getAllQuotescurntval();
					 $cashinhand=$this->getcashinhand();
					 $gainorlossamt=$this->getgainlossfrmssbtc();
					 $orgcost=$getAllQuotescurntvalarr['oc'];

					 $curntval=$this->totalcurrentvalueofallstocksbuy();

					 $currentvalueofportfolio=($curntval)+($this->getcashinhand())+($this->getgainlossfrmssbtc());

					 $totavgreturn=number_format(((($currentvalueofportfolio-$orgcost)/$orgcost)*100), 2, '.', ',');
					 $snppencnt=$this->getexchangevaldetails("S&P",$inception_date[date]);
					 $nasdaqpercnt=$this->getexchangevaldetails("nasdaq",$inception_date[date]);
					 if($inception_date[date]!=''){
					 $dispinception_date=$this->formatdate2Local($inception_date[date]);
					 }else{
						 $dispinception_date="";
					 }
					 $strdisp='<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;" border="1" bordercolor="#cccccc" >';
					 if($islogin){
					 $strdisp.='<tr>
						 <td width="27%" class="quintsubhedingname1">Original Cost </td>
						 <td width="26%" class="quintsubhedingname1">$ ';
						 $orgcost=number_format($orgcost, 2, '.', ',');
						 $strdisp.=$orgcost.'</td>
						 </tr>
						 <tr>
						 <td class="quintsubhedingname1">Current Value of Flexfolio</td><td class="quintsubhedingname1"> $ ';
						 $currentvalueofportfolio=number_format($currentvalueofportfolio, 2, '.', ',');
						 $strdisp.=$currentvalueofportfolio.'</td>
						 </tr>';
						 if(Date('Y')>='2009'){
							$strdisp.='<tr>
						 <td class="quintsubhedingname1">';
						 $strdisp.=Date('Y').' YTD Return </td>
					     <td class="quintsubhedingname1">';
						 //$ytdret=number_format($getYTDAllQuotescurntvalarr['avg'], 2, '.', ',');
						 $ytdret=number_format($ytdobj->ytdreturnpercnt(), 2, '.', ',');

						 $strdisp.=$ytdret.'%</td>
						 </tr>';
						 }
						 }
						 $strdisp.='<tr>
						 <td class="quintsubhedingname1">Flexfolio Total Average Return </td>
						 <td class="quintsubhedingname1">';
						 $totavgreturn=number_format($totavgreturn, 2, '.', ',');
						 $strdisp.=$totavgreturn.'%</td>
						 </tr>
						 <tr>
						 <td class="quintsubhedingname1">S&P 500 </td>
					     <td class="quintsubhedingname1">';
						 $snppencnt=number_format($snppencnt, 2, '.', ',');
						 $strdisp.=$snppencnt.'%</td>
						 </tr>
						 <tr>
						 <td class="quintsubhedingname1">Nasdaq</td>
					     <td class="quintsubhedingname1">';
						 $nasdaqpercnt=number_format($nasdaqpercnt, 2, '.', ',');
						 $strdisp.=$nasdaqpercnt.'%</td>
						 </tr>
						 <tr>
						 <td colspan="2" class="Disctoday">*Since Inception '.$dispinception_date.'</td>
						 </tr>
					     </table>';
					 return $strdisp;
				 }


function addinjsfile(){
	global $HTPFX,$HTHOST,$D_R;

	$strStockTagQuery  = "select id,stocksymbol as tag,CompanyName,exchange from ex_stock order by stocksymbol";
	$strStockTag="";
	foreach(exec_query($strStockTagQuery) as $row)
	{
		if($strStockTag==""){
			$strStockTag.= $row[tag].' : '.$row[CompanyName].' : '.$row[exchange] ;
		}
		else{
			$strStockTag.= '","'.$row[tag].':'.$row[CompanyName].':'.$row[exchange] ;
		}
	}
	$strTags=$strStockTag;
	$strTags='var customarray=new Array("'.$strTags.'");';
	global $CDN_BUCKET;
	$fname=$CDN_BUCKET."/js/stock_suggestion.js";
	$options = [ "gs" => [ "Content-Type" => "text/plain", "acl" => "public-read" ]];
	$ctx = stream_context_create($options);
	file_put_contents($fname, $strTags, 0, $ctx);
	}
/* Aswini for smith start */
public function updatetransaction($type,$description=null,$optionunitprice=null,$recordid,$transtype,$quoteid)
{
	if($type=='update')
	{
		$updated=0; // YTD
		switch ($transtype)
		{
			case 0:
				$transupdate=$this->updatestockbuytbl($updatedshares,$optionunitprice,$description,'1',$recordid);
				if(isset($transupdate)&& ($transupdate>0))
				{
					$lotupdated=$this->updatelottabl($quoteid);
				}
				echo $lotupdated;
				break;
			case 1:
				$transupdate=$this->updatestockbuytbl($updatedshares,$optionunitprice,$description,'1',$recordid); // only update the transaction table
				echo $transupdate;
				break;
			case 2:
				$transupdate=$this->updatestockbuytbl($updatedshares,$optionunitprice,$description,'1',$recordid); // only update the transaction table
				echo $transupdate;
				break;
			case 3:
				$transupdate=$this->updatestockbuytbl($updatedshares,$optionunitprice,$description,'1',$recordid); // only update the transaction table
				echo $transupdate;
				break;
			default:
				echo $updated;
		}
	}
	else if($type=='delete')
	{
		$updated=0;
		switch ($transtype)
		{
			case 0:
				$transupdate=$this->updatestockbuytbl($updatedshares,$optionunitprice,$description,'0',$recordid);
				$updated=$this->updatelottabl($quoteid);
				$sellupdated=$this->updateselltabl($recordid,$transtype);
				echo $sellupdated;
				break;
			case 1:
				$transupdate=$this->deletestockbuytbl($updatedshares,$optionunitprice,$description,'0',$recordid);
				if(isset($transupdate))
				{
				$sellupdated=$this->deleteselltabl($recordid,$transtype);
				if(isset($sellupdated))
				{
					$updated=$this->updatelottabl($quoteid);
				}
				}
				echo $updated;
				break;
			case 2:
				$transupdate=$this->deletestockbuytbl($updatedshares,$optionunitprice,$description,'0',$recordid);
				$sellupdated=$this->deleteselltabl($recordid,$transtype);
				if(isset($sellupdated))
				{
					$updated=$this->updatelottabl($quoteid);
				}
				echo $updated;
				break;
			case 3:
				$transupdate=$this->deletestockbuytbl($updatedshares,$optionunitprice,$description,'0',$recordid);
				if(isset($transupdate)){$sellupdated=$this->deleteselltabl($recordid,$transtype);}
				echo $sellupdated;
				break;
			default:
			echo $updated;
		}
	}
}
public function updatestockbuytbl($updatedshares,$unitprice,$description,$status,$idtupdate)
{
	$tablename='ss_transaction';
	$req_update=array();
	$req_update['updation_date']=date('Y-m-d H:i:s');
	$req_update['unit_price']=$unitprice;
	$req_update['description']=$description;
	$req_update['status']=$status;
	$ret_up=update_query($tablename,$req_update,array(id=>$idtupdate));
	$memCacheDelete = new memCacheObj();
	$key="ssOpenPositiontrade";
	$memCacheDelete->deleteKey($key);
	$key="ssOpenPosition";
	$memCacheDelete->deleteKey($key);
	$key="ssDisplayPerformance";
	$memCacheDelete->deleteKey($key);
	return $ret_up;
}

public function deletestockbuytbl($updatedshares,$unitprice,$description,$status,$idtupdate)
{
	$tablename='ss_transaction';
	$req_update=array();
	$req_update['updation_date']=date('Y-m-d H:i:s');
	//$req_update['unit_price']=$unitprice;
	//$req_update['description']=$description;
	$req_update['status']=$status;
	$ret_up=update_query($tablename,$req_update,array('id'=>$idtupdate));
	$memCacheDelete = new memCacheObj();
	$key="ssOpenPositiontrade";
	$memCacheDelete->deleteKey($key);
	$key="ssOpenPosition";
	$memCacheDelete->deleteKey($key);
	$key="ssDisplayPerformance";
	$memCacheDelete->deleteKey($key);
	return $ret_up;
}

public function deleteselltabl($autoids,$transtype)
{
	$updatedon=date('Y-m-d H:i:s');
	$selltransactiondata_up=array(status=>'0','updation_date'=>$updatedon);

	if($transtype==1)
	{ // while its sell
		$retstat=0;
		$retstat=update_query("ss_sell_transaction",$selltransactiondata_up,array('sell_trans_id'=>$autoids));
		return $retstat;
	}
	else if($transtype==3)
	{
		$retstat=0;
		$retstat=update_query("ss_sell_transaction",$selltransactiondata_up,array('buy_trans_id'=>$autoids,'entity_type'=>'0'));
		//get the sell_trans_id from sell table against this BTC
		$sqlbtc="select sell_trans_id from ss_sell_transaction where buy_trans_id='$autoids' and entity_type='0'";
		$searchfound=exec_query($sqlbtc,1);
		$stid=$searchfound['sell_trans_id'];
		if($stid!=0 && isset($stid))
		{
			$req_update=array();
			$req_update['updation_date']=$updatedon;
			$req_update['trans_status']='pending';
			$ret_up=update_query('ss_sell_transaction',$req_update,array('sell_trans_id'=>$stid,'buy_trans_id'=>'0'));
			if(isset($ret_up))
			{
				$memCacheDelete = new memCacheObj();
				$key="ssOpenPositiontrade";
				$memCacheDelete->deleteKey($key);
				$key="ssOpenPosition";
				$memCacheDelete->deleteKey($key);
				$key="ssDisplayPerformance";
				$memCacheDelete->deleteKey($key);
			}
		}
		return $retstat;
	}
	else if($transtype==2)
	{
		$retstat=0;
		$retstat=update_query("ss_sell_transaction",$selltransactiondata_up,array('sell_trans_id'=>$autoids));
		$sql_selectdisbledids="select buy_trans_id from ss_sell_transaction where entity_type='0' and sell_trans_id='$autoids' and status=0 and buy_trans_id!='0'";
		$allressym=exec_query($sql_selectdisbledids);
		$totalrecds=0;
		if(count($allressym)>0)
		{
			foreach($allressym as $alltransres)
			{
				$idtoupdate=$alltransres['buy_trans_id'];
				unset($req_update);
				$req_update=array();
				$req_update['updation_date']=$updatedon;
				$req_update['status']=0;
				$retstat=update_query("ss_transaction",$req_update,array('id'=>$idtoupdate));
			}
		$memCacheDelete = new memCacheObj();
		$key="ssOpenPositiontrade";
		$memCacheDelete->deleteKey($key);
		$key="ssOpenPosition";
		$memCacheDelete->deleteKey($key);
		$key="ssDisplayPerformance";
		$memCacheDelete->deleteKey($key);
		}
		return $retstat;
	}else
	{
		$retstat=0;
		$retstat=update_query("ss_sell_transaction",$selltransactiondata_up,array(buy_trans_id=>$autoids));
		$sql_selectdisbledids="select sell_trans_id from ss_sell_transaction where buy_trans_id='$autoids' and status=0";
		$allressym=exec_query($sql_selectdisbledids);
		$totalrecds=0;
		if(count($allressym)>0){
			foreach($allressym as $alltransres){
				$idtoupdate=$alltransres['sell_trans_id'];
				unset($req_update);
				$req_update=array();
				$req_update['updation_date']=$updatedon;
				$req_update['status']=0;
				$retstat=update_query("ss_transaction",$req_update,array(id=>$idtoupdate));
			}
		}
		$memCacheDelete = new memCacheObj();
		$key="ssOpenPositiontrade";
		$memCacheDelete->deleteKey($key);
		$key="ssOpenPosition";
		$memCacheDelete->deleteKey($key);
		$key="ssDisplayPerformance";
		$memCacheDelete->deleteKey($key);
		return $retstat; // 0 return means there was no entry against this buy transaction
	}
}


/* Aswini for smith End */
public function getstockSymboldetails($tickersymbol)
{
	$sid=0;
	if(isset($tickersymbol))
	{
		$handle = fopen("http://download.finance.yahoo.com/d/quotes.csv?s=$tickersymbol&f=sl1d1t1c1ohgvn&e=.csv", "r");
		if(isset($handle))
		{
			$read = fread($handle, 2000);
			fclose($handle);
			$strreplaceread = str_replace("\"", "", $read);
			$read = explode(",", $strreplaceread);
			return $read;
		}
	}
	return $sid;
}
}

function getcurrentquotefeed($tickersymbol,$entitytype=""){

    $m=date("m")-1;
	$d=date("d")-1;
	$y=date("Y");
	if (isset($tickersymbol))
		{
		 if($entitytype=="1"){
		 	$open = fopen("http://download.finance.yahoo.com/d/quotes.csv?s=$tickersymbol.X&f=sl1d1t1c1ohgvn&e=.csv", "r");
		 }else{
			$open = fopen("http://download.finance.yahoo.com/d/quotes.csv?s=$tickersymbol&f=sl1d1t1c1ohgvn&e=.csv", "r");
		 }
			$read = fread($open, 2000);
			fclose($open);
			$read = str_replace("\"", "", $read);
			$read = explode(",", $read);
			if ($read[1] == 0)
			{
			  if($entitytype!=="1"){
				$open = fopen("http://ichart.finance.yahoo.com/table.csv?s=$tickersymbol&a=$m&b=$d&c=$y&d=$m&e=$d&f=$y&g=d&ignore=.csv", "r");$read = fread($open, 2000);
			 }
				fclose($open);
				$read = str_replace("\"", "", $read);
				$read = explode(",", $read);
				if ($read[12] == 0)	{
					return 0;
				}else{
					return $read[12];
				}
			}else{
				return $read[1];
			}
	}

}





class optionPortfolio
{

	public function updateOpenPositionRecords()
			{
				$sql="SELECT ST.id,ST.entity_type,ST.quote_id,ST.transaction_type,ST.creation_date,ST.unit_price,ST.quantity,
	(SELECT SUM(quantity) quantity FROM ss_transaction STINNER ,ss_sell_transaction SST
	WHERE STINNER.status =1 AND SST.sell_trans_id = STINNER.id AND SST.buy_trans_id = ST.id
	GROUP BY ST.id)AS sold_quantity FROM ss_transaction AS ST WHERE transaction_type = 0
	AND STATUS =1

	UNION

	SELECT ST.id,ST.entity_type,ST.quote_id,ST.transaction_type,ST.creation_date,ST.unit_price,ST.quantity,
	(SELECT SUM(quantity) quantity FROM ss_transaction STINNER ,ss_sell_transaction SST
	WHERE STINNER.status =1 AND SST.sell_trans_id = ST.id AND SST.buy_trans_id = STINNER.id
	GROUP BY ST.id) AS sold_quantity FROM ss_transaction AS ST WHERE transaction_type = '2'
	AND STATUS ='1' ORDER BY id";
				$result = exec_query($sql);
				if(count($result)>0)
				{
					exec_query_nores('delete from ss_openpositions where transaction_id>0');
					foreach($result as $key=>$value)
					{
						$temptabledata['transaction_id']=$value['id'];
						$temptabledata['quote_id']=$value['quote_id'];
						$temptabledata['transaction_type']=$value['transaction_type'];
						$temptabledata['creation_date']=$value['creation_date'];
						$temptabledata['unit_price']=$value['unit_price'];
						$temptabledata['quantity']=$value['quantity'];
						$temptabledata['sold_quantity']=$value['sold_quantity'];
						$temptabledata['entity_type']=$value['entity_type'];
						if($temptabledata['quantity']!=$temptabledata['sold_quantity'])
						{
							$sid=insert_query("ss_openpositions",$temptabledata);
						}
						unset($temptabledata);
					}
				}
			}

			public function getOpenPositionsByTrade($order='company')
							{
								global $optionSmith_memcache_expire;
								$memCacheOpenPosition = new memCacheObj();
								$key="ssOpenPositiontrade";
								$dataOpenPosition = $memCacheOpenPosition->getKey($key);
								if(!$dataOpenPosition && !$this->openPosition)
								{
									$qryGetOpenPositions="select concat(ES.companyname,' ',DATE_FORMAT(ST.expirydate,'%b\'%y'),' ',strike_price,' ',UCASE(option_type)) as companyname,SOP.entity_type,
									EO.optionticker ticker,SOP.transaction_type,DATE_FORMAT(SOP.creation_date,'%m/%d/%y') as creation_date_get,
									round((SOP.quantity-SOP.sold_quantity)/100) as contracts,round(SOP.quantity*SOP.unit_price/SOP.quantity,2) as costbasic
									from ss_openpositions SOP,ex_stock ES,ex_option EO,ss_transaction ST
									where SOP.quote_id=EO.id and EO.baseStockId=ES.id and ST.id=SOP.transaction_id and SOP.entity_type='1'
									union
									select ES.CompanyName as companyname,SOP.entity_type,
									ES.stocksymbol ticker,SOP.transaction_type,DATE_FORMAT(SOP.creation_date,'%m/%d/%y') as creation_date_get,
									(SOP.quantity-SOP.sold_quantity) as contracts,round(SOP.quantity*SOP.unit_price/SOP.quantity,2) as costbasic
									from ss_openpositions SOP,ex_stock ES,ss_transaction ST
									where SOP.quote_id=ES.id and ST.id=SOP.transaction_id and SOP.entity_type='0'
									order by creation_date_get desc";

									$resGetOpenPositions=exec_query($qryGetOpenPositions);
									if($resGetOpenPositions){
										foreach($resGetOpenPositions as $openPosition)
										{
											$this->openPosition[$openPosition['ticker']]['compName']=$openPosition['companyname'];
											$this->openPosition[$openPosition['ticker']]['ticker']=$openPosition['ticker'];
											$this->openPosition[$openPosition['ticker']]['transaction_type']=$openPosition['transaction_type'];
											$this->openPosition[$openPosition['ticker']]['openDate']=$openPosition['creation_date_get'];
											if($openPosition['transaction_type']=='0'){
												$this->openPosition[$openPosition['ticker']]['tradeType']='L';
												$this->openPosition[$openPosition['ticker']]['contracts']=$openPosition['contracts'];
											}
											elseif($openPosition['transaction_type']=='2'){
												$this->openPosition[$openPosition['ticker']]['tradeType']='S';
												$this->openPosition[$openPosition['ticker']]['contracts']=-$openPosition['contracts'];
											}
											else{
												$this->openPosition[$openPosition['ticker']]['tradeType']='N/A';
												$this->openPosition[$openPosition['ticker']]['contracts']=$openPosition['contracts'];
											}
											$this->openPosition[$openPosition['ticker']]['costBasis']=$openPosition['costbasic'];
											if($openPosition['entity_type']=='1'){
												$optionsymbols[]=$openPosition['ticker'];
											}else{
												$stocksymbols[]=$openPosition['ticker'];
											}
										}
									$objPerformance=new Performance();
									$currentOpenOptions=$objPerformance->getCurrentStockValue($optionsymbols);
				if(!empty($optionsymbols)){
									foreach($optionsymbols as $symbol)
									{
										$this->openPosition[$symbol]['currentQuote']=$currentOpenOptions[$symbol];
										$this->openPosition[$symbol]['currentValue']=$currentOpenOptions[$symbol]*$this->openPosition[$symbol]['contracts']*100;
										$this->openPosition[$symbol]['gainorloss']=round(($this->openPosition[$symbol]['currentQuote'] - $this->openPosition[$symbol]['costBasis'])*$this->openPosition[$symbol]['contracts']*100,2);
									}
				}
									$currentOpenStocks=$objPerformance->getCurrentStockValue($stocksymbols,1);
				if(!empty($stocksymbols)){
									foreach($stocksymbols as $symbol)
									{
										$this->openPosition[$symbol]['currentQuote']=$currentOpenStocks[$symbol];
										$this->openPosition[$symbol]['currentValue']=$currentOpenStocks[$symbol]*$this->openPosition[$symbol]['contracts'];
										$this->openPosition[$symbol]['gainorloss']=round(($this->openPosition[$symbol]['currentQuote'] - $this->openPosition[$symbol]['costBasis'])*$this->openPosition[$symbol]['contracts'],2);
									}
									}
			}
									$memCacheOpenPosition->setKey($key,$this->openPosition,$optionSmith_memcache_expire);
								}elseif($dataOpenPosition)
								{
									$this->openPosition=$dataOpenPosition;
								}
								return $this->openPosition;
	}



	public function getOpenPositions($order='company')
				{
					global $optionSmith_memcache_expire;
					$memCacheOpenPosition = new memCacheObj();
					$key="ssOpenPosition";
					$dataOpenPosition = $memCacheOpenPosition->getKey($key);
					if(!$dataOpenPosition && !$this->openPosition)
					{
						$qryGetOpenPositions="select concat(ES.companyname,' ',DATE_FORMAT(ST.expirydate,'%b\'%y'),' ',strike_price,' ',UCASE(option_type)) as companyname,
						EO.optionticker ticker,SOP.transaction_type,DATE_FORMAT(SOP.creation_date,'%m/%d/%y') as creation_date_get,
						round(SUM(SOP.quantity-SOP.sold_quantity)/100) as contracts,round(SUM(SOP.quantity*SOP.unit_price)/SUM(SOP.quantity),2) as costbasic,SOP.entity_type
						from ss_openpositions SOP,ex_stock ES,ex_option EO,ss_transaction ST
						where SOP.quote_id=EO.id and EO.baseStockId=ES.id and ST.id=SOP.transaction_id and SOP.entity_type='1'
						group by EO.optionticker
						union
						select ES.companyname as companyname,ES.stocksymbol ticker,SOP.transaction_type,
						DATE_FORMAT(SOP.creation_date,'%m/%d/%y') as creation_date_get,
						round(SOP.quantity-SOP.sold_quantity) as contracts,round(SUM(SOP.quantity*SOP.unit_price)/SUM(SOP.quantity),2) as costbasic,SOP.entity_type
						from ss_openpositions SOP,ex_stock ES,ss_transaction ST
						where SOP.quote_id=ES.id and ST.id=SOP.transaction_id and SOP.entity_type='0'
						group by ES.stocksymbol
						order by creation_date_get desc
						";
						$resGetOpenPositions=exec_query($qryGetOpenPositions);
						if($resGetOpenPositions){
							foreach($resGetOpenPositions as $openPosition)
							{
								$this->openPosition[$openPosition['ticker']]['compName']=$openPosition['companyname'];
								$this->openPosition[$openPosition['ticker']]['ticker']=$openPosition['ticker'];
								$this->openPosition[$openPosition['ticker']]['transaction_type']=$openPosition['transaction_type'];
								$this->openPosition[$openPosition['ticker']]['openDate']=$openPosition['creation_date_get'];
								if($openPosition['transaction_type']=='0'){
									$this->openPosition[$openPosition['ticker']]['tradeType']='L';
									$this->openPosition[$openPosition['ticker']]['contracts']=$openPosition['contracts'];
								}
								elseif($openPosition['transaction_type']=='2'){
									$this->openPosition[$openPosition['ticker']]['tradeType']='S';
									$this->openPosition[$openPosition['ticker']]['contracts']=-$openPosition['contracts'];
								}
								else{
									$this->openPosition[$openPosition['ticker']]['tradeType']='N/A';
									$this->openPosition[$openPosition['ticker']]['contracts']=$openPosition['contracts'];
								}
								$this->openPosition[$openPosition['ticker']]['costBasis']=$openPosition['costbasic'];
								if($openPosition['entity_type']=='1'){
									$optionsymbols[]=$openPosition['ticker'];
								}else{
									$stocksymbols[]=$openPosition['ticker'];
								}
							}
						$objPerformance=new Performance();
						$currentOpenOptions=$objPerformance->getCurrentStockValue($optionsymbols);
						foreach($optionsymbols as $symbol)
						{
							$this->openPosition[$symbol]['currentQuote']=$currentOpenOptions[$symbol];
							$this->openPosition[$symbol]['currentValue']=$currentOpenOptions[$symbol]*$this->openPosition[$symbol]['contracts']*100;
							$this->openPosition[$symbol]['gainorloss']=round(($this->openPosition[$symbol]['currentQuote'] - $this->openPosition[$symbol]['costBasis'])*$this->openPosition[$symbol]['contracts']*100);
						}
						$currentOpenStocks=$objPerformance->getCurrentStockValue($stocksymbols,1);
						foreach($stocksymbols as $symbol)
						{
							$this->openPosition[$symbol]['currentQuote']=$currentOpenStocks[$symbol];
							$this->openPosition[$symbol]['currentValue']=$currentOpenStocks[$symbol]*$this->openPosition[$symbol]['contracts'];
							$this->openPosition[$symbol]['gainorloss']=round(($this->openPosition[$symbol]['currentQuote'] - $this->openPosition[$symbol]['costBasis'])*$this->openPosition[$symbol]['contracts']);
						}
						}
						$memCacheOpenPosition->setKey($key,$this->openPosition,$optionSmith_memcache_expire);
					}elseif($dataOpenPosition)
					{
						$this->openPosition=$dataOpenPosition;
					}
					return $this->openPosition;
	}


}

?>
