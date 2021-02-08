<?php

namespace Vibbe\ACL\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vibbe\ACL\Contracts\ACLUserInterface;

/**
 * @ORM\Entity()
 */
class UserRole
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="roles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Vibbe\ACL\Entity\ACLRole", inversedBy="permissions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $role;

    /**
     * UserRole constructor.
     *
     * @param ACLRole          $role
     * @param ACLUserInterface $user
     */
    public function __construct(ACLRole $role, ACLUserInterface $user)
    {
        $this->user = $user;
        $this->role = $role;
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
     * @return UserRole
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return ACLUserInterface
     */
    public function getUser(): ACLUserInterface
    {
        return $this->user;
    }

    /**
     * @param ACLUserInterface $user
     *
     * @return UserRole
     */
    public function setUser(ACLUserInterface $user)
    {
        $this->user = $user;

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
     * @return UserRole
     */
    public function setRole(ACLRole $role)
    {
        $this->role = $role;

        return $this;
    }

}

