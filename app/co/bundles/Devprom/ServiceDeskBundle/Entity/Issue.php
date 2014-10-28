<?php

namespace Devprom\ServiceDeskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;
use Doctrine\Common\Collections\Criteria;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>

 * @ORM\Entity(repositoryClass="Devprom\ServiceDeskBundle\Repository\IssueRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="pm_ChangeRequest")
 */
class Issue extends BaseEntity {

    const NORMAL_PRIORITY = 3;

    /**
     * @ORM\Id @ORM\Column(type="integer", name="pm_ChangeRequestId")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="Caption")
     * @Assert\NotBlank
     * @var string
     */
    private $caption;

    /**
     * @ORM\Column(type="string", name="Description")
     * @Assert\NotBlank
     * @var string
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="IssueType")
     * @ORM\JoinColumn(name="Type", referencedColumnName="pm_IssueTypeId")
     * @Assert\NotBlank
     * @var IssueType
     */
    private $issueType;

    /**
     * @ORM\OneToOne(targetEntity="IssueState")
     * @ORM\JoinColumn(name="State", referencedColumnName="ReferenceName"),
     * @var IssueState
     */
    private $state;

    /**
     * @ORM\OneToOne(targetEntity="IssueStateComment")
     * @ORM\JoinColumn(name="StateObject", referencedColumnName="pm_StateObjectId")
     * @var IssueStateComment
     */
    private $stateComment;
    
    /**
     * @ORM\OneToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="Function", referencedColumnName="pm_FunctionId")
     * @var Product
     */
    private $product;

    /**
     * @ORM\OneToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="Project", referencedColumnName="pm_ProjectId")
     * @var Project
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity="IssueAttachment", mappedBy="issue", fetch="EAGER", cascade={"all"})
     * @var ArrayCollection
     */
    private $attachments;

    /** @var  IssueAttachment */
    private $newAttachment;

    /**
     * @var string
     */
    private $authorEmail;

    /**
     * @ORM\OneToMany(targetEntity="IssueComment", mappedBy="issue", fetch="EAGER", cascade={"all"})
     * @ORM\OrderBy({"createdAt" = "ASC"})
     * @var ArrayCollection
     */
    private $comments;

    /**
     * @ORM\OneToOne(targetEntity="ProjectParticipant")
     * @ORM\JoinColumn(name="Owner", referencedColumnName="pm_ParticipantId")
     * @var ProjectParticipant
     */
    private $assignedTo;

    /**
     * @ORM\OneToOne(targetEntity="Priority")
     * @ORM\JoinColumn(name="Priority", referencedColumnName="PriorityId")
     * @Assert\NotBlank
     * @var Priority
     */
    private $priority;


    function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->attachments = new ArrayCollection();
    }


    /**
     * @param IssueComment $comment
     */
    function addComment($comment) {
        $this->comments[] = $comment;
        $comment->setIssue($this);
    }

    /**
     * @param ArrayCollection $attachments
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments->matching(
        			Criteria::create()->where(Criteria::expr()->eq("ObjectClass", "request"))
			)->toArray();
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
     * @param ArrayCollection $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return array
     */
    public function getComments()
    {
        return $this->comments->toArray();
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
     * @param string $createdBy
     */
    public function setAuthorEmail($createdBy)
    {
        $this->authorEmail = $createdBy;
    }

    /**
     * @return string
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

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
     * @param \Devprom\ServiceDeskBundle\Entity\Product $function
     */
    public function setProduct($function)
    {
        $this->product = $function;
    }

    /**
     * @return \Devprom\ServiceDeskBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
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
     * @param \Devprom\ServiceDeskBundle\Entity\IssueType $issueType
     */
    public function setIssueType($issueType)
    {
        $this->issueType = $issueType;
    }

    /**
     * @return \Devprom\ServiceDeskBundle\Entity\IssueType
     */
    public function getIssueType()
    {
        return $this->issueType;
    }

    /**
     * @param IssueState $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return IssueState
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param IssueStateComment $stateComment
     */
    public function setStateComment($stateComment)
    {
        $this->stateComment = $stateComment;
    }

    /**
     * @return IssueStateComment
     */
    public function getStateComment()
    {
        return $this->stateComment;
    }
    
    /**
     * @param \Devprom\ServiceDeskBundle\Entity\ProjectParticipant $assignedTo
     */
    public function setAssignedTo($assignedTo)
    {
        $this->assignedTo = $assignedTo;
    }

    /**
     * @return \Devprom\ServiceDeskBundle\Entity\ProjectParticipant
     */
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }


    /**
     * @param Priority $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return Priority
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param \Devprom\ServiceDeskBundle\Entity\IssueAttachment $newAttachment
     */
    public function setNewAttachment($newAttachment)
    {
        $this->newAttachment = $newAttachment;
    }

    /**
     * @return \Devprom\ServiceDeskBundle\Entity\IssueAttachment
     */
    public function getNewAttachment()
    {
        return $this->newAttachment;
    }



}
