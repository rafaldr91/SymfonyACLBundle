<?php

namespace Vibbe\ACL\Controller;

use App\Exceptions\NotFoundException;
use App\Service\SimpleValidator\Exceptions\ValidationException;
use Omines\DataTablesBundle\DataTable;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Vibbe\ACL\AccessServiceAwareTrait;
use Vibbe\ACL\Datatable\RoleDatatable;
use Vibbe\ACL\Entity\UserRole;
use App\Utils\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Vibbe\ACL\Form\AddRoleForm;
use Vibbe\ACL\Form\PermissionsForm;
use Vibbe\ACL\Permissions\ACLPermissions;
use Vibbe\ACL\Presenter\RolePresenter;
use Vibbe\ACL\Processor\RoleProcessor;
use Vibbe\ACL\Service\AccessService;

class ACLController extends AbstractController
{

    use AccessServiceAwareTrait;

    /**
     * @var RoleDatatable
     */
    private $roleDatatable;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;
    /** @var RoleProcessor */
    private $processor;
    /** @var RolePresenter */
    private $presenter;


    /**
     * UsersController constructor.
     * @param UserDatatable $roleDatatable
     * @param UserProcessor $processor
     * @param RolePresenter $presenter
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(RoleDatatable $roleDatatable,
                                RoleProcessor $processor,
                                RolePresenter $presenter,
                                UrlGeneratorInterface $urlGenerator)
    {
        $this->roleDatatable = $roleDatatable;
        $this->processor = $processor;
        $this->presenter = $presenter;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/admin/role-list", name="dashboard.acl.role-list")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function roleList(Request $request): Response
    {
        $this->control(ACLPermissions::ACL_ROLE_READ);
        /** @var DataTable $datatable */
        $datatable = $this->roleDatatable
            ->setRequest($request)
            ->create();


        if ($datatable->isCallback()) {
            return $datatable->getResponse();
        };

        return $this->render('@VibbeACL/role/index.html.twig', [
            'datatable' => $datatable
        ]);
    }

    public function addRole(Request $request)
    {
        try {
            $form = $this->createForm(AddRoleForm::class)->createView();
        } catch (\Throwable $e) {
            return $this->presenter->handleException($e, $request);
        }

        return $this->render('@VibbeACL/role/create.html.twig',
            array_merge([
                'form' => $form,
                'tab' => 'account-data',
            ])
        );
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(int $id, Request $request)
    {
        $this->control(ACLPermissions::ACL_ROLE_DELETE);
        $isAjax = $request->request->has('ajax') && $request->request->get('ajax');
        try {
            $this->processor->delete($id);

            if ($isAjax) {
                return $this->presenter->successJsonResponse('messages.user_removed_success');
            }

            return $this->redirectToRoute('admin_acl_role-list');

        } catch (\Throwable $e) {
            if ($isAjax) {
                return $this->presenter->failureJsonResponse($e->getMessage());
            }

            return $this->presenter->handleException($e, $request);
        }
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws NotFoundException
     */
    public function edit(int $id, Request $request)
    {
        $this->control(ACLPermissions::ACL_ROLE_UPDATE);
        try {
            $entity = $this->processor->getEntity($id);
            $form = $this->createForm(AddRoleForm::class, $entity)->createView();

        } catch (\Throwable $e) {
            return $this->presenter->handleException($e, $request);
        }

        return $this->render('@VibbeACL/role/edit.html.twig', [
            'form' => $form,
            'role' => $entity
        ]);
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        $this->control(ACLPermissions::ACL_ROLE_UPDATE);
        try {
            /** @var  $form */
            $form = $this->createForm(AddRoleForm::class);
            $this->processor->store($request);

            return $this->presenter->handleRedirectWithMessage(
                'admin_acl_role-list',
                'messages.save_changes_success'
            );

        } catch (ValidationException $validationException) {
            $this->assignValidationErrorsToFormFields(
                $this->presenter->translateValidationErrors($validationException),
                $form);

            return $this->render('admin/users/edit.html.twig', ['form' => $form->createView()]);
        } catch (\Throwable $e) {
            return $this->presenter->handleException($e, $request);
        }
    }

    public function storePermissions(int $id, Request $request)
    {
        $this->control(ACLPermissions::ACL_ROLE_PERMISSIONS);
        try {
            $this->processor->saveRolePermissions($id, $request);

            return $this->presenter->handleRedirectWithMessage(
                'admin_acl_role-list',
                'messages.save_changes_success'
            );
        } catch (\Throwable $e) {
            $this->presenter->handleException($e, $request);
        }
    }

    public function permissions(int $id, Request $request)
    {
        $this->control(ACLPermissions::ACL_ROLE_PERMISSIONS);
        try {
            $entity = $this->processor->getEntity($id);
            /** @var  $form */
            $form = $this->createForm(PermissionsForm::class, null, [
                'role' => $entity
            ]);

        } catch (\Throwable $e) {
            return $this->presenter->handleException($e, $request);
        }

        return $this->render('@VibbeACL/role/edit-permissions.html.twig', [
            'form' => $form->createView(),
            'role' => $entity
        ]);
    }

}
