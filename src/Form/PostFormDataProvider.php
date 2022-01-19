<?php

namespace PrestaShop\Module\AsBlog\Form;

use DateTime;
use PrestaShop\Module\AsBlog\Model\Post;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository;
use PrestaShop\Module\AsBlog\Repository\PostRepository;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

use Language;
/**
 * Class PostFormDataProvider.
 */
class PostFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var int|null
     */
    private $idPost;

    /**
     * @var PostRepository
     */
    private $repository;

    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    /**
     * @var array
     */
    private $languages;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * PostFormDataProvider constructor.
     *
     * @param PostRepository $repository
     * @param ModuleRepository $moduleRepository
     * @param array $languages
     * @param int $shopId
     */
    public function __construct(
        PostRepository $repository,
        ModuleRepository $moduleRepository,
        array $languages,
        $shopId,
        Configuration $configuration
    ) {
        $this->repository = $repository;
        $this->moduleRepository = $moduleRepository;
        $this->languages = $languages;
        $this->shopId = $shopId;
        $this->configuration = $configuration;

    }

    /**
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getData()
    {
        if (null === $this->idPost) {
            return [];
        }

        $post = new Post($this->idPost);
        $arrayPost = $post->toArray();

        return ['post' => [
            'id_post' => $arrayPost['id_post'],
            'title' => $arrayPost['title'],
            'content' => $arrayPost['content'],
            'date_add' => new DateTime($arrayPost['date_add'])
        ]];
    }

    /**
     * Make sure to fill empty multilang fields if value for default is available
     *
     * @param array $post
     *
     * @return array
     */
    public function prepareData(array $post): array
    {
        $defaultLanguageId = (int) $this->configuration->get('PS_LANG_DEFAULT');

        if (!empty($post['title'])) {
            foreach ($this->languages as $language) {
                if (empty($post['title'][$language['id_lang']])) {
                    $post['title'][$language['id_lang']] = $post['title'][$defaultLanguageId];
                }
            }
        }

        return $post;
    }

    /**
     * @param array $data
     *
     * @return array
     *
     * @throws \PrestaShop\PrestaShop\Adapter\Entity\PrestaShopDatabaseException
     */
    public function setData(array $data)
    {
        $post = $this->prepareData($data['post']);

        $errors = $this->validatePost($post);

        if (!empty($errors)) {
            return $errors;
        }

        if (empty($post['id_post'])) {
            $postId = $this->repository->create($post);
            $this->setIdPost($postId);
        } else {
            $postId = $post['id_post'];
            $this->repository->update($postId, $post);
        }

        return [];
    }

    /**
     * @return int
     */
    public function getIdPost()
    {
        return $this->idPost;
    }

    /**
     * @param int $idPost
     *
     * @return PostFormDataProvider
     */
    public function setIdPost($idPost)
    {
        $this->idPost = $idPost;
        return $this;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function validatePost(array $data)
    {
        $errors = [];

        if (!isset($data['title'])) {
            $errors[] = [
                'key' => 'Missing title',
                'domain' => 'Admin.Catalog.Notification',
                'parameters' => [],
            ];
        } else {
            foreach ($this->languages as $language) {
                if (empty($data['title'][$language['id_lang']])) {

                    $errors[] = [
                        'key' => 'Missing title for language %s',
                        'domain' => 'Admin.Catalog.Notification',
                        'parameters' => [$language['iso_code']],
                    ];
                }
            }
        }

        $defaultLanguageId = (int) $this->configuration->get('PS_LANG_DEFAULT');
        $fields = ['content'];
        foreach ($fields as $field) {
            if (empty($data[$field][$defaultLanguageId])) {
                $errors[] = [
                    'key' => 'Missing %s content for language %s',
                    'domain' => 'Admin.Catalog.Notification',
                    'parameters' => [$field, Language::getIsoById($defaultLanguageId)],
                ];
            }
        }

        return $errors;
    }
}
