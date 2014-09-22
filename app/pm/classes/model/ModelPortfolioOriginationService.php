<?php

include_once SERVER_ROOT_PATH."core/classes/model/ModelProjectOriginationService.php";

class ModelPortfolioOriginationService extends ModelProjectOriginationService
{
	public function buildAvailableOrigins( $object )
	{
	 	switch ( $object->getClassName() )
 	    {
 	        case 'pm_Project':
 	        case 'pm_Methodology':
 	        case 'co_UserGroup':
 	        case 'co_ProjectGroup':
            case 'cms_User':
 	        case 'pm_CustomAttribute':
                
                return array( '-' );

 	        case 'pm_CustomReport':
 	        case 'pm_UserSetting':
 	        case 'cms_PluginModule':
 	            
 	            return array( $this->getSession()->getProjectIt()->get('VPD') );

            case 'cms_Resource':
            case 'pm_State':
            	
                return $this->getSession()->getLinkedIt()->fieldToArray('VPD');
 	            
 	        default:
 
 	            if ( $object instanceof SharedObjectSet ) return array('-');
 	            
 	            if ( !$this->getSharedSet()->hasObject($object) ) return array('-');
 	            
 	            $vpds = array();
 	            
 	            $linked_it = $this->getSession()->getLinkedIt();
 	            
 	            while ( !$linked_it->end() )
 	            {
 	                if ( $this->getSharedSet()->sharedInProject( $object, $linked_it ) )
 	                {
 	                    $vpds[] = $linked_it->get('VPD');
 	                }
 	                
 	                $linked_it->moveNext();
 	            }
 	            
 	            return count($vpds) > 0 ? $vpds : array('-');
 	    }		
	}		
}