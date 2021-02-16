<?php
namespace Devprom\ServiceDeskBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="pm_Severity")
 */
class Severity extends BaseEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="pm_SeverityId")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="Caption")
     * @var string
     */
    private $name;

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