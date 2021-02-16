<?php
namespace Devprom\ServiceDeskBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="pm_TextTemplate")
 */
class TextTemplate extends BaseEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="pm_TextTemplateId")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="Caption")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", name="Content")
     * @var string
     */
    private $content;

    /**
     * @ORM\Column(type="string", name="ObjectClass")
     * @var string
     */
    private $objectclass;

    /**
     * @ORM\Column(type="string", name="IsDefault")
     * @var string
     */
    private $default;

    /**
     * @ORM\Column(type="integer", name="OrderNum")
     * @var integer
     */
    private $orderNum;

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

    /**
     * @param string $name
     */
    public function setContent($value)
    {
        $this->content = $value;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $name
     */
    public function setObjectclass($value)
    {
        $this->objectclass = $value;
    }

    /**
     * @return string
     */
    public function getObjectclass()
    {
        return $this->objectclass;
    }

    /**
     * @param string $name
     */
    public function setDefault($value)
    {
        $this->default = $value;
    }

    /**
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
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

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return int
     */
    public function getOrderNum()
    {
        return $this->orderNum;
    }

    /**
     * @param int $id
     */
    public function setOrderNum($value)
    {
        $this->orderNum = $value;
    }
}