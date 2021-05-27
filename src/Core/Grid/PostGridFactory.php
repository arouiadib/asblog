<?php


namespace PrestaShop\Module\AsBlog\Core\Grid;

use PrestaShop\Module\AsBlog\Core\Grid\Definition\Factory\PostDefinitionFactory;
use PrestaShop\Module\AsBlog\Core\Search\Filters\PostFilters;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\GridFilterFormFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Grid;
use PrestaShop\PrestaShop\Core\Grid\GridFactory;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class PostGridFactory.
 */
final class PostGridFactory
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var GridDataFactoryInterface
     */
    private $dataFactory;

    /**
     * @var GridFilterFormFactoryInterface
     */
    private $filterFormFactory;

    /**
     * HookGridFactory constructor.
     *
     * @param TranslatorInterface $translator
     * @param HookDispatcherInterface $hookDispatcher
     * @param GridDataFactoryInterface $dataFactory
     * @param GridFilterFormFactoryInterface $filterFormFactory
     */
    public function __construct(
        TranslatorInterface $translator,
        GridDataFactoryInterface $dataFactory,
        HookDispatcherInterface $hookDispatcher,
        GridFilterFormFactoryInterface $filterFormFactory
    ) {
        $this->translator = $translator;
        $this->hookDispatcher = $hookDispatcher;
        $this->dataFactory = $dataFactory;
        $this->filterFormFactory = $filterFormFactory;
    }

    /**
     * @param array $hooks
     * @param array $filtersParams
     *
     * @return Grid[]
     */
    public function getGrid(array $filtersParams)
    {
        $filters = new PostFilters($filtersParams);
        $gridFactory = $this->buildGridFactory();
        $grid = $gridFactory->getGrid($filters);

        return $grid;
    }

    /**

     * @return GridFactory
     */
    private function buildGridFactory()
    {
        $definitionFactory = new PostDefinitionFactory();
        $definitionFactory->setTranslator($this->translator);
        $definitionFactory->setHookDispatcher($this->hookDispatcher);

        return new GridFactory(
            $definitionFactory,
            $this->dataFactory,
            $this->filterFormFactory
        );
    }
}
