<?php

namespace PrestaShop\Module\AsBlog\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;


class DateTimeTransformer implements DataTransformerInterface
{
    /**
     * Transforms an object (DateTime) to a string.
     *
     * @param  DateTime|null $datetime
     * @return string
     */
    public function transform($datetime)
    {
        if (null === $datetime) {
            return '';
        }


        return $datetime->format('d/m/Y H:i');
    }

    /**
     * Transforms a string to an object (DateTime).
     *
     * @param  string $datetime
     * @return DateTime|null
     */
    public function reverseTransform($datetime)
    {
        // datetime optional
        if (!$datetime) {
            return;
        }
/*        var_dump($datetime);
        var_dump(date_create_from_format('d/m/Y H:i', $datetime, new \DateTimeZone('Europe/Paris')));die;*/
        return date_create_from_format('d/m/Y H:i', $datetime, new \DateTimeZone('Europe/Madrid'));
    }
}
