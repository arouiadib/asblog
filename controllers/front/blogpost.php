<?php

use PrestaShop\Module\AsBlog\Model\Post;
use PrestaShop\Module\AsBlog\Link\BlogLink;

class AsBlogBlogPostModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {

        $id_post = pSQL( Tools::getvalue( 'id_post' ) );

        if ($id_post) {

            $post = new Post($id_post, true, $this->context->language->id, $this->context->shop->id);
            $this->post = $post->toArray();


            if (!$this->post['active']) {
                $this->post = array();
            }

        }

        if (!Validate::isLoadedObject($post)) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            $this->errors[] = Tools::displayError('Post not found');
        }

        parent::initContent();

        if (!$this->errors) {
            $id_lang = $this->context->language->id;
            $currentPostPosition = $this->post['position'];
            //$post['post_img'] = null; // --extra added

            //$id_category      = $post['id_category'];
            $posts_previous = Post::getPreviousPostsById($id_lang, $currentPostPosition);
            $posts_next = Post::getNextPostsById($id_lang, $currentPostPosition);

            /* Server Params */
            $protocol_link    = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
            $protocol_content = (isset($useSSL) and $useSSL and Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';

            $bloglink = new BlogLink($protocol_link, $protocol_content);

            if (file_exists(_PS_IMG_SOURCE_DIR_ . 'blog/post/' . $id_post . '.jpg')) {
                $post_img = $id_post;
            } else {
                $post_img = 'no';
            }

            $this->context->smarty->assign(
                array(
                    //'link_rewrite_'            => SmartBlogPost::GetPostSlugById($id_post, $this->context->language->id),
                    'bloglink'                   => $bloglink,
                    'baseDir'                    => _PS_BASE_URL_SSL_ . __PS_BASE_URI__,
                    'modules_dir'                => _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'modules/',
                    'post'                       => $this->post,
                    'posts_next'                 => $posts_next,
                    'posts_previous'             => $posts_previous,
                    //'title_category'             => (isset($title_category[0][0]['name'])) ? $title_category[0][0]['name'] : '',
                    //'cat_link_rewrite'           => (isset($title_category[0][0]['link_rewrite'])) ? $title_category[0][0]['link_rewrite'] : '',
                    'meta_title'                 => $this->post['meta_title'],
                    'post_active'                => $this->post['active'],
                    'content'                    => $this->post['content'],
                    'id_post'                    => $this->post['id_post'],
                    'created'                    => $this->post['date_add'],//Smartblog::displayDate($post['date_add']),
                    'post_img'                   => $post_img,
                    'id_category'                => $this->post['id_category'],
                )
            );
        }

        $this->setTemplate('module:asblog/views/templates/front/view_post.tpl');
    }
}
