<?php

namespace Devprom\ServiceDeskBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="cms_User")
 */
class InternalUser {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="cms_UserId")
     * @ORM\GeneratedValue
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="Caption")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", name="Email")
     * @var string
     */
    protected $email;

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    function __toString()
    {
        return $this->getName();
    }

}
