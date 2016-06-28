<?php

namespace Devprom\ServiceDeskBundle\Service;
use Devprom\ServiceDeskBundle\Entity\Comment;
use Devprom\ServiceDeskBundle\Entity\IssueCommentAttachment;
use Doctrine\ORM\EntityManager;


class CommentAttachmentService {

    /**
     * @var EntityManager
     */
    private $em;

    function __construct($em) {
        $this->em = $em;
    }

    /**
     * @param $attachmentId
     * @return IssueCommentAttachment
     */
    public function getAttachmentById($attachmentId) {
        return $this->em->getRepository("DevpromServiceDeskBundle:IssueCommentAttachment")->find($attachmentId);
    }
}