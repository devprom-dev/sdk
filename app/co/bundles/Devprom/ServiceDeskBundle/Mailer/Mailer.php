<?php

namespace Devprom\ServiceDeskBundle\Mailer;
use Devprom\ServiceDeskBundle\Entity\Issue;
use Devprom\ServiceDeskBundle\Entity\IssueComment;
use FOS\UserBundle\Mailer\TwigSwiftMailer;
use FOS\UserBundle\Model\UserInterface;

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

    public function sendIssueCreatedMessage(Issue $issue, $toEmail, $language = 'ru', $issueLink = '') {
        $template = 'Email/issue_created.html.twig';
        $context = array(
            'issue' => $issue,
            'issueLink' => $issueLink,
            'language' => $language
        );

        $this->sendMessage($template, $context, $this->getFromAddress($issue), $toEmail, $issue->getEmailMessageId());
    }

    public function sendIssueUpdatedMessage(Issue $issue, $comment, $changes, $toEmail, $language = 'ru', $version) {
        $template = 'Email/issue_updated.html.twig';
        $context = array(
            'issue' => $issue,
            'changes' => $changes,
            'language' => $language,
            'comment' => $comment,
            'version' => $version
        );

        $this->sendMessage($template, $context, $this->getFromAddress($issue), $toEmail, $issue->getEmailMessageId());
    }

    public function sendIssueCommentedMessage(Issue $issue, IssueComment $comment, $toEmail, $language = 'ru') {
        $template = 'Email/issue_commented.html.twig';
        $context = array(
            'issue' => $issue,
            'comment' => $comment,
            'language' => $language
        );

        $messageId = $comment->getEmailMessageId();
        if ( $messageId == '' ) {
            $messageId = $issue->getEmailMessageId();
        }
        $this->sendMessage($template, $context, $this->getFromAddress($issue), $toEmail, $messageId);
    }

    /**
     * @return array
     */
    public function getFromAddress( Issue $issue = null )
    {
        if ( $issue ) {
            if ( $issue->getChannelEmail() != '' ) {
                return html_entity_decode($issue->getChannelEmail());
            }
        }
        return '';
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
    protected function sendMessage($templateName, $context, $fromEmail, $toEmail, $messageId = '')
    {
        $context = $this->twig->mergeGlobals($context);
        $template = $this->twig->loadTemplate($templateName);

        $mail = new \HtmlMailbox;
        $mail->appendAddress($toEmail);
        $mail->setBody($template->renderBlock('body_html', $context));
        $mail->setSubject($template->renderBlock('subject', $context));
        if ( mb_strlen($fromEmail) > 1 ) {
            $mail->setFrom($fromEmail);
        }
        $mail->setInReplyMessageId($messageId);
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