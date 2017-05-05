<?php

class FieldCheckNotifications extends FieldCheck
{
    private $anchor_it = null;
    private $emails = array();

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

        if ( count($this->emails) > 0 )
        {
            echo '<div class="alert alert-hint alert-comment">';
                parent::draw($view);
            echo '</div>';
        }
 	}
}
