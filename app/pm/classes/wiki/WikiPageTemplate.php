<?php

include "WikiPageTemplateIterator.php";
include "WikiPageTemplateRegistry.php";

class WikiPageTemplate extends Metaobject
{
    function __construct()
    {
		parent::__construct('WikiPage', new WikiPageTemplateRegistry($this));

		$this->setSortDefault( new SortOrderedClause() );

		$this->setAttributeType( 'UserField1', 'CHAR' );
		$this->setAttributeCaption( 'UserField1', translate('Шаблон по умолчанию') );
    }
     
    function createIterator()
 	{
 	    return new WikiPageTemplateIterator($this);
 	}

    function getDocumentName() {
        return '';
    }

 	function getPageName()
 	{
		return parent::getPageName();
 	}

	function getPageNameViewMode( $objectid ) 
	{
		return $this->getPage().'&object='.$objectid;
	}
 	
	function getDefaultIt() 
	{
		return $this->getByRefArray( array( 
			"IFNULL(UserField1, 'N')" => "Y",
			'ReferenceName' => $this->getReferenceName() 
		));
	}

 	function getDefaultAttributeValue( $name ) 
	{
		switch( $name )
		{
		    case 'ReferenceName':
		        
		        return $this->getReferenceName();
		    
			case 'OrderNum':
				
				$it = $this->getFirst(1, array(new SortRevOrderedClause()) );
				
				return $it->get('OrderNum') + 10;
				
			case 'Project':
				
				return getSession()->getProjectIt()->getId();

			case 'Author':
				
			    return getSession()->getParticipantIt()->getId();
			    
			case 'IsTemplate': return 1;
		}
		
		return parent::getDefaultAttributeValue($name);
	}
}