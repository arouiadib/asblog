<?php

namespace PrestaShop\Module\AsBlog\Model;

use Db;
use Context;
use Employee;
/**
 * Class Post
 */
class Post extends \ObjectModel
{
    /**
     * @var int
     */
    public $id_post;

    /**
     * @var int
     */
    public $id_category;

    /**
     * @var int
     */
    public $id_author;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $summary;

    /**
     * @var string
     */
    public $content;

    /**
     * @var boolean
     */
    public $active = true;

    /**
     * @var \DateTime
     */
    public $date_add;

    /**
     * @var string
     */
    public $meta_title;

    /**
     * @var string
     */
    public $meta_keywords;

    /**
     * @var string
     */
    public $meta_description;

    /**
     * @var int
     */
    public $position;

    /**
     * @var string
     */
    public $link_rewrite;

    /**
     * @var int
     */
    public $nb_views;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'post',
        'primary' => 'id_post',
        'multilang' => true,
        'fields' => array(
            'id_category' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_author' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'size' => 70),
            'summary' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'size' => 450),
            'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString', 'required' => true),
            'meta_title'       => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
            'meta_keywords'     => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'lang' => true),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
            'active'           => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'required' => true),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'link_rewrite' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'),
            'nb_views' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

    }

    public function add($auto_date = true, $null_values = false)
    {
        $return = parent::add($auto_date, $null_values);

        return $return;
    }

    public function update($auto_date = true, $null_values = false)
    {
        $return = parent::update($auto_date, $null_values);

        return $return;
    }

    public function toArray()
    {
        return [
            'id_post' => $this->id_post,
            'id_category' => $this->id_category,
            'id_author' => $this->id_category,
            'title' => $this->title,
            'summary' => $this->summary,
            'content' => $this->content,
            'active' => $this->active,
            'position' => $this->position,
            'date_add' => $this->date_add,
            'meta_title' => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'link_rewrite' => $this->link_rewrite,
            'nb_views' => $this->nb_views
        ];
    }

    public static function getNextPostsById($id_lang = null, $position =  0)
    {
        $sql = 'SELECT  p.id_post, pl.title, pl.link_rewrite
                FROM ' . _DB_PREFIX_ . 'post p
                INNER JOIN ' . _DB_PREFIX_ . 'post_lang pl
                ON p.id_post=pl.id_post
                WHERE pl.id_lang=' . (int) $id_lang . '
                AND p.active = 1
                AND p.position = ' . (int) $position . ' + 1';

        if (!$posts_next = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $posts_next;
    }

    public static function getPreviousPostsById($id_lang = null, $position =  0)
    {
        $sql = 'SELECT  p.id_post, pl.title, pl.link_rewrite
                FROM ' . _DB_PREFIX_ . 'post p
                INNER JOIN ' . _DB_PREFIX_ . 'post_lang pl
                ON p.id_post=pl.id_post
                WHERE pl.id_lang=' . (int) $id_lang . '
                AND p.active = 1
                AND p.position = ' . (int) $position . '- 1';

        if (!$posts_previous = Db::getInstance()->executeS($sql)) {
            return false;
        }
        return $posts_previous;
    }

    public static function getTotal($id_lang = null)
    {
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'post p
                INNER JOIN ' . _DB_PREFIX_ . 'post_lang pl ON p.id_post = pl.id_post
                WHERE pl.id_lang =' . (int) $id_lang . '
                AND p.active = 1';

        if (!$posts = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return count($posts);

    }

    public static function getTotalByCategory($id_category = null)
    {
        if ($id_category == null) {
            $id_category = 1;
        }
        $sql = 'SELECT COUNT(*) AS num
                FROM ' . _DB_PREFIX_ . 'post p
                WHERE p.id_category =' . (int) $id_category . '
                AND p.active = 1';

        return Db::getInstance()->getValue($sql);
    }

    public static function getAllPost($id_lang = null, $limit_start, $limit, $orderby = "id_post", $order = "DESC")
    {
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        if ($limit_start == '' || $limit_start < 0) {
            $limit_start = 0;
        }
        if ($limit == '') {
            $limit = 5;
        }
        $result       = array();

        if($orderby == "name"){
            $orderby = "pl.meta_title";
        }else{
            $orderby = 'p.' . $orderby;
        }

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'post p
                INNER JOIN ' . _DB_PREFIX_ . 'post_lang pl ON p.id_post = pl.id_post
                /*INNER JOIN ' . _DB_PREFIX_ . 'post_shop ps ON pl.id_post = ps.id_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '*/
                WHERE pl.id_lang=' . (int) $id_lang . '
                AND p.active = 1
                ORDER BY '. $orderby .' ' . $order . '
                LIMIT ' . (int) $limit_start . ',' . (int) $limit;

        if (!$posts = Db::getInstance()->executeS($sql)) {
            return false;
        }

        $blogCategory = new Category();
        $i            = 0;
        foreach ($posts as $post) {

            $link_rewrite = $post['link_rewrite'];

            if ($link_rewrite == '') {
                $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'post p INNER JOIN
					' . _DB_PREFIX_ . 'post_lang pl ON p.id_post = pl.id_post INNER JOIN
					' . _DB_PREFIX_ . 'post_shop ps ON pl.id_post = ps.id_post
					WHERE p.active= 1 AND p.id_post = ' . (int) $post['id_post'];

                if (!$post1 = Db::getInstance()->executeS($sql)) {
                    return false;
                }
                $link_rewrite = $post1[0]['link_rewrite'];
            }
            /*
                        $selected_cat = BlogCategory::getPostCategoriesFull((int) $post['id_smart_blog_post'], Context::getContext()->language->id);

                       $result[$i]['id_category']      = 1;
                        $result[$i]['cat_link_rewrite'] = '';
                        $result[$i]['cat_name']         = '';

                        foreach ($selected_cat as $key => $value) {
                            $result[$i]['id_category']      = $selected_cat[$key]['id_category'];
                            $result[$i]['cat_link_rewrite'] = $selected_cat[$key]['link_rewrite'];
                            $result[$i]['cat_name']         = $selected_cat[$key]['name'];
                        }*/

            $result[$i]['id_post']           = $post['id_post'];
            $result[$i]['id_author']           = $post['id_author'];
            $result[$i]['id_category']           = $post['id_category'];
            $result[$i]['nb_views']            = $post['nb_views'];
            $result[$i]['title']                = $post['title'];
            $result[$i]['meta_title']        = $post['meta_title'];
            $result[$i]['meta_description']  = $post['meta_description'];
            $result[$i]['summary'] = $post['summary'];
            $result[$i]['position']          = $post['position'];
            $result[$i]['content']           = $post['content'];
            $result[$i]['meta_keywords']      = $post['meta_keywords'];
            $result[$i]['link_rewrite']      = $link_rewrite;

            $employee                          = new Employee($post['id_author']);

            $result[$i]['lastname']  = $employee->lastname;
            $result[$i]['firstname'] = $employee->firstname;
            if (file_exists(_PS_IMG_DIR_ . 'blog/post/' . $post['id_post'] . '.jpeg')) {
                $image                    = $post['id_post'];
                $result[$i]['post_img'] = $image;
            } else {
                $result[$i]['post_img'] = 'no';
            }
            $result[$i]['date_add'] = $post['date_add'];

            $i++;
        }

        return $result;
    }

    public static function search($keyword = null, $id_lang = NULL)
    {
        if ($keyword == null) {
            return false;
        }
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        $sql     = 'SELECT *
                    FROM ' . _DB_PREFIX_ . 'post_lang pl, ' . _DB_PREFIX_ . 'post p
                    WHERE pl.id_lang = "' . pSQL($id_lang) . '"
                    AND p.active = 1
                    AND pl.id_post = p.id_post
                    AND (
                            pl.title LIKE \'%' . $keyword . '%\' OR
                            pl.meta_title LIKE \'%' . $keyword . '%\' OR
                            pl.meta_keywords LIKE \'%' . $keyword . '%\' OR
                            pl.meta_description LIKE \'%' . $keyword . '%\' OR
                            pl.content LIKE \'%' . $keyword . '%\'
                        )
                    ORDER BY p.id_post DESC';
        if (!$posts = Db::getInstance()->executeS($sql)) {
            return false;
        }

        //$BlogCategory = new Category();
        $i            = 0;

        $result = array();

        foreach ($posts as $post) {

            $result[$i]['id_post']           = $post['id_post'];
            $result[$i]['nb_views']            = $post['nb_views'];
            //$result[$i]['is_featured']       = $post['is_featured'];
            $result[$i]['meta_title']        = $post['meta_title'];
            $result[$i]['title']            = $post['title'];
            //$result[$i]['short_description'] = $post['short_description'];
            $result[$i]['meta_description']  = $post['meta_description'];
            $result[$i]['content']           = $post['content'];
            $result[$i]['meta_keywords']      = $post['meta_keywords'];
            $result[$i]['id_category']       = $post['id_category'];
            $result[$i]['link_rewrite']      = $post['link_rewrite'];
            //$result[ $i ]['cat_name']          = $BlogCategory->getCatName(  $post['id_smart_blog_post'] );
            //$result[$i]['cat_link_rewrite']  = $BlogCategory->getCatLinkRewrite($post['id_smart_blog_post']);
           $employee                          = new Employee($post['id_author']);

            $result[$i]['lastname']  = $employee->lastname;
            $result[$i]['firstname'] = $employee->firstname;
            if (file_exists(_PS_IMG_DIR_ . 'blog/post/' . $post['id_post'] . '.jpeg')) {
                $image                    = $post['id_post'];
                $result[$i]['post_img'] = $image;
            } else {
                $result[$i]['post_img'] = 'no';
            }
            $result[$i]['date_add'] = $post['date_add'];
            $i++;
        }
        return $result;
    }

    public static function searchCount($keyword = null, $id_lang = null)
    {
        if ($keyword == null) {
            return false;
        }
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        $sql     = 'SELECT * FROM ' . _DB_PREFIX_ . 'post_lang pl, ' . _DB_PREFIX_ . 'post p
                WHERE pl.id_lang =' . (int) $id_lang . '
                AND pl.id_post = p.id_post
                AND p.active = 1
                AND (
                        pl.title LIKE \'%' . $keyword . '%\' OR
                        pl.meta_title LIKE \'%' . $keyword . '%\' OR
                        pl.meta_keywords LIKE \'%' . $keyword . '%\' OR
                        pl.meta_description LIKE \'%' . $keyword . '%\' OR
                        pl.content LIKE \'%' . $keyword . '%\'
                    )
                ORDER BY p.id_post DESC';
        if (!$posts = Db::getInstance()->executeS($sql)) {
            return false;
        }
        return count($posts);
    }

    public static function getRecentPosts($id_lang = null, $destination = 'left')
    {
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        $limit = 6;
        if ($destination === 'home') $limit = 3;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT  p.id_author, p.id_post, p.date_add, p.id_category, pl.meta_title, pl.link_rewrite , pl.title, p.nb_views, pl.summary
                    FROM ' . _DB_PREFIX_ . 'post p 
                    INNER JOIN ' . _DB_PREFIX_ . 'post_lang pl ON p.id_post = pl.id_post 
                    WHERE pl.id_lang =' . $id_lang . '  AND p.active = 1 
                    ORDER BY p.date_add DESC LIMIT 0,' . $limit
        );

        foreach ($result as $key => $value) {
            //$result[$key]['created'] = smartblog::displayDate($value['created']);
        }

        return $result;
    }


    public static function getPopularPosts($id_lang = null)
    {
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        $limit = 5;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT p.id_author, p.nb_views, p.date_add, p.id_post, pl.meta_title, pl.link_rewrite 
                    FROM ' . _DB_PREFIX_ . 'post p 
                    INNER JOIN ' . _DB_PREFIX_ . 'post_lang pl 
                    ON p.id_post = pl.id_post 
                    WHERE pl.id_lang=' . $id_lang . ' AND p.active = 1 
                    ORDER BY p.nb_views DESC LIMIT 0,' . $limit
        );
        foreach ($result as $key => $value) {
            //$result[$key]['created'] = smartblog::displayDate($value['created']);
        }

        return $result;
    }

    public static function setNbViews($id_post)
    {
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'post as p 
        SET p.nb_views = (p.nb_views + 1) 
        WHERE p.id_post = ' . (int) $id_post;

        return Db::getInstance()->execute($sql);
    }
}
