<?php

namespace Devprom\ServiceDeskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="WikiPageFile")
 */
class KnowledgeBaseAttachment extends BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="WikiPageFileId")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="ContentPath")
     * @var string
     */
    private $filePath;

    /**
     * @ORM\Column(type="string", name="ContentExt")
     * @var string
     */
    private $originalFilename;

    /**
     * @ORM\ManyToOne(targetEntity="KnowledgeBase", inversedBy="attachments")
     * @ORM\JoinColumn(name="WikiPage", referencedColumnName="WikiPageId")
     * @var KnowledgeBase
     */
    private $page;

    /**
     * @ORM\Column(type="string", name="ContentMime")
     * @var string
     */
    private $contentType;

    /**
     * @Assert\NotBlank (message="attachment.no.file.error")
     * @var UploadedFile
     */
    private $file;

    /**
     * @param KnowledgeBase $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return KnowledgeBase
     */
    public function getComment()
    {
        return $this->page;
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
}
