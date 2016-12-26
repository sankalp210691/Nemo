<?php
function decorateWithLinks($text){
   $pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';
   $callback = create_function('$matches', '
       $url       = array_shift($matches);
       $url_parts = parse_url($url);

       $text = parse_url($url, PHP_URL_HOST) . parse_url($url, PHP_URL_PATH);
       $text = preg_replace("/^www./", "", $text);

       $last = -(strlen(strrchr($text, "/"))) + 1;
       if ($last < 0) {
           $text = substr($text, 0, $last) . "&hellip;";
       }

       return sprintf(\'<a rel="nowfollow" href="%s">%s</a>\', $url, $text);
   ');
   return preg_replace_callback($pattern, $callback, $text);
}

function unrenderHTML($text) {
    $text = preg_replace("/&/", "&amp;", $text);
    $text = preg_replace("/</", "&lt;", $text);
    $text = preg_replace("/>/", "&gt;", $text);
    return $text;
}

function validateEmailAddress($email_id) {
    $email_id = trim($email_id);
    if (strlen($email_id) == 0)
        return false;
    $reg = "/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/";
    if (preg_match($reg, $email_id) == 0) {
        return false;
    } else {
        return true;
    }
}

function video_image($url) {
    $image_url = parse_url($url);
    if ($image_url['host'] == 'www.youtube.com' || $image_url['host'] == 'youtube.com') {
        $array = explode("&", $image_url['path']);
        return "http://img.youtube.com/vi/" . substr($array[0], 3) . "/0.jpg";
    } else if ($image_url['host'] == 'www.vimeo.com' || $image_url['host'] == 'vimeo.com') {
        $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/" . substr($image_url['path'], 1) . ".php"));
        return $hash[0]["thumbnail_small"];
    } else {
        return -1;
    }
}

function fetchImage($url) {
    if (strlen($url) == 0 || filter_var($url, FILTER_VALIDATE_URL) == false) {
        echo -1;
        return;
    } else {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $rawdata = curl_exec($ch);
        curl_close($ch);
        $filename = md5($url);
        $fullpath = "../users/images/" . $filename . ".jpg";
        if (!file_exists($fullpath)) {
            $fp = fopen($fullpath, 'x');
            fwrite($fp, $rawdata);
            fclose($fp);
        }
        $fullpath = "users/images/" . $filename . ".jpg";
        return $fullpath;
    }
}

function checkUserAge($dd, $mm, $yyyy) {
    $valid_age = 13;

    $current_year = date("Y");
    $current_month = date("m");
    $current_date = date("d");

    if ($current_year - $yyyy < $valid_age) {
        return false;
    } else if ($current_year - $yyyy > $valid_age) {
        return true;
    } else {
        if ($current_month - $mm < 0) {
            return false;
        } else if ($current_month - $mm > 0) {
            return true;
        } else {
            if ($current_date - $dd < 0) {
                return false;
            } else if ($current_date - $dd >= 0) {
                return true;
            }
        }
    }
}

function getMonthNumber($month_name) {
    $month_name_array = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    $short_month_name_array = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
    $month_number = array_search($month_name, $month_name_array);
    if ($month_number === false) {
        $month_number = array_search($month_name, $short_month_name_array);
        if ($month_number === false) {
            return 0;
        }
    }
    return $month_number + 1;
}

function securePassword($password, $user_id, $date, $time) {
    $salt = sha1(sha1($user_id) . "#" . md5($date) . "@" . sha1($time));
    $password = md5($password . "<>" . sha1(substr($password, 0, 3)));
    return sha1($salt . "_" . $password . "_" . $salt);
}

function date_difference($date1, $date2) {

    $month_array_30 = [4, 6, 9, 11];

    $date_array1 = explode("-", $date1);
    $date_array2 = explode("-", $date2);

    $year1 = $date_array1[0];
    $year2 = $date_array2[0];

    $month1 = $date_array1[1];
    $month2 = $date_array2[1];

    $date1 = $date_array1[2];
    $date2 = $date_array2[2];

    if ($date_array1[0] < $date_array2[0]) {
        $year1 = $date_array1[0];
        $year2 = $date_array2[0];

        $month1 = $date_array1[1];
        $month2 = $date_array2[1];

        $date1 = $date_array1[2];
        $date2 = $date_array2[2];
    } else if ($date_array1[0] > $date_array2[0]) {
        $year1 = $date_array2[0];
        $year2 = $date_array1[0];

        $month1 = $date_array2[1];
        $month2 = $date_array1[1];

        $date1 = $date_array2[2];
        $date2 = $date_array1[2];
    } else {
        $year1 = $year2 = $date_array1[0];
        if ($date_array1[1] < $date_array2[1]) {
            $month1 = $date_array1[1];
            $month2 = $date_array2[1];

            $date1 = $date_array1[2];
            $date2 = $date_array2[2];
        } else if ($date_array1[1] > $date_array2[1]) {
            $month1 = $date_array2[1];
            $month2 = $date_array1[1];

            $date1 = $date_array2[2];
            $date2 = $date_array1[2];
        } else {
            $month1 = $month2 = $date_array1[1];
            if ($date_array1[2] < $date_array2[1]) {
                $date1 = $date_array1[2];
                $date2 = $date_array2[2];
            } else if ($date_array1[2] > $date_array2[2]) {
                $date1 = $date_array2[2];
                $date2 = $date_array1[2];
            } else {
                return 0;
            }
        }
    }

    $difference = 0;
    $is_leap_year1 = isLeapYear($year1);
    if ($year1 != $year2) {
        while ($month1 != 13) {
            if ($month1 == 2 && $is_leap_year1 == true) {
                $difference += abs(29 - $date1);
            } else if ($month1 == 2 && $is_leap_year1 == false) {
                $difference += abs(28 - $date1);
            } else if (in_array($month1, $month_array_30) == true) {
                $difference += abs(30 - $date1);
            } else {
                $difference += abs(31 - $date1);
            }
            $month1++;
            $date1 = 0;
        }

        $is_leap_year2 = isLeapYear($year2);
        $i = 1;
        while ($i != $month2) {
            if ($i == 2 && $is_leap_year2 == true) {
                $difference += 29;
            } else if ($i == 2 && $is_leap_year2 == false) {
                $difference += 28;
            } else if (in_array($i, $month_array_30) == true) {
                $difference += 30;
            } else {
                $difference += 31;
            }
            $i++;
        }
        $difference += $date2;
        while ($year1++ < $year2 - 1) {
            if (isLeapYear($year1) == true) {
                $difference+=366;
            } else
                $difference+=365;
        }
    }else {
        if ($month1 != $month2) {
            if ($month1 == 2 && $is_leap_year1 == true) {
                $difference += abs(29 - $date1);
            } else if ($month1 == 2 && $is_leap_year1 == false) {
                $difference += abs(28 - $date1);
            } else if (in_array($month1, $month_array_30) == true) {
                $difference += abs(30 - $date1);
            } else {
                $difference += abs(31 - $date1);
            }

            while ($month1++ < $month2 - 1) {
                if (in_array($month1, $month_array_30) == true) {
                    $difference+=30;
                } else if ($month1 == 2 && $is_leap_year1 == true) {
                    $difference+=29;
                } else if ($month1 == 2 && $is_leap_year1 == false) {
                    $difference+=28;
                } else {
                    $difference+=31;
                }
            }

            $difference+=$date2;
        } else {
            $difference = $date2 - $date1;
        }
    }
    return $difference;
}

function isLeapYear($year) {
    if ($year % 4 == 0) {
        return true;
    } else {
        return false;
    }
}

function cropNSave($file_address, $cords, $don, $purpose) {     //$don is flag to retain orignal version or not. 1 = delete and rename new as orignal, else don't delete
    $file_address = "../" . $file_address;
    $ext = strtolower(substr($file_address, strripos($file_address, ".") + 1));
    if ($ext != "jpg" && $ext != "jpeg" && $ext != "png")
        return -1;

    $crop_x = $cords[0];
    $crop_y = $cords[1];
    $crop_w = $cords[4];
    $crop_h = $cords[5];

    if (!($img = new Imagick($file_address))) {
        return -1;
    }
    $img->cropimage($crop_w, $crop_h, $crop_x, $crop_y);
    $imgprops = $img->getImageGeometry();
    if ($purpose == "profile_pic") {
        if($imgprops['width'] < 50 || $imgprops['height'] < 50){
            return -2;
        }
    }else if($purpose=="cover_pic"){
        if($imgprops['width'] < 762 || $imgprops['height'] < 89.2){
            return -2;
        }
    }

    if ($don == 1) {
        unlink($file_address);
        if ($img->writeimage($file_address) == false) {
            return -1;
        }
    } else {
        $dirs = explode("/", $file_address);
        $dir_lst = "";
        for ($i = 4; $i < sizeOf($dirs) - 1; $i++) {
            $dir_lst = $dir_lst . $dirs[$i] . "/";
        }

        $randomName = rand(100, 1000000);
        $dt = date("mdHis");
        $yr = date("Y") - 1900;
        $randomName = $randomName . $yr . $dt;
        $randomName = md5($randomName);
        $randomName = $randomName . '.' . $ext;

        $file_address = $dir_lst . $randomName;

        if ($img->writeimage($file_address) == false) {
            return -1;
        }
    }

    if ($purpose == "cover_pic") {
        createSmallImage($file_address, $ext,$imgprops, 256, 30);
    } else if ($purpose == "profile_pic") {
        createSmallImage($file_address, $ext,$imgprops, 30, 30);
    }
    return substr($file_address, 3);
}

function createSmallImage($file_address, $ext,$imgprops, $req_width, $req_height) {
    if ($ext != "png" && $ext != "jpg" && $ext != "jpeg") {
        return -1;
    } else {
        $exploded_file_address = explode("/", $file_address);
        $real_name = end($exploded_file_address);
        $fad = "";
        for ($i = 0; $i < sizeOf($exploded_file_address) - 1; $i++) {
            $fad = $fad . $exploded_file_address[$i] . "/";
        }
        $fad = $fad . "blur_" . $real_name;

        $img = new Imagick($file_address);
        $img->resizeImage($req_width,$req_height, imagick::FILTER_LANCZOS, 0.9, true);
        if ($img->writeimage($fad) == false) {
            return -1;
        }else{
            return $fad;
        }
    }
}

function formattedDate($date_string) {
    $date_array = explode("-", $date_string);
    $year = $date_array[0];
    $month = (int) $date_array[1];
    $date = (int) $date_array[2];
    $month_name = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    $month = $month_name[$month];
    return $date . " " . $month . ", " . $year;
}

function getBlurPicAddress($profile_pic) {
    $profile_pic_address_array = explode("/", $profile_pic);
    $last_index = sizeof($profile_pic_address_array) - 1;
    $profile_pic_address_array[$last_index] = "blur_" . $profile_pic_address_array[$last_index];
    $blur_profile_pic = "";
    for ($i = 0; $i < $last_index; $i++) {
        $blur_profile_pic.=$profile_pic_address_array[$i] . "/";
    }
    $blur_profile_pic.=$profile_pic_address_array[$i];
    return $blur_profile_pic;
}
?>