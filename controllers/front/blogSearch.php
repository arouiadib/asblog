<?php

use PrestaShop\Module\AsBlog\Model\Post;
use PrestaShop\Module\AsBlog\Model\Category;
use PrestaShop\Module\AsBlog\Link\BlogLink;

class AsBlogSearchModuleFrontController extends ModuleFrontController
{

    public $ssl = false;

    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        $keyword = pSQL(Tools::getValue('search'));

        $id_lang = (int) $this->context->language->id;
        $title_category = '';
        $posts_per_page = 5;
        $limit_start = 0;
        $limit = $posts_per_page;

        if ((bool) Tools::getValue('page')) {
            $c = (int) Tools::getValue('page');
            $limit_start = $posts_per_page * ($c - 1);
        }


        $result = Post::search($keyword, $id_lang, $limit_start, $limit);

        $total = Post::searchCount($keyword, $id_lang);
        $totalpages = ceil($total / $posts_per_page);


        $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
        $protocol_content = (isset($useSSL) and $useSSL and Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';

        $bloglink = new BlogLink($protocol_link, $protocol_content);
        if(isset($limit_start) && $limit_start != 0){
			$limit_start = $limit_start+1;
		}else{
			$limit_start = 0;
		}
        $this->context->smarty->assign(array(
            'bloglink' => $bloglink,
            'postcategory' => $result,
            'title_category' => $title_category,
            'limit' => isset($limit) ? $limit : 0,
            'limit_start' => isset($limit_start) ? $limit_start : 0,
            'c' => isset($c) ? $c : 1,
            'total' => $total,
            'post_per_page' => $posts_per_page,
            'smartsearch' => $keyword,
            'pagenums' => $totalpages - 1,
            'totalpages' => $totalpages
        ));

        $this->setTemplate("module:asblog/views/templates/front/searchresult.tpl");

    }
}
