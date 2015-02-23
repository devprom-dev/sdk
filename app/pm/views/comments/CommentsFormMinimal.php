<?php

class CommentsFormMinimal extends PMPageForm
{
	private $anchor_it;
	
	public function setAnchorIt( $anchor_it )
	{
		$this->anchor_it = $anchor_it;
	}
	
	function IsAttributeVisible( $attr_name )
	{
		return $attr_name == 'Caption';
	}
	
	function getFieldValue( $attr )
	{
	    switch ( $attr )
	    {
	        case 'ObjectId':
	            return $this->anchor_it->getId();
	            
	        case 'ObjectClass':
	            return get_class($this->anchor_it->object);
	            
	        case 'AuthorId':
	        	return getSession()->getUserIt()->getId();
	            
	        default:
	            return parent::getFieldValue( $attr );
	    }
	}
		
	function createFieldObject( $attribute )
	{
		switch ( $attribute )
		{
		    case 'Caption':
		    		
                $field = new FieldWYSIWYG();
						
 				is_object($this->getObjectIt()) 
 					? $field->setObjectIt( $this->getObjectIt() ) : $field->setObject( $this->getObject() );

				$editor = $field->getEditor();

				$field->setHasBorder( false );

				$editor->setMode( WIKI_MODE_MINIMAL );
						
				$field->setName($attribute);
				
				return $field;
				
		    default:
		    	return parent::createFieldObject( $attribute );
		}
	}

	function getRenderParms()
	{
		return array_merge( parent::getRenderParms(), array(
				'form_body_template' => "pm/CommentsFormMinimal.php" 
		));
	}
}