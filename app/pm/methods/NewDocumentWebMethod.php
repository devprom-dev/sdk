<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

abstract class NewDocumentWebMethod extends WebMethod
{
	private $object_it = null;

	function __construct( $object_it = null )
	{
		$this->setObjectIt( is_object($object_it) 
			? $object_it : getFactory()->getObject('entity')->getEmptyIterator()
		);
		parent::__construct();
        $this->setBeforeCallback('devpromOpts.updateUI');
        $this->setRedirectUrl('devpromOpts.updateUI');
	}

	abstract public function getObject();
	abstract public function getReferences();

	public function getObjectIt() {
		return $this->object_it;
	}
	
	public function setObjectIt( $object_it ) {
		$this->object_it = $object_it;
	}

 	function hasAccess() {
 		return getFactory()->getAccessPolicy()->can_create($this->getObject());
 	}
	
	function getCaption() {
		return translate('Документ');
	}

	function getMethodName() {
		return 'Method:'.get_class($this);
	}
	
	function getJSCall( $parms = array() )
	{
		return parent::getJSCall(
		    array_merge(
                array (
                    'template' => is_object($this->object_it) ? $this->object_it->getId() : 0
                ),
                $parms
            )
		);
	}

 	function execute_request()
 	{
		$object = $this->getObject();

		if ( $_REQUEST['template'] > 0 ) {
			$template = new DocumentTemplate($this->getObject());
			$template_it = $template->getExact($_REQUEST['template']);
			$xml = $template_it->getHtmlDecoded('Content');

			if ( $xml == '' ) throw new Exception('Document template is broken');

			getFactory()->setEventsManager(new \ModelEventsManager());
			$context = new CloneContext();
            $context->setResetUids(true);
			$context->setUseExistingReferences(true);

			foreach( $this->getReferences() as $cloneObject )
			{
				$cloneObject = getFactory()->getObject( get_class($cloneObject) );
				$iterator = $cloneObject->createXMLIterator($xml);
				CloneLogic::Run( $context, $cloneObject, $iterator, getSession()->getProjectIt());
			}

			$ids = $context->getIdsMap();
			$object_it = $object->getExact(array_shift($ids[$object->getEntityRefName()]));

			$object->modify_parms($object_it->getId(), array (
				'Caption' => $template_it->getHtmlDecoded('Caption'),
                'PageType' => $_REQUEST['PageType'],
                'IsDocument' => 1
			));

			if ( $_REQUEST['Request'] > 0 ) {
                $request_it = getFactory()->getObject('Request')->getExact($_REQUEST['Request']);
                if ( $request_it->getId() > 0 ) {
                    $trace = getFactory()->getObject('RequestTraceRequirement');
                    $trace->getRegistry()->Merge(
                        array(
                            'ObjectId' => $object_it->getId(),
                            'ObjectClass' => $trace->getObjectClass(),
                            'ChangeRequest' => $request_it->getId(),
                            'Type' => $request_it->get('Type') == '' ? REQUEST_TRACE_REQUEST : REQUEST_TRACE_PRODUCT
                        ),
                        array('ObjectId','ObjectClass','ChangeRequest')
                    );
                }
            }

            if ( $_REQUEST['Task'] > 0 ) {
                $taskIt = getFactory()->getObject('Task')->getExact($_REQUEST['Task']);
                if ( $taskIt->getId() > 0 ) {
                    $trace = getFactory()->getObject('TaskTraceRequirement');
                    $trace->getRegistry()->Merge(
                        array(
                            'ObjectId' => $object_it->getId(),
                            'ObjectClass' => $trace->getObjectClass(),
                            'Task' => $taskIt->getId()
                        )
                    );
                }
            }

            if ( $_REQUEST['Feature'] > 0 ) {
                $featureIt = getFactory()->getObject('Feature')->getExact($_REQUEST['Feature']);
                if ( $featureIt->getId() > 0 ) {
                    $trace = getFactory()->getObject('FunctionTraceRequirement');
                    $trace->getRegistry()->Merge(
                            array(
                                'ObjectId' => $object_it->getId(),
                                'ObjectClass' => $trace->getObjectClass(),
                                'Feature' => $featureIt->getId()
                            )
                        );
                }
            }
        }
		else {
			$object_it = $object->getExact($object->add_parms(
				array (
					'Caption' => $this->getCaption(),
                    'PageType' => $_REQUEST['PageType'],
                    'IsDocument' => 1
				)
			));
		}

		echo $object_it->getUidUrl();
 	}
}