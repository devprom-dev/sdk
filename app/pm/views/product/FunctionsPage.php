<?php

include_once SERVER_ROOT_PATH."pm/classes/product/FeatureModelExtendedBuilder.php";

include "FunctionForm.php";
include "FunctionSearchIssueForm.php";
include "FunctionTable.php";
include "PageSettingFeatureBuilder.php";

class FunctionsPage extends PMPage
{
 	function FunctionsPage()
 	{
 	    getSession()->addBuilder( new PageSettingFeatureBuilder() );
 		parent::__construct();
 		
 		if ( $this->needDisplayForm() )
 		{
 		    if ( $this->getFormRef() instanceof FunctionForm ) {
                $this->addInfoSection(new PageSectionAttributes($this->getFormRef()->getObject(), 'hierarchy', translate('Декомпозиция')));
                $this->addInfoSection(new PageSectionAttributes($this->getFormRef()->getObject(), 'trace', translate('Трассировки')));

                $object_it = $this->getObjectIt();
                if( is_object($object_it) && $object_it->getId() > 0 ) {
                    $this->addInfoSection( new PageSectionComments($object_it) );
                    $this->addInfoSection( new PMLastChangesSection ( $object_it ) );
                    $this->addInfoSection( new NetworkSection($object_it) );
                }
            }
 		}
 		else {
            $this->addInfoSection(new DetailsInfoSection());
        }
 	}
 	
 	function getObject()
 	{
 		$object = getFactory()->getObject('Feature');

		$builder = new FeatureModelExtendedBuilder();
		$builder->build($object);

		return $object;
 	}
 	
 	function getTable() 
 	{
		return new FunctionTable( $this->getObject() );
 	}
 	
 	function getForm() 
 	{
 	    if ( $_REQUEST['BindIssue'] != '' ) {
            return new FunctionSearchIssueForm( $this->getObject() );
        }
        else {
            return new FunctionForm( $this->getObject() );
        }
 	}
}