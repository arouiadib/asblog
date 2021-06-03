<?php

namespace PrestaShop\Module\AsBlog\Core\Grid\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class PostQueryBuilder.
 */
final class PostQueryBuilder extends AbstractDoctrineQueryBuilder
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
            p.id_post,
            p.date_add,
            pl.title
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
        $qb->select('COUNT(p.id_post)');

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
            ->from($this->dbPrefix . 'post', 'p')
            ->innerJoin('p', $this->dbPrefix . 'post_lang', 'pl', 'p.id_post = pl.id_post')
        ;

        foreach ($filters as $name => $value) {
            if ('id_lang' === $name) {
                $qb
                    ->andWhere("pl.id_lang = :$name")
                    ->setParameter($name, $value)
                ;

                continue;
            }


            $qb
                ->andWhere(sprintf('pl.%s LIKE :%s', $name, $name))
                ->setParameter($name, '%' . $value . '%')
            ;
        }

        return $qb;
    }
}
