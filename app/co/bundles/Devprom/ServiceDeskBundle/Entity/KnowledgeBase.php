<?php

namespace Devprom\ServiceDeskBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="WikiPage")
 */
class KnowledgeBase extends BaseEntity
{
    /**
     * @ORM\Id @ORM\Column(type="integer", name="WikiPageId")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="Caption")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", name="Content")
     * @var string
     */
    private $content;

    /**
     * @ORM\Column(type="string", name="ReferenceName")
     * @var string
     */
    private $referenceName;

    /**
     * @ORM\Column(type="string", name="SortIndex")
     * @var string
     */
    private $sortIndex;

    /**
     * @ORM\OneToOne(targetEntity="KnowledgeBase")
     * @ORM\JoinColumn(name="ParentPage", referencedColumnName="WikiPageId")
     * @var parent
     */
    private $parent;

    /**
     * @param string $codeName
     */
    public function setReferenceName($codeName)
    {
        $this->referenceName = $codeName;
    }

    /**
     * @return string
     */
    public function getReferenceName()
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
    public function setContent($value)
    {
        $this->content = $value;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param \Devprom\ServiceDeskBundle\Entity\KnowledgeBase $value
     */
    public function setParent($value)
    {
        $this->parent = $value;
    }

    /**
     * @return \Devprom\ServiceDeskBundle\Entity\KnowledgeBase
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param string $value
     */
    public function setSortIndex($value)
    {
        $this->sortIndex = $value;
    }

    /**
     * @return string
     */
    public function getSortIndex()
    {
        return $this->sortIndex;
    }

    function __toString()
    {
        return $this->getName();
    }
}