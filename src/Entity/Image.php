<?php

declare(strict_types=1);

namespace PrestaShop\Module\AsBlog\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="ps_blog_image")
 * @ORM\Entity(repositoryClass="PrestaShop\Module\AsBlog\Repository\ImageRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap(
 *   {
 *     "post"  = "PrestaShop\Module\AsBlog\Entity\PostImage",
 *     "category" = "PrestaShop\Module\AsBlog\Entity\CategoryImage"
 *   }
 * )
 */

abstract class Image
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_image", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_child", type="integer")
     */
    private $idChild;

    /**
     * @return int
     */
    public function getIdChild(): int
    {
        return $this->idChild;
    }

    /**
     * @param int $idChild
     */
    public function setIdChild(int $idChild): void
    {
        $this->idChild = $idChild;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

}
