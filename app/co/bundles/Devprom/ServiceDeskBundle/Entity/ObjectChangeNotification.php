<?php

namespace Devprom\ServiceDeskBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ObjectChangeNotification")
 */
class ObjectChangeNotification extends BaseEntity {

    /**
     * @ORM\Id @ORM\Column(type="integer", name="ObjectChangeNotificationId")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="ObjectId")
     * @var integer
     */
    private $objectId;

    /**
     * @ORM\Column(type="string", name="ObjectClass")
     * @var string
     */
    private $className;

    /**
     * @ORM\OneToOne(targetEntity="InternalUser")
     * @ORM\JoinColumn(name="SystemUser", referencedColumnName="cms_UserId")
     * @var InternalUser
     */
    private $systemuser;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="Customer", referencedColumnName="cms_ExternalUserId")
     * @var User
     */
    private $customer;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $objectId
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;
    }

    /**
     * @return int
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param User $createdBy
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return User
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param InternalUser $user
     */
    public function setSystemUser($user)
    {
        $this->systemuser = $user;
    }

    /**
     * @return InternalUser
     */
    public function getSystemUser()
    {
        return $this->systemuser;
    }
}