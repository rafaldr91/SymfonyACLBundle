<?php

namespace Vibbe\ACL\Contracts;

interface ACLUserInterface
{

    public function getRoles();
    public function getId();

}
