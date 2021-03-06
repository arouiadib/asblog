<?php

namespace PrestaShop\Module\AsBlog\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use PrestaShopBundle\Form\Admin\Type\TranslateTextType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\Length;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CategoryType extends TranslatorAwareType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $choices;

    /**
     * PostType constructor.
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $parentCategoryChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $parentCategoryChoices
    ) {
        parent::__construct($translator, $locales);
        $this->translator = $translator;
        $this->choices = $parentCategoryChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id_category', HiddenType::class)
            ->add('name', TranslateTextType::class, [
                'locales' => $this->locales,
                'required' => true,
                'label' => $this->trans('Name of the category', 'Modules.AsBlog.Admin'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new Length([
                            'max' => 150,
                            'maxMessage' => $this->translator->trans(
                                'Name of the category cannot be longer than %limit% characters',
                                [
                                    '%limit%' => 150
                                ],
                                'Modules.Asblog.Admin'
                            ),
                        ]),
                    ],
                ],
            ])
           ->add('id_parent', ParentCategoryChoiceTreeType::class, [
                'disabled_values' => [],
               'required' => true,
                'label' => $this->trans('Parent category', 'Modules.AsBlog.Admin'),
            ])
            ->add('upload_image_file', FileType::class, [
                'label' => $this->trans('Featured image', 'Modules.AsBlog.Admin'),
                'required' => false,
            ])
            ->add('description',  TranslateType::class, [
                'required' => false,
                'hideTabs' => false,
                'locales' => $this->locales,
                'label' => $this->trans('Summary', 'Modules.AsBlog.Admin'),
                'type' => FormattedTextareaType::class,
                'options' => [
                    'limit' => 150000,
                    'attr' => [
                        'class' => 'serp-default-description',
                    ],
                    'constraints' => [
                        new Length([
                            'max' => 150000,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters.',
                                'Admin.Notifications.Error',
                                [
                                    '%limit%' => 150000,
                                ]
                            ),
                        ]),
                    ],
                ],
                //'label_tag_name' => 'h2',
            ])
            ->add('meta_keywords', TranslateTextType::class, [
                'locales' => $this->locales,
                'required' => true,
                'label' => $this->trans('Meta Keywords', 'Modules.Asblog.Admin'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new Length([
                            'max' => 40,
                            'maxMessage' => $this->translator->trans(
                                'Meta Keywords cannot be more than %limit% characters',
                                [
                                    '%limit%' => 40
                                ],
                                'Modules.Asblog.Admin'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('meta_title', TranslateTextType::class, [
                'locales' => $this->locales,
                'required' => true,
                'label' => $this->trans('Meta Title', 'Modules.Asblog.Admin'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new Length([
                            'max' => 40,
                            'maxMessage' => $this->translator->trans(
                                'Meta Keywords cannot be more than %limit% characters',
                                [
                                    '%limit%' => 40
                                ],
                                'Modules.Asblog.Admin'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('meta_description', TranslateTextType::class, [
                'locales' => $this->locales,
                'required' => true,
                'label' => $this->trans('Meta Description', 'Modules.Asblog.Admin'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new Length([
                            'max' => 40,
                            'maxMessage' => $this->translator->trans(
                                'Meta description cannot be more than %limit% characters',
                                [
                                    '%limit%' => 40
                                ],
                                'Modules.Asblog.Admin'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('active', SwitchType::class, [
                // Customized choices with ON/OFF instead of Yes/No
                'label' => $this->trans('Active', 'Modules.AsBlog.Admin'),
            ])
            ->add('link_rewrite', TranslateTextType::class, [
                'locales' => $this->locales,
                'required' => false,
                'label' => $this->trans('Link rewrite', 'Modules.Asblog.Admin'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
            ])
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'module_post_category';
    }

}
