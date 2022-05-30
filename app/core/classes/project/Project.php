<?php

include "ProjectIterator.php";
include "predicates/ProjectCurrentPredicate.php";
include "predicates/ProjectFilterPredicate.php";
include "predicates/ProjectLinkedPredicate.php";
include "predicates/ProjectParticipatePredicate.php";
include "predicates/ProjectRolePredicate.php";
include "predicates/ProjectStatePredicate.php";
include "predicates/ProjectUserParticipatesPredicate.php";
include "predicates/ProjectVpdPredicate.php";
include "predicates/ProjectNoGroupsPredicate.php";
include "predicates/ProjectAccessiblePredicate.php";
include "persisters/ProjectVPDPersister.php";
include "persisters/ProjectLeadsPersister.php";
include "persisters/ProjectLinksPersister.php";
include_once "persisters/ProjectLinkedPersister.php";
include "validators/ModelValidatorProjectCodeName.php";
include "validators/ModelValidatorProjectIntegration.php";
include "sorts/SortProjectImportanceClause.php";
include "sorts/SortImportanceClause.php";
include "sorts/SortProjectSelfFirstClause.php";
include "sorts/SortProjectCaptionClause.php";

class Project extends Metaobject 
{
 	function __construct( ObjectRegistrySQL $registry = null )
 	{
		parent::__construct('pm_Project', $registry);
        $this->setAttributeType( 'CodeName', 'VARCHAR' );
        $this->addAttribute( 'LinkedProject', 'REF_pm_ProjectId', translate('Связанные проекты'), false );
        $this->addPersister( new ProjectLinkedPersister() );
		$this->setSortDefault(
			array (
				new SortImportanceClause('Importance'),
				new SortAttributeClause('Caption')
			)
		);
 	}

 	function createIterator() {
		return new ProjectIterator( $this );
	}

	function getVpdValue() {
        return '';
    }

    function getValidators() {
        return array(
            new ModelValidatorProjectCodeName()
        );
    }

    function IsDeletedCascade( $object )
	{
	    switch ( $object->getEntityRefName() )
	    {
	        case 'pm_ProjectLink':
	        case 'pm_Methodology':
            case 'co_CompanyProject':
	            return true;
	            
	        default:
	            return false;
	    }
	}
	
	function getDefaultAttributeValue( $attr )
	{
		switch( $attr )
		{
		    case 'Caption':
		    	return translate('Проект').' '.($this->getRegistry()->Count() + 1);
		    case 'OrderNum':
		    	return 10;
		    case 'Importance':
		    	return 3;
            case 'StartDate':
                return date('Y-m-d');
		    default:
		    	return parent::getDefaultAttributeValue( $attr );
		}
	}
	
	function add_parms( $parms )
	{
		$id = parent::add_parms( $parms );

		if ( $id > 0 ) {
		    DAL::Instance()->Query(" UPDATE pm_Project SET VPD = '".ModelProjectOriginationService::getOrigin($id)."' WHERE pm_ProjectId = ".$id);
        }

		return $id;
	}

	function getDisplayName()
    {
        return text('project.name');
    }
}
