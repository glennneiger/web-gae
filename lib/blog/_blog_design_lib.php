<?
global $D_R;
include_once("$D_R/lib/blog/_blog_data_lib.php");
class blogDesign{
function displayBlogs()
	    {
	      global $HTHOST,$HTPFX,$IMG_SERVER,$productItemType; 
          $objblogData = new blogData();
		  $result=$objblogData->getBlogs();
		  $count=count($result->items);
	      $item=$result->items;
		  ?>
		    <div  class="top-module">
	        <ul id="all-articles">
		    <div style="width: 110px; float: left;">
			
		    <a href="http://blogs.minyanville.com/" target="_blank"><h3><img  src="<?php echo $HTPFX.$HTHOST;?>/images/home_redesign/bloggercommunity.gif"></h3></a></div>
		    <div style="width: 81px; float: left;"></div>
		    <div style="clear: both;"></div>
		  <?php
	      for($i=0;$i<$count;$i=$i+1)
	      {
	       $title=$item[$i]['title']['0'];
		   $link=$item[$i]['link']['0'];
		   $creator=$item[$i]['dc']['creator']['0'];
	      ?>
		  <li><a href="<?php echo $link;?>" target="_blank"><?php echo  $title;?></a>&nbsp;<br><?php echo $creator;;?></li>
		  <?php
		   }
		   ?>  
		  <li><a href="http://blogs.minyanville.com/page/2/" target="_blank">More »</a></li>
         </ul>
         </div>
		 
		   <?php
		 
	  	}
	
      
} // class End

?>
