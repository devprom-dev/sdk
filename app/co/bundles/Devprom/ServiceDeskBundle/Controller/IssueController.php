<?php

namespace Devprom\ServiceDeskBundle\Controller;

use Devprom\ServiceDeskBundle\Entity\IssueComment;
use Devprom\ServiceDeskBundle\Service\IssueAttachmentService;
use Devprom\ServiceDeskBundle\Service\IssueService;
use Devprom\ServiceDeskBundle\Util\TextUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Devprom\ServiceDeskBundle\Entity\Issue;
use Devprom\ServiceDeskBundle\Form\Type\IssueFormType;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Issue controller.
 *
 * @Route("/")
 */
class IssueController extends Controller
{
    /**
     * Creates a new Issue entity.
     *
     * @Route("/issue", name="issue_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
    	if ( !is_object($this->getUser()) ) throw $this->createAccessDeniedException();
    	
    	$vpds = $this->getProjectVpds();
        if ( count($vpds) < 1 ) {
            return $this->redirect($this->generateUrl('issue_list'));
        }

    	$issue = new Issue();
        $form = $this->createForm( IssueFormType::class, $issue, array(
                'vpds' => $vpds,
                'user' => $this->getUser(),
                'allowAttachment' => true
            ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getIssueService()->saveIssue($issue, $this->getUser());
            if ($issue->getNewAttachment()) {
                $this->getAttachmentService()->save($issue->getNewAttachment(), $issue);
            }
            $this->container->get('session')->getFlashBag()->set('issue_created', 'issue.add.flash');

            return $this->redirect($this->generateUrl('issue_show', array('id' => $issue->getId())));
        }
        else {
            $this->get('logger')->err($form->getErrors());
            throw new \LogicException('Unable append Issue entity.');
        }

        return array(
            'issue' => $issue,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Issue entity.
     *
     * @Route("/issue/new", name="issue_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction( Request $request )
    {
    	if ( !is_object($this->getUser()) ) throw $this->createAccessDeniedException();
    	
        $vpds = $this->getProjectVpds();
        if ( count($vpds) < 1 ) {
            return $this->redirect($this->generateUrl('issue_list'));
        }

        if ( $request->get('project') != '' ) {
            $vpds = array_intersect($vpds, array($request->get('project')));
        }
        else {
            try {
                $maxProducts = $this->container->getParameter('max_products_in_combo');
                if ( $maxProducts != '' && count($vpds) > $maxProducts ) {
                    return $this->redirect($this->generateUrl('select_product'));
                }
            }
            catch( \Exception $e ) {
            }
        }

        $issue = $this->getIssueService()->getBlankIssue($vpds);
        $form = $this->createForm( IssueFormType::class, $issue, array(
                'vpds' => $vpds,
                'user' => $this->getUser(),
                'allowAttachment' => true
            ));

        return $this->render('Issue/new.html.twig', array(
            'issue' => $issue,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Issue entity.
     *
     * @Route("/issue/{id}", name="issue_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
    	if ( !is_object($this->getUser()) ) throw $this->createAccessDeniedException();
    	
        $issue = $this->getIssueService()->getIssueById($id);
        if (!$issue) {
            throw new \LogicException('Unable to find Issue entity.');
        }

        $this->checkUserIsAuthorized($issue);
        $commentForm = $this->getCommentForm(new IssueComment());

        $this->getIssueService()->clearNotifications($issue, $this->getUser());

        return $this->render('Issue/show.html.twig', array(
            'issue' => $issue,
            'comment_form' => $commentForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Issue entity.
     *
     * @Route("/issue/{id}/edit", name="issue_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
    	if ( !is_object($this->getUser()) ) throw $this->createAccessDeniedException();
    	
        $issue = $this->getIssueService()->getIssueById($id);
        if (!$issue) {
            throw new \LogicException('Unable to find Issue entity.');
        }

        $this->checkUserIsAuthorized($issue);
        $issue->setCaption(TextUtil::unescapeHtml($issue->getCaption()));
        $issue->setDescription(TextUtil::unescapeHtml($issue->getDescription()));

        $editForm = $this->createForm(IssueFormType::class, $issue, array(
                'vpds' => array($issue->getVpd()),
                'user' => $this->getUser(),
                'allowAttachment' => false
            ));

        $this->getIssueService()->clearNotifications($issue, $this->getUser());

        return $this->render('Issue/edit.html.twig', array(
            'issue' => $issue,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Edits an existing Issue entity.
     *
     * @Route("/issue/{id}", name="issue_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
    	if ( !is_object($this->getUser()) ) throw $this->createAccessDeniedException();
    	
        $issue = $this->getIssueService()->getIssueById($id);
        if (!$issue) {
            throw new \LogicException('Unable to find Issue entity.');
        }

        $this->checkUserIsAuthorized($issue);

        $editForm = $this->createForm( IssueFormType::class, $issue, array(
                'method' => 'put',
                'vpds' => $this->getProjectVpds(),
                'user' => $this->getUser(),
                'allowAttachment' => false
            ));

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->getIssueService()->saveIssue($issue, $this->getUser());
            return $this->redirect($this->generateUrl('issue_show', array('id' => $id)));
        }
        else {
            $this->get('logger')->err((string) $editForm->getErrors(true, false));
            throw new \LogicException('Form is not valid.');
        }

        return array(
            'issue' => $issue,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Issue entity.
     *
     * @Route("/issue/{issueId}/comment", name="issue_add_comment")
     * @Method("POST")
     */
    public function addCommentAction(Request $request, $issueId)
    {
    	if ( !is_object($this->getUser()) ) throw $this->createAccessDeniedException();
    	
        $issue = $this->getIssueService()->getIssueById($issueId);
        if (!$issue) {
            throw new \LogicException('Unable to find Issue entity.');
        }

        $this->checkUserIsAuthorized($issue);

        $issueComment = new IssueComment();
        $commentForm = $this->getCommentForm($issueComment);
        $commentForm->handleRequest($request);

        $this->getIssueService()->saveComment($issueComment, $issue, $this->getUser());
        return $this->redirect($this->generateUrl('issue_show', array('id' => $issueId)));

        return $this->render("Issue/show.html.twig", array(
            'issue' => $issue,
            'comment_form' => $commentForm->createView(),
        ));
    }

    /**
     * Lists all Issue entities.
     *
     * @Route("/issues/{filter}/{state}/{sortColumn}/{sortDirection}", name="issue_list", requirements={"filter" = "my|company"}, defaults={"filter" = "my", "state" = "open", "sortColumn" = "issue.createdAt", "sortDirection" = "desc"})
     * @Method("GET")
     * @Template()
     */
    public function indexAction($filter, $state, $sortColumn, $sortDirection)
    {
    	if ( !is_object($this->getUser()) ) throw $this->createAccessDeniedException();

        $company = $this->getUser()->getCompany();
    	if ( $filter == 'my' || (is_object($company) && $company->getSeeCompanyIssues() != 'Y') )
    	{
	        $issues = $this->getIssueService()->getIssuesByAuthor(
	            $this->getUser()->getEmail(), $sortColumn, $sortDirection, $state
	        );
    	}
    	else
    	{
	        $issues = $this->getIssueService()->getIssuesByCompany(
	            $this->getUser()->getEmail(), $sortColumn, $sortDirection, $state
	        );
    	}

        return $this->render('Issue/index.html.twig', array(
            'issues' => $issues,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'issuesFilter' => $filter,
            'state' => $state
        ));
    }

    /**
     * Displays list of Products.
     *
     * @Route("/products", name="select_product")
     * @Method("GET")
     * @Template("DevpromServiceDeskBundle:Issue:product.html.twig")
     */
    public function productAction()
    {
        if ( !is_object($this->getUser()) ) throw $this->createAccessDeniedException();

        $projects = $this->get('doctrine.orm.entity_manager')
            ->getRepository('DevpromServiceDeskBundle:Project')
            ->findBy(
                array('vpd' => $this->getProjectVpds()),
                array('importance' => 'ASC', 'name' => 'ASC')
            );

        return array(
            'projects' => $projects
        );
    }

    protected function getProjectVpds()
    {
    	$customer_vpds = array();
    	if ( is_object($this->getUser()) && $this->getUser()->getCompany() ) {
	    	foreach($this->getUser()->getCompany()->getProjects() as $project_ref) {
	    		$customer_vpds[] = $project_ref->getProject()->getVpd();
	    	}
    	}
        if ( count($customer_vpds) > 0 ) return $customer_vpds;
    	return $this->container->getParameter('commonProjectVpds');
    }
    
    /**
     * @param $issueComment
     * @return \Symfony\Component\Form\Form
     */
    protected function getCommentForm($issueComment)
    {
        return $this->createFormBuilder($issueComment)
            ->add("text", 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array(
                'label' => false,
                'attr' => ['rows' => 10]
            ))
            ->getForm();
    }

    /**
     * @return IssueService
     */
    protected function getIssueService() {
        return $this->container->get('issue_service');
    }

    /**
     * @return IssueAttachmentService
     */
    protected function getAttachmentService() {
        return $this->container->get('issue_attachment_service');
    }

    /**
     * @param $issue
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function checkUserIsAuthorized(Issue $issue)
    {
        if (!is_object($this->getUser())) throw new HttpException(403);
        if (!is_object($issue->getCustomer())) throw new HttpException(403);

        if ($issue->getCustomer()->getEmail() == $this->getUser()->getEmail()) return;
        $service = $this->container->get('user_service');
        if ( $service->isCollegues($issue->getCustomer()->getEmail(), $this->getUser()->getEmail()) ) return;
        
        throw new HttpException(403);
    }
}