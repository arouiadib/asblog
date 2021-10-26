<?php

namespace PrestaShop\Module\AsBlog\Form\ChoiceProvider;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class AbstractDatabaseChoiceProvider.
 */
abstract class AbstractDatabaseChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $dbPrefix;

    /**
     * @var int
     */
    protected $idLang;

    /**
     * @var array
     */
    protected $shopIds;

    /**
     * AbstractDatabaseChoiceProvider constructor.
     *
     * @param Connection $connection
     * @param string $dbPrefix
     * @param int|null $idLang
     * @param array|null $shopIds
     */
    public function __construct(Connection $connection, $dbPrefix, $idLang = null, array $shopIds = null)
    {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->idLang = $idLang;
        $this->shopIds = $shopIds;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getChoices();
}
