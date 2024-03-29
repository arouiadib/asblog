<?php

namespace PrestaShop\Module\AsBlog\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use PrestaShopBundle\Form\Admin\Type\TranslateTextType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\Length;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\Module\AsBlog\Form\DataTransformer\DateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;

class PostType extends TranslatorAwareType
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
            ->add('id_post', HiddenType::class)
            ->add('title', TranslateTextType::class, [
                'locales' => $this->locales,
                'required' => true,
                'label' => $this->trans('Title of the post', 'Modules.AsBlog.Admin'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new Length([
                            'max' => 1500,
                            'maxMessage' => $this->translator->trans(
                                'Name of the block cannot be longer than %limit% characters',
                                [
                                    '%limit%' => 1500
                                ],
                                'Modules.Asblog.Admin'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('id_category', ParentCategoryChoiceTreeType::class, [
                'required' => true,
                'label' => $this->trans('Category', 'Modules.AsBlog.Admin'),
            ])
            ->add('summary', TranslatableType::class, [
                'type' => TextareaType::class,
                'locales' => $this->locales,
                'required' => true,
                'label' => $this->trans('Summary', 'Modules.Asblog.Admin'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new Length([
                            'max' => 450,
                            'maxMessage' => $this->translator->trans(
                                'Summary cannot be more than %limit% characters',
                                [
                                    '%limit%' => 450
                                ],
                                'Modules.Asblog.Admin'
                            ),
                        ]),
                    ],
                ],
                'attr' => array('style' => 'meta_description_field ')
            ])
            ->add('content',  TranslateType::class, [
                'required' => false,
                'locales' => $this->locales,
                'hideTabs' => false,
                'label' => $this->trans('Summary', 'Admin.Global'),
                'type' => FormattedTextareaType::class,
                'options' => [
                    'limit' => 200,
                    'attr' => [
                        'class' => 'serp-default-description',
                    ],
                    'constraints' => [
                        new Length([
                            'max' => 100000,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters.',
                                'Admin.Notifications.Error',
                                [
                                    '%limit%' => 100000,
                                ]
                            ),
                        ]),
                    ],
                ],
                //'label_tag_name' => 'h2',
            ])
            ->add('meta_keywords', TranslateTextType::class, [
                'locales' => $this->locales,
                'required' => false,
                'label' => $this->trans('Meta Keywords', 'Modules.Asblog.Admin'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new Length([
                            'max' => 1000,
                            'maxMessage' => $this->translator->trans(
                                'Meta Keywords cannot be more than %limit% characters',
                                [
                                    '%limit%' => 1000
                                ],
                                'Modules.Asblog.Admin'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('meta_title', TranslateTextType::class, [
                'locales' => $this->locales,
                'required' => false,
                'label' => $this->trans('Meta Title', 'Modules.Asblog.Admin'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new Length([
                            'max' => 1000,
                            'maxMessage' => $this->translator->trans(
                                'Meta Keywords cannot be more than %limit% characters',
                                [
                                    '%limit%' => 1000
                                ],
                                'Modules.Asblog.Admin'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('meta_description', TranslatableType::class, [
                'locales' => $this->locales,
                'required' => false,
                'label' => $this->trans('Meta Description', 'Modules.Asblog.Admin'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new Length([
                            'max' => 1500,
                            'maxMessage' => $this->translator->trans(
                                'Meta description cannot be more than %limit% characters',
                                [
                                    '%limit%' => 1500
                                ],
                                'Modules.Asblog.Admin'
                            ),
                        ]),
                    ],
                ],
                'attr' => array('style' => 'meta_description_field ')
            ])
            ->add('upload_image_file', FileType::class, [
                'label' => $this->trans('Featured image', 'Modules.AsBlog.Admin'),
                'required' => false,
            ])
            ->add('date_add', TextType::class, [
                'label' => $this->trans('Published at', 'Modules.AsBlog.Admin'),
                'required' => true,
                'attr' => array(
                    'class' => 'form-control input-inline datetimepicker',
                    'data-provide' => 'datepicker',
                    'data-format' => 'dd-mm-yyyy HH:ii',
                )
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

        $builder->get('date_add')
            ->addModelTransformer(new DateTimeTransformer());
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
        return 'module_post';
    }
}
