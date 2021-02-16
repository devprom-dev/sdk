<?php

class AutoActionList extends PMPageList
{
    function getGroupFields() {
        return array();
    }

    function drawCell($object_it, $attr)
    {
        switch( $attr ) {
            case 'Conditions':
                echo $object_it->getConditionXPath(true);
                break;

            case 'Actions':
                $lines = array();
                foreach(json_decode($object_it->getHtmlDecoded($attr), true) as $attribute => $value ) {
                    if ( $value == '' ) continue;
                    $attributeTitle = $this->getObject()->getAttributeUserName($attribute);
                    if ( in_array('task', $object_it->object->getAttributeGroups($attribute)) ) {
                        $attributeTitle = translate('Задача').'.'.$attributeTitle;
                    }
                    $lines[] = htmlentities($attributeTitle.'='.urldecode($value));
                }
                echo join('<br/>', $lines);
                break;

            default:
                parent::drawCell($object_it, $attr);
        }
    }

    function getColumnFields()
    {
        return array_diff(
            parent::getColumnFields(),
            $this->getObject()->getAttributesByGroup('actions'),
            $this->getObject()->getAttributesByGroup('task')
        );
    }
}
