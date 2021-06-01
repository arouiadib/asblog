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
 * Class PostDefinitionFactory.
 */
final class PostDefinitionFactory extends AbstractGridDefinitionFactory
{
    const FACTORY_ID = 'blog_post_grid_';

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
        return 'post grid';
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new DataColumn('id_post'))
                ->setName($this->trans('ID', [], 'Modules.Asblog.Admin'))
                ->setOptions([
                    'field' => 'id_post',
                ])
            )
            ->add((new DataColumn('title'))
                ->setName($this->trans('Title', [], 'Modules.Asblog.Admin'))
                ->setOptions([
                    'field' => 'title',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_blog_post_edit',
                                'route_param_name' => 'post_id',
                                'route_param_field' => 'id_post',
                            ])
                        )
                        ->add((new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'method' => 'POST',
                                'route' => 'admin_blog_post_delete',
                                'route_param_name' => 'post_id',
                                'route_param_field' => 'id_post',
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
