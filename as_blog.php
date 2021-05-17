<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class As_blog extends Module
{
    protected $config_form = false;

    /* @var PostRepository */
    private $postRepository;

    public function __construct()
    {
        $this->name = 'as_blog';
        $this->tab = 'content_management';
        $this->version = '1.0.0';
        $this->author = 'Adib Aroui';
        $this->need_instance = 1;

        parent::__construct();

        $this->displayName = $this->l('Blog for Prestashop 1.7');
        $this->description = $this->l('Simple and Straightforward module for a blog inside Prestashop 1.7 e-commerce. ');

        $this->confirmUninstall = $this->l('');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }


    public function install()
    {
        Configuration::updateValue('AS_BLOG_LIVE_MODE', false);

        if (!parent::install()) {
            return false;
        }

        if (null !== $this->getPostRepository()) {
            $installed = $this->installDatabase();
        } else {
            $installed = $this->installLegacyDatabase();
        }

        if ($installed
            && $this->registerHook('header')
            && $this->registerHook('backOfficeHeader')
        ) {
            return true;
        }

        $this->uninstall();

        return false;
    }


    public function installDatabase() {

        $installed = true;

        $errors = $this->postRepository->createTables();

        if (!empty($errors)) {
            $this->addModuleErrors($errors);
            $installed = false;
        }

        return $installed;
    }

    public function uninstall()
    {
        Configuration::deleteByName('AS_BLOG_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * @return bool
     */
    private function installLegacyDatabase()
    {
        return true;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitAs_blogModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAs_blogModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'AS_BLOG_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'AS_BLOG_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'AS_BLOG_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'AS_BLOG_LIVE_MODE' => Configuration::get('AS_BLOG_LIVE_MODE', true),
            'AS_BLOG_ACCOUNT_EMAIL' => Configuration::get('AS_BLOG_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'AS_BLOG_ACCOUNT_PASSWORD' => Configuration::get('AS_BLOG_ACCOUNT_PASSWORD', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    /**
     * @return PostRepository|LegacyPostRepository|null
     */
    private function getRepository()
    {
        if (null === $this->posRepository) {
            try {
                $this->postRepository = $this->get('prestashop.module.as_blog.repository');
            } catch (Throwable $e) {
                try {
                    $container = SymfonyContainer::getInstance();
                    if (null !== $container) {
                        //Module is not installed so its services are not loaded
                        /** @var LegacyContext $context */
                        $legacyContext = $container->get('prestashop.adapter.legacy.context');
                        /** @var Context $shopContext */
                        $shopContext = $container->get('prestashop.adapter.shop.context');
                        $this->postRepository = new PostRepository(
                            $container->get('doctrine.dbal.default_connection'),
                            $container->getParameter('database_prefix'),
                            $legacyContext->getLanguages(true, $shopContext->getContextShopID()),
                            $container->get('translator')
                        );
                    }
                } catch (Throwable $e) {
                }
            }
        }

        // Container is not available so we use legacy repository as fallback
        /*if (!$this->repository) {
            $this->repository = $this->legacyBlockRepository;
        }*/

        return $this->postRepository;
    }
}
