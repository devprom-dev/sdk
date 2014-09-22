<?php

namespace Devprom\ServiceDeskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="pm_StateObject")
 */
class IssueStateComment extends BaseEntity {

    /**
     * @ORM\Id @ORM\Column(type="integer", name="pm_StateObjectId")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="Comment")
     * @var string
     */
    private $comment;

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
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    function __toString()
    {
        return $this->getComment();
    }
}