<?php

namespace AppBundle\Model\Request;

use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RecoveryPasswordRequest.
 *
 * @Annotation\ExclusionPolicy("all")
 */
class RecoveryPasswordRequest
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Annotation\Type("string")
     * @Annotation\SerializedName("old_password")
     * @Annotation\Expose
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    protected $oldPassword;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Annotation\Type("string")
     * @Annotation\SerializedName("new_password")
     * @Annotation\Expose
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    protected $newPassword;

    /**
     * @return string
     */
    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    /**
     * @param string $oldPassword
     */
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * @return string
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * @param string $newPassword
     */
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;
    }
}
