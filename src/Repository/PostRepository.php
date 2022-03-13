<?php

namespace PrestaShop\Module\AsBlog\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;
use Symfony\Component\Translation\TranslatorInterface;


/**
 * Class PostRepository.
 */
class PostRepository
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
     * PostRepository constructor.
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
            "CREATE TABLE IF NOT EXISTS `{$this->dbPrefix}post`(
    			`id_post` int(10) unsigned NOT NULL auto_increment,
    			`active` bool,
    			`date_add` datetime,
    			`id_category` int(10) unsigned NOT NULL,
    			PRIMARY KEY (`id_post`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8",
            "CREATE TABLE IF NOT EXISTS `{$this->dbPrefix}post_lang`(
    			`id_post` int(10) unsigned NOT NULL,
    			`id_lang` int(10) unsigned NOT NULL,
    			`title` varchar(40) NOT NULL default '',
    			`content` text default NULL,
    			`meta_title` text default NULL,
    			`meta_keywords` text default NULL,
    			`meta_description` text default NULL,
    			PRIMARY KEY (`id_post`, `id_lang`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8",
            "CREATE TABLE IF NOT EXISTS `{$this->dbPrefix}post_shop` (
    			`id_post` int(10) unsigned NOT NULL auto_increment,
    			`id_shop` int(10) unsigned NOT NULL,
    			PRIMARY KEY (`id_post`, `id_shop`)
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
            'post_shop',
            'post_lang',
            'post',
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
            ->insert($this->dbPrefix . 'post')
            ->values([
                    'date_add' => ':dateAdd',
                    'active' => ':active',
                    'id_category' => ':id_category'
            ])
            ->setParameters([
                'dateAdd' => date('Y-m-d H:i:s'),
                'active' => $data['active'],
                'id_category' => $data['id_category']
            ])
        ;

        $this->executeQueryBuilder($qb, 'Post error');
        $postId = $this->connection->lastInsertId();

        $this->updateLanguages($postId, $data);

        return $postId;
    }

    /**
     * @param int $postId
     * @param array $data
     *
     * @throws DatabaseException
     */
    public function update($postId, array $data)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->update($this->dbPrefix . 'post', 'p')
            ->andWhere('p.id_post = :postId')
            ->set('date_add', ':dateAdd')
            ->set('active', ':active')
            ->set('id_category', ':idCategory')
            ->setParameters([
                'postId' => $postId,
                'active' => $data['active'],
                'dateAdd' => $data['date_add']->format('d-m-y H:i'),
                'idCategory' => $data['id_category']
            ])
        ;

        $this->executeQueryBuilder($qb, 'Blog post update error');

        $this->updateLanguages($postId, $data);
    }

    /**
     * @param int $idPost
     *
     * @throws DatabaseException
     */
    public function delete($idPost)
    {
        $tableNames = [
            'post_shop',
            'post_lang',
            'post',
        ];

        foreach ($tableNames as $tableName) {
            $qb = $this->connection->createQueryBuilder();
            $qb
                ->delete($this->dbPrefix . $tableName)
                ->andWhere('id_post = :idPost')
                ->setParameter('idPost', $idPost)
            ;
            $this->executeQueryBuilder($qb, 'Delete error');
        }
    }

    /**
     * @param int $postId
     * @param array $blockName
     * @param array $custom
     *
     * @throws DatabaseException
     */
    private function updateLanguages($postId, array $postData)
    {

        foreach ($this->languages as $language) {
            $qb = $this->connection->createQueryBuilder();
            $qb
                ->select('pl.id_post')
                ->from($this->dbPrefix . 'post_lang', 'pl')
                ->andWhere('pl.id_post = :postId')
                ->andWhere('pl.id_lang = :langId')
                ->setParameter('postId', $postId)
                ->setParameter('langId', $language['id_lang'])
            ;
            $foundRows = $qb->execute()->rowCount();

            $qb = $this->connection->createQueryBuilder();
            if (!$foundRows) {
                $qb
                    ->insert($this->dbPrefix . 'post_lang')
                    ->values([
                        'id_post' => ':postId',
                        'id_lang' => ':langId',
                        'title' => ':title',
                        'content' => ':content',
                        'meta_title' => ':metaTitle',
                        'meta_description' => ':metaDescription',
                        'meta_keywords' => ':metaKeywords'
                    ])
                ;
            } else {
                $qb
                    ->update($this->dbPrefix . 'post_lang', 'pl')
                    ->set('title', ':title')
                    ->set('content', ':content')
                    ->set('meta_title', ':metaTitle')
                    ->set('meta_description', ':metaDescription')
                    ->set('meta_keywords', ':metaKeywords')
                    ->andWhere('pl.id_post = :postId')
                    ->andWhere('pl.id_lang = :langId')
                ;
            }

            $qb
                ->setParameters([
                    'postId' => $postId,
                    'langId' => $language['id_lang'],
                    'title' => $postData['title'][$language['id_lang']],
                    'content' => empty($postData['content'][$language['id_lang']]) ? null : $postData['content'][$language['id_lang']],
                    'metaTitle' => $postData['meta_title'][$language['id_lang']],
                    'metaDescription' => $postData['meta_description'][$language['id_lang']],
                    'metaKeywords' => $postData['meta_keywords'][$language['id_lang']],
                ]);

            $this->executeQueryBuilder($qb, 'Post language error');
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

    protected function findPreviousPosts($id_lang = null, $position =  0) {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('p.id_post, pl.meta_title')
            ->from($this->dbPrefix . 'post', 'p')
            ->innerJoin('pl', $this->dbPrefix . 'post_lang', 'pl', 'p.id_post = pl.id_post')
            ->innerJoin('ps', $this->dbPrefix . 'post_shop', 'ps', 'p.id_post = ps.id_post')
            ->andWhere('p.active = 1')
            ->andWhere('p.position = 1 + :position')
            ->andWhere('pl.id_lang = :idLang')
            ->andWhere('ps.id_shop IN (:shopIds)')
            ->setParameter('idLang', $this->idLang)
            ->setParameter('position', $position)
            ->setParameter('shopIds', implode(',', $this->shopIds))
            ->orderBy('p.position')
        ;

        $posts = $qb->execute()->fetchAll();
    }
}
