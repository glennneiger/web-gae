<?
class techstratYTDRET
{
	public $firsttransactiondate=null; // first transaction date of the current year
	function __construct()
	{
		$this->firsttransactiondate=$this->getfirstTransDate();
	}
	function getfirstTransDate()
	{
		$curyear=date('Y');
		// not checking any kind of transaction_type (buy/sell/ss/btc)
		$qryfirsttrans="select creation_date from techstrat_transaction where creation_date>='$curyear-01-01 00:00:00' and status=1 LIMIT 1";
		//echo $qryfirsttrans;
		$firstdata=exec_query($qryfirsttrans);
		if(count($firstdata)>0)
		{
			foreach($firstdata as $allresultss)
			{
				$dateget=$allresultss['creation_date'];
			}
		}
		return $dateget;
	}

	public function performanceCalculation(){
		global $techStratMemcacheExpire,$techPortfolioInception,$performanceobj,$arrPerformance;
		$key="techStratDisplayPerformance";
		$arrPerformance['cashinhand']=$performanceobj->getcashinhand();
		$arrPerformance['marketValOfSecurities']=$performanceobj->getMarketValOfSecurities();
		$arrPerformance['curVal']=$arrPerformance['marketValOfSecurities']+$arrPerformance['cashinhand'];
		$arrPerformance['curVal']=number_format($arrPerformance['curVal'], 2, '.', ',');

		return $arrPerformance;
	}

	// only for buy / sell
	public function getcashinhandytd()
	{
		global $transObj,$initialcashamtget;
		$dates=$this->firsttransactiondate;
		if(isset($dates))
		{
			$append=" and creation_date>='$dates'";
		}
		else
		{
			$append='';
		}
		$initialcashamt=0;
		$totbuycash=0;
		$totsellcash=0;

		 $initialcashqry="select sum(cash_amt) as cash_amt from techstrat_cash where (status='1' or status='2')";// doubt as everytime we are fetching the same value
		 $allresintcash=exec_query($initialcashqry);

		 if(count($allresintcash)>0)
		 {
			 foreach($allresintcash as $k=>$val)
			 {
				 $initialcashamt = $val['cash_amt'];
				 $initialcashamtget=$initialcashamt;
			 }
		 }
		 $sql_totbuyamtofbuytrans="select sum(quantity*unit_price) as totbuyamt from techstrat_transaction where status='1' and (transaction_type='0') $append";
		 $allresbuy=exec_query($sql_totbuyamtofbuytrans);
		 if(count($allresbuy)>0)
		 {
			 foreach($allresbuy as $key=>$value)
			 {
				 $totbuycash = $value['totbuyamt'];
			 }

		 }
		 $sql_totsellamtofselltrans="select sum(quantity*unit_price) as totsell from techstrat_transaction where status='1' and (transaction_type='1') $append";
		 $allressell=exec_query($sql_totsellamtofselltrans);
		 if(count($allressell)>0)
		 {
			foreach($allressell as $keys=>$values)
			{
				$totsellcash = $values['totsell'];
			}
		 }
		/*
		echo "<br>Initial cash amt : ".$initialcashamt;
		echo "<br>total buy amt : ".$totbuycash;
		echo "<br>total sell amt : ".$totsellcash;
		*/
		$totcashinhand=($initialcashamt-$totbuycash)+$totsellcash;
		return $totcashinhand;
	}

	# This function calculates the total loss/gain of the short sell / btc
	private function getgainlossfrmssbtc()
	{
		global $transObj;
		$dates=$this->firsttransactiondate;
		$totgainloss=0;
		$totgainlossd=0;
		$isbtcdone=0;
		$allressell=$transObj->ytdallgainLossfromss;//exec_query($sqlallsellids);
		if(count($allressell)>0)
		{
			$allresbtc=$transObj->ytdallgainLossfrombtc;//exec_query($sqlchkbtc);
			$selltransArray =$transObj->ytdallids;

			//start
			foreach($allressell as $allselltransres)
			{
				$transellid=$allselltransres['id']; // auto id of transaction table
				$transellidup=$allselltransres['unit_price']; // unit price of each ss
				$transellidqty=$allselltransres['quantity']; // total ss qty
				$transellquote_id=$allselltransres['quote_id'];// symbol id
				$transellsymbol=$allselltransres['stocksymbol']; // symbol name required for yahoo fetch


				if(count($allresbtc)>0 && in_array($transellid,$selltransArray))
				{
					$isbtcdone=1;
					$totqtybtcoved=0;
					$totalbtcdup=0;

					foreach($allresbtc[$transellid] as $allbtctransres=>$vals)
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
					$restsharescurntval=$restsharesqty*($transObj->getcurrent_quote($transellsymbol));
					$netlossonrestshares=0;
					$netlossonrestshares=$restsharesselltot-$restsharescurntval;
					$totgainlossd=$totgainlossd+($netprofitonbtcshares+$netlossonrestshares);
				}// end
				else
				{
					$isbtcdone=0; // no btc has done against this ss
					$currentquote=0;
					$currentquote= $transObj->getcurrent_quote($transellsymbol);
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

	# This function returns the totalcurrent value of balance stocks ( those in hand & transaction_type=0 )
	function totalcurrentvalueofallstocksbuyytd()
	{
		global $transObj;
		$dates=$this->firsttransactiondate;
		if(isset($dates)){
			$append=" and QT.creation_date>='$dates'";
		}else{
			$append='';
		}
		$stockinhandarr=$transObj->ytdstockinhandarr;//$transObj->getallbuystocks($dates);
		$totalcurrentvalue=0;
		$qryforallbuytran="select QT.quote_id,QT.transaction_type,ES.stocksymbol,QT.unit_price,QT.quantity from techstrat_transaction QT, ex_stock ES where  QT.quote_id=ES.id and (QT.transaction_type='0') and QT.status='1' group by QT.quote_id,QT.transaction_type order by QT.transaction_type,ES.companyname,QT.creation_date $append";
		$tottransarr=exec_query($qryforallbuytran);

		if(count($tottransarr)>0)
		{
			foreach($tottransarr as $res)
			{
				$symbolname=$res['stocksymbol'];
				$sid=$res['quote_id'];
				/* If there is some stock in hand then go for current value */
				if($stockinhandarr[$sid]!=0)
				{
					$currentquote= $transObj->getcurrent_quote($symbolname);
				}
				else
				{
					$currentquote=0;
				}
				$currentval=$currentquote * $stockinhandarr[$sid];
				$totalcurrentvalue=$totalcurrentvalue+$currentval;
			}
			return $totalcurrentvalue;
		}
		else
		{
			return 0;
		}
	}

	public function ytdreturnpercnt()
	{
		global $performanceobj, $techPortfolioInception;
		$curntvalofportfolio=$performanceobj->curVal;
		$curntvalofportfolio = str_replace(',','',$curntvalofportfolio);
		$ytdtobereturn=(($curntvalofportfolio-$techPortfolioInception)/$techPortfolioInception)*(100);
		return $ytdtobereturn;
	}
}
?>