<?php

namespace PrestaShop\Module\AsBlog\Controller\Admin;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PostController.
 *
 * @ModuleActivated(moduleName="as_blog", redirectRoute="admin_module_manage")
 */
class PostController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        return $this->render('@Modules/as_blog/views/templates/admin/blog_post/list.html.twig', [
        ]);
    }

    /**
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function createAction(Request $request)
    {
        return $this->render('@Modules/as_blog/views/templates/admin/blog_post/form.html.twig', [
        ]);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param int $linkBlockId
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function editAction(Request $request, $linkBlockId)
    {
        return $this->render('@Modules/as_blog/views/templates/admin/blog_post/form.html.twig', [
        ]);
    }

    /**
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function createProcessAction(Request $request)
    {
        return $this->processForm($request, 'Successful creation.');
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param int $linkBlockId
     *
     * @return RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function editProcessAction(Request $request, $blogPostId)
    {
        return $this->processForm($request, 'Successful update.', $blogPostId);
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param int $linkBlockId
     *
     * @return RedirectResponse
     */
    public function deleteAction($blogPostId)
    {
        return $this->redirectToRoute('admin_blog_post_list');
    }

    /**
     * @param Request $request
     * @param string $successMessage
     * @param int|null $linkBlockId
     *
     * @return Response|RedirectResponse
     *
     * @throws \Exception
     */
    private function processForm(Request $request, $successMessage, $blogPostId = null)
    {
        return $this->render('@Modules/as_blog/views/templates/admin/blog_post/form.html.twig', [
        ]);
    }
}
