<?php

include_once SERVER_ROOT_PATH."pm/views/ui/FieldHierarchySelector.php";

class FieldWikiPageTrace extends FieldHierarchySelector
{
	private $form_id = '';
	
	public function setFormId( $id )
	{
		$this->form_id = $id;
	}
	
	public function getFormId()
	{
		return $this->form_id;
	}
	
    function draw()
    {
		$snapshot = getFactory()->getObject('Snapshot');
		
	    $snapshot->addFilter( new FilterAttributePredicate('ObjectClass', get_class($this->getObject())) );
	    $snapshot->addFilter( new FilterAttributePredicate('Type', 'none') ); 

    	$snapshot_it = $snapshot->getAll();
		
		$data = array();
		
		while( !$snapshot_it->end() )
		{
			$item = array();
			
			$item[] = "'id':'".$snapshot_it->getId()."'";
			$item[] = "'documentid':'".$snapshot_it->get('ObjectId')."'";
			$item[] = "'label':'".preg_replace('/"/', "'", $snapshot_it->getDisplayName())."'";
			
			$data[] = "{".join(',',$item)."}";
			
			$snapshot_it->moveNext();
		}

    	$this->setOnSelectCallback("buildSnapshotSelect('#".$this->getId()."', '".$this->getFormId()."', 'Baseline', [".join(',',$data)."])");
		
    	$this->setAdditionalAttributes( array('DocumentId') );

    	parent::draw();
    	
		$field = new FieldDictionary( $snapshot );
		
		$field->setName( preg_replace('/_.+/', '_Baseline', $this->getName()) );

		$field->setId($this->getId().'Baseline');
	    
		echo '<div style="display:none;">';
			echo '<div>';
				echo $snapshot->getDisplayName();
			echo '</div>';
			echo '<div>';
				$field->draw();
			echo '</div>';
		echo '</div>';
    }
}