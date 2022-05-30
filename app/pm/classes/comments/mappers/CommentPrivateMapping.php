<?php

class CommentPrivateMapping
{
    public function map( Metaobject $object, &$values )
    {
        foreach( array('Notification', 'TransitionNotification') as $field ) {
            $notificationSpecified = array_key_exists($field, $values) || array_key_exists($field.'OnForm', $values);
            if ( $notificationSpecified && in_array($values[$field], array('N','')) ) {
                $values['IsPrivate'] = 'Y';
            }
        }
    }
}