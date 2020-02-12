<?php
/*******************************************************************
 *          2020 Lathanao - Module for Prestashop
 *          Add a great module and modules on your great shop.
 *
 *          @author         Lathanao <welcome@lathanao.com>
 *          @copyright      2020 Lathanao
 *          @license        MIT (see LICENCE file)
 ********************************************************************/

ini_set('max_execution_time', 1);
error_reporting(E_ALL);

define('_URL_', 'http://127.0.0.1');
define('_PORT_', '7700');
define('_TYPE_', 'POST');
define('_INDEX_', 'v5dzl8ki');
define('_COLLECTION_', 'products');
define('_QUERY_', 'captor');

$fieldToManage = array(
    'attributesToHighlight',
    'description',
    'cropLength',
    'limit',
);


$uri = _URL_ . '/indexes/' . _INDEX_ . '/search?';

foreach ($_GET as $key => &$getItem) {
    if (array_key_exists($key, $fieldToManage)) {
        $getItem = filter_var($getItem, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        $uri .=  $key . '=' . $getItem . '&';
    }
}

header('Content-Type: application/json; charset=utf-8');
echo curlRequest($uri, null , $type = 'GET');
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
