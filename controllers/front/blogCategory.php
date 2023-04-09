<?php

use PrestaShop\Module\AsBlog\Model\Post;
use PrestaShop\Module\AsBlog\Model\Category;
use PrestaShop\Module\AsBlog\Link\BlogLink;

class AsBlogBlogCategoryModuleFrontController extends ModuleFrontController
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
		$post    = new Post();

        $id_category = Tools::getValue('id_category');
        //var_dump(count($id_category) );die;
        $orderby     = 'date_add';
        $order = 'desc';
        $posts_per_page = 12;


		$limit_start    = 0;
		$limit          = $posts_per_page;

		if (!$id_category) {
			$total = (int) $post->getTotal($this->context->language->id);
		} else {
			$total = (int) $post->getTotalByCategory($id_category);
		}


		if ($total != '' || $total != 0) {
			$totalpages = ceil($total / $posts_per_page);
		}
		if ((bool) Tools::getValue('page')) {
			$c           = (int) Tools::getValue('page');
			$limit_start = $posts_per_page * ($c - 1);
		}

		if (!$id_category) {
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


			if (!Validate::isLoadedObject($category )) {
                header('HTTP/1.1 404 Not Found');
                header('Status: 404 Not Found');
                $this->errors[] = Tools::displayError('Category not found');
            }

			$categoryinfo   = $category->toArray();

/*			echo "<pre>";
			var_dump($category);die;*/
			$title_category = $categoryinfo['name'][$this->context->language->id];
			$category_description = $categoryinfo['description'][$this->context->language->id];
			$meta_title       = $categoryinfo['meta_title'][$this->context->language->id];
			$meta_keyword     = $categoryinfo['meta_keywords'][$this->context->language->id];
			$meta_description = $categoryinfo['meta_description'][$this->context->language->id];

			$category_status  = $categoryinfo['active'];
			$cat_link_rewrite = $categoryinfo['link_rewrite'][$this->context->language->id];

            $allNews = Category::getCategoryPosts($this->context->language->id, $id_category, $limit_start, $limit);


		}


        //var_dump($allNews);die;
        $i = 0;
        if (!empty($allNews)) {
            foreach ($allNews as $item) {
                if (file_exists(_PS_IMG_DIR_ . 'blog/post/' . $item['id_post'] . '.jpeg')) {
                    $allNews[$i]['post_img'] = $item['id_post'] . '.jpeg';
                } else {
                    $allNews[$i]['post_img'] = 'no';
                }

                $employee                 = new Employee((int)$item['id_author']);
                $avatar = $employee->getImage();
                $allNews[$i]['author_image'] = $avatar;

                $allNews[$i]['lastname'] = $employee->lastname;
                $allNews[$i]['firstname'] = $employee->firstname;

                $id_category      = (int)$item['id_category'];


                $category = new Category($id_category, $this->context->language->id, $this->context->shop->id);
                $allNews[$i]['category'] = isset($category->name) ? $category->name : '';
                $i++;
            }
        }


		$protocol_link        = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
		$protocol_content = (isset($useSSL) and $useSSL and Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';

		$bloglink = new BlogLink($protocol_link, $protocol_content);

		$this->post_id = $id_category;
		parent::initContent();
		if(isset($limit_start) && $limit_start != 0){
			$limit_start = $limit_start + 1;
		}else{
			$limit_start = 0;
		}

        //var_dump($allNews);die;
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
				'limit'                => isset($limit) ? $limit : 0,
				'limit_start'          => isset($limit_start) ? $limit_start : 0,
				'c'                    => isset($c) ? $c : 1,
				'total'                => $total,
				'post_per_page'        => $posts_per_page,
				'pagenums'             => $totalpages - 1,
				'totalpages'           => $totalpages,
			)
		);

        $this->setTemplate('module:asblog/views/templates/front/view_category.tpl');
	}
}
