<?php
namespace PrestaShop\Module\AsBlog\Link;

use PrestaShop\Module\AsBlog\Model\Post;
use Configuration;
use Context;
use Dispatcher;
use Language;

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


    public  function getBlogPostLink($blogpost, $rewrite = null, $ssl = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $url = $this->getBlogUrl();
        $dispatcher = Dispatcher::getInstance();

        if (!is_object($blogpost)) {
            if ($rewrite !== null) {
                return  $url .  $dispatcher->createUrl('module-asblog-blogpost', $id_lang, array('id_post' => (int)$blogpost, 'rewrite' => $rewrite), $this->allow, '', $id_shop);
            }
            $blogpost = new Post($blogpost, $id_lang);
        }

        $params = array();
        $params['rewrite'] = $blogpost->link_rewrite;
        $params['id_post'] = $blogpost->id_post;
        //return 'adib';
        return $url . $dispatcher->createUrl('module-asblog-blogpost', $id_lang, $params, $this->allow);
    }

    public static function getBlogUrl()
    {
        $ssl_enable       = Configuration::get('PS_SSL_ENABLED');
        $id_lang          = (int) Context::getContext()->language->id;
        $id_shop          = (int) Context::getContext()->shop->id;
        $rewrite_set      = (int) Configuration::get('PS_REWRITING_SETTINGS');
        $ssl              = null;
        static $force_ssl = null;
        if ($ssl === null) {
            if ($force_ssl === null) {
                $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            }
            $ssl = $force_ssl;
        }
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null) {
            $shop = new Shop($id_shop);
        } else {
            $shop = Context::getContext()->shop;
        }
        $base    = ($ssl == 1 && $ssl_enable == 1) ? 'https://' . $shop->domain_ssl : 'http://' . $shop->domain;
        $langUrl = Language::getIsoById($id_lang) . '/';
        if ((!$rewrite_set && in_array($id_shop, array((int) Context::getContext()->shop->id, null))) || !Language::isMultiLanguageActivated($id_shop) || !(int) Configuration::get('PS_REWRITING_SETTINGS', null, null, $id_shop)) {
            $langUrl = '';
        }

        return $base . $shop->getBaseURI() . $langUrl;
    }
}
