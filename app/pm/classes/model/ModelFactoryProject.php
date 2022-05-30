<?php
include SERVER_ROOT_PATH . "pm/classes/model/classes.php";
include SERVER_ROOT_PATH . "pm/classes/common/CustomAttributesModelBuilder.php";
include SERVER_ROOT_PATH . "pm/classes/common/CustomAttributesObjectModelBuilder.php";
include "validators/ModelCustomAttributesValidator.php";
include "validators/ModelValidatorAvoidInfiniteLoop.php";

class ModelFactoryProject extends ModelFactoryExtended
{
	protected function buildClasses()
	{
		return array_merge( parent::buildClasses(), array(
			'pm_calendarinterval' => array('Calendar'),
			'wikipage' => array( 'PMWikiPage'),
			'wikipagetype' => array( 'WikiTypeBase'),
			'pm_tasktype' => array( 'TaskType'),
			'pm_release' => array( 'Iteration'),
		    'pm_task' => array( 'Task'),
			'pm_changerequest' => array( 'Request'),
			'pm_participant' => array( 'Participant'),
			'pm_function' => array( 'Feature'),
			'pm_methodology' => array( 'Methodology' ),
		    'pm_attachment' => array( 'Attachment'),
			'pm_activity' => array( 'Activity'),
			'pm_requesttag' => array( 'RequestTag'),
			'pm_question' => array( 'Question' ),
			'pm_milestone' => array( 'Milestone' ),
		    'pm_projectrole' => array( 'ProjectRole' ),
			'pm_participantrole' => array( 'ParticipantRole'),
			'pm_changerequestlink' => array('RequestLink'),
		    'pm_changerequestlinktype' => array('RequestLinkType'),
		    'pm_accessright' => array('AccessRight'),
			'pm_version' => array( 'Release'),
			'pm_watcher' => array( 'Watcher' ),
			'pm_objectaccess' => array( 'AccessObject' ),
			'pm_issuetype' => array('RequestType'),
			'pm_changerequesttrace' => array('RequestTraceBase'),
			'pm_tasktrace' => array('TaskTraceBase'),
			'pm_customtag' => array('CustomTag'),
			'pm_importance' => array('Importance'),
			'pm_state' => array('StateBase'),
            'pm_stateobject' => array('StateObject'),
			'pm_transition' => array('Transition'),
			'pm_transitionrole' => array('TransitionRole'),
			'pm_transitionpredicate' => array('TransitionPredicate'),
			'pm_transitionresetfield' => array('TransitionResetField'),
            'pm_transitionaction' => array('TransitionAction'),
			'pm_customreport' => array('PMCustomReport'),
			'pm_stateaction' => array( 'StateAction'),
			'pm_functiontrace' => array( 'FunctionTrace' ),
		    'pm_workspace' => array( 'Workspace'),
		    'pm_workspacemenu' => array( 'WorkspaceMenu' ),
		    'pm_workspacemenuitem' => array( 'WorkspaceMenuItem' ),
		    'pm_projectuse' => array( 'ProjectUse' ),
			'pm_featuretype' => array( 'FeatureType' ),
			'pm_stateattribute' => array( 'StateAttribute' ),
            'pm_invitation' => array('Invitation'),
            'pm_recurring' => array('Recurring'),
            'comment' => array('Comment'),
            'pm_exporttemplate' => array('ExportTemplate'),
            'pm_componenttype' => array('ComponentType'),
            'pm_component' => array('Component'),
            'pm_componenttrace' => array('ComponentTrace')
		));
	}

	function getModelValidators()
    {
        return array_merge(
            parent::getModelValidators(),
            array(
                new ModelCustomAttributesValidator()
            )
        );
    }

    public function invalidateCache( $sections = array('sessions') )
    {
        getFactory()->getAccessPolicy()->invalidateCache();
        getFactory()->getEntityOriginationService()->invalidateCache();
        getFactory()->getCacheService()->setReadonly();
        foreach( $sections as $section ) {
            getFactory()->getCacheService()->invalidate($section);
        }
        getSession()->truncate();
    }

    public function createEntity( $object, $parms, $validators = array(), $mappers = array() )
    {
        $builder = new \CustomAttributesModelBuilder();
        $builder->build($object);
        return parent::createEntity( $object, $parms, $validators, $mappers);
    }

    public function modifyEntity( $objectIt, $parms, $validators = array(), $mappers = array() )
    {
        $builder = new \CustomAttributesObjectModelBuilder($objectIt);
        $builder->build($objectIt->object);
        return parent::modifyEntity($objectIt, $parms, $validators, $mappers);
    }

    public function getEntities( $object, $objectId )
    {
        if ( !is_array($objectId) ) $objectId = array($objectId);

        $data = array();
        foreach( $objectId as $id ) {
            $data[] = array(
                $object->getIdAttribute() => $id
            );
        }

        $builder = new \CustomAttributesObjectModelBuilder(
            $object->createCachedIterator($data));
        $builder->build($object);

        return parent::getEntities( $object, $objectId );
    }
}