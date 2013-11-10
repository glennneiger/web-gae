<?php
$pageName = "sshome";
include("../_minyanville_header.htm");
include_once("../lib/ss/_search_lib.php");
include_once("../lib/ss/_home_lib.php");
include_once("../lib/ss/_ss_config.php");

$USER=new user();
$loginoption = $USER->is_option();
$DATE_STR="D M jS, Y";
			if(($_GET['a'] != "") && (is_numeric($_GET['a']))) {
				$id = $_GET['a'];
				$result = getalert($id);
				if ($result != 0) {
					$resultSet = true;
					if (!$USER->isAuthed) {
						$loggedin = "no";
					} else {
						$loggedin = "yes";
					}
				} else {
					$resultSet = false;
				}
			}
?>
<!--left contaner start from here-->
<div class="left_contant">
<div class="ArticleColumnContainer">
		<div class="columnLeft1">
			<? if($loginoption) {
			 include("../optionsmith/ss_header.htm");
			 }
			?>
		 </div>
	<!-- code start -->
	<?php
	if($result['body']==""){
		if($result['type']==""){
			$resulttype="alert";
		}else{
			$resulttype=$result['type'];
		}
	?>
		<div style="margin-left:30px; margin-top:50px;">
		No Such <?=ucfirst(strtolower($resulttype)); ?> Is Available</div>
	<?php
	}
	else
	{
	?>
		<div class="ArticleColumnLeftInner">
		<div style="padding-left:4px;" class="sub_common_title"></div>
		<table width="975px" border="0">
		<tr>
			<td align="left">
				<table border="0">
				   <tr><td class="sub_common_title"><?=$result['title']; ?></td></tr>
					<tr>
						<td><?= date($DATE_STR,$result['udate']); ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="left">
				<div style="padding-left:4px;" class="articleBody">
				<?= $result['body'] ?>
				</div>
			</td>
		</tr>
		</table>
		</div>
	<?
	}
	?>
	</div>
</div><!--left contaner end here-->

<!--right contaner start from here-->
<div class="right_contant" >
</div><!--right contaner end here-->
<table width="975px" border="0">
<tr><td>
<?
optiondesc();
?>
</td></tr>
</table>
<? include("../_minyanville_footer.htm"); ?>
