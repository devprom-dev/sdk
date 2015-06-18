<?php

namespace Devprom\ServiceDeskBundle\Controller;

use Devprom\ServiceDeskBundle\Entity\Issue;
use Devprom\ServiceDeskBundle\Entity\IssueAttachment;
use Devprom\ServiceDeskBundle\Form\Type\AttachmentFormType;
use Devprom\ServiceDeskBundle\Service\AttachmentService;
use Devprom\ServiceDeskBundle\Service\IssueService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @Route("/attachment/{attachmentId}", name="attachment_download", requirements={"attachmentId" = "\d+"})
     * @Method("GET")
     */
    public function downloadAction($attachmentId)
    {
        $attachment = $this->getAttachmentService()->getAttachmentById($attachmentId);

        $issue = $attachment->getIssue();
        $this->checkUserIsAuthorized($issue);

        $response = new BinaryFileResponse($attachment->getFilePath());
        $filename = mb_convert_encoding($attachment->getOriginalFilename(), 'UTF-8', APP_ENCODING);
        $response->headers->set('Content-Disposition', 'attachment; filename=' . rawurlencode($filename));
        $response->headers->set('Content-Type', $attachment->getContentType());

        return $response;
    }

    /**
     * @Route("/attachment/{issueId}", name="attachment_upload")
     * @Method("POST")
     * @Template()
     */
    public function uploadAction($issueId)
    {
        $attachment = new IssueAttachment();

        $form = $this->createForm(new AttachmentFormType(), $attachment);
        $form->bind($this->getRequest());

        $issue = $this->getIssueService()->getIssueById($issueId);

        $this->checkUserIsAuthorized($issue);

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
     * @Route("/attachment/{issueId}/new", name="attachment", requirements={"issueId" = "\d+"})
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
     * @Route("/attachment/{attachmentId}/delete", name="attachment_delete")
     * @Method("GET") //todo: change to DELETE as it should be (would require ajax call)
     * @Template()
     */
    public function deleteAction($attachmentId) {

        $attachment = $this->getAttachmentService()->getAttachmentById($attachmentId);

        $issue = $attachment->getIssue();
        $this->checkUserIsAuthorized($issue);

        $this->getAttachmentService()->delete($attachment);

        return $this->redirect($this->generateUrl('issue_show', array('id' => $issue->getId())));
    }


    /**
     * @param $issue
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function checkUserIsAuthorized(Issue $issue)
    {
    	$issue = $this->getIssueService()->getIssueById($issue->getId());

        if ($issue->getAuthorEmail() == $this->getUser()->getEmail()) return;
        
        $service = $this->container->get('user_service');
        if ( $service->isCollegues($issue->getAuthorEmail(), $this->getUser()->getEmail()) ) return;

        throw new HttpException(403);
    }

    /**
     * @return AttachmentService
     */
    protected function getAttachmentService() {
        return $this->container->get('attachment_service');
    }

    /**
     * @return IssueService
     */
    protected function getIssueService() {
        return $this->container->get('issue_service');
    }
}