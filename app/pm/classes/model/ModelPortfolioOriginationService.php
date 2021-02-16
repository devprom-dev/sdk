<?php

include_once SERVER_ROOT_PATH."core/classes/model/ModelProjectOriginationService.php";

class ModelPortfolioOriginationService extends ModelProjectOriginationService
{
    protected function buildSelfOrigin( $object )
    {
        $value = ModelEntityOriginationService::buildSelfOrigin( $object );
        if ( $value != DUMMY_PROJECT_VPD ) return $value;

        return $this->getSession()->getProjectIt()->get('VPD');
    }

	public function buildAvailableOrigins( $object )
	{
	 	switch ( $object->getClassName() )
 	    {
 	        case 'pm_Methodology':
 	        case 'co_UserGroup':
 	        case 'co_ProjectGroup':
            case 'cms_User':
                return array( '-' );

 	        case 'pm_CustomReport':
 	        case 'pm_UserSetting':
            case 'pm_WorkspaceMenu':
            case 'pm_WorkspaceMenuItem':
 	        case 'cms_PluginModule':
 	            return array( $this->getSession()->getProjectIt()->get('VPD') );

            case 'pm_Project':
                return $this->getSession()->getLinkedIt()->fieldToArray('VPD');

            case 'pm_State':
            case 'pm_Participant':
            case 'pm_CustomAttribute':
            case 'WikiPageType':
                $vpds = array();
                $linked_it = $this->getSession()->getLinkedIt();
                while ( !$linked_it->end() ) {
                    if ( $linked_it->get('IsClosed') != 'Y' ) {
                        $vpds[] = $linked_it->get('VPD');
                    }
                    $linked_it->moveNext();
                }
                return $vpds;

            case 'cms_Resource':
                $linkedIt = $this->getSession()->getLinkedIt();
                if ( count($linkedIt->fieldToArray('Tools')) > 1 ) {
                    return array('-');
                }
                else {
                    return array($linkedIt->get('VPD'));
                }

 	        default:
 	            if ( $object instanceof SharedObjectSet ) return array('-');
                if ( $object instanceof Portfolio ) return array('-');
                if ( $object instanceof Program ) return array('-');
 	            if ( !$this->getSharedSet()->hasObject($object) ) return array('-');
 	            
 	            $vpds = array();
 	            
 	            $linked_it = $this->getSession()->getLinkedIt();
 	            while ( !$linked_it->end() ) {
 	                if ( $linked_it->get('IsClosed') != 'Y' && $this->getSharedSet()->sharedInProject( $object, $linked_it ) ) {
 	                    $vpds[] = $linked_it->get('VPD');
 	                }
 	                $linked_it->moveNext();
 	            }
 	            return count($vpds) > 0 ? $vpds : array('-');
 	    }		
	}		
}