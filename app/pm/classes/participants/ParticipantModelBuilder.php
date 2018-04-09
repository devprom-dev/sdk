<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class ParticipantModelBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
    	if ( !$object instanceof Participant) return;

    	$moduleIt = getFactory()->getObject('Module')->getExact('whatsnew');
    	$object->setAttributeDescription('NotificationTrackingType',
            str_replace('%1',
                $moduleIt->getUrl(), str_replace('%2',
                    $moduleIt->getDisplayName(), text(2467))
            )
        );
   }
}