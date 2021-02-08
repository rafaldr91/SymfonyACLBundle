<?php


namespace Vibbe\ACL\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Vibbe\ACL\Service\AccessService;

class AbstractACLController extends AbstractController
{
    /** @var AccessService */
    private $accessService;

    /**
     * @required
     */
    public function setAccessService(AccessService $accessService)
    {
        $this->accessService = $accessService;
    }

    public function control(string $permissionSlug)
    {
        if(!$this->accessService->can($permissionSlug)) {
            throw new AccessDeniedException('access_denied');
        }

        return true;
    }

}