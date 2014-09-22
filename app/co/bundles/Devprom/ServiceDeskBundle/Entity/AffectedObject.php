<?php

namespace Devprom\ServiceDeskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="co_AffectedObjects")
 */
class AffectedObject extends BaseEntity {

    /**
     * @ORM\Id @ORM\Column(type="integer", name="co_AffectedObjectsId")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="ObjectClass")
     * @var string
     */
    private $objectClass;

    /**
     * @ORM\Column(type="integer", name="ObjectId")
     * @var integer
     */
    private $objectId;

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
    public function setObjectClass($className)
    {
        $this->objectClass = $className;
    }

    /**
     * @return string
     */
    public function getObjectClass()
    {
        return $this->objectClass;
    }
}