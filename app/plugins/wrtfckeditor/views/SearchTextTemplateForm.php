<?php

class SearchTextTemplateForm extends PMPageForm
{
    function extendModel()
    {
        $object = $this->getObject();
        foreach( $object->getAttributes() as $attribute => $info ) {
            $object->setAttributeVisible($attribute, false);
            $object->setAttributeRequired($attribute, false);
        }

        $module_it = getFactory()->getObject('Module')->getExact('dicts-texttemplate');

        $object->addAttribute('Template', 'REF_'.$this->getTemplateClassName().'Id', translate('Шаблон'), true, false,
            str_replace('%1', $module_it->getUrl(),
                str_replace('%2', $module_it->getDisplayName(), text('wrtfckeditor9')))
        );
    }

    function getTemplateClassName()
    {
        $templateMap = array(
            'Requirement' => 'RequirementTextTemplate',
            'TestScenario' => 'TestScenarioTextTemplate',
            'ProjectPage' => 'ProjectPageTextTemplate',
            'HelpPage' => 'HelpPageTextTemplate',
            'Request' => 'RequestTextTemplate',
            'Issue' => 'IssueTextTemplate',
            'Increment' => 'IncrementTextTemplate',
            'Comment' => 'CommentTextTemplate'
        );
        $className = getFactory()->getClass($templateMap[$_REQUEST['objectclass']]);
        if ( !class_exists($className) ) $className = 'TextTemplate';

        return $className;
    }

    function createFieldObject($attr)
    {
        if ( !$this->getObject()->IsReference($attr) ) return null;
        return new FieldAutoCompleteObject($this->getObject()->getAttributeObject($attr));
    }

    function process()
    {
        if ($this->getAction() != 'add') return false;

        $this->extendModel();

        $value = $this->getFieldValue('Template');
        if ( $value != '' ) {
            $value_it = $this->getObject()->getAttributeObject('Template')->getExact($value);
            if ( $value_it->getId() != '' ) {
                echo json_encode(
                    array(
                        'text' => $value_it->getHtmlDecoded('Content')
                    )
                );
                return true;
            }
        }
        echo json_encode(
            array(
                'text' => ''
            )
        );
        return true;
    }

    function getRenderParms()
    {
        return array_merge(
            parent::getRenderParms(),
            array (
                'iframe' => true
            )
        );
    }
}