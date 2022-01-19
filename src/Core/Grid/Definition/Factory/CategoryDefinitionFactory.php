<?php

namespace PrestaShop\Module\AsBlog\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;

/**
 * Class CategoryDefinitionFactory.
 */
final class CategoryDefinitionFactory extends AbstractGridDefinitionFactory
{
    const FACTORY_ID = 'blog_category_grid_';

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::FACTORY_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return 'category grid';
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new DataColumn('id_category'))
                ->setName($this->trans('ID', [], 'Modules.Asblog.Admin'))
                ->setOptions([
                    'field' => 'id_category',
                ])
            )
/*            ->add((new DataColumn('date'))
                ->setName($this->trans('Date', [], 'Modules.Asblog.Admin'))
                ->setOptions([
                    'field' => 'date_add',
                ])
            )
            ->add((new DataColumn('title'))
                ->setName($this->trans('Title', [], 'Modules.Asblog.Admin'))
                ->setOptions([
                    'field' => 'title',
                ])
            )*/
            ->add((new ActionColumn('actions'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_blog_category_edit',
                                'route_param_name' => 'category_id',
                                'route_param_field' => 'id_category',
                            ])
                        )
                        ->add((new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'method' => 'POST',
                                'route' => 'admin_blog_category_delete',
                                'route_param_name' => 'category_id',
                                'route_param_field' => 'id_category',
                                'confirm_message' => $this->trans(
                                    'Delete selected item?',
                                    [],
                                    'Admin.Notifications.Warning'
                                ),
                            ])
                        ),
                ])
            )
        ;
    }
}
