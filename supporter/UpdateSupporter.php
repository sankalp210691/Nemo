<?php
	function getUpdates($user_id,$type){
		$update_array = array();
		$description = "<b>Sankalp Kulshreshta</b> liked your pic. Also Big boss is awesome. I just love it!";
		if(strlen($description)>50){
			$description = substr($description,0,47)."<b>...</b>";
		}
		$update_array[0] = array(
			"id"=>1,
			"url"=>"http://www.google.com",
			"img"=>"img/blur_default_profile_pic.jpg",
			"description"=>$description
		);
		return $update_array;
	}
?>
