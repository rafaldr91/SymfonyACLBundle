<?php

namespace Vibbe\ACL\Subscribers;

use App\Event\ConfigureMenuEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vibbe\ACL\Permissions\ACLPermissions;
use Vibbe\ACL\Service\AccessService;

class ConfigureMenuListener implements EventSubscriberInterface
{
    /** @var AccessService */
    private $accessService;

    /**
     * ConfigureMenuListener constructor.
     * @param AccessService $accessService
     */
    public function __construct(AccessService $accessService)
    {
        $this->accessService = $accessService;
    }

    public static function getSubscribedEvents()
    {
        return [];
    }

    public function onAppEventConfigureMenuEvent(ConfigureMenuEvent $event)
    {
        if ($this->accessService->can(ACLPermissions::ACL_ROLE_READ)) {
            $menu = $event->getMenu();

            $menu->addChild('ACL', [
                'route' => 'admin_acl_role-list',
                'attributes' => ['icon' => 'fa fa-user']
            ]);
        }
    }

}
