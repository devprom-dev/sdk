<?php

namespace Devprom\ServiceDeskBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use FR3D\LdapBundle\Model\LdapUserInterface;
use FR3D\LdapBundle\Model;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 *
 * @ORM\Entity(repositoryClass="Devprom\ServiceDeskBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="cms_ExternalUser")
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="usernameCanonical", column=@ORM\Column(type="string", name="username_canonical", length=255, unique=false))
 * })
 **/
class User extends BaseUser implements LdapUserInterface {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="cms_ExternalUserId")
     * @ORM\GeneratedValue
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="language")
     * @var string
     */
    protected $language;

    /**
     * @ORM\Column(type="string", name="dn")
     * @var string
     */
    protected $dn;

    /**
     * @ORM\OneToOne(targetEntity="Company")
     * @ORM\JoinColumn(name="Company", referencedColumnName="co_CompanyId")
     * @var Company
     */
    protected $company;

    /**
     * @ORM\OneToMany(targetEntity="ObjectChangeNotification", mappedBy="customer", fetch="EAGER", cascade={"all"})
     * @var ArrayCollection
     */
    private $notifications;

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
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param Company $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getDn() : ?string
    {
        return $this->dn;
    }

    /**
     * @param string $value
     */
    public function setDn($value)
    {
        $this->dn = $value;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    public function isCredentialsNonExpired()
    {
    	return true;
    }

    /**
     * @param ObjectChangeNotification $value
     */
    public function setNotifications($value)
    {
        $this->notifications = $value;
    }

    /**
     * @return ArrayCollection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }
}
