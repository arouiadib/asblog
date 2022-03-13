<?php

use PrestaShop\Module\AsBlog\Model\Post;

class BlogLink
{

    /** @var bool Rewriting activation */
    protected $allow;
    protected $url;
    public static $cache = array('page' => array());
    public $protocol_link;
    public $protocol_content;
    protected $ssl_enable;
    protected static $category_disable_rewrite = null;

    /**
     * Constructor (initialization only)
     */
    public function __construct($protocol_link = null, $protocol_content = null)
    {
        $this->allow = (int) Configuration::get('PS_REWRITING_SETTINGS');
        $this->url = $_SERVER['SCRIPT_NAME'];
        $this->protocol_link = $protocol_link;
        $this->protocol_content = $protocol_content;

        if (!defined('_PS_BASE_URL_')) {
            define('_PS_BASE_URL_', Tools::getShopDomain(true));
        }
        if (!defined('_PS_BASE_URL_SSL_')) {
            define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));
        }

        /* if (Link::$category_disable_rewrite === null) {
          Link::$category_disable_rewrite = array(Configuration::get('PS_HOME_CATEGORY'), Configuration::get('PS_ROOT_CATEGORY'));
          } */

        $this->ssl_enable = Configuration::get('PS_SSL_ENABLED');
    }


    public  function getBlogPostLink($blogpost, $alias = null, $ssl = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        //$url = $this->getBaseLink($id_shop, $ssl, $relative_protocol).$this->getLangLink($id_lang, null, $id_shop);
        //$url = smartblog::GetSmartBlogUrl();

        $dispatcher = Dispatcher::getInstance();

        if (!is_object($blogpost)) {
            if ($alias !== null && !$dispatcher->hasKeyword('module-asblog-blogpost', $id_lang, 'meta_keywords', $id_shop) && !$dispatcher->hasKeyword('smartblog_post_rule', $id_lang, 'meta_title', $id_shop)) {
                return  /*$url .*/  $dispatcher->createUrl('module-asblog-blogpost', $id_lang, array('id_post' => (int)$blogpost, 'slug' => $alias), $this->allow, '', $id_shop);
            }
            $blogpost = new Post($blogpost, $id_lang);
        }

        $params = array();
        $params['slug'] = $blogpost->link_rewrite;
        $params['id_post'] = $blogpost->id_smart_blog_post;

        if ($params != null) {
            return /*$url .*/ $dispatcher->createUrl('module-asblog-blogpost', $id_lang, $params, $this->allow);
        } else {
            $params = array();
            return /*$url .*/  $dispatcher->createUrl('module-asblog-blogpost', $id_lang, $params,  $this->allow);
        }
    }
}
