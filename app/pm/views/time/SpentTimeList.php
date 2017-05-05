<?php

class SpentTimeList extends PMPageList
{
    function extendModel()
    {
        parent::extendModel();

        $object = $this->getObject();
        foreach( array('LeftWork','Completed') as $attribute ) {
            $object->removeAttribute($attribute);
        }

        if ( getSession()->getParticipantIt()->IsLead() ) {
            $object->setAttributeVisible('Participant', true);
            $object->setAttributeVisible('Issue', true);
        }
        else {
            $object->addAttributeGroup('Participant', 'system');
        }
        $object->addAttributeGroup('Completed', 'system');

        $object->setAttributeOrderNum('Participant', 5);
        $object->setAttributeOrderNum('ReportDate', 6);
        $object->setAttributeOrderNum('Capacity', 7);
        $object->setAttributeOrderNum('LeftWork', 8);

        foreach( array_keys($object->getAttributes()) as $attribute ) {
            $object->addAttributeGroup($attribute, 'nonbulk');
        }

        foreach( array('Capacity') as $attribute ) {
            $object->addAttributeGroup($attribute, 'hours');
        }
    }

    function getGroupDefault() {
        return getSession()->getParticipantIt()->IsLead() ? 'Participant' : 'ReportDate';
    }
}
 