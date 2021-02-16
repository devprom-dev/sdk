<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

abstract class NewDocumentTemplateWebMethod extends WebMethod
{
	private $object_it = null;

	function __construct( $object_it = null )
	{
		$this->setObjectIt( is_object($object_it) 
			? $object_it : getFactory()->getObject('entity')->getEmptyIterator()
		);
		parent::__construct();
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
		return text(2129);
	}

	function getMethodName() {
		return 'Method:'.get_class($this).':Caption';
	}
	
	function getJSCall( $parms = array() )
	{
		$id = $this->object_it->getId() != '' ? $this->object_it->getId() : $parms['id'];
 		return "javascript:processBulk('".$this->getCaption()."','?formonly=true&operation=".$this->getMethodName()."', ".$id.", ".$this->getRedirectUrl().")";
	}
	
 	function execute_request()
 	{
 		if ( $this->object_it->getId() == '' ) {
 			$this->setObjectIt(
 					$_REQUEST['objects'] != '' 
 						? $this->getObject()->getExact(\TextUtils::parseIds($_REQUEST['objects']))
 						: $this->getObject()->getEmptyIterator()
 			);
 		}

		$references = $this->getReferences();

		$xml = '<?xml version="1.0" encoding="'.APP_ENCODING.'"?><entities>';
		foreach( $references as $object ) {
			$xml .= $object->serialize2Xml();
		}
		$xml .= '</entities>';

		$template = new DocumentTemplate($this->getObject());
		$template->add_parms(
			array (
				'Caption' => $_REQUEST['Caption'] != ''
                                ? $_REQUEST['Caption']
                                : $this->object_it->getHtmlDecoded('Caption'),
				'Content' => $xml
			)
		);
 	}
}