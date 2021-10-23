<?php

namespace PrestaShop\Module\AsBlog\Core\Grid\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class CategoryQueryBuilder.
 */
final class CategoryQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @param null|SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria = null)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('
            pc.id_category,
            pcl.name,
            pcl.description
            ')
            ->orderBy(
                $searchCriteria->getOrderBy(),
                $searchCriteria->getOrderWay()
            )
        ;

        if ($searchCriteria->getLimit() > 0) {
            $qb
                ->setFirstResult($searchCriteria->getOffset())
                ->setMaxResults($searchCriteria->getLimit())
            ;
        }

        return $qb;
    }

    /**
     * @param null|SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria = null)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(pc.id_category)');

        return $qb;
    }

    /**
     * Get generic query builder.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters)
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'post_category', 'pc')
            ->innerJoin('pc', $this->dbPrefix . 'post_category_lang', 'pcl', 'pc.id_category = pcl.id_category')
        ;

        foreach ($filters as $name => $value) {
            if ('id_lang' === $name) {
                $qb
                    ->andWhere("pcl.id_lang = :$name")
                    ->setParameter($name, $value)
                ;

                continue;
            }


            $qb
                ->andWhere(sprintf('pcl.%s LIKE :%s', $name, $name))
                ->setParameter($name, '%' . $value . '%')
            ;
        }

        return $qb;
    }
}
