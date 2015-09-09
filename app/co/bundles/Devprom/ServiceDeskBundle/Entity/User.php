<?php

namespace Devprom\ServiceDeskBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

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
class User extends BaseUser {

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
     * @ORM\OneToOne(targetEntity="Company")
     * @ORM\JoinColumn(name="Company", referencedColumnName="co_CompanyId")
     * @var Company
     */
    protected $company;
    
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
}
