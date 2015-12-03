<?php

namespace Devprom\ServiceDeskBundle\Mailer;
use Devprom\ServiceDeskBundle\Entity\Issue;
use FOS\UserBundle\Mailer\TwigSwiftMailer;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class Mailer extends TwigSwiftMailer {

    public function sendRegistrationEmailMessage(UserInterface $user, $plainPassword)
    {
        $template = $this->parameters['template']['registration'];
        $context = array(
            'login' => $user->getEmail(),
            'password' => $plainPassword
        );

        $this->sendMessage($template, $context, $this->getFromAddress(), $user->getEmail());
    }

    public function sendIssueCreatedMessage(Issue $issue, $toEmail, $language = 'ru') {
        $template = 'DevpromServiceDeskBundle:Email:issue_created.html.twig';
        $context = array(
            'issue' => $issue,
            'language' => $language
        );

        $this->sendMessage($template, $context, $this->getFromAddress($issue->getVpd()), $toEmail);
    }

    public function sendIssueUpdatedMessage(Issue $issue, $comment, $changes, $toEmail, $language = 'ru') {
        $template = 'DevpromServiceDeskBundle:Email:issue_updated.html.twig';
        $context = array(
            'issue' => $issue,
            'changes' => $changes,
            'language' => $language,
            'comment' => $comment
        );

        $this->sendMessage($template, $context, $this->getFromAddress($issue->getVpd()), $toEmail);
    }

    public function sendIssueCommentedMessage(Issue $issue, $comment, $toEmail, $language = 'ru') {
        $template = 'DevpromServiceDeskBundle:Email:issue_commented.html.twig';
        $context = array(
            'issue' => $issue,
            'comment' => $comment,
            'language' => $language
        );

        $this->sendMessage($template, $context, $this->getFromAddress($issue->getVpd()), $toEmail);
    }

    public function sendIssueResolvedMessage(Issue $issue, $comment, $toEmail, $language = 'ru', $version = '') {
        $template = 'DevpromServiceDeskBundle:Email:issue_resolved.html.twig';
        $context = array(
            'issue' => $issue,
            'comment' => $comment,
            'language' => $language,
        	'version' => $version
        );

        $this->sendMessage($template, $context, $this->getFromAddress($issue->getVpd()), $toEmail);
    }

    /**
     * @return array
     */
    public function getFromAddress( $vpd = '' )
    {
        $supportEmail = $this->parameters['from_email']['default']['address'];

        $emails = $this->getEntityManager()
            ->getConnection()
            ->query("SELECT p.VPD vpd, IF(rm.SenderAddress IS NOT NULL, rm.SenderAddress, IF(rm.EmailAddress IS NOT NULL, IF(rm.EmailAddress NOT LIKE '%%@%%', CONCAT(rm.EmailAddress, '@', rm.HostAddress), rm.EmailAddress), ' ')) email
                       FROM co_RemoteMailbox rm, pm_Project p WHERE p.pm_ProjectId = rm.Project;")
            ->fetchAll();

        if ( count($emails) > 0 ) {
            $supportEmail = $emails[0]['email'];
        }

        foreach( $emails as $email ) {
            if ( $email['vpd'] == $vpd ) {
                $supportEmail = $email['email'];
                break;
            }
        }

        return array(
            $supportEmail => $this->parameters['from_email']['default']['sender_name']);
    }

    protected function getEntityManager()
    {
        return $this->parameters['em'];
    }

    /**
     * @param string $templateName
     * @param array  $context
     * @param string $fromEmail
     * @param string $toEmail
     */
    protected function sendMessage($templateName, $context, $fromEmail, $toEmail)
    {
        $context = $this->twig->mergeGlobals($context);
        $template = $this->twig->loadTemplate($templateName);

        $mail = new \HtmlMailbox;
        $mail->appendAddress($toEmail);
        $mail->setBody($template->renderBlock('body_html', $context));
        $mail->setSubject($template->renderBlock('subject', $context));
        $mail->setFrom($this->normalizeEmailAddress($fromEmail), false);
        $mail->send();
   }

    /**
     * @param $supportEmail
     * @return string
     */
    protected function normalizeEmailAddress($supportEmail)
    {
        if (!$supportEmail) {
            return " ";
        } else if (preg_match("/.+<(.+)>/", html_entity_decode($supportEmail), $matches)) {
            return $matches[1];
        }

        return $supportEmail;
    }
}