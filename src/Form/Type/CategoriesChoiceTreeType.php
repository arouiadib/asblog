<?php

namespace PrestaShop\Module\AsBlog\Form\Type;

use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CategoriesChoiceTreeType.
 */
class CategoriesChoiceTreeType extends AbstractType
{
    /**
     * @var array
     */
    private $categoryTreeChoices;

    /**
     * @param array $categoryTreeChoices
     */
    public function __construct(array $categoryTreeChoices)
    {
        //echo "<pre>";
        //var_dump($categoryTreeChoices);die;
        $this->categoryTreeChoices = $categoryTreeChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices_tree' => $this->categoryTreeChoices,
            'choice_label' => 'name',
            'choice_value' => 'id_category',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return MaterialChoiceTableType::class;
    }
}
