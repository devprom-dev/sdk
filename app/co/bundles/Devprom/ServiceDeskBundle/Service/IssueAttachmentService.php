<?php

namespace Devprom\ServiceDeskBundle\Service;
use Devprom\ServiceDeskBundle\Entity\Issue;
use Devprom\ServiceDeskBundle\Entity\IssueAttachment;
use Doctrine\ORM\EntityManager;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class IssueAttachmentService {

    /**
     * @var EntityManager
     */
    private $em;

    function __construct($em) {
        $this->em = $em;
    }

    public function save(IssueAttachment $attachment, Issue $issue) {
        $storedFilename = "File" . md5(uniqid());

        $attachment->setIssue($issue);
        $attachment->setContentType($attachment->getFile()->getMimeType());
        $attachment->setOriginalFilename($attachment->getFile()->getClientOriginalName());
        $attachment->setObjectClass('request');
        $attachment->setVpd($issue->getVpd());
        $attachment->setFilePath(SERVER_FILES_PATH . "/pm_Attachment/" . $storedFilename);
        $attachment->getFile()->move(SERVER_FILES_PATH . "/pm_Attachment/", $storedFilename);
        $attachment->setFileSize(filesize(SERVER_FILES_PATH . "/pm_Attachment/" . $storedFilename));

        $this->em->persist($attachment);
        $this->em->flush();
    }

    public function delete(IssueAttachment $attachment) {
        $this->em->remove($attachment);
        $this->em->flush();
    }

    /**
     * @param $attachmentId
     * @return IssueAttachment
     */
    public function getAttachmentById($attachmentId) {
        return $this->em->getRepository("DevpromServiceDeskBundle:IssueAttachment")->find($attachmentId);
    }

}