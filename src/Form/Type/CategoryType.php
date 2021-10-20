<?php

namespace PrestaShop\Module\AsBlog\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use PrestaShopBundle\Form\Admin\Type\TranslateTextType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\Length;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;


class CategoryType extends TranslatorAwareType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * PostType constructor.
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales
    ) {
        parent::__construct($translator, $locales);
        $this->translator = $translator;
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
            ->add('description',  TranslateType::class, [
                'required' => false,
                'locales' => $this->locales,
                'label' => $this->trans('Summary', 'Admin.Global'),
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
                'label_tag_name' => 'h2',
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