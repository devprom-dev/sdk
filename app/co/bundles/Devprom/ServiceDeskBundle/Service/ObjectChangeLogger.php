<?php

namespace Devprom\ServiceDeskBundle\Service;
use Devprom\ServiceDeskBundle\Entity\Issue;
use Devprom\ServiceDeskBundle\Entity\IssueComment;
use Devprom\ServiceDeskBundle\Entity\ObjectChangeLog;
use Devprom\ServiceDeskBundle\Entity\AffectedObject;
use Devprom\ServiceDeskBundle\Entity\ProjectParticipant;
use Devprom\ServiceDeskBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

include_once SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';
include_once SERVER_ROOT_PATH.'admin/classes/common/AdminSession.php';

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class ObjectChangeLogger
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Translator
     */
    private $translator;
    private $project_session = null;

    function __construct(EntityManager $em, Translator $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }


    public function logIssueCreated(Issue $issue, User $user) {
		$lock = new \LockFileSystem( 'Request' );
        $lock->Release();
        $this->notifyIssueCreated($issue,$user);
    }

    public function logCommentCreated(IssueComment $comment, User $user) {
		$lock = new \LockFileSystem( 'Comment' );
        $lock->Release();
        $this->notifyCommentCreated($comment,$user);
    }

    /**
     * This function should be called only on detached entity and only prior to persist.
     * Otherwise it won't detected changed fields properly
     */
    public function logIssueModified(Issue $issue, User $user)
    {
        $this->buildProjectSession($issue, $user);
        $stateIt = getFactory()->getObject('Request')->getExact($issue->getId());

        $ocl = $this->createBaseObjectChangeLog($issue);
        $ocl->setChangeKind('modified');
        $ocl->setVisibilityLevel(2);
        $ocl->setClassName('request');
        $changedFields = $this->getChangedFields($issue);

        $content = '';
        $modified = array();
        foreach ($changedFields as $key => $value) {
            $content .= $this->translator->trans('issue_' . $key) . ": " . $value[1] . "\r\n";
            $modified[$key] = '';
        }

        $ocl->setContent($content);

        $this->em->persist($ocl);
        $this->em->flush();

        return array(
            $modified, $stateIt
        );
    }

    public function logExternalUserRegistered(User $user) {
        $ocl = new ObjectChangeLog();
        $ocl->setCaption($user->getUsername());
        $ocl->setEntityRefName('cms_ExternalUser');
        $ocl->setEntityName('Внешний пользователь');
        $ocl->setChangeKind('added');
        $ocl->setClassName('externaluser');
        $ocl->setObjectId($user->getId());
        $ocl->setVisibilityLevel(1);

        $this->em->persist($ocl);
        $this->em->flush();
        
        $this->notifyCustomerCreated($user);
    }


    /**
     * @param Issue $issue
     * @return ObjectChangeLog
     */
    protected function createBaseObjectChangeLog(Issue $issue)
    {
        $ocl = new ObjectChangeLog();
        $ocl->setCaption($issue->getCaption());
        $ocl->setEntityRefName('pm_ChangeRequest');
        $ocl->setObjectId($issue->getId());
        $ocl->setVpd($issue->getVpd());
        $ocl->setVisibilityLevel(1);
        return $ocl;
    }
    
    protected function createAffectedObject(Issue $issue)
    {
        $ocl = new AffectedObject();
        $ocl->setObjectClass('Request');
        $ocl->setObjectId($issue->getId());
        $ocl->setVpd($issue->getVpd());
        return $ocl;
    }

    /**
     * @param Issue $issue
     * @return array
     */
    protected function getChangedFields(Issue $issue)
    {
        $unitOfWork = $this->em->getUnitOfWork();
        $unitOfWork->computeChangeSets();
        return $unitOfWork->getEntityChangeSet($issue);
    }

    protected function buildProjectSession($issue, User $user)
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
    
    protected function notifyCustomerCreated(User $user)
    {
		getFactory()->getEventsManager()->notify_object_add(
				getFactory()->getObject('Customer')->getExact($user->getId()), array()
			);
    }
    
    protected function notifyIssueCreated(Issue $issue, User $user)
    {
    	$this->buildProjectSession($issue, $user);
		$object_it = getFactory()->getObject('Request')
                        ->getExact($issue->getId())->getSpecifiedIt();
        $object_it->object->updateUID($object_it->getId());
		
		getFactory()->getEventsManager()->notify_object_add($object_it, array());
		getFactory()->getEventsManager()
	    	->executeEventsAfterBusinessTransaction($object_it, 'WorklfowMovementEventHandler');
    }

    public function notifyIssueModified(Issue $issue, User $user, $stateIt, array $changed)
    {
        $this->buildProjectSession($issue, $user);
        $request = getFactory()->getObject('Request');
        getFactory()->resetCachedIterator($request);
        $object_it = $request->getExact($issue->getId());
        getFactory()->getEventsManager()->notify_object_modify($stateIt, $object_it, $changed);
    }

    protected function notifyCommentCreated(IssueComment $comment, User $user)
    {
    	$this->buildProjectSession($comment->getIssue(), $user);
    	getFactory()->getEventsManager()->notify_object_add(
				getFactory()->getObject('Comment')->getExact($comment->getId()), array()
			);
    }
}