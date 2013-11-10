<?php
	$limit = $_POST[count]; ?>
	<div style="border:solid 1px #CCCCCC; margin:15px; padding:25px;">
		<div style="font-weight:bold; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:18px; border-bottom: solid 3px  #FCEEFD;">Minyanville</div>
		<div style="color:#5f5f5f; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; padding-bottom:10px; padding-top:2px;">The Trusted Choice for the Wall Street Voice</div>
		<?
			$sql = "select articles.id id, articles.title, contributors.name author, articles.contributor, contributors.disclaimer,        	        articles.position, contrib_id authorid, publish_date, blurb, body, position, character_text ";
			$sql .= "from articles, contributors ";
			$sql .= "where articles.contrib_id = contributors.id and articles.approved='1' and articles.is_live='1' ";
			$sql .= "ORDER BY publish_date DESC LIMIT 0, $limit";

			$results = exec_query($sql);
	
			foreach ($results as $result) {
				$numWords = 100;
				$articlebody=strip_tag_style($result['body']);
				$tempBody = htmlentities(strip_tags($articlebody,'ENT_QUOTES'));
				$bodyArray = str_word_count($tempBody,1);
				$bodyArray = preg_split("/[\s,]+/",$tempBody);
				for($i=0;$i<$numWords;$i++) {
					$body .= $bodyArray[$i] . " ";
				}

				if (count($bodyArray) > 100) {
					$body .= "...";
				}
				$title=htmlentities(strip_tags($result['title']));
		?>
			<tr>
				<td>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr><td style="font-weight:600; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px;"><a href="<?=$HTPFX.$HTHOST?>/articles/index.php?a=<?=$result['id'];?>"><?=$title;?></a></td></tr>
						<tr><td style="padding-bottom:15px; word-spacing:0px; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px;"><?=date('D, j M Y H:i:s',strtotime($result['publish_date'])) ?></td></tr>
						<tr><td style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px;"><?=$body;?></td></td>
					</table>
				</td>
			</tr>
		<?
				$body = ""; echo "<br>";
			}
		?>
	</div>