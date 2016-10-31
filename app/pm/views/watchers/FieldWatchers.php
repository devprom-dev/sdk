<?php
include "FormWatcherEmbedded.php";

class FieldWatchers extends FieldForm
{
 	var $object_it, $writable, $watchers, $persistent_mode;
 	
 	function FieldWatchers( $object_it = null, $writable = true, $persistent_mode = false )
 	{
 		global $model_factory;
 		
 		if ( is_a($object_it, 'IteratorBase') )
 		{
 		    $this->object_it = $object_it;
 		}
 		else
 		{
 		    $this->object_it = $object_it->createCachedIterator(array());
 		}
 			
 		$this->writable = $writable;
 		$this->persistent_mode = $persistent_mode;

	    if ( isset($this->object_it) )
	    {
	        $this->watchers = $model_factory->getObject2('pm_Watcher', $this->object_it );

	        $email_parts = preg_split('/</', $this->object_it->getHtmlDecoded('ExternalAuthor'));
	        
	        $this->watchers->addFilter( 
	        		new FilterHasNoAttributePredicate('Email', 
 		    				count($email_parts) > 1 ? trim($email_parts[1], '>') : $this->object_it->get('ExternalAuthor')
 		    		)
 		    );
	    }
 	}
 	
 	function getWatchers()
 	{
 		return $this->watchers;
 	}
 	
 	function getObjectIt()
 	{
 		return $this->object_it;
 	}
 	
 	function draw( $view = null )
	{
		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
			$this->drawBody();
		echo '</div>';
	}
	
	function render( $view )
	{
	    $this->drawBody( $view );
	}
	
	function drawBody( $view = null )
	{
		global $model_factory;
		
 		$form = new FormWatcherEmbedded( 
 			is_object($this->getWatchers()) ? 
 				$this->getWatchers() : $model_factory->getObject('pm_Watcher'), 
			'ObjectId' );
 			
		$object_it = $this->getObjectIt();
		
		$form->setAnchorIt( $object_it );
		
 		if ( is_object($object_it) )
 		{
 			if ( !$this->getEditMode() ) $form->setObjectIt( $object_it );
 		}

 		$form->setReadonly( $this->readOnly() );
 			
 		$form->setTabIndex( $this->getTabIndex() );
 			
 		$form->draw( $view );
	}
}
