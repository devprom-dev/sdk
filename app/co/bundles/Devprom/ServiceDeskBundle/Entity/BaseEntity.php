<?php

namespace Devprom\ServiceDeskBundle\Entity;
use DateTime;
use DateTimeZone;
use DateInterval;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class BaseEntity {

    /**
     * @ORM\Column(type="datetime", name="RecordCreated")
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", name="RecordModified")
     * @var DateTime
     */
    protected $modifiedAt;

    /**
     * @ORM\Column(type="string", name="VPD")
     * @var string
     */
    protected $vpd;

    /**
     * @ORM\Column(type="integer", name="RecordVersion")
     * @var integer
     */
    protected $version;

    /**
     * @ORM\PrePersist
     */
    function prePersist()
    {
    	$time = $this->getNowDateTime();
        $this->setCreatedAt($time);
        $this->setModifiedAt($time);
        $this->setVersion(1);
    }

    /**
     * @ORM\PreUpdate
     */
    function preUpdate()
    {
        $this->setModifiedAt($this->getNowDateTime());
        $this->setVersion($this->getVersion() + 1);
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param string $vpd
     */
    public function setVpd($vpd)
    {
        $this->vpd = $vpd;
    }

    /**
     * @return string
     */
    public function getVpd()
    {
        return $this->vpd;
    }

    /**
     * @param integer $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    protected function getNowDateTime()
    {
		return new DateTime("now", \EnvironmentSettings::getClientTimeZone());
    }
}