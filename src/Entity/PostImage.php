<?php

namespace PrestaShop\Module\AsBlog\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use PrestaShop\Module\AsBlog\Entity\Image;

/**
 * @ORM\Entity()
 */

class PostImage extends Image
{
    /**
     * @ORM\Column(name="id_post", type="integer")
     */
    private $postId;

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @param mixed $postId
     */
    public function setPostId($postId): void
    {
        $this->postId = $postId;
    }
}
