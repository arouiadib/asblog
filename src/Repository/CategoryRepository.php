<?php

namespace PrestaShop\Module\AsBlog\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;
use PNX\NestedSet\Node;
use PNX\NestedSet\NodeKey;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;
use Symfony\Component\Translation\TranslatorInterface;
use PrestaShop\Module\AsBlog\Model\Category;

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
        $idParent = (int) $data['id_parent'];
        $parentCategory = new Category($idParent);
        $categoryId = $this->addNewCategoryBelow($parentCategory, $data);

        $this->updateLanguages($categoryId, $data);

        return $categoryId;
    }


    /**
     * {@inheritdoc}
     */
    public function addNewCategoryBelow(Category $target, $data) {

        //$target = $this->ensureNodeIsFresh($target);
        $newLeftPosition = $target->nright;
        //$depth = $target->getDepth() + 1;
        return $this->insertCategoryAtPosition($newLeftPosition, $data);//, $depth);
    }

    /**
     * Inserts a node to the target position.
     *
     * @param int $newLeftPosition
     *   The new left position.
     * @param int $depth
     *   The new depth.
     *
     * @return \PNX\NestedSet\Node
     *   The new node with updated position.
     *
     * @throws \Exception
     *   If a transaction error occurs.
     */
    protected function insertCategoryAtPosition($newLeftPosition, $data) {
        try {
            $this->connection->setAutoCommit(FALSE);
            $this->connection->beginTransaction();

            $this->connection->executeUpdate('UPDATE ' . $this->dbPrefix . 'post_category SET nright = nright + 2 WHERE nright >= ?',
                [$newLeftPosition]
            );
            $this->connection->executeUpdate('UPDATE ' . $this->dbPrefix . 'post_category SET nleft = nleft + 2  WHERE nleft >= ?',
                [$newLeftPosition]
            );

            $newCategory = $this->doInsertNewCategory($data,  $newLeftPosition, $newLeftPosition + 1);//, $depth);

            $this->connection->commit();
        }
        catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        } finally {
            $this->connection->setAutoCommit(TRUE);
        }
        return $newCategory;

    }


    /**
     * Moves a subtree to a new position.
     *
     * @param int $newLeftPosition
     *   The new left position.
     * @param Category $node
     *   The node to move.
     * @param int $newDepth
     *   Depth of new position.
     *
     * @throws \Exception
     *   If a transaction error occurs.
     */
    protected function moveSubTreeToPosition($newLeftPosition, Category $node) {
        $this->moveMultipleSubTreesToPosition($newLeftPosition, [$node]);
    }

    /**
     * Moves multiple subtrees to a new position.
     *
     * @param int $newLeftPosition
     *   The new left position.
     * @param Category $nodes
     *   The nodes to move.
     * @param int $newDepth
     *   Depth of new position.
     *
     * @throws \Exception
     *   If a transaction error occurs.
     */
    protected function moveMultipleSubTreesToPosition($newLeftPosition, array $nodes) {
        try {
            $firstNode = reset($nodes);
            $lastNode = end($nodes);

            // Calculate position adjustment variables.
            $width = $lastNode->nright - $firstNode->nleft + 1;
            $distance = $newLeftPosition - $firstNode->nleft;
            $tempPos = $firstNode->nleft;

            $this->connection->setAutoCommit(FALSE);
            $this->connection->beginTransaction();

            // Calculate depth difference.
            //$depthDiff = $newDepth - $firstNode->getDepth();

            // Backwards movement must account for new space.
            if ($distance < 0) {
                $distance -= $width;
                $tempPos += $width;
            }

            // Create new space for subtree.
            $this->connection->executeUpdate('UPDATE ' . $this->dbPrefix . 'post_category SET nleft = nleft + ? WHERE nleft >= ?',
                [$width, $newLeftPosition]
            );

            $this->connection->executeUpdate('UPDATE ' . $this->dbPrefix . 'post_category SET nright = nright + ? WHERE nright >= ?',
                [$width, $newLeftPosition]
            );

            // Move subtree into new space.
            $this->connection->executeUpdate('UPDATE ' . $this->dbPrefix . 'post_category SET nleft = nleft + ?, nright = nright + ? WHERE nleft >= ? AND nright < ?',
                [$distance, $distance, $tempPos, $tempPos + $width]
            );

            // Remove old space vacated by subtree.
            $this->connection->executeUpdate('UPDATE ' . $this->dbPrefix . 'post_category SET  nleft = nleft - ? WHERE nleft > ?',
                [$width, $lastNode->nright]
            );

            $this->connection->executeUpdate('UPDATE ' . $this->dbPrefix . 'post_category SET nright = nright  - ? WHERE nright > ?',
                [$width, $lastNode->nright]
            );
            $this->connection->commit();
        }
        catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        } finally {
            $this->connection->setAutoCommit(TRUE);
        }
    }

    /**
     * Inserts a new node by its parameters.
     *
     * @param int $left
     *   The left position.
     * @param int $right
     * @param int $depth
     *   The right position.
     *   The depth.
     *
     * @return int $categoryId
     *
     */
    protected function doInsertNewCategory($data, $left, $right) {

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->insert($this->dbPrefix . 'post_category')
            ->values([
                'id_parent' => ':idParent',
                'active' => ':active',
                'nleft' => ':nleft',
                'nright' => ':nright',
                //'depth' => ':depth'
            ])
            ->setParameters([
                'idParent' => $data['id_parent'],
                'active' => $data['active'],
                'nleft' => $left,
                'nright' => $right,
               // 'depth' => 0
            ])
        ;

        $this->executeQueryBuilder($qb, 'Category error');
        $categoryId = $this->connection->lastInsertId();

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
        $editedCategory = new Category($categoryId);
        $parentCategoryEdited = $editedCategory->id_parent;

        $parentCategory = new Category($data['id_parent']);

        if ( $data['id_parent'] !== $parentCategoryEdited) {
            $this->moveSubTree($parentCategory, $editedCategory);
        }

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
     * {@inheritdoc}
     */
    public function moveSubTree(Category $parent, Category $target) {

        //$target = $this->ensureNodeIsFresh($target);
        $newLeftPosition = $parent->nright;
        //$depth = $target->getDepth() + 1;
        return $this->moveSubTreeToPosition($newLeftPosition, $target);//, $depth);
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
                ->andWhere('pcl.id_category = :categoryId')
                ->andWhere('pcl.id_lang = :langId')
                ->setParameter('categoryId', $categoryId)
                ->setParameter('langId', $language['id_lang'])
            ;
            $foundRows = $qb->execute()->rowCount();

            $qb = $this->connection->createQueryBuilder();
            if (!$foundRows) {
                $qb
                    ->insert($this->dbPrefix . 'post_category_lang')
                    ->values([
                        'id_category' => ':categoryId',
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
     * @param int #idCategory
     *
     * @throws DatabaseException
     */
    public function delete($idCategory)
    {
        $editedCategory = new Category($idCategory);
        $parentCategory = new Category($editedCategory->id_parent);

        $this->deleteNode($editedCategory);

        $tableNames = [
            'post_category',
            'post_category_lang',
            'post_category_shop'
        ];

        foreach ($tableNames as $tableName) {
            $qb =   $this->connection->createQueryBuilder();
            $qb
                ->delete($this->dbPrefix . $tableName)
                ->andWhere('id_category = :idCategory')
                ->setParameter('idCategory', $idCategory)
            ;
            $this->executeQueryBuilder($qb, 'Delete error');
        }

    }


    /**
     * {@inheritdoc}
     */
    public function deleteNode(Category $node) {
        //$node = $this->ensureNodeIsFresh($node);
        if ($node->nleft < 1 || $node->nright < 1) {
            throw new \InvalidArgumentException("Left and right values must be > 0");
        }
        $left = $node->nleft;
        $right = $node->nright;
        //$width = $right - $left + 1;

        try {
            $this->connection->setAutoCommit(FALSE);
            $this->connection->beginTransaction();

            // Delete the node.
            $this->connection->executeUpdate('DELETE FROM ' . $this->dbPrefix . 'post_category  WHERE nleft = ?',
                [$left]
            );

            // Move children up a level.
            $this->connection->executeUpdate('UPDATE ' . $this->dbPrefix . 'post_category SET nright = nright - 1, nleft = nleft - 1 WHERE nleft BETWEEN ? AND ?',
                [$left, $right]
            );

            // Move everything back two places.
            $this->connection->executeUpdate('UPDATE ' . $this->dbPrefix . 'post_category SET nright = nright - 2 WHERE nright > ?',
                [$right]
            );
            $this->connection->executeUpdate('UPDATE ' . $this->dbPrefix . 'post_category SET nleft = nleft - 2 WHERE nleft > ?',
                [$right]
            );

            $this->connection->commit();

        }
        catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        } finally {
            $this->connection->setAutoCommit(TRUE);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function findDescendants(NodeKey $nodeKey, $depth = 0, $start = 1) {
        $descendants = [];
        $query = $this->connection->createQueryBuilder();
        $query->select('child.id_category', 'child.nleft', 'child.nright', 'child.depth')
            ->from($this->tableName, 'child')
            ->from($this->tableName, 'parent')
            ->andWhere('parent.id = :id')
            ->andwhere('child.left_pos > parent.left_pos')
            ->andWhere('child.right_pos < parent.right_pos')
            ->orderBy('child.left_pos', 'ASC')
            ->setParameter(':id', $nodeKey->getId());
        if ($start > 0) {
            $query->andWhere('child.depth >= :start_depth + parent.depth')
                ->setParameter(':start_depth', $start);
        }

        $stmt = $query->execute();
        while ($row = $stmt->fetch()) {
            $descendants[] = new Node(new NodeKey($row['id'], $row['revision_id']), $row['left_pos'], $row['right_pos'], $row['depth']);
        }
        return $descendants;
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
