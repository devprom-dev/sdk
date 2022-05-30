<?php
use Devprom\ProjectBundle\Service\Wiki\WikiDeltaService;
include_once SERVER_ROOT_PATH."pm/views/issues/RequestFormBase.php";
include_once SERVER_ROOT_PATH."pm/views/issues/RequestFormMethods.php";
include_once SERVER_ROOT_PATH."pm/views/time/FieldSpentTimeRequest.php";
include_once SERVER_ROOT_PATH."pm/views/watchers/FieldWatchers.php";
include_once SERVER_ROOT_PATH."pm/views/ui/FieldAttachments.php";
include_once SERVER_ROOT_PATH."core/views/c_issue_type_view.php";
include_once SERVER_ROOT_PATH."pm/views/project/FieldParticipantDictionary.php";
include_once SERVER_ROOT_PATH."pm/views/issues/FieldIssueTrace.php";
include_once SERVER_ROOT_PATH.'pm/classes/wiki/converters/WikiConverter.php';

class RequestForm extends RequestFormBase
{
    protected function extendModel()
    {
        parent::extendModel();

        $this->getObject()->setAttributeEditable('ResponseSLA', false);
        $this->getObject()->setAttributeEditable('LeadTimeSLA', false);
    }

    public function buildFieldActions() {
        return $this->buildReferencesActions( $this->getObjectIt(), array (
            'priority' => 'Priority',
            'severity' => 'Severity',
            'release' => 'PlannedRelease',
            'iteration' => 'Iteration',
            'owner' => 'Owner',
            'type' => 'Type'
        ));
    }

    function getTemplateObject() {
        return getFactory()->getObject('RequestTemplate');
    }

	public function buildMethods() {
	    return new RequestFormMethods($this->getObject(), $this->IsFormDisplayed());
    }

   	function getDefaultValue( $attr )
   	{
   		$value = parent::getDefaultValue( $attr );
   		
   		switch( $attr )
   		{
   		    case 'PlannedRelease':
   		    	if ( $value == '' && $this->IsAttributeRequired($attr) )
				{
					if ( $_REQUEST['Iteration'] != '' ) {
						$release_id = getFactory()->getObject('Iteration')->getExact(preg_split('/,/',$_REQUEST['Iteration']))->get('Version');
						if ( $release_id != '' ) return $release_id;
					}
	   		    	return getFactory()->getObject('Release')->getRegistry()->Query(
	   		    				array (
	   		    					new FilterVpdPredicate(),
	   		    					new ReleaseTimelinePredicate('not-passed')
	   		    				)
	   		    		)->getId();
   		    	}
   		    	break;
            case 'Type':
                if ( $value == '' ) {
                    if ( $_REQUEST['Requirement'] != '' ) {
                        return getFactory()->getObject('RequestType')->getByRef('ReferenceName', 'enhancement')->getId();
                    }
                    if ( $_REQUEST['TypeBase'] != '' ) {
                        return getFactory()->getObject('RequestType')->getByRef('ReferenceName', $_REQUEST['TypeBase'])->getId();
                    }
                }
                break;
   		}

   		return $value;
   	}

	function getCaption()
	{
		if ( is_object($this->getObjectIt()) && $this->getObjectIt()->get('TypeName') != ''  ) {
			return $this->getObjectIt()->get('TypeName');
		}
		else {
		    if ( $_REQUEST['Type'] != '' ) {
		        $typeIt = $this->getObject()->getAttributeObject('Type')->getExact($_REQUEST['Type']);
		        if ( $typeIt->getId() != '' ) {
		            return $typeIt->getDisplayName();
                }
            }
			return parent::getCaption();
		}
	}

    function getShortAttributes()
    {
        return array_merge(
            parent::getShortAttributes(),
            array(
                'Priority', 'Estimation', 'PlannedResponse', 'Iteration',
                'PlannedRelease', 'Owner', 'Tags', 'Severity', 'Environment',
                'SubmittedVersion', 'LinkType', 'Type'
            )
        );
    }

    function getFieldDescription( $attr )
    {
        switch( $attr ) {
            case 'PlannedRelease':
            case 'Iteration':
                $report_it = getFactory()->getObject('PMReport')->getExact('projectplan');
                return str_replace('%1', $report_it->getUrl(),
                            str_replace('%2', $report_it->getDisplayName(),
                                text(2263)));
            default:
                return parent::getFieldDescription($attr);
        }
    }

    protected function getNeighbourAttributes() {
        return array('PlannedRelease', 'Iteration', 'State', 'Priority', 'Function');
    }

    function process()
    {
        if ( in_array($this->getAction(), array('add','modify')) ) {
            unset($_REQUEST['Fact']); // filled by embedded form instead of attribute
            $this->getObject()->setAttributeEditable('Fact', false);
        }
        return parent::process();
    }

    function persist()
    {
        if ( $this->getAction() == 'add' && $_REQUEST['template'] == 'true' )
        {
            $template = getFactory()->getObject('RequestTemplate');
            $id = getFactory()->createEntity($template,
                array(
                    'Caption' => $_REQUEST['Caption'],
                    'ListName' => $template->getListName(),
                    'ObjectClass' => get_class($this->getObject())
                )
            )->getId();

            getFactory()->transformEntityData($this->getObject(), $_REQUEST);
            $objectIt = $this->getObject()->createCachedIterator(
                array(
                    array_merge(
                        $_REQUEST,
                        array (
                            $this->getObject()->getIdAttribute() => '1'
                        )
                    )
                )
            );
            $template->persistSnapshot($id, $objectIt);
            $this->setObjectIt($objectIt);
            return true;
        }

        return parent::persist();
    }

    function getSameItemsText() {
        return getSession()->IsRDD() ? text(2825) : parent::getSameItemsText();
    }
}