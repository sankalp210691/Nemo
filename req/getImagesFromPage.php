<?php

set_time_limit(0);
// Inculde the phpcrawl-mainclass 
include("PHPCrawler/libs/PHPCrawler.class.php");


class ContentCrawler extends PHPCrawler {

    function handleDocumentInfo($DocInfo) {
        if ($DocInfo->received == true) {
            $html = $DocInfo->content;
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $array = array();
            $dom->preserveWhiteSpace = false;
            $imgs = $dom->getElementsByTagName("img");
            $metas = $dom->getElementsByTagName("meta");
            $metaarray = array();
            foreach ($metas as $meta) {
                $property = $meta->getAttribute('property');
                $content = $meta->getAttribute('content');
                if ($property == "og:title") {
                    $metaarray["title"] = $content;
                } else if ($property == "og:description") {
                    $metaarray["description"] = $content;
                }
            }
            $imgarray = array();
            $i = 0;
            foreach ($imgs as $img) {
                $address = explode("?", $img->getAttribute('src'))[0];
                $arr = explode(".", $address);
                $extension = $arr[sizeof($arr) - 1];
                if ($extension == "jpg" || $extension == "jpeg" || $extension == "png" || $extension == "bmp") {
                    $imgarray[$i] = $address;
                    $i++;
                }
            }
            $array["meta"] = $metaarray;
            $array["img"] = $imgarray;
            echo "<br><br><hr><br>".json_encode($array);
            return;
        } else {
            echo json_encode(array());
        }
        flush();
    }

}

$url = $_POST["url"];
if (strlen($url) == 0) {
    echo -1;
    return;
} else {
//    $crawler = new ContentCrawler();
//
//// URL to crawl 
//    $crawler->setURL($url);
//
//// Only receive content of files with content-type "text/html" 
//    $crawler->addContentTypeReceiveRule("#text/html#");
//
//// Ignore links to pictures, dont even request pictures 
//    $crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i");
//
//// Store and send cookie-data like a browser does 
//    $crawler->enableCookieHandling(true);
//
//// Set the traffic-limit to 1 MB (in bytes, 
//// for testing we dont want to "suck" the whole site) 
//    $crawler->setTrafficLimit(1000 * 1024);
//
//// Thats enough, now here we go 
//    $crawler->go();



    $html = getHTML($url, 10);
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $array = array();
    $dom->preserveWhiteSpace = false;
    $imgs = $dom->getElementsByTagName("img");
    $metas = $dom->getElementsByTagName("meta");
    $metaarray = array();
    foreach ($metas as $meta) {
        $property = $meta->getAttribute('property');
        $content = $meta->getAttribute('content');
        if ($property == "og:title") {
            $metaarray["title"] = $content;
        } else if ($property == "og:description") {
            $metaarray["description"] = $content;
        }
    }
    $imgarray = array();
    $i = 0;
    foreach ($imgs as $img) {
        $address = explode("?", $img->getAttribute('src'))[0];
        $arr = explode(".", $address);
        $extension = $arr[sizeof($arr) - 1];
        if ($extension == "jpg" || $extension == "jpeg" || $extension == "png" || $extension == "bmp") {
            $imgarray[$i] = $address;
            $i++;
        }
    }
    $array["meta"] = $metaarray;
    $array["img"] = $imgarray;
    echo json_encode($array);
}

function getHTML($url, $timeout) {
    $ch = curl_init($url); // initialize curl with given url
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
    curl_setopt($ch, CURLOPT_POST, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_NOBODY, FALSE);
    curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
    curl_setopt($ch, CURLOPT_REFERER, "");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36");
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
?>