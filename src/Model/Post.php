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
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $content;

    /**
     * @var \DateTime
     */
    public $date_add;


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
            'date_add' => array('type' => self::TYPE_DATE, 'required' => true),
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
            'title' => $this->title,
            'content' => $this->content,
            'date_add' => $this->date_add
        ];
    }
}
