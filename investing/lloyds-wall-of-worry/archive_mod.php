<?
include_once("$D_R/lib/lloyds-wall-of-worry/_worry_data_lib.php");
$objWorryData = new worryData();

switch($_POST['type']){
	case "openArchive" :
		$archive = $objWorryData->getArchive();
		foreach($archive as $key=>$value){
		
			if(strtolower($value['month'])==strtolower($_POST['month'])){
				$allArchive = $objWorryData->getArchivesByMonth($_POST['month'],$_POST['year']);
			?>
			<div class="month-name-visible"  onclick="closeArchive();">
					<img src="<?=$IMG_SERVER?>/images/llyods-wall-of-worry/arrowdown.gif" width="9px" height="8px" />
					<span><?=$_POST['month']?>,<?=$_POST['year']?></span>
				</div> 
			<?						
			foreach($allArchive as $worry){
			?>		
			<div class="archives" onclick="displayArchiveWry('<?=$worry['0']['publish_date']?>');">
			<? 
				  foreach($worry as $key=>$wry){
						$i = $key+1;  
						if($i%4 == 0){
			?>
				<img id="archive-fourth-img" src="<?=$IMG_SERVER?>/assets/lloyds-wall-of-worry/<?=$wry['archive_img']?>" width="50px" height="42px" />
				<?
						}else { 
				?>
				<img src="<?=$IMG_SERVER?>/assets/lloyds-wall-of-worry/<?=$wry['archive_img']?>" width="50px" height="42px" />
			<?
						}
					}
			?>		
				<div style="clear:both;"></div>
				<div class="month-detail"><?=date("F d, Y",strtotime($worry['0']['publish_date']))?></div>
				</div>
				<?
				}				
		}else{
		?>
			<div style="clear:both;"></div><div class="month-name-hidden" onclick="openArchive('<?=$value['month']?>','<?=$value['year']?>');">
				<img src="<?=$IMG_SERVER?>/images/llyods-wall-of-worry/arrowrght.gif" width="8px" height="9px" />
				<span><?=$value['month']?>,<?=$value['year']?></span>				
			</div>	
			<?	 }			
		}
		break;
		
	case "getArchive":
		$date = $_POST['date'];
		$wryByDate = $objWorryData->getWorryByDate($date);
		$totalWorries = count($wryByDate); ?>
		<div class="wall-wry-box" id="archive-left-wry">
				<div class="click-here">click on a date to the right to see previous posts<br>
				you are currently viewing <span><?=date("F d,Y",strtotime($date))?> / <?=$totalWorries?> worries</span></div>
				<div style="clear:both;"></div>

		<?	foreach ($wryByDate as $key=>$val){
		       $val['worry_summary']=htmlentities($val['worry_summary'],ENT_QUOTES);
				$i = $key+1;  
				if($key=="2" || $key==$chkThirdWall+4){
					   $chkThirdWall=$key;
					?>
						<img id="third-img" class="tTip" name="<?=$val['title']?>" title="<?=$val['worry_summary']?>" src="<?=$IMG_SERVER?>/assets/lloyds-wall-of-worry/<?=$val['worry_img']?>" width="169px" height="144px" usemap="#mapclose" />
                     <?   
				}elseif($i%4 == 0){ ?>
					<img class="tTip" name="<?=$val['title']?>" title="<?=$val['worry_summary']?>" id="fourth-img" src="<?=$IMG_SERVER?>/assets/lloyds-wall-of-worry/<?=$val['worry_img']?>" width="169px" height="144px" />
			<?	}else { ?>
					<img class="tTip" name="<?=$val['title']?>" title="<?=$val['worry_summary']?>" src="<?=$IMG_SERVER?>/assets/lloyds-wall-of-worry/<?=$val['worry_img']?>" width="169px" height="144px" />
			<?	}
			} ?>
		</div>
		<?
		break; 
		
	case 'closeArchive':
			$archive_month = $objWorryData->getArchive();
			foreach($archive_month as $key=>$value){ ?>
			<div class="month-name-hidden" id="month-name-hidden" onclick="openArchive('<?=$value['month']?>','<?=$value['year']?>');">
				<img src="<?=$IMG_SERVER?>/images/llyods-wall-of-worry/arrowrght.gif" width="8px" height="9px" />
				<span><?=$value['month'].','.$value['year']?></span>				
			</div>
		<?		} 
		break;
		
} // switch
?>