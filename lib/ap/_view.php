<?php
require_once($D_R.'/lib/ap/_model.php');
require_once($D_R.'/lib/ap/_controller.php');
class apNewsView extends apNews{
	function showArticles(){
                $objapNewsController = new apNewsController();
                $last14DayArticles=$objapNewsController->getArticles(NULL,14);
			foreach ($last14DayArticles as $article) {
                                        echo "<li>";
                                        echo "<a target='_parent' href='/articles/ap-content/index/ap/".$article['ap_id']."'>".$article['headline']."</a><br>";
                                        echo "<span>".date('M d g:i a', strtotime($article['date']))."</span>";
                                        echo "</li>";
                                        $i++;
                        }
        }
// dan functions 

        function showArticlesListings(){
                $objapNewsController = new apNewsController();
                $last2days=$objapNewsController->getArticles(NULL,2);
                        foreach ($last2days as $article) {
                                if ($i < 21) {
                                        echo "<li>";
                                        echo "<a target='_parent' href='/articles/ap-content/index/ap/".$article['ap_id']."'>".$article['headline']."</a><br>";
                                        echo "<span>".date('g:i a', strtotime($article['date']))."</span>";
                                        echo "</li>";
                                        $i++;
                                }
                        }

        }

         function showArticlesHome($limit){
                 $objapNewsController = new apNewsController();
                 $last2days=$objapNewsController->getArticles(NULL,2);
                         foreach ($last2days as $article) {
                                 if ($i < $limit) {
                                         echo "<li>";
                                         echo "<a target='_parent' href='/articles/ap-content/index/ap/".$article['ap_id']."'>".$article['headline']."</a><br>";
                                         echo "<span>".date('g:i a', strtotime($article['date']))."</span>";
                                         echo "</li>";
                                         $i++;
                                 }
                 }
         }


        function showSingleArticle($apId){
                $objapArticleController = new apNewsController();
                $singleArticle=$objapArticleController->getSingleArticle($apId);
                return $singleArticle;
        }

}
$objapNewsView=new apNewsView();
//$objapNewsView->showArticles();
?>
