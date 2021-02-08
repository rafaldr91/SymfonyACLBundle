<?php
/**
 * Created by Vibbe.
 * User: Rafał Drożdżal (rafal@vibbe.pl)
 * Date: 24.11.2020
 */
namespace Vibbe\ACL\Decorator;

use Symfony\Contracts\Translation\TranslatorInterface;
use Vibbe\ACL\Entity\ACLRole;
use Vibbe\ACL\Entity\UserRole;

class UserRolesDecorator
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    private const PATTERN = '<span class="label label-%s">%s</span>';
    private const RAW_PATTERN = '%s';
    /** @var bool */
    private $rawText;

    /**
     * UserRoleDecorator constructor.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->rawText = false;
    }

    /**
     * @param bool $asRaw
     */
    public function getAsRowText(bool $asRaw = true)
    {
        $this->rawText = $asRaw;
    }

    public function decorate(array $roles = [])
    {
        $return = '';
        foreach ($roles as $role) {
            $roleFormatted = $role;
            switch ($role) {
                case ACLRole::ROLE_SUPER_ADMIN:
                    $return .= $this->decorateSuperAdmin($roleFormatted);
                    break;
                case ACLRole::ROLE_ADMIN:
                    $return .= $this->decorateAdmin($roleFormatted);
                    break;
                case ACLRole::ROLE_USER:
                    $return .= $this->decorateUser($roleFormatted);
                    break;
                case ACLRole::ROLE_SUBSCRIBER:
                    $return .= $this->decorateSubscriber($roleFormatted);
                    break;
                default:
                    $return .= $this->decorateUnknown();
                    break;
            }
        }

        return $return;
    }

    /**
     * @param string $role
     * @return string
     */
    public function decorateSingle(ACLRole $role)
    {
        switch ($role->getSlug()) {
            case ACLRole::ROLE_SUPER_ADMIN:
                $return = $this->decorateSuperAdmin($role->getSlug());
                break;
            case ACLRole::ROLE_ADMIN:
                $return = $this->decorateAdmin($role->getSlug());
                break;
            case ACLRole::ROLE_USER:
                $return = $this->decorateUser($role->getSlug());
                break;
            case ACLRole::ROLE_SUBSCRIBER:
                $return = $this->decorateSubscriber($role->getSlug());
                break;
            default:
                $return = $role;
                break;
        }

        return $return;
    }
    /**
     * @return string
     */
    private function decorateSuperAdmin(string $role)
    {
        $translated = $this->translateRole($role);
        return $this->rawText ? sprintf(self::RAW_PATTERN, $translated) : sprintf(self::PATTERN, 'danger', $translated);
    }
    /**
     * @return string
     */
    private function decorateAdmin(string $role)
    {
        $translated = $this->translateRole($role);

        return $this->rawText ? sprintf(self::RAW_PATTERN, $translated) : sprintf(self::PATTERN, 'success', $translated);
    }
    /**
     * @return string
     */
    private function decorateUser(string $role)
    {
        $translated = $this->translateRole($role);

        return $this->rawText ? sprintf(self::RAW_PATTERN, $translated) :  sprintf(self::PATTERN, 'primary', $translated);
    }
    /**
     * @return string
     */
    private function decorateSubscriber(string $role)
    {
        $translated = $this->translateRole($role);

        return $this->rawText ? sprintf(self::RAW_PATTERN, $translated) : sprintf(self::PATTERN, 'info', $translated);
    }
    /**
     * @return string
     */
    private function decorateUnknown()
    {
        $translated = $this->translateRole('unknown');

        return $this->rawText ? sprintf(self::RAW_PATTERN, $translated) : sprintf(self::PATTERN, 'default', $translated);
    }

    /**
     * @param string $role
     * @return string
     */
    private function translateRole(string $role)
    {
        return $this->translator->trans('user.roles.' . $role);
    }
}
