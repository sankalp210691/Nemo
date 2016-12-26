<?php

require "../db/DBConnect.php";
include "../model/CategoryModel.php";
include "../controller/CategoryController.php";
include "../supporter/CategorySupporter.php";

$req = $_GET["req"];
if ($req == "get_categories") {
    $categorys = getCategories();
    echo json_encode($categorys);
} else {
    header("location:badpage.html");
}
?>
