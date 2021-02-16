<?php

namespace Devprom\ServiceDeskBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="pm_FeatureType")
 */
class ProductType {

    /**
     * @ORM\Id @ORM\Column(type="integer", name="pm_FeatureTypeId")
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
     * @ORM\Column(type="string", name="VPD")
     * @var string
     */
    private $vpd;

    /**
     * @ORM\Column(type="string", name="HasIssues")
     * @var string
     */
    private $hasIssues;

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

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

    /**
     * @param string $value
     */
    public function setHasIssues($value)
    {
        $this->hasIssues = $value;
    }

    /**
     * @return string
     */
    public function getHasIssues()
    {
        return $this->hasIssues;
    }

    function __toString()
    {
        return $this->getName();
    }
}
