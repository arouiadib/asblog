<?php

namespace PrestaShop\Module\AsBlog\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\ModuleActivated;
use PrestaShop\Module\AsBlog\Core\Search\Filters\CategoryFilters;
use PrestaShop\Module\AsBlog\Entity\CategoryImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @param int $category_id
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
            'categoryCoverImage' => $this->getCategoryCoverImageUrl($category_id),
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
                $data = $form->getData();
                $this->uploadImage($data);

                $saveErrors = $formHandler->save($data);

                if (0 === count($saveErrors)) {
                    $this->addFlash('success', $this->trans($successMessage, 'Admin.Notifications.Success'));

                    return $this->redirectToRoute('admin_blog_category_list');
                }
                $this->flashErrors($saveErrors);
            }
            $formErrors = [];
            foreach ($form->getErrors(true) as $error) {
                $formErrors[] = $error->getMessage();
            }

            $this->flashErrors($formErrors);
        }

        return $this->render('@Modules/asblog/views/templates/admin/blog_category/form.html.twig', [
            'categoryForm' => $form->createView(),
            'categoryCoverImage' => $this->getCategoryCoverImageUrl($categoryId),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     *
     * @param int $category_id
     *
     * @return RedirectResponse
     */
    public function deleteAction($category_id)
    {
        $postRepository = $this->get('prestashop.module.asblog.category.repository');
        $errors = [];
        try {
            $postRepository->delete($category_id);
        }
        catch (DatabaseException $exception) {
            $errors[] = [
                'key' => 'Cannot delete category with ID #%i',
                'domain' => 'Admin.Catalog.Notification',
                'parameters' => [$category_id]
            ];
        }

        if (!count($errors)) {
            $this->addFlash('Success', 'Admin.Notifications.Success' );
        } else {
            $this->flashErrors($errors);
        }
        return $this->redirectToRoute('admin_blog_post_list');
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
        $filtersParams['filters']['id_lang'] = $this->getContext()->language->id;

        return $filtersParams;
    }

    /**
     * @param array $params
     */
    private function uploadImage(array $params): void
    {

        /** @var ImageUploaderInterface $marqueImageUploader */
        $imageUploader = $this->get('prestashop.module.asblog.uploader.image_uploader');

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $params['category']['upload_image_file'];

        $imageData['type'] = 'category';
        $imageData['id'] = $params['category']['id_category'];

        if ($uploadedFile instanceof UploadedFile) {
            $imageUploader->upload($imageData, $uploadedFile);
        }
    }

    private  function getCategoryCoverImageUrl($categoryId) {

        $categoryImageRepository = $this->get('doctrine.orm.entity_manager')->getRepository(CategoryImage::class);

        $image = $categoryImageRepository->findOneBy(['idChild' => $categoryId]);

        if ($image && file_exists(_PS_IMG_DIR_ . '/blog/category/' . $categoryId . '.jpeg')) {
            $imageUrl = '/img/blog/category/' . $categoryId . '.jpeg';
        }

        return (isset($imageUrl)) ? $imageUrl : null;
    }

    /**
     * Deletes post cover image.
     *
     * @param Request $request
     * @param int $postId
     *
     * @return RedirectResponse
     */
    public function deleteCoverImageAction(Request $request, $id_category)
    {
        $imageData = [];
        if (!$this->isCsrfTokenValid('delete-cover-image', $request->request->get('_csrf_token'))) {
            return $this->redirectToRoute('admin_security_compromised', [
                'uri' => $this->generateUrl('admin_blog_category_edit', [
                    'category_id' => $id_category,
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
        }

        try {
            $imageUploader = $this->get('prestashop.module.asblog.uploader.image_uploader');
            $imageData['type'] = 'category';
            $imageData['id'] = $id_category;

            $imageUploader->deleteImage($imageData);
            $this->addFlash(
                'success',
                $this->trans('The image was successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_blog_category_edit', [
            'category_id' =>  $id_category,
        ]);
    }
}
