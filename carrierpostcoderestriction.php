<?php
/**
 * 2007-2025 PrestaShop
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
 *  @copyright 2007-2025 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Carrierpostcoderestriction extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'carrierpostcoderestriction';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'dewwwe';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Carrier Postcode Restriction', [], 'Modules.Carrierpostcoderestriction.Admin');
        $this->description = $this->trans('Restrict carrier options according to delivery address postcode', [], 'Modules.Carrierpostcoderestriction.Admin');

        // 1.7.6 because using the new translation system
        $this->ps_versions_compliancy = array('min' => '1.7.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        // Configuration::updateValue('CARRIERPOSTCODERESTRICTION_LIVE_MODE', false);

        include(dirname(__FILE__) . '/sql/install.php');

        return parent::install()
            // Add JS & CSS to front office
            && $this->registerHook('header')
            // Add JS @ CSS to back office
            && $this->registerHook('displayBackOfficeHeader')
            // Filter the carrier options in the front office
            && $this->registerHook('actionFilterDeliveryOptionList')
            // Others let's see
            // && $this->registerHook('actionCarrierProcess')
            // && $this->registerHook('actionCarrierUpdate')
            // && $this->registerHook('displayBeforeCarrier')
            // && $this->registerHook('displayCarrierExtraContent')
            // Install the quick access tab in the back office
            && $this->installTab();
    }

    public function uninstall()
    {
        // Configuration::deleteByName('CARRIERPOSTCODERESTRICTION_LIVE_MODE');

        include(dirname(__FILE__) . '/sql/uninstall.php');

        // Uninstall tabs
        $this->uninstallTab();

        return parent::uninstall();
    }

    /**
     * Use new translation system
     * @return bool
     */
    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    /**
     * Create admin tab for quick access
     */
    private function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminCarrierPostcodeRestriction';
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->trans('Postcode Restriction', [], 'Modules.Carrierpostcoderestriction.Admin', $lang['locale']);
        }

        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentShipping');
        $tab->module = $this->name;

        return $tab->add();
    }

    /**
     * Remove admin tab
     */
    private function uninstallTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminCarrierPostcodeRestriction');

        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }

        return true;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = '';

        /**
         * If values have been submitted in the form, process.
         */
        if (((bool) Tools::isSubmit('submitCarrierpostcoderestrictionModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    public function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCarrierpostcoderestrictionModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        // Get the form structure
        $form = $this->getConfigForm();

        // Process custom field types
        foreach ($form['form']['input'] as &$input) {
            if ($input['type'] == 'carrier_list') {
                $input['html_content'] = $this->renderCarrierList($input['carriers']);
                $input['type'] = 'html';
            }
        }

        return $helper->generateForm(array($form));
    }

    /**
     * Create the structure of your form.
     */
    public function getConfigForm()
    {
        // Get all carriers
        $carriers = Carrier::getCarriers($this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS);


        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->trans('Postcode Restriction Settings', [], 'Modules.Carrierpostcoderestriction.Admin'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'html',
                        'name' => 'form_layout_start',
                        'html_content' => '<style>
                        .form-wrapper .form-group label.control-label {
                            text-align: left;
                            float: none;
                            width: 100%;
                            padding: 0;
                            margin-bottom: 10px;
                        }
                        .form-wrapper .form-group .col-lg-9,
                        .form-wrapper .form-group .col-lg-8,
                        .form-wrapper .form-group .col-lg-6 {
                            width: 100%;
                            float: none;
                        }
                        .form-wrapper .form-group {
                            margin-bottom: 4px;
                        }
                        @media (min-width: 1200px) {
                            .bootstrap .col-lg-offset-3 {
                                margin-left: 0;
                            }
                        }
                        h4 {
                            margin-top: 16px;
                        }
                        label.control-label {
                            display: none;
                        }
                    </style>',
                    ),
                    array(
                        'type' => 'html',
                        'name' => 'postcode_label',
                        'html_content' => '<h4>' . $this->trans('Allowed Postcode Prefixes', [], 'Modules.Carrierpostcoderestriction.Admin') . '</h4>',
                    ),
                    array(
                        'col' => 6,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-map-marker"></i>',
                        'desc' => $this->trans('Enter two-digit numbers separated by commas (e.g., 75,77,78). Only customers with postcodes starting with these numbers will be allowed to select delivery.', [], 'Modules.Carrierpostcoderestriction.Admin'),
                        'name' => 'CARRIERPOSTCODERESTRICTION_ALLOWED_POSTCODES',
                        'label' => '',
                    ),
                    array(
                        'type' => 'html',
                        'name' => 'carrier_list_label',
                        'html_content' => '<h4>' . $this->trans('Carrier Restrictions', [], 'Modules.Carrierpostcoderestriction.Admin') . '</h4>',
                    ),
                    array(
                        'type' => 'html',
                        'name' => 'carrier_list_header',
                        'html_content' => '<div class="alert alert-info">' . $this->trans('Carriers with "Bypass restriction" enabled will always be shown to customers, regardless of their delivery address.', [], 'Modules.Carrierpostcoderestriction.Admin') . '</div>',
                    ),
                    array(
                        'type' => 'carrier_list',
                        'label' => '',
                        'name' => 'carrier_restrictions',
                        'carriers' => $carriers,
                    ),
                ),
                'submit' => array(
                    'title' => $this->trans('Save', [], 'Modules.Carrierpostcoderestriction.Admin'),
                ),
            ),
        );
    }

    /**
     * Add a custom field type for carrier list
     */
    public function renderCarrierList($carriers)
    {
        $this->context->smarty->assign(array(
            'carriers' => $carriers,
            'bypass_values' => $this->getCarrierBypassValues(),
        ));

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/carrier_list.tpl');
    }

    /**
     * Set values for the inputs.
     */
    public function getConfigFormValues()
    {
        $values = array(
            'CARRIERPOSTCODERESTRICTION_ALLOWED_POSTCODES' => Configuration::get('CARRIERPOSTCODERESTRICTION_ALLOWED_POSTCODES', null),
        );

        // Add carrier bypass values
        $carrier_bypass_values = $this->getCarrierBypassValues();
        foreach ($carrier_bypass_values as $id_carrier => $bypass) {
            $values['CARRIER_BYPASS_' . $id_carrier] = $bypass;
        }

        return $values;
    }

    /**
     * Get carrier bypass values from db
     */
    public function getCarrierBypassValues()
    {
        $bypass_values = array();
        $carriers = Carrier::getCarriers($this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS);

        foreach ($carriers as $carrier) {
            $id_carrier = (int) $carrier['id_carrier'];

            // Check if this carrier has a record in our table
            $sql = 'SELECT bypass_restriction FROM `' . _DB_PREFIX_ . 'carrierpostcoderestriction` 
                    WHERE carrier_id = ' . $id_carrier;
            $result = Db::getInstance()->getValue($sql);

            // If no record exists, default to 0 (false)
            $bypass_values[$id_carrier] = ($result !== false) ? (bool) $result : false;
        }

        return $bypass_values;
    }

    /**
     * Save form data.
     * @return string Confirmation message
     */
    public function postProcess()
    {
        // Save general settings
        Configuration::updateValue('CARRIERPOSTCODERESTRICTION_ALLOWED_POSTCODES', Tools::getValue('CARRIERPOSTCODERESTRICTION_ALLOWED_POSTCODES'));

        // Save carrier bypass settings
        $carriers = Carrier::getCarriers($this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS);

        foreach ($carriers as $carrier) {
            $id_carrier = (int) $carrier['id_carrier'];
            $bypass_value = (bool) Tools::getValue('CARRIER_BYPASS_' . $id_carrier, false);

            // Check if record exists
            $sql = 'SELECT id_carrierpostcoderestriction FROM `' . _DB_PREFIX_ . 'carrierpostcoderestriction` 
                    WHERE carrier_id = ' . $id_carrier;
            $id_record = Db::getInstance()->getValue($sql);

            $now = date('Y-m-d H:i:s');

            if ($id_record) {
                // Update existing record
                Db::getInstance()->update(
                    'carrierpostcoderestriction',
                    array(
                        'bypass_restriction' => (int) $bypass_value,
                        'date_upd' => $now
                    ),
                    'id_carrierpostcoderestriction = ' . (int) $id_record
                );
            } else {
                // Insert new record
                Db::getInstance()->insert(
                    'carrierpostcoderestriction',
                    array(
                        'carrier_id' => $id_carrier,
                        'bypass_restriction' => (int) $bypass_value,
                        'date_add' => $now,
                        'date_upd' => $now
                    )
                );
            }
        }

        return $this->displayConfirmation($this->trans('Settings updated successfully', [], 'Modules.Carrierpostcoderestriction.Admin'));
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    // public function hookActionCarrierProcess()
    // {
    //     /* Place your code here. */
    // }

    // public function hookActionCarrierUpdate()
    // {
    //     /* Place your code here. */
    // }

    // public function hookDisplayBeforeCarrier()
    // {
    //     /* Place your code here. */
    // }

    // public function hookDisplayCarrierExtraContent()
    // {
    //     /* Place your code here. */
    // }

    public function hookActionFilterDeliveryOptionList($params)
    {
        $deliveryOptionList = &$params['delivery_option_list'];
        $carrierBypassValues = $this->getCarrierBypassValues();
        $id_address_delivery = (int) $params['cart']->id_address_delivery;
        $delivery_address_postcode = $this->getPostcodeByAddressId(($id_address_delivery));

        // dump($carrierBypassValues);

        // Loop through the delivery options (first level is address ID)
        foreach ($deliveryOptionList as $addressId => &$addressOptions) {
            // Loop through each carrier option for this address
            foreach ($addressOptions as $carrierKey => &$carrierOption) {
                // Debug: Check the structure of each carrier option
                // dump("Address: $addressId, Carrier key: $carrierKey");
                // dump($carrierOption);

                // Check if this is a carrier we want to filter out
                $shouldRemove = false;

                // Loop through the carrier list in this option
                foreach ($carrierOption['carrier_list'] as $carrierListId => $carrier) {
                    // Debug: Check carrier details
                    // dump("Carrier instance: ");
                    // dump($carrier['instance']);

                    $carrier_id = (int) $carrier['instance']->id;
                    $bypassRestrictionForCarrier = isset($carrierBypassValues[$carrier_id]) ? $carrierBypassValues[$carrier_id] : false;
                    // dump("Carrier ID: $carrier_id, Bypass restriction: " . ($bypassRestrictionForCarrier ? 'Yes' : 'No'));

                    // If this carrier has bypass restriction enabled, skip filtering
                    if ($bypassRestrictionForCarrier) {
                        // dump("Bypassing restriction for carrier: " . $carrier['instance']->name);
                        continue;
                    }
                    // Check if the delivery address postcode starts with any of the allowed prefixes
                    $allowedPostcodes = Configuration::get('CARRIERPOSTCODERESTRICTION_ALLOWED_POSTCODES');
                    $allowedPostcodesArray = array_map('trim', explode(',', $allowedPostcodes));
                    $postcodeMatches = false;
                    foreach ($allowedPostcodesArray as $prefix) {
                        if (strpos($delivery_address_postcode, $prefix) === 0) {
                            $postcodeMatches = true;
                            break;
                        }
                    }
                    // If postcode does not match any allowed prefix, mark for removal
                    if (!$postcodeMatches) {
                        // dump("Postcode '$delivery_address_postcode' does not match allowed prefixes: " . implode(', ', $allowedPostcodesArray));
                        $shouldRemove = true;
                        break;
                    }

                }

                // Remove this carrier option if it doesn't match our criteria
                if ($shouldRemove) {
                    unset($addressOptions[$carrierKey]);
                }
            }
        }
    }

    /**
     * Get postcode from an address ID
     *
     * @param int $id_address The address ID
     * @return string|false The postcode or false if address not found
     */
    public function getPostcodeByAddressId($id_address)
    {
        // Validate address ID
        $id_address = (int) $id_address;
        if (!$id_address) {
            return false;
        }

        // Method 1: Using the Address object
        $address = new Address($id_address);
        if (Validate::isLoadedObject($address)) {
            return $address->postcode;
        }

        return false;
    }
}
