<?php
 
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

///////////////////////////////////////////////////////////////////////////////////////
 class TagWebMethod extends WebMethod
 {
 	function execute_request()
 	{
 		global $_REQUEST;
	 	if($_REQUEST['Tag'] != '') {
	 		$this->execute($_REQUEST['Tag']);
	 	}
 	}
 }
  
 ///////////////////////////////////////////////////////////////////////////////////////
 class FilterTagWebMethod extends FilterWebMethod
 {
 	var $tag_it, $object;
 	
 	function FilterTagWebMethod( $object = null )
 	{
 		if ( is_object($object) )
 		{
	 		$this->object = $object;
	 		$this->tag_it = $this->object->getAll();
 			
 			parent::FilterWebMethod( $object->getClassName() );
 		}
 		else
 		{
 			parent::FilterWebMethod( '' );
 		}
 	}

 	function getCaption()
 	{
 		return translate('Тэги');
 	}
 	
 	function getValues()
 	{
  		$values = array (
 			'all' => translate('Все'),
 			);
		$items = array();

 		while ( !$this->tag_it->end() )
 		{
			$items[$this->tag_it->get('Caption')][] = $this->tag_it->get('TagId');
 			$this->tag_it->moveNext();
 		}
		foreach( $items as $key => $ids ) {
			$items[$key] = ' '.join('-',$ids);
		}
		$values = array_merge($values, array_flip($items));

 		if ( $this->getValue() > 0 )
 		{
	 		$tag_it = $this->tag_it->object->getExact($this->getValue());
	 		
     		if ( $tag_it->getId() != '' )
     		{
    			$values[' '.$tag_it->get('TagId')] = $tag_it->get('Caption');
     		}
 		}
 		
		$values[' 0'] = translate('Тэги: не заданы');

 		return $values;
	}
	
	function getStyle()
	{
		return 'width:120px;';
	}

 	function getValueParm()
 	{
 		return 'tag';
 	}
 }
 
?>