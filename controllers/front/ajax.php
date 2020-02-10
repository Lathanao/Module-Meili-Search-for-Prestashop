<?php
/**
 *       Visio Modules for Visio template
 *
 * @author         Lathanao <welcome@lathanao.com>
 * @copyright      2018 Lathanao
 * @license        OSL-3
 * @version        1.0
 **/

class ao_searchAjaxModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        $searchString = Tools::replaceAccentedChars(Tools::getValue('queryString'));

        ob_end_clean();
        header('content-type text/html charset=utf-8');

        if ($searchString)
            die ($this->module->AjaxSearch($searchString));
        else
            die(json_encode([
                'error' => "something wrong happend",
            ]));
    }
}
