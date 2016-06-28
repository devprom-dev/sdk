<?php

include_once SERVER_ROOT_PATH."pm/classes/settings/MethodologyPlanningMode.php";
include "EstimationStrategyDictionary.php";
        
class MethodologyForm extends PMPageForm
{
    function IsNeedButtonNew() {
        return false;
    }
    function IsNeedButtonCopy() {
        return false;
    }
    function IsNeedButtonDelete() {
        return false;
    }
    function IsNeedButtonSave() {
        return true;
    }

    function IsAttributeVisible( $attr_name )
    {
        switch( $attr_name )
        {
            case 'IsTasks':
            case 'IsFixedRelease':
            case 'ReleaseDuration':
            	return true;
        	
            case 'Project':
            case 'IsCrossChecking':
            case 'VerificationTime':
            case 'RequestApproveRequired':
            case 'IsHighTolerance':
            case 'IsDeadlineUsed':
            case 'IsDesign':
            case 'UseEnvironments':
            case 'IsResponsibleForFunctions':
            case 'IsTasksDepend':
            case 'IsResponsibleForIssue':
            case 'IsVersionsUsed':
                return false;

            default:
                return parent::IsAttributeVisible( $attr_name );
        }
    }

    function getFieldDescription( $name )
    {
        switch ( $name )
        {
            case 'IsPlanningUsed':
                return text(63);
            
            case 'IsFixedRelease':

                $estimation = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
                
                return str_replace('%1', trim($estimation->getDimensionText('')), text(1423));
            
            case 'IsResponsibleForFunctions':
                return text(10);
            case 'UseScrums':
                return text(14);
            case 'HasMilestones':
                return text(16);
            case 'RequestEstimationRequired':
                return text(18);
            case 'IsParticipantsTakeTasks':
                return text(19);
            case 'UseFunctionalDecomposition':
                return text(20);
            case 'IsReportsOnActivities':
                return text(410);
            case 'IsReleasesUsed':
                return text(671);
            case 'IsRequestOrderUsed':
                return text(1034);
            case 'TaskEstimationUsed':
                return text(1366);
            case 'IsTasks':
            	return text(1717);
            case 'ReleaseDuration':
            	return text(1740);
        }

        return parent::getFieldDescription( $name );
    }

    function createFieldObject( $attr )
    {
        switch ( $attr )
        {
            case 'RequestEstimationRequired':
            	return new EstimationStrategyDictionary();

            case 'IsReleasesUsed':
            	$field = new FieldDictionary( new MethodologyPlanningMode() );
            	$field->setNullOption(false);
            	return $field;

            case 'IsRequirements':
                $field = new FieldDictionary( new ReqManagementMode() );
                $field->setNullOption(false);
                return $field;

            default:
                return parent::createFieldObject( $attr );
        }
    }

    function getPageTitle()
    {
        return '';
    }
    
	function getBodyTemplate()
	{
	    return "pm/MethodologyFormBody.php";
	}
    
    function getRenderParms()
    {
        return array_merge( parent::getRenderParms(), array (
                'uid_icon' => ''
        ));
    }
    
    function validateInputValues( $id, $action )
    {
        $message = parent::validateInputValues( $id, $action );

        if ( $message != '' ) return $message;

        // convert methodology planning settings into model attributes
        switch ( $_REQUEST['IsReleasesUsed'] )
        {
            case MethodologyPlanningModeRegistry::None:
            	$_REQUEST['IsPlanningUsed'] = 'N';
            	$_REQUEST['HasMilestones'] = 'N';
            	break;
            	
            case MethodologyPlanningModeRegistry::Releases:
            	$_REQUEST['IsPlanningUsed'] = 'N';
            	$_REQUEST['HasMilestones'] = 'Y';
            	break;
            	
            case MethodologyPlanningModeRegistry::Iterations:
            case MethodologyPlanningModeRegistry::IterationsOnly:
            	$_REQUEST['IsPlanningUsed'] = 'Y';
            	$_REQUEST['HasMilestones'] = 'Y';
            	break;
        }
        
        return '';
    }
    
    function drawScripts()
    {
    	parent::drawScripts();
    	
    	?>
    	<script type="text/javascript">
			function toggleIterationProps() {
				var items = ['<?=MethodologyPlanningModeRegistry::Iterations?>','<?=MethodologyPlanningModeRegistry::Releases?>','<?=MethodologyPlanningModeRegistry::IterationsOnly?>'];
				if ( $.inArray($('#pm_MethodologyIsReleasesUsed').val(),items) != -1 ) {
					$("#pm_MethodologyIsFixedRelease").parents('.control-group').show();
					$("#pm_MethodologyReleaseDuration").parents('.control-group').show();
				} 
				else {
					$("#pm_MethodologyIsFixedRelease").parents('.control-group').hide();
					$("#pm_MethodologyReleaseDuration").parents('.control-group').hide();
				}
			}

			$('#pm_MethodologyIsReleasesUsed').change( function() {
				toggleIterationProps();
			});
			$(document).ready( function() {
				toggleIterationProps();
			});
		</script>
    	<?php
    }
}