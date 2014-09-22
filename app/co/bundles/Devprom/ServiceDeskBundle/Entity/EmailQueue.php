<?php

namespace Devprom\ServiceDeskBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="EmailQueue")
 */
class EmailQueue extends BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="EmailQueueId")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="Caption")
     * @var string
     */
    private $subject;

    /**
     * @ORM\Column(type="string", name="Description")
     * @var string
     */
    private $body;

    /**
     * @ORM\Column(type="string", name="FromAddress")
     * @var string
     */
    private $fromAddress;

    /**
     * @ORM\Column(type="string", name="MailboxClass")
     * @var string
     */
    private $mailboxClass;

    /**
     * @ORM\OneToMany(targetEntity="EmailQueueAddress", mappedBy="queue", cascade={"all"})
     * @var ArrayCollection
     */
    private $emailQueueAddresses;

    function __construct()
    {
        $this->emailQueueAddresses = new ArrayCollection();
    }


    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param ArrayCollection $emailQueueAddresses
     */
    public function setEmailQueueAddresses($emailQueueAddresses)
    {
        $this->emailQueueAddresses = $emailQueueAddresses;
    }

    /**
     * @return ArrayCollection
     */
    public function getEmailQueueAddresses()
    {
        return $this->emailQueueAddresses;
    }

    /**
     * @param string $fromAddress
     */
    public function setFromAddress($fromAddress)
    {
        $this->fromAddress = $fromAddress;
    }

    /**
     * @return string
     */
    public function getFromAddress()
    {
        return $this->fromAddress;
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
     * @param string $mailboxClass
     */
    public function setMailboxClass($mailboxClass)
    {
        $this->mailboxClass = $mailboxClass;
    }

    /**
     * @return string
     */
    public function getMailboxClass()
    {
        return $this->mailboxClass;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $address
     */
    public function addRecipient($address) {
        $eqa = new EmailQueueAddress();
        $eqa->setToAddress($address);
        $eqa->setQueue($this);
        $this->emailQueueAddresses->add($eqa);
    }


}