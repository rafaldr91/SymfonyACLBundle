<?php


namespace Vibbe\ACL\Service;


use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Vibbe\ACL\Entity\UserRole;

class AccessService
{

    /** @var ParameterBagInterface  */
    private $params;
    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(ParameterBagInterface $params, TokenStorageInterface $tokenStorage)
    {
        $this->params = $params;
        $this->tokenStorage = $tokenStorage;
    }

    public function getAllAvailableActions()
    {
        return $this->params->get('vibbe_acl_bundle.vibbe_acl_actions');
    }

    public function getAllAvailableActionsNormalized()
    {
        $normalized = [];
        foreach($this->params->get('vibbe_acl_bundle.vibbe_acl_actions') as $group => $actions) {
            foreach($actions as $action) {
                $normalized[$group.'-'.$action] = $group.'-'.$action;
            }
        }

        return $normalized;
    }

    public function setAccessProcessor()
    {

    }

    public function can(string $permissionString)
    {
        /** @var UserRole $role */
        foreach($this->getUser()->getRolesEntity() as $role) {
            foreach($role->getRole()->getPermissions() as $permission) {
                if($permission->getPermission() == $permissionString && $permission->getIsAllowed() == 1) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return UserInterface
     */
    private function getUser():UserInterface
    {
        return $this->tokenStorage->getToken()->getUser();
    }

}
