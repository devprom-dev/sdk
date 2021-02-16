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
     * @ORM\Column(type="string", name="Description")
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="string", name="CodeName")
     * @var string
     */
    private $codeName;

    /**
     * @ORM\Column(type="integer", name="Importance")
     * @var string
     */
    private $importance;

    /**
     * @ORM\Column(type="string", name="VPD")
     * @var string
     */
    private $vpd;

    /**
     * @ORM\Column(type="string", name="KnowledgeBaseServiceDesk")
     * @var string
     */
    private $knowledgeBaseServiceDesk;

    /**
     * @ORM\Column(type="string", name="KnowledgeBaseAuthorizedAccess")
     * @var string
     */
    private $knowledgeBaseAuthorizedAccess;

    /**
     * @ORM\Column(type="string", name="KnowledgeBaseUseProducts")
     * @var string
     */
    private $knowledgeBaseUseProducts;

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
     * @param string $value
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
     * @param int $value
     */
    public function setImportance($value)
    {
        $this->importance = $value;
    }

    /**
     * @return int
     */
    public function getImportance()
    {
        return $this->importance;
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
     * @param string $vpd
     */
    public function setKnowledgeBaseServiceDesk($value)
    {
        $this->knowledgeBaseServiceDesk = $value;
    }

    /**
     * @return string
     */
    public function getKnowledgeBaseServiceDesk()
    {
        return $this->knowledgeBaseServiceDesk;
    }

    /**
     * @param string $vpd
     */
    public function setKnowledgeBaseAuthorizedAccess($value)
    {
        $this->knowledgeBaseAuthorizedAccess = $value;
    }

    /**
     * @return string
     */
    public function getKnowledgeBaseAuthorizedAccess()
    {
        return $this->knowledgeBaseAuthorizedAccess;
    }

    /**
     * @param string $vpd
     */
    public function setKnowledgeBaseUseProducts($value)
    {
        $this->knowledgeBaseUseProducts = $value;
    }

    /**
     * @return string
     */
    public function getKnowledgeBaseUseProducts()
    {
        return $this->knowledgeBaseUseProducts;
    }
}