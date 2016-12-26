<?php

$uploaddir = '../users/images/';

// The posted data, for reference
$req = $_POST['req'];

if ($req == 'upload') {
    $file = $_POST['value'];
    $name = $_POST['name'];
    $record = $_POST["record"];

    //Allowed Mimes
    $allowedMime = array("jpg", "jpeg", "png", "bmp");

// Get the mime
    $getMime = explode('.', $name);
    $mime = end($getMime);
    if (in_array($mime, $allowedMime) == false) {
        echo json_encode(array(-1));
        return;
    }

// Separate out the data
    $data = explode(',', $file);

// Encode it correctly
    $encodedData = str_replace(' ', '+', $data[1]);
    $decodedData = base64_decode($encodedData);

// Random name

    $randomName = rand(100, 1000000);
    $dt = date("mdHis");
    $yr = date("Y") - 1900;
    $randomName = md5($randomName . $yr . $dt);
    $randomName = $randomName . '.' . $mime;

    if (file_put_contents($uploaddir . $randomName, $decodedData)) {
        //generate thumbnail
        $rn = mt_rand(0, 1000000);
        $rn_len = strlen($rn);
        if ($rn_len < 7) {
            for ($i = 0; $i < 7 - $rn_len; $i++) {
                $rn = "0" . $rn;
            }
        }
        if ($record == 1) {
            $size = getimagesize("$uploaddir" . "$randomName");
            $imageId = $yr . $dt . $rn;
            $uploaddir = substr($uploaddir, 3);
            echo json_encode(array("photo_address" => $uploaddir . $randomName, "photo_width" => $size[0], "photo_height" => $size[1], "image_id" => $imageId));
        } else {
            $size = array();
            $size = getimagesize("$uploaddir" . "$randomName");
            $imageId = $yr . $dt . $rn;
            $uploaddir = substr($uploaddir, 3);
            echo json_encode(array("photo_address" => $uploaddir . $randomName, "photo_width" => $size[0], "photo_height" => $size[1], "image_id" => $imageId));
        }
    } else {
        echo json_encode(array(-1));
    }
} else if ($req == 'delete') {
    $path = $_POST["path"];
    unlink("../" . $path);
} else {
    echo json_encode(array(-1));
}
?>
