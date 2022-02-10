<?php

namespace PrestaShop\Module\AsBlog\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use PrestaShop\Module\AsBlog\Entity\Image;

/**
 * @ORM\Entity()
 */

class CategoryImage extends Image
{
    /**
     * @ORM\Column(name="id_category", type="integer")
     */
    private $categoryId;

    /**
     * @return mixed
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param mixed $categoryId
     */
    public function setCategoryId($categoryId): void
    {
        $this->categoryId = $categoryId;
    }
}
