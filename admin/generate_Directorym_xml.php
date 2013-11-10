<?
			// The query will get rows for the past 7 days.
			$qry="select * from articles where date between DATE_SUB('".mysqlNow()."',INTERVAL 7 DAY) and '".mysqlNow()."' and is_live='1' and approved='1'";
			$results=exec_query($qry);
			$count = num_rows($qry);
			$cur_year = date('Y');

			if ($count != '0') {
			// XML Content starts here.
			$xml_content = '<?xml version="1.0" encoding="UTF-8"?>
						<DATA>
						<TITLE>Minyanville articles data</TITLE>
						<TOTALARTICLES>'.$count.'</TOTALARTICLES>
						<COPYRIGHT>Copyright '.$cur_year.' Minyanville Publishing and Multimedia, LLC. All Rights Reserved.</COPYRIGHT>
						<LINK>http://www.minyanville.com/</LINK>';



			foreach($results as $result)
			{
			$articleId = $result['id'];
			$articleKeyword = $result['keyword'];
			$articleBlurb = $result['blurb'];
			$articleTitle=$result['title'];
			$categoryId=$result['category_ids'];
			$articleContrib=$result['contributor'];
			$articlepublishDate=$result['date'];
			//$articleDateOld=$result['date'];
			//$articleDate=gmdate("Y-m-d", strtotime($articleDateOld));


			$articlebody = article_body_word_replace($result['body']);

			// Metadata for XML File.
			$categorydata = article_categories($categoryId);
			$tickerString = article_tickers($articleId);
			$tagStringData = article_tags($articleId);

			// Article Content looping thro the articles with in XML.
			$xml_content.= '<ARTICLE>
					<PUBDATE>'.$articlepublishDate.'</PUBDATE>
					<URL>http://www.minyanville.com'.makeArticleslink($articleId,$articleKeyword,$articleBlurb).'/from/directorym</URL>
					<ID>'.$articleId.'</ID>';
			$xml_content.= $tickerString;
			$xml_content.= $categoryData;
			$xml_content.= $tagStringData;
			$xml_content.= '<BODYTEXT>'.htmlentities($articlebody).'</BODYTEXT>
					<TITLE>'.htmlentities($articleTitle).'</TITLE>
					<AUTHORNAME>'.$articleContrib.'</AUTHORNAME>
					<KEYWORDS>'.$articleKeyword.'</KEYWORDS>
					</ARTICLE>';
			}


			$xml_content.=  "</DATA>";
			// XML Content ends.
//echo $xml_content;
		if($xml_content!='')
			{
				$fpath = "../assets/directorym/";
				//$feedName = "c://web_dev/assets/directorym/".date("Ymd")."_mv_articles.xml";
				$feedName = $fpath.date("Ymd")."_mv_articles.xml";
				$feedFile=fopen($feedName,"w+");
				fwrite($feedFile,$xml_content);
				fclose($feedFile);
				chmod($feedName, 0777);
				$conn_id = ftp_connect($ftp_conn_id);
				$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_password);

				if ((!$conn_id) || (!$login_result)) {
					echo "FTP connection has failed!";
					echo "Attempted to connect to $ftp_conn_id for user $ftp_user_name";
					exit;
				       }
				else {
				$fp = fopen($feedName, 'r');
				$remote_file = date("Ymd")."_mv_articles.xml";

				ftp_pasv($conn_id, true);

			        if (ftp_fput($conn_id, $remote_file, $fp, FTP_ASCII)) {
				    echo "Successfully uploaded $file\n";
				    echo "Feed for ".$count." Articles has been generated<br>";
				}
				else {
				mail('mvil@ebusinessware.com','DirectoryM XML Failed','The XML File $feedName was not uploaded.please check','mvil@ebusinessware.com');
				}
				ftp_close($conn_id);
				fclose($fp);
				}
			}
		}
		else {
			echo "There are no articles,XML Feed file not generated";
		}
?>
