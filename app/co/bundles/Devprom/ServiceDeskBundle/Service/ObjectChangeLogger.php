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

include_once SERVER_ROOT_PATH.'core/classes/system/LockFileSystem.php';
include_once SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';

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
    private $session = null;

    function __construct(EntityManager $em, Translator $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }


    public function logIssueCreated(Issue $issue) {
		$lock = new \LockFileSystem( 'Request' );
        $lock->Release();

        $this->notifyIssueCreated($issue);
    }

    public function logCommentCreated(IssueComment $comment) {
        $ocl = $this->createBaseObjectChangeLog($comment->getIssue());
        $ocl->setChangeKind('commented');
        $ocl->setClassName('request');
        $ocl->setVisibilityLevel(2);
        $ocl->setContent($this->translator->trans('change.log.commented', array(
            '%commentId%' => $comment->getId(),
            '%comment%' => $comment->getText(),
        )));

        $this->em->persist($ocl);
        $this->em->flush();
    }

    /**
     * This function should be called only on detached entity and only prior to persist.
     * Otherwise it won't detected changed fields properly
     */
    public function logIssueModified(Issue $issue) {
        $ocl = $this->createBaseObjectChangeLog($issue);
        $ocl->setChangeKind('modified');
        $ocl->setVisibilityLevel(2);
        $ocl->setClassName('request');
        $changedFields = $this->getChangedFields($issue);

        $content = '';
        foreach ($changedFields as $key => $value) {
            $content .= $this->translator->trans('issue_' . $key) . ": " . $value[1] . "\r\n";
        }

        $ocl->setContent($content);

        $this->em->persist($ocl);
        $this->em->flush();
    }

    public function logExternalUserRegistered(User $user, $projectVPD) {
        $ocl = new ObjectChangeLog();
        $ocl->setCaption($user->getUsername());
        $ocl->setEntityRefName('cms_ExternalUser');
        $ocl->setEntityName('Внешний пользователь');
        $ocl->setChangeKind('added');
        $ocl->setClassName('externaluser');
        $ocl->setObjectId($user->getId());
        $ocl->setVpd($projectVPD);
        $ocl->setVisibilityLevel(1);

        $this->em->persist($ocl);
        $this->em->flush();
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

    protected function buildSession($issue)
    {
    	if ( is_object($this->session) ) return;
		
    	$system_it = getFactory()->getObject('SystemSettings')->getAll();
		return $this->session = new \PMSession(
				getFactory()->getObject('Project')->getExact($issue->getProject()), 
				new \AuthenticationFactory(
						getFactory()->getObject('User')->createCachedIterator(
								array (
										array (
												'Caption' => $system_it->getDisplayName(),
												'Email' => $system_it->get('AdminEmail')
										)
								)
							)						
        		)
			);
    }
    
    protected function notifyIssueCreated(Issue $issue)
    {
    	$this->buildSession($issue);
		getFactory()->getEventsManager()->notify_object_add(
				getFactory()->getObject('Request')->getExact($issue->getId()), array()
			);
    }

    protected function notifyIssueModified(Issue $issue)
    {
    	$this->buildSession($issue);
    	$issue_it = getFactory()->getObject('Request')->getExact($issue->getId());
		getFactory()->getEventsManager()->notify_object_modify(
				$issue_it, $issue_it, array()
			);
    }
}