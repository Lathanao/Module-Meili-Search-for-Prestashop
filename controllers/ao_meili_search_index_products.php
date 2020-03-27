<?php
/*******************************************************************
 *          2020 Lathanao - Module for Prestashop
 *          Add a great module and modules on your great shop.
 *
 * @author         Lathanao <welcome@lathanao.com>
 * @copyright      2020 Lathanao
 * @license        MIT (see LICENCE file)
 ********************************************************************/

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;

include(dirname(__FILE__) . '/../../../config/config.inc.php');
include(dirname(__FILE__) . '/../../../init.php');

header('Content-Type: application/json; charset=utf-8');
//header('Content-type: text/plain; charset=utf-8');

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

/* Bring back module instance to use method */
$ao_meili_search = Module::getInstanceByName('ao_meili_search');

if (!$ao_meili_search->active) {
    die('Module Inactive');
}

/* Create collection then check result */
if ($resultImport = ao_product_collection_create()) {
    echo 'Type index : ' . json_decode($resultImport, true)['name'] . PHP_EOL;
    echo 'Uid index  : ' . json_decode($resultImport, true)['uid'] . PHP_EOL;
    Configuration::updateValue('SEARCH_UID_PRODUCT', json_decode($resultImport, true)['uid']);
} else {
    die('Product index not created. Meili API not respond');
}

/* Import product collection */
if (ao_products_collection_import()) {
    echo PHP_EOL . 'Import product done in index ' . Configuration::get('SEARCH_UID_PRODUCT');
} else {
    echo PHP_EOL . 'Import product fail';
}

die();


function ao_product_collection_create()
{
    if (!Configuration::get('SEARCH_API_PATH')) {
        throw new Exception('Port API path Meili must be set.');
    }

    $uri = Context::getContext()->link->getBaseLink() . Configuration::get('SEARCH_API_PATH') . '/indexes';

    $ao_meili_search = Module::getInstanceByName('ao_meili_search');
    $data = '{
          "name": "' . _COLLECTION_ . '",
          "schema": {
            "id": ["identifier", "indexed", "displayed"],
            "id_category_default": ["indexed", "displayed"],
            "id_shop_default": ["indexed", "displayed"],
            "manufacturer_name": ["indexed", "displayed"],
            "supplier_name": ["indexed", "displayed"],
            "name": ["indexed", "displayed"],
            "description": ["indexed", "displayed"],
            "description_short": ["indexed", "displayed"],
            "quantity": ["indexed", "displayed"],
            "price": ["indexed", "displayed"],
            "specificPrice": ["indexed", "displayed"],
            "on_sale": ["indexed", "displayed"],
            "online_only": ["indexed", "displayed"],
            "unity": ["indexed", "displayed"],
            "unit_price": ["indexed", "displayed"],
            "reference": ["indexed", "displayed"],
            "ean13": ["indexed", "displayed"],
            "isbn": ["indexed", "displayed"],
            "upc": ["indexed", "displayed"],
            "mpn": ["indexed", "displayed"],
            "link_rewrite": ["indexed", "displayed"],
            "meta_description": ["indexed", "displayed"],
            "meta_keywords": ["indexed", "displayed"],
            "meta_title": ["indexed", "displayed"],
            "quantity_discount": ["indexed", "displayed"],
            "customizable": ["indexed", "displayed"],
            "new": ["indexed", "displayed"],
            "active": ["indexed", "displayed"],
            "available_for_order": ["indexed", "displayed"],
            "category": ["indexed", "displayed"],
            "link": ["indexed", "displayed"],
            "link_image": ["indexed", "displayed"]
          }
        }';

//    allow_oosp
    return $ao_meili_search->curlRequest($uri, $data);
}

function ao_products_collection_import()
{

    $fieldsToKeep = array(
        'id',
        'id_category_default',
        'id_shop_default',
        'manufacturer_name',
        'supplier_name',
        'name',
        'description',
        'description_short',
        'quantity',
        'price',
        'specificPrice',
        'on_sale',
        'online_only',
        'unity',
        'unit_price',
        'reference',
        'ean13',
        'isbn',
        'upc',
        'link_rewrite',
        'meta_description',
        'meta_keywords',
        'meta_title',
        'quantity_discount',
        'customizable',
        'new',
        'active',
        'available_for_order',
        'category',
        'link',
        'link_image',
    );

    $ao_meili_search = Module::getInstanceByName('ao_meili_search');

    $uri = Context::getContext()->link->getBaseLink() . Configuration::get('SEARCH_API_PATH') . '/indexes/';
    $uri .= Configuration::get('SEARCH_UID_PRODUCT') . '/documents';

    $context = Context::getContext();

    $assembler = new ProductAssembler($context);
    $presenterFactory = new ProductPresenterFactory($context);
    $presentationSettings = $presenterFactory->getPresentationSettings();
    $presenter = new ProductListingPresenter(
        new ImageRetriever(
            $context->link
        ),
        $context->link,
        new PriceFormatter(),
        new ProductColorsRetriever(),
        $context->getTranslator()
    );

    $products_for_template = [];

    foreach (getAllProductIds(true) as $key => &$rawProduct) {

        $rawProduct = $presenter->present(
            $presentationSettings,
            $assembler->assembleProduct($rawProduct),
            $context->language
        );

        $rawProduct['link_image'] = $rawProduct["cover"]["bySize"]["cart_default"]["url"];
        $rawProduct['description'] = Tools::getDescriptionClean($rawProduct['description']);
        $rawProduct['description_short'] = Tools::getDescriptionClean($rawProduct['description_short']);

        $productToImport = [];
        foreach ($fieldsToKeep as $item) {
            $productToImport[$item] = $rawProduct[$item];
        }
        $ao_meili_search->curlRequest($uri, '[' . json_encode($productToImport) . ']');
        $rawProduct = null;
    }

    return true;
}

function getAllProductIds($active = false)
{
    $query = new DbQuery();
    $query->select('p.id_product');
    $query->from('product', 'p');
    $query->innerJoin('product_lang', 'pl', 'p.id_product = pl.id_product');
    $query->innerJoin('product_shop', 'ps', 'p.id_product = ps.id_product');
    if ($active) {
        $query->where('p.active = 1');
    }
    $query->where('pl.id_lang = ' . (int)Context::getContext()->language->id);
    $query->where('pl.id_shop = ' . (int)Context::getContext()->shop->id);

    return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
}