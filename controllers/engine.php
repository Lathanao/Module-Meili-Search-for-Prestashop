<?php
/****************************************************************/
/**
 *          Beautiful Blog Notification or Prestashop
 *          Add some beautiful Blog, where and when you want on your shop
 *
 * @author         Lathanao <welcome@lathanao.com>
 * @copyright      2017 Lathanao
 * @version        1.0
 * @license        Commercial license see README.md
 ********************************************************************/

require_once(dirname(__FILE__) . '/../../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../../init.php');
require_once(dirname(__FILE__) . '/../visio_livesearch.php');

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

$searchString = Tools::replaceAccentedChars($_REQUEST['queryString']);
$context = Context::getContext();
$serp = Search::find(Context::getContext()->language->id,
    $searchString,
    1 /*page_number*/,
    Configuration::get('LIVESEARCH_LIMIT_ITEM') /*page_size*/,
    Configuration::get('LIVESEARCH_ORDERWAY') /*position*/,
    Configuration::get('LIVESEARCH_ORDERBY') /*desc*/,
    $ajax = false,
    $use_cookie = true,
    $context);

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

foreach ($serp['result'] as &$rawProduct) {

    $products_for_template[] = $presenter->present(
        $presentationSettings,
        $assembler->assembleProduct($rawProduct),
        $context->language
    );
}

$livesearch = new Btf_livesearch();
Context::getContext()->smarty->assign($livesearch->getSetup());
Context::getContext()->smarty->assign(array('products' => $products_for_template,));
echo $livesearch->display(dirname(__FILE__) . '/visio_livesearch.php', 'views/btf_livesearch_response.tpl');
