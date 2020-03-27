<?php
/*******************************************************************
 *          2020 Lathanao - Module for Prestashop
 *          Add a great module and modules on your great shop.
 *
 * @author         Lathanao <welcome@lathanao.com>
 * @copyright      2020 Lathanao
 * @license        MIT (see LICENCE file)
 ********************************************************************/

include(dirname(__FILE__) . '/../../../config/config.inc.php');
include(dirname(__FILE__) . '/../../../init.php');

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;

//header('Content-Type: application/json; charset=utf-8');
//header('Content-Type: application/json; charset=utf-8');
header('Content-type =  text/html; charset=utf-8');

ini_set('display_errors', 1);
ini_set('track_errors', 1);
ini_set('html_errors', 1);
ini_set('realpath_cache_size', '5M');
ini_set('max_execution_time', 60000);
error_reporting(E_ALL);

define('_COLLECTION_', 'products');

/* Check security token */
if (!Tools::isPHPCLI()) {
    if (Tools::substr(Tools::encrypt('ao_meili_search/cron'), 0, 10) !== Tools::getValue('token') || !Module::isInstalled('ao_meili_search')) {
        die('Bad token');
    }
}

Configuration::updateValue('SEARCH_UID_PRODUCT', null);
Configuration::updateValue('SEARCH_UID_CATEGORY', null);

$ao_meili_search = Module::getInstanceByName('ao_meili_search');
if (!$ao_meili_search->active) {
    die('Module Inactive');
}

$uri = Context::getContext()->link->getBaseLink() . Configuration::get('SEARCH_API_PATH') . '/indexes';
$indexesListMeili = $ao_meili_search->curlRequest($uri, null, 'GET');

if (!$indexesListMeili) {
    throw new \Exception('Connexion with Meili Search server not found. You need to start Meili server and set the URL API correctly.');
}

foreach (json_decode($indexesListMeili, true) as $item) {
    $ao_meili_search->curlRequest($uri . '/' . $item['uid'], null, 'DELETE');
}

die('All indexes have been droped');