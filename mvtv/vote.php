 <?
include_once("../lib/_module_data_lib.php");
include_once("../lib/_module_design_lib.php");
include_once("../lib/_redesign_design_lib.php");
include_once("../lib/_constants.php");

$pageAction = $_POST['action'];
$object_name=$_POST['object_name'];
if($pageAction != '')
{
	switch($pageAction)
	{

		case "rate":
			if ($object_name=='video')
			{
				echo voteCastAV($_POST['item_id'],$_POST['rating_type'],$_POST['voter_id'],$_POST['object_id'],$object_name);
			}
			elseif($object_name=='story')
			{
				echo voteCastDailyFeed($_POST['item_id'],$_POST['rating_type'],$_POST['voter_id'],$_POST['object_id'],$object_name);
			}
			else
			{
				echo voteCast($_POST['item_id'],$_POST['rating_type'],$_POST['voter_id'],$_POST['object_id'],$object_name);
			}
		break;

	}
}
?>

