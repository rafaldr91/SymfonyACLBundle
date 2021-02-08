<?php

namespace Vibbe\ACL;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vibbe\ACL\DependencyInjection\VibbeACLBundleExtension;

class VibbeACLBundle extends Bundle
{

    public function getContainerExtension()
    {
        return new VibbeACLBundleExtension();
    }

}
