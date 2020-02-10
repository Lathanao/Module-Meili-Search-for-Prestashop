<?php


ini_set('max_execution_time', 1);
error_reporting(E_ALL);

define('_URL_', 'http://127.0.0.1');
define('_PORT_', '7700');
define('_TYPE_', 'POST');
define('_INDEX_', 'v5dzl8ki');
define('_COLLECTION_', 'products');
define('_QUERY_', 'captor');


$uri = '';
if(isset($_GET['query'])) {
    $query = filter_var($_GET['query'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $uri = _URL_ . '/indexes/' . _INDEX_ . '/search?q=' . $query;
} else {
    header('Content-Type: text/plain');
    die('No query search found');
}
if(isset($_GET['attributesToHighlight'])) {
    $attributesToHighlight = filter_var($_GET['attributesToHighlight'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $uri .= '&attributesToHighlight=' . $attributesToHighlight;
} else {
    $uri .= '&attributesToHighlight=\'name,description\'';
}
if(isset($_GET['description'])) {
    $attributesToCrop = filter_var($_GET['description'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $uri .= '&attributesToCrop=' . $attributesToCrop;
} else {
    $uri .= '&attributesToCrop=\'description\'';
}
if(isset($_GET['cropLength'])) {
    $cropLength = filter_var($_GET['cropLength'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $uri .= '&cropLength=' . $cropLength;
} else {
    $uri .= '&cropLength=200';
}
if(isset($_GET['limit'])) {
    $limit = filter_var($_GET['limit'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $uri .= '&limit=' . $limit;
} else {
    $uri .= '&limit=2';
}


//    $attributesToHighlight = filter_var($_GET['attributesToHighlight'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
//    $attributesToCrop = filter_var($_GET['description'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
//    $cropLength = filter_var($_GET['cropLength'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
//    $limit = filter_var($_GET['limit'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

//
//$uri = _URL_ . '/indexes/' . _INDEX_ . '/search?q=' . $query;
//$uri .= '&attributesToHighlight=\'name,description\'';
//$uri .= '&attributesToCrop=\'description\'';
//$uri .= '&cropLength=200';
//$uri .= '&limit=2';

$data = [];
header('Content-Type: application/json; charset=utf-8');
echo curlRequest($uri, '[' . json_encode($data) . ']' , $type = 'GET');


die();



function curlRequest($uri, $data = null, $method = 'POST')
{
    $curlObj = curl_init();
    $options = [
        CURLOPT_URL => $uri,
        CURLOPT_PORT => _PORT_,
        CURLOPT_HTTPHEADER => ['content-type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method
    ];
    if (!empty($data)) {
        $options[CURLOPT_POSTFIELDS] = $data;
    }
    if (stripos($uri, 'https://') === 0) {
        $options[CURLOPT_SSL_VERIFYHOST] = 2; // have to use false
        $options[CURLOPT_SSL_VERIFYPEER] = true; // have to use false
    }

    curl_setopt_array($curlObj, $options);

    if (curl_errno($curlObj)) {
        $returnData = curl_error($curlObj);
    } else {
        $returnData = curl_exec($curlObj);
    }

    curl_close($curlObj);

    return $returnData;
}
