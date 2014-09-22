<?php

namespace Devprom\ServiceDeskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="ObjectChangeLog")
 */
class ObjectChangeLog extends BaseEntity {

    /**
     * @ORM\Id @ORM\Column(type="integer", name="ObjectChangeLogId")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="Caption")
     * @var string
     */
    private $caption;

    /**
     * @ORM\Column(type="string", name="Content")
     * @var string
     */
    private $content;

    /**
     * @ORM\Column(type="string", name="ChangeKind")
     * @var string
     */
    private $changeKind;

    /**
     * @ORM\Column(type="string", name="EntityRefName")
     * @var string
     */
    private $entityRefName;

    /**
     * @ORM\Column(type="string", name="EntityName")
     * @var string
     */
    private $entityName;

    /**
     * @ORM\Column(type="integer", name="ObjectId")
     * @var integer
     */
    private $objectId;

    /**
     * @ORM\OneToOne(targetEntity="ProjectParticipant")
     * @ORM\JoinColumn(name="Author", referencedColumnName="pm_ParticipantId")
     * @var ProjectParticipant
     */
    private $author;

    /**
     * @ORM\Column(type="integer", name="VisibilityLevel")
     * @var integer
     */
    private $visibilityLevel;

    /**
     * @ORM\Column(type="string", name="ClassName")
     * @var string
     */
    private $className;

    /**
     * @param \Devprom\ServiceDeskBundle\Entity\ProjectParticipant $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return \Devprom\ServiceDeskBundle\Entity\ProjectParticipant
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $caption
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @param string $changeKind
     */
    public function setChangeKind($changeKind)
    {
        $this->changeKind = $changeKind;
    }

    /**
     * @return string
     */
    public function getChangeKind()
    {
        return $this->changeKind;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $entityName
     */
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @param string $entityRefName
     */
    public function setEntityRefName($entityRefName)
    {
        $this->entityRefName = $entityRefName;
    }

    /**
     * @return string
     */
    public function getEntityRefName()
    {
        return $this->entityRefName;
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
     * @param int $objectId
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;
    }

    /**
     * @return int
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @param int $visibilityLevel
     */
    public function setVisibilityLevel($visibilityLevel)
    {
        $this->visibilityLevel = $visibilityLevel;
    }

    /**
     * @return int
     */
    public function getVisibilityLevel()
    {
        return $this->visibilityLevel;
    }

    /**
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }


}