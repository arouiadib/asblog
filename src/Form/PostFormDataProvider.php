<?php

namespace PrestaShop\Module\AsBlog\Form;

use PrestaShop\Module\AsBlog\Model\Post;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository;
use PrestaShop\Module\AsBlog\Repository\PostRepository;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

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
            'title' => $arrayPost['name'],
            'content' => $arrayPost['content'],
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

        return $errors;
    }
}
