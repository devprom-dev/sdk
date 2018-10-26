<?php
include_once SERVER_ROOT_PATH."pm/views/project/FieldParticipantDictionary.php";

class SpentTimeForm extends PMPageForm
{
	private $anchor_it;

	function extendModel()
    {
        parent::extendModel();

        if ( getFactory()->getAccessPolicy()->can_modify_attribute(new Activity(), 'Participant') ) {
            $this->getObject()->setAttributeVisible('Participant', true);
        }
    }

    public function setAnchorIt( $anchor_it )
	{
		$this->anchor_it = $anchor_it;
	}
	
	public function getLeftFieldName()
	{
		return 'LeftWork';
	}
	
	function IsAttributeVisible( $attr_name )
	{
		switch ( $attr_name ) 
		{
		    case 'LeftWork':
		    	return getSession()->getProjectIt()->getMethodologyIt()->TaskEstimationUsed();
		    default:
		    	return parent::IsAttributeVisible($attr_name);
		}
	}

    function IsAttributeEditable( $attr_name )
    {
        if ( !$this->getObject()->getAttributeEditable($attr_name) ) return false;
        return getFactory()->getAccessPolicy()->can_modify_attribute(new Activity(), $attr_name);
    }

	function getDefaultValue($field)
    {
        switch( $field ) {
            case 'Participant':
                return getSession()->getUserIt()->getId();
            default:
                return parent::getDefaultValue($field);
        }
    }

    function getFieldValue( $attr )
	{
	    $value = parent::getFieldValue( $attr );
	    if ( $value != '' ) return $value;

	    switch ( $attr )
	    {
	        case 'Issue':
	        case 'Task':
	        	return $this->anchor_it->getId();
	            
			case 'ReportDate':
				return getSession()->getLanguage()->getDateFormatted( date('Y-m-d') );

			case 'LeftWork':
				return $this->anchor_it->get($this->getLeftFieldName());
	        	
	        default:
	        	return parent::getFieldValue( $attr );
	    }
	}
	
	function createField( $attr )
	{
		$field = parent::createField( $attr );
		
		if ( $attr == 'LeftWork' )
		{
			$field->setDefault( $this->anchor_it->get($this->getLeftFieldName()) );
		}
		
		return $field;
	}

	function createFieldObject($attr)
    {
        switch( $attr ) {
            case 'Participant':
                return new FieldParticipantDictionary();
            default:
                return parent::createFieldObject($attr);
        }
    }

    function drawScripts()
	{
		?>
		<script type="text/javascript">
			$(document).ready( function() {
				$('#<?=$this->getId()?> #pm_ActivityCapacity').on('keydown', function() {
					updateLeftWork($(this), $('#<?=$this->getId()?> #pm_ActivityLeftWork'));
				});	
			});
		</script>
		<?php
	}
}