<?php
function getTopTrends($start_from,$count){
    $interest = new Interests();
    $interestcon = new InterestsController();
    
    $interests = $interestcon->findByAll($interest, array("*"), "order by score desc limit ".$start_from.",".$count);
    return $interests;
}
?>
