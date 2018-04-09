<?php

namespace Devprom\ServiceDeskBundle\Controller;

use Devprom\ServiceDeskBundle\Entity\Issue;
use Devprom\ServiceDeskBundle\Entity\IssueAttachment;
use Devprom\ServiceDeskBundle\Form\Type\AttachmentFormType;
use Devprom\ServiceDeskBundle\Service\IssueAttachmentService;
use Devprom\ServiceDeskBundle\Service\IssueService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\HttpException;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class AttachmentController extends Controller
{

    /**
     * @Route("/issue/attachment/{attachmentId}", name="attachment_download", requirements={"attachmentId" = "\d+"})
     * @Method("GET")
     */
    public function downloadIssueAttachmentAction($attachmentId)
    {
        $attachment = $this->getAttachmentService()->getAttachmentById($attachmentId);
        if ( !is_object($attachment) ) {
            $this->redirect($this->generateUrl('issue_list', array()));
        }

        $issue = $attachment->getIssue();
        if ( !$this->checkUserIsAuthorized($issue) ) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_security_login', array(), true));
        }

        $response = new BinaryFileResponse($attachment->getFilePath());
        $response->headers->set(\EnvironmentSettings::getDownloadHeader($attachment->getOriginalFilename()));
        $response->headers->set('Content-Type', $attachment->getContentType());

        return $response;
    }

    /**
     * @Route("/comment/attachment/{attachmentId}", name="comment_attachment_download", requirements={"attachmentId" = "\d+"})
     * @Method("GET")
     */
    public function downloadCommentIssueAttachmentAction($attachmentId)
    {
        $attachment = $this->container->get('comment_attachment_service')->getAttachmentById($attachmentId);

        $issue = $attachment->getComment()->getIssue();
        if ( !$this->checkUserIsAuthorized($issue) ) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_security_login', array(), true));
        }

        $response = new BinaryFileResponse($attachment->getFilePath());
        $response->headers->set(\EnvironmentSettings::getDownloadHeader($attachment->getOriginalFilename()));
        $response->headers->set('Content-Type', $attachment->getContentType());

        return $response;
    }

    /**
     * @Route("/issue/attachment/{issueId}", name="attachment_upload")
     * @Method("POST")
     * @Template()
     */
    public function uploadAction(Request $request, $issueId)
    {
        $attachment = new IssueAttachment();

        $form = $this->createForm(new AttachmentFormType(), $attachment);
        $form->bind($request);

        $issue = $this->getIssueService()->getIssueById($issueId);

        if ( !$this->checkUserIsAuthorized($issue) ) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_security_login', array(), true));
        }

        if ($form->isValid()) {

            $this->getAttachmentService()->save($attachment, $issue);

            return $this->redirect($this->generateUrl('issue_show', array('id' => $issueId)));
        }

        return array(
            'issue' => $issue,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/issue/attachment/{issueId}/new", name="attachment", requirements={"issueId" = "\d+"})
     * @Method("GET")
     * @template("DevpromServiceDeskBundle:Attachment:upload.html.twig")
     */
    public function showFormAction($issueId) {

        $issue = $this->getIssueService()->getIssueById($issueId);

        $form = $this->createForm(new AttachmentFormType());

        return array(
            'issue' => $issue,
            'form' => $form->createView()
        );
    }


    /**
     * @Route("/issue/attachment/{attachmentId}/delete", name="attachment_delete")
     * @Method("GET") //todo: change to DELETE as it should be (would require ajax call)
     * @Template()
     */
    public function deleteAction($attachmentId) {

        $attachment = $this->getAttachmentService()->getAttachmentById($attachmentId);
        if ( !is_object($attachment) ) {
            $this->redirect($this->generateUrl('issue_list', array()));
        }

        $issue = $attachment->getIssue();
        if ( !$this->checkUserIsAuthorized($issue) ) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_security_login', array(), true));
        }

        $this->getAttachmentService()->delete($attachment);

        return $this->redirect($this->generateUrl('issue_show', array('id' => $issue->getId())));
    }


    /**
     * @param $issue
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function checkUserIsAuthorized(Issue $issue)
    {
        if ( !is_object($this->getUser()) ) return false;
        if ( !is_object($issue->getCustomer()) ) {
            throw new HttpException(403);
        }

    	$issue = $this->getIssueService()->getIssueById($issue->getId());
        if ($issue->getCustomer()->getEmail() == $this->getUser()->getEmail()) return true;

        $service = $this->container->get('user_service');
        if ( $service->isCollegues($issue->getCustomer()->getEmail(), $this->getUser()->getEmail()) ) return true;

        throw new HttpException(403);
    }

    /**
     * @return IssueAttachmentService
     */
    protected function getAttachmentService() {
        return $this->container->get('issue_attachment_service');
    }

    /**
     * @return IssueService
     */
    protected function getIssueService() {
        return $this->container->get('issue_service');
    }
}