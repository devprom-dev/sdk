<?php

namespace Devprom\ServiceDeskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="v_pm_State")
 */
class IssueState extends BaseEntity {

    /**
     * @ORM\Column(type="integer", name="pm_StateId")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="Caption")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", name="Description")
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="string", name="ReferenceName")
     * @ORM\Id
     * @var string
     */
    private $referenceName;

    /**
     * @ORM\Column(type="string", name="ObjectClass")
     * @var string
     */
    private $objectClass;

    /**
     * @ORM\Column(type="string", name="IsTerminal")
     * @var string
     */
    private $terminal;

    /**
     * @ORM\Column(type="integer", name="IsTerminalNum")
     * @var string
     */
    private $terminalNum;

    /**
     * @ORM\Column(type="integer", name="OrderNum")
     * @var integer
     */
    private $orderNum;


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
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setDescription($value)
    {
        $this->description = $value;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $objectClass
     */
    public function setObjectClass($objectClass)
    {
        $this->objectClass = $objectClass;
    }

    /**
     * @return string
     */
    public function getObjectClass()
    {
        return $this->objectClass;
    }

    /**
     * @param string $referenceName
     */
    public function setReferenceName($referenceName)
    {
        $this->referenceName = $referenceName;
    }

    /**
     * @return string
     */
    public function getReferenceName()
    {
        return $this->referenceName;
    }

    /**
     * @param int $orderNum
     */
    public function setOrderNum($orderNum)
    {
        $this->orderNum = $orderNum;
    }

    /**
     * @return int
     */
    public function getOrderNum()
    {
        return $this->orderNum;
    }


    /**
     * @param string $name
     */
    public function setTerminal($value)
    {
        $this->terminal = $value;
    }

    /**
     * @return string
     */
    public function getTerminal()
    {
        return $this->terminal;
    }

    /**
     * @param string $name
     */
    public function setTerminalNum($value)
    {
        $this->terminalNum = $value;
    }

    /**
     * @return string
     */
    public function getTerminalNum()
    {
        return $this->terminalNum;
    }

    function __toString()
    {
        return $this->getName();
    }
}