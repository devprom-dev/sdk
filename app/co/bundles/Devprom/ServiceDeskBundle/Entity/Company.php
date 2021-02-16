<?php

namespace Devprom\ServiceDeskBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="co_Company")
 */
class Company extends BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="co_CompanyId")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="CanSeeCompanyIssues")
     * @var integer
     */
    private $seecompanyissues;

    /**
     * @ORM\Column(type="string", name="Domains")
     * @var string
     */
    private $domains;

    /**
     * @ORM\OneToMany(targetEntity="CompanyProject", mappedBy="company", fetch="EAGER", cascade={"all"})
     * @var ArrayCollection
     */
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity="CompanyProduct", mappedBy="company", fetch="EAGER", cascade={"all"})
     * @var ArrayCollection
     */
    private $products;

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
     * @param string $flag
     */
    public function setSeeCompanyIssues($flag)
    {
        $this->seecompanyissues = $flag;
    }

    /**
     * @return string
     */
    public function getSeeCompanyIssues()
    {
        return $this->seecompanyissues;
    }

    /**
     * @param ArrayCollection $projects
     */
    public function setProjects($projects)
    {
        $this->projects = $projects;
    }

    /**
     * @return array
     */
    public function getProjects()
    {
        return $this->projects->toArray();
    }

    /**
     * @param ArrayCollection $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->products->toArray();
    }

    /**
     * @param string $value
     */
    public function setDomains($value)
    {
        $this->domains = $value;
    }

    /**
     * @return string
     */
    public function getDomains()
    {
        return $this->domains;
    }
}