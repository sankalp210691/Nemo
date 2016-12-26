<?php
function getCategories(){
    $categorys = array();
    $category = new Category();
    $categorycon = new CategoryController();
    
    $categorys = $categorycon->findByAll($category,array("id","name","image_src"),"order by name",null);
    $categorys_size = sizeof($categorys);
    $cat = array();
    for($i=0;$i<$categorys_size;$i++){
        $cat[$i] = array(
            "id"=>$categorys[$i]->getId(),
            "name"=>$categorys[$i]->getName(),
            "img_src"=>$categorys[$i]->getImage_src()
        );
    }
    return $cat;
}
?>
