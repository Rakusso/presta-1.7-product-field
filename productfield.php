<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}


use Symfony\Component\Form\Extension\Core\Type\TextType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;

class Productfield extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'productfield';
        $this->tab = 'others';
        $this->version = '1.7.8';
        $this->author = 'Radke R';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('productfield');
        $this->description = $this->l('Produkt field for testing');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('PRODUCTFIELD_LIVE_MODE', false);

        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('displayAdminProductsMainStepLeftColumnMiddle') &&
            $this->registerHook('actionProductFormBuilderModifier') &&
            $this->registerHook('customFieldToProduct') &&
            $this->registerHook('actionProductSave') &&
            $this->registerHook('actionProductUpdate') ;
    }

    public function uninstall()
    {
        Configuration::deleteByName('PRODUCTFIELD_LIVE_MODE');

        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    // public function getContent()
    // {
    //     /**
    //      * If values have been submitted in the form, process.
    //      */
    //     if (((bool)Tools::isSubmit('submitProductfieldModule')) == true) {
    //         $this->postProcess();
    //     }

    //     $this->context->smarty->assign('module_dir', $this->_path);

    //     $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

    //     return $output.$this->renderForm();
    // }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    // protected function renderForm()
    // {
    //     $helper = new HelperForm();

    //     $helper->show_toolbar = false;
    //     $helper->table = $this->table;
    //     $helper->module = $this;
    //     $helper->default_form_language = $this->context->language->id;
    //     $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

    //     $helper->identifier = $this->identifier;
    //     $helper->submit_action = 'submitProductfieldModule';
    //     $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
    //         .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
    //     $helper->token = Tools::getAdminTokenLite('AdminModules');

    //     $helper->tpl_vars = array(
    //         'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
    //         'languages' => $this->context->controller->getLanguages(),
    //         'id_language' => $this->context->language->id,
    //     );

    //     return $helper->generateForm(array($this->getConfigForm()));
    // }

    /**
     * Create the structure of your form.
     */
    // protected function getConfigForm()
    // {
    //     return array(
    //         'form' => array(
    //             'legend' => array(
    //             'title' => $this->l('Settings'),
    //             'icon' => 'icon-cogs',
    //             ),
    //             'input' => array(
    //                 array(
    //                     'type' => 'switch',
    //                     'label' => $this->l('Live mode'),
    //                     'name' => 'PRODUCTFIELD_LIVE_MODE',
    //                     'is_bool' => true,
    //                     'desc' => $this->l('Use this module in live mode'),
    //                     'values' => array(
    //                         array(
    //                             'id' => 'active_on',
    //                             'value' => true,
    //                             'label' => $this->l('Enabled')
    //                         ),
    //                         array(
    //                             'id' => 'active_off',
    //                             'value' => false,
    //                             'label' => $this->l('Disabled')
    //                         )
    //                     ),
    //                 ),
    //                 array(
    //                     'col' => 3,
    //                     'type' => 'text',
    //                     'prefix' => '<i class="icon icon-envelope"></i>',
    //                     'desc' => $this->l('Enter a valid email address'),
    //                     'name' => 'PRODUCTFIELD_ACCOUNT_EMAIL',
    //                     'label' => $this->l('Email'),
    //                 ),
    //                 array(
    //                     'type' => 'password',
    //                     'name' => 'PRODUCTFIELD_ACCOUNT_PASSWORD',
    //                     'label' => $this->l('Password'),
    //                 ),
    //             ),
    //             'submit' => array(
    //                 'title' => $this->l('Save'),
    //             ),
    //         ),
    //     );
    // }

    /**
     * Set values for the inputs.
     */
    // protected function getConfigFormValues()
    // {
    //     return array(
    //         'PRODUCTFIELD_LIVE_MODE' => Configuration::get('PRODUCTFIELD_LIVE_MODE', true),
    //         'PRODUCTFIELD_ACCOUNT_EMAIL' => Configuration::get('PRODUCTFIELD_ACCOUNT_EMAIL', 'contact@prestashop.com'),
    //         'PRODUCTFIELD_ACCOUNT_PASSWORD' => Configuration::get('PRODUCTFIELD_ACCOUNT_PASSWORD', null),
    //     );
    // }

    /**
     * Save form data.
     */
    // protected function postProcess()
    // {
    //     $form_values = $this->getConfigFormValues();

    //     foreach (array_keys($form_values) as $key) {
    //         Configuration::updateValue($key, Tools::getValue($key));
    //     }
    // }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    // public function hookDisplayBackOfficeHeader()
    // {
    //     if (Tools::getValue('configure') == $this->name) {
    //         $this->context->controller->addJS($this->_path.'views/js/back.js');
    //         $this->context->controller->addCSS($this->_path.'views/css/back.css');
    //     }
    // }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    // public function hookHeader()
    // {
    //     $this->context->controller->addJS($this->_path.'/views/js/front.js');
    //     $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    // }

    // public function hookDisplayProductExtraContent($params)
    // {
    //     echo "ser";
    //     die();
    //     $productId = $params['id_product'];
    //     $formFactory = $this->get('form.factory');
    //     $twig = $this->get('twig');
    
    //     $product = new Product($productId);
    
    //     $form = $formFactory
    //       ->createNamedBuilder('seo_special_field', TextType::class, "")
    //       ->getForm();
    
    //     $template = '@Modules/productfield/views/templates/custom.html.twig';
    
    //     return $twig->render($template, [
    //       'seo_special_field' => $form->createView()
    //     ]);
      
    // }

    // public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params)
    public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params)
    {
        $productId = $params['id_product'];
        // $formBuilder = $params['form_builder'];
        // $formBuilder->add('custom_field', FormattedTextareaType::class,
        //     [
        //         'label' => $this->getTranslator()->trans('Dodatkowy opis', [], 'Modules.gmcatseconddesc.Admin'),
        //         'required' => false,
        //         'data' => $this->getDescription($productId),
        //     ]);
       
            //$params['data']['custom_field'] = $this->getDescription($productId);
        
        //$formBuilder->setData($params['data']);
        $formFactory = $this->get('form.factory');
        $twig = $this->get('twig');
        
        $product = new Product($productId);
        
        $form = $formFactory
        ->createNamedBuilder('seo_special_field', FormattedTextareaType::class, $this->getDescription($productId))
        ->getForm();
        
        $template = '@Modules/productfield/views/templates/admin/custom.html.twig';

        return $twig->render($template, [
        'seo_special_field' => $form->createView()
        ]);
      
    //   $form = $formFactory->createNamedBuilder('seo_special_field', FormattedTextareaType::class, "")
    //   ->getForm();
      
    //   $template = $this->getLocalPath().'views/templates/admin/custom.tpl';

    //   $this->context->smarty->assign(array(
    //       'seo_special_field' => $form->createView()
    //   ));
  
    //   return $this->display(__FILE__, 'views/templates/admin/custom.tpl');
    }

    public function hookActionProductSave($params)
    {
        $productId = $params['id_product'];
        $desc = Tools::getValue('seo_special_field');
        $this->storeDescription($productId, $desc);
    }

    public function hookActionProductUpdate($params)
    {
        $productId = $params['id_product'];
        $desc = Tools::getValue('seo_special_field');
        $this->storeDescription($productId, $desc);
    }

    protected function storeDescription($productId, $desc)
    {
        if ($this->exists($productId)) {
            Db::getInstance()->update('productfield', array('custom_field' => pSQL($desc, true),
            'date_upd' => date('Y-m-d H:i:s'),),
                '`id` = '.$productId);
        } else {
            Db::getInstance()->insert('productfield', 
                array(
                    'id' => $productId,
                    'custom_field' => pSQL($desc, true),
                    'date_add' => date('Y-m-d H:i:s'),
                    'date_upd' => date('Y-m-d H:i:s'),
            ));
        }
    }

    protected function exists($productId)
    {
        return ($this->getDescription($productId) !== false );
    }

    protected function getDescription($productId)
    {
        if ((int) $productId) {
            $result = Db::getInstance()->getValue('SELECT `custom_field` FROM `'._DB_PREFIX_.'productfield` WHERE `id` = '.$productId);
            return $result;
        }
        return false;
    }

    public function hookCustomFieldToProduct($params){
        
        $productId = $params['id_product'];
        $custom_field = $this->getDescription($productId);
        $twig = $this->get('twig');
        $template = '@Modules/productfield/views/templates/admin/custom-field.html.twig';

        return $twig->render($template, [
        'custom_field' => $custom_field
        ]);

    }
}
