<?php

namespace Devprom\ServiceDeskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="pm_Attachment")
 */
class IssueCommentAttachment extends BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="pm_AttachmentId")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="FilePath")
     * @var string
     */
    private $filePath;

    /**
     * @ORM\Column(type="string", name="FileExt")
     * @var string
     */
    private $originalFilename;

    /**
     * @ORM\ManyToOne(targetEntity="IssueComment", inversedBy="attachments")
     * @ORM\JoinColumn(name="ObjectId", referencedColumnName="CommentId")
     * @var Comment
     */
    private $comment;

    /**
     * @ORM\Column(type="string", name="FileMime")
     * @var string
     */
    private $contentType;

    /**
     * @ORM\Column(type="string", name="ObjectClass")
     * @var string
     */
    private $objectClass;

    /**
     * @Assert\NotBlank (message="attachment.no.file.error")
     * @var UploadedFile
     */
    private $file;

    /**
     * @param Comment $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return Comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
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
     * @param string $originalFilename
     */
    public function setOriginalFilename($originalFilename)
    {
        $this->originalFilename = $originalFilename;
    }

    /**
     * @return string
     */
    public function getOriginalFilename()
    {
        return $this->originalFilename;
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $objectClass
     */
    public function setObjectClass($objectClass)
    {
        $this->objectClass = $objectClass;
    }

    /**
     * @return string
     */
    public function getObjectClass()
    {
        return $this->objectClass;
    }





}
