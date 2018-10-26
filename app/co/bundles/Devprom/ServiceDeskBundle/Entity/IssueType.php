<?php

namespace Devprom\ServiceDeskBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="pm_IssueType")
 */
class IssueType {

    /**
     * @ORM\Id @ORM\Column(type="integer", name="pm_IssueTypeId")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="Caption")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", name="ReferenceName")
     * @var string
     */
    private $referenceName;

    /**
     * @ORM\Column(type="string", name="VPD")
     * @var string
     */
    private $vpd;

    /**
     * @ORM\Column(type="string", name="Option1")
     * @var string
     */
    private $visible;

    /**
     * @ORM\Column(type="integer", name="OrderNum")
     * @var string
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
     * @param string $vpd
     */
    public function setVpd($vpd)
    {
        $this->vpd = $vpd;
    }

    /**
     * @return string
     */
    public function getVpd()
    {
        return $this->vpd;
    }

    function __toString()
    {
        return $this->getName();
    }

    /**
     * @param string $value
     */
    public function setVisible($value)
    {
        $this->visible = $value;
    }

    /**
     * @return string
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @param string $value
     */
    public function setOrderNum($value)
    {
        $this->orderNum = $value;
    }

    /**
     * @return string
     */
    public function getOrderNum()
    {
        return $this->orderNum;
    }
}
