<?php

namespace Devprom\ServiceDeskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="pm_Participant")
 */ 
class ProjectParticipant {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="pm_ParticipantId")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="Caption")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="integer", name="Project")
     * @var integer
     */
    private $project;

    /**
     * @ORM\OneToOne(targetEntity="InternalUser")
     * @ORM\JoinColumn(name="SystemUser", referencedColumnName="cms_UserId")
     * @var InternalUser
     */
    private $user;


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
        $this->getUser()->setName($name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getUser()->getName();
    }

    function __toString()
    {
        return $this->getName();
    }

    /**
     * @param int $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return int
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param \Devprom\ServiceDeskBundle\Entity\InternalUser $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return \Devprom\ServiceDeskBundle\Entity\InternalUser
     */
    public function getUser()
    {
        return $this->user;
    }






}
