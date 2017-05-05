<?php

class FunctionSearchIssueForm extends PMPageForm
{
    function extendModel()
    {
        parent::extendModel();
        foreach( array_keys($this->getObject()->getAttributes()) as $attribute ) {
            $this->getObject()->setAttributeVisible($attribute, false);
            $this->getObject()->setAttributeRequired($attribute, false);
        }
        $this->getObject()->addAttribute('Issue', 'REF_RequestId', translate('Пожелание'), true);
        $this->getObject()->addAttribute('BindIssue', 'VARCHAR', '', false);
        $this->getObject()->setAttributeDefault('BindIssue', 'true');
    }

    function process()
	{
		if ( $this->getAction() != 'modify' ) return parent::process();

		$issueIt = getFactory()->getObject('Request')->getExact($_REQUEST['Issue']);
		if ( $issueIt->getId() != '' ) {
            $issueIt->object->getRegistry()->Store(
                $issueIt,
                array(
                    'Function' => $this->getObjectIt()->getId()
                )
            );
        }
        $this->redirectOnModified($this->object_it, $this->getRedirectUrl());
	}
}