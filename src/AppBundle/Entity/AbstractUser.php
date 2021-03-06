<?php

namespace AppBundle\Entity;

use JMS\Serializer\Annotation;

/**
 * AbstractUser.
 */
abstract class AbstractUser
{
    use TraitTimestampable;

    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *     $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("array<AppBundle\Entity\Role>")
     * @Annotation\SerializedName("roles")
     * @Annotation\Groups({"profile"})
     */
    public function getSerializedRole()
    {
        $result = [];
        foreach ($this->getUserRoles() as $role) {
            $result[] = $role;
        }

        return $result;
    }
}
