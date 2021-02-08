<?php
/**
 * Created by Vibbe.
 * User: Marcin Domanski (marcin@vibbe.pl)
 * Date: 27.01.2021
 */

namespace Vibbe\ACL\Form;

use App\Form\AbstractForm;
use App\Form\Type\ExtendedChoiceType;
use App\Form\Type\ExtendedEmailType;
use App\Form\Type\ExtendedTextType;
use App\Traits\UserRoleTrait;
use App\Traits\UserTrait;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vibbe\ACL\Repository\ACLRoleRepository;

class AddRoleForm extends AbstractForm
{
    use UserTrait, UserRoleTrait;

    private const TRANSLATION_PAGE = 'pages.role.forms.';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * AccountForm constructor.
     * @param TranslatorInterface $translator
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(TranslatorInterface $translator,
                                UrlGeneratorInterface $urlGenerator,
                                ContainerInterface $container)
    {
        parent::__construct($translator, $urlGenerator);
        $this->container = $container;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($this->urlGenerator->generate('admin_acl_role-store'))
            ->add('id', HiddenType::class)
            ->add('name', ExtendedTextType::class, $this->setFieldOptions([
                'label' => $this->translator->trans(self::TRANSLATION_PAGE . 'labels.name'),
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => $this->translator->trans(self::TRANSLATION_PAGE . 'placeholders.name')
                ],
                'class_input_div' => 'col-sm-10'
            ]));

        $builder->add('save', SubmitType::class, $this->setSubmitButtonOptions([
            'label' => $this->translator->trans('buttons.save')
        ]));
        $builder->add('cancel', ButtonType::class, [
            'label' => $this->translator->trans('buttons.back_to_list'),
            'attr' => [
                'onclick' => 'location.href=\'' . $this->urlGenerator->generate('admin_acl_role-list') . '\'',
                'class' => 'btn btn-link btn-sm'
            ]
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => array(
                'class' => 'form-horizontal'
            )
        ]);
    }

}
