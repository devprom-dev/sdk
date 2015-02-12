<?php

namespace Devprom\ServiceDeskBundle\Entity;
use Doctrine\ORM\Mapping as ORM;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="co_CompanyProject")
 */
class CompanyProject extends BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="co_CompanyProjectId")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="Project", referencedColumnName="pm_ProjectId")
     * @var Company
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="projects")
     * @ORM\JoinColumn(name="Company", referencedColumnName="co_CompanyId")
     * @var Company
     */
    private $company;
    
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
     * @param Project $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param Company $parm
     */
    public function setCompany($parm)
    {
        $this->company = $parm;
    }

    /**
     * @return Project
     */
    public function getCompany()
    {
        return $this->company;
    }
}