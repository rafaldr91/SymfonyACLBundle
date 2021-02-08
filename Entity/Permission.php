<?php

namespace Vibbe\ACL\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Permission
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $permission;

    /**
     * @ORM\Column(type="integer", length=1)
     */
    private $is_allowed;

    /**
     * @ORM\ManyToOne(targetEntity="Vibbe\ACL\Entity\ACLRole", inversedBy="permissions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $role;

    /**
     * Permission constructor.
     *
     * @param string  $permission
     * @param ACLRole $role
     */
    public function __construct(string $permission, ACLRole $role, int $is_allowed = 0)
    {
        $this->permission = $permission;
        $this->role = $role;
        $this->is_allowed = $is_allowed;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return Permission
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getPermission(): string
    {
        return $this->permission;
    }

    /**
     * @param string $permission
     *
     * @return Permission
     */
    public function setPermission(string $permission)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * @return ACLRole
     */
    public function getRole(): ACLRole
    {
        return $this->role;
    }

    /**
     * @param ACLRole $role
     *
     * @return Permission
     */
    public function setRole(ACLRole $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return int
     */
    public function getIsAllowed(): int
    {
        return $this->is_allowed;
    }

    /**
     * @param int $is_allowed
     *
     * @return Permission
     */
    public function setIsAllowed(int $is_allowed)
    {
        $this->is_allowed = $is_allowed;

        return $this;
    }

}

