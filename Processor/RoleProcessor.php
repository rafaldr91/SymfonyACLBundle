<?php
/**
 * Created by Vibbe.
 * User: Rafał Drożdżal (rafal@vibbe.pl)
 * Date: 23.11.2020
 */

namespace Vibbe\ACL\Processor;

use App\Common\Util;
use App\Entity\User;
use App\Event\User\PostChangePasswordEvent;
use App\Event\User\PostChangePermissionsEvent;
use App\Event\User\PostProfileUpdateEvent;
use App\Event\User\PostUserCreateEvent;
use App\Event\User\PostUserSoftDeleteEvent;
use App\Event\User\PostUserUpdateEvent;
use App\Event\User\PreChangePasswordEvent;
use App\Event\User\PreChangePermissionsEvent;
use App\Event\User\PreProfileUpdateEvent;
use App\Event\User\PreUserCreateEvent;
use App\Event\User\PreUserSoftDeleteEvent;
use App\Event\User\PreUserUpdateEvent;
use App\Exceptions\EngineException;
use App\Exceptions\NotFoundException;
use App\Hydrator\EntityHydrator;
use App\Processor\BaseProcessor;
use App\Repository\UserRepository;
use App\Service\User\Errors\UserAccountDataNotValidException;
use App\Service\User\Validator\PasswordValidator;
use App\Service\User\Validator\PermissionsValidator;
use App\Service\User\Validator\ProfileValidator;
use App\Service\User\Validator\UserValidator;
use App\Traits\UserTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Vibbe\ACL\Entity\ACLRole;
use Vibbe\ACL\Entity\Permission;
use Vibbe\ACL\Repository\ACLRoleRepository;
use Vibbe\ACL\Service\AccessService;

final class RoleProcessor extends BaseProcessor
{
    /** @var ACLRoleRepository  */
    protected $repository;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var EntityHydrator
     */
    protected $entityHydrator;
    /**
     * @var CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    /** @var AccessService */
    protected $accessService;

    /**
     * RoleProcessor constructor.
     *
     * @param $repository
     */
    public function __construct(ACLRoleRepository $repository,
                                EntityManagerInterface $entityManager,
                                EntityHydrator $entityHydrator,
                                CsrfTokenManagerInterface $csrfTokenManager,
                                AccessService $accessService,
                                EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($entityManager, $entityHydrator, $csrfTokenManager, $eventDispatcher);
        $this->repository = $repository;
        $this->accessService = $accessService;
    }


    /**
     * @param int $id
     * @return ACLRole
     * @throws NotFoundException
     */
    public function showRole(int $id): User
    {
        $user = $this->userRepository->find($id);

        if(!$user instanceof User) {
            throw new NotFoundException('messages.user_not_found');
        }

        return $user;
    }

    /**
     * @param ACLRole|null $user
     * @param Request      $request
     *
     * @return bool
     * @throws EngineException
     */
    public function store(Request $request)
    {
        if($request->request->has('add_role_form')) {
            $name = $request->request->get('add_role_form')['name'];
            $id = $request->request->get('add_role_form')['id'];
            if($id > 0) {
                $role = $this->getEntity($id);   
            } else {
                $role = new ACLRole();
            }
            $role->setName($name);
            $role->setSlug($this->prepareSlugFromName($name));

            if($this->repository->checkWhetherSuchRoleExists($role->getSlug(), $id)) {
                throw new EngineException('messages.role_exists');
            }

            $this->entityManager->persist($role);
            $this->entityManager->flush();

            return;
        }

        throw new EngineException('messages.invalid_form_data');
    }

    /**
     * @param        $value
     * @param string $identificator
     *
     * @return int
     * @throws NotFoundException
     */
    public function delete($value, string $identificator = 'id')
    {
        /** @var ACLRole $role */
        $role = $this->getEntity($value);
        foreach($role->getPermissions() as $permission) {
            $this->entityManager->remove($permission);
        }

        return parent::delete($value, $identificator);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function prepareSlugFromName(string $name)
    {
        return strtoupper(str_replace('-','_', Util::slugify($name)));
    }

    public function saveRolePermissions(int $roleId, Request $request)
    {
        /** @var ACLRole $role */
        $role = $this->getEntity($roleId);

        $newPermissions = [];
        foreach($request->request->all()['permissions_form'] as $slug => $notRelevant) {
            $exploded = explode('::--::', $slug);
            if(count($exploded) == 2) {
                $normalized = str_replace('::--::', '-', $slug);
                $newPermissions[$normalized] = $normalized;
            }
        }
        $allowedPermissions = $newPermissions;
        $allPermissions = $this->accessService->getAllAvailableActionsNormalized();

        /** @var Permission $permission */
        foreach($role->getPermissions() as $permission) {
            if(in_array($permission->getPermission(), $allPermissions)) {
                unset($allPermissions[$permission->getPermission()]);
            }
            if(in_array($permission->getPermission(), $newPermissions) && !$permission->getIsAllowed()) {
                $permission->setIsAllowed(1);
                $this->entityManager->persist($permission);
            }
            if(!in_array($permission->getPermission(), $newPermissions) && $permission->getIsAllowed()) {
                $permission->setIsAllowed(0);
                $this->entityManager->persist($permission);
            }

            unset($newPermissions[$permission->getPermission()]);
        }
        foreach($newPermissions as $permission) {
            $permissionToAdd = new Permission($permission, $role, 1);
            $this->entityManager->persist($permissionToAdd);
            unset($allPermissions[$permission]);
        }
        foreach($allPermissions as $permission) {
            $permissionToAdd = new Permission($permission, $role, 0);
            $this->entityManager->persist($permissionToAdd);
        }


        $this->entityManager->flush();
    }

}
