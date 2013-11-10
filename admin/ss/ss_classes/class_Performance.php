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
public function getallbuystocks($dates=NULL)
{
		if(isset($dates)){
			$append=" and creation_date>='$dates'";
		}else{
			$append='';
		}
		$quer_for_buyids="select entity_type,quote_id,sum(quantity) as totpurch from ss_transaction where status='1' and transaction_type='0' $append group by quote_id,entity_type";
		//echo $quer_for_buyids;

		$allr=exec_query($quer_for_buyids);

		if(count($allr)>0)
		{
			$stockbuys=array();

			foreach($allr as $allresultbuy)
			{
				$qid=$allresultbuy['quote_id'];
				$entity_type=$allresultbuy['entity_type'];

					// chek for rotal sell amt
					$sellqty=0;
					$quer_for_sellid="select entity_type,sum(quantity) as totsell from ss_transaction where status='1' and transaction_type='1' and quote_id='$qid' and entity_type='$entity_type' $append group by quote_id";
					//echo "<br>".$quer_for_sellid;

					$alls=exec_query($quer_for_sellid);
						if(count($alls)>0){
							foreach($alls as $allresultsell){
								$sellqty=$allresultsell['totsell'];
							}
						}
			$buyqty=$allresultbuy['totpurch'];
			$stocksinhand=$buyqty-$sellqty;
			$stockbuys[$entity_type][$qid]=$stocksinhand;
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


public function unitpriceofstockinhand($dates=NULL)
{
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
# This function returns the array of all symbols name
	private function allOptionsTickerarr(){
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


 function selltransforsellshort()
 {
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

 function purpricetrans()
 {
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
     return $purtransarr;
 }

 // called in side displayperformance()
public function getAllQuotescurntval()
{
	$sql_chk="select cash_amt as tot,id from ss_cash where status='1'";
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

	 $allstockids=$this->getallbuystocks(); // returns all quote_id with qty in hand aswini

	 if(count($allstockids)>0)
		{
			$allsymbolidsarr=array_keys($allstockids['0']);
			$allOptionidsarr=array_keys($allstockids['1']);
		}

		/****** STOCK QUOTES CONCAT START ***********/
		$qrystr='';

		if(count($allsymbolidsarr)>0)
		{
			$allsymbolnamesarr=$this->allsymbolsarr(); // returns all symbol names

			for($x=0;$x<count($allsymbolidsarr);$x++)
			{
			// check the qty in hand !=0
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
		/****** OPTION QUOTES CONCAT START ***********/
		$optqrystr='';
		if(count($allOptionidsarr)>0)
		{
			$allOptionnamesarr=$this->allOptionsTickerarr(); // returns all OPTION TICKER names

			for($x=0;$x<count($allOptionidsarr);$x++)
			{
			// check the qty in hand !=0
			if(($allstockids['1'][$allOptionidsarr[$x]])!=0)
			{
					if($qrystr=='')
					{
						$optqrystr=$allOptionnamesarr[$allOptionidsarr[$x]].".X";
					}
					else
					{
						$optqrystr=$optqrystr."+".$allOptionnamesarr[$allOptionidsarr[$x]].".X";
					}
			}
			}
		}
		/****** STOCK QUOTES CONCAT END ***********/

		/* http://download.finance.yahoo.com/d/quotes.csv?s=QAVCC.X&f=sl1d1t1c1ohgvn&e=.csv returns
		QAVCC.X	1.02	2/24/2009	4:14pm	0	0.88	1.16	0.81	81921	QQQQ Mar 2009 29.
		http://download.finance.yahoo.com/d/quotes.csv?s=QAVCC.X&f=sl1&e=.csv returns
		QAVCC.X	1.02
		*/


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

		/*****OPTION STRAT ******/
		if($optqrystr!='')
		{
			$unitpriceofoptioninhand=$this->unitpriceofstockinhandoptions(); // returns an array with quote_id as key
			// cant change use multiple
			$filename="http://download.finance.yahoo.com/d/allquotes.csv?s=".$optqrystr."&f=sl1&e=.csv";
			$open = file($filename);

			if(isset($open)){
				$currentOptionvalarr=array();
				$totOptfetched=count($open);
				$totaloptcurrentvalue=0;
				$totalOptoriginalcost=0;
				$totalOptavgreturnpercnt=0;
				$totalOptgainorloss=0;

				for($k=0;$k<$totOptfetched;$k++){
					$read = $open[$k];
					$read = str_replace("\"", "", $read);

					$read = explode(",", $read);
					$result=explode(".",$read[0]); // QAVCC.X

					if(isset($key))	unset($key);
					$key = array_search($result[0], $allOptionnamesarr);

					if($read[1]==0)
					{
						$tickersymbol=$result[0];
						$read[1]=$this->getcurrentquotefeed($tickersymbol,1);
					}
					//****$currentOptionvalarr[$key]=$read[1];
					$thiskeyval=0;
					$thiskeyval=$allstockids['1'][$key]*$read[1];

					$totaloptcurrentvalue=$totaloptcurrentvalue+$thiskeyval;
					$totalOptoriginalcost=$totalOptoriginalcost+($allstockids['1'][$key]*$unitpriceofoptioninhand[$key]);
				}
				//echo "<br>Total Option Original cost===== ".$totalOptoriginalcost;
				//echo "<br>Total Option currnt value====== ".$totaloptcurrentvalue;

				$totalOptgainorloss=$totaloptcurrentvalue-$totalOptoriginalcost;
				//echo "<br>Option totalgainorloss---- ".$totalOptgainorloss;
				$totalOptavgreturnpercnt=($totalOptgainorloss/$totalOptoriginalcost)*100;
				//echo "<br>Option totalOptavgreturnpercnt---- ".$totalOptavgreturnpercnt;
			}

		}

		/*****OPTION END ******/

		$getAllQuotescurntvalarr['oc']=$cashget; // changed as on 12022008 on request // originalcost

		$getAllQuotescurntvalarr['cqv']=$totalcurrentvalue; // current quote value


		$getAllQuotescurntvalarr['avg']=$totalavgreturnpercnt; // avg. price of all quotes
		return $getAllQuotescurntvalarr;
}

public function getOriginalcost()
{
	$sql_chk="select cash_amt as tot,id from ss_cash where status='1'";
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

public function updatetranstabl($creatdateconv,$updatedon,$updatedshares,$unitprice,$description,$status,$idtupdate){
				 $tablename='ss_transaction';
				 $req_update=array();
				 $req_update['creation_date']=$creatdateconv;
				 $req_update['updation_date']=$updatedon;
				 $req_update['quantity']=$updatedshares;
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

			 public function updatelottabl($upqtid){
				 $totinhandqty=$this->getparticularstocksinh($upqtid);
				 $avg_unit_price=0;
				 $avg_unit_price=$this->avgunitpriceofstockinhand($upqtid);
				# Update tab-2
				 $transactiondata_up=array(
										   quantity=>$totinhandqty,
										   avg_unit_price=>$avg_unit_price,
										   recent_trade_date=>$datetime
					 ); // as we are not keeping any track for last updated so its default for datetime
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

			 public function updateselltabl($autoids,$transtype){
				 $updatedon=date('Y-m-d H:i:s');
				 $selltransactiondata_up=array(status=>0,updation_date=>$updatedon);

				if($transtype==1){ // while its sell
				$retstat=0;
				$retstat=update_query("ss_sell_transaction",$selltransactiondata_up,array(sell_trans_id=>$autoids));
				return $retstat;
				}else if($transtype==3){
					$retstat=0;
					$retstat=update_query("ss_sell_transaction",$selltransactiondata_up,array(buy_trans_id=>$autoids));
					return $retstat;
				}else if($transtype==2){
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
					}
						$memCacheDelete = new memCacheObj();
						$key="ssOpenPositiontrade";
						$memCacheDelete->deleteKey($key);
						$key="ssOpenPosition";
						$memCacheDelete->deleteKey($key);
						$key="ssDisplayPerformance";
						$memCacheDelete->deleteKey($key);
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

# This function calculates the total loss/gain of the short sell+btc
function getgainlossfrmssbtc()
{
			 $totgainloss=0;
			 $totgainlossd=0;
			 $isbtcdone=0;

			 $sqlallsellids="select qt.entity_type,qt.id,qt.quote_id,ES.stocksymbol,qt.unit_price,qt.quantity from ss_transaction qt,ex_stock ES where qt.transaction_type='2' and qt.status='1' and qt.quote_id=ES.id and entity_type='0' UNION ALL select qt.entity_type,qt.id,qt.quote_id,ES.optionticker stocksymbol,qt.unit_price,qt.quantity from ss_transaction qt,ex_option ES where qt.transaction_type='2' and qt.status='1' and (qt.quote_id=ES.id) and entity_type='1'";
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
					 $sqlchkbtc="select qt.entity_type,qt.id,qt.unit_price,qt.quantity from ss_sell_transaction qst, ss_transaction qt where qst.sell_trans_id='$transellid' and qst.status='1' and qst.trans_status!='pending' and qt.id=qst.buy_trans_id";
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
	if(isset($inceptiondate))
	{
					 if($exchangename=='S&P'){$exchangename='5EGSPC';}else if($exchangename=='nasdaq'){$exchangename='5EIXIC';}
					 $tickersymbol=$exchangename;
					 if (isset($tickersymbol))
					 {
						 if(isset($fileforlatest)){unset($fileforlatest);}
						 $fileforlatest="http://download.finance.yahoo.com/d/quotes.csv?s=%$tickersymbol&f=sl1d1t1c1ohgvn&e=.csv";
						 $openlatest = file($fileforlatest);
						 if(isset($currentsymvalarrlatest)){unset($currentsymvalarrlatest);}
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
						 //echo "<br>Inception date value : ".$inceptionDateValue." Current date Value : ".$currentopenpostionval;
						 $growthinpercnt=(($currentopenpostionval - $inceptionDateValue)/ $inceptionDateValue)*100;
						 return number_format($growthinpercnt, 2, '.', ',');
					 }
					 else
					 {
						 return '0.00';
					 }
}
}

# This function returns the totalcurrent value of balance stocks ( those in hand & transaction_type=0 )
public function totalcurrentvalueofallstocksbuy()
{
		 $stockinhandarr=$this->getallbuystocks();
		 /*
		 Array([0] => Array([1] => 1000) [1] => Array([3] => 3000))
		*/
		 $totalcurrentvalue=0;
		 //*** old one $qryforallbuytran="select QT.quote_id,QT.transaction_type,ES.stocksymbol,QT.unit_price,QT.quantity from ss_transaction QT, ex_stock ES where  QT.quote_id=ES.id and (QT.transaction_type='0') and QT.status='1' group by QT.quote_id,QT.transaction_type order by QT.transaction_type,ES.companyname,QT.creation_date";
		 //for details $qryforallbuytran="select QT.entity_type,QT.quote_id,QT.transaction_type,ES.stocksymbol,QT.unit_price,QT.quantity from ss_transaction QT, ex_stock ES where QT.quote_id=ES.id and (QT.transaction_type='0') and QT.status='1' and entity_type='0' group by QT.quote_id,QT.transaction_type UNION ALL select QT.entity_type,QT.quote_id,QT.transaction_type,ES.optionticker stocksymbol,QT.unit_price,QT.quantity from ss_transaction QT, ex_option ES where QT.quote_id=ES.id and (QT.transaction_type='0') and QT.status='1' and entity_type='1'group by QT.quote_id,QT.transaction_type order by transaction_type";
		 $qryforallbuytran="select QT.entity_type,QT.quote_id,ES.stocksymbol from ss_transaction QT, ex_stock ES where QT.quote_id=ES.id and (QT.transaction_type='0') and QT.status='1' and entity_type='0' group by QT.quote_id UNION ALL select QT.entity_type,QT.quote_id,ES.optionticker stocksymbol from ss_transaction QT, ex_option ES where QT.quote_id=ES.id and (QT.transaction_type='0') and QT.status='1' and entity_type='1'group by QT.quote_id,QT.transaction_type order by quote_id";
		 //echo $qryforallbuytran;
		 $tottransarr=exec_query($qryforallbuytran);
		 if(count($tottransarr)>0){
		 foreach($tottransarr as $res)
		 {
			 $symbolname=$res['stocksymbol'];
			 $sid=$res['quote_id'];
			 $entitytype=$res['entity_type'];
			 $currentquote= $this->getcurrentquotefeed($symbolname,$entitytype);
			 //***echo '<br>'.$symbolname." -------- ".$currentquote.'------'.$stockinhandarr[$entitytype][$sid];
			 $currentval=$currentquote * $stockinhandarr[$entitytype][$sid];
			 $totalcurrentvalue=$totalcurrentvalue+$currentval;
		 }
		 return $totalcurrentvalue;
		 }else
		 {
		 return 0;
}
}

/* Working........ */
public function displayperformance()
{      global $optionSmith_memcache_expire;
	   $displayperformance=array();
	   $memCacheOpenPosition = new memCacheObj();
	   $key="ssDisplayPerformance";
	   $displayperformance = $memCacheOpenPosition->getKey($key);
	   if(!$displayperformance)
		{
			 global $ytdobj;
			 // Inception date: The date on which first transaction made

			 $inception_date=$this->getInceptionDate();
			 $getAllQuotescurntvalarr=$this->getOriginalcost();  // teturns the array that describes the original cost == initail cah entry
			 $cashinhand=$this->getcashinhand(); // Formula Cash in Hand  = (Initial same cash value -Buy Amount of Delivery)+ Sell amount of delivery
			 //echo "<br>----".$cashinhand;
			 $gainorlossfrombtcss=""; // to be confirmed from Gaurav
			 //echo "<br>~~~~gainorlossamt ".$gainorlossamt.'<br>';
			 $orgcost=$getAllQuotescurntvalarr['oc'];
			 $curntval=$this->totalcurrentvalueofallstocksbuy(); // return current value of all stocks+options that buy only
			 $currentvalueofportfolio=$curntval+$cashinhand+$gainorlossamt;

			 if($orgcost>0)
			 {
				 $totavgreturn=number_format(((($currentvalueofportfolio-$orgcost)/$orgcost)*100), 2, '.', ',');
			 }
			 $snppencnt=$this->getexchangevaldetails("S&P",$inception_date[date]); // return the s&p growth
			 $nasdaqpercnt=0;/////********* $this->getexchangevaldetails("nasdaq",$inception_date[date]);
			 if($inception_date[date]!='')
			 {
					 $dispinception_date=$this->formatdate2Local($inception_date[date]);
			 }else
			 {
					 $dispinception_date="";
			 }
			 $cashinhanddispl=$cashinhand+$gainorlossamt;
			 $displayperformance['cashinhand']=number_format(($cashinhand+$gainorlossamt), 2,'.',',');
			 $displayperformance['oldcashinhand']=$cashinhand+$gainorlossamt;
                         $displayperformance['actualgainorlossfrmbtc']=$gainorlossamt;
			 //echo "Cash In Hand: ".$cashinhand." Gain or loss from BTC : ".$gainorlossamt
			 // Formula : Market Value of Securities = current value of portfolio
			 // Formula ( changed ): 29022008
			 // $curntval=$this->totalcurrentvalueofallstocksbuy();

			 $totvalueofflex=($curntval);//+($this->getgainlossfrmssbtc());  // confirmed by Rupinder 26022009 12.22PM
			 $marketvalueofsecurities=number_format(($totvalueofflex), 2,'.',',');

			 $displayperformance['marketvalueofsecurities']=$marketvalueofsecurities;
			 $totvalofporto=0;
			 $totvalofporto=number_format(($cashinhanddispl+$totvalueofflex), 2,'.',',');
			 $displayperformance['totvalofporto']=$totvalofporto;
			 $gainorlossamt=number_format($gainorlossamt,2,'.',',');
			 $displayperformance['initialcashentry']=$orgcost;
			 $orgcost=number_format($orgcost, 2, '.', ',');
			 $displayperformance['orgcost']=$orgcost;
			 $currentvalueofportfolio=number_format($currentvalueofportfolio, 2, '.', ',');
			 $displayperformance['currentvalueofportfolio']=$currentvalueofportfolio;

			 if(Date('Y')>='2010')
			 {
				 ///**** $ytdret=number_format($ytdobj->ytdreturnpercnt(), 2, '.', ',');
				 $displayperformance['ytdret']=$ytdret;
			}
			 $totavgreturn=number_format($totavgreturn, 2, '.', ',');
			 $displayperformance['totavgreturn']=$totavgreturn;
			 $snppencnt=number_format($snppencnt, 2, '.', ',');
			 $displayperformance['snppencnt']=$snppencnt;
			 $nasdaqpercnt=number_format($nasdaqpercnt, 2, '.', ',');
			 $displayperformance['nasdaqpercnt']=$nasdaqpercnt;
			 $displayperformance['dispinception_date']=$dispinception_date;
			 $displayperformance['gainorlossfrombtc_ss']=$gainorlossfrombtcss;
			 $memCacheOpenPosition->setKey($key,$displayperformance,$optionSmith_memcache_expire);

			}
		return $displayperformance;
}

public function datadisplayperformance_home($islogin,$home="")
{
	 //global $ytdobj;
 	 $this->calculatePerformance();
?>
<div class="right_common_head"><h2>optionsmith performance</h2></div>
<div>
<table class="quint_sub_heading" align="right" width="95%" border="0" cellspacing="5" cellpadding="0">
<tr>
<td>Current Value of OptionSmith</td>
<td>$ <?=$this->curVal; ?></td>
</tr>
  <tr>
    <td>OptionSmith Total Average Return</td>
    <td><?=$this->TotAvgReturn;?> %</td>
  </tr>
  <tr>
  <tr>
      <td>2010 Return</td>
      <td>39.47 %</td>
  </tr>
   <tr>
      <td>2011 Return</td>
      <td>27.57 %</td>
  </tr>
  <tr>
      <td>2012 Return</td>
      <td>14.65 %</td>
  </tr>
  <tr>
      <td><?=date("Y");?> YTD  Return</td>
      <td><?=$this->YTDAvgReturn;?> %</td>
  </tr>
  <tr>
    <td colspan="2" class="quint_small_heading">Since Inception <?=$this->inceptionDate;?></td>
  </tr>
</table>
</div>
<? }
/*******************deepika***/
public function datadisplayperformance($islogin,$home="")
{
		$strdisp='<table width="100%" border="0" cellpadding="0" cellspacing="0" align="left">';
		if($home!=1)
		{
			$performance=$this->calculatePerformance();
			$strdisp.='<tr>	<td  valign="top" width="310">
			<div style="border:solid 1px #cccccc; margin-bottom:10px; width:310px;">
			<div class="right_common_head">
			<h2>current value of optionsmith</h2>
			</div><table width="309" class="market_heading" border="0" cellpadding="0" cellspacing="0">
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
			</div> ';
		}
	return $strdisp;
}

public function displayoptionperformence($islogin,$home="")
{
		global $ytdobj;
		$this->calculatePerformance();
		$dataperformance=$this->performanceData;//$this->displayperformance($islogin);
		$strdisp.='<div class="right_common_container_flexfolio">
		<div class="right_common_head">
		<h2>optionsmith performance</h2>
		</div>
		<table width="100%" class="market_heading" border="0" cellpadding="0" cellspacing="0">';

		if($islogin)
		{
			$strdisp.='<tr><td>Current Value of OptionSmith </td>
			<td >$ ';
			$strdisp.=$this->curVal.'</td>
			</tr>';
		}
		$strdisp.='<tr class="market_grey_row">
		<td>OptionSmith Total Average Return </td>
		<td>';
		$strdisp.=$this->TotAvgReturn.'%</td>
		</tr>
		<tr class="$dynamicclass">
				<td>2010 Return </td>
				<td>39.47 %</td>
		</tr>
		<tr class="market_grey_row">
				<td>2011 Return </td>
				<td>27.57 %</td>
		</tr>
		<tr class="$dynamicclass">
				<td>2012 Return </td>
				<td>14.65 %</td>
		</tr>
		</tr>
		<tr class="market_grey_row">
				<td>'.date("Y").' YTD Average Return </td>
				<td>'.$this->YTDAvgReturn.' %</td>
		</tr>
		<tr>
		<td colspan="2" ><div class="divider_container">
		*since inception '.$this->inceptionDate.'
		</div>  </td>
		</tr>
		</table>
		</td>
		</tr></table></div>';
		return $strdisp;
	}

		public function displayperformanceforhome($islogin)
		{
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
						 <td class="quintsubhedingname1">Current Value of OptionSmith</td><td class="quintsubhedingname1"> $ ';
						 $currentvalueofportfolio=number_format($currentvalueofportfolio, 2, '.', ',');
						 $strdisp.=$currentvalueofportfolio.'</td>
						 </tr>';
						 if(Date('Y')>='2010')
						 {
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
						 <td class="quintsubhedingname1">OptionSmith Total Average Return </td>
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
	$sql="select unit_price,id,entity_type,quote_id from ss_transaction where transaction_type='2' and status='1'";
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

			//echo "<br>id: $transellid ------- Entity: $transentity --------- Quote id: $quote_id --------- $balanceQty-------$unit_price";
			//echo "<br>id: $transellid ------- Entity: $transentity --------- Quote Name: $symbolName --------- $balanceQty -------$unit_price ---------- $currentValueget";

			$finalBalanceGainLoss=$finalBalanceGainLoss+($unit_price-$currentValueget)*$balanceQty;
			// id: 1 ------- Entity: 1 --------- Quote id: 3 --------- 500
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
		//$qry_transaction="select quote_id,transaction_type,unit_price,quantity from ss_transaction  where entity_type='$entitytype' and status='1' and id='$transid' union ALL select QT.quote_id,QT.transaction_type,QT.unit_price,QT.quantity  from ss_transaction QT,ss_sell_transaction QST where QT.entity_type='$entitytype' and QST.sell_trans_id='$transid' and QST.status='1' and QST.buy_trans_id!=0 and QST.buy_trans_id=QT.id";
		$qry_transaction="select transaction_type,quantity from ss_transaction where entity_type='$entitytype' and status='1' and id='$transid' union ALL select QT.transaction_type,QT.quantity  from ss_transaction QT,ss_sell_transaction QST where QT.entity_type='$entitytype' and QST.sell_trans_id='$transid' and QST.status='1' and QST.buy_trans_id!=0 and QST.buy_trans_id=QT.id";
		//echo "<br>$transid -------- $entitytype -----------";
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
	//echo "<br>".$dataperformance['oldcashinhand']." ----- $getBalanceCurrentValue ----- $this->finalGainLossFromBalancedSS";
	//$newCashEntry = ($dataperformance['oldcashinhand']-$getBalanceCurrentValue)-($gainlossfrombtc);
	$newCashEntry = ($dataperformance['oldcashinhand']-$getBalanceCurrentValue)-($this->finalGainLossFromBalancedSS);
	return $newCashEntry;//$dataperformance['newCashInHand']=$newCashEntry;
}








		public function getOpenShortPosProfit(){
			$qryGetOpenShortPosOption="select unit_price,EO.optionticker, (quantity-sold_quantity) as openpos from ss_openpositions SOP,ex_option EO  where EO.id=SOP.quote_id and transaction_type='2' and entity_type='1'";
			$resGetOpenShortPosOption=exec_query($qryGetOpenShortPosOption);
			if($resGetOpenShortPosOption)
			{
				foreach($resGetOpenShortPosOption as $value)
				{
					$optionsymbols[]=$value['optionticker'];
				}
				$currValOptionStocks=$this->getCurrentStockValue($optionsymbols);
				$totValOpenShortPosOption=0;
				foreach($resGetOpenShortPosOption as $value)
				{
					$profitOpenShortPosOption+=($value['unit_price']-$currValOptionStocks[$value['optionticker'].".X"])*$value['openpos'];

				}
			}
			$qryGetOpenShortPosStock="select unit_price,ES.stocksymbol, (quantity-sold_quantity) as openpos
									  from ss_openpositions SOP,ex_stock ES
									where ES.id=SOP.quote_id and transaction_type='2' and entity_type='0'";
			$resGetOpenShortPosStock=exec_query($qryGetOpenShortPosStock);
			if($resGetOpenShortPosStock)
			{
				foreach($resGetOpenShortPosStock as $value)
				{
					$stocksymbols[]=$value['stocksymbol'];
				}
				$currValStocks=$this->getCurrentStockValue($stocksymbols,1);

				$profitOpenShortPosStock=0;
				foreach($resGetOpenShortPosStock as $value)
				{
					$profitOpenShortPosStock+=($value['unit_price']-$currValStocks[$value['stocksymbol']])*$value['openpos'];
				}
			}

			$profitOpenShortPos=$profitOpenShortPosStock+$profitOpenShortPosOption;
			return $profitOpenShortPos;
		}


		public function getcashinhand()
		{
					 //Cash IN Hand = Original Cost-Total Purchase amount of Open Positions+Profit/loss from Closed Positions+Gain/loss from Short selling
					 $originalCost=$this->getOrginalCost();
					 $profitClosePosition=$this->getClosePositionAmount();

					 $sqlOpenBuyAmmount="select sum(unit_price*(quantity-sold_quantity)) as buyammount from ss_openpositions where transaction_type='0'";
					 $resOpenBuyAmmount=exec_query($sqlOpenBuyAmmount,1);
					 $totOpenBuyAmmount = $resOpenBuyAmmount['buyammount'];
					 $openShortPosProfit=$this->getOpenShortPosProfit();
					 //echo $originalCost."<br />".$totOpenBuyAmmount."<br />".$profitClosePosition."<br />".$openShortPosProfit;
					 $totcashinhand=$originalCost-$totOpenBuyAmmount+$profitClosePosition+$openShortPosProfit+(-$originalCost);
					 return $totcashinhand;
		 }


		function getOrginalCost()
		{
			$sqlGetOriginalCost="SELECT SUM(cash_amt) originalcost,substring((min(creation_date)),1,10) as date FROM ss_cash WHERE STATUS ='1'";
			$resGetOriginalCost=exec_query($sqlGetOriginalCost,1);
			return $resGetOriginalCost['originalcost'];
		}

		public function getMarketValOfSecurities(){
			$objPortfolio= new optionPortfolio();
			$openPositions=$objPortfolio->getOpenPositionsByTrade();
			$marketValOfSecurities=0;
			foreach($openPositions as $openpos){
				$marketValOfSecurities+=$openpos['currentValue'];
			}
			return $marketValOfSecurities;
		}


		public function getCurrentPortfolioValue()
		{
			$qryGetOpenPositions="SELECT transaction_id,transaction_type,SUM(quantity) totalbuy,
SUM(sold_quantity) totalsold,EO.optionticker
FROM ss_openpositions SOP, ex_option EO
WHERE SOP.quote_id=EO.id GROUP BY SOP.quote_id,transaction_type";
			$resGetOpenPositions=exec_query($qryGetOpenPositions);
			foreach($resGetOpenPositions as $stockDetails)
			{

				$socksymbol[] = strtoupper($stockDetails['optionticker']);
				$toalInHand[] = $stockDetails['totalbuy'] - $stockDetails['totalsold'];
				$tranctionType[] = $stockDetails['transaction_type'];
			}
			$currentOpenStocks=$this->getCurrentStockValue($socksymbol);
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
		SELECT ST.id,quote_id,(unit_price*quantity) AS sold_amount,quantity,(SELECT unit_price*ST.quantity  FROM ss_transaction WHERE id = SST.buy_trans_id) AS buy_amount
			FROM ss_transaction AS ST, ss_sell_transaction AS SST
		WHERE ST.id = SST.sell_trans_id AND (ST.transaction_type='1') AND ST.status='1'
			UNION
		SELECT ST.id,quote_id,(SELECT unit_price*ST.quantity FROM ss_transaction WHERE id = SST.sell_trans_id) AS sold_amount,quantity,(unit_price*quantity) AS buy_amount
		FROM ss_transaction AS ST, ss_sell_transaction AS SST
		WHERE ST.id = SST.buy_trans_id AND (ST.transaction_type='3') AND ST.status='1'
			ORDER BY quote_id) AS ss";
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
				if($getStockData){
					if(sizeof($stocksymbols)>1){
						foreach($getStockData['Quote']['Record'] as $stockDetails)
						{
							$currentStocksValue[$stockDetails['Ticker']]=$stockDetails['Last'];
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
		$sqlGetInceptionDate="select substring((min(creation_date)),1,10) as inceptiondate from ss_transaction where status='1'";
		$resGetInceptionDate=exec_query($sqlGetInceptionDate,1);
		return $resGetInceptionDate['inceptiondate'];
	}

	public function calculatePerformance()
	{
		global $optionSmith_memcache_expire,$optionPortfolioInception2010;
		$memCacheOpenPosition = new memCacheObj();
		$key="ssDisplayPerformance";
	    $displayperformance = $memCacheOpenPosition->getKey($key);
		if($displayperformance){
			$this->inceptionDate=$displayperformance->inceptionDate;
			$this->originalCost=$displayperformance->originalCost;
			$this->cashinhand=$displayperformance->cashinhand;
			$this->marketValOfSecurities=$displayperformance->marketValOfSecurities;
			$this->curVal=$displayperformance->curVal;
			$this->TotAvgReturn=$displayperformance->TotAvgReturn;
			$this->YTDAvgReturn=$displayperformance->YTDAvgReturn;
		}else{
			$this->inceptionDate=$this->getInceptionDate();
			$this->originalCost=$this->getOrginalCost();
			$this->cashinhand=$this->getcashinhand();
			$this->marketValOfSecurities=$this->getMarketValOfSecurities();
			$marketValOfSecurities = $this->marketValOfSecurities;
			$this->curVal=$this->marketValOfSecurities+$this->cashinhand;
			$this->TotAvgReturn=round((($this->curVal)/$this->originalCost)*100,2);
			$this->YTDAvgReturn=round((($this->curVal-$optionPortfolioInception2010)/$optionPortfolioInception2010)*100,2);

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
			if($marketValOfSecurities=='0'){
				//do nothing
			}else{
				$memCacheOpenPosition->setKey($key,$this,$optionSmith_memcache_expire);
			}
		}
	}
	/*Display Performance on New Landing Page*/
	public function datadisplayperformance_newLanding($islogin,$home="")
	{
		 $this->calculatePerformance();
	?>
	<div class="perfContent">
		<div class="perfBold">Original cost</div>
		<div class="perfCount">$ <?=$this->originalCost;?></div>
	</div>

	<div class="perfContent">
		<div class="perfBold">Current Value of OptionSmith</div>
		<div class="perfCount">$ <?=$this->curVal; ?></div>
	</div>

	<div class="perfContent">
		<div class="perfBold">OptionSmith Total Average Return</div>
		<div class="perfCount"><?=$this->TotAvgReturn;?> %</div>
	</div>
	<div class="perfContent">
		<div class="perfBold">2010 Return</div>
		<div class="perfCount">39.47 %</div>
	</div>
	<!--<div class="perfContent">
		<div class="perfBold"><?=date("Y");?> YTD  Return</div>
		<div class="perfCount"><?=$this->YTDAvgReturn;?> %</div>
	</div>-->

	<div class="perfContentDate">Data gathered since Inception <?=$this->inceptionDate;?></div>

	<? }

}
?>