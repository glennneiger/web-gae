<?php
class contributorView{
	function getBioDetail($contributors,$arBio)
	{	
	global $IMG_SERVER,$HTPFX,$HTHOST;
	$blockcontributoremail = array("kadlec@minyanville.com","greg@minyanville.com","jay@optionstrader.com","weldon@minyanville.com","calianos@minyanville.com","james.brumley@minyanville.com","lederman@minyanville.com","adami@minyanville.com","saral@minyanville.com","mary@minyanville.com","payne@minyanville.com","ryan@minyanville.com","nelson@minyanville.com","lmcguirk@endeavourfunds.com","kathy@minyanville.com","adami@minyanville.com","sarah@minyanville.com","sally@minyanville.com","mary@minyanville.com","ryan@minyanville.com","nelson@minyanville.com","lmcguirk@endeavourfunds.com","ryan@minyanville.com","nelson@minyanville.com","lmcguirk@endeavourfunds.com","detullio@minyanville.com","shartsis@minyanville.com","jay@optionstrader.com","kathey@minyanville.com","guy@minyanville.com","mrcents@minyanville.com","gallowayevan@gmail.com","tgordon@minyanville.com","osborne@minyanville.com");
	?>
	<!--Our professors main start from here-->
	<div id="homepage-content" class='bottomSpace'>
	<div class="breadcrum"><?php $this->biosBreadCrumDetail($arBio['name']);?></div>
	<div class="professor_main_container">
	<!--Professor left start from here-->
		<div class="professor_left">
			<div class="professor_main_title">our professors</div>
	<div id="div-bios">
	
    <div id="div-bio<?=$arBio[id]?>">
        <div class="professor_title"><?=$arBio[name]?></div>
        <div class="professor_name_block">
        <a id="bio<?=$arBio[id]?>">
        <?
        if($arBio[bio_asset])
        {
        ?>
        <div style="border:solid 1px #000; float:left;"><img src="<?=$IMG_SERVER.$arBio[bio_asset]?>" width="78" height="78" border="0" align="left"></div></a>
        <?
        }
        else
        {
        ?><div style="border:solid 1px #000; float:left;"><img src="<?=$IMG_SERVER.'/assets/bios/noimage.jpg'?>" width="78" height="78" border="0" align="left"></div></a><?}?>

        <span style="margin-top:5px;float:left;word-wrap: break-word; "><a href="<?=$HTPFX.$HTHOST?>/library/search.htm?search=1&contrib_id=<?=$arBio[id]?>">see what <?=$arBio[name]?> has written &raquo;</a></span>
        </div>
        <div > <?=$arBio[description]?> </div>
        <h5><a href="<?=$HTPFX.$HTHOST?>/library/search.htm?search=1&contrib_id=<?=$arBio[id]?>">see what <?=$arBio[name]?> has written &raquo;</a></h5>
        <div class="professor_rss"><span><a href="<?=$HTPFX.$HTHOST?>/rss/author.rss?authorid=<?=$arBio[id]?>"><img src="<?=$IMG_SERVER?>/images/navigation/rss_logo.gif" /></a></span><a href="<?=$HTPFX.$HTHOST?>/rss/author.rss?authorid=<?=$arBio[id]?>">subscribe to <?=$arBio[name]?></a></div>
    </div>
				
			</div><!-- div-bios End here-->
		</div><!--Professor left End here-->
	
		<!--Professor right start from here-->
		<div class="professor_right">
				<div class="template7_header">meet the professors</div>
				<?	foreach($contributors as $i=>$row){
	
					if(!$row[name] or (in_array($row['email'],$blockcontributoremail)))
					continue;
				?>
				<div class="professor_right_inner">
				<!--<h3><a href="#" onclick="bio(<?=$row[id]?>);return true;"><?=$row[name]?></a><br></h3>-->
					<? if ($row[id] != "215") { ?>
				<h3><a href="/gazette/bios.htm?bio=<?=$row[id]?>"><?=$row[name]?></a><br></h3>
					<? } ?>
	</div>
	<?}?>
	</div>
		<!--Professor right ends here-->
		 </div><!--Our professors main Closed here-->
	 </div>
	
	<?php
	}
	
	function getBioProfessorsLanding($contributors,$weAreTitle,$groupId)
	{
	?>
	<div id="homepage-content">
	<div class="breadcrumLanding"><?php $this->biosBreadCrumLanding($groupId);?></div>
	<!--Our professors main start from here-->
	<div class="introduction">
	<div class="bioHeader"><h1><?=$weAreTitle;?></h1></div>
	<div class="hNbImg"><img src="<?=$IMG_SERVER?>/images/hNbSilhouette.gif" width="81" height="49" border="0" alt="Hoofy &amp; Boo" title="Hoofy &amp; Boo"/></div>
	<div class="wwa-desc">Minyanville and its contributors create branded business content that informs, entertains and educates all generations about the worlds of business and finance. Minyanville is a place where people who seek useful, unbiased information come to learn, laugh and connect.</div>
	</div>
	<div class='introduction'>
	<a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?type=professors" title="Sort by Professors" class="anchorLink"><div class="professorBox">P</div></a><div class="bioCategory"><a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?type=professors" title="Sort by Professors"><img src="<?=$IMG_SERVER?>/images/professors.png" border="0" alt="Minyanville Professors" /></a>
	<div class='groupDesc'>Professors are active professionals in financial markets. They are full-time traders and investors, not full time writers, who offer intraday observations, thoughts, and ideas as the markets move.</div></div>
	
	<a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?type=contributors" title="Sort by Contributors" class="anchorLink"><div class="contributorBox">C</div></a><div class="bioCategory"><a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?type=contributors" title="Sort by Contributors"><img src="<?=$IMG_SERVER?>/images/contributors.png" border="0" alt="Minyanville Contributors" /></a><div class='groupDesc'>Contributors are people within the Minyanville community who write for the site. They provide food for thought and opinion, and may be professional writers, freelancers, or are uniquely positioned to give a "boots on the ground" perspective on a particular issue.</div></div>
	
	<a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?type=buzz" title="Sort by Buzz & Banter" class="anchorLink"><div class="buzzBox">B</div></a><div class="bioCategory"><a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?type=buzz" title="Sort by Buzz & Banter"><img src="<?=$IMG_SERVER?>/images/buzz.png" border="0" alt="Buzz & Banter  Contributors" /></a><div class='groupDesc'>Buzz and Banter contributors are active professionals in financial markets. They are full-time traders and investors who offer intraday observations, thoughts, and ideas as the markets move through Minyanville's Buzz &amp; Banter.<span class="buzzLink"><a  href="<?=$HTPFX.$HTHOST?>/buzzbanter/" title="Free Trail of Buzz & Banter" class="buzzLink">Take a free trial of Buzz & Banter</a></span></div></div>
	<a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?type=staff" title="Sort by Staff" class="anchorLink"><div class="staffBox">S</div></a>
	<div class="bioCategory"><a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?type=staff" title="Sort by Staff"><img src="<?=$IMG_SERVER?>/images/staff.png" border="0" alt="Minyanville Staff" /></a><div class='groupDesc'>Staff writers are professional writers employed by Minyanville.com.</div></div>
	</div>
	<div style="clear:both;margin-bottom:15px;"></div>
	<div class="grid-container">
		<div class="biographies">
			<?php
				global $D_R,$HTPFX,$HTHOST,$IMG_SERVER;
				foreach ($contributors as $row)
				{
					$contName		= 	$row['name'];
					$contTumbImg	=	$row['small_bio_asset'];
					$contId			=	$row['id'];
					//if(is_file("$D_R$contTumbImg")){
					if($contTumbImg != ""){
						$thumbImg	=	$IMG_SERVER.$contTumbImg;
					} else {
						$thumbImg	=	$IMG_SERVER."/assets/bios/thumb/no-photo.jpg"; }
				
					$sqlContGroup = "select g.id,g.group_name from contributor_groups g, contributor_groups_mapping gm
where g.id=gm.group_id and gm.contributor_id='".$contId."'";
					$resContGroup	= exec_query($sqlContGroup);
				?>
				<div class="bio bionmLt bionmRt">
				<div class="thmb"><a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?bio=<?=$contId;?>"><img src="<?=$thumbImg;?>" border=0 height="50px" width="50px;" title="<?=ucwords($contName);?>"></a> </div>
					<?php
					if(count($resContGroup)>0)
					{
						echo "<div class='bioFlags'>";
						foreach($resContGroup as $row)
						{
							if($row['id'] == '1')
							{
							?>
								<a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?type=professors" title="Sort by Professors"  class="anchorLink">
								<div class='mvProfessors' title="Sort by Professors"></div>
								</a>
							<?php }
							if($row['id'] == '2')
							{
							?>
								<a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?type=contributors" title="Sort by Contributors"  class="anchorLink">
								<div class='mvContributors' title="Sort by Contributors"></div>
								</a>
							<?php }
							if($row['id'] == '3')
							{
							?>
								<a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?type=staff" title="Sort by Staff"  class="anchorLink" >
								<div class='mvStaff' title="Sort by Staff"></div>
								</a>
							<?php
							}		
							if($row['id'] == '4')
							{?>
								<a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?type=buzz" title="Sort by Buzz & Banter"  class="anchorLink">
								<div class='buzzNbanter' title="Sort by Buzz & Banter"></div>
								</a>
							<?php
							}
						}
					echo "</div>";
					}
					?>
				<div class="name-tag">
				<a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?bio=<?=$contId;?>" class="name-tag">
				<p><h3><?=ucwords($contName);?></h3></p></a></div>
				</div>
				<?php 
				}
				?>
		</div>
	</div>
	<div style="clear:both;" class="bottomSpace"></div>
<!--Our professors main Closed here-->
	</div><!-- Grid Container Section End-->
	<?php
	}
	function biosBreadCrumDetail($profName)
	{
	?>
		<a href="<?=$HTPFX.$HTHOST?>/" title='Minyanville'>Minyanville</a>&nbsp;>&nbsp;
		<a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm" title='Professors'>Professors</a>&nbsp;>&nbsp;
		<span><?=$profName;?></span>
	<?php
	}
	function biosBreadCrumLanding($groupId)
	{
		if($groupId>0)
		{
			if($groupId	==	1)
				$breadcrumTitle =  'Minyanville Professors';
			
			elseif($groupId	==	2)
				$breadcrumTitle =  'Minyanville Contributors';
			
			elseif($groupId	==	3)
				$breadcrumTitle =  'Minyanville Staff';
			elseif($groupId	==	4)
				$breadcrumTitle =  'Buzz & Banter Contributors';
			else
				$breadcrumTitle = '';
		}
		else
				$breadcrumTitle = '';
		?>
		<a href="<?=$HTPFX.$HTHOST?>/" title='Minyanville'>Minyanville</a>&nbsp;>&nbsp;
		<a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm" title='Professors'>Professors</a>
		<?php
		if($breadcrumTitle!='')
		{
			echo "&nbsp;>&nbsp;<span>".$breadcrumTitle."</span>";
		}
	}
}
?>