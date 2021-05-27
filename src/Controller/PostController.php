<?php

namespace PrestaShop\Module\AsBlog\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\ModuleActivated;

/**
 * Class PostController.
 *
 * @ModuleActivated(moduleName="as_blog", redirectRoute="admin_module_manage")
 */
class PostController extends FrameworkBundleAdminController
{
    /**
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        var_dump('hi');die;
        return $this->render('@Modules/as_blog/views/templates/admin/blog_post/list.html.twig', [
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
        $this->get('prestashop.module.as_blog.form_provider')->setIdPost(null);
        $form = $this->get('prestashop.module.as_blog.form_handler')->getForm();

        return $this->render('@Modules/as_blog/views/templates/admin/blog_post/form.html.twig', [
            'postForm' => $form->createView(),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
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

    /**
     * Gets the header toolbar buttons.
     *
     * @return array
     */
    private function getToolbarButtons()
    {
        return [
            'add' => [
                'href' => $this->generateUrl('admin_blog_post_create'),
                'desc' => $this->trans('New post', 'Modules.Asblog.Admin'),
                'icon' => 'add_circle_outline',
            ],
        ];
    }
}
