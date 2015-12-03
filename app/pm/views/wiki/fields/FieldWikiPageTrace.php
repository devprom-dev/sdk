<?php

include_once SERVER_ROOT_PATH."pm/views/ui/FieldHierarchySelector.php";

class FieldWikiPageTrace extends FieldHierarchySelector
{
	private $form_id = '';
    private $baseline_attribute = 'Baseline';

    function __construct( $object, $formId, $attributes = null ) {
        $this->form_id = $formId;
        parent::__construct($object, $attributes);
    }

	public function getFormId()
	{
		return $this->form_id;
	}

    public function setBaselineAttribute( $attribute ) {
        $this->baseline_attribute = $attribute;
    }

    function draw()
    {
		$data = array();
    	$snapshot_it = getFactory()->getObject('Snapshot')->getRegistry()->Query(
			array (
				new FilterAttributePredicate('ObjectClass', get_class($this->getObject())),
				new FilterAttributePredicate('Type', 'none'),
				new FilterVpdPredicate()
			)
		);
		while( !$snapshot_it->end() )
		{
			$data[] = preg_replace('/"/', "'", json_encode(
                array(
                    'id' => $snapshot_it->getId(),
                    'documentid' => $snapshot_it->get('ObjectId'),
                    'label' => $snapshot_it->getDisplayName()
                )
            ));
			$snapshot_it->moveNext();
		}

    	$this->setOnSelectCallback("buildSnapshotSelect('#".$this->getId()."', '".$this->getFormId()."','".$this->baseline_attribute."', [".join(',',$data)."])");
    	$this->setAdditionalAttributes( array('DocumentId') );

    	parent::draw();
    	
		$field = new FieldDictionary( $snapshot_it->object );
		$field->setName( preg_replace('/_.+/', '_'.$this->baseline_attribute, $this->getName()) );
		$field->setId($this->getId().$this->baseline_attribute);
	    
		echo '<div style="display:none;">';
			echo '<div>';
				echo $snapshot_it->object->getDisplayName();
			echo '</div>';
			echo '<div>';
				$field->draw();
			echo '</div>';
		echo '</div>';
    }
}