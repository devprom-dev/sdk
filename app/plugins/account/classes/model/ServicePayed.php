<?php

include "persisters/ServicePayedPersister.php";

class ServicePayed extends Metaobject
{
    function __construct()
    { 
        parent::__construct('co_Service');
        
        $visible_attrs = array (
        		'Caption',
        		'Description',
        		'PayedTill'
        );
        foreach( $this->getAttributes() as $attribute => $data )
        {
        	$this->setAttributeVisible($attribute, in_array($attribute, $visible_attrs));
        	$this->setAttributeRequired($attribute, false);
        }
        $this->addAttribute('IID', 'VARCHAR', 'IID', true, false, '', 15);
        $this->addAttribute('PayedTill', 'DATE', 'Оплачено до', true, true, '', 18);
        $this->addAttribute('Users', 'INTEGER', 'Пользователей', true, true, '', 20);
        $this->addAttribute('License', 'VARCHAR', 'Лицензия', true, true, '', 22);
        $this->addAttribute('LTV', 'VARCHAR', 'Lifetime', true, false, '', 23);
        $this->addAttribute('IsActive', 'VARCHAR', 'Active', true, false, '', 24);

        $this->removeAttribute('Category');
        $this->removeAttribute('Author');
        $this->removeAttribute('Team');
        
        $this->addPersister(new ServicePayedPersister());
    }
    
    function add_parms( $parms )
    {
    	if ( $parms['IID'] != '' ) $parms['VPD'] = $parms['IID'];
    	return parent::add_parms( $parms );
    }
    
    function modify_parms( $id, $parms )
    {
    	if ( $parms['IID'] != '' ) $parms['VPD'] = $parms['IID'];
    	return parent::modify_parms( $id, $parms );
    }
}