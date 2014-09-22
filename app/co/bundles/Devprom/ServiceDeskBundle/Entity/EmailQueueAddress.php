<?php

namespace Devprom\ServiceDeskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="EmailQueueAddress")
 */
class EmailQueueAddress extends BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="EmailQueueId")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="ToAddress")
     * @var string
     */
    private $toAddress;

    /**
     * @ORM\ManyToOne(targetEntity="EmailQueue", inversedBy="emailQueueAddresses")
     * @ORM\JoinColumn(name="EmailQueue", referencedColumnName="EmailQueueId")
     * @var EmailQueue
     */
    private $queue;

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
     * @param \Devprom\ServiceDeskBundle\Entity\EmailQueue $queue
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;
    }

    /**
     * @return \Devprom\ServiceDeskBundle\Entity\EmailQueue
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param string $toAddress
     */
    public function setToAddress($toAddress)
    {
        $this->toAddress = $toAddress;
    }

    /**
     * @return string
     */
    public function getToAddress()
    {
        return $this->toAddress;
    }

}