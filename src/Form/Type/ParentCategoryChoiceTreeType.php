<?php

namespace PrestaShop\Module\AsBlog\Form\Type;

use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTreeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CategoryChoiceTreeType.
 */
class ParentCategoryChoiceTreeType extends AbstractType
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
        return MaterialChoiceTreeType::class;
    }
}
