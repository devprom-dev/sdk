<?php

namespace Devprom\ServiceDeskBundle\Service;

use Devprom\ServiceDeskBundle\Entity\Issue;
use Devprom\ServiceDeskBundle\Entity\IssueComment;
use Devprom\ServiceDeskBundle\Entity\IssueState;
use Devprom\ServiceDeskBundle\Entity\IssueStateComment;
use Devprom\ServiceDeskBundle\Entity\Priority;
use Devprom\ServiceDeskBundle\Entity\Severity;
use Devprom\ServiceDeskBundle\Entity\TextTemplate;
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

    public function saveIssue(Issue $issue, User $author, array $post) {
        $issue->setCaption(TextUtil::escapeHtml(addslashes($issue->getCaption())));
        $issue->setDescription(TextUtil::escapeHtml(addslashes($issue->getDescription())));
        if ($issue->getId()) {
            $this->updateIssue($issue, $author, $post);
        } else {
            $this->createIssue($issue, $author, $post);
        }
    }

    /**
     * @return Issue
     */
    public function getIssueById($id) {
        return $this->em->getRepository('DevpromServiceDeskBundle:Issue')->find($id);
    }

    public function getIssuesByAuthor($authorEmail, $sortColumn, $sortDirection, $state, $page)
    {
        $issueRepository = $this->em->getRepository('DevpromServiceDeskBundle:Issue');
        return $issueRepository->findByAuthor(
            $authorEmail,
            array(
                'state.terminalNum' => 'asc',
                'project.importance' => 'asc',
                'state.orderNum' => 'asc',
                'state.name' => 'asc',
                $sortColumn => $sortDirection
            ),
            $state,
            $page
        );
    }
    
    public function getIssuesByCompany($authorEmail, $sortColumn, $sortDirection, $state, $page)
    {
        $issueRepository = $this->em->getRepository('DevpromServiceDeskBundle:Issue');
        return $issueRepository->findByCompany(
            $authorEmail,
            array(
                'state.terminalNum' => 'asc',
                'project.importance' => 'asc',
                'state.orderNum' => 'asc',
                'state.name' => 'asc',
                $sortColumn => $sortDirection
        	),
            $state,
            $page
        );
    }

    /**
     * @return Issue
     */
    public function getBlankIssue( $vpds )
    {
        $textTemplate = $this->em->getRepository('DevpromServiceDeskBundle:TextTemplate')->findBy(array(
            "vpd" => array_pop($vpds),
            "objectclass" => 'Request',
            "default" => 'Y'
        ));

        $issue = new Issue();
        $issue->setDescription(
            count($textTemplate) > 0 ? TextUtil::unescapeHtml(array_pop($textTemplate)->getContent()) : '');
        $issue->setPriority($this->getDefaultPriority());
        $issue->setSeverity($this->getDefaultSeverity());
    	$issue->setProject(
            array_pop(
                $this->em->getRepository('DevpromServiceDeskBundle:Project')->findBy(array(
                    "vpd" => array_pop($vpds),
            ))));
        return $issue;
    }

    public function saveComment(IssueComment $issueComment, Issue $issue, User $author)
    {
        $issueComment->setText(TextUtil::escapeHtml(addslashes($issueComment->getText())));
        $issueComment->setVpd($issue->getVpd());
        $issueComment->setObjectClass('Request');
        $issueComment->setExternalAuthor($author->getUsername());
        $issueComment->setExternalEmail($author->getEmail());
        $issue->addComment($issueComment);

        $this->clearNotifications($issue, $author);
        $this->objectChangeLogger->logCommentCreated($issueComment,$author);
    }

    public function clearNotifications(Issue $issue, User $user)
    {
        if ( !is_object($user) ) return;

        foreach( $issue->getNotifications($user) as $notification ) {
            $this->em->remove($notification);
        }
        $issue->setNotifications(null);
        $this->em->persist($issue);
        $this->em->flush();
    }

    protected function createIssue(Issue $issue, User $author, array $post)
    {
        // persist issue
        $product = $issue->getProduct();
        if ( is_object($product) ) {
            $project = array_shift(
                $this->em->getRepository('DevpromServiceDeskBundle:Project')->findBy(
                    array(
                        'vpd' => $product->getVpd()
                    )
                )
            );
            $issue->setProject($project);
        }
        else {
            $project = $issue->getProject();
        }
	    $issue->setVpd($project->getVpd());
        $issue->setState($this->getFirstIssueStateForProject($issue, $project));
        $severity = $issue->getSeverity();
        if ( is_object($severity) ) {
            try {
                $priority = $this->em->getRepository('Devprom\ServiceDeskBundle\Entity\Priority')->find($severity->getId());
            }
            catch( \Exception $e) {}
        }
        if ( !is_object($priority) ) {
            $priority = $this->em->getRepository('Devprom\ServiceDeskBundle\Entity\Priority')->findOneBy([]);
        }
        $issue->setPriority($priority);
        $issue->setCustomer($author);
        $issue->setAuthor(
            array_shift($this->em->getRepository('DevpromServiceDeskBundle:InternalUser')->findBy(
                array(
                    'email' => $author->getEmail()
                )
            ))
        );
        $issue->setOrderNum(10);

        $this->em->persist($issue);
        $this->em->flush();

        $this->updateCustomAttributes($issue, $author, $post);
        $this->objectChangeLogger->logIssueCreated($issue,$author);
    }

    public function updateIssue(Issue $issue, User $author, array $post)
    {
        $severity = $issue->getSeverity();
        if ( is_object($severity) ) {
            try {
                $priority = $this->em->getRepository(
                    'Devprom\ServiceDeskBundle\Entity\Priority')->find($severity->getId());
                $issue->setPriority($priority);
            }
            catch( \Exception $e) {}
        }

        list($changed, $stateIt) = $this->objectChangeLogger->logIssueModified($issue, $author);
        $this->em->persist($issue);
        $this->em->flush();

        $this->updateCustomAttributes($issue, $author, $post);
        $this->objectChangeLogger->notifyIssueModified($issue, $author, $stateIt, $changed);
    }

    public function updateCustomAttributes( Issue $issue, User $author, array $post )
    {
        $this->buildProjectSession($issue, $author);

        $request = getFactory()->getObject('Request');
        $attributes = $request->getAttributesByOrigin(ORIGIN_CUSTOM) ;

        $post = array_filter($post, function($value, $key) use($attributes) {
                        return in_array($key, $attributes);
                    }, ARRAY_FILTER_USE_BOTH);
        if ( count($post) < 1 ) return;

        getFactory()->resetCachedIterator($request);
        getFactory()->modifyEntity($request->getExact($issue->getId()), $post);
    }

    /**
     * @return Priority
     */
    protected function getDefaultPriority()
    {
        $result = $this->em->getRepository("DevpromServiceDeskBundle:Priority")->findBy(
            array(
                "default" => 'Y'
            )
        );
        if ( count($result) < 1 ) {
            $result = $this->em->getRepository("DevpromServiceDeskBundle:Priority")->findAll();
        }
        return array_pop($result);
    }

    /**
     * @return Priority
     */
    protected function getDefaultSeverity()
    {
        $result = $this->em->getRepository("DevpromServiceDeskBundle:Severity")->findBy(
            array(
                "default" => 'Y'
            )
        );
        if ( count($result) < 1 ) {
            $result = $this->em->getRepository("DevpromServiceDeskBundle:Severity")->findAll();
        }
        return array_pop($result);
    }

    /**
     * @return IssueState
     */
    protected function getFirstIssueStateForProject($issue, $project)
    {
        $methodology = $this->em->getRepository("DevpromServiceDeskBundle:Methodology")
            ->findBy(array(
                    "project" => $project->getId()
                ), array(), 1);
        $firstState = $this->em->getRepository("DevpromServiceDeskBundle:IssueState")->findBy(array(
            "vpd" => $project->getVpd(),
            "objectClass" => $methodology[0]->getRequirements() == 'Y' && $issue->getIssueType() == '' ? 'issue' : 'request',
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

    protected function buildProjectSession(Issue $issue, User $user)
    {
        global $session;
        $session = new \PMSession(
            getFactory()->getObject('Project')->getExact($issue->getProject()->getId()),
            new \AuthenticationFactory(
                getFactory()->getObject('User')->createCachedIterator(
                    array (
                        array (
                            'Caption' => $user->getUsername(),
                            'Email' => $user->getEmail()
                        )
                    )
                )
            )
        );
        getFactory()->setAccessPolicy(new \AccessPolicy(getFactory()->getCacheService()));
    }
}