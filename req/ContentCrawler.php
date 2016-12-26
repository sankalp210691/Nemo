<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ContentCrawler
 *
 * @author sankalp
 */
// It may take a whils to crawl a site ... 
set_time_limit(10000);
// Inculde the phpcrawl-mainclass 
include("PHPCrawler/libs/PHPCrawler.class.php");

class ContentCrawler extends PHPCrawler {

    function handleDocumentInfo($DocInfo) {
        // Just detect linebreak for output ("\n" in CLI-mode, otherwise "<br>"). 
        if (PHP_SAPI == "cli")
            $lb = "\n";
        else
            $lb = "<br />";

        // Print the URL and the HTTP-status-Code 
        echo "Page requested: " . $DocInfo->url . " (" . $DocInfo->http_status_code . ")" . $lb;

        // Print the refering URL 
        echo "Referer-page: " . $DocInfo->referer_url . $lb;

        // Print if the content of the document was be recieved or not 
        if ($DocInfo->received == true)
            echo "Content received: " . $DocInfo->bytes_received . " bytes" . $lb;
        else
            echo "Content not received" . $lb;

        // Now you should do something with the content of the actual 
        // received page or file ($DocInfo->source), we skip it in this example  

        echo $lb;

        flush();
    }

}

// Now, create a instance of your class, define the behaviour 
// of the crawler (see class-reference for more options and details) 
// and start the crawling-process.  

$crawler = new ContentCrawler();

// URL to crawl 
$crawler->setURL("http://www.nytimes.com/2013/04/23/business/electronic-smarts-at-hotels-attract-guests.html?_r=0");

// Only receive content of files with content-type "text/html" 
$crawler->addContentTypeReceiveRule("#text/html#");

// Ignore links to pictures, dont even request pictures 
$crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i");

// Store and send cookie-data like a browser does 
$crawler->enableCookieHandling(true);

// Set the traffic-limit to 1 MB (in bytes, 
// for testing we dont want to "suck" the whole site) 
$crawler->setTrafficLimit(1000 * 1024);

// Thats enough, now here we go 
$crawler->go();

// At the end, after the process is finished, we print a short 
// report (see method getProcessReport() for more information) 
$report = $crawler->getProcessReport();

if (PHP_SAPI == "cli")
    $lb = "\n";
else
    $lb = "<br />";

echo "Summary:" . $lb;
echo "Links followed: " . $report->links_followed . $lb;
echo "Documents received: " . $report->files_received . $lb;
echo "Bytes received: " . $report->bytes_received . " bytes" . $lb;
echo "Process runtime: " . $report->process_runtime . " sec" . $lb;
