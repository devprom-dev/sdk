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
     * @ORM\Column(type="datetime", name="DeliveryDate")
     * @var DateTime
     */
    private $deliveryDate;

    /**
     * @ORM\OneToOne(targetEntity="IssueType")
     * @ORM\JoinColumn(name="Type", referencedColumnName="pm_IssueTypeId")
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
     * @ORM\OneToOne(targetEntity="InternalUser")
     * @ORM\JoinColumn(name="Author", referencedColumnName="cms_UserId")
     * @var User
     */
    private $author;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="Customer", referencedColumnName="cms_ExternalUserId")
     * @var User
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity="IssueComment", mappedBy="issue", fetch="EAGER", cascade={"all"})
     * @ORM\OrderBy({"createdAt" = "ASC"})
     * @var ArrayCollection
     */
    private $comments;

    /**
     * @ORM\OneToOne(targetEntity="InternalUser")
     * @ORM\JoinColumn(name="Owner", referencedColumnName="cms_UserId")
     * @var User
     */
    private $assignedTo;

    /**
     * @ORM\OneToOne(targetEntity="Priority")
     * @ORM\JoinColumn(name="Priority", referencedColumnName="PriorityId")
     * @var Priority
     */
    private $priority;

    /**
     * @ORM\OneToOne(targetEntity="Severity")
     * @ORM\JoinColumn(name="Severity", referencedColumnName="pm_SeverityId")
     * @Assert\NotBlank
     * @var Severity
     */
    private $severity;
    
    /**
     * @ORM\Column(type="string", name="ClosedInVersion")
     * @var string
     */
    private $resolvedVersion;

    /**
     * @ORM\Column(type="string", name="SupportChannelEmail")
     * @var string
     */
    private $channelEmail;

    /**
     * @ORM\OneToMany(targetEntity="IssueChangeNotification", mappedBy="issue", fetch="EAGER", cascade={"all"})
     * @var ArrayCollection
     */
    private $notifications;


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
        			Criteria::create()->where(Criteria::expr()->in("ObjectClass", array("request","pm_ChangeRequest")))
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
     * @param User $createdBy
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return User
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param User $user
     */
    public function setAuthor($user)
    {
        $this->author = $user;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
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
     * @return DateTime
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * @param \DateTime $deliveryDate
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
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
     * @param \Devprom\ServiceDeskBundle\Entity\InternalUser $assignedTo
     */
    public function setAssignedTo($assignedTo)
    {
        $this->assignedTo = $assignedTo;
    }

    /**
     * @return \Devprom\ServiceDeskBundle\Entity\InternalUser
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
     * @param Severity $severity
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;
    }

    /**
     * @return Severity
     */
    public function getSeverity()
    {
        return $this->severity;
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

    /**
     * @param string $text
     */
    public function setResolvedVersion($text)
    {
        $this->resolvedVersion = $text;
    }

    /**
     * @return string
     */
    public function getResolvedVersion()
    {
        return $this->resolvedVersion;
    }

    /**
     * @param string $text
     */
    public function setChannelEmail($text)
    {
        $this->channelEmail = $text;
    }

    /**
     * @return string
     */
    public function getChannelEmail()
    {
        return $this->channelEmail;
    }

    /**
     * @param ArrayCollection $attachments
     */
    public function setNotifications($value) {
        $this->notifications = $value;
    }

    /**
     * @return array
     */
    public function getNotifications( $customer = null )
    {
        return $this->notifications->matching(
            Criteria::create()->where(
                Criteria::expr()->andX(
                    Criteria::expr()->eq('className', 'Request'),
                    is_object($customer)
                        ? Criteria::expr()->eq('customer', $customer)
                        : Criteria::expr()->neq('customer', null)
                )
            )
        )->toArray();
    }
}
