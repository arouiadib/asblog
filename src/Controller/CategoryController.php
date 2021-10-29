<?php

namespace PrestaShop\Module\AsBlog\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\ModuleActivated;
use PrestaShop\Module\AsBlog\Core\Search\Filters\CategoryFilters;
/**
 * Class CategoryController.
 *
 * @ModuleActivated(moduleName="asblog", redirectRoute="admin_module_manage")
 */
class CategoryController extends FrameworkBundleAdminController
{
    /**
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        $filtersParams = $this->buildFiltersParamsByRequest($request);

        /** @var CategoryGridFactory $categoryGridFactory */
        $categoryGridFactory = $this->get('prestashop.module.asblog.category.grid.factory');
        $grid = $categoryGridFactory->getGrid($filtersParams);
        $presentedGrid = $this->presentGrid($grid);


        return $this->render('@Modules/asblog/views/templates/admin/list.html.twig', [
            'grid' => $presentedGrid,
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }
    /**
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $this->get('prestashop.module.asblog.category.form_provider')->setIdCategory(null);
        $form = $this->get('prestashop.module.asblog.category.form_handler')->getForm();

        return $this->render('@Modules/asblog/views/templates/admin/blog_category/form.html.twig', [
            'categoryForm' => $form->createView(),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     *
     * @param Request $request
     * @param int $categoryId
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function editAction(Request $request, $category_id)
    {
        $this->get('prestashop.module.asblog.category.form_provider')->setIdCategory($category_id);
        $form = $this->get('prestashop.module.asblog.category.form_handler')->getForm();

        return $this->render('@Modules/asblog/views/templates/admin/blog_category/form.html.twig', [
            'categoryForm' => $form->createView(),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function processCreateAction(Request $request)
    {
        return $this->processForm($request, 'Successful creation.');
    }

    /**
     *
     * @param Request $request
     * @param int $category_id
     *
     * @return RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function processEditAction(Request $request, $category_id)
    {
        return $this->processForm($request, 'Successful update.', $category_id);
    }


    /**
     * @param Request $request
     * @param string $successMessage
     * @param int|null $categoryId
     *
     * @return Response|RedirectResponse
     *
     * @throws \Exception
     */
    private function processForm(Request $request, $successMessage, $categoryId = null)
    {
        $this->get('prestashop.module.asblog.category.form_provider')->setIdCategory($categoryId);

        $formHandler = $this->get('prestashop.module.asblog.category.form_handler');
        $form = $formHandler->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $saveErrors = $formHandler->save($form->getData());

                if (0 === count($saveErrors)) {
                    $this->addFlash('success', $this->trans($successMessage, 'Admin.Notifications.Success'));

                    return  $this->redirectToRoute('admin_blog_category_list');
                }
                $formErrors = [];
                foreach ($form->getErrors(true) as $error) {
                    $formErrors[] = $error->getMessage();
                }

                $this->flashErrors($formErrors);
            }
        }

        return $this->render('@Modules/asblog/views/templates/admin/blog_category/form.html.twig', [
            'categoryForm' => $form->createView(),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Gets the header toolbar buttons.
     *
     * @return array
     */
    private function getToolbarButtons()
    {
        return [
            'add' => [
                'href' => $this->generateUrl('admin_blog_category_create'),
                'desc' => $this->trans('New category', 'Modules.Asblog.Admin'),
                'icon' => 'add_circle_outline',
            ],
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function buildFiltersParamsByRequest(Request $request)
    {
        $filtersParams = array_merge(CategoryFilters::getDefaults(), $request->query->all());
        return $filtersParams;
    }
}
