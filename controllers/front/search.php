<?php

/**
 * 2007-2015 PrestaShop
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
 *  @copyright 2007-2015 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

include_once(dirname(__FILE__) . '/../../classes/controllers/FrontController.php');

class smartblogsearchModuleFrontController extends smartblogModuleFrontController
{

    public $ssl = false;

    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        $keyword = pSQL(Tools::getValue('smartsearch'));

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

        $total = SmartBlogPost::sarchCount($keyword, $id_lang);
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

                $this->setTemplate("module:smartblog/views/templates/front/searchresult.tpl");

    }
}
