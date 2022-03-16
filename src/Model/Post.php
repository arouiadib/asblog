<?php

namespace PrestaShop\Module\AsBlog\Model;

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
     * @var string
     */
    public $title;

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
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'post',
        'primary' => 'id_post',
        'multilang' => true,
        'fields' => array(
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'size' => 40),
            'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString', 'required' => true),
            'meta_title'       => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
            'meta_keywords'     => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'lang' => true),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
            'active'           => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'required' => true),
            'id_category' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
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
            'title' => $this->title,
            'content' => $this->content,
            'active' => $this->active,
            'position' => $this->position,
            'date_add' => $this->date_add,
            'meta_title' => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
        ];
    }

    public static function getNextPostsById($id_lang = null, $position =  0)
    {

        $sql = 'SELECT  p.id_smart_blog_post, pl.meta_title, pl.link_rewrite
                FROM ' . _DB_PREFIX_ . 'smart_blog_post p
                INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_lang pl
                ON p.id_smart_blog_post=pl.id_smart_blog_post
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

        $sql = 'SELECT  p.id_smart_blog_post, pl.meta_title, pl.link_rewrite
                FROM ' . _DB_PREFIX_ . 'smart_blog_post p
                INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_lang pl
                ON p.id_smart_blog_post=pl.id_smart_blog_post
                WHERE pl.id_lang=' . (int) $id_lang . '
                AND p.active = 1
                AND p.position = ' . (int) $position . '- 1';

        if (!$posts_previous = Db::getInstance()->executeS($sql)) {
            return false;
        }
        return $posts_previous;
    }


}
