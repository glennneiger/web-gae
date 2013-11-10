<?php
global $D_R;
include_once("$D_R/lib/_includes.php");
include_once("$D_R/lib/_db.php");
include_once("$D_R/lib/techstrat/_techstratData.php");
$objTechStartData= new techstartData("techstrat_posts");

$q = strtolower( $_GET["q"] );
if (!$q) return;

/*$dbhost = "localhost";		// Database Host
$dbuser = "root";			// User
$dbpass = "Test";			// Password
$dbname = "minyanville_stage";			// Name of Database

mysql_connect( $dbhost, $dbuser, $dbpass ) or die( mysql_error() );
mysql_select_db( $dbname ) or die( mysql_error() );*/

$queryString=$_GET['q'];

$data=explode(",",$queryString);
$countval=count($data);
$queryString=$data[$countval-1];

switch($_GET['type']){
	case 'ticker':
		$query="select stocksymbol,exchange from ex_stock WHERE stocksymbol LIKE '$queryString%' limit 5";
		$result=exec_query($query);
		if($result){
			foreach($result as $row){
				echo $row['stocksymbol'] . " - " . $row['exchange'] . "\n";
			}
		}
	break;

	case 'topic':
		$query="SELECT distinct(xt.tag)  as tag FROM ex_item_tags xbt, ex_tags xt where xt.id = xbt.tag_id and xbt.item_type ='".$objTechStartData->contentType."' and xt.tag LIKE '$queryString%' order by xt.tag asc limit 5";
		$result=exec_query($query);
		if($result){
			foreach($result as $row){
				echo $row['tag']. "\n";
			}
		}
	break;

	case 'tickerPortfolio':
		$query="select stocksymbol,exchange,companyname from ex_stock WHERE stocksymbol LIKE '$queryString%'  AND is_active='1' GROUP BY stocksymbol limit 8";
  		$result=exec_query($query);
		if(!empty($result)){
			foreach($result as $row){
				echo strtoupper($row['exchange']). ":" .strtoupper($row['stocksymbol']). "\n";
			}
		}
  	break;
}
?>