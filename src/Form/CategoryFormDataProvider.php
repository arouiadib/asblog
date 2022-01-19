<?php

namespace PrestaShop\Module\AsBlog\Form;


use PrestaShop\Module\AsBlog\Model\Category;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository;
use PrestaShop\Module\AsBlog\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

use Language;
/**
 * Class CategoryFormDataProvider.
 */
class CategoryFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var int|null
     */
    private $idCategory;

    /**
     * @var CategoryRepository
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
     * CategoryFormDataProvider constructor.
     *
     * @param CategoryRepository $repository
     * @param ModuleRepository $moduleRepository
     * @param array $languages
     * @param int $shopId
     */
    public function __construct(
        CategoryRepository $repository,
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
        if (null === $this->idCategory) {
            return [];
        }

        $category = new Category($this->idCategory);
        $arrayCategory = $category->toArray();


        return ['category' => [
            'id_category' => $arrayCategory['id_category'],
            'id_parent' => $arrayCategory['id_parent'],
            'name' => $arrayCategory['name'],
            'description' => $arrayCategory['description'],
            'active' => $arrayCategory['active'],
            'meta_title' => $arrayCategory['meta_title'],
            'meta_keywords' => $arrayCategory['meta_keywords'],
            'meta_description' => $arrayCategory['meta_description'],
        ]];
    }

    /**
     * Make sure to fill empty multilang fields if value for default is available
     *
     * @param array $category
     *
     * @return array
     */
    public function prepareData(array $category): array
    {
        $defaultLanguageId = (int) $this->configuration->get('PS_LANG_DEFAULT');

        if (!empty($category['description'])) {
            foreach ($this->languages as $language) {
                if (empty($category['description'][$language['id_lang']])) {
                    $category['description'][$language['id_lang']] = $category['description'][$defaultLanguageId];
                }
            }
        }

        if (!empty($category['meta_keywords'])) {
            foreach ($this->languages as $language) {
                if (empty($category['meta_keywords'][$language['id_lang']])) {
                    $category['meta_keywords'][$language['id_lang']] = $category['meta_keywords'][$defaultLanguageId];
                }
            }
        }

        if (!empty($category['meta_description'])) {
            foreach ($this->languages as $language) {
                if (empty($category['meta_description'][$language['id_lang']])) {
                    $category['meta_description'][$language['id_lang']] = $category['meta_description'][$defaultLanguageId];
                }
            }
        }

        if (!empty($category['meta_title'])) {
            foreach ($this->languages as $language) {
                if (empty($category['meta_title'][$language['id_lang']])) {
                    $category['meta_title'][$language['id_lang']] = $category['meta_title'][$defaultLanguageId];
                }
            }
        }

        return $category;
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
        $category = $this->prepareData($data['category']);

        $errors = $this->validateCategory($category);

        if (!empty($errors)) {
            return $errors;
        }

        if (empty($category['id_category'])) {
            $categoryId = $this->repository->create($category);
            $this->setIdCategory($categoryId);
        } else {
            $categoryId = $category['id_category'];
            $this->repository->update($categoryId, $category);
        }


        return [];
    }

    /**
     * @return int
     */
    public function getIdCategory()
    {
        return $this->idCategory;
    }

    /**
     * @param int $idCategory
     *
     * @return CategoryFormDataProvider
     */
    public function setIdCategory($idCategory)
    {
        $this->idCategory = $idCategory;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function validateCategory(array $data)
    {
        $errors = [];

        return $errors;
    }
}
