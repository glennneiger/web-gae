<?
class Performance
{
	public $performanceData=array();
	public $finalGainLossFromBalancedSS=0;

	public function Performance()
	{
		//$this->performanceData=$this->displayperformance();
	}

	function validatesymbol($symbolname,$entitytype)
	{
		$tickersymbol=$symbolname;
		if (isset($tickersymbol))
		{
			$valid=1;
			$read=$this->getcurrentquotefeed($tickersymbol,$entitytype);
			IF ($read == 0)
			{
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

	public function unitpriceofstockinhand($dates=NULL)
	{
		if(isset($dates)){
			$append=" and creation_date>='$dates'";
		}else{
			$append='';
		}
		$quer_for_buyids="select quote_id,sum(quantity) as totpurchqty,sum((unit_price)*(quantity)) as totpurchamt  from techstrat_transaction where entity_type='0' and status='1' and transaction_type='0' $append group by quote_id";
		$allr=exec_query($quer_for_buyids);
		if(count($allr)>0){
			$stockbuys=array();

			foreach($allr as $allresultbuy){
				$qid=$allresultbuy['quote_id'];
				$totalpurchsdqty=$allresultbuy['totpurchqty']; // Final for each qid
				$totpurchamtget=$allresultbuy['totpurchamt'];

				$totalsoldamt=0;
				$sellqty=0;		// chek for total sell amt

				$quer_for_sellid="select id, quantity as totsell from techstrat_transaction where entity_type='0' and status='1' and transaction_type='1' and quote_id='$qid' $append";
				$alls=exec_query($quer_for_sellid);
				$totalsellqty=0;
				if(count($alls)>0){
					$soldqty=0;
					$totalsellqty=0;

					foreach($alls as $allresultsell){
						$selltransid=$allresultsell['id'];
						$soldqty=$allresultsell['totsell'];
					 	$totalsellqty=$totalsellqty+$soldqty;
        				$upqry="select QT.unit_price up,QT.id from techstrat_transaction QT, techstrat_sell_transaction QST where QT.entity_type='0' and QST.sell_trans_id='$selltransid' and QST.buy_trans_id=QT.id";
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

	// called in side displayperformance()
	public function getAllQuotescurntval()
	{
		global $transObj;
		$sql_chk="select cash_amt as tot,id from techstrat_cash where status='1'";
		$searchfound=exec_query($sql_chk);

		 if($searchfound[0]['tot']=='')
		 {
			 $cashget='0.00';
		 }
		 else
		 {
			 if(count($searchfound)>0)
			 {
				 foreach($searchfound as $res)
				 {
					 $cashget=$res['tot'];
				 }
			 }
		 }

		$getAllQuotescurntvalarr=array();

		$allstockids=$transObj->getallbuystocks(); // returns all quote_id with qty in hand aswini

		if(count($allstockids)>0){
			$allsymbolidsarr=array_keys($allstockids);
		}

		/****** STOCK QUOTES CONCAT START ***********/
		$qrystr='';

		if(count($allsymbolidsarr)>0)
		{
			$allsymbolnamesarr=$this->allsymbolsarr(); // returns all symbol names

			for($x=0;$x<count($allsymbolidsarr);$x++)
			{
				if(($allstockids['0'][$allsymbolidsarr[$x]])!=0)
				{
					if($qrystr=='')
					{
						$qrystr=$allsymbolnamesarr[$allsymbolidsarr[$x]];
					}
					else
					{
						$qrystr=$qrystr."+".$allsymbolnamesarr[$allsymbolidsarr[$x]];
					}
				}
			}
		}
			/****** STOCK QUOTES CONCAT END ***********/

		# if there are some symbols go for yahoo and get there current quotes
		if($qrystr!='')
		{
			$unitpriceofstockinhand=$this->unitpriceofstockinhand(); // returns an array with quote_id as key
		// cant change use multiple
			$filename="http://download.finance.yahoo.com/d/allquotes.csv?s=".$qrystr."&f=sl1&e=.csv";
			$open = file($filename);
			if(isset($open))
			{
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

					if($read[1]==0)
					{
					    $tickersymbol=$read[0];
						$read[1]=$this->getcurrentquotefeed($tickersymbol,0);
					}
					//*** $currentsymvalarr[$key]=$read[1];
					$thiskeyval=0;
					$thiskeyval=$allstockids['0'][$key]*$read[1];
					$totalcurrentvalue=$totalcurrentvalue+$thiskeyval;
					$totaloriginalcost=$totaloriginalcost+($allstockids['0'][$key]*$unitpriceofstockinhand[$key]);
				}
				$totalgainorloss=$totalcurrentvalue-$totaloriginalcost;
				//echo "<br>Stock totalgainorloss----".$totalgainorloss;
				$totalavgreturnpercnt=($totalgainorloss/$totaloriginalcost)*100;
				//echo "<br>Stock totalavgreturnpercnt----".$totalavgreturnpercnt;
			}
		}

		$getAllQuotescurntvalarr['oc']=$cashget; // changed as on 12022008 on request // originalcost

		$getAllQuotescurntvalarr['cqv']=$totalcurrentvalue; // current quote value

		$getAllQuotescurntvalarr['avg']=$totalavgreturnpercnt; // avg. price of all quotes
		return $getAllQuotescurntvalarr;
	}

	public function getOriginalcost()
	{
		$sql_chk="select cash_amt as tot,id from techstrat_cash where status='1'";
		$searchfound=exec_query($sql_chk);

		if($searchfound[0]['tot']=='')
		{
			$cashget='0.00';
		}
		else
		{
			if(count($searchfound)>0)
			{
				foreach($searchfound as $res)
				{
					$cashget=$res['tot'];
				}
			}
		}
		$getAllQuotescurntvalarr['oc']=$cashget; // changed as on 12022008 on request // originalcost
		return $getAllQuotescurntvalarr;

	}

	# This function calculates the total loss/gain of the short sell+btc
	function getgainlossfrmssbtc()
	{
		 $totgainloss=0;
		 $totgainlossd=0;
		 $isbtcdone=0;

		$sqlallsellids="select qt.entity_type,qt.id,qt.quote_id,ES.stocksymbol,qt.unit_price,qt.quantity from techstrat_transaction qt,ex_stock ES where qt.transaction_type='2' and qt.status='1' and qt.quote_id=ES.id and entity_type='0' UNION ALL select qt.entity_type,qt.id,qt.quote_id,ES.optionticker stocksymbol,qt.unit_price,qt.quantity from techstrat_transaction qt,ex_option ES where qt.transaction_type='2' and qt.status='1' and (qt.quote_id=ES.id) and entity_type='1'";
			 //echo $sqlallsellids;

			 $allressell=exec_query($sqlallsellids);
			 if(count($allressell)>0)
			 {
				 //start
				 foreach($allressell as $allselltransres)
				 {
					 $transellid=$allselltransres['id']; // auto id of transaction table
					 $transellidup=$allselltransres['unit_price']; // unit price of each ss
					 $transellidqty=$allselltransres['quantity']; // total ss qty
					 $transellquote_id=$allselltransres['quote_id'];// symbol id
					 $transellsymbol=$allselltransres['stocksymbol']; // symbol name required for yahoo fetch
					 $entitytype=$allselltransres['entity_type'];

					# check the btc quantity against this ss
					 $sqlchkbtc="select qt.entity_type,qt.id,qt.unit_price,qt.quantity from techstrat_sell_transaction qst, techstrat_transaction qt where qst.sell_trans_id='$transellid' and qst.status='1' and qst.trans_status!='pending' and qt.id=qst.buy_trans_id";
					 //echo '<br>'.$sqlchkbtc.";";

					 $allresbtc=exec_query($sqlchkbtc);

					 if(count($allresbtc)>0)
					 {
							 // if there is some btc
						     $isbtcdone=1;
							 $totqtybtcoved=0;
							 $totalbtcdup=0;
							 foreach($allresbtc as $allbtctransres)
							 {
							 $qtybtcoved=$allbtctransres['quantity'];
							 $btcovedup=$allbtctransres['unit_price'];
							 $entitytype=$allbtctransres['entity_type'];
							 $btcdup=$qtybtcoved*$btcovedup;
							 $totalbtcdup=$totalbtcdup+$btcdup;
							 $totqtybtcoved=$totqtybtcoved+$qtybtcoved;
							 }
							 /*
							 echo "<br> Original ss qty : ".$transellidqty;
							 echo "<br> Total btc qty : ".$totqtybtcoved."<br>";
							 echo "<br> Total rest qty : ".($transellidqty-$totqtybtcoved)."<br>";
							 echo "<br> Total Buy to cover amt of ($totqtybtcoved) shares : ".$totalbtcdup."<br>";
							 */
							 // calculate total gain or loss on abhi tak shares jo btc ho gaye
							 // chek $totqtybtcoved shares sell amt
							 $soldsharesorginalval=0;
							 $soldsharesorginalval=$transellidup*$totqtybtcoved;
							 $buysharesorginalval=$totalbtcdup;
							 $netprofitonbtcshares=0;
							 $restsharesqty=$transellidqty-$totqtybtcoved;
							 $netprofitonbtcshares=($soldsharesorginalval-$totalbtcdup);
							 $restsharesselltot=0;
							 //***echo '<br>----'.$restsharesqty.'------'.$transellidup;
							 $restsharesselltot=$restsharesqty*$transellidup;
							 $restsharescurntval=0;
							 $restsharescurntval=$restsharesqty*($this->getcurrentquotefeed($transellsymbol,$entitytype));
//							 $totgainlossd=$totgainlossd+$netprofitonbtcshares;
							 /*
							 echo "<br> un shares pe profit hua : ".$netprofitonbtcshares."<br>";
							 echo "<br> rest shares tot amt : ".$restsharesselltot;
							 echo "<br> rest shares market value : ".$restsharescurntval."<br>";
							 */
							 $netlossonrestshares=0;
							 $netlossonrestshares=$restsharesselltot-$restsharescurntval;
							 //echo "<br> rest shares gain/loss value : ".$netlossonrestshares."<br>";
							 //****echo "<br>totgainloss--------$totgainlossd netprofitonbtcshares--------: $netprofitonbtcshares netlossonrestshares--------: $netlossonrestshares";
							 $totgainlossd=$totgainlossd+($netprofitonbtcshares+$netlossonrestshares);
							 //echo "<br>............. $totgainlossd<br>";
					 }// end
					 else
					 {
						 $isbtcdone=0; // no btc has done against this ss
						 //echo "<br>".$sqlchkbtc;
						 $currentquote=0;
						 $currentquote= $this->getcurrentquotefeed($transellsymbol,$entitytype);
						 //echo "<br>$transellsymbol cuurent valus : ".$currentquote;
						 $totsellvalue=0;
						 $totsellcurntvalue=0;
						 $totsellvalue=$transellidqty*$transellidup; // total ss qty *  unit price of each ss
						 $totsellcurntvalue=$transellidqty*$currentquote;
						 //echo "<br>totgainloss--------$totgainloss totsellvalue--------$totsellvalue totsellcurntvalue--------$totsellcurntvalue";
						 $totgainloss=$totgainloss+($totsellvalue-$totsellcurntvalue);
					 }
				 }
				 return $totgainloss+$totgainlossd;
			 }//end
			 else
			 {
				 return $totgainloss+$totgainlossd;
			 }
	}


	public function getexchangevaldetails($exchangename,$inceptiondate)
	{
		if(isset($inceptiondate)){
			if($exchangename=='S&P'){
				$exchangename='5EGSPC';
			}else if($exchangename=='nasdaq'){
				$exchangename='5EIXIC';
			}
			$tickersymbol=$exchangename;
			if (isset($tickersymbol)){
				if(isset($fileforlatest)){
					unset($fileforlatest);
				}
				$fileforlatest="http://download.finance.yahoo.com/d/quotes.csv?s=%$tickersymbol&f=sl1d1t1c1ohgvn&e=.csv";
				$openlatest = file($fileforlatest);
				if(isset($currentsymvalarrlatest)){
					unset($currentsymvalarrlatest);
				}
				$currentsymvalarrlatest=array();
				$currentsymvalarrlatest=explode(',',($openlatest[0]));
				$currentopenpostionval=0;
				$currentopenpostionval=$currentsymvalarrlatest[1];
				 /**
				  We are getting the inception date value from constant file
				  Changes made on: 13th March 2009
				  Request id: 2009-03-37984
				 **/
				global $inceptionDateValue;
				$growthinpercnt=0;
				$growthinpercnt=(($currentopenpostionval - $inceptionDateValue)/ $inceptionDateValue)*100;
				return number_format($growthinpercnt, 2, '.', ',');
			}else{
				return '0.00';
			}
		}
	}

	public function displayTechCurrentValueBox()
	{
		$strdisp='<table width="100%" border="0" cellpadding="0" cellspacing="0" align="left">';
		$performance=$this->calculatePerformance();
		$strdisp.='<tr>	<td  valign="top" width="310px">
			<div style="border:solid 1px #cccccc; margin-bottom:10px; width:310px;">
				<div class="right_common_head">
					<h2>current value of techstrat</h2>
				</div>
				<table width="309" class="market_heading" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>cash in hand<!-- Previously was showing: $dataperformance [cashinhand] --></td><td>$';
		 			$strdisp .=$this->cashinhand.'</td>
				</tr>
				<tr>
					<td>MARKET VALUE OF SECURITIES<!-- Previously was showing: $dataperformance [cashinhand] --></td><td>';
					 $strdisp .=$this->marketValOfSecurities.'</td>
				</tr>
				<tr>
					<td>TOTAL VALUE OF PORTFOLIO <!-- Previously was showing: $dataperformance [cashinhand] --></td><td>$';
					 $strdisp .=$this->curVal.'</td>
				</tr>
			</table>
		</div> </td></tr></table>';
		return $strdisp;
	}

	public function displayTechPositionPerformance()
	{
		global $ytObj;
		$this->calculatePerformance();
		$dataperformance=$this->performanceData;
		$strdisp.='<div class="right_common_container_flexfolio">
			<div class="right_common_head">
				<h2>techstrat performance</h2>
			</div>
			<table width="100%" class="market_heading" border="0" cellpadding="0" cellspacing="0">';
				$strdisp.='<tr><td >Original Cost </td><td >$ ';
					$strdisp.=$this->originalCost.'</td></tr>
					<tr class="market_grey_row"><td>Current Value of TechStrat </td>
						<td >$ ';
						$strdisp.=$this->curVal.'</td>
					</tr>';
					$strdisp.='<tr>
						<td class="$dynamicclass" >Total Average Return </td>
						<td class="$dynamicclass">';
						$strdisp.=$this->TotAvgReturn.'%</td>
					</tr>';
					$strdisp.='<tr class="market_grey_row">
						<td>2012 Return </td>
						<td>';
						$strdisp.='4.34%</td>
					</tr>';
					if(Date('Y')>='2013'){
						$strdisp.='<tr><td>';
						$strdisp.=Date('Y').' YTD Return </td><td>';
						$ytdret=number_format($ytObj->ytdreturnpercnt(), 2, '.', ',');
					 	$strdisp.=$ytdret.'%</td>
						 	</tr>';
					}
					$strdisp.='<tr>
						<td colspan="2" ><div class="divider_container">*since inception '.$this->inceptionDate.'</div>  </td>
					</tr>
				</table>
			</div>';
		return $strdisp;
	}

	public function displayTechPositionPerformanceRight()
	{
		 global $ytObj,$transObj;
		 $inception_query="select substring((min(creation_date)),1,10) as date from techstrat_transaction where status = 1";
		 $inception_date=exec_query($inception_query,1);
		 $this->calculatePerformance();
		 if($inception_date[date]!=''){
		 	$dispinception_date=date('Y-m-d',strtotime($inception_date[date]));
		 }else{
			 $dispinception_date="";
		 }
		 $strdisp='<div class="performanceRightContainer">
			<table width="100%" cellspacing="0" cellpadding="0" class="market_heading">';
		 $strdisp.='<tr>
			 	<td>';
				$strdisp.='2012 Return </td>
		     	<td>';
			 	$strdisp.='4.34%</td>
			 	</tr>';
		if(Date('Y')>='2013'){
			 $strdisp.='<tr>
			 	<td>';
				$strdisp.=Date('Y').' YTD Return </td>
		     	<td>';
				$ytdret=number_format($ytObj->ytdreturnpercnt(), 2, '.', ',');
			 	$strdisp.=$ytdret.'%</td>
			 	</tr>';
		}

		$strdisp.='<tr>
			 <td>Total Average Return </td>
			 	<td>';
			 	$totavgreturn=number_format($this->TotAvgReturn, 2, '.', ',');
				$strdisp.=$totavgreturn.'%</td>
			 </tr>
			 <tr>
			 	<td colspan="2" class="divider_container">*Since Inception '.$dispinception_date.'</td>
			 </tr>
		 </table></div>';
		 return $strdisp;
	 }

	public function getcurrentquotefeed($tickersymbol,$entitytype="")
	{
		$m=date("m")-1;
		$d=date("d")-1;
		$y=date("Y");

		if (isset($tickersymbol))
		{
			$open='';
			if($entitytype=="1")
			{
				$open = fopen("http://download.finance.yahoo.com/d/quotes.csv?s=$tickersymbol.X&f=sl1d1t1c1ohgvn&e=.csv", "r");
			}
			else
			{
				$open = fopen("http://download.finance.yahoo.com/d/quotes.csv?s=$tickersymbol&f=sl1d1t1c1ohgvn&e=.csv", "r");
			}
			$read = fread($open, 2000);
			fclose($open);
			$read = str_replace("\"", "", $read);
			$read = explode(",", $read);

			if ($read[1] == 0)
			{
				return 0;
			}else
			{
				return $read[1];
			}
		}
	}
	/*
	This function will return the balance short sell stocks+options current value
	*/
	private function getBalanceCurrentvalue()
	{
		$currentvalue=0;
		$totalAmount=0;
		$sql="select unit_price,id,entity_type,quote_id from techstrat_transaction where transaction_type='2' and status='1'";
		$allressell=exec_query($sql);
		if(count($allressell)>0)
		{
			$currentQuoteValuesArray['entitytpe']['optionorsymbol']=array();
			$allOptionnamesarr=$this->allOptionsTickerarr(); // returns all OPTION TICKER names
			$allsymbolnamesarr=$this->allsymbolsarr(); // returns all symbol names
			$finalBalanceGainLoss=0;

			foreach($allressell as $allselltransres)
			{
				$transellid=$allselltransres['id'];
				$transentity=$allselltransres['entity_type'];
				$quote_id=$allselltransres['quote_id'];
				$unit_price=$allselltransres['unit_price'];

				$balanceQty = $this->getBalanceQtyShortsell($transellid,$transentity);
				if($transentity==1)
				{
					$symbolName=$allOptionnamesarr[$quote_id];

				}
				else if($transentity==0)
				{
					$symbolName=$allsymbolnamesarr[$quote_id];
				}

				if($currentQuoteValuesArray[$transentity][$symbolName]!='')
				{
					$currentValueget = $currentQuoteValuesArray[$transentity][$symbolName];
				}else
				{
					if($balanceQty!=0)
					{
						$currentValueget=0;
						$currentValueget = $currentQuoteValuesArray[$transentity][$symbolName]=$this->getcurrentquotefeed($symbolName,$transentity);
					}
				}

				if($balanceQty!=0)
				{
						$totalAmount=$totalAmount+($currentValueget*$balanceQty);
				}
				$finalBalanceGainLoss=$finalBalanceGainLoss+($unit_price-$currentValueget)*$balanceQty;
			}
			$this->finalGainLossFromBalancedSS=$finalBalanceGainLoss;
			return $totalAmount;
		}
		else
		{
			// When no ss sell done
			return $totalAmount;
		}
	}

	# This function calculates the total loss/gain of the balance short sells
	function getBalanceQtyShortsell($transid,$entitytype)
	{
		$totalstockstobtc=0;
		$qry_transaction="select transaction_type,quantity from techstrat_transaction where entity_type='$entitytype' and status='1' and id='$transid' union ALL select QT.transaction_type,QT.quantity  from techstrat_transaction QT,techstrat_sell_transaction QST where QT.entity_type='$entitytype' and QST.sell_trans_id='$transid' and QST.status='1' and QST.buy_trans_id!=0 and QST.buy_trans_id=QT.id";
		$execqry = exec_query($qry_transaction);
		if(count($execqry)>0)
		{
			$totalstockstobtc=0;
			foreach(exec_query($qry_transaction) as $result)
			{
				$transaction_type_get=$result['transaction_type'];
				$quantity_get=$result['quantity'];
				if($transaction_type_get==2){$totalstockstobtc=$totalstockstobtc+$quantity_get;}
				if($transaction_type_get==3){$totalstockstobtc=$totalstockstobtc-$quantity_get;}
			}
			return $totalstockstobtc;
		}else
		{
			return $totalstockstobtc;
		}
	}

	public function displayCashInHand()
	{
		$getBalanceCurrentValue = $this->getBalanceCurrentvalue();
		$dataperformance=$this->performanceData;
		//$newCashEntry = ($dataperformance['oldcashinhand']-$getBalanceCurrentValue)-($gainlossfrombtc);
		$newCashEntry = ($dataperformance['oldcashinhand']-$getBalanceCurrentValue)-($this->finalGainLossFromBalancedSS);
		return $newCashEntry;//$dataperformance['newCashInHand']=$newCashEntry;
	}

		public function getOpenShortPosProfit(){
			$qryGetOpenShortPosStock="select unit_price,ES.stocksymbol, (quantity-sold_quantity) as openpos from techstrat_openpositions SOP,ex_stock ES where ES.id=SOP.quote_id and transaction_type='2'";
			$resGetOpenShortPosStock=exec_query($qryGetOpenShortPosStock);
			if($resGetOpenShortPosStock)
			{
				foreach($resGetOpenShortPosStock as $value)
				{
					$stocksymbols[]=$value['stocksymbol'];
				}
				$currValStocks=$this->getCurrentStockValue($stocksymbols);

				$profitOpenShortPosStock=0;
				foreach($resGetOpenShortPosStock as $value)
				{
					$profitOpenShortPosStock+=($value['unit_price']-$currValStocks[$value['stocksymbol']])*$value['openpos'];
				}
			}

			$profitOpenShortPos=$profitOpenShortPosStock;
			return $profitOpenShortPos;
		}


		public function getcashinhand()
		{
			 //Cash IN Hand = Original Cost-Total Purchase amount of Open Positions+Profit/loss from Closed Positions+Gain/loss from Short selling
			$originalCost=$this->getOrginalCost();
			$profitClosePosition=$this->getClosePositionAmount();

			$sqlOpenBuyAmmount="select sum(unit_price*(quantity-sold_quantity)) as buyammount from techstrat_openpositions where transaction_type='0'";
			$resOpenBuyAmmount=exec_query($sqlOpenBuyAmmount,1);
			$totOpenBuyAmmount = $resOpenBuyAmmount['buyammount'];
			$openShortPosProfit=$this->getOpenShortPosProfit();
			$totcashinhand=$originalCost-($totOpenBuyAmmount+$openShortPosProfit)+$profitClosePosition;
			return $totcashinhand;
		 }


		function getOrginalCost()
		{
			$sqlGetOriginalCost="SELECT SUM(cash_amt) originalcost,substring((min(creation_date)),1,10) as date FROM techstrat_cash WHERE STATUS ='1'";
			$resGetOriginalCost=exec_query($sqlGetOriginalCost,1);
			return $resGetOriginalCost['originalcost'];
		}

		public function getMarketValOfSecurities(){
			$objPortfolio= new techstratTransaction();
			$openPositions=$objPortfolio->getOpenPositionsTradeRecord('0');
			$marketValOfSecurities=0;
			foreach($openPositions as $openpos){
				$marketValOfSecurities+=$openpos['currentValue'];
			}
			return $marketValOfSecurities;
		}

		function getClosePositionAmount()
		{
			$stClosePositionQuery = "SELECT SUM(sold_amount-buy_amount) AS profit FROM (SELECT ST.id,quote_id,(unit_price*quantity) AS sold_amount,quantity,(SELECT unit_price*ST.quantity  FROM techstrat_transaction WHERE id = SST.buy_trans_id) AS buy_amount FROM techstrat_transaction AS ST, techstrat_sell_transaction AS SST WHERE ST.id = SST.sell_trans_id AND (ST.transaction_type='1') AND ST.status='1'
		UNION
		SELECT ST.id,quote_id,(SELECT unit_price*ST.quantity FROM techstrat_transaction WHERE id = SST.sell_trans_id) AS sold_amount,quantity,(unit_price*quantity) AS buy_amount FROM techstrat_transaction AS ST, techstrat_sell_transaction AS SST WHERE ST.id = SST.buy_trans_id AND (ST.transaction_type='3') AND ST.status='1' ORDER BY quote_id) AS ss";
			$resClosePosition=exec_query($stClosePositionQuery,1);
			return $resClosePosition['profit'];
		}

		function getCurrentStockValue($stocksymbols,$is_stock=NULL)
		{
			if(is_array($stocksymbols))
			{
				// Have to divide the symbols into list of 25 each, cause financial content gives maximum 25 records per request
				$arStockSymbolsChunk = array_chunk($stocksymbols,25);
				foreach($arStockSymbolsChunk as $arStockSymbols)
				{
					$arChunk[]=implode('+',$arStockSymbols);
				}
			}
			else
			{
				$arChunk[]=$stocksymbols;
			}
			foreach($arChunk as $stocklist)
			{
				$getStockData = xml2array("http://feeds.financialcontent.com/XMLREST?Account=minyanville&Ticker=".$stocklist);
				if(!empty($getStockData)){
					if(sizeof($stocksymbols)>1){
						foreach($getStockData['Quote']['Record'] as $stockDetails)
						{
							if(is_array($stockDetails)){
								$currentStocksValue[$stockDetails['Ticker']]=$stockDetails['Last'];
							}else{
								$currentStocksValue[$getStockData['Quote']['Record']['Ticker']]=$getStockData['Quote']['Record']['Last'];
							}
						}
					}else{
						foreach($getStockData['Quote'] as $stockDetails)
						{
							$currentStocksValue[$getStockData['Quote']['Record']['Ticker']]=$getStockData['Quote']['Record']['Last'];
						}
					}
				}else{
					return FALSE;
				}
				unset($getStockData);
			}
			return $currentStocksValue;
		}

	function getCurrentStockValueArr($stocksymbols,$is_stock=NULL)
		{
			if(is_array($stocksymbols))
			{
				$stocklist=implode('+',$stocksymbols);
			}
			else
			{
				$stocklist=$stocksymbols;
			}
			if($stocklist!="")
			{
				$getStockData = xml2array("http://feeds.financialcontent.com/XMLREST?Account=minyanville&Ticker=".$stocklist);
			}
			return $getStockData[Quote][Record];
		}


	public function getInceptionDate()
	{
		$sqlGetInceptionDate="select substring((min(creation_date)),1,10) as inceptiondate from techstrat_transaction where status='1'";
		$resGetInceptionDate=exec_query($sqlGetInceptionDate,1);
		return $resGetInceptionDate['inceptiondate'];
	}

	public function calculatePerformance()
	{
		global $techStratMemcacheExpire,$techPortfolioInception;
		$memCacheOpenPosition = new memCacheObj();
		$key="techStratDisplayPerformance";
	    /*$displayperformance = $memCacheOpenPosition->getKey($key);
		if($displayperformance){
			$this->inceptionDate=$displayperformance->inceptionDate;
			$this->originalCost=$displayperformance->originalCost;
			$this->cashinhand=$displayperformance->cashinhand;
			$this->marketValOfSecurities=$displayperformance->marketValOfSecurities;
			$this->curVal=$displayperformance->curVal;
			$this->TotAvgReturn=$displayperformance->TotAvgReturn;
			$this->YTDAvgReturn=$displayperformance->YTDAvgReturn;
		}else{*/
			$this->inceptionDate=$this->getInceptionDate();
			$this->originalCost=$this->getOrginalCost();
			$this->cashinhand=$this->getcashinhand();
			$this->marketValOfSecurities=$this->getMarketValOfSecurities();
			$this->curVal=$this->marketValOfSecurities+$this->cashinhand;
			$this->TotAvgReturn=round((($this->curVal-$this->originalCost)/$this->originalCost)*100,2);
			$this->YTDAvgReturn=round((($this->curVal-$techPortfolioInception)/$techPortfolioInception)*100,2);

			########Format performace###########
			$this->originalCost=number_format($this->originalCost, 2, '.', ',');
			$this->cashinhand=number_format($this->cashinhand, 2, '.', ',');
			if($this->marketValOfSecurities<0){
				$this->marketValOfSecurities="-$".number_format(-$this->marketValOfSecurities, 2, '.', ',');
			}
			else{
				$this->marketValOfSecurities="$".number_format($this->marketValOfSecurities, 2, '.', ',');
			}
			$this->curVal=number_format($this->curVal, 2, '.', ',');
			$this->TotAvgReturn=number_format($this->TotAvgReturn, 2, '.', ',');
			$this->YTDAvgReturn=number_format($this->YTDAvgReturn, 2, '.', ',');
			//$memCacheOpenPosition->setKey($key,$this,$techStratMemcacheExpire);
		/*}*/
			return $this;
	}

}
?>