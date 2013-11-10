<?php
global $HTPFX,$HTHOST,$D_R;
ini_set('magic_quotes_runtime',0);
include_once($D_R."/lib/config/_disqus_config.php");
class disqusSys
{
	public function disqusComment($articleid,$tableName,$item_type,$disqusItemUrl){
		global $HTPFX,$HTHOST,$shortName;
		$disqusqry = "SELECT eim.title as title,eim.url as url
		FROM ex_item_meta eim WHERE eim.item_id = '".$articleid."' AND eim.item_type='".$item_type."'";
		$disThread = exec_query($disqusqry,1); 
		if($disqusItemUrl || $disqusItemUrl!=""){
			$disThread['url']=$disqusItemUrl;
		}
		?>
		<script type="text/javascript">
		//var disqus_developer = 1;

			/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
			var disqus_shortname = "<?=$shortName?>"; // required: replace example with your forum shortname

			// The following are highly recommended additional parameters. Remove the slashes in front to use.
			var disqus_identifier = "<?=$HTPFX.$HTHOST.$disThread['url']?>";
			var disqus_url = "<?=$HTPFX.$HTHOST.$disThread['url']?>";
			var disqus_title="<?=addslashes($disThread['title']);?>";

			/* * * DON'T EDIT BELOW THIS LINE * * */
			(function() {
				var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
				dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
				(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
			})();
		</script>
		<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
	<!--<a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>-->
<?
	}

	public function disqusSSO(){
		global $HTPFX,$HTHOST;
		global $_SESSION;
		$userName = $_SESSION["nameFirst"].' '.$_SESSION["nameLast"];
		$data = array(
				"id" => $_SESSION["user_id"],
				"username" => $userName,
				"email" => $_SESSION["email"]
			);
			$message = base64_encode(json_encode($data));
			$timestamp = time();
			$hmac = hash_hmac('sha1', "$message $timestamp", DISQUS_SECRET_KEY);
		?>

		<script type="text/javascript">
		var disqus_config = function() {
			this.page.remote_auth_s3 = "<?php echo "$message $hmac $timestamp"; ?>";
			this.page.api_key = "<?php echo DISQUS_PUBLIC_KEY; ?>";
		}
		</script>
<?
	}

	function disqusCommentCount(){
		global $HTPFX,$HTHOST,$shortName;
	?>
		<script type="text/javascript">
		/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
		var disqus_shortname = "<?=$shortName?>"; // required: replace example with your forum shortname

		/* * * DON'T EDIT BELOW THIS LINE * * */
		(function () {
			var s = document.createElement('script'); s.async = true;
			s.type = 'text/javascript';
			s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
			(document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
		}());
	</script>

<?	}

	function disqusMostComment()
	  {
		  try{
			  $disqus = new DisqusAPI(DISQUS_SECRET_KEY);
			  $forums = $disqus->threads->listPopular(array('forum'=>'minyanvillemainsite','limit'=>'5','since'=>time()-2592000));
      return $forums; 
	  }
		  catch(Exception $e)
		  {
			return false;		  
		  }
	  }
	   

	  function getDiqusCommentCount($url){
		  try{
			  $disqus = new DisqusAPI(DISQUS_SECRET_KEY);
			  $forums = $disqus->threads->details(array('forum'=>'minyanvillemainsite','thread:ident'=>$url));
			  return $forums; 
		  }
		  catch(Exception $e)
		  {
			return false;	  
		  }
	  } 

} // class End
?>