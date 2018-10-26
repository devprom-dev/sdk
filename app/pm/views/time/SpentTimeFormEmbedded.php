<?php

class SpentTimeFormEmbedded extends PMFormEmbedded
{
 	var $anchor_it;

 	function setAnchorIt( $anchor_it ) {
 		$this->anchor_it = $anchor_it;
 	}

 	function getAnchorIt() {
 	    return $this->anchor_it;
    }

    function getAnchorField() {
        return 'Task';
    }

    function getLeftWorkAttribute() {
	    return 'LeftWork';
	}
	
	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'LeftWork':
 			    return getSession()->getProjectIt()->getMethodologyIt()->TaskEstimationUsed();

            case 'Participant':
                return getFactory()->getAccessPolicy()->can_modify_attribute(new Activity(), 'Participant');

 			default:
 				return parent::IsAttributeVisible( $attribute );
 		}
 	}
 	
	function getAttributeType( $attr )
	{
 		switch ( $attr )
 		{
 			default:
 				return parent::getAttributeType( $attr );
 		}
	}
 	
	function getAttributeObject( $attr )
	{
 		switch ( $attr )
 		{
 			default:
 				return parent::getAttributeObject( $attr );
 		}
	}
	
	function getFieldValue( $attr )
	{
		switch ( $attr )
		{
			case 'ReportDate':
				return "today";

			case 'LeftWork':
			    return $this->anchor_it->get($this->getLeftWorkAttribute());

            case 'Participant':
                return getSession()->getUserIt()->getId();

			default:
			    if ( $attr == $this->getAnchorField() ) {
					return $this->anchor_it->getId();
				}
				return parent::getFieldValue( $attr );
		}
	}

    function createField( $attr )
    {
        switch( $attr ) {
            case 'Participant':
                return new FieldParticipantDictionary();
            default:
                return parent::createField( $attr );
        }
    }

    function getSaveCallback() {
		return 'updateLeftWorkAttribute';
	}
	
 	function drawField( $attr, $type, $value, $tabindex )
 	{
 		$field_name = $this->getFieldName( $attr );
 		
 		switch ( $attr )
 		{
 		    case 'LeftWork':
				echo '<div class="line">';
					echo '<div class="line">';
						echo translate($this->object->getAttributeUserName('LeftWork'));
					echo '</div>';
					echo '<input type="text" class="spent-time input-block-level" id="'.$this->getFieldName('LeftWork').'" name="'.$this->getFieldName('LeftWork').'" default="'.$this->getFieldValue('LeftWork').'" tabindex="'.($tabindex+1).'">';
				echo '</div>';
		        break;
 		        
 			case 'Capacity':
				echo '<div class="row-fluid formvalueholder formvalue-short">';
					echo '<div>';
						echo translate($this->object->getAttributeUserName('Capacity')).text(2191);
					echo '</div>';
					$script = "javascript: updateLeftWork($('#".$field_name."'), $('#".$this->getFieldName('LeftWork')."'));";
					echo '<input type="text" class="input-medium" id="'.$field_name.'" name="'.$field_name.'" default="'.$value.'" tabindex="'.$tabindex.'" onkeydown="'.$script.'" title="'.htmlentities($this->object->getAttributeDescription('Capacity')).'">';

                    echo '<span class="auto-time-field">';
                        echo '<span class="auto-time"> &nbsp; </span>';
					    $this->drawAutoTimes();
                    echo '</span>';
				echo '</div>';
				break;
 				
			default:
 				return parent::drawField( $attr, $type, $value, $tabindex );
 		}
 	}

    function getActions( $object_it, $item )
    {
        $actions = array();

        $method = new ObjectModifyWebMethod($object_it);
        if ( $method->hasAccess() ) {
            $actions[] = array(
                'click' => $method->getJSCall(),
                'name' => $method->getCaption()
            );
            $actions[] = array();
        }

        return array_merge(
            $actions,
            parent::getActions($object_it, $item)
        );
    }

    function drawAutoTimes()
    {
        $intervals = array(
            strval(5/60) => '', strval(10/60) => '', strval(20/60) => '', '1' => ''
        );
        $objectIt = $this->getAnchorIt();
        if ( $objectIt->get('StateDurationRecent') > 0 ) {
            $workHours = strval($objectIt->get('StateDurationRecent') - (24 - 8) * $objectIt->get('StateDaysRecent'));
            $intervals[$workHours] = $objectIt->object->getAttributeUserName('StateDuration');
        }

        foreach( $intervals as $interval => $description ) {
            $timePassed = getSession()->getLanguage()->getHoursWording($interval);
            $class = $description != '' ? 'label-success' : 'label-warning';
            echo '<span class="auto-time">';
                echo '<a class="'.$class.' label" href="javascript: useAutoTime('.$this->getFormId().', \''.$timePassed.'\');" title="'.$description.'">';
                    echo $timePassed;
                echo '</a>';
            echo '</span>';
        }
    }
}
