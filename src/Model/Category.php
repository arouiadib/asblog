<?php

namespace PrestaShop\Module\AsBlog\Model;

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
}
