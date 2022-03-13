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
use PrestaShop\Module\AsBlog\Repository\CategoryRepository;
use PrestaShop\Module\AsBlog\Repository\ImageObjectRepository;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

define('_PS_BLOG_CATEGORY_IMG_DIR_', _PS_IMG_DIR_.'blog/category/');
define('_PS_BLOG_POST_IMG_DIR_', _PS_IMG_DIR_.'blog/post/');

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
        ),
    );

    protected $config_form = false;

    /* @var PostRepository */
    private $postRepository;

    /* @var CategoryRepository */
    private $categoryRepository;

    /* @var ImageRepository */
    private $imageRepository;

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
        if (!file_exists(_PS_BLOG_CATEGORY_IMG_DIR_)) {
            mkdir(_PS_BLOG_CATEGORY_IMG_DIR_, 0775, true);
        }

        if (!file_exists(_PS_BLOG_POST_IMG_DIR_)) {
            mkdir(_PS_BLOG_POST_IMG_DIR_, 0775, true);
        }

        if (!parent::install() || !$this->registerHook('moduleRoutes')) {
            return false;
        }

        if (null !== $this->getPostRepository()
            && null !== $this->getCategoryRepository()
            && null !== $this->getImageRepository()
        ) {
            $installed = $this->installDatabase();
        }

        if ($installed) {
            return true;
        }

        $this->uninstall();

        return false;
    }


    public function installDatabase() {
        $installed = true;

        $errorsPostTables = $this->postRepository->createTables();
        $errorsCategoryTables = $this->categoryRepository->createTables();
        $errorsImageTables = $this->imageRepository->createTables();
        $errors = array_merge($errorsPostTables, $errorsCategoryTables, $errorsImageTables);

        if (!empty($errors)) {
            $this->addModuleErrors($errors);
            $installed = false;
        }

        return $installed;
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallTabs();
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
     * @return PostRepository|null
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

    /**
     * @return CategoryRepository|null
     */
    private function getCategoryRepository()
    {
        if (null === $this->categoryRepository) {
            try {
                $this->categoryRepository = $this->get('prestashop.module.asblog.category.repository');
            } catch (Throwable $e) {
                /** @var LegacyContext $context */
                $legacyContext = $this->get('prestashop.adapter.legacy.context');
                /** @var Context $shopContext */
                $shopContext = $this->get('prestashop.adapter.shop.context');

                $this->categoryRepository = new CategoryRepository(
                    $this->get('doctrine.dbal.default_connection'),
                    SymfonyContainer::getInstance()->getParameter('database_prefix'),
                    $legacyContext->getLanguages(true, $shopContext->getContextShopID()),
                    $this->get('translator')
                );
            }
        }

        return $this->categoryRepository;
    }

    /**
     * @return ImageObjectRepository|null
     */
    private function getImageRepository()
    {
        if (null === $this->imageRepository) {
            try {
                $this->imageRepository = $this->get('prestashop.module.asblog.image_object.repository');
            } catch (Throwable $e) {
                /** @var LegacyContext $context */
                $legacyContext = $this->get('prestashop.adapter.legacy.context');
                /** @var Context $shopContext */
                $shopContext = $this->get('prestashop.adapter.shop.context');

                $this->imageRepository = new ImageObjectRepository(
                    $this->get('doctrine.dbal.default_connection'),
                    SymfonyContainer::getInstance()->getParameter('database_prefix'),
                    $legacyContext->getLanguages(true, $shopContext->getContextShopID()),
                    $this->get('translator')
                );
            }
        }

        return $this->imageRepository;
    }


    public function enable($force_all = false)
    {
        return parent::enable($force_all)
            && $this->installTabs()
            ;
    }

    public function disable($force_all = false)
    {
        return parent::disable($force_all)
            && $this->uninstallTabs()
            ;
    }

    private function installTabs()
    {
        $mainTabId = (int) Tab::getIdFromClassName('AsBlogAdmin');
        if (!$mainTabId) {
            $mainTabId = null;
        }

        $mainTab = new Tab($mainTabId);
        $mainTab->active = 1;
        $mainTab->class_name = 'AsBlogAdmin';
        $mainTab->name = array();

        foreach (Language::getLanguages(true) as $lang) {
            $mainTab->name[$lang['id_lang']] = $this->trans('Blog', array(), 'Modules.Asblog.Admin', $lang['locale']);
        }

        $mainTab->id_parent = 0;
        $mainTab->module = $this->name;

        $return = $mainTab->save();
        $mainTabId = $mainTab->id;

        $tabs = $this->getAsBlogTabs();

        foreach ($tabs as $tab) {
            $subTab             = new Tab();
            $subTab->class_name = $tab['class_name'];
            $subTab->id_parent = $mainTabId;
            $subTab->module = $this->name;
            $subTab->route_name = $tab['route_name'];
            foreach (Language::getLanguages(true) as $lang) {
                $subTab->name[$lang['id_lang']] = $this->trans($tab['name'], array(), 'Modules.Asblog.Admin', $lang['locale']);
            }
            $return &= $subTab->save();
        }

        return $return;
    }

    private function uninstallTabs()
    {
        $return = true;

        $mainTabId = (int) Tab::getIdFromClassName('AsBlogAdmin');
        if ($mainTabId) {
            $mainTab = new Tab($mainTabId);
            $return &= $mainTab->delete();
        }

        $tabs = $this->getAsBlogTabs();
        foreach ($tabs as $tab) {
            $subTabId = (int) Tab::getIdFromClassName($tab['class_name']);
            $subTab = new Tab($subTabId);
            $return &= $subTab->delete();
        }

        return $return;
    }

    private function getAsBlogTabs()
    {
        return [
            [
                'class_name' => 'AsBlogPostController',
                'name'       => 'Blog Post',
                'route_name' =>  'admin_blog_post_list'
            ],
            [
                'class_name' => 'AsBlogCategoryController',
                'name'       => 'Blog Category',
                'route_name' =>  'admin_blog_category_list'
            ],
        ];
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
