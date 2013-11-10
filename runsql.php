<?php
if($_POST['sql']!="")
{
    echo "<br><br>";
     echo "<br><br>";
    echo $_POST['sql'];
     echo "<br><br>";
    
    $res = exec_query($_POST['sql']);
    htmlprint_r($res);
}

?>
<form name="runqry" method="post">
<input type="text" name="sql" id="sql">
<input type="submit" name="execute" id="execute" >
</form>
