<?php

namespace Devprom\ServiceDeskBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="co_CompanyProduct")
 */
class CompanyProduct extends BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="co_CompanyProductId")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="Product", referencedColumnName="pm_FunctionId")
     * @var Product
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="products")
     * @ORM\JoinColumn(name="Company", referencedColumnName="co_CompanyId")
     * @var Company
     */
    private $company;
    
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
     * @param Project $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return Project
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Company $parm
     */
    public function setCompany($parm)
    {
        $this->company = $parm;
    }

    /**
     * @return Project
     */
    public function getCompany()
    {
        return $this->company;
    }
}