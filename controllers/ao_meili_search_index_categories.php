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

//header('Content-type = text/html');
header('Content-type: text/plain; charset=utf-8');

ini_set('display_errors', 1);
ini_set('track_errors', 1);
ini_set('html_errors', 1);
ini_set('realpath_cache_size', '5M');
ini_set('max_execution_time', 60000);
error_reporting(E_ALL);

define('_COLLECTION_', 'categories');

/* Check security token */
if (!Tools::isPHPCLI()) {
    if (Tools::substr(Tools::encrypt('ao_meili_search/cron'), 0, 10) !== Tools::getValue('token') || !Module::isInstalled('ao_meili_search')) {
        die('Bad token');
    }
}

$ao_meili_search = Module::getInstanceByName('ao_meili_search');

if (!$ao_meili_search->active) {
    die('Module Inactive');
}

if ($resultImport = ao_category_collection_create()) {
    echo 'Type index : ' . json_decode($resultImport, true)['name'] . PHP_EOL;
    echo 'Uid index  : ' . json_decode($resultImport, true)['uid'] . PHP_EOL;
    Configuration::updateValue('SEARCH_UID_CATEGORY', json_decode($resultImport, true)['uid']);
} else {
    die('Category index not created. Meili API not respond');
}

if (ao_category_collection_import()) {
    die('Import category done in index ' . Configuration::get('SEARCH_UID_CATEGORY'));
} else {
    die('Import category fail');
}

function ao_category_collection_create()
{
    if (!Configuration::get('SEARCH_API_PATH')) {
        throw new \Exception('Port API server Meili must be set.');
    }

    $uri = Context::getContext()->link->getBaseLink() . Configuration::get('SEARCH_API_PATH') . '/indexes';

    $ao_meili_search = Module::getInstanceByName('ao_meili_search');
    $data = '{
          "name": "' . _COLLECTION_ . '",
          "schema": {
            "id":   ["identifier", "indexed", "displayed"],
            "name": ["indexed", "displayed"],
            "description": ["indexed", "displayed"],
            "link":       ["indexed", "displayed"],
            "link_image": ["indexed", "displayed"],
            "meta_title": ["indexed", "displayed"],
            "meta_keywords":    ["indexed", "displayed"],
            "meta_description": ["indexed", "displayed"],
            "active":           ["indexed", "displayed"]
          }
        }';

    return $ao_meili_search->curlRequest($uri, $data);
}

function ao_category_collection_import()
{

    $ao_meili_search = Module::getInstanceByName('ao_meili_search');

    $uri = Context::getContext()->link->getBaseLink() . Configuration::get('SEARCH_API_PATH') . '/indexes/';
    $uri .= Configuration::get('SEARCH_UID_CATEGORY') . '/documents';
    $context = Context::getContext();
    $link = Context::getContext()->link;
    $idLang = Context::getContext()->language->id;
    $idShop = Context::getContext()->shop->id;

    foreach (Category::getAllCategoriesName() as $key => &$rawCategory) {

        $category = (array)new Category($rawCategory['id_category'], $idLang, $idShop);

        $rawCategory['id'] = $category['id_category'];
        $rawCategory['name'] = $category['name'];
        $rawCategory['description'] = Tools::getDescriptionClean($category['description']);
        $rawCategory['link'] = $link->getCategoryLink((int)$category['id_category'], $category['link_rewrite'], (int)$idLang);
        $rawCategory['meta_title'] = $category['meta_title'];
        $rawCategory['meta_keywords'] = $category['meta_keywords'];
        $rawCategory['meta_description'] = $category['meta_description'];
        $rawCategory['active'] = $category['active'];

        if (file_exists(_PS_CAT_IMG_DIR_ . (int)$category['id_category'] . '-0_thumb.jpg')) {
            $rawCategory['link_image'] = $link->getCatImageLink($category['name'], $category['id_category'], '0_thumb');
        } else {
            $rawCategory['link_image'] = '';
        }

        $ao_meili_search->curlRequest($uri, '[' . json_encode($rawCategory) . ']');
        $rawCategory = null;
    }
    return true;
}
