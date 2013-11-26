<?
class techstratTransaction
{
	public $currentQuotesValue;
	public $performanceData=array();
	public $ytdallgainLossfrombtc=array();
	public $ytdallgainLossfromss=array();
	public $ytdallids=array();
	public $ytdstockinhandarr;
	public $getallbuystocksarray;
	public $allBuyFlag=0;
	public $unitpriceofstockinhands;

	public function __construct($getCurrentQuotes=null)
	{

	}
	function getalldetailsofSymbol($serchsymname){
		global $HTPFX,$HTHOST,$IMG_SERVER;
		if($this->getSymbolid($serchsymname)!=0){			// if the symbol is valid
			$symbid=$this->getSymbolid($serchsymname);		// Get the transaction details of this symbol
			$sqlfortransdetail="select id,creation_date,unit_price,quantity from techstrat_transaction where transaction_type='0' and quote_id='$symbid' and status='1'";
			$allressym='';
			$allressym=exec_query($sqlfortransdetail);
			$totalrecds=0;
			if(!empty($allressym)){
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
					$selltransactionarr1=$this->stocksales($pid);
					$qty=$qty-$selltransactionarr1[$pid];
					$cnt++;
					if($cnt%2==0){
						$tog="#FFFFFF";
					}else{
						$tog="#EFEFEF";
					}
					$strdisp.='<tr bgcolor="'.$tog.'"><td><nobr><input type="hidden" id="hid_'.$pid.'" value="'.$pid.'">'.date('m/d/Y',strtotime($purcgdate)).'<input type="hidden" name="buydate_'.$pid.'" id="buydate_'.$pid.'"  value="'.date('m/d/Y',strtotime($purcgdate)).'"></td><td>'.$qty.'<input type="hidden" name="buyqty_'.$pid.'" id="buyqty_'.$pid.'"  value="'.$qty.'"></td><td>'.$unitprice.'</td><td>
					<input type="text" id="sellqty_'.$pid.'" name="sellqty_'.$pid.'" value="0" size="8" maxlength="20"  onKeyup=javascript:chknosvalidation("'.$fname.'",this)></td>
					<td><input type="text" size="8" maxlength="25"  name="sellprice_'.$pid.'" id="sellprice_'.$pid.'" value="0.00" onKeyup=javascript:chknosvalidation("'.$fname.'",this)></td><td>';
					$strdisp.='<input type="text" size="14" class="techSellDate" name="selldate_'.$pid.'" id="selldate_'.$pid.'" value="" readonly>&nbsp;';
					$str="selldate_".$pid;
					$strdisp.='</td>
						<td><input name="sellnote_'.$pid.'" id="sellnote_'.$pid.'"  type="text" size="35" maxlength="500"></td><td><input type="checkbox" name="sellchk_'.$pid.'" id="sellchk_'.$pid.'" style="border:0px" value="'.$pid.'" class="techSellCheckBox" onClick="javascript:validateTechSellData('.$pid.')"></td>';
					$strdisp.='</tr>';

					if($stockid_get_all1==''){
						$stockid_get_all1=$pid;
					}else{
						$stockid_get_all1=$stockid_get_all1.",".$pid;
					}
				}
				$strdisp.='</table>';
				if($totalrecds>0){
					$strdisp.='<p><img src="'.$IMG_SERVER.'/images/quint_images/btn-savechanges.gif" align="left" onclick="javascript:saveTechStratSellTrans();" style="cursor:pointer;" /></p>';
				}
				$strdisp.='<input type="hidden" name="serchsymname" value='.$serchsymname.'><input type="hidden" name="exchangename" value='.$exchangename.'><input type="hidden" name="totrecords" value='.$totalrecds.'><input type="hidden" name="stockid_get_all1" value='.$stockid_get_all1.'><input type="hidden" id="mode"  name="mode" value="'.$updatemode.'"></form>';
			}else if(count($allressym)==0){
		  		$strdisp = "<b><font color=red>You have nothing to sell against this symbol</font></b>";
			}
		}else if($this->getSymbolid($serchsymname)==0){		// else if the symbol is invalid as per our db
			$strdisp = "<b>Entered Symbol is not Listed.....</b>";
		}
		return $strdisp;
	}

	function getSymbolid($serchsymname){
		$getidfromstocks="select id from ex_stock where stocksymbol= trim('".$serchsymname."') GROUP BY stocksymbol";
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

	public function getstockdetails($symbolname){
		if (isset($symbolname))
		{
			$getStockData=xml2array("http://feeds.financialcontent.com/XMLREST?Account=minyanville&Ticker=".$tickersymbol);
			if(!empty($getStockData))
			{
				return $getStockData['Quote']['Record'];
			}else{
				return 0;
			}
		}
	}

	public function getcurrentquote($symbolname)
	{
		$currentValue=$this->currentQuotesValue[$symbolname];
		if($currentValue=='')
		{
			// If the stock symbol is a valid symbol then get the value
			$chkvaliditystr=$this->validatesymbol($symbolname);
			$chkvalidityarr=explode('~',$chkvaliditystr);
			if($chkvalidityarr['0']==1)
			{
				$this->currentQuotesValue[$symbolname]=$chkvalidityarr['1'];
				return $chkvalidityarr['1'];
			}
			else if($chkvalidityarr['0']==0)
			{
				$this->currentQuotesValue[$symbolname]=$chkvalidityarr['1'];
				return $chkvalidityarr['1'];
			}
		}
		else
		{
			return $currentValue;
		}
	}
	// This function returns the array of total stocks in hand by indexing the quote_id working........
	public function getallbuystocks($dates=NULL)
	{
		if(isset($dates)){
			$append=" and creation_date>='$dates'";
		}else{
			$append='';
		}

		$quer_for_buyids="select creation_date,quote_id,sum(quantity) as totpurch from techstrat_transaction where status='1' and transaction_type='0' $append group by quote_id order by quote_id";
		//echo "<br>".$quer_for_buyids;
		$allbuysarray=exec_query($quer_for_buyids);

		if(count($allbuysarray)>0)
		{
			$stockbuys=array();
			$ytdstockbuys=array();
			global $ytObj;
			$thisYearsFirstTransDate = $ytObj->firsttransactiondate;

			foreach($allbuysarray as $allresultbuys)
			{
				$quotearr[$j]=$allresultbuys['quote_id'];
				if($allresultbuys['creation_date']>=$thisYearsFirstTransDate)
				{
					$ytdstockbuys[]=array('creation_date'=>$allresultbuys['creation_date'],'quote_id'=>$allresultbuys['quote_id'],'totpurch'=>$allresultbuys['totpurch']);
				}
				$j++;
			}
			$allquoteids=implode(",",$quotearr);
			unset($quotearr);
			$quer_for_sellid="select creation_date,quote_id,sum(quantity) as totsell from techstrat_transaction where status='1' and transaction_type='1' and quote_id in($allquoteids) $append group by quote_id order by quote_id";
			//echo $quer_for_sellid;
			$allsellarray=exec_query($quer_for_sellid);
			foreach($allsellarray as $allresultsell)
			{
				$finalSellsArray[$allresultsell['quote_id']]=$allresultsell['totsell'];

				if($allresultsell['creation_date']>=$thisYearsFirstTransDate)
				{
					$YTDfinalSellsArray[$allresultsell['quote_id']]=$allresultsell['totsell'];
				}

			}

			foreach($allbuysarray as $allresultbuy)
			{
				$qid=$allresultbuy['quote_id'];
				$sellqty=0;
				$sellqty=$finalSellsArray[$qid];
				$buyqty=$allresultbuy['totpurch'];
				$stocksinhand=$buyqty-$sellqty;
				$stockbuys[$qid]=$stocksinhand;
			}

			foreach($ytdstockbuys as $ytdallresultbuy)
			{
				$ytdqid=$ytdallresultbuy['quote_id'];
				$ytdsellqty=0;
				$ytdsellqty=$YTDfinalSellsArray[$ytdqid];
				$ytdbuyqty=$ytdallresultbuy['totpurch'];
				$ytdstockbuysinhand[$ytdqid]=($ytdbuyqty-$ytdsellqty);
			}

			$this->allBuyFlag=1;
			$this->getallbuystocksarray=$stockbuys;
			//**** $this->ytdstockinhandarr=$ytdstockbuys;
			$this->ytdstockinhandarr=$ytdstockbuysinhand;
		}
		return $stockbuys;
	}

	public function getcurrent_quote($symbolname)
	{

		$currentValue=$this->currentQuotesValue[$symbolname];
		// If the stock symbol is a valid symbol then get the value

		if($currentValue=='')
		{
			$chkvaliditystr=$this->validate_symbol($symbolname);
			$chkvalidityarr=explode('~',$chkvaliditystr);
			if($chkvalidityarr['0']==1)
			{
				$this->currentQuotesValue[$symbolname]=$chkvalidityarr['1'];
				return $chkvalidityarr['1'];
			}
			else if($chkvalidityarr['0']==0)
			{
				$this->currentQuotesValue[$symbolname]=$chkvalidityarr['1'];
				return $chkvalidityarr['1'];
			}

		}
		else
		{
			return $this->currentQuotesValue[$symbolname];
		}
	}

	// returns total stock inhand for short sell buy to cover for openposition companywise
	public function getallshortselltocks()
	{
		$query_for_shortsellids="select quote_id,sum(quantity) as totpurch from techstrat_transaction where status='1' and transaction_type='2' $append group by quote_id";
		// echo "<br>".$query_for_shortsellids;
		$allr=exec_query($query_for_shortsellids);

		if(count($allr)>0)
		{
			$buytocoverarray=array();
			$query_for_buytocoverid="select quote_id,sum(quantity) as totbuytocover from techstrat_transaction where status='1' and transaction_type='3' group by quote_id";
			$alls=exec_query($query_for_buytocoverid);
			if(count($alls)>0)
			{
				foreach($alls as $allresultbuytocover)
				{
					$buytocoverarray[$allresultbuytocover[quote_id]] = $allresultbuytocover['totbuytocover'];
				}
			}

			$stocksellshort=array();
			foreach($allr as $allresultsortsell)
			{
				$qid=$allresultsortsell['quote_id'];

				if(array_key_exists($qid,$buytocoverarray))
				{
					$buytocoverqty = $buytocoverarray[$qid];
				}else
				{
					$buytocoverqty=0;
				}
				$sortsellqty=$allresultsortsell['totpurch'];
				$stocksinhand=$sortsellqty-$buytocoverqty;
				$stocksellshort[$qid]=$stocksinhand;
			}
		}
		unset($allr);
		unset($alls);
		return $stocksellshort;
	}

	// This function returns the total stocks in hand by passing the quote_id
	function getparticularstocksinh($qid){
		$qry_for_buyqty="select sum(quantity) as totpurch from techstrat_transaction where status='1' and transaction_type='0' and quote_id='$qid' group by quote_id";
		$allr=exec_query($qry_for_buyqty);
		if(count($allr)>0){
			$stocksinhand=0;
			foreach($allr as $allresultbuy){

				$sellqty=0;
				$qry_for_sellid="select sum(quantity) as totsell from techstrat_transaction where status='1' and transaction_type='1' and quote_id='$qid' group by quote_id";
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

	# This function returns the avg unit price of particular quote_id used in edittransaction/QP_selltransaction.htm page
	function avgunitpriceofstockinhand($qid){
		$quer_for_buyids="select sum(quantity) as totpurchqty,sum((unit_price)*(quantity)) as totpurchamt  from techstrat_transaction where status='1' and transaction_type='0' and quote_id='$qid' group by quote_id";
		$allr=exec_query($quer_for_buyids);
		if(count($allr)>0){
			$avgunitpricearr=array();

			foreach($allr as $allresultbuy){
				$totalpurchsdqty=$allresultbuy['totpurchqty']; // Final for each qid
				$totpurchamtget=$allresultbuy['totpurchamt'];
				$totalsoldamt=0;
				$sellqty=0;
				$quer_for_sellid="select id, quantity as totsell from techstrat_transaction where status='1' and transaction_type='1' and quote_id='$qid'";
				$alls=exec_query($quer_for_sellid);
				if(count($alls)>0){
					$soldqty=0;
					$totalsellqty=0;
					foreach($alls as $allresultsell){
						$selltransid=$allresultsell['id'];
						$soldqty=$allresultsell['totsell'];
						$totalsellqty=$totalsellqty+$soldqty;
						$upqry="select QT.unit_price up,QT.id from techstrat_transaction QT, techstrat_sell_transaction QST where QST.sell_trans_id='$selltransid' and QST.buy_trans_id=QT.id";
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
				if(($totalpurchsdqty-$totalsellqty)!=0)
				{
					$avgunitprice=($totpurchamtget-$totalsoldamt)/($totalpurchsdqty-$totalsellqty);
				}else{
					$avgunitprice=0.00;
				}

				$avgunitpricearr[$qid]=$avgunitprice;
			}
		}
		return $avgunitpricearr[$qid];
	}

	function getsinglebuystocks(){
		$quer_for_buyids="select QT.id,QT.quote_id,QT.quantity as totpurch from techstrat_transaction QT, ex_stock ES where QT.quote_id=ES.id and QT.status='1' and QT.transaction_type='0' order by ES.companyname";
		$allr=exec_query($quer_for_buyids);
		if(count($allr)>0){
			$stockbuys=array();
			foreach($allr as $allresultbuy){
				$qid=$allresultbuy['id'];
					// chek for rotal sell amt
					$sellqty=0;
					$quer_for_sellid="select QT.quantity from techstrat_transaction QT, techstrat_sell_transaction QS where QS.buy_trans_id='$qid' and  QS.buy_trans_id=QT.id and QT.transaction_type='1' and QT.status='1'";
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
		$shortsellqry="select QT.id,QT.quote_id,QT.quantity as totpurch from techstrat_transaction QT, ex_stock ES where QT.quote_id=ES.id and QT.status='1' and QT.transaction_type='2' order by ES.companyname,QT.creation_date";
		$allr=exec_query($shortsellqry);
		if(count($allr)>0){
			// $stockbuys=array();
			$shortsell=array();
			foreach($allr as $allshortsell){
				$qid=$allshortsell['id'];
					// chek for total sort sell
					$butytocoverqty=0;
					// query for buy to cover
					$query_for_buytocover="select QT.quantity from techstrat_transaction QT, techstrat_sell_transaction QS where QS.sell_trans_id='$qid' and QS.buy_trans_id=QT.id and QT.transaction_type='3' and QT.status='1'";
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

	public function unitpriceofstockinhand($dates=NULL)
	{
		if(isset($dates))
		{
			$append=" and creation_date>='$dates'";
		}
		else
		{
			$append='';
		}

		$quer_for_buyids="select quote_id,sum(quantity) as totpurchqty,sum((unit_price)*(quantity)) as totpurchamt  from techstrat_transaction where status='1' and transaction_type='0' $append group by quote_id order by quote_id";
		$allr=exec_query($quer_for_buyids);

		if(count($allr)>0)
		{
			$stockbuys=array();

			foreach($allr as $allresultbuy)
			{
				$qids[$j]=$allresultbuy['quote_id'];
				$j++;
			}
			$alqids = implode(",",$qids);
			$quer_for_sellid="select quote_id,id,sum(quantity) as totsell from techstrat_transaction where status='1' and transaction_type='1' and quote_id in ($alqids) $append group by quote_id order by quote_id";
			$alls=exec_query($quer_for_sellid);

			if(count($alls)>0)
			{
				$soldqty=0;
				$totalsellqty=0;
				//$idsall='';
				foreach($alls as $allresultsell)
				{
					$sellQuantiesArray[$allresultsell['quote_id']]['totalsell']=$allresultsell['totsell'];
					$sellQuantiesArray[$allresultsell['quote_id']]['selltransid']=$allresultsell['id'];
					$idsallUnitprice[]=$allresultsell['id'];
				}

				$uniitpriceids=implode(",",$idsallUnitprice);
				$upqry="select QT.unit_price up,QT.id,QST.sell_trans_id as sell_trans_id from techstrat_transaction QT, techstrat_sell_transaction QST where QST.sell_trans_id in($uniitpriceids) and QST.buy_trans_id=QT.id order by QST.sell_trans_id";
				$upsell=exec_query($upqry);

				if(count($upsell)>0)
				{
					$soldat=0;
					foreach($upsell as $upall)
					{
						$unitpriceArray[$upall['sell_trans_id']]=$upall['up'];
					}
				}
			}

			foreach($allr as $allresultbuy)
			{
				$qid=$allresultbuy['quote_id'];
				$totalpurchsdqty=$allresultbuy['totpurchqty']; // Final for each qid
				$totpurchamtget=$allresultbuy['totpurchamt'];
				$totalsoldamt=0;
				$sellqty=0;
				$totalsellqty=0;

				if($sellQuantiesArray[$qid]['totalsell']!='')
				{
					$soldqty=0;
					$totalsellqty=0;
					$selltransid=$sellQuantiesArray[$qid]['selltransid'];
					$soldqty=$sellQuantiesArray[$qid]['totalsell'];
					$totalsellqty=$totalsellqty+$soldqty;

					if($unitpriceArray[$selltransid]!='')
					{
						$soldat=0;
						$soldat=$soldqty*$unitpriceArray[$selltransid];
						$totalsoldamt=$totalsoldamt+$soldat;
					}
				}

				if(!(($totalpurchsdqty-$totalsellqty)==0))
				{
					$avgunitprice=($totpurchamtget-$totalsoldamt)/($totalpurchsdqty-$totalsellqty);
				}
				else
				{
					$avgunitprice=0;
				}
				$stockbuys[$qid]=$avgunitprice;
			}
		}
		$this->unitpriceofstockinhands=$stockbuys;
		return $stockbuys;
	}

	public function costbasispershareforshortsell()
	{
		$query_for_shortsellids="select quote_id,sum(quantity) as totshortsellqty,sum((unit_price)*(quantity)) as totshortsellamt  from techstrat_transaction where status='1' and transaction_type='2' group by quote_id";
		$allr=exec_query($query_for_shortsellids);

		if(count($allr)>0){

			foreach($allr as $allresultsell)
			{
				$idsalls[]=$allresultsell['quote_id'];
			}

			$allquotes=implode(",",$idsalls);

			$query_for_buytocoverid="select id,quote_id,quantity as totbuytocover from techstrat_transaction where status='1' and transaction_type='3' and quote_id in($allquotes) order by id";
			$buytocoverarray=exec_query($query_for_buytocoverid);

			if(count($buytocoverarray)>0)
			{
				foreach($buytocoverarray as $allresultshortsell)
				{
					$buytocoverresidsarray[$allresultshortsell['quote_id']][]=$allresultshortsell['id'];
					$buytocoverresqtyarray[$allresultshortsell['quote_id']][]=$allresultshortsell['totbuytocover'];
				}
			}
			$stockbuys=array();
			$avgunitprice=0;
			$totalbuyamt=0;
			$totshortsellamtget=0;
			$totalbuytocoverqty=0;
			$totshortsellqty=0;

			foreach($allr as $allresultshortsell)
			{
				$qid=$allresultshortsell['quote_id'];
				$totshortsellqty=$allresultshortsell['totshortsellqty']; // Final for each qid
				$totshortsellamtget=$allresultshortsell['totshortsellamt'];
				$totalbuyamt=0;
				$buytocoverqty=0;
				$totalbuytocoverqty=0;

				//**** if(count($alls)>0)
				if(count($buytocoverresidsarray[$qid])>0)
				{
					$buytocoverqty=0;

						for($i=0;$i<count($buytocoverresidsarray[$qid]);$i++)
						{
							$buytocoverqty=$buytocoverresqtyarray[$qid][$i];
							$totalbuytocoverqty=$totalbuytocoverqty+$buytocoverqty;
							$btid=$buytocoverresidsarray[$qid][$i];
							$upqry="select unit_price up from techstrat_transaction where id in (select sell_trans_id from techstrat_sell_transaction where buy_trans_id='$btid')";
							$upbuy=exec_query($upqry);
							if(count($upbuy)>0)
							{
								$buytocoverdat=0;
								foreach($upbuy as $upall)
								{
									$buytocoverdat=$buytocoverqty*$upall['up'];
									$totalbuyamt=$totalbuyamt+$buytocoverdat;
								}
							}
						}

						//}
				}
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
		$quer_for_buyids="select QT.id,QT.quote_id,QT.quantity as totpurchqty,((QT.unit_price)*(QT.quantity)) as totpurchamt  from techstrat_transaction QT, ex_stock ES where status='1' and QT.transaction_type='0' and QT.quote_id=ES.id order by ES.companyname";
		$allr=exec_query($quer_for_buyids);
		if(count($allr)>0){
			$stockbuys=array();

			foreach($allr as $allresultbuy){

				$qid=$allresultbuy['quote_id'];
					// chek for rotal sell amt
					$sellqty=0;

					$quer_for_sellid="select quantity as totsell from techstrat_transaction where status='1' and transaction_type='1' and quote_id='$qid' group by quote_id";
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
	function allsymbolsarr()
	{
		$quer_for_allsym="select id,stocksymbol from ex_stock";
		$allressym=exec_query($quer_for_allsym);
		if(count($allressym)>0){
			$id_stockname=array();
			foreach($allressym as $allresultsym)
			{
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
		$errmsg=explode('-',$msg);
		$errstockid=$errmsg[1];

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

		$fixedtransaction="select id,quote_id,transaction_type,creation_date,unit_price,quantity,description from techstrat_transaction  where status='1' order by creation_date DESC";
		$fixedtransaction1 = exec_query($fixedtransaction);
		$fixedresults=count($fixedtransaction1);

		$qry_transaction="select id,quote_id,transaction_type,creation_date,unit_price,quantity,description from techstrat_transaction  where status='1' order by creation_date DESC $lmt";
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
				$creation_date_get = date('m/d/Y',strtotime($creation_date_get));
				$strout.='<tr bgcolor="'.$tog.'"><td>'.$id_stockname[$quote_get].'</td><td>'.$id_comname[$quote_get].'</td>';
				$strout.='<td><select name="combo_'.$stockid_get.'" disabled>';
				$strout.='<option value="0"';
					if($transaction_type_get=="0"){ $strout.=' selected ';}
					$strout.='>Buy</option>';
				$strout.='<option value="1"';
				if($transaction_type_get=="1"){$strout.='selected';}
					$strout.='>Sell</option></select>';
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
						$strout.='<a class="viewnav" style="cursor:pointer;padding-left:10px;" onClick=javascript:makeTechPrevLinks("'.$start.'","'.$end.'")>&lt; Previous</a>';
					}
					$strout.='</td><td width="50%" style="padding-right:10px;" align="right"><a class="viewnav"  style="cursor:pointer;" onClick=javascript:makeTechNextLinks("'.$start.'","'.$end.'")>Next ></a></td></tr></table></td></tr>';
					$totrecds=$cntlnk;
					break;
				}
				else if(($chkres>=$fixedresults)){
					$strout.='<tr><td colspan="8" style="padding-top:5px;"><table border="0" cellspacing=0 cellpadding=0 width="100%">
						<td width="50%" style="padding-left:10px;" align="left">';
					if($start!=0){
						$strout.='<a class="viewnav"  style="cursor:pointer;padding-left:10px;" onClick=javascript:makeTechPrevLinks("'.$start.'","'.$end.'")>&lt; Previous</a>';
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
							<br><img src="'.$IMG_SERVER.'/images/quint_images/btn-savechanges.gif" align="left" onclick="javascript:updateTechStratField();"/>
						</td></tr></table>';
					}
					$strout.='<input type="hidden" name="totrecords" value='.$totrecds.'><input type="hidden" name="stockid_get_all" value='.$stockid_get_all.'><input type="hidden" name="sratindx" value='.$sratindx.'><input type="hidden" name="endindx" value='.$endindx.'></form><br><font color=red>'.$errmsg[0].'</font></div>';
				}else{
					$strout='<div id="trans_detail"><b><font color="#081E33"><br/>Make Your First Transaction!!</font></b></div>';
				}
				return $strout;
				}

	function alltransactionedit($start=null,$end=null)
	{
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

		$qryFixedTransaction="select id,quote_id,transaction_type,creation_date,unit_price,quantity,description from techstrat_transaction  where status='1' order by creation_date DESC";
		$resFixedtransaction = exec_query($qryFixedTransaction);
		$fixedresults=count($resFixedtransaction);

		$qryTransaction="select id,quote_id,transaction_type,creation_date,unit_price,quantity,description from techstrat_transaction  where status='1' order by creation_date DESC $lmt";
		$resTransaction = exec_query($qryTransaction);
		$cntLT=count($resTransaction);

		if(!empty($resTransaction)){
			$strout='';
			$strout='<div id="trans_detail" class="techtransDetail"><form name="edittransactionfrm" id="edittransactionfrm" method="POST" action="">
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
			foreach($resTransaction as $result){
				$cnt++;
				if($cnt%2==0){
					$tog="#FFFFFF";
				}else{
					$tog="#EFEFEF";
				}

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

				$creation_date_get = date('m/d/Y',strtotime($creation_date_get));

				$strout.='<tr bgcolor="'.$tog.'"><td>'.$id_stockname[$quote_get].'<input type="hidden" name="qtid_'.$stockid_get.'" id="qtid_'.$stockid_get.'" value="'.$quote_get.'" ></td><td>'.$id_comname[$quote_get].'</td>';
				$strout.='<td><input type="hidden" name="hcombo_'.$stockid_get.'" id="hcombo_'.$stockid_get.'" value="'.$transaction_type_get.'"><select name="combo_'.$stockid_get.'" disabled>';
				$strout.='<option value="0"';
					if($transaction_type_get=="0"){ $strout.=' selected ';}
					$strout.='>Buy</option>';
				$strout.='<option value="1"';
					if($transaction_type_get=="1"){$strout.='selected';}
					$strout.='>Sell</option>';
				$strout.='<option value="2"';
					if($transaction_type_get=="2"){$strout.='selected';}
					$strout.='>Short Sell</option>';
				$strout.='<option value="3"';
					if($transaction_type_get=="3"){$strout.='selected';}
					$strout.='>Buy to cover</option></select>';
				$strout.='</td><td><input type="text" size="10" name="creatdate_'.$stockid_get.'" id="creatdate_'.$stockid_get.'" value="'.$creation_date_get.'" readonly class="portfolioDate">';
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
						$strout.='<a class="viewnav" style="cursor:pointer;padding-left:10px;" onClick=javascript:makeTechPrevLinks("'.$start.'","'.$end.'")>&lt; Previous</a>';
					}
					$strout.='</td><td width="50%" style="padding-right:10px;" align="right"><a class="viewnav"  style="cursor:pointer;" onClick=javascript:makeTechNextLinks("'.$start.'","'.$end.'")>Next ></a></td></tr></table></td></tr>';
					$totrecds=$cntlnk;
					break;
				}
				else if(($chkres>=$fixedresults)){
						$strout.='<tr><td colspan="8" style="padding-top:5px;"><table border="0" cellspacing=0 cellpadding=0 width="100%">
						<td width="50%" style="padding-left:10px;" align="left">';
						if($start!=0){
							$strout.='<a class="viewnav"  style="cursor:pointer;padding-left:10px;" onClick=javascript:makeTechPrevLinks("'.$start.'","'.$end.'")>&lt; Previous</a>';
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
							<br><img src="'.$IMG_SERVER.'/images/quint_images/btn-savechanges.gif" align="left" style="cursor:pointer;" onclick="javascript:updateTechStratField();"/>
							</td></tr></table>';
					}
					$strout.='<input type="hidden" name="totrecords" value='.$totrecds.'><input type="hidden" name="stockid_get_all" id="stockid_get_all" value='.$stockid_get_all.'><input type="hidden" name="sratindx" value='.$sratindx.'><input type="hidden" name="endindx" value='.$endindx.'></form></div>';

				}else{
				$strout='<div id="trans_detail" class="techtransDetail"><b><font color="#081E33"><br/>Make Your First Transaction!!</font></b></div>';
			}
		return $strout;
		}

	function alltransaction($msg=NULL,$start=NULL,$end=NULL,$fldname=NULL,$ord=NULL,$items=NULL){
		global $transactioncnt,$IMG_SERVER;

		if($fldname==''){
			$fldname='creation_date';
		}else{
			$fldname=$fldname;
		}


		if($ord==''){
			$ord='ASC';
			$asndesn=$IMG_SERVER.'/images/quint_images/down.jpg';
			$nextord='ASC';
		}else{
			$ord=$ord;
			if($ord=='ASC'){$nextord='DESC';$asndesn=$IMG_SERVER.'/images/quint_images/up.jpg';}else{$nextord='ASC';$asndesn=$IMG_SERVER.'/images/quint_images/down.jpg';}
		}

		if($items==''){
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
		}else{
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

		$fixedtransaction="select id,quote_id,transaction_type,creation_date,unit_price,quantity,description from techstrat_transaction  where status='1' order by creation_date DESC";
		$fixedtransaction1 = exec_query($fixedtransaction);
		$fixedresults=count($fixedtransaction1);

		if($fldname!='' && $ord!='' && $items!=''){
			$qry_transaction="select QT.id,QT.quote_id,et.stocksymbol,et.CompanyName,QT.transaction_type,QT.creation_date,QT.unit_price,QT.quantity,QT.description from techstrat_transaction QT,ex_stock et where QT.status='1' and et.id=QT.quote_id and QT.id in($items) order by $fldname $ord";
		}else{
			$qry_transaction="select QT.id,QT.quote_id,et.stocksymbol,et.CompanyName,QT.transaction_type,QT.creation_date,QT.unit_price,QT.quantity,QT.description from techstrat_transaction QT,ex_stock et where QT.status='1' and et.id=QT.quote_id order by creation_date DESC $lmt";
		}
		//echo "<br>".$qry_transaction; exit;
		$execqry = exec_query($qry_transaction);
		if(count($execqry)>0){
			$strout='';
			$strout='<div id="trans_detail" class="techtransDetail">
				<form name="transactionform" method="POST"><table width="100%" style="border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" cellspacing="0" cellpadding="5" border=0 bordercolor="black" class="quintportfolio">
				<tr><td class="quintportfolio"><a href=javascript:techstratSortBy("'.$start.'","'.$end.'","stocksymbol","'.$nextord.'") class="headeer">Symbol';
				if($fldname=='stocksymbol'){
					$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
				}
				$strout.='</a></td>
				<td class="quintportfolio"><a href=javascript:techstratSortBy("'.$start.'","'.$end.'","CompanyName","'.$nextord.'") class="headeer">Name';
				if($fldname=='CompanyName'){
					$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
				}
				$strout.='</a></td>
				<td class="quintportfolio"><a href=javascript:techstratSortBy("'.$start.'","'.$end.'","transaction_type","'.$nextord.'") class="headeer">Type';
				if($fldname=='transaction_type'){
					$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
				}
				$strout.='</a></td>
				<td class="quintportfolio"><a href=javascript:techstratSortBy("'.$start.'","'.$end.'","creation_date","'.$nextord.'") class="headeer">Date';
				if($fldname=='creation_date'){
					$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
				}
				$strout.='</a></td>
				<td class="quintportfolio"><a href=javascript:techstratSortBy("'.$start.'","'.$end.'","quantity","'.$nextord.'") class="headeer">Shares';
				if($fldname=='quantity'){
					$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
				}
				$strout.='</a></td>
				<td class="quintportfolio"><a href=javascript:techstratSortBy("'.$start.'","'.$end.'","unit_price","'.$nextord.'") class="headeer">Price';
				if($fldname=='unit_price'){
					$strout.='<img src="'.$asndesn.'" style="border:0px" align="absmiddle">';
				}
				$strout.='</a></td>
				<td class="quintportfolio">Notes</th></tr>';
				$cntlnk=0;
				foreach(exec_query($qry_transaction) as $result){
					$stocksymbol=$result['stocksymbol'];
					$companyname=$result['CompanyName'];
					$autoidget=$result['id'];
					$stockid_get=$result['quote_id'];
					$transaction_type_get=$result['transaction_type'];
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
					if($cnt1%2==0){
						$tog1="#FFFFFF";
					}else{
						$tog1="#EFEFEF";
					}
					$creation_date_get=date('m/d/Y',strtotime($creation_date_get));
					$strout.='<tr bgcolor='.$tog1.'><td>'.$stocksymbol.'</td><td>'.$companyname.'</td><td>'.$transaction_type_get.'</td><td>'.$creation_date_get.'</td><td>'.$quantity_get.'</td><td>'.$unit_price_get.'</td><td>';

					if($transaction_type_get=='Short Sell'){
						$autoidgetenc=	strrev(base64_encode($autoidget));
						$btnurl=$IMG_SERVER.'/images/quint_images/by-to-cover.jpg';
						$cnt++;
						$btid="btc_".$cnt;
						$strout.='<table width="100%" border=0><tr><td>'.$notes_get.'</td><td width="10%">';
						$strout.='<img src="'.$btnurl.'" id="'.$btid.'" onClick="buytransaction(\''.$autoidgetenc.'\')" alt="BTC" title="click here for Buy to cover" style="display:none;cursor:pointer"/></td></tr></table>';
					}else{
						$strout.=$notes_get;
					}
					$strout.='</td></tr>';

		// Navigation starts here
					$cntlnk++;
					$chkres=$cntlnk+$start;
					if(($chkres<$fixedresults) && ($cntlnk==$transactioncnt)){
						$strout.='<tr><td colspan="7" style="padding-top:2px;"><table border="0" cellspacing=0 cellpadding=0 width="100%"><td width="50%" style="padding-left:10px;" align="left">';
						if($start!=0){
							$strout.='<a class="viewnav" style="cursor:pointer;padding-left:10px;" onClick=javascript:makeTechPrevLinks1("'.$start.'","'.$end.'")>&lt; Previous</a>';
						}
						$strout.='</td><td width="50%" style="padding-right:10px;" align="right"><a class="viewnav"  style="cursor:pointer;" onClick=javascript:makeTechNextLinks1("'.$start.'","'.$end.'")>Next ></a></td></tr></table></td></tr>';
						$totrecds=$cntlnk;
						break;
					}else if(($chkres>=$fixedresults)){
						$strout.='<tr><td colspan="7" style="padding-top:5px;"><table border="0" cellspacing=0 cellpadding=0 width="100%">
						<td width="50%" style="padding-left:10px;" align="left">';
						if($start!=0){
							$strout.='<a class="viewnav"  style="cursor:pointer;padding-left:10px;" onClick=javascript:makeTechPrevLinks1("'.$start.'","'.$end.'")>&lt; Previous</a>';
						}
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
				$strout='<div id="trans_detail" class="techtransDetail"><b><font color="#081E33"><br/>Make Your First Transaction!!</font></b></div>';
			}
		return $strout;
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
		$quer_for_buyids="select sum(quantity) as totpurchqty,sum((unit_price)*(quantity)) as totpurchamt,((sum((unit_price)*(quantity)))/(sum(quantity))) as avgunitprice from techstrat_transaction where status='1' and transaction_type='0' and quote_id='$sid' group by quote_id";
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
		$qry="select QST.buy_trans_id as bti,sum(QT.quantity) as totsell from techstrat_transaction QT, techstrat_sell_transaction QST where QST.sell_trans_id=QT.id and QT.status='1' $append group by buy_trans_id";
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

	function techSellTrans() {
		$qry="select QS.sell_trans_id id, QT.creation_date, QT.unit_price from techstrat_sell_transaction QS, techstrat_transaction QT where QS.buy_trans_id=QT.id";
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
	    return $selltransarr;
	 }


	function techSellTransForSellShort() {
		$qry="select QS.buy_trans_id id, QT.creation_date, QT.unit_price from techstrat_sell_transaction QS, techstrat_transaction QT where QS.buy_trans_id=QT.id";
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
	    return $selltransarr;
	}

	function techPurPriceTrans() {
		$qry="select QS.sell_trans_id id, QT.creation_date, QT.unit_price from techstrat_sell_transaction QS, techstrat_transaction QT where QS.buy_trans_id=QT.id";
		$selldata=exec_query($qry);
		$selltransarr=array();
		$purctransarr=array();

		if(count($selldata)>0){
			foreach($selldata as $allresult){
				$sellid=$allresult['id'];
				$purtransarr[$sellid]=$allresult['unit_price'];;
			}
		}
	    return $purtransarr;
	}

	function techPurPriceTransForBuyToCover() {
		// Make the array for all shorts sell unit prices
		 $ary_up_ss="select id,unit_price from techstrat_transaction where transaction_type='2' and status='1'";
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
			 $qry_up_btc="select buy_trans_id,sell_trans_id from techstrat_sell_transaction where buy_trans_id in (select id from techstrat_transaction where transaction_type='3' and status='1')";
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
	function techDateSoldShortSell($id) {
		$qry="select a.sell_trans_id,a.creation_date from techstrat_sell_transaction a where a.buy_trans_id='$id'";
	    $datesold=exec_query($qry,1);
		if(isset($datesold)){
		     return $datesold;
		}
	}

	function techCreationDateShortSell($id) {
		$qry="select id, creation_date from techstrat_transaction where transaction_type=2 and status=1 and id in(select a.sell_trans_id from techstrat_sell_transaction a where a.buy_trans_id='$id')";
	    $creationdatesold=exec_query($qry,1);

		if(isset($creationdatesold)){
		     return $creationdatesold;
		}
	}

	function techShortSellSalePrice() {
		$qry="select id,unit_price from techstrat_transaction where transaction_type='3' and status='1'";
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
		$qry="select QS.sell_trans_id id,QT.creation_date,QT.unit_price from techstrat_sell_transaction QS,techstrat_transaction QT where QS.buy_trans_id=QT.id";
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

	function buytocover($transid){
		 $fname='btc';
		 global $lang;
		 $id_stockname1=$this->allsymbolsarr();
		 $id_comname1=$this->allsymbolscomapnyarr();
				 $qry_transaction="select id,quote_id,transaction_type,creation_date,unit_price,quantity,description from techstrat_transaction  where status='1' and id='$transid' union ALL  select QT.id,QT.quote_id,QT.transaction_type,QT.creation_date,QT.unit_price,QT.quantity,QT.description  from techstrat_transaction QT,techstrat_sell_transaction QST where QST.sell_trans_id='$transid' and QST.status='1' and QST.buy_trans_id!=0 and QST.buy_trans_id=QT.id";
				 $execqry = exec_query($qry_transaction);
				 if(count($execqry)>0){
					 $strout='';
					 $strout='<div id="trans_detail" class="techtransDetail">
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
					 $creation_date_get=date('m/d/Y',strtotime($creation_date_get));
					 $strout.='<tr bgcolor='.$tog1.'><td>'.$id_stockname1[$stockid_get].'</td><td>'.$id_comname1[$stockid_get].'</td><td>';
					 if($transaction_type_get=='Short Sell'){
					 $strout.='<input type="hidden" id="orgsortselldate" name="orgsortselldate" value="'.$creation_date_get.'">';
					 }
					 $strout.=$transaction_type_get.'</td><td>'.$creation_date_get.'</td><td>'.$quantity_get.'</td><td>'.$unit_price_get.'</td><td>';
					 $strout.=$notes_get;
					 $strout.='</td></tr>';
					 }
				 $strout.='</table>';
				 if($totalstockstobtc!=0){
						 $strout.='<p class="quintportfolio">Buy To Cover (&nbsp;'.$lang["max_shares_for_btc"].' '.$totalstockstobtc.' &nbsp;)</p><form name="'.$fname.'" method="post"><table width="100%" style="border-left:1px solid #cccccc; padding:0px;margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; padding:0px;margin:0px;" cellspacing="0" cellpadding="5" border=0 bordercolor="black" class="quintportfolio">
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
						 $strout.='<input type="text" size="10" name="selldate" class="portfolioBuyToCoverDate" id="selldate" value="" readonly>&nbsp;';
						 $str="selldate";

						 $strout.='</td>
						 <td class="quintportfolio" width="10%"><input size="8" type="text" name="shareqty" id="shareqty"  value="" onKeyup=javascript:chknosvalidation("'.$fname.'",this)></td>
						 <td class="quintportfolio" width="10%"><input size="8" type="text" name="price" id="price"  value="" onKeyup=javascript:chknosvalidation("'.$fname.'",this)></td>
						 <td class="quintportfolio"><input type="text" size="40" name="notes" value="">
						 <input type="button" value="SAVE" onClick="javascript:chknsubmit()">&nbsp;<input type="reset" value="RESET"></td>
						 </tr>
						 </table></form>';
					 }
				 $strout.='</div>';
			 }else{
				 $strout='<div id="trans_detail" class="techtransDetail"><b><font color="#081E33"><br/>Make Your First Short Sell .... !!</font></b></div>';
			 }
		return $strout;
	}

	public function updatetransaction($type,$description=null,$optionunitprice=null,$recordid,$transtype,$quoteid)
	 {
		 if($type=='delete')
		 {
			 $updated=0;
			 switch ($transtype)
			 {
				 case 0:
				 				$transupdate=$this->deletestockbuytbl($updatedshares,$optionunitprice,$description,'0',$recordid);
					 $sellupdated=$this->deleteselltabl($recordid,$transtype);
					 $updated=$this->updatelottabl($quoteid);
					 echo $updated;
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

	public function deleteselltabl($autoids,$transtype)
	{
		 $updatedon=date('Y-m-d H:i:s');
		 $selltransactiondata_up=array(status=>'0','updation_date'=>$updatedon);

		 if($transtype==1)
		 { //while its sell
			 $retstat=0;
			 $retstat=update_query("techstrat_sell_transaction",$selltransactiondata_up,array('sell_trans_id'=>$autoids));
			 $memCacheDelete = new memCacheObj();
			 $key="techStratOpenPositiontrade";
			 $memCacheDelete->deleteKey($key);
			 $key="techStratOpenPosition";
			 $memCacheDelete->deleteKey($key);
			 $key="techStratDisplayPerformance";
			 $memCacheDelete->deleteKey($key);
			 $this->updateOpenPositionRecords();
			 return $retstat;
		 }else if($transtype==3){
			 $retstat=0;
			 		$retstat=update_query("techstrat_sell_transaction",$selltransactiondata_up,array('buy_trans_id'=>$autoids));
			//get the sell_trans_id from sell table against this BTC
			 $sqlbtc="select sell_trans_id from techstrat_sell_transaction where buy_trans_id='$autoids'";
			 $searchfound=exec_query($sqlbtc,1);
			 $stid=$searchfound['sell_trans_id'];
			 if($stid!=0 && isset($stid))
			 {
				 $req_update=array();
				 $req_update['updation_date']=$updatedon;
				 $req_update['trans_status']='pending';
				 $ret_up=update_query('techstrat_sell_transaction',$req_update,array('sell_trans_id'=>$stid,'buy_trans_id'=>'0'));
			 }
			 $memCacheDelete = new memCacheObj();
			 $key="techStratOpenPositiontrade";
			 $memCacheDelete->deleteKey($key);
			 $key="techStratOpenPosition";
			 $memCacheDelete->deleteKey($key);
			 $key="techStratDisplayPerformance";
			 $memCacheDelete->deleteKey($key);
			 $this->updateOpenPositionRecords();
			 return $retstat;
		 }
		 else if($transtype==2)
		 {
			 $retstat=0;
			 $retstat=update_query("techstrat_sell_transaction",$selltransactiondata_up,array('sell_trans_id'=>$autoids));
			 $sql_selectdisbledids="select buy_trans_id from techstrat_sell_transaction where sell_trans_id='$autoids' and status=0 and buy_trans_id!='0'";
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
					 $retstat=update_query("techstrat_transaction",$req_update,array('id'=>$idtoupdate));
				 }
			 }
			 $memCacheDelete = new memCacheObj();
			 $key="techStratOpenPositiontrade";
			 $memCacheDelete->deleteKey($key);
			 $key="techStratOpenPosition";
			 $memCacheDelete->deleteKey($key);
			 $key="techStratDisplayPerformance";
			 $memCacheDelete->deleteKey($key);
			 $this->updateOpenPositionRecords();
			 return $retstat;
		 }else
		 {
			 $retstat=0;
			 $retstat=update_query("techstrat_sell_transaction",$selltransactiondata_up,array('buy_trans_id'=>$autoids));
			 $sql_selectdisbledids="select sell_trans_id from techstrat_sell_transaction where buy_trans_id='$autoids' and status=0";
			 $allressym=exec_query($sql_selectdisbledids);
			 $totalrecds=0;
			 if(count($allressym)>0){
				 foreach($allressym as $alltransres){
					 $idtoupdate=$alltransres['sell_trans_id'];
					 unset($req_update);
					 $req_update=array();
					 $req_update['updation_date']=$updatedon;
					 $req_update['status']=0;
					 $retstat=update_query("techstrat_transaction",$req_update,array(id=>$idtoupdate));
				 }
			 }
			 $memCacheDelete = new memCacheObj();
			 $key="techStratOpenPositiontrade";
			 $memCacheDelete->deleteKey($key);
			 $key="techStratOpenPosition";
			 $memCacheDelete->deleteKey($key);
			 $key="techStratDisplayPerformance";
			 $memCacheDelete->deleteKey($key);
			 $this->updateOpenPositionRecords();
			 return $retstat; // 0 return means there was no entry against this buy transaction
		 }
	}

	public function deletestockbuytbl($updatedshares,$unitprice,$description,$status,$idtupdate)
	{
		 $tablename='techstrat_transaction';
		 $req_update=array();
		 $req_update['updation_date']=date('Y-m-d H:i:s');
		 $req_update['status']=$status;
		 $ret_up=update_query($tablename,$req_update,array('id'=>$idtupdate));
		 $memCacheDelete = new memCacheObj();
		 $key="techStratOpenPositiontrade";
		 $memCacheDelete->deleteKey($key);
		 $key="techStratOpenPosition";
		 $memCacheDelete->deleteKey($key);
		 $key="techStratDisplayPerformance";
		 $memCacheDelete->deleteKey($key);
		 $this->updateOpenPositionRecords();
		 return $ret_up;
	}

	public function updatetranstabl($creatdateconv,$updatedon,$updatedshares,$unitprice,$description,$status,$idtupdate){
		$tablename='techstrat_transaction';
		$req_update=array();
		$req_update['creation_date']=$creatdateconv;
		$req_update['updation_date']=$updatedon;
		$req_update['quantity']=$updatedshares;
		$req_update['unit_price']=$unitprice;
		$req_update['description']=$description;
		$req_update['status']=$status;
		$ret_up=update_query($tablename,$req_update,array(id=>$idtupdate));
		$memCacheDelete = new memCacheObj();
		$key="techStratOpenPositiontrade";
		$memCacheDelete->deleteKey($key);
		$key="techStratOpenPosition";
		$memCacheDelete->deleteKey($key);
		$key="techStratDisplayPerformance";
		$memCacheDelete->deleteKey($key);
		$this->updateOpenPositionRecords();
		return $ret_up;
	}

	public function updatelottabl($upqtid){
		 $totinhandqty=$this->getparticularstocksinh($upqtid);
		 $avg_unit_price=0;
		 $avg_unit_price=$this->avgunitpriceofstockinhand($upqtid);
# Update tab-2
 		$transactiondata_up = array(
 			'quantity'=>$totinhandqty,
 			'avg_unit_price'=>$avg_unit_price,
 			'recent_trade_date'=>$datetime); // as we are not keeping any track for last updated so its default for datetime
		 $retstat=0;
		 $retstat=update_query("techstrat_user_portfolio",$transactiondata_up,array(quote_id=>$upqtid));
		 $memCacheDelete = new memCacheObj();
		 $key="techStratOpenPositiontrade";
		 $memCacheDelete->deleteKey($key);
		 $key="techStratOpenPosition";
		 $memCacheDelete->deleteKey($key);
		 $key="techStratDisplayPerformance";
		 $memCacheDelete->deleteKey($key);
		 $this->updateOpenPositionRecords();
		 return $retstat;
	}

	function updateselltabl($autoids,$transtype){
		$updatedon=date('Y-m-d H:i:s');
		$selltransactiondata_up=array(status=>0,updation_date=>$updatedon);

		if($transtype==1){ // while its sell
			$retstat=0;
			$retstat=update_query("techstrat_sell_transaction",$selltransactiondata_up,array(sell_trans_id=>$autoids));
			$memCacheDelete = new memCacheObj();
			$key="techStratOpenPositiontrade";
			$memCacheDelete->deleteKey($key);
			$key="techStratOpenPosition";
			$memCacheDelete->deleteKey($key);
			$key="techStratDisplayPerformance";
			$memCacheDelete->deleteKey($key);
			$this->updateOpenPositionRecords();
			return $retstat;
		}else if($transtype==3){
			$retstat=0;
			$retstat=update_query("techstrat_sell_transaction",$selltransactiondata_up,array(buy_trans_id=>$autoids));
			$memCacheDelete = new memCacheObj();
			$key="techStratOpenPositiontrade";
			$memCacheDelete->deleteKey($key);
			$key="techStratOpenPosition";
			$memCacheDelete->deleteKey($key);
			$key="techStratDisplayPerformance";
			$memCacheDelete->deleteKey($key);
			$this->updateOpenPositionRecords();
			return $retstat;
		}else if($transtype==2){
			$retstat=0;
			$retstat=update_query("techstrat_sell_transaction",$selltransactiondata_up,array(sell_trans_id=>$autoids));
			$sql_selectdisbledids="select buy_trans_id from techstrat_sell_transaction where sell_trans_id='$autoids' and status=0 and buy_trans_id!='0'";
			$allressym=exec_query($sql_selectdisbledids);
			$totalrecds=0;
			if(count($allressym)>0){
				foreach($allressym as $alltransres){
					$idtoupdate=$alltransres['buy_trans_id'];
					unset($req_update);
					$req_update=array();
					$req_update['updation_date']=$updatedon;
					$req_update['status']=0;
					$retstat=update_query("techstrat_transaction",$req_update,array(id=>$idtoupdate));
				}
			}
		 $memCacheDelete = new memCacheObj();
		 $key="techStratOpenPositiontrade";
		 $memCacheDelete->deleteKey($key);
		 $key="techStratOpenPosition";
		 $memCacheDelete->deleteKey($key);
		 $key="techStratDisplayPerformance";
		 $memCacheDelete->deleteKey($key);
		 $this->updateOpenPositionRecords();
		 return $retstat;
		}else
		{
		 $retstat=0;
		 $retstat=update_query("techstrat_sell_transaction",$selltransactiondata_up,array(buy_trans_id=>$autoids));
		 $sql_selectdisbledids="select sell_trans_id from techstrat_sell_transaction where buy_trans_id='$autoids' and status=0";
		 $allressym=exec_query($sql_selectdisbledids);
		 $totalrecds=0;
		 if(count($allressym)>0){
			 foreach($allressym as $alltransres){
				 $idtoupdate=$alltransres['sell_trans_id'];
				 unset($req_update);
				 $req_update=array();
				 $req_update['updation_date']=$updatedon;
				 $req_update['status']=0;
				 $retstat=update_query("techstrat_transaction",$req_update,array(id=>$idtoupdate));
			 }
		 }
		 $memCacheDelete = new memCacheObj();
		 $key="techStratOpenPositiontrade";
		 $memCacheDelete->deleteKey($key);
		 $key="techStratOpenPosition";
		 $memCacheDelete->deleteKey($key);
		 $key="techStratDisplayPerformance";
		 $memCacheDelete->deleteKey($key);
		 $this->updateOpenPositionRecords();
		 return $retstat;
		}
	}

	# This function calculates the total loss/gain of the short sell / btc
	public function getgainlossfrmssbtc()
	{
		 global $ytObj;
		 $thisYearsFirstTransDate = $ytObj->firsttransactiondate;
		 $totgainloss=0;
		 $totgainlossd=0;
		 $isbtcdone=0;
		 $sqlallsellids="select qt.creation_date,qt.id,qt.quote_id,ES.stocksymbol,qt.unit_price,qt.quantity from techstrat_transaction qt,ex_stock ES where qt.transaction_type='2' and qt.status='1' and qt.quote_id=ES.id order by id";
		 //echo "<br>".$sqlallsellids.'<br>';
		 $allressell=exec_query($sqlallsellids);

		 if(count($allressell)>0)
		 {
			 //start
			 $j=0;
			 foreach($allressell as $key=>$value)
			 {
				 $arr[$j]=$value['id'];

				 if($value['creation_date']>=$thisYearsFirstTransDate)
				 {
						 $YTDallressell[]=array('creation_date'=>$value['creation_date'],'id'=>$value['id'],'quote_id'=>$value['quote_id'],'stocksymbol'=>$value['stocksymbol'],'unit_price'=>$value['unit_price'],'quantity'=>$value['quantity']);
				 }

				$j++;
			 }

			 $this->ytdallgainLossfromss=$YTDallressell; // used in ytd return

			 $allids=implode(",",$arr);
			 unset($arr);
			 $sqlchkbtc="select qt.creation_date,qst.sell_trans_id,qt.id,qt.unit_price,qt.quantity from techstrat_transaction qt,techstrat_sell_transaction qst where qst.status='1' and qst.sell_trans_id in($allids) and qst.trans_status!='pending' and qt.id=qst.buy_trans_id order by id";
			 //echo "<br>".$sqlchkbtc;
			 $allresbtc=exec_query($sqlchkbtc);
			 $combineresult=array();

			 foreach($allresbtc as $key=>$value)
			 {
				 $selltransArray[$z]=$value['sell_trans_id'];
				 $resultBTCArray[$value['sell_trans_id']][] = array('unit_price'=>$value['unit_price'],'quantity'=>$value['quantity']);

				 if($value['creation_date']>=$thisYearsFirstTransDate)
				 {
					 $YTDselltransArray[$z]=$value['sell_trans_id'];
					 $YTDresultBTCArray[$value['sell_trans_id']][] = array('unit_price'=>$value['unit_price'],'quantity'=>$value['quantity']);
				 }
				 $z++;
			 }

			$this->ytdallids=$YTDselltransArray; // used in ytd return
			$this->ytdallgainLossfrombtc=$YTDresultBTCArray; // used in ytd return


			 foreach($allressell as $allselltransres)
			 {
				 $transellid=$allselltransres['id']; // auto id of transaction table
				 $transellidup=$allselltransres['unit_price']; // unit price of each ss
				 $transellidqty=$allselltransres['quantity']; // total ss qty
				 $transellquote_id=$allselltransres['quote_id'];// symbol id
				 $transellsymbol=$allselltransres['stocksymbol']; // symbol name required for yahoo fetch

				# check the btc quantity against this ss
				 if(count($allresbtc)>0 && in_array($transellid,$selltransArray))
				 {
						 $isbtcdone=1;
						 $totqtybtcoved=0;
						 $totalbtcdup=0;

						 foreach($resultBTCArray[$transellid] as $allbtctransres=>$vals)
						 {
							 $qtybtcoved=$vals['quantity'];
							 $btcovedup=$vals['unit_price'];
							 $btcdup=$qtybtcoved*$btcovedup;
							 $totalbtcdup=$totalbtcdup+$btcdup;
							 $totqtybtcoved=$totqtybtcoved+$qtybtcoved;
						 }

						 $soldsharesorginalval=0;
						 $soldsharesorginalval=$transellidup*$totqtybtcoved;
						 $buysharesorginalval=$totalbtcdup;

						 $netprofitonbtcshares=0;
						 $restsharesqty=$transellidqty-$totqtybtcoved;

						 $netprofitonbtcshares=($soldsharesorginalval-$totalbtcdup);
						 $restsharesselltot=0;
						 $restsharesselltot=$restsharesqty*$transellidup;
						 $restsharescurntval=0;
						 $restsharescurntval=$restsharesqty*($this->getcurrentquote($transellsymbol));
						 $netlossonrestshares=0;
						 $netlossonrestshares=$restsharesselltot-$restsharescurntval;
						 $totgainlossd=$totgainlossd+($netprofitonbtcshares+$netlossonrestshares);
				 }// end
				 else
				 {
					 $isbtcdone=0; // no btc has done against this ss
					 $currentquote=0;
					 $currentquote= $this->getcurrentquote($transellsymbol);
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

	 function getexchangevaldetails($exchangename,$inceptiondate){
		 if(isset($inceptiondate)){
			 if($exchangename=='S&P'){
				 $exchangename='5EGSPC';
			 }else if($exchangename=='nasdaq'){
				 $exchangename='5EIXIC';
			 }
			 $afterexp=explode('-',$inceptiondate);
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

			 $tickersymbol=$exchangename;
			 if (isset($tickersymbol))
			 {
			 if(isset($filename)){unset($filename);}
			$filename="http://ichart.finance.yahoo.com/table.csv?s=%$exchangename&a=$im&b=$id&c=$iy&d=$im1&e=$id1&f=$iy1&g=d&ignore=.csv";
				if(isset($fileforlatest)){unset($fileforlatest);}
					$fileforlatest="http://download.finance.yahoo.com/d/quotes.csv?s=%$tickersymbol&f=sl1d1t1c1ohgvn&e=.csv";
				$openlatest = file($fileforlatest);
				if(isset($currentsymvalarrlatest)){
					unset($currentsymvalarrlatest);
				}
				$currentsymvalarrlatest=array();
				$currentsymvalarrlatest=explode(',',($openlatest[0]));
				$currentopenpostionval=0;
				$currentopenpostionval=$currentsymvalarrlatest[1];
				$open = file($filename);
				if(isset($open)){
					 if(isset($currentsymvalarr)){
					 	unset($currentsymvalarr);
					 }
					 $currentsymvalarr=array();
					 $totfetched=count($open);

					 $firsttotfetched=0;
					 $lasttotfetched=$totfetched-1;

					 $totalcurrentvalue=0;
					 $totaloriginalcost=0;
					 $inceptiondateresarr=explode(',',($open[$lasttotfetched]));
					 $growthinpercnt=0;
					 $inceptionopenpostionval=0;
					 $inceptionopenpostionval=$inceptiondateresarr[6];
					 $growthinpercnt=(($currentopenpostionval-$inceptionopenpostionval)*100)*(1/$inceptionopenpostionval);
					 return number_format($growthinpercnt, 2, '.', ',');
				 }
			 }else{
			 	return '0.00';
			 }
		 }else{
		 	return '0.00';
		 }
	 }

# This function returns the totalcurrent value of balance stocks ( those in hand & transaction_type=0 )
	 public function totalcurrentvalueofallstocksbuy()
	 {
		 if($this->allBuyFlag==1){
			 $stockinhandarr=$this->getallbuystocksarray;
		 }else{
			$stockinhandarr=$this->getallbuystocks();
		 }
		 $totalcurrentvalue=0;
		 $qryforallbuytran="select QT.quote_id,QT.transaction_type,ES.stocksymbol,QT.unit_price,QT.quantity from techstrat_transaction QT, ex_stock ES where  QT.quote_id=ES.id and (QT.transaction_type='0') and QT.status='1' group by QT.quote_id,QT.transaction_type order by QT.transaction_type,ES.companyname,QT.creation_date";
		 $tottransarr=exec_query($qryforallbuytran);
		 if(count($tottransarr)>0){
		 	foreach($tottransarr as $res){
				 $symbolname=$res['stocksymbol'];
				 $sid=$res['quote_id'];
				 $currentquote= $this->getcurrentquote($symbolname);
				 $currentval=$currentquote * $stockinhandarr[$sid];
				 $totalcurrentvalue=$totalcurrentvalue+$currentval;
			}
		 	return $totalcurrentvalue;
		 }else{
			 return 0;
		 }
	 }

	public function getcashinhand()
	{
		 $initialcashamt=0;
		 $totbuycash=0;
		 $totsellcash=0;

		 $initialcashqry="select sum(cash_amt) as cash_amt from techstrat_cash where (status='1' or status='2')";
		 $allresintcash=exec_query($initialcashqry);

		 if(count($allresintcash)>0)
		 {
			 foreach($allresintcash as $k=>$val)
			 {
				 $initialcashamt = $val['cash_amt'];
			 }
		 }

		 //**	120208 hided as we are only displaying by/sell
		 $sql_totbuyamtofalltrans="select sum(quantity*unit_price) as totbuyamt from techstrat_transaction where status='1' and (transaction_type='0')";
		 $allresbuy=exec_query($sql_totbuyamtofalltrans);
		 if(count($allresbuy)>0)
		 {
			 foreach($allresbuy as $key=>$value)
			 {
				 $totbuycash = $value['totbuyamt'];
			 }
		 }

		 $sql_totsellamtofalltrans="select sum(quantity*unit_price) as totsell from techstrat_transaction where status='1' and (transaction_type='1')";
		 $allressell=exec_query($sql_totsellamtofalltrans);
		 if(count($allressell)>0)
		 {
			 foreach($allressell as $keys=>$values)
			 {
				 $totsellcash = $values['totsell'];
			 }
		 }
		 $totcashinhand=($initialcashamt-$totbuycash)+$totsellcash;
		 return $totcashinhand;
	}

	function getPortfolioCashinHand()
	{
	 	if($this->cashInHand){
			return $this->cashInHand;
		}else{
	 		$totalbuy-$totalsell+shortsell-buytocover;
		 	$stAllTransactionQuery = "SELECT SUM(unit_price*quantity) as price,transaction_type FROM techstrat_transaction WHERE STATUS='1' GROUP BY transaction_type";
		 	$resAllTransaction=exec_query($stAllTransactionQuery);
		 	$arOriginalCost = $this->getOrginalCost();
		 	$cashInHand = $arOriginalCost['originalcost'];
		 	foreach($resAllTransaction as $recordAllTransaction)
		    {
		    	if($recordAllTransaction['transaction_type'] == '0' || $recordAllTransaction['transaction_type'] == '3')
		    	{
				  $cashInHand -=$recordAllTransaction['price'];
				}
				else
				{
					$cashInHand +=$recordAllTransaction['price'];
				}
		    }
			$this->cashInHand = $cashInHand;
	    	return $cashInHand;
	 	}
	 }

	function getOrginalCost()
	{
		$sqlGetOriginalCost="SELECT SUM(cash_amt) originalcost,substring((min(creation_date)),1,10) as date FROM techstrat_cash WHERE STATUS ='1'";
		$resGetOriginalCost=exec_query($sqlGetOriginalCost,1);
		return $resGetOriginalCost;
	}
	function getCurrentPortfolioValue()
	{
		global $performanceobj;
		$qryGetOpenPositions="SELECT transaction_id,transaction_type,SUM(quantity) totalbuy, SUM(sold_quantity) totalsold,EX.stocksymbol FROM techstrat_openpositions QOP, ex_stock EX  WHERE QOP.quote_id=EX.id GROUP BY QOP.quote_id,transaction_type";
		$resGetOpenPositions=exec_query($qryGetOpenPositions);
		foreach($resGetOpenPositions as $stockDetails)
		{

			$socksymbol[] = strtoupper($stockDetails['stocksymbol']);
			$toalInHand[] = $stockDetails['totalbuy'] - $stockDetails['totalsold'];
			$tranctionType[] = $stockDetails['transaction_type'];


		}
		$currentOpenStocks=$performanceobj->getCurrentStockValue($socksymbol);

		$currentOpenpositionValue = 0;
		$currentOpenpositionShortSellValue = 0;
		$currentOpenpositionBuyValue = 0;
		foreach($socksymbol as $trans_id => $symbol)
		{
			if($tranctionType[$trans_id] == '2')
			{
				$currentOpenpositionShortSellValue += $currentOpenStocks[$symbol]*$toalInHand[$trans_id];
			}
			else
			{
				$currentOpenpositionBuyValue += $currentOpenStocks[$symbol]*$toalInHand[$trans_id];
			}
		}
		$this->currentOpenpositionShortSellValue = $currentOpenpositionShortSellValue;
		$this->currentMarketValue = $currentOpenpositionBuyValue;
		return $currentPortfolioValue = $this->getPortfolioCashinHand()+$currentOpenpositionBuyValue-$currentOpenpositionShortSellValue;

	}

	function getClosePositionAmount()
	{
		$stClosePositionQuery = "SELECT SUM(sold_amount-buy_amount) AS profit FROM (
	SELECT QT.id,quote_id,(unit_price*quantity) AS sold_amount,quantity,(SELECT unit_price*QT.quantity  FROM techstrat_transaction WHERE id = QST.buy_trans_id) AS buy_amount
		FROM techstrat_transaction AS QT, techstrat_sell_transaction AS QST
	WHERE QT.id = QST.sell_trans_id AND (QT.transaction_type='1') AND QT.status='1'
		UNION
	SELECT QT.id,quote_id,(SELECT unit_price*QT.quantity FROM techstrat_transaction WHERE id = QST.sell_trans_id) AS sold_amount,quantity,(unit_price*quantity) AS buy_amount
	FROM techstrat_transaction AS QT, techstrat_sell_transaction AS QST
	WHERE QT.id = QST.buy_trans_id AND (QT.transaction_type='3') AND QT.status='1'
		ORDER BY quote_id) AS qp";
		$resClosePosition=exec_query($stClosePositionQuery,1);
		return $resClosePosition['profit'];

	}

	function getAvgReturn($originalCost=NULL,$portfolioCurrentValue= NULL)
	{

		if($originalCost==NULL)
		{
			$arOriginalCost = $this->getOrginalCost();
			$originalCost =$arOriginalCost['originalcost'];
		}
		if(currentPortfolioValue == NULL)
		{
			$portfolioCurrentValue = $this->getCurrentPortfolioValue();
		}
		$portfolioAverageReturn = (($portfolioCurrentValue - $originalCost)/$originalCost)*100;
		return $portfolioAverageReturn = number_format($portfolioAverageReturn, 2, '.', ',');
	}

	function addinjsfile()
	{
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
		//***$fname="$HTPFX"."$HTHOST"."/js/stock_suggestion.js";
		global $CDN_BUCKET;
		$fname=$CDN_BUCKET."/js/stock_suggestion.js";
		$options = [ "gs" => [ "Content-Type" => "text/plain", "acl" => "public-read" ]];
		$ctx = stream_context_create($options);
		file_put_contents($fname, $strTags, 0, $ctx);
	}

	public function displayOpenPositions()
	{
		$dataOpenPositions=$this->getOpenPositionsRecord();?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" >
    		<tr class="viewby_heading">
	   			<form style="margin:0px; padding:0px;" name="form1" id="form1" method="post" action="">
		    		<td width="50" valign="bottom" >view by:</td>
		       		<td width="20"> <input type="radio" name="rad" value="1" checked="checked" onclick="javascript:redirectpage('1');" /> </td>
		        	<td width="50">company</td>
		       		<td width="20"><input type="radio" name="rad" value="2"  onclick="javascript:redirectpage('2');"/></td>
		        	<td width="50">Trade</td>
		       		<td colspan="4" width="800">&nbsp;</td>
	            </form>
			</tr>
       		<td colspan="9" valign="top">
    			<div class="open_main_container">
    			<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
				<tr>
					<td class="">Name</td> <!--company name for stocks-->
					<td>Ticker</td>
			        <td>Current Quote</td>
			        <td>Open Date</td>
			        <td>L/S</td>
			        <td># of Shares</td>
					<td>Cost Basis/Share</td>
			        <td>Current Value</td>
			        <td>Gain/Loss $</td>
			        <td>Percentage Portfolio</td>
				</tr>
				<tr>
        			<td colspan="10" valign="top"><div class="open_position_divider"></div> </td>
        		</tr>
			<? 	if(count($dataOpenPositions)>0){
					$flag = false;
	 				foreach($dataOpenPositions as $postkey=>$postval){
						if($flag){
				 			echo '<tr class="market_grey_row">';
							$flag = false;
		 				}else{
						   echo '<tr>';
						   $flag = true;
		 				} ?>
		 				<td><?=$postval['compName'];?></td>
				        <td><?=$postval['ticker'];?></td>
						<td >$<?=$postval['currentQuote'];?> </td>
						<td ><?=$postval['openDate'];?> </td>
						<td ><?=$postval['tradeType'];?> </td>
						<td ><?=$postval['contracts'];?> </td>
						<td ><?=$postval['costBasis'];?> </td>
						<? if(substr($postval['currentValue'],0,1)=='-'){ ?>
							<td >-$<?=substr($postval['currentValue'],1,15);?> </td>
						<? }else{ ?>
							<td >$<?=substr($postval['currentValue'],0,15);?> </td>
						<? }
						if(substr($postval['gainorloss'],0,1)=='-'){
							$gain=substr($postval['gainorloss'],1,15);?>
							<td  style="color:#FF0000">-$<?=$gain;?></td>
						<? } else { ?>
							<td  style="color:#009900">$<?=$postval['gainorloss'];?></td>
						<? } ?>
						<td><?=$postval['percentagePortfolio'];?>%</td>
						</tr>
				<? }// for loop end?>
			</table></div></td>
		<? 	} ?>
         </table>
	<? }

	private function getOpenPositionsRecord()
	{
		global $techStratMemcacheExpire,$performanceobj, $ytObj;
		/*$memCacheOpenPosition = new memCacheObj();
		$key="techStratOpenPosition";
		$dataOpenPosition = $memCacheOpenPosition->getKey($key);
		if(empty($dataOpenPosition))
		{*/
			$totPerformance = $ytObj->performanceCalculation();
			$totCurrentVal = $totPerformance['curVal'];
			$totCurrentVal = str_replace(',','',$totCurrentVal);
			$qryGetOpenPositions="SELECT ES.companyname AS companyname, ES.stocksymbol ticker, SOP.transaction_type, DATE_FORMAT(MIN(SOP.creation_date),'%m/%d/%y') AS creation_date_get, ROUND(SUM(SOP.quantity-SOP.sold_quantity)) AS contracts, ROUND(SUM(SOP.quantity*SOP.unit_price)/SUM(SOP.quantity),2) AS costbasic FROM techstrat_openpositions SOP,ex_stock ES,techstrat_transaction ST WHERE SOP.quote_id=ES.id AND ST.id=SOP.transaction_id AND ST.status='1' GROUP BY ES.stocksymbol ORDER BY creation_date_get DESC";
			$resGetOpenPositions=exec_query($qryGetOpenPositions);
			if(!empty($resGetOpenPositions)){
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
					$this->openPosition[$openPosition['ticker']]['costBasis'] = number_format($openPosition['costbasic'],2);
					$this->openPosition[$openPosition['ticker']]['percentagePortfolio'] = number_format(($openPosition['contracts']*$openPosition['costbasic']/$totCurrentVal)*100,2);
					$stocksymbols[]=$openPosition['ticker'];
				}

				$currentOpenStocks=$performanceobj->getCurrentStockValue($stocksymbols);
				foreach($stocksymbols as $symbol)
				{
					$this->openPosition[$symbol]['currentQuote']=number_format($currentOpenStocks[$symbol],2);
					$this->openPosition[$symbol]['currentValue']=round($currentOpenStocks[$symbol]*$this->openPosition[$symbol]['contracts']);
					$this->openPosition[$symbol]['currentQuote'] = str_replace(',', '', $this->openPosition[$symbol]['currentQuote']);
					$this->openPosition[$symbol]['gainorloss']=round(($this->openPosition[$symbol]['currentQuote'] - $this->openPosition[$symbol]['costBasis'])*$this->openPosition[$symbol]['contracts']);
				}
			}
			/*$dataOpenPosition = $memCacheOpenPosition->setKey($key,$this->openPosition,$techStratMemcacheExpire);
		}*/
		return $this->openPosition;
	}

	private function getcurrentquotefeed($tickersymbol)
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

	public function updateOpenPositionRecords()
	{
		$sql="SELECT qt.id,qt.quote_id,qt.transaction_type,qt.creation_date,qt.unit_price,qt.quantity,(SELECT SUM(quantity) quantity FROM techstrat_transaction qp ,techstrat_sell_transaction qst WHERE qp.status =1 AND qst.sell_trans_id = qp.id AND qst.buy_trans_id = qt.id GROUP BY qt.id)AS sold_quantity FROM techstrat_transaction AS qt WHERE transaction_type = 0 AND STATUS =1 UNION SELECT qt.id,qt.quote_id,qt.transaction_type,qt.creation_date,qt.unit_price,qt.quantity, (SELECT SUM(quantity) quantity FROM techstrat_transaction qp ,techstrat_sell_transaction qst WHERE qp.status =1 AND qst.sell_trans_id = qt.id AND qst.buy_trans_id = qp.id GROUP BY qt.id) AS sold_quantity FROM techstrat_transaction AS qt WHERE transaction_type = '2' AND STATUS ='1' ORDER BY quote_id";
		$result = exec_query($sql);
		if(count($result)>0)
		{
			exec_query_nores('delete from techstrat_openpositions where transaction_id>0');
			foreach($result as $key=>$value)
			{
				$qp_id=$value['id'];
				$qp_quote_id=$value['quote_id'];
				$qp_transaction_type=$value['transaction_type'];
				$qp_creation_date=$value['creation_date'];
				$qp_unit_price=$value['unit_price'];
				$qp_quantity=$value['quantity'];
				$qp_sold_quantity=$value['sold_quantity'];

				if($qp_quantity!=$qp_sold_quantity)
				{
					$temptabldata=array('transaction_id'=>$qp_id,'quote_id'=>$qp_quote_id,'transaction_type'=>$qp_transaction_type,'creation_date'=>$qp_creation_date,'unit_price'=>$qp_unit_price,'quantity'=>$qp_quantity,'sold_quantity'=>$qp_sold_quantity);
					$sid=insert_query("techstrat_openpositions",$temptabldata);
					unset($temptabldata);
				}
			}
		}
	}

	public function getOpenPositionsTradeRecord($calPerfom)	/*if $calPerfom= 1 only then calculate Perfor*/
	{
		global $techStratMemcacheExpire,$performanceobj,$ytObj;
		/*$memCacheOpenPosition = new memCacheObj();
		$key="techStratOpenPositiontrade";
		$dataOpenPositiontrade = $memCacheOpenPosition->getKey($key);
		if(empty($dataOpenPositiontrade))
		{*/
			if($calPerfom=="1"){
				$totPerformance = $ytObj->performanceCalculation();
				$totCurrentVal = $totPerformance['curVal'];
				$totCurrentVal = str_replace(',','',$totCurrentVal);
			}
			$qryRecords="SELECT ES.CompanyName AS companyname, ES.stocksymbol ticker, SOP.transaction_type, DATE_FORMAT(SOP.creation_date,'%m/%d/%y') AS creation_date_get, ROUND(SOP.quantity-SOP.sold_quantity) AS contracts, ROUND(SOP.quantity*SOP.unit_price/SOP.quantity,2) AS costbasic FROM techstrat_openpositions SOP,ex_stock ES,techstrat_transaction ST WHERE SOP.quote_id=ES.id AND ST.id=SOP.transaction_id AND ST.status='1' ORDER BY creation_date_get DESC";
			$resGetOpenPositions=exec_query($qryRecords);
			$totalcurrentvalue=0;
			if(!empty($resGetOpenPositions)){
				foreach($resGetOpenPositions as $key=>$openPosition)
				{
					$this->openPosition[$key]['compName']=$openPosition['companyname'];
					$this->openPosition[$key]['ticker']=$openPosition['ticker'];
					$this->openPosition[$key]['transaction_type']=$openPosition['transaction_type'];
					$this->openPosition[$key]['openDate']=$openPosition['creation_date_get'];
					if($openPosition['transaction_type']=='0'){
						$this->openPosition[$key]['tradeType']='L';
						$this->openPosition[$key]['contracts']=$openPosition['contracts'];
					}
					elseif($openPosition['transaction_type']=='2'){
						$this->openPosition[$key]['tradeType']='S';
						$this->openPosition[$key]['contracts']=-$openPosition['contracts'];
					}
					else{
						$this->openPosition[$key]['tradeType']='N/A';
						$this->openPosition[$key]['contracts']=$openPosition['contracts'];
					}
					$this->openPosition[$key]['costBasis']=number_format($openPosition['costbasic'],2);
					if($calPerfom=="1"){
						$this->openPosition[$key]['percentagePortfolio'] = number_format(($openPosition['contracts']*$openPosition['costbasic']/$totCurrentVal)*100,2);
					}
					$stocksymbols[]=$openPosition['ticker'];
				}

				$currentOpenStocks=$performanceobj->getCurrentStockValue($stocksymbols);
				if(!empty($stocksymbols)){
					foreach($stocksymbols as $k=>$symbol)
					{
						$this->openPosition[$k]['currentQuote']=number_format($currentOpenStocks[$symbol],2);
						$this->openPosition[$k]['currentValue']=round($currentOpenStocks[$symbol]*$this->openPosition[$k]['contracts']);
						$this->openPosition[$k]['gainorloss']=round(($this->openPosition[$k]['currentQuote'] - $this->openPosition[$k]['costBasis'])*$this->openPosition[$k]['contracts']);
					}
				}
			}
			/*$dataOpenPositiontrade = $memCacheOpenPosition->setKey($key,$this->openPosition,$techStratMemcacheExpire);
		}*/
		return $this->openPosition;
	}

	public function displayOpenPositionTrade(){
		 $dataOpenPositionstrade=$this->getOpenPositionsTradeRecord('1');?>
		 	<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" >
     		<tr class="viewby_heading">
   				<form style="margin:0px; padding:0px;" name="form1" id="form1" method="post" action="">
    				<td width="50" valign="bottom" >view by:</td>
       				<td width="20"> <input type="radio" name="rad" value="1"  onclick="javascript:redirectpage('1');" /> </td>
        			<td width="50">company</td>
       				<td width="20"><input type="radio" name="rad" value="2" checked="checked" onclick="javascript:redirectpage('2');"/></td>
        			<td width="50">Trade</td>
        			<td colspan="4" width="800">&nbsp;</td>
      			</form>
  			</tr>
			<td colspan="9" valign="top">
    			<div class="open_main_container">
    			<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
      			<tr>
        			<td class="">Name</td>  <!--company name for stocks-->
					<td>Ticker</td>
			        <td>Current Quote</td>
			        <td>Open Date</td>
			        <td>L/S</td>
			        <td># of Shares</td>
			        <td>Cost Basis/Share</td>
			        <td>Current Value</td>
			        <td>Gain/Loss $</td>
			        <td>Percentage Portfolio</td>
      			</tr>
      			<tr>
      				<td valign="top" colspan="10"><div class="open_position_divider"></div></td>
      			</tr>
			<? 	if(count($dataOpenPositionstrade)>0){
					$flag = false;
					 foreach($dataOpenPositionstrade as $postkey=>$postval){
					 	if($flag){
		 					echo '<tr class="market_grey_row">';
		 					$flag = false;
		 				}else{
		   					echo '<tr>';
		   					$flag = true;
		 				}?>
		 				<td><?=$postval['compName'];?></td>
				        <td><?=$postval['ticker'];?></td>
						<td >$<?=$postval['currentQuote'];?> </td>
						<td ><?=$postval['openDate'];?> </td>
						<td ><?=$postval['tradeType'];?> </td>
						<td ><?=$postval['contracts'];?> </td>
						<td ><?=$postval['costBasis'];?> </td>
						<? if(substr($postval['currentValue'],0,1)=='-'){ ?>
							<td >-$<?=substr($postval['currentValue'],1,15);?> </td>
						<? }else{ ?>
							<td >$<?=substr($postval['currentValue'],0,15);?> </td>
						<? }
						if(substr($postval['gainorloss'],0,1)=='-'){
							$gain=substr($postval['gainorloss'],1,15);?>
							<td  style="color:#FF0000">-$<?=$gain;?></td>
						<? } else { ?>
							<td  style="color:#009900">$<?=$postval['gainorloss'];?></td>
						<? } ?>
						<td><?=$postval['percentagePortfolio'];?>%</td>
					</tr>
			<?	} // for loop end
		} ?>
  		</table></div></td>
  	</table>
	<? }
} // Class End
?>
