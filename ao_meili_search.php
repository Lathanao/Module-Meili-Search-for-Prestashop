<?php
/*******************************************************************
 *          2020 Lathanao - Module for Prestashop
 *          Add a great module and modules on your great shop.
 *
 *          @author         Lathanao <welcome@lathanao.com>
 *          @copyright      2020 Lathanao
 *          @license        MIT (see LICENCE file)
 ********************************************************************/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

class ao_meili_search extends Module implements WidgetInterface
{
    public $values = array( 'SEARCH_MEILI_ACTIVE' => '1',
                            'SEARCH_API_URL' => 'http://127.0.0.1',
                            'SEARCH_API_PORT' => '700',
                            'SEARCH_LIMIT_PRODUCTS' => '6',
                            'SEARCH_LIMIT_CATEGORY' => '2',
                            'SEARCH_UID_PRODUCT' => '',
                            'SEARCH_UID_CATEGORY' => '',
                            'SEARCH_INPUT_MSG' => array(
                                '1' => 'Enter your search key ...',
                                '2' => 'Recherchez un produit'),
                            );

    /**
     * @var bool|string
     */
    private $indexListMeili;
    /**
     * @var array
     */
    private $templateFile;


    public function __construct()
    {
        $this->name      = 'ao_meili_search';
        $this->tab       = 'front_office_features';
        $this->author    = 'Lathanao';
        $this->version   = '1.1.0';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Meili Search', array(), 'Modules.' . $this->name . '.Admin');
        $this->description = $this->trans('Speed up your research on online shop with an instant engine.', array(), 'Modules.' . $this->name . '.Admin');
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->controllers = array('ajax');

        $this->templateFile = array(
            'displayTop' => 'module:' . $this->name . '/views/templates/frontend/ao_search_displayTop.tpl');
    }

    public function install()
    {
        $this->checkSetup();
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
        Media::addJsDef(array('UidIndexSearchProducts' => Configuration::get('SEARCH_UID_PRODUCT')));
        Media::addJsDef(array('UidIndexSearchCategories' => Configuration::get('SEARCH_UID_CATEGORY')));

        Media::addJsDef(array('SearchLimitItem' =>  Configuration::get('SEARCH_LIMIT_ITEM', $this->context->language->id)));

        Media::addJsDef(array("SearchUrl" => $this->context->link->getModuleLink($this->name, 'ajax', array(), null, null, null, true)));
        Media::addJsDef(array("DirectSearchUrl" => $this->context->link->getModuleLink($this->name, 'ajax', array(), null, null, null, true)));

        Media::addJsDef(array("SearchWaitMsg" => Configuration::get('SEARCH_WAIT_MSG', $this->context->language->id)));
        Media::addJsDef(array("SearchErrorMsg" => Configuration::get('SEARCH_ERROR_MSG', $this->context->language->id)));


        Media::addJsDef(array("URL_API_MEILI" => Configuration::get('SEARCH_API_URL') . ':' . Configuration::get('SEARCH_API_PORT')));


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
                'description' => $this->trans('You must too install a Meili server on your server.', array(), 'Modules.' . $this->name . '.Admin'),
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
                            'type' => 'text',
                            'label' => $this->trans('Api Url to connect with Meili', array(), 'Modules.' . $this->name . '.Admin'),
                            'name' => 'SEARCH_API_URL',
                            'desc' => $this->trans('Without any specific setup, it\'s http://127.0.0.1', array(), 'Modules.' . $this->name . '.Admin'),
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->trans('Port', array(), 'Modules.' . $this->name . '.Admin'),
                            'name' => 'SEARCH_API_PORT',
                            'desc' => $this->trans('Without any specific setup, it\'s will be 7700.', array(), 'Modules.' . $this->name . '.Admin'),
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

        $generateForm = $helper->generateForm(array($fields_form));

        $moduleUrl = Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUri();
        $controllerUrl = $moduleUrl . 'controllers/';
        $token = substr(Tools::encrypt($this->name . '/cron'), 0, 10);

        $this->context->smarty->assign([
            'uri' => $this->getPathUri(),
            'products_indexer' => $controllerUrl . $this->name . '_index_products.php' . '?token=' . $token,
            'categories_indexer' => $controllerUrl . $this->name . '_index_categories.php' . '?token=' . $token,
            'drop_indexes' => $controllerUrl . $this->name . '_drop_all.php' . '?token=' . $token,
        ]);

        if(Configuration::get('SEARCH_UID_PRODUCT')) {
            $url = Configuration::get('SEARCH_API_URL') . ':' . Configuration::get('SEARCH_API_PORT');
            $this->context->smarty->assign([
                'products_indexer_info' => $url . '/indexes/' . Configuration::get('SEARCH_UID_PRODUCT'),
                'products_indexer_documents' => $url . '/indexes/' . Configuration::get('SEARCH_UID_PRODUCT') . '/documents',
                'products_indexer_stats' => $url . '/stats/' . Configuration::get('SEARCH_UID_PRODUCT'),
            ]);
        }

        if(Configuration::get('SEARCH_UID_CATEGORY')) {
            $url = Configuration::get('SEARCH_API_URL') . ':' . Configuration::get('SEARCH_API_PORT');
            $this->context->smarty->assign([
                'categories_indexer_info' => $url . '/indexes/' . Configuration::get('SEARCH_UID_CATEGORY'),
                'categories_indexer_documents' => $url . '/indexes/' . Configuration::get('SEARCH_UID_CATEGORY') . '/documents',
                'categories_indexer_stats' => $url . '/stats/' . Configuration::get('SEARCH_UID_CATEGORY'),
            ]);
        }
        $generateOverride = $this->display(__FILE__, 'views/templates/admin/manage.tpl');

        return $generateForm.$generateOverride;
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if (!Configuration::get('SEARCH_MEILI_ACTIVE') || !isset($this->templateFile[$hookName]) ) {
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

    private function installValues($html = null)
    {
        foreach ($this->values as $key => $value) {
            if (!Configuration::updateValue($key, $value, $html)) {
                if (_PS_MODE_DEV_) {
                    throw new \Exception('Error while update value $key');
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
                !Configuration::hasKey($key, $this->context->language->id, null, $this->context->shop->id) &&
                !Configuration::updateValue($key, $value, $html) &&
                _PS_MODE_DEV_) {
                    throw new \Exception(" Value : $key updated, just reload the page for checking next values");
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
            }
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

    public function getSetup($Multilang = null)
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

    public function getUidIndexMieli($index = 'products')
    {
        $result = '';
        $uri = $this->values['SEARCH_API_URL'] . '/indexes';

        $this->indexListMeili = $this->curlRequest($uri, null, 'GET');

        if (!$this->indexListMeili) {
            $this->isProblemConnexionDetected = true;
            throw new \Exception('Connexion with Meili Search server not found. You need to start Meili server and set the URL API correctly.');
        }

        foreach (json_decode( $this->indexListMeili, true) as $item) {
            if ($item['name'] === $index) {
                Configuration::updateValue('SEARCH_UID_INDEX_PRODUCTS', $item['uid']);
                $result = $item['uid'];
            }
        }

        if (!$result && _PS_MODE_DEV_) {
            $this->isProblemConnexionDetected = true;
            throw new \Exception('Index ' . $index . ' in Meili Search server not found. You need to set indexes.');
        }

        return false;
    }

    public function testMieliSearchApi($index = 'products')
    {
        $uri = $this->values['SEARCH_API_URL'] . '/indexes';

        $resulTestMeili = $this->curlRequest($uri, null, 'GET');

        if ($resulTestMeili) {
            return true;
        }
        return true;

    }


    public function curlRequest($uri, $data = null, $method = 'POST')
    {
        $curlObj = curl_init();
        $options = [
            CURLOPT_URL => $uri,
            CURLOPT_PORT => Configuration::get('SEARCH_API_PORT'),
            CURLOPT_HTTPHEADER => ['content-type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method
        ];
        if (!empty($data)) {
            $options[CURLOPT_POSTFIELDS] = $data;
        }
        if (stripos($uri, 'https://') === 0) {
            $options[CURLOPT_SSL_VERIFYHOST] = 2;
            $options[CURLOPT_SSL_VERIFYPEER] = true;
        }

        curl_setopt_array($curlObj, $options);

        if (curl_errno($curlObj)) {
            $returnData = curl_error($curlObj);
        } else {
            $returnData = curl_exec($curlObj);
        }
        echo $uri . PHP_EOL;
        echo $data . PHP_EOL;
        echo $method . PHP_EOL;
        echo $returnData . PHP_EOL;
        var_dump($options) . PHP_EOL;
        return $returnData;
    }
}
