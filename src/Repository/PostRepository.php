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
                    'date_add' => ':dateAdd'
            ])
            ->setParameters([
                'dateAdd' => date('Y-m-d H:i:s')
            ])
        ;

        $this->executeQueryBuilder($qb, 'Post error');
        $postId = $this->connection->lastInsertId();

        $this->updateLanguages($postId, $data['title'], $data['content']);

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
            ->setParameters([
                'postId' => $postId,
                'dateAdd' => $data['date_add']->format('d-m-y H:i')
            ])
        ;

        $this->executeQueryBuilder($qb, 'Blog post update error');

        $this->updateLanguages($postId, $data['title'], $data['content']);
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
    			PRIMARY KEY (`id_post`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8",
            "CREATE TABLE IF NOT EXISTS `{$this->dbPrefix}post_lang`(
    			`id_post` int(10) unsigned NOT NULL,
    			`id_lang` int(10) unsigned NOT NULL,
    			`title` varchar(40) NOT NULL default '',
    			`content` text default NULL,
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
     * @param int $postId
     * @param array $blockName
     * @param array $custom
     *
     * @throws DatabaseException
     */
    private function updateLanguages($postId, array $postTitle, array $postContent)
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
                    ])
                ;
            } else {
                $qb
                    ->update($this->dbPrefix . 'post_lang', 'pl')
                    ->set('title', ':title')
                    ->set('content', ':content')
                    ->andWhere('pl.id_post = :postId')
                    ->andWhere('pl.id_lang = :langId')
                ;
            }

            $qb
                ->setParameters([
                    'postId' => $postId,
                    'langId' => $language['id_lang'],
                    'title' => $postTitle[$language['id_lang']],
                    'content' => empty($postContent) ? null : $postContent[$language['id_lang']],
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
}
