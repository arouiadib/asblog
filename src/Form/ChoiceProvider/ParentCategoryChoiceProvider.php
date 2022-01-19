<?php

namespace PrestaShop\Module\AsBlog\Form\ChoiceProvider;

use Doctrine\DBAL\Connection;

/**
 * Class ParentCategoryChoiceProvider.
 */
final class ParentCategoryChoiceProvider extends AbstractDatabaseChoiceProvider
{

    public function __construct(Connection $connection, $dbPrefix, $idLang = null, array $shopIds = null)
    {
        parent::__construct($connection, $dbPrefix, $idLang, $shopIds);
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        $choices = [];
        $categories = [];

        $qb = $this->connection->createQueryBuilder();
        $qb->select('c1.*, cl.name')
            ->from($this->dbPrefix . 'post_category', 'c1')
            ->leftJoin('c1', $this->dbPrefix . 'post_category_lang', 'cl', 'c1.id_category = cl.id_category')
            //->innerJoin('c1', $this->dbPrefix . 'post_category_shop', 'cs', 'c1.id_category = cs.id_category')
            ->rightJoin('c1', $this->dbPrefix . 'post_category', 'c2', '1 = c2.id_category' )
            //->andWhere('c.active = 1')
            ->andWhere('cl.id_lang = :idLang')
            //->andWhere('cs.id_shop IN (:shopIds)')
            ->andWhere('c1.`nleft` >= c2.`nleft` AND c1.`nright` <= c2.`nright`')
            ->setParameter('idLang', $this->idLang)
            //->setParameter('shopIds', implode(',', $this->shopIds))
            ;

        $result = $qb->execute()->fetchAll();
        foreach ($result as $row) {
            $current = &$buff[$row['id_category']];
            $current = $row;

            if ($row['id_category'] == 1) {
                $categories[$row['id_category']] = &$current;
            } else {
                $buff[$row['id_parent']]['children'][$row['id_category']] = &$current;
            }
        }

        foreach ($categories as $category) {
            $choices[] = $this->buildChoiceTree($category);
        }

        return $choices;
    }


    /**
     * @param array $category
     *
     * @return array
     */
    private function buildChoiceTree(array $category)
    {
        $tree = [
            'id_category' => $category['id_category'],
            'name' => $category['name'],
        ];

        if (isset($category['children'])) {
            foreach ($category['children'] as $childCategory) {
                $tree['children'][] = $this->buildChoiceTree($childCategory);
            }
        }

        return $tree;
    }
}
