<?php

namespace PrestaShop\Module\AsBlog\Model;

use Configuration;
use Db;
/**
 * Class Category
 */
class Category extends \ObjectModel
{
    /**
     * @var int
     */
    public $id_category;

    /**
     * @var int
     */
    public $id_parent;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var boolean
     */
    public $active = true;

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
     * @var string
     */
    public $link_rewrite;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'post_category',
        'primary' => 'id_category',
        'multilang' => true,
        'fields' => array(
            'id_parent' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'nleft' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'nright' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'active'           => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'size' => 40),
            'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString', 'required' => true),
            'meta_title'       => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
            'meta_keywords'     => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'lang' => true),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
            'link_rewrite' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString')
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
            'id_category' => $this->id_category,
            'id_parent' => $this->id_parent,
            'name' => $this->name,
            'description' => $this->description,
            'active' => $this->active,
            'meta_title' => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'link_rewrite' => $this->link_rewrite,
        ];
    }

    public static function getCategory( $active = 1, $id_lang = null ) {
        if ( $id_lang == null ) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        $sorting  = Configuration::get( 'sort_category_by' );
        $orderby  = 'pcl.name';
        $orderway = 'ASC';
        if ( $sorting == 'name_ASC' ) {
            $orderby  = 'pcl.name';
            $orderway = 'ASC';
        } elseif ( $sorting == 'name_DESC' ) {
            $orderby  = 'pcl.name';
            $orderway = 'DESC';
        } elseif ( $sorting == 'id_ASC' ) {
            $orderby  = 'pc.id_category';
            $orderway = 'ASC';
        } else {
            $orderby  = 'pc.id_category';
            $orderway = 'DESC';
        }

        $result = Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS(
            '
                SELECT * FROM `' . _DB_PREFIX_ . 'post_category` pc 
                INNER JOIN `' . _DB_PREFIX_ . 'post_category_lang` pcl 
                ON (pc.`id_category` = pcl.`id_category` AND pcl.`id_lang` = ' . (int) ( $id_lang ) . ')
                WHERE pc.`active`= ' . $active . ' 
                ORDER BY ' . $orderby . ' ' . $orderway
        );

        return $result;
    }

    public static function getCountPostsByCategory( $id_category ) {
        $sql = 'SELECT count(id_post) as count 
                from `' . _DB_PREFIX_ . 'post` 
                where id_category = ' . (int) $id_category;

        if ( ! $result = Db::getInstance()->executeS( $sql ) ) {
            return false;
        }

        return $result[0]['count'];
    }

    
    public static function getCategoryPosts($id_lang, $id_category, $limit_start, $limit)
    {
        $result = array();
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'post p
                LEFT JOIN ' . _DB_PREFIX_ . 'post_lang pl 
                ON p.id_post = pl.id_post 
                /*WHERE pl.id_lang=' . (int) $id_lang . ' */
                WHERE p.active = 1 
                AND p.id_category = ' . (int) $id_category . '
                ORDER BY p.id_post DESC LIMIT ' . (int) $limit_start . ',' . (int) $limit;

        //var_dump($limit_start);
        //var_dump($sql);
        //die;
        if (!$posts = Db::getInstance()->executeS($sql))  return false;

        $i = 0;
        foreach ($posts as $post) {

            /*$result[$i]['cat_link_rewrite'] = '';
            $result[$i]['cat_name'] = 'Home';*/


            $result[$i]['id_post'] = $post['id_post'];
            $result[$i]['id_author'] = $post['id_author'];
            $result[$i]['nb_views'] = $post['nb_views'];
            $result[$i]['title'] = $post['title'];
            $result[$i]['summary'] = $post['summary'];
            $result[$i]['position'] = $post['position'];
            $result[$i]['meta_title'] = $post['meta_title'];
            $result[$i]['meta_description'] = $post['meta_description'];
            $result[$i]['meta_keywords'] = $post['meta_keywords'];
            $result[$i]['content'] = $post['content'];
            $result[$i]['id_category'] = $post['id_category'];
            $result[$i]['link_rewrite'] = $post['link_rewrite'];
//            $result[$i]['cat_link_rewrite'] = $BlogCategory->getCatLinkRewrite($post['id_category']);
/*            $employee = new Employee($post['id_author']);

            $result[$i]['lastname'] = $employee->lastname;
            $result[$i]['firstname'] = $employee->firstname;*/
            if (file_exists(_PS_IMG_DIR_ . 'blog/post/' . $post['id_post'] . '.jpeg')) {
                $image = $post['id_post'];
                $result[$i]['post_img'] = $image;
            } else {
                $result[$i]['post_img'] = 'no';
            }
            $result[$i]['date_add'] = $post['date_add'];

            $i++;
        }


        return $result;
    }


}
