<?php

namespace PrestaShop\Module\AsBlog\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;
use Symfony\Component\Translation\TranslatorInterface;


/**
 * Class CategoryRepository.
 */
class CategoryRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var array
     */
    private $languages;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * CategoryRepository constructor.
     *
     * @param Connection $connection
     * @param string $dbPrefix
     * @param array $languages
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        array $languages,
        TranslatorInterface $translator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->languages = $languages;
        $this->translator = $translator;
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createTables()
    {
        $errors = [];
        $engine = _MYSQL_ENGINE_;
        $this->dropTables();

        $queries = [
            "CREATE TABLE IF NOT EXISTS `{$this->dbPrefix}post_category`(
    			`id_category` int(10) unsigned NOT NULL auto_increment,
    			`id_parent` int(10) unsigned,
    			`nleft` int(10) unsigned,
    			`nright` int(10) unsigned,
    			`active` bool,
    			PRIMARY KEY (`id_category`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8",
            "CREATE TABLE IF NOT EXISTS `{$this->dbPrefix}post_category_lang`(
    			`id_category` int(10) unsigned NOT NULL,
    			`id_lang` int(10) unsigned NOT NULL,
    			`name` varchar(40) NOT NULL default '',
    			`description` text default NULL,
    			`meta_title` text default NULL,
    			`meta_keywords` text default NULL,
    			`meta_description` text default NULL,
    			PRIMARY KEY (`id_category`, `id_lang`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8",
            "CREATE TABLE IF NOT EXISTS `{$this->dbPrefix}post_category_shop` (
    			`id_category` int(10) unsigned NOT NULL auto_increment,
    			`id_shop` int(10) unsigned NOT NULL,
    			PRIMARY KEY (`id_category`, `id_shop`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8",
        ];

        foreach ($queries as $query) {
            $statement = $this->connection->executeQuery($query);
            if (0 != (int) $statement->errorCode()) {
                $errors[] = [
                    'key' => json_encode($statement->errorInfo()),
                    'parameters' => [],
                    'domain' => 'Admin.Modules.Notification',
                ];
            }
        }

        return $errors;
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function dropTables()
    {
        $errors = [];
        $tableNames = [
            'post_category_shop',
            'post_category_lang',
            'post_category',
        ];
        foreach ($tableNames as $tableName) {
            $sql = 'DROP TABLE IF EXISTS ' . $this->dbPrefix . $tableName;
            $statement = $this->connection->executeQuery($sql);
            if ($statement instanceof Statement && 0 != (int) $statement->errorCode()) {
                $errors[] = [
                    'key' => json_encode($statement->errorInfo()),
                    'parameters' => [],
                    'domain' => 'Admin.Modules.Notification',
                ];
            }
        }

        return $errors;
    }

    /**
     * @param array $data
     *
     * @return string
     *
     * @throws DatabaseException
     */
    public function create(array $data)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->insert($this->dbPrefix . 'post_category')
            ->values([
                'id_parent' => ':idParent',
                'active' => ':active',
                'nleft' => 1,
                'nright' => 1
            ])
            ->setParameters([
                'idParent' => $data['id_parent'],
                'active' => $data['active'],
            ])
        ;

        $this->executeQueryBuilder($qb, 'Category error');
        $categoryId = $this->connection->lastInsertId();

        $this->updateLanguages($categoryId, $data);

        return $categoryId;
    }

    /**
     * @param int $categoryId
     * @param array $data
     *
     * @throws DatabaseException
     */
    public function update($categoryId, array $data)
    {

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->update($this->dbPrefix . 'post_category', 'pc')
            ->andWhere('pc.id_category = :categoryId')
            ->set('id_parent', ':idParent')
            ->set('active', ':active')
            ->setParameters([
                'categoryId' => $categoryId,
                'idParent' => $data['id_parent'],
                'active' => $data['active']
            ])
        ;

        $this->executeQueryBuilder($qb, 'Category update error');

        $this->updateLanguages($categoryId, $data);
    }

    /**
     * @param int $categoryId
     * @param array $blockName
     * @param array $custom
     *
     * @throws DatabaseException
     */
    private function updateLanguages($categoryId, array $categoryData)
    {
        foreach ($this->languages as $language) {
            $qb = $this->connection->createQueryBuilder();
            $qb
                ->select('pcl.id_category')
                ->from($this->dbPrefix . 'post_category_lang', 'pcl')
                ->andWhere('pcl.id_category = :cateogryId')
                ->andWhere('pcl.id_lang = :langId')
                ->setParameter('cateogryId', $categoryId)
                ->setParameter('langId', $language['id_lang'])
            ;
            $foundRows = $qb->execute()->rowCount();

            $qb = $this->connection->createQueryBuilder();
            if (!$foundRows) {
                $qb
                    ->insert($this->dbPrefix . 'post_category_lang')
                    ->values([
                        'id_category' => ':cateogryId',
                        'id_lang' => ':langId',
                        'name' => ':name',
                        'description' => ':description',
                        'meta_title' => ':metaTitle',
                        'meta_description' => ':metaDescription',
                        'meta_keywords' => ':metaKeywords'
                    ])
                ;
            } else {
                $qb
                    ->update($this->dbPrefix . 'post_category_lang', 'pcl')
                    ->set('name', ':name')
                    ->set('description', ':description')
                    ->set('meta_title', ':metaTitle')
                    ->set('meta_description', ':metaDescription')
                    ->set('meta_keywords', ':metaKeywords')
                    ->andWhere('pcl.id_category = :categoryId')
                    ->andWhere('pcl.id_lang = :langId')
                ;
            }

            $qb
                ->setParameters([
                    'categoryId' => $categoryId,
                    'langId' => $language['id_lang'],
                    'name' => $categoryData['name'][$language['id_lang']],
                    'description' => $categoryData['description'][$language['id_lang']],
                    'metaTitle' => $categoryData['meta_title'][$language['id_lang']],
                    'metaDescription' => $categoryData['meta_description'][$language['id_lang']],
                    'metaKeywords' => $categoryData['meta_keywords'][$language['id_lang']],
                ]);

            $this->executeQueryBuilder($qb, 'Category language error');
        }
    }

    /**
     * @param QueryBuilder $qb
     * @param string $errorPrefix
     *
     * @return Statement|int
     *
     * @throws DatabaseException
     */
    private function executeQueryBuilder(QueryBuilder $qb, $errorPrefix = 'SQL error')
    {
        $statement = $qb->execute();
        if ($statement instanceof Statement && !empty($statement->errorInfo())) {
            throw new DatabaseException($errorPrefix . ': ' . var_export($statement->errorInfo(), true));
        }

        return $statement;
    }
}
