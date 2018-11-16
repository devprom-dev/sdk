<?php
include_once SERVER_ROOT_PATH . "pm/classes/model/validators/ModelNotificationValidator.php";

class FieldCheckNotifications extends FieldCheck
{
    private $anchor_it = null;
    private $emails = array();
    private $transitionIt = null;

    function __construct( $transitionIt = null )
    {
        $this->transitionIt = $transitionIt;
        parent::__construct('');
    }

    public function setAnchor( $anchor_it )
    {
        $this->anchor_it = $anchor_it;
        $items = getFactory()->getEventsManager()->getNotificators('ServicedeskCommentEmailNotificator');
        if ( count($items) > 0 && $this->anchor_it->object instanceof Request )
        {
            $notificator = array_shift($items);
            $this->emails = $notificator->getEmails($this->anchor_it);
        }
    }

 	function draw( $view = null )
 	{
        $this->setCheckName(str_replace('%1', '<b>'.join('</b>, <b>', $this->emails).'</b>', text(2318)));
        $this->setValue('Y');

        $skipNotification = false;
        if ( is_object($this->transitionIt) && $this->transitionIt->getId() != '' ) {
            if ( $this->transitionIt->getRef('TargetState')->get('SkipEmailNotification') == 'Y' ) {
                $skipNotification = true;
            }
        }

        if ( count($this->emails) > 0 )
        {
            if ( $skipNotification ) {
                echo '<input type="hidden" name="IsPrivate" value="Y">';
            }
            else {
                echo '<div class="alert alert-danger alert-comment">';
                    parent::draw($view);
                echo '</div>';
            }
        }
 	}

 	function getValidator()
    {
        return new ModelNotificationValidator();
    }
}
