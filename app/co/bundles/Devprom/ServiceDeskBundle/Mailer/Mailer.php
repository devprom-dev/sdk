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

        $this->sendMessage($template, $context, $this->getFromAddress(), $toEmail);
    }

    public function sendIssueUpdatedMessage(Issue $issue, $changes, $toEmail, $language = 'ru') {
        $template = 'DevpromServiceDeskBundle:Email:issue_updated.html.twig';
        $context = array(
            'issue' => $issue,
            'changes' => $changes,
            'language' => $language
        );

        $this->sendMessage($template, $context, $this->getFromAddress(), $toEmail);
    }

    public function sendIssueCommentedMessage(Issue $issue, $comment, $toEmail, $language = 'ru') {
        $template = 'DevpromServiceDeskBundle:Email:issue_commented.html.twig';
        $context = array(
            'issue' => $issue,
            'comment' => $comment,
            'language' => $language
        );

        $this->sendMessage($template, $context, $this->getFromAddress(), $toEmail);
    }

    public function sendIssueResolvedMessage(Issue $issue, $comment, $toEmail, $language = 'ru', $version = '') {
        $template = 'DevpromServiceDeskBundle:Email:issue_resolved.html.twig';
        $context = array(
            'issue' => $issue,
            'comment' => $comment,
            'language' => $language,
        	'version' => $version
        );

        $this->sendMessage($template, $context, $this->getFromAddress(), $toEmail);
    }

    /**
     * @return array
     */
    public function getFromAddress()
    {
        return array(
            $this->parameters['from_email']['default']['address']
            =>
            $this->parameters['from_email']['default']['sender_name']);
    }

}