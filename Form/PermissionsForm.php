<?php
/**
 * Created by Vibbe.
 * User: Marcin Domanski (marcin@vibbe.pl)
 * Date: 27.01.2021
 */

namespace Vibbe\ACL\Form;

use App\Form\AbstractForm;
use App\Form\Type\CustomTextType;
use App\Form\Type\ExtendedCheckboxType;
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
use Vibbe\ACL\Entity\ACLRole;
use Vibbe\ACL\Repository\ACLRoleRepository;
use Vibbe\ACL\Service\AccessService;

class PermissionsForm extends AbstractForm
{

    private const TRANSLATION_PAGE = 'pages.acl_role_permissions.forms.';

    /**
     * @var ContainerInterface
     */
    private $container;
    /** @var AccessService */
    private $accessService;

    /**
     * AccountForm constructor.
     * @param TranslatorInterface $translator
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(TranslatorInterface $translator,
                                UrlGeneratorInterface $urlGenerator,
                                AccessService $accessService,
                                ContainerInterface $container)
    {
        parent::__construct($translator, $urlGenerator);
        $this->container = $container;
        $this->accessService = $accessService;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ACLRole $role */
        $role = $options['role'];
        $activePermissions = [];
        foreach($role->getPermissions() as $permission) {
            if($permission->getIsAllowed() == 1) {
                $activePermissions[] = $permission->getPermission();
            }
        }
        $builder->setAction($this->urlGenerator->generate('admin_acl_role-permissions-store', ['id' => $role->getId()]));
        foreach ($this->accessService->getAllAvailableActions() as $group => $actions) {
            $builder->add($group . '_dummy', ExtendedCheckboxType::class, $this->setCheckboxFieldOptions([
                'mapped' => true,
                'label' => $this->translator->trans(self::TRANSLATION_PAGE . $group . '.group_title'),
                'label_html' => true,
                'required' => false,
                'iCheck' => false,
                'class_input_div' => 'form-group-div',
                'label_attr' => [
                    'class' => 'form-group-label form-group-label-parent'
                ],
                'attr' => [
                    'class' => '',
                    'acl_group' => $group,
                ]
            ]));
            foreach ($actions as $action) {
                $isChecked = in_array($group.'-'.$action, $activePermissions)? ' is-checked':'';
                $builder->add($group . '::--::' . $action, ExtendedCheckboxType::class, $this->setCheckboxFieldOptions([
                    'mapped' => true,
                    'label' => $this->translator->trans(self::TRANSLATION_PAGE . $group.'.'.$action),
                    'label_html' => true,
                    'required' => false,
                    'class_input_div' => 'form-group-div is-nested ' . $isChecked,
                    'data' => in_array($group.'-'.$action, $activePermissions),
                    'label_attr' => [
                        'class' => 'form-group-label'
                    ],
                    'attr' => [
                        'class' => '',
                        'parent' => $group,
                        'checked' => in_array($group.'-'.$action, $activePermissions)
                    ]
                ]));
            }
        }

        $builder->add('save', SubmitType::class, $this->setSubmitButtonOptions([
            'label' => $this->translator->trans('buttons.save'),
            'attr' => [
                'class' => 'btn btn-success btn-permission btn-rounded'
            ]
        ]));
        $builder->add('cancel', ButtonType::class, [
            'label' => $this->translator->trans('buttons.back_to_list'),
            'attr' => [
                'onclick' => 'location.href=\'' . $this->urlGenerator->generate('admin_acl_role-list') . '\'',
                'class' => 'btn btn-link btn-sm btn-cancel'
            ]
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('role');
        $resolver->setAllowedTypes('role', array(ACLRole::class));

        $resolver->setDefaults([
            'attr' => array(
                'class' => 'form-horizontal'
            )
        ]);
    }

}
