<?php

namespace Vibbe\ACL;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Vibbe\ACL\Service\AccessService;

trait AccessServiceAwareTrait
{

    /** @var AccessService */
    private $accessService;

    /**
     * @required
     * @param AccessService $accessService
     */
    public function setAccessService(AccessService $accessService)
    {
        $this->accessService = $accessService;
    }

    /**
     * @param string $permissionSlug
     * @return bool
     */
    public function control(string $permissionSlug)
    {
        if(!$this->accessService->can($permissionSlug)) {
            throw new AccessDeniedException('access_denied');
        }

        return true;
    }

    public function can(string $permissionSlug)
    {
        return $this->accessService->can($permissionSlug);
    }

}