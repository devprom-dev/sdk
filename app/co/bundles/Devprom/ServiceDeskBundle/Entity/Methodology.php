<?php

namespace Devprom\ServiceDeskBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="pm_Methodology")
 */
class Methodology {

    /**
     * @ORM\Id @ORM\Column(type="integer", name="pm_MethodologyId")
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="Project", referencedColumnName="pm_ProjectId")
     * @var Project
     */
    private $project;

    /**
     * @ORM\Column(type="string", name="IsSupportUsed")
     * @var string
     */
    private $support;

    /**
     * @ORM\Column(type="string", name="IsRequirements")
     * @var string
     */
    private $requirements;

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
     * @param string $check
     */
    public function setSupport($check)
    {
        $this->support = $check;
    }

    /**
     * @return string
     */
    public function getSupport()
    {
        return $this->support;
    }

    /**
     * @param string $check
     */
    public function setRequirements($check)
    {
        $this->requirements = $check;
    }

    /**
     * @return string
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    function __toString()
    {
        return 'Methodology:'.$this->getId();
    }
}