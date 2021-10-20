<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\Module\AsBlog\Repository\PostRepository;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Asblog extends Module implements WidgetInterface
{
    public static $ModuleRoutes = array(
        'module-asblog-blogpost' => array(
            'controller' => 'blogpost',
            'rule' => 'blog_post{/:id_post}',
            'keywords' => array(
                'id_post' => array('regexp' => '[0-9]+', 'param' => 'id_post'),
            ),
            'params' => array(
                'fc' => 'module',
                'module' => 'asblog',
            )
        )
    );

    protected $config_form = false;

    /* @var PostRepository */
    private $postRepository;

    public $templates = [
        'blog_post_list' => 'blog_post_list.tpl',
        'blog_post' => 'blog_post.tpl',
    ];

    public function __construct()
    {
        $this->name = 'asblog';
        $this->tab = 'content_management';
        $this->version = '1.0.0';
        $this->author = 'Adib Aroui';
        $this->need_instance = 1;

        parent::__construct();

        $this->displayName = $this->l('Blog for Prestashop 1.7');
        $this->description = $this->l('Simple and Straightforward module for a blog inside Prestashop 1.7 store');

        $this->confirmUninstall = $this->l('Uninstall?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {

        if (!parent::install() || !$this->registerHook('moduleRoutes')) {
            return false;
        }
        if (!$this->installTab()) {
            return false;
        }

        if (null !== $this->getPostRepository()) {
            $installed = $this->installDatabase();
        } else {
            $installed = $this->installLegacyDatabase();
        }

        if ($installed) {
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
        return parent::uninstall() && $this->uninstallTab();
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
        if (((bool)Tools::isSubmit('submitAsblogModule')) == true) {
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
        $helper->submit_action = 'submitAsblogModule';
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
                ),
                /*'submit' => array(
                    'title' => $this->l('Save'),
                ),*/
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array();
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
     * @return PostRepository|LegacyPostRepository|null
     */
    private function getPostRepository()
    {
        if (null === $this->postRepository) {
            try {
                $this->postRepository = $this->get('prestashop.module.asblog.post.repository');
            } catch (Throwable $e) {
                /** @var LegacyContext $context */
                $legacyContext = $this->get('prestashop.adapter.legacy.context');
                /** @var Context $shopContext */
                $shopContext = $this->get('prestashop.adapter.shop.context');

                $this->postRepository = new PostRepository(
                    $this->get('doctrine.dbal.default_connection'),
                    SymfonyContainer::getInstance()->getParameter('database_prefix'),
                    $legacyContext->getLanguages(true, $shopContext->getContextShopID()),
                    $this->get('translator')
                );
            }
        }

        return $this->postRepository;
    }

    public function enable($force_all = false)
    {
        return parent::enable($force_all)
            && $this->installTab()
            ;
    }

    public function disable($force_all = false)
    {
        return parent::disable($force_all)
            && $this->uninstallTab()
            ;
    }

    private function installTab()
    {
        $tabId = (int) Tab::getIdFromClassName('AsBlogPostController');
        if (!$tabId) {
            $tabId = null;
        }

        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = 'AsBlogPostController';
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->trans('Blog', array(), 'Modules.Asblog.Admin', $lang['locale']);
        }
        $tab->route_name = 'admin_blog_post_list';
        $tab->id_parent = (int) Tab::getIdFromClassName('DEFAULT');
        $tab->module = $this->name;

        return $tab->save();
    }

    private function uninstallTab()
    {
        $tabId = (int) Tab::getIdFromClassName('AsBlogPostController');
        if (!$tabId) {
            return true;
        }

        $tab = new Tab($tabId);

        return $tab->delete();
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        $template_vars = [];
        if($hookName == null && isset($configuration['hook']))
        {
            $hookName = $configuration['hook'];
        }

        if($hookName == 'displayBlogPostList') {
            $template_vars = $this->getTemplateVarBlogPostList();
        } elseif ($hookName == 'displayBlogPost') {
            $template_vars = $this->getTemplateVarBlogPost();
        }

        return $template_vars;
    }

    /**
     * @return array
     */
    public function getTemplateVarBlogPostList()
    {
        // get list of blog post titles and their urls

        return;
    }

    /**
     * @return array
     */
    public function getTemplateVarBlogPost()
    {
        // get the title of blog post
        // get the content of blog post
        return;
    }


    public function renderWidget($hookName, array $configuration)
    {
        if (!$this->active) {
            return;
        }

        $template_file = null;

        if($hookName == null && isset($configuration['hook']))
        {
            $hookName = $configuration['hook'];
        }

        if($hookName == 'displayBlogPostList') {
            $template_file = $this->templates['blog_post_list'];
        } elseif ($hookName == 'displayBlogPost') {
            $template_file = $this->templates['blog_post'];
        }

        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));

        return $this->display(__FILE__, 'views/templates/widget/' . $template_file);
    }

    public function hookModuleRoutes($route = '', $detail = array())
    {
        return $this->getStaticModuleRoutes('ModuleRoutes');
    }

    public function getStaticModuleRoutes($ModuleRoutes)
    {
        if ($ModuleRoutes == 'ModuleRoutes') {
            return self::$ModuleRoutes;
        }
    }
}
