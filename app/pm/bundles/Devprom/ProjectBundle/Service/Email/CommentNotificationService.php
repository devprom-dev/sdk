<?php
namespace Devprom\ProjectBundle\Service\Email;

class CommentNotificationService
{
    private $anchor_it = null;
    private $emails = array();

    public function __construct( $anchorIt ) {
        $this->anchor_it = $anchorIt;

        if ( is_object($this->anchor_it) ) {
            $items = getFactory()->getEventsManager()->getNotificators('ServicedeskCommentEmailNotificator');
            if ( count($items) > 0 && $this->anchor_it->object instanceof \Request )
            {
                $notificator = array_shift($items);
                $this->emails = $notificator->getEmails($this->anchor_it);
            }
        }
    }

    public function getEmails() {
        return $this->emails;
    }

    public function getPrivate( $transitionIt ) {
        if ( $transitionIt->getId() != '' ) {
            if ( $transitionIt->getRef('TargetState')->get('SkipEmailNotification') == 'Y' ) {
                return true;
            }
        }
        return false;
    }
}