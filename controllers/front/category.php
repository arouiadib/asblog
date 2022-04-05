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

use PrestaShop\Module\AsBlog\Model\Post;
use PrestaShop\Module\AsBlog\Model\Category;
use PrestaShop\Module\AsBlog\Link\BlogLink;

class AsBlogCategoryModuleFrontController extends ModuleFrontController
{


	public $ssl = false;
	public $smartblogCategory;

	public function init()
	{

		parent::init();
	}

	public function canonicalRedirection($canonicalURL = '')
	{
		if (Tools::getValue('live_edit')) {
			return;
		}
		$protocol_link    = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
		$protocol_content = (isset($useSSL) and $useSSL and Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';

		$smartbloglink = new SmartBlogLink($protocol_link, $protocol_content);
		if (Validate::isLoadedObject($this->smartblogCategory) && ($canonicalURL = $smartbloglink->getSmartBlogCategoryLink($this->smartblogCategory, $this->smartblogCategory->link_rewrite))) {
			parent::canonicalRedirection($canonicalURL);
		}
	}


	public function initContent()
	{

		$category_status  = '';
		$totalpages       = 0;
		$cat_image        = 'no';
		$categoryinfo     = '';
		$title_category   = '';
		$cat_link_rewrite = '';
		//$blogcomment      = new Blogcomment();
		$post    = new Post();

		//$BlogPostCategory = new BlogPostCategory();

        /*			$smartblogurlpattern = (int) Configuration::get('smartblogurlpattern');

            switch ($smartblogurlpattern) {

                    case 1:
                        $SmartBlog   = new smartblog();
                        $slug        = Tools::getValue('slug');
                        $id_category = $SmartBlog->categoryslug2id($slug);
                        break;
                    case 2:
                        $SmartBlog   = new smartblog();
                        $id_category = Tools::getValue('id_category');
                        break;

                    default:
                        $id_category = Tools::getValue('id_category');
                }*/

        $id_category = Tools::getValue('id_category');

		/*$orderby     = Configuration::get('sborderby');
		$order = Configuration::get('sborder');
		$posts_per_page = Configuration::get('smartpostperpage');*/

        $orderby     = 'id_post';
        $order = 'asc';
        $posts_per_page = 5;


		$limit_start    = 0;
		$limit          = $posts_per_page;

		if (!$id_category) {
			$total = (int) $post->getTotal($this->context->language->id);
		} else {
			$total = (int) $post->getTotalByCategory($id_category);
			//Hook::exec('actionsbcat', array('id_category' => pSQL(Tools::getvalue('id_category'))));
		}


		if ($total != '' || $total != 0) {
			$totalpages = ceil($total / $posts_per_page);
		}
		if ((bool) Tools::getValue('page')) {
			$c           = (int) Tools::getValue('page');
			$limit_start = $posts_per_page * ($c - 1);
		}

		if (!$id_category) {
			//$meta_title       = Configuration::get('smartblogmetatitle');
			//$meta_keyword     = Configuration::get('smartblogmetakeyword');
			//$meta_description = Configuration::get('smartblogmetadescrip');

			$allNews = $post->getAllPost($this->context->language->id, $limit_start, $limit, $orderby, $order);

		} else {
            $cat_image = '';
			if (file_exists(_PS_IMG_DIR_ . 'blog/category/' . $id_category . '.jpeg')) {
				$ssl       = null;
				$force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
				$base_ssl  = ($force_ssl == 1) ? 'https://' : 'http://';
				$uri_path  = __PS_BASE_URI__ . 'img/blog/category/' . $id_category . '.jpeg';
				$cat_image = $base_ssl . Tools::getMediaServer($uri_path) . $uri_path;
			}

            $category     = new Category($id_category);

			$categoryinfo   = $category->toArray();

			$title_category = $categoryinfo['name'][$this->context->language->id];
			$category_description = $categoryinfo['description'][$this->context->language->id];
			$meta_title       = $categoryinfo['meta_title'][$this->context->language->id];
			$meta_keyword     = $categoryinfo['meta_keywords'][$this->context->language->id];
			$meta_description = $categoryinfo['meta_description'][$this->context->language->id];

			$category_status  = $categoryinfo['active'];
			$cat_link_rewrite = $categoryinfo['link_rewrite'][$this->context->language->id];


			/*if ($category_status == 1) {
				$allNews = $post->getTotalByCategory($this->context->language->id, $id_category, $limit_start, $limit);
			} elseif ($category_status == 0) {
				$allNews = '';
			}*/

            $allNews = $post->getAllPost($this->context->language->id, $limit_start, $limit, $orderby, $order);

		}
		/*$i  = 0;
		$to = array();*/

		if (!empty($allNews)) {
			foreach ($allNews as $item) {
				//$to[$i] = $blogcomment->getToltalComment($item['id_post']);
				//$i++;

				if (file_exists(_PS_IMG_DIR_ . 'blog/post/' . $item['id_post'] . '.jpeg')) {
					$item['post_img'] = $item['id_post'] . '.jpg';
				} else {
					$item['post_img'] = 'no';
				}
			}
			/*$j = 0;
			foreach ($to as $item) {
				if ($item == '') {
					$allNews[$j]['totalcomment'] = 0;
				} else {
					$allNews[$j]['totalcomment'] = $item;
				}
				$j++;
			}*/
		}
		$protocol_link        = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
		$protocol_content = (isset($useSSL) and $useSSL and Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';

		$bloglink = new BlogLink($protocol_link, $protocol_content);
        /*$i             = 0;
        if (!empty($allNews)) {
            if (count($allNews) >= 1) {
                if (is_array($allNews)) {
                    foreach ($allNews as $post) {
                        $allNews[$i]['created'] = Smartblog::displayDate($post['created']);
                        if (new DateTime() >= new DateTime($post['created'])) {
                            $allNews[$i]['published'] = true;
                        } else {
                            $allNews[$i]['published'] = false;
                        }
                        $i++;
                    }
                }
            }
        }*/

		$this->post_id = $id_category;
		parent::initContent();
		if(isset($limit_start) && $limit_start != 0){
			$limit_start = $limit_start + 1;
		}else{
			$limit_start = 0;
		}


		$this->context->smarty->assign(
			array(
				'bloglink'             => $bloglink,
				'postcategory'         => $allNews,
				'category_status'      => $category_status,
				'title_category'       => $title_category,
                'category_description' => $category_description,
                'meta_title'           => $meta_title,
				'cat_link_rewrite'     => $cat_link_rewrite,
				'id_category'          => $id_category,
				'cat_image'            => $cat_image,
				'categoryinfo'         => $categoryinfo,
				//'smartshowauthorstyle' => Configuration::get('smartshowauthorstyle'),
				//'smartshowauthor'    => Configuration::get('smartshowauthor'),
				'limit'                => isset($limit) ? $limit : 0,
				'limit_start'          => isset($limit_start) ? $limit_start : 0,
				'c'                    => isset($c) ? $c : 1,
				'total'                => $total,
				//'smartblogliststyle' => Configuration::get('smartblogliststyle'),
				//'smartcustomcss'     => Configuration::get('smartcustomcss'),
				//'smartshownoimg'     => Configuration::get('smartshownoimg'),
				//'smartdisablecatimg' => Configuration::get('smartdisablecatimg'),
				//'smartshowviewed'    => Configuration::get('smartshowviewed'),
				'post_per_page'        => $posts_per_page,
				'pagenums'             => $totalpages - 1,
				'totalpages'           => $totalpages,
			)
		);

/*		if ($overridden_template = Hook::exec(
			'DisplayOverrideSmartBlogTemplate',
			[
				'controller' => $this,
				'template_file' => "module:smartblog/postcategory",
			]
		)) {
			$this->setTemplate($overridden_template);
		} else {*/
/*			$theme = Configuration::get('smarttheme') ? Configuration::get('smarttheme') : 'default';
			var_dump($theme);die;
			$templatepath = $this->getTemplatePath("postcategory.tpl", $theme);

			if ($templatepath != "outside") {
				$this->setTemplate("module:smartblog/views/templates/front/themes/" . $templatepath . "/postcategory.tpl");
			} else {
				$this->setTemplate("module:smartblog/views/templates/front/postcategory.tpl");
			}*/
		//}

        $this->setTemplate('module:asblog/views/templates/front/view_category.tpl');
	}
}
