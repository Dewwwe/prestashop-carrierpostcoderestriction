<?php
/**
 * Controller for Carrier Postcode Restriction settings
 */

class AdminCarrierPostcodeRestrictionController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        
        parent::__construct();

        $this->meta_title = $this->module->getTranslator()->trans('Carrier Postcode Restriction', [], 'Modules.Carrierpostcoderestriction.Admin');
        // Set page title
        $this->page_header_toolbar_title = $this->module->getTranslator()->trans('Carrier Postcode Restriction', [], 'Modules.Carrierpostcoderestriction.Admin');

        // Remove default actions
        $this->actions = array();
        $this->list_no_link = true;
    }
    
    /**
     * Render the configuration page
     * @return string HTML content
     */
    public function renderView()
    {
        // Instead of calling getContent(), we'll replicate its functionality here
        $output = '';
        
        // Load the module explicitly for better linting
        /** @var Carrierpostcoderestriction $module */
        $module = $this->module;

        // Process form submission
        if ((bool)Tools::isSubmit('submitCarrierpostcoderestrictionModule')) {
            // Store the result of postProcess() in a variable
            $processResult = $module->postProcess();
            if ($processResult) {
                $output .= $processResult;
            }

            // Add confirmation message directly to controller
            $this->confirmations[] = $this->module->getTranslator()->trans('Settings updated successfully', [], 'Modules.Carrierpostcoderestriction.Admin');
        }

        $this->context->smarty->assign('module_dir', $this->module->getLocalPath());
        $output .= $this->context->smarty->fetch($this->module->getLocalPath().'views/templates/admin/configure.tpl');
        $output .= $module->renderForm();
        
        return $output;
    }
    
    /**
     * Process the form submission
     * @return void
     */
    public function postProcess()
    {
        // Let the module handle form submission
        if (Tools::isSubmit('submitCarrierpostcoderestrictionModule')) {
            // Don't call parent::postProcess() to avoid double processing
            return;
        }
        
        parent::postProcess();
    }
}
