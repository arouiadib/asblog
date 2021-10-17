<?php


class AsBlogBlogPostModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $id_post = (int) Tools::getValue('id_post');

        // get the repository of post

        // get the post
        // get the post tile and post content


        $this->context->smarty->assign(
            [   'post_title' => 'title',
                'post_content' => 'content'
            ]
        );


        $this->setTemplate('module:asblog/views/templates/front/view_post.tpl');

        parent::initContent();
    }
}
