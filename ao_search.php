<?php
/**
 *       Visio Modules for Visio template
 *
 * @author         Lathanao <welcome@lathanao.com>
 * @copyright      2018 Lathanao
 * @license        OSL-3
 * @version        1.0
 **/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

define('_URL_', 'http://localhost');
define('_PORT_', '7700');

class ao_search extends Module implements WidgetInterface
{
    public $values = array('SEARCH_ACTIVE' => '1',
        'SEARCH_MEILI_ACTIVE' => '1',
        'SEARCH_TITLE' => array('1' => '',
            '2' => ''),
        'SEARCH_INPUT_MSG' => array('1' => 'Enter your search key ...',
                                    '2' => 'Recherchez un produit'),
        'SEARCH_WAIT_MSG' => array('1' => 'Loading...',
                                     '2' => 'Recherche en cours...'),
        'SEARCH_ERROR_MSG' => array('1' => 'Sorry, no results founded for this search.',
            '2' => 'Désolé, la recherche n\'a retourné aucun résultat'),
        'SEARCH_LINK' => array('1' => 'read more',
            '2' => 'lire la suite'),
        'SEARCH_TRUNC_DESC' => '50',
        'SEARCH_TRUNC_TITLE' => '50',
        'SEARCH_ORDERWAY' => 'position',
        'SEARCH_ORDERBY' => 'desc',
        'SEARCH_LIMIT_ITEM' => '10',
        'SEARCH_SHOW_VIEW' => '1',
        'SEARCH_SHOW_DESC' => '1',
        'SEARCH_SHOW_PRICE' => '1',
        'SEARCH_UID_INDEX_PRODUCTS' => '',
        'SEARCH_UID_INDEX_CATEGORIES' => ''
        );

    public $templateFile = array(
        'displayTop' => 'module:ao_search/views/ao_search_displayTop.tpl',
        'displaySearchClean' => 'module:ao_search/views/ao_search_displaySearchClean.tpl',
        'displayNotFound' => 'module:ao_search/views/ao_search_displayNotFound.tpl',
        'displayMobile' => 'module:ao_search/views/ao_search_mobile.tpl');
    /**
     * @var bool
     */

    public function __construct()
    {
        $this->name      = 'ao_search';
        $this->tab       = 'front_office_features';
        $this->author    = 'Lathanao';
        $this->version   = '1.1.0';
        $this->bootstrap = true;

        parent::__construct();

        $this->orderBy = array('0' => array('name' => 'asc'),
            '1' => array('name' => 'desc'));

        $this->orderWay = array('0' => array('name' => 'position'),
            '1' => array('name' => 'date_add'),
            '2' => array('name' => 'quantity'),
            '3' => array('name' => 'price'));

        $this->displayName = $this->trans('Search - visio', array(), 'Modules.' . $this->name . '.Admin');
        $this->description = $this->trans('Speed up your research on online shop.', array(), 'Modules.' . $this->name . '.Admin');
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
        $this->controllers = array('ajax');
    }

    public function install()
    {
        return parent::install()
            && $this->installValues()
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayTop')
            && $this->registerHook('displayNotFound');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function hookdisplayHeader($params)
    {
        Media::addJsDef(array('UidIndexSearchProducts' => $this->getUidIndexProducts()));
        Media::addJsDef(array('UidIndexSearchCategories' => $this->getUidIndexCategories()));
        Media::addJsDef(array('SearchLimitItem' =>  Configuration::get('SEARCH_LIMIT_ITEM', $this->context->language->id)));
        Media::addJsDef(array("SearchUrl" => $this->context->link->getModuleLink($this->name, 'ajax', array(), null, null, null, true)));
        Media::addJsDef(array("SearchWaitMsg" => Configuration::get('SEARCH_WAIT_MSG', $this->context->language->id)));
        Media::addJsDef(array("SearchErrorMsg" => Configuration::get('SEARCH_ERROR_MSG', $this->context->language->id)));


        $this->context->controller->registerStylesheet(
            'modules-css-' . $this->name,
            'modules/' . $this->name . '/css/' . $this->name . '.css',
            ['media' => 'all', 'priority' => 800]
        );

        $this->context->controller->registerJavascript(
            'modules-js-' . $this->name,
            'modules/' . $this->name . '/js/' . $this->name . '.js',
            ['position' => 'bottom', 'priority' => 800]
        );
        $this->context->controller->registerJavascript(
            'modules-js-meili-' . $this->name,
            'modules/' . $this->name . '/js/ao_meilisearch.js',
            ['position' => 'bottom', 'priority' => 800]
        );
    }

    public function getContent()
    {
        $this->checkSetup();
        $this->setSetup();
        return $this->renderForm();
    }

    public function renderForm()
    {
        $this->registerHook('displayTop');
        $fields_form = array(
            'form' => array(
                'legend' => array('title' => $this->trans('Settings', array(), 'Admin.Global'), 'icon' => 'icon-cogs'),
                'description' => $this->trans('You can modify the image size in image configurator (improve -> design -> image settings) (default size (50px)).', array(), 'Modules.' . $this->name . '.Admin'),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->trans('Activate module', array(), 'Admin.Global'),
                        'name' => array_keys($this->values)[0],
                        'required' => false,
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', array(), 'Admin.Global')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', array(), 'Admin.Global')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->trans('Activate module', array(), 'Admin.Global'),
                        'name' => 'SEARCH_MEILI_ACTIVE',
                        'required' => false,
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', array(), 'Admin.Global')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', array(), 'Admin.Global')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Title of the block search', array(), 'Modules.' . $this->name . '.Admin'),
                        'name' => 'SEARCH_TITLE',
                        'desc' => $this->trans('For displaying nothing, leave this field blank.', array(), 'Modules.' . $this->name . '.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Message for input query', array(), 'Modules.' . $this->name . '.Admin'),
                        'name' => 'SEARCH_INPUT_MSG',
                        'desc' => $this->trans('choose message for input query.', array(), 'Modules.' . $this->name . '.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Message for waiting during searching', array(), 'Modules.' . $this->name . '.Admin'),
                        'name' => 'SEARCH_WAIT_MSG',
                        'desc' => $this->trans('Choose message for waiting during searching.', array(), 'Modules.' . $this->name . '.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Message when no result return', array(), 'Modules.' . $this->name . '.Admin'),
                        'name' => 'SEARCH_ERROR_MSG',
                        'desc' => $this->trans('Choose message when no result return.', array(), 'Modules.' . $this->name . '.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Link after truncate description', array(), 'Modules.' . $this->name . '.Admin'),
                        'name' => 'SEARCH_LINK',
                        'desc' => $this->trans('For displaying nothing, leave this field blank.', array(), 'Modules.' . $this->name . '.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Number max of results to display', array(), 'Modules.' . $this->name . '.Admin'),
                        'name' => 'SEARCH_LIMIT_ITEM',
                        'desc' => $this->trans('Choose message for waiting during searching.', array(), 'Modules.' . $this->name . '.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Desciption truncate length', array(), 'Modules.' . $this->name . '.Admin'),
                        'name' => 'SEARCH_TRUNC_TITLE',
                        'desc' => $this->trans('Choose 50 for truncate the product\'s title after 50 caracteres.', array(), 'Modules.' . $this->name . '.Admin'),
                        'validation' => 'isInt',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Title truncate length', array(), 'Modules.' . $this->name . '.Admin'),
                        'name' => 'SEARCH_TRUNC_DESC',
                        'desc' => $this->trans('Choose 50 for truncate the product\'s short description after 50 caracteres.', array(), 'Modules.' . $this->name . '.Admin'),
                        'validation' => 'isInt',
                    ),
                    array(
                        'name' => 'SEARCH_ORDERWAY',
                        'type' => 'select',
                        'label' => $this->trans('Sort result', array(), 'Modules.' . $this->name . '.Admin'),
                        'desc' => $this->trans('Choose the kind of result\'s sort to display', array(), 'Modules.' . $this->name . '.Admin'),
                        'options' => array(
                            'query' => $this->orderWay,
                            'id' => 'name',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'name' => 'SEARCH_ORDERBY',
                        'type' => 'select',
                        'label' => $this->trans('Direction of sorting', array(), 'Modules.' . $this->name . '.Admin'),
                        'desc' => $this->trans('You can choose acsendant or descendant', array(), 'Modules.' . $this->name . '.Admin'),
                        'options' => array(
                            'query' => $this->orderBy,
                            'id' => 'name',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->trans('Show descrition on results popup', array(), 'Modules.' . $this->name . '.Admin'),
                        'name' => 'SEARCH_SHOW_DESC',
                        'required' => false,
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', array(), 'Admin.Global')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', array(), 'Admin.Global')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->trans('Show price on results popup', array(), 'Modules.' . $this->name . '.Admin'),
                        'name' => 'SEARCH_SHOW_PRICE',
                        'required' => false,
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', array(), 'Admin.Global')),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', array(), 'Admin.Global'))),

                    ),
                ),
                'submit' => array(
                    'title' => $this->trans('Save', array(), 'Admin.Global'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->default_form_language = $this->context->language->id;
        $helper->show_toolbar = true;
        $helper->submit_action = 'submitForm';
        $helper->tpl_vars = array(
            'fields_value' => $this->getSetup(true),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if ($hookName === null && isset($configuration['hook'])) {
            $hookName = $configuration['hook'];
        }

        if (!Configuration::get('SEARCH_ACTIVE')) {
            return false;
        }

        if (!$this->isCached($this->templateFile[$hookName], $this->getCacheId())) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }

        return $this->fetch($this->templateFile[$hookName], $this->getCacheId());
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        return array(
            'setup' => $this->getSetup(false),
            'search_controller_url' => $this->context->link->getPageLink('search', null, null, null, false, null, true),
            'SearchUrl' => $this->context->link->getModuleLink($this->name, 'ajax', array(), null, null, null, true),
        );
    }

    public function AjaxSearch($searchString)
    {
        $context = Context::getContext();

        $serp = Search::find(
            Context::getContext()->language->id,
            $searchString,
            1 /*page_number*/,
            Configuration::get('SEARCH_LIMIT_ITEM') /*page_size*/,
            Configuration::get('SEARCH_ORDERWAY') /*position*/,
            Configuration::get('SEARCH_ORDERBY') /*desc*/,
            $ajax = false,
            $use_cookie = true,
            $context
        );

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

        $this->smarty->assign($this->getSetup(false));
        $this->smarty->assign(array('products' => $products_for_template));
        return $this->display(dirname(__FILE__) . '/ao_search.php', 'views/ao_search_response.tpl');
    }

    private function installValues($html = null)
    {
        foreach ($this->values as $key => $value) {
            if (!Configuration::updateValue($key, $value, $html)) {
                if (_PS_MODE_DEV_) {
                    die('Error while update value $key');
                } else {
                    return false;
                }
            }
        }
        return true;
    }

    private function uninstallValues()
    {
        foreach ($this->values as $key => $value) {
            if (!Configuration::deleteByName($key, $value)) {
                if (_PS_MODE_DEV_) {
                    die('Error while delete value $key');
                }

                return false;
            }
        }
        return true;
    }



    public function checkSetup($html = null)
    {
        foreach ($this->values as $key => $value) {
            if (!Configuration::hasKey($key) &&
                !Configuration::hasKey($key, null, null, $this->context->shop->id) &&
                !Configuration::hasKey($key, $this->context->language->id) &&
                !Configuration::hasKey($key, $this->context->language->id, null, $this->context->shop->id)) {
                if (!Configuration::updateValue($key, $value, $html) && _PS_MODE_DEV_) {
                    die(' Value : $key updated, just reload the page for checking next values');
                }
            }
        }

        return $this->values;
    }

    public function setSetup(array $newSetup = null /*from backup*/)
    {
        $updated = []; // will return value for ajax callback
        $newSetup || $newSetup = $_POST;

        foreach ($newSetup as $key => $value) {
            if (array_key_exists($key, $this->values)) {
                Configuration::updateValue($key, $value, true /*HTML*/) && $updated[$key] = $value;
            } //value couls be an array from backup or a srting fo from form
            elseif (array_key_exists(substr($key, 0, -2), $this->values)) { // in case of form with multi lang like TEXT_2
                Configuration::updateValue(substr($key, 0, -2), array(substr($key, -1) => $value), true) && $updated[$key] = $value;
            }
        }

        foreach ($newSetup as $key => $value) {
            if (isset(array_keys($this->values)[0]) && $key === array_keys($this->values)[0]) {
                $value ? $this->enable() : $this->disable();
            }
        }

        if (isset($this->_html)) {
            $this->_html .= $this->displayConfirmation($this->trans('Settings updated.', array(), 'Admin.Notifications.Success'));
        }

        if (!isset($this->templateFile)) {
            return $updated;
        }

        if (is_array($this->templateFile)) {
            foreach ($this->templateFile as $template) {
                $this->_clearCache($template);
            }
        } else {
            $this->_clearCache((string)$this->templateFile);
        }

        return $updated;
    }

    public function getSetup($Multilang = null /*Need multilang for admin, no need for front */)
    {
        foreach ($this->values as $key => $value) {
            if ($Multilang && is_array($value)) {
                foreach (Language::getLanguages(false) as $lang) {
                    $this->values[$key][$lang['id_lang']] = Configuration::get($key, $lang['id_lang']);
                }
            } else {
                $this->values[$key] = Configuration::get($key, $this->context->language->id);
            }
        }

        return $this->values;
    }

    public function getUidIndexCategories($html = null)
    {
//        if(Configuration::get('SEARCH_UID_INDEX_CATEGORIES')) {
//            return Configuration::get('SEARCH_UID_INDEX_CATEGORIES');
//        }

        $uri = 'http://127.0.0.1/indexes';
        foreach (json_decode($this->curlRequest($uri, null, 'GET'), true) as $item) {
            if ($item['name'] === 'categories') {
                Configuration::updateValue('SEARCH_UID_INDEX_CATEGORIES', $item['uid']);
                return $item['uid'];
            }
        }
        return false;
    }

    public function getUidIndexProducts($html = null)
    {
//        if(Configuration::get('SEARCH_UID_INDEX_PRODUCTS')) {
//            return Configuration::get('SEARCH_UID_INDEX_PRODUCTS');
//        }

        $uri = 'http://127.0.0.1/indexes';
        foreach (json_decode($this->curlRequest($uri, null, 'GET'), true) as $item) {
            if ($item['name'] === 'products') {
                Configuration::updateValue('SEARCH_UID_INDEX_PRODUCTS', $item['uid']);
                return $item['uid'];
            }
        }
        return false;
    }

    public function getUidIndexSearch($html = null)
    {
        $uri = 'http://127.0.0.1/indexes';
        return $this->curlRequest($uri, null, 'GET');
        return false;
    }


    public function curlRequest($uri, $data = null, $method = 'POST')
    {
        echo PHP_EOL . 'star method curlRequest()';
        echo PHP_EOL . $uri;

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

}
