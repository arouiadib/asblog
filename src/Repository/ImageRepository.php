<?php

declare(strict_types=1);

namespace PrestaShop\Module\AsBlog\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShop\Module\AsBlog\Entity\CategoryImage;
use PrestaShop\Module\AsBlog\Entity\PostImage;

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
        $image = $this->findOneBy(['idChild' => $imageData['id']]);

        if (!$image) {
            $className = 'PrestaShop\\Module\\AsBlog\\Entity\\'. ucwords($imageData['type']) . 'Image';
            $image = new $className();
            $image->setIdChild((int)$imageData['id']);
        }

        $em = $this->getEntityManager();
        $em->persist($image);
        $em->flush();
    }

    public function deleteImage($imageData)
    {
        $image = $this->findOneBy(['idChild' => $imageData['id']]);

        if (!$image) {
            // throw exception
            //$this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        $em = $this->getEntityManager();
        $em->remove($image);
        $em->flush();
    }
}
