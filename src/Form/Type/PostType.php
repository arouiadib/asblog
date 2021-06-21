<?php

namespace PrestaShop\Module\AsBlog\Form\Type;

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
                            'max' => 150,
                            'maxMessage' => $this->translator->trans(
                                'Name of the block cannot be longer than %limit% characters',
                                [
                                    '%limit%' => 150
                                ],
                                'Modules.Asblog.Admin'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('content',  TranslateType::class, [
                'required' => false,
                'locales' => $this->locales,
                'label' => $this->trans('Summary', 'Admin.Global'),
                'type' => FormattedTextareaType::class,
                'options' => [
                    'limit' => 200,
                    'attr' => [
                        'class' => 'serp-default-description',
                    ],
                    'constraints' => [
                        new Length([
                            'max' => 500,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters.',
                                'Admin.Notifications.Error',
                                [
                                    '%limit%' => 500,
                                ]
                            ),
                        ]),
                    ],
                ],
                'label_tag_name' => 'h2',
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
        return 'module_post';
    }
}
