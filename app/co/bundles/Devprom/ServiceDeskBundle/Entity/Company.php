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
     * @ORM\OneToMany(targetEntity="CompanyProject", mappedBy="company", fetch="EAGER", cascade={"all"})
     * @var ArrayCollection
     */
    private $projects;
    
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
}