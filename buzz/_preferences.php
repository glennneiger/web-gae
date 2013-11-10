<?php
$checked = ' checked="checked" ';
?>

<!-- Preferences layer starts here -->

<div id="prefs" class="mainLayer" style="display:none;">
	<div id="prefsTabBar">
		<div class="button"><a href="javascript:void(0);" onclick="switchToEdit();" id="prefEdit" class="prefSelected">EDIT PREFERENCES</a></div>
		<div class="button"><a href="javascript:void(0);" onclick="switchToFilter();" id="prefFilter" class="filterDeselected">FILTER BUZZ POSTS</a></div>
	</div>
	<div id="prefsSubEdit" class="subLayer">
		<form name="formPrefs" id="formPrefs">
		<h5>DISPLAY CRITTER IMAGES</h5>
		<input name="characters" type="radio" value="1" <?php if ($characters==1) {echo $checked;} ?>/>Yes
		<input name="characters" type="radio" value="0" <?php if ($characters==0) {echo $checked;} ?>/>No
		<div class="prefsDivider"></div>
		<h5>ALERTS</h5>
		<input name="alerts" type="radio" value="1" <?php if ($alerts==1) {echo $checked;} ?>/>Show alert window
		<input name="alerts" type="radio" value="0" <?php if ($alerts==0) {echo $checked;} ?>/>Hide alert window
		<div class="prefsDivider"></div>
		<h5>SET WINDOW SIZE AT STARTUP</h5>
		<input name="window_size" type="radio" value="s" <?php if ($window_size=='s') {echo $checked;} ?>/>Small
		<input name="window_size" type="radio" value="m" <?php if ($window_size=='m') {echo $checked;} ?>/>Medium
		<input name="window_size" type="radio" value="l" <?php if ($window_size=='l') {echo $checked;} ?>/>Large
		<div class="prefsDivider"></div>
		<h5>SET TEXT SIZE</h5>
		<input name="text_size" type="radio" value="s" <?php if ($text_size=='s') {echo $checked;} ?>/>Small
		<input name="text_size" type="radio" value="m" <?php if ($text_size=='m') {echo $checked;} ?>/>Medium
		<input name="text_size" type="radio" value="l" <?php if ($text_size=='l') {echo $checked;} ?>/>Large
		<div class="prefsDivider"></div>
		<h5>VIEWING</h5>
		Automatically jump to latest post<br />
		<input name="auto_jump" type="radio" value="1" <?php if ($auto_jump==1) {echo $checked;} ?>/>On
		<input name="auto_jump" type="radio" value="0]" <?php if ($auto_jump==0) {echo $checked;} ?>/>Off <br />
		Limit number of Buzz posts per page<br />
		<input name="posts_per_page" type="radio" value="5" <?php if ($posts_per_page==5) {echo $checked;} ?>/>5 per page
		<input name="posts_per_page" type="radio" value="10" <?php if ($posts_per_page==10) {echo $checked;} ?>/>10 per page
		<input name="posts_per_page" type="radio" value="15" <?php if ($posts_per_page==15) {echo $checked;} ?>/>15 per page

	</div>
	<div id="prefsSubFilter" class="subLayer" style="display:none; ">

<?php

$qry = "SELECT C.name, C.id  AS cid,SCF.subscriber_id AS sid
FROM contributor_groups_mapping CGM,contributor_groups CG ,
contributors C LEFT OUTER JOIN subscriber_contributor_filter SCF ON C.id = SCF.contrib_id AND SCF.subscriber_id='".$sid."'
WHERE C.id=CGM.contributor_id AND CGM.group_id=CG.id AND CG.group_name='Buzz & Banter' order by C.name";

$rows = exec_query($qry);
$numTeachers = 0;

//echo $filters;
$teacherInfo = null;
foreach ($rows as $row) {
?>
		<div class="right">
			<input name="teacher<?= $numTeachers; ?>" type="radio" value="1" <?php if (!$row['sid']) {echo $checked;} ?>/>Show
			<input name="teacher<?= $numTeachers; ?>" type="radio" value="0" <?php if ($row['sid']) {echo $checked;} ?>/>Hide
		</div>
	<p><?= $row['name']; ?></p>
<?php
  $teacherInfo .= 'teacherID[' . $numTeachers . '] = ' . $row['cid'] . ';' . "\n" .
                  'teacherSH[' . $numTeachers . '] = getRadioButtonValue(document.formPrefs.teacher' . $numTeachers . ');' . "\n";
$numTeachers += 1;
}

?>
</div>

	<input type="hidden" value="<?=$numTeachers?>" name="numTeachers" id="numTeachers" />
	<input name="submitPrefs" id="submitPrefs" type="button" value="SAVE CHANGES" onclick="javascript:submitPrefsForm();" />
	<input name="cancelPrefs" id="cancelPrefs" type="button" value="CANCEL" onclick="javascript:cancelSubmit();" />
</form>
  <script type="text/javascript" language="javascript">
	var teacherID = new Array();
	var teacherSH = new Array();
    <?php echo $teacherInfo; ?>
  </script>
</div>



