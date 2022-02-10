<?php

declare(strict_types=1);

namespace PrestaShop\Module\AsBlog\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShop\Module\AsBlog\Entity\PostImage;
use PrestaShop\Module\AsBlog\Entity\CategoryImage;

/**
 * Class ImageRepository
 * @package PrestaShop\Module\DemoExtendSymfonyForm\Repository
 */
class ImageRepository extends EntityRepository
{
    /**
     * @param $imageData
     */
    public function upsertImage($imageData)
    {
        /** @var Image $image */
        $image = $this->findOneBy(['id' => $imageData['id']]);

        if (!$image) {
            if ($imageData['type'] === 'category') {
                $image = new CategoryImage();
                $image->setCategoryId($imageData['id']);
            }
            elseif ($imageData['type'] === 'post') {
                $image = new PostImage();
                $image->setPostId($imageData['id']);
            }
        }

        $em = $this->getEntityManager();
        $em->persist($image);
        $em->flush();
    }
}
