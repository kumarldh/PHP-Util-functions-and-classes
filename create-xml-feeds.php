<?php
//The number of milli seconds to wait while trying to connect. Use 0 to wait indefinitely.
define('APITIMEOUT', 3000);
define('EXECUTIONTIMEOUT', 10);
define('LOGTOEMAIL', 10);

//TRUE to fail silently if the HTTP code returned is greater than or equal to 400.
//The default behavior is to return the page normally, ignoring the code.
define('FAILONERROR', TRUE);

function fetchXML($url, $debug=0){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, APITIMEOUT);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_TIMEOUT, EXECUTIONTIMEOUT);
    curl_setopt($ch, CURLOPT_FAILONERROR, FAILONERROR);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    if(false == $output || curl_errno($ch))
    {
        echo (1===$debug) ? ($output."Curl error: ". curl_error($ch))."\nFailed URI:".$url : '';//base64_encode
        mail(LOGTOEMAIL, 'CURL - URI - '.$url, "
CURL call failed for URL = ". $url ."\n
Curl error no: ". curl_errno($ch) ."\n
Curl error: ". curl_error($ch) ."\n
Time:". date( 'Y-m-d H:i:s', strtotime("now")) ."\n
", 'From: '.LOGTOEMAIL."\r\n"); 
        return false;
    }
    curl_close($ch);
    return $output;
}


/*
 * Following is a test
 */
$urlArray = array(
        'http://www.shameless-self-promotion.org/feed/',
        'http://www.kumarchetan.com/blog/feed/',
    );
/*Must be writeable*/
$dataFolder = './feeds/';

foreach($urlArray as $url){
    $content = fetchXML($url, 1);
    if($content != ''){
        $fh = fopen($dataFolder . md5($url), 'w');
        fwrite($fh, $content);
        fclose($fh);
    }
}
