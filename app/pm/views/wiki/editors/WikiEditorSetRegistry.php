<?php

include_once "WikiSyntaxEditor.php";

class WikiEditorSetRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 	    getSession()->addBuilder( new WikiSyntaxEditor() );
 	    
 	    foreach( getSession()->getBuilders('WikiEditorBase') as $builder )
 	    {
 			$data[] = array (
 				'entityId' => get_class($builder),
 				'ReferenceName' => get_class($builder),
 				'Caption' => $builder->getDisplayName()
 			);
 	    }
 	    
 		return $this->createIterator( $data );
 	}
}