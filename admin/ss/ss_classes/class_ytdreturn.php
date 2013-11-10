<?
class YTDRET{
	function getfirstTransDate(){
	$curyear=date('Y');
	// not checking any kind of transaction_type (buy/sell/ss/btc)
	$qryfirsttrans="select creation_date from qp_transaction where creation_date>='$curyear-01-01 00:00:00' and status=1 LIMIT 1";
	//echo $qryfirsttrans;
	$firstdata=exec_query($qryfirsttrans);
	if(count($firstdata)>0){
		foreach($firstdata as $allresultss){
			$dateget=$allresultss['creation_date'];
		}
	}
	return $dateget;
}	

// only for buy / sell
function getcashinhandytd(){
		global $transobj;
		global $initialcashamtget;
	// (initial_cash_entry-buyamttotal)+sellamttotal;
	$dates=$this->getfirstTransDate();
	if(isset($dates)){
		$append=" and creation_date>='$dates'";
	}else{
		$append='';
	}
					 $initialcashqry="select cash_amt from qp_cash where status='1'";// doubt as everytime we are fetching the same value
					 $initialcashamt=0;
					 $totbuycash=0;
					 $totsellcash=0;
					 $allresintcash=exec_query($initialcashqry);
					 if(count($allresintcash)>0){
						 $initialcashamt=exec_query($initialcashqry,1);
						 $initialcashamtget=$initialcashamt['cash_amt'];
						 
					 }

					 $sql_totbuyamtofbuytrans="select sum(quantity*unit_price) as totbuyamt from qp_transaction where status='1' and (transaction_type='0') $append";
					 //echo "<br>".$sql_totbuyamtofbuytrans;
					 $allresbuy=exec_query($sql_totbuyamtofalltrans);
					 if(count($allresbuy)>0){
					 $totbuycash=exec_query($sql_totbuyamtofalltrans,1);
					 }
					 
					 $sql_totsellamtofselltrans="select sum(quantity*unit_price) as totsell from qp_transaction where status='1' and (transaction_type='1') $append";
					 //echo $sql_totsellamtofselltrans;
					 $allressell=exec_query($sql_totsellamtofalltrans);
					 if(count($allressell)>0){
					 $totsellcash=exec_query($sql_totsellamtofalltrans,1);
					 }
					 $totcashinhand=($initialcashamt['cash_amt']-$totbuycash['totbuyamt'])+$totsellcash['totsell'];
					 return $totcashinhand;

			 }

# This function calculates the total loss/gain of the short sell / btc
function getgainlossfrmssbtc(){
		global $transobj;
	$dates=$this->getfirstTransDate();
	if(isset($dates)){
		$append=" and creation_date>='$dates'";
	}else{
		$append='';
	}
	$totgainloss=0;
	$totgainlossd=0;
	$isbtcdone=0;

	$sqlallsellids="select qt.id,qt.quote_id,ES.stocksymbol,qt.unit_price,qt.quantity from qp_transaction qt,ex_stock ES where qt.transaction_type='2' and qt.status='1' and qt.quote_id=ES.id $append";
	$allressell=exec_query($sqlallsellids);
	if(count($allressell)>0){//start
		foreach($allressell as $allselltransres){
			$transellid=$allselltransres['id']; // auto id of transaction table
			$transellidup=$allselltransres['unit_price']; // unit price of each ss
			$transellidqty=$allselltransres['quantity']; // total ss qty
			$transellquote_id=$allselltransres['quote_id'];// symbol id
			$transellsymbol=$allselltransres['stocksymbol']; // symbol name required for yahoo fetch

# check the btc quantity against this ss
			$sqlchkbtc="select qt.id,qt.unit_price,qt.quantity from qp_sell_transaction qst, qp_transaction qt where qst.sell_trans_id='$transellid' and qst.status='1' and qst.trans_status!='pending' and qt.id=qst.buy_trans_id $append";
			$allresbtc=exec_query($sqlchkbtc);

			if(count($allresbtc)>0){// agar mila
			 //echo "<br>~~~~~~~~~~~~~~~~$totgainlossd";
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
				$restsharescurntval=$restsharesqty*($transobj->getcurrentquote($transellsymbol));

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
				$currentquote= $transobj->getcurrentquote($transellsymbol);
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

# This function returns the totalcurrent value of balance stocks ( those in hand & transaction_type=0 )
function totalcurrentvalueofallstocksbuyytd(){
	global $transobj;
	$dates=$this->getfirstTransDate();
	if(isset($dates)){
		$append=" and QT.creation_date>='$dates'";
	}else{
		$append='';
	}
	
	$stockinhandarr=$transobj->getallbuystocks($dates);
	$totalcurrentvalue=0;
	$qryforallbuytran="select QT.quote_id,QT.transaction_type,ES.stocksymbol,QT.unit_price,QT.quantity from qp_transaction QT, ex_stock ES where  QT.quote_id=ES.id and (QT.transaction_type='0') and QT.status='1' group by QT.quote_id,QT.transaction_type order by QT.transaction_type,ES.companyname,QT.creation_date $append";
	$tottransarr=exec_query($qryforallbuytran);

	if(count($tottransarr)>0){
		foreach($tottransarr as $res){
			$symbolname=$res['stocksymbol'];
			$sid=$res['quote_id'];
			$currentquote= $transobj->getcurrentquote($symbolname);
			$currentval=$currentquote * $stockinhandarr[$sid];
			$totalcurrentvalue=$totalcurrentvalue+$currentval;
		}

		return $totalcurrentvalue;
	}else{
		return 0;
	}
	

}


function ytdreturnpercnt(){
	global $transobj;
	global $initialcashamtget;
	
	$balsharescurval=$this->totalcurrentvalueofallstocksbuyytd();
	$cashinhand=$this->getcashinhandytd();
	$lossgainfrmss=$this->getgainlossfrmssbtc();

	/*echo "<br>**********".$balsharescurval;
	echo "<br>**********".$cashinhand;
	echo "<br>**********".$lossgainfrmss;
	echo "<br>**********".$initialcashamtget;
	*/
	$curntvalofportfolio=$balsharescurval+$cashinhand+$lossgainfrmss;
	if($initialcashamtget>0){
		$ytdtobereturn=(($curntvalofportfolio-$initialcashamtget)/$initialcashamtget)*(100);
	}
	return $ytdtobereturn;
}
}
?>
