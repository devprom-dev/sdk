<?php
include_once SERVER_ROOT_PATH.'core/methods/ObjectCreateNewWebMethod.php';

class IncludeWikiPageWebMethod extends ObjectCreateNewWebMethod
{
	const FIND_SUBPAGE = '1';
	const FIND_PARENTPAGE = '2';
	
	function __construct( $object, $mode = IncludeWikiPageWebMethod::FIND_SUBPAGE )
	{
		parent::__construct($object);
		$this->mode = $mode;
		
		$this->setRedirectUrl("donothing");
	}
	
	function getNewObjectUrl()
	{
		$url = $this->getObject()->getPageName().'&Include='.$this->mode;
		if ( $this->mode == IncludeWikiPageWebMethod::FIND_SUBPAGE ) $url .= '&ParentPage=%ids%';
		return $url;
	}
	
	function url( $scenarios_ids = array() ) {
		$parms = array (
				'PageToInclude' => join(',',$scenarios_ids)
		);
		return parent::getJSCall($parms);
	}
	
	private $mode = '';
}