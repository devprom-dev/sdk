<?php

namespace Devprom\ServiceDeskBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ObjectChangeNotification")
 */
class IssueChangeNotification extends ObjectChangeNotification
{
    /**
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="notifications")
     * @ORM\JoinColumn(name="ObjectId", referencedColumnName="pm_ChangeRequestId")
     * @var Issue
     */
    private $issue;

    /**
     * @param Issue $value
     */
    public function setIssue($value)
    {
        $this->issue = $value;
    }

    /**
     * @return Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }
}