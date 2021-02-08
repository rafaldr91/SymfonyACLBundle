<?php

namespace Vibbe\ACL\Entity;

use App\Contracts\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Vibbe\ACL\Repository\ACLRoleRepository;
use Vibbe\ACL\Contracts\ACLUserInterface;

/**
 * @ORM\Entity(repositoryClass=ACLRoleRepository::class)
 */
class ACLRole implements EntityInterface
{
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    const ROLE_ADMIN       = 'ROLE_ADMIN';
    const ROLE_USER        = 'ROLE_USER';
    const ROLE_SUBSCRIBER  = 'ROLE_SUBSCRIBER';

    const ROLES_DEFAULT = [
        self::ROLE_SUPER_ADMIN => 'Super Admin',
        self::ROLE_ADMIN       => 'Admin',
        self::ROLE_USER        => 'User',
        self::ROLE_SUBSCRIBER  => 'Subscriber'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Vibbe\ACL\Entity\Permission", mappedBy="role")
     * @ORM\JoinColumn(nullable=false)
     */
    private $permissions;

    /**
     * ACLRole constructor.
     *
     * @param string $slug
     * @param string $name
     */
    public function __construct(string $slug = '', string $name = '')
    {
        $this->slug = $slug;
        $this->name = $name;
    }

    /**
     * @param ACLUserInterface|null $user
     */
    public function setUser(?ACLUserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return ACLRole
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param mixed $slug
     *
     * @return ACLRole
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Permission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

}

