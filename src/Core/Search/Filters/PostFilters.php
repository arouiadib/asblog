<?php

namespace PrestaShop\Module\AsBlog\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class PostFilters.
 */
final class PostFilters extends Filters
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 0,
            'offset' => 0,
            'orderBy' => 'id_post',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
