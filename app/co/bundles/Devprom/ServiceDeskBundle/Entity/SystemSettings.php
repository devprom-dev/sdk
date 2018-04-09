<?php

namespace Devprom\ServiceDeskBundle\Entity;
use Devprom\ServiceDeskBundle\Util\TextUtil;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="cms_SystemSettings")
 */
class SystemSettings {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="cms_SystemSettingsId")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="Caption")
     * @var string
     */
    private $clientName;

    /**
     * @ORM\Column(type="string", name="AdminEmail")
     * @var string
     */
    private $adminEmail;

    /**
     * @ORM\Column(type="string", name="Language")
     * @var string
     */
    private $language;

    /**
     * @param string $adminEmail
     */
    public function setAdminEmail($adminEmail)
    {
        $this->adminEmail = $adminEmail;
    }

    /**
     * @return string
     */
    public function getAdminEmail()
    {
        return $this->adminEmail;
    }

    /**
     * @param string $value
     */
    public function setLanguage($value)
    {
        $this->language = $value;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $clientName
     */
    public function setClientName($clientName)
    {
        $this->clientName = $clientName;
    }

    /**
     * @return string
     */
    public function getClientName()
    {
        return TextUtil::unescapeHtml($this->clientName);
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

}