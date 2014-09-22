<?php

namespace Devprom\ServiceDeskBundle\Service;

use Devprom\ServiceDeskBundle\Entity\Issue;
use Devprom\ServiceDeskBundle\Entity\IssueComment;
use Devprom\ServiceDeskBundle\Entity\IssueState;
use Devprom\ServiceDeskBundle\Entity\IssueStateComment;
use Devprom\ServiceDeskBundle\Entity\Priority;
use Devprom\ServiceDeskBundle\Entity\User;
use Devprom\ServiceDeskBundle\Entity\Watcher;
use Devprom\ServiceDeskBundle\Mailer\Mailer;
use Devprom\ServiceDeskBundle\Repository\IssueRepository;
use Devprom\ServiceDeskBundle\Util\TextUtil;
use Doctrine\ORM\EntityManager;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class IssueService {

    /** @var  EntityManager */
    private $em;

    /** @var  ObjectChangeLogger */
    private $objectChangeLogger;

    /** @var  Mailer */
    private $mailer;

    function __construct($em, ObjectChangeLogger $objectChangeLogger, Mailer $mailer)
    {
        $this->em = $em;
        $this->objectChangeLogger = $objectChangeLogger;
        $this->mailer = $mailer;
    }

    public function saveIssue(Issue $issue, $projectId, User $author) {
        $issue->setCaption(TextUtil::escapeHtml($issue->getCaption()));
        $issue->setDescription(TextUtil::escapeForDevpromWysiwygFields($issue->getDescription()));
        if ($issue->getId()) {
            $this->updateIssue($issue);
        } else {
            $this->createIssue($issue, $projectId, $author);
        }
    }

    /**
     * @return Issue
     */
    public function getIssueById($id) {
        return $this->em->getRepository('DevpromServiceDeskBundle:Issue')->find($id);
    }

    public function getIssuesByProjectAndAuthor($projectId, $authorEmail, $sortColumn, $sortDirection) {
        /** @var IssueRepository $issueRepository */
        $issueRepository = $this->em->getRepository('DevpromServiceDeskBundle:Issue');
        return $issueRepository->findByProjectAndAuthor(
            $projectId,
            $authorEmail,
            array($sortColumn => $sortDirection)
        );
    }

    /**
     * @return Issue
     */
    public function getBlankIssue() {
        $issue = new Issue();
        $issue->setPriority($this->getDefaultPriority());
        return $issue;
    }

    public function saveComment(IssueComment $issueComment, Issue $issue, User $author) {
        $issueComment->setText(TextUtil::escapeForDevpromWysiwygFields($issueComment->getText()));

        $issueComment->setVpd($issue->getVpd());
        $issueComment->setObjectClass('Request');
        $issueComment->setExternalAuthor($author->getUsername());
        $issueComment->setExternalEmail($author->getEmail());
        $issue->addComment($issueComment);

        $this->em->persist($issue);
        $this->em->flush();

        $this->objectChangeLogger->logCommentCreated($issueComment);
    }

    protected function createIssue(Issue $issue, $projectId, User $author) {
        $vpd = $this->getProjectVPD($projectId);

        // persist issue
        $issue->setProject($this->em->getReference("DevpromServiceDeskBundle:Project", $projectId));
        $issue->setVpd($vpd);
        $issue->setState($this->getFirstIssueStateForProject($projectId));
        $this->em->persist($issue);
        $this->em->flush();

        $this->addIssueWatcher($issue, $author->getEmail(), $vpd);

        $this->objectChangeLogger->logIssueCreated($issue);

        $this->mailer->sendIssueCreatedMessage($issue, $author->getEmail(), $author->getLanguage());
    }

    protected function updateIssue(Issue $issue) {
        $this->objectChangeLogger->logIssueModified($issue);

        $this->em->persist($issue);
        $this->em->flush();
    }

    /**
     * @return string
     */
    protected function getProjectVPD($projectId)
    {
        return \ModelProjectOriginationService::getOrigin($projectId);
    }

    /**
     * @return Priority
     */
    protected function getDefaultPriority()
    {
        return $this->em->getReference("DevpromServiceDeskBundle:Priority", Issue::NORMAL_PRIORITY);
    }

    /**
     * @return IssueState
     */
    protected function getFirstIssueStateForProject($projectId)
    {
        $firstState = $this->em->getRepository("DevpromServiceDeskBundle:IssueState")->findBy(array(
            "vpd" => $this->getProjectVPD($projectId),
            "objectClass" => 'request',
        ), array('orderNum' => 'ASC'), 1);
        return $firstState[0];
    }

    /**
     * @param Issue $issue
     * @param $authorEmail
     * @param $vpd
     */
    protected function addIssueWatcher(Issue $issue, $authorEmail, $vpd)
    {
        $watcher = new Watcher();
        $watcher->setEmail($authorEmail);
        $watcher->setVpd($vpd);
        $watcher->setObjectClass('request');
        $watcher->setObjectId($issue->getId());

        $this->em->persist($watcher);
        $this->em->flush();
    }

}