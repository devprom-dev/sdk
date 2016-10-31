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
			array (
				'template' => is_object($this->object_it) ? $this->object_it->getId() : 0
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
			$context->setUseExistingReferences(false);

			foreach( $this->getReferences() as $cloneObject )
			{
				$cloneObject = getFactory()->getObject( get_class($cloneObject) );
				$iterator = $cloneObject->createXMLIterator($xml);
				CloneLogic::Run( $context, $cloneObject, $iterator, getSession()->getProjectIt());
			}

			$ids = $context->getIdsMap();
			$object_it = $object->getExact(array_shift($ids[$object->getEntityRefName()]));

			$object->modify_parms($object_it->getId(), array (
				'Caption' => $template_it->getHtmlDecoded('Caption')
			));
		}
		else {
			$object_it = $object->getExact($object->add_parms(
				array (
					'Caption' => $this->getCaption()
				)
			));
		}

		echo $object_it->getViewUrl();
 	}
}