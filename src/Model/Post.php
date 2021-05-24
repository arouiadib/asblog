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
    public $id_link_block;

    /**
     * @var string
     */
    public $title;

    /**
     * @var array
     */
    public $content;


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'post',
        'primary' => 'id_post',
        'multilang' => true,
        'fields' => array(
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'size' => 40),
            'content' => array('type' => self::TYPE_STRING),
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
            'id' => $this->id,
            'id_post' => $this->id_post,
            'title' => $this->title,
            'content' => $this->content,
        ];
    }
}
