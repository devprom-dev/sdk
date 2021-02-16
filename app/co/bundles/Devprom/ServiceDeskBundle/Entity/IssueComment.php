<?php

namespace Devprom\ServiceDeskBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="Comment")
 */
class IssueComment extends BaseEntity
{

    /**
     * @ORM\Id @ORM\Column(type="integer", name="CommentId")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="InternalUser")
     * @ORM\JoinColumn(name="AuthorId", referencedColumnName="cms_UserId")
     * @var User
     */
    private $internalAuthor;

    /**
     * @ORM\Column(type="string", name="Caption")
     * @Assert\NotBlank
     * @var string
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="comments")
     * @ORM\JoinColumn(name="ObjectId", referencedColumnName="pm_ChangeRequestId")
     * @var Issue
     */
    private $issue;

    /**
     * @ORM\Column(type="string", name="ObjectClass")
     * @var string
     */
    private $objectClass;

    /**
     * @ORM\Column(type="string", name="ExternalAuthor")
     * @var string
     */
    private $externalAuthor;

    /**
     * @ORM\Column(type="string", name="ExternalEmail")
     * @var string
     */
    private $externalEmail;

    /**
     * @ORM\OneToMany(targetEntity="IssueCommentAttachment", mappedBy="comment", fetch="EAGER", cascade={"all"})
     * @var ArrayCollection
     */
    private $attachments;

    /**
     * @ORM\Column(type="string", name="IsPrivate")
     * @var string
     */
    private $isprivate = 'N';

    /**
     * @ORM\Column(type="string", name="EmailMessageId")
     * @var string
     */
    private $emailMessageId;

    /**
     * @ORM\OneToOne(targetEntity="IssueComment")
     * @ORM\JoinColumn(name="PrevComment", referencedColumnName="CommentId")
     * @var IssueComment
     */
    private $parentComment;

    function __construct()
    {
        $this->attachments = new ArrayCollection();
    }

    /**
     * @param \Devprom\ServiceDeskBundle\Entity\User $author
     */
    public function setInternalAuthor($author)
    {
        $this->internalAuthor = $author;
    }

    /**
     * @return \Devprom\ServiceDeskBundle\Entity\User
     */
    public function getInternalAuthor()
    {
        return $this->internalAuthor;
    }

    /**
     * @param string $caption
     */
    public function setText($caption)
    {
        $this->text = $caption;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
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
     * @param \Devprom\ServiceDeskBundle\Entity\Issue $issue
     */
    public function setIssue($issue)
    {
        $this->issue = $issue;
    }

    /**
     * @return \Devprom\ServiceDeskBundle\Entity\Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }

    /**
     * @param string $objectClass
     */
    public function setObjectClass($objectClass)
    {
        $this->objectClass = $objectClass;
    }

    /**
     * @return string
     */
    public function getObjectClass()
    {
        return $this->objectClass;
    }

    /**
     * @param string $externalAuthor
     */
    public function setExternalAuthor($externalAuthor)
    {
        $this->externalAuthor = $externalAuthor;
    }

    /**
     * @return string
     */
    public function getExternalAuthor()
    {
        return $this->externalAuthor;
    }

    /**
     * @param string $externalEmail
     */
    public function setExternalEmail($externalEmail)
    {
        $this->externalEmail = $externalEmail;
    }

    /**
     * @return string
     */
    public function getExternalEmail()
    {
        return $this->externalEmail;
    }

    /**
     * @param string $value
     */
    public function setIsPrivate($value)
    {
        $this->isprivate = $value;
    }

    /**
     * @return string
     */
    public function getIsPrivate()
    {
        return $this->isprivate;
    }

    public function getAuthor() {
        if ($this->getInternalAuthor() && $this->getInternalAuthor()->getId() > 0) {
            return $this->getInternalAuthor();
        }

        return $this->getExternalAuthor()
            ? $this->getExternalAuthor()
            : $this->getExternalEmail();
    }

    /**
     * @param ArrayCollection $attachments
     */
    public function setAttachments($attachments) {
        $this->attachments = $attachments;
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments->matching(
            Criteria::create()->where(Criteria::expr()->in("ObjectClass", array("comment","Comment")))
        )->toArray();
    }

    /**
     * @param string $text
     */
    public function setEmailMessageId($text)
    {
        $this->emailMessageId = $text;
    }

    /**
     * @return string
     */
    public function getEmailMessageId()
    {
        return html_entity_decode($this->emailMessageId);
    }

    /**
     * @param IssueComment $value
     */
    public function setParentComment($value)
    {
        $this->parentComment = $value;
    }

    /**
     * @return IssueComment
     */
    public function getParentComment()
    {
        return $this->parentComment;
    }
}
