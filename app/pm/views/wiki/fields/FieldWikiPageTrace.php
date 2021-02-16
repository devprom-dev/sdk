<?php
include_once SERVER_ROOT_PATH."pm/views/ui/FieldHierarchySelector.php";

class FieldWikiPageTrace extends FieldHierarchySelector
{
	private $form_id = '';
    private $baseline_attribute = 'Baseline';

    function __construct( $object, $formId, $attributes = null ) {
        $this->form_id = $formId;
        parent::__construct($object, $attributes);
        $this->setMultiselect();
    }

	public function getFormId()
	{
		return $this->form_id;
	}

    public function setBaselineAttribute( $attribute ) {
        $this->baseline_attribute = $attribute;
    }

    function draw( $view = null )
    {
		$data = array();
    	$snapshot_it = getFactory()->getObject('Snapshot')->getRegistry()->Query(
			array (
				new FilterAttributePredicate('ObjectClass', get_class($this->getObject())),
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

    	$this->setAdditionalAttributes( array('DocumentId') );
        $this->setSystemAttribute('realtraces');

    	parent::draw();
    }
}