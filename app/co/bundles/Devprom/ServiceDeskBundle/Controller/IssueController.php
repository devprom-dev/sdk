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
    	if ( !is_object($this->getUser()) ) throw $this->createNotFoundException('Authorization is required.');
    	
    	$vpds = $this->getProjectVpds();
        
    	$issue = new Issue();
        $form = $this->createForm(new IssueFormType($this->get('doctrine.orm.entity_manager'), $vpds, $this->getUser(), true), $issue);
        $form->bind($request);

        if ($form->isValid()) {
            $this->getIssueService()->saveIssue($issue, $this->getUser());
            if ($issue->getNewAttachment()) {
                $this->getAttachmentService()->save($issue->getNewAttachment(), $issue);
            }
            $this->container->get('session')->getFlashBag()->set('issue_created', 'issue.add.flash');

            return $this->redirect($this->generateUrl('issue_show', array('id' => $issue->getId())));
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
    	if ( !is_object($this->getUser()) ) throw $this->createNotFoundException('Authorization is required.');
    	
        $issue = $this->getIssueService()->getBlankIssue($this->getProjectVpds());
        $vpds = $this->getProjectVpds();
        if ( $request->get('project') != '' ) {
            $vpds = array_intersect($vpds, array($request->get('project')));
        }
        $form = $this->createForm(new IssueFormType($this->get('doctrine.orm.entity_manager'), $vpds, $this->getUser(), true), $issue);

        if ( count($vpds) > 1 ) {
            try {
                $numberOfProjects = count($form->get('product')->getConfig()->getOption('choice_list')->getValues());
                $maxProducts = $this->container->getParameter('max_products_in_combo');
                if ( $maxProducts != '' && $numberOfProjects > $maxProducts ) {
                    return $this->redirect($this->generateUrl('select_product'));
                }
            }
            catch( \Exception $e ) {
            }
        }

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
    	if ( !is_object($this->getUser()) ) throw $this->createNotFoundException('Authorization is required.');
    	
        $issue = $this->getIssueService()->getIssueById($id);
        if (!$issue) {
            throw $this->createNotFoundException('Unable to find Issue entity.');
        }

        $this->checkUserIsAuthorized($issue);
        $commentForm = $this->getCommentForm(new IssueComment());

        $this->getIssueService()->clearNotifications($issue, $this->getUser());

        return array(
            'issue' => $issue,
            'comment_form' => $commentForm->createView(),
        );
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
    	if ( !is_object($this->getUser()) ) throw $this->createNotFoundException('Authorization is required.');
    	
        $issue = $this->getIssueService()->getIssueById($id);
        if (!$issue) {
            throw $this->createNotFoundException('Unable to find Issue entity.');
        }

        $this->checkUserIsAuthorized($issue);
        $issue->setCaption(TextUtil::unescapeHtml($issue->getCaption()));

        // убираем экранирвание html разметки от Девпрома
        $descr = TextUtil::unescapeHtml($issue->getDescription());
        // убираем всю html разметку от Девпрома
        $descr = strip_tags($descr);
        // убираем экранирование html разметки из Сервисдеска
        $descr = TextUtil::unescapeHtml($descr);
        $issue->setDescription($descr);

        $editForm = $this->createForm(new IssueFormType($this->get('doctrine.orm.entity_manager'), $this->getProjectVpds(), $this->getUser(), false), $issue);

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
    	if ( !is_object($this->getUser()) ) throw $this->createNotFoundException('Authorization is required.');
    	
        $issue = $this->getIssueService()->getIssueById($id);

        if (!$issue) {
            throw $this->createNotFoundException('Unable to find Issue entity.');
        }

        $this->checkUserIsAuthorized($issue);

        $editForm = $this->createForm(new IssueFormType($this->get('doctrine.orm.entity_manager'), $this->getProjectVpds(), $this->getUser()), $issue);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $this->getIssueService()->saveIssue($issue, $this->getUser());

            return $this->redirect($this->generateUrl('issue_show', array('id' => $id)));
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
    	if ( !is_object($this->getUser()) ) throw $this->createNotFoundException('Authorization is required.');
    	
        $issue = $this->getIssueService()->getIssueById($issueId);
        if (!$issue) {
            throw $this->createNotFoundException('Unable to find Issue entity.');
        }

        $this->checkUserIsAuthorized($issue);

        $issueComment = new IssueComment();
        $commentForm = $this->getCommentForm($issueComment);
        $commentForm->bind($request);

        if ($commentForm->isValid()) {
            $this->getIssueService()->saveComment($issueComment, $issue, $this->getUser());
            return $this->redirect($this->generateUrl('issue_show', array('id' => $issueId)));
        }

        return $this->render("DevpromServiceDeskBundle:Issue:show.html.twig", array(
            'issue' => $issue,
            'comment_form' => $commentForm->createView(),
        ));
    }

    /**
     * Lists all Issue entities.
     *
     * @Route("/issues/{filter}/{sortColumn}/{sortDirection}", name="issue_list", requirements={"filter" = "my|company"}, defaults={"filter" = "my", "sortColumn" = "issue.createdAt", "sortDirection" = "desc"})
     * @Method("GET")
     * @Template()
     */
    public function indexAction($filter, $sortColumn, $sortDirection)
    {
    	if ( !is_object($this->getUser()) ) throw $this->createNotFoundException('Authorization is required.');

        $company = $this->getUser()->getCompany();
    	if ( $filter == 'my' || (is_object($company) && $company->getSeeCompanyIssues() != 'Y') )
    	{
	        $issues = $this->getIssueService()->getIssuesByAuthor(
	            $this->getUser()->getEmail(), $sortColumn, $sortDirection
	        );
    	}
    	else
    	{
	        $issues = $this->getIssueService()->getIssuesByCompany(
	            $this->getUser()->getEmail(), $sortColumn, $sortDirection
	        );
    	}

        return array(
            'issues' => $issues,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
        	'issuesFilter' => $filter
        );
    }

    /**
     * Displays list of Products.
     *
     * @Route("/products", name="select_product")
     * @Method("GET")
     * @Template()
     */
    public function productAction()
    {
        if ( !is_object($this->getUser()) ) throw $this->createNotFoundException('Authorization is required.');

        $issue = $this->getIssueService()->getBlankIssue($this->getProjectVpds());
        $form = $this->createForm(new IssueFormType($this->get('doctrine.orm.entity_manager'), $this->getProjectVpds(), $this->getUser(), true), $issue);

        $products = $this->get('doctrine.orm.entity_manager')->getRepository('DevpromServiceDeskBundle:Product')
            ->findById($form->get('product')->getConfig()->getOption('choice_list')->getValues());
        foreach($products as $product_ref) {
            $vpds[] = $product_ref->getVpd();
        }
        $projects = $this->get('doctrine.orm.entity_manager')->getRepository('DevpromServiceDeskBundle:Project')
            ->findBy(array('vpd' => $vpds));
        return array(
            'projects' => $projects
        );
    }

    protected function getProjectVpds()
    {
    	$customer_vpds = array();
    	if ( $this->getUser()->getCompany() ) {
	    	foreach($this->getUser()->getCompany()->getProjects() as $project_ref) {
	    		$customer_vpds[] = $project_ref->getProject()->getVpd();
	    	}
    	}
        $intersection = array_intersect($customer_vpds, $this->container->getParameter('supportProjectVpds'));
        if ( count($intersection) > 0 ) return $intersection;

    	return $this->container->getParameter('supportProjectVpds');
    }
    
    /**
     * @param $issueComment
     * @return \Symfony\Component\Form\Form
     */
    protected function getCommentForm($issueComment)
    {
        return $this->createFormBuilder($issueComment)
            ->add("text", "textarea", array(
                'label' => false
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