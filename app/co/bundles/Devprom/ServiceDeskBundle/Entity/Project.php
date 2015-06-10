<?php

namespace Devprom\ServiceDeskBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="pm_Project")
 */
class Project {

    /**
     * @ORM\Id @ORM\Column(type="integer", name="pm_ProjectId")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="Caption")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", name="CodeName")
     * @var string
     */
    private $codeName;

    /**
     * @ORM\Column(type="string", name="VPD")
     * @var string
     */
    private $vpd;

    /**
     * @param string $codeName
     */
    public function setCodeName($codeName)
    {
        $this->codeName = $codeName;
    }

    /**
     * @return string
     */
    public function getCodeName()
    {
        return $this->codeName;
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

    function __toString()
    {
        return $this->getName();
    }
}