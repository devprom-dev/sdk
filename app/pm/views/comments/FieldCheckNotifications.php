<?php

class FieldCheckNotifications extends FieldCheck
{
    private $emails = array();
    private $private = false;

    function __construct() {
        parent::__construct('');
    }

    public function setEmails( $emails ) {
        $this->emails = $emails;
    }

    public function setPrivate( $value ) {
        $this->private = $value;
    }

 	function draw( $view = null )
 	{
        $this->setCheckName(str_replace('%1', '<b>'.join('</b>, <b>', $this->emails).'</b>', text(2318)));
        $this->setValue('Y');

        if ( count($this->emails) > 0 ) {
            if ( $this->private ) {
                echo '<input type="hidden" name="IsPrivate" value="Y">';
            }
            else {
                echo '<div class="alert alert-danger alert-comment">';
                    parent::draw($view);
                echo '</div>';
            }
        }
 	}
}
